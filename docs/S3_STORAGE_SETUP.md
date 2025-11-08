# S3 Storage Configuration Guide
## AWS S3 & DigitalOcean Spaces

---

## 📋 Overview

This application supports two S3-compatible storage options:
1. **AWS S3** - Amazon's object storage (for local testing or production)
2. **DigitalOcean Spaces** - S3-compatible storage (recommended for production per developer)

---

## 🔧 The ACL Issue & Fix

### **Problem:**
Modern AWS S3 buckets (post-2023) have **ACLs disabled by default** for security. Laravel's default S3 configuration tries to set ACLs when uploading files, causing:
```
Error: AccessControlListNotSupported - The bucket does not allow ACLs
```

### **Solution:**
We configured the S3 driver to use `bucket-owner-full-control` ACL instead of `public` ACL. This works for:
- ✅ AWS S3 buckets with ACLs disabled (modern default)
- ✅ DigitalOcean Spaces (has ACLs enabled)
- ✅ Any S3-compatible service

---

## 🌐 Configuration for Different Environments

### **Option A: AWS S3 (Current Local Testing)**

#### **.env Configuration:**
```env
FILESYSTEM_DISK=s3

# AWS S3 Credentials
AWS_ACCESS_KEY_ID=AKIA4AQ3UCG4VCCUZEM5
AWS_SECRET_ACCESS_KEY=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
AWS_DEFAULT_REGION=eu-north-1
AWS_BUCKET=goappstorage

# ACL Configuration (for modern AWS S3 with ACLs disabled)
AWS_ACL=bucket-owner-full-control

# DO NOT SET (let AWS SDK auto-generate):
# AWS_URL=
# AWS_ENDPOINT=
# AWS_USE_PATH_STYLE_ENDPOINT=
```

#### **Admin Panel Settings:**
1. Go to: **Settings → Storage Connection**
2. Enable **3rd Party Storage** toggle
3. Disable **Local Storage** toggle
4. **Leave S3 credentials empty** (they're read from .env)

#### **AWS S3 Bucket Policy (Make Objects Public):**

Since ACLs are disabled, use bucket policy for public access:

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "PublicReadGetObject",
            "Effect": "Allow",
            "Principal": "*",
            "Action": "s3:GetObject",
            "Resource": "arn:aws:s3:::goappstorage/*"
        }
    ]
}
```

Apply this in: AWS Console → S3 → goappstorage → Permissions → Bucket Policy

---

### **Option B: DigitalOcean Spaces (Production Deployment)**

#### **.env Configuration:**
```env
FILESYSTEM_DISK=s3

# DigitalOcean Spaces Credentials
AWS_ACCESS_KEY_ID=<your-do-spaces-key>
AWS_SECRET_ACCESS_KEY=<your-do-spaces-secret>
AWS_DEFAULT_REGION=<region>  # e.g., nyc3, sgp1, fra1
AWS_BUCKET=<your-space-name>

# DigitalOcean Spaces Endpoint (REQUIRED for DO Spaces)
AWS_ENDPOINT=https://<region>.digitaloceanspaces.com
AWS_USE_PATH_STYLE_ENDPOINT=false

# ACL Configuration (DO Spaces supports ACLs)
AWS_ACL=public-read  # or bucket-owner-full-control
```

Example for NYC3 region:
```env
AWS_DEFAULT_REGION=nyc3
AWS_BUCKET=my-app-storage
AWS_ENDPOINT=https://nyc3.digitaloceanspaces.com
```

#### **Admin Panel Settings for DO Spaces:**
1. Go to: **Settings → Storage Connection**
2. Enable **3rd Party Storage** toggle
3. Enter S3 Credentials:
   - **Key**: Your DO Spaces Access Key
   - **Secret**: Your DO Spaces Secret Key
   - **Region**: Your DO region (e.g., nyc3)
   - **Bucket**: Your Space name
   - **URL**: Leave empty (or full space URL)
   - **Endpoint**: `https://nyc3.digitaloceanspaces.com` (replace with your region)

---

## 🔑 Key Differences

| Feature | AWS S3 | DigitalOcean Spaces |
|---------|--------|---------------------|
| **Endpoint** | Auto-generated (don't set) | Must set: `https://region.digitaloceanspaces.com` |
| **ACLs** | Often disabled (use bucket-owner-full-control) | Enabled (can use public-read) |
| **URL Format** | `https://bucket.s3.region.amazonaws.com/key` | `https://space.region.digitaloceanspaces.com/key` |
| **Cost** | Pay per GB + requests | Flat rate $5/month (250GB) |
| **Use Case** | Global, scalable | Cost-effective for small/medium apps |

---

## 🛠️ Code Changes Made

### **1. config/filesystems.php**
```php
's3' => [
    'driver' => 's3',
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION'),
    'bucket' => env('AWS_BUCKET'),
    'throw' => false,
    'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
    'options' => [
        'ACL' => env('AWS_ACL', 'bucket-owner-full-control'),
    ],
],
```

### **2. app/CentralLogics/Helpers.php - upload() function**
```php
if ($disk === 's3') {
    // Don't set visibility parameter - rely on config ACL
    Storage::disk($disk)->put($dir . $imageName, file_get_contents($image->getRealPath()));
} else {
    Storage::disk($disk)->putFileAs($dir, $image, $imageName);
}
```

---

## 🧪 Testing

### **Test Upload Function:**
```bash
php test-s3-connection.php
```

### **Test from Admin Panel:**
1. Go to **Cuisine → Add New**
2. Upload an image
3. Check S3 bucket:
```bash
aws s3 ls s3://YOUR_BUCKET/cuisine/ --region YOUR_REGION --recursive
```

---

## 🚀 Switching Between Environments

### **Local Testing (AWS S3):**
```env
# .env
AWS_DEFAULT_REGION=eu-north-1
AWS_BUCKET=goappstorage
AWS_ACL=bucket-owner-full-control
# No AWS_ENDPOINT
```

### **Production (DigitalOcean Spaces):**
```env
# .env
AWS_DEFAULT_REGION=nyc3
AWS_BUCKET=your-space-name
AWS_ENDPOINT=https://nyc3.digitaloceanspaces.com
AWS_USE_PATH_STYLE_ENDPOINT=false
AWS_ACL=public-read  # DO Spaces supports this
```

**That's it!** Just update .env - no code changes needed when switching!

---

## 🔐 Security Notes

### **For AWS S3:**
- Use bucket policy for public access (not ACLs)
- Keep "Block all public access" ON
- Set "Object Ownership" to "Bucket owner enforced"
- Use IAM user with minimal S3 permissions

### **For DigitalOcean Spaces:**
- Set Space to "Public" in DO control panel
- Use Spaces CDN for better performance
- Rotate access keys regularly

---

## 📊 Public Access Methods

### **AWS S3 (ACLs Disabled):**
Files are made public via **Bucket Policy**, not individual object ACLs.

### **DigitalOcean Spaces:**
Files can be made public via:
1. Space-level public setting (recommended)
2. Individual file ACLs (supported)
3. CDN configuration

---

## ✅ Verification Checklist

- [ ] S3 credentials in .env
- [ ] Bucket exists and accessible
- [ ] 3rd party storage enabled in admin panel
- [ ] Local storage disabled in admin panel
- [ ] Test upload works (run `php test-s3-connection.php`)
- [ ] Images uploaded from admin panel appear in bucket
- [ ] Images display correctly on frontend

---

## 🐛 Troubleshooting

### **Error: "AccessControlListNotSupported"**
**Cause:** Bucket has ACLs disabled (modern AWS default)
**Fix:** Already fixed! Using `bucket-owner-full-control` ACL

### **Error: "Bucket does not exist"**
**Cause:** Wrong bucket name or region
**Fix:** Check `AWS_BUCKET` and `AWS_DEFAULT_REGION` in .env

### **Images upload but don't display:**
**Cause:** Bucket/objects not public
**Fix:**
- AWS S3: Add bucket policy (see above)
- DO Spaces: Set Space to "Public" in DO panel

### **Uploads fail silently:**
**Cause:** Wrong credentials
**Fix:** Verify AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY

---

## 📝 For DigitalOcean Deployment

When deploying to DigitalOcean:

1. **Create a Space** in DigitalOcean control panel
2. **Get Spaces Keys** (Settings → API → Spaces Keys)
3. **Update .env**:
```env
AWS_ACCESS_KEY_ID=<your-do-spaces-key>
AWS_SECRET_ACCESS_KEY=<your-do-spaces-secret>
AWS_DEFAULT_REGION=nyc3  # or your region
AWS_BUCKET=<your-space-name>
AWS_ENDPOINT=https://nyc3.digitaloceanspaces.com
AWS_ACL=public-read
```
4. **Update Admin Panel** (Settings → Storage Connection)
5. **Test upload** through admin panel

---

**Created:** November 8, 2025
**Last Updated:** November 8, 2025
**Tested With:** AWS S3 (eu-north-1), DigitalOcean Spaces compatible
