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

    // Product needs an update. Fetch history to base our prediction on.
    $history = getPriceHistory($productId, 7);
    if (empty($history)) {
        $report[] = "[SKIPPED] {$p['name']} - No historical data available.";
        continue;
    }

    // Calculate predictions using our backend engine
    $predictions = calculatePredictions($history, $p['unit']);
    $predictedPrice = null;

    foreach ($predictions as $pred) {
        if ($pred['date'] === $todayStr) {
            $predictedPrice = $pred['price'];
            break;
        }
    }

    // Fallback if the gap is too large (e.g., missed 5 days)
    if ($predictedPrice === null) {
        $predictedPrice = $history[count($history) - 1]['average_price'];
    }

    // Save the new price to the database (this also generates city prices!)
    try {
        saveDailyPrices($productId, $todayStr, $predictedPrice);
        $report[] = "[UPDATED] {$p['name']} - Set to ৳{$predictedPrice} based on AI prediction.";
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
