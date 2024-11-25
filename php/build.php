<?php
define("Residentmemory", "build");
define('WWW',str_ireplace('\\','/', dirname(__FILE__) . '/'));

$_POST['filelist'] = [];
function KuoSao($dir, $virtual = false)
{
    $ds = DIRECTORY_SEPARATOR;
    $dir = $virtual ? realpath($dir) : $dir;
    $dir = substr($dir, -1) == $ds ? substr($dir, 0, -1) : $dir;
    if (is_dir($dir) && $handle = opendir($dir)) {
        while ($file = readdir($handle)) {
            if ($file == '.' || $file == '..' || $file=='.DS_Store') {
                continue;
            } elseif (is_dir($dir . $ds . $file)) {
                KuoSao($dir . $ds . $file);
            } else {
                $_POST['filelist'][] = str_ireplace('\\','/',$dir . $ds . $file);
            }
        }
        closedir($handle);
        return true;
    }
    return false;
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

function strlen2(string $string): int
{
    return mb_strlen($string, 'UTF-8');
}
function Kuochuli($fileneirong)
{
    $classneirong = "";
    $fileneirong = str_ireplace(["\r\n","\r"],["\n","\n"],$fileneirong);
    $nlist = explode("\n", $fileneirong);
    $keshi = false;
    foreach($nlist as $weo){
        if($keshi){
            if($weo != ''){
                $classneirong.="\n".$weo;
            }
           
        }else{
            if(strstr($weo, "class Kuo_") !==false || strstr($weo, '$_GLOBALS') !==false){
                $keshi = true;
                $classneirong.="\n".$weo;
            }
        }
    }
    return $classneirong;
}

$buildPHP = WWW.'www/';
$PHPindex = $buildPHP.'index.php';
KuoCreate($buildPHP);
copy(WWW.'kuo.php',$PHPindex);
$PHPN = file_get_contents($PHPindex);

$fileneirong = str_ireplace(["\r\n","\r"],["\n","\n"],$PHPN);
$nlist = explode("\n", $fileneirong);
$PHPN  = "";
foreach($nlist as $weo){
    if($weo != ''){
        $PHPN.=$weo."\n";
    }
}

$fileneirong = file_get_contents(WWW.'config/KuoSoft.com.php');
$classneirong = Kuochuli($fileneirong);

$_POST['filelist'] = [];
KuoSao(WWW.'Controller');
foreach($_POST['filelist'] as $files){

    if( strstr($files, ".php") !==false ){
        $fileneirong = file_get_contents($files);
        $classneirong .= Kuochuli($fileneirong);
        
        
    }
}
$PHPN =  str_ireplace('/////#Class include#////',$classneirong,$PHPN );

file_put_contents($PHPindex,$PHPN);