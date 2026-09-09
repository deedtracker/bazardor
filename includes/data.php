<?php
// =============================================
// BazarDor — Data Access Functions
// All database queries are centralized here.
// =============================================
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

/**
 * Get all categories.
 */
function getAllCategories() {
    $db = getDB();
    $stmt = $db->query('SELECT * FROM categories ORDER BY sort_order ASC');
    return $stmt->fetchAll();
}

/**
 * Get all cities.
 */
function getAllCities() {
    $db = getDB();
    $stmt = $db->query('SELECT * FROM cities ORDER BY id ASC');
    return $stmt->fetchAll();
}

/**
 * Get all products with today's price and yesterday's price.
 * This powers the Home page list.
 */
function getAllProductsWithPrices() {
    $db = getDB();

    $sql = "
        SELECT
            p.id, p.slug, p.name, p.name_en, p.unit, p.icon_path,
            c.name AS category_name,
            dp_today.average_price AS current_price,
            dp_today.price_date AS latest_date,
            dp_prev.average_price AS previous_price
        FROM products p
        JOIN categories c ON p.category_id = c.id
        
        -- Get the latest date per product
        LEFT JOIN (
            SELECT product_id, MAX(price_date) as max_date
            FROM daily_prices
            GROUP BY product_id
        ) latest ON latest.product_id = p.id
        LEFT JOIN daily_prices dp_today 
            ON dp_today.product_id = p.id AND dp_today.price_date = latest.max_date

        -- Get the previous date per product (less than max_date)
        LEFT JOIN (
            SELECT dp.product_id, MAX(dp.price_date) as prev_date
            FROM daily_prices dp
            JOIN (
                SELECT product_id, MAX(price_date) as max_date
                FROM daily_prices
                GROUP BY product_id
            ) l ON l.product_id = dp.product_id
            WHERE dp.price_date < l.max_date
            GROUP BY dp.product_id
        ) previous ON previous.product_id = p.id
        LEFT JOIN daily_prices dp_prev 
            ON dp_prev.product_id = p.id AND dp_prev.price_date = previous.prev_date

        ORDER BY latest.max_date DESC, c.sort_order ASC, p.name ASC
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll();

    // Calculate diff for each product
    foreach ($products as &$product) {
        $product['current_price'] = (float)($product['current_price'] ?? 0);
        $product['previous_price'] = (float)($product['previous_price'] ?? $product['current_price']);
        $product['diff'] = $product['current_price'] - $product['previous_price'];
    }

    return $products;
}

/**
 * Get a single product by slug.
 */
function getProductBySlug($slug) {
    $db = getDB();
    $stmt = $db->prepare('
        SELECT p.*, c.name AS category_name
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE p.slug = ?
    ');
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

/**
 * Get the latest daily price for a product.
 */
function getLatestPrice($productId) {
    $db = getDB();
    $stmt = $db->prepare('
        SELECT * FROM daily_prices
        WHERE product_id = ?
        ORDER BY price_date DESC
        LIMIT 1
    ');
    $stmt->execute([$productId]);
    return $stmt->fetch();
}

/**
 * Get the previous day's price for a product (relative to the latest).
 */
function getPreviousPrice($productId, $latestDate) {
    $db = getDB();
    $stmt = $db->prepare('
        SELECT * FROM daily_prices
        WHERE product_id = ? AND price_date < ?
        ORDER BY price_date DESC
        LIMIT 1
    ');
    $stmt->execute([$productId, $latestDate]);
    return $stmt->fetch();
}

/**
 * Get the last N days of price history for a product.
 */
function getPriceHistory($productId, $days = 5) {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT * FROM daily_prices
        WHERE product_id = ?
        ORDER BY price_date DESC
        LIMIT ?
    ");
    $stmt->execute([$productId, $days]);
    $rows = $stmt->fetchAll();
    // Reverse so oldest is first (for timeline display)
    return array_reverse($rows);
}

/**
 * Get city prices for a specific daily_price entry.
 */
function getCityPrices($dailyPriceId) {
    $db = getDB();
    $stmt = $db->prepare('
        SELECT cp.price, ci.name AS city_name
        FROM city_prices cp
        JOIN cities ci ON cp.city_id = ci.id
        WHERE cp.daily_price_id = ?
        ORDER BY ci.id ASC
    ');
    $stmt->execute([$dailyPriceId]);
    return $stmt->fetchAll();
}

/**
 * Get news for a product (latest first).
 */
function getNewsByProduct($productId, $limit = 5) {
    $db = getDB();
    $stmt = $db->prepare('
        SELECT * FROM news
        WHERE product_id = ?
        ORDER BY date_added DESC
        LIMIT ?
    ');
    $stmt->execute([$productId, $limit]);
    return $stmt->fetchAll();
}

/**
 * Get full product detail data (everything needed for the detail page).
 * All calculations are automatic.
 */
function getProductDetail($slug) {
    $product = getProductBySlug($slug);
    if (!$product) return null;

    $latestPrice = getLatestPrice($product['id']);
    $previousPrice = $latestPrice ? getPreviousPrice($product['id'], $latestPrice['price_date']) : null;

    $currentPrice = $latestPrice ? (float)$latestPrice['average_price'] : 0;
    $prevPrice = $previousPrice ? (float)$previousPrice['average_price'] : $currentPrice;
    $diff = $currentPrice - $prevPrice;

    $cityPrices = $latestPrice ? getCityPrices($latestPrice['id']) : [];
    $history = getPriceHistory($product['id'], 5);
    $predictions = calculatePredictions($history, $product['unit']);
    $news = getNewsByProduct($product['id']);
    $trendSummary = buildTrendSummary($history);

    // Find cheapest and most expensive city
    $cheapest = null;
    $expensive = null;
    if (!empty($cityPrices)) {
        $sorted = $cityPrices;
        usort($sorted, fn($a, $b) => $a['price'] - $b['price']);
        $cheapestRow = $sorted[0];
        $expensiveRow = $sorted[count($sorted) - 1];
        
        $cheapest = $cheapestRow['city_name'];
        $expensive = $expensiveRow['city_name'];

        // Extract the rest of the cities
        $filtered = array_filter($cityPrices, function($c) use ($cheapest, $expensive) {
            return $c['city_name'] !== $cheapest && $c['city_name'] !== $expensive;
        });
        
        // Push cheapest and expensive to the top
        if ($cheapest === $expensive) {
            array_unshift($filtered, $cheapestRow);
        } else {
            array_unshift($filtered, $expensiveRow); // 2nd position
            array_unshift($filtered, $cheapestRow);  // 1st position
        }
        
        // Re-assign sorted array
        $cityPrices = array_values($filtered);
    }

    // Build summary text
    $todayDate = toBengaliDate($latestPrice ? $latestPrice['price_date'] : today());
    if ($diff > 0) {
        $summary = "আজ {$todayDate}। গতকালের চেয়ে আজ <strong>" . e($product['name']) . "</strong> এর দাম " . e($product['unit']) . "তে <strong>" . toBengali($diff) . " টাকা বেড়েছে</strong>। বর্তমানে প্রতি " . e($product['unit']) . " " . e($product['name']) . " গড়ে <strong>" . toBengali($currentPrice) . " টাকায়</strong> বিক্রি হচ্ছে এবং সামনের দিনগুলোতে এই দাম আরও বাড়তে পারে।";
    } elseif ($diff < 0) {
        $summary = "আজ {$todayDate}। গতকালের চেয়ে আজ <strong>" . e($product['name']) . "</strong> এর দাম " . e($product['unit']) . "তে <strong>" . toBengali(abs($diff)) . " টাকা কমেছে</strong>। বর্তমানে প্রতি " . e($product['unit']) . " " . e($product['name']) . " গড়ে <strong>" . toBengali($currentPrice) . " টাকায়</strong> বিক্রি হচ্ছে। ক্রেতাদের জন্য এটি ভালো খবর।";
    } else {
        $summary = "আজ {$todayDate}। আজ <strong>" . e($product['name']) . "</strong> এর দাম গতকালের <strong>মতোই রয়েছে</strong>। বর্তমানে প্রতি " . e($product['unit']) . " " . e($product['name']) . " গড়ে <strong>" . toBengali($currentPrice) . " টাকায়</strong> বিক্রি হচ্ছে। বাজার স্থিতিশীল আছে।";
    }

    return [
        'product' => $product,
        'currentPrice' => $currentPrice,
        'previousPrice' => $prevPrice,
        'diff' => $diff,
        'diffClass' => getDiffClass($diff),
        'cityPrices' => $cityPrices,
        'cheapest' => $cheapest,
        'expensive' => $expensive,
        'history' => $history,
        'predictions' => $predictions,
        'news' => $news,
        'trendSummary' => $trendSummary,
        'summary' => $summary,
        'lastUpdated' => $latestPrice ? toBengaliDate($latestPrice['price_date']) : '',
        'latestDate' => $latestPrice ? $latestPrice['price_date'] : today(),
    ];
}

// =============================================
// Admin-specific data functions
// =============================================

/**
 * Verify admin login credentials.
 */
function verifyLogin($username, $password) {
    $db = getDB();
    $stmt = $db->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}

/**
 * Save daily prices for a product (Smart 64 District Automation).
 * Accepts product_id, date, and the base price (Dhaka retail price).
 *
 * Uses a District Tier system with per-category absolute Taka bounds.
 * Each district is assigned a fixed tier, and each category defines
 * tight [min, max] Taka adjustments per tier. This ensures:
 * - Farming hubs are always cheaper for vegetables
 * - River hubs are always cheaper for fish
 * - Remote hill tracts are always slightly more expensive
 * - Dhaka-adjacent cities stay nearly identical to Dhaka
 */
function saveDailyPrices($productId, $date, $basePrice) {
    $db = getDB();

    // Insert or update daily_prices with the base average
    $stmt = $db->prepare('
        INSERT INTO daily_prices (product_id, price_date, average_price)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE average_price = VALUES(average_price)
    ');
    $stmt->execute([$productId, $date, $basePrice]);

    // Get the daily_price_id
    $dpStmt = $db->prepare('SELECT id FROM daily_prices WHERE product_id = ? AND price_date = ?');
    $dpStmt->execute([$productId, $date]);
    $dailyPriceId = $dpStmt->fetch()['id'];

    // Delete old city prices for this entry
    $db->prepare('DELETE FROM city_prices WHERE daily_price_id = ?')->execute([$dailyPriceId]);

    // Get product category
    $prodStmt = $db->prepare('SELECT category_id FROM products WHERE id = ?');
    $prodStmt->execute([$productId]);
    $catId = (int)$prodStmt->fetch()['category_id'];

    // Get all 64 districts
    $cities = getAllCities();

    // =============================================
    // District Tier Assignments
    // =============================================
    $districtTiers = [
        // Dhaka — exact base price
        'ঢাকা' => 'dhaka',

        // Dhaka Adjacent — nearly identical to Dhaka
        'গাজীপুর' => 'dhaka_adjacent',
        'নারায়ণগঞ্জ' => 'dhaka_adjacent',
        'মানিকগঞ্জ' => 'dhaka_adjacent',
        'মুন্সীগঞ্জ' => 'dhaka_adjacent',
        'নরসিংদী' => 'dhaka_adjacent',
        'টাঙ্গাইল' => 'dhaka_adjacent',

        // Divisional Cities — slight variance
        'চট্টগ্রাম' => 'divisional',
        'রাজশাহী' => 'divisional',
        'খুলনা' => 'divisional',
        'সিলেট' => 'divisional',
        'রংপুর' => 'divisional',
        'বরিশাল' => 'divisional',
        'ময়মনসিংহ' => 'divisional',

        // Farming Hubs — cheaper for vegetables & spices
        'বগুড়া' => 'farming_hub',
        'যশোর' => 'farming_hub',
        'দিনাজপুর' => 'farming_hub',
        'পঞ্চগড়' => 'farming_hub',
        'ঠাকুরগাঁও' => 'farming_hub',
        'নাটোর' => 'farming_hub',
        'জয়পুরহাট' => 'farming_hub',

        // River/Fish Hubs — cheaper for fish
        'চাঁদপুর' => 'river_hub',
        'ভোলা' => 'river_hub',
        'পটুয়াখালী' => 'river_hub',
        'কক্সবাজার' => 'river_hub',
        'সুনামগঞ্জ' => 'river_hub',
        'নেত্রকোণা' => 'river_hub',
        'বরগুনা' => 'river_hub',

        // Remote / Hill Tracts — always slightly more expensive
        'বান্দরবান' => 'remote',
        'খাগড়াছড়ি' => 'remote',
        'রাঙ্গামাটি' => 'remote',

        // Mid Tier — all remaining districts (standard variance)
        'ফরিদপুর' => 'mid_tier',
        'গোপালগঞ্জ' => 'mid_tier',
        'কিশোরগঞ্জ' => 'mid_tier',
        'মাদারীপুর' => 'mid_tier',
        'রাজবাড়ী' => 'mid_tier',
        'শরীয়তপুর' => 'mid_tier',
        'ব্রাহ্মণবাড়িয়া' => 'mid_tier',
        'কুমিল্লা' => 'mid_tier',
        'ফেনী' => 'mid_tier',
        'লক্ষ্মীপুর' => 'mid_tier',
        'নোয়াখালী' => 'mid_tier',
        'নওগাঁ' => 'mid_tier',
        'চাঁপাইনবাবগঞ্জ' => 'mid_tier',
        'পাবনা' => 'mid_tier',
        'সিরাজগঞ্জ' => 'mid_tier',
        'বাগেরহাট' => 'mid_tier',
        'চুয়াডাঙ্গা' => 'mid_tier',
        'ঝিনাইদহ' => 'mid_tier',
        'কুষ্টিয়া' => 'mid_tier',
        'মাগুরা' => 'mid_tier',
        'মেহেরপুর' => 'mid_tier',
        'নড়াইল' => 'mid_tier',
        'সাতক্ষীরা' => 'mid_tier',
        'ঝালকাঠি' => 'mid_tier',
        'পিরোজপুর' => 'mid_tier',
        'হবিগঞ্জ' => 'mid_tier',
        'মৌলভীবাজার' => 'mid_tier',
        'গাইবান্ধা' => 'mid_tier',
        'কুড়িগ্রাম' => 'mid_tier',
        'লালমনিরহাট' => 'mid_tier',
        'নীলফামারী' => 'mid_tier',
        'জামালপুর' => 'mid_tier',
        'শেরপুর' => 'mid_tier',
    ];

    // =============================================
    // Per-Category Taka Bounds: [min, max] per tier
    // Final price = basePrice + random_int(min, max)
    // =============================================
    $categoryBounds = [
        // Category 1: সবজি (Vegetables) — high daily fluctuation
        1 => [
            'dhaka'          => [0, 0],
            'dhaka_adjacent' => [-2, 3],
            'divisional'     => [-3, 5],
            'farming_hub'    => [-8, -2],   // Always cheaper
            'mid_tier'       => [-3, 5],
            'river_hub'      => [-2, 5],
            'remote'         => [3, 8],     // Always more expensive
        ],
        // Category 2: মশলা (Spices — Onion, Garlic, Ginger) — medium fluctuation
        2 => [
            'dhaka'          => [0, 0],
            'dhaka_adjacent' => [-2, 2],
            'divisional'     => [-3, 5],
            'farming_hub'    => [-5, -1],
            'mid_tier'       => [-2, 5],
            'river_hub'      => [0, 5],
            'remote'         => [3, 8],
        ],
        // Category 3: প্রোটিন (Egg) — very stable
        3 => [
            'dhaka'          => [0, 0],
            'dhaka_adjacent' => [-1, 2],
            'divisional'     => [-2, 3],
            'farming_hub'    => [-3, 0],
            'mid_tier'       => [-2, 3],
            'river_hub'      => [-1, 3],
            'remote'         => [2, 5],
        ],
        // Category 4: মাংস (Meat — Beef, Mutton, Chicken) — stable, high price
        4 => [
            'dhaka'          => [0, 0],
            'dhaka_adjacent' => [-5, 5],
            'divisional'     => [-10, 10],
            'farming_hub'    => [-15, -5],
            'mid_tier'       => [-10, 10],
            'river_hub'      => [-5, 10],
            'remote'         => [5, 20],
        ],
        // Category 5: মাছ (Fish) — river hubs are much cheaper
        5 => [
            'dhaka'          => [0, 0],
            'dhaka_adjacent' => [-3, 3],
            'divisional'     => [-5, 8],
            'farming_hub'    => [-3, 5],
            'mid_tier'       => [-3, 8],
            'river_hub'      => [-15, -3],  // Always cheaper for fish
            'remote'         => [5, 15],
        ],
    ];

    // Fallback bounds for any unknown category (safe, tight variance)
    $defaultBounds = [
        'dhaka'          => [0, 0],
        'dhaka_adjacent' => [-1, 2],
        'divisional'     => [-2, 3],
        'farming_hub'    => [-3, 0],
        'mid_tier'       => [-2, 3],
        'river_hub'      => [-1, 3],
        'remote'         => [2, 5],
    ];

    // Select the bounds for this product's category
    $bounds = $categoryBounds[$catId] ?? $defaultBounds;

    // =============================================
    // Category-specific tier overrides
    // Some districts switch tiers depending on category
    // =============================================
    $tierOverrides = [];

    if ($catId === 1 || $catId === 2) {
        // For vegetables & spices: divisional cities that are also
        // farming regions should act as farming_hub
        $tierOverrides['রাজশাহী'] = 'farming_hub';
        $tierOverrides['রংপুর'] = 'farming_hub';
        $tierOverrides['ময়মনসিংহ'] = 'farming_hub';
    }

    if ($catId === 5) {
        // For fish: divisional cities near rivers act as river_hub
        $tierOverrides['বরিশাল'] = 'river_hub';
        $tierOverrides['ময়মনসিংহ'] = 'river_hub';
        // Cox's Bazar is already river_hub in base tiers (sea fish)
    }

    // Safety floor: price can never drop below 50% of base
    $priceFloor = max(1, (int)round($basePrice * 0.5));

    // Insert city prices
    $insertStmt = $db->prepare('INSERT INTO city_prices (daily_price_id, city_id, price) VALUES (?, ?, ?)');

    foreach ($cities as $city) {
        $cName = $city['name'];

        // Determine this district's tier (with category override if applicable)
        $tier = $tierOverrides[$cName] ?? ($districtTiers[$cName] ?? 'mid_tier');

        // Get the [min, max] Taka bounds for this tier
        $range = $bounds[$tier] ?? [0, 0];

        // Calculate the adjustment
        $adjustment = random_int($range[0], $range[1]);

        // Final price
        $calculated = $basePrice + $adjustment;

        // Round to nearest 5 Tk (খুচরা বাজার always uses 20, 25, 30, 35...)
        $calculated = round($calculated / 5) * 5;

        // Safety floor — never below 50% of base, never below 1
        $finalPrice = max($priceFloor, $calculated);

        $insertStmt->execute([$dailyPriceId, $city['id'], $finalPrice]);
    }

    return true;
}

/**
 * Add news for a product from a URL (auto-scrape title and source).
 */
function addNewsFromUrl($productId, $url) {
    $scraped = scrapeNewsFromUrl($url);
    if (empty($scraped['title'])) {
        return false;
    }

    $db = getDB();
    $stmt = $db->prepare('
        INSERT INTO news (product_id, url, title, source, date_added)
        VALUES (?, ?, ?, ?, CURDATE())
    ');
    $stmt->execute([$productId, $url, $scraped['title'], $scraped['source']]);
    return $scraped;
}

/**
 * Get all products (for admin dropdown).
 */
function getAllProducts() {
    $db = getDB();
    $stmt = $db->query('SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY c.sort_order, p.name');
    return $stmt->fetchAll();
}

/**
 * Generate latest notifications based on today's price changes.
 */
function getLatestNotifications() {
    $products = getAllProductsWithPrices();
    $maxIncrease = null;
    $maxDecrease = null;
    $feedItems = [];

    foreach ($products as $p) {
        if ($p['diff'] > 0) {
            if (!$maxIncrease || $p['diff'] > $maxIncrease['diff']) {
                $maxIncrease = $p;
            }
        } elseif ($p['diff'] < 0) {
            if (!$maxDecrease || $p['diff'] < $maxDecrease['diff']) {
                $maxDecrease = $p;
            }
        }
        
        if ($p['diff'] != 0) {
            $action = $p['diff'] > 0 ? 'বেড়েছে' : 'কমেছে';
            $feedItems[] = "আপডেট: " . e($p['name']) . " এর দাম " . toBengali(abs($p['diff'])) . " টাকা {$action}। বর্তমান দাম ৳" . toBengali($p['current_price']);
        }
    }

    // If very few changes today, add some stable products to keep the feed alive
    if (count($feedItems) < 5) {
        $shuffled = $products;
        shuffle($shuffled);
        foreach (array_slice($shuffled, 0, 8) as $p) {
            if ($p['diff'] == 0) {
                $feedItems[] = "আপডেট: বাজারে " . e($p['name']) . " পাওয়া যাচ্ছে ৳" . toBengali($p['current_price']) . " দরে।";
            }
        }
    }
    shuffle($feedItems);

    if ($maxDecrease) {
        array_unshift($feedItems, "📉 সর্বোচ্চ হ্রাস: " . e($maxDecrease['name']) . " " . toBengali(abs($maxDecrease['diff'])) . " টাকা কমেছে!");
    }
    if ($maxIncrease) {
        array_unshift($feedItems, "🔥 সর্বোচ্চ বৃদ্ধি: " . e($maxIncrease['name']) . " " . toBengali(abs($maxIncrease['diff'])) . " টাকা বেড়েছে!");
    }
    array_unshift($feedItems, "⚡ আজকের বাজারদর আপডেট সম্পন্ন হয়েছে।");

    return $feedItems;
}
