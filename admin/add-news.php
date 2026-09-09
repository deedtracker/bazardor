<?php
// =============================================
// BazarDor — Admin: Add News (URL Scraper)
// =============================================
session_start();
require_once __DIR__ . '/../includes/data.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$products = getAllProducts();
$success = '';
$error = '';
$scrapedResult = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = (int)($_POST['product_id'] ?? 0);
    $url = trim($_POST['news_url'] ?? '');

    if ($productId <= 0) {
        $error = 'পণ্য নির্বাচন করুন।';
    } elseif (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
        $error = 'সঠিক URL দিন।';
    } else {
        $result = addNewsFromUrl($productId, $url);
        if ($result) {
            $success = 'খবর সফলভাবে যোগ হয়েছে!';
            $scrapedResult = $result;
        } else {
            $error = 'URL থেকে টাইটেল আনতে পারিনি। URL টি চেক করুন।';
        }
    }
}

// Get recent news
$db = getDB();
$recentNews = $db->query('
    SELECT n.*, p.name AS product_name
    FROM news n
    JOIN products p ON n.product_id = p.id
    ORDER BY n.date_added DESC, n.id DESC
    LIMIT 20
')->fetchAll();
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>খবর যোগ করুন | <?= e(SITE_NAME) ?> অ্যাডমিন</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
</head>
<body>
    <div class="admin-wrapper">
        <header class="admin-header">
            <div class="admin-header-inner">
                <div class="admin-header-left">
                    <a href="dashboard.php" class="admin-back-link"><span class="material-icons-round">arrow_back</span></a>
                    <h1>খবর যোগ করুন</h1>
                </div>
                <div class="admin-header-right">
                    <a href="logout.php" class="admin-btn outline small">লগআউট</a>
                </div>
            </div>
        </header>

        <main class="admin-main">
            <?php if ($success): ?>
                <div class="admin-alert success">
                    <?= e($success) ?>
                    <?php if ($scrapedResult): ?>
                        <div class="scraped-preview">
                            <strong>টাইটেল:</strong> <?= e($scrapedResult['title']) ?><br>
                            <strong>সোর্স:</strong> <?= e($scrapedResult['source']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="admin-alert error"><?= e($error) ?></div>
            <?php endif; ?>

            <div class="admin-form-card">
                <h2 class="admin-form-title">নিউজ URL দিন</h2>
                <p class="admin-form-hint">একটি খবরের URL পেস্ট করুন। টাইটেল ও সোর্স অটোমেটিক আসবে।</p>

                <form method="POST" class="admin-form">
                    <div class="form-group">
                        <label for="product_id">কোন পণ্যের খবর?</label>
                        <select name="product_id" id="product_id" required>
                            <option value="">-- পণ্য বাছাই করুন --</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= e($p['name']) ?> (<?= e($p['name_en']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="news_url">নিউজ URL</label>
                        <input type="url" name="news_url" id="news_url" required
                               placeholder="https://www.prothomalo.com/economy/..."
                               class="url-input">
                    </div>
                    <button type="submit" class="admin-btn primary full-width">
                        <span class="material-icons-round">auto_awesome</span>
                        অটো-ফেচ করে সেভ করুন
                    </button>
                </form>
            </div>

            <!-- Recent News Table -->
            <?php if (!empty($recentNews)): ?>
            <div class="admin-section">
                <h2 class="admin-section-title">সাম্প্রতিক খবর</h2>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>পণ্য</th>
                                <th>টাইটেল</th>
                                <th>সোর্স</th>
                                <th>তারিখ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentNews as $n): ?>
                            <tr>
                                <td><?= e($n['product_name']) ?></td>
                                <td>
                                    <?php if (!empty($n['url'])): ?>
                                        <a href="<?= e($n['url']) ?>" target="_blank" rel="noopener"><?= e($n['title']) ?></a>
                                    <?php else: ?>
                                        <?= e($n['title']) ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= e($n['source']) ?></td>
                                <td><?= toBengaliDateShort($n['date_added']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
