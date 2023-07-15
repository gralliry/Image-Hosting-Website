<?php
class MYSQL{
    // 静态用self 非静态用this
    public $statu = false;
    private $conn;
    private static $host = "127.0.0.1";
    private static $dbname = "photo_forye_top";
    private static $username = "photo_forye_top";
    private static $password = "BczWJpDBHn4SaXMH";
    private static $port = 3306;
    public function __construct(){
        
    }
    public function __destruct(){
        if($this->conn) $this->conn = null;
    }
    public function connect(){
        @$this->conn = new PDO(
            "mysql:host=".self::$host.";port=".self::$port.";dbname=".self::$dbname,
            self::$username,
            self::$password
        ) or exit(404);
        if($this->conn->errorCode()) return;
        $this->statu = true; 
    }
    // 直接执行SQL指令 // 需要防止sql注入
    public function execute($command,...$content){
        // 构造预处理命令
        $stmt = $this->conn->prepare($command);
        // 写入参数
        for($i=1,$j=count($content);$i<=$j;$i++){
            // if(is_int($content[$i-1]))
            //     $stmt->bindValue($i,$content[$i-1],PDO::PARAM_INT);
            // else
            $stmt->bindValue($i,$content[$i-1],PDO::PARAM_STR);
        }
        // 执行成功
        @$statu = $stmt->execute() or exit();
        if($statu) return $stmt;
        else return false;
    }
}