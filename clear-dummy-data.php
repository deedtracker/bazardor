<?php
require_once __DIR__ . '/includes/data.php';

$db = getDB();

echo "<h2>Database Cleanup Utility</h2>";

try {
    // Disable foreign key checks temporarily
    $db->exec('SET FOREIGN_KEY_CHECKS = 0');

    // Empty the transaction tables
    $db->exec('TRUNCATE TABLE city_prices');
    $db->exec('TRUNCATE TABLE daily_prices');

    // Re-enable foreign key checks
    $db->exec('SET FOREIGN_KEY_CHECKS = 1');

    echo "<p style='color: green;'><strong>Success!</strong> All dummy prices have been completely removed.</p>";
    echo "<p>Your Products, Categories, and 64 Districts remain intact.</p>";
    echo "<p>You now have a fresh start. You can go to the Admin Panel and begin entering your real prices.</p>";
    echo "<p style='color: red;'><strong>Important:</strong> For security reasons, please delete this `clear-dummy-data.php` file from your server after you have successfully run it.</p>";

} catch (PDOException $e) {
    echo "<p style='color: red;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
}
