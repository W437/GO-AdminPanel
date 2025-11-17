# Feature Requirement: Explore & Restaurant Profile Pages

## 🎯 Overview

Add two new pages to the Hopa.delivery React website:
1. **Explore Page** - Browse restaurants by delivery zones
2. **Restaurant Profile Page** - Individual restaurant details with full menu

**Integration:** Use existing website header and footer components for consistency.

---

## 🌐 API Information

### **Base URL**
```
https://api.hopa.delivery/api/v1
```

### **Required Headers**
```
Accept: application/json
Content-Type: application/json
zoneId: {user_selected_zone_id}  // Required for restaurant endpoints
```

### **Authentication**
None required - all endpoints are public.

---

## 📡 Available API Endpoints

### **1. Get All Zones**
```
GET /api/v1/public/zones
```

**Response Structure:**
```json
{
  "zones": [
    {
      "id": 1,
      "name": "Tel Aviv Central",
      "display_name": "Tel Aviv - Center",
      "restaurant_count": 45,
      "minimum_shipping_charge": 10.00,
      "per_km_shipping_charge": 2.50,
      "maximum_shipping_charge": 50.00,
      "increased_delivery_fee": 5.00,
      "increased_delivery_fee_status": true,
      "status": 1
    }
  ],
  "total_zones": 5
}
```

### **2. Get Zone Details**
```
GET /api/v1/public/zones/{id}
```

**Response includes:**
- Zone information (name, delivery charges, surge fees)
- List of restaurants in this zone (up to 20)
- Restaurant count
- Delivery charge information

### **3. Get Restaurant by Slug** (SEO-friendly)
```
GET /api/v1/public/restaurants/by-slug/{slug}
```

**Example:** `/api/v1/public/restaurants/by-slug/pizza-palace`

**Response includes:**
- Restaurant details (name, phone, email, address, location)
- Images (logo, cover photo) with full URLs
- Rating, delivery time, minimum order
- Cuisines array
- Status (active/inactive)

**Headers required:** `zoneId: {zone_id}`

### **4. Get Restaurant Operating Hours**
```
GET /api/v1/public/restaurants/{id}/schedules
```

**Response includes:**
- Array of 7 days (Sunday=0 to Saturday=6)
- Each day: opening time, closing time, day name
- Boolean indicating if currently open
- Opening/closing times in HH:MM:SS format

### **5. Get Complete Restaurant Menu**
```
GET /api/v1/public/restaurants/{id}/menu
```

**Response includes:**
- Restaurant ID and name
- Categories array, each containing:
  - Category info (id, name, image)
  - Products count
  - Products array with:
    - Product details (name, description, price)
    - Discount info (amount, type: percent/fixed)
    - Images
    - Attributes (veg, recommended, rating)
    - Availability times
- Total products count

**Headers required:** `zoneId: {zone_id}`

---

## 📄 Page 1: Explore Page

### **Purpose**
Allow users to browse and select delivery zones, then view restaurants available in each zone.

### **URL Structure**
```
/explore
/explore/{zone-name-slug}
```

### **Page Sections Required**

**1. Hero Section**
- Page title: "Explore Restaurants in Your Area"
- Subtitle explaining zone-based delivery
- Optional: Search bar for address/zone search

**2. Zone Grid Section**
Display all available zones as cards in a responsive grid.

**Each Zone Card Should Show:**
- Zone name (use display_name if available, fallback to name)
- Number of restaurants
- Delivery charge range (min to max)
- Surge fee indicator if `increased_delivery_fee_status` is true
- Visual indication if selected
- Clickable to view restaurants in that zone

**3. Selected Zone Details Section** (Appears when zone is clicked)
- Zone name as heading
- Restaurant count
- Delivery charge information
- Surge fee message if applicable
- Grid of restaurant cards for this zone

**Each Restaurant Card Should Show:**
- Restaurant logo/image
- Restaurant name
- Rating
- Delivery time
- Minimum order amount
- Click to navigate to restaurant profile page
- Use restaurant slug in URL

### **Functionality Requirements**

**On Page Load:**
- Fetch all zones
- Display zone grid
- Show loading states while fetching

**When Zone Clicked:**
- Update URL to `/explore/{zone-slug}`
- Fetch zone details with restaurants
- Highlight selected zone visually
- Scroll to restaurant grid
- Display restaurants in responsive grid

**When Restaurant Clicked:**
- Navigate to `/restaurant/{restaurant-slug}`

**Responsive Design:**
- Mobile: Single column or horizontal scroll for zones
- Tablet: 2-3 columns
- Desktop: 3-4 columns
- Maintain touch-friendly spacing on mobile

---

## 📄 Page 2: Restaurant Profile Page

### **Purpose**
Display complete restaurant information including details, operating hours, and full menu.

### **URL Structure**
```
/restaurant/{restaurant-slug}
```

Examples:
- `/restaurant/pizza-palace`
- `/restaurant/sushi-bar-tel-aviv`

### **Page Sections Required**

**1. Restaurant Hero Section**

Display:
- Cover photo (full-width banner)
- Restaurant logo (overlapping cover photo)
- Restaurant name (prominent heading)
- Rating with review count
- Delivery time estimate
- Minimum order amount
- Zone/location name
- Cuisine tags
- Current open/closed status (prominent visual indicator)

**2. Operating Hours Section**

Display:
- Weekly schedule (all 7 days)
- Opening and closing times for each day
- Visual indication of current day
- Prominent "Open Now" or "Closed" status
- If closed, show when they open next
- Handle days with no hours (display as "Closed")

**Day Mapping:**
- day: 0 = Sunday
- day: 1 = Monday
- ... up to 6 = Saturday

**3. Menu Section**

**Category Navigation:**
- Sticky/fixed category tabs showing all categories
- Category names with item counts
- Clicking category scrolls to that section
- Active category highlighted
- Horizontal scroll on mobile

**Menu Display:**
- Organized by categories
- Each category as a distinct section
- Category heading with icon/image if available
- Product count per category

**Each Product Card Should Show:**
- Product image
- Product name
- Description (truncated if long)
- Price
- Discount information:
  - Original price (strikethrough if discounted)
  - Final price (prominently displayed)
  - Discount percentage/amount badge
- Rating (if available)
- Vegetarian indicator (if veg: true)
- Recommended badge (if recommended: true)
- Add to cart functionality (quantity selector)
- Availability times (if specified)

**Menu Features:**
- Search within menu (filter products)
- Sticky category navigation while scrolling
- Auto-update active category based on scroll position
- Smooth scroll to categories
- Empty state if no products

### **Functionality Requirements**

**On Page Load:**
- Extract slug from URL parameter
- Fetch restaurant by slug
- If 404, redirect to explore page or show error
- Fetch operating hours (parallel request)
- Fetch complete menu (parallel request)
- Show loading states during fetch
- Once loaded, scroll to top

**Operating Hours Logic:**
- Get current day of week (0-6)
- Get current time
- Compare with schedule to determine if open
- Show next opening time if currently closed
- Highlight current day in schedule display

**Menu Interaction:**
- Category tabs fixed at top when scrolling through menu
- Clicking category scrolls to that menu section
- As user scrolls, update active category indicator
- Search filters products across all categories in real-time

**Product Interaction:**
- Add to cart starts at 0
- Plus button increments
- Minus button decrements (minimum 0)
- Quantity selector appears after first add
- Cart icon/total updates (if cart system exists)

**Error Handling:**
- Restaurant not found (404) → Redirect or show error page
- API failure → Show user-friendly error message
- Network issues → Allow retry
- Missing images → Use placeholder images

---

## 🎨 Design Requirements

### **Visual Hierarchy**
- Restaurant name: Most prominent
- Current open/closed status: Highly visible (green for open, red for closed)
- Prices: Clear and readable
- Discounts: Highlighted with contrasting color
- Recommended items: Special badge/indicator

### **Color Usage**
- Success/Open: Green tones
- Error/Closed: Red tones
- Discounts: Red or orange for savings
- Recommended: Yellow/gold badges
- Veg indicator: Green
- Primary actions: Brand blue

### **Layout Principles**
- Clean, uncluttered design
- Generous white space
- Card-based components
- Clear visual grouping
- Consistent spacing
- Touch-friendly targets on mobile (minimum 44x44px)

### **Image Guidelines**
- Restaurant images: High quality, fill container
- Product images: Square format preferred
- Fallback placeholders for missing images
- Lazy loading for performance
- Proper alt text for accessibility

---

## 📱 Responsive Requirements

### **Breakpoints**
- Mobile: < 768px
- Tablet: 768px - 1024px
- Desktop: > 1024px

### **Mobile-Specific**
- Single column layouts
- Horizontal scrolling category tabs
- Collapsible operating hours
- Bottom-sticky add to cart
- Touch-optimized spacing

### **Desktop-Specific**
- Multi-column grids
- Sticky category sidebar (optional)
- Hover states
- Wider containers

---

## 🔍 SEO Requirements

### **Restaurant Page SEO**
- Page title: "{Restaurant Name} - Order Online | Hopa.delivery"
- Meta description: Use restaurant description or generate
- Open Graph tags for social sharing
- Canonical URL using slug
- Structured data (Schema.org Restaurant)

### **URL Structure**
- Clean, readable slugs
- No IDs in URLs (use slugs only)
- Hyphens for word separation
- Lowercase only

---

## ⚡ Performance Requirements

- Initial page load: < 3 seconds
- Image lazy loading
- Parallel API requests where possible
- Minimize re-renders
- Debounced search (if implemented)
- Optimized images (WebP preferred)

---

## ✅ Acceptance Criteria

### **Explore Page Must:**
1. Display all available delivery zones
2. Show restaurant count per zone
3. Show delivery fee ranges
4. Allow zone selection
5. Display restaurants when zone selected
6. Link to restaurant pages using slugs
7. Work on mobile, tablet, and desktop
8. Show loading states
9. Handle empty zones gracefully
10. Integrate with existing header/footer

### **Restaurant Page Must:**
1. Load via SEO-friendly slug URL
2. Display restaurant information clearly
3. Show accurate open/closed status
4. Display weekly operating hours
5. Show complete menu organized by categories
6. Allow category navigation
7. Display products with all details
8. Show discounts and special badges
9. Enable add to cart functionality
10. Handle 404 for invalid slugs
11. Work on all screen sizes
12. Have proper SEO meta tags
13. Integrate with existing header/footer

---

## 🎁 Optional Enhancements (Nice to Have)

- Recently viewed restaurants
- Favorite/save restaurants
- Share restaurant functionality
- Filter restaurants (by rating, delivery time, etc.)
- Search within menu
- Print menu option
- Scroll to top button
- Breadcrumb navigation
- Related/similar restaurants
- Customer reviews section

---

## 📞 Technical Notes

**API Behavior:**
- All endpoints return JSON
- Status codes: 200 (success), 404 (not found), 500 (server error)
- No authentication tokens needed
- CORS configured for hopa.delivery domain

**Data Notes:**
- Zone IDs are integers
- Restaurant slugs are unique (enforced at database)
- Prices in Israeli Shekels (₪)
- Times in 24-hour format (HH:MM:SS)
- Ratings are decimal numbers (0-5)
- Discounts can be percentage or fixed amount

**Image URLs:**
- Use `*_full_url` fields when available
- Construct URLs for legacy data: `https://api.hopa.delivery/storage/restaurant/{filename}`
- Always have fallback placeholder images

---

## 🚀 Implementation Priority

1. **Phase 1:** Explore Page with zone browsing
2. **Phase 2:** Restaurant Profile Page basics (hero, hours, menu)
3. **Phase 3:** Enhanced features (search, filters, animations)

---

## 📋 Deliverables

- Two fully functional pages
- Mobile-responsive design
- SEO-optimized
- Integrated with existing website
- Error handling implemented
- Loading states for all API calls

**Questions? Check API documentation at:** https://admin.hopa.delivery/docs (admin login required)

---

**Ready to build!** 🚀
