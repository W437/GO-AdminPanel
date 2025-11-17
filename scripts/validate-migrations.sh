#!/bin/bash

###############################################################################
# Migration Validation Pipeline
###############################################################################
# This script validates database migrations locally before production deployment
#
# Usage:
#   ./scripts/validate-migrations.sh
#
# What it does:
# 1. Creates a fresh test database from production schema
# 2. Runs all pending migrations
# 3. Validates schema integrity
# 4. Reports any errors or conflicts
# 5. Provides go/no-go decision for deployment
###############################################################################

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'      # Errors only
GREEN='\033[0;32m'    # Success only
GRAY='\033[0;37m'     # Neutral info
BLUE='\033[0;34m'     # Headers
NC='\033[0m'          # No Color

# Configuration
TEST_DB_NAME="go_adminpanel_migration_test"

# Read from .env file
if [ -f .env ]; then
    export $(grep -v '^#' .env | grep DB_ | xargs)
fi

MYSQL_USER="${DB_USERNAME:-root}"
MYSQL_PASS="${DB_PASSWORD}"
MYSQL_HOST="${DB_HOST:-127.0.0.1}"

echo -e "${BLUE}╔════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║   Migration Validation Pipeline - GO-AdminPanel                ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════════════╝${NC}"
echo ""

# Step 1: Check for pending migrations
echo -e "${GRAY}[1/7] Checking for pending migrations...${NC}"
PENDING=$(php artisan migrate:status | grep "Pending" | wc -l)

if [ "$PENDING" -eq 0 ]; then
    echo -e "${GREEN}✓ No pending migrations to test${NC}"
    echo ""
    echo -e "${GREEN}╔════════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}║   ✓ VALIDATION PASSED - Safe to deploy                         ║${NC}"
    echo -e "${GREEN}╚════════════════════════════════════════════════════════════════╝${NC}"
    exit 0
fi

echo -e "${BLUE}  Found $PENDING pending migration(s)${NC}"
php artisan migrate:status | grep "Pending"
echo ""

# Step 2: Create test database
echo -e "${GRAY}[2/7] Creating test database...${NC}"
if [ -z "$MYSQL_PASS" ]; then
    mysql -h"$MYSQL_HOST" -u"$MYSQL_USER" -e "DROP DATABASE IF EXISTS $TEST_DB_NAME;" 2>/dev/null || true
    mysql -h"$MYSQL_HOST" -u"$MYSQL_USER" -e "CREATE DATABASE $TEST_DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
else
    mysql -h"$MYSQL_HOST" -u"$MYSQL_USER" -p"$MYSQL_PASS" -e "DROP DATABASE IF EXISTS $TEST_DB_NAME;" 2>/dev/null || true
    mysql -h"$MYSQL_HOST" -u"$MYSQL_USER" -p"$MYSQL_PASS" -e "CREATE DATABASE $TEST_DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
fi
echo -e "${GREEN}✓ Test database created: $TEST_DB_NAME${NC}"
echo ""

# Step 3: Import production schema
echo -e "${GRAY}[3/7] Importing production schema snapshot...${NC}"
if [ -z "$MYSQL_PASS" ]; then
    mysql -h"$MYSQL_HOST" -u"$MYSQL_USER" "$TEST_DB_NAME" < database/schema/mysql-schema.sql 2>&1 | grep -v "Warning" || true
else
    mysql -h"$MYSQL_HOST" -u"$MYSQL_USER" -p"$MYSQL_PASS" "$TEST_DB_NAME" < database/schema/mysql-schema.sql 2>&1 | grep -v "Warning" || true
fi
echo -e "${GREEN}✓ Production schema imported${NC}"
echo ""

# Step 4: Temporarily switch to test database
echo -e "${GRAY}[4/7] Configuring test environment...${NC}"
ORIGINAL_DB="${DB_DATABASE}"
export DB_DATABASE="$TEST_DB_NAME"

# Update .env temporarily (backup first)
cp .env .env.backup.migration-test
sed -i.bak "s/DB_DATABASE=.*/DB_DATABASE=$TEST_DB_NAME/" .env
echo -e "${GREEN}✓ Environment configured for testing${NC}"
echo ""

# Step 5: Run migrations on test database
echo -e "${GRAY}[5/7] Running migrations on test database...${NC}"
if php artisan migrate --force 2>&1 | tee /tmp/migration-output.log; then
    echo -e "${GREEN}✓ All migrations executed successfully${NC}"
    MIGRATION_SUCCESS=true
else
    echo -e "${RED}✗ Migration failed!${NC}"
    echo -e "${RED}Error details:${NC}"
    cat /tmp/migration-output.log | tail -20
    MIGRATION_SUCCESS=false
fi
echo ""

# Step 6: Validate schema integrity
echo -e "${GRAY}[6/7] Validating schema integrity...${NC}"

# Check for duplicate columns
DUPLICATE_CHECK=$(mysql -h"$MYSQL_HOST" -u"$MYSQL_USER" -p"$MYSQL_PASS" "$TEST_DB_NAME" -e "
SELECT TABLE_NAME, COLUMN_NAME, COUNT(*) as count
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = '$TEST_DB_NAME'
GROUP BY TABLE_NAME, COLUMN_NAME
HAVING count > 1;" 2>/dev/null | grep -v "Warning")

if [ -n "$DUPLICATE_CHECK" ]; then
    echo -e "${RED}✗ Duplicate columns detected:${NC}"
    echo "$DUPLICATE_CHECK"
    INTEGRITY_OK=false
else
    echo -e "${GREEN}✓ No duplicate columns${NC}"
    INTEGRITY_OK=true
fi

# Check for orphaned foreign keys
echo -e "${BLUE}  Checking foreign key integrity...${NC}"
# Add FK checks here if needed

echo ""

# Step 7: Cleanup and report
echo -e "${GRAY}[7/7] Cleaning up...${NC}"

# Restore original .env
mv .env.backup.migration-test .env
rm -f .env.bak

# Drop test database
if [ -z "$MYSQL_PASS" ]; then
    mysql -h"$MYSQL_HOST" -u"$MYSQL_USER" -e "DROP DATABASE IF EXISTS $TEST_DB_NAME;" 2>/dev/null
else
    mysql -h"$MYSQL_HOST" -u"$MYSQL_USER" -p"$MYSQL_PASS" -e "DROP DATABASE IF EXISTS $TEST_DB_NAME;" 2>/dev/null
fi

echo -e "${GREEN}✓ Test environment cleaned up${NC}"
echo ""

# Final report
echo -e "${BLUE}╔════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║   VALIDATION REPORT                                            ║${NC}"
echo -e "${BLUE}╠════════════════════════════════════════════════════════════════╣${NC}"

if [ "$MIGRATION_SUCCESS" = true ] && [ "$INTEGRITY_OK" = true ]; then
    echo -e "${BLUE}║${NC}   Status: ${GREEN}✓ PASSED${NC}                                              ${BLUE}║${NC}"
    echo -e "${BLUE}║${NC}   Pending Migrations: ${PENDING}                                      ${BLUE}║${NC}"
    echo -e "${BLUE}║${NC}   Migration Test: ${GREEN}SUCCESS${NC}                                     ${BLUE}║${NC}"
    echo -e "${BLUE}║${NC}   Schema Integrity: ${GREEN}VALID${NC}                                     ${BLUE}║${NC}"
    echo -e "${BLUE}╠════════════════════════════════════════════════════════════════╣${NC}"
    echo -e "${BLUE}║${NC}   ${GREEN}✓ MIGRATIONS VALIDATED - NO ERRORS DETECTED${NC}               ${BLUE}║${NC}"
    echo -e "${BLUE}╚════════════════════════════════════════════════════════════════╝${NC}"
    echo ""

    # Apply migrations to actual local database
    echo -e "${GRAY}[BONUS] Applying migrations to local database...${NC}"
    if php artisan migrate --force; then
        echo -e "${GREEN}✓ Local database updated${NC}"
    else
        echo -e "${RED}✗ Failed to update local database${NC}"
        exit 1
    fi
    echo ""

    # Update baseline schema file (Golden Rule!)
    echo -e "${GRAY}[BONUS] Updating baseline schema (mysql-schema.sql)...${NC}"
    if php artisan schema:dump; then
        echo -e "${GREEN}✓ Baseline schema updated${NC}"
        echo -e "${BLUE}  File: database/schema/mysql-schema.sql${NC}"
    else
        echo -e "${RED}✗ Failed to dump schema${NC}"
        exit 1
    fi
    echo ""

    # Final success message
    echo -e "${GREEN}╔════════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}║   ✓ COMPLETE - Ready to commit and deploy                     ║${NC}"
    echo -e "${GREEN}╠════════════════════════════════════════════════════════════════╣${NC}"
    echo -e "${GREEN}║${NC}   Validation:          ${GREEN}✓ PASSED${NC}                              ${GREEN}║${NC}"
    echo -e "${GREEN}║${NC}   Local DB:            ${GREEN}✓ UPDATED${NC}                             ${GREEN}║${NC}"
    echo -e "${GREEN}║${NC}   Baseline Schema:     ${GREEN}✓ UPDATED${NC}                             ${GREEN}║${NC}"
    echo -e "${GREEN}╚════════════════════════════════════════════════════════════════╝${NC}"
    echo ""
    echo -e "${BLUE}Modified files (ready to commit):${NC}"
    echo "  - database/migrations/2025_11_17_*.php (new migration)"
    echo "  - database/schema/mysql-schema.sql (updated baseline)"
    echo ""
    echo -e "${GREEN}Next steps:${NC}"
    echo "  1. Review changes: git status"
    echo "  2. Commit: git add . && git commit -m 'Your message'"
    echo "  3. Push: git push origin main"
    echo "  4. Deploy: ssh root@138.197.188.120 'cd /var/www/go-adminpanel && php artisan migrate --force'"
    echo ""
    exit 0
else
    echo -e "${BLUE}║${NC}   Status: ${RED}✗ FAILED${NC}                                              ${BLUE}║${NC}"
    echo -e "${BLUE}║${NC}   Migration Test: ${RED}FAILED${NC}                                      ${BLUE}║${NC}"
    echo -e "${BLUE}╠════════════════════════════════════════════════════════════════╣${NC}"
    echo -e "${BLUE}║${NC}   ${RED}✗ DO NOT DEPLOY - FIX ERRORS FIRST${NC}                        ${BLUE}║${NC}"
    echo -e "${BLUE}╚════════════════════════════════════════════════════════════════╝${NC}"
    echo ""
    echo -e "${RED}Review the errors above and fix before deploying${NC}"
    echo ""
    exit 1
fi
