# Deployment Guide - Railway Hosting

## Overview
This document covers deploying the GO Admin Panel Laravel application to Railway hosting platform.

## Railway Platform Features

### ✅ What Railway Provides:
- **24/7 Server Uptime** - Laravel app runs continuously
- **MySQL Database** - Persistent, always-on database
- **Custom Domain** - Use your own domain name
- **SSL Certificate** - HTTPS automatically enabled
- **Auto-scaling** - Handles traffic spikes
- **Monitoring** - App health and performance tracking

### 💰 Pricing:
- **Free Tier:** $5 credit monthly (good for testing)
- **Pro Plan:** $20/month for production apps
- **Usage-based:** Pay only for what you use

## Important: Ephemeral Storage

### ⚠️ What "Ephemeral Storage" Means:
Railway's file system is **temporary** and gets wiped clean every time your app restarts or redeploys.

### Files that get deleted on restart:
- User uploaded images (restaurant logos, food photos)
- Generated PDFs (invoices, reports)
- Log files
- Any files saved to `storage/app/public/`

### Files that are safe:
- Your code files
- Database data (stored separately)
- Environment variables

## Storage Solutions

### Option 1: Railway Persistent Volumes
Mount a volume in Railway settings to:
```
/app/storage/app/public
```

### Option 2: Cloud Storage (Recommended)
Use external storage services:
- **AWS S3** - Industry standard
- **Cloudinary** - Great for images
- **Railway's built-in storage** - Easiest

Update your `.env` file:
```env
FILESYSTEM_DISK=s3
# Add S3 credentials
```

## Files Requiring Persistent Storage

For the GO Admin Panel, you'll need persistent storage for:
- Restaurant logos
- Food item photos
- Invoice PDFs
- User profile pictures
- Menu item images
- Campaign banners

## Deployment Steps

1. **Push code to GitHub**
2. **Connect Railway to GitHub repo**
3. **Deploy automatically**
4. **Set up persistent storage for uploads**
5. **Configure custom domain (optional)**

## Database Migration

Export current database:
```bash
mysqldump -u root go-server > database_backup.sql
```

Import to Railway database after deployment.

## Environment Variables

Update these in Railway dashboard:
- `APP_URL` - Your Railway app URL
- `DB_*` - Railway will provide these automatically
- Storage credentials (if using S3)

## Custom Domain Setup

### Step 1: In Railway Dashboard
1. Go to your deployed project
2. Click **"Settings"** tab
3. Find **"Domains"** section
4. Click **"Add Domain"**
5. Enter your domain (e.g., `admin.yourdomain.com`)

### Step 2: Configure DNS Records
In your domain registrar (GoDaddy, Namecheap, Cloudflare, etc.):

#### Option A: CNAME Record (Recommended for subdomains)
```
Type: CNAME
Name: admin (or whatever subdomain you want)
Value: your-app-name.up.railway.app
TTL: Auto or 300
```

#### Option B: A Record (For root domain)
```
Type: A
Name: @ (for root domain) or admin (for subdomain)
Value: [Railway will provide the IP address]
TTL: Auto or 300
```

### Step 3: SSL Certificate
- Railway automatically generates SSL certificates
- Takes 5-15 minutes after DNS propagation
- Your site will be accessible via HTTPS

### Step 4: Update Laravel Environment
Update your `.env` in Railway:
```env
APP_URL=https://admin.yourdomain.com
```

### Example Domain Setups:
- `admin.yourdomain.com` - Admin panel subdomain
- `yourdomain.com` - Root domain
- `go-admin.yourdomain.com` - Custom subdomain

### DNS Propagation Time:
- Usually 5-30 minutes
- Can take up to 24-48 hours worldwide
- Use [whatsmydns.net](https://whatsmydns.net) to check status

## Post-Deployment Checklist

- [ ] Database migrated successfully
- [ ] File storage configured
- [ ] SSL certificate active
- [ ] Custom domain configured and working
- [ ] DNS records properly set
- [ ] APP_URL updated in environment
- [ ] Payment gateways tested
- [ ] Email sending working
- [ ] Admin login functional

---

**Note:** Railway is perfect for full-stack Laravel applications that need both server and database running 24/7.