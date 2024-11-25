<?php

/**
 * setshiwu
 * order
 * where
 * pwhere
 * find
 * update
 * delete
 * insert
 * total
 * select
 * qurey
 * query
 * 
 * join
 * joinwhere
 * setbiao

 * biao

 * setku
 
 */
class Kuo_DbMysql{

    public $DB = null;
    public $MYSQL = null;
    public $CANSHU = [];

    public $DbName = "write";

    public $table = "";
    public $tablejg = [];
    public $ZHICHA = "";
    public $PAICHU = "";
    public $where_ = [];
    public $order = [];
    public $data = [];
    public $limit = [];
    public $tiaoshi = false;
    
    public function __construct($data = [])
    {
        if($data){
            $this -> DB = $data;
        }else{
            $this -> DB = KuoConfig['DbConfig'];
        }

        return $this;
        
    }
    public function  init(){
        $this ->where_ = [];
        $this ->order = [];
        $this ->data = [];
        $this ->limit =[];
        $this ->ZHICHA = '';
        $this ->PAICHU = '';
        $this ->tiaoshi = false;
    }
    function pwhere()
    {
        $this ->tiaoshi = true;
        return $this;
    }
    function biao()
    {
        return $this->table;
    }
    public function lianjie($data){

        $options = [
            PDO::ATTR_ERRMODE  => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_PERSISTENT => true,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES ".$data['charset']
        ];

        $dsn = "mysql:host=".$data['host'].";port=".$data['port'].";dbname=".$data['database'];

        try {
            $pdo = new PDO($dsn, $data['user'], $data['password'], $options);
        } catch (\PDOException $e) {
            if(KuoConfig['Debug']){
                p($e->getMessage());
            }
            $pdo = new PDO($dsn, $data['user'], $data['password'], $options);
        }

        $this ->MYSQL = $pdo;
        return $pdo;
        
    }
    public function TableJiegou() {

        $sql = "desc `{$this->table}`";
        $stmt = $this->MYSQL->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        $DATA = [];
        if($rows){
            foreach($rows  as $row){
                $DATA[$row['Field']]  = $row['Extra'] == '' ? $row['Field'] . '_' . $row['Default'] : $row['Extra'];
            }
        }

        return $DATA;
    }

    public  function zhicha($data = '')
    {   
        $ZIDUAN = $this->tablejg['1'];
        $zuhe = [];
        if ($data && is_array($data)) {
        }else{
            $data = explode(',',$data);
        }
        foreach($data as $vvv){
            $vvv = trim($vvv);
            if(isset($ZIDUAN[$vvv])){
                $zuhe[] = '`'.$vvv. '`';
            }
        }
        $this ->ZHICHA = implode(',',$zuhe);

        return $this;
    }

    public function Safeconversion($data)
    {
        if (!get_magic_quotes_gpc_()) return KuoSql(addslashes($data));
        else return KuoSql($data);
    }

    public  function paichu($data = '')
    {    
        $zuhe = [];
        $ZIDUAN = $this->tablejg['1'];
        if ($data && is_array($data)) {
        }else{
            $data = explode(',',$data);
        }
        foreach($ZIDUAN  as  $k=> $vvv){
            $vvv = trim($vvv);
            if(array_search( $k, $data ) === false){
                $zuhe[] =  '`' .$k. '`';
            }
        }
        $this ->ZHICHA = implode(',',$zuhe);
        return $this;
    }

    public function Tablesql(){
        $biaojieguo = "*";
            if($this-> ZHICHA != ''){
                $biaojieguo = $this-> ZHICHA ;
            }else if($this-> PAICHU != ''){
                $biaojieguo =  $this-> PAICHU;
            }else if($this->tablejg[0]){
                $biaojieguo = '`'.implode('`,`',$this->tablejg[0]).'`';
            }
            return $biaojieguo;
    }
    
    public  function zhixing($moth = '')
    {
        if($moth == 'find'){
            

            $sql =  'SELECT '.$this ->Tablesql().' FROM `'.$this->table.'` ';
            $sqlzu = [];
            
            if($this ->where_ ){
                $sql  .= $this ->where_ ['0'];
                $sqlzu = $this ->where_ ['1'];
            }

            if($this ->order ){
                $sql.= ' ORDER BY '. implode(' , ',$this ->order);
            }

            if($this ->tiaoshi){
                p($sql ,$sqlzu);
            }

            $sth = $this ->MYSQL -> prepare($sql  );
            $sth ->execute($sqlzu);
            $row = $sth->fetch(PDO::FETCH_ASSOC);
            if (!$row) return false;
            else return $row;
        }

    }


    
    public  function order($data = "")
    {   
        $this ->order = [];
        $ZIDUAN = $this->tablejg['1'];

        if ($data && is_array($data)) {
        }else{
            $data = explode(',', $data);
        }

        foreach($data as $k =>$v){
            if(strtolower($v) != 'asc' && strtolower($v) != 'desc'){
                $v_ = explode(" ", $v);
                $k = $v_['0'];
                $v= isset($v_['1'])?$v_['1']:'asc';
            }
            if(isset($ZIDUAN[$k])  && ( strtolower($v) == 'asc' || strtolower($v) == 'desc') ){
                $this ->order[] = $k.' '.strtoupper($v);
            }
        }
    
        return $this;
    }
   
    function wherezuhe($data = '')
    {   
        $x = '';
        $ZHIS = [];
        if (is_array($data)) {
            $zhsss = count($data);
            if ($zhsss < 1) return "";
            $ZIDUAN = $this->tablejg['1'];
            foreach ($data as $k => $v) {
                $k = $this->Safeconversion($k);
                if (!is_array($v)) {
                    $v = $this->Safeconversion($v);
                }
                $guize = str_replace(['>=','>','(',')','<=','<','!=','FLK','FOLK','OLK','LIKE','OR','IN','DAYD','DAY','XIYD','XIY','DEY',' ','=','~','@'], '', $k);
                if($guize != "" &&!isset($ZIDUAN [$guize ])){
                    continue ;
                }
                $k = str_replace('@', '', $k);
                $MMQQIAN =  "and";
                if(strstr($k, '~') !== false ){
                    $k = str_replace(['~',' '], '', $k);
                    $MMQQIAN =  "or";
                }
                if (strstr($k, '>=') !== false) {
                    $k = trimE(str_replace('>=', '', $k));
                    $x .= " ".$MMQQIAN." `$k` >= ? ";
                    $ZHIS[] = $v;
                } else if (strstr($k, '>') !== false) {
                    $k = trimE(str_replace('>', '', $k));
                    $x .= " ".$MMQQIAN." `$k` > ? ";
                    $ZHIS[] = $v;
                } else if (strstr($k, '(') !== false) {
                    if ($v == 'and') $v = 'and';
                    else            $v = 'OR';
                    $x .= " $v (";
                } else if (strstr($k, ')') !== false) {
                    $x .= " ) ";
                } else if (strstr($k, '<=') !== false) {
                    $k = trimE(str_replace('<=', '', $k));
                    $x .= " ".$MMQQIAN." `$k` <= ? ";
                    $ZHIS[] = $v;
                } else if (strstr($k, '<') !== false) {
                    $k = trimE(str_replace('<', '', $k));
                    $x .= " ".$MMQQIAN." `$k` < ? ";
                    $ZHIS[] = $v;
                } else if (strstr($k, '!=') !== false) {
                    $k = trimE(str_replace('!=', '', $k));
                    $x .= " ".$MMQQIAN." `$k` != ? ";
                    $ZHIS[] = $v;
                } else if (strstr($k, 'FOLK') !== false) {
                    $k = trimE(str_replace('FOLK', '', $k));
                    $v = str_replace('%','',$v);
                    $x .= " OR instr(`$k`,?) > 0" ;
                    $ZHIS[] = $v;
                } else if (strstr($k, 'FLK') !== false) {
                    $k = trimE(str_replace('FLK', '', $k));
                    $v = str_replace('%','',$v);
                    $x .= " ".$MMQQIAN." instr(`$k`,? ) > 0";
                    $ZHIS[] = $v;
                } else if (strstr($k, 'OLK') !== false) {
                    $k = trimE(str_replace('OLK', '', $k));
                    $x .= " OR `$k` LIKE ?";
                    $ZHIS[] = $v;
                } else if (strstr($k, 'LIKE') !== false) {
                    $k = trimE(str_replace('LIKE', '', $k));
                    $x .= " ".$MMQQIAN." `$k` LIKE ? ";
                    $ZHIS[] = $v;
                } else if (strstr($k, 'OR') !== false) {
                    $k = trimE(str_replace('OR', '', $k));
                    $x .= " OR `$k` = ? ";
                    $ZHIS[] = $v;
                } else if (strstr($k, 'IN') !== false) {
                    $k = trimE(str_replace('IN', '', $k));
                    if (is_array($v)){
                        $x .= " ".$MMQQIAN." `$k` IN(?)";
                        $ZHIS[] = implode(',', $v);
                    }else{
                        $x .= " ".$MMQQIAN." `$k` IN(?)";
                        $ZHIS[] = $v;
                    }

                } else if (strstr($k, 'DAYD') !== false) {
                    $k = trimE(str_replace('DAYD', '', $k));
                    $x .= " ".$MMQQIAN." $k >=?";
                    $ZHIS[] = $v;
                } else if (strstr($k, 'DAY') !== false) {
                    $k = trimE(str_replace('DAY', '', $k));
                    $x .= " ".$MMQQIAN." $k > ? ";
                    $ZHIS[] = $v;
                } else if (strstr($k, 'XIYD') !== false) {
                    $k = trimE(str_replace('XIYD', '', $k));
                    $x .= " ".$MMQQIAN." $k <= ?";
                    $ZHIS[] = $v;
                } else if (strstr($k, 'XIY') !== false) {
                    $k = trimE(str_replace('XIY', '', $k));
                    $x .= " ".$MMQQIAN." $k < ?";
                    $ZHIS[] = $v;
                } else if (strstr($k, 'DEY') !== false) {
                    $k = trimE(str_replace('DEY', '', $k));
                    $x .= " ".$MMQQIAN." $k = ? ";
                    $ZHIS[] = $v;
                } else{
                    $x .= " ".$MMQQIAN." `$k`= ? ";
                    $ZHIS[] = $v;
                }
                  
            }
            $x = str_replace(array('( OR ', '( and '), array('( ', '( '), $x);
            $x = (ltrimE(trimE($x), 'OR'));
        } else $x .= $data;

        return ['WHERE ' . (ltrimE(trimE($x), 'and')) ,$ZHIS];
    }

    public function where($data = ""){
        if ($data && $data != '') {
            if (is_array($data)) {
                $this->where_ = $this->wherezuhe($data);
            } else {
                $chaxun = $this->tablejg[1];
                $dataf = [];
                foreach ($chaxun as $k => $v) {
                    if ($v == 'auto_increment') {
                        $dataf[$k . ' IN'] = $data;
                        break;
                    }
                }

                $this->where_ = $this->wherezuhe($dataf);
            }
        }
        return $this;
    }
    
    public function find($data = ""){

        $this ->where($data);

        return  $this->zhixing('find');
    }
    public function shezhi($table = ""){

        $this ->DbName = KuoConfig['DbMain'];
        if (KuoConfig['DbDoku'] ) {
            $suiji = array_rand($this->DB, 1);
        } else {
            $suiji = $this ->DbName;
        }
        $this->lianjie($this->DB[$suiji]);
        $this ->init();
        if($table != ''){
            $this->table = $this->DB[$suiji]['prefix']. $table;
            $HASH = 'db/' . KuoMd5($this->table);
            $DATA = $GLOBALS['KuoMemClass'] ->Get($HASH);
            if($DATA && !KuoConfig['Debug']){
                $this -> tablejg = $DATA;
            }else{
                $fanhui =  $this->TableJiegou();
                if($fanhui ){
                    $zuhe = [];
                    $mmmx = array_flip($fanhui);
                    foreach ($mmmx as $zz) {
                        $zuhe[] =  Kuosql($zz) ;
                    }
                  
                    $this->tablejg = [$zuhe,$fanhui];
                    $GLOBALS['KuoMemClass'] ->set($HASH, $this->tablejg );
                }
            }
        }

        return $this;
    }
}