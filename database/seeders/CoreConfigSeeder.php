<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

class CoreConfigSeeder extends Seeder
{
    /**
     * Tables we hydrate from installation/backup/database.sql
     */
    protected array $dumpTables = [
        'addon_settings',
        'admin_roles',
        'business_settings',
        'data_settings',
        'email_templates',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dumpPath = base_path('installation/backup/database.sql');

        if (!File::exists($dumpPath)) {
            throw new RuntimeException('Missing installation/backup/database.sql – cannot seed baseline configuration.');
        }

        $dump = File::get($dumpPath);

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($this->dumpTables as $table) {
            $insertSql = $this->extractInsertStatement($dump, $table);

            if (!$insertSql) {
                throw new RuntimeException("Insert statement for {$table} not found in {$dumpPath}");
            }

            DB::table($table)->truncate();
            DB::unprepared($insertSql);
        }

        DB::table('currencies')->truncate();
        DB::table('currencies')->insert([
            'id' => 1,
            'country' => 'United States',
            'currency_code' => 'USD',
            'currency_symbol' => '$',
            'exchange_rate' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    protected function extractInsertStatement(string $dump, string $table): ?string
    {
        $pattern = '/INSERT INTO `' . preg_quote($table, '/') . '`.*?;[\r\n]/s';

        if (preg_match($pattern, $dump, $matches)) {
            return trim($matches[0]);
        }

        return null;
    }
}
