<?php
// =============================================
// BazarDor — Generate Password Hash
// =============================================
// Run this file ONCE on your server to generate a proper password hash.
// Then copy the hash into your seed.sql or update the database directly.
// DELETE THIS FILE after use!

$password = 'admin123'; // Change this to your desired password
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<h2>Password Hash Generator</h2>";
echo "<p><strong>Password:</strong> " . htmlspecialchars($password) . "</p>";
echo "<p><strong>Hash:</strong> <code>" . htmlspecialchars($hash) . "</code></p>";
echo "<hr>";
echo "<p>Copy the hash above and run this SQL in phpMyAdmin:</p>";
echo "<pre>UPDATE users SET password = '" . htmlspecialchars($hash) . "' WHERE username = 'admin';</pre>";
echo "<hr>";
echo "<p style='color: red; font-weight: bold;'>⚠️ DELETE THIS FILE (generate-hash.php) after use!</p>";
