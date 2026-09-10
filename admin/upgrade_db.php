<?php
require_once __DIR__ . '/../includes/db.php';

$db = getDB();

try {
    // 1. Schema Updates
    $db->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS pricing_mode ENUM('variable', 'flat') DEFAULT 'variable'");
    $db->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS price_difference INT DEFAULT 5");

    echo "Schema updated successfully! Added pricing_mode and price_difference columns to products table.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
