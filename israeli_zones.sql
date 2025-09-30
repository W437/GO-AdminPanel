-- Israeli Service Zones
-- Based on the locations from the screenshot

-- First, get the next available ID
SET @next_id = (SELECT COALESCE(MAX(id), 0) + 1 FROM zones);

-- 1. Judaydah Almaker - Yarka - Yassif (Northern villages)
INSERT INTO zones (id, name, display_name, coordinates, status, created_at, updated_at) VALUES
(@next_id, 'judaydah-almaker-yarka-yassif', 'Judaydah Almaker - Yarka - Yassif',
ST_GeomFromText('POLYGON((35.15 32.95, 35.25 32.95, 35.25 33.05, 35.15 33.05, 35.15 32.95))'),
1, NOW(), NOW());

-- 2. Acre - Nahariya (Coastal cities in the north)
INSERT INTO zones (id, name, display_name, coordinates, status, created_at, updated_at) VALUES
(@next_id + 1, 'acre-nahariya', 'Acre - Nahariya',
ST_GeomFromText('POLYGON((35.05 32.90, 35.15 32.90, 35.15 33.05, 35.05 33.05, 35.05 32.90))'),
1, NOW(), NOW());

-- 3. Tamra - Kabul (Northern Arab towns)
INSERT INTO zones (id, name, display_name, coordinates, status, created_at, updated_at) VALUES
(@next_id + 2, 'tamra-kabul', 'Tamra - Kabul',
ST_GeomFromText('POLYGON((35.15 32.80, 35.25 32.80, 35.25 32.90, 35.15 32.90, 35.15 32.80))'),
1, NOW(), NOW());

-- 4. Karmiel - Shaghur (Galilee region)
INSERT INTO zones (id, name, display_name, coordinates, status, created_at, updated_at) VALUES
(@next_id + 3, 'karmiel-shaghur', 'Karmiel - Shaghur',
ST_GeomFromText('POLYGON((35.25 32.90, 35.35 32.90, 35.35 33.00, 35.25 33.00, 35.25 32.90))'),
1, NOW(), NOW());

-- 5. Shefa-Amr - I'billin (Central Galilee)
INSERT INTO zones (id, name, display_name, coordinates, status, created_at, updated_at) VALUES
(@next_id + 4, 'shefa-amr-ibillin', 'Shefa-Amr - I\'billin',
ST_GeomFromText('POLYGON((35.15 32.75, 35.25 32.75, 35.25 32.85, 35.15 32.85, 35.15 32.75))'),
1, NOW(), NOW());

-- 6. Sakhnin - Arraba - Deir Hanna (Lower Galilee villages)
INSERT INTO zones (id, name, display_name, coordinates, status, created_at, updated_at) VALUES
(@next_id + 5, 'sakhnin-arraba-deir-hanna', 'Sakhnin - Arraba - Deir Hanna',
ST_GeomFromText('POLYGON((35.25 32.80, 35.35 32.80, 35.35 32.90, 35.25 32.90, 35.25 32.80))'),
1, NOW(), NOW());

-- 7. Haifa (Major coastal city)
INSERT INTO zones (id, name, display_name, coordinates, status, created_at, updated_at) VALUES
(@next_id + 6, 'haifa', 'Haifa',
ST_GeomFromText('POLYGON((34.95 32.75, 35.10 32.75, 35.10 32.85, 34.95 32.85, 34.95 32.75))'),
1, NOW(), NOW());

-- 8. Nazareth area (Historic city)
INSERT INTO zones (id, name, display_name, coordinates, status, created_at, updated_at) VALUES
(@next_id + 7, 'nazareth-area', 'Nazareth area',
ST_GeomFromText('POLYGON((35.25 32.68, 35.35 32.68, 35.35 32.78, 35.25 32.78, 35.25 32.68))'),
1, NOW(), NOW());

-- 9. Afula (Jezreel Valley)
INSERT INTO zones (id, name, display_name, coordinates, status, created_at, updated_at) VALUES
(@next_id + 8, 'afula', 'Afula',
ST_GeomFromText('POLYGON((35.25 32.58, 35.35 32.58, 35.35 32.68, 35.25 32.68, 35.25 32.58))'),
1, NOW(), NOW());

-- 10. Tel Aviv-Yafo (Major metropolitan area)
INSERT INTO zones (id, name, display_name, coordinates, status, created_at, updated_at) VALUES
(@next_id + 9, 'tel-aviv-yafo', 'Tel Aviv-Yafo',
ST_GeomFromText('POLYGON((34.75 32.05, 34.85 32.05, 34.85 32.15, 34.75 32.15, 34.75 32.05))'),
1, NOW(), NOW());