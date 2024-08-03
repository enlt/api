<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
include '/usr/home/luoh/domains/api.luoh.my.to/public_html/plugins/Logger.php';

function getTextLink($type) {
    $baseUrl = 'https://api.luoh.my.to/storage/ijson/text/';
    return $baseUrl . $type . '.txt';
}

function getRandomLine($url) {
    $lines = @file($url, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return ($lines !== false && count($lines) > 0) ? $lines[array_rand($lines)] : null;
}

$type = $_GET['type'] ?? null;

if ($type) {
    $textLink = getTextLink($type);
    $randomLine = getRandomLine($textLink);
    echo $randomLine ? $randomLine : "该类型不存在";
} else {
    echo "参数错误";
}

?>