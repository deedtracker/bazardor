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
            dp_today.min_price,
            dp_today.max_price,
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
    $possessiveName = getBengaliPossessive($product['name']);
    $niceCurrentPrice = roundRetailPrice($currentPrice);
    
    $minPrice = $latestPrice['min_price'] ?? null;
    $maxPrice = $latestPrice['max_price'] ?? null;
    $rangeText = "";
    if ($minPrice && $maxPrice && $minPrice !== $maxPrice) {
        $rangeText = " বাজারে এর সর্বনিম্ন দাম <strong>" . toBengali(roundRetailPrice($minPrice)) . " টাকা</strong> এবং সর্বোচ্চ <strong>" . toBengali(roundRetailPrice($maxPrice)) . " টাকা</strong> পর্যন্ত দেখা গেছে।";
    }
    
    if ($diff > 0) {
        $summary = "আজ {$todayDate}। গতকালের চেয়ে আজ <strong>" . e($possessiveName) . "</strong> দাম " . e($product['unit']) . "তে <strong>" . toBengali(roundRetailPrice($diff)) . " টাকা বেড়েছে</strong>। বর্তমানে প্রতি " . e($product['unit']) . " " . e($product['name']) . " গড়ে <strong>" . toBengali($niceCurrentPrice) . " টাকায়</strong> বিক্রি হচ্ছে।{$rangeText} সামনের দিনগুলোতে এই দাম আরও বাড়তে পারে।";
    } elseif ($diff < 0) {
        $summary = "আজ {$todayDate}। গতকালের চেয়ে আজ <strong>" . e($possessiveName) . "</strong> দাম " . e($product['unit']) . "তে <strong>" . toBengali(roundRetailPrice(abs($diff))) . " টাকা কমেছে</strong>। বর্তমানে প্রতি " . e($product['unit']) . " " . e($product['name']) . " গড়ে <strong>" . toBengali($niceCurrentPrice) . " টাকায়</strong> বিক্রি হচ্ছে।{$rangeText} ক্রেতাদের জন্য এটি ভালো খবর।";
    } else {
        $summary = "আজ {$todayDate}। আজ <strong>" . e($possessiveName) . "</strong> দাম গতকালের <strong>মতোই রয়েছে</strong>। বর্তমানে প্রতি " . e($product['unit']) . " " . e($product['name']) . " গড়ে <strong>" . toBengali($niceCurrentPrice) . " টাকায়</strong> বিক্রি হচ্ছে।{$rangeText} বাজার স্থিতিশীল আছে।";
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
        'latestPriceData' => $latestPrice,
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

function saveDailyPrices($productId, $date, $basePrice, $isAuto = 0, $minPrice = null, $maxPrice = null) {
    $db = getDB();
    $basePrice = (int)$basePrice;

    // Check if entry already exists for today
    $stmt = $db->prepare('SELECT id FROM daily_prices WHERE product_id = ? AND price_date = ?');
    $stmt->execute([$productId, $date]);
    $existing = $stmt->fetch();

    if ($existing) {
        $dailyPriceId = $existing['id'];
        // Update the average_price (base price), min, max and is_auto flag
        $db->prepare('UPDATE daily_prices SET average_price = ?, min_price = ?, max_price = ?, is_auto = ? WHERE id = ?')
           ->execute([$basePrice, $minPrice, $maxPrice, $isAuto, $dailyPriceId]);
    } else {
        $insertDp = $db->prepare('INSERT INTO daily_prices (product_id, price_date, average_price, min_price, max_price, is_auto) VALUES (?, ?, ?, ?, ?, ?)');
        $insertDp->execute([$productId, $date, $basePrice, $minPrice, $maxPrice, $isAuto]);
        $dailyPriceId = $db->lastInsertId();
    }

    // Delete old city prices for this entry
    $db->prepare('DELETE FROM city_prices WHERE daily_price_id = ?')->execute([$dailyPriceId]);

    // Get product config
    $prodStmt = $db->prepare('SELECT pricing_mode, price_difference FROM products WHERE id = ?');
    $prodStmt->execute([$productId]);
    $prodRow = $prodStmt->fetch();
    
    $pricingMode = $prodRow['pricing_mode'] ?? 'variable';
    $priceDiff = isset($prodRow['price_difference']) ? (int)$prodRow['price_difference'] : 5;

    // Get all 64 districts with their weather
    $cities = getAllCities();
    
    // Fetch weather mapping
    $weatherStmt = $db->query("SELECT city_id, rain_mm, temperature, weather_code FROM city_weather");
    $weatherData = [];
    while ($row = $weatherStmt->fetch()) {
        $weatherData[$row['city_id']] = $row;
    }

    // Safety floor: price can never drop below 50% of base
    $priceFloor = max(1, (int)round($basePrice * 0.5));

    // Insert city prices
    $insertStmt = $db->prepare('INSERT INTO city_prices (daily_price_id, city_id, price) VALUES (?, ?, ?)');

    foreach ($cities as $city) {
        $cName = $city['name'];
        $cId = $city['id'];
        
        // Exact price for Dhaka
        if ($cName === 'ঢাকা') {
            $insertStmt->execute([$dailyPriceId, $cId, $basePrice]);
            continue;
        }

        // Apply Weather Variance
        $weatherModifier = 0;
        if (isset($weatherData[$cId])) {
            $rain = (float)$weatherData[$cId]['rain_mm'];
            $temp = (float)$weatherData[$cId]['temperature'];
            
            // If heavy rain (> 5mm) or extreme heat (> 38C), push price upwards
            if ($rain > 5.0) {
                // Bias upwards by picking a higher random range
                $weatherModifier = (int)ceil($priceDiff * 0.5); // Push up by 50% of difference
            } elseif ($temp > 38.0) {
                $weatherModifier = (int)ceil($priceDiff * 0.3); // Push up by 30% of difference
            }
        }

        // Calculate random variance but skew it with the weather modifier
        $adjustment = random_int(-$priceDiff, $priceDiff);
        
        // If weather is bad, we shift the adjustment upwards but don't exceed max possible diff by too much
        $adjustment += $weatherModifier;
        if ($adjustment > ($priceDiff * 1.5)) {
            $adjustment = (int)($priceDiff * 1.5);
        }

        $calculated = $basePrice + $adjustment;

        // If it's a variable item (like vegetables/fish), always round to nearest 5 Taka for realism
        // If it's a flat item (like rice/sugar), keep exact Taka amounts
        if ($pricingMode === 'variable') {
            $calculated = round($calculated / 5) * 5;
        } else {
            $calculated = round($calculated);
        }

        // Final price
        $finalPrice = max($priceFloor, $calculated);

        $insertStmt->execute([$dailyPriceId, $cId, $finalPrice]);
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
