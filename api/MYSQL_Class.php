<?php
class MYSQL{
    // 静态用self 非静态用this
    public $status = false;
    private $conn;
    private static $host = "127.0.0.1";
    private static $dbname = "image_bed";
    private static $username = "image_bed";
    private static $password = "wYZBKcCfhiNCJfFr";
    private static $port = 54231;
    public function __construct(){
        @$this->conn = new PDO(
            "mysql:host=".self::$host.";port=".self::$port.";dbname=".self::$dbname,
            self::$username,
            self::$password
        ) or exit(404);
        if($this->conn->errorCode()) return;
        $this->status = true; 
    }
    public function __destruct(){
        // if($this->conn) $this->conn->closeCursor();
    }
    // 直接执行SQL指令 // 需要防止sql注入
    public function execute($command,...$content){
        // 构造预处理命令
        $stmt = $this->conn->prepare($command);
        // 写入参数
        for($i=1,$j=count($content);$i<=$j;$i++){
            if(is_int($content[$i-1]))
                $stmt->bindValue($i,$content[$i-1],PDO::PARAM_INT);
            else
                $stmt->bindValue($i,$content[$i-1],PDO::PARAM_STR);
        }
        // 执行成功
        @$status = $stmt->execute() or exit();
        if($status) return $stmt;
        else return false;
    }
}