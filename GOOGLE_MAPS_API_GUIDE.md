# 🗺️ Google Maps API Keys in GO-AdminPanel

## Overview
Your GO-AdminPanel system uses **two separate Google Maps API keys** for different purposes and security levels.

## 📍 The Two API Keys Explained

### 1. **Map API Key (Client)** 🌐
**Where it's stored**: `business_settings` table → key: `map_api_key`

**Purpose**: Used in **frontend/browser** JavaScript code
- Visible in browser source code
- Used for interactive maps that users see
- Powers the visual map interface

**Where it's used**:
```javascript
// In Blade templates (frontend views)
https://maps.googleapis.com/maps/api/js?key={{ map_api_key }}
```

**Found in these files**:
- `/resources/views/admin-views/vendor/edit.blade.php` - Restaurant location editing
- `/resources/views/admin-views/vendor/index.blade.php` - Restaurant listing map
- `/resources/views/admin-views/zone/index.blade.php` - Zone management
- `/resources/views/admin-views/zone/edit.blade.php` - Zone boundary editing
- `/resources/views/admin-views/order/order-view.blade.php` - Order tracking map
- `/resources/views/admin-views/pos/index.blade.php` - Point of Sale map

**Features powered by Client Key**:
- 📍 Restaurant location picker
- 🗺️ Zone boundary drawing
- 📦 Order tracking visualization
- 🏠 Customer address selection
- 🚚 Delivery route display

---

### 2. **Map API Key (Server)** 🔐
**Where it's stored**: `business_settings` table → key: `map_api_key_server`

**Purpose**: Used for **backend/server** API calls
- Never exposed to users
- More secure, hidden from public view
- Higher quota limits possible

**Where it's used**:
```php
// In API Controller (ConfigController.php)
$this->map_api_key = BusinessSetting::where(['key' => 'map_api_key_server'])->first()?->value;
```

**API endpoints using Server Key**:
1. **Place Autocomplete** (`/api/v1/config/place-api-autocomplete`)
   ```
   https://places.googleapis.com/v1/places:autocomplete
   ```

2. **Distance Matrix** (`/api/v1/config/distance-api`)
   ```
   https://routes.googleapis.com/distanceMatrix/v2:computeRouteMatrix
   ```

3. **Place Details** (`/api/v1/config/place-api-details`)
   ```
   https://places.googleapis.com/v1/places/{placeid}
   ```

4. **Geocoding** (`/api/v1/config/geocode-api`)
   ```
   https://maps.googleapis.com/maps/api/geocode/json
   ```

**Features powered by Server Key**:
- 🔍 Address search autocomplete (mobile apps)
- 📏 Distance calculations for delivery
- 📍 Convert coordinates to addresses
- 🏢 Get place details and information
- 💰 Calculate delivery charges based on distance

---

## 🔒 Security & Best Practices

### Client Key Security:
1. **Restrict by HTTP referrers** in Google Cloud Console:
   - Add your domain: `https://yourdomain.com/*`
   - Add admin panel: `https://yourdomain.com/admin/*`

2. **Enable only required APIs**:
   - Maps JavaScript API
   - Places API
   - Geocoding API (optional)

### Server Key Security:
1. **Restrict by IP address** in Google Cloud Console:
   - Add your server's IP: `138.197.188.120`
   - Never use this key in frontend code

2. **Enable only required APIs**:
   - Places API
   - Distance Matrix API
   - Routes API
   - Geocoding API

---

## 📱 Mobile App Integration

The mobile apps (customer, delivery, restaurant) get map functionality through:

1. **Direct API calls** using the server key (via your backend)
   - The server key is never sent to mobile apps
   - All Google Maps API calls go through your server

2. **Mobile SDKs** may need their own keys:
   - Android: Set in `AndroidManifest.xml`
   - iOS: Set in `AppDelegate` or Info.plist
   - These are separate from web keys

---

## 💡 How the System Works

### Customer Orders Food (Example Flow):

1. **Customer opens app** → Shows map with restaurants
   - Mobile app uses its own Google Maps SDK key
   - OR calls your API which uses server key

2. **Customer enters address** → Address autocomplete
   - App calls: `POST /api/v1/config/place-api-autocomplete`
   - Your server uses **server key** to call Google Places API
   - Results returned to app

3. **Admin views order** → See on map
   - Browser loads: `maps.googleapis.com/maps/api/js?key={client_key}`
   - Uses **client key** for visual display

4. **Calculate delivery distance** → For pricing
   - App calls: `POST /api/v1/config/distance-api`
   - Your server uses **server key** to get distance
   - Returns distance/duration to app

---

## ⚙️ Configuration Steps

### To Set Up:

1. **Get keys from Google Cloud Console**:
   - Create project at https://console.cloud.google.com
   - Enable required APIs
   - Create 2 API keys

2. **Configure in Admin Panel**:
   - Go to: Third-Party APIs → Map APIs
   - Enter Client Key (for web)
   - Enter Server Key (for backend)

3. **Set restrictions**:
   - Client key: HTTP referrer restrictions
   - Server key: IP address restrictions

---

## 🚨 Common Issues & Solutions

### Issue: "This page can't load Google Maps correctly"
**Solution**: Check client key restrictions, ensure your domain is whitelisted

### Issue: Mobile app can't search addresses
**Solution**: Check server key is set and Places API is enabled

### Issue: Distance calculation not working
**Solution**: Ensure Distance Matrix API is enabled for server key

### Issue: High API usage/costs
**Solution**:
- Implement caching for repeated queries
- Use server key for batch operations
- Set daily quotas in Google Cloud Console

---

## 📊 API Usage Summary

| Feature | Key Used | API Called | Who Uses It |
|---------|----------|------------|-------------|
| View restaurant on map | Client | Maps JavaScript | Admin Panel |
| Draw delivery zones | Client | Maps JavaScript + Drawing | Admin Panel |
| Search address | Server | Places Autocomplete | Mobile Apps |
| Calculate distance | Server | Distance Matrix | Mobile Apps |
| Get coordinates | Server | Geocoding | Mobile Apps |
| Track order live | Client | Maps JavaScript | Admin Panel |
| Show customer location | Client | Maps JavaScript | POS System |

---

## 💰 Cost Optimization Tips

1. **Cache API responses** when possible
2. **Batch distance calculations** instead of individual calls
3. **Use viewport biasing** for autocomplete to reduce irrelevant results
4. **Set budget alerts** in Google Cloud Console
5. **Monitor usage** regularly in Google Cloud Console

---

## 🔗 Important Links

- [Google Cloud Console](https://console.cloud.google.com)
- [Maps API Pricing](https://developers.google.com/maps/billing-and-pricing/pricing)
- [API Key Best Practices](https://developers.google.com/maps/api-security-best-practices)
- [Usage Reports](https://console.cloud.google.com/google/maps-apis/metrics)