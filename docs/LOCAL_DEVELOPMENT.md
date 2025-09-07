# StackFood Admin Panel - Local Development Guide

## Prerequisites

Before starting the server, ensure you have the following installed:

- **PHP 8.1+** (Currently using PHP 8.4.12)
- **MySQL 5.7+** (Currently using MySQL 9.4.0)
- **Node.js 16+** (Currently using Node.js v22.17.0)
- **Composer** (Currently using Composer 2.8.11)
- **npm** (Comes with Node.js)

## Starting the Server

### 1. Start Required Services

First, ensure MySQL is running:
```bash
# Start MySQL service
brew services start mysql

# Or if you prefer to run it manually:
# /opt/homebrew/opt/mysql/bin/mysqld_safe --datadir=/opt/homebrew/var/mysql
```

### 2. Start the Laravel Development Server

Navigate to your project directory and run:
```bash
cd /Users/drvanhoover/Documents/GitHub/GO-AdminPanel
php artisan serve
```

The server will start on **http://localhost:8000** or **http://127.0.0.1:8000**

### 3. Alternative Port (if 8000 is busy)

If port 8000 is already in use, specify a different port:
```bash
php artisan serve --port=8080
```

### 4. Make Server Accessible on Network

To access from other devices on your network:
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

## Development Workflow

### Building Assets
When making frontend changes, rebuild assets:
```bash
# For development (one-time build)
npm run dev

# For production (minified)
npm run prod

# Watch for changes (auto-rebuild)
npm run watch
```

### Database Operations
```bash
# Run new migrations
php artisan migrate

# Reset database and run all migrations
php artisan migrate:fresh

# Seed database with sample data
php artisan db:seed
```

### Clearing Caches
```bash
# Clear all caches
php artisan optimize:clear

# Or clear specific caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## Troubleshooting

### Server Won't Start
1. Check if port 8000 is already in use:
   ```bash
   lsof -i :8000
   ```
2. Kill any processes using the port or use a different port

### Database Connection Issues
1. Ensure MySQL is running: `brew services list | grep mysql`
2. Check database credentials in `.env` file
3. Test connection: `mysql -u root -p stackfood`

### Permission Issues
```bash
# Fix storage permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### PHP Deprecated Warnings
The warnings you see are normal for PHP 8.4 with older Laravel packages. They don't affect functionality but can be ignored for development.

## Environment Configuration

Key environment variables in `.env`:
```env
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stackfood
DB_USERNAME=root
DB_PASSWORD=
```

## Stopping the Server

Press `Ctrl+C` in the terminal where the server is running to stop it gracefully.
