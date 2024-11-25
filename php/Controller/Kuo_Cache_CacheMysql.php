<?php

// Mem.Get("key"); //获取值
// Mem.Put("key","值支持对象更新部分","时间"); //更新值 可以只更新时间 时间0不过期 值不存在返回 false
// Mem.Set(key,value,"时间");//设置或者新增值 
// Mem.Add(key,"默认值1",time);//增加值  支持时间过期
// Mem.Cut(key,"默认值1",time);//减少值 支持时间过期
// DMem.Del(key);//删除
// DMem.Flush(key);// key 为空清理全部  不为空删除包含关键词
//Save 保留接口
//Total 同级条数
class Kuo_CacheMysql{

    public $DB = null;
    public $CANSHU = [];
    public $Ext = "db";
    public function __construct($data = [])
    {
        
    }

    private function  Guolv($key =""){


    }

    public function Get($key = "",$data=[]){
        $name = $this->Guolv($key);
        if(isexist($name)){
            return $name;
        }
        return $data;
    }

    public function Put($key ="" , $data =[] , $time = 0){
        $name = $this->Guolv($key);
        if(!$name){
            $name = [];
        }

        foreach($data as $k =>$v){
            $name[$k] = $v;
        }

       return $this ->Set($key, $name , $time );
    }
    public function Set($key ="", $data =[] , $time = 0){
        $mingzi  = $this->DB.str_ireplace('.','',$key).'.'.$this->Ext;
        if ($time > 0) {
            $time += time();
        }
        KuoCreate($mingzi);
        if (file_put_contents($mingzi,json_encode([$data,$time]), LOCK_EX) !== false) {
            clearstatcache();
            return true;
        }
       return false;

    }

    public function Add($key ="" , $data =1 , $time = 0){
        $name = $this->Get($key);
        if(!$name || !is_numeric($name )){
            $name = 0;
        }
        $name += $data ;
        $this ->Set($key, $name , $time );
        return $name;
    }

    public function Cut($key = "", $data =1, $time = 0){
        $name = $this->Get($key);
        if(!$name || !is_numeric($name )){
            $name = 0;
        }
        $name -= $data ;
        $this ->Set($key, $name , $time );
        return $name;
    }

    public function Del($key = ""){
        //删除key
        $mingzi  = $this->DB.str_ireplace('.','',$key).'.'.$this->Ext;
        if(is_file($mingzi)){
            @unlink($mingzi);
            return true;
        }else{
            return false;
        }
    }

    public function Flush($key = ""){

        if($key == ""){
            KuoRmdir(KuoTempPath);
            KuoCreate(KuoTempPath);
        }else{
            $key = KuoTempPath.str_ireplace('..', '', $key);
            KuoRmdir($key);
        }
        return true;
    }

    public function Save(){
        return true;
    }
    public function Total(){
        return false;
    }

}