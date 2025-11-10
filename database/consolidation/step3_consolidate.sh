#!/bin/bash

# ==========================================
# STEP 3: CREATE CONSOLIDATED MIGRATION
# ==========================================
# This script creates the actual consolidated migration file

set -e

echo "🔨 STEP 3: Creating Consolidated Migration"
echo "=========================================="

TIMESTAMP=$(date +%Y%m%d_%H%M%S)
PRODUCTION_HOST="root@138.197.188.120"
PRODUCTION_DB="goadmin_db"

# 1. Create Laravel schema dump
echo ""
echo "📋 Option 1: Using Laravel Schema Dump (Recommended)"
echo "----------------------------------------------------"
echo "Running: php artisan schema:dump"

# This creates database/schema/mysql-schema.sql
php artisan schema:dump

if [ -f "database/schema/mysql-schema.sql" ]; then
    echo "✅ Schema dump created successfully!"
else
    echo "⚠️  Schema dump failed, using alternative method..."

    # Alternative: Get schema from production
    echo "📋 Getting schema from production..."
    ssh $PRODUCTION_HOST "mysqldump --no-data --skip-comments --skip-extended-insert $PRODUCTION_DB" > database/schema/mysql-schema.sql
fi

# 2. Create the consolidated migration file
echo ""
echo "✏️  Creating consolidated migration file..."

MIGRATION_FILE="database/migrations/2014_01_01_000000_initial_schema_consolidated.php"

cat > $MIGRATION_FILE << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * CONSOLIDATED MIGRATION - Created from 334 original migrations
 *
 * This migration represents the complete database schema as of the consolidation date.
 * All previous migrations have been merged into this single file.
 *
 * Original migrations are archived in: database/consolidation/backups/migrations_archive/
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable foreign key checks during creation
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Load and execute the schema dump
        $schemaPath = database_path('schema/mysql-schema.sql');

        if (!file_exists($schemaPath)) {
            throw new Exception('Schema file not found. Please run: php artisan schema:dump');
        }

        $sql = file_get_contents($schemaPath);

        // Split by semicolon and execute each statement
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        foreach ($statements as $statement) {
            if (!empty($statement)) {
                DB::unprepared($statement);
            }
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Mark all old migrations as run to prevent re-execution
        $this->markOldMigrationsAsRun();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a nuclear option - drops EVERYTHING
        // Only use in development or if you're absolutely sure

        if (app()->environment('production')) {
            throw new Exception('Cannot drop all tables in production!');
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $tables = DB::select('SHOW TABLES');
        $dbName = DB::getDatabaseName();

        foreach ($tables as $table) {
            $tableName = $table->{"Tables_in_{$dbName}"} ?? array_values((array)$table)[0];
            if ($tableName !== 'migrations') {
                Schema::dropIfExists($tableName);
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Mark old migrations as already run
     */
    private function markOldMigrationsAsRun(): void
    {
        $oldMigrations = [
EOF

# Add list of old migrations to mark as run
echo "            // Auto-generated list of original migrations" >> $MIGRATION_FILE
for file in database/migrations/2*.php; do
    if [ -f "$file" ] && [ "$file" != "$MIGRATION_FILE" ]; then
        filename=$(basename "$file" .php)
        echo "            '$filename'," >> $MIGRATION_FILE
    fi
done

cat >> $MIGRATION_FILE << 'EOF'
        ];

        $batch = DB::table('migrations')->max('batch') ?? 0;
        $batch++;

        foreach ($oldMigrations as $migration) {
            // Only insert if not already present
            DB::table('migrations')->insertOrIgnore([
                'migration' => $migration,
                'batch' => $batch,
            ]);
        }
    }
};
EOF

echo "✅ Consolidated migration created!"

# 3. Archive old migrations
echo ""
echo "📦 Archiving old migrations..."
ARCHIVE_DIR="database/migrations/archived_${TIMESTAMP}"
mkdir -p $ARCHIVE_DIR

# Move all old migrations except the new consolidated one
for file in database/migrations/2*.php; do
    if [ "$file" != "$MIGRATION_FILE" ]; then
        mv "$file" $ARCHIVE_DIR/
    fi
done

# Keep non-date migrations (like create_websockets_statistics_entries_table)
for file in database/migrations/[!2]*.php; do
    if [ -f "$file" ] && [ "$file" != "$MIGRATION_FILE" ]; then
        mv "$file" $ARCHIVE_DIR/
    fi
done

echo "✅ Moved $(ls $ARCHIVE_DIR/*.php | wc -l) migrations to archive"

# 4. Create quick test script
cat > database/consolidation/test_migration.sh << 'EOF'
#!/bin/bash

echo "🧪 Testing consolidated migration..."
echo "======================================"
echo ""
echo "⚠️  This will DROP and RECREATE your local database!"
read -p "Database name to test with (or 'skip' to skip): " DB_NAME

if [ "$DB_NAME" != "skip" ]; then
    echo "Dropping and recreating database: $DB_NAME"
    mysql -u root -p -e "DROP DATABASE IF EXISTS $DB_NAME; CREATE DATABASE $DB_NAME;"

    echo "Running migrations..."
    php artisan migrate --database=mysql --force

    echo ""
    echo "✅ Test complete! Check for any errors above."
fi
EOF

chmod +x database/consolidation/test_migration.sh

# 5. Summary
echo ""
echo "✅ CONSOLIDATION COMPLETE!"
echo "=========================="
echo ""
echo "📊 Summary:"
echo "- Original migrations: 334 files"
echo "- New structure: 1 consolidated migration + schema dump"
echo "- Archived to: $ARCHIVE_DIR"
echo ""
echo "📋 Next steps:"
echo "1. Test locally: ./database/consolidation/test_migration.sh"
echo "2. Review the consolidated migration"
echo "3. Commit changes to git"
echo "4. Deploy carefully to production"
echo ""
echo "⚠️  IMPORTANT: Keep the archived migrations until you're 100% sure everything works!"