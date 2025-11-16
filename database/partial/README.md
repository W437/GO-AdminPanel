# Partial SQL Patches

This folder stores small SQL snippets that the **in-app updater** replays during upgrades. They are **not** part of Laravel migrations or the seeder flow.

## Active Files

- `addon_settings.sql` – Seeds payment gateway and SMS gateway configurations (9 gateways total)
- `data_settings.sql` – Seeds login URLs and landing page content for admin/restaurant/delivery interfaces
- `email_tempaltes.sql` – Seeds 29 email templates for notifications (registration, orders, refunds, etc.)

## Removed/Obsolete Files

- ~~`payment_requests.sql`~~ – **REMOVED** - This table structure already exists in `database/schema/mysql-schema.sql` (the single source of truth). The partial file was redundant and has been deleted.

## When They Run

`App\Http\Controllers\UpdateController` loads these files and executes them whenever you run the admin "System Update" workflow. Keep them in sync with any production changes; removing or editing them without updating the updater will break upgrades.

## Important Notes

- **Seed Data Only**: These files contain INSERT statements for configuration/seed data, NOT table structures
- **Table Structures**: All table structures belong in `database/schema/mysql-schema.sql` per the Golden Rule
- **Do NOT rename files**: The updater expects these exact paths
- **Fresh Installs**: The `CoreConfigSeeder` loads these files to populate a fresh database
- **Testing**: Always review and test SQL snippets before deploying—there's no automatic transaction handling

## Golden Rule Reminder

> Always treat `database/schema/mysql-schema.sql` as the single source of truth for the live database. Any structural change must ship as a brand-new migration created on top of that schema, followed immediately by `php artisan schema:dump` so the file stays current.

If you need to add a new partial patch:
1. Add the SQL file here with seed/configuration data only
2. Reference it from `UpdateController`
3. Add it to the `CoreConfigSeeder`'s `$dumpTables` array if needed for fresh installs
