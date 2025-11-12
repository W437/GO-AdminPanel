<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseSettingController extends Controller
{
    /**
     * Tables that should never surface in the UI.
     */
    protected array $restrictedTables = [
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
        $tableMeta = $this->getDatabaseTablesWithMeta();
        $defaultTable = $tableMeta[0]['name'] ?? null;

        return view('admin-views.business-settings.db-manager', compact('tableMeta', 'defaultTable'));
    }

    public function tables()
    {
        return response()->json([
            'tables' => $this->getDatabaseTablesWithMeta(),
        ]);
    }

    public function table(Request $request, string $table)
    {
        $table = $this->ensureAllowedTable($table);
        $perPage = (int) $request->get('per_page', 50);
        $perPage = max(1, min($perPage, 200));
        $page = max((int) $request->get('page', 1), 1);
        $offset = ($page - 1) * $perPage;

        $structure = $this->getTableStructure($table);
        $columnNames = array_column($structure['columns'], 'name');

        $query = DB::table($table);
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
        $rows = $rowsCollection->map(function ($row) {
            return (array) $row;
        })->values()->all();

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

        $table = $this->ensureAllowedTable($table);
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

        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
        $filtered = array_values(array_diff($tables, $this->restrictedTables));

        return $this->cachedAllowedTables = $filtered;
    }

    protected function getDatabaseTablesWithMeta(): array
    {
        $meta = [];
        foreach ($this->getAllowedTables() as $table) {
            $meta[] = [
                'name' => $table,
                'rows' => DB::table($table)->count(),
            ];
        }

        return $meta;
    }

    protected function ensureAllowedTable(string $table): string
    {
        $table = trim($table);
        if (! in_array($table, $this->getAllowedTables(), true)) {
            abort(404);
        }

        return $table;
    }

    protected function getTableStructure(string $table): array
    {
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
    }
}
