<?php
if (!defined('KuoPath')) {
    define('KuoPath', str_ireplace('\\','/',dirname(__FILE__)) . '/../');
}
if (!defined('KuoTempPath')) {
    define('KuoTempPath', KuoPath . 'temp/');
}
ob_start();
define("KuoVer", '2.0.0');

/**
 * 检查变量是否存在且不为false
 * 
 * @param mixed $name 要检查的变量
 * @return bool 如果变量存在且不为false，则返回true，否则返回false
 */
function isexist($name)
{
    if (isset($name) && $name !== false) {
        return true;
    } else {
        return false;
    }
}

/**
 * 计算UTF-8编码字符串的长度
 * 
 * @param string $string 要计算长度的字符串
 * @return int 字符串的长度（按UTF-8字符计算）
 */
function strlen2(string $string): int
{
    return mb_strlen($string, 'UTF-8');
}

/**
 * 将字符串按指定长度分割成数组
 * @param string $string    要分割的字符串
 * @param int $split_len    分割的长度，默认为1
 * @return array           返回分割后的数组
 */
function str2_split($string, $split_len = 1)
{
    $len = mb_strlen($string, 'UTF-8');
    if ($len > $split_len || !$split_len) {
        for ($i = 0; $i < $len; $i++) {
            $parts[] = mb_substr($string, 0, $split_len, 'UTF-8');
            $string  = mb_substr($string, $split_len, $len, 'UTF-8');
        }
    } else {
        $parts = array($string);
    }
    return $parts;
}

/**
 * 截取字符串（支持中文等多字节字符）
 * 
 * @param string $str    要截取的字符串
 * @param int    $start  开始位置（负数表示从末尾开始计算）
 * @param int    $length 截取长度（0表示截取到末尾）
 * @return string 截取后的字符串
 */
function KuoSub($str = "", $start = 0, $length = 0)
{
    $war =  str2_split($str);
    $new = [];
    if ($start >= 0) {

        if ($length > 0) $new = array_slice($war, $start, $length);
        else $new = array_slice($war, $start);
    } else {
        $weizhi_ = count($war) + $start;
        $new = array_slice($war, $weizhi_);
    }

    return implode("", $new);
}

function ltrimE($nn, $wenzi = "")
{
    if ($wenzi == "") {
        return ltrim($nn, $wenzi);
    }
    $quanbu = KuoSub($nn, 0, strlen2($wenzi));
    if ($quanbu == $wenzi) {
        $canxx = explode($wenzi, $nn);
        array_shift($canxx);
        return implode($wenzi, $canxx);
    } else {
        return $nn;
    }
}

/**
 * rtrimE函数优化
 * 
 * @param string $nn    要处理的字符串
 * @param string $wenzi 要去除的字符串
 * @return string 处理后的字符串
 */
function rtrimE($nn, $wenzi = "")
{
    if ($wenzi == "") {
        return rtrim($nn, $wenzi);
    }
    $quanbu = KuoSub($nn, -strlen2($wenzi));
    if ($quanbu == $wenzi) {
        $canxx = explode($wenzi, $nn);
        array_pop($canxx);
        return implode($wenzi, $canxx);
    } else {
        return $nn;
    }
}


/**
 * trimE函数优化
 * 
 * @param string $nn    要处理的字符串
 * @param string $wenzi 要去除的字符串
 * @return string 处理后的字符串
 */
function trimE($nn, $wenzi = "")
{
    if ($wenzi == "") {
        return trim($nn);
    }
    return rtrimE(ltrimE($nn, $wenzi), $wenzi);
}

//Output debugging
function p()
{
    $args = func_get_args();
    if (count($args) < 1) {
        echo ("<font color='red'> Debug </font>");
        return;
    }
    echo "<div style='width:100%;text-align:left'><pre>\n\n";
    foreach ($args as $arg) {

        if (is_array($arg)) {
            print_r($arg);
            echo "\n";
        } else if (is_string($arg)) {
            echo $arg . "\n";
        } else {
            var_dump($arg);
            echo "\n";
        }
    }
    echo "\n</pre></div>";
}
/**
 * 创建目录函数
 * 
 * @param string $dir 目录路径
 * @param string $zz   目录权限
 * @return bool 创建成功或失败
 */
function KuoCreate($dir, $zz = '')
{
    if (strstr($dir, "#")) {
        return;
    }
    if ($zz == '') {
        $dirs = substr(strrchr($dir, '/'), 1);
        if ($dirs != '') {
            $dir = str_replace($dirs, '', $dir);
        }
        $dir =  rtrimE($dir, '/');
    }
    if (!is_dir($dir)) {
        if (!KuoCreate(dirname($dir), $zz = 2)) return false;
        if (!mkdir($dir, 0777)) return false;
    }
    return true;
}


/**
 * 删除目录函数
 * 
 * @param string $dir 目录路径
 * @param bool $virtual 是否为虚拟目录
 * @return bool 删除成功或失败
 */
function KuoRmdir($dir, $virtual = false)
{
    p( $dir);
    $ds = DIRECTORY_SEPARATOR;
    $dir = $virtual ? realpath($dir) : $dir;
    $dir = substr($dir, -1) == $ds ? substr($dir, 0, -1) : $dir;
    if (is_dir($dir) && $handle = opendir($dir)) {
        while ($file = readdir($handle)) {
            if ($file == '.' || $file == '..') {
                continue;
            } elseif (is_dir($dir . $ds . $file)) {
                KuoRmdir($dir . $ds . $file);
            } else {
               
                unlink($dir . $ds . $file);
            }
        }
        closedir($handle);
        rmdir($dir);
        return true;
    }
    return false;
}


/**
 * MD5加密函数
 * 
 * @param string $var 要加密的字符串，默认为'ELikj'
 * @return string 加密后的字符串
 */
function KuoMd5($var = 'ELikj')
{
    if (!$var) {
        $var = time() . 'b891037e3d772605f56f8e9877d8593c';
    }
    $varstr = strlen($var);
    if ($varstr < 1) {
        $varstr = 32;
    }
    $hash = md5('@中国@' . base64_encode($var . '知者不惑，') . $var . '仁者不忧，' . sha1($var . '勇者不惧，') . '@制造@');
    return substr($hash, 1, $varstr * 3);
}


/**
 * 生成UUID函数
 * 
 * @param string $hash 可选的哈希值，默认为空
 * @return string 生成的UUID字符串
 */
function uuid($hash = "")
{
    if ($hash == "") {
        $hash = strtoupper(hash('ripemd128', rand(1, 9999999) . time() . md5(sha1(microtime()))));
    } else {
        $hash = strtoupper(md5(sha1($hash)));
    }
    return substr($hash,  0,  8) . '-' . substr($hash,  8,  4) . '-' . substr($hash, 12,  4) . '-' . substr($hash, 16,  4) . '-' . substr($hash, 20, 12);
}

/**
 * 生成订单号函数
 * 
 * @param string $biaoqian 标识符，默认为"ELi"
 * @return string 生成的订单号字符串
 */
function orderid($biaoqian = "Kuo")
{
    usleep(1);
    return $biaoqian . date('Ymd' . rand(10, 99) . 'His') . mt_rand(100000, 999999);
}

/**
 * 发送POST请求的函数
 * 
 * @param string $url 请求的URL地址
 * @param mixed $para POST请求的参数，可以是字符串或数组
 * @param array $Extension 可选的curl扩展设置，默认为空数组
 * @param string $cacert_url 可选的SSL证书路径，默认为空字符串
 */
function KuoPost($url,  $para, $Extension = array(), $cacert_url = '')
{
    $curl = curl_init($url);
    if (strpos($url, "s://") !== false) {
        if ($cacert_url != '') {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 2);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
            curl_setopt($curl, CURLOPT_CAINFO, $cacert_url);
        } else {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        }
    }
    curl_setopt($curl, CURLOPT_TIMEOUT, 240);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $para);
    if ($Extension) {
        foreach ($Extension as $k => $v) {
            curl_setopt($curl, $k, $v);
        }
    }
    $responseText = curl_exec($curl);
    curl_close($curl);
    return $responseText;
}

/**
 * 发送GET请求的函数
 * 
 * @param string $url 请求的URL地址
 * @param array $Extension 可选的curl扩展设置，默认为空数组
 * @param string $cacert_url 可选的SSL证书路径，默认为空字符串
 */
function KuoGet($url, $Extension = array(), $cacert_url = '')
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 240);
    if (strpos($url, "s://") !== false) {
        if ($cacert_url != '') {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 2);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
            curl_setopt($curl, CURLOPT_CAINFO, $cacert_url);
        } else {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        }
    }
    if ($Extension) {
        foreach ($Extension as $k => $v) {
            curl_setopt($curl, $k, $v);
        }
    }
    $responseText = curl_exec($curl);
    curl_close($curl);
    return $responseText;
}

// 检查magic_quotes_gpc是否开启
function get_magic_quotes_gpc_(){
   
    if(version_compare(PHP_VERSION,'7.4.0', '<')){
        return ini_get('magic_quotes_gpc') ? true : false;
    }else{
        return false;
    }
}

//Safe conversion
function Safeconversion($data)
{
    if (!get_magic_quotes_gpc_()) return addslashes($data);
    else return $data;
}
//xss defense
function KuoXss($data)
{
    $guolv = array('<', '>', '=', '"', "'", 'script', '%', '\\', '&');
    $guoHO = array('', '', '', '', '', '', '', '', '');
    return str_ireplace($guolv, $guoHO, $data);
}
//sql Safe websocket
function KuoSql($data)
{
    $guolv = array('<?php', 'create ', 'select ', 'update ', 'delete ', 'database ', DBprefix, 'show tables', 'drop ', 'insert ', 'alter ', '`');
    $guoHO = array('&#60;&#63;&#112;&#104;&#112;', 'crteea', 'secelt', 'upteda', 'detele', 'datbase', 'DBprefix', 'showw tbales', 'dorp', 'inesrt', 'atler', '');
    return str_ireplace($guolv, $guoHO, $data);
}

//Sorting an array A-Z
function azpaixu($para)
{
    ksort($para);
    reset($para);
    return $para;
}
//Database paging
function Limit($page_size = 10, $page = 5)
{
    $page = (int) ((int)($page) <= 0 ? 1 : $page);
    $page_size =  (int)((int)($page_size) <= 0 ? 1 : $page_size);
    return $pages = (($page - 1) * $page_size) . "," . $page_size;
}
//Group URL
function getarray($para)
{   $arg  = "";
    $zuhe = array();
    foreach ($para as $k => $v) {
        $zuhe[] = "$k=$v";
    }
    $arg = implode("&", $zuhe);
    if (get_magic_quotes_gpc_()) {
        $arg = stripslashes($arg);
    }
    return $arg;
}
// to array
function toget($string)
{   
    $string = str_replace('&nbsp;',' ',$string);
    parse_str($string,$_GET_);
    return $_GET_;
}
//Multidimensional array string to array
function _POST_( &$_POST_ ,$key = "",$zhi = ""){
    if (strstr($key , '[') !== false ) {
        unset($_POST_[$key]);
        $key = str_replace(['[',']'],['{@@}',''],$key);
        $klist = explode('{@@}',$key);
        $chang = count($klist)-1;
        for ($i = 0; $i < $chang ; $i++) {
            if (!isset($_POST_[$klist[$i]])){
                $_POST_[$klist[$i]] = [];
            }
            $_POST_ = & $_POST_[$klist[$i]];
        }
        $_POST_[$klist[$chang]] = $zhi;
    }
}

//Safe replacement
function ELiSecurity($name)
{
    return str_replace(array('-', '#', '/', '，', '|', '、', '\\', '*', '?', '<', '>', '.', "\n", "\r", '【', '】', '(', ')', '：', '{', '}', '\'', '"', ':', ';', ' '), array('_', ''), strtolower(trimE($name)));
}


//Get route function
function KuoUri()
{
    if (isset($_SERVER['argv'])) {
        unset($_SERVER['argv']['0']);
        $KuoUri = $_SERVER['PHP_SELF'];
        if(!empty($_SERVER['argv']) ){
            $KuoUri   .=  implode("", $_SERVER['argv']??[]);
            foreach ($_SERVER['argv']??[] as $vvv) {
                $_GET[] = $vvv;
            }
        }

    } else if (isset($_SERVER['QUERY_STRING'])) {
        $KuoUri = $_SERVER['PHP_SELF'] . (empty($_SERVER['QUERY_STRING']) ? '' : ('?' . $_SERVER['QUERY_STRING']));
    } else if (isset($_SERVER['REQUEST_URI'])) {
        $KuoUri = $_SERVER['REQUEST_URI'];
    } else {
        $KuoUri = $_SERVER['PHP_SELF'] . (empty($_SERVER['QUERY_STRING']) ? '' : ('?' . $_SERVER['QUERY_STRING']));
    }
    $_SERVER['REQUEST_URI'] = $KuoUri;
}


//Get ip address
if (!function_exists('ip')) {
    function ip()
    {   if (isset($GLOBALS['ip'])) {
            return $GLOBALS['ip'];
        }
        $ip1 = getenv("HTTP_CLIENT_IP") ? getenv("HTTP_CLIENT_IP") : "none";
        $ip2 = getenv("HTTP_X_FORWARDED_FOR") ? getenv("HTTP_X_FORWARDED_FOR") : "none";
        $ip3 = getenv("REMOTE_ADDR") ? getenv("REMOTE_ADDR") : "none";
        $ip4 = $_SERVER['REMOTE_ADDR'] ? $_SERVER['REMOTE_ADDR'] : "none";
        if (isset($ip3) && $ip3 != "none" && $ip3 != "unknown") $ip = $ip3;
        else if (isset($ip4) && $ip4 != "none" && $ip4 != "unknown") $ip = $ip4;
        else if (isset($ip2) && $ip2 != "none" && $ip2 != "unknown") $ip = $ip2;
        else if (isset($ip1) && $ip1 != "none" && $ip1 != "unknown") $ip = $ip1;
        else $ip = $_SERVER['REMOTE_ADDR'];
        if (strstr($ip, ",")) {
            $ip_ = explode(',', $ip);
            $ip = $ip_['0'];
        }
        return $ip;
    }
}

function db( $table = "", $ELiDataBase_ = [])
{
    $Kuodb = "Kuo_Db".KuoConfig['DbClass'];
    $KuoDatabaseDriver = new $Kuodb($ELiDataBase_ );
    return $KuoDatabaseDriver->shezhi($table);
}

/////#Class include#////
$news = 'Kuo_Cache'.KuoConfig['MemClass'];
$GLOBALS['KuoMemClass'] = new $news(KuoConfig['MemClassConfig']);

$db = db("admin");
p($db ->pwhere()->order(["id"=>'desc',"off desc"])->where(['id'=>1,'off'=>1])-> find());
