<?php
require_once __DIR__ . '/../includes/db.php';

$db = getDB();

try {
    // 1. V2 Schema Updates (from earlier)
    $db->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS pricing_mode ENUM('variable', 'flat') DEFAULT 'variable'");
    $db->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS price_difference INT DEFAULT 5");

    // 2. V3 Schema Updates (Guardrails and Weather)
    // Avoid fatal errors if columns already exist
    try { $db->exec("ALTER TABLE daily_prices ADD COLUMN is_auto BOOLEAN DEFAULT 0"); } catch (PDOException $e) {}
    try { $db->exec("ALTER TABLE cities ADD COLUMN lat DECIMAL(10,8) DEFAULT NULL"); } catch (PDOException $e) {}
    try { $db->exec("ALTER TABLE cities ADD COLUMN lon DECIMAL(11,8) DEFAULT NULL"); } catch (PDOException $e) {}

    $db->exec("CREATE TABLE IF NOT EXISTS city_weather (
        id INT AUTO_INCREMENT PRIMARY KEY,
        city_id INT NOT NULL,
        weather_code INT DEFAULT 0,
        temperature DECIMAL(5,2) DEFAULT 0,
        rain_mm DECIMAL(5,2) DEFAULT 0,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_city (city_id),
        FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    echo "Schema updated successfully! Added V3 Weather and Guardrail columns.\n";

    // 3. Populate Coordinates for Weather API
    $coords = [
        'ঢাকা' => [23.8103, 90.4125], 'গাজীপুর' => [24.0023, 90.4264], 'নারায়ণগঞ্জ' => [23.6238, 90.5000], 'মানিকগঞ্জ' => [23.8617, 90.0003],
        'মুন্সীগঞ্জ' => [23.5422, 90.5305], 'নরসিংদী' => [23.9193, 90.7176], 'টাঙ্গাইল' => [24.2498, 89.9166], 'ফরিদপুর' => [23.6071, 89.8429],
        'গোপালগঞ্জ' => [23.0051, 89.8267], 'কিশোরগঞ্জ' => [24.4376, 90.7811], 'মাদারীপুর' => [23.1641, 90.1897], 'রাজবাড়ী' => [23.7574, 89.6445],
        'শরীয়তপুর' => [23.2082, 90.3416], 'চট্টগ্রাম' => [22.3569, 91.7832], 'কক্সবাজার' => [21.4272, 92.0058], 'ব্রাহ্মণবাড়িয়া' => [23.9571, 91.1119],
        'কুমিল্লা' => [23.4607, 91.1809], 'চাঁদপুর' => [23.2333, 90.6667], 'ফেনী' => [23.0159, 91.3976], 'খাগড়াছড়ি' => [23.1193, 91.9847],
        'লক্ষ্মীপুর' => [22.9425, 90.8412], 'নোয়াখালী' => [22.8696, 91.0993], 'রাঙ্গামাটি' => [22.6333, 92.2000], 'বান্দরবান' => [22.1953, 92.2184],
        'রাজশাহী' => [24.3636, 88.6241], 'বগুড়া' => [24.8481, 89.3730], 'জয়পুরহাট' => [25.1010, 89.0305], 'নওগাঁ' => [24.8081, 88.9461],
        'নাটোর' => [24.4206, 89.0004], 'চাঁপাইনবাবগঞ্জ' => [24.5965, 88.2774], 'পাবনা' => [24.0049, 89.2438], 'সিরাজগঞ্জ' => [24.4534, 89.7007],
        'খুলনা' => [22.8456, 89.5403], 'বাগেরহাট' => [22.6516, 89.7859], 'চুয়াডাঙ্গা' => [23.6394, 88.8475], 'যশোর' => [23.1634, 89.2182],
        'ঝিনাইদহ' => [23.5448, 89.1539], 'কুষ্টিয়া' => [23.9013, 89.1205], 'মাগুরা' => [23.4873, 89.4198], 'মেহেরপুর' => [23.7622, 88.6318],
        'নড়াইল' => [23.1725, 89.5126], 'সাতক্ষীরা' => [22.7153, 89.0706], 'বরিশাল' => [22.7010, 90.3535], 'বরগুনা' => [22.1504, 90.1197],
        'ভোলা' => [22.6865, 90.6481], 'ঝালকাঠি' => [22.6406, 90.1987], 'পটুয়াখালী' => [22.3596, 90.3298], 'পিরোজপুর' => [22.5841, 89.9720],
        'সিলেট' => [24.8949, 91.8687], 'হবিগঞ্জ' => [24.3749, 91.4155], 'মৌলভীবাজার' => [24.4830, 91.7685], 'সুনামগঞ্জ' => [25.0658, 91.3950],
        'রংপুর' => [25.7439, 89.2752], 'দিনাজপুর' => [25.6217, 88.6355], 'গাইবান্ধা' => [25.3288, 89.5281], 'কুড়িগ্রাম' => [25.8054, 89.6361],
        'লালমনিরহাট' => [25.9923, 89.2847], 'নীলফামারী' => [25.9318, 88.8560], 'পঞ্চগড়' => [26.3354, 88.5517], 'ঠাকুরগাঁও' => [26.0337, 88.4617],
        'ময়মনসিংহ' => [24.7471, 90.4203], 'জামালপুর' => [24.9196, 89.9481], 'নেত্রকোণা' => [24.8709, 90.7279], 'শেরপুর' => [25.0205, 90.0153]
    ];

    $stmt = $db->prepare("UPDATE cities SET lat = ?, lon = ? WHERE name = ?");
    foreach ($coords as $name => $coord) {
        $stmt->execute([$coord[0], $coord[1], $name]);
    }
    echo "City coordinates updated!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
