<?php

class MYSQL
{
    // 静态用self 非静态用this
    public bool $status = false;
    private PDO $conn;
    private static string $host;
    private static string $database;
    private static string $username;
    private static string $password;
    private static int $port;

    public function __construct()
    {
        self::$host = getenv('MYSQL_HOST') ?: "127.0.0.1";
        self::$port = (int)(getenv('MYSQL_PORT') ?: "3306");
        self::$database = getenv('MYSQL_DATABASE') ?: "image_bed";
        self::$username = getenv('MYSQL_USERNAME') ?: 'image_bed';
        self::$password = getenv('MYSQL_PASSWORD') ?: 'wYZBKcCfhiNCJfFr';
        @$this->conn = new PDO(
            "mysql:host=" . self::$host . ";port=" . self::$port . ";dbname=" . self::$database,
            self::$username,
            self::$password
        ) or exit(404);
        if ($this->conn->errorCode()) return;
        $this->status = true;
    }

    public function __destruct()
    {
        // if($this->conn) $this->conn->closeCursor();
    }

    // 直接执行SQL指令 // 需要防止sql注入
    public function execute($command, ...$content)
    {
        // 构造预处理命令
        $stmt = $this->conn->prepare($command);
        // 写入参数
        for ($i = 1, $j = count($content); $i <= $j; $i++) {
            if (is_int($content[$i - 1]))
                $stmt->bindValue($i, $content[$i - 1], PDO::PARAM_INT);
            else
                $stmt->bindValue($i, $content[$i - 1], PDO::PARAM_STR);
        }
        // 执行成功
        @$status = $stmt->execute() or exit();
        if ($status) return $stmt;
        else return false;
    }
}