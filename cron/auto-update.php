<?php
// =============================================
// BazarDor — Automated Pricing System (Cron Job)
// =============================================
// This script automatically predicts and inserts
// missing daily prices for all products.
// =============================================

$secretKey = 'bazardor_auto_2026';
$isCli = php_sapi_name() === 'cli';

// Security check for browser access
if (!$isCli) {
    if (!isset($_GET['key']) || $_GET['key'] !== $secretKey) {
        http_response_code(403);
        die("Forbidden: Invalid Secret Key. Please provide the correct ?key parameter.");
    }
}

require_once __DIR__ . '/../includes/data.php';
require_once __DIR__ . '/../includes/helpers.php';

$db = getDB();
$todayStr = today();
$logFile = __DIR__ . '/auto-update.log';

$report = [];
$report[] = str_repeat('=', 50);
$report[] = "Auto-Update Run: " . date('Y-m-d H:i:s');
$report[] = "Target Date: " . $todayStr;
$report[] = str_repeat('=', 50);

$products = getAllProducts();
$updatedCount = 0;
$skippedCount = 0;

foreach ($products as $p) {
    $productId = $p['id'];
    
    // Check if this product already has a price for today
    $stmt = $db->prepare('SELECT id FROM daily_prices WHERE product_id = ? AND price_date = ?');
    $stmt->execute([$productId, $todayStr]);
    if ($stmt->fetch()) {
        $skippedCount++;
        continue;
    }

    // V3 Engine: Find the last HUMAN-ENTERED price (The Anchor)
    $stmtAnchor = $db->prepare('SELECT average_price FROM daily_prices WHERE product_id = ? AND is_auto = 0 ORDER BY price_date DESC LIMIT 1');
    $stmtAnchor->execute([$productId]);
    $anchorRow = $stmtAnchor->fetch();
    
    // If no human anchor exists, fallback to whatever the last entry was
    if ($anchorRow) {
        $humanAnchorPrice = (float)$anchorRow['average_price'];
    } else {
        $history = getPriceHistory($productId, 1);
        if (empty($history)) {
            $report[] = "[SKIPPED] {$p['name']} - No historical data available.";
            continue;
        }
        $humanAnchorPrice = (float)$history[0]['average_price'];
    }

    // Get the last known price to calculate trend
    $history = getPriceHistory($productId, 7);
    $lastPrice = (float)($history[count($history) - 1]['average_price'] ?? $humanAnchorPrice);
    
    // Calculate simple trend (average change)
    $trend = 0;
    if (count($history) > 1) {
        $changes = [];
        for ($i = 1; $i < count($history); $i++) {
            $changes[] = $history[$i]['average_price'] - $history[$i - 1]['average_price'];
        }
        $trend = array_sum($changes) / count($changes);
    }

    // 1. Base Prediction (Apply decayed trend)
    // We decay the trend so it flattens out (e.g. going up by 2 today, 1 tomorrow)
    $predictedPrice = $lastPrice + ($trend * 0.5);

    // 2. Jumma Effect (Thursday/Friday spike for Fish, Meat, Vegetables)
    $dayOfWeek = date('w', strtotime($todayStr)); // 0=Sun, 4=Thu, 5=Fri
    $catId = (int)$p['category_id'];
    $isJummaCategory = in_array($catId, [1, 4, 5]); // Veg, Meat, Fish
    
    if ($isJummaCategory && ($dayOfWeek == 4 || $dayOfWeek == 5)) {
        // Spike by 2-5%
        $spike = $predictedPrice * (random_int(2, 5) / 100);
        $predictedPrice += $spike;
    } elseif ($isJummaCategory && ($dayOfWeek == 0)) {
        // Drop by 2-3% on Sunday
        $drop = $predictedPrice * (random_int(2, 3) / 100);
        $predictedPrice -= $drop;
    }

    // 3. National Weather Impact
    // Fetch average rain across the country
    $weatherStmt = $db->query('SELECT AVG(rain_mm) as avg_rain FROM city_weather');
    $nationalRain = (float)$weatherStmt->fetch()['avg_rain'];
    
    if ($nationalRain > 10.0) { // Heavy national rain
        $weatherBump = $predictedPrice * (random_int(3, 8) / 100);
        $predictedPrice += $weatherBump;
        $report[] = "  [WEATHER] Heavy national rain detected, increased prediction.";
    }

    // 4. THE GUARDRAIL (Max ±15% drift from Human Anchor)
    $maxDrift = $humanAnchorPrice * 0.15;
    $ceiling = $humanAnchorPrice + $maxDrift;
    $floor = $humanAnchorPrice - $maxDrift;

    if ($predictedPrice > $ceiling) {
        $predictedPrice = $ceiling;
        $report[] = "  [GUARDRAIL] Hit +15% ceiling from human anchor ($humanAnchorPrice).";
    } elseif ($predictedPrice < $floor) {
        $predictedPrice = $floor;
        $report[] = "  [GUARDRAIL] Hit -15% floor from human anchor ($humanAnchorPrice).";
    }

    // Rounding
    // If it's a variable item (like vegetables/fish), always round to nearest 5 Taka (e.g., 60, 65)
    // If it's a flat item (like rice/sugar), we don't force it to 5.
    $pricingMode = $p['pricing_mode'] ?? 'variable';
    if ($pricingMode === 'variable') {
        $predictedPrice = round($predictedPrice / 5) * 5;
    } else {
        $predictedPrice = round($predictedPrice);
    }
    
    // Safety check
    if ($predictedPrice < 1) $predictedPrice = 1;

    // Save the new price to the database (pass isAuto = 1)
    try {
        saveDailyPrices($productId, $todayStr, $predictedPrice, 1);
        $report[] = "[UPDATED] {$p['name']} - Set to ৳{$predictedPrice} (Auto).";
        $updatedCount++;
    } catch (Exception $e) {
        $report[] = "[ERROR] {$p['name']} - Failed: " . $e->getMessage();
    }
}

$report[] = str_repeat('-', 50);
$report[] = "Summary: $updatedCount updated, $skippedCount already up-to-date.";
$report[] = str_repeat('=', 50) . "\n";

$logContent = implode("\n", $report);

// Append to log file
file_put_contents($logFile, $logContent, FILE_APPEND);

// Output to screen/console
if (!$isCli) {
    echo "<h1>Auto-Update Complete</h1>";
    echo "<pre style='background:#f4f4f4; padding:15px; border-radius:5px;'>";
    echo htmlspecialchars($logContent);
    echo "</pre>";
} else {
    echo $logContent;
}
