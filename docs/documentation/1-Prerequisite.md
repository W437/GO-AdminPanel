# Prerequisites - GO Admin Panel

GO Admin Panel (Hopa Delivery) requires specific server software and PHP extensions to run properly. This document lists all requirements for a fresh installation.

---

## 🖥️ Server Requirements

### **Operating System**
- Ubuntu 20.04 LTS or newer (recommended)
- Debian 10 or newer
- Any Linux distribution with systemd support

### **Minimum Hardware**
- **CPU**: 2 vCPU (4 recommended for production)
- **RAM**: 4GB (8GB recommended for production)
- **Disk**: 40GB SSD (minimum)
- **Bandwidth**: 2TB/month

---

## 📦 Required Software Stack

### **1. Web Server**
**Apache 2.4** or higher with the following modules enabled:
```bash
sudo a2enmod rewrite      # URL rewriting
sudo a2enmod headers      # HTTP headers manipulation
sudo a2enmod ssl          # SSL/TLS support (for HTTPS)
sudo systemctl restart apache2
```

**Alternative**: Nginx 1.18+ (configuration not covered in this guide)

### **2. PHP 8.2+**
**Minimum**: PHP 8.2
**Recommended**: PHP 8.4 (latest stable)

**Installation (Ubuntu/Debian)**:
```bash
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.4-fpm php8.4-cli
```

### **3. MySQL 8.0+**
**Minimum**: MySQL 5.7
**Recommended**: MySQL 8.0+ or MySQL 8.4

**Installation**:
```bash
sudo apt install mysql-server mysql-client
```

### **4. Redis 6.0+**
**Required for**: Caching, sessions, queue management

**Installation**:
```bash
sudo apt install redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server
```

### **5. Composer 2.x**
**Required for**: PHP dependency management

**Installation**:
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

### **6. Git**
**Required for**: Code deployment and version control

**Installation**:
```bash
sudo apt install git
```

### **7. Supervisor**
**Required for**: Managing Laravel queue workers

**Installation**:
```bash
sudo apt install supervisor
sudo systemctl enable supervisor
sudo systemctl start supervisor
```

### **8. Certbot (Optional but Recommended)**
**Required for**: Free SSL/TLS certificates via Let's Encrypt

**Installation**:
```bash
sudo apt install certbot python3-certbot-apache
```

---

## 🔧 Required PHP Extensions

All of the following PHP extensions **must** be installed and enabled:

### **Core Extensions**
```bash
sudo apt install \
  php8.4-bcmath \      # Arbitrary precision mathematics
  php8.4-ctype \       # Character type checking
  php8.4-curl \        # HTTP client for API requests
  php8.4-dom \         # XML document manipulation
  php8.4-fileinfo \    # File type detection
  php8.4-gd \          # Image processing
  php8.4-intl \        # Internationalization support
  php8.4-json \        # JSON encoding/decoding
  php8.4-mbstring \    # Multibyte string handling
  php8.4-mysql \       # MySQL database driver
  php8.4-opcache \     # PHP performance optimizer
  php8.4-openssl \     # Encryption and security
  php8.4-pdo \         # Database abstraction layer
  php8.4-sodium \      # Modern encryption library
  php8.4-tokenizer \   # PHP code parsing
  php8.4-xml \         # XML parsing
  php8.4-zip \         # Archive file handling
  php8.4-redis         # Redis client for PHP
```

### **Verify Extensions Are Loaded**
```bash
php -m | grep -E 'bcmath|ctype|curl|dom|fileinfo|gd|intl|json|mbstring|mysqli|openssl|pdo_mysql|sodium|tokenizer|xml|zip|redis'
```

All extensions should appear in the output.

---

## 🌐 Third-Party Services (Optional)

### **Required for Full Functionality**

1. **AWS S3 or DigitalOcean Spaces**
   - For file storage (images, videos, documents)
   - DigitalOcean Spaces recommended for production

2. **Twilio Account**
   - For SMS/OTP authentication
   - Get account at: https://www.twilio.com

3. **Firebase Account**
   - For push notifications
   - Get account at: https://firebase.google.com

4. **OpenAI API Key (Optional)**
   - For AI-powered translation
   - More accurate than Google Translate
   - Get API key at: https://platform.openai.com/api-keys

5. **Payment Gateways**
   - Stripe
   - PayPal
   - Razorpay
   - (Configure only what you need)

---

## ✅ Installation Verification

### **Check PHP Version and Extensions**
```bash
php -v
php -m
```

### **Check MySQL**
```bash
mysql --version
sudo systemctl status mysql
```

### **Check Redis**
```bash
redis-cli ping  # Should return "PONG"
```

### **Check Apache**
```bash
apache2 -v
sudo systemctl status apache2
```

### **Check Supervisor**
```bash
sudo systemctl status supervisor
```

---

## 📝 Quick Install Script (Ubuntu 24.10)

**For a fresh Ubuntu server, run these commands:**

```bash
#!/bin/bash
# Update system
sudo apt update && sudo apt upgrade -y

# Add PHP repository
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install Apache
sudo apt install apache2 -y
sudo a2enmod rewrite headers ssl
sudo systemctl enable apache2

# Install MySQL
sudo apt install mysql-server -y
sudo systemctl enable mysql

# Install PHP 8.4 and extensions
sudo apt install php8.4 php8.4-fpm php8.4-cli \
  php8.4-bcmath php8.4-ctype php8.4-curl php8.4-dom \
  php8.4-fileinfo php8.4-gd php8.4-intl php8.4-json \
  php8.4-mbstring php8.4-mysql php8.4-opcache \
  php8.4-openssl php8.4-pdo php8.4-sodium \
  php8.4-tokenizer php8.4-xml php8.4-zip php8.4-redis -y

# Install Redis
sudo apt install redis-server -y
sudo systemctl enable redis-server

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Install Git
sudo apt install git -y

# Install Supervisor
sudo apt install supervisor -y
sudo systemctl enable supervisor

# Install Certbot (for SSL)
sudo apt install certbot python3-certbot-apache -y

echo "✅ All prerequisites installed!"
echo "Next: Clone repository and configure application"
```

---

## ⚠️ Important Notes

1. **PHP Version**: Must be 8.2 or higher. PHP 8.4 recommended.
2. **MySQL Version**: Must be 5.7 or higher. MySQL 8.0+ recommended.
3. **Redis**: Required for queues and caching - don't skip this!
4. **Supervisor**: Required to keep queue workers running.
5. **Extensions**: All listed PHP extensions are mandatory.

---

## 🚨 Troubleshooting

### **"Extension not found" Errors**
Make sure you're installing for the correct PHP version:
```bash
php -v  # Check your PHP version
sudo apt install php8.4-extensionname  # Match the version
```

### **Apache Not Starting**
```bash
sudo apache2ctl configtest  # Check for config errors
sudo systemctl status apache2  # Check service status
sudo tail -f /var/log/apache2/error.log  # View error logs
```

### **Redis Connection Failed**
```bash
sudo systemctl status redis-server
redis-cli ping  # Should return "PONG"
```

---

## 📚 Next Steps

After installing all prerequisites:

1. **Set up MySQL database** - See `2-environmentvars.md`
2. **Clone repository** - See `3-mandatorysetup.md`
3. **Configure .env file** - See `2-environmentvars.md`
4. **Run migrations** - `php artisan migrate`
5. **Set up SSL** - `sudo certbot --apache`

---

**Updated**: 2025-11-08
**For**: GO Admin Panel / Hopa Delivery
**Minimum PHP**: 8.2
**Recommended**: Ubuntu 24.10, PHP 8.4, MySQL 8.4, Redis 8.0
