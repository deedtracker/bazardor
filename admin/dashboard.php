<?php
// =============================================
// BazarDor — Admin Dashboard
// =============================================
session_start();
require_once __DIR__ . '/../includes/data.php';

// Auth check
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$products = getAllProducts();
$categories = getAllCategories();
$cities = getAllCities();
$todayStr = today();

// Get today's prices status for each product
$db = getDB();
$statusStmt = $db->prepare('SELECT product_id FROM daily_prices WHERE price_date = ?');
$statusStmt->execute([$todayStr]);
$todayUpdated = array_column($statusStmt->fetchAll(), 'product_id');
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ড্যাশবোর্ড | <?= e(SITE_NAME) ?> অ্যাডমিন</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Admin Header -->
        <header class="admin-header">
            <div class="admin-header-inner">
                <div class="admin-header-left">
                    <span class="admin-logo-icon">🛒</span>
                    <h1><?= e(SITE_NAME) ?> <span class="admin-badge">অ্যাডমিন</span></h1>
                </div>
                <div class="admin-header-right">
                    <span class="admin-user"><?= e($_SESSION['admin_user']) ?></span>
                    <a href="logout.php" class="admin-btn outline small">লগআউট</a>
                </div>
            </div>
        </header>

        <main class="admin-main">
            <!-- Dashboard Stats -->
            <div class="admin-stats">
                <div class="admin-stat-card">
                    <div class="stat-icon green"><span class="material-icons-round">inventory_2</span></div>
                    <div class="stat-info">
                        <div class="stat-number"><?= toBengali(count($products)) ?></div>
                        <div class="stat-label">মোট পণ্য</div>
                    </div>
                </div>
                <div class="admin-stat-card">
                    <div class="stat-icon blue"><span class="material-icons-round">location_city</span></div>
                    <div class="stat-info">
                        <div class="stat-number"><?= toBengali(count($cities)) ?></div>
                        <div class="stat-label">এলাকা</div>
                    </div>
                </div>
                <div class="admin-stat-card">
                    <div class="stat-icon <?= count($todayUpdated) === count($products) ? 'green' : 'orange' ?>"><span class="material-icons-round">update</span></div>
                    <div class="stat-info">
                        <div class="stat-number"><?= toBengali(count($todayUpdated)) ?>/<?= toBengali(count($products)) ?></div>
                        <div class="stat-label">আজ আপডেট হয়েছে</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="admin-section">
                <h2 class="admin-section-title">দ্রুত কাজ</h2>
                <div class="admin-actions-grid">
                    <a href="add-prices.php" class="admin-action-card">
                        <span class="material-icons-round">edit_note</span>
                        <h3>দাম আপডেট</h3>
                        <p>আজকের বাজার দর ইনপুট করুন</p>
                    </a>
                    <a href="products.php" class="admin-action-card">
                        <span class="material-icons-round">inventory_2</span>
                        <h3>পণ্য ম্যানেজ</h3>
                        <p>নতুন পণ্য যোগ ও সেটিং পরিবর্তন</p>
                    </a>
                    <a href="add-news.php" class="admin-action-card">
                        <span class="material-icons-round">newspaper</span>
                        <h3>খবর যোগ করুন</h3>
                        <p>নিউজ URL দিন, অটোমেটিক টাইটেল</p>
                    </a>
                    <a href="generate-image.php" class="admin-action-card">
                        <span class="material-icons-round">image</span>
                        <h3>ডিজাইন জেনারেট</h3>
                        <p>ফেসবুক পোস্টের জন্য ছবি তৈরি করুন</p>
                    </a>
                </div>
            </div>

            <!-- Products Status Table -->
            <div class="admin-section">
                <h2 class="admin-section-title">পণ্য তালিকা ও আজকের স্ট্যাটাস</h2>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>পণ্য</th>
                                <th>ক্যাটাগরি</th>
                                <th>ইউনিট</th>
                                <th>আজকের দাম</th>
                                <th>স্ট্যাটাস</th>
                                <th>অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $p):
                                $isUpdated = in_array($p['id'], $todayUpdated);
                                $latest = getLatestPrice($p['id']);
                            ?>
                            <tr>
                                <td>
                                    <div class="admin-product-cell">
                                        <img src="/<?= e($p['icon_path']) ?>" alt="" class="admin-product-img">
                                        <strong><?= e($p['name']) ?></strong>
                                    </div>
                                </td>
                                <td><?= e($p['category_name']) ?></td>
                                <td><?= e($p['unit']) ?></td>
                                <td>
                                    <?php if ($latest): ?>
                                        ৳ <?= toBengali($latest['average_price']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($isUpdated): ?>
                                        <span class="admin-status-badge updated">✓ আপডেটেড</span>
                                    <?php else: ?>
                                        <span class="admin-status-badge pending">অপেক্ষমান</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="add-prices.php?product_id=<?= $p['id'] ?>" class="admin-btn small primary">দাম দিন</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
