<?php
require_once __DIR__ . '/includes/data.php';

$db = getDB();
$stmt = $db->query("SELECT COUNT(*) as count FROM city_prices");
$count = $stmt->fetch()['count'];

$dailyPrices = $db->query("
    SELECT dp.* 
    FROM daily_prices dp
    LEFT JOIN city_prices cp ON dp.id = cp.daily_price_id
    WHERE cp.id IS NULL
")->fetchAll();

echo "Found " . count($dailyPrices) . " daily_prices records missing city prices.\n";

$generated = 0;
foreach ($dailyPrices as $dp) {
    saveDailyPrices($dp['product_id'], $dp['price_date'], $dp['average_price']);
    $generated++;
}

echo "Successfully generated city prices for {$generated} missing daily prices!\n";
