<?php

class FTP
{
    private $conn;
    public $system;
    public $status = false;
    private static $host = "127.0.0.1";
    private static $username = "image_bed";
    private static $password = "pTnC8Anhx4w5JLHE";
    private static $port = 21;
    private static $timeout = 90;

    public function __construct()
    {
        // 联接FTP服务器 
        @$this->conn = ftp_connect(self::$host, self::$port, self::$timeout) or exit("Syntax Error!");
        if (!$this->conn) return false;
        if (!ftp_login($this->conn, self::$username, self::$password)) return ftp_quit($this->conn);
        $this->system = ftp_systype($this->conn);
        $this->status = true;
        return true;
    }

    public function __destruct()
    {
        if ($this->conn) ftp_quit($this->conn);
    }

    //改变路径
    public function chdir($directory)
    {
        return ftp_chdir($this->conn, $directory);
    }

    //回上层目录
    public function cdup()
    {
        return ftp_cdup($this->conn);
    }

    //取得目前所在路径
    public function pwd()
    {
        return ftp_pwd($this->conn);
    }

    //建新目录
    public function mkdir($directory)
    {
        return ftp_mkdir($this->conn, $directory);
    }

    //删除目录
    public function rmdir($directory)
    {
        return ftp_rmdir($this->conn, $directory);
    }

    //列出指定目录中所有文件
    public function nlist($directory)
    {
        return ftp_nlist($this->conn, $directory);
    }

    //详细列出指定目录中所有文件
    public function rawlist($directory)
    {
        return ftp_rawlist($this->conn, $directory);
    }

    //切换主被动传输模式
    public function pasv($status)
    {
        return ftp_pasv($this->conn, $status);
    }

    //下载文件
    public function get($from, $to)
    {
        return ftp_get($this->conn, $to, $from, FTP_BINARY);
    }

    //上传文件
    public function put($remote_file_path, $local_file_path, $mode)
    {
        return ftp_put($this->conn, $remote_file_path, $local_file_path, $mode);
    }

    //获得指定文件的大小
    public function size($directory)
    {
        return ftp_size($this->conn, $directory);
    }

    //获得指定文件的最后修改时间
    public function mdtm($directory)
    {
        return ftp_mdtm($this->conn, $directory);
    }

    //将文件改名
    public function rename($oldname, $newname)
    {
        return ftp_rename($this->conn, $oldname, $newname);
    }

    //将文件删除
    public function delete($directory)
    {
        return ftp_delete($this->conn, $directory);
    }
}
