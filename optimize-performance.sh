#!/bin/bash
# Laravel Performance Optimization Script for Railway
# Run this on Railway after each deployment

echo "🚀 Starting Laravel Performance Optimization..."

# Clear all caches first
echo "📦 Clearing old caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild optimized caches
echo "⚡ Building optimized caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Optimize Composer autoloader
echo "📚 Optimizing Composer autoloader..."
composer install --optimize-autoloader --no-dev

# Queue worker reminder
echo ""
echo "✅ Optimization complete!"
echo ""
echo "⚠️  IMPORTANT: Make sure you have a Redis queue worker running:"
echo "    php artisan queue:work redis --sleep=3 --tries=3 --max-time=3600"
echo ""
echo "💡 TIP: On Railway, add this as a background process in your Procfile"
