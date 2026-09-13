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

    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet"></noscript>
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



        <div class="hero">
            <div>
                <p class="greeting">আসসালামু আলাইকুম</p>
                <h1>
                    আজ বাজারে<br>
                    <span class="underline-wrap">কোন জিনিসের দাম কত?<svg viewBox="0 0 300 10" preserveAspectRatio="none"><path d="M2 6 Q75 2 150 6 T298 5" stroke="#D99A2B" stroke-width="3" fill="none" stroke-linecap="round"/></svg></span>
                </h1>
                <p class="sub">বাংলাদেশের <b>৬৪ জেলার</b> পাইকারি ও খুচরা বাজারদর প্রতিদিন হালনাগাদ হয় সকাল ৮টায় — চাল, সবজি, মাছ-মাংস থেকে মসলা পর্যন্ত।</p>

                <div class="search">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="flex-shrink:0">
                        <circle cx="11" cy="11" r="7" stroke="#9A968A" stroke-width="2"/>
                        <path d="M20 20L16.5 16.5" stroke="#9A968A" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <input type="text" id="search-input" placeholder="পণ্য খুঁজুন (যেমন: চাল, পেঁয়াজ)…" oninput="handleSearch(event)">
                </div>

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
                <div class="section-title">
                    <h3>আজকের বাজার দর</h3>
                    <span class="update-time">আপডেট: <?= $todayBengali ?></span>
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
                                <img src="/assets/thumbs/<?= e(pathinfo($p['icon_path'], PATHINFO_FILENAME)) ?>.webp" alt="<?= e($p['name']) ?>" class="item-image" width="56" height="56" loading="lazy" decoding="async" onerror="this.src='<?= e($p['icon_path']) ?>'">
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
</body>
</html>
