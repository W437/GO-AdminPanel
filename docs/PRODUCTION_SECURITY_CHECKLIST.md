# Production Security Checklist

## 🔒 Essential Security Steps for Production Deployment

This checklist ensures your GO Admin Panel is secure when deployed to production (DigitalOcean or any hosting).

---

## ✅ Before Going Live

### **1. Regenerate OAuth Passport Clients** 🔴 **CRITICAL**

**Why:** Default OAuth secrets in SQL dump are shared with all CodeCanyon customers.

**Command:**
```bash
ssh root@your-server
cd /var/www/GO-AdminPanel
php artisan passport:install --force
```

**Result:** Creates unique OAuth client secrets for your installation.

**Mobile Apps:** ✅ No updates needed - they use Personal Access Tokens

---

### **2. Regenerate Application Key** 🔴 **CRITICAL**

**Why:** APP_KEY is used for encryption. Default key is insecure.

**Command:**
```bash
php artisan key:generate --force
```

**Result:** New unique encryption key in `.env`

**Warning:** Will invalidate existing sessions/encrypted data!

---

### **3. Environment Configuration** 🔴 **CRITICAL**

**Update `.env` file:**

```env
# Application
APP_ENV=production          # NOT 'live' or 'local'
APP_DEBUG=false            # NEVER true in production!
APP_MODE=live              # Application mode

# Database (use your production credentials)
DB_CONNECTION=mysql
DB_HOST=localhost          # or your DB host
DB_PORT=3306
DB_DATABASE=your_prod_db
DB_USERNAME=your_db_user
DB_PASSWORD=strong_password_here

# Cache & Sessions (use Redis in production)
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null        # or set Redis password
REDIS_PORT=6379
```

---

### **4. Storage Configuration** 🟡 **IMPORTANT**

**For DigitalOcean Spaces (Recommended):**

```env
# S3/Spaces Configuration
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your_do_spaces_key
AWS_SECRET_ACCESS_KEY=your_do_spaces_secret
AWS_DEFAULT_REGION=nyc3    # or sgp1, fra1, etc.
AWS_BUCKET=your-space-name
AWS_ENDPOINT=https://nyc3.digitaloceanspaces.com
AWS_ACL=public-read        # DO Spaces supports ACLs
```

**Get Credentials:** DigitalOcean Control Panel → API → Spaces Keys

See `docs/S3_STORAGE_SETUP.md` for detailed setup.

---

### **5. Database Security** 🟡 **IMPORTANT**

**Remove Default/Test Data:**

```bash
# Delete test admin if exists
mysql -u root -p your_db -e "DELETE FROM admins WHERE email = 'admin@admin.com' AND id = 1;"

# Create your production admin
php artisan db:seed --class=AdminSeeder
# Or create via installation wizard
```

**Secure MySQL:**
```bash
mysql_secure_installation
# Set root password
# Remove anonymous users
# Disallow root remote login
# Remove test database
```

---

### **6. File Permissions** 🟡 **IMPORTANT**

```bash
# Set correct ownership
chown -R www-data:www-data /var/www/GO-AdminPanel

# Set correct permissions
find /var/www/GO-AdminPanel -type f -exec chmod 644 {} \;
find /var/www/GO-AdminPanel -type d -exec chmod 755 {} \;

# Storage and cache must be writable
chmod -R 775 storage bootstrap/cache
chgrp -R www-data storage bootstrap/cache
```

---

### **7. SSL Certificate** 🔴 **CRITICAL**

**Install Let's Encrypt (Free SSL):**

```bash
# Install Certbot
apt install certbot python3-certbot-nginx

# Get certificate
certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Auto-renewal is configured automatically
```

**Result:** HTTPS enabled, secure connections for all traffic.

---

### **8. Disable Debug Tools** 🔴 **CRITICAL**

**In `.env`:**
```env
APP_DEBUG=false
```

**Remove Debug Bar (if installed):**
```bash
composer remove barryvdh/laravel-debugbar --dev
```

---

### **9. Configure CORS** 🟡 **IMPORTANT**

**For Mobile App API Access:**

Edit `config/cors.php`:
```php
'allowed_origins' => ['*'],  // Or specify your mobile app domains
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
```

---

### **10. Queue Worker** 🟡 **IMPORTANT**

**Set up supervisor to run queue workers:**

```bash
# Install supervisor
apt install supervisor

# Create config
nano /etc/supervisor/conf.d/laravel-worker.conf
```

**Config file:**
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/GO-AdminPanel/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/GO-AdminPanel/storage/logs/worker.log
stopwaitsecs=3600
```

**Start worker:**
```bash
supervisorctl reread
supervisorctl update
supervisorctl start laravel-worker:*
```

---

## 🔍 Security Verification Checklist

After deployment, verify:

- [ ] `APP_DEBUG=false` in .env
- [ ] `APP_ENV=production` in .env
- [ ] New APP_KEY generated
- [ ] OAuth clients regenerated (check oauth_clients table)
- [ ] HTTPS working (SSL certificate installed)
- [ ] Default admin password changed
- [ ] File permissions correct (not 777)
- [ ] Redis password set (if exposed to internet)
- [ ] MySQL only accessible from localhost
- [ ] Firewall configured (ufw)
- [ ] Queue workers running
- [ ] DigitalOcean Spaces configured
- [ ] Error logs not publicly accessible

---

## 🚨 Common Security Mistakes

### **❌ DON'T:**
- Leave APP_DEBUG=true in production
- Use default admin credentials
- Expose .env file publicly
- Run without HTTPS
- Skip Passport regeneration
- Use default encryption keys
- Set file permissions to 777
- Expose Redis without password

### **✅ DO:**
- Use strong passwords everywhere
- Enable HTTPS/SSL
- Regenerate all default secrets
- Set restrictive file permissions
- Monitor error logs
- Keep Laravel and dependencies updated
- Use environment-specific .env files
- Regular backups

---

## 📊 Security Best Practices

### **Server Level:**
```bash
# Enable firewall
ufw allow 22    # SSH
ufw allow 80    # HTTP
ufw allow 443   # HTTPS
ufw enable

# Disable root SSH login (after creating sudo user)
nano /etc/ssh/sshd_config
# Set: PermitRootLogin no

# Install fail2ban (prevents brute force)
apt install fail2ban
systemctl enable fail2ban
```

### **Application Level:**
```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set strict permissions
chmod 600 .env
chmod 644 composer.json
```

---

## 🔑 Credentials to Change

| Item | Location | Action |
|------|----------|--------|
| APP_KEY | .env | `php artisan key:generate --force` |
| OAuth Clients | Database | `php artisan passport:install --force` |
| Admin Password | Database/Panel | Change via admin panel |
| DB Password | .env + MySQL | Strong password, not 'root' |
| Redis Password | .env + Redis config | Set if exposed |
| Spaces Keys | .env | Use production DO Spaces keys |
| Mail Credentials | .env | Production SMTP credentials |

---

## 📱 Mobile App Configuration

**What to update in mobile apps for production:**

```javascript
// In your mobile app config:
const API_BASE_URL = 'https://yourdomain.com/api/v1';  // ← Change this

// OAuth secrets? NO - NOT needed!
// The app only needs the API URL
```

---

## 🧪 Testing Production Deployment

### **Before Launching:**
```bash
# Test SSL
curl -I https://yourdomain.com

# Test API
curl https://yourdomain.com/api/v1/config

# Test mobile app login
# (use Postman or mobile app)

# Check logs for errors
tail -f storage/logs/laravel.log
```

---

## 📞 Support Resources

- Laravel Security: https://laravel.com/docs/security
- DigitalOcean Tutorials: https://www.digitalocean.com/community/tags/laravel
- Passport Docs: https://laravel.com/docs/passport

---

**Created:** November 8, 2025
**Last Updated:** November 8, 2025
**Version:** 8.5

---

## Quick Reference

**Essential Commands After Deployment:**
```bash
# On production server
php artisan key:generate --force
php artisan passport:install --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Mobile Apps:** ✅ No changes needed after Passport regeneration!
