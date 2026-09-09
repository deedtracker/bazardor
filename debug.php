<?php
require_once __DIR__ . '/includes/data.php';
$db = getDB();
$stmt = $db->query('SELECT COUNT(*) as cnt FROM city_prices');
echo "city_prices count: " . $stmt->fetch()['cnt'] . "\n";
$stmt = $db->query('SELECT COUNT(*) as cnt FROM cities');
echo "cities count: " . $stmt->fetch()['cnt'] . "\n";
$stmt = $db->query('SELECT COUNT(*) as cnt FROM daily_prices');
echo "daily_prices count: " . $stmt->fetch()['cnt'] . "\n";
