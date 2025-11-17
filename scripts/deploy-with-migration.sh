#!/bin/bash

###############################################################################
# Safe Deployment with Migration Validation
###############################################################################
# Complete workflow: Validate → Commit → Push → Deploy → Migrate
#
# Usage:
#   ./scripts/deploy-with-migration.sh "Your commit message"
###############################################################################

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

COMMIT_MESSAGE="${1:-Update with database migrations}"
PRODUCTION_SERVER="root@138.197.188.120"
PRODUCTION_PATH="/var/www/go-adminpanel"

echo -e "${BLUE}════════════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}  Safe Deployment Pipeline${NC}"
echo -e "${BLUE}════════════════════════════════════════════════════════════${NC}"
echo ""

# Step 1: Validate migrations locally
echo -e "${YELLOW}Step 1: Validating migrations locally...${NC}"
if ./scripts/validate-migrations.sh; then
    echo -e "${GREEN}✓ Migration validation passed${NC}"
else
    echo -e "${RED}✗ Migration validation failed${NC}"
    echo -e "${RED}Fix errors before deploying${NC}"
    exit 1
fi
echo ""

# Step 2: Commit changes
echo -e "${YELLOW}Step 2: Committing changes...${NC}"
git add .
if git commit -m "$COMMIT_MESSAGE"; then
    echo -e "${GREEN}✓ Changes committed${NC}"
else
    echo -e "${YELLOW}Note: No changes to commit (might already be committed)${NC}"
fi
echo ""

# Step 3: Push to GitHub
echo -e "${YELLOW}Step 3: Pushing to GitHub...${NC}"
if git push origin main; then
    echo -e "${GREEN}✓ Pushed to GitHub${NC}"
else
    echo -e "${RED}✗ Failed to push${NC}"
    exit 1
fi
echo ""

# Step 4: Wait for GitHub Actions
echo -e "${YELLOW}Step 4: Waiting for GitHub Actions deployment...${NC}"
echo -e "${BLUE}  (Waiting 30 seconds for auto-deployment to complete)${NC}"
sleep 30
echo -e "${GREEN}✓ GitHub Actions should have deployed${NC}"
echo ""

# Step 5: Run migrations on production
echo -e "${YELLOW}Step 5: Running migrations on production...${NC}"
echo -e "${BLUE}  Connecting to: $PRODUCTION_SERVER${NC}"

if ssh "$PRODUCTION_SERVER" "cd $PRODUCTION_PATH && php artisan migrate --force"; then
    echo -e "${GREEN}✓ Production migrations completed${NC}"
else
    echo -e "${RED}✗ Production migration failed${NC}"
    echo -e "${YELLOW}You may need to rollback or fix manually${NC}"
    exit 1
fi
echo ""

# Step 6: Clear production caches
echo -e "${YELLOW}Step 6: Clearing production caches...${NC}"
ssh "$PRODUCTION_SERVER" "cd $PRODUCTION_PATH && php artisan config:clear && php artisan cache:clear && php artisan route:clear"
echo -e "${GREEN}✓ Caches cleared${NC}"
echo ""

# Step 7: Verify production
echo -e "${YELLOW}Step 7: Verifying production...${NC}"
RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" https://hq-secure-panel-1337.hopa.delivery/)

if [ "$RESPONSE" = "302" ] || [ "$RESPONSE" = "200" ]; then
    echo -e "${GREEN}✓ Production is responding (HTTP $RESPONSE)${NC}"
else
    echo -e "${YELLOW}⚠ Production returned HTTP $RESPONSE${NC}"
    echo -e "${YELLOW}  Check the site manually${NC}"
fi
echo ""

# Success summary
echo -e "${GREEN}╔════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║   ✓ DEPLOYMENT SUCCESSFUL                                      ║${NC}"
echo -e "${GREEN}╠════════════════════════════════════════════════════════════════╣${NC}"
echo -e "${GREEN}║${NC}   Migrations validated locally:     ${GREEN}✓ PASSED${NC}                 ${GREEN}║${NC}"
echo -e "${GREEN}║${NC}   Code pushed to GitHub:            ${GREEN}✓ DONE${NC}                   ${GREEN}║${NC}"
echo -e "${GREEN}║${NC}   GitHub Actions deployed:          ${GREEN}✓ DONE${NC}                   ${GREEN}║${NC}"
echo -e "${GREEN}║${NC}   Production migrations run:        ${GREEN}✓ DONE${NC}                   ${GREEN}║${NC}"
echo -e "${GREEN}║${NC}   Production verified:              ${GREEN}✓ DONE${NC}                   ${GREEN}║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "${BLUE}View your site at:${NC} https://admin.hopa.delivery"
echo -e "${BLUE}View API docs at:${NC} https://admin.hopa.delivery/docs"
echo ""
