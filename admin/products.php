<?php
// =============================================
// BazarDor — Admin Product List
// =============================================
session_start();
require_once __DIR__ . '/../includes/data.php';

// Auth check
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$db = getDB();

// Handle deletion
if (isset($_POST['delete_product_id'])) {
    $id = (int)$_POST['delete_product_id'];
    $db->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
    header('Location: products.php?deleted=1');
    exit;
}

$stmt = $db->query("
    SELECT p.*, c.name AS category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.name ASC
");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>পণ্য ম্যানেজ | <?= e(SITE_NAME) ?> অ্যাডমিন</title>
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
</head>
<body>
    <div class="admin-wrapper">
        <header class="admin-header">
            <div class="admin-header-inner">
                <div class="admin-header-left">
                    <a href="dashboard.php" class="back-link"><span class="material-icons-round">arrow_back</span></a>
                    <h1>পণ্য ম্যানেজ</h1>
                </div>
                <div class="admin-header-right">
                    <a href="add-product.php" class="admin-btn small">নতুন পণ্য যোগ করুন</a>
                </div>
            </div>
        </header>

        <main class="admin-main">
            <div class="admin-section">
                <?php if(isset($_GET['success'])): ?>
                    <div style="background: #dcfce7; color: #166534; padding: 10px; border-radius: 8px; margin-bottom: 20px;">
                        সফলভাবে সংরক্ষিত হয়েছে!
                    </div>
                <?php endif; ?>
                <?php if(isset($_GET['deleted'])): ?>
                    <div style="background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 8px; margin-bottom: 20px;">
                        পণ্য ডিলিট করা হয়েছে!
                    </div>
                <?php endif; ?>

                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>পণ্য</th>
                                <th>ক্যাটাগরি</th>
                                <th>ইউনিট</th>
                                <th>ধরণ (Mode)</th>
                                <th>পার্থক্য (Difference)</th>
                                <th>অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $p): ?>
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
                                    <?php if(($p['pricing_mode'] ?? 'variable') === 'flat'): ?>
                                        <span style="background: #eef2ff; color: #4f46e5; padding: 2px 8px; border-radius: 10px; font-size: 12px; border: 1px solid #c7d2fe;">Flat</span>
                                    <?php else: ?>
                                        <span style="background: #f3f4f6; color: #4b5563; padding: 2px 8px; border-radius: 10px; font-size: 12px; border: 1px solid #d1d5db;">Variable</span>
                                    <?php endif; ?>
                                </td>
                                <td>± <?= e($p['price_difference'] ?? 5) ?> ৳</td>
                                <td>
                                    <a href="edit-product.php?id=<?= $p['id'] ?>" class="admin-btn small outline">এডিট</a>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('আপনি কি নিশ্চিত? এই পণ্যের সব দাম ডিলিট হয়ে যাবে।');">
                                        <input type="hidden" name="delete_product_id" value="<?= $p['id'] ?>">
                                        <button type="submit" class="admin-btn small outline" style="color: #dc2626; border-color: #fca5a5;">ডিলিট</button>
                                    </form>
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
