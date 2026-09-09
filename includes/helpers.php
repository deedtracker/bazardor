<?php
// =============================================
// BazarDor — Helper Functions
// =============================================

/**
 * Convert English digits to Bengali digits.
 */
function toBengali($num) {
    $digits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
    return preg_replace_callback('/[0-9]/', function ($m) use ($digits) {
        return $digits[(int)$m[0]];
    }, (string)$num);
}

/**
 * Format a date string (YYYY-MM-DD) to Bengali date (e.g., "৫ সেপ্টেম্বর, ২০২৬").
 */
function toBengaliDate($dateStr, $includeYear = true) {
    $months = [
        1 => 'জানুয়ারি', 2 => 'ফেব্রুয়ারি', 3 => 'মার্চ', 4 => 'এপ্রিল',
        5 => 'মে', 6 => 'জুন', 7 => 'জুলাই', 8 => 'আগস্ট',
        9 => 'সেপ্টেম্বর', 10 => 'অক্টোবর', 11 => 'নভেম্বর', 12 => 'ডিসেম্বর'
    ];
    $ts = strtotime($dateStr);
    $day = toBengali(date('j', $ts));
    $month = $months[(int)date('n', $ts)];
    if ($includeYear) {
        $year = toBengali(date('Y', $ts));
        return $day . ' ' . $month . ', ' . $year;
    }
    return $day . ' ' . $month;
}

/**
 * Format a date string to short Bengali date (e.g., "৩০ আগস্ট").
 */
function toBengaliDateShort($dateStr) {
    return toBengaliDate($dateStr, false);
}

/**
 * Get today's date in YYYY-MM-DD format.
 */
function today() {
    return date('Y-m-d');
}

/**
 * Sanitize output for HTML to prevent XSS.
 */
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Get the price difference text in Bengali.
 */
function getDiffLabel($diff) {
    $rDiff = round($diff);
    if ($rDiff > 0) return toBengali($rDiff) . ' টাকা বেড়েছে';
    if ($rDiff < 0) return toBengali(abs($rDiff)) . ' টাকা কমেছে';
    return 'অপরিবর্তিত';
}

/**
 * Get the CSS class for a price difference.
 */
function getDiffClass($diff) {
    if ($diff > 0) return 'up';
    if ($diff < 0) return 'down';
    return 'same';
}

/**
 * Get the Material Icon name for a trend.
 */
function getTrendIcon($diff) {
    if ($diff > 0) return 'trending_up';
    if ($diff < 0) return 'trending_down';
    return 'remove';
}

/**
 * Get the CSS class for the trend badge.
 */
function getTrendBadgeClass($diff) {
    if ($diff > 0) return 'trend-up';
    if ($diff < 0) return 'trend-down';
    return 'trend-neutral';
}

/**
 * Get the trend text for the home page list.
 */
function getTrendText($diff) {
    $rDiff = round($diff);
    if ($rDiff > 0) return '+' . toBengali($rDiff);
    if ($rDiff < 0) return '-' . toBengali(abs($rDiff));
    return '০';
}

/**
 * Calculate predictions based on the last N days of price history.
 * Uses Simple Moving Average (SMA) with trend extrapolation.
 * Returns array of 3 predictions with date, price, and confidence.
 */
function calculatePredictions($history, $unit) {
    $predictions = [];
    $count = count($history);

    if ($count < 2) {
        // Not enough data — return flat predictions
        $lastPrice = $count > 0 ? $history[$count - 1]['average_price'] : 0;
        $lastDate = $count > 0 ? $history[$count - 1]['price_date'] : today();
        for ($i = 1; $i <= 3; $i++) {
            $predictions[] = [
                'date' => date('Y-m-d', strtotime($lastDate . ' +' . $i . ' day')),
                'price' => round($lastPrice),
                'confidence' => 'low'
            ];
        }
        return $predictions;
    }

    // Calculate the average daily change over the last few days
    $prices = array_column($history, 'average_price');
    $lastDate = $history[$count - 1]['price_date'];
    $lastPrice = $prices[$count - 1];

    // Calculate trend (average change per day over last entries)
    $changes = [];
    for ($i = 1; $i < $count; $i++) {
        $changes[] = $prices[$i] - $prices[$i - 1];
    }
    $avgChange = array_sum($changes) / count($changes);

    // Calculate volatility (standard deviation of changes)
    $variance = 0;
    foreach ($changes as $c) {
        $variance += ($c - $avgChange) ** 2;
    }
    $stdDev = sqrt($variance / count($changes));

    // Generate predictions
    for ($i = 1; $i <= 3; $i++) {
        $predictedPrice = round($lastPrice + ($avgChange * $i));
        if ($predictedPrice < 0) $predictedPrice = 0;

        // Confidence based on volatility and how far out we're predicting
        $volatilityRatio = $lastPrice > 0 ? ($stdDev / $lastPrice) * 100 : 100;
        if ($i === 1 && $volatilityRatio < 5) {
            $confidence = 'high';
        } elseif ($i <= 2 && $volatilityRatio < 10) {
            $confidence = 'medium';
        } else {
            $confidence = 'low';
        }

        $predictions[] = [
            'date' => date('Y-m-d', strtotime($lastDate . ' +' . $i . ' day')),
            'price' => $predictedPrice,
            'confidence' => $confidence
        ];
    }

    return $predictions;
}

/**
 * Get the Bengali label for prediction confidence.
 */
function getConfidenceLabel($confidence) {
    switch ($confidence) {
        case 'high': return 'সম্ভাবনা বেশি';
        case 'medium': return 'মাঝারি সম্ভাবনা';
        default: return 'অনিশ্চিত';
    }
}

/**
 * Build the trend summary text for the history section.
 */
function buildTrendSummary($history) {
    $count = count($history);
    if ($count < 2) return '';

    $prices = array_column($history, 'average_price');
    $allUp = true;
    $allDown = true;
    $allSame = true;

    for ($i = 1; $i < $count; $i++) {
        if ($prices[$i] > $prices[$i - 1]) { $allDown = false; $allSame = false; }
        elseif ($prices[$i] < $prices[$i - 1]) { $allUp = false; $allSame = false; }
        else { $allUp = false; $allDown = false; }
    }

    $totalChange = $prices[$count - 1] - $prices[0];
    $days = toBengali($count);

    if ($allSame) return "গত {$days} দিনে দাম <strong>একই</strong> রয়েছে";
    if ($allUp) return "গত {$days} দিনে দাম <strong>ক্রমাগত বেড়েছে</strong>";
    if ($allDown) return "গত {$days} দিনে দাম <strong>ক্রমাগত কমেছে</strong>";
    if ($totalChange > 0) return "গত {$days} দিনে <strong>ওঠানামা</strong> করেছে, মোট <strong>" . toBengali($totalChange) . " টাকা বেড়েছে</strong>";
    if ($totalChange < 0) return "গত {$days} দিনে <strong>ওঠানামা</strong> করেছে, মোট <strong>" . toBengali(abs($totalChange)) . " টাকা কমেছে</strong>";
    return "গত {$days} দিনে <strong>ওঠানামা</strong> করেছে, তবে সার্বিকভাবে <strong>অপরিবর্তিত</strong>";
}

/**
 * Fetch news title and source from a URL using cURL and DOM parsing.
 */
function scrapeNewsFromUrl($url) {
    $result = ['title' => '', 'source' => ''];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; BazarDor/1.0)',
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $html = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200 || empty($html)) {
        return $result;
    }

    // Suppress DOM warnings for malformed HTML
    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $doc->loadHTML('<?xml encoding="UTF-8">' . $html);
    libxml_clear_errors();

    $xpath = new DOMXPath($doc);

    // Try og:title first, then <title>
    $ogTitle = $xpath->query('//meta[@property="og:title"]/@content');
    if ($ogTitle->length > 0) {
        $result['title'] = trim($ogTitle->item(0)->nodeValue);
    } else {
        $titleTags = $doc->getElementsByTagName('title');
        if ($titleTags->length > 0) {
            $result['title'] = trim($titleTags->item(0)->textContent);
        }
    }

    // Try og:site_name for source
    $ogSiteName = $xpath->query('//meta[@property="og:site_name"]/@content');
    if ($ogSiteName->length > 0) {
        $result['source'] = trim($ogSiteName->item(0)->nodeValue);
    } else {
        // Fallback: extract domain name
        $parsedUrl = parse_url($url);
        $result['source'] = $parsedUrl['host'] ?? '';
    }

    return $result;
}

/**
 * SVG Icons used throughout the site (same as the JS ICONS object).
 */
function getIcon($name) {
    $icons = [
        'clock' => '<svg viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>',
        'search' => '<svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>',
        'back' => '<svg viewBox="0 0 24 24"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>',
        'location' => '<svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>',
        'history' => '<svg viewBox="0 0 24 24"><path d="M13 3a9 9 0 0 0-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42A8.954 8.954 0 0 0 13 21a9 9 0 0 0 0-18zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/></svg>',
        'predict' => '<svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>',
        'news' => '<svg viewBox="0 0 24 24"><path d="M22 3H2C.9 3 0 3.9 0 5v14c0 1.1.9 2 2 2h20c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H2V5h20v14zM3 6h8v5H3V6zm0 6h8v5H3v-5zm9-6h8v2h-8V6zm0 3h8v2h-8V9zm0 3h8v2h-8v-2zm0 3h8v2h-8v-2z"/></svg>',
        'download' => '<svg viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>',
        'calendar' => '<svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="18" rx="4" fill="#16a34a" /><path d="M7 2v3M17 2v3M2 8h20M7 13h2v2H7zm5 0h2v2h-2zm5 0h2v2h-2z" stroke="white" stroke-width="2" fill="none" stroke-linecap="round" /></svg>',
        'towerIcon' => '<svg viewBox="0 0 48 48" fill="none"><circle cx="24" cy="16" r="3" fill="currentColor"/><path d="M24 19V38" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/><path d="M24 38L17 44" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M24 38L31 44" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M19 32L29 32" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M16 10C18 12 18 15 16 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" fill="none"/><path d="M11 7C14.5 10.5 14.5 17 11 21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" fill="none"/><path d="M32 10C30 12 30 15 32 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" fill="none"/><path d="M37 7C33.5 10.5 33.5 17 37 21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" fill="none"/></svg>',
        'trendUp' => '<svg viewBox="0 0 50 50" fill="none"><path d="M14 34L21 25L27 30L38 17" stroke="#16a34a" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/><path d="M31 17H38V24" stroke="#16a34a" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        'trendDown' => '<svg viewBox="0 0 50 50" fill="none"><path d="M14 17L21 26L27 21L38 34" stroke="#16a34a" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/><path d="M31 34H38V27" stroke="#16a34a" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        'arrowUp' => '<svg viewBox="0 0 24 24"><path d="M7 14l5-5 5 5z"/></svg>',
        'arrowDown' => '<svg viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z"/></svg>',
        'arrowNeutral' => '<svg viewBox="0 0 24 24"><path d="M6 12h12" stroke="currentColor" stroke-width="2" fill="none"/></svg>',
    ];
    return $icons[$name] ?? '';
}

/**
 * Get the arrow SVG based on price difference.
 */
function getArrowSVG($diff) {
    if ($diff > 0) return getIcon('arrowUp');
    if ($diff < 0) return getIcon('arrowDown');
    return getIcon('arrowNeutral');
}
