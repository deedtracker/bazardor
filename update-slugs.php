<?php
require_once __DIR__ . '/includes/data.php';

echo "<h2>Slug Updater Utility</h2>";

try {
    $db = getDB();
    
    // Fetch all products
    $stmt = $db->query('SELECT id, slug FROM products');
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $updateStmt = $db->prepare('UPDATE products SET slug = ? WHERE id = ?');
    $updatedCount = 0;

    foreach ($products as $p) {
        $oldSlug = $p['slug'];
        
        // Check if it already has '-price-today'
        if (strpos($oldSlug, '-price-today') === false) {
            $newSlug = $oldSlug . '-price-today';
            
            // Execute update
            $updateStmt->execute([$newSlug, $p['id']]);
            $updatedCount++;
            
            echo "Updated: <strong>$oldSlug</strong> -> <strong>$newSlug</strong><br>";
        }
    }
    
    if ($updatedCount > 0) {
        echo "<p style='color: green;'><strong>Success!</strong> $updatedCount product slugs were updated successfully.</p>";
    } else {
        echo "<p>No slugs needed updating. They already have the suffix.</p>";
    }
    
    echo "<p style='color: red;'><strong>Important:</strong> You can delete this `update-slugs.php` file after running it.</p>";

} catch (PDOException $e) {
    echo "<p style='color: red;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
}
