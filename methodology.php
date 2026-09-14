<?php
require_once __DIR__ . '/includes/data.php';
$pageTitle = SITE_NAME . ' | পদ্ধতি (Methodology)';
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
        /* Modern Methodology Styling */
        body { background-color: #FDFCF8; }
        .method-hero {
            background: linear-gradient(135deg, #F0F7F2 0%, #DFF0E4 100%);
            padding: 6rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
        }
        .method-hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            color: #142416;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            letter-spacing: -0.02em;
            font-family: var(--font-bengali);
        }
        .method-hero p {
            font-size: 1.35rem;
            color: #4A5D4E;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.6;
            font-family: var(--font-bengali);
        }

        .method-section { padding: 6rem 1.5rem; border-bottom: 1px solid var(--border-color); }
        .method-section.bg-white { background: #FFFFFF; }
        
        .method-container { max-width: 1100px; margin: 0 auto; font-family: var(--font-bengali); }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: #142416;
            margin-bottom: 1.5rem;
            line-height: 1.3;
            text-align: center;
        }
        .section-desc {
            font-size: 1.2rem;
            line-height: 1.8;
            color: #4A5D4E;
            margin-bottom: 4rem;
            text-align: center;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Timeline / Steps Grid */
        .steps-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        @media (min-width: 768px) {
            .steps-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (min-width: 1024px) {
            .steps-grid { grid-template-columns: repeat(4, 1fr); }
        }

        .step-card {
            background: #FFFFFF;
            border: 1px solid var(--border-color);
            padding: 2.5rem 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(20, 36, 22, 0.03);
            position: relative;
            z-index: 1;
            transition: transform 0.3s;
        }
        .step-card:hover { transform: translateY(-5px); }
        
        .step-number {
            width: 48px;
            height: 48px;
            background: var(--primary);
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 10px rgba(47, 107, 67, 0.3);
        }

        .step-card h3 {
            font-size: 1.4rem;
            font-weight: 700;
            color: #142416;
            margin-bottom: 1rem;
        }
        .step-card p {
            font-size: 1.05rem;
            line-height: 1.6;
            color: #4A5D4E;
        }
        .step-time {
            display: inline-block;
            background: #F0F7F2;
            color: var(--primary);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        /* Feature Row */
        .feature-row {
            display: grid;
            grid-template-columns: 1fr;
            gap: 3rem;
            align-items: center;
            margin-top: 4rem;
        }
        @media (min-width: 860px) {
            .feature-row { grid-template-columns: 1fr 1fr; }
        }
        .feature-content h3 { font-size: 2rem; color: #142416; margin-bottom: 1rem; font-weight: 700; }
        .feature-content p { font-size: 1.15rem; color: #4A5D4E; line-height: 1.8; margin-bottom: 1.5rem; }
        
        .check-list { list-style: none; padding: 0; }
        .check-list li {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            font-size: 1.1rem;
            color: #142416;
            margin-bottom: 1rem;
            font-weight: 500;
        }
        .check-list i { color: var(--primary); font-size: 1.5rem; margin-top: 2px; }
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
    <header class="method-hero">
        <h1>আমাদের কার্যপদ্ধতি</h1>
        <p>ভোর থেকে শুরু করে আপনার টেবিলে বাজারের সঠিক হিসাব পৌঁছে দেওয়া পর্যন্ত—কীভাবে আমরা নিখুঁত ও বাস্তব বাজারদর নিশ্চিত করি, তার স্বচ্ছ চিত্র।</p>
    </header>

    <!-- Step by Step Section -->
    <section class="method-section bg-white">
        <div class="method-container">
            <h2 class="section-title">প্রতিদিনের কর্মযজ্ঞ</h2>
            <p class="section-desc">বাজারদর প্ল্যাটফর্মটি সম্পূর্ণভাবে নির্ভুল ও যাচাইকৃত তথ্যের উপর ভিত্তি করে তৈরি। আমাদের তথ্য সংগ্রহের পদ্ধতি অত্যন্ত স্বচ্ছ এবং সুশৃঙ্খল। প্রতিদিন সকালে বাজার শুরু হওয়ার সাথে সাথে আমাদের প্রতিনিধিরা কাজ শুরু করেন।</p>
            
            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">১</div>
                    <span class="step-time">ভোর ৫টা - ৬টা</span>
                    <h3>বাজারে উপস্থিতি</h3>
                    <p>কারওয়ান বাজারসহ দেশের প্রধান প্রধান খুচরা বাজারগুলোতে আমাদের প্রতিনিধিরা ভোরবেলাতেই অবস্থান নেন।</p>
                </div>
                <div class="step-card">
                    <div class="step-number">২</div>
                    <span class="step-time">সকাল ৬টা - ৭টা</span>
                    <h3>ক্রেতার বেশে যাচাই</h3>
                    <p>আমাদের প্রতিনিধিরা সাধারণ ক্রেতার ছদ্মবেশে বিভিন্ন দোকান ঘুরে দাম যাচাই করেন। কোনো পাইকারি আড়তদারের কাছে না গিয়ে, সরাসরি খুচরা বিক্রেতার কাছে দাম শোনা হয়।</p>
                </div>
                <div class="step-card">
                    <div class="step-number">৩</div>
                    <span class="step-time">সকাল ৭টা - ৭:৩০</span>
                    <h3>গড় মূল্য ও ক্রস-চেক</h3>
                    <p>একটি বাজারের অন্তত ৪-৫টি দোকানের দাম সংগ্রহ করার পর, আমাদের কোয়ালিটি কন্ট্রোল টিম সেই দামগুলোর গড় (Average) করে সবচেয়ে বাস্তব দামটি নির্ধারণ করে।</p>
                </div>
                <div class="step-card">
                    <div class="step-number">৪</div>
                    <span class="step-time">সকাল ৮টা</span>
                    <h3>ওয়েবসাইটে লাইভ</h3>
                    <p>যাচাই-বাছাই সম্পন্ন হওয়ার পর ঠিক সকাল ৮টার মধ্যে ওয়েবসাইট ও অ্যাপে আজকের সর্বশেষ বাজারদরটি পাবলিশ করা হয়।</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Retail vs Wholesale -->
    <section class="method-section">
        <div class="method-container">
            <div class="feature-row">
                <div class="feature-content" style="order: 2;">
                    <h3>কেন আমরা পাইকারি দাম দেখাই না?</h3>
                    <p>টেলিভিশনের সংবাদ বা অনেক প্ল্যাটফর্ম প্রায়ই কারওয়ান বাজারের আড়তের পাইকারি দর প্রকাশ করে। আপনি যখন খবর দেখে বাজারে যান, দেখেন দামের বিশাল পার্থক্য! কারণ খুচরা বাজারে পরিবহন খরচ, দোকান ভাড়া এবং বিক্রেতার লাভ যুক্ত হয়।</p>
                    <p>এই অসামঞ্জস্যতা দূর করতেই আমরা শুধুমাত্র খুচরা (Retail) দাম সংগ্রহ করি।</p>
                    <ul class="check-list">
                        <li><i class="ph ph-check-circle"></i> ক্রেতা হিসেবে আপনি ঠিক যে দামে কিনবেন।</li>
                        <li><i class="ph ph-check-circle"></i> দোকানদারের সাথে দরদামের সঠিক ধারণা।</li>
                        <li><i class="ph ph-check-circle"></i> বাজারের প্রকৃত মূল্যস্ফীতির চিত্র।</li>
                    </ul>
                </div>
                <div style="order: 1; display: flex; justify-content: center;">
                    <div style="background: white; border-radius: 24px; padding: 3rem; text-align: center; border: 1px solid var(--border-color); box-shadow: 0 20px 40px rgba(0,0,0,0.05); width: 100%;">
                        <i class="ph ph-scales" style="font-size: 5rem; color: var(--primary); margin-bottom: 1.5rem;"></i>
                        <h4 style="font-size: 1.5rem; color: #142416; margin-bottom: 1rem;">পাইকারি বনাম খুচরা</h4>
                        <p style="color: #4A5D4E; font-size: 1.1rem; line-height: 1.6;">আমরা সাধারণ মানুষের প্ল্যাটফর্ম। তাই আমরা বড় ব্যবসায়ীর হিসাব নয়, বরং সাধারণ মানুষের পকেটের হিসাবকে প্রাধান্য দিই।</p>
                    </div>
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

