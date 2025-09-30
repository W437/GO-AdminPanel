-- Menu items for all restaurants

-- Shawarma Papa Luski (Restaurant 3)
INSERT INTO food (id, name, description, image, category_id, price, tax, restaurant_id, status, veg, created_at, updated_at, slug) VALUES
(16, 'Chicken Shawarma Wrap', 'Marinated chicken shawarma in laffa bread', 'def.png', 6, 30.00, 17.00, 3, 1, 0, NOW(), NOW(), 'chicken-shawarma-wrap-3'),
(17, 'Beef Shawarma Wrap', 'Tender beef shawarma with tahini', 'def.png', 6, 35.00, 17.00, 3, 1, 0, NOW(), NOW(), 'beef-shawarma-wrap-3'),
(18, 'Shawarma Platter', 'Mixed shawarma with rice and salad', 'def.png', 6, 45.00, 17.00, 3, 1, 0, NOW(), NOW(), 'shawarma-platter-3'),
(19, 'Chicken Shawarma Salad', 'Shawarma over fresh garden salad', 'def.png', 9, 38.00, 17.00, 3, 1, 0, NOW(), NOW(), 'shawarma-salad-3'),
(20, 'French Fries', 'Crispy golden fries', 'def.png', 26, 15.00, 17.00, 3, 1, 1, NOW(), NOW(), 'french-fries-3'),
(21, 'Hummus Side', 'Side of hummus', 'def.png', 6, 12.00, 17.00, 3, 1, 1, NOW(), NOW(), 'hummus-side-3'),
(22, 'Tahini Sauce', 'Fresh tahini sauce', 'def.png', 6, 8.00, 17.00, 3, 1, 1, NOW(), NOW(), 'tahini-sauce-3');

-- Pizza Roy Yasif (Restaurant 5)
INSERT INTO food (id, name, description, image, category_id, price, tax, restaurant_id, status, veg, created_at, updated_at, slug) VALUES
(23, 'Margherita Pizza', 'Classic tomato, mozzarella, and basil', 'def.png', 3, 45.00, 17.00, 5, 1, 1, NOW(), NOW(), 'margherita-pizza-5'),
(24, 'Mushroom & Olive Pizza', 'Mushrooms, black olives, and cheese', 'def.png', 3, 52.00, 17.00, 5, 1, 1, NOW(), NOW(), 'mushroom-olive-pizza-5'),
(25, 'Bulgarian Cheese Pizza', 'Bulgarian cheese and corn', 'def.png', 3, 48.00, 17.00, 5, 1, 1, NOW(), NOW(), 'bulgarian-cheese-pizza-5'),
(26, 'Vegetable Supreme', 'Mixed vegetables and cheese', 'def.png', 3, 55.00, 17.00, 5, 1, 1, NOW(), NOW(), 'vegetable-supreme-5'),
(27, 'Meat Lovers Pizza', 'Beef, salami, and pepperoni', 'def.png', 3, 62.00, 17.00, 5, 1, 0, NOW(), NOW(), 'meat-lovers-5'),
(28, 'Garlic Bread', 'Fresh garlic bread with herbs', 'def.png', 30, 18.00, 17.00, 5, 1, 1, NOW(), NOW(), 'garlic-bread-5'),
(29, 'Caesar Salad', 'Romaine lettuce with Caesar dressing', 'def.png', 9, 28.00, 17.00, 5, 1, 1, NOW(), NOW(), 'caesar-salad-5');

-- Galilee Grills (Restaurant 6)
INSERT INTO food (id, name, description, image, category_id, price, tax, restaurant_id, status, veg, created_at, updated_at, slug) VALUES
(30, 'Mixed Grill Platter', 'Kebab, kofta, and chicken skewers', 'def.png', 28, 65.00, 17.00, 6, 1, 0, NOW(), NOW(), 'mixed-grill-platter-6'),
(31, 'Lamb Kebab Skewers', 'Grilled lamb kebab skewers', 'def.png', 28, 55.00, 17.00, 6, 1, 0, NOW(), NOW(), 'lamb-kebab-6'),
(32, 'Beef Kofta', 'Spiced ground beef kofta', 'def.png', 28, 48.00, 17.00, 6, 1, 0, NOW(), NOW(), 'beef-kofta-6'),
(33, 'Chicken Shish Tawook', 'Marinated chicken skewers', 'def.png', 28, 45.00, 17.00, 6, 1, 0, NOW(), NOW(), 'chicken-shish-tawook-6'),
(34, 'Grilled Lamb Chops', 'Tender grilled lamb chops', 'def.png', 28, 75.00, 17.00, 6, 1, 0, NOW(), NOW(), 'lamb-chops-6'),
(35, 'Hummus with Meat', 'Hummus topped with grilled meat', 'def.png', 6, 42.00, 17.00, 6, 1, 0, NOW(), NOW(), 'hummus-meat-6'),
(36, 'Rice Pilaf', 'Fragrant rice pilaf', 'def.png', 20, 18.00, 17.00, 6, 1, 1, NOW(), NOW(), 'rice-pilaf-6'),
(37, 'Grilled Vegetables', 'Mixed grilled vegetables', 'def.png', 9, 22.00, 17.00, 6, 1, 1, NOW(), NOW(), 'grilled-vegetables-6'),
(38, 'Fattoush Salad', 'Lebanese salad with fried pita', 'def.png', 9, 24.00, 17.00, 6, 1, 1, NOW(), NOW(), 'fattoush-salad-6'),
(39, 'Tabbouleh Salad', 'Parsley and bulgur salad', 'def.png', 9, 24.00, 17.00, 6, 1, 1, NOW(), NOW(), 'tabbouleh-salad-6');

-- Uri Buri (Restaurant 7) - Seafood
INSERT INTO food (id, name, description, image, category_id, price, tax, restaurant_id, status, veg, created_at, updated_at, slug) VALUES
(40, 'Salmon Sashimi', 'Fresh salmon sashimi with wasabi sorbet', 'def.png', 27, 95.00, 17.00, 7, 1, 0, NOW(), NOW(), 'salmon-sashimi-7'),
(41, 'Sea Bass in Coconut Milk', 'Sea bass in spiced coconut milk', 'def.png', 27, 110.00, 17.00, 7, 1, 0, NOW(), NOW(), 'sea-bass-coconut-7'),
(42, 'Grilled Octopus', 'Tender grilled octopus', 'def.png', 27, 98.00, 17.00, 7, 1, 0, NOW(), NOW(), 'grilled-octopus-7'),
(43, 'St. Peter Fish', 'Fresh St. Peter fish grilled', 'def.png', 27, 88.00, 17.00, 7, 1, 0, NOW(), NOW(), 'st-peter-fish-7'),
(44, 'Ceviche', 'Fresh fish ceviche', 'def.png', 27, 75.00, 17.00, 7, 1, 0, NOW(), NOW(), 'ceviche-7'),
(45, 'Barramundi Lemon Butter', 'Barramundi in lemon butter sauce', 'def.png', 27, 105.00, 17.00, 7, 1, 0, NOW(), NOW(), 'barramundi-7'),
(46, 'Seafood Soup', 'Rich seafood soup', 'def.png', 29, 68.00, 17.00, 7, 1, 0, NOW(), NOW(), 'seafood-soup-7'),
(47, 'Grilled Shrimp', 'Garlic butter grilled shrimp', 'def.png', 27, 92.00, 17.00, 7, 1, 0, NOW(), NOW(), 'grilled-shrimp-7');

-- El Marsa Restaurant (Restaurant 8) - Seafood & Mediterranean
INSERT INTO food (id, name, description, image, category_id, price, tax, restaurant_id, status, veg, created_at, updated_at, slug) VALUES
(48, 'Grilled Sea Bream', 'Fresh sea bream grilled to perfection', 'def.png', 27, 85.00, 17.00, 8, 1, 0, NOW(), NOW(), 'grilled-sea-bream-8'),
(49, 'Mixed Seafood Grill', 'Assorted grilled seafood platter', 'def.png', 27, 120.00, 17.00, 8, 1, 0, NOW(), NOW(), 'mixed-seafood-grill-8'),
(50, 'Calamari Fritti', 'Crispy fried calamari', 'def.png', 27, 65.00, 17.00, 8, 1, 0, NOW(), NOW(), 'calamari-fritti-8'),
(51, 'Fish Kebab', 'Grilled fish kebab skewers', 'def.png', 27, 75.00, 17.00, 8, 1, 0, NOW(), NOW(), 'fish-kebab-8'),
(52, 'Mediterranean Salad', 'Fresh Mediterranean salad', 'def.png', 9, 32.00, 17.00, 8, 1, 1, NOW(), NOW(), 'mediterranean-salad-8'),
(53, 'Seafood Pasta', 'Pasta with mixed seafood', 'def.png', 4, 82.00, 17.00, 8, 1, 0, NOW(), NOW(), 'seafood-pasta-8'),
(54, 'Grilled Lobster', 'Fresh grilled lobster', 'def.png', 27, 145.00, 17.00, 8, 1, 0, NOW(), NOW(), 'grilled-lobster-8');

-- Sama Restaurant (Restaurant 9) - Mediterranean
INSERT INTO food (id, name, description, image, category_id, price, tax, restaurant_id, status, veg, created_at, updated_at, slug) VALUES
(55, 'Grilled Lamb Rack', 'Herb-crusted lamb rack', 'def.png', 28, 95.00, 17.00, 9, 1, 0, NOW(), NOW(), 'lamb-rack-9'),
(56, 'Beef Tenderloin', 'Grilled beef tenderloin', 'def.png', 28, 110.00, 17.00, 9, 1, 0, NOW(), NOW(), 'beef-tenderloin-9'),
(57, 'Chicken Breast Stuffed', 'Stuffed chicken breast', 'def.png', 28, 72.00, 17.00, 9, 1, 0, NOW(), NOW(), 'stuffed-chicken-9'),
(58, 'Vegetarian Moussaka', 'Layers of vegetables and cheese', 'def.png', 13, 58.00, 17.00, 9, 1, 1, NOW(), NOW(), 'moussaka-9'),
(59, 'Greek Salad', 'Traditional Greek salad', 'def.png', 9, 35.00, 17.00, 9, 1, 1, NOW(), NOW(), 'greek-salad-9'),
(60, 'Hummus Plate', 'Creamy hummus with olive oil', 'def.png', 6, 28.00, 17.00, 9, 1, 1, NOW(), NOW(), 'hummus-plate-9'),
(61, 'Baklava', 'Sweet pistachio baklava', 'def.png', 11, 25.00, 17.00, 9, 1, 1, NOW(), NOW(), 'baklava-9');

-- Donatella (Restaurant 10) - Italian
INSERT INTO food (id, name, description, image, category_id, price, tax, restaurant_id, status, veg, created_at, updated_at, slug) VALUES
(62, 'Spaghetti Carbonara', 'Classic carbonara with pancetta', 'def.png', 4, 58.00, 17.00, 10, 1, 0, NOW(), NOW(), 'carbonara-10'),
(63, 'Margherita Pizza', 'Fresh mozzarella and basil pizza', 'def.png', 3, 48.00, 17.00, 10, 1, 1, NOW(), NOW(), 'margherita-10'),
(64, 'Lasagna Bolognese', 'Traditional meat lasagna', 'def.png', 4, 62.00, 17.00, 10, 1, 0, NOW(), NOW(), 'lasagna-10'),
(65, 'Penne Arrabbiata', 'Spicy tomato pasta', 'def.png', 4, 52.00, 17.00, 10, 1, 1, NOW(), NOW(), 'penne-arrabbiata-10'),
(66, 'Risotto Funghi', 'Mushroom risotto', 'def.png', 4, 65.00, 17.00, 10, 1, 1, NOW(), NOW(), 'risotto-funghi-10'),
(67, 'Caprese Salad', 'Tomato, mozzarella, and basil', 'def.png', 9, 42.00, 17.00, 10, 1, 1, NOW(), NOW(), 'caprese-salad-10'),
(68, 'Tiramisu', 'Classic Italian tiramisu', 'def.png', 11, 32.00, 17.00, 10, 1, 1, NOW(), NOW(), 'tiramisu-10');

-- Alexander Local Diner (Restaurant 11) - American
INSERT INTO food (id, name, description, image, category_id, price, tax, restaurant_id, status, veg, created_at, updated_at, slug) VALUES
(69, 'Classic Cheeseburger', 'Beef burger with cheese', 'def.png', 4, 52.00, 17.00, 11, 1, 0, NOW(), NOW(), 'cheeseburger-11'),
(70, 'BBQ Bacon Burger', 'Burger with BBQ sauce and bacon', 'def.png', 4, 58.00, 17.00, 11, 1, 0, NOW(), NOW(), 'bbq-burger-11'),
(71, 'Chicken Schnitzel', 'Crispy chicken schnitzel', 'def.png', 24, 48.00, 17.00, 11, 1, 0, NOW(), NOW(), 'schnitzel-11'),
(72, 'Club Sandwich', 'Triple-decker club sandwich', 'def.png', 5, 45.00, 17.00, 11, 1, 0, NOW(), NOW(), 'club-sandwich-11'),
(73, 'French Fries', 'Crispy golden fries', 'def.png', 26, 18.00, 17.00, 11, 1, 1, NOW(), NOW(), 'fries-11'),
(74, 'Onion Rings', 'Crispy onion rings', 'def.png', 26, 22.00, 17.00, 11, 1, 1, NOW(), NOW(), 'onion-rings-11'),
(75, 'Chocolate Milkshake', 'Rich chocolate milkshake', 'def.png', 12, 25.00, 17.00, 11, 1, 1, NOW(), NOW(), 'chocolate-shake-11');

-- Cafe Cafe Tamra (Restaurant 12) - Cafe
INSERT INTO food (id, name, description, image, category_id, price, tax, restaurant_id, status, veg, created_at, updated_at, slug) VALUES
(76, 'Shakshuka', 'Eggs poached in tomato sauce', 'def.png', 10, 32.00, 17.00, 12, 1, 1, NOW(), NOW(), 'shakshuka-12'),
(77, 'Israeli Breakfast', 'Eggs, salads, cheese, and bread', 'def.png', 10, 45.00, 17.00, 12, 1, 1, NOW(), NOW(), 'israeli-breakfast-12'),
(78, 'Avocado Toast', 'Smashed avocado on sourdough', 'def.png', 10, 38.00, 17.00, 12, 1, 1, NOW(), NOW(), 'avocado-toast-12'),
(79, 'Tuna Sandwich', 'Tuna with egg and vegetables', 'def.png', 5, 32.00, 17.00, 12, 1, 0, NOW(), NOW(), 'tuna-sandwich-12'),
(80, 'Greek Salad', 'Fresh Greek salad', 'def.png', 9, 35.00, 17.00, 12, 1, 1, NOW(), NOW(), 'greek-salad-12'),
(81, 'Cheese Boureka', 'Flaky cheese pastry', 'def.png', 30, 22.00, 17.00, 12, 1, 1, NOW(), NOW(), 'boureka-12'),
(82, 'Fresh Orange Juice', 'Freshly squeezed OJ', 'def.png', 12, 18.00, 17.00, 12, 1, 1, NOW(), NOW(), 'orange-juice-12');

-- Tamra Grill House (Restaurant 13)
INSERT INTO food (id, name, description, image, category_id, price, tax, restaurant_id, status, veg, created_at, updated_at, slug) VALUES
(83, 'Mixed Grill Platter', 'Kebab, kofta, chicken', 'def.png', 28, 60.00, 17.00, 13, 1, 0, NOW(), NOW(), 'mixed-grill-13'),
(84, 'Lamb Kebab', 'Grilled lamb kebab', 'def.png', 28, 52.00, 17.00, 13, 1, 0, NOW(), NOW(), 'lamb-kebab-13'),
(85, 'Chicken Skewers', 'Marinated chicken skewers', 'def.png', 28, 45.00, 17.00, 13, 1, 0, NOW(), NOW(), 'chicken-skewers-13'),
(86, 'Beef Kofta', 'Spiced beef kofta', 'def.png', 28, 48.00, 17.00, 13, 1, 0, NOW(), NOW(), 'beef-kofta-13'),
(87, 'Grilled Vegetables', 'Seasonal grilled vegetables', 'def.png', 9, 24.00, 17.00, 13, 1, 1, NOW(), NOW(), 'grilled-veg-13'),
(88, 'Hummus Plate', 'Fresh hummus', 'def.png', 6, 20.00, 17.00, 13, 1, 1, NOW(), NOW(), 'hummus-13'),
(89, 'Fattoush Salad', 'Lebanese bread salad', 'def.png', 9, 26.00, 17.00, 13, 1, 1, NOW(), NOW(), 'fattoush-13'),
(90, 'Rice Pilaf', 'Aromatic rice', 'def.png', 20, 16.00, 17.00, 13, 1, 1, NOW(), NOW(), 'rice-13');

-- Kabul Kitchen (Restaurant 14)
INSERT INTO food (id, name, description, image, category_id, price, tax, restaurant_id, status, veg, created_at, updated_at, slug) VALUES
(91, 'Lamb Kebab Platter', 'Grilled lamb with rice', 'def.png', 28, 55.00, 17.00, 14, 1, 0, NOW(), NOW(), 'lamb-platter-14'),
(92, 'Chicken Tikka', 'Spiced chicken tikka', 'def.png', 28, 45.00, 17.00, 14, 1, 0, NOW(), NOW(), 'chicken-tikka-14'),
(93, 'Beef Kebab', 'Tender beef kebab', 'def.png', 28, 50.00, 17.00, 14, 1, 0, NOW(), NOW(), 'beef-kebab-14'),
(94, 'Vegetable Curry', 'Mixed vegetable curry', 'def.png', 15, 38.00, 17.00, 14, 1, 1, NOW(), NOW(), 'veg-curry-14'),
(95, 'Naan Bread', 'Fresh baked naan', 'def.png', 30, 12.00, 17.00, 14, 1, 1, NOW(), NOW(), 'naan-14'),
(96, 'Rice Biryani', 'Fragrant spiced rice', 'def.png', 20, 28.00, 17.00, 14, 1, 1, NOW(), NOW(), 'biryani-14'),
(97, 'Afghan Salad', 'Fresh vegetable salad', 'def.png', 9, 18.00, 17.00, 14, 1, 1, NOW(), NOW(), 'afghan-salad-14');

-- Tamra Pizza (Restaurant 15)
INSERT INTO food (id, name, description, image, category_id, price, tax, restaurant_id, status, veg, created_at, updated_at, slug) VALUES
(98, 'Margherita Pizza', 'Classic tomato and mozzarella', 'def.png', 3, 42.00, 17.00, 15, 1, 1, NOW(), NOW(), 'margherita-15'),
(99, 'Pepperoni Pizza', 'Pepperoni and cheese', 'def.png', 3, 48.00, 17.00, 15, 1, 0, NOW(), NOW(), 'pepperoni-15'),
(100, 'Vegetable Pizza', 'Mixed vegetables and cheese', 'def.png', 3, 45.00, 17.00, 15, 1, 1, NOW(), NOW(), 'vegetable-15'),
(101, 'Four Cheese Pizza', 'Four types of cheese', 'def.png', 3, 52.00, 17.00, 15, 1, 1, NOW(), NOW(), 'four-cheese-15'),
(102, 'Meat Lovers Pizza', 'Mixed meats', 'def.png', 3, 58.00, 17.00, 15, 1, 0, NOW(), NOW(), 'meat-lovers-15'),
(103, 'Garlic Bread', 'Fresh garlic bread', 'def.png', 30, 16.00, 17.00, 15, 1, 1, NOW(), NOW(), 'garlic-bread-15'),
(104, 'Caesar Salad', 'Crisp Caesar salad', 'def.png', 9, 26.00, 17.00, 15, 1, 1, NOW(), NOW(), 'caesar-salad-15');

-- Tamra Shawarma (Restaurant 16)
INSERT INTO food (id, name, description, image, category_id, price, tax, restaurant_id, status, veg, created_at, updated_at, slug) VALUES
(105, 'Chicken Shawarma Wrap', 'Grilled chicken shawarma', 'def.png', 6, 28.00, 17.00, 16, 1, 0, NOW(), NOW(), 'chicken-shawarma-16'),
(106, 'Beef Shawarma Wrap', 'Tender beef shawarma', 'def.png', 6, 32.00, 17.00, 16, 1, 0, NOW(), NOW(), 'beef-shawarma-16'),
(107, 'Mixed Shawarma Platter', 'Chicken and beef with rice', 'def.png', 6, 42.00, 17.00, 16, 1, 0, NOW(), NOW(), 'mixed-shawarma-16'),
(108, 'Shawarma Salad Bowl', 'Shawarma on salad', 'def.png', 9, 35.00, 17.00, 16, 1, 0, NOW(), NOW(), 'shawarma-salad-16'),
(109, 'French Fries', 'Crispy fries', 'def.png', 26, 14.00, 17.00, 16, 1, 1, NOW(), NOW(), 'fries-16'),
(110, 'Hummus Side', 'Side hummus', 'def.png', 6, 10.00, 17.00, 16, 1, 1, NOW(), NOW(), 'hummus-16'),
(111, 'Pickles Plate', 'Assorted pickles', 'def.png', 18, 8.00, 17.00, 16, 1, 1, NOW(), NOW(), 'pickles-16');

-- I'll continue with the remaining restaurants in subsequent batches...