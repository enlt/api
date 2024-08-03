<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
include '/usr/home/luoh/domains/api.luoh.my.to/public_html/plugins/Logger.php';

function getMimeType($extension) {
    $mimeTypes = [
        'webp' => 'image/webp',
        'png' => 'image/png',
        'jpg' => 'image/jpg'
    ];
    return $mimeTypes[$extension] ?? 'application/octet-stream';
}

function getJsonLink() {
    return 'https://api.luoh.my.to/storage/ijson/image/ecy/wallpaper/.json';
}

function getImageLink($value) {
    return "https://i1.wp.com/new-api-2.pages.dev/image/ecy/wallpaper/{$value}";
}

$return = $_GET['return'] ?? 'image';

$jsonLink = getJsonLink();

$jsonData = file_get_contents($jsonLink);

if ($jsonData === false) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 500, 'message' => '远程获取 JSON 失败']);
    exit;
}

$imageList = json_decode($jsonData, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 500, 'message' => '服务端错误']);
    exit;
}

$randomImage = $imageList[array_rand($imageList)];
$imageUrl = getImageLink($randomImage);

if ($return === 'json') {
    header('Content-Type: application/json');
    echo json_encode(['status' => 200, 'imageurl' => $imageUrl]);
} else {
    $extension = pathinfo($randomImage, PATHINFO_EXTENSION);
    $mimeType = getMimeType($extension);
    header('Content-Type: ' . $mimeType);
    echo file_get_contents($imageUrl);
}
?>