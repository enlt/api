<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
include '/usr/home/luoh/domains/api.luoh.my.to/public_html/plugins/Logger.php';

function getJsonLink($type) {
    $baseUrl = 'https://api.luoh.my.to/storage/ijson/image/ecy/anime/';
    return $baseUrl . $type . '/.json';
}

function getImageLink($type, $value) {
    $baseUrl = 'https://i1.wp.com/new-api-2.pages.dev/image/ecy/anime/';
    return $baseUrl . $type . '/' . $value;
}

function getRandomValueFromJson($jsonContent) {
    $values = json_decode($jsonContent, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($values) && count($values) > 0) {
        return $values[array_rand($values)];
    }
    return null;
}

function get_mime_type($imageName) {
    $extension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
    $mimeTypes = [
        'webp' => 'image/webp',
        'png' => 'image/png',
        'jpg' => 'image/jpg'
    ];
    return $mimeTypes[$extension] ?? 'application/octet-stream';
}

function handleError($message) {
    http_response_code(400);
    $response = [
        'status' => '400',
        'imgurl' => $message
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$type = $_GET['type'] ?? null;
$returnType = $_GET['return'] ?? 'image';

if (!$type || !$returnType) {
    handleError("参数错误");
}

$jsonLink = getJsonLink($type);
$jsonContent = @file_get_contents($jsonLink);
if ($jsonContent === false) {
    handleError("此类型不存在");
}

$randomValue = getRandomValueFromJson($jsonContent);
if (!$randomValue) {
    handleError("远程获取值失败");
}

$imageLink = getImageLink($type, $randomValue);

if ($returnType == 'image') {
    $imageContent = @file_get_contents($imageLink);
    if ($imageContent === false) {
        handleError("服务端错误");
    }

    $mimeType = get_mime_type($randomValue);
    header('Content-Type: ' . $mimeType);
    echo $imageContent;
} elseif ($returnType == 'json') {
    $response = [
        'status' => '200',
        'imageurl' => $imageLink
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    handleError("参数错误");
}

?>