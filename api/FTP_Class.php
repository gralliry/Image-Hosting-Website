<?php 
class FTP{
    private $conn;
    public $system;
    public $status = false;
    private static $host = "127.0.0.1";
    private static $username = "image_bed";
    private static $password = "6ATEmbsttRj2MSXp";
    private static $port = 54219;
    private static $timeout = 90;
    public function __construct(){
        // 联接FTP服务器 
        @$this->conn = ftp_connect(self::$host,self::$port,self::$timeout) or exit( "Sysnax Error!");
        if(!$this->conn) return;
        if(!ftp_login($this->conn,self::$username,self::$password)) return ftp_quit($this->conn);
        $this->system = ftp_systype($this->conn);
        $this->status = true;
    }
    public function __destruct(){
        if($this->conn) ftp_quit($this->conn);
    }
    //改变路径
    public function chdir($directory){
        return ftp_chdir($this->conn,$directory);
    }
    //回上层目录
    public function cdup(){
        return ftp_cdup($this->conn);
    }
    //取得目前所在路径
    public function pwd(){
        return ftp_pwd($this->conn);
    }
    //建新目录
    public function mkdir($directory){
        return ftp_mkdir($this->conn);
    }
    //删除目录
    public function rmdir($directory){
        return ftp_rmdir($this->conn,$directory);
    }
    //列出指定目录中所有文件
    public function nlist($directory){
        return ftp_nlist($this->conn,$directory);
    }
    //详细列出指定目录中所有文件
    public function rawlist($directory){
        return ftp_rawlist($this->conn,$directory);
    }
    //切换主被动传输模式
    public function pasv($status){
        return ftp_pasv($this->conn,$status);
    }
    //下载文件
    public function get($directory){
        return ftp_get($this->conn,$directory);
    }
    //上传文件
    public function put($remote_file_path,$local_file_path,$mode){
        return ftp_put($this->conn,$remote_file_path,$local_file_path,$mode);
    }
    //获得指定文件的大小
    public function size($directory){
        return ftp_size($this->conn,$directory);
    }
    //获得指定文件的最后修改时间
    public function mdtm($directory){
        return ftp_mdtm($this->conn,$directory);
    }
    //将文件改名
    public function rename($oldname,$newname){
        return ftp_rename($this->conn,$oldname,$newname);
    }
    //将文件删除
    public function delete($directory){
        return ftp_delete($this->conn,$directory);
    }
}
   
// 使用username和password登录 
// ftp_login($conn, "root", "root"); 
// var_dump(ftp_pwd($conn));

// ftp_chdir($conn,"css");
// $filelist = ftp_nlist($conn, ".");
// var_dump($filelist);

// ftp_cdup($conn);
// $filelist = ftp_nlist($conn, ".");
// var_dump($filelist);
// 获取远端系统类型 