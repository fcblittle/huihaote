<?php
// +----------------------------------------------------------------------
// | IXCore
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://www.interidea.org All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: page7 <zhounan0120@gmail.com>
// +----------------------------------------------------------------------
// $Id$

/**
 +------------------------------------------------------------------------------
 * IXCore Model模型类 抽象类
 * 实现了ORM和ActiveRecords模式
 +------------------------------------------------------------------------------
 * @category   IXCore
 * @package  IXCore
 * @subpackage  Core
 * @author    page7 <zhounan0120@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class Model extends Base  implements IteratorAggregate
{
    // 数据库连接对象列表
    protected $_db = array();

    // 当前数据库操作对象
    protected $db = null;

    // 数据表前缀
    protected $tablePrefix  =   '';

    // 数据表后缀
    protected $tableSuffix = '';

    // 模型名称
    protected $name = '';

    // 数据库名称
    protected $dbName  = '';

    // 数据表名（不包含表前缀）
    protected $tableName = '';

    // 实际数据表名（包含表前缀）
    protected $trueTableName ='';

    // 字段信息
    protected $fields = array();

    // 字段类型信息
    protected $type  =   array();

    // 数据信息
    protected $data =   array();

    // 查询表达式参数
    protected $options  =   array();

    // 数据列表信息
    protected $dataList =   array();

    // 上次错误信息
    protected $error = '';

    // 乐观锁
    protected $optimLock = 'lock_version';
    // 悲观锁
    protected $pessimisticLock = false;

    protected $lazyQuery   =   false;                  // 是否启用惰性查询

    /**
     +----------------------------------------------------------
     * 架构函数
     * 取得DB类的实例对象 数据表字段检查
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    public function __construct($table='', $tablePrefix=false, $tableSuffix=false, $config='')
    {
        // 模型初始化

        $this->_initialize();
        // 模型名称获取
        $this->name = strtolower($table);
        //数据库初始化
        import("IXCore.Db.Db");
        // 获取数据库操作对象
        if(!empty($config)) {
            // 当前模型有独立的数据库连接信息
            $this->db = Db::getInstance($config);
        }else{
            $this->db = Db::getInstance();
        }
        // 设置数据库的返回数据格式
        $this->db->resultType   =   C('DATA_RESULT_TYPE');
        //为获得ORACLE自增LastID而统一考虑的
        $this->db->tableName = $this->parseName($this->name);
        // 设置默认的数据库连接
        $this->_db[0]   =   &$this->db;
        // 设置表前后缀
        $this->tablePrefix = $tablePrefix!==false ? $tablePrefix : C('DB_PREFIX');
        $this->tableSuffix = $tableSuffix!==false ? $tableSuffix : C('DB_SUFFIX');
        // 数据表字段检测
        $this->_checkTableInfo();
    }

    /**
     +----------------------------------------------------------
     * 取得模型实例对象
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @return mixed 返回数据模型实例
     +----------------------------------------------------------
     */
    public static function getInstance()
    {
        return get_instance_of(__CLASS__);
    }

    /**
     +----------------------------------------------------------
     * 设置数据对象的值 （魔术方法）
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $name 名称
     * @param mixed $value 值
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function __set($name,$value) {
        // 设置数据对象属性
        $this->data[$name]  =   $value;
    }

    /**
     +----------------------------------------------------------
     * 获取数据对象的值 （魔术方法）
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $name 名称
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function __get($name) {
        if(isset($this->data[$name])) {
            return $this->data[$name];
        }elseif(property_exists($this,$name)){
            return $this->$name;
        }else{
            return null;
        }
    }

    /**
     +----------------------------------------------------------
     * 字符串命名风格转换
     * type
     * =0 将Java风格转换为C的风格
     * =1 将C风格转换为Java的风格
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param string $name 字符串
     * @param integer $type 转换类型
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    protected function parseName($name,$type=0) {
        if($type) {
            return preg_replace("/_([a-zA-Z])/e", "strtoupper('\\1')", $name);
        }else{
            $name = preg_replace("/[A-Z]/", "_\\0", $name);
            return strtolower(trim($name, "_"));
        }
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
        if(in_array(strtolower($method),array('field','table','where','order','limit','having','group','lock','lazy','cache'),true)) {
            // 连贯操作的实现
            $this->options[strtolower($method)] =   $args[0];
            return $this;
        }elseif(in_array(strtolower($method),array('count','sum','min','max','avg'),true)){
            // 统计查询的实现
            $field =  isset($args[1])?$args[1]:'*';
            $condition = isset($args[0])?$args[0]:'1=1';
            $result = array_values($this->find($condition, strtoupper($method).'('.$field.') AS i_'.$method));
            return floatval($result[0]);
        }else{
            throw_exception(__CLASS__.':'.$method.L('_METHOD_NOT_EXIST_'));
            return;
        }
    }
    // 回调方法 初始化模型
    protected function _initialize() {}

    /**
     +----------------------------------------------------------
     * 数据库Create操作入口
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @param array $data 要create的数据
     * @param bool $multi 多数据操作
     +----------------------------------------------------------
     * @return false|integer
     +----------------------------------------------------------
     */
    private function _create(&$data,$multi=false) {
        // 插入数据库
        if(false === $result = $this->db->add($data,$this->getTableName(),$multi)){
            // 数据库插入操作失败
            $this->error = L('_OPERATION_WRONG_');
            return false;
        }else {
            $insertId   =   $this->getLastInsID();
            if($insertId && !isset($data[$this->getPk()])) {
                $data[$this->getPk()]   =    $insertId;
            }
            //成功后返回插入ID
            return $insertId ?  $insertId   : $result;
        }
    }

    /**
     +----------------------------------------------------------
     * 数据库Update操作入口
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @param array $data 要update的数据
     * @param mixed $where 更新条件
     * @param string $limit limit
     * @param string $order order
     * @param boolean $lock 是否加锁
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    private function _update(&$data,$where='',$limit='',$order='',$lock=false) {
        $table      =   $this->getTableName();
        if(!empty($this->options)) {
            // 已经有定义的查询表达式
            $where   =   isset($this->options['where'])?     $this->options['where']:    $where;
            $limit      =   isset($this->options['limit'])?     $this->options['limit']:        $limit;
            $order    =   isset($this->options['order'])?     $this->options['order']:    $order;
            $lock      =   isset($this->options['lock'])?      $this->options['lock']:     $lock;
            $table     =   isset($this->options['table'])?     $this->options['table']:    $this->getTableName();
            $this->options  =   array();
        }
        $lock    =   ($this->pessimisticLock || $lock);
        if(false ===$this->db->save($data,$table,$where,$limit,$order,$lock) ){
            $this->error = L('_OPERATION_WRONG_');
            return false;
        }else {
            return true;
        }
    }

    /**
     +----------------------------------------------------------
     * 数据库Read操作入口
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @param mixed $condition 查询条件
     * @param string $fields 查询字段
     * @param boolean $all 是否返回多个数据
     * @param string $order
     * @param string $limit
     * @param string $group
     * @param string $having
     * @param string $join
     * @param boolean $cache 是否查询缓存
     * @param boolean $lazy 是否惰性查询
     * @param boolean $lock 是否加锁
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    private function _read($condition='',$fields='*',$all=false,$order='',$limit='',$group='',$having='',$join='',$cache=false,$lazy=false,$lock=false) {
        $table      =   $this->getTableName();
        if(!empty($this->options)) {
            // 已经有定义的查询表达式
            $condition  =   isset($this->options['where'])?         $this->options['where']:    $condition;
            $table       =   isset($this->options['table'])?         $this->options['table']:    $this->getTableName();
            $fields       =   isset($this->options['field'])?         $this->options['field']:    $fields;
            $limit        =   isset($this->options['limit'])?         $this->options['limit']:        $limit;
            $order      =   isset($this->options['order'])?         $this->options['order']:    $order;
            $group      =   isset($this->options['group'])?         $this->options['group']:    $group;
            $having     =   isset($this->options['having'])?        $this->options['having']:   $having;
            $join         =   isset($this->options['join'])?          $this->options['join']:     $join;
            $cache      =   isset($this->options['cache'])?         $this->options['cache']:    $cache;
            $lock         =   isset($this->options['lock'])?          $this->options['lock']:     $lock;
            $lazy        =   isset($this->options['lazy'])?          $this->options['lazy']: $lazy;
            $this->options  =   array();
        }
        if($cache) {//启用动态数据缓存
            if($all) {
                $identify   = $this->name.'List_'.to_guid_string(func_get_args());
            }else{
                $identify   = $this->name.'_'.to_guid_string($condition);
            }
            $result  =  S($identify);
            if(false !== $result) {
                if(!$all) {
                    $this->cacheLockVersion($result);
                }
                return $result;
            }
        }
        $lazy    =   ($this->lazyQuery || $lazy);
        $lock    =   ($this->pessimisticLock || $lock);
        $rs = $this->db->find($condition,$table,$fields,$order,$limit,$group,$having,$join,$cache,$lazy,$lock);
        $result =   $this->rsToVo($rs,$all,0);
        if($result && $cache) {
            S($identify,$result);
        }
        return $result;
    }

    /**
     +----------------------------------------------------------
     * 数据库Delete操作入口
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @param mixed $data 删除的数据
     * @param mixed $condition 查询条件
     * @param string $limit
     * @param string $order
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    private function _delete($data,$where='',$limit=0,$order='') {
        $table      =   $this->getTableName();
        if(!empty($this->options)) {
            // 已经有定义的查询表达式
            $where      =   isset($this->options['where'])?     $this->options['where']:    $where;
            $table          =   isset($this->options['table'])?     $this->options['table']:    $this->getTableName();
            $limit          =   isset($this->options['limit'])?     $this->options['limit']:        $limit;
            $order      =   isset($this->options['order'])?     $this->options['order']:    $order;
            $this->options  =   array();
        }
        $result=    $this->db->remove($where,$table,$limit,$order);
        if(false === $result ){
            $this->error =  L('_OPERATION_WRONG_');
            return false;
        }else {
            //返回删除记录个数
            return $result;
        }
    }
    /**
     +----------------------------------------------------------
     * 数据库Query操作入口(使用SQL语句的Query）
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @param mixed $sql 查询的SQL语句
     * @param boolean $cache 是否使用查询缓存
     * @param boolean $lazy 是否惰性查询
     * @param boolean $lock 是否加锁
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    private function _query($sql='',$cache=false,$lazy=false,$lock=false) {
        if(!empty($this->options)) {
            $sql        =   isset($this->options['sql'])?           $this->options['sql']:      $sql;
            $cache  =   isset($this->options['cache'])?     $this->options['cache']:    $cache;
            $lazy       =   isset($this->options['lazy'])?      $this->options['lazy']: $lazy;
            $lock       =   isset($this->options['lock'])?      $this->options['lock']:     $lock;
            $this->options  =   array();
        }
        if($cache) {//启用动态数据缓存
            $identify   = md5($sql);
            $result =   S($identify);
            if(false !== $result) {
                return $result;
            }
        }
        $lazy    =   ($this->lazyQuery || $lazy);
        $lock    =   ($this->pessimisticLock || $lock);
        $result =   $this->db->query($sql,$cache,$lazy,$lock);
        if($cache)    S($identify,$result);
        return $result;
    }

    /**
     +----------------------------------------------------------
     * 数据表字段检测 并自动缓存
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    private function _checkTableInfo() {
        // 如果不是Model类 自动记录数据表信息
        // 只在第一次执行记录
        if(empty($this->fields) && $this -> name != '') {
            // 如果数据表字段没有定义则自动获取
            if(C('DB_FIELDS_CACHE')) {
                $identify   =   $this->name.'_fields';
                $this->fields = F($identify);
                if(!$this->fields) {
                    $this->flush();
                }
            }else{
                // 每次都会读取数据表信息
                $this->flush();
            }
        }
    }

    /**
     +----------------------------------------------------------
     * 强制刷新数据表信息
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function flush() {
        // 缓存不存在则查询数据表信息
        $fields =   $this->db->getFields($this->getTableName());
        $this->fields   =   array_keys($fields);
        $this->fields['_autoInc'] = false;
        foreach ($fields as $key=>$val){
            // 记录字段类型
            //$this->type[$key]    =   $val['type'];
            if($val['primary']) {
                $this->fields['_pk']    =   $key;
                if($val['autoInc']) $this->fields['_autoInc']   =   true;
            }
        }
        // 2008-3-7 增加缓存开关控制
        if(C('DB_FIELDS_CACHE')) {
            // 永久缓存数据表信息
            // 2007-10-31 更改为F方法保存，保存在项目的Data目录，并且始终采用文件形式
            $identify   =   $this->name.'_fields';
            F($identify,$this->fields);
        }
    }

    /**
     +-----------------------------------------------------------
     * 自动获取
     +-----------------------------------------------------------
     * @param array $data
     +-----------------------------------------------------------
     * @return array
     +-----------------------------------------------------------
     */
    public function getValues($data)
    {
        $fields = $this -> fields;
        $return = array();
        $temp = array_values($data);
        if(is_array($temp[0])){
            foreach ($data as $val) {
                $return[] = $this -> getValue($val);
            }
            $this -> dataList = $return;
        }else{
            foreach ($fields as $val) {
                if(isset($data[$val])) $return[$val] = $data[$val];
            }
            $this -> data = $return;
        }
        return empty($return)?'':$return;
    }

    /**
     +----------------------------------------------------------
     * 获取Iterator因子
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return Iterate
     +----------------------------------------------------------
     */
    public function getIterator()
    {
        if(!empty($this->dataList)) {
            // 存在数据集则返回数据集
            return new ArrayObject($this->dataList);
        }elseif(!empty($this->data)){
            // 存在数据对象则返回对象的Iterator
            return new ArrayObject($this->data);
        }else{
            // 否则返回字段名称的Iterator
            $fields = $this->fields;
            unset($fields['_pk'],$fields['_autoInc']);
            return new ArrayObject($fields);
        }
    }

    /**
     +----------------------------------------------------------
     * 把数据对象转换成数组
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    public function toArray()
    {
        if(!empty($this->dataList)) {
            return $this->dataList;
        }elseif (!empty($this->data)){
            return $this->data;
        }
        return false;
    }

    /**
     +----------------------------------------------------------
     * 新增数据 支持数组、HashMap对象、std对象、数据对象
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $data 数据
     * @param boolean $multi 多数据写入
     +----------------------------------------------------------
     * @return int
     +----------------------------------------------------------
     */
    public function add($data=null, $multi=false)
    {
        if(empty($data)) {
            // 没有传递数据，获取当前数据对象的值
            if(!empty($this->options['data'])) {
                $data    =   $this->options['data'];
            }elseif(!empty($this->data)) {
                $data    =   $this->data;
            }elseif(!empty($this->dataList)){
                return $this->addAll($this->dataList);
            }else{
                $this->error = L('_DATA_TYPE_INVALID_');
                return false;
            }
        }
        // 对保存到数据库的数据对象进行处理
        $data   =   $this->_facade($data);
        if(!$data) {
            $this->error = L('_DATA_TYPE_INVALID_');
            return false;
        }
        // 记录乐观锁
        if($this->optimLock && !isset($data[$this->optimLock]) ) {
            if(in_array($this->optimLock,$this->fields,true)) {
                $data[$this->optimLock]  =   0;
            }
        }
        return $this->_create($data);
    }

    /**
     +----------------------------------------------------------
     * 对保存到数据库的数据对象进行处理
     * 统一使用数组方式到数据库中间层 facade字段支持
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param mixed $data 要操作的数据
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    protected function _facade($data) {
        if(is_instance_of($data,'HashMap')){
            // Map对象转换为数组
            $data = $data->toArray();
        }elseif(is_object($data)) {
            $data    =   get_object_vars($data);
        }elseif(is_string($data)){
            parse_str($data,$data);
        }elseif(!is_array($data)){
            return false;
        }
        // 检查非数据字段
        foreach ($data as $key=>$val){
            if(!in_array($key,$this->fields,true)) {
                unset($data[$key]);
            }
        }
        return $data;
    }

    /**
     +----------------------------------------------------------
     * 批量新增数据
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $dataList 数据列表
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    public function addAll($dataList='')
    {
        if(empty($dataList)) {
            $dataList   =   $this->dataList;
        }elseif(!is_array($dataList)) {
            $this->error = L('_DATA_TYPE_INVALID_');
            return false;
        }
        return $this->_create($dataList,true);
    }

    /**
     +----------------------------------------------------------
     * 更新数据
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $data 要更新的数据
     * @param mixed $where 更新数据的条件
     * @param integer $limit 要更新的记录数
     * @param string $order  更新的顺序
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    public function save($data=null,$where='',$limit=0,$order='')
    {
        if(empty($data)) {
            if(!empty($this->options['data'])) {
                $data    =   $this->options['data'];
            }elseif(!empty($this->data)) {
                // 保存当前数据对象
                $data    =   $this->data;
            }elseif(!empty($this->dataList)){
                // 保存当前数据集
                $data    =   $this->dataList;
                $this->startTrans();
                foreach ($data as $val){
                    $result   =  $this->save($val,$where);
                }
                $this->commit();
                return $result;
            }
        }
        //多数据更新
        $pk   =  $this->getPk();
        $dates = (isset($data[0]) && is_array($data[0]) && isset($data[0][$pk]) && !is_array($data[0][$pk]))?true:false;
        if(!$dates)
        {
            $data  =  $this->_facade($data);
            if(!$data) {
                $this->error = L('_DATA_TYPE_INVALID_');
                return false;
            }
            if(empty($where) && isset($data[$pk]) && !is_array($data[$pk])) {
                $where  = "`$pk`='{$data[$pk]}'";
                //unset($data[$pk]);
            }
            // 检查乐观锁
            if(!$this->checkLockVersion($data,$where)) {
                $this->error = L('_RECORD_HAS_UPDATE_');
                return false;
            }
            return $this->_update($data,$where,$limit,$order);

        }else{
            foreach ($data as $val){
                $result  =  $this->save($val);
            }
            return $result;
        }

    }

    /**
     +----------------------------------------------------------
     * 检查乐观锁
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param array $data  当前数据
     * @param mixed $where 查询条件
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    protected function checkLockVersion(&$data, &$where='') {
        $pk =   $this->getPk();
        $id =   $data[$pk];
        if(empty($where) && isset($id) ) {
            $where  = $pk."=".$id;
        }
        // 检查乐观锁
        $identify   = $this->name.'_'.$id.'_lock_version';
        if($this->optimLock && isset($_SESSION[$identify])) {
            $lock_version = $_SESSION[$identify];
            if(!empty($where)) {
                $vo = $this->find($where,$this->optimLock);
            }else {
                $vo = $this->find($data,$this->optimLock);
            }
            $_SESSION[$identify]     =   $lock_version;
            $curr_version = is_array($vo)?$vo[$this->optimLock]:$vo->{$this->optimLock};
            if(isset($curr_version)) {
                if($curr_version>0 && $lock_version != $curr_version) {
                    // 记录已经更新
                    return false;
                }else{
                    // 更新乐观锁
                    $save_version = $data[$this->optimLock];
                    if($save_version != $lock_version+1) {
                        $data[$this->optimLock]  =   $lock_version+1;
                    }
                    $_SESSION[$identify]     =   $lock_version+1;
                }
            }
        }
        unset($data[$pk]);
        return true;
    }

    /**
     +----------------------------------------------------------
     * 根据条件删除表数据
     * 如果成功返回删除记录个数
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $data 删除条件
     * @param integer $limit 要删除的记录数
     * @param string $order  删除的顺序
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    public function delete($data=null,$limit='',$order='')
    {
        if(preg_match('/^\d+(\,\d+)*$/',$data)) { //如果是数字
            return $this->_delete(false,$this->getPk()." IN ($data)",$limit,$order);
        }
        if(empty($data)) {
            $data    =   $this->data;
        }
        $pk   =  $this->getPk();
        if(is_array($data) && isset($data[$pk]) && !is_array($data[$pk])) {
            $data   =   $this->_facade($data);
            $where  = $pk."=".$data[$pk];
        }else {
            $where  =   $data;
        }
        return $this->_delete($data,$where,$limit,$order);
    }

    /**
     +----------------------------------------------------------
     * 根据条件删除数据
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $condition 删除条件
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    public function deleteAll($condition='')
    {
        if(is_instance_of($condition,'HashMap')) {
            $condition    = $condition->toArray();
        }elseif(empty($condition) && !empty($this->dataList)){
            $id = array();
            foreach ($this->dataList as $data){
                $data = (array)$data;
                $id[]    =   $data[$this->getPk()];
            }
            $ids = implode(',',$id);
            $condition = $this->getPk().' IN ('.$ids.')';
        }
        return $this->_delete(false,$condition,0,'');
    }

    /**
     +----------------------------------------------------------
     * 根据条件得到一条记录
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $condition 条件
     * @param string $fields 字段名，默认为*
     * @param boolean $cache 是否读取缓存
     * @param boolean $lazy 是否惰性查询
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function find($condition='',$fields='*',$cache=false,$lazy=false)
    {
        return $this->_read($condition,$fields,false,null,null,null,null,null,$cache,$lazy);
    }

    /**
     +----------------------------------------------------------
     * 查找记录
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $condition  条件
     * @param string $fields  查询字段
     * @param string $order  排序
     * @param string $limit
     * @param string $group
     * @param string $having
     * @param string $join
     * @param boolean $cache 是否读取缓存
     * @param boolean $lazy 是否惰性查询
     +----------------------------------------------------------
     * @return array|ResultIterator
     +----------------------------------------------------------
     */
    public function findAll($condition='',$fields='*',$order='',$limit='',$group='',$having='',$join='',$cache=false,$lazy=false)
    {
        return $this->_read($condition,$fields,true,$order,$limit,$group,$having,$join,$cache,$lazy);
    }

    /**
     +----------------------------------------------------------
     * SQL查询
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $sql  SQL指令
     * @param boolean $cache  是否缓存
     * @param boolean $lazy  是否惰性查询
     +----------------------------------------------------------
     * @return array|ResultIterator
     +----------------------------------------------------------
     */
    public function query($sql,$cache=false,$lazy=false)
    {
        if(empty($sql) && !empty($this->options['sql'])) {
            $sql    =   $this->options['sql'];
        }
        if(!empty($sql)) {
            if(strpos($sql,'__TABLE__')) {
                $sql    =   str_replace('__TABLE__',$this->getTableName(),$sql);
            }
            return $this->_query($sql,$cache,$lazy);
        }else{
            return false;
        }
    }

    /**
     +----------------------------------------------------------
     * 执行SQL语句
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $sql  SQL指令
     +----------------------------------------------------------
     * @return false | integer
     +----------------------------------------------------------
     */
    public function execute($sql='')
    {
        if(empty($sql) && !empty($this->options['sql'])) {
            $sql    =   $this->options['sql'];
        }
        if(!empty($sql)) {
            if(strpos($sql,'__TABLE__')) {
                $sql    =   str_replace('__TABLE__',$this->getTableName(),$sql);
            }
            $result =   $this->db->execute($sql);
            return $result;
        }else {
            return false;
        }
    }

    /**
     +----------------------------------------------------------
     * 设置记录的某个字段值
     * 支持使用数据库字段和方法
     * 例如 setField('score','(score+1)','id=5');
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string|array $field  字段名
     * @param string|array $value  字段值
     * @param mixed $condition  条件
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function setField($field,$value,$condition='',$asString=true) {
        if(empty($condition) && isset($this->options['where'])) {
            $condition   =  $this->options['where'];
        }
        return $this->db->setField($field,$value,$this->getTableName(),$condition,$asString);
    }

    /**
     +----------------------------------------------------------
     * 查询符合条件的第N条记录
     * 0 表示第一条记录 -1 表示最后一条记录
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param integer $position 记录位置
     * @param mixed $condition 条件
     * @param string $order 排序
     * @param string $fields 字段名，默认为*
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function getN($position=0,$condition='',$order='',$fields='*')
    {
        $table      =   $this->getTableName();
        if(!empty($this->options)) {
            // 已经有定义的查询表达式
            $condition  =   $this->options['where']?            $this->options['where']:    $condition;
            $table          =   $this->options['table']?            $this->options['table']:    $this->getTableName();
            $fields     =   $this->options['filed']?            $this->options['field']:    $fields;
            $limit          =   $this->options['limit']?            $this->options['limit']:        $limit;
            $order      =   $this->options['order']?            $this->options['order']:    $order;
            $this->options  =   array();
        }
        if($position>=0) {
            $rs = $this->db->find($condition,$table,$fields,$order,$position.',1');
            return $this->rsToVo($rs,false,0);
        }else{
            $rs = $this->db->find($condition,$this->getTableName(),$fields,$order);
            return $this->rsToVo($rs,false,$position);
        }
    }

    /**
     +----------------------------------------------------------
     * 获取满足条件的第一条记录
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $condition 条件
     * @param string $fields 字段名，默认为*
     * @param string $order 排序
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function first($condition='',$order='',$fields='*') {
        return $this->getN(0,$condition,$order,$fields);
    }

    /**
     +----------------------------------------------------------
     * 获取满足条件的第后一条记录
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $condition 条件
     * @param string $fields 字段名，默认为*
     * @param string $order 排序
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function last($condition='',$order='',$fields='*') {
        return $this->getN(-1,$condition,$order,$fields);
    }

    /**
     +----------------------------------------------------------
     * 记录乐观锁
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $data 数据对象
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    protected function cacheLockVersion($data) {
        if($this->optimLock) {
            if(is_object($data))    $data   =   get_object_vars($data);
            if(isset($data[$this->optimLock]) && isset($data[$this->getPk()])) {
                // 只有当存在乐观锁字段和主键有值的时候才记录乐观锁
                $_SESSION[$this->name.'_'.$data[$this->getPk()].'_lock_version']    =   $data[$this->optimLock];
            }
        }
    }

    /**
     +----------------------------------------------------------
     * 把查询结果转换为数据（集）对象
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $resultSet 查询结果记录集
     * @param Boolean $returnList 是否返回记录集
     * @param Integer $position 定位的记录集位置
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function rsToVo($resultSet,$returnList=false,$position=0)
    {
        if($resultSet ) {
            if(!$returnList) {
                if(is_instance_of($resultSet,'ResultIterator')) {
                    // 如果是延时查询返回的是ResultIterator对象
                    $resultSet  =   $resultSet->getIterator();
                }
                // 返回数据对象
                if($position<0) {
                    // 逆序查找
                    $position = count($resultSet)-abs($position);
                    if($position < 0) return null;
                }
                if(count($resultSet)<= $position) {
                    // 记录集位置不存在
                    $this->error = L('_SELECT_NOT_EXIST_');
                    return false;
                }
                $result  =  $resultSet[$position];
                // 取出数据对象的时候记录乐观锁
                $this->cacheLockVersion($result);
                // 对数据对象自动编码转换
                $result  =  auto_charset($result,C('DB_CHARSET'),C('TEMPLATE_CHARSET'));
                // 记录当前数据对象
                $this->data  =   (array)$result;
                return $result;
            }else{
                if(is_instance_of($resultSet,'ResultIterator')) {
                    // 如果是延时查询返回的是ResultIterator对象
                    return $resultSet;
                }
                // 对数据集对象自动编码转换
                $resultSet  =  auto_charset($resultSet,C('DB_CHARSET'),C('TEMPLATE_CHARSET'));
                // 记录数据列表
                $this->dataList =   $resultSet;
                return $resultSet;
            }
        }else {
            return false;
        }
    }

    /**
     +----------------------------------------------------------
     * 得到完整的数据表名
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function getTableName()
    {
        if(empty($this->trueTableName)) {
            $tableName  = !empty($this->tablePrefix) ? $this->tablePrefix : '';
            if(!empty($this->tableName)) {
                $tableName .= $this->tableName;
            }elseif(C('TABLE_NAME_IDENTIFY')){
                // 智能识别表名
                $tableName .= $this->parseName($this->name);
            }else{
                $tableName .= $this->name;
            }
            $tableName .= $this->tableSuffix;
            if(!empty($this->dbName)) {
                $tableName    =  $this->dbName.'.'.$tableName;
            }
            $this->trueTableName    =   strtolower($tableName);
        }
        return $this->trueTableName;
    }

    /**
     +----------------------------------------------------------
     * 开启惰性查询
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function startLazy()
    {
        $this->lazyQuery = true;
        return ;
    }

    /**
     +----------------------------------------------------------
     * 关闭惰性查询
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function stopLazy()
    {
        $this->lazyQuery = false;
        return ;
    }

    /**
     +----------------------------------------------------------
     * 开启锁机制
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function startLock()
    {
        $this->pessimisticLock = true;
        return ;
    }

    /**
     +----------------------------------------------------------
     * 关闭锁机制
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function stopLock()
    {
        $this->pessimisticLock = false;
        return ;
    }

    /**
     +----------------------------------------------------------
     * 启动事务
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function startTrans()
    {
        $this->commit();
        $this->db->startTrans();
        return ;
    }

    /**
     +----------------------------------------------------------
     * 提交事务
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function commit()
    {
        return $this->db->commit();
    }

    /**
     +----------------------------------------------------------
     * 事务回滚
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function rollback()
    {
        return $this->db->rollback();
    }

    /**
     +----------------------------------------------------------
     * 得到主键名称
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function getPk() {
        return isset($this->fields['_pk'])?$this->fields['_pk']:'id';
    }

    /**
     +----------------------------------------------------------
     * 返回当前错误信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function getError(){
        return $this->error;
    }

    /**
     +----------------------------------------------------------
     * 返回数据库字段信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function getDbFields(){
        return $this->fields;
    }

    /**
     +----------------------------------------------------------
     * 返回最后插入的ID
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function getLastInsID() {
        return $this->db->lastInsID;
    }

    /**
     +----------------------------------------------------------
     * 返回操作影响的记录数
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function getAffectRows() {
        return $this->db->numRows;
    }

    /**
     +----------------------------------------------------------
     * 返回最后执行的sql语句
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function getLastSql() {
        return $this->db->getLastSql();
    }

    /**
     +----------------------------------------------------------
     * 增加数据库连接
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $config 数据库连接信息
     * 支持批量添加 例如 array(1=>$config1,2=>$config2)
     * @param mixed $linkNum  创建的连接序号
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function addConnect($config,$linkNum=NULL) {
        if(isset($this->_db[$linkNum])) {
            return false;
        }
        if(NULL === $linkNum && is_array($config)) {
            // 支持批量增加数据库连接
            foreach ($config as $key=>$val){
                $this->_db[$key]            =    Db::getInstance($val);
            }
            return true;
        }
        // 创建一个新的实例
        $this->_db[$linkNum]            =    Db::getInstance($config);
        return true;
    }

    /**
     +----------------------------------------------------------
     * 删除数据库连接
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param integer $linkNum  创建的连接序号
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function delConnect($linkNum) {
        if(isset($this->_db[$linkNum])) {
            $this->_db[$linkNum]->close();
            unset($this->_db[$linkNum]);
            return true;
        }
        return false;
    }

    /**
     +----------------------------------------------------------
     * 关闭数据库连接
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param integer $linkNum  创建的连接序号
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function closeConnect($linkNum) {
        if(isset($this->_db[$linkNum])) {
            $this->_db[$linkNum]->close();
            return true;
        }
        return false;
    }

    /**
     +----------------------------------------------------------
     * 切换数据库连接
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param integer $linkNum  创建的连接序号
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    public function switchConnect($linkNum) {
        if(isset($this->_db[$linkNum])) {
            // 在不同实例直接切换
            $this->db   =   $this->_db[$linkNum];
            return true;
        }else{
            return false;
        }
    }

    /**
     +----------------------------------------------------------
     * 查询SQL组装 join
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $join
     +----------------------------------------------------------
     * @return Model
     +----------------------------------------------------------
     */
    public function join($join) {
        if(is_array($join)) {
            $this->options['join'] =  $join;
        }else{
            $this->options['join'][]  =   $join;
        }
        return $this;
    }


};
?>