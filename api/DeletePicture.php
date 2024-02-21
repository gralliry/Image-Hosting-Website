<?php
if (
    !isset($_POST["uid"]) || !isset($_POST["key"]) ||
    $_POST["uid"] == "undefined" || $_POST["key"] == "undefined" ||
    !$_POST["uid"] || !$_POST["key"]
) {
    exit(json_encode([
        "status" => 400,
        "message" => "'uid' or 'key' is null"
    ]));
}
$uid = $_POST["uid"];
$key = $_POST["key"];

require_once "./MYSQL_Class.php";
$base = new MYSQL();
if (!$base->status) {
    exit(json_encode(["status" => 500, "message" => "Mysql error"]));
}
if (!($result = $base->execute(
    "select `id`,`url` from info where `uid`=? and `key`=?",
    $uid, $key
))) {
    exit(json_encode(["status" => 401, "message" => "Uid does not exist or Key is wrong"]));
}
$info = $result->fetch(PDO::FETCH_ASSOC);
//exit(json_encode(["status"=>300,"message"=>json_encode($info)]));
if ($base->execute(
        "delete from info where id=?",
        $info["id"]
    )->rowCount() == 0) {
    exit(json_encode(["status" => 500, "message" => "Mysql delete error"]));
}

require_once "./FTP_Class.php";
$base = new FTP();
if (!$base->delete("/" . $info["url"])) {
    exit(json_encode(["status" => 500, "message" => "Ftp delete error"]));
}
$base = null;

exit(json_encode(["status" => 200, "message" => "Delete successful"]));