<?php
// =============================================
// BazarDor — API: Get All Products (Home Page Data)
// Endpoint: GET /api/products.php
// =============================================
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../includes/data.php';

$products = getAllProductsWithPrices();
$categories = getAllCategories();

// Filter by category if requested
$filterCat = $_GET['category'] ?? '';
if (!empty($filterCat) && $filterCat !== 'সব') {
    $products = array_filter($products, fn($p) => $p['category_name'] === $filterCat);
    $products = array_values($products);
}

// Search
$search = $_GET['q'] ?? '';
if (!empty($search)) {
    $q = mb_strtolower($search);
    $products = array_filter($products, function ($p) use ($q) {
        return mb_strpos(mb_strtolower($p['name']), $q) !== false
            || mb_strpos(mb_strtolower($p['name_en']), $q) !== false;
    });
    $products = array_values($products);
}

echo json_encode([
    'success' => true,
    'date' => today(),
    'categories' => $categories,
    'products' => array_map(function ($p) {
        return [
            'id' => (int)$p['id'],
            'slug' => $p['slug'],
            'name' => $p['name'],
            'name_en' => $p['name_en'],
            'category' => $p['category_name'],
            'unit' => $p['unit'],
            'icon' => SITE_URL . '/' . $p['icon_path'],
            'current_price' => $p['current_price'],
            'previous_price' => $p['previous_price'],
            'diff' => $p['diff'],
            'trend' => getDiffClass($p['diff']),
            'last_updated' => $p['latest_date'],
        ];
    }, $products)
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
