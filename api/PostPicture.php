<?php
header("Access-Control-Allow-Origin: *");
// exit(json_encode(["status"=>300,"message"=>"test"]));
// 减少压力？
// if(rand(1,10)==10) exit(json_encode(["status"=>300,"message"=>"404"]));
// 检查是否上传文件
if (!empty($_FILES)) {
    // 检测文件大小
    if ($_FILES["file"]["size"] > 1024 * 1024 * 10)
        exit(json_encode(["status" => 400, "message" => "The file is too big (>10M)"]));
    // 获取文件后缀名
    $filetype = pathinfo($_FILES["file"]["name"])["extension"];
    $image = array(
        "webp", "jpg", "png", "ico", "bmp", "gif", "tif", "pcx", "tga", "bmp", "pxc", "tiff", "jpeg", "exif", "fpx",
        "svg", "psd", "cdr", "pcd", "dxf", "ufo", "eps", "ai", "hdri"
    );
    // 检查是否为图片类型
    if (!in_array($filetype, $image))
        exit(json_encode(["status" => 400, "message" => "The file is not an image"]));
    // 获取文件内容
    $temp_file_path = $_FILES["file"]["tmp_name"];
    // 检查是否设置url
} else if (isset($_POST["url"]) && $_POST["url"] && $_POST["url"] != "undefined") {
    $url = $_POST["url"];
    // 初始化
    $curl = curl_init();
    // 设置url
    curl_setopt($curl, CURLOPT_URL, $url);
    // 返回HTTP头部信息
    curl_setopt($curl, CURLOPT_HEADER, true);
    // 关闭SSL
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    // 设置ipv4
    curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    // 设置获取的信息以文件流的形式返回
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    // 设置伪造请求头
    $headers = ["Referer: " . $url];
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    // 设置超时时间
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
    // 执行命令
    $response = curl_exec($curl);
    // exit(json_encode(["status"=>300,"message"=>$response]));
    if (curl_errno($curl)) {
        exit(json_encode(["status" => 503, "message" => curl_error($curl)]));
    }
    // 获取header信息
    $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    if ($status_code != 200) {
        exit(json_encode(["status" => 400, "message" => "The Url is wrong"]));
    }
    $content_type = explode(";", curl_getinfo($curl, CURLINFO_CONTENT_TYPE))[0];
    //
    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    // $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    // 关闭URL请求
    curl_close($curl);
    // 允许的文件类型
    $mime_and_extends = array(
        "image/bmp" => "bmp",
        "image/x-cmx" => "cmx",
        "image/cis-cod" => "cod",
        "image/gif" => "gif",
        "image/x-icon" => "ico",
        "image/ief" => "ief",
        "image/pipeg" => "jfif",
        "image/jpeg" => "jpg",
        "image/pjpeg" => "jpg",
        "image/x-portable-bitmap" => "pbm",
        "image/x-portable-graymap" => "pgm",
        "image/png" => "png",
        "image/x-png" => "png",
        "image/x-portable-anymap" => "pnm",
        "image/x-portable-pixmap" => "ppm",
        "image/x-cmu-raster" => "ras",
        "image/x-rgb" => "rgb",
        "image/tiff" => "tif",
        "image/x-xbitmap" => "xbm",
        "image/x-xpixmap" => "xpm",
        "image/x-xwindowdump" => "xwd",
    );
    if (array_key_exists($content_type, $mime_and_extends)) {
        exit(json_encode(["status" => 400, "message" => "The file is not an image: " . $content_type]));
    }
    $filetype = $mime_and_extends[$content_type];
    $temp_file = tmpfile();
    fwrite($temp_file, $body);
    fseek($temp_file, 0);
    $temp_file_path = stream_get_meta_data($temp_file)["uri"]; // eg: /tmp/phpFx0513a
} else {
    exit(json_encode(["status" => 400, "message" => "Url is null"]));
}
$uid = uniqid();
$remote_file_name = "/" . $uid . "." . $filetype;

require_once "./model/FTP.php";
$base = new FTP();
if (!$base->status) {
    exit(json_encode(["status" => 500, "message" => "Ftp connect error"]));
}
if (!$base->put($remote_file_name, $temp_file_path, FTP_BINARY)) {
    exit(json_encode(["status" => 500, "message" => "Ftp exec error"]));
}

require_once "./model/MYSQL.php";
$base = new MYSQL();
if (!$base->status) {
    exit(json_encode(["status" => 500, "message" => "Mysql connect error"]));
}
$key = (string)rand(1000000000, 9999999999);
if (!$base->execute(
    "insert into info (`uid`,`key`,`url`,`des`) values (?,?,?,?)",
    $uid, $key, $remote_file_name, $_POST["des"]
)) {
    exit(json_encode(["status" => 500, "message" => "mysql exec error"]));
} else {
    $protocol = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on" ? "https" : "http";
    $domain = $_SERVER["SERVER_NAME"];
    $port = $_SERVER["SERVER_PORT"];
    $path = "/source";
    $base_url = $protocol . "://" . $domain . ":" . $port . $path;
    exit(json_encode([
        "status" => 200,
        "message" => "Upload picture successfully",
        "data" => [
            "uid" => $uid,
            "key" => $key,
            "url" => $base_url . $remote_file_name
        ]
    ]));
}


