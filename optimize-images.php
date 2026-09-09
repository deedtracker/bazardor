<?php
/**
 * ONE-TIME image optimization script.
 * Run this ONCE on your server to compress all product images.
 * It creates small WebP thumbnails in assets/thumbs/ for homepage use.
 * 
 * Usage: php optimize-images.php
 * Or visit: https://bazardor.app/optimize-images.php
 */

$sourceDir = __DIR__ . '/assets';
$thumbDir  = __DIR__ . '/assets/thumbs';

// Create thumbs directory
if (!is_dir($thumbDir)) {
    mkdir($thumbDir, 0755, true);
}

$targetSize = 120; // 120px is more than enough for a 56px display (2x for retina)
$quality = 80;

$files = glob($sourceDir . '/*.{png,jpg,jpeg,webp}', GLOB_BRACE);
$count = 0;

echo "<h2>Image Optimization Report</h2><pre>\n";

foreach ($files as $file) {
    $filename = pathinfo($file, PATHINFO_FILENAME);
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $outFile = $thumbDir . '/' . $filename . '.webp';
    
    // Skip non-product files
    if (in_array($filename, ['favicon', 'logo', 'html2canvas.min'])) continue;
    
    // Skip if thumbnail already exists
    if (file_exists($outFile)) {
        echo "[EXISTS] $filename.$ext - Thumbnail already generated\n";
        continue;
    }
    
    $originalSize = filesize($file);
    
    // Load image based on type
    switch ($ext) {
        case 'png':  $img = @imagecreatefrompng($file); break;
        case 'jpg':
        case 'jpeg': $img = @imagecreatefromjpeg($file); break;
        case 'webp': $img = @imagecreatefromwebp($file); break;
        default: continue 2;
    }
    
    if (!$img) {
        echo "[SKIP] $filename.$ext - Could not read image\n";
        continue;
    }
    
    $w = imagesx($img);
    $h = imagesy($img);
    
    // Calculate new dimensions (maintain aspect ratio)
    if ($w > $h) {
        $newW = $targetSize;
        $newH = (int)($h * $targetSize / $w);
    } else {
        $newH = $targetSize;
        $newW = (int)($w * $targetSize / $h);
    }
    
    // Resize
    $thumb = imagecreatetruecolor($newW, $newH);
    
    // Preserve transparency for PNG
    imagealphablending($thumb, false);
    imagesavealpha($thumb, true);
    $transparent = imagecolorallocatealpha($thumb, 0, 0, 0, 127);
    imagefill($thumb, 0, 0, $transparent);
    
    imagecopyresampled($thumb, $img, 0, 0, 0, 0, $newW, $newH, $w, $h);
    
    // Save as WebP
    imagewebp($thumb, $outFile, $quality);
    
    $newSize = filesize($outFile);
    $saved = round(($originalSize - $newSize) / $originalSize * 100, 1);
    
    echo "[OK] $filename: " . round($originalSize/1024) . "KB → " . round($newSize/1024) . "KB ($saved% smaller)\n";
    
    imagedestroy($img);
    imagedestroy($thumb);
    $count++;
}

echo "\nDone! Optimized $count images.\n";
echo "</pre>";
