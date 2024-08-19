<?php
header("Access-Control-Allow-Origin: *");
// $content = file_get_contents("php://input");
// @$postData = json_decode($content,true) or exit(json_encode(["status"=>300,"message"=>"post data error"]));
if (!isset($_POST["order"])) {
    exit(json_encode(["status" => 400, "message" => "post data error"]));
}
$postData = abs((int)$_POST["order"]);

require_once "./model/MYSQL.php";
$base = new MYSQL();
if (!$base->status) {
    exit(json_encode(["status" => 500, "message" => "mysql connect error"]));
}

$pageSize = 10;
$startIndex = ($postData - 1) * $pageSize;
$result = $base->execute(
    "select `uid`,`url`,`des` from info limit ?,?",
    $startIndex,
    $pageSize
);
$base = null;
if (!$result) {
    exit(json_encode(["status" => 500, "message" => "mysql exec error"]));
}
$returnData = [];

$protocol = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on" ? "https" : "http";
$domainName = $_SERVER["HTTP_HOST"];
$port = $_SERVER["SERVER_PORT"];
$path = "/source";

while ($info = $result->fetch(PDO::FETCH_ASSOC)) {
    $returnData[] = [
        "uid" => $info["uid"],
        "url" => $protocol."://".$domainName.":".$port.$path.$info["url"],
        "des" => $info["des"]
    ];
}
exit(json_encode([
    "status" => 200,
    "data" => $returnData
]));