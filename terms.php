<?php
require_once __DIR__ . '/includes/data.php';
$pageTitle = SITE_NAME . ' | শর্তাবলী (Terms of Service)';
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
        /* Modern Terms Styling */
        body { background-color: #FDFCF8; }
        .terms-hero {
            background: linear-gradient(135deg, #F0F7F2 0%, #DFF0E4 100%);
            padding: 6rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
        }
        .terms-hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            color: #142416;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            letter-spacing: -0.02em;
            font-family: var(--font-bengali);
        }
        .terms-hero p {
            font-size: 1.35rem;
            color: #4A5D4E;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.6;
            font-family: var(--font-bengali);
        }

        .terms-section { padding: 6rem 1.5rem; border-bottom: 1px solid var(--border-color); background: #FFFFFF; }
        .terms-container { max-width: 1000px; margin: 0 auto; font-family: var(--font-bengali); }
        
        .intro-text {
            font-size: 1.25rem;
            line-height: 1.8;
            color: #4A5D4E;
            text-align: center;
            margin-bottom: 4rem;
        }

        .terms-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        @media (min-width: 768px) {
            .terms-grid { grid-template-columns: 1fr 1fr; }
        }

        .terms-card {
            background: #FDFCF8;
            border: 1px solid var(--border-color);
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .terms-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(20, 36, 22, 0.05);
        }
        
        .terms-icon {
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

        .terms-card h3 {
            font-size: 1.4rem;
            font-weight: 700;
            color: #142416;
            margin-bottom: 1rem;
        }
        .terms-card p {
            font-size: 1.05rem;
            line-height: 1.7;
            color: #4A5D4E;
        }

        .legal-notice {
            margin-top: 4rem;
            background: #F0F7F2;
            border-left: 4px solid var(--primary);
            padding: 2rem;
            border-radius: 0 16px 16px 0;
            font-size: 1.15rem;
            color: #142416;
            line-height: 1.7;
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
    <header class="terms-hero">
        <h1>ব্যবহারের শর্তাবলী</h1>
        <p>আমাদের প্ল্যাটফর্মটি ব্যবহার করার পূর্বে সাধারণ নিয়মকানুন ও শর্তাবলী সম্পর্কে পরিষ্কার ধারণা থাকা জরুরি।</p>
    </header>

    <!-- Terms Section -->
    <section class="terms-section">
        <div class="terms-container">
            <p class="intro-text">
                বাজারদর (BazarDor) ওয়েবসাইট বা অ্যাপ ব্যবহার করার অর্থ হলো আপনি নিচে বর্ণিত শর্তাবলীতে সম্মতি জ্ঞাপন করেছেন। আমরা বিশ্বাস করি একটি স্বচ্ছ সম্পর্কের ভিত্তি হলো সঠিক বোঝাপড়া, তাই আমাদের নীতিমালা অত্যন্ত সহজ ভাষায় উপস্থাপন করা হলো।
            </p>
            
            <div class="terms-grid">
                <div class="terms-card">
                    <div class="terms-icon"><i class="ph ph-scroll"></i></div>
                    <h3>সাধারণ শর্তাবলী</h3>
                    <p>বাজারদর একটি উন্মুক্ত তথ্যভিত্তিক প্ল্যাটফর্ম। আমরা যেকোনো সময় ব্যবহারকারীদের আরো ভালো অভিজ্ঞতা দেওয়ার জন্য এই শর্তাবলী আপডেট করার অধিকার সংরক্ষণ করি। সাইট ব্যবহারের সময় সকল স্থানীয় আইন মেনে চলা আপনার দায়িত্ব।</p>
                </div>

                <div class="terms-card">
                    <div class="terms-icon"><i class="ph ph-scales"></i></div>
                    <h3>তথ্যের সঠিকতা ও দায়বদ্ধতা</h3>
                    <p>আমরা প্রতিদিন দেশের বিভিন্ন বাজার থেকে সরাসরি খুচরা বাজারদর সংগ্রহ করি। তবে বাজারদরের প্রকৃতি পরিবর্তনশীল হওয়ায়, অঞ্চল, সময় বা দোকানভেদে দামের কিছুটা তারতম্য হতে পারে। আমাদের দেওয়া তথ্য শুধুমাত্র একটি গাইডলাইন হিসেবে ব্যবহারের জন্য। কোনো আর্থিক লেনদেনে দামের অমিল হলে বাজারদর কর্তৃপক্ষ কোনোভাবেই দায়ী থাকবে না।</p>
                </div>

                <div class="terms-card">
                    <div class="terms-icon"><i class="ph ph-copyright"></i></div>
                    <h3>কপিরাইট ও মেধা স্বত্ব</h3>
                    <p>বাজারদর ওয়েবসাইটের ডিজাইন, লোগো, গ্রাফিক্স, সোর্স কোড এবং লেখাগুলোর সম্পূর্ণ কপিরাইট আমাদের কর্তৃপক্ষের সংরক্ষিত। আমাদের লিখিত অনুমতি ছাড়া কোনো কমার্শিয়াল বা বাণিজ্যিক কাজে এই সাইটের কন্টেন্ট, ডেটা বা ডিজাইন হুবহু নকল করা বা স্ক্র্যাপ (Scraping) করা সম্পূর্ণ আইনত দণ্ডনীয়।</p>
                </div>

                <div class="terms-card">
                    <div class="terms-icon"><i class="ph ph-lock-key"></i></div>
                    <h3>পরিষেবা বন্ধ বা স্থগিতকরণ</h3>
                    <p>আমাদের সার্ভার বা প্ল্যাটফর্মের অপব্যবহার, অতিরিক্ত রিকোয়েস্ট (Spamming) বা সাইবার আক্রমণের চেষ্টা করা হলে যেকোনো আইপি ঠিকানা বা ব্যবহারকারীকে কোনো পূর্ব ঘোষণা ছাড়াই চিরতরে ব্লক করার অধিকার বাজারদর কর্তৃপক্ষ সংরক্ষণ করে।</p>
                </div>
            </div>

            <!-- Legal Notice -->
            <div class="legal-notice">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 1rem;">
                    <i class="ph ph-warning-circle" style="font-size: 1.5rem; color: var(--primary);"></i>
                    <h3 style="font-size: 1.35rem; margin: 0;">বিশেষ দ্রষ্টব্য</h3>
                </div>
                <p style="margin: 0;">
                    এই শর্তাবলী বাংলাদেশের আইন অনুযায়ী পরিচালিত হবে। বাজারদরের উদ্দেশ্য কাউকে আর্থিকভাবে ক্ষতিগ্রস্ত করা নয়, বরং সঠিক তথ্য দিয়ে সাহায্য করা। প্ল্যাটফর্মটি ব্যবহারের মাধ্যমে আপনি আমাদের এই লক্ষ্যের সাথে একমত পোষণ করছেন।
                </p>
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

