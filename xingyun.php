<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
include '/usr/home/luoh/domains/api.luoh.my.to/public_html/plugins/Logger.php';

$returnType = isset($_GET["return"]) ? $_GET["return"] : 'json';

if ($returnType == 'json') {
    header('Content-Type: application/json');
}

$name = isset($_GET["msg"]) ? $_GET["msg"] : '';
$hh = isset($_GET["hh"]) ? $_GET["hh"] : "\n";

if ($name == "") {
    $response = ["status" => 400, "message" => "抱歉，输入为空。"];
    if ($returnType == 'json') {
        echo json_encode($response);
    } else {
        returnImage($response);
    }
    exit();
}

$name = str_replace('座', '', $name);
$jk = [
    "白羊" => "1",
    "金牛" => "2",
    "双子" => "3",
    "巨蟹" => "4",
    "狮子" => "5",
    "处女" => "6",
    "天秤" => "7",
    "天蝎" => "8",
    "射手" => "9",
    "摩羯" => "10",
    "水瓶" => "11",
    "双鱼" => "12"
];

$l = isset($jk[$name]) ? $jk[$name] : null;
if ($l == null) {
    $response = ["status" => 400, "message" => "不存在此类型，请查证后重试。"];
    if ($returnType == 'json') {
        echo json_encode($response);
    } else {
        returnImage($response);
    }
    exit();
}

$z = file_get_contents("http://cal.meizu.com/android/unauth/horoscope/gethoroscope.do?type=".$l."&date=".date("Y-m-d")."&searchType=0");
$z = myTrim($z);

$p = preg_match_all('/{"contentAll":"(.*?)","contentCareer":"(.*?)","contentFortune":"(.*?)","contentHealth":"(.*?)","contentLove":"(.*?)","contentTravel":"(.*?)","date":(.*?),"direction":"(.*?)","enemies":"(.*?)","friends":"(.*?)","horoscopeType":(.*?),"id":(.*?),"lucklyColor":"(.*?)","lucklyTime":"(.*?)","mark":(.*?),"numbers":(.*?),"pointAll":(.*?),"pointCareer":(.*?),"pointFortune":(.*?),"pointHealth":(.*?),"pointLove":(.*?),"pointTravel":(.*?),"shorts":"(.*?)"}/', $z, $z);

if ($p == 0) {
    $response = ["status" => 500, "message" => "抱歉，获取出错。"];
    if ($returnType == 'json') {
        echo json_encode($response);
    } else {
        returnImage($response);
    }
    exit();
}

$response = [
    "星座" => $name,
    "贵人方位" => $z[8][0],
    "贵人星座" => $z[10][0],
    "幸运数字" => $z[16][0],
    "幸运颜色" => $z[13][0],
    "爱情运势" => $z[5][0],
    "财富运势" => $z[3][0],
    "事业运势" => $z[2][0],
    "整体运势" => $z[1][0],
    "提示" => $z[23][0]
];

if ($returnType == 'json') {
    echo json_encode(["status" => 200, "data" => $response]);
} else {
    returnImage($response);
}

function myTrim($str)
{
    $search = [" ", "　", "\n", "\r", "\t"];
    $replace = ["", "", "", "", ""];
    return str_replace($search, $replace, $str);
}

function returnImage($response)
{
    header('Content-Type: image/png');
    $im = imagecreatetruecolor(400, 300);
    $bg = imagecolorallocate($im, 255, 255, 255);
    $text_color = imagecolorallocate($im, 0, 0, 0);
    imagefilledrectangle($im, 0, 0, 400, 300, $bg);

    $text = '';
    foreach ($response as $key => $value) {
        $text .= $key . ": " . $value . "\n";
    }

    imagestring($im, 5, 10, 10, $text, $text_color);
    imagepng($im);
    imagedestroy($im);
}
?>