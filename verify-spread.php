<?php
require_once __DIR__ . '/includes/data.php';

echo "<pre>";
echo "<h2>Pricing Algorithm Spread Verification</h2>";

$db = getDB();
$cities = getAllCities();

function simulateSpread($profile, $basePrice, $cities) {
    echo "<h3>Profile: $profile | Dhaka Base Price: $basePrice Tk</h3>";
    
    $sourceHubs = [];
    $sourceDiscount = 0.20;
    
    if ($profile === 'veg_north') {
        $sourceHubs = ['বগুড়া', 'বগুড়া', 'রাজশাহী', 'রংপুর', 'মুন্সীগঞ্জ', 'যশোর'];
        $sourceDiscount = 0.25; 
    } elseif ($profile === 'fish') {
        $sourceHubs = ['চাঁদপুর', 'বরিশাল', 'ভোলা', 'ময়মনসিংহ', 'কক্সবাজার'];
        $sourceDiscount = 0.15;
    } elseif ($profile === 'imported_land') {
        $sourceHubs = ['যশোর', 'সাতক্ষীরা', 'দিনাজপুর', 'চট্টগ্রাম'];
        $sourceDiscount = 0.20;
    }
    
    $sourcePrice = $basePrice * (1 - $sourceDiscount);
    
    $megaHubs = ['ঢাকা', 'গাজীপুর', 'নারায়ণগঞ্জ'];
    $eastDistricts = ['সিলেট', 'সুনামগঞ্জ', 'হবিগঞ্জ', 'মৌলভীবাজার', 'কুমিল্লা', 'ব্রাহ্মণবাড়িয়া', 'চাঁদপুর', 'নোয়াখালী', 'ফেনী', 'লক্ষ্মীপুর'];
    $southDistricts = ['বরিশাল', 'পটুয়াখালী', 'ভোলা', 'পিরোজপুর', 'বরগুনা', 'ঝালকাঠি', 'খুলনা', 'বাগেরহাট'];
    $northDistricts = ['রংপুর', 'দিনাজপুর', 'গাইবান্ধা', 'কুড়িগ্রাম', 'নীলফামারী', 'পঞ্চগড়', 'ঠাকুরগাঁও', 'লালমনিরহাট'];
    $farRemote = ['বান্দরবান', 'খাগড়াছড়ি', 'রাঙ্গামাটি', 'কক্সবাজার'];

    $results = [];
    
    foreach ($cities as $city) {
        $cName = $city['name'];
        $calculated = $basePrice;

        if (in_array($cName, $sourceHubs)) {
            $calculated = $sourcePrice;
        } elseif (in_array($cName, $megaHubs)) {
            $calculated = $basePrice;
        } else {
            $penalty = 0.05;

            if ($profile === 'imported_land') {
                if (in_array($cName, $eastDistricts)) $penalty = 0.18;
                elseif (in_array($cName, $farRemote)) $penalty = 0.25;
                else $penalty = 0.08;
            } 
            elseif ($profile === 'fish') {
                if (in_array($cName, $northDistricts)) $penalty = 0.15;
                elseif (in_array($cName, $farRemote)) $penalty = 0.20;
                else $penalty = 0.05;
            } 
            elseif ($profile === 'veg_north') {
                if (in_array($cName, $southDistricts)) $penalty = 0.20;
                elseif (in_array($cName, $eastDistricts)) $penalty = 0.15;
                elseif (in_array($cName, $farRemote)) $penalty = 0.25;
                else $penalty = 0.05;
            }
            
            $calculated = $basePrice * (1 + $penalty);
        }

        if (!in_array($cName, $megaHubs) && !in_array($cName, $sourceHubs)) {
            $magnitude = mt_rand(5, 10);
            $sign = (mt_rand(0, 1) === 0) ? -1 : 1;
            $calculated += ($magnitude * $sign);
        }

        // Round to nearest 5
        $finalPrice = max(0, round($calculated / 5) * 5);
        $results[$cName] = $finalPrice;
    }
    
    asort($results);
    foreach ($results as $city => $price) {
        echo str_pad($city, 30) . " : " . $price . " Tk\n";
    }
    echo "<hr>";
}

simulateSpread('imported_land', 50, $cities);
simulateSpread('fish', 1000, $cities);
simulateSpread('veg_north', 40, $cities);

echo "</pre>";
