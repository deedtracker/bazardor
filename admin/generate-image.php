<?php
// =============================================
// BazarDor — Admin Image Generator
// =============================================
session_start();
require_once __DIR__ . '/../includes/data.php';

// Auth check
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

// Handle AJAX Request for Product Data
if (isset($_GET['action']) && $_GET['action'] === 'get_product_data') {
    header('Content-Type: application/json');
    $productId = (int)$_GET['id'];
    
    $db = getDB();
    $stmt = $db->prepare('SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?');
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    
    if (!$product) {
        echo json_encode(['error' => 'Product not found']);
        exit;
    }
    
    $latestPrice = getLatestPrice($product['id']);
    $previousPrice = $latestPrice ? getPreviousPrice($product['id'], $latestPrice['price_date']) : null;
    
    $currentPrice = $latestPrice ? (float)$latestPrice['average_price'] : 0;
    $prevPrice = $previousPrice ? (float)$previousPrice['average_price'] : $currentPrice;
    $diff = $currentPrice - $prevPrice;
    
    $cityPrices = $latestPrice ? getCityPrices($latestPrice['id']) : [];
    
    // Pick 4 cities (Cheapest, Expensive, and 2 others)
    $selectedCities = [];
    if (!empty($cityPrices)) {
        usort($cityPrices, function($a, $b) { return $a['price'] <=> $b['price']; });
        $cheapest = $cityPrices[0];
        $expensive = $cityPrices[count($cityPrices) - 1];
        
        $middleCities = array_slice($cityPrices, 1, -1);
        shuffle($middleCities);
        
        $selectedCities[] = $cheapest;
        $selectedCities[] = $expensive;
        if (isset($middleCities[0])) $selectedCities[] = $middleCities[0];
        if (isset($middleCities[1])) $selectedCities[] = $middleCities[1];
    }
    
    // Format response
    $response = [
        'name' => $product['name'],
        'icon' => '../' . $product['icon_path'],
        'current_price_bn' => toBengali(round($currentPrice)),
        'diff' => $diff,
        'diff_abs_bn' => toBengali(abs(round($diff))),
        'trend' => $diff > 0 ? 'up' : ($diff < 0 ? 'down' : 'same'),
        'date_bn' => toBengaliDate($latestPrice ? $latestPrice['price_date'] : today()),
        'markets' => []
    ];
    
    foreach ($selectedCities as $c) {
        $response['markets'][] = [
            'name' => $c['city_name'],
            'price_bn' => toBengali(round($c['price']))
        ];
    }
    
    // Fill if less than 4 markets
    while (count($response['markets']) < 4) {
        $response['markets'][] = ['name' => 'অজানা', 'price_bn' => '০'];
    }
    
    echo json_encode($response);
    exit;
}

$products = getAllProducts();
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ডিজাইন জেনারেট | <?= e(SITE_NAME) ?> অ্যাডমিন</title>
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <script src="../assets/html2canvas.min.js"></script>
    
    <!-- Image Generator Styles (Scoped) -->
    <style>
        @font-face {
            font-family: 'Hind Siliguri';
            font-style: normal;
            font-weight: 300;
            font-display: swap;
            src: url('../assets/HindSiliguri-400.ttf') format('truetype');
        }
        @font-face {
            font-family: 'Hind Siliguri';
            font-style: normal;
            font-weight: 400;
            font-display: swap;
            src: url('../assets/HindSiliguri-400.ttf') format('truetype');
        }
        @font-face {
            font-family: 'Hind Siliguri';
            font-style: normal;
            font-weight: 500;
            font-display: swap;
            src: url('../assets/HindSiliguri-500.ttf') format('truetype');
        }
        @font-face {
            font-family: 'Hind Siliguri';
            font-style: normal;
            font-weight: 600;
            font-display: swap;
            src: url('../assets/HindSiliguri-600.ttf') format('truetype');
        }
        @font-face {
            font-family: 'Hind Siliguri';
            font-style: normal;
            font-weight: 700;
            font-display: swap;
            src: url('../assets/HindSiliguri-700.ttf') format('truetype');
        }
        @font-face {
            font-family: 'Anek Bangla';
            font-style: normal;
            font-weight: 700;
            font-display: swap;
            src: url('../assets/anek-bangla-700.woff2') format('woff2');
        }

        .generator-wrapper {
            font-family: 'Hind Siliguri', sans-serif;
            background-color: #d6d6d6;
            padding: 30px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .layout {
            display: flex;
            flex-wrap: wrap;
            gap: 50px;
            justify-content: center;
        }

        .module {
            display: flex;
            flex-direction: column;
            gap: 20px;
            align-items: center;
        }
        
        .controls {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 100%;
            max-width: 640px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .controls select, .controls button {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            font-family: inherit;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .controls button {
            background: #1a7a35;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border: none;
        }
        .controls button:hover {
            background: #145c27;
        }

        /* ===== PREVIEW COLUMN ===== */
        .preview-column {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        /* ===== CARD DESIGN ===== */
        .card {
            width: 640px;
            height: 640px;
            background: #FAFAF8;
            border-radius: 6px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.12);
            display: flex;
            flex-direction: column;
        }
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 200px;
            height: 200px;
            background-image: radial-gradient(rgba(0, 0, 0, 0.06) 1.5px, transparent 1.5px);
            background-size: 14px 14px;
            pointer-events: none;
            z-index: 0;
        }
        .card-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            position: relative;
            z-index: 2;
        }
        .deco-arcs {
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            overflow: hidden;
            z-index: 1;
            pointer-events: none;
        }
        .deco-arcs::before {
            content: '';
            position: absolute;
            top: -80px;
            right: -80px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            border: 12px solid rgba(26, 122, 53, 0.15);
        }
        .deco-arcs::after {
            content: '';
            position: absolute;
            top: 10px;
            right: -100px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            border: 8px solid rgba(26, 122, 53, 0.08);
        }
        .header {
            padding: 20px 30px 6px 30px;
            text-align: center;
        }
        .main-title {
            font-family: 'Anek Bangla', sans-serif;
            font-size: 48px;
            font-weight: 700;
            line-height: 1.15;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
            text-align: center;
            margin-top: 0;
        }
        .main-title .dark { color: #1c1c1c; }
        .main-title .green { color: #1a7a35; }
        .main-title .location-prefix { font-size: inherit; margin-right: 8px; }
        .date-line {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 12px;
            font-size: 15px;
            font-weight: 600;
            color: #666;
        }
        .date-line .dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background-color: #1a7a35;
            flex-shrink: 0;
        }
        .date-line .dot-line {
            width: 36px;
            height: 0;
            border-top: 2px dotted #ccc;
        }
        .date-line svg { width: 22px; height: 22px; flex-shrink: 0; }
        .price-rows {
            padding: 0 40px;
            display: flex;
            flex-direction: column;
            flex: 1;
            justify-content: center;
        }
        .price-row {
            display: flex;
            align-items: center;
            background: #ffffff;
            border-radius: 14px;
            padding: 12px 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0, 0, 0, 0.04);
            margin-bottom: 10px;
        }
        .price-row:last-child { margin-bottom: 0; }
        .price-row .icon-box {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: transparent;
            color: #1a7a35;
            margin-right: 16px;
        }
        .price-row .icon-box svg { width: 100%; height: 100%; }
        .price-row .market-name {
            flex: 1;
            font-size: 25px;
            font-weight: 600;
            color: #2a2a2a;
            line-height: 1.3;
        }
        .price-row .price {
            display: flex;
            align-items: baseline;
            flex-shrink: 0;
            width: 120px;
            padding-left: 24px;
            border-left: 2px solid rgba(0, 0, 0, 0.06);
        }
        .price-row .price .amount {
            margin-right: 8px;
            font-size: 48px;
            font-weight: 700;
            color: #1a7a35;
            line-height: 1;
        }
        .price-row .price .currency { font-size: 22px; font-weight: 600; color: #666; }
        .price-change {
            margin: 12px 40px 22px 40px;
            background: #F1F6F3;
            border: 1.5px solid #C8DBCF;
            border-radius: 12px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            position: relative;
            overflow: visible;
        }
        .price-change .trend-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: #ffffff;
            border-radius: 50%;
            margin-right: 18px;
        }
        .price-change .trend-icon svg { width: 32px; height: 32px; }
        .price-change .change-text {
            flex: 1;
            border-left: 1.5px solid #C8DBCF;
            padding-left: 18px;
        }
        .price-change .change-text .subtitle {
            font-size: 16px;
            font-weight: 500;
            color: #555;
            line-height: 1.3;
        }
        .price-change .change-text .amount-change {
            font-size: 30px;
            font-weight: 700;
            color: #1a7a35;
            line-height: 1.2;
        }
        .price-change .onion-img {
            position: absolute;
            right: -10px;
            bottom: -5px;
            width: 165px;
            height: 165px;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }
        .price-change .onion-img img { width: 100%; height: 100%; object-fit: contain; }
        .trend-up-icon { display: block; }
        .trend-down-icon { display: none; }
        .price-change.decreased .trend-up-icon { display: none; }
        .price-change.decreased .trend-down-icon { display: block; }

        /* ===== TREND MODULE CSS ===== */
        .trend-card-wrapper {
            background: #f8faf9;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            width: 640px;
            height: 640px;
            overflow: hidden;
            position: relative;
            display: flex;
            flex-direction: column;
        }
        .trend-card-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 200px;
            height: 200px;
            background-image: radial-gradient(rgba(0, 0, 0, 0.06) 1.5px, transparent 1.5px);
            background-size: 14px 14px;
            pointer-events: none;
            z-index: 0;
        }
        .trend-header {
            padding: 30px 40px 10px 40px;
            text-align: center;
        }
        .trend-header-title {
            font-family: 'Anek Bangla', sans-serif;
            font-size: 48px;
            font-weight: 700;
            color: #1c1c1c;
            line-height: 1.2;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
            margin-top: 0;
        }
        .trend-rows {
            flex: 1;
            padding: 10px 40px 30px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 12px;
            position: relative;
            z-index: 2;
        }
        .trend-row {
            display: flex;
            align-items: center;
            background: #ffffff;
            border-radius: 14px;
            padding: 14px 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0, 0, 0, 0.04);
        }
        .trend-icon-box {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-right: 16px;
        }
        .trend-icon-box img { width: 100%; height: 100%; object-fit: contain; }
        .trend-name {
            flex: 1;
            font-size: 26px;
            font-weight: 600;
            color: #2a2a2a;
        }
        .trend-price {
            font-size: 32px;
            font-weight: 700;
            color: #1c1c1c;
            margin-right: 20px;
            text-align: right;
            min-width: 80px;
        }
        .trend-price span { font-size: 18px; color: #888; font-weight: 500; }
        .trend-badge {
            display: flex;
            align-items: center;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 18px;
            font-weight: 700;
            min-width: 110px;
            justify-content: center;
            gap: 6px;
        }
        .trend-badge.up { background: rgba(211, 47, 47, 0.1); color: #d32f2f; }
        .trend-badge.up svg { fill: #d32f2f; width: 16px; height: 16px; }
        .trend-badge.down { background: rgba(26, 122, 53, 0.1); color: #1a7a35; }
        .trend-badge.down svg { fill: #1a7a35; width: 16px; height: 16px; }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <header class="admin-header">
            <div class="admin-header-inner">
                <div class="admin-header-left">
                    <span class="admin-logo-icon">🛒</span>
                    <h1><?= e(SITE_NAME) ?> <span class="admin-badge">অ্যাডমিন</span></h1>
                </div>
                <div class="admin-header-right">
                    <a href="dashboard.php" class="admin-btn outline small">ড্যাশবোর্ড</a>
                    <span class="admin-user"><?= e($_SESSION['admin_user']) ?></span>
                </div>
            </div>
        </header>

        <main class="admin-main">
            <div class="admin-section">
                <h2 class="admin-section-title">ডিজাইন জেনারেট</h2>
                
                <div class="generator-wrapper">
                    <div class="layout">
                        
                        <!-- MODULE 1: PRICE CARD -->
                        <div class="module">
                            <div class="controls">
                                <h3>Product Image Generator</h3>
                                <select id="product-select">
                                    <option value="">পণ্য নির্বাচন করুন...</option>
                                    <?php foreach ($products as $p): ?>
                                        <option value="<?= $p['id'] ?>"><?= e($p['name']) ?> (<?= e($p['category_name']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                                <button onclick="downloadImage('price-card', 'product_price.png')">Download Product Image</button>
                            </div>
                            
                            <div class="preview-column">
                                <div class="card" id="price-card">
                                    <div class="deco-arcs"></div>
                                    <div class="card-content">
                                        <div class="header">
                                            <h1 class="main-title">
                                                <span class="dark location-prefix">আজ</span><span class="green" id="display-product">পেয়াজের</span> <span class="dark">দাম</span>
                                            </h1>
                                            <div class="date-line">
                                                <div class="dot-line"></div><div class="dot"></div>
                                                <svg viewBox="0 0 24 24">
                                                    <rect x="2" y="3" width="20" height="18" rx="4" fill="#1a7a35" />
                                                    <path d="M7 2v3M17 2v3M2 8h20M7 13h2v2H7zm5 0h2v2h-2zm5 0h2v2h-2z" stroke="white" stroke-width="2" fill="none" stroke-linecap="round" />
                                                </svg>
                                                <span id="display-date">১২ সেপ্টেম্বর ২০২৬</span>
                                                <div class="dot"></div><div class="dot-line"></div>
                                            </div>
                                        </div>

                                        <div class="price-rows" id="price-rows">
                                            <!-- Rows populated by JS -->
                                        </div>

                                        <div class="price-change" id="price-change">
                                            <div class="trend-icon">
                                                <svg class="trend-up-icon" viewBox="0 0 50 50" fill="none">
                                                    <path d="M14 34L21 25L27 30L38 17" stroke="#d32f2f" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M31 17H38V24" stroke="#d32f2f" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <svg class="trend-down-icon" viewBox="0 0 50 50" fill="none">
                                                    <path d="M14 17L21 26L27 21L38 34" stroke="#1a7a35" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M31 34H38V27" stroke="#1a7a35" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </div>
                                            <div class="change-text">
                                                <div class="subtitle">গতকাল থেকে আজকে</div>
                                                <div class="amount-change" id="display-change">৪ টাকা বেড়েছে</div>
                                            </div>
                                            <div class="onion-img">
                                                <img id="display-product-icon" src="../assets/onion.png" alt="Product Image">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- MODULE 4: TREND CARD -->
                        <div class="module">
                            <div class="controls">
                                <h3>Trend Image Generator</h3>
                                <div style="display:flex; gap: 10px; margin-bottom: 10px;">
                                    <select id="trend-select-1" style="margin-bottom:0;" onchange="updateTrendRow(1)">
                                        <option value="">পণ্য ১...</option>
                                        <?php foreach ($products as $p): ?><option value="<?= $p['id'] ?>"><?= e($p['name']) ?></option><?php endforeach; ?>
                                    </select>
                                    <select id="trend-select-2" style="margin-bottom:0;" onchange="updateTrendRow(2)">
                                        <option value="">পণ্য ২...</option>
                                        <?php foreach ($products as $p): ?><option value="<?= $p['id'] ?>"><?= e($p['name']) ?></option><?php endforeach; ?>
                                    </select>
                                </div>
                                <div style="display:flex; gap: 10px; margin-bottom: 10px;">
                                    <select id="trend-select-3" style="margin-bottom:0;" onchange="updateTrendRow(3)">
                                        <option value="">পণ্য ৩...</option>
                                        <?php foreach ($products as $p): ?><option value="<?= $p['id'] ?>"><?= e($p['name']) ?></option><?php endforeach; ?>
                                    </select>
                                    <select id="trend-select-4" style="margin-bottom:0;" onchange="updateTrendRow(4)">
                                        <option value="">পণ্য ৪...</option>
                                        <?php foreach ($products as $p): ?><option value="<?= $p['id'] ?>"><?= e($p['name']) ?></option><?php endforeach; ?>
                                    </select>
                                </div>
                                <button onclick="downloadImage('trend-card', 'market_trends.png')">Download Trend Image</button>
                            </div>
                            
                            <div class="preview-column">
                                <div class="trend-card-wrapper" id="trend-card">
                                    <div class="deco-arcs"></div>
                                    <div class="trend-header">
                                        <div class="trend-header-title">আজকের বাজারদর আপডেট</div>
                                        <div class="date-line" style="justify-content:center; margin-top:5px; display:flex; align-items:center;">
                                            <div class="dot-line" style="width:30px; border-top:2px dotted #ccc;"></div>
                                            <div class="dot" style="width:4px; height:4px; border-radius:50%; background:#1a7a35; margin:0 5px;"></div>
                                            <span id="trend-display-date" style="font-size:15px; color:#555; font-weight:600;"><?= toBengaliDate(today()) ?></span>
                                            <div class="dot" style="width:4px; height:4px; border-radius:50%; background:#1a7a35; margin:0 5px;"></div>
                                            <div class="dot-line" style="width:30px; border-top:2px dotted #ccc;"></div>
                                        </div>
                                    </div>

                                    <div class="trend-rows" id="trend-display-rows">
                                        <!-- Placeholder Rows -->
                                        <div class="trend-row" id="trend-row-1" style="opacity:0.5"><div class="trend-name">পণ্য ১ নির্বাচন করুন</div></div>
                                        <div class="trend-row" id="trend-row-2" style="opacity:0.5"><div class="trend-name">পণ্য ২ নির্বাচন করুন</div></div>
                                        <div class="trend-row" id="trend-row-3" style="opacity:0.5"><div class="trend-name">পণ্য ৩ নির্বাচন করুন</div></div>
                                        <div class="trend-row" id="trend-row-4" style="opacity:0.5"><div class="trend-name">পণ্য ৪ নির্বাচন করুন</div></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                
            </div>
        </main>
    </div>

    <script>
        const SVGS = [
            `<svg viewBox="0 0 48 48" fill="none"><circle cx="24" cy="16" r="3" fill="currentColor"/><path d="M24 19V38" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/><path d="M24 38L17 44" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M24 38L31 44" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M19 32L29 32" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M16 10C18 12 18 15 16 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M11 7C14.5 10.5 14.5 17 11 21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M32 10C30 12 30 15 32 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M37 7C33.5 10.5 33.5 17 37 21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>`,
            `<svg viewBox="0 0 48 48" fill="none"><path d="M8 12H40V20C40 20 37 24 34 20C31 24 28 20 28 20C28 20 25 24 22 20C19 24 16 20 16 20C16 24 8 20 8 20V12Z" fill="currentColor" opacity="0.3"/><rect x="8" y="10" width="32" height="3" rx="1" fill="currentColor"/><rect x="10" y="20" width="28" height="20" fill="currentColor" opacity="0.15"/><path d="M10 20H38V40H10V20Z" stroke="currentColor" stroke-width="2"/><rect x="19" y="28" width="10" height="12" rx="1" fill="currentColor" opacity="0.3"/><rect x="19" y="28" width="10" height="12" rx="1" stroke="currentColor" stroke-width="1.5"/><rect x="13" y="23" width="6" height="5" rx="0.5" fill="currentColor" opacity="0.2"/><rect x="13" y="23" width="6" height="5" rx="0.5" stroke="currentColor" stroke-width="1.2"/><rect x="29" y="23" width="6" height="5" rx="0.5" fill="currentColor" opacity="0.2"/><rect x="29" y="23" width="6" height="5" rx="0.5" stroke="currentColor" stroke-width="1.2"/><path d="M8 13C8 13 11 18 16 13C21 18 22 18 24 13C26 18 27 18 32 13C37 18 40 13 40 13" stroke="currentColor" stroke-width="1.5" fill="none"/></svg>`,
            `<svg viewBox="0 0 48 48" fill="none"><rect x="6" y="12" width="36" height="22" rx="4" fill="currentColor" opacity="0.2"/><rect x="6" y="12" width="36" height="22" rx="4" stroke="currentColor" stroke-width="2"/><rect x="10" y="16" width="8" height="8" rx="1.5" fill="currentColor" opacity="0.15"/><rect x="10" y="16" width="8" height="8" rx="1.5" stroke="currentColor" stroke-width="1.3"/><rect x="20" y="16" width="8" height="8" rx="1.5" fill="currentColor" opacity="0.15"/><rect x="20" y="16" width="8" height="8" rx="1.5" stroke="currentColor" stroke-width="1.3"/><rect x="30" y="16" width="8" height="8" rx="1.5" fill="currentColor" opacity="0.15"/><rect x="30" y="16" width="8" height="8" rx="1.5" stroke="currentColor" stroke-width="1.3"/><line x1="6" y1="30" x2="42" y2="30" stroke="currentColor" stroke-width="1.5"/><circle cx="15" cy="36" r="3.5" fill="currentColor" opacity="0.3"/><circle cx="15" cy="36" r="3.5" stroke="currentColor" stroke-width="1.8"/><circle cx="15" cy="36" r="1.2" fill="currentColor"/><circle cx="33" cy="36" r="3.5" fill="currentColor" opacity="0.3"/><circle cx="33" cy="36" r="3.5" stroke="currentColor" stroke-width="1.8"/><circle cx="33" cy="36" r="1.2" fill="currentColor"/><rect x="38" y="27" width="4" height="3" rx="1" fill="currentColor" opacity="0.4"/></svg>`,
            `<svg viewBox="0 0 48 48" fill="none"><path d="M8 12H40V20C40 20 37 24 34 20C31 24 28 20 28 20C28 20 25 24 22 20C19 24 16 20 16 20C16 24 8 20 8 20V12Z" fill="currentColor" opacity="0.3"/><rect x="8" y="10" width="32" height="3" rx="1" fill="currentColor"/><rect x="10" y="20" width="28" height="20" fill="currentColor" opacity="0.15"/><path d="M10 20H38V40H10V20Z" stroke="currentColor" stroke-width="2"/><rect x="19" y="28" width="10" height="12" rx="1" fill="currentColor" opacity="0.3"/><rect x="19" y="28" width="10" height="12" rx="1" stroke="currentColor" stroke-width="1.5"/><rect x="13" y="23" width="6" height="5" rx="0.5" fill="currentColor" opacity="0.2"/><rect x="13" y="23" width="6" height="5" rx="0.5" stroke="currentColor" stroke-width="1.2"/><rect x="29" y="23" width="6" height="5" rx="0.5" fill="currentColor" opacity="0.2"/><rect x="29" y="23" width="6" height="5" rx="0.5" stroke="currentColor" stroke-width="1.2"/><path d="M8 13C8 13 11 18 16 13C21 18 22 18 24 13C26 18 27 18 32 13C37 18 40 13 40 13" stroke="currentColor" stroke-width="1.5" fill="none"/></svg>`
        ];

        function getBengaliPossessive(word) {
            const lastChar = word.slice(-1);
            const vowelSigns = ['া', 'ি', 'ী', 'ু', 'ূ', 'ৃ', 'ে', 'ৈ', 'ো', 'ৌ'];
            if (vowelSigns.includes(lastChar)) {
                return word + 'র';
            }
            return word + 'ের';
        }

        document.getElementById('product-select').addEventListener('change', async (e) => {
            const id = e.target.value;
            if(!id) return;
            
            const res = await fetch(`generate-image.php?action=get_product_data&id=${id}`);
            const data = await res.json();
            
            if(data.error) {
                alert(data.error);
                return;
            }
            
            // Update Card 1
            document.getElementById('display-product').textContent = getBengaliPossessive(data.name);
            document.getElementById('display-date').textContent = data.date_bn;
            document.getElementById('display-product-icon').src = data.icon;
            
            // Render Markets
            const rowsContainer = document.getElementById('price-rows');
            rowsContainer.innerHTML = '';
            
            data.markets.forEach((m, i) => {
                const icon = SVGS[i % SVGS.length];
                rowsContainer.innerHTML += `
                    <div class="price-row">
                        <div class="icon-box">${icon}</div>
                        <div class="market-name">${m.name}</div>
                        <div class="price">
                            <span class="amount">${m.price_bn}</span>
                            <span class="currency">টাকা</span>
                        </div>
                    </div>
                `;
            });
            
            // Render Change
            const changeEl = document.getElementById('price-change');
            changeEl.className = 'price-change ' + (data.trend === 'down' ? 'decreased' : '');
            
            let changeText = 'অপরিবর্তিত';
            if (data.trend === 'up') changeText = `${data.diff_abs_bn} টাকা বেড়েছে`;
            else if (data.trend === 'down') changeText = `${data.diff_abs_bn} টাকা কমেছে`;
            
            document.getElementById('display-change').textContent = changeText;
        });

        async function updateTrendRow(index) {
            const select = document.getElementById(`trend-select-${index}`);
            const row = document.getElementById(`trend-row-${index}`);
            const id = select.value;
            
            if(!id) {
                row.style.opacity = 0.5;
                row.innerHTML = `<div class="trend-name">পণ্য ${index} নির্বাচন করুন</div>`;
                return;
            }
            
            const res = await fetch(`generate-image.php?action=get_product_data&id=${id}`);
            const data = await res.json();
            
            row.style.opacity = 1;
            
            let badgeHtml = '';
            if (data.trend === 'up') {
                badgeHtml = `<div class="trend-badge up"><svg viewBox="0 0 24 24"><path d="M4 12l1.41 1.41L11 7.83V20h2V7.83l5.58 5.59L20 12l-8-8-8 8z" /></svg> ${data.diff_abs_bn} ৳</div>`;
            } else if (data.trend === 'down') {
                badgeHtml = `<div class="trend-badge down"><svg viewBox="0 0 24 24"><path d="M20 12l-1.41-1.41L13 16.17V4h-2v12.17l-5.58-5.59L4 12l8 8 8-8z" /></svg> ${data.diff_abs_bn} ৳</div>`;
            } else {
                badgeHtml = `<div class="trend-badge" style="background: rgba(0,0,0,0.04); color:#666;">অপরিবর্তিত</div>`;
            }

            row.innerHTML = `
                <div class="trend-icon-box"><img src="${data.icon}" alt="icon"></div>
                <div class="trend-name">${data.name}</div>
                <div class="trend-price">${data.current_price_bn} <span>টাকা</span></div>
                ${badgeHtml}
            `;
        }

        function downloadImage(elementId, filename) {
            const element = document.getElementById(elementId);
            if (!element) return;
            
            html2canvas(element, {
                scale: 2, // Higher resolution
                useCORS: true,
                backgroundColor: '#FAFAF8'
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = filename;
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        }
    </script>
</body>
</html>
