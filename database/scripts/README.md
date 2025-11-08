# Database Scripts

This directory contains SQL scripts for database operations, data imports, and migrations.

## Scripts

### Data Import Scripts
- `add_menu_items.sql` - Menu items data
- `add_restaurants_by_zone.sql` - Restaurant data organized by zones
- `complete_menu_items_batch.sql` - Batch menu items import
- `food_categories.sql` - Food categories data
- `israeli_zones.sql` - Zone data for Israel
- `remaining_menu_items.sql` - Additional menu items data

## Usage

These scripts can be imported into your database using:

```bash
# Import a specific script
mysql -u root -p go-server < database/scripts/your_script.sql

# Or from MySQL command line
mysql -u root -p
USE go-server;
SOURCE database/scripts/your_script.sql;
```

## Warning

⚠️ **Always backup your database before running these scripts!**

```bash
mysqldump -u root -p go-server > backup_before_import.sql
```

## Notes

- These scripts may contain sample/demo data
- Review scripts before importing to production
- Some scripts may reference specific IDs that need to exist first

