# DigitalOcean Deployment - Complete Setup Guide

## 🎉 Deployment Status: COMPLETE

Your GO Admin Panel is now deployed and running on DigitalOcean!

---

## 📍 Server Information

**Server IP:** `138.197.188.120`
**SSH Access:** `ssh root@138.197.188.120`
**Web Access:** `http://138.197.188.120`

---

## 🗄️ Database Credentials

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=goadmin_db
DB_USERNAME=goadmin_user
DB_PASSWORD=GoAdmin2025!Secure
```

**Note:** These credentials are configured in `/var/www/go-adminpanel/.env` on the server.

---

## 📂 Project Location

- **Web Root:** `/var/www/go-adminpanel`
- **Public Directory:** `/var/www/go-adminpanel/public`
- **Environment File:** `/var/www/go-adminpanel/.env`
- **Logs:** `/var/www/go-adminpanel/storage/logs/`

---

## ✅ Installed Services

### 1. **Web Stack**
- ✅ Apache 2.4.64 (with mod_rewrite enabled)
- ✅ PHP 8.4.11 (with all required extensions)
- ✅ MySQL 8.4.6
- ✅ Composer 2.8.12

### 2. **PHP Extensions**
- ✅ BCMath, Ctype, Fileinfo, GD, JSON, Mbstring
- ✅ OpenSSL, PDO, Sodium, Tokenizer, XML, Zip
- ✅ MySQL PDO, cURL

### 3. **Supporting Services**
- ✅ Redis 8.0.2 (caching & sessions)
- ✅ Supervisor 4.2.5 (queue workers)
- ✅ Git 2.51.0

### 4. **Queue Workers**
- ✅ 2 Laravel queue workers running via Supervisor
- ✅ Auto-restart on failure
- ✅ Logs: `/var/www/go-adminpanel/storage/logs/worker.log`

---

## 🚀 Auto-Deployment Setup

### Deployment Script

A deployment script has been created at `/var/www/deploy.sh`

**What it does:**
1. Puts site in maintenance mode
2. Pulls latest code from GitHub (`main` branch)
3. Installs/updates Composer dependencies
4. Clears and rebuilds all caches
5. Sets proper file permissions
6. Restarts queue workers
7. Takes site out of maintenance mode

### Manual Deployment

To deploy updates manually:

```bash
ssh root@138.197.188.120
bash /var/www/deploy.sh
```

### Automatic Deployment with GitHub Webhooks

To trigger deployment automatically when you push to GitHub:

#### Option 1: Using GitHub Actions (Recommended)

1. In your GitHub repo, go to **Settings → Secrets and variables → Actions**
2. Add these secrets:
   - `DEPLOY_HOST`: `138.197.188.120`
   - `DEPLOY_USER`: `root`
   - `DEPLOY_KEY`: (paste contents of `/root/.ssh/id_ed25519` from server)

3. Create `.github/workflows/deploy.yml`:

```yaml
name: Deploy to DigitalOcean

on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - name: Deploy to Production
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.DEPLOY_HOST }}
          username: ${{ secrets.DEPLOY_USER }}
          key: ${{ secrets.DEPLOY_KEY }}
          script: |
            bash /var/www/deploy.sh
```

#### Option 2: Using a Webhook Endpoint

Create a simple webhook endpoint that triggers the deployment script. (Requires additional setup)

---

## 🔑 SSH Deploy Key

A deploy key has been generated and added to your GitHub repository:

```
ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIJOBQuRKxugflHPo6GPnHz+kNEMjjhY3c/pV6oFT78Ck deploy@go-adminpanel
```

This allows the server to pull code from your private repository.

---

## 🔧 Common Operations

### View Apache Logs
```bash
ssh root@138.197.188.120
tail -f /var/log/apache2/go-adminpanel-error.log
tail -f /var/log/apache2/go-adminpanel-access.log
```

### View Laravel Logs
```bash
ssh root@138.197.188.120
tail -f /var/www/go-adminpanel/storage/logs/laravel.log
```

### View Queue Worker Logs
```bash
ssh root@138.197.188.120
tail -f /var/www/go-adminpanel/storage/logs/worker.log
```

### Restart Services
```bash
ssh root@138.197.188.120

# Restart Apache
systemctl restart apache2

# Restart Queue Workers
supervisorctl restart go-adminpanel-worker:*

# Restart Redis
systemctl restart redis-server
```

### Clear Laravel Caches
```bash
ssh root@138.197.188.120
cd /var/www/go-adminpanel
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Run Artisan Commands
```bash
ssh root@138.197.188.120
cd /var/www/go-adminpanel
php artisan [command]
```

---

## 🌐 Next Steps

### 1. **Configure Domain Name** (Optional but Recommended)

If you have a domain name:

1. **Add A Record** in your DNS:
   - Type: `A`
   - Name: `@` (or `admin` for subdomain)
   - Value: `138.197.188.120`
   - TTL: `3600`

2. **Update Apache VirtualHost:**
   ```bash
   ssh root@138.197.188.120
   nano /etc/apache2/sites-available/go-adminpanel.conf
   ```

   Change `ServerName 138.197.188.120` to `ServerName yourdomain.com`

   ```bash
   systemctl reload apache2
   ```

3. **Install SSL Certificate** (Let's Encrypt):
   ```bash
   ssh root@138.197.188.120
   apt install snapd -y
   snap install --classic certbot
   ln -s /snap/bin/certbot /usr/bin/certbot
   certbot --apache -d yourdomain.com -d www.yourdomain.com
   ```

### 2. **Configure Environment Variables**

Edit the production `.env` file:

```bash
ssh root@138.197.188.120
nano /var/www/go-adminpanel/.env
```

**Important settings to configure:**
- `APP_URL` - Your production domain
- `AWS_ACCESS_KEY_ID` - DigitalOcean Spaces key
- `AWS_SECRET_ACCESS_KEY` - DigitalOcean Spaces secret
- `AWS_DEFAULT_REGION` - DigitalOcean Spaces region (e.g., `fra1`)
- `AWS_BUCKET` - Your Spaces bucket name
- `AWS_ENDPOINT` - DigitalOcean Spaces endpoint (e.g., `https://fra1.digitaloceanspaces.com`)
- Mail settings (SMTP)
- Firebase credentials
- Payment gateway credentials

After editing, clear config cache:
```bash
php artisan config:cache
```

### 3. **Set Up DigitalOcean Spaces for File Storage**

1. Create a Space in DigitalOcean
2. Generate Spaces access keys
3. Update `.env` with credentials (as shown above)
4. Test upload from admin panel

### 4. **Configure Firewall** (Recommended)

```bash
ssh root@138.197.188.120

# Install UFW
apt install ufw -y

# Allow SSH, HTTP, HTTPS
ufw allow 22/tcp
ufw allow 80/tcp
ufw allow 443/tcp

# Enable firewall
ufw enable
```

### 5. **Set Up Backups**

Create automated database backups:

```bash
ssh root@138.197.188.120
crontab -e
```

Add this line (daily backup at 2 AM):
```
0 2 * * * mysqldump -u goadmin_user -p'GoAdmin2025!Secure' goadmin_db > /root/backups/goadmin_$(date +\%Y\%m\%d).sql
```

Create backup directory:
```bash
mkdir -p /root/backups
```

---

## 🔒 Security Checklist

- ✅ SSH key-based authentication enabled
- ⚠️ Root login via SSH enabled (consider creating non-root user)
- ⚠️ Firewall not configured (recommended: UFW)
- ⚠️ SSL not installed (recommended: Let's Encrypt)
- ✅ Database user has limited privileges
- ✅ `.env` file permissions set to 755
- ⚠️ Production database password saved in `.env` (secure file permissions required)

### Recommendations:
1. Set up SSL certificate with Let's Encrypt
2. Configure UFW firewall
3. Create non-root sudo user for SSH access
4. Disable root SSH login after creating sudo user
5. Set up automated backups to DigitalOcean Spaces or external storage
6. Enable fail2ban for SSH brute-force protection

---

## 📊 Admin Panel Access

**URL:** `http://138.197.188.120/admin`

**Default Credentials:**
- Check your database dump or run:
  ```bash
  ssh root@138.197.188.120
  cd /var/www/go-adminpanel
  php artisan db:seed --class=AdminSeeder
  ```

Or check `installation/backup/database_8.2.sql` for admin credentials.

---

## 🐛 Troubleshooting

### Site shows "500 Internal Server Error"

1. Check Apache error logs:
   ```bash
   tail -f /var/log/apache2/go-adminpanel-error.log
   ```

2. Check Laravel logs:
   ```bash
   tail -f /var/www/go-adminpanel/storage/logs/laravel.log
   ```

3. Ensure permissions are correct:
   ```bash
   cd /var/www/go-adminpanel
   chmod -R 777 storage bootstrap/cache
   chown -R www-data:www-data /var/www/go-adminpanel
   ```

### Database connection errors

1. Verify credentials in `.env`
2. Test database connection:
   ```bash
   mysql -u goadmin_user -p'GoAdmin2025!Secure' goadmin_db -e "SHOW TABLES;"
   ```

### Queue workers not processing

1. Check Supervisor status:
   ```bash
   supervisorctl status
   ```

2. Restart workers:
   ```bash
   supervisorctl restart go-adminpanel-worker:*
   ```

3. Check worker logs:
   ```bash
   tail -f /var/www/go-adminpanel/storage/logs/worker.log
   ```

---

## 📞 Support

For issues specific to:
- **Server setup:** Check DigitalOcean documentation
- **Laravel errors:** Check `/storage/logs/laravel.log`
- **GO Admin Panel features:** Refer to product documentation in `/docs/documentation/`

---

## 🎓 Additional Resources

- [Laravel Deployment Documentation](https://laravel.com/docs/deployment)
- [DigitalOcean Community Tutorials](https://www.digitalocean.com/community/tutorials)
- [Let's Encrypt SSL Setup](https://certbot.eff.org/)
- [Supervisor Documentation](http://supervisord.org/)

---

**Deployment completed on:** 2025-11-08
**Deployed by:** Claude Code (Anthropic)
