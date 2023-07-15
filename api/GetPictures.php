<?php
    header("Access-Control-Allow-Origin:*");
    // exit(json_encode(['statu'=>300,'message'=>'mysql connect error']));
    // $content = file_get_contents("php://input");
    // @$postData = json_decode($content,true) or exit(json_encode(["statu"=>300,"message"=>"post data error"]));
    if(!isset($_POST['order'])) exit(json_encode(["statu"=>300,"message"=>"post data error"]));
    $postData = abs((int)$_POST['order']);    
    require_once "../exec/MYSQL_Class.php";
    $base = new MYSQL();
    $base->connect();
    if(!$base->statu) exit(json_encode(['statu'=>300,'message'=>'mysql connect error']));

    $pageSize = 10;
    $startIndex = ($postData-1)*$pageSize;
    $result = $base->execute(
        'select `num`,`url`,`des` from info limit ?,?',
        $startIndex,
        $pageSize
    );
    $base = null;
    if(!$result) exit(json_encode(['statu'=>300,'message'=>'mysql exec error']));
    $returnData = [];
    require_once "../exec/FTP_Class.php";
    $base = new FTP();
    $baseURL = $base->rootPath;

    while($info = $result->fetch(PDO::FETCH_ASSOC)){
        array_push($returnData,[
            'num'=>$info['num'],
            'url'=>$baseURL.$info['url'],
            'des'=>$info['des']
        ]);
    }
    exit(json_encode([
        'statu'=>200,
        'data'=>$returnData
    ]));