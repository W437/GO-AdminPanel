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
