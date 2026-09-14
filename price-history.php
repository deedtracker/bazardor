<?php
require_once __DIR__ . '/includes/data.php';
$pageTitle = SITE_NAME . ' | দামের ইতিহাস (Price History)';
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
        /* Modern History Styling */
        body { background-color: #FDFCF8; }
        .history-hero {
            background: linear-gradient(135deg, #F0F7F2 0%, #DFF0E4 100%);
            padding: 6rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
        }
        .history-hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            color: #142416;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            letter-spacing: -0.02em;
            font-family: var(--font-bengali);
        }
        .history-hero p {
            font-size: 1.35rem;
            color: #4A5D4E;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.6;
            font-family: var(--font-bengali);
        }

        .history-section { padding: 6rem 1.5rem; border-bottom: 1px solid var(--border-color); background: #FFFFFF; }
        .history-container { max-width: 1000px; margin: 0 auto; font-family: var(--font-bengali); }
        
        .intro-text {
            font-size: 1.25rem;
            line-height: 1.8;
            color: #4A5D4E;
            text-align: center;
            margin-bottom: 4rem;
        }

        .history-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        @media (min-width: 768px) {
            .history-grid { grid-template-columns: 1fr 1fr; }
        }

        .history-card {
            background: #FDFCF8;
            border: 1px solid var(--border-color);
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .history-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(20, 36, 22, 0.05);
        }
        
        .history-icon {
            width: 56px;
            height: 56px;
            background: #FFFFFF;
            color: var(--primary);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border-color);
        }

        .history-card h3 {
            font-size: 1.4rem;
            font-weight: 700;
            color: #142416;
            margin-bottom: 1rem;
        }
        .history-card p {
            font-size: 1.05rem;
            line-height: 1.7;
            color: #4A5D4E;
        }

        .feature-box {
            margin-top: 4rem;
            background: linear-gradient(180deg, #F0F7F2 0%, #FFFFFF 100%);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .feature-box h3 { font-size: 1.75rem; color: #142416; margin-bottom: 1rem; }
        .feature-box p { font-size: 1.15rem; color: #4A5D4E; margin-bottom: 1.5rem; max-width: 700px; }
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
    <header class="history-hero">
        <h1>দামের ইতিহাস</h1>
        <p>বিগত দিনের বাজারদরের সম্পূর্ণ আর্কাইভ, যা আপনাকে বুদ্ধিমান ক্রেতা হতে সাহায্য করবে।</p>
    </header>

    <!-- History Section -->
    <section class="history-section">
        <div class="history-container">
            <p class="intro-text">
                বর্তমান দামটি স্বাভাবিক নাকি অস্বাভাবিকভাবে বেশি, তা বোঝার একমাত্র উপায় হলো অতীতের দামের সাথে তুলনা করা। বাজারদর (BazarDor) আপনার জন্য প্রতিটি পণ্যের ডেটা সংরক্ষণ করে রাখে।
            </p>
            
            <div class="history-grid">
                <div class="history-card">
                    <div class="history-icon"><i class="ph ph-database"></i></div>
                    <h3>দামের সংরক্ষণাগার</h3>
                    <p>আমাদের প্ল্যাটফর্ম প্রতিটি পণ্যের প্রতিদিনের দাম সতর্কতার সাথে ডাটাবেজে সংরক্ষণ করে। এই আর্কাইভের মাধ্যমে আপনি যেকোনো পণ্যের গত এক সপ্তাহ, এক মাস বা এক বছরের দামের ওঠানামার ইতিহাস দেখতে পারবেন।</p>
                </div>

                <div class="history-card">
                    <div class="history-icon"><i class="ph ph-chart-line-up"></i></div>
                    <h3>কীভাবে দামের ইতিহাস দেখবেন?</h3>
                    <p>আমাদের হোমপেজ থেকে যেকোনো পণ্যের নামের ওপর ক্লিক করলেই ওই পণ্যের বিস্তারিত পাতা (Product Details) ওপেন হবে। সেখানে একটি ইন্টারেক্টিভ চার্ট বা গ্রাফের মাধ্যমে আপনি সহজেই দেখতে পারবেন ওই পণ্যটির দাম গত কিছুদিনে কীভাবে পরিবর্তিত হয়েছে।</p>
                </div>

                <div class="history-card">
                    <div class="history-icon"><i class="ph ph-magnifying-glass"></i></div>
                    <h3>প্যাটার্ন শনাক্তকরণ</h3>
                    <p>দামের ইতিহাস জানা থাকলে আপনি সহজেই বুঝতে পারবেন বর্তমান দামটি কেন বাড়ছে বা কমছে। এছাড়া উৎসবের সময় (যেমন: রমজান বা ঈদ) বা কোনো বিশেষ মৌসুমে পণ্যের দাম কীভাবে বাড়ে বা কমে, তার একটি পরিষ্কার ধারণা পাওয়া যায়।</p>
                </div>

                <div class="history-card">
                    <div class="history-icon"><i class="ph ph-brain"></i></div>
                    <h3>সঠিক সিদ্ধান্ত</h3>
                    <p>ঐতিহাসিক ডেটার উপর ভিত্তি করে আপনি সিদ্ধান্ত নিতে পারবেন আজই বেশি করে পণ্য কিনে রাখবেন, নাকি দাম কমার জন্য আরও কিছুদিন অপেক্ষা করবেন। এটি আপনার মাসের বাজার খরচ অনেকাংশে কমিয়ে আনতে সাহায্য করে।</p>
                </div>
            </div>

            <!-- Note Box -->
            <div class="feature-box">
                <i class="ph ph-calendar-check" style="font-size: 4rem; color: var(--primary); margin-bottom: 1rem;"></i>
                <h3>নির্ভুল ডেটাবেজ</h3>
                <p>আমাদের প্রতিটি সংরক্ষিত ডেটা আমাদের মাঠপর্যায়ের কর্মীদের দ্বারা সংগৃহীত এবং কোয়ালিটি কন্ট্রোল টিম দ্বারা যাচাইকৃত। আমরা ডেটার শতভাগ স্বচ্ছতা ও নির্ভুলতা নিশ্চিত করতে বদ্ধপরিকর।</p>
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

