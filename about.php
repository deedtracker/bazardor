<?php
require_once __DIR__ . '/includes/data.php';
$pageTitle = SITE_NAME . ' | আমাদের সম্পর্কে (About Us)';
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title><?= e($pageTitle) ?></title>
    <link rel="icon" type="image/png" sizes="192x192" href="/assets/favicon.png">
    <link rel="apple-touch-icon" href="/assets/favicon.png">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="/style.css">
    <style>
        /* Modern About Us Styling */
        body { background-color: #FDFCF8; }
        .about-hero {
            background: linear-gradient(135deg, #F0F7F2 0%, #DFF0E4 100%);
            padding: 6rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
        }
        .about-hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            color: #142416;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            letter-spacing: -0.02em;
            font-family: var(--font-bengali);
        }
        .about-hero p {
            font-size: 1.35rem;
            color: #4A5D4E;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.6;
            font-family: var(--font-bengali);
        }

        .about-section { padding: 6rem 1.5rem; border-bottom: 1px solid var(--border-color); }
        .about-section.bg-white { background: #FFFFFF; }
        
        .about-container { max-width: 1100px; margin: 0 auto; font-family: var(--font-bengali); }
        
        .section-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 4rem;
            align-items: center;
        }
        @media (min-width: 860px) {
            .section-grid { grid-template-columns: 1fr 1fr; }
        }

        .feature-card {
            background: #FFFFFF;
            border: 1px solid var(--border-color);
            padding: 3rem 2.5rem;
            border-radius: 24px;
            box-shadow: 0 12px 40px rgba(20, 36, 22, 0.04);
            transition: transform 0.3s;
            height: 100%;
        }
        .feature-card:hover { transform: translateY(-5px); }
        
        .feature-icon {
            width: 72px;
            height: 72px;
            background: #F0F7F2;
            color: var(--primary);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.25rem;
            margin-bottom: 2rem;
        }

        .feature-card h3 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #142416;
            margin-bottom: 1rem;
        }
        .feature-card p {
            font-size: 1.15rem;
            line-height: 1.7;
            color: #4A5D4E;
        }

        .about-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: #142416;
            margin-bottom: 1.5rem;
            line-height: 1.3;
        }
        .about-text {
            font-size: 1.2rem;
            line-height: 1.8;
            color: #4A5D4E;
            margin-bottom: 1.5rem;
        }
        
        .highlight-text {
            color: var(--primary);
            font-weight: 600;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin-top: 3rem;
            padding-top: 3rem;
            border-top: 1px solid var(--border-color);
        }
        .stat-box { text-align: left; }
        .stat-number {
            font-size: 3.5rem;
            font-weight: 800;
            color: var(--primary);
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        .stat-label {
            font-size: 1.1rem;
            color: #4A5D4E;
            font-weight: 500;
        }
        
        /* Two col cards grid */
        .cards-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        @media (min-width: 768px) {
            .cards-grid { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body>
    <div class="wrap home-wrap" style="background: white; border-bottom: 1px solid var(--border-color); margin-bottom: 0;">
        <div class="topbar" style="justify-content: center; padding: 15px;">
            <a href="/" class="brand" style="text-decoration:none;">
                <img class="brand-logo" src="/assets/logo.png" alt="BazarDor" width="130" height="32" onerror="this.style.display='none'">
            </a>
        </div>
    </div>

    <!-- Hero Section -->
    <header class="about-hero">
        <h1>বাজারের সঠিক দাম, আপনার হাতের মুঠোয়</h1>
        <p>বাজারদর (BazarDor) হলো বাংলাদেশের সাধারণ ক্রেতাদের জন্য তৈরি প্রথম নির্ভরযোগ্য খুচরা বাজারদর প্ল্যাটফর্ম। আমাদের মূল লক্ষ্য তথ্য ও প্রযুক্তির সাহায্যে আপনাকে একজন সচেতন ও বুদ্ধিমান ক্রেতা হিসেবে গড়ে তোলা।</p>
    </header>

    <!-- Mission Section -->
    <section class="about-section bg-white">
        <div class="about-container section-grid">
            <div>
                <h2 class="about-title">কেন আমাদের এই পথচলা?</h2>
                <p class="about-text">প্রতিদিন সকালে বাজারে গিয়ে দাম নিয়ে বিভ্রান্তি, বিক্রেতার সাথে অহেতুক দরদাম নিয়ে অস্বস্তি এবং সিন্ডিকেটের কারসাজি—এই সমস্যাগুলো আমাদের নিত্যদিনের সঙ্গী। সাধারণ ক্রেতা হিসেবে আমরা কখনোই নিশ্চিত হতে পারি না যে যে দামে পণ্যটি কিনছি, তা সত্যিই সঠিক দাম কিনা।</p>
                <p class="about-text">এই অস্পষ্টতা এবং অনিশ্চয়তা দূর করতেই বাজারদরের জন্ম। আমাদের একমাত্র উদ্দেশ্য হলো <span class="highlight-text">সঠিক তথ্যের মাধ্যমে ভোক্তাদের ক্ষমতায়ন করা</span>, যাতে কেউ আর বাজারের প্রকৃত দাম নিয়ে অন্ধকারে না থাকেন।</p>
            </div>
            <div class="cards-grid">
                <div class="feature-card" style="padding: 2rem;">
                    <i class="ph ph-shield-check feature-icon" style="width: 56px; height: 56px; font-size: 1.75rem; margin-bottom: 1rem;"></i>
                    <h3 style="font-size: 1.4rem;">স্বচ্ছতা নিশ্চিতকরণ</h3>
                    <p style="font-size: 1rem;">বাজারের দাম নিয়ে সব ধরনের বিভ্রান্তি ও গুজব প্রতিরোধ করা আমাদের মূল লক্ষ্য।</p>
                </div>
                <div class="feature-card" style="padding: 2rem;">
                    <i class="ph ph-hand-coins feature-icon" style="width: 56px; height: 56px; font-size: 1.75rem; margin-bottom: 1rem;"></i>
                    <h3 style="font-size: 1.4rem;">সঠিক বাজেট</h3>
                    <p style="font-size: 1rem;">বাজারে যাওয়ার আগেই পকেটের বাজেট হিসাব করার সুযোগ তৈরি করে দেওয়া।</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team & Data Collection Section -->
    <section class="about-section">
        <div class="about-container section-grid">
            <div style="order: 2;">
                <h2 class="about-title">মাঠপর্যায়ের ডেডিকেটেড টিম</h2>
                <p class="about-text">আমরা বিশ্বাস করি, এসি রুমে বসে কম্পিউটারের পর্দায় বাজারের সঠিক চিত্র পাওয়া কখনোই সম্ভব নয়। তাই আমাদের রয়েছে একটি অত্যন্ত পরিশ্রমী ও ডেডিকেটেড টিম যারা সরাসরি বাজারের রূঢ় বাস্তবতার সাথে কাজ করেন।</p>
                <p class="about-text">কারওয়ান বাজার, চাঁদপুর, রংপুর, রাজশাহীসহ দেশের বিভিন্ন প্রান্তের প্রধান বাজারগুলোতে আমাদের প্রতিনিধিরা প্রতিদিন উপস্থিত থাকেন। তারা কোনো পাইকারি আড়তদার নন, বরং একজন সাধারণ ক্রেতার ছদ্মবেশে বাজারে গিয়ে বিভিন্ন দোকান থেকে দাম যাচাই করেন। বিক্রেতা এবং সাধারণ ক্রেতাদের সাথে কথা বলে তারা বাজারের সবচেয়ে নিখুঁত ও বাস্তব চিত্রটি আমাদের প্ল্যাটফর্মে তুলে আনেন।</p>
                
                <div class="stats-container">
                    <div class="stat-box">
                        <div class="stat-number">০৮</div>
                        <div class="stat-label">ডেডিকেটেড ফিল্ড টিম মেম্বার</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number">৮টা</div>
                        <div class="stat-label">সকাল ৮টার মধ্যে ডেটা লাইভ</div>
                    </div>
                </div>
            </div>
            <div style="order: 1;">
                <div class="feature-card" style="background: linear-gradient(180deg, #FFFFFF 0%, #FDFCF8 100%);">
                    <i class="ph ph-storefront feature-icon"></i>
                    <h3>শুধুমাত্র খুচরা (Retail) দাম</h3>
                    <p>বেশিরভাগ প্ল্যাটফর্ম বা সংবাদমাধ্যম যেখানে বড় আড়তের পাইকারি দর প্রকাশ করে, সেখানে আমরা শুধুমাত্র <strong>খুচরা দাম</strong> প্রকাশ করি। কারণ একজন সাধারণ ক্রেতা কখনো পাইকারি বাজারে গিয়ে কেনাকাটা করেন না। আপনার পাশের মুদি দোকান বা কাঁচাবাজারে গেলে আপনি ঠিক যে দামে পণ্যটি কিনতে পারবেন, আমরা ঠিক সেই দামটিই তুলে ধরি। কয়েকটি দোকানের দামের গড় করে সবচেয়ে বাস্তব দামটিই এখানে প্রকাশ করা হয়।</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Promise Section -->
    <section class="about-section bg-white" style="text-align: center;">
        <div class="about-container">
            <h2 class="about-title" style="margin-bottom: 2rem;">আমাদের প্রতিশ্রুতি</h2>
            <p class="about-text" style="max-width: 800px; margin: 0 auto 3rem auto;">তথ্যপ্রযুক্তির এই আধুনিক যুগে সঠিক তথ্য পাওয়া সবার নাগরিক অধিকার। আমরা প্রতিশ্রুতিবদ্ধ যে, বাজারদরের প্রতিটি তথ্য সম্পূর্ণ নিরপেক্ষ এবং যাচাইকৃত হবে। আমাদের কোনো বিশেষ ব্যবসায়ী গোষ্ঠী বা সিন্ডিকেটের সাথে কোনো ধরনের সম্পৃক্ততা নেই। আমরা শুধু এবং শুধুমাত্র সাধারণ ক্রেতাদের পক্ষে কথা বলি।</p>
            
            <div class="cards-grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
                <div class="feature-card">
                    <i class="ph ph-clock feature-icon" style="margin: 0 auto 1.5rem auto;"></i>
                    <h3 style="text-align: center;">প্রতিদিন আপডেট</h3>
                    <p style="text-align: center;">বাজারের দামের কোনো সাপ্তাহিক ছুটি নেই, তাই আমাদেরও নেই। প্রতিদিনই পাবেন লাইভ আপডেট।</p>
                </div>
                <div class="feature-card">
                    <i class="ph ph-chart-line-up feature-icon" style="margin: 0 auto 1.5rem auto;"></i>
                    <h3 style="text-align: center;">ইনডেক্স ও ইতিহাস</h3>
                    <p style="text-align: center;">অতীতের দামের সাথে বর্তমানের দাম মিলিয়ে দেখার সুবিধা, যা আপনাকে সচেতন করবে।</p>
                </div>
                <div class="feature-card">
                    <i class="ph ph-prohibit feature-icon" style="margin: 0 auto 1.5rem auto;"></i>
                    <h3 style="text-align: center;">সিন্ডিকেট মুক্ত</h3>
                    <p style="text-align: center;">কোনো ব্যবসায়ীর দ্বারা প্রভাবিত না হয়ে শুধুমাত্র প্রকৃত বাজারের অবস্থাই তুলে ধরা হয়।</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-container">
            <div class="footer-top">
                <div class="footer-brand">
                    <a href="/" class="footer-logo">
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

