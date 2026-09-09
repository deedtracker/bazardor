<?php
require_once __DIR__ . '/includes/config.php';
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>পেজ পাওয়া যায়নি - 404 | <?= e(SITE_NAME) ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tiro+Bangla:ital@0;1&display=swap" rel="stylesheet">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
            flex-direction: column;
            background-color: var(--background);
        }
        .error-code {
            font-size: 6rem;
            font-weight: bold;
            color: var(--primary);
            margin: 0;
            line-height: 1;
        }
        .error-title {
            font-size: 2rem;
            margin: 10px 0 20px;
            color: var(--text-dark);
        }
        .error-desc {
            color: var(--text-muted);
            margin-bottom: 30px;
        }
        .home-btn {
            background: var(--primary);
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 500;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(22, 163, 74, 0.3);
        }
        .home-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(22, 163, 74, 0.4);
        }
    </style>
</head>
<body>
    <h1 class="error-code">404</h1>
    <h2 class="error-title">পেজটি পাওয়া যায়নি</h2>
    <p class="error-desc">আপনি যে পেজটি খুঁজছেন তা মুছে ফেলা হয়েছে অথবা লিংকটি ভুল।</p>
    <a href="/" class="home-btn">হোমপেজে ফিরে যান</a>
</body>
</html>
