<?php
// =============================================
// BazarDor — Admin Add Product
// =============================================
session_start();
require_once __DIR__ . '/../includes/data.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$db = getDB();
$categories = getAllCategories();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $name_en = trim($_POST['name_en'] ?? '');
    $unit = trim($_POST['unit'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $pricing_mode = $_POST['pricing_mode'] === 'flat' ? 'flat' : 'variable';
    $price_difference = (int)($_POST['price_difference'] ?? 5);

    if (empty($name) || empty($name_en) || empty($unit) || empty($category_id)) {
        $error = 'সব তথ্য পূরণ করুন।';
    } else {
        $iconPath = 'assets/placeholder.png';

        // Image upload logic
        if (isset($_FILES['icon']) && $_FILES['icon']['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['icon']['tmp_name'];
            $imgInfo = getimagesize($tmpName);
            if ($imgInfo !== false) {
                // Generate WebP
                $newFilename = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name_en)) . '_' . time() . '.webp';
                $destPath = __DIR__ . '/../assets/' . $newFilename;
                
                $img = null;
                switch ($imgInfo[2]) {
                    case IMAGETYPE_JPEG: $img = imagecreatefromjpeg($tmpName); break;
                    case IMAGETYPE_PNG: $img = imagecreatefrompng($tmpName); break;
                    case IMAGETYPE_WEBP: $img = imagecreatefromwebp($tmpName); break;
                }

                if ($img) {
                    imagepalettetotruecolor($img);
                    imagewebp($img, $destPath, 80);
                    imagedestroy($img);
                    $iconPath = 'assets/' . $newFilename;
                }
            }
        }

        // Slug generation
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name_en));
        $slug = trim($slug, '-');

        // Check if slug exists to avoid duplicates
        $checkStmt = $db->prepare("SELECT id FROM products WHERE slug = ?");
        $checkStmt->execute([$slug]);
        if ($checkStmt->fetch()) {
            $slug = $slug . '-' . time();
        }

        // Insert DB
        $insertStmt = $db->prepare("
            INSERT INTO products (name, name_en, slug, unit, category_id, icon_path, pricing_mode, price_difference)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $insertStmt->execute([$name, $name_en, $slug, $unit, $category_id, $iconPath, $pricing_mode, $price_difference]);

        header('Location: products.php?success=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>নতুন পণ্য যোগ | <?= e(SITE_NAME) ?> অ্যাডমিন</title>
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <style>
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; }
        .form-control { width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; font-family: inherit; font-size: 1rem; }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <header class="admin-header">
            <div class="admin-header-inner">
                <div class="admin-header-left">
                    <a href="products.php" class="back-link"><span class="material-icons-round">arrow_back</span></a>
                    <h1>নতুন পণ্য যোগ করুন</h1>
                </div>
            </div>
        </header>

        <main class="admin-main">
            <div class="admin-section" style="max-width: 600px;">
                <?php if ($error): ?>
                    <div style="background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 8px; margin-bottom: 20px;">
                        <?= e($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>বাংলা নাম (উদাঃ আলু)</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>ইংরেজি নাম (উদাঃ Potato)</label>
                        <input type="text" name="name_en" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>ক্যাটাগরি</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">নির্বাচন করুন</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>ইউনিট (উদাঃ ১ কেজি)</label>
                        <input type="text" name="unit" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>পণ্যের ছবি (PNG/JPG/WEBP)</label>
                        <input type="file" name="icon" class="form-control" accept="image/png, image/jpeg, image/webp">
                        <small style="color: #6b7280; margin-top: 5px; display: block;">আপলোড করলে তা স্বয়ংক্রিয়ভাবে WebP তে কনভার্ট ও ছোট হয়ে যাবে।</small>
                    </div>

                    <div class="form-group" style="padding-top: 20px; border-top: 1px solid var(--border);">
                        <label>প্রাইসিং মোড (Pricing Mode)</label>
                        <select name="pricing_mode" class="form-control">
                            <option value="variable">Variable (সাধারণ পণ্য)</option>
                            <option value="flat">Flat (সারা দেশে একই দাম, যেমন চাল/ডাল)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>পার্থক্য (Price Difference Taka)</label>
                        <input type="number" name="price_difference" class="form-control" value="5" min="0" required>
                        <small style="color: #6b7280; margin-top: 5px; display: block;">অটোমেশন এই পরিমাণ টাকা র‍্যান্ডমভাবে যোগ বা বিয়োগ করবে। Flat পণ্যের জন্য ১ বা ২ দিন, Variable এর জন্য ৫ বা ১০ দিন।</small>
                    </div>

                    <div class="form-group" style="margin-top: 30px;">
                        <button type="submit" class="admin-btn">সেভ করুন</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
