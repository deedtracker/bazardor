<?php
// =============================================
// BazarDor — Product Detail Page (product.php)
// Server-Side Rendered for SEO
// =============================================
require_once __DIR__ . '/includes/data.php';

$slug = $_GET['slug'] ?? '';
$detail = getProductDetail($slug);

if (!$detail) {
    http_response_code(404);
    echo '<!DOCTYPE html><html lang="bn"><head><meta charset="UTF-8"><title>পাওয়া যায়নি</title></head><body><h1>পণ্য পাওয়া যায়নি</h1><a href="/">হোমে ফিরুন</a></body></html>';
    exit;
}

$product = $detail['product'];
$currentPrice = $detail['currentPrice'];
$previousPrice = $detail['previousPrice'];
$diff = $detail['diff'];
$cls = $detail['diffClass'];
$cityPrices = $detail['cityPrices'];
$cheapest = $detail['cheapest'];
$expensive = $detail['expensive'];
$history = $detail['history'];
$predictions = $detail['predictions'];
$news = $detail['news'];
$trendSummary = $detail['trendSummary'];
$summaryText = $detail['summary'];
$lastUpdated = $detail['lastUpdated'];
$latestDate = $detail['latestDate'];
$latestPrice = $detail['latestPriceData'];
$feedItems = getLatestNotifications();

// SEO Meta
$possessiveName = getBengaliPossessive($product['name']);
$pageTitle = e($possessiveName) . ' আজকের বাজারদর — ' . toBengali(round($currentPrice)) . ' টাকা/' . e($product['unit']) . ' | ' . SITE_NAME;
$pageDescription = 'বাংলাদেশের ৬৪ জেলায় আজ ' . e($possessiveName) . ' দাম প্রতি ' . e($product['unit']) . ' গড়ে ' . toBengali(round($currentPrice)) . ' টাকা। গতকালের চেয়ে ' . getDiffLabel($diff) . '। আপনার এলাকার সঠিক খুচরা বাজারদর, গত ৭ দিনের দামের গ্রাফ এবং আগামীকালের দামের পূর্বাভাস জানতে এখনই ভিজিট করুন।';
$pageUrl = SITE_URL . '/product/' . e($product['slug']);
$ogImage = SITE_URL . '/' . e($product['icon_path']);
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title><?= $pageTitle ?></title>
    <meta name="description" content="<?= $pageDescription ?>">
    <link rel="canonical" href="<?= $pageUrl ?>/">
    
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Article",
      "headline": "<?= $pageTitle ?>",
      "image": "<?= $ogImage ?>",
      "description": "<?= e($pageDescription) ?>",
      "author": {
        "@type": "Organization",
        "name": "<?= SITE_NAME ?>"
      },
      "publisher": {
        "@type": "Organization",
        "name": "<?= SITE_NAME ?>",
        "logo": {
          "@type": "ImageObject",
          "url": "<?= SITE_URL ?>/assets/logo.png"
        }
      },
      "dateModified": "<?= $latestDate ?>"
    }
    </script>

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="article">
    <meta property="og:url" content="<?= $pageUrl ?>">
    <meta property="og:title" content="<?= $pageTitle ?>">
    <meta property="og:description" content="<?= $pageDescription ?>">
    <meta property="og:image" content="<?= $ogImage ?>">
    <meta property="og:locale" content="bn_BD">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="<?= $pageTitle ?>">
    <meta name="twitter:description" content="<?= $pageDescription ?>">
    <meta name="twitter:image" content="<?= $ogImage ?>">

    <!-- Theme Color -->
    <meta name="theme-color" content="#16a34a">

    <!-- Favicons (Absolute Path for Reliable Google Indexing) -->
    <link rel="icon" type="image/png" sizes="192x192" href="<?= SITE_URL ?>/assets/favicon.png">
    <link rel="apple-touch-icon" href="<?= SITE_URL ?>/assets/favicon.png">

    <!-- Canonical URL -->
    <link rel="canonical" href="<?= $pageUrl ?>">

    <link rel="stylesheet" href="/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet"></noscript>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
</head>
<body>
    <!-- Main Content Area -->
    <main class="main-container">
        
        <div class="topbar">
            <div class="brand">
                <a href="/">
                    <img class="brand-logo" src="/assets/logo.png" alt="<?= e(SITE_NAME) ?>" width="130" height="32" onerror="this.style.display='none'">
                </a>
            </div>
            <div class="datestamp"><?= toBengaliDate($latestDate) ?></div>
        </div>

        <div id="app">
            <div class="detail-page">
                <?php
                // Sparkline calculation
                $sparkHistory = array_slice($history, 0, 7);
                $sparkHistory = array_reverse($sparkHistory);
                
                $sparkMax = 1;
                $sparkMin = 999999;
                foreach ($sparkHistory as $sh) {
                    if ($sh['average_price'] > $sparkMax) $sparkMax = $sh['average_price'];
                    if ($sh['average_price'] < $sparkMin) $sparkMin = $sh['average_price'];
                }
                $sparkRange = max(1, $sparkMax - $sparkMin);
                $sparkMinAdj = max(0, $sparkMin - ($sparkRange * 0.2));
                $sparkMaxAdj = $sparkMax + ($sparkRange * 0.2);
                $sparkRangeAdj = $sparkMaxAdj - $sparkMinAdj;

                $sparkStart = count($sparkHistory) > 0 ? toBengaliDateShort($sparkHistory[0]['price_date']) : '';
                $sparkEnd = count($sparkHistory) > 0 ? toBengaliDateShort($sparkHistory[count($sparkHistory)-1]['price_date']) : '';

                // Change chip calculation
                $diffAbs = abs($diff);
                $chipCls = "same"; $chipArrow = "—"; $diffText = "অপরিবর্তিত";
                if ($diff > 0) {
                    $chipCls = "up"; $chipArrow = "▲"; $diffText = toBengali($diffAbs) . " টাকা বেড়েছে";
                } elseif ($diff < 0) {
                    $chipCls = "down"; $chipArrow = "▼"; $diffText = toBengali($diffAbs) . " টাকা কমেছে";
                }
                ?>
                <!-- Hero Card -->
                <div class="product-hero">
                    <div class="product-hero-top">
                        <div class="product-icon-wrap">
                            <img src="/assets/thumbs/<?= e(pathinfo((string)$product['icon_path'], PATHINFO_FILENAME)) ?>.webp" alt="<?= e($product['name']) ?>" width="64" height="64" loading="lazy" decoding="async" onerror="this.src='/<?= e($product['icon_path']) ?>'">
                        </div>
                        <div>
                            <div class="product-hero-title"><?= e(getBengaliPossessive($product['name'])) ?> আজকের বাজারদর</div>
                            <div class="product-hero-sub">সর্বশেষ হালনাগাদ: <?= toBengaliDate($latestDate) ?></div>
                        </div>
                    </div>

                    <div class="price-row">
                        <div class="price-main">৳<?= formatPriceRange($currentPrice, $latestPrice['min_price'] ?? null, $latestPrice['max_price'] ?? null) ?><sub>/ <?= e($product['unit']) ?></sub></div>
                        <div class="change-chip <?= $chipCls ?>">
                            <div class="arrow"><?= $chipArrow ?></div>
                            <div class="txt">গতকালের তুলনায়<b><?= $diffText ?></b></div>
                        </div>
                    </div>

                    <div class="spark">
                        <?php foreach ($sparkHistory as $index => $sh): 
                            $h = (($sh['average_price'] - $sparkMinAdj) / $sparkRangeAdj) * 100;
                            $isLatest = ($index === count($sparkHistory) - 1) ? 'latest' : '';
                        ?>
                        <i class="<?= $isLatest ?>" style="height:<?= round($h) ?>%"></i>
                        <?php endforeach; ?>
                    </div>
                    <div class="spark-label">
                        <span><?= $sparkStart ?></span>
                        <span><?= $sparkEnd ?></span>
                    </div>
                </div>

                <!-- Insight -->
                <div class="insight">
                    <?= $summaryText ?>
                </div>

                <!-- City Prices -->
                <?php if (!empty($cityPrices)): ?>
                <div class="city-section">
                    <div class="section-label" style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <?= getIcon('location') ?>
                            বিভিন্ন এলাকার দাম
                        </div>
                        <div style="width: 150px; font-weight: normal;">
                            <select id="city-search">
                                <option value="">জেলা খুঁজুন...</option>
                                <?php foreach ($cityPrices as $c): ?>
                                    <option value="<?= e($c['city_name']) ?>"><?= e($c['city_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="city-list" id="city-list-container">
                        <?php 
                        // Only show top 10 initially
                        $displayCities = array_slice($cityPrices, 0, 10);
                        foreach ($displayCities as $c):
                            $isCheap = $c['city_name'] === $cheapest;
                            $isExp = $c['city_name'] === $expensive;
                        ?>
                        <div class="city-row <?= $isCheap ? 'cheapest' : '' ?> <?= $isExp ? 'expensive' : '' ?>">
                            <div class="city-name">
                                <?= e($c['city_name']) ?>
                                <?php if ($isCheap): ?><span class="city-tag low">সস্তা</span><?php endif; ?>
                                <?php if ($isExp): ?><span class="city-tag high">বেশি</span><?php endif; ?>
                            </div>
                            <div class="city-price">
                                <span class="city-price-val"><?= toBengali(roundRetailPrice((float)$c['price'])) ?></span>
                                <span class="city-price-unit">টাকা</span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($cityPrices) > 10): ?>
                    <div id="load-more-container" style="text-align: center; margin-top: 15px;">
                        <button id="load-more-btn" onclick="loadMoreCities()" style="background: var(--surface); color: var(--primary); border: 1px solid var(--border); padding: 8px 16px; border-radius: 20px; font-size: 0.9rem; cursor: pointer; transition: all 0.2s ease;">
                            আরও দেখুন (Load More)
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Price History Timeline -->
                <?php if (!empty($history)): ?>
                <div class="timeline-section">
                    <div class="section-label">
                        গত <?= toBengali(count($history)) ?> দিনের দাম
                    </div>
                    <div class="timeline-track">
                        <?php foreach ($history as $i => $h):
                            $prev = $i > 0 ? $history[$i - 1]['average_price'] : $h['average_price'];
                            $change = $h['average_price'] - $prev;
                            $changeCls = getDiffClass($change);
                            $changeText = '';
                            if ($i > 0) {
                                if ($change > 0) $changeText = '↑' . toBengali($change);
                                elseif ($change < 0) $changeText = '↓' . toBengali(abs($change));
                                else $changeText = '—';
                            }
                        ?>
                        <div class="timeline-point">
                            <div class="timeline-dot-wrap">
                                <div class="timeline-dot-inner"></div>
                            </div>
                            <div class="timeline-price"><?= toBengali((float)$h['average_price']) ?>৳</div>
                            <div class="timeline-date"><?= toBengaliDateShort($h['price_date']) ?></div>
                            <?php if ($i > 0): ?>
                                <div class="timeline-change <?= $changeCls ?>"><?= $changeText ?></div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="timeline-summary"><?= $trendSummary ?></div>
                </div>
                <?php endif; ?>

                <!-- Predictions -->
                <?php if (!empty($predictions)): ?>
                <div class="predict-section">
                    <div class="section-label">
                        আগামী ৩ দিনের সম্ভাব্য দাম
                    </div>
                    <div class="forecast-grid">
                        <?php foreach ($predictions as $pred): 
                            $conf = $pred['confidence']; // 'high', 'medium', 'low'
                            $tagCls = 'tag-unsure';
                            $priceColor = 'var(--text-muted)';
                            if ($conf === 'high') {
                                $tagCls = 'tag-high';
                                $priceColor = 'var(--green)';
                            } elseif ($conf === 'medium') {
                                $tagCls = 'tag-medium';
                                $priceColor = 'var(--amber-600)';
                            }
                        ?>
                        <div class="f-card">
                            <div class="f-date"><?= toBengaliDateShort($pred['date']) ?></div>
                            <div class="f-price" style="color:<?= $priceColor ?>;"><?= toBengali($pred['price']) ?></div>
                            <div class="f-unit">টাকা / <?= e($product['unit']) ?></div>
                            <span class="f-tag <?= $tagCls ?>"><?= getConfidenceLabel($conf) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="forecast-note">* বিগত কয়েক সপ্তাহের বাজারদরের প্রবণতা বিশ্লেষণ করে এই সম্ভাব্য মূল্য অনুমান করা হয়েছে।</div>
                </div>
                <?php endif; ?>

                <!-- News -->
                <?php if (!empty($news)): ?>
                <div class="news-section">
                    <div class="section-label">
                        <?= getIcon('news') ?>
                        সম্পর্কিত খবর
                    </div>
                    <div class="news-list">
                        <?php foreach ($news as $n): ?>
                        <div class="news-item">
                            <?php if (!empty($n['url'])): ?>
                                <a href="<?= e($n['url']) ?>" target="_blank" rel="noopener" class="news-title"><?= e($n['title']) ?></a>
                            <?php else: ?>
                                <div class="news-title"><?= e($n['title']) ?></div>
                            <?php endif; ?>
                            <div class="news-meta">
                                <span class="news-source"><?= e($n['source']) ?></span>
                                <span>•</span>
                                <span><?= toBengaliDateShort($n['date_added']) ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="news-coming-soon">
                        <span class="cs-dot"></span>
                        শীঘ্রই সরাসরি সংবাদ যুক্ত হবে
                    </div>
                </div>

                <?php endif; ?>

                <!-- Credibility / Trust Section -->
                <div class="trust-divider"></div>
                <details class="trust-accordion">
                    <summary class="trust-summary">
                        <span class="material-icons-round">verified_user</span>
                        <span style="flex:1;">আমাদের তথ্যের নির্ভরযোগ্যতা</span>
                        <span class="material-icons-round drop-icon">expand_more</span>
                    </summary>
                    <div class="trust-content">
                        <div class="trust-item">
                            <div class="t-icon">
                                <span class="material-icons-round">groups</span>
                            </div>
                            <div class="t-text">
                                <strong>মাঠপর্যায়ের প্রতিনিধি</strong> কারওয়ান বাজার, চাঁদপুর, রংপুর, রাজশাহীসহ দেশের বিভিন্ন প্রান্ত থেকে আমাদের ৮ জনের একটি ডেডিকেটেড টিম প্রতিদিন বাজারের বাস্তব চিত্র সংগ্রহ করে।
                            </div>
                        </div>
                        <div class="trust-item">
                            <div class="t-icon">
                                <span class="material-icons-round">storefront</span>
                            </div>
                            <div class="t-text">
                                <strong>খুচরা বাজারের সঠিক দাম</strong> আমরা কোনো পাইকারি দর দেখাই না। একজন সাধারণ ক্রেতা বাজারে গেলে যে দামে পণ্য কিনতে পারেন, আমরা ঠিক সেই খুচরা (Retail) দামই প্রকাশ করি।
                            </div>
                        </div>
                        <div class="trust-item">
                            <div class="t-icon">
                                <span class="material-icons-round">update</span>
                            </div>
                            <div class="t-text">
                                <strong>নিয়মিত আপডেট</strong> প্রতিদিন সকাল ৮টার মধ্যে বাজারদর আপডেট করা হয়, যাতে আপনি সারাদিনের কেনাকাটার সঠিক সিদ্ধান্ত নিতে পারেন।
                            </div>
                        </div>
                    </div>
                </details>

                <!-- Share Card Removed -->

            </div><!-- /.detail-page -->

            <!-- Bottom spacing for mobile nav -->
            <div class="mobile-nav-spacer"></div>
        </div>
    </main>

    <!-- Mobile Bottom Navigation -->
    <nav class="bottom-nav" id="bottom-nav">
        <a href="/" class="nav-item">
            <span class="material-icons-round">home</span>
            <span>হোম</span>
        </a>
        <a href="#" class="nav-item" onclick="openNotificationSheet(event)">
            <div style="position:relative; display:inline-block;">
                <span class="material-icons-round">notifications</span>
                <span class="nav-badge"></span>
            </div>
            <span>আপডেট</span>
        </a>
    </nav>

    <!-- Notification Bottom Sheet Overlay -->
    <div class="bottom-sheet-overlay" id="notif-overlay" onclick="closeNotificationSheet()"></div>

    <!-- Notification Bottom Sheet -->
    <div class="bottom-sheet" id="notif-sheet">
        <div class="bs-header">
            <h3>লাইভ আপডেট</h3>
            <button class="bs-close" onclick="closeNotificationSheet()">
                <span class="material-icons-round">close</span>
            </button>
        </div>
        <div class="bs-content">
            <ul class="notif-list">
                <?php foreach ($feedItems as $item): ?>
                    <li>
                        <span class="material-icons-round notif-icon">bolt</span>
                        <span><?= e($item) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
    // ===== City Search Dropdown =====
    const cityData = <?= json_encode($cityPrices) ?>;
    let currentDisplayCount = 10;
    
    if (document.getElementById('city-search')) {
        new TomSelect("#city-search", {
            create: false,
            placeholder: "জেলা খুঁজুন...",
            onChange: function(val) {
                if(!val) {
                    renderCities(cityData.slice(0, currentDisplayCount));
                } else {
                    const found = cityData.find(c => c.city_name.trim() === val.trim());
                    if(found) {
                        renderCities([found]);
                    } else {
                        renderCities(cityData.slice(0, currentDisplayCount)); // fallback
                    }
                }
            }
        });
    }

    function loadMoreCities() {
        currentDisplayCount += 10;
        renderCities(cityData.slice(0, currentDisplayCount));
    }

    function updateLoadMoreButton(displayedCities) {
        const container = document.getElementById('load-more-container');
        if (!container) return;
        
        // Hide if we're showing a single search result, or if we've reached the end
        if (displayedCities.length === 1 || currentDisplayCount >= cityData.length) {
            container.style.display = 'none';
        } else {
            container.style.display = 'block';
        }
    }

    function renderCities(cities) {
        const list = document.getElementById('city-list-container');
        if (!list) return;
        list.innerHTML = '';
        cities.forEach(c => {
            const isCheap = c.city_name === '<?= $cheapest ?>';
            const isExp = c.city_name === '<?= $expensive ?>';
            
            const digits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
            const toBn = (num) => String(num).replace(/[0-9]/g, d => digits[parseInt(d)]);
            const rawPrice = parseFloat(c.price);
            const roundedPrice = Math.round(rawPrice / 5) * 5;
            const priceBn = toBn(roundedPrice);
            
            let tags = '';
            if (isCheap) tags += '<span class="city-tag low">সস্তা</span>';
            if (isExp) tags += '<span class="city-tag high">বেশি</span>';

            const html = `
                <div class="city-row ${isCheap ? 'cheapest' : ''} ${isExp ? 'expensive' : ''}">
                    <div class="city-name">${c.city_name} ${tags}</div>
                    <div class="city-price">
                        <span class="city-price-val">${priceBn}</span>
                        <span class="city-price-unit">টাকা</span>
                    </div>
                </div>
            `;
            list.innerHTML += html;
        });
        
        updateLoadMoreButton(cities);
    }


    // Init

    function openNotificationSheet(e) {
        e.preventDefault();
        document.getElementById('notif-overlay').classList.add('active');
        document.getElementById('notif-sheet').classList.add('active');
        const badge = document.querySelector('.nav-badge');
        if (badge) badge.style.display = 'none';
    }

    function closeNotificationSheet() {
        document.getElementById('notif-overlay').classList.remove('active');
        document.getElementById('notif-sheet').classList.remove('active');
    }
    </script>
</body>
</html>
