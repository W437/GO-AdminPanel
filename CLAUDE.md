- See @project_tips folder content.
- Implementing, fixing, or modifying code - should be done in this repo not the SSH.
-  The One-Way Flow: Local Repo → Git → Production (SSH)

# Memory: Deployment Flow
- **DO NOT manually `git pull` on production SSH** - GitHub Actions automatically deploys all pushed changes to production
- After pushing to `main`, wait for GitHub Actions to complete deployment
- Only SSH in for: database migrations, cache clearing, or emergency fixes
- > **Golden Rule for All Coding Agents**  
> Always treat `database/schema/mysql-schema.sql` as the single source of truth for the live database. 

- After database schema changes (migration file), always read @MIGRATION_VALIDATION_PIPELINE to validate the migration locally, then after user pushes and the latest version is deployed, agent SSH's in and migrates in production.

- Production DB name: goadmin_db

- Production server: ssh root@138.197.188.120 

- Production URL: https://hq-secure-panel-1337.hopa.delivery
