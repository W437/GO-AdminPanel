# GO Admin Panel Documentation

Welcome to the GO Admin Panel documentation! This comprehensive food delivery management system is built with Laravel 10 and provides powerful tools for managing restaurants, orders, deliveries, and more.

## 📚 Documentation Index

### Getting Started
- **[Local Development Guide](LOCAL_DEVELOPMENT.md)** - How to run the server locally
- **[Hosting & Deployment Guide](HOSTING_DEPLOYMENT.md)** - Production deployment options
- **[Redis Deployment Guide](REDIS_DEPLOYMENT.md)** - Redis installation and configuration
- **[Performance Optimization Guide](PERFORMANCE_OPTIMIZATION_GUIDE.md)** - Performance tuning tips
- **[Quick Deploy Checklist](QUICK_DEPLOY_CHECKLIST.md)** - Deployment checklist

### Additional Documentation
- **[Story System Plan](story_system_plan.md)** - Story feature planning
- **[Story System Summary](story_system_summary.md)** - Story feature summary
- **[Deployment Guide](deployment-guide.md)** - General deployment guide

## 🚀 Quick Start

### Start Local Development Server
```bash
# Navigate to project directory
cd /Users/drvanhoover/Documents/GitHub/GO-AdminPanel

# Start MySQL service
brew services start mysql

# Start Laravel development server-
php artisan serve
```

**Access your application at:** http://localhost:8000

## 🏗️ System Architecture

### Technology Stack
- **Backend Framework**: Laravel 10 (PHP 8.1+)
- **Frontend**: Laravel Mix + Vue.js components
- **Database**: MySQL 5.7+
- **Caching**: Redis (optional)
- **Queue System**: Laravel Queues
- **File Storage**: Local/AWS S3/DigitalOcean Spaces

### Key Features
- 🏪 **Restaurant Management** - Complete restaurant profiles and menus
- 📱 **Order Management** - Real-time order tracking and processing
- 🚚 **Delivery System** - Delivery personnel management and tracking
- 💳 **Payment Integration** - Multiple payment gateways (Stripe, PayPal, Razorpay)
- 📊 **Analytics & Reporting** - Comprehensive business insights
- 🔔 **Notification System** - Push notifications and email alerts
- 👥 **User Management** - Customers, restaurants, and delivery personnel
- 🎯 **Campaign Management** - Promotions and marketing campaigns
- 📍 **Zone Management** - Delivery area configuration
- 💰 **Wallet System** - Digital wallet and loyalty points

## 🛠️ Development

### Project Structure
```
├── app/                    # Application logic
├── resources/views/        # Blade templates
├── public/                # Public assets
├── database/
│   ├── migrations/        # Database migrations
│   ├── scripts/          # SQL scripts and data imports
│   └── seeders/          # Database seeders
├── routes/                # Route definitions
├── config/                # Configuration files
├── scripts/               # Utility scripts
└── docs/                  # Documentation (this folder)
```

### Environment Requirements
- PHP 8.1+ (Currently using PHP 8.4.12)
- MySQL 5.7+ (Currently using MySQL 9.4.0)
- Node.js 16+ (Currently using Node.js v22.17.0)
- Composer 2.0+

## 🌐 Hosting Options

### Recommended for Beginners
1. **DigitalOcean App Platform** - $12-25/month
2. **Laravel Forge + VPS** - $17-32/month
3. **Shared Hosting** - $3-15/month

### Enterprise Solutions
1. **AWS Elastic Beanstalk** - $20-100+/month
2. **Google Cloud Platform** - Variable pricing
3. **Custom VPS Setup** - $5-50+/month

## 📖 Additional Resources

### Laravel Resources
- [Laravel Documentation](https://laravel.com/docs/10.x)
- [Laravel Bootcamp](https://bootcamp.laravel.com/)
- [Laracasts](https://laracasts.com/) - Video tutorials

### Community & Support
- [Laravel Community](https://laravel.com/community)
- [GitHub Issues](https://github.com/your-repo/issues) - Report bugs
- [Stack Overflow](https://stackoverflow.com/questions/tagged/laravel)

## 🔧 Troubleshooting

### Common Issues
1. **Server won't start** → Check if port 8000 is available
2. **Database connection failed** → Verify MySQL is running
3. **Permission denied** → Fix storage folder permissions
4. **Assets not loading** → Run `npm run dev` to rebuild

### Getting Help
1. Check the documentation in this folder
2. Review Laravel official documentation
3. Search existing GitHub issues
4. Ask in Laravel community forums

## 🚀 What's Next?

1. **Read the Local Development Guide** to understand how to start the server
2. **Review the Hosting Guide** when you're ready to deploy to production
3. **Explore the codebase** to understand the application structure
4. **Customize the application** to fit your specific needs

---

**Happy coding! 🎉**

For detailed setup instructions, see [LOCAL_DEVELOPMENT.md](LOCAL_DEVELOPMENT.md)
For deployment options, see [HOSTING_DEPLOYMENT.md](HOSTING_DEPLOYMENT.md)
