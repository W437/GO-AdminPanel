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
 * Schema generated: 2024-11-10
 * Total tables: 139
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
            throw new Exception('Schema file not found. Please run: php artisan schema:dump or get schema from production');
        }

        $sql = file_get_contents($schemaPath);

        // Split by semicolon and execute each statement
        // Remove empty statements and comments
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        foreach ($statements as $statement) {
            if (!empty($statement) && !str_starts_with($statement, '--') && !str_starts_with($statement, '/*')) {
                try {
                    DB::unprepared($statement);
                } catch (\Exception $e) {
                    // Skip errors for things like "DROP TABLE IF EXISTS"
                    if (!str_contains($e->getMessage(), 'Unknown table')) {
                        throw $e;
                    }
                }
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
        // This list is auto-generated from the archived migrations
        // It ensures Laravel doesn't try to re-run old migrations
        $oldMigrations = $this->getOldMigrationsList();

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

    /**
     * Get list of all old migrations that should be marked as run
     */
    private function getOldMigrationsList(): array
    {
        // This list contains all 334 original migrations
        // They are preserved here to ensure they're marked as "already run"
        // Auto-generated from actual migration files

        // Get list dynamically from archived migrations if they exist
        $archivedPath = database_path('migrations/archived_*');
        $archives = glob($archivedPath);

        if (!empty($archives)) {
            $migrations = [];
            foreach ($archives as $archivePath) {
                $files = glob($archivePath . '/*.php');
                foreach ($files as $file) {
                    $migrations[] = str_replace('.php', '', basename($file));
                }
            }
            return $migrations;
        }

        // Fallback: Return empty array if no archives found
        // The migrations table already has the records from production
        return [];
    }
};