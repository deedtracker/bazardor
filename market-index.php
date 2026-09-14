<?php
require_once __DIR__ . '/includes/data.php';
$pageTitle = SITE_NAME . ' | মার্কেট ইনডেক্স (Market Index)';
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
        /* Modern Index Styling */
        body { background-color: #FDFCF8; }
        .index-hero {
            background: linear-gradient(135deg, #F0F7F2 0%, #DFF0E4 100%);
            padding: 6rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
        }
        .index-hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            color: #142416;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            letter-spacing: -0.02em;
            font-family: var(--font-bengali);
        }
        .index-hero p {
            font-size: 1.35rem;
            color: #4A5D4E;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.6;
            font-family: var(--font-bengali);
        }

        .index-section { padding: 6rem 1.5rem; border-bottom: 1px solid var(--border-color); background: #FFFFFF; }
        .index-container { max-width: 1000px; margin: 0 auto; font-family: var(--font-bengali); }
        
        .intro-text {
            font-size: 1.25rem;
            line-height: 1.8;
            color: #4A5D4E;
            text-align: center;
            margin-bottom: 4rem;
        }

        .index-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        @media (min-width: 768px) {
            .index-grid { grid-template-columns: 1fr 1fr; }
        }

        .index-card {
            background: #FDFCF8;
            border: 1px solid var(--border-color);
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .index-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(20, 36, 22, 0.05);
        }
        
        .index-icon {
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

        .index-card h3 {
            font-size: 1.4rem;
            font-weight: 700;
            color: #142416;
            margin-bottom: 1rem;
        }
        .index-card p {
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
    <header class="index-hero">
        <h1>মার্কেট ইনডেক্স</h1>
        <p>শেয়ার বাজারের মতো দৈনন্দিন বাজারের মূল্যস্ফীতি বোঝার সবচেয়ে আধুনিক, বৈজ্ঞানিক ও সহজ পরিমাপক।</p>
    </header>

    <!-- Index Section -->
    <section class="index-section">
        <div class="index-container">
            <p class="intro-text">
                সাধারণত আমরা বাজারে গিয়ে কোনো একটি নির্দিষ্ট পণ্যের দাম বাড়লে হতাশ হয়ে যাই বা ভাবি পুরো বাজারই চড়া। কিন্তু <strong>মার্কেট ইনডেক্স</strong> আপনাকে পুরো বাজারের একটি সার্বিক চিত্র দেয়, যা আপনাকে বুদ্ধিমান ক্রেতা হতে সাহায্য করবে।
            </p>
            
            <div class="index-grid">
                <div class="index-card">
                    <div class="index-icon"><i class="ph ph-chart-polar"></i></div>
                    <h3>মার্কেট ইনডেক্স কী?</h3>
                    <p>বাজারদর মার্কেট ইনডেক্স (BazarDor Market Index) হলো দৈনন্দিন বাজারদর ওঠানামা বোঝার একটি সার্বিক সূচক। এটি মূলত দৈনন্দিন প্রয়োজনীয় নিত্যপণ্যের (যেমন: চাল, ডাল, তেল, সবজি, মাছ ও মাংস) গড় দামের ওপর ভিত্তি করে তৈরি করা হয়।</p>
                </div>

                <div class="index-card">
                    <div class="index-icon"><i class="ph ph-math-operations"></i></div>
                    <h3>এটি কীভাবে কাজ করে?</h3>
                    <p>প্রতিদিন আমাদের প্রতিনিধিরা বাজার থেকে যে খুচরা দাম সংগ্রহ করেন, তার ওপর ভিত্তি করে একটি গাণিতিক গড় (Average) বের করা হয়। প্রতিটি পণ্যের গুরুত্ব অনুযায়ী ওজন (Weight) নির্ধারণ করে এই সূচকটি নিখুঁতভাবে হিসাব করা হয়।</p>
                </div>

                <div class="index-card">
                    <div class="index-icon"><i class="ph ph-trend-up"></i></div>
                    <h3>উর্ধ্বমুখী সূচক (Upward Trend)</h3>
                    <p>যদি আজকের ইনডেক্স গতকালের চেয়ে বেশি হয়, তবে বুঝতে হবে বাজারে সার্বিকভাবে মূল্যস্ফীতি বা দাম বেড়েছে। এর মানে হলো, আজ বাজারে গেলে গত কয়েকদিনের তুলনায় আপনার পকেটে বেশি চাপ পড়তে পারে।</p>
                </div>

                <div class="index-card">
                    <div class="index-icon"><i class="ph ph-trend-down"></i></div>
                    <h3>নিম্নমুখী সূচক (Downward Trend)</h3>
                    <p>আর যদি ইনডেক্স কমে যায়, তবে বুঝতে হবে সাধারণ ক্রেতাদের জন্য বাজারের পরিস্থিতি কিছুটা স্বস্তিদায়ক। এর মানে হলো কয়েকটি নির্দিষ্ট পণ্যের দাম বাড়লেও, সার্বিকভাবে বাজারের বেশিরভাগ পণ্যের দাম কমেছে।</p>
                </div>
            </div>

            <!-- Coming Soon Box -->
            <div class="feature-box">
                <i class="ph ph-presentation-chart" style="font-size: 4rem; color: var(--primary); margin-bottom: 1rem;"></i>
                <h3>খুব শীঘ্রই আসছে লাইভ গ্রাফ</h3>
                <p>খুব শীঘ্রই আমরা এই মার্কেট ইনডেক্স ফিচারটি সবার জন্য উন্মুক্ত করতে যাচ্ছি, যেখানে আপনি ইন্টারেক্টিভ গ্রাফের মাধ্যমে গত কয়েক সপ্তাহের বা মাসের বাজারের সার্বিক অবস্থার একটি স্পষ্ট চিত্র দেখতে পারবেন।</p>
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

