#!/bin/bash

# ==========================================
# STEP 5: DEPLOY TO PRODUCTION (USE WITH CAUTION!)
# ==========================================
# This script guides you through safely deploying the consolidation to production

set -e

echo "🚨 STEP 5: Production Deployment Guide"
echo "======================================="
echo ""
echo "⚠️  WARNING: This affects your PRODUCTION database!"
echo "⚠️  Make sure you have completed and tested steps 1-4!"
echo ""

# Checklist
echo "Pre-deployment Checklist:"
echo "-------------------------"
echo "Have you:"
read -p "✓ Created full backup (step 1)? (yes/no): " CHECK1
read -p "✓ Analyzed migrations (step 2)? (yes/no): " CHECK2
read -p "✓ Created consolidated migration (step 3)? (yes/no): " CHECK3
read -p "✓ Tested locally (step 4)? (yes/no): " CHECK4
read -p "✓ Committed changes to git? (yes/no): " CHECK5

if [ "$CHECK1" != "yes" ] || [ "$CHECK2" != "yes" ] || [ "$CHECK3" != "yes" ] || [ "$CHECK4" != "yes" ] || [ "$CHECK5" != "yes" ]; then
    echo ""
    echo "❌ Please complete all steps before deploying!"
    exit 1
fi

echo ""
echo "📋 Deployment Steps:"
echo "===================="
echo ""

cat << 'DEPLOY_GUIDE'
MANUAL DEPLOYMENT STEPS (DO NOT AUTOMATE):

1️⃣  BACKUP PRODUCTION ONE MORE TIME:
   ssh root@138.197.188.120
   mysqldump --single-transaction goadmin_db > pre_deploy_backup_$(date +%Y%m%d_%H%M%S).sql

2️⃣  PULL LATEST CODE:
   cd /var/www/go-adminpanel
   git pull origin main

3️⃣  VERIFY CONSOLIDATED MIGRATION EXISTS:
   ls -la database/migrations/2014_01_01_000000_initial_schema_consolidated.php
   ls -la database/schema/mysql-schema.sql

4️⃣  MARK OLD MIGRATIONS AS RAN (CRITICAL!):
   This prevents Laravel from trying to run them again.

   php artisan tinker
   >>> $migrations = [
   >>>     // Copy the list from your consolidated migration file
   >>> ];
   >>> foreach($migrations as $m) {
   >>>     DB::table('migrations')->insertOrIgnore(['migration' => $m, 'batch' => 999]);
   >>> }
   >>> exit

5️⃣  VERIFY MIGRATION STATUS:
   php artisan migrate:status | tail -20

   You should see:
   - All old migrations marked as "Ran"
   - New consolidated migration as "Pending"

6️⃣  RUN THE CONSOLIDATION:
   php artisan migrate --force

7️⃣  VERIFY SUCCESS:
   - Check application is working
   - Check database tables exist
   - Run key functionality tests

8️⃣  IF SOMETHING GOES WRONG:
   mysql goadmin_db < pre_deploy_backup_[timestamp].sql
   git checkout HEAD~1
   php artisan config:cache

DEPLOY_GUIDE

echo ""
echo "📝 Creating deployment verification script..."

cat > database/consolidation/verify_deployment.sh << 'VERIFY'
#!/bin/bash

echo "🔍 Verifying Production Deployment"
echo "=================================="

PRODUCTION_HOST="root@138.197.188.120"

echo ""
echo "1. Checking migration status..."
ssh $PRODUCTION_HOST "cd /var/www/go-adminpanel && php artisan migrate:status | grep -E '(initial_schema_consolidated|No migrations)' | head -5"

echo ""
echo "2. Checking table count..."
ssh $PRODUCTION_HOST "mysql -N goadmin_db -e 'SELECT COUNT(*) as table_count FROM information_schema.tables WHERE table_schema=\"goadmin_db\"'"

echo ""
echo "3. Checking key tables..."
TABLES=("users" "restaurants" "orders" "business_settings")
for table in "${TABLES[@]}"; do
    count=$(ssh $PRODUCTION_HOST "mysql -N goadmin_db -e 'SELECT COUNT(*) FROM $table' 2>/dev/null" || echo "ERROR")
    echo "   - $table: $count records"
done

echo ""
echo "4. Checking application status..."
curl -s -o /dev/null -w "   HTTP Status: %{http_code}\n" https://your-domain.com

echo ""
echo "✅ Verification complete!"
VERIFY

chmod +x database/consolidation/verify_deployment.sh

echo ""
echo "🎯 DEPLOYMENT READINESS:"
echo "========================"
echo ""
echo "Your consolidation package is ready for deployment!"
echo ""
echo "📁 Files to deploy:"
echo "   - database/migrations/2014_01_01_000000_initial_schema_consolidated.php"
echo "   - database/schema/mysql-schema.sql"
echo "   - Archived migrations (keep but don't deploy)"
echo ""
echo "⚡ After deployment, your system will:"
echo "   - Start faster (1 migration vs 334)"
echo "   - Deploy quicker"
echo "   - Be easier to maintain"
echo ""
echo "📞 Support files created:"
echo "   - verify_deployment.sh (check if deployment worked)"
echo "   - backups/* (your safety net)"
echo ""
echo "Good luck with your deployment! 🚀"