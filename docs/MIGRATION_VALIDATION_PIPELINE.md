# Migration Validation Pipeline

## 🎯 Overview

This pipeline ensures database migrations are tested locally **before** deploying to production, preventing schema conflicts and downtime.

`★ Insight ─────────────────────────────────────`
• Test migrations against production schema snapshot
• Catch errors locally before they break production
• Automated validation prevents common migration mistakes
`─────────────────────────────────────────────────`

---

## 🚀 Quick Start

### **Option 1: Validate Only** (Recommended before committing)
```bash
./scripts/validate-migrations.sh
```

### **Option 2: Full Deployment Pipeline** (Validate → Commit → Deploy)
```bash
./scripts/deploy-with-migration.sh "Add new public restaurant APIs"
```

---

## 📋 **How It Works**

### **Validation Pipeline Steps:**

```
┌─────────────────────────────────────────────────┐
│ 1. Check for pending migrations                │
│    - Scans migrate:status                      │
│    - Exits if none pending                     │
└─────────────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────────────┐
│ 2. Create test database                        │
│    - Name: go_adminpanel_migration_test        │
│    - Fresh database for testing                │
└─────────────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────────────┐
│ 3. Import production schema                    │
│    - Source: database/schema/mysql-schema.sql  │
│    - Exact copy of production structure        │
└─────────────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────────────┐
│ 4. Run pending migrations                      │
│    - Executes on test database                 │
│    - Captures all errors                       │
└─────────────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────────────┐
│ 5. Validate schema integrity                   │
│    - Check for duplicate columns               │
│    - Verify foreign keys                       │
│    - Validate constraints                      │
└─────────────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────────────┐
│ 6. Clean up test database                      │
│    - Drop test database                        │
│    - Restore original .env                     │
└─────────────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────────────┐
│ 7. Report: PASS ✓ or FAIL ✗                   │
└─────────────────────────────────────────────────┘
```

---

## 🛠️ **Setup Requirements**

### **1. MySQL Access**
The script needs MySQL credentials from your `.env`:
```env
DB_HOST=127.0.0.1
DB_DATABASE=go_adminpanel
DB_USERNAME=root
DB_PASSWORD=your_password
```

### **2. MySQL Command-Line Tools**
```bash
# Check if installed:
which mysql mysqldump

# Install on Mac:
brew install mysql-client

# Install on Ubuntu:
apt install mysql-client
```

### **3. Script Permissions**
```bash
chmod +x scripts/validate-migrations.sh
chmod +x scripts/deploy-with-migration.sh
```

---

## 📖 **Usage Examples**

### **Example 1: Before Committing Changes**

```bash
# 1. Create your migration
php artisan make:migration add_something_to_table

# 2. Write migration code
# ... edit the migration file ...

# 3. Validate locally
./scripts/validate-migrations.sh

# Output if successful:
# ╔════════════════════════════════════════════════════╗
# ║   ✓ VALIDATION PASSED - Safe to deploy            ║
# ╚════════════════════════════════════════════════════╝

# 4. Now safe to commit and push!
```

### **Example 2: Full Deployment**

```bash
# One command does everything:
./scripts/deploy-with-migration.sh "Add unique index to restaurant slugs"

# This will:
# ✓ Validate migrations locally
# ✓ Commit changes
# ✓ Push to GitHub
# ✓ Wait for GitHub Actions
# ✓ Run migrations on production
# ✓ Clear caches
# ✓ Verify production is up
```

### **Example 3: After Fixing Errors**

```bash
# You see errors in validation:
./scripts/validate-migrations.sh
# ✗ Duplicate column 'type' detected

# Fix your migration
# ... edit migration file ...

# Validate again
./scripts/validate-migrations.sh
# ✓ VALIDATION PASSED

# Deploy with confidence!
```

---

## 🔍 **What the Validator Checks**

### **1. Duplicate Columns**
```sql
-- BAD: Migration tries to add existing column
alter table `restaurants` add `slug` varchar(255);
-- Error: Column already exists

-- The validator catches this before production!
```

### **2. Missing Tables**
```sql
-- BAD: Migration references non-existent table
alter table `non_existent_table` add column...
-- Error: Table doesn't exist

-- Validator fails early
```

### **3. Constraint Conflicts**
```sql
-- BAD: Adding UNIQUE on column with duplicates
alter table `restaurants` add unique(`slug`);
-- Error: Duplicate entry 'pizza-palace' for key 'slug'

-- Validator runs the duplicate-fixing logic first
```

### **4. Foreign Key Failures**
```sql
-- BAD: FK references deleted table
alter table `orders` add foreign key(`restaurant_id`)...
-- Error: Referenced table doesn't exist

-- Validator checks FK integrity
```

---

## 🐛 **Troubleshooting**

### **Issue: "Access denied for user"**

**Problem:** MySQL credentials incorrect

**Solution:**
```bash
# Check your .env file
cat .env | grep DB_

# Test MySQL connection
mysql -h127.0.0.1 -uroot -p -e "SHOW DATABASES;"
```

### **Issue: "Database already exists"**

**Problem:** Previous test database not cleaned up

**Solution:**
```bash
# Manually drop test database
mysql -uroot -p -e "DROP DATABASE IF EXISTS go_adminpanel_migration_test;"

# Run validator again
./scripts/validate-migrations.sh
```

### **Issue: Migration fails locally but need to deploy anyway**

**Problem:** Local and production databases differ

**Solution:**
```bash
# Skip validation (use with caution!)
git add .
git commit -m "Your message"
git push origin main

# Then manually migrate on production
ssh root@138.197.188.120 "cd /var/www/go-adminpanel && php artisan migrate --force"
```

---

## 📊 **Integration with Workflow**

### **Your Current Workflow (Before):**
```
1. Create migration
2. git commit
3. git push
4. GitHub Actions deploys
5. SSH in and migrate
6. 🤞 Hope it works
```

### **New Workflow (After):**
```
1. Create migration
2. ./scripts/validate-migrations.sh  ← NEW!
3. ✓ Pass? Continue. ✗ Fail? Fix first
4. git commit
5. git push
6. GitHub Actions deploys
7. SSH in and migrate
8. ✅ Confident it will work
```

### **Even Better - Automated:**
```
./scripts/deploy-with-migration.sh "Your commit message"
↓
Everything happens automatically!
```

---

## 🔐 **Safety Features**

### **1. Non-Destructive**
- Creates temporary test database
- Never touches production until you approve
- Automatically cleans up test database

### **2. Isolated Testing**
- Uses separate database: `go_adminpanel_migration_test`
- Temporarily switches .env (restores after)
- No impact on your local development database

### **3. Detailed Error Reports**
```
✗ Migration failed!
Error details:
SQLSTATE[42S21]: Column already exists: 1060 Duplicate column name 'type'
```
Shows exactly what went wrong and where

### **4. Rollback-Friendly**
```bash
# If production migration fails, rollback:
ssh root@138.197.188.120
cd /var/www/go-adminpanel
php artisan migrate:rollback --step=1
```

---

## 🎓 **Best Practices**

### **1. Always Validate Before Pushing**
```bash
# DO THIS:
./scripts/validate-migrations.sh
git push origin main

# NOT THIS:
git push origin main  # Hope for the best 🤞
```

### **2. Test with Production-Like Data**
```bash
# If you want to test with actual data:
# 1. Export sample data from production
ssh root@138.197.188.120 "mysqldump go_adminpanel restaurants --where='1 LIMIT 100'" > test_data.sql

# 2. Import into test database (modify validation script to include this)
```

### **3. Keep Schema File Updated**
```bash
# After running migration locally:
php artisan migrate
php artisan schema:dump  # ← Updates mysql-schema.sql

# This is THE GOLDEN RULE from CLAUDE.md
```

### **4. Review Migration Before Committing**
```bash
# Preview what SQL will run:
php artisan migrate --pretend

# Check migration file carefully:
cat database/migrations/2025_11_17_*.php
```

---

## 📁 **Files in This System**

```
scripts/
├── validate-migrations.sh      # Validate migrations locally
└── deploy-with-migration.sh    # Full deployment pipeline

database/
├── schema/
│   └── mysql-schema.sql        # Single source of truth
└── migrations/
    └── *.php                    # Individual migration files

docs/
└── MIGRATION_VALIDATION_PIPELINE.md  # This file
```

---

## 🚨 **Common Migration Errors Caught**

### **Error 1: Duplicate Column**
```
✗ Migration failed!
SQLSTATE[42S21]: Column already exists: 1060 Duplicate column name 'slug'
```
**Fix:** Check if column already in `mysql-schema.sql`

### **Error 2: Missing Table**
```
✗ Migration failed!
SQLSTATE[42S02]: Base table or view not found: 1146 Table 'zones' doesn't exist
```
**Fix:** Create table first or check spelling

### **Error 3: Duplicate Key**
```
✗ Migration failed!
SQLSTATE[23000]: Duplicate entry 'pizza-palace' for key 'slug'
```
**Fix:** Our migration handles this! See `ensureUniqueSlugs()` method

### **Error 4: Data Truncation**
```
✗ Migration failed!
Data too long for column 'slug' at row 1
```
**Fix:** Increase column size: `$table->string('slug', 500)`

---

## 📚 **Advanced Usage**

### **Custom MySQL Credentials**
```bash
# Override .env settings:
DB_USERNAME=custom_user \
DB_PASSWORD=custom_pass \
./scripts/validate-migrations.sh
```

### **Dry Run (Preview Only)**
```bash
# See what would happen without creating test DB:
php artisan migrate --pretend
```

### **Validate Specific Migration**
```bash
# Run only one migration on test DB:
php artisan migrate --path=database/migrations/2025_11_17_015643_*.php
```

---

## ✅ **Checklist: Before Every Deployment**

- [ ] Run `./scripts/validate-migrations.sh`
- [ ] Check output for errors
- [ ] Review migration SQL with `php artisan migrate --pretend`
- [ ] Verify `mysql-schema.sql` is updated (`php artisan schema:dump`)
- [ ] Commit all changes including schema file
- [ ] Push to GitHub
- [ ] Wait for GitHub Actions
- [ ] SSH and run `php artisan migrate --force`
- [ ] Verify production works

---

## 🎯 **Summary**

**Before this pipeline:**
```
❌ Manual testing
❌ Errors found in production
❌ Downtime during rollbacks
❌ Stressful deployments
```

**With this pipeline:**
```
✅ Automated validation
✅ Errors caught locally
✅ Zero production downtime
✅ Confident deployments
```

---

**Last Updated:** November 2025
**Maintained By:** Development Team
