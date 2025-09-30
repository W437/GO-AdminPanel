-- Food Categories for GO Delivery App
-- Get the next available ID
SET @next_id = (SELECT COALESCE(MAX(id), 0) + 1 FROM categories);

-- Main Food Categories (Popular Items)
INSERT INTO categories (id, name, image, parent_id, position, status, priority, slug, created_at, updated_at) VALUES
(@next_id + 0, 'Pizza', 'def.png', 0, 1, 1, 10, 'pizza', NOW(), NOW()),
(@next_id + 1, 'Burgers', 'def.png', 0, 2, 1, 9, 'burgers', NOW(), NOW()),
(@next_id + 2, 'Sandwiches & Wraps', 'def.png', 0, 3, 1, 8, 'sandwiches-wraps', NOW(), NOW()),
(@next_id + 3, 'Pasta & Italian', 'def.png', 0, 4, 1, 7, 'pasta-italian', NOW(), NOW()),
(@next_id + 4, 'Sushi & Asian', 'def.png', 0, 5, 1, 7, 'sushi-asian', NOW(), NOW()),
(@next_id + 5, 'Middle Eastern', 'def.png', 0, 6, 1, 8, 'middle-eastern', NOW(), NOW()),
(@next_id + 6, 'Salads & Healthy', 'def.png', 0, 7, 1, 6, 'salads-healthy', NOW(), NOW()),
(@next_id + 7, 'Breakfast', 'def.png', 0, 8, 1, 5, 'breakfast', NOW(), NOW()),
(@next_id + 8, 'Desserts & Sweets', 'def.png', 0, 9, 1, 5, 'desserts-sweets', NOW(), NOW()),
(@next_id + 9, 'Beverages', 'def.png', 0, 10, 1, 4, 'beverages', NOW(), NOW()),

-- Cuisine Types
(@next_id + 10, 'Mediterranean', 'def.png', 0, 11, 1, 6, 'mediterranean', NOW(), NOW()),
(@next_id + 11, 'Chinese', 'def.png', 0, 12, 1, 6, 'chinese', NOW(), NOW()),
(@next_id + 12, 'Indian', 'def.png', 0, 13, 1, 5, 'indian', NOW(), NOW()),
(@next_id + 13, 'Mexican', 'def.png', 0, 14, 1, 5, 'mexican', NOW(), NOW()),
(@next_id + 14, 'Thai', 'def.png', 0, 15, 1, 4, 'thai', NOW(), NOW()),

-- Meal Types
(@next_id + 15, 'Appetizers', 'def.png', 0, 16, 1, 4, 'appetizers', NOW(), NOW()),
(@next_id + 16, 'Main Course', 'def.png', 0, 17, 1, 5, 'main-course', NOW(), NOW()),
(@next_id + 17, 'Side Dishes', 'def.png', 0, 18, 1, 3, 'side-dishes', NOW(), NOW()),

-- Special Dietary
(@next_id + 18, 'Vegetarian', 'def.png', 0, 19, 1, 6, 'vegetarian', NOW(), NOW()),
(@next_id + 19, 'Vegan', 'def.png', 0, 20, 1, 5, 'vegan', NOW(), NOW()),
(@next_id + 20, 'Gluten-Free', 'def.png', 0, 21, 1, 4, 'gluten-free', NOW(), NOW()),

-- Fast Food
(@next_id + 21, 'Fried Chicken', 'def.png', 0, 22, 1, 7, 'fried-chicken', NOW(), NOW()),
(@next_id + 22, 'Hot Dogs', 'def.png', 0, 23, 1, 3, 'hot-dogs', NOW(), NOW()),
(@next_id + 23, 'French Fries & Sides', 'def.png', 0, 24, 1, 4, 'fries-sides', NOW(), NOW()),

-- Specialty
(@next_id + 24, 'Seafood', 'def.png', 0, 25, 1, 5, 'seafood', NOW(), NOW()),
(@next_id + 25, 'Grilled & BBQ', 'def.png', 0, 26, 1, 6, 'grilled-bbq', NOW(), NOW()),
(@next_id + 26, 'Soups', 'def.png', 0, 27, 1, 3, 'soups', NOW(), NOW()),
(@next_id + 27, 'Bakery & Bread', 'def.png', 0, 28, 1, 3, 'bakery-bread', NOW(), NOW()),

-- Beverages subcategories
(@next_id + 28, 'Coffee & Tea', 'def.png', 0, 29, 1, 4, 'coffee-tea', NOW(), NOW()),
(@next_id + 29, 'Smoothies & Juices', 'def.png', 0, 30, 1, 4, 'smoothies-juices', NOW(), NOW()),
(@next_id + 30, 'Soft Drinks', 'def.png', 0, 31, 1, 3, 'soft-drinks', NOW(), NOW());