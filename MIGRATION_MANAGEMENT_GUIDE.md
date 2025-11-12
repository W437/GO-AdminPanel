# Laravel Migration Management & Sync Guide

## 🔄 1. How to Sync DB Changes Between Production & Repository

> **Golden Rule for All Coding Agents**  
> Always treat `database/schema/mysql-schema.sql` as the single source of truth for the live database. Any structural change must ship as a brand-new migration created on top of that schema, followed immediately by `php artisan schema:dump` so the file stays current. Never edit or reuse an old migration that already ran in production.

### The Correct Workflow:

```
Repository (Local) → Production (SSH)
```

**NEVER** do this backwards! Here's why and how:

### ✅ Correct Process:

1. **Create migrations locally**
   ```bash
   php artisan make:migration add_new_feature_to_users_table
   ```

2. **Test locally**
   ```bash
   php artisan migrate
   php artisan migrate:rollback  # Test rollback
   php artisan migrate           # Re-apply
   ```

3. **Commit to repository**
   ```bash
   git add database/migrations/
   git commit -m "Add new feature migration"
   git push
   ```

4. **Deploy to production**
   ```bash
   ssh root@server
   cd /var/www/go-adminpanel
   git pull
   php artisan migrate --force
   ```

### ❌ What NOT to Do:

- **Never edit migrations that have already run in production**
- **Never make database changes directly on production without a migration**
- **Never delete entries from the migrations table manually**

## 🚫 2. Should You Edit Migration Files?

### Rule: **NEVER edit migrations that have been deployed!**

**Why?**
- Laravel tracks executed migrations in the `migrations` table
- Editing won't re-run them (Laravel thinks they're already done)
- Causes inconsistency between environments

### What to do instead:

```php
// ❌ WRONG: Editing existing migration
// 2025_10_03_123607_add_emoji_profile_fields_to_users_table.php
$table->string('profile_emoji', 10)->nullable();
// Changed to 20 characters - WON'T WORK!
$table->string('profile_emoji', 20)->nullable();

// ✅ CORRECT: Create a new migration
// 2025_11_10_000000_increase_profile_emoji_length.php
Schema::table('users', function (Blueprint $table) {
    $table->string('profile_emoji', 20)->nullable()->change();
});
```

## 📦 3. Migration Consolidation Strategies

With 334 migrations, you should consolidate! Here are three approaches:

### Strategy 1: **Schema Dump** (Recommended for your case)

```bash
# 1. Create a schema dump of current production database
php artisan schema:dump

# 2. This creates: database/schema/mysql-schema.sql

# 3. Optional: Remove old migrations after dump
php artisan schema:dump --prune
```

**Benefits:**
- Reduces 334 files to 1 SQL file + new migrations
- Faster deployment and testing
- Preserves exact database state

### Strategy 2: **Squash Migrations** (Manual Consolidation)

Create a single "snapshot" migration:

```bash
# 1. Export current schema from production
ssh root@138.197.188.120 "mysqldump --no-data goadmin_db > schema.sql"

# 2. Create new consolidated migration locally
php artisan make:migration create_initial_schema

# 3. Convert SQL to Laravel migration (manually or use a tool)
```

Example structure:
```php
// 2025_11_10_000000_create_initial_schema.php
class CreateInitialSchema extends Migration
{
    public function up()
    {
        // All 334 migrations consolidated into logical groups
        $this->createUsersTables();
        $this->createOrdersTables();
        $this->createRestaurantsTables();
        // etc...
    }

    private function createUsersTables()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('f_name', 100)->nullable();
            $table->string('l_name', 100)->nullable();
            // ... all current columns
            $table->string('profile_emoji', 10)->nullable()->default('😊');
            $table->string('profile_bg_color', 7)->nullable()->default('#FFFFFF');
            $table->timestamps();
        });
    }
}
```

### Strategy 3: **Fresh Start with Seeder** (Nuclear Option)

For major refactoring:

```bash
# 1. Backup everything
mysqldump goadmin_db > backup_$(date +%Y%m%d).sql

# 2. Create fresh migrations from current schema
php artisan migrate:fresh --seed

# 3. Archive old migrations
mkdir database/migrations/archived
mv database/migrations/2* database/migrations/archived/
```

## 🛠️ 4. Practical Consolidation Plan for Your Project

Given your 334 migrations, here's what I recommend:

### Step 1: Audit Current State
```bash
# Check which migrations have run on production
ssh root@138.197.188.120 "mysql goadmin_db -e 'SELECT COUNT(*) FROM migrations;'"

# Export current production schema
ssh root@138.197.188.120 "mysqldump --no-data goadmin_db > current_schema.sql"
```

### Step 2: Create Consolidated Migration
```bash
# Create new base migration
php artisan make:migration create_base_schema_2025

# Copy current database structure into this migration
# Group related tables together for clarity
```

### Step 3: Test Thoroughly
```bash
# Test on a fresh database
php artisan migrate:fresh
php artisan migrate:rollback
php artisan migrate

# Compare schemas
mysqldump --no-data test_db > test_schema.sql
diff current_schema.sql test_schema.sql
```

### Step 4: Deploy Carefully
```bash
# 1. Backup production
ssh root@server "mysqldump goadmin_db > pre_consolidation_backup.sql"

# 2. Mark old migrations as run (if using consolidation)
# This prevents Laravel from trying to re-run them

# 3. Deploy new code
git pull
php artisan migrate --force
```

## 📋 5. Best Practices Going Forward

### DO:
- ✅ Always create migrations in the repository first
- ✅ Test migrations locally before deploying
- ✅ Use descriptive migration names
- ✅ Group related changes in single migrations
- ✅ Keep migrations small and focused

### DON'T:
- ❌ Edit migrations after deployment
- ❌ Make direct database changes on production
- ❌ Delete migrations without proper consolidation
- ❌ Mix structural changes with data changes

## 🔍 6. Checking Sync Status

To verify production and repo are in sync:

```bash
# Local: Generate migration status
php artisan migrate:status > local_status.txt

# Production: Generate migration status
ssh root@server "cd /var/www/go-adminpanel && php artisan migrate:status" > prod_status.txt

# Compare
diff local_status.txt prod_status.txt
```

## 💡 7. Quick Consolidation Script

Here's a script to help consolidate your migrations:

```bash
#!/bin/bash
# consolidate_migrations.sh

# 1. Backup current state
mkdir -p database/migrations/archive_$(date +%Y%m%d)
cp database/migrations/*.php database/migrations/archive_$(date +%Y%m%d)/

# 2. Export current schema
php artisan schema:dump

# 3. Create new consolidated migration
cat > database/migrations/2025_11_10_000001_consolidated_schema.php << 'EOF'
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up() {
        // Run the schema dump
        DB::unprepared(file_get_contents(database_path('schema/mysql-schema.sql')));
    }

    public function down() {
        // Drop all tables (careful!)
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        $tables = DB::select('SHOW TABLES');
        foreach($tables as $table) {
            $table = array_values((array)$table)[0];
            Schema::dropIfExists($table);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
EOF

echo "Consolidation complete! Test with: php artisan migrate:fresh"
```

## 🎯 Recommended Action for Your Project

1. **Immediate**: Continue using current migrations (they work!)
2. **Short-term**: Document any direct DB changes made via SSH
3. **Medium-term**: Plan consolidation during next major release
4. **Long-term**: Implement schema dumps for faster CI/CD

Your 334 migrations are technical debt but not critical. Consolidate when you have time for proper testing!
