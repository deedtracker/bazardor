<?php
// =============================================
// BazarDor — Price Automation Test Script
// Run this to verify the new district-tier logic
// Usage: php test-automation.php
// =============================================
require_once __DIR__ . '/includes/data.php';

echo "=== BazarDor Price Automation Test ===\n\n";

// Test cases: [product_id, product_name, category, base_price]
// We will simulate saving and then reading back the prices
$testCases = [
    // Cheap vegetable — the main problem case
    ['name' => 'আলু (Potato)', 'catId' => 1, 'base' => 20],
    // Mid-range vegetable
    ['name' => 'পেঁয়াজ (Onion)', 'catId' => 1, 'base' => 50],
    // Expensive spice
    ['name' => 'আদা (Ginger)', 'catId' => 2, 'base' => 280],
    // Protein
    ['name' => 'ডিম (Egg)', 'catId' => 3, 'base' => 48],
    // Meat (high price, stable)
    ['name' => 'গরুর মাংস (Beef)', 'catId' => 4, 'base' => 750],
    // Fish
    ['name' => 'ইলিশ (Hilsa)', 'catId' => 5, 'base' => 1200],
];

// Define the bounds inline for simulation (same as saveDailyPrices)
$categoryBounds = [
    1 => [
        'dhaka' => [0, 0], 'dhaka_adjacent' => [-2, 3], 'divisional' => [-3, 5],
        'farming_hub' => [-8, -2], 'mid_tier' => [-3, 5], 'river_hub' => [-2, 5], 'remote' => [3, 8],
    ],
    2 => [
        'dhaka' => [0, 0], 'dhaka_adjacent' => [-2, 2], 'divisional' => [-3, 5],
        'farming_hub' => [-5, -1], 'mid_tier' => [-2, 5], 'river_hub' => [0, 5], 'remote' => [3, 8],
    ],
    3 => [
        'dhaka' => [0, 0], 'dhaka_adjacent' => [-1, 2], 'divisional' => [-2, 3],
        'farming_hub' => [-3, 0], 'mid_tier' => [-2, 3], 'river_hub' => [-1, 3], 'remote' => [2, 5],
    ],
    4 => [
        'dhaka' => [0, 0], 'dhaka_adjacent' => [-5, 5], 'divisional' => [-10, 10],
        'farming_hub' => [-15, -5], 'mid_tier' => [-10, 10], 'river_hub' => [-5, 10], 'remote' => [5, 20],
    ],
    5 => [
        'dhaka' => [0, 0], 'dhaka_adjacent' => [-3, 3], 'divisional' => [-5, 8],
        'farming_hub' => [-3, 5], 'mid_tier' => [-3, 8], 'river_hub' => [-15, -3], 'remote' => [5, 15],
    ],
];

$defaultBounds = [
    'dhaka' => [0, 0], 'dhaka_adjacent' => [-1, 2], 'divisional' => [-2, 3],
    'farming_hub' => [-3, 0], 'mid_tier' => [-2, 3], 'river_hub' => [-1, 3], 'remote' => [2, 5],
];

// Sample districts for each tier
$sampleDistricts = [
    'dhaka'          => 'ঢাকা',
    'dhaka_adjacent' => 'গাজীপুর',
    'divisional'     => 'চট্টগ্রাম',
    'farming_hub'    => 'বগুড়া',
    'mid_tier'       => 'কুমিল্লা',
    'river_hub'      => 'চাঁদপুর',
    'remote'         => 'বান্দরবান',
];

foreach ($testCases as $tc) {
    $catId = $tc['catId'];
    $base = $tc['base'];
    $bounds = $categoryBounds[$catId] ?? $defaultBounds;
    $priceFloor = max(1, (int)round($base * 0.5));

    echo "─────────────────────────────────────\n";
    echo "📦 {$tc['name']}  |  Base: {$base} Tk\n";
    echo "─────────────────────────────────────\n";

    foreach ($sampleDistricts as $tier => $districtName) {
        $range = $bounds[$tier];
        $adjustment = random_int($range[0], $range[1]);
        $calculated = $base + $adjustment;

        if ($base >= 100) {
            $calculated = round($calculated / 5) * 5;
        } else {
            $calculated = round($calculated);
        }

        $final = max($priceFloor, $calculated);

        $rangeStr = "[{$range[0]}, +{$range[1]}]";
        $padName = str_pad($districtName, 30, ' ');
        $padTier = str_pad($tier, 16, ' ');
        echo "  {$padName} ({$padTier}) => {$final} Tk   (bounds: {$rangeStr})\n";
    }
    echo "\n";
}

echo "✅ Test complete. Review the prices above to ensure they look realistic.\n";
echo "   - Farming hubs should be cheaper for vegetables\n";
echo "   - River hubs should be cheaper for fish\n";
echo "   - Remote areas should be slightly more expensive\n";
echo "   - No absurd prices (like 10 Tk potato or 35 Tk potato)\n";
