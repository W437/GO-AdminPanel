#!/bin/bash

# ==========================================
# STEP 4: DATABASE QUICK SETUP
# ==========================================
# This script provides a fast way to set up a working database

set -e

echo "🚀 STEP 4: Database Quick Setup"
echo "================================"
echo ""

# Configuration
PRODUCTION_HOST="root@138.197.188.120"
PRODUCTION_DB="goadmin_db"

# Menu
echo "Choose setup option:"
echo "--------------------"
echo "1) Fresh install (empty database with schema only)"
echo "2) Development setup (schema + sample data)"
echo "3) Production clone (exact copy of production)"
echo "4) Minimal working setup (schema + essential data only)"
echo ""
read -p "Select option (1-4): " OPTION

read -p "Enter local database name: " LOCAL_DB
read -p "Enter MySQL username (default: root): " DB_USER
DB_USER=${DB_USER:-root}

echo ""
echo "⚠️  This will DROP and RECREATE database: $LOCAL_DB"
read -p "Continue? (yes/no): " CONFIRM

if [ "$CONFIRM" != "yes" ]; then
    echo "Cancelled"
    exit 1
fi

# Drop and recreate database
echo "🗄️  Setting up database..."
mysql -u $DB_USER -p << EOF
DROP DATABASE IF EXISTS $LOCAL_DB;
CREATE DATABASE $LOCAL_DB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE $LOCAL_DB;
EOF

case $OPTION in
    1)
        echo "📋 Option 1: Fresh Install"
        echo "-------------------------"
        # Run migrations only
        php artisan migrate --database=mysql --force

        echo "✅ Empty database with schema created!"
        ;;

    2)
        echo "🔧 Option 2: Development Setup"
        echo "------------------------------"

        # Run migrations
        php artisan migrate --database=mysql --force

        # Create development seeder if it doesn't exist
        if [ ! -f "database/seeders/QuickDevSeeder.php" ]; then
            cat > database/seeders/QuickDevSeeder.php << 'SEEDER'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class QuickDevSeeder extends Seeder
{
    public function run()
    {
        echo "🌱 Seeding development data...\n";

        // 1. Create admin user
        DB::table('admins')->insert([
            'f_name' => 'Admin',
            'l_name' => 'User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'phone' => '1234567890',
            'role_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✅ Admin user created (admin@example.com / password)\n";

        // 2. Create test customer
        DB::table('users')->insert([
            'f_name' => 'Test',
            'l_name' => 'Customer',
            'email' => 'customer@example.com',
            'phone' => '9876543210',
            'password' => Hash::make('password'),
            'is_phone_verified' => 1,
            'is_email_verified' => 1,
            'profile_emoji' => '😊',
            'profile_bg_color' => '#4CAF50',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✅ Test customer created (customer@example.com / password)\n";

        // 3. Create essential business settings
        $settings = [
            ['key' => 'business_name', 'value' => 'GO Admin Panel Dev'],
            ['key' => 'currency', 'value' => 'USD'],
            ['key' => 'currency_symbol_position', 'value' => 'left'],
            ['key' => 'country', 'value' => 'US'],
            ['key' => 'time_zone', 'value' => 'America/New_York'],
            ['key' => 'phone', 'value' => '1234567890'],
            ['key' => 'email', 'value' => 'admin@example.com'],
            ['key' => 'maintenance_mode', 'value' => '0'],
        ];

        foreach ($settings as $setting) {
            DB::table('business_settings')->insertOrIgnore([
                'key' => $setting['key'],
                'value' => $setting['value'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        echo "✅ Business settings configured\n";

        // 4. Create a test zone
        DB::table('zones')->insert([
            'id' => 1,
            'name' => 'Default Zone',
            'coordinates' => json_encode([
                ['lat' => 40.7128, 'lng' => -74.0060],
                ['lat' => 40.7614, 'lng' => -73.9776],
                ['lat' => 40.7489, 'lng' => -73.9442],
                ['lat' => 40.7074, 'lng' => -73.9866],
            ]),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✅ Default zone created\n";

        echo "\n🎉 Development seeding complete!\n";
    }
}
SEEDER
        fi

        # Run seeder
        php artisan db:seed --class=QuickDevSeeder

        echo "✅ Development database ready with sample data!"
        ;;

    3)
        echo "📦 Option 3: Production Clone"
        echo "-----------------------------"

        # Get full production backup
        echo "Downloading production data..."
        ssh $PRODUCTION_HOST "mysqldump --single-transaction $PRODUCTION_DB" | mysql -u $DB_USER -p $LOCAL_DB

        echo "✅ Production database cloned locally!"
        ;;

    4)
        echo "⚡ Option 4: Minimal Working Setup"
        echo "----------------------------------"

        # Run migrations
        php artisan migrate --database=mysql --force

        # Get only essential data from production
        echo "Importing essential data only..."

        ESSENTIAL_TABLES=(
            "business_settings"
            "currencies"
            "zones"
            "translations"
            "addon_settings"
            "email_verifications"
            "password_resets"
        )

        for table in "${ESSENTIAL_TABLES[@]}"; do
            echo "Importing $table..."
            ssh $PRODUCTION_HOST "mysqldump --single-transaction $PRODUCTION_DB $table" | mysql -u $DB_USER -p $LOCAL_DB
        done

        # Create minimal admin user
        mysql -u $DB_USER -p $LOCAL_DB << 'SQL'
INSERT IGNORE INTO admins (id, f_name, l_name, email, password, role_id, created_at)
VALUES (1, 'Admin', 'User', 'admin@localhost', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW());
SQL

        echo "✅ Minimal database ready with essential data!"
        echo "📧 Login: admin@localhost / password"
        ;;
esac

# Show database stats
echo ""
echo "📊 Database Statistics:"
echo "----------------------"
TABLE_COUNT=$(mysql -u $DB_USER -p -N $LOCAL_DB -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$LOCAL_DB'")
echo "Tables created: $TABLE_COUNT"

if [ "$OPTION" != "1" ]; then
    USER_COUNT=$(mysql -u $DB_USER -p -N $LOCAL_DB -e "SELECT COUNT(*) FROM users" 2>/dev/null || echo "0")
    ADMIN_COUNT=$(mysql -u $DB_USER -p -N $LOCAL_DB -e "SELECT COUNT(*) FROM admins" 2>/dev/null || echo "0")
    echo "Users: $USER_COUNT"
    echo "Admins: $ADMIN_COUNT"
fi

echo ""
echo "✅ DATABASE SETUP COMPLETE!"
echo ""
echo "Your $LOCAL_DB database is ready to use!"