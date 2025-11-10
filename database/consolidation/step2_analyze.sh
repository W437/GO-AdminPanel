#!/bin/bash

# ==========================================
# STEP 2: ANALYZE MIGRATIONS
# ==========================================
# This script analyzes your migrations to understand what needs consolidation

set -e

echo "🔍 STEP 2: Analyzing Migrations"
echo "================================"

ANALYSIS_DIR="database/consolidation/analysis"
mkdir -p $ANALYSIS_DIR

# 1. Group migrations by year
echo ""
echo "📅 Migrations by Year:"
echo "----------------------"
for year in 2014 2015 2016 2017 2018 2019 2020 2021 2022 2023 2024 2025; do
    count=$(ls database/migrations/${year}_*.php 2>/dev/null | wc -l || echo "0")
    if [ "$count" -gt 0 ]; then
        printf "%s: %3d migrations\n" $year $count
    fi
done

# 2. Find migrations by table
echo ""
echo "📊 Analyzing table modifications..."
> $ANALYSIS_DIR/tables_analysis.txt

# Common table names to check
TABLES=(
    "users" "restaurants" "foods" "orders" "order_details"
    "categories" "zones" "delivery_men" "admins" "vendors"
    "reviews" "notifications" "banners" "coupons" "currencies"
    "business_settings" "translations" "add_ons"
)

echo "Table Name          | Migrations Count" >> $ANALYSIS_DIR/tables_analysis.txt
echo "-------------------|------------------" >> $ANALYSIS_DIR/tables_analysis.txt

for table in "${TABLES[@]}"; do
    count=$(grep -l "table('$table'" database/migrations/*.php 2>/dev/null | wc -l || echo "0")
    if [ "$count" -gt 0 ]; then
        printf "%-18s | %3d\n" $table $count >> $ANALYSIS_DIR/tables_analysis.txt
    fi
done

cat $ANALYSIS_DIR/tables_analysis.txt

# 3. Find create vs alter migrations
echo ""
echo "📈 Migration Types:"
echo "-------------------"
CREATE_COUNT=$(grep -l "Schema::create" database/migrations/*.php | wc -l)
ALTER_COUNT=$(grep -l "Schema::table" database/migrations/*.php | wc -l)
echo "CREATE table migrations: $CREATE_COUNT"
echo "ALTER table migrations: $ALTER_COUNT"

# 4. Identify problematic patterns
echo ""
echo "⚠️  Checking for potential issues..."
echo "------------------------------------"

# Check for raw SQL
RAW_SQL=$(grep -l "DB::statement\|DB::raw\|DB::unprepared" database/migrations/*.php | wc -l || echo "0")
if [ "$RAW_SQL" -gt 0 ]; then
    echo "Found $RAW_SQL migrations with raw SQL (need careful review)"
    grep -l "DB::statement\|DB::raw\|DB::unprepared" database/migrations/*.php > $ANALYSIS_DIR/raw_sql_migrations.txt
fi

# Check for data migrations (not just schema)
DATA_MIGRATIONS=$(grep -l "DB::table.*insert\|DB::table.*update\|Seeder" database/migrations/*.php | wc -l || echo "0")
if [ "$DATA_MIGRATIONS" -gt 0 ]; then
    echo "Found $DATA_MIGRATIONS migrations that modify data (need special handling)"
fi

# 5. Generate consolidation groups
echo ""
echo "📦 Suggested Consolidation Groups:"
echo "-----------------------------------"
cat > $ANALYSIS_DIR/consolidation_plan.txt << 'EOF'
Group 1: Core User System
- users table and related tables (addresses, wallets, etc.)

Group 2: Restaurant System
- restaurants, foods, categories, add-ons

Group 3: Order System
- orders, order_details, order_transactions

Group 4: Configuration
- business_settings, currencies, zones, translations

Group 5: Marketing & Analytics
- coupons, banners, reviews, notifications

Group 6: Recent Changes (2024-2025)
- Keep these as separate migrations for now
EOF

cat $ANALYSIS_DIR/consolidation_plan.txt

# 6. Create migration mapping
echo ""
echo "📝 Creating migration inventory..."
ls -la database/migrations/*.php | awk '{print $9}' > $ANALYSIS_DIR/all_migrations.txt
echo "✅ Full inventory saved to: $ANALYSIS_DIR/all_migrations.txt"

echo ""
echo "✅ ANALYSIS COMPLETE!"
echo ""
echo "📊 Analysis reports saved in: $ANALYSIS_DIR/"
echo ""
echo "Review the analysis, then run: ./step3_consolidate.sh"