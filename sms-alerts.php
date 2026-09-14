<?php
require_once __DIR__ . '/includes/data.php';
$pageTitle = SITE_NAME . ' | এসএমএস এলার্ট (SMS Alerts)';
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
        /* Modern SMS Styling */
        body { background-color: #FDFCF8; }
        .sms-hero {
            background: linear-gradient(135deg, #F0F7F2 0%, #DFF0E4 100%);
            padding: 6rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
        }
        .sms-hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            color: #142416;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            letter-spacing: -0.02em;
            font-family: var(--font-bengali);
        }
        .sms-hero p {
            font-size: 1.35rem;
            color: #4A5D4E;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.6;
            font-family: var(--font-bengali);
        }

        .sms-section { padding: 6rem 1.5rem; border-bottom: 1px solid var(--border-color); background: #FFFFFF; }
        .sms-container { max-width: 1000px; margin: 0 auto; font-family: var(--font-bengali); }
        
        .intro-text {
            font-size: 1.25rem;
            line-height: 1.8;
            color: #4A5D4E;
            text-align: center;
            margin-bottom: 4rem;
        }

        .sms-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        @media (min-width: 768px) {
            .sms-grid { grid-template-columns: 1fr 1fr; }
        }

        .sms-card {
            background: #FDFCF8;
            border: 1px solid var(--border-color);
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .sms-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(20, 36, 22, 0.05);
        }
        
        .sms-icon {
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

        .sms-card h3 {
            font-size: 1.4rem;
            font-weight: 700;
            color: #142416;
            margin-bottom: 1rem;
        }
        .sms-card p {
            font-size: 1.05rem;
            line-height: 1.7;
            color: #4A5D4E;
        }

        .subscribe-box {
            margin-top: 4rem;
            background: linear-gradient(180deg, #F0F7F2 0%, #FFFFFF 100%);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 4rem 3rem;
            text-align: center;
        }
        .subscribe-box h3 { font-size: 2rem; color: #142416; margin-bottom: 1rem; font-weight: 700; }
        .subscribe-box p { font-size: 1.15rem; color: #4A5D4E; margin-bottom: 2.5rem; max-width: 700px; margin-left: auto; margin-right: auto; line-height: 1.6; }
        
        .coming-soon-badge {
            display: inline-block;
            background: #142416;
            color: #FFFFFF;
            padding: 0.5rem 1.5rem;
            border-radius: 30px;
            font-weight: 600;
            font-size: 1.1rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            box-shadow: 0 10px 20px rgba(20, 36, 22, 0.15);
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
    <header class="sms-hero">
        <h1>এসএমএস এলার্ট সার্ভিস</h1>
        <p>ইন্টারনেট সংযোগ না থাকলেও বাজারের প্রতিদিনের হালনাগাদ দাম এখন পৌঁছে যাবে সরাসরি আপনার মোবাইলে!</p>
    </header>

    <!-- SMS Section -->
    <section class="sms-section">
        <div class="sms-container">
            <p class="intro-text">
                আমরা জানি, সব সময় ইন্টারনেট সংযোগ থাকা সম্ভব নয়। বিশেষ করে যখন আপনি বাজারে থাকেন বা আপনার কাছে শুধু একটি বাটন ফোন থাকে। এই সমস্যার কথা মাথায় রেখেই বাজারদর (BazarDor) নিয়ে আসছে অত্যন্ত সাশ্রয়ী ও স্মার্ট এসএমএস এলার্ট সার্ভিস।
            </p>
            
            <div class="sms-grid">
                <div class="sms-card">
                    <div class="sms-icon"><i class="ph ph-bell-ringing"></i></div>
                    <h3>দৈনিক আপডেট</h3>
                    <p>প্রতিদিন সকাল ৯টার মধ্যে আপনার নির্বাচিত বা প্রয়োজনীয় পণ্যের সর্বশেষ দামের একটি সংক্ষিপ্ত এসএমএস পৌঁছে যাবে আপনার ইনবক্সে।</p>
                </div>

                <div class="sms-card">
                    <div class="sms-icon"><i class="ph ph-warning-circle"></i></div>
                    <h3>ব্রেকিং এলার্ট</h3>
                    <p>বাজারে হঠাৎ কোনো পণ্যের দাম অস্বাভাবিকভাবে বেড়ে গেলে বা কমে গেলে (যেমন: পেঁয়াজ বা ভোজ্যতেল), সাথে সাথে আপনি একটি ইনস্ট্যান্ট ব্রেকিং এলার্ট পাবেন।</p>
                </div>

                <div class="sms-card">
                    <div class="sms-icon"><i class="ph ph-device-mobile"></i></div>
                    <h3>ইন্টারনেট ছাড়াই আপডেট</h3>
                    <p>স্মার্টফোন বা ইন্টারনেট সংযোগ ছাড়াই বাজারের সাথে আপডেট থাকার এটি সবচেয়ে সহজ উপায়। সাধারণ বাটন ফোনেই এই সার্ভিসটি কাজ করবে।</p>
                </div>

                <div class="sms-card">
                    <div class="sms-icon"><i class="ph ph-sliders"></i></div>
                    <h3>কাস্টমাইজেশন</h3>
                    <p>আপনি চাইলেই নিজের প্রয়োজন অনুযায়ী পণ্যের তালিকা সেট করতে পারবেন। শুধুমাত্র যে পণ্যগুলোর দাম আপনি জানতে চান, ঠিক সেগুলোর আপডেটই পাঠানো হবে।</p>
                </div>
            </div>

            <!-- Subscribe Box -->
            <div class="subscribe-box">
                <i class="ph ph-chat-circle-text" style="font-size: 5rem; color: var(--primary); margin-bottom: 1.5rem;"></i>
                <h3>সার্ভিসটি এখনো ডেভেলপমেন্ট পর্যায়ে রয়েছে</h3>
                <p>খুব শীঘ্রই আমরা এই সেবাটি সবার জন্য উন্মুক্ত করতে যাচ্ছি। সার্ভিসটি চালু হওয়ার সাথে সাথেই আমাদের ওয়েবসাইট এবং ফেসবুক পেজের মাধ্যমে বিস্তারিত জানিয়ে দেওয়া হবে। আমাদের সাথেই থাকুন!</p>
                <div class="coming-soon-badge">Coming Soon</div>
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

