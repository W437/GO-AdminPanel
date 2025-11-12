- See @project_tips folder content.
- Implementing, fixing, or modifying code - should be done in this repo not the SSH.
-  The One-Way Flow: Local Repo → Git → Production (SSH)
- > **Golden Rule for All Coding Agents**  
> Always treat `database/schema/mysql-schema.sql` as the single source of truth for the live database. Any structural change must ship as a brand-new migration created on top of that schema, followed immediately by `php artisan schema:dump` so the file stays current. Never edit or reuse an old migration that already ran in production.