<?php
// =============================================
// BazarDor — Fix TCB SEO Slugs
// =============================================
// Run this file in your browser to convert all 
// 'tcb-xxxx' slugs into SEO friendly 'ajke-X-er-dam'
// =============================================

require_once __DIR__ . '/includes/data.php';

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
    
    // Basic fallback transliteration
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
echo "<h1>Fixing TCB Slugs</h1>";

$stmt = $db->query("SELECT id, name FROM products");
$products = $stmt->fetchAll();

if (count($products) === 0) {
    echo "<p>No products found in the database!</p>";
} else {
    $updateStmt = $db->prepare("UPDATE products SET slug = ? WHERE id = ?");
    $count = 0;
    foreach ($products as $p) {
        $baseSlug = toBanglishSlug($p['name']);
        
        // Follow the pattern "ajke-X-er-dam"
        // E.g., ajke-alu-er-dam
        $finalSlug = "ajke-" . $baseSlug . "-er-dam";
        
        // Ensure no duplicates
        $checkStmt = $db->prepare("SELECT id FROM products WHERE slug = ? AND id != ?");
        $checkStmt->execute([$finalSlug, $p['id']]);
        if ($checkStmt->fetch()) {
            $finalSlug .= "-" . $p['id']; // append ID to make it unique if collision
        }
        
        $updateStmt->execute([$finalSlug, $p['id']]);
        echo "<p>Updated: <b>{$p['name']}</b> -> <code>$finalSlug</code></p>";
        $count++;
    }
    echo "<h2 style='color:green;'>Successfully fixed $count slugs!</h2>";
}
echo "<p><b>Important:</b> Please delete this <code>update-tcb-slugs.php</code> file from your server after you are done.</p>";
