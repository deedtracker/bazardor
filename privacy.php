<?php
require_once __DIR__ . '/includes/data.php';
$pageTitle = SITE_NAME . ' | প্রাইভেসি পলিসি (Privacy Policy)';
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
        /* Modern Privacy Policy Styling */
        body { background-color: #FDFCF8; }
        .privacy-hero {
            background: linear-gradient(135deg, #F0F7F2 0%, #DFF0E4 100%);
            padding: 6rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
        }
        .privacy-hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            color: #142416;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            letter-spacing: -0.02em;
            font-family: var(--font-bengali);
        }
        .privacy-hero p {
            font-size: 1.35rem;
            color: #4A5D4E;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.6;
            font-family: var(--font-bengali);
        }

        .privacy-section { padding: 6rem 1.5rem; border-bottom: 1px solid var(--border-color); background: #FFFFFF; }
        .privacy-container { max-width: 1000px; margin: 0 auto; font-family: var(--font-bengali); }
        
        .intro-text {
            font-size: 1.25rem;
            line-height: 1.8;
            color: #4A5D4E;
            text-align: center;
            margin-bottom: 4rem;
        }

        .policy-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        @media (min-width: 768px) {
            .policy-grid { grid-template-columns: 1fr 1fr; }
        }

        .policy-card {
            background: #FDFCF8;
            border: 1px solid var(--border-color);
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .policy-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(20, 36, 22, 0.05);
        }
        
        .policy-icon {
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

        .policy-card h3 {
            font-size: 1.4rem;
            font-weight: 700;
            color: #142416;
            margin-bottom: 1rem;
        }
        .policy-card p {
            font-size: 1.05rem;
            line-height: 1.7;
            color: #4A5D4E;
        }

        .contact-box {
            margin-top: 4rem;
            background: linear-gradient(180deg, #FDFCF8 0%, #FFFFFF 100%);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 3rem;
            text-align: center;
        }
        .contact-box h3 { font-size: 1.75rem; color: #142416; margin-bottom: 1rem; }
        .contact-box p { font-size: 1.15rem; color: #4A5D4E; margin-bottom: 1.5rem; }
        .contact-email {
            display: inline-block;
            background: #F0F7F2;
            color: var(--primary);
            padding: 0.75rem 1.5rem;
            border-radius: 30px;
            font-weight: 600;
            font-size: 1.1rem;
            text-decoration: none;
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
    <header class="privacy-hero">
        <h1>প্রাইভেসি পলিসি</h1>
        <p>আপনার তথ্যের সর্বোচ্চ গোপনীয়তা রক্ষায় আমরা প্রতিশ্রুতিবদ্ধ। আমাদের প্ল্যাটফর্মটি কীভাবে কাজ করে এবং আপনার গোপনীয়তা কীভাবে সুরক্ষিত থাকে, তা নিচে বিস্তারিত দেওয়া হলো।</p>
    </header>

    <!-- Policy Section -->
    <section class="privacy-section">
        <div class="privacy-container">
            <p class="intro-text">
                বাজারদর (BazarDor) বিশ্বাস করে যে, আপনার ব্যক্তিগত তথ্যের নিরাপত্তা সবার আগে। আমরা অত্যন্ত স্বচ্ছ একটি পলিসি মেনে চলি যাতে আপনি সম্পূর্ণ নিশ্চিন্তে আমাদের প্ল্যাটফর্ম থেকে প্রতিদিনের বাজারদর যাচাই করতে পারেন।
            </p>
            
            <div class="policy-grid">
                <div class="policy-card">
                    <div class="policy-icon"><i class="ph ph-shield-check"></i></div>
                    <h3>ব্যক্তিগত তথ্য সংগ্রহ</h3>
                    <p>আমাদের ওয়েবসাইট ব্যবহার করার সময় আমরা আপনার কোনো নাম, ঠিকানা বা মোবাইল নম্বর সংগ্রহ করি না। আমাদের মূল লক্ষ্য হলো বাজারের সঠিক দাম বিনামূল্যে আপনাদের কাছে পৌঁছে দেওয়া। শুধুমাত্র বিশেষ সেবা (যেমন: এসএমএস এলার্ট) চালুর ক্ষেত্রে ব্যবহারকারীর সম্মতিক্রমে নম্বর নেওয়া হতে পারে।</p>
                </div>

                <div class="policy-card">
                    <div class="policy-icon"><i class="ph ph-cookie"></i></div>
                    <h3>কুকিজ (Cookies) ব্যবহার</h3>
                    <p>আপনার ব্রাউজিং অভিজ্ঞতাকে দ্রুত ও উন্নত করতে এবং আমাদের ওয়েবসাইটের ট্রাফিক বিশ্লেষণ করতে আমরা কিছু সাধারণ কুকিজ ব্যবহার করি। তবে এই কুকিজের মাধ্যমে কোনোভাবেই আপনার ব্যক্তিগত পরিচয় সনাক্ত করা সম্ভব নয়।</p>
                </div>

                <div class="policy-card">
                    <div class="policy-icon"><i class="ph ph-link"></i></div>
                    <h3>তৃতীয় পক্ষের লিংক</h3>
                    <p>তথ্য প্রদানের সুবিধার্থে আমাদের ওয়েবসাইটে মাঝে মাঝে অন্যান্য নির্ভরযোগ্য ওয়েবসাইটের বা নিউজের লিংক থাকতে পারে। তবে তৃতীয় পক্ষের ওয়েবসাইটের প্রাইভেসি পলিসি বা তাদের কন্টেন্টের ব্যাপারে আমরা কোনো দায়ভার বহন করি না।</p>
                </div>

                <div class="policy-card">
                    <div class="policy-icon"><i class="ph ph-arrows-clockwise"></i></div>
                    <h3>নীতিমালা পরিবর্তন</h3>
                    <p>ব্যবহারকারীদের আরো ভালো সেবা দেওয়ার লক্ষ্যে বাজারদর কর্তৃপক্ষ যেকোনো সময় এই প্রাইভেসি পলিসি আপডেট করার অধিকার সংরক্ষণ করে। কোনো বড় পরিবর্তন আনা হলে তা আমাদের ওয়েবসাইটে বিজ্ঞপ্তির মাধ্যমে স্পষ্টভাবে জানিয়ে দেওয়া হবে।</p>
                </div>
            </div>

            <!-- Contact Box -->
            <div class="contact-box">
                <i class="ph ph-envelope-simple-open" style="font-size: 3rem; color: var(--primary); margin-bottom: 1rem;"></i>
                <h3>কোনো প্রশ্ন আছে কি?</h3>
                <p>আমাদের প্রাইভেসি পলিসি বা ব্যক্তিগত তথ্যের নিরাপত্তা সম্পর্কে আপনার যদি কোনো প্রশ্ন বা মতামত থাকে, তবে আমাদের সাথে নির্দ্বিধায় যোগাযোগ করুন।</p>
                <a href="mailto:support@bazardor.app" class="contact-email">support@bazardor.app</a>
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

