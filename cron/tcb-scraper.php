<?php
// =============================================
// BazarDor — TCB Automation Scraper
// =============================================
// This script fetches the daily TCB price sheet
// and updates the local database.
// =============================================

$secretKey = 'bazardor_tcb_2026';
$isCli = php_sapi_name() === 'cli';

if (!$isCli) {
    if (!isset($_GET['key']) || $_GET['key'] !== $secretKey) {
        http_response_code(403);
        die("Forbidden: Invalid Secret Key.");
    }
}

require_once __DIR__ . '/../includes/data.php';
require_once __DIR__ . '/../includes/SimpleXLSX.php';

use Shuchkin\SimpleXLSX;

$logFile = __DIR__ . '/tcb-scraper.log';
$report = [];

function addLog($msg) {
    global $report;
    $time = date('Y-m-d H:i:s');
    $report[] = "[$time] $msg";
}

function fetchUrl($url) {
    if (!function_exists('curl_init')) {
        return @file_get_contents($url);
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

addLog("Starting TCB Scraper...");

// 1. Fetch HTML
$htmlUrl = 'http://tcb.gov.bd/pages/daily-rmps/';
$html = fetchUrl($htmlUrl);

if (!$html) {
    addLog("ERROR: Failed to fetch HTML from TCB. (URL: $htmlUrl) Aborting scraper so existing automation can take over.");
    file_put_contents($logFile, implode("\n", $report) . "\n\n", FILE_APPEND);
    exit;
}

// 2. Extract Excel link
$matches = [];
// TCB typically links to files in object storage or their own domain ending in .xlsx
preg_match_all('/href=["\']([^"\']+\.xlsx)["\']/i', $html, $matches);

if (empty($matches[1])) {
    addLog("ERROR: No .xlsx link found on the daily page. Aborting scraper.");
    file_put_contents($logFile, implode("\n", $report) . "\n\n", FILE_APPEND);
    exit;
}

$excelUrl = $matches[1][0]; // Grab the first one (most recent)
if (strpos($excelUrl, 'http') === false) {
    $excelUrl = 'http://tcb.gov.bd' . $excelUrl;
}

addLog("Found Excel URL: $excelUrl");

// 3. Download Excel
$tmpFile = __DIR__ . '/tcb_temp_' . time() . '.xlsx';
$excelData = fetchUrl($excelUrl);
if (!$excelData) {
    addLog("ERROR: Failed to download the Excel file. (URL: $excelUrl) Aborting.");
    file_put_contents($logFile, implode("\n", $report) . "\n\n", FILE_APPEND);
    exit;
}
file_put_contents($tmpFile, $excelData);

// 4. Parse Excel
if ($xlsx = SimpleXLSX::parse($tmpFile)) {
    $rows = $xlsx->rows();
    addLog("Successfully parsed Excel file (" . count($rows) . " rows).");
} else {
    addLog("ERROR: Failed to parse Excel file: " . SimpleXLSX::parseError());
    unlink($tmpFile);
    file_put_contents($logFile, implode("\n", $report) . "\n\n", FILE_APPEND);
    exit;
}

// No alias mapping - insert raw TCB product names

function isNum($val) {
    if ($val === null || $val === '') return false;
    return is_numeric($val);
}

function looksLikeProduct($row) {
    if (!isset($row[0]) || !isset($row[1])) return false;
    $name = trim($row[0]);
    $unit = trim($row[1]);
    if ($name === '' || $unit === '') return false;
    if (strlen($name) > 100) return false;
    
    // In TCB sheets, col 2,3 are today's min/max. col 4,5 are 1 week ago min/max
    return isNum($row[2]) || isNum($row[3]) || isNum($row[4]) || isNum($row[5]);
}

$db = getDB();
$today = today();

// Load existing products
$stmt = $db->query('SELECT id, name FROM products');
$existingProducts = [];
while($p = $stmt->fetch()) {
    $existingProducts[mb_strtolower(trim($p['name']))] = $p['id'];
}

$updatedCount = 0;
$createdCount = 0;

foreach ($rows as $row) {
    if (looksLikeProduct($row)) {
        $tcbName = trim($row[0]);
        $unit = trim($row[1]);
        $minPrice = is_numeric($row[2]) ? (int)$row[2] : null;
        $maxPrice = is_numeric($row[3]) ? (int)$row[3] : null;
        
        if ($minPrice === null && $maxPrice === null) continue;
        
        if ($minPrice === null) $minPrice = $maxPrice;
        if ($maxPrice === null) $maxPrice = $minPrice;
        
        $avgPrice = round(($minPrice + $maxPrice) / 2);
        
        $searchNameLower = mb_strtolower($tcbName);
        
        if (isset($existingProducts[$searchNameLower])) {
            $productId = $existingProducts[$searchNameLower];
        } else {
            // Auto create product
            $slug = 'tcb-' . substr(md5(uniqid()), 0, 8); // Temporary slug since we don't have English name
            $stmtInsert = $db->prepare('INSERT INTO products (name, name_en, slug, unit, category_id, created_at) VALUES (?, ?, ?, ?, ?, ?)');
            // Defaulting category_id to 1 (Vegetables/General) for auto-created items
            $stmtInsert->execute([$tcbName, $tcbName, $slug, $unit, 1, date('Y-m-d H:i:s')]);
            $productId = $db->lastInsertId();
            $existingProducts[$searchNameLower] = $productId;
            $createdCount++;
            addLog("Created new product: $tcbName");
        }
        
        try {
            // isAuto = 0 so it counts as manual/TCB data (protects it from auto-update script)
            saveDailyPrices($productId, $today, $avgPrice, 0, $minPrice, $maxPrice);
            $updatedCount++;
        } catch (Exception $e) {
            addLog("Failed to save price for $searchName: " . $e->getMessage());
        }
    }
}

unlink($tmpFile);
addLog("TCB Scraper Complete! Created: $createdCount, Updated: $updatedCount.");

$logContent = implode("\n", $report) . "\n\n";
file_put_contents($logFile, $logContent, FILE_APPEND);

if (!$isCli) {
    echo "<pre>" . htmlspecialchars($logContent) . "</pre>";
} else {
    echo $logContent;
}
