# Redis Installation & Deployment Guide

## What is Redis?

Redis is an in-memory data structure store used as a database, cache, and message broker. In this project, it's used for:
- **Caching**: Faster data retrieval (banners, ads, settings)
- **Sessions**: User authentication and session storage
- **Queues**: Background job processing (emails, media processing)

## Local Development (macOS)

### 1. Install Redis Server
```bash
brew install redis
```

### 2. Start Redis Service
```bash
# Start and auto-start on login
brew services start redis

# Or run manually (foreground)
redis-server /opt/homebrew/etc/redis.conf
```

### 3. Install PHP Redis Client (Predis)
The project uses **Predis** (PHP library) which is already installed via Composer:
```bash
composer require predis/predis
```

**Note**: Predis is a pure PHP library that works without compiling extensions. For production, you can optionally use `phpredis` (C extension) which is faster but requires compilation.

### 4. Configure Redis Client in .env
```env
REDIS_CLIENT=predis
```

### 5. Verify Installation
```bash
# Test Redis server
redis-cli ping
# Should return: PONG

# Test from Laravel
php artisan tinker
>>> Redis::ping()
# Should return: "PONG"
```

### Stop Redis
```bash
brew services stop redis
```

## Production Deployment

### Option 1: DigitalOcean App Platform (Recommended)

Redis is automatically managed when you add a Redis component:

1. **In DigitalOcean Dashboard:**
   - Go to your App
   - Click "Components" → "Add Component"
   - Select "Redis"
   - Choose plan (Basic: $15/month, Professional: $30+/month)

2. **Environment Variables:**
   - DigitalOcean automatically sets these:
     - `REDIS_HOST` (auto-configured)
     - `REDIS_PORT` (auto-configured)
     - `REDIS_PASSWORD` (auto-configured)
   - Update your `.env` to use these values

3. **No manual installation needed!**

### Option 2: Laravel Forge + Managed Redis

**Using DigitalOcean Managed Redis:**
1. Create Redis database in DigitalOcean
2. Get connection details (host, port, password)
3. Add to Forge server environment:
   ```
   REDIS_HOST=your-redis-host.db.ondigitalocean.com
   REDIS_PORT=25061
   REDIS_PASSWORD=your-password
   ```

**Using Redis Cloud (RedisLabs):**
1. Sign up at https://redis.com/try-free/
2. Create free database (up to 30MB)
3. Get connection string
4. Configure in Forge environment variables

### Option 3: Self-Hosted on VPS (Ubuntu/Debian)

#### Step 1: Install Redis Server
```bash
# Update system
sudo apt update
sudo apt upgrade -y

# Install Redis
sudo apt install redis-server -y

# Start Redis
sudo systemctl start redis-server

# Enable auto-start on boot
sudo systemctl enable redis-server

# Verify installation
redis-cli ping
```

#### Step 2: Install PHP Redis Client (Predis)
Predis is installed automatically when you run `composer install` in production. No additional steps needed!

#### Configuration
```bash
# Edit Redis config
sudo nano /etc/redis/redis.conf

# Important settings:
# 1. Set bind address (for remote access)
bind 127.0.0.1 ::1

# 2. Set password (IMPORTANT for production!)
requirepass your-strong-password-here

# 3. Enable persistence
save 900 1
save 300 10
save 60 10000

# Restart Redis
sudo systemctl restart redis-server
```

#### Firewall Configuration
```bash
# Allow Redis port (if accessing from outside)
sudo ufw allow 6379/tcp

# Or use SSH tunnel for security (recommended)
ssh -L 6379:localhost:6379 user@your-server
```

#### Update .env
```env
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=your-strong-password-here
```

**Note**: Predis is automatically installed via `composer install`. No PHP extension compilation needed!

### Option 4: AWS ElastiCache

1. **Create ElastiCache Redis Cluster:**
   - Go to AWS Console → ElastiCache
   - Create Redis cluster
   - Choose instance type (cache.t3.micro for dev)
   - Configure security groups

2. **Get Endpoint:**
   - Use the Primary Endpoint as `REDIS_HOST`
   - Default port: 6379
   - Set up authentication token

3. **Update .env:**
   ```env
   REDIS_HOST=your-cluster.xxxxx.0001.use1.cache.amazonaws.com
   REDIS_PORT=6379
   REDIS_PASSWORD=your-auth-token
   ```

### Option 5: Heroku

```bash
# Add Redis addon
heroku addons:create heroku-redis:hobby-dev

# View Redis URL
heroku config:get REDIS_URL

# Heroku automatically sets:
# - REDIS_URL (connection string)
# - REDIS_TLS_URL (TLS connection)
```

### Option 6: Shared Hosting (cPanel)

Most shared hosting doesn't support Redis. **Use alternatives:**
- **Caching**: Switch to `CACHE_DRIVER=file` or `database`
- **Sessions**: Switch to `SESSION_DRIVER=file` or `database`
- **Queues**: Switch to `QUEUE_CONNECTION=sync` or `database`

Update `.env`:
```env
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

## Verification

### Test Redis Connection (Laravel)
```bash
# In Laravel Tinker
php artisan tinker
>>> Redis::ping()
# Should return: "PONG"

>>> Cache::put('test', 'value', 60)
>>> Cache::get('test')
# Should return: "value"
```

### Test from Command Line
```bash
# Connect to Redis
redis-cli

# Test commands
PING
# Returns: PONG

SET test "Hello Redis"
GET test
# Returns: "Hello Redis"

INFO server
# Shows Redis server information
```

## Security Best Practices

### 1. **Set a Strong Password**
```bash
# In redis.conf
requirepass your-very-strong-password-here
```

### 2. **Bind to Localhost Only** (if on same server)
```bash
bind 127.0.0.1
```

### 3. **Use SSL/TLS** (for remote connections)
- Enable TLS in Redis 6+
- Use stunnel or SSH tunnel

### 4. **Firewall Rules**
```bash
# Only allow from specific IPs
sudo ufw allow from YOUR_APP_SERVER_IP to any port 6379
```

### 5. **Disable Dangerous Commands**
```bash
# In redis.conf
rename-command FLUSHDB ""
rename-command FLUSHALL ""
rename-command CONFIG ""
```

## Monitoring & Maintenance

### Check Redis Status
```bash
# Check if running
sudo systemctl status redis-server

# Check memory usage
redis-cli INFO memory

# Check connected clients
redis-cli CLIENT LIST
```

### Backup Redis Data
```bash
# Create snapshot
redis-cli BGSAVE

# Copy RDB file
sudo cp /var/lib/redis/dump.rdb /backup/redis-$(date +%Y%m%d).rdb
```

### Clear Cache (if needed)
```bash
# Clear all cache (DANGEROUS - use with caution!)
redis-cli FLUSHALL

# Clear specific keys
redis-cli --scan --pattern "your-app-cache:*" | xargs redis-cli DEL
```

## Troubleshooting

### Redis Not Starting
```bash
# Check logs
sudo tail -f /var/log/redis/redis-server.log

# Check if port is in use
sudo netstat -tulpn | grep 6379
```

### Connection Refused
- Check if Redis is running: `sudo systemctl status redis-server`
- Check firewall: `sudo ufw status`
- Verify host/port in `.env`

### Out of Memory
```bash
# Check memory usage
redis-cli INFO memory

# Set max memory in redis.conf
maxmemory 256mb
maxmemory-policy allkeys-lru
```

### Laravel Can't Connect
1. Verify Redis is running: `redis-cli ping`
2. Check `.env` settings
3. Clear config cache: `php artisan config:clear`
4. Check Redis password matches

## Cost Comparison

| Solution | Cost | Best For |
|----------|------|----------|
| DigitalOcean Managed | $15-100+/month | Easy setup, managed |
| AWS ElastiCache | $13-500+/month | AWS ecosystem |
| Redis Cloud (Free) | Free (30MB) | Small apps, testing |
| Self-Hosted VPS | $5-20/month | Full control |
| Heroku Redis | $0-200+/month | Heroku apps |
| Shared Hosting | N/A | Use file/database instead |

## Recommendation

**For Production:**
- **Best Option**: DigitalOcean Managed Redis or AWS ElastiCache
- **Budget Option**: Self-hosted on VPS (if you have one)
- **Free Option**: Redis Cloud free tier (for small apps)

**For Development:**
- Local Redis installation (what we just set up)
- Or use file/database drivers

## Next Steps

1. ✅ Redis installed locally
2. ✅ Redis service running
3. Update your production `.env` with Redis credentials
4. Test connection in production
5. Monitor Redis usage and performance

## Additional Resources

- [Redis Official Documentation](https://redis.io/documentation)
- [Laravel Redis Documentation](https://laravel.com/docs/cache#redis)
- [DigitalOcean Redis Guide](https://docs.digitalocean.com/products/databases/redis/)
- [AWS ElastiCache Guide](https://aws.amazon.com/elasticache/redis/)

---

**Your local Redis is now running!** 🎉

Test it: `redis-cli ping` should return `PONG`

