<?php

$apiUrl = "https://mapi.mobilelegends.com/hero/list";

// ==========================
// FUNCTION GET API (cURL)
// ==========================
function getApiData($url) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        die("cURL Error: " . curl_error($ch));
    }

    //curl_close($ch);

    return $response;
}

// ==========================
// FUNCTION DOWNLOAD IMAGE
// ==========================
function downloadImage($url, $savePath) {
    $ch = curl_init($url);
    $fp = fopen($savePath, 'w');

    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    curl_exec($ch);

    if (curl_errno($ch)) {
        echo "Error download: " . curl_error($ch) . "\n";
    }

    //curl_close($ch);
    fclose($fp);
}

// ==========================
// AMBIL DATA API
// ==========================
$response = getApiData($apiUrl);

$data = json_decode($response, true);

if (!isset($data['data'])) {
    die("Format API tidak valid");
}

// ==========================
// SIAPKAN FOLDER
// ==========================
$saveDir = __DIR__ . "/img/";

if (!is_dir($saveDir)) {
    mkdir($saveDir, 0777, true);
}

// ==========================
// LOOP & DOWNLOAD
// ==========================
foreach ($data['data'] as $hero) {

    $heroId = $hero['heroid'];
    $imageUrl = $hero['key'];

    // Tambahkan https:
    if (strpos($imageUrl, '//') === 0) {
        $imageUrl = "https:" . $imageUrl;
    }

    // Ambil ekstensi file
    $parsedUrl = parse_url($imageUrl);
    $path = $parsedUrl['path'];
    $ext = pathinfo($path, PATHINFO_EXTENSION);

    if (!$ext) {
        $ext = "jpg"; // fallback
    }

    $fileName = $heroId . "." . $ext;
    $filePath = $saveDir . $fileName;

    echo "Downloading: $fileName ... ";

    downloadImage($imageUrl, $filePath);

    if (file_exists($filePath) && filesize($filePath) > 0) {
        echo "OK\n";
    } else {
        echo "FAILED\n";
    }
}

echo "\nSelesai semua download!\n";