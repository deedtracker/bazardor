<?php
// =============================================
// BazarDor — Admin: Add Daily Prices
// =============================================
session_start();
require_once __DIR__ . '/../includes/data.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$products = getAllProducts();
$cities = getAllCities();
$todayStr = today();
$success = '';
$error = '';

// Pre-select product and date if passed via URL or form
$selectedProductId = (int)($_GET['product_id'] ?? 0);
$selectedDate = $_GET['date'] ?? $todayStr;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = (int)($_POST['product_id'] ?? 0);
    $date = $_POST['price_date'] ?? $todayStr;

    // Validate
    $basePrice = (float)($_POST['base_price'] ?? 0);
    
    if ($productId <= 0) {
        $error = 'পণ্য নির্বাচন করুন।';
    } elseif ($basePrice <= 0) {
        $error = 'সঠিক বেইজ প্রাইস দিন।';
    } else {
        try {
            saveDailyPrices($productId, $date, $basePrice);
            $success = 'দাম সফলভাবে সেভ হয়েছে! ৬৪টি জেলার দাম স্বয়ংক্রিয়ভাবে হিসাব হয়ে গেছে।';
            $selectedProductId = $productId;
            $selectedDate = $date;
        } catch (Exception $e) {
            $error = 'সেভ করতে সমস্যা হয়েছে: ' . $e->getMessage();
        }
    }
}

// Get existing average price to prefill the base_price field
$existingBasePrice = '';
if ($selectedProductId > 0) {
    $db = getDB();
    $stmt = $db->prepare('SELECT average_price FROM daily_prices WHERE product_id = ? AND price_date = ?');
    $stmt->execute([$selectedProductId, $selectedDate]);
    $dailyPrice = $stmt->fetch();

    if ($dailyPrice) {
        $existingBasePrice = $dailyPrice['average_price'];
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>দাম আপডেট | <?= e(SITE_NAME) ?> অ্যাডমিন</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
</head>
<body>
    <div class="admin-wrapper">
        <header class="admin-header">
            <div class="admin-header-inner">
                <div class="admin-header-left">
                    <a href="dashboard.php" class="admin-back-link"><span class="material-icons-round">arrow_back</span></a>
                    <h1>দাম আপডেট করুন</h1>
                </div>
                <div class="admin-header-right">
                    <a href="logout.php" class="admin-btn outline small">লগআউট</a>
                </div>
            </div>
        </header>

        <main class="admin-main">
            <?php if ($success): ?>
                <div class="admin-alert success"><?= e($success) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="admin-alert error"><?= e($error) ?></div>
            <?php endif; ?>

            <div class="admin-form-card">
                <form method="POST" class="admin-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="product_id">পণ্য নির্বাচন করুন</label>
                            <select name="product_id" id="product_id" required>
                                <option value="">-- পণ্য বাছাই করুন --</option>
                                <?php foreach ($products as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= $selectedProductId == $p['id'] ? 'selected' : '' ?>>
                                        <?= e($p['name']) ?> (<?= e($p['name_en']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="price_date">তারিখ</label>
                            <input type="date" name="price_date" id="price_date" value="<?= e($selectedDate) ?>" required onchange="loadExistingData()">
                        </div>
                    </div>

                    <h3 class="admin-form-section-title">বেইজ প্রাইস (টাকায়)</h3>
                    <p class="admin-form-hint">ঢাকার খুচরা দামটি (Dhaka Retail Price) এখানে দিন। এর ওপর ভিত্তি করে অ্যালগরিদম স্বয়ংক্রিয়ভাবে সোর্স হাব (যেমন: যশোর/বগুড়া) থেকে শুরু করে রিমোট এরিয়া (যেমন: সিলেট/বান্দরবান) পর্যন্ত ৬৪টি জেলার দাম হিসাব করে সেভ করবে।</p>

                    <div class="form-group" style="margin-bottom: 1.5rem; background: var(--surface); padding: 1rem; border-radius: 8px; border: 1px solid var(--border);">
                        <label for="base_price" style="color: var(--primary);">বেইজ প্রাইস</label>
                        <div class="input-with-prefix">
                            <span class="input-prefix">৳</span>
                            <input type="number" name="base_price" id="base_price" step="0.01" min="0" placeholder="যেমন: ১০০" required value="<?= e($existingBasePrice) ?>">
                        </div>
                    </div>

                    <button type="submit" class="admin-btn primary full-width">
                        <span class="material-icons-round">save</span>
                        সেভ করুন
                    </button>
                </form>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
    // Initialize searchable dropdown
    new TomSelect("#product_id", {
        create: false,
        placeholder: "-- পণ্য বাছাই করুন (খুঁজুন) --",
        onChange: function(value) {
            if(value) loadExistingData();
        }
    });

    function loadExistingData() {
        const productId = document.getElementById('product_id').value;
        const date = document.getElementById('price_date').value;
        if (productId && date) {
            window.location.href = `add-prices.php?product_id=${productId}&date=${date}`;
        }
    }
    </script>
</body>
</html>
