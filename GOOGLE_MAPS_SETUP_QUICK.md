# 🚀 Quick Google Maps API Setup

## Step 1: Create Keys in Google Cloud Console
1. Go to https://console.cloud.google.com
2. Create new project or select existing
3. Go to **APIs & Services** → **Credentials**
4. Click **+ CREATE CREDENTIALS** → **API Key** (do this twice)

## Step 2: Set Restrictions

### 🌐 Key 1 - CLIENT (for browser)
1. Name it: `Hopa Delivery Client`
2. **Application restrictions**: HTTP referrers
3. **Add these referrers**:
   - `https://admin.hopa.delivery/*`
   - `https://hopa.delivery/*`
   - `http://localhost:8000/*` (for testing)

### 🔐 Key 2 - SERVER (for backend)
1. Name it: `Hopa Delivery Server`
2. **Application restrictions**: IP addresses
3. **Add this IP**: `138.197.188.120`

## Step 3: Enable Required APIs

Go to **APIs & Services** → **Library** and enable:

### For BOTH keys:
✅ **Places API**

### For CLIENT key only:
✅ **Maps JavaScript API**

### For SERVER key only:
✅ **Geocoding API**
✅ **Distance Matrix API**
✅ **Routes API**

## Step 4: Add to Admin Panel
1. Go to: https://admin.hopa.delivery/admin/business-settings/config-setup
2. Paste Client Key in first field
3. Paste Server Key in second field
4. Click Save

## 📊 Monitor Usage
- Check daily: https://console.cloud.google.com/google/maps-apis/metrics
- Set budget alerts: https://console.cloud.google.com/billing

## 💰 Free Tier
Google gives $200/month free credit which covers:
- ~28,000 map loads
- ~40,000 geocoding requests
- ~40,000 place searches

## ⚠️ Security Tips
- NEVER share server key publicly
- ALWAYS restrict keys properly
- Monitor for unusual usage spikes