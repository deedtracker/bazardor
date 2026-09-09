-- =============================================
-- BazarDor — Seed Data
-- Run AFTER schema.sql
-- =============================================

USE bazardo1_bazardor_db;

-- =============================================
-- 1. Insert Admin User (password: admin123)
--    Change this password after first login!
-- =============================================
INSERT INTO users (username, password) VALUES
('admin', '$2y$10$YKBKxRlHqSx0VqGpBGRU7.oGlT1vEBqGMbNMQqX5zfXjYGZ1yrKSi');

-- =============================================
-- 2. Insert Categories
-- =============================================
INSERT INTO categories (name, sort_order) VALUES
('সবজি', 1),
('মশলা', 2),
('প্রোটিন', 3),
('মাংস', 4);

-- =============================================
-- 3. Insert Cities
-- =============================================
INSERT INTO cities (name) VALUES
('ঢাকা'),
('চট্টগ্রাম'),
('চাঁদপুর'),
('রংপুর'),
('সিলেট'),
('খুলনা'),
('রাজশাহী'),
('বরিশাল');

-- =============================================
-- 4. Insert Products
-- =============================================
INSERT INTO products (category_id, slug, name, name_en, unit, icon_path) VALUES
(1, 'onion', 'পেঁয়াজ', 'Onion', 'কেজি', 'assets/onion.png'),
(1, 'potato', 'আলু', 'Potato', 'কেজি', 'assets/potato.png'),
(2, 'green-chili', 'কাঁচা মরিচ', 'Green Chili', 'কেজি', 'assets/chili.png'),
(1, 'tomato', 'টমেটো', 'Tomato', 'কেজি', 'assets/tomato.png'),
(1, 'eggplant', 'বেগুন', 'Eggplant', 'কেজি', 'assets/eggplant.png'),
(2, 'garlic', 'রসুন', 'Garlic', 'কেজি', 'assets/garlic.png'),
(2, 'ginger', 'আদা', 'Ginger', 'কেজি', 'assets/ginger.png'),
(3, 'egg', 'ডিম', 'Egg', 'হালি', 'assets/egg.png'),
(4, 'chicken', 'মুরগি', 'Chicken', 'কেজি', 'assets/chicken.png'),
(4, 'beef', 'গরুর মাংস', 'Beef', 'কেজি', 'assets/beef.png');

-- =============================================
-- 5. Insert Daily Prices (5 days of history per product)
--    We use fixed dates relative to each other.
--    Adjust these dates to your actual dates when deploying.
-- =============================================

-- Helper: We will use dates 2026-08-26 through 2026-08-30

-- --- Onion (product_id = 1) ---
INSERT INTO daily_prices (product_id, price_date, average_price) VALUES
(1, '2026-08-26', 42), (1, '2026-08-27', 45), (1, '2026-08-28', 46), (1, '2026-08-29', 46), (1, '2026-08-30', 50);

-- --- Potato (product_id = 2) ---
INSERT INTO daily_prices (product_id, price_date, average_price) VALUES
(2, '2026-08-26', 33), (2, '2026-08-27', 34), (2, '2026-08-28', 34), (2, '2026-08-29', 35), (2, '2026-08-30', 35);

-- --- Green Chili (product_id = 3) ---
INSERT INTO daily_prices (product_id, price_date, average_price) VALUES
(3, '2026-08-26', 180), (3, '2026-08-27', 250), (3, '2026-08-28', 240), (3, '2026-08-29', 220), (3, '2026-08-30', 200);

-- --- Tomato (product_id = 4) ---
INSERT INTO daily_prices (product_id, price_date, average_price) VALUES
(4, '2026-08-26', 60), (4, '2026-08-27', 62), (4, '2026-08-28', 65), (4, '2026-08-29', 70), (4, '2026-08-30', 80);

-- --- Eggplant (product_id = 5) ---
INSERT INTO daily_prices (product_id, price_date, average_price) VALUES
(5, '2026-08-26', 50), (5, '2026-08-27', 48), (5, '2026-08-28', 46), (5, '2026-08-29', 45), (5, '2026-08-30', 40);

-- --- Garlic (product_id = 6) ---
INSERT INTO daily_prices (product_id, price_date, average_price) VALUES
(6, '2026-08-26', 215), (6, '2026-08-27', 218), (6, '2026-08-28', 220), (6, '2026-08-29', 220), (6, '2026-08-30', 220);

-- --- Ginger (product_id = 7) ---
INSERT INTO daily_prices (product_id, price_date, average_price) VALUES
(7, '2026-08-26', 310), (7, '2026-08-27', 305), (7, '2026-08-28', 300), (7, '2026-08-29', 300), (7, '2026-08-30', 280);

-- --- Egg (product_id = 8) ---
INSERT INTO daily_prices (product_id, price_date, average_price) VALUES
(8, '2026-08-26', 44), (8, '2026-08-27', 44), (8, '2026-08-28', 45), (8, '2026-08-29', 46), (8, '2026-08-30', 48);

-- --- Chicken (product_id = 9) ---
INSERT INTO daily_prices (product_id, price_date, average_price) VALUES
(9, '2026-08-26', 190), (9, '2026-08-27', 195), (9, '2026-08-28', 195), (9, '2026-08-29', 200), (9, '2026-08-30', 210);

-- --- Beef (product_id = 10) ---
INSERT INTO daily_prices (product_id, price_date, average_price) VALUES
(10, '2026-08-26', 750), (10, '2026-08-27', 750), (10, '2026-08-28', 750), (10, '2026-08-29', 750), (10, '2026-08-30', 750);

-- =============================================
-- 6. Insert City Prices for the LATEST day (Aug 30) only
--    For historical days, the average_price is sufficient.
--    City prices for older days can be added later if needed.
-- =============================================

-- Onion (daily_price_id = 5 for Aug 30)
INSERT INTO city_prices (daily_price_id, city_id, price) VALUES
(5, 1, 52), (5, 2, 48), (5, 3, 45), (5, 4, 44), (5, 5, 55), (5, 6, 47), (5, 7, 43), (5, 8, 46);

-- Potato (daily_price_id = 10 for Aug 30)
INSERT INTO city_prices (daily_price_id, city_id, price) VALUES
(10, 1, 36), (10, 2, 35), (10, 3, 33), (10, 4, 30), (10, 5, 37), (10, 6, 34), (10, 7, 32), (10, 8, 34);

-- Green Chili (daily_price_id = 15 for Aug 30)
INSERT INTO city_prices (daily_price_id, city_id, price) VALUES
(15, 1, 210), (15, 2, 195), (15, 3, 180), (15, 4, 175), (15, 5, 220), (15, 6, 190), (15, 7, 185), (15, 8, 195);

-- Tomato (daily_price_id = 20 for Aug 30)
INSERT INTO city_prices (daily_price_id, city_id, price) VALUES
(20, 1, 85), (20, 2, 78), (20, 3, 75), (20, 4, 72), (20, 5, 88), (20, 6, 76), (20, 7, 70), (20, 8, 80);

-- Eggplant (daily_price_id = 25 for Aug 30)
INSERT INTO city_prices (daily_price_id, city_id, price) VALUES
(25, 1, 42), (25, 2, 40), (25, 3, 38), (25, 4, 35), (25, 5, 44), (25, 6, 38), (25, 7, 36), (25, 8, 39);

-- Garlic (daily_price_id = 30 for Aug 30)
INSERT INTO city_prices (daily_price_id, city_id, price) VALUES
(30, 1, 230), (30, 2, 220), (30, 3, 210), (30, 4, 200), (30, 5, 235), (30, 6, 215), (30, 7, 205), (30, 8, 218);

-- Ginger (daily_price_id = 35 for Aug 30)
INSERT INTO city_prices (daily_price_id, city_id, price) VALUES
(35, 1, 290), (35, 2, 275), (35, 3, 270), (35, 4, 260), (35, 5, 295), (35, 6, 275), (35, 7, 265), (35, 8, 278);

-- Egg (daily_price_id = 40 for Aug 30)
INSERT INTO city_prices (daily_price_id, city_id, price) VALUES
(40, 1, 50), (40, 2, 48), (40, 3, 46), (40, 4, 44), (40, 5, 50), (40, 6, 46), (40, 7, 45), (40, 8, 47);

-- Chicken (daily_price_id = 45 for Aug 30)
INSERT INTO city_prices (daily_price_id, city_id, price) VALUES
(45, 1, 220), (45, 2, 210), (45, 3, 200), (45, 4, 195), (45, 5, 225), (45, 6, 205), (45, 7, 198), (45, 8, 208);

-- Beef (daily_price_id = 50 for Aug 30)
INSERT INTO city_prices (daily_price_id, city_id, price) VALUES
(50, 1, 780), (50, 2, 750), (50, 3, 720), (50, 4, 700), (50, 5, 770), (50, 6, 740), (50, 7, 710), (50, 8, 730);

-- =============================================
-- 7. Insert News
-- =============================================
INSERT INTO news (product_id, url, title, source, date_added) VALUES
(1, NULL, 'ভারত থেকে পেঁয়াজ আমদানি কমেছে — দাম বাড়ার আশঙ্কা', 'প্রথম আলো', '2026-08-29'),
(1, NULL, 'কেন্দ্রীয় মজুদে পেঁয়াজ কমছে, আগামী সপ্তাহে প্রভাব পড়তে পারে', 'ডেইলি স্টার', '2026-08-28'),
(2, NULL, 'কোল্ড স্টোরেজে পর্যাপ্ত আলু মজুদ রয়েছে', 'বাংলা ট্রিবিউন', '2026-08-28'),
(2, NULL, 'আলুর দাম স্থিতিশীল থাকবে বলে জানিয়েছে কৃষি মন্ত্রণালয়', 'যুগান্তর', '2026-08-29'),
(3, NULL, 'বন্যায় মরিচ ক্ষেতের ক্ষতি — দাম অস্থির', 'কালের কণ্ঠ', '2026-08-27'),
(3, NULL, 'নতুন চালানের মরিচ বাজারে আসছে, দাম কমবে', 'প্রথম আলো', '2026-08-29'),
(4, NULL, 'বর্ষায় টমেটোর উৎপাদন কমেছে — দাম বাড়ছে', 'সমকাল', '2026-08-29'),
(4, NULL, 'ভারত থেকে টমেটো আমদানি বাড়ানোর চিন্তা', 'বাংলাদেশ প্রতিদিন', '2026-08-28'),
(5, NULL, 'বেগুনের ভালো ফলন হয়েছে এবার', 'যুগান্তর', '2026-08-28'),
(5, NULL, 'স্থানীয় বাজারে বেগুন সহজলভ্য', 'কালের কণ্ঠ', '2026-08-29'),
(6, NULL, 'চীন থেকে রসুন আমদানি স্বাভাবিক চলছে', 'ডেইলি স্টার', '2026-08-29'),
(6, NULL, 'দেশীয় রসুনের মৌসুম শুরু হতে এখনো সময় আছে', 'বাংলা ট্রিবিউন', '2026-08-27'),
(7, NULL, 'নতুন চালানের আদা আসছে — দাম কমার সম্ভাবনা', 'প্রথম আলো', '2026-08-29'),
(7, NULL, 'ভারত থেকে আদা আমদানি বেড়েছে', 'সমকাল', '2026-08-28'),
(8, NULL, 'ফিডের দাম বাড়ায় ডিমের দাম বাড়ছে', 'কালের কণ্ঠ', '2026-08-29'),
(8, NULL, 'পোল্ট্রি খামারিরা ক্ষতির মুখে', 'যুগান্তর', '2026-08-28'),
(9, NULL, 'মুরগির ফিডের দাম বেড়ে যাওয়ায় ব্রয়লারের দাম বাড়ছে', 'বাংলা ট্রিবিউন', '2026-08-29'),
(9, NULL, 'মুরগির মাংসের চাহিদা বাড়ছে ঈদের আগে', 'ডেইলি স্টার', '2026-08-28'),
(10, NULL, 'গরুর মাংসের দাম স্থিতিশীল রয়েছে', 'প্রথম আলো', '2026-08-29'),
(10, NULL, 'আমদানি গরু কমায় সামনে দাম বাড়তে পারে', 'সমকাল', '2026-08-27');
