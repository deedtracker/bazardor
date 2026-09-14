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
    
    // Explicitly exclude MS Rod (Construction Materials)
    if (strpos($name, 'রড') !== false) return false;
    
    // In TCB sheets, col 2,3 are today's min/max. col 4,5 are 1 week ago min/max
    return isNum($row[2]) || isNum($row[3]) || isNum($row[4]) || isNum($row[5]);
}

function toBanglishSlug($text) {
    $cleanText = trim($text);
    $customSlugMap = [
        'আটা সাদা (খোলা)' => 'ata-sada-khola',
        'আটা (প্যাকেট)' => 'ata-packet',
        'ময়দা (খোলা)' => 'moyda-khola',
        'ময়দা (প্যাকেট)' => 'moyda-packet',
        'সয়াবিন তেল (লুজ)' => 'soyabin-tel-loose',
        'সয়াবিন তেল (বোতল)' => 'soyabin-tel-botol',
        'পাম অয়েল (লুজ)' => 'palm-oil-loose',
        'সুপার পাম অয়েল (লুজ)' => 'super-palm-oil-loose',
        'রাইস ব্রান তেল (বোতল)' => 'rice-bran-tel-botol',
        'মশুর ডাল (বড় দানা)' => 'moshur-dal-boro-dana',
        'মশুর ডাল (মাঝারী দানা)' => 'moshur-dal-majhari-dana',
        'মশুর ডাল (ছোট দানা)' => 'moshur-dal-choto-dana',
        'মুগ ডাল (মানভেদে)' => 'moog-dal',
        'এ্যাংকর ডাল' => 'anchor-dal',
        'ছোলা (মানভেদে)' => 'chola',
        'আলু (নতুন/পুরাতন)' => 'alu-notun-puraton',
        'পিঁয়াজ (আমদানি)' => 'peyaj-amdani',
        'পিঁয়াজ (দেশী)' => 'peyaj-deshi',
        'রসুন (আমদানি)' => 'rosun-amdani',
        'রসুন (দেশী)' => 'rosun-deshi',
        'শুকনা মরিচ (দেশী)' => 'shukna-morich-deshi',
        'শুকনা মরিচ (আমদানি)' => 'shukna-morich-amdani',
        'হলুদ (দেশী)' => 'holud-deshi',
        'হলুদ (আমদানি)' => 'holud-amdani',
        'আদা (আমদানি)' => 'ada-amdani',
        'আদা (দেশী)' => 'ada-deshi',
        'জিরা' => 'jeera',
        'দারুচিনি' => 'daruchini',
        'লবঙ্গ' => 'lobongo',
        'এলাচ(ছোট)' => 'elach-choto',
        'ধনে' => 'dhone',
        'তেজপাতা' => 'tejpata',
        'ইলিশ' => 'ilish',
        'ব্রয়লার মুরগি' => 'broiler-murgi',
        'মুরগী(ব্রয়লার)' => 'broiler-murgi',
        'মুরগী (দেশী)' => 'deshi-murgi',
        'রুই' => 'rui-mach',
        'গরু' => 'gorur-mangsho',
        'খাসী' => 'khashir-mangsho',
        'ডানো' => 'dano',
        'ডিপ্লোমা (নিউজিল্যান্ড)' => 'diploma',
        'ফ্রেশ' => 'fresh',
        'মার্কস' => 'marks',
        'চিনি' => 'chini',
        'খেজুর(সাধারণ মানের)' => 'khejur',
        'লবণ' => 'lobon',
        'লবণ(প্যাঃ)আয়োডিনযুক্ত' => 'lobon-packet',
        'কাঁচামরিচ' => 'kacha-morich',
        'ডিম (ফার্ম)' => 'dim-farm',
        'লেখার কাগজ(সাদা)' => 'kagoj-sada',
        'এম,এস রড (৬০ গ্রেড)' => 'ms-rod-60-grade',
        'এম,এস রড( ৪০ গ্রেড)' => 'ms-rod-40-grade',
        'চাল সুগন্ধী (পোলাও)' => 'chal-polao',
        'চাল (সরু/নাজিরশাইল)' => 'chal-najirshail',
        'চাল সরু (নাজির/মিনিকেট)' => 'chal-najirshail',
        'চাল (মাঝারি/পাইজাম)' => 'chal-paijam',
        'চাল (মাঝারী)পাইজাম/আটাশ' => 'chal-paijam',
        'চাল (মোটা)/স্বর্ণা/চায়না ইরি' => 'chal-mota',
    ];
    
    if (isset($customSlugMap[$cleanText])) {
        return $customSlugMap[$cleanText];
    }
    
    $map = [
        'ক' => 'k', 'খ' => 'kh', 'গ' => 'g', 'ঘ' => 'gh', 'ঙ' => 'ng',
        'চ' => 'ch', 'ছ' => 'chh', 'জ' => 'j', 'ঝ' => 'jh', 'ঞ' => 'n',
        'ট' => 't', 'ঠ' => 'th', 'ড' => 'd', 'ঢ' => 'dh', 'ণ' => 'n',
        'ত' => 't', 'থ' => 'th', 'দ' => 'd', 'ধ' => 'dh', 'ন' => 'n',
        'প' => 'p', 'ফ' => 'f', 'ব' => 'b', 'ভ' => 'v', 'ম' => 'm',
        'য' => 'j', 'র' => 'r', 'ল' => 'l', 'শ' => 'sh', 'ষ' => 'sh', 'স' => 's', 'হ' => 'h',
        'ড়' => 'r', 'ঢ়' => 'rh', 'য়' => 'y', 'ৎ' => 't', 'ং' => 'ng', 'ঃ' => 'h', 'ঁ' => '',
        'অ' => 'o', 'আ' => 'a', 'ই' => 'i', 'ঈ' => 'e', 'উ' => 'u', 'ঊ' => 'u', 'ঋ' => 'ri', 'এ' => 'e', 'ঐ' => 'oi', 'ও' => 'o', 'ঔ' => 'ou',
        'া' => 'a', 'ি' => 'i', 'ী' => 'i', 'ু' => 'u', 'ূ' => 'u', 'ৃ' => 'ri', 'ে' => 'e', 'ৈ' => 'oi', 'ো' => 'o', 'ৌ' => 'ou',
        '্' => ''
    ];
    $text = strtr($text, $map);
    $text = preg_replace('/[^a-zA-Z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return strtolower(trim($text, '-'));
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
            // Auto create product with SEO friendly slug
            $baseSlug = toBanglishSlug($tcbName);
            $slug = "ajke-" . $baseSlug . "-er-dam";
            
            // Basic duplicate prevention (rare but possible)
            $check = $db->prepare('SELECT id FROM products WHERE slug = ?');
            $check->execute([$slug]);
            if ($check->fetch()) {
                $slug .= "-" . rand(100, 999);
            }
            
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
