#!/bin/bash

# ==========================================
# MASTER CONSOLIDATION RUNNER
# ==========================================
# This script guides you through the entire consolidation process

set -e

echo "╔════════════════════════════════════════════╗"
echo "║   LARAVEL MIGRATION CONSOLIDATION TOOL     ║"
echo "║          334 → 1 Migration File            ║"
echo "╚════════════════════════════════════════════╝"
echo ""

# Make all scripts executable
chmod +x database/consolidation/*.sh

echo "This tool will help you consolidate 334 migration files safely."
echo ""
echo "Choose your path:"
echo "=================="
echo ""
echo "1) 🚀 QUICK MODE - Run all steps automatically (recommended)"
echo "2) 📋 GUIDED MODE - Run each step with explanations"
echo "3) 🔧 MANUAL MODE - Choose individual steps"
echo "4) 📖 READ DOCUMENTATION - View the README"
echo "5) ❌ EXIT"
echo ""
read -p "Select option (1-5): " MODE

case $MODE in
    1)
        echo ""
        echo "🚀 QUICK MODE"
        echo "============="
        echo "This will run steps 1-3 automatically."
        echo ""
        read -p "Continue? (yes/no): " CONFIRM
        if [ "$CONFIRM" == "yes" ]; then
            echo ""
            echo "Step 1: Backing up..."
            ./database/consolidation/step1_backup.sh

            echo ""
            echo "Step 2: Analyzing..."
            ./database/consolidation/step2_analyze.sh

            echo ""
            echo "Step 3: Consolidating..."
            ./database/consolidation/step3_consolidate.sh

            echo ""
            echo "✅ Consolidation complete!"
            echo ""
            echo "Next: Test with ./database/consolidation/step4_database_setup.sh"
        fi
        ;;

    2)
        echo ""
        echo "📋 GUIDED MODE"
        echo "=============="
        echo ""

        echo "Step 1: BACKUP"
        echo "--------------"
        echo "This creates comprehensive backups of your database and migrations."
        read -p "Run backup? (yes/no): " RUN1
        if [ "$RUN1" == "yes" ]; then
            ./database/consolidation/step1_backup.sh
        fi

        echo ""
        echo "Step 2: ANALYZE"
        echo "---------------"
        echo "This analyzes your 334 migrations to understand the structure."
        read -p "Run analysis? (yes/no): " RUN2
        if [ "$RUN2" == "yes" ]; then
            ./database/consolidation/step2_analyze.sh
        fi

        echo ""
        echo "Step 3: CONSOLIDATE"
        echo "-------------------"
        echo "This creates the actual consolidated migration file."
        read -p "Run consolidation? (yes/no): " RUN3
        if [ "$RUN3" == "yes" ]; then
            ./database/consolidation/step3_consolidate.sh
        fi

        echo ""
        echo "Step 4: TEST"
        echo "------------"
        echo "This helps you test the consolidation locally."
        read -p "Run test setup? (yes/no): " RUN4
        if [ "$RUN4" == "yes" ]; then
            ./database/consolidation/step4_database_setup.sh
        fi

        echo ""
        echo "✅ Guided process complete!"
        ;;

    3)
        echo ""
        echo "🔧 MANUAL MODE"
        echo "=============="
        echo ""
        echo "Available scripts:"
        echo "1) step1_backup.sh - Create backups"
        echo "2) step2_analyze.sh - Analyze migrations"
        echo "3) step3_consolidate.sh - Create consolidation"
        echo "4) step4_database_setup.sh - Test setup"
        echo "5) step5_deploy_production.sh - Deployment guide"
        echo ""
        read -p "Which step? (1-5): " STEP

        case $STEP in
            1) ./database/consolidation/step1_backup.sh ;;
            2) ./database/consolidation/step2_analyze.sh ;;
            3) ./database/consolidation/step3_consolidate.sh ;;
            4) ./database/consolidation/step4_database_setup.sh ;;
            5) ./database/consolidation/step5_deploy_production.sh ;;
            *) echo "Invalid option" ;;
        esac
        ;;

    4)
        echo ""
        cat database/consolidation/README.md | less
        ;;

    5)
        echo "Exiting..."
        exit 0
        ;;

    *)
        echo "Invalid option"
        exit 1
        ;;
esac

echo ""
echo "═══════════════════════════════════════════════"
echo "For more help, see: database/consolidation/README.md"