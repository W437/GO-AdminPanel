# API: Dietary Preference Filtering

## Overview
This document describes the dietary preference filtering API endpoints added to the Hopa! backend. These endpoints allow filtering food items by dietary tags such as Vegan, Gluten-Free, Kosher, etc.

**Base URL:** `https://hq-secure-panel-1337.hopa.delivery/api/v1`

---

## Available Dietary Preferences

### System Dietary Preferences (19 total)

All dietary preferences are stored in the `dietary_preferences` table with the following structure:

```json
{
  "id": 1,
  "name": "Vegan",
  "type": "diet",
  "status": 1
}
```

### Grouped by Type:

#### **Diet Type** (type: `diet`) - 8 options
| ID | Name | Type |
|----|------|------|
| 1  | Vegan | diet |
| 2  | Pescatarian | diet |
| 3  | Egg-Free | diet |
| 4  | Dairy-Free | diet |
| 5  | Sugar-Free | diet |
| 6  | Low-Carb | diet |
| 7  | Keto-Friendly | diet |
| 8  | High-Protein | diet |

#### **Cultural Preferences** (type: `religious`) - 2 options
| ID | Name | Type |
|----|------|------|
| 9  | Kosher | religious |
| 10 | No Alcohol | religious |

#### **Allergy-Free Options** (type: `allergy`) - 6 options
| ID | Name | Type |
|----|------|------|
| 11 | Gluten-Free | allergy |
| 12 | Nut-Free | allergy |
| 13 | Lactose-Free | allergy |
| 14 | Soy-Free | allergy |
| 15 | Shellfish-Free | allergy |
| 16 | Sesame-Free | allergy |

#### **Other Attributes** (type: `other`) - 3 options
| ID | Name | Type |
|----|------|------|
| 17 | Organic | other |
| 18 | Healthy | other |
| 19 | Light | other |

---

## Updated API Endpoints

All product list endpoints now support the `dietary_preferences` parameter.

### 1. Latest Products

**Endpoint:** `GET /api/v1/products/latest`

**Headers:**
```
zoneId: [zone_id]  (required)
```

**Parameters:**
```
restaurant_id    integer  required    Restaurant ID
category_id      integer  required    Category ID (0 for all)
limit            integer  required    Items per page
offset           integer  required    Page number
type             string   optional    all|veg|non_veg (default: all)
dietary_preferences  string|array  optional  Comma-separated IDs or array
```

**Example Requests:**
```http
# Comma-separated format
GET /api/v1/products/latest?restaurant_id=1&category_id=0&limit=10&offset=1&dietary_preferences=1,11,17

# Array format
GET /api/v1/products/latest?restaurant_id=1&category_id=0&limit=10&offset=1&dietary_preferences[]=1&dietary_preferences[]=11

# Combined with type filter
GET /api/v1/products/latest?restaurant_id=1&category_id=0&limit=10&offset=1&type=veg&dietary_preferences=1
```

---

### 2. Popular Products

**Endpoint:** `GET /api/v1/products/popular`

**Headers:**
```
zoneId: [zone_id]  (required)
longitude: [longitude]  (optional)
latitude: [latitude]  (optional)
```

**Parameters:**
```
limit               integer  optional    Items per page (default: 10)
offset              integer  optional    Page number (default: 1)
type                string   optional    all|veg|non_veg (default: all)
dietary_preferences string|array  optional  Dietary preference IDs
```

**Example:**
```http
GET /api/v1/products/popular?limit=20&offset=1&dietary_preferences=1,11
Headers: zoneId: 1
```

---

### 3. Search Products

**Endpoint:** `GET /api/v1/products/search`

**Headers:**
```
zoneId: [zone_id]  (required)
```

**Parameters:**
```
name                string   optional    Search query
limit               integer  optional    Items per page
offset              integer  optional    Page number
type                string   optional    all|veg|non_veg
category_id         integer  optional    Filter by category
restaurant_id       integer  optional    Filter by restaurant
min_price           float    optional    Minimum price
max_price           float    optional    Maximum price
rating_1 to rating_5  boolean  optional    Filter by rating
dietary_preferences string|array  optional  Dietary preference IDs
```

**Example:**
```http
GET /api/v1/products/search?name=pizza&dietary_preferences=1,11&type=veg&min_price=5&max_price=20
Headers: zoneId: 1
```

---

### 4. Most Reviewed Products

**Endpoint:** `GET /api/v1/products/most-reviewed`

**Headers:**
```
zoneId: [zone_id]  (required)
longitude: [longitude]  (optional)
latitude: [latitude]  (optional)
```

**Parameters:**
```
limit               integer  optional    Items per page
offset              integer  optional    Page number
type                string   optional    all|veg|non_veg
dietary_preferences string|array  optional  Dietary preference IDs
```

**Example:**
```http
GET /api/v1/products/most-reviewed?limit=10&offset=1&dietary_preferences=1,9
Headers: zoneId: 1
```

---

### 5. Recommended Products

**Endpoint:** `GET /api/v1/products/recommended`

**Headers:**
```
zoneId: [zone_id]  (required)
```

**Parameters:**
```
restaurant_id       integer  required    Restaurant ID
limit               integer  optional    Items per page
offset              integer  optional    Page number
type                string   optional    all|veg|non_veg
name                string   optional    Search term
dietary_preferences string|array  optional  Dietary preference IDs
```

**Example:**
```http
GET /api/v1/products/recommended?restaurant_id=5&limit=10&offset=1&dietary_preferences=1,4,11
Headers: zoneId: 1
```

---

## API Response Format

### Product Object with Dietary Preferences

```json
{
  "id": 123,
  "name": "Vegan Buddha Bowl",
  "description": "Healthy plant-based bowl",
  "price": 15.99,
  "image": "https://...",
  "veg": 1,
  "is_halal": 1,
  "restaurant_id": 5,
  "category_ids": [{"id": "3", "position": 1}],

  "dietary_preferences": {
    "diet": [
      {"id": 1, "name": "Vegan", "type": "diet"},
      {"id": 4, "name": "Dairy-Free", "type": "diet"},
      {"id": 8, "name": "High-Protein", "type": "diet"}
    ],
    "allergy": [
      {"id": 11, "name": "Gluten-Free", "type": "allergy"},
      {"id": 12, "name": "Nut-Free", "type": "allergy"}
    ],
    "other": [
      {"id": 17, "name": "Organic", "type": "other"}
    ]
  },

  "nutritions_name": ["Protein", "Fiber"],
  "allergies_name": ["Contains Garlic"],
  "avg_rating": 4.5,
  "rating_count": 23,
  "restaurant_name": "Green Kitchen",
  "restaurant_status": 1
}
```

**Key Points:**
- ✅ `dietary_preferences` is an object grouped by type
- ✅ Each type contains an array of preference objects
- ✅ Empty types are excluded (e.g., if no cultural preferences, that key won't exist)
- ✅ If product has no dietary preferences: `dietary_preferences: {}`

---

## Filtering Logic

### AND Logic (Intersection)

**How it works:**
- Selecting multiple dietary preferences uses **AND logic**
- Food must have **ALL** selected preferences to match

**Example:**
```
User selects: Vegan (1) + Gluten-Free (11) + Organic (17)

Foods returned:
✅ Pizza with tags: [Vegan, Gluten-Free, Organic, Nut-Free]  // Has all 3
✅ Salad with tags: [Vegan, Gluten-Free, Organic]  // Has exactly 3
❌ Burger with tags: [Vegan, Gluten-Free]  // Missing Organic
❌ Pasta with tags: [Vegan, Organic]  // Missing Gluten-Free
```

### Combining with Existing Filters

**All filters work together:**

```http
GET /api/v1/products/search
  ?name=pizza
  &type=veg
  &min_price=10
  &max_price=30
  &rating_4=1
  &dietary_preferences=1,11

Result: Veg pizzas, priced $10-$30, rated 4+, that are Vegan AND Gluten-Free
```

---

## Implementation Guide for Frontend

### Step 1: Get Available Dietary Preferences

**Create an endpoint or use static data:**

```dart
// Option A: Static data (since preferences are predefined)
final List<DietaryPreference> dietaryPreferences = [
  DietaryPreference(id: 1, name: 'Vegan', type: 'diet'),
  DietaryPreference(id: 2, name: 'Pescatarian', type: 'diet'),
  // ... all 19 options
];

// Option B: Fetch from backend (future endpoint)
// GET /api/v1/dietary-preferences
// Returns: [{ "id": 1, "name": "Vegan", "type": "diet" }, ...]
```

---

### Step 2: Build Filter UI

**Recommended UI Pattern:**

```
┌─────────────────────────────────────┐
│ 🔍 Filters                          │
├─────────────────────────────────────┤
│                                     │
│ Diet Type                           │
│ ☐ Vegan                            │
│ ☐ Pescatarian                      │
│ ☐ Egg-Free                         │
│ ☐ Dairy-Free                       │
│ ...                                 │
│                                     │
│ Cultural Preferences                │
│ ☐ Kosher                           │
│ ☐ No Alcohol                       │
│                                     │
│ Allergy-Free                        │
│ ☐ Gluten-Free                      │
│ ☐ Nut-Free                         │
│ ...                                 │
│                                     │
│ Other                               │
│ ☐ Organic                          │
│ ☐ Healthy                          │
│ ☐ Light                            │
│                                     │
│ [Apply Filters]  [Clear]           │
└─────────────────────────────────────┘
```

---

### Step 3: Build API Request

**Flutter/Dart Example:**

```dart
class ProductService {
  Future<ProductListResponse> getProducts({
    required int restaurantId,
    int categoryId = 0,
    int limit = 10,
    int offset = 1,
    String type = 'all',
    List<int>? dietaryPreferences,
  }) async {
    // Build query parameters
    final queryParams = {
      'restaurant_id': restaurantId.toString(),
      'category_id': categoryId.toString(),
      'limit': limit.toString(),
      'offset': offset.toString(),
      'type': type,
    };

    // Add dietary preferences if selected
    if (dietaryPreferences != null && dietaryPreferences.isNotEmpty) {
      // Option 1: Comma-separated (recommended)
      queryParams['dietary_preferences'] = dietaryPreferences.join(',');

      // Option 2: Array format (also supported)
      // No need to add manually, use Uri.https with List
    }

    final uri = Uri.https(
      'hq-secure-panel-1337.hopa.delivery',
      '/api/v1/products/latest',
      queryParams,
    );

    final response = await http.get(
      uri,
      headers: {
        'zoneId': zoneId.toString(),
        'Accept': 'application/json',
      },
    );

    // Parse response
    return ProductListResponse.fromJson(jsonDecode(response.body));
  }
}
```

**JavaScript/React Example:**

```javascript
async function getProducts({
  restaurantId,
  categoryId = 0,
  limit = 10,
  offset = 1,
  type = 'all',
  dietaryPreferences = []
}) {
  const params = new URLSearchParams({
    restaurant_id: restaurantId,
    category_id: categoryId,
    limit,
    offset,
    type
  });

  // Add dietary preferences
  if (dietaryPreferences.length > 0) {
    // Option 1: Comma-separated
    params.append('dietary_preferences', dietaryPreferences.join(','));

    // Option 2: Multiple params (also works)
    // dietaryPreferences.forEach(id => {
    //   params.append('dietary_preferences[]', id);
    // });
  }

  const response = await fetch(
    `https://hq-secure-panel-1337.hopa.delivery/api/v1/products/latest?${params}`,
    {
      headers: {
        'zoneId': zoneId,
        'Accept': 'application/json'
      }
    }
  );

  return await response.json();
}
```

---

### Step 4: Parse Response

**Dart Model:**

```dart
class Food {
  final int id;
  final String name;
  final double price;
  final bool veg;
  final bool isHalal;
  final Map<String, List<DietaryPreference>> dietaryPreferences;

  Food.fromJson(Map<String, dynamic> json)
      : id = json['id'],
        name = json['name'],
        price = double.parse(json['price'].toString()),
        veg = json['veg'] == 1,
        isHalal = json['is_halal'] == 1,
        dietaryPreferences = _parseDietaryPreferences(json['dietary_preferences']);

  static Map<String, List<DietaryPreference>> _parseDietaryPreferences(
    dynamic data
  ) {
    if (data == null || data is! Map) return {};

    final Map<String, List<DietaryPreference>> result = {};

    data.forEach((type, prefs) {
      if (prefs is List) {
        result[type] = prefs
            .map((p) => DietaryPreference.fromJson(p as Map<String, dynamic>))
            .toList();
      }
    });

    return result;
  }
}

class DietaryPreference {
  final int id;
  final String name;
  final String type;

  DietaryPreference({
    required this.id,
    required this.name,
    required this.type,
  });

  factory DietaryPreference.fromJson(Map<String, dynamic> json) {
    return DietaryPreference(
      id: json['id'],
      name: json['name'],
      type: json['type'],
    );
  }
}
```

---

### Step 5: Display Dietary Tags

**UI Pattern:**

```dart
Widget buildDietaryTags(Food food) {
  final allPrefs = <DietaryPreference>[];

  // Flatten all dietary preferences
  food.dietaryPreferences.forEach((type, prefs) {
    allPrefs.addAll(prefs);
  });

  if (allPrefs.isEmpty) {
    return SizedBox.shrink();
  }

  return Wrap(
    spacing: 4,
    runSpacing: 4,
    children: allPrefs.map((pref) {
      return Chip(
        label: Text(pref.name),
        backgroundColor: _getColorForType(pref.type),
        labelStyle: TextStyle(fontSize: 10),
      );
    }).toList(),
  );
}

Color _getColorForType(String type) {
  switch (type) {
    case 'diet':
      return Colors.green.shade100;
    case 'religious':
      return Colors.purple.shade100;
    case 'allergy':
      return Colors.orange.shade100;
    case 'other':
      return Colors.blue.shade100;
    default:
      return Colors.grey.shade200;
  }
}
```

---

## Complete Request/Response Examples

### Example 1: Basic Filtering

**Request:**
```http
GET /api/v1/products/latest
  ?restaurant_id=1
  &category_id=0
  &limit=10
  &offset=1
  &dietary_preferences=1

Headers:
  zoneId: 1
```

**Response:**
```json
{
  "total_size": 5,
  "limit": 10,
  "offset": 1,
  "products": [
    {
      "id": 123,
      "name": "Vegan Pizza",
      "price": 15.99,
      "veg": 1,
      "is_halal": 1,
      "dietary_preferences": {
        "diet": [
          {"id": 1, "name": "Vegan", "type": "diet"}
        ]
      },
      "avg_rating": 4.5,
      "rating_count": 23
    }
  ]
}
```

---

### Example 2: Multiple Dietary Preferences

**Request:**
```http
GET /api/v1/products/popular
  ?limit=20
  &offset=1
  &dietary_preferences=1,11,9

Headers:
  zoneId: 1
```

**Meaning:** Find popular foods that are:
- Vegan (ID: 1) **AND**
- Gluten-Free (ID: 11) **AND**
- Kosher (ID: 9)

**Response:**
```json
{
  "total_size": 2,
  "limit": 20,
  "offset": 1,
  "products": [
    {
      "id": 456,
      "name": "Quinoa Salad",
      "dietary_preferences": {
        "diet": [
          {"id": 1, "name": "Vegan", "type": "diet"}
        ],
        "religious": [
          {"id": 9, "name": "Kosher", "type": "religious"}
        ],
        "allergy": [
          {"id": 11, "name": "Gluten-Free", "type": "allergy"}
        ]
      }
    }
  ]
}
```

---

### Example 3: Combined Filters

**Request:**
```http
GET /api/v1/products/search
  ?name=pizza
  &type=veg
  &min_price=10
  &max_price=25
  &rating_4=1
  &dietary_preferences=1,11,17

Headers:
  zoneId: 1
```

**Filters applied:**
1. Name contains "pizza"
2. Type is Vegetarian
3. Price between $10-$25
4. Rating 4+
5. Has Vegan + Gluten-Free + Organic tags

---

## Filter UI Implementation Patterns

### Pattern 1: Chip/Tag Selection

```dart
class DietaryFilterChips extends StatefulWidget {
  final Function(List<int>) onChanged;

  @override
  _DietaryFilterChipsState createState() => _DietaryFilterChipsState();
}

class _DietaryFilterChipsState extends State<DietaryFilterChips> {
  Set<int> selectedIds = {};

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text('Diet Type', style: TextStyle(fontWeight: FontWeight.bold)),
        Wrap(
          spacing: 8,
          children: dietPreferences.where((p) => p.type == 'diet').map((pref) {
            final isSelected = selectedIds.contains(pref.id);
            return FilterChip(
              label: Text(pref.name),
              selected: isSelected,
              onSelected: (selected) {
                setState(() {
                  if (selected) {
                    selectedIds.add(pref.id);
                  } else {
                    selectedIds.remove(pref.id);
                  }
                  widget.onChanged(selectedIds.toList());
                });
              },
            );
          }).toList(),
        ),
      ],
    );
  }
}
```

---

### Pattern 2: Dropdown/Sheet Selection

```dart
void showDietaryFilters(BuildContext context) {
  showModalBottomSheet(
    context: context,
    builder: (context) {
      return StatefulBuilder(
        builder: (context, setState) {
          return ListView(
            children: [
              _buildFilterSection('Diet Type', 'diet', setState),
              _buildFilterSection('Cultural Preferences', 'religious', setState),
              _buildFilterSection('Allergy-Free', 'allergy', setState),
              _buildFilterSection('Other', 'other', setState),

              Padding(
                padding: EdgeInsets.all(16),
                child: ElevatedButton(
                  onPressed: () {
                    Navigator.pop(context);
                    _applyFilters();
                  },
                  child: Text('Apply Filters'),
                ),
              ),
            ],
          );
        },
      );
    },
  );
}

Widget _buildFilterSection(String title, String type, StateSetter setState) {
  final prefs = dietaryPreferences.where((p) => p.type == type).toList();

  return ExpansionTile(
    title: Text(title),
    children: prefs.map((pref) {
      return CheckboxListTile(
        title: Text(pref.name),
        value: selectedDietaryIds.contains(pref.id),
        onChanged: (checked) {
          setState(() {
            if (checked == true) {
              selectedDietaryIds.add(pref.id);
            } else {
              selectedDietaryIds.remove(pref.id);
            }
          });
        },
      );
    }).toList(),
  );
}
```

---

### Pattern 3: Badge Display

**Show dietary tags on food cards:**

```dart
Widget buildFoodCard(Food food) {
  return Card(
    child: Column(
      children: [
        Image.network(food.image),
        Text(food.name),
        Text('\$${food.price}'),

        // Dietary preference badges
        if (food.dietaryPreferences.isNotEmpty)
          Wrap(
            spacing: 4,
            children: _getDietaryBadges(food),
          ),
      ],
    ),
  );
}

List<Widget> _getDietaryBadges(Food food) {
  final badges = <Widget>[];

  // Show max 3-4 badges to avoid clutter
  int count = 0;
  food.dietaryPreferences.forEach((type, prefs) {
    for (var pref in prefs) {
      if (count >= 3) break;
      badges.add(
        Container(
          padding: EdgeInsets.symmetric(horizontal: 6, vertical: 2),
          decoration: BoxDecoration(
            color: _getColorForType(pref.type),
            borderRadius: BorderRadius.circular(12),
          ),
          child: Text(
            pref.name,
            style: TextStyle(fontSize: 10),
          ),
        ),
      );
      count++;
    }
  });

  // Show "+X more" if there are more tags
  final totalTags = food.dietaryPreferences.values
      .fold<int>(0, (sum, list) => sum + list.length);
  if (totalTags > 3) {
    badges.add(
      Text('+${totalTags - 3} more', style: TextStyle(fontSize: 10)),
    );
  }

  return badges;
}
```

---

## Testing Checklist

### Backend API Tests

- [ ] Test with no dietary_preferences parameter (should return all products)
- [ ] Test with single preference (dietary_preferences=1)
- [ ] Test with multiple preferences (dietary_preferences=1,11,17)
- [ ] Test with array format (dietary_preferences[]=1&dietary_preferences[]=11)
- [ ] Test combining with type filter (type=veg&dietary_preferences=1)
- [ ] Test combining with search (name=pizza&dietary_preferences=1)
- [ ] Test combining with price range
- [ ] Test combining with rating filter
- [ ] Verify response includes dietary_preferences object
- [ ] Verify dietary_preferences grouped by type
- [ ] Test with invalid IDs (should ignore gracefully)
- [ ] Test with empty array (should return all products)

### Frontend Tests

- [ ] Filter UI displays all 19 dietary preferences
- [ ] Filters are grouped by type (Diet, Cultural, Allergy-Free, Other)
- [ ] Selecting filters updates API request
- [ ] Multiple selections create AND logic
- [ ] Clear filters removes all selections
- [ ] Dietary badges display on food cards
- [ ] Badge colors match dietary type
- [ ] Limit badges to 3-4 per card
- [ ] Show "+X more" for additional tags

---

## Performance Considerations

### Database Indexing

**Recommended indexes:**
```sql
-- Index on dietary_preference_food pivot table (already created via foreign keys)
-- Consider adding composite index if performance is slow:
ALTER TABLE dietary_preference_food
  ADD INDEX idx_diet_pref_lookup (dietary_preference_id, food_id);
```

### Query Optimization

**Current approach:**
- Each dietary preference adds a `whereHas()` query
- For 3 preferences = 3 EXISTS subqueries
- Reasonably fast for moderate data sizes

**If performance becomes an issue:**
- Consider caching popular dietary combinations
- Add `dietary_preferences_count` column to foods table
- Use eager loading: `Food::with('dietaryPreferences')`

---

## Migration Notes

### Existing Data

**Products without dietary preferences:**
- Will have `dietary_preferences: {}` in response
- Will NOT appear in filtered results
- Vendors need to add dietary tags to existing products

**Recommendation:**
- Add admin bulk-edit feature to tag multiple products at once
- Suggest dietary tags based on product name/description (future AI enhancement)

---

## Error Handling

### Invalid Preference IDs

**Request:**
```http
GET /api/v1/products/latest?dietary_preferences=999,1000
```

**Behavior:**
- Invalid IDs are **ignored**
- Query continues with valid IDs only
- Returns products matching valid preferences

**No error thrown** - graceful degradation

---

### Empty Results

**Request:**
```http
GET /api/v1/products/latest?dietary_preferences=1,11,17
```

**Response if no products match:**
```json
{
  "total_size": 0,
  "limit": 10,
  "offset": 1,
  "products": []
}
```

**Frontend should display:**
- "No products match your dietary preferences"
- Suggest removing some filters
- Show nearest matches (without all tags)

---

## Future Enhancements

### Phase 2: Advanced Features

1. **OR Logic Support**
   ```http
   ?dietary_preferences_any=1,2,3  // Has ANY of these
   ?dietary_preferences_all=11,12  // Has ALL of these
   ```

2. **Exclude Preferences**
   ```http
   ?dietary_preferences_exclude=10  // Does NOT have "No Alcohol"
   ```

3. **Restaurant Filtering**
   ```http
   GET /api/v1/restaurants/get-restaurants
     ?dietary_preferences=1,11
   // Returns restaurants that serve items with these preferences
   ```

4. **Dietary Preference Autocomplete**
   ```http
   GET /api/v1/dietary-preferences?search=glut
   // Returns: [{"id": 11, "name": "Gluten-Free", ...}]
   ```

5. **Popular Combinations Analytics**
   ```http
   GET /api/v1/dietary-preferences/popular
   // Returns most commonly filtered combinations
   ```

---

## Backward Compatibility

### Existing Filters Still Work

**All existing parameters continue to function:**

✅ `type=veg` / `type=non_veg` (primary categorization)
✅ `veg=1` / `non_veg=1` (in some endpoints)
✅ `name` (search query)
✅ `category_id` (category filter)
✅ `min_price` / `max_price` (price range)
✅ `rating_1` to `rating_5` (rating filters)
✅ `new=1` / `popular=1` / `rating=1` (sorting)

**New parameter is additive:**
- Old apps without `dietary_preferences` → work exactly as before
- New apps with `dietary_preferences` → get enhanced filtering

**No breaking changes!**

---

## Summary Table

| Endpoint | Supports Dietary Filtering | Format | Response Includes Tags |
|----------|---------------------------|--------|------------------------|
| `/products/latest` | ✅ Yes | comma/array | ✅ Yes |
| `/products/popular` | ✅ Yes | comma/array | ✅ Yes |
| `/products/search` | ✅ Yes | comma/array | ✅ Yes |
| `/products/most-reviewed` | ✅ Yes | comma/array | ✅ Yes |
| `/products/recommended` | ✅ Yes | comma/array | ✅ Yes |
| `/products/details/{id}` | N/A (single item) | N/A | ✅ Yes |
| `/categories/products/{id}` | 🔄 Coming soon | comma/array | ✅ Yes |

---

## Quick Start for Frontend Developers

### 1. Add Static Dietary Preferences Data

Create `lib/constants/dietary_preferences.dart`:

```dart
const List<Map<String, dynamic>> DIETARY_PREFERENCES = [
  {"id": 1, "name": "Vegan", "type": "diet"},
  {"id": 2, "name": "Pescatarian", "type": "diet"},
  {"id": 3, "name": "Egg-Free", "type": "diet"},
  {"id": 4, "name": "Dairy-Free", "type": "diet"},
  {"id": 5, "name": "Sugar-Free", "type": "diet"},
  {"id": 6, "name": "Low-Carb", "type": "diet"},
  {"id": 7, "name": "Keto-Friendly", "type": "diet"},
  {"id": 8, "name": "High-Protein", "type": "diet"},
  {"id": 9, "name": "Kosher", "type": "religious"},
  {"id": 10, "name": "No Alcohol", "type": "religious"},
  {"id": 11, "name": "Gluten-Free", "type": "allergy"},
  {"id": 12, "name": "Nut-Free", "type": "allergy"},
  {"id": 13, "name": "Lactose-Free", "type": "allergy"},
  {"id": 14, "name": "Soy-Free", "type": "allergy"},
  {"id": 15, "name": "Shellfish-Free", "type": "allergy"},
  {"id": 16, "name": "Sesame-Free", "type": "allergy"},
  {"id": 17, "name": "Organic", "type": "other"},
  {"id": 18, "name": "Healthy", "type": "other"},
  {"id": 19, "name": "Light", "type": "other"},
];
```

### 2. Update API Service

Add `dietary_preferences` parameter to all product API calls.

### 3. Update Product Model

Add `dietaryPreferences` field to Food model with proper parsing.

### 4. Build Filter UI

Create filter bottom sheet or screen with checkboxes/chips.

### 5. Display Tags

Show dietary tags as badges on food cards.

---

## Support & Questions

**For backend issues:**
- Check `docs/DIETARY_PREFERENCE_FILTERING.md` for implementation details

**For database schema:**
- Tables: `dietary_preferences`, `dietary_preference_food`
- Model: `App\Models\DietaryPreference`
- Relationship: `Food::dietaryPreferences()`

**For testing:**
- Use Postman/Insomnia with examples above
- Check response format matches documentation
- Verify AND logic (all selected tags must match)

---

**Last Updated:** November 22, 2025
**Version:** 1.0
**Status:** Production Ready ✅
