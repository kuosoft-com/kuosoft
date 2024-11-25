<?php
$_GLOBALS = [];


$_GLOBALS['Debug']  = true;
$_GLOBALS['WZHOST']  = "http://127.0.0.1/";
$_GLOBALS['CDNHOST']  = "http://127.0.0.1/";
$_GLOBALS['MemClass']  =  "Mysql";
$_GLOBALS['MemClassConfig']  = ['dbname'=>'memcached'];
$_GLOBALS['Defaultclass']  ="admin"; //默认控制器
$_GLOBALS['Defaultmethod']  ="index"; //默认函数
$_GLOBALS['Defaultplus']  = ""; //录入了隐藏控制器 需要调用其他控制器需要/开始/
$_GLOBALS['WebExt']  =""; //网页后缀
$_GLOBALS['FENGE']  ="/";  //分隔符号
$_GLOBALS['UPClass']  ="KuoFile";
$_GLOBALS['UPconfig'] = [
    "size"=>100244444,
    "name"=>"",//保留原名字为空不保留 可追加时间戳 YmdHis 
    "ext"=>['gif', 'bmp', 'jpg', 'jpeg', 'png', 'swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2', '7z', 'mp4','apk'],
    "SecretId"=>"",
    "SecretKey"=>"",
    "SecretHost"=>"",
    "Bucket"=>""
];


$_GLOBALS['DbClass']  = "Mysql";
$_GLOBALS['DbMain']  = "write"; //主要写入库
$_GLOBALS['DbDoku']  =  true; //是否开启多库链接
$_GLOBALS['DbConfig'] = [
    "write" =>[
         "host"=>'127.0.0.1',
        "user"=> 'root',
        "port"=>3306,
        "password"=>'root',
        "database"=>'newkuo',
        "prefix"=> "kuo_",
        "charset"=> 'utf8mb4',
    ],
    "du1" =>[
         "host"=>'127.0.0.1',
        "user"=> 'root',
        "port"=>3306,
        "password"=>'root',
        "database"=>'newkuo',
        "prefix"=> "kuo_",
        "charset"=> 'utf8mb4',
    ],
    "du2" =>[
         "host"=>'127.0.0.1',
        "user"=> 'root',
        "port"=>3306,
        "password"=>'root',
        "database"=>'newkuo',
        "prefix"=> "kuo_",
        "charset"=> 'utf8mb4',
    ]
]; 

if (!defined('DBprefix')){
    define("DBprefix",$_GLOBALS['DbConfig'][$_GLOBALS['DbMain']]["prefix"]);
}

define("KuoConfig",$_GLOBALS);