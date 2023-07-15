<?php
    namespace Tools;
    //获取本地服务器url
    function Get_Local_Url(){
        $url = (isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']=='on'?'https://':'http://').$_SERVER['SERVER_NAME'];
        $url .= ($_SERVER['SERVER_PORT']!=80?':'.$_SERVER['SERVER_PORT']:'');
        return $url;
    }
    //获取用户IP
    function Get_Client_IP(){
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $realip = $_SERVER['REMOTE_ADDR'];
            }
        } else {
            if (getenv("HTTP_X_FORWARDED_FOR")) {
                $realip = getenv( "HTTP_X_FORWARDED_FOR");
            } elseif (getenv("HTTP_CLIENT_IP")) {
                $realip = getenv("HTTP_CLIENT_IP");
            } else {
                $realip = getenv("REMOTE_ADDR");
            }
        }
        return $realip;
    }    
    //获取用户地址 
    function Get_Client_Address($ip){
        //$ip = Get_Client_IP();
        $url = 'https://open.onebox.so.com/dataApi?type=ip&src=onebox&query=ip&ip='.$ip.'&url=ip';
        // $url = 'https://open.onebox.so.com/dataApi?type=ip&src=onebox&query=ip&ip=43.136.82.191&url=ip';
        $res = file_get_contents($url);
        if(!$res) return NULL;
        $data = json_decode($res,true);
        // echo '<p>'.$address['0'].$address['1'].$address['2'].$address['3'].$address['5'].'</p>';
        return $data;
    }   