<?php
require_once __DIR__ . '/includes/data.php';

header("Content-Type: application/xml; charset=utf-8");

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// Add Homepage
echo '<url>';
echo '<loc>' . SITE_URL . '/</loc>';
echo '<changefreq>daily</changefreq>';
echo '<priority>1.0</priority>';
echo '</url>';

// Add All Products
$db = getDB();
$stmt = $db->query('SELECT slug FROM products');
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($products as $p) {
    echo '<url>';
    echo '<loc>' . SITE_URL . '/product/' . htmlspecialchars($p['slug']) . '</loc>';
    echo '<changefreq>daily</changefreq>';
    echo '<priority>0.8</priority>';
    echo '</url>';
}

echo '</urlset>';
