<?php
require_once __DIR__ . '/includes/data.php';

header("Content-Type: application/xml; charset=utf-8");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// Get the global latest update date for the homepage
$db = getDB();
$homeStmt = $db->query('SELECT MAX(price_date) as latest FROM daily_prices');
$homeLatest = $homeStmt->fetchColumn();
$homeDate = $homeLatest ? $homeLatest : date('Y-m-d');

// Add Homepage
echo '<url>';
echo '<loc>' . SITE_URL . '/</loc>';
echo '<lastmod>' . $homeDate . '</lastmod>';
echo '<changefreq>daily</changefreq>';
echo '<priority>1.0</priority>';
echo '</url>';

// Add All Products with their specific last modified date
$stmt = $db->query("
    SELECT p.slug, MAX(dp.price_date) as last_update
    FROM products p
    LEFT JOIN daily_prices dp ON p.id = dp.product_id
    GROUP BY p.id
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($products as $p) {
    $lastMod = $p['last_update'] ? $p['last_update'] : date('Y-m-d');
    echo '<url>';
    echo '<loc>' . SITE_URL . '/product/' . htmlspecialchars($p['slug']) . '</loc>';
    echo '<lastmod>' . $lastMod . '</lastmod>';
    echo '<changefreq>daily</changefreq>';
    echo '<priority>0.8</priority>';
    echo '</url>';
}

echo '</urlset>';
