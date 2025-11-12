<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseSettingController extends Controller
{
    /**
     * Tables that contain sensitive data and should be highlighted in the UI.
     */
    protected array $sensitiveTables = [
        'admin_roles', 'admins', 'business_settings', 'colors', 'currencies',
        'failed_jobs', 'migrations', 'oauth_access_tokens', 'oauth_auth_codes',
        'oauth_clients', 'oauth_personal_access_clients', 'oauth_refresh_tokens',
        'password_resets', 'personal_access_tokens', 'phone_or_email_verifications',
        'social_medias', 'soft_credentials', 'users', 'email_verifications',
        'phone_verifications', 'restaurant_zone', 'mail_configs',
        'restaurant_notification_settings', 'translations', 'vendor_employees',
        'jobs', 'data_settings', 'addon_settings', 'storages', 'notification_settings',
    ];

    protected ?array $cachedAllowedTables = null;
    protected ?array $cachedAllTables = null;

    public function db_index()
    {
        $tableMeta = $this->getDatabaseTablesWithMeta();
        $tables = array_column($tableMeta, 'name');
        $rows = array_column($tableMeta, 'rows');

        return view('admin-views.business-settings.db-index', compact('tables', 'rows'));
    }

    public function clean_db(Request $request)
    {
        $tables = array_intersect((array) $request->tables, $this->getAllowedTables());

        if (count($tables) === 0) {
            Toastr::error(translate('No Table Updated'));
            return back();
        }

        try {
            DB::transaction(function () use ($tables) {
                foreach ($tables as $table) {
                    DB::table($table)->delete();
                }
            });
        } catch (\Exception $exception) {
            info($exception->getMessage());
            Toastr::error(translate('Failed to update!'));
            return back();
        }

        Toastr::success(translate('messages.updated_successfully'));
        return back();
    }

    public function db_manager()
    {
        $tableMeta = $this->getDatabaseTablesWithMeta(true);
        $stats = $this->getDatabaseStats($tableMeta);
        $defaultTable = $tableMeta[0]['name'] ?? null;

        return view('admin-views.business-settings.db-manager', compact('tableMeta', 'defaultTable', 'stats'));
    }

    public function tables()
    {
        return response()->json([
            'tables' => $this->getDatabaseTablesWithMeta(true),
            'stats' => $this->getDatabaseStats(),
        ]);
    }

    public function table(Request $request, string $table)
    {
        $table = $this->ensureTable($table, true);
        $perPage = (int) $request->get('per_page', 50);
        $perPage = max(1, min($perPage, 200));
        $page = max((int) $request->get('page', 1), 1);
        $offset = ($page - 1) * $perPage;

        $structure = $this->getTableStructure($table);
        $columnNames = array_column($structure['columns'], 'name');

        $query = DB::table($table);
        $selectColumns = $this->buildSelectColumns($table, $structure['columns']);
        if (! empty($selectColumns)) {
            $query->select($selectColumns);
        }
        $sortBy = $request->get('sort_by');
        $sortDir = strtolower($request->get('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        if ($sortBy && in_array($sortBy, $columnNames, true)) {
            $query->orderBy($sortBy, $sortDir);
        } elseif ($structure['primary_key']) {
            $query->orderBy($structure['primary_key'], 'desc');
        } elseif (! empty($columnNames)) {
            $query->orderBy($columnNames[0], 'desc');
        }

        $total = (clone $query)->count();
        $rowsCollection = $query->offset($offset)->limit($perPage)->get();
        $rows = $rowsCollection
            ->map(function ($row) use ($structure) {
                return $this->normalizeRow($row, $structure['columns']);
            })
            ->values()
            ->all();

        return response()->json([
            'table' => $table,
            'columns' => $structure['columns'],
            'primary_key' => $structure['primary_key'],
            'rows' => $rows,
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
        ]);
    }

    public function updateRow(Request $request, string $table, $primary)
    {
        if (env('APP_MODE') === 'demo') {
            return response()->json([
                'message' => translate('messages.this_option_is_disabled_in_demo_mode'),
            ], 403);
        }

        $table = $this->ensureTable($table, true);
        $structure = $this->getTableStructure($table);
        $primaryKey = $structure['primary_key'];

        if (! $primaryKey) {
            return response()->json([
                'message' => translate('messages.this_table_does_not_have_a_primary_key'),
            ], 422);
        }

        $incoming = $request->input('values', []);
        if (! is_array($incoming) || empty($incoming)) {
            return response()->json([
                'message' => translate('messages.no_data_received'),
            ], 422);
        }

        $columns = collect($structure['columns'])->keyBy('name');
        $payload = [];

        foreach ($incoming as $key => $value) {
            if (! $columns->has($key)) {
                continue;
            }

            $column = $columns->get($key);

            if ($column['autoincrement'] && $key === $primaryKey) {
                continue;
            }

            if ($value === '' && $column['not_null'] === false) {
                $payload[$key] = null;
                continue;
            }

            if ($column['type'] === 'boolean') {
                $payload[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                continue;
            }

            $payload[$key] = $value;
        }

        if (empty($payload)) {
            return response()->json([
                'message' => translate('messages.no_fields_to_update'),
            ], 422);
        }

        try {
            $affected = DB::table($table)->where($primaryKey, $primary)->update($payload);
        } catch (\Throwable $exception) {
            info($exception->getMessage());
            return response()->json([
                'message' => translate('messages.failed_to_update_record'),
            ], 500);
        }

        if ($affected === 0) {
            return response()->json([
                'message' => translate('messages.no_rows_updated'),
            ], 404);
        }

        return response()->json([
            'message' => translate('messages.record_updated_successfully'),
        ]);
    }

    protected function getAllowedTables(): array
    {
        if ($this->cachedAllowedTables !== null) {
            return $this->cachedAllowedTables;
        }

        $tables = $this->getAllTables();
        $filtered = array_values(array_diff($tables, $this->sensitiveTables));

        return $this->cachedAllowedTables = $filtered;
    }

    protected function getAllTables(): array
    {
        if ($this->cachedAllTables !== null) {
            return $this->cachedAllTables;
        }

        return $this->cachedAllTables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
    }

    protected function getDatabaseTablesWithMeta(bool $includeSensitive = false): array
    {
        $tables = $includeSensitive ? $this->getAllTables() : $this->getAllowedTables();
        $meta = [];
        foreach ($tables as $table) {
            $meta[] = [
                'name' => $table,
                'rows' => DB::table($table)->count(),
                'sensitive' => $this->isSensitiveTable($table),
            ];
        }

        return $meta;
    }

    protected function getDatabaseStats(array $tableMeta = []): array
    {
        if (empty($tableMeta)) {
            $tableMeta = $this->getDatabaseTablesWithMeta(true);
        }

        $tableCount = count($tableMeta);
        $totalRows = array_sum(array_column($tableMeta, 'rows'));
        $sizeBytes = $this->resolveDatabaseSizeBytes();

        return [
            'table_count' => $tableCount,
            'total_rows' => $totalRows,
            'database_size_bytes' => $sizeBytes,
            'database_size_human' => $this->formatBytes($sizeBytes),
        ];
    }

    protected function ensureTable(string $table, bool $allowSensitive = false): string
    {
        $table = trim($table);
        $pool = $allowSensitive ? $this->getAllTables() : $this->getAllowedTables();
        if (! in_array($table, $pool, true)) {
            abort(404);
        }

        return $table;
    }

    protected function getTableStructure(string $table): array
    {
        try {
            $schema = DB::connection()->getDoctrineSchemaManager();
            $tableDetails = $schema->listTableDetails($table);
            $primaryKey = null;

            if ($tableDetails->hasPrimaryKey()) {
                $primaryKeyColumns = $tableDetails->getPrimaryKey()->getColumns();
                $primaryKey = $primaryKeyColumns[0] ?? null;
            }

            $columns = [];
            foreach ($tableDetails->getColumns() as $column) {
                $columns[] = [
                    'name' => $column->getName(),
                    'type' => $column->getType()->getName(),
                    'length' => $column->getLength(),
                    'not_null' => $column->getNotnull(),
                    'default' => $column->getDefault(),
                    'autoincrement' => $column->getAutoincrement(),
                    'precision' => $column->getPrecision(),
                    'scale' => $column->getScale(),
                ];
            }

            return [
                'primary_key' => $primaryKey,
                'columns' => $columns,
            ];
        } catch (\Throwable $exception) {
            info('db_manager_structure_fallback: ' . $exception->getMessage());
            return $this->getTableStructureFallback($table);
        }
    }

    protected function getTableStructureFallback(string $table): array
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            if ($columns = $this->resolveMysqlColumns($table)) {
                return $columns;
            }
        } elseif ($driver === 'pgsql') {
            if ($columns = $this->resolvePostgresColumns($table)) {
                return $columns;
            }
        }

        return $this->basicColumnListing($table);
    }

    protected function resolveMysqlColumns(string $table): ?array
    {
        try {
            $wrappedTable = $this->wrapIdentifier($table);
            $rawColumns = DB::select("SHOW FULL COLUMNS FROM {$wrappedTable}");

            if (empty($rawColumns)) {
                return null;
            }

            $primaryKey = null;
            $columns = [];

            foreach ($rawColumns as $column) {
                $field = $column->Field ?? $column->field ?? null;
                if (! $field) {
                    continue;
                }

                if (($column->Key ?? '') === 'PRI' && ! $primaryKey) {
                    $primaryKey = $field;
                }

                $columns[] = [
                    'name' => $field,
                    'type' => $column->Type ?? 'string',
                    'length' => null,
                    'not_null' => ($column->Null ?? '') === 'NO',
                    'default' => $column->Default ?? null,
                    'autoincrement' => stripos($column->Extra ?? '', 'auto_increment') !== false,
                    'precision' => null,
                    'scale' => null,
                ];
            }

            return [
                'primary_key' => $primaryKey,
                'columns' => $columns,
            ];
        } catch (\Throwable $exception) {
            info('mysql_column_fallback_failed: ' . $exception->getMessage());
            return null;
        }
    }

    protected function resolvePostgresColumns(string $table): ?array
    {
        try {
            $rawColumns = DB::select(
                'SELECT column_name, data_type, is_nullable, column_default
                 FROM information_schema.columns
                 WHERE table_name = ?
                 ORDER BY ordinal_position',
                [$table]
            );

            if (empty($rawColumns)) {
                return null;
            }

            $primaryKey = $this->resolvePostgresPrimaryKey($table);
            $columns = [];

            foreach ($rawColumns as $column) {
                $columns[] = [
                    'name' => $column->column_name ?? $column->column_Name ?? '',
                    'type' => $column->data_type ?? 'string',
                    'length' => null,
                    'not_null' => ($column->is_nullable ?? '') === 'NO',
                    'default' => $column->column_default ?? null,
                    'autoincrement' => false,
                    'precision' => null,
                    'scale' => null,
                ];
            }

            return [
                'primary_key' => $primaryKey,
                'columns' => $columns,
            ];
        } catch (\Throwable $exception) {
            info('pgsql_column_fallback_failed: ' . $exception->getMessage());
            return null;
        }
    }

    protected function resolvePostgresPrimaryKey(string $table): ?string
    {
        try {
            $result = DB::select(
                'SELECT a.attname
                 FROM pg_index i
                 JOIN pg_attribute a ON a.attrelid = i.indrelid AND a.attnum = ANY(i.indkey)
                 WHERE i.indrelid = ?::regclass AND i.indisprimary = true
                 LIMIT 1',
                [$table]
            );

            return $result[0]->attname ?? null;
        } catch (\Throwable $exception) {
            info('pgsql_primary_key_lookup_failed: ' . $exception->getMessage());
            return null;
        }
    }

    protected function basicColumnListing(string $table): array
    {
        try {
            $columnNames = DB::getSchemaBuilder()->getColumnListing($table);
        } catch (\Throwable $exception) {
            info('basic_column_listing_failed: ' . $exception->getMessage());
            $columnNames = [];
        }

        $columns = array_map(function ($name) {
            return [
                'name' => $name,
                'type' => 'string',
                'length' => null,
                'not_null' => false,
                'default' => null,
                'autoincrement' => false,
                'precision' => null,
                'scale' => null,
            ];
        }, $columnNames);

        return [
            'primary_key' => null,
            'columns' => $columns,
        ];
    }

    protected function wrapIdentifier(string $name): string
    {
        return '`' . str_replace('`', '``', $name) . '`';
    }

    protected function resolveDatabaseSizeBytes(): int
    {
        $driver = DB::connection()->getDriverName();

        try {
            if ($driver === 'mysql') {
                $dbName = DB::getDatabaseName();
                $result = DB::select(
                    'SELECT SUM(data_length + index_length) AS size
                     FROM information_schema.tables
                     WHERE table_schema = ?',
                    [$dbName]
                );

                return (int) ($result[0]->size ?? 0);
            }

            if ($driver === 'pgsql') {
                $result = DB::select('SELECT pg_database_size(current_database()) AS size');

                return (int) ($result[0]->size ?? 0);
            }
        } catch (\Throwable $exception) {
            info('resolve_db_size_failed: ' . $exception->getMessage());
        }

        return 0;
    }

    protected function formatBytes(?int $bytes): string
    {
        $bytes = max(0, (int) $bytes);
        if ($bytes === 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = (int) floor(log($bytes, 1024));
        $power = min($power, count($units) - 1);

        $value = $bytes / (1024 ** $power);

        return number_format($value, $power === 0 ? 0 : 2) . ' ' . $units[$power];
    }

    protected function buildSelectColumns(string $table, array $columns): array
    {
        if (empty($columns)) {
            return [];
        }

        $grammar = DB::connection()->getQueryGrammar();
        $selects = [];

        foreach ($columns as $column) {
            $name = $column['name'] ?? null;
            if (! $name) {
                continue;
            }

            if ($this->isGeometryType($column['type'] ?? '')) {
                $wrapped = $grammar->wrap($table . '.' . $name);
                $alias = $grammar->wrap($name);
                $selects[] = DB::raw("ST_AsText({$wrapped}) as {$alias}");
            } else {
                $selects[] = $table . '.' . $name;
            }
        }

        return $selects;
    }

    protected function normalizeRow($row, array $columns): array
    {
        $normalized = [];

        foreach ($columns as $column) {
            $name = $column['name'] ?? null;
            if (! $name) {
                continue;
            }

            $value = is_array($row) ? ($row[$name] ?? null) : ($row->{$name} ?? null);
            $normalized[$name] = $this->normalizeValue($value);
        }

        return $normalized;
    }

    protected function normalizeValue($value)
    {
        if (is_resource($value)) {
            $data = stream_get_contents($value);
            if ($data === false) {
                return '[resource]';
            }

            $length = strlen($data);
            $previewBytes = substr($data, 0, 32);
            $preview = bin2hex($previewBytes);
            if ($length > 32) {
                $preview .= '…';
            }

            return sprintf('[binary %d bytes] %s', $length, $preview);
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format(DATE_ATOM);
        }

        if (is_object($value)) {
            if (method_exists($value, '__toString')) {
                return (string) $value;
            }

            return json_encode($value, JSON_PRETTY_PRINT);
        }

        if (is_array($value)) {
            return json_encode($value, JSON_PRETTY_PRINT);
        }

        return $value;
    }

    protected function isGeometryType(?string $type): bool
    {
        $type = strtolower($type ?? '');
        if ($type === '') {
            return false;
        }

        $geometryTypes = [
            'geometry', 'point', 'multipoint', 'linestring', 'multilinestring',
            'polygon', 'multipolygon', 'geomcollection', 'geometrycollection',
        ];

        return in_array($type, $geometryTypes, true);
    }

    protected function isSensitiveTable(string $table): bool
    {
        return in_array($table, $this->sensitiveTables, true);
    }
}
