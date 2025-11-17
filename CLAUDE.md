- See @project_tips folder content.
- Implementing, fixing, or modifying code - should be done in this repo not the SSH.
-  The One-Way Flow: Local Repo → Git → Production (SSH)
- > **Golden Rule for All Coding Agents**  
> Always treat `database/schema/mysql-schema.sql` as the single source of truth for the live database. 

- When creating migrations, always read @MIGRATION_VALIDATION_PIPELINE to validate locally, then after user pushes and the latest version is deployed, agent SSH's in and migrates in production.

- Production DB name: go_adminpanel

- Production server: ssh root@138.197.188.120 
