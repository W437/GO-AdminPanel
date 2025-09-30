-- Add 5 restaurants per zone for GO Delivery App
-- Password for all vendors: password123 (bcrypt hash)

-- Zone 2: Judeida-Makr, Yarka, Yasif
INSERT INTO vendors (id, f_name, l_name, phone, email, password, status, created_at, updated_at) VALUES
(2, 'Hummus', 'Said', '0501234001', 'hummus.said@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(3, 'Shawarma', 'Papa', '0501234002', 'shawarma.papa@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(4, 'Falafel', 'Awad', '0501234003', 'falafel.awad@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(5, 'Pizza', 'Roy', '0501234004', 'pizza.roy@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(6, 'Galilee', 'Grills', '0501234005', 'galilee.grills@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),

-- Zone 3: Acre, Nahariya
(7, 'Uri', 'Buri', '0501234006', 'uri.buri@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(8, 'El', 'Marsa', '0501234007', 'el.marsa@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(9, 'Sama', 'Restaurant', '0501234008', 'sama.restaurant@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(10, 'Donatella', 'Italian', '0501234009', 'donatella@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(11, 'Alexander', 'Diner', '0501234010', 'alexander.diner@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),

-- Zone 4: Tamra, Kabul
(12, 'Cafe', 'Tamra', '0501234011', 'cafe.tamra@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(13, 'Tamra', 'Grill', '0501234012', 'tamra.grill@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(14, 'Kabul', 'Kitchen', '0501234013', 'kabul.kitchen@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(15, 'Tamra', 'Pizza', '0501234014', 'tamra.pizza@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(16, 'Tamra', 'Shawarma', '0501234015', 'tamra.shawarma@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),

-- Zone 5: Karmiel, Shaghur
(17, 'Karmiel', 'Cafe', '0501234016', 'karmiel.cafe@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(18, 'Lebanese', 'Kitchen', '0501234017', 'lebanese.karmiel@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(19, 'Karmiel', 'Grill', '0501234018', 'karmiel.grill@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(20, 'Shaghur', 'Shawarma', '0501234019', 'shaghur.shawarma@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(21, 'Karmiel', 'Pizza', '0501234020', 'karmiel.pizza@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),

-- Zone 6: Shefa-Amr, Ibillin
(22, 'Shefa', 'Grill', '0501234021', 'shefa.grill@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(23, 'Ibillin', 'Kitchen', '0501234022', 'ibillin.kitchen@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(24, 'Shefa', 'Pizza', '0501234023', 'shefa.pizza@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(25, 'Shefa', 'Cafe', '0501234024', 'shefa.cafe@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(26, 'Ibillin', 'Shawarma', '0501234025', 'ibillin.shawarma@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),

-- Zone 7: Sakhnin, Arraba, Deir Hanna
(27, 'Sakhnin', 'Grill', '0501234026', 'sakhnin.grill@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(28, 'Arraba', 'Kitchen', '0501234027', 'arraba.kitchen@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(29, 'Deir Hanna', 'Restaurant', '0501234028', 'deirhanna@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(30, 'Sakhnin', 'Pizza', '0501234029', 'sakhnin.pizza@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(31, 'Arraba', 'Shawarma', '0501234030', 'arraba.shawarma@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),

-- Zone 8: Haifa
(32, 'Hanamal', '24', '0501234031', 'hanamal24@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(33, 'Douzan', 'Restaurant', '0501234032', 'douzan@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(34, 'Ein El', 'Wadi', '0501234033', 'einelwadi@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(35, 'Raffaello', 'Haifa', '0501234034', 'raffaello@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(36, 'Maayan', 'Habira', '0501234035', 'maayan.habira@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),

-- Zone 9: Nazareth
(37, 'Al', 'Rida', '0501234036', 'alrida@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(38, 'Tishreen', 'Restaurant', '0501234037', 'tishreen@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(39, 'Abu', 'Ghanem', '0501234038', 'abu.ghanem@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(40, 'Olga', 'Restaurant', '0501234039', 'olga@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(41, 'Diana', 'Nazareth', '0501234040', 'diana@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),

-- Zone 10: Afula
(42, 'Afula', 'Grill', '0501234041', 'afula.grill@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(43, 'Afula', 'Hummus', '0501234042', 'afula.hummus@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(44, 'Gilboa', 'Restaurant', '0501234043', 'gilboa@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(45, 'Afula', 'Pizza', '0501234044', 'afula.pizza@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(46, 'Afula', 'Shawarma', '0501234045', 'afula.shawarma@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),

-- Zone 11: Tel Aviv-Yafo
(47, 'Abu', 'Hassan', '0501234046', 'abu.hassan@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(48, 'Mashya', 'TLV', '0501234047', 'mashya@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(49, 'HaAchim', 'Brothers', '0501234048', 'haachim@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(50, 'Batshon', 'Seafood', '0501234049', 'batshon@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
(51, 'Cafe', 'Puaa', '0501234050', 'cafe.puaa@restaurant.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW());

-- Now add the restaurants
INSERT INTO restaurants (id, name, phone, email, vendor_id, zone_id, status, active, latitude, longitude, address, minimum_order, delivery_time, tax, created_at, updated_at, slug) VALUES
-- Zone 2 Restaurants
(2, 'Hummus Said', '0501234001', 'hummus.said@restaurant.com', 2, 2, 1, 1, '32.9408', '35.1699', 'Judeida-Makr', 20.00, '20-30', 17.00, NOW(), NOW(), 'hummus-said'),
(3, 'Shawarma Papa Luski', '0501234002', 'shawarma.papa@restaurant.com', 3, 2, 1, 1, '32.9608', '35.2899', 'Yarka', 25.00, '25-35', 17.00, NOW(), NOW(), 'shawarma-papa-luski'),
(4, 'Falafel Awad Fadi', '0501234003', 'falafel.awad@restaurant.com', 4, 2, 1, 1, '32.9508', '35.1799', 'Judeida-Makr', 15.00, '15-25', 17.00, NOW(), NOW(), 'falafel-awad-fadi'),
(5, 'Pizza Roy Yasif', '0501234004', 'pizza.roy@restaurant.com', 5, 2, 1, 1, '32.9508', '35.1599', 'Kafr Yasif', 30.00, '30-40', 17.00, NOW(), NOW(), 'pizza-roy-yasif'),
(6, 'Galilee Grills', '0501234005', 'galilee.grills@restaurant.com', 6, 2, 1, 1, '32.9458', '35.1649', 'Judeida-Makr', 40.00, '25-35', 17.00, NOW(), NOW(), 'galilee-grills'),

-- Zone 3 Restaurants
(7, 'Uri Buri', '0501234006', 'uri.buri@restaurant.com', 7, 3, 1, 1, '32.9253', '35.0713', 'Acre Old City', 80.00, '35-45', 17.00, NOW(), NOW(), 'uri-buri'),
(8, 'El Marsa Restaurant', '0501234007', 'el.marsa@restaurant.com', 8, 3, 1, 1, '32.9273', '35.0733', 'Acre Port', 70.00, '30-40', 17.00, NOW(), NOW(), 'el-marsa-restaurant'),
(9, 'Sama Restaurant', '0501234008', 'sama.restaurant@restaurant.com', 9, 3, 1, 1, '32.9243', '35.0723', 'Old Acre', 60.00, '25-35', 17.00, NOW(), NOW(), 'sama-restaurant'),
(10, 'Donatella', '0501234009', 'donatella@restaurant.com', 10, 3, 1, 1, '33.0045', '35.0969', 'Nahariya', 50.00, '30-40', 17.00, NOW(), NOW(), 'donatella'),
(11, 'Alexander Local Diner', '0501234010', 'alexander.diner@restaurant.com', 11, 3, 1, 1, '33.0065', '35.0979', 'Nahariya', 45.00, '25-35', 17.00, NOW(), NOW(), 'alexander-local-diner'),

-- Zone 4 Restaurants
(12, 'Cafe Cafe Tamra', '0501234011', 'cafe.tamra@restaurant.com', 12, 4, 1, 1, '32.8526', '35.1994', 'Tamra', 25.00, '20-30', 17.00, NOW(), NOW(), 'cafe-cafe-tamra'),
(13, 'Tamra Grill House', '0501234012', 'tamra.grill@restaurant.com', 13, 4, 1, 1, '32.8546', '35.2014', 'Tamra', 35.00, '25-35', 17.00, NOW(), NOW(), 'tamra-grill-house'),
(14, 'Kabul Kitchen', '0501234013', 'kabul.kitchen@restaurant.com', 14, 4, 1, 1, '32.8466', '35.1914', 'Kabul', 30.00, '30-40', 17.00, NOW(), NOW(), 'kabul-kitchen'),
(15, 'Tamra Pizza', '0501234014', 'tamra.pizza@restaurant.com', 15, 4, 1, 1, '32.8536', '35.2004', 'Tamra', 28.00, '25-35', 17.00, NOW(), NOW(), 'tamra-pizza'),
(16, 'Tamra Shawarma', '0501234015', 'tamra.shawarma@restaurant.com', 16, 4, 1, 1, '32.8556', '35.2024', 'Tamra', 20.00, '15-25', 17.00, NOW(), NOW(), 'tamra-shawarma'),

-- Zone 5 Restaurants
(17, 'Karmiel Cafe', '0501234016', 'karmiel.cafe@restaurant.com', 17, 5, 1, 1, '32.9186', '35.2968', 'Karmiel', 30.00, '20-30', 17.00, NOW(), NOW(), 'karmiel-cafe'),
(18, 'Lebanese Kitchen Karmiel', '0501234017', 'lebanese.karmiel@restaurant.com', 18, 5, 1, 1, '32.9196', '35.2978', 'Karmiel', 45.00, '30-40', 17.00, NOW(), NOW(), 'lebanese-kitchen-karmiel'),
(19, 'Karmiel Grill', '0501234018', 'karmiel.grill@restaurant.com', 19, 5, 1, 1, '32.9176', '35.2958', 'Karmiel', 40.00, '25-35', 17.00, NOW(), NOW(), 'karmiel-grill'),
(20, 'Shaghur Shawarma', '0501234019', 'shaghur.shawarma@restaurant.com', 20, 5, 1, 1, '32.8076', '35.1758', 'Shfaram', 22.00, '20-30', 17.00, NOW(), NOW(), 'shaghur-shawarma'),
(21, 'Karmiel Pizza House', '0501234020', 'karmiel.pizza@restaurant.com', 21, 5, 1, 1, '32.9206', '35.2988', 'Karmiel', 32.00, '25-35', 17.00, NOW(), NOW(), 'karmiel-pizza-house'),

-- Zone 6 Restaurants
(22, 'Shefa Grill', '0501234021', 'shefa.grill@restaurant.com', 22, 6, 1, 1, '32.8086', '35.1700', 'Shefa-Amr', 35.00, '25-35', 17.00, NOW(), NOW(), 'shefa-grill'),
(23, 'Ibillin Kitchen', '0501234022', 'ibillin.kitchen@restaurant.com', 23, 6, 1, 1, '32.8186', '35.2900', 'Ibillin', 30.00, '30-40', 17.00, NOW(), NOW(), 'ibillin-kitchen'),
(24, 'Shefa Pizza', '0501234023', 'shefa.pizza@restaurant.com', 24, 6, 1, 1, '32.8096', '35.1710', 'Shefa-Amr', 28.00, '25-35', 17.00, NOW(), NOW(), 'shefa-pizza'),
(25, 'Shefa Cafe', '0501234024', 'shefa.cafe@restaurant.com', 25, 6, 1, 1, '32.8106', '35.1720', 'Shefa-Amr', 25.00, '20-30', 17.00, NOW(), NOW(), 'shefa-cafe'),
(26, 'Ibillin Shawarma', '0501234025', 'ibillin.shawarma@restaurant.com', 26, 6, 1, 1, '32.8176', '35.2890', 'Ibillin', 20.00, '15-25', 17.00, NOW(), NOW(), 'ibillin-shawarma'),

-- Zone 7 Restaurants
(27, 'Sakhnin Grill', '0501234026', 'sakhnin.grill@restaurant.com', 27, 7, 1, 1, '32.8652', '35.2975', 'Sakhnin', 35.00, '25-35', 17.00, NOW(), NOW(), 'sakhnin-grill'),
(28, 'Arraba Kitchen', '0501234027', 'arraba.kitchen@restaurant.com', 28, 7, 1, 1, '32.8552', '35.3275', 'Arraba', 32.00, '30-40', 17.00, NOW(), NOW(), 'arraba-kitchen'),
(29, 'Deir Hanna Restaurant', '0501234028', 'deirhanna@restaurant.com', 29, 7, 1, 1, '32.8652', '35.3675', 'Deir Hanna', 30.00, '25-35', 17.00, NOW(), NOW(), 'deir-hanna-restaurant'),
(30, 'Sakhnin Pizza', '0501234029', 'sakhnin.pizza@restaurant.com', 30, 7, 1, 1, '32.8662', '35.2985', 'Sakhnin', 28.00, '20-30', 17.00, NOW(), NOW(), 'sakhnin-pizza'),
(31, 'Arraba Shawarma', '0501234030', 'arraba.shawarma@restaurant.com', 31, 7, 1, 1, '32.8562', '35.3285', 'Arraba', 22.00, '15-25', 17.00, NOW(), NOW(), 'arraba-shawarma'),

-- Zone 8 Restaurants (Haifa)
(32, 'Hanamal 24', '0501234031', 'hanamal24@restaurant.com', 32, 8, 1, 1, '32.8191', '34.9885', 'Haifa Port', 100.00, '40-50', 17.00, NOW(), NOW(), 'hanamal-24'),
(33, 'Douzan', '0501234032', 'douzan@restaurant.com', 33, 8, 1, 1, '32.8155', '34.9889', 'German Colony, Haifa', 80.00, '35-45', 17.00, NOW(), NOW(), 'douzan'),
(34, 'Ein El Wadi', '0501234033', 'einelwadi@restaurant.com', 34, 8, 1, 1, '32.8191', '34.9991', 'Wadi Nisnas, Haifa', 50.00, '25-35', 17.00, NOW(), NOW(), 'ein-el-wadi'),
(35, 'Raffaello Haifa', '0501234034', 'raffaello@restaurant.com', 35, 8, 1, 1, '32.8145', '34.9879', 'Kiryon, Haifa', 45.00, '30-40', 17.00, NOW(), NOW(), 'raffaello-haifa'),
(36, 'Maayan Habira', '0501234035', 'maayan.habira@restaurant.com', 36, 8, 1, 1, '32.8101', '34.9885', 'Haifa', 40.00, '25-35', 17.00, NOW(), NOW(), 'maayan-habira'),

-- Zone 9 Restaurants (Nazareth)
(37, 'Al Rida', '0501234036', 'alrida@restaurant.com', 37, 9, 1, 1, '32.7022', '35.2973', 'Old City, Nazareth', 60.00, '30-40', 17.00, NOW(), NOW(), 'al-rida'),
(38, 'Tishreen', '0501234037', 'tishreen@restaurant.com', 38, 9, 1, 1, '32.7032', '35.2983', 'Nazareth', 70.00, '35-45', 17.00, NOW(), NOW(), 'tishreen'),
(39, 'Hummus Abu Ghanem', '0501234038', 'abu.ghanem@restaurant.com', 39, 9, 1, 1, '32.7012', '35.2963', 'Nazareth', 25.00, '15-25', 17.00, NOW(), NOW(), 'hummus-abu-ghanem'),
(40, 'Olga Restaurant', '0501234039', 'olga@restaurant.com', 40, 9, 1, 1, '32.7042', '35.2993', 'Nazareth', 55.00, '30-40', 17.00, NOW(), NOW(), 'olga-restaurant'),
(41, 'Diana Nazareth', '0501234040', 'diana@restaurant.com', 41, 9, 1, 1, '32.7052', '35.3003', 'Nazareth', 50.00, '25-35', 17.00, NOW(), NOW(), 'diana-nazareth'),

-- Zone 10 Restaurants (Afula)
(42, 'Afula Grill House', '0501234041', 'afula.grill@restaurant.com', 42, 10, 1, 1, '32.6074', '35.2897', 'Afula', 40.00, '25-35', 17.00, NOW(), NOW(), 'afula-grill-house'),
(43, 'Afula Hummus Bar', '0501234042', 'afula.hummus@restaurant.com', 43, 10, 1, 1, '32.6084', '35.2907', 'Afula', 20.00, '15-25', 17.00, NOW(), NOW(), 'afula-hummus-bar'),
(44, 'Gilboa Restaurant', '0501234043', 'gilboa@restaurant.com', 44, 10, 1, 1, '32.6064', '35.2887', 'Afula', 45.00, '30-40', 17.00, NOW(), NOW(), 'gilboa-restaurant'),
(45, 'Afula Pizza', '0501234044', 'afula.pizza@restaurant.com', 45, 10, 1, 1, '32.6094', '35.2917', 'Afula', 30.00, '25-35', 17.00, NOW(), NOW(), 'afula-pizza'),
(46, 'Afula Shawarma', '0501234045', 'afula.shawarma@restaurant.com', 46, 10, 1, 1, '32.6104', '35.2927', 'Afula', 22.00, '15-25', 17.00, NOW(), NOW(), 'afula-shawarma'),

-- Zone 11 Restaurants (Tel Aviv-Yafo)
(47, 'Abu Hassan', '0501234046', 'abu.hassan@restaurant.com', 47, 11, 1, 1, '32.0543', '34.7539', 'Jaffa', 30.00, '20-30', 17.00, NOW(), NOW(), 'abu-hassan'),
(48, 'Mashya', '0501234047', 'mashya@restaurant.com', 48, 11, 1, 1, '32.0853', '34.7818', 'Tel Aviv', 90.00, '40-50', 17.00, NOW(), NOW(), 'mashya'),
(49, 'HaAchim (The Brothers)', '0501234048', 'haachim@restaurant.com', 49, 11, 1, 1, '32.0743', '34.7739', 'Tel Aviv', 70.00, '35-45', 17.00, NOW(), NOW(), 'haachim-brothers'),
(50, 'Batshon Seafood', '0501234049', 'batshon@restaurant.com', 50, 11, 1, 1, '32.0643', '34.7639', 'Jaffa Port', 80.00, '30-40', 17.00, NOW(), NOW(), 'batshon-seafood'),
(51, 'Cafe Puaa', '0501234050', 'cafe.puaa@restaurant.com', 51, 11, 1, 1, '32.0543', '34.7539', 'Jaffa Flea Market', 35.00, '25-35', 17.00, NOW(), NOW(), 'cafe-puaa');