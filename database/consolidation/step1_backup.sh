#!/bin/bash

# ==========================================
# STEP 1: COMPLETE BACKUP
# ==========================================
# This script creates comprehensive backups before consolidation
# Run this FIRST before any other steps!

set -e  # Exit on any error

# Configuration
BACKUP_DIR="database/consolidation/backups"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
PRODUCTION_HOST="root@138.197.188.120"
PRODUCTION_DB="goadmin_db"
LOCAL_DB="go_adminpanel" # Update this with your local DB name

echo "🔐 STEP 1: Creating Complete Backups"
echo "====================================="

# Create backup directory
mkdir -p $BACKUP_DIR
mkdir -p $BACKUP_DIR/migrations_archive

echo "📁 Created backup directory: $BACKUP_DIR"

# 1. Backup all migration files
echo ""
echo "📝 Backing up migration files..."
cp -r database/migrations/*.php $BACKUP_DIR/migrations_archive/
echo "✅ Backed up $(ls database/migrations/*.php | wc -l) migration files"

# 2. Backup production database WITH DATA
echo ""
echo "💾 Backing up production database (with data)..."
ssh $PRODUCTION_HOST "mysqldump --single-transaction --routines --triggers --events $PRODUCTION_DB" > $BACKUP_DIR/production_full_${TIMESTAMP}.sql
echo "✅ Production database backed up to: production_full_${TIMESTAMP}.sql"

# 3. Backup production schema only (no data)
echo ""
echo "📋 Backing up production schema..."
ssh $PRODUCTION_HOST "mysqldump --no-data --single-transaction $PRODUCTION_DB" > $BACKUP_DIR/production_schema_${TIMESTAMP}.sql
echo "✅ Production schema backed up to: production_schema_${TIMESTAMP}.sql"

# 4. Export migrations table to know what has run
echo ""
echo "📊 Exporting migrations table..."
ssh $PRODUCTION_HOST "mysql $PRODUCTION_DB -e 'SELECT * FROM migrations'" > $BACKUP_DIR/migrations_table_${TIMESTAMP}.txt
echo "✅ Migrations table exported"

# 5. Count statistics
echo ""
echo "📈 Current Statistics:"
echo "----------------------"
TOTAL_MIGRATIONS=$(ls database/migrations/*.php | wc -l)
TABLES_COUNT=$(ssh $PRODUCTION_HOST "mysql -N $PRODUCTION_DB -e 'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = \"$PRODUCTION_DB\"'")
echo "Total migration files: $TOTAL_MIGRATIONS"
echo "Total database tables: $TABLES_COUNT"
echo "Backup size: $(du -h $BACKUP_DIR/production_full_${TIMESTAMP}.sql | cut -f1)"

# 6. Create restore script
cat > $BACKUP_DIR/restore_if_needed.sh << 'EOF'
#!/bin/bash
# Emergency restore script - use if something goes wrong

echo "⚠️  WARNING: This will restore the database to its backup state!"
read -p "Are you sure? (yes/no): " confirm

if [ "$confirm" == "yes" ]; then
    echo "Restoring database..."
    # Update these variables
    BACKUP_FILE="production_full_TIMESTAMP.sql"
    mysql -u root -p go_adminpanel < $BACKUP_FILE
    echo "✅ Database restored from backup"
else
    echo "Restore cancelled"
fi
EOF

chmod +x $BACKUP_DIR/restore_if_needed.sh

echo ""
echo "✅ BACKUP COMPLETE!"
echo ""
echo "📁 All backups saved in: $BACKUP_DIR"
echo "🚨 Keep these backups safe until consolidation is verified!"
echo ""
echo "Next step: Run ./step2_analyze.sh"