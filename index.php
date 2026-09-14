<?php
// =============================================
// BazarDor — Home Page (index.php)
// Server-Side Rendered for SEO
// =============================================
require_once __DIR__ . '/includes/data.php';

$products = getAllProductsWithPrices();
$categories = getAllCategories();
$todayBengali = toBengaliDate(today());

// Prepare Live Notification Feed Data
$feedItems = getLatestNotifications();

// SEO Meta
$pageTitle = SITE_NAME . ' | আজকের বাজার দর';
$pageDescription = SITE_DESCRIPTION;
$pageUrl = SITE_URL;
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($pageDescription) ?>">
    <link rel="canonical" href="<?= e($pageUrl) ?>/">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= e($pageUrl) ?>">
    <meta property="og:title" content="<?= e($pageTitle) ?>">
    <meta property="og:description" content="<?= e($pageDescription) ?>">
    <meta property="og:locale" content="bn_BD">

    <!-- Theme Color -->
    <meta name="theme-color" content="#16a34a">

    <!-- Favicons (Absolute Path for Reliable Google Indexing) -->
    <link rel="icon" type="image/png" sizes="192x192" href="<?= SITE_URL ?>/assets/favicon.png">
    <link rel="apple-touch-icon" href="<?= SITE_URL ?>/assets/favicon.png">

    <!-- Phosphor Icons for Footer -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet"></noscript>
    <style>
    /* CSS Blinking Cursor Animation */
    @keyframes blink {
        0%, 100% { border-color: transparent; }
        50% { border-color: var(--primary); }
    }
    </style>
</head>
<body>
<?php
// Calculate updated products count
$todayFormatted = today();
$totalProducts = count($products);
$updatedProducts = 0;

foreach ($products as $p) {
    if ($p['latest_date'] === $todayFormatted) {
        $updatedProducts++;
    }
}
$bengaliUpdated = toBengali($updatedProducts);
$bengaliTotal = toBengali($totalProducts);

// Filter for one-word products (no spaces, no brackets)
$simpleProducts = [];
foreach ($products as $p) {
    if (strpos($p['name'], ' ') === false && strpos($p['name'], '(') === false && strpos($p['name'], '-') === false) {
        $simpleProducts[] = $p;
    }
}
// Fallback if none found
if (empty($simpleProducts)) $simpleProducts = $products;

// Generate dynamic typing strings
$typingStrings = [];
shuffle($simpleProducts);
$count = 0;
foreach ($simpleProducts as $item) {
    if ($count >= 6) break;
    
    if ($item['diff'] > 0) {
        $typingStrings[] = [
            'text' => "আজ " . e(getBengaliPossessive($item['name'])) . " দাম বেড়েছে <span style='color:var(--red);font-weight:bold;'>" . toBengali(roundRetailPrice($item['diff'])) . " টাকা</span>"
        ];
    } elseif ($item['diff'] < 0) {
        $typingStrings[] = [
            'text' => "আজ " . e(getBengaliPossessive($item['name'])) . " দাম কমেছে <span style='color:var(--primary);font-weight:bold;'>" . toBengali(roundRetailPrice(abs($item['diff']))) . " টাকা</span>"
        ];
    } else {
        $typingStrings[] = [
            'text' => "আজ " . e($item['name']) . " বিক্রি হচ্ছে <span style='color:var(--primary);font-weight:bold;'>" . toBengali(roundRetailPrice($item['current_price'])) . " টাকায়</span>"
        ];
    }
    $count++;
}

// Generate Ticker Data
$tickerItems = [];
foreach ($products as $p) {
    if ($p['diff'] != 0) {
        $tickerItems[] = $p;
    }
}
if (count($tickerItems) < 10 && count($products) > 0) {
    $tickerItems = array_merge($tickerItems, array_slice($products, 0, 15));
}
$tickerIds = [];
$finalTicker = [];
foreach ($tickerItems as $t) {
    if (!isset($tickerIds[$t['id']])) {
        $tickerIds[$t['id']] = true;
        $finalTicker[] = $t;
    }
    if (count($finalTicker) >= 15) break;
}
shuffle($finalTicker);
?>
    <div class="wrap home-wrap">
        <div class="topbar">
            <div class="brand">
                <img class="brand-logo" src="/assets/logo.png" alt="<?= e(SITE_NAME) ?>" width="130" height="32" onerror="this.style.display='none'">
            </div>
            <div class="update-badge">
                <span class="pulse-dot"></span>
                আপডেট <?= $bengaliUpdated ?>/<?= $bengaliTotal ?>
            </div>
        </div>



        <div class="hero" style="padding-top: 10px;">
            <div>
                <h1 style="min-height: 40px; text-align: left; width: max-content; max-width: 100%; margin: 0 auto 15px auto; display: flex; align-items: flex-end;">
                    <span id="typewriter"></span>
                </h1>

            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <main class="main-container">
        <div id="app">
            
            <!-- Categories (Restored to previous chip design) -->
            <section class="categories-section">
                <div class="category-list">
                    <button class="cat-btn active" data-filter="সব" onclick="handleFilter('সব')">সব পণ্য</button>
                    <button class="cat-btn" data-filter="দাম বেড়েছে" onclick="handleFilter('দাম বেড়েছে')" style="color: var(--up);">দাম বেড়েছে <span class="material-icons-round" style="font-size:14px;vertical-align:middle;">trending_up</span></button>
                    <button class="cat-btn" data-filter="দাম কমেছে" onclick="handleFilter('দাম কমেছে')" style="color: var(--down);">দাম কমেছে <span class="material-icons-round" style="font-size:14px;vertical-align:middle;">trending_down</span></button>
                    <?php foreach ($categories as $cat): ?>
                        <button class="cat-btn" data-filter="<?= e($cat['name']) ?>" onclick="handleFilter('<?= e($cat['name']) ?>')"><?= e($cat['name']) ?></button>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Price List -->
            <section class="prices-section" id="prices">


                <!-- Sleek Ticker Tape -->
                <div class="market-ticker">
                    <div class="ticker-track">
                        <?php for($i=0; $i<2; $i++): // Render twice for seamless loop ?>
                        <div class="ticker-content">
                            <?php foreach($finalTicker as $item): 
                                $diff = $item['diff'];
                                if ($diff > 0) {
                                    $iconClass = 'ph ph-trend-up text-trend-up';
                                } elseif ($diff < 0) {
                                    $iconClass = 'ph ph-trend-down text-trend-down';
                                } else {
                                    $iconClass = 'ph ph-minus text-trend-flat';
                                }
                            ?>
                            <span class="ticker-item">
                                <span class="ticker-item-name"><?= e($item['name']) ?>:</span> 
                                <?= toBengali(roundRetailPrice($item['current_price'])) ?> ৳ 
                                <i class="<?= $iconClass ?>"></i>
                            </span>
                            <span class="ticker-dot">•</span>
                            <?php endforeach; ?>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Modern Search Bar -->
                <div class="modern-search-bar">
                    <span class="material-icons-round search-icon">search</span>
                    <input type="text" id="search-input" placeholder="পণ্য খুঁজুন (যেমন: চাল, পেঁয়াজ, আলু)…" oninput="handleSearch(event)">
                </div>

                <div class="price-list" id="price-list">
                    <?php if (empty($products)): ?>
                        <p style="text-align: center; color: var(--text-muted); padding: 2rem; width: 100%;">কোনো পণ্যের দাম পাওয়া যায়নি।</p>
                    <?php else: ?>
                        <?php foreach ($products as $p):
                            $diff = $p['diff'];
                            $trendClass = getTrendBadgeClass($diff);
                            $trendIcon = getTrendIcon($diff);
                            $trendText = getTrendText($diff);
                            $trendValue = $diff > 0 ? 'up' : ($diff < 0 ? 'down' : 'same');
                        ?>
                        <div class="price-item" data-name="<?= e($p['name']) ?>" data-name-en="<?= e(strtolower($p['name_en'])) ?>" data-category="<?= e($p['category_name']) ?>" data-trend="<?= $trendValue ?>" onclick="window.location.href='/product/<?= e($p['slug']) ?>'" style="cursor: pointer;">
                            <div class="item-left">
                                <img src="/assets/thumbs/<?= e(pathinfo((string)$p['icon_path'], PATHINFO_FILENAME)) ?>.webp" alt="<?= e($p['name']) ?>" class="item-image" width="56" height="56" loading="lazy" decoding="async" onerror="this.src='<?= e($p['icon_path']) ?>'">
                                <div class="item-details">
                                    <h4><?= e($p['name']) ?></h4>
                                    <p>প্রতি <?= e($p['unit']) ?></p>
                                </div>
                            </div>
                            <div class="item-right">
                                <div class="item-price">৳ <?= formatPriceRange($p['current_price'], $p['min_price'] ?? null, $p['max_price'] ?? null) ?></div>
                                <div class="item-trend <?= $trendClass ?>">
                                    <span class="material-icons-round trend-icon"><?= $trendIcon ?></span>
                                    <span><?= $diff !== 0 ? $trendText : '০' ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div id="home-load-more" style="display: none; text-align: center; margin-top: 1.5rem;">
                    <button onclick="loadMore()" style="background: var(--surface); color: var(--primary); border: 1px solid var(--border); padding: 10px 24px; border-radius: 20px; font-size: 1rem; cursor: pointer; transition: all 0.2s ease;">
                        আরও দেখুন (Load More)
                    </button>
                </div>
            </section>

            <!-- Bottom spacing for mobile nav -->
            <div class="mobile-nav-spacer"></div>
        </div>
    </main>

    <!-- Mobile Bottom Navigation -->
    <nav class="bottom-nav" id="bottom-nav">
        <a href="/" class="nav-item active">
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

    <script>
    // ===== Client-side Search, Filter & Pagination =====
    let limit = 15;
    let currentFilter = 'সব';
    let currentQuery = '';

    function renderList() {
        const items = Array.from(document.querySelectorAll('.price-item'));
        let visibleCount = 0;
        let matchCount = 0;

        items.forEach(item => {
            const name = item.dataset.name || '';
            const nameEn = item.dataset.nameEn || '';
            const cat = item.dataset.category || '';
            const trend = item.dataset.trend || '';
            
            const matchQuery = currentQuery === '' || name.includes(currentQuery) || nameEn.includes(currentQuery) || cat.includes(currentQuery);
            
            let matchFilter = false;
            if (currentFilter === 'সব') {
                matchFilter = true;
            } else if (currentFilter === 'দাম বেড়েছে') {
                matchFilter = (trend === 'up');
            } else if (currentFilter === 'দাম কমেছে') {
                matchFilter = (trend === 'down');
            } else {
                matchFilter = (cat === currentFilter);
            }
            
            if (matchQuery && matchFilter) {
                matchCount++;
                if (visibleCount < limit) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            } else {
                item.style.display = 'none';
            }
        });

        const loadMoreBtn = document.getElementById('home-load-more');
        if (loadMoreBtn) {
            if (visibleCount < matchCount) {
                loadMoreBtn.style.display = 'block';
            } else {
                loadMoreBtn.style.display = 'none';
            }
        }
    }

    function handleSearch(e) {
        currentQuery = e.target.value.toLowerCase();
        limit = 15; // Reset limit on new search
        renderList();
    }

    function handleFilter(cat) {
        currentFilter = cat;
        limit = 15; // Reset limit on new filter
        document.querySelectorAll('.cat-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.filter === cat);
        });
        
        // Clear search when changing filter
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.value = '';
            currentQuery = '';
        }
        renderList();
    }

    function loadMore() {
        limit += 15;
        renderList();
    }

    // Initial render
    window.addEventListener('DOMContentLoaded', renderList);
    </script>

    <script>
    // Typewriter Effect
    const typingStrings = <?= json_encode($typingStrings) ?>;
    let typeIndex = 0;
    let charIndex = 0;
    let isDeleting = false;
    const typeElement = document.getElementById('typewriter');
    
    function typeEffect() {
        const currentData = typingStrings[typeIndex];
        const currentString = currentData.text;
        
        if (isDeleting) {
            charIndex--;
            // Instantly skip over HTML tags when deleting backwards
            if (currentString.charAt(charIndex) === '>') {
                while (charIndex > 0 && currentString.charAt(charIndex) !== '<') {
                    charIndex--;
                }
            }
        } else {
            // Instantly skip over HTML tags when typing forwards
            if (currentString.charAt(charIndex) === '<') {
                while (charIndex < currentString.length && currentString.charAt(charIndex) !== '>') {
                    charIndex++;
                }
                charIndex++; // include the '>' character itself
            } else {
                charIndex++;
            }
        }
        
        let displayedStr = currentString.substring(0, charIndex);

        // Add a simple cursor instead of the SVG underline
        const cursor = '<span style="border-right: 2px solid var(--primary); margin-left: 2px; animation: blink 1s step-end infinite;">&nbsp;</span>';
        typeElement.innerHTML = displayedStr + cursor;
        
        let typeSpeed = isDeleting ? 30 : 70;
        
        if (!isDeleting && charIndex === currentString.length) {
            typeSpeed = 2500; // Pause at end
            isDeleting = true;
        } else if (isDeleting && charIndex === 0) {
            isDeleting = false;
            typeIndex = (typeIndex + 1) % typingStrings.length;
            typeSpeed = 500; // Pause before typing next
        }
        
        setTimeout(typeEffect, typeSpeed);
    }
    
    // Start typing
    setTimeout(typeEffect, 500);
    </script>

    <script>
    function openNotificationSheet(e) {
        e.preventDefault();
        document.getElementById('notif-overlay').classList.add('active');
        document.getElementById('notif-sheet').classList.add('active');
        // Remove badge dot after clicking
        const badge = document.querySelector('.nav-badge');
        if (badge) badge.style.display = 'none';
    }

    function closeNotificationSheet() {
        document.getElementById('notif-overlay').classList.remove('active');
        document.getElementById('notif-sheet').classList.remove('active');
    }
    </script>

    <!-- Site Footer -->
    <footer class="site-footer">
        <div class="footer-container">
            <div class="footer-top">
                <div class="footer-brand">
                    <a href="#" class="footer-logo">
                        <div class="footer-logo-icon">
                            <i class="ph ph-leaf"></i>
                        </div>
                        <span class="footer-logo-text">Bazar<span class="footer-logo-highlight">Dor</span></span>
                    </a>
                    <p class="footer-desc">
                        Bringing transparency to Bangladesh's grocery markets. Empowering consumers with accurate daily data.
                    </p>
                </div>
                
                <div class="footer-links-wrapper">
                    <div class="footer-col">
                        <h4>Platform</h4>
                        <ul>
                            <li><a href="/market-index">Market Index</a></li>
                            <li><a href="/price-history">Price History</a></li>
                            <li><a href="/sms-alerts">SMS Alerts</a></li>
</ul>
                    </div>
                    
                    <div class="footer-col">
                        <h4>Company</h4>
                        <ul>
                            <li><a href="/about">About Us</a></li>
                            <li><a href="/methodology">Methodology</a></li>
                            <li><a href="/privacy">Privacy</a></li>
                            <li><a href="/terms">Terms</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p class="footer-copyright">
                    &copy; <span id="year"><?= date('Y') ?></span> BazarDor Platform. Designed in Bangladesh.
                </p>
                <div class="footer-socials">
                    <a href="https://www.facebook.com/BazardorApp" target="_blank" class="footer-social-btn"><i class="ph ph-facebook-logo"></i></a>
                </div>
            </div>
        </div>
    </footer>
</body>


