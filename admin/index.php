<?php
// =============================================
// BazarDor — Admin Login Page
// =============================================
session_start();
require_once __DIR__ . '/../includes/data.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $user = verifyLogin($username, $password);
    if ($user) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_user'] = $user['username'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'ইউজারনেম অথবা পাসওয়ার্ড ভুল হয়েছে।';
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>অ্যাডমিন লগইন | <?= e(SITE_NAME) ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-login-wrapper">
        <div class="admin-login-card">
            <div class="admin-login-header">
                <span class="admin-logo-icon">🛒</span>
                <h1><?= e(SITE_NAME) ?></h1>
                <p>অ্যাডমিন প্যানেলে প্রবেশ করুন</p>
            </div>

            <?php if ($error): ?>
                <div class="admin-alert error"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="POST" class="admin-login-form">
                <div class="form-group">
                    <label for="username">ইউজারনেম</label>
                    <input type="text" id="username" name="username" required autofocus placeholder="আপনার ইউজারনেম">
                </div>
                <div class="form-group">
                    <label for="password">পাসওয়ার্ড</label>
                    <input type="password" id="password" name="password" required placeholder="আপনার পাসওয়ার্ড">
                </div>
                <button type="submit" class="admin-btn primary full-width">লগইন</button>
            </form>

            <div class="admin-login-footer">
                <a href="/">← হোমে ফিরুন</a>
            </div>
        </div>
    </div>
</body>
</html>
