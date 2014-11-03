<?php
// +----------------------------------------------------------------------
// | IXCore
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.interidea.org All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: page7 <zhounan0120@gmail.com>
// +----------------------------------------------------------------------
// $Id$

/**
 +------------------------------------------------------------------------------
 * IXCore Model模型类 关联模型类
 * 2010.04.26
 * 修正 Hasone类型无错误
 +------------------------------------------------------------------------------
 * @category   IXCore
 * @package  IXCore
 * @subpackage  Core
 * @author    page7 <zhounan0120@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class RelationModel extends Model
{
    // 关联定义
    protected $RelationType = array(
        'HAS_ONE',
        'HAS_MANY',
        'BELONG_TO', //无关联数据将删除
        'MANY_TO_MANY',
    );

    //当前关联表
    protected $table = '';

    //关联表字段
    protected $fields = array();

    //默认表前后缀
    protected $tablePrefix = '';
    protected $tableSuffix = '';

    //关联配置
    protected $sql = '';
    protected $_link = array();
    protected $_linkId = array();
    protected $_linkTable = array();

    //后关联查询
    protected $after_link = array();
    protected $after_sql = '';

    //关联个数
    protected $linknum = 0;

    //字段转义
    protected $tempfields = array();

    //保存数据用于在处理
    protected $data = '';
    protected $datatree = array();
    /*
    结构 datatree = array(
            b => array(
                0 => Nameb,
                c => array( 0 => Namec )
            )
            d => array( 0 => Named )
         )
    */

    //初始化
    public function __construct($table, $tablePrefix=false, $tableSuffix=false, $config='') {
        if(empty($table)) return false;
        $this->name = strtolower($table);
        import("IXCore.Db.Db");
        if(!empty($config)) {
            // 当前模型有独立的数据库连接信息
            $this->db = Db::getInstance($config);
        }else{
            $this->db = Db::getInstance();
        }
        // 设置数据库的返回数据格式
        $this->db->resultType   =   C('DATA_RESULT_TYPE');
        //为获得ORACLE自增LastID而统一考虑的
        $this->db->tableName = $this->parseName(strtolower($table));
        // 设置默认的数据库连接
        $this->_db[0]   =   &$this->db;
        // 设置表前后缀
        $this -> tablePrefix = $tablePrefix!==false?$tablePrefix:C('DB_PREFIX');
        $this -> tableSuffix = $tableSuffix!==false?$tableSuffix:C('DB_SUFFIX');
        $this -> table = $this->getReleTableName($table);
        $this -> _linkId[$this -> table] = 'a';
        $DB = D($table, $tablePrefix, $tableSuffix, $config);
        $this -> fields['a'] = $DB -> getDbFields();
    }

/**
 +----------------------------------------------------------
 * 利用__call方法实现一些特殊的Model方法 (魔术方法)
 +----------------------------------------------------------
 * @access public
 +----------------------------------------------------------
 * @param string $method 方法名称
 * @param array $args 调用参数
 +----------------------------------------------------------
 * @return mixed
 +----------------------------------------------------------
 */
    public function __call($method, $args) {
        if(in_array(strtolower($method),array('count','sum','min','max','avg'),true)){
            // 统计查询的实现
            $field =  isset($args[1])?$args[1]:'*';
            $condition = isset($args[0])?$args[0]:'1=1';
            $sql = $this -> parseSQL(strtoupper($method), $condition, $field);
            $DB = $this->_db[0];
            $result = array_values($DB -> query($sql));
            $method = 'i_'.strtoupper($method);
            return isset($result[0][$method])?floatval($result[0][$method]):'';
        }else{
            throw_exception(__CLASS__.':'.$method.L('_METHOD_NOT_EXIST_'));
            return;
        }
    }

/**
 +-------------------------------------------------------
 * 关联数据表
 +-------------------------------------------------------
 * @param $name      关联名，也是返回值保存名
 * @param $type      关联类型
 * @param $key       被关联键名
 * @param $linktable 关联表名
 * @param $linkkey   关联键名
 * @param $betwtable 被关联表名,默认为主表
 +-------------------------------------------------------
 * @return string
 +-------------------------------------------------------
 */
    public function link($name, $type='HAS_ONE', $key='id', $linktable='', $linkkey='keyid', $fields='*' , $condition='', $order='', $limit='', $group='', $having='', $betwtable='') {
        //判断、格式化关联类型
        if(in_array(strtoupper($type), $this -> RelationType)) {
            $type = strtoupper($type);
        }else{
            return false;
        }
        //数据库 AS 字符代称
        if($this -> linknum > 25) return false;
        $id = chr(98+$this -> linknum);
        $this -> linknum++;
        //获取被关联表名
        $table = ($type=='MANY_TO_MANY' && $betwtable)?$this->getReleTableName($betwtable):$this->table;
        //获取字段
        $DB = (is_array($linktable))?D($this->getReleTableName($betwtable), false, '', ''):D($linktable);
        $dbfields = $DB -> getDbFields();
        $this -> fields[$id] = $dbfields;
        unset($dbfields['_autoInc'], $dbfields['_pk']);
        if($fields == '*') {
            $fields = $dbfields;
        }else if(empty($fields)) {
            $fields = $key;
        }
        //检测字段
        if(is_string($fields)) {
            $fields =  $fields;
        }elseif(is_array($fields)){
            $fields =  implode(',',$fields);
        }
        //获取关联表名，空值为自关联
        $linktable = $linktable ? $this->getReleTableName($linktable) : $this->table;
        $linkid = ($type=='MANY_TO_MANY' && $betwtable) ? $this->_linkId[$table] : 'a';
        $return = array('Id'=>$id,'Name'=>$name,'Type'=>$type,'Table'=>$table,'K'=>$key,'LinkTable'=>$linktable,'Key'=>$linkkey,'Link'=>"{$linkid}.{$key}={$id}.{$linkkey}",'Join'=>($type == 'BELONG_TO' || ($type=='HAS_ONE' && strripos($this->db->parseWhere($condition), $linkkey)!==false))?false:true,'Condition'=>$condition,'Fields'=>"{$fields}",'Order'=>$order,'Limit'=>$limit,'Group'=>$group,'Having'=>$having,);
        if(!$limit) {
            $this -> _link[$table][$id] = $return;
        }else{
            $this -> after_link[$table][$id] = $return;
        }
        //保存关联信息
        $this -> _linkId[$linktable] = $id;
        $this -> _linkTable[$id] = $table;
        return $this;
    }

/**
 +-------------------------------------------------------
 * 后置SQL生成(未完成....>.<)
 +-------------------------------------------------------
 */
    protected function afterSQL()
    {
        if(!isset($this -> after_link[$this->table])) return false;
        $data = $this -> after_link[$this->table];
        foreach ($data as $dbvalue)
        {
            foreach ($dbvalue as $key => $value)
            {
                if(isset($this -> _link[$value['Table']]))
                {
                    $aftersql[] = $this -> parseSQL($value['Condition'], $value['Fields'], $value['Order'], $value['Limit'], $value['Group'], $value['Having'], array($value['Id'],$value['LinkTable']));
                }else{
                    $DB = D($value['LinkTable'],'','');
                    $data[$value['Id']] = $DB -> find("{$value['Key']} IN(".import().')');
                }
            }
        }
    }

/**
 +-------------------------------------------------------
 * 关联操作 SELECT 入口、
 +-------------------------------------------------------
 * @return array
 +-------------------------------------------------------
 */
    protected function _select($list, $condition='', $fields='*', $order='', $limit='', $group='', $having='')
    {
        $DB = D();
        if($fields == '' || $fields == '*'){
            $fields = $this -> fields['a'];
            unset($fields['_pk'], $fields['_autoInc']);
        }
        $sql = $this -> parseSQL('select', $condition, $fields, $order, $limit, $group, $having);
        $this -> data = $DB -> query($sql); //print_r($this->data);
        $this -> dataFormat(); //print_r($this->data);
        //$after_sql = $this -> afterSQL();
        $this -> data = $this -> dataTree();
        if($list===false && $this -> data){
            $this -> data = array_shift($this -> data);
        }
    }

/**
 +-------------------------------------------------------
 * 关联查询
 +-------------------------------------------------------
 * @return array
 +-------------------------------------------------------
 */
    public function xfindAll($condition='', $fields='*', $order='', $limit='', $group='', $having='')
    {
        $this -> _select(true, $condition, $fields, $order, $limit, $group, $having);
        return $this -> data;
    }

/**
 +-------------------------------------------------------
 * 关联查询
 +-------------------------------------------------------
 * @return array
 +-------------------------------------------------------
 */
    public function xfind($condition, $fields='*')
    {
        $this -> _select(false, $condition, $fields, '');
        return $this -> data;
    }

/**
 +-------------------------------------------------------
 * SQL语句解析整装
 * @param mix $condition  条件，字符串字段要标明‘`’引用号
 * @param array $default 默认连接表
 +-------------------------------------------------------
 * @return string
 +-------------------------------------------------------
 */
    protected function parseSQL($action, $condition='', $fields='', $order='', $limit='', $group='', $having='', $default=array())
    {
        $id = empty($default)?'a':$default[0];
        $table = empty($default)?$this -> table:$default[1];
        //取缓存
        if(!isset($this -> _link[$table])) return ;
        $data = $this->_link[$table]; //获取可关联数据
        //数据特殊备用
        $oldfields = $fields;//特殊备用
        //数据解析
        $sql = array('Table' => array(),'Fields' => array(),'Join' => array('Table'=>array(), 'On'=>array()),'Condition' => array(),'Order'=>array(),'Group'=>array(),'Having'=>array());
        //缓存拆包
        foreach ($data as $key => $value)
        {
            //定义常用量
            $vid = $value['Id'];
            $vcondition = $value['Condition'];
            $vlinktable = $value['LinkTable'];
            //记录数据结构
            $this -> datatree[$vid][0] = $value['Name'];
            //开始记录数据
            $sql['Fields'][$vid] = $this -> parse($value['Fields'].','.$value['Key'], $vid, '{$key}.{$value} AS {$key}_{$value}');
            if($value['Join']) {
                //生成Join
                if($value['Type'] == 'MANY_TO_MANY') {
                    if($value['Table'] == $table) {
                        $return = $this -> nestingJoin($vlinktable, $this -> datatree[$vid]);
                        var_dump($return);
                        if($return) {
                            $sql['Join']['Table'][$vid] = $return['Table'];
                            $sql['Fields'][$this -> _linkId[$value['Table']]] = $return['Fields'];
                            $sql['Join']['On'][$vid] = $value['Link'];
                            if($vcondition) $sql['Join']['On'][$vid] .= ' AND ('.$this -> parseWhere($vcondition ,$vid).')';
                        }
                    }
                }else{
                    $sql['Join']['Table'][$vid] = "{$vlinktable} AS {$vid}";
                    $sql['Join']['On'][$vid] = $value['Link'];
                    if($vcondition) $sql['Join']['On'][$vid] .= ' AND ('.$this -> parseWhere($vcondition ,$vid).')';
                }
            }else{
                $sql['Table'][$vid]   =  "{$vlinktable} AS {$vid}";
                $sql['Condition'][$vid] =  $value['Link'];
                if($vcondition) $sql['Condition'][$vid] .= ' AND ('.$this -> parseWhere($vcondition ,$vid).')';
            }
            if($value['Order']) $sql['Order'][$vid]   =  $this -> parse($value['Order'], $vid, '{$key}.{$value}');
            if($value['Group']) $sql['Group'][$vid]   =  $this -> parse($value['Group'], $vid, '{$key}.{$value}');
            if($value['Having']) $sql['Having'][$vid]  =  $this -> parse($value['Having'], $vid, '{$key}.{$value}');
        }
        //添加主表记录
        $sql['Table'][$id] = $table.' AS '.$id;
        if($condition) $sql['Condition'][$id] = $this -> parseWhere($condition, $id);
        $sql['Fields'][$id] = $this -> parse($fields, $id, '{$key}.{$value} AS {$key}_{$value}');
        if($order) $sql['Order'][$id] = $this -> parse($order, $id, '{$key}.{$value}');
        if($limit) $limit = 'LIMIT '.$limit;
        if($group) $sql['Group'][$id] = $this -> parse($group, $id, '{$key}.{$value}');
        if($having) $sql['Having'][$id] = $this -> parse($having, $id, '{$key}.{$value}');
        //简单格式化
        $table = implode(',', $sql['Table']);
        $fields = implode(',', array_filter($sql['Fields']));
        $join = '';
        if(!empty($sql['Join']['Table']))
        {
            foreach($sql['Join']['Table'] as $k => $v)
            {
                $join .= ' LEFT JOIN '.$v.' ON '.$sql['Join']['On'][$k];
            }
        }
        $condition = (!empty($sql['Condition']))?' WHERE '.implode(' AND ', array_filter($sql['Condition'])):'';
        $order = (!empty($sql['Order']))?' ORDER BY '.implode(', ', array_filter($sql['Order'])):'';
        $group = (!empty($sql['Group']))?' GROUP BY '.implode(', ', array_filter($sql['Group'])):'';
        $having = (!empty($sql['Having']))?' HAVING '.implode(', ', array_filter($sql['Having'])):'';//print_r($sql);
        switch ($action) {
            case 'select':
                $sql = "SELECT {$fields} FROM {$table} {$join} {$condition} {$order} {$group} {$having} {$limit}";
                break;
            default:
                $sql = "SELECT {$action}({$oldfields}) as i_{$action} FROM {$table} {$join} {$condition}";
                break;
        }
        return $sql;
    }

/**
 +-------------------------------------------------------
 * JOIN嵌套整装
 +-------------------------------------------------------
 * @param string $table
 * @param array $tree
 +-------------------------------------------------------
 * @return string
 +-------------------------------------------------------
 */
    protected function nestingJoin($table, &$tree)
    {
        if(isset($this -> _link[$table]))
        {
            $data = $this -> _link[$table];
            foreach ($data as $value)
            {
                //记录数据结构
                $tree[$value['Id']] = array(0=>$value['Name']);
                if($value['LinkTable'] != $this -> table)
                {
                    $table = $this -> nestingJoin($value['LinkTable'], $tree['LinkTable']);
                }
                $sqlTable[] = "{$value['LinkTable']} as {$value['Id']}";
                $value['Condition'] = $this -> parseWhere($value['Condition'], $value['Id']);
                $sqlOn[] = "{$value['Link']} AND {$value['Condition']}";
                $sqlFields[] = $this -> parse($value['Fields'], $value['Id'], '{$key}.{$value} AS {$key}_{$value}');
            }
            $sql['Table'] = " ({$table} as {$this -> _linkId[$table]} LEFT JOIN ".implode(',', $sqlTable).' ON '.implode(',', $sqlOn).' ) ';
            $sql['Fields'] = implode(',', $sqlFields);
            //记录结构
            return $sql;
        }else{
            return false;
        }
    }

/**
 +-------------------------------------------------------
 * Where解析
 +-------------------------------------------------------
 * @param string $where
 * @param string $id
 * @time 2011-7-12
 +-------------------------------------------------------
 * @return string
 +-------------------------------------------------------
 */
    protected function parseWhere($where, $id){
        if(is_string($where) || is_null($where))
        {
            $this -> fields[$id];
            $whereStr =  preg_replace("/([ ]+)([`])(\w+)([`][ ]*)((=)|(!=)|(>=)|(>)|(<=)|(<)|(IN)|(NOT LIKE)|(LIKE)|(BETWEEN)|(IS NOT NULL)|(IS NULL)){1}([ ]*)/i","\$1{$id}.`\$3` \$5 ", ' '.$where);
        }else{
            $whereStr = $this -> db -> parseWhere($where);
            $whereStr =  preg_replace("/([ ]+)([`])(\w+)([`][ ]*)((=)|(!=)|(>=)|(>)|(<=)|(<)|(IN)|(NOT LIKE)|(LIKE)|(BETWEEN)|(IS NOT NULL)|(IS NULL)){1}([ ]*)/i","\$1{$id}.`\$3` \$5 ", substr($whereStr, 6));
        }
        return $whereStr;
    }

/**
 +-------------------------------------------------------
 * SQL条件的灵活便装
 * 例如 `keyid`='12' 变化成 a.`keyid`='12'
 +-------------------------------------------------------
 * @param mix $data
 * @param string $key 关键字符
 * @param string $format
 +-------------------------------------------------------
 * @param array
 +-------------------------------------------------------
 */
    protected function parse($data, $key, $format)
    {
        if(!$data) return '';
        if(is_string($data)) $data = explode(',',$data);
        $data = array_unique($data);
        foreach ($data as $value){
            $value = trim($value);
            if(!$value) break;
            if(stripos($value, 'count(')!==0 && !in_array(strtolower(substr($value,0,4)), array('sum(','min(','max(','avg(')))
            {
                eval("\$return[] = \"{$format}\";");
            }else{
                $return[] = $value;
            }
        }
        if(isset($return)) $return = implode(',', $return); else $return = '';
        return $return;
    }

/**
 +-------------------------------------------------------
 * 树状结果处理
 +-------------------------------------------------------
 * @param array $data  循环值处理
 * @param array $new  总值
 +-------------------------------------------------------
 * @return array
 +-------------------------------------------------------
 */
    protected function dataTree($tree='', $data=array())
    {
        if(!is_array($data) || !isset($this->data['a'])) return false;
        if(empty($tree)){$tree = $this -> datatree; $data = $this->data['a'];}

        //数据树解析
        foreach ($data as $key => $value){
            foreach ($tree as $k => $v){
                $name = $v[0]; unset($v[0]);
                $linkkey = $this -> data[$k]['_key'];
                foreach ($this -> data[$k] as $dkey => $dvalue){
                    //包含类型数据
                    if(count($v)>=1) $dvalue = $this -> dataTree($v, $dvalue);//多层数据
                    if(isset($value[$linkkey])){
                        if($dkey == $value[$linkkey])
                        {
                            if($name === ''){
                                $data[$key] = array_merge($data[$key], $dvalue);
                            }else{
                                $data[$key][$name] = $dvalue;
                            }
                        }
                    }else{
                        if(!is_array($value)) return;
                        $check = current($value);
                        if(isset($check[$linkkey])){
                            $data[$key] = $this -> dataTree($tree, $data[$key]);
                        }
                    }
                    //新键值没有值，则赋予空值 2011/7/19
                    if($name && !isset($data[$key][$name]))
                        $data[$key][$name] = array();
                }
            }
        }
        return $data;
    }


/**
 +-------------------------------------------------------
 * 关联查询数据格式化
 +-------------------------------------------------------
 * @param array $data
 * @param bool  $list
 +-------------------------------------------------------
 */
    protected function dataFormat($data='', $list=true)
    {
        $formatdata = empty($data) ? $this -> data : $data;//print_r($this -> _link);
        if(!$formatdata) return $formatdata;
        //关键转义
        $LinkId = array_flip($this -> _linkId);
        //命名记录
        $LinkName = array();
        foreach ($formatdata as $key => $value)
        {
            foreach ($value as $k => $v)
            {
                $Id = substr($k,0,1);
                $Field = substr($k,2);
                if(isset($this->_linkTable[$Id])){
                    $Table = $this->_linkTable[$Id];
                    $Key = $this->_link[$Table][$Id]['Key'];
                    $Type = $this->_link[$Table][$Id]['Type'];
                    $Fields = explode(',', $this->_link[$Table][$Id]['Fields']);
                    $new[$Id]['_key'] = $this->_link[$Table][$Id]['K'];
                    switch ($Type){
                        case 'MANY_TO_MANY':
                            $Type = $this -> _link[$Table][$Id]['Table'] == $this -> table ? 1 : 0;
                            break;
                        case 'HAS_MANY':
                            $Type = 1;
                            break;
                        default:
                            $Type = 0;
                    }
                }
                if($Id == 'a') {
                    if(isset($this -> fields['a']['_pk'])) {
                        $Key = $this -> fields['a']['_pk'];
                    }else{
                        $value["{$Id}_{$Key}"] = $key;
                    }
                    $Type = 0;
                }
                if($Type == 0) {
                    if($Id == 'a' || in_array($Field, $Fields)) $new[$Id][$value["{$Id}_{$Key}"]][$Field] = $v;
                }else{
                    if(count($Fields) == 1 && $Field == $Fields[0])
                    {
                        $new[$Id][$value["{$Id}_{$Key}"]][$key] = $v;
                    }else{
                        if(in_array($Field, $Fields)) $new[$Id][$value["{$Id}_{$Key}"]][$key][$Field] = $v;
                    }
                }
            }
        }
        $new = isset($new) ? $new : array();
        $this -> data = empty($data) ? $new : array_merge($this->data, $new);
    }

/**
 +-------------------------------------------------------
 * 保存SQL
 +-------------------------------------------------------
 * @param unknown_type $type
 +-------------------------------------------------------
 */
    public function savelink($type) {
        $countlink = count($this -> _linkId);
        $name = md5("{$type}_{$this->table}_{$countlink}_".C_MODULE_NAME.'_'.C_ACTION_NAME);
        $data = array(
            //关联查询
            'sql' => $this->sql,
            'link' => $this->_link,
            'linkId' => $this->_linkId,
            'linkTable' => $this->_linkTable,
            'after_sql' => $this->after_sql,
            'after_link' => $this->after_link,
        );
        if(C('DB_FIELDS_CACHE')) {
            F($name,$data);
        }
    }

/**
 +-------------------------------------------------------
 * 得到关联数据表名
 +-------------------------------------------------------
 * @param mix $table
 +-------------------------------------------------------
 * @return string
 +-------------------------------------------------------
 */
    public function getReleTableName($table)
    {
        if(is_array($table))
        {
            $relationTable  = !empty($table[1]) ? $table[1] : '';
            $relationTable .= $table[0];
            $relationTable .= !isset($table[2]) ? $table[2] : '';
        }else if(is_object($table) && substr(get_class($table),-5,0)=='model'){
            $relationTable = $table -> getTableName();
        }else{
            $relationTable = $this->tablePrefix.$table.$this->tableSuffix;
        }
        return strtolower($relationTable);
    }

/**
 +-------------------------------------------------------
 * 得到关联数据表ID
 +-------------------------------------------------------
 * @param mix $name
 +-------------------------------------------------------
 * @return string
 +-------------------------------------------------------
 */
    public function tableId($name)
    {
        return $this -> _linkId[$name]?$this -> _linkId[$name]:'a';
    }

}
?>