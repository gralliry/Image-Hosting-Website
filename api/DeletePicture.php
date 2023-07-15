<?php
    header("Access-Control-Allow-Origin:*");
    // exit(json_encode(['statu'=>200,'message'=>'Mysql delete error']));
    if(
        !isset($_POST['num'])||
        !isset($_POST['key'])||
        $_POST['num']=='undefined'||
        $_POST['key']=='undefined'||
        !$_POST['num']||
        !$_POST['key']
    ) exit(json_encode([
        'statu'=>300,
        'message'=>"'num' or 'key' is null"
    ]));
    $num = $_POST['num'];
    $key = $_POST['key'];
    require_once "../exec/MYSQL_Class.php";
    $base = new MYSQL();
    $base->connect();
    if(!$base->statu) exit(json_encode(['statu'=>300,'message'=>'Mysql error']));
    if(!($result = $base->execute(
        'select `id`,`url` from info where `num`=? and `key`=?',
        $num,
        $key
    ))) exit(json_encode(['statu'=>300,'message'=>'Num does not exist or Key is wrong']));
    $info = $result->fetch(PDO::FETCH_ASSOC);
    //exit(json_encode(['statu'=>300,'message'=>json_encode($info)]));
    if($base->execute(
        'delete from info where id=?',
        $info['id']
    )->rowCount()==0) exit(json_encode(['statu'=>300,'message'=>'Mysql delete error']));
    //
    require_once "../exec/FTP_Class.php";
    $base = new FTP();
    $base->connect();
    if(!$base->delete($info['url'])) exit(json_encode(['statu'=>300,'message'=>'Ftp delete error']));
    $base = null;
    exit(json_encode(['statu'=>200,'message'=>'Delete successful']));