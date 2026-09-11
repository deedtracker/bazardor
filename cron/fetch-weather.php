<?php
// =============================================
// BazarDor — Weather Fetcher Cron (V3)
// =============================================
// Runs via Cron Job (e.g., every 6 hours)
// Fetches live weather for all 64 districts via Open-Meteo

$secretKey = 'bazardor_weather_2026';
$isCli = php_sapi_name() === 'cli';

if (!$isCli) {
    if (!isset($_GET['key']) || $_GET['key'] !== $secretKey) {
        http_response_code(403);
        die("Forbidden: Invalid Secret Key.");
    }
}

require_once __DIR__ . '/../includes/data.php';
$db = getDB();
$logFile = __DIR__ . '/fetch-weather.log';

$report = [];
$report[] = "Weather Update Run: " . date('Y-m-d H:i:s');

// 1. Get all cities with lat/lon
$stmt = $db->query("SELECT id, name, lat, lon FROM cities WHERE lat IS NOT NULL AND lon IS NOT NULL");
$cities = $stmt->fetchAll();

if (empty($cities)) {
    die("No cities with coordinates found. Run upgrade_db.php first.");
}

// 2. Build bulk API URL for Open-Meteo
$lats = [];
$lons = [];
$cityIds = [];

foreach ($cities as $city) {
    $lats[] = $city['lat'];
    $lons[] = $city['lon'];
    $cityIds[] = $city['id'];
}

$latStr = implode(',', $lats);
$lonStr = implode(',', $lons);

// Fetch current temperature, precipitation (rain), and weather_code
$url = "https://api.open-meteo.com/v1/forecast?latitude={$latStr}&longitude={$lonStr}&current=temperature_2m,precipitation,weather_code&timezone=auto";

// 3. Make the API request
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$response) {
    $report[] = "ERROR: Failed to fetch from Open-Meteo API. HTTP Code: $httpCode";
    file_put_contents($logFile, implode("\n", $report) . "\n\n", FILE_APPEND);
    die("API Error");
}

$data = json_decode($response, true);

// Open-Meteo returns an array of objects if multiple locations are requested
if (!is_array($data) || empty($data)) {
    die("Invalid JSON response.");
}

// 4. Update the Database
$insertStmt = $db->prepare("
    INSERT INTO city_weather (city_id, weather_code, temperature, rain_mm) 
    VALUES (?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE 
        weather_code = VALUES(weather_code), 
        temperature = VALUES(temperature), 
        rain_mm = VALUES(rain_mm)
");

$successCount = 0;

foreach ($data as $index => $locationData) {
    if (!isset($locationData['current'])) continue;
    
    $current = $locationData['current'];
    $temp = $current['temperature_2m'] ?? 0;
    $rain = $current['precipitation'] ?? 0;
    $code = $current['weather_code'] ?? 0;
    $cId = $cityIds[$index];

    $insertStmt->execute([$cId, $code, $temp, $rain]);
    $successCount++;
}

$report[] = "SUCCESS: Updated weather for $successCount districts.";
file_put_contents($logFile, implode("\n", $report) . "\n\n", FILE_APPEND);

echo implode("<br>", $report);
