<?php
// =============================================
// BazarDor — API: Get Single Product Detail
// Endpoint: GET /api/product.php?slug=onion
// =============================================
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../includes/data.php';

$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'slug parameter is required'], JSON_UNESCAPED_UNICODE);
    exit;
}

$detail = getProductDetail($slug);

if (!$detail) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Product not found'], JSON_UNESCAPED_UNICODE);
    exit;
}

$product = $detail['product'];

echo json_encode([
    'success' => true,
    'product' => [
        'id' => (int)$product['id'],
        'slug' => $product['slug'],
        'name' => $product['name'],
        'name_en' => $product['name_en'],
        'category' => $product['category_name'],
        'unit' => $product['unit'],
        'icon' => SITE_URL . '/' . $product['icon_path'],
    ],
    'pricing' => [
        'current_price' => $detail['currentPrice'],
        'previous_price' => $detail['previousPrice'],
        'diff' => $detail['diff'],
        'trend' => $detail['diffClass'],
        'last_updated' => $detail['latestDate'],
    ],
    'city_prices' => array_map(function ($c) {
        return [
            'city' => $c['city_name'],
            'price' => (float)$c['price'],
        ];
    }, $detail['cityPrices']),
    'cheapest_city' => $detail['cheapest'],
    'expensive_city' => $detail['expensive'],
    'history' => array_map(function ($h) {
        return [
            'date' => $h['price_date'],
            'price' => (float)$h['average_price'],
        ];
    }, $detail['history']),
    'predictions' => array_map(function ($p) {
        return [
            'date' => $p['date'],
            'price' => $p['price'],
            'confidence' => $p['confidence'],
        ];
    }, $detail['predictions']),
    'news' => array_map(function ($n) {
        return [
            'title' => $n['title'],
            'source' => $n['source'],
            'url' => $n['url'],
            'date' => $n['date_added'],
        ];
    }, $detail['news']),
    'summary' => strip_tags($detail['summary']),
    'trend_summary' => strip_tags($detail['trendSummary']),
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
