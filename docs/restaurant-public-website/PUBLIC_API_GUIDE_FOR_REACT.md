# Public API Guide for React Restaurant Website

## Overview
This guide documents all publicly accessible API endpoints that can be used to build a React-based restaurant website without requiring authentication.

## Base Configuration

### API Base URL
```javascript
const API_BASE_URL = 'https://admin.hopa.delivery/api/v1';
```

### Required Headers
Most endpoints require a `zoneId` header to filter restaurants by zone/city:
```javascript
const headers = {
  'Content-Type': 'application/json',
  'zoneId': '1', // Required for most endpoints
  'latitude': '23.8103', // Optional: for distance calculations
  'longitude': '90.4125' // Optional: for distance calculations
};
```

## Zone Endpoints

### 1. Get All Zones (NEEDS TO BE CREATED)
```javascript
// This endpoint needs to be added to the backend
GET /api/v1/zones

const getZones = async () => {
  const response = await fetch(`${API_BASE_URL}/zones`);
  return response.json();
};

// Expected response:
{
  "zones": [
    {
      "id": 1,
      "name": "Bangkok",
      "slug": "bangkok",
      "coordinates": {...},
      "restaurant_count": 45,
      "active_restaurants": 42,
      "delivery_available": true
    },
    {
      "id": 2,
      "name": "Phuket",
      "slug": "phuket",
      "coordinates": {...},
      "restaurant_count": 28,
      "active_restaurants": 25,
      "delivery_available": true
    }
  ]
}
```

### 2. Get Zone Details (NEEDS TO BE CREATED)
```javascript
// This endpoint needs to be added to the backend
GET /api/v1/zones/{id}

const getZoneDetails = async (zoneId) => {
  const response = await fetch(`${API_BASE_URL}/zones/${zoneId}`);
  return response.json();
};
```

### 3. Get Restaurants by Zone
```javascript
// Use existing endpoint with zone header
GET /api/v1/restaurants/get-restaurants/all

const getZoneRestaurants = async (zoneId, limit = 12, offset = 1) => {
  const response = await fetch(
    `${API_BASE_URL}/restaurants/get-restaurants/all?limit=${limit}&offset=${offset}`,
    {
      headers: {
        'Content-Type': 'application/json',
        'zoneId': zoneId.toString()
      }
    }
  );
  return response.json();
};
```

## Restaurant Endpoints

### 1. Get Restaurant Details
```javascript
// Get restaurant by ID
GET /api/v1/restaurants/details/{id}

// Example
const getRestaurant = async (restaurantId) => {
  const response = await fetch(`${API_BASE_URL}/restaurants/details/${restaurantId}`, {
    headers: headers
  });
  return response.json();
};

// Response includes:
{
  "id": 1,
  "name": "Restaurant Name",
  "slug": "restaurant-name",
  "phone": "+1234567890",
  "email": "restaurant@example.com",
  "logo": "url",
  "cover_photo": "url",
  "address": "123 Main St",
  "latitude": "23.8103",
  "longitude": "90.4125",
  "zone_id": 1,
  "opening_time": "08:00:00",
  "closeing_time": "22:00:00",
  "off_day": "sunday",
  "gst_status": true,
  "gst_code": "GST123",
  "delivery": true,
  "take_away": true,
  "veg": 1,
  "non_veg": 1,
  "free_delivery": false,
  "delivery_charge": "5.00",
  "delivery_time": "30 min",
  "minimum_order": "10.00",
  "avg_rating": 4.5,
  "rating_count": 150,
  "positive_rating": 85,
  "category_ids": [{"id": "1", "position": 1}, {"id": "2", "position": 2}],
  // ... more fields
}
```

### 2. List Restaurants
```javascript
GET /api/v1/restaurants/get-restaurants/{filter_data}

// Filter options: all, popular, latest, near_me, best_reviewed, wish, premium
const getRestaurants = async (filter = 'all', limit = 10, offset = 1) => {
  const response = await fetch(
    `${API_BASE_URL}/restaurants/get-restaurants/${filter}?limit=${limit}&offset=${offset}`,
    { headers: headers }
  );
  return response.json();
};
```

### 3. Search Restaurants
```javascript
GET /api/v1/restaurants/search

const searchRestaurants = async (searchTerm) => {
  const response = await fetch(
    `${API_BASE_URL}/restaurants/search?name=${searchTerm}&limit=10&offset=1`,
    { headers: headers }
  );
  return response.json();
};
```

### 4. Get Restaurant Reviews
```javascript
GET /api/v1/restaurants/reviews

const getRestaurantReviews = async (restaurantId) => {
  const response = await fetch(
    `${API_BASE_URL}/restaurants/reviews?restaurant_id=${restaurantId}`,
    { headers: headers }
  );
  return response.json();
};
```

### 5. Get Restaurant Coupons
```javascript
GET /api/v1/restaurants/get-coupon

const getRestaurantCoupons = async (restaurantId) => {
  const response = await fetch(
    `${API_BASE_URL}/restaurants/get-coupon?restaurant_id=${restaurantId}`,
    { headers: headers }
  );
  return response.json();
};
```

## Menu & Category Endpoints

### 1. Get All Categories
```javascript
GET /api/v1/categories

const getCategories = async () => {
  const response = await fetch(`${API_BASE_URL}/categories`, {
    headers: headers
  });
  return response.json();
};
```

### 2. Get Subcategories
```javascript
GET /api/v1/categories/childes/{category_id}

const getSubcategories = async (categoryId) => {
  const response = await fetch(
    `${API_BASE_URL}/categories/childes/${categoryId}`,
    { headers: headers }
  );
  return response.json();
};
```

### 3. Get Products by Category
```javascript
GET /api/v1/categories/products/{category_id}

const getCategoryProducts = async (categoryId, limit = 10, offset = 1) => {
  const response = await fetch(
    `${API_BASE_URL}/categories/products/${categoryId}?limit=${limit}&offset=${offset}`,
    { headers: headers }
  );
  return response.json();
};
```

### 4. Get Product Details
```javascript
GET /api/v1/products/details/{id}

const getProductDetails = async (productId) => {
  const response = await fetch(
    `${API_BASE_URL}/products/details/${productId}`,
    { headers: headers }
  );
  return response.json();
};

// Response includes:
{
  "id": 1,
  "name": "Burger",
  "slug": "burger",
  "description": "Delicious burger",
  "image": "url",
  "category_id": 1,
  "category_ids": [{"id": "1", "position": 1}],
  "variations": [...],
  "add_ons": [...],
  "attributes": [...],
  "choice_options": [...],
  "price": 10.99,
  "tax": 5,
  "tax_type": "percent",
  "discount": 10,
  "discount_type": "percent",
  "available_time_starts": "10:00:00",
  "available_time_ends": "22:00:00",
  "veg": 0,
  "status": 1,
  "restaurant_id": 1,
  "avg_rating": 4.5,
  "rating_count": 50,
  // ... more fields
}
```

### 5. Search Products
```javascript
GET /api/v1/products/search

const searchProducts = async (params) => {
  const queryString = new URLSearchParams({
    name: params.searchTerm,
    zone_id: headers.zoneId,
    limit: params.limit || 10,
    offset: params.offset || 1,
    restaurant_id: params.restaurantId, // Optional: filter by restaurant
    category_id: params.categoryId, // Optional: filter by category
    min_price: params.minPrice, // Optional
    max_price: params.maxPrice, // Optional
    rating: params.rating, // Optional: 3, 4, 5
    veg: params.veg, // Optional: 1 for veg only
    non_veg: params.nonVeg, // Optional: 1 for non-veg only
    new_variation: params.newVariation, // Optional: 1 for items with variations
    discount: params.discount, // Optional: 1 for discounted items
    sort_by: params.sortBy // Optional: name, price_low_to_high, price_high_to_low
  }).toString();

  const response = await fetch(
    `${API_BASE_URL}/products/search?${queryString}`,
    { headers: headers }
  );
  return response.json();
};
```

### 6. Get Product Reviews
```javascript
GET /api/v1/products/reviews/{food_id}

const getProductReviews = async (productId) => {
  const response = await fetch(
    `${API_BASE_URL}/products/reviews/${productId}`,
    { headers: headers }
  );
  return response.json();
};
```

## Working with Restaurant Slugs

Since the API doesn't have a direct "get by slug" endpoint, you have two options:

### Option 1: Search by Restaurant Name (Derived from Slug)
```javascript
const getRestaurantBySlug = async (slug) => {
  // Convert slug to name (replace hyphens with spaces, capitalize)
  const name = slug.replace(/-/g, ' ');

  const response = await fetch(
    `${API_BASE_URL}/restaurants/search?name=${encodeURIComponent(name)}&limit=1&offset=1`,
    { headers: headers }
  );

  const data = await response.json();

  // Find exact match by slug
  const restaurant = data.restaurants?.find(r => r.slug === slug);

  if (!restaurant) {
    throw new Error('Restaurant not found');
  }

  // Get full details
  return getRestaurant(restaurant.id);
};
```

### Option 2: Create a Custom Endpoint (Backend Modification)
Add this to your Laravel API:
```php
// In routes/api/v1/api.php
Route::get('restaurants/by-slug/{slug}', 'Api\V1\RestaurantController@getBySlug');

// In RestaurantController
public function getBySlug($slug)
{
    $restaurant = Restaurant::where('slug', $slug)
        ->with(['cuisine', 'schedules'])
        ->active()
        ->firstOrFail();

    return response()->json($restaurant);
}
```

## Getting Restaurant Schedules

Currently, restaurant schedules are NOT available via public API. You need to add an endpoint:

### Backend Addition Required:
```php
// In routes/api/v1/api.php
Route::get('restaurants/{id}/schedules', 'Api\V1\RestaurantController@schedules');

// In RestaurantController
public function schedules($id)
{
    $schedules = RestaurantSchedule::where('restaurant_id', $id)
        ->select(['day', 'opening_time', 'closing_time'])
        ->get();

    return response()->json(['schedules' => $schedules]);
}
```

### Or Include in Restaurant Details:
Modify the existing `get_restaurant_details` method to include schedules:
```php
$restaurant = Restaurant::with(['schedules', 'cuisine'])->find($id);
```

## React Implementation Examples

### 1. Explore Page with Zones
```jsx
// pages/Explore.jsx
import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';

const ExplorePage = () => {
  const [zones, setZones] = useState([]);
  const [loading, setLoading] = useState(true);
  const navigate = useNavigate();

  useEffect(() => {
    const fetchZones = async () => {
      try {
        // Fetch all zones
        const response = await fetch(`${API_BASE_URL}/zones`);
        const data = await response.json();
        setZones(data.zones);
      } catch (error) {
        console.error('Error fetching zones:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchZones();
  }, []);

  const handleZoneClick = (zoneId, zoneSlug) => {
    navigate(`/explore/${zoneSlug}`, { state: { zoneId } });
  };

  if (loading) return <div className="loading">Loading zones...</div>;

  return (
    <div className="explore-page">
      <h1>Explore Our Locations</h1>
      <p>Select a city to browse restaurants</p>

      <div className="zones-grid">
        {zones.map(zone => (
          <div
            key={zone.id}
            className="zone-card"
            onClick={() => handleZoneClick(zone.id, zone.slug)}
          >
            <div className="zone-image">
              <img src={zone.image || '/default-zone.jpg'} alt={zone.name} />
            </div>
            <div className="zone-info">
              <h3>{zone.name}</h3>
              <div className="zone-stats">
                <span className="restaurant-count">
                  {zone.active_restaurants} Active Restaurants
                </span>
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
};

export default ExplorePage;
```

### 2. Zone Restaurants Page
```jsx
// pages/ZoneRestaurants.jsx
import React, { useEffect, useState } from 'react';
import { useParams, useNavigate, useLocation } from 'react-router-dom';

const ZoneRestaurantsPage = () => {
  const { zoneSlug } = useParams();
  const location = useLocation();
  const navigate = useNavigate();

  const [restaurants, setRestaurants] = useState([]);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);

  const zoneId = location.state?.zoneId || 1;

  useEffect(() => {
    fetchRestaurants();
  }, [zoneId, page]);

  const fetchRestaurants = async () => {
    try {
      setLoading(true);
      const limit = 12;
      const offset = (page - 1) * limit + 1;

      const response = await fetch(
        `${API_BASE_URL}/restaurants/get-restaurants/all?limit=${limit}&offset=${offset}`,
        {
          headers: {
            'Content-Type': 'application/json',
            'zoneId': zoneId.toString()
          }
        }
      );

      const data = await response.json();

      if (page === 1) {
        setRestaurants(data.restaurants || []);
      } else {
        setRestaurants(prev => [...prev, ...(data.restaurants || [])]);
      }

      setHasMore(data.restaurants?.length === limit);

    } catch (error) {
      console.error('Error fetching restaurants:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleRestaurantClick = (restaurantSlug) => {
    navigate(`/${restaurantSlug}`);
  };

  return (
    <div className="zone-restaurants-page">
      <div className="zone-header">
        <button onClick={() => navigate('/explore')} className="back-button">
          ← Back to Zones
        </button>
        <h1>Restaurants in {zoneSlug}</h1>
      </div>

      <div className="restaurants-grid">
        {restaurants.map(restaurant => (
          <RestaurantCard
            key={restaurant.id}
            restaurant={restaurant}
            onClick={() => handleRestaurantClick(restaurant.slug)}
          />
        ))}
      </div>

      {hasMore && (
        <button onClick={() => setPage(p => p + 1)} disabled={loading}>
          Load More
        </button>
      )}
    </div>
  );
};

// Restaurant Card Component
const RestaurantCard = ({ restaurant, onClick }) => {
  return (
    <div className="restaurant-card" onClick={onClick}>
      <div className="restaurant-image">
        <img
          src={restaurant.cover_photo || '/default-restaurant.jpg'}
          alt={restaurant.name}
          loading="lazy"
        />
      </div>
      <div className="restaurant-info">
        <h3>{restaurant.name}</h3>
        <div className="rating">
          ⭐ {restaurant.avg_rating || 'New'}
        </div>
        <div className="delivery-info">
          {restaurant.delivery_time} • ${restaurant.minimum_order} min
        </div>
      </div>
    </div>
  );
};

export default ZoneRestaurantsPage;
```

### 3. Restaurant Profile Component
```jsx
// pages/RestaurantProfile.jsx
import React, { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';

const RestaurantProfile = () => {
  const { slug } = useParams();
  const [restaurant, setRestaurant] = useState(null);
  const [menu, setMenu] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchRestaurantData = async () => {
      try {
        // Get restaurant by slug (will need backend endpoint)
        const response = await fetch(
          `${API_BASE_URL}/restaurants/by-slug/${slug}`
        );
        const restaurantData = await response.json();
        setRestaurant(restaurantData);

        // Get menu items for each category
        const categoryPromises = restaurantData.category_ids.map(cat =>
          fetch(
            `${API_BASE_URL}/categories/products/${cat.id}?limit=100&offset=1`,
            { headers: { 'zoneId': restaurantData.zone_id.toString() } }
          ).then(res => res.json())
        );

        const categoryProducts = await Promise.all(categoryPromises);
        setMenu(categoryProducts);

      } catch (error) {
        console.error('Error fetching restaurant:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchRestaurantData();
  }, [slug]);

  if (loading) return <div>Loading...</div>;
  if (!restaurant) return <div>Restaurant not found</div>;

  return (
    <div className="restaurant-profile">
      <div className="restaurant-header">
        <img src={restaurant.cover_photo} alt={restaurant.name} />
        <div className="restaurant-details">
          <h1>{restaurant.name}</h1>
          <p>{restaurant.address}</p>
          <div className="meta-info">
            <span>⭐ {restaurant.avg_rating}</span>
            <span>🚚 {restaurant.delivery_time}</span>
            <span>Min: ${restaurant.minimum_order}</span>
          </div>
        </div>
      </div>

      <div className="restaurant-menu">
        <h2>Menu</h2>
        {menu.map((category, idx) => (
          <div key={idx} className="category-section">
            <h3>{category.category_name}</h3>
            <div className="menu-items">
              {category.products?.map(item => (
                <div key={item.id} className="menu-item">
                  <img src={item.image} alt={item.name} />
                  <div className="item-info">
                    <h4>{item.name}</h4>
                    <p>{item.description}</p>
                    <span className="price">${item.price}</span>
                  </div>
                </div>
              ))}
            </div>
          </div>
        ))}
      </div>
    </div>
  );
};

export default RestaurantProfile;
```

### 4. React Router Setup
```jsx
// App.jsx
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import HomePage from './pages/HomePage';
import ExplorePage from './pages/Explore';
import ZoneRestaurantsPage from './pages/ZoneRestaurants';
import RestaurantProfile from './pages/RestaurantProfile';

function App() {
  return (
    <Router>
      <Routes>
        {/* Home page */}
        <Route path="/" element={<HomePage />} />

        {/* Explore zones */}
        <Route path="/explore" element={<ExplorePage />} />

        {/* Zone restaurants */}
        <Route path="/explore/:zoneSlug" element={<ZoneRestaurantsPage />} />

        {/* Restaurant profile */}
        <Route path="/:slug" element={<RestaurantProfile />} />
      </Routes>
    </Router>
  );
}

export default App;
```

## CORS Configuration

Make sure your Laravel backend allows CORS from your React app domain:

```php
// In config/cors.php
'paths' => ['api/*'],
'allowed_origins' => [
    'http://localhost:3000', // Development
    'https://hopa.delivery'   // Production
],
```

## Rate Limiting

The API has rate limiting configured. Default limits:
- 60 requests per minute for unauthenticated users
- Consider implementing request caching in your React app

## Error Handling

```javascript
const apiCall = async (endpoint, options = {}) => {
  try {
    const response = await fetch(`${API_BASE_URL}${endpoint}`, {
      ...options,
      headers: {
        ...headers,
        ...options.headers
      }
    });

    if (!response.ok) {
      throw new Error(`API Error: ${response.status}`);
    }

    return await response.json();
  } catch (error) {
    console.error('API Call Failed:', error);
    throw error;
  }
};
```

## Testing the API

Use tools like Postman or curl to test endpoints:

```bash
# Test restaurant details
curl -H "zoneId: 1" \
  https://admin.hopa.delivery/api/v1/restaurants/details/1

# Test restaurant search
curl -H "zoneId: 1" \
  "https://admin.hopa.delivery/api/v1/restaurants/search?name=pizza&limit=10&offset=1"
```

## Next Steps

1. **Add Missing Endpoints** (in Laravel backend):
   - Restaurant schedules endpoint
   - Restaurant by slug endpoint (optional)

2. **Build React App**:
   - Set up React Router for slug-based routing
   - Create restaurant profile components
   - Implement menu display with categories
   - Add search and filtering

3. **Performance Optimization**:
   - Implement React Query or SWR for caching
   - Use lazy loading for images
   - Implement infinite scroll for large menus

4. **Deployment**:
   - Deploy React app to Vercel/Netlify
   - Configure custom domain (hopa.delivery)
   - Set up SSL certificates
   - Configure CDN for assets

---

**Note**: This guide is based on the current API structure as of November 2024. Always refer to the latest API documentation for updates.