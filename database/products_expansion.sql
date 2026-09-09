-- =============================================
-- BazarDor — Extended Product List (Vegetables, Meat, Fish)
-- Run this script in phpMyAdmin to add more categories and items
-- =============================================

USE bazardo1_bazardor_db;

-- =============================================
-- 1. Add New Categories
-- =============================================
-- Category 1 (সবজি) and Category 4 (মাংস) already exist.
-- We will only add Category 5 for Fish.
INSERT INTO categories (id, name, sort_order) VALUES
(5, 'মাছ', 5)
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- =============================================
-- 2. Add New Products
-- =============================================

-- --- সবজি (Vegetables - Category 1) ---
INSERT INTO products (category_id, slug, name, name_en, unit, icon_path) VALUES
(1, 'okra', 'ঢ্যাঁড়শ', 'Okra', 'কেজি', 'assets/okra.png'),
(1, 'bitter-gourd', 'করলা', 'Bitter Gourd', 'কেজি', 'assets/bitter-gourd.png'),
(1, 'cabbage', 'বাঁধাকপি', 'Cabbage', 'পিস', 'assets/cabbage.png'),
(1, 'cauliflower', 'ফুলকপি', 'Cauliflower', 'পিস', 'assets/cauliflower.png'),
(1, 'carrot', 'গাজর', 'Carrot', 'কেজি', 'assets/carrot.png'),
(1, 'cucumber', 'শসা', 'Cucumber', 'কেজি', 'assets/cucumber.png'),
(1, 'bottle-gourd', 'লাউ', 'Bottle Gourd', 'পিস', 'assets/bottle-gourd.png'),
(1, 'pumpkin', 'মিষ্টিকুমড়া', 'Pumpkin', 'কেজি', 'assets/pumpkin.png'),
(1, 'radish', 'মুলা', 'Radish', 'কেজি', 'assets/radish.png'),
(1, 'papaya', 'পেঁপে', 'Papaya', 'কেজি', 'assets/papaya.png'),
(1, 'lemon', 'লেবু', 'Lemon', 'হালি', 'assets/lemon.png'),
(1, 'green-banana', 'কাঁচকলা', 'Green Banana', 'হালি', 'assets/green-banana.png'),
(1, 'pointed-gourd', 'পটল', 'Pointed Gourd', 'কেজি', 'assets/pointed-gourd.png'),
(1, 'snake-gourd', 'চিচিঙ্গা', 'Snake Gourd', 'কেজি', 'assets/snake-gourd.png'),
(1, 'sponge-gourd', 'ধুন্দুল', 'Sponge Gourd', 'কেজি', 'assets/sponge-gourd.png'),
(1, 'ash-gourd', 'চালকুমড়া', 'Ash Gourd', 'পিস', 'assets/ash-gourd.png'),
(1, 'yardlong-bean', 'বরবটি', 'Yardlong Bean', 'কেজি', 'assets/yardlong-bean.png'),
(1, 'spinach', 'পালং শাক', 'Spinach', 'আঁটি', 'assets/spinach.png'),
(1, 'red-amaranth', 'লাল শাক', 'Red Amaranth', 'আঁটি', 'assets/red-amaranth.png'),
(1, 'water-spinach', 'কলমি শাক', 'Water Spinach', 'আঁটি', 'assets/water-spinach.png'),
(1, 'taro-root', 'কচু / মুখী কচু', 'Taro Root', 'কেজি', 'assets/taro-root.png'),
(1, 'taro-stolon', 'কচুর লতি', 'Taro Stolon', 'কেজি', 'assets/taro-stolon.png'),
(1, 'taro-leaves', 'কচু শাক', 'Taro Leaves', 'আঁটি', 'assets/taro-leaves.png'),
(1, 'teasel-gourd', 'কাঁকরোল', 'Teasel Gourd', 'কেজি', 'assets/teasel-gourd.png'),
(1, 'ivy-gourd', 'তেলাকুচা / কুন্দরি', 'Ivy Gourd', 'কেজি', 'assets/ivy-gourd.png'),
(1, 'ridge-gourd', 'ঝিঙ্গা', 'Ridge Gourd', 'কেজি', 'assets/ridge-gourd.png'),
(1, 'drumstick', 'সজিনা', 'Drumstick', 'কেজি', 'assets/drumstick.png'),
(1, 'plantain-flower', 'কলার মোচা', 'Plantain Flower', 'পিস', 'assets/plantain-flower.png'),
(1, 'plantain-stem', 'কলার থোড়', 'Plantain Stem', 'পিস', 'assets/plantain-stem.png'),
(1, 'sweet-potato', 'মিষ্টি আলু', 'Sweet Potato', 'কেজি', 'assets/sweet-potato.png'),
(1, 'malabar-spinach', 'পুঁই শাক', 'Malabar Spinach', 'আঁটি', 'assets/malabar-spinach.png'),
(1, 'bottle-gourd-leaves', 'লাউ শাক', 'Bottle Gourd Leaves', 'আঁটি', 'assets/bottle-gourd-leaves.png'),
(1, 'pumpkin-leaves', 'কুমড়া শাক', 'Pumpkin Leaves', 'আঁটি', 'assets/pumpkin-leaves.png'),
(1, 'amaranth', 'ডাঁটা শাক', 'Amaranth', 'আঁটি', 'assets/amaranth.png'),
(1, 'stem-amaranth', 'ডাঁটা', 'Stem Amaranth', 'আঁটি', 'assets/stem-amaranth.png'),
(1, 'coriander-leaves', 'ধনে পাতা', 'Coriander Leaves', 'আঁটি', 'assets/coriander-leaves.png'),
(1, 'mint-leaves', 'পুদিনা পাতা', 'Mint Leaves', 'আঁটি', 'assets/mint-leaves.png'),
(1, 'green-jackfruit', 'এঁচোড়', 'Green Jackfruit', 'কেজি', 'assets/green-jackfruit.png'),
(1, 'long-coriander', 'বিলাতি ধনে পাতা', 'Long Coriander', 'আঁটি', 'assets/long-coriander.png'),
(1, 'hyacinth-bean', 'শিম', 'Hyacinth Bean', 'কেজি', 'assets/hyacinth-bean.png'),
(1, 'bean-seeds', 'শিমের বিচি', 'Bean Seeds', 'কেজি', 'assets/bean-seeds.png'),
(1, 'green-tomato', 'কাঁচা টমেটো', 'Green Tomato', 'কেজি', 'assets/green-tomato.png'),
(1, 'capsicum', 'ক্যাপসিকাম', 'Capsicum', 'কেজি', 'assets/capsicum.png'),
(1, 'turnip', 'শালগম', 'Turnip', 'কেজি', 'assets/turnip.png'),
(1, 'beetroot', 'বিটরুট', 'Beetroot', 'কেজি', 'assets/beetroot.png'),
(1, 'jute-leaves', 'পাট শাক', 'Jute Leaves', 'আঁটি', 'assets/jute-leaves.png'),
(1, 'mustard-leaves', 'সরিষা শাক', 'Mustard Leaves', 'আঁটি', 'assets/mustard-leaves.png');


-- --- মাংস (Meat - Category 4) ---
INSERT INTO products (category_id, slug, name, name_en, unit, icon_path) VALUES
(4, 'mutton', 'খাসির মাংস', 'Mutton', 'কেজি', 'assets/mutton.png'),
(4, 'sonali-chicken', 'সোনালী মুরগি', 'Sonali Chicken', 'কেজি', 'assets/sonali-chicken.png'),
(4, 'deshi-chicken', 'দেশি মুরগি', 'Deshi Chicken', 'কেজি', 'assets/deshi-chicken.png'),
(4, 'duck', 'হাঁস', 'Duck', 'পিস', 'assets/duck.png'),
(4, 'pigeon', 'কবুতর', 'Pigeon', 'জোড়া', 'assets/pigeon.png'),
(4, 'quail', 'কোয়েল', 'Quail', 'পিস', 'assets/quail.png'),
(4, 'beef-liver', 'গরুর কলিজা', 'Beef Liver', 'কেজি', 'assets/beef-liver.png'),
(4, 'mutton-liver', 'খাসির কলিজা', 'Mutton Liver', 'কেজি', 'assets/mutton-liver.png'),
(4, 'beef-head', 'গরুর মাথার মাংস', 'Cow Head Meat', 'কেজি', 'assets/beef-head.png'),
(4, 'beef-brain', 'গরুর মগজ', 'Cow Brain', 'কেজি', 'assets/beef-brain.png'),
(4, 'beef-tripe', 'গরুর বট/ভুঁড়ি', 'Cow Tripe', 'কেজি', 'assets/beef-tripe.png'),
(4, 'beef-paya', 'গরুর পায়া/নেহারি', 'Cow Trotters', 'পিস', 'assets/beef-paya.png');


-- --- মাছ (Fish - Category 5) ---
INSERT INTO products (category_id, slug, name, name_en, unit, icon_path) VALUES
(5, 'rui-fish', 'রুই মাছ', 'Rui Fish', 'কেজি', 'assets/rui-fish.png'),
(5, 'katla-fish', 'কাতলা মাছ', 'Katla Fish', 'কেজি', 'assets/katla-fish.png'),
(5, 'hilsa-fish', 'ইলিশ মাছ', 'Hilsa Fish', 'কেজি', 'assets/hilsa-fish.png'),
(5, 'pangash-fish', 'পাঙ্গাশ মাছ', 'Pangash Fish', 'কেজি', 'assets/pangash-fish.png'),
(5, 'tilapia-fish', 'তেলাপিয়া মাছ', 'Tilapia Fish', 'কেজি', 'assets/tilapia-fish.png'),
(5, 'shrimp', 'চিংড়ি মাছ', 'Shrimp', 'কেজি', 'assets/shrimp.png'),
(5, 'pabda-fish', 'পাবদা মাছ', 'Pabda Fish', 'কেজি', 'assets/pabda-fish.png'),
(5, 'koi-fish', 'কৈ মাছ', 'Koi Fish', 'কেজি', 'assets/koi-fish.png'),
(5, 'tengra-fish', 'টেংরা মাছ', 'Tengra Fish', 'কেজি', 'assets/tengra-fish.png'),
(5, 'mola-fish', 'মলা মাছ', 'Mola Fish', 'কেজি', 'assets/mola-fish.png'),
(5, 'mrigal-fish', 'মৃগেল মাছ', 'Mrigal Fish', 'কেজি', 'assets/mrigal-fish.png'),
(5, 'kalibaus-fish', 'কালিবাউশ মাছ', 'Kalibaus Fish', 'কেজি', 'assets/kalibaus-fish.png'),
(5, 'boal-fish', 'বোয়াল মাছ', 'Boal Fish', 'কেজি', 'assets/boal-fish.png'),
(5, 'ayre-fish', 'আইড় মাছ', 'Ayre Fish', 'কেজি', 'assets/ayre-fish.png'),
(5, 'shol-fish', 'শোল মাছ', 'Shol Fish', 'কেজি', 'assets/shol-fish.png'),
(5, 'taki-fish', 'টাকি মাছ', 'Taki Fish', 'কেজি', 'assets/taki-fish.png'),
(5, 'gazar-fish', 'গজার মাছ', 'Gazar Fish', 'কেজি', 'assets/gazar-fish.png'),
(5, 'magur-fish', 'মাগুর মাছ', 'Magur Fish', 'কেজি', 'assets/magur-fish.png'),
(5, 'singi-fish', 'শিং মাছ', 'Singi Fish', 'কেজি', 'assets/singi-fish.png'),
(5, 'bele-fish', 'বেলে মাছ', 'Bele Fish', 'কেজি', 'assets/bele-fish.png'),
(5, 'baim-fish', 'বাইন মাছ', 'Baim Fish', 'কেজি', 'assets/baim-fish.png'),
(5, 'puti-fish', 'পুঁটি মাছ', 'Puti Fish', 'কেজি', 'assets/puti-fish.png'),
(5, 'sarpunti-fish', 'সরপুঁটি মাছ', 'Sarpunti Fish', 'কেজি', 'assets/sarpunti-fish.png'),
(5, 'batasi-fish', 'বাতাসী মাছ', 'Batasi Fish', 'কেজি', 'assets/batasi-fish.png'),
(5, 'kachki-fish', 'কাঁচকি মাছ', 'Kachki Fish', 'কেজি', 'assets/kachki-fish.png'),
(5, 'chapila-fish', 'চাপিলা মাছ', 'Chapila Fish', 'কেজি', 'assets/chapila-fish.png'),
(5, 'kholisha-fish', 'খলিসা মাছ', 'Kholisha Fish', 'কেজি', 'assets/kholisha-fish.png'),
(5, 'foli-fish', 'ফলি মাছ', 'Foli Fish', 'কেজি', 'assets/foli-fish.png'),
(5, 'chital-fish', 'চিতল মাছ', 'Chital Fish', 'কেজি', 'assets/chital-fish.png'),
(5, 'silver-carp', 'সিলভার কার্প', 'Silver Carp', 'কেজি', 'assets/silver-carp.png'),
(5, 'grass-carp', 'গ্রাস কার্প', 'Grass Carp', 'কেজি', 'assets/grass-carp.png'),
(5, 'bighead-carp', 'বিগহেড কার্প', 'Bighead Carp', 'কেজি', 'assets/bighead-carp.png'),
(5, 'rupchanda-fish', 'রূপচাঁদা মাছ', 'Rupchanda Fish', 'কেজি', 'assets/rupchanda-fish.png'),
(5, 'loitta-fish', 'লইট্টা মাছ', 'Loitta Fish', 'কেজি', 'assets/loitta-fish.png'),
(5, 'koral-fish', 'কোরাল/ভেটকি মাছ', 'Koral Fish', 'কেজি', 'assets/koral-fish.png');
