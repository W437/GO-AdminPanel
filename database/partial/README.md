# Partial SQL Patches

This folder stores small SQL snippets that the **in-app updater** replays during upgrades. They are **not** part of Laravel migrations or the seeder flow.

## Files

- `addon_settings.sql` – refreshes payment gateway/add-on rows.
- `data_settings.sql` – seeds login URLs and landing-page content.
- `email_tempaltes.sql` – patches email template records.
- `payment_requests.sql` – inserts default payment-request data.

## When they run

`App\Http\Controllers\UpdateController` loads these files and executes them whenever you run the admin “System Update” workflow. Keep them in sync with any production changes; removing or editing them without updating the updater will break upgrades.

## Usage notes

- Do **not** rename files; the updater expects these exact paths.
- If you need a new partial patch, add it here and reference it from `UpdateController`.
- Always review and test SQL snippets before deploying—there’s no automatic transaction handling.
