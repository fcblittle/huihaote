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
 * IXCore公共函数库
 +------------------------------------------------------------------------------
 * @category   IXCore
 * @package  Common
 * @author   page7 <zhounan0120@gmail.com>
 * @version  $Id$
 +------------------------------------------------------------------------------
 */
// URL组装 支持不同模式和路由
function U($url,$params=array(),$redirect=false,$suffix=true) {
    if(0===strpos($url,'/'))
        $url   =  substr($url,1);
    if(!strpos($url,'://')) // 没有指定项目名 使用当前项目名
        $url   =  APP_NAME.'://'.$url;
    if(stripos($url,'@?')) { // 给路由传递参数
        $url   =  str_replace('@?','@think?',$url);
    }elseif(stripos($url,'@')) { // 没有参数的路由
        $url   =  $url.MODULE_NAME;
    }

    // 分析URL地址
    $array   =  parse_url($url);
    $app      =  isset($array['scheme'])?   $array['scheme']  :APP_NAME;
    $route    =  isset($array['user'])?$array['user']:'';
    if(defined('GROUP_NAME') && strcasecmp(GROUP_NAME,C('DEFAULT_GROUP')))
        $group=  GROUP_NAME;
    if(isset($array['path'])) {
        $action  =  substr($array['path'],1);
        if(!isset($array['host'])) {
            // 没有指定模块名
            $module = MODULE_NAME;
        }else{// 指定模块
            if(strpos($array['host'],'-')) {
                list($group,$module) = explode('-',$array['host']);
            }else{
                $module = $array['host'];
            }
        }
    }else{ // 只指定操作
        $module = MODULE_NAME;
        $action   =  $array['host'];
    }
    if(isset($array['query'])) {
        parse_str($array['query'],$query);
        $params = array_merge($query,$params);
    }

    if(C('URL_DISPATCH_ON') && C('URL_MODEL')>0) {
        $depr = C('URL_PATHINFO_MODEL')==2?C('URL_PATHINFO_DEPR'):'/';
        $str    =   $depr;
        foreach ($params as $var=>$val)
            $str .= $var.$depr.$val.$depr;
        $str = substr($str,0,-1);
        $group   = isset($group)?$group.$depr:'';
        if(!empty($route)) {
            $url    =   str_replace(APP_NAME,$app,__APP__).'/'.$group.$route.$str;
        }else{
            $url    =   str_replace(APP_NAME,$app,__APP__).'/'.$group.$module.$depr.$action.$str;
        }
        if($suffix && C('URL_HTML_SUFFIX'))
            $url .= C('URL_HTML_SUFFIX');
    }else{
        $params =   http_build_query($params);
        if(isset($group)) {
            $url    =   str_replace(APP_NAME,$app,__APP__).'?'.C('VAR_GROUP').'='.$group.'&'.C('VAR_MODULE').'='.$module.'&'.C('VAR_ACTION').'='.$action.'&'.$params;
        }else{
            $url    =   str_replace(APP_NAME,$app,__APP__).'?'.C('VAR_MODULE').'='.$module.'&'.C('VAR_ACTION').'='.$action.'&'.$params;
        }
    }
    if($redirect)
        redirect($url);
    else
        return $url;
}


function get_client_ip(){
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
    $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
    $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
    $ip = getenv("REMOTE_ADDR");
    else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
    $ip = $_SERVER['REMOTE_ADDR'];
    else
    $ip = "unknown";
    return($ip);
}

/**
 +----------------------------------------------------------
 * URL组装 支持不同模式和路由
 +----------------------------------------------------------
 * @param string $action 操作名
 * @param string $module 模块名
 * @param string $app 项目名
 * @param string $route 路由名
 * @param array $params 其它URL参数
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function url($action=ACTION_NAME,$module=MODULE_NAME,$route='',$app=APP_NAME,$params=array()) {
    if(!$app)
        $app = APP_NAME;
    if(C('DISPATCH_ON') && C('URL_MODEL')>0) {
        switch(C('PATH_MODEL')) {
            case 1:// 普通PATHINFO模式
            $str    =   '/';
            foreach ($params as $var=>$val)
            $str .= $var.'/'.$val.'/';
            $str = substr($str,0,-1);
            if(!empty($route)) {
                $url    =   str_replace(APP_NAME,$app,__APP__).'/'.C('VAR_ROUTER').'/'.$route.'/'.$str;
            }else{
                $url    =   str_replace(APP_NAME,$app,__APP__).'/'.C('VAR_MODULE').'/'.$module.'/'.C('VAR_ACTION').'/'.$action.$str;
            }
            break;
            case 2:// 智能PATHINFO模式
            $depr   =   C('PATH_DEPR');
            $str    =   $depr;
            foreach ($params as $var=>$val)
            $str .= $var.$depr.$val.$depr;
            $str = substr($str,0,-1);
            if(!empty($route)) {
                $url    =   str_replace(APP_NAME,$app,__APP__).'/'.$route.$str;
            }else{
                $url    =   str_replace(APP_NAME,$app,__APP__).'/'.$module.$depr.$action.$str;
            }
            break;
        }
        if(C('HTML_URL_SUFFIX')) {
            $url .= C('HTML_URL_SUFFIX');
        }
    }else{
        $params =   http_build_query($params);
        if(!empty($route)) {
            $url    =   str_replace(APP_NAME,$app,__APP__).'?'.C('VAR_ROUTER').'='.$route.'&'.$params;
        }else{
            $url    =   str_replace(APP_NAME,$app,__APP__).'?'.C('VAR_MODULE').'='.$module.'&'.C('VAR_ACTION').'='.$action.'&'.$params;
        }
    }
    return $url;
}

/**
 +----------------------------------------------------------
 * 错误输出
 * 在调试模式下面会输出详细的错误信息
 * 否则就定向到指定的错误页面
 +----------------------------------------------------------
 * @param mixed $error 错误信息 可以是数组或者字符串
 * 数组格式为异常类专用格式 不接受自定义数组格式
 +----------------------------------------------------------
 * @return void
 +----------------------------------------------------------
 */
function halt($error) {
    $e = array();
    if(C('DEBUG_MODE')){
        //调试模式下输出错误信息
        if(!is_array($error)) {
            $trace = debug_backtrace();
            $e['message'] = $error;
            $e['file'] = $trace[0]['file'];
            $e['class'] = $trace[0]['class'];
            $e['function'] = $trace[0]['function'];
            $e['line'] = $trace[0]['line'];
            $traceInfo='';
            $time = date("y-m-d H:i:m");
            foreach($trace as $t)
            {
                $traceInfo .= '['.$time.'] '.$t['file'].' ('.$t['line'].') ';
                $traceInfo .= $t['class'].$t['type'].$t['function'].'(';
                $traceInfo .= implode(', ', $t['args']);
                $traceInfo .=")<br/>";
            }
            $e['trace']  = $traceInfo;
        }else {
            $e = $error;
        }
        if(C('EXCEPTION_TMPL_FILE')) {
            // 定义了异常页面模板
            include C('EXCEPTION_TMPL_FILE');
        }else{
            // 使用默认的异常模板文件
            include IXCORE_PATH.'/Tpl/IXCoreException.tpl.php';
        }
    }
    else
    {
        //否则定向到错误页面
        $error_page =   C('ERROR_PAGE');
        if(!empty($error_page)){
            redirect($error_page);
        }else {
            if(C('SHOW_ERROR_MSG')) {
                $e['message'] =  is_array($error)?$error['message']:$error;
            }else{
                $e['message'] = C('ERROR_MESSAGE');
            }
            if(C('EXCEPTION_TMPL_FILE')) {
                // 定义了异常页面模板
                include C('EXCEPTION_TMPL_FILE');
            }else{
                // 使用默认的异常模板文件
                include IXCORE_PATH.'/Tpl/IXCoreException.tpl.php';
            }
        }
    }
    exit;
}

/**
 +----------------------------------------------------------
 * URL重定向
 +----------------------------------------------------------
 * @static
 * @access public
 +----------------------------------------------------------
 * @param string $url  要定向的URL地址
 * @param integer $time  定向的延迟时间，单位为秒
 * @param string $msg  提示信息
 +----------------------------------------------------------
 */
function redirect($url,$time=0,$msg='')
{
    //多行URL地址支持
    $url = str_replace(array("\n", "\r"), '', $url);
    if(empty($url)) $url = 'http://'.$_SERVER['SERVER_NAME'];
    if(empty($msg)) {
        $msg    =   "系统将在{$time}秒之后自动跳转到{$url}！";
    }
    if (!headers_sent()) {
        // redirect
        header("Content-Type:text/html; charset=".C('OUTPUT_CHARSET'));
        if(0===$time) {
            header("Location: ".$url);
        }else {
            header("refresh:{$time};url={$url}");
            echo($msg);
        }
        exit();
    }else {
        $str    = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if($time!=0) {
            $str   .=   $msg;
        }
        exit($str);
    }
}

/**
 +----------------------------------------------------------
 * 自定义异常处理
 +----------------------------------------------------------
 * @param string $msg 错误信息
 * @param string $type 异常类型 默认为IXCoreException
 * 如果指定的异常类不存在，则直接输出错误信息
 +----------------------------------------------------------
 * @return void
 +----------------------------------------------------------
 */
function throw_exception($msg,$type='IXCoreException',$code=0)
{
    if(isset($_REQUEST[C('VAR_AJAX_SUBMIT')])) {
        header("Content-Type:text/html; charset=utf-8");
        exit($msg);
    }
    if(class_exists($type,false)){
        throw new $type($msg,$code,true);
    }else {
        // 异常类型不存在则输出错误信息字串
        halt($msg);
    }
}

/**
 +----------------------------------------------------------
 *  区间调试开始
 +----------------------------------------------------------
 * @param string $label  标记名称
 +----------------------------------------------------------
 * @return void
 +----------------------------------------------------------
 */
function debug_start($label='')
{
    $GLOBALS[$label]['_beginTime'] = microtime(TRUE);
    if ( MEMORY_LIMIT_ON )  $GLOBALS[$label]['memoryUseStartTime'] = memory_get_usage();
}

/**
 +----------------------------------------------------------
 *  区间调试结束，显示指定标记到当前位置的调试
 +----------------------------------------------------------
 * @param string $label  标记名称
 +----------------------------------------------------------
 * @return void
 +----------------------------------------------------------
 */
function debug_end($label='')
{
    $GLOBALS[$label]['_endTime'] = microtime(TRUE);
    echo '<div style="text-align:center;width:100%">Process '.$label.': Times '.number_format($GLOBALS[$label]['_endTime']-$GLOBALS[$label]['_beginTime'],6).'s ';
    if ( MEMORY_LIMIT_ON )  {
        $GLOBALS[$label]['memoryUseEndTime'] = memory_get_usage();
        echo ' Memories '.number_format(($GLOBALS[$label]['memoryUseEndTime']-$GLOBALS[$label]['memoryUseStartTime'])/1024).' k';
    }
    echo '</div>';
}

/**
 +----------------------------------------------------------
 * 系统调试输出 Log::record 的一个调用方法
 +----------------------------------------------------------
 * @param string $msg 调试信息
 +----------------------------------------------------------
 * @return void
 +----------------------------------------------------------
 */
function system_out($msg)
{
    if(!empty($msg))
    Log::record($msg,WEB_LOG_DEBUG);
}

/**
 +----------------------------------------------------------
 * 变量输出
 +----------------------------------------------------------
 * @param string $var 变量名
 * @param string $label 显示标签
 * @param string $echo 是否显示
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function dump($var, $echo=true,$label=null, $strict=true)
{
    $label = ($label===null) ? '' : rtrim($label) . ' ';
    if(!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = "<pre>".$label.htmlspecialchars($output,ENT_QUOTES,C('OUTPUT_CHARSET'))."</pre>";
        } else {
            $output = $label . " : " . print_r($var, true);
        }
    }else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if(!extension_loaded('xdebug')) {
            $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
            $output = '<pre style="text-align:left;">'
            . $label
            . htmlspecialchars($output, ENT_QUOTES,C('OUTPUT_CHARSET'))
            . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    }else {
        return $output;
    }
}

/**
 +----------------------------------------------------------
 * 取得对象实例 支持调用类的静态方法
 +----------------------------------------------------------
 * @param string $className 对象类名
 * @param string $method 类的静态方法名
 +----------------------------------------------------------
 * @return object
 +----------------------------------------------------------
 */
function get_instance_of($className,$method='',$args=array())
{
    static $_instance = array();
    if(empty($args)) {
        $identify   =   $className.$method;
    }else{
        $identify   =   $className.$method.to_guid_string($args);
    }
    if (!isset($_instance[$identify])) {
        if(class_exists($className)){
            $o = new $className();
            if(method_exists($o,$method)){
                if(!empty($args)) {
                    $_instance[$identify] = call_user_func_array(array(&$o, $method), $args);
                }else {
                    $_instance[$identify] = $o->$method();
                }
            }
            else
            $_instance[$identify] = $o;
        }
        else
        halt(L('_CLASS_NOT_EXIST_'));
    }
    return $_instance[$identify];
}

/**
 +----------------------------------------------------------
 * 系统自动加载IXCore基类库和当前项目的model和Action对象
 * 并且支持配置自动加载路径
 +----------------------------------------------------------
 * @param string $classname 对象类名
 +----------------------------------------------------------
 * @return void
 +----------------------------------------------------------
 */
function __autoload($classname)
{
    // 自动加载当前项目的Actioon类
    if(substr($classname,-6)=="Action"){
        if(!import('@.Action.'.$classname)) {
            // 如果加载失败 尝试加载组件Action类库
            import("@.*.Action.".$classname);
        }
    }else {
        // 根据自动加载路径设置进行尝试搜索
        if(C('AUTO_LOAD_PATH')) {
            $paths  =   explode(',',C('AUTO_LOAD_PATH'));
            foreach ($paths as $path){
                if(import($path.$classname)) {
                    // 如果加载类成功则返回
                    return ;
                }
            }
        }
    }
    return ;
}

/**
 +----------------------------------------------------------
 * 自动转换字符集 支持数组转换
 * 需要 iconv 或者 mb_string 模块支持
 * 如果 输出字符集和模板字符集相同则不进行转换
 +----------------------------------------------------------
 * @param string $fContents 需要转换的字符串
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function auto_charset($fContents,$from='',$to=''){
    if(empty($from)) $from = C('TEMPLATE_CHARSET');
    if(empty($to))  $to =   C('OUTPUT_CHARSET');
    $from   =  strtoupper($from)=='UTF8'? 'utf-8':$from;
    $to       =  strtoupper($to)=='UTF8'? 'utf-8':$to;
    if( strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents)) ){
        //如果编码相同或者非字符串标量则不转换
        return $fContents;
    }
    if(is_string($fContents) ) {
        if(function_exists('mb_convert_encoding')){
            return mb_convert_encoding ($fContents, $to, $from);
        }elseif(function_exists('iconv')){
            return iconv($from,$to,$fContents);
        }else{
            halt(L('_NO_AUTO_CHARSET_'));
            return $fContents;
        }
    }
    elseif(is_array($fContents)){
        foreach ( $fContents as $key => $val ) {
            $_key =  $this -> auto_charset($key,$from,$to);
            $fContents[$_key] = $this -> auto_charset($val,$from,$to);
            if($key != $_key ) {
                unset($fContents[$key]);
            }
        }
        return $fContents;
    }
    elseif(is_object($fContents)) {
        $vars = get_object_vars($fContents);
        foreach($vars as $key=>$val) {
            $fContents->$key = $this -> auto_charset($val,$from,$to);
        }
        return $fContents;
    }
    else{
        //halt('系统不支持对'.gettype($fContents).'类型的编码转换！');
        return $fContents;
    }
}

/**
 +----------------------------------------------------------
 * 反序列化对象时自动回调方法
 +----------------------------------------------------------
 * @param string $classname 对象类名
 +----------------------------------------------------------
 * @return void
 +----------------------------------------------------------
 */
function unserialize_callback($classname)
{
    // 根据自动加载路径设置进行尝试搜索
    if(C('CALLBACK_LOAD_PATH')) {
        $paths  =   explode(',',C('CALLBACK_LOAD_PATH'));
        foreach ($paths as $path){
            if(import($path.$classname)) {
                // 如果加载类成功则返回
                return ;
            }
        }
    }
}

$GLOBALS['include_file'] = 0;
/**
 +----------------------------------------------------------
 * 优化的include_once
 +----------------------------------------------------------
 * @param string $filename 文件名
 +----------------------------------------------------------
 * @return boolen
 +----------------------------------------------------------
 */
function include_cache($filename)
{
    static $_import = array();
    if (!isset($_import[$filename])) {
        if(file_exists($filename)){
            include $filename;
            $GLOBALS['include_file']++;
            $_import[$filename] = true;
        }
        else
        {
            $_import[$filename] = false;
        }
    }
    return $_import[$filename];
}

/**
 +----------------------------------------------------------
 * 优化的require_once
 +----------------------------------------------------------
 * @param string $filename 文件名
 +----------------------------------------------------------
 * @return boolen
 +----------------------------------------------------------
 */
function require_cache($filename)
{
    static $_import = array();
    if (!isset($_import[$filename])) {
        if(file_exists_case($filename)){
            require $filename;
            $GLOBALS['include_file']++;
            $_import[$filename] = true;
        }
        else
        {
            $_import[$filename] = false;
        }
    }
    return $_import[$filename];
}

// 区分大小写的文件存在判断
function file_exists_case($filename) {
    if(file_exists($filename)) {
        if(IS_WIN && C('CHECK_FILE_CASE')) {
            $files =  scandir(dirname($filename));
            if(!in_array(basename($filename),$files)) {
                return false;
            }
        }
        return true;
    }
    return false;
}

/**
 +----------------------------------------------------------
 * 导入所需的类库 支持目录和* 同java的Import
 * 本函数有缓存功能
 +----------------------------------------------------------
 * @param string $class 类库命名空间字符串
 * @param string $baseUrl 起始路径
 * @param string $ext 导入的文件扩展名
 * @param string $subdir 是否导入子目录 默认false
 +----------------------------------------------------------
 * @return boolen
 +----------------------------------------------------------
 */
function import($class,$baseUrl = '',$ext='.class.php',$subdir=false)
{
    //echo('<br>'.$class.$baseUrl);
    static $_file = array();
    static $_class = array();
    $class    =   str_replace(array('.','#'), array('/','.'), $class);
    if(isset($_file[strtolower($class.$baseUrl)]))
    return true;
    else
    $_file[strtolower($class.$baseUrl)] = true;
    //if (preg_match('/[^a-z0-9\-_.*]/i', $class)) throw_exception('Import非法的类名或者目录！');
    if( 0 === strpos($class,'@'))     $class =  str_replace('@',APP_NAME,$class);
    if(empty($baseUrl)) {
        // 默认方式调用应用类库
        $baseUrl   =  dirname(LIB_PATH);
    }else {
        //相对路径调用
        $isPath =  true;
    }
    $class_strut = explode("/",$class);
    if('*' == $class_strut[0] || isset($isPath) ) {
        //多级目录加载支持
        //用于子目录递归调用
    }
    elseif(APP_NAME == $class_strut[0]) {
        //加载当前项目应用类库
        $class =  str_replace(APP_NAME.'/',LIB_DIR.'/',$class);
    }
    elseif(in_array(strtolower($class_strut[0]),array('ixcore','extend','com'))) {
        //加载IXCore基类库或者公共类库
        // ixcore 官方基类库 extend 第三方公共类库 com 企业公共类库
        $baseUrl =  IXCORE_PATH.'/'.LIB_DIR.'/';
    }else {
        // 加载其他项目应用类库
        $class    =   substr_replace($class, '', 0,strlen($class_strut[0])+1);
        $class_strut[0] = $class_strut[0]?$class_strut[0].'/':'';
        $baseUrl =  APP_PATH.'/../'.$class_strut[0].LIB_DIR.'/';
    }
    if(substr($baseUrl, -1) != "/")    $baseUrl .= "/";
    $classfile = $baseUrl . $class . $ext;
    if(false !== strpos($classfile,'*') || false !== strpos($classfile,'?') ) {
        // 导入匹配的文件
        $match  =   glob($classfile);
        if($match) {
            foreach($match as $key=>$val) {
                if(is_dir($val)) {
                    if($subdir) import('*',$val.'/',$ext,$subdir);
                }else{
                    if($ext == '.class.php') {
                        // 冲突检测
                        $class = basename($val,$ext);
                        if(isset($_class[$class])) {
                            throw_exception($class.L('_CLASS_CONFLICT_'));
                        }
                        $_class[$class] = $val;
                    }
                    //导入类库文件
                    $result =   require_cache($val);
                }
            }
            return $result;
        }else{
            return false;
        }
    }else{
        if($ext == '.class.php' && file_exists($classfile)) {
            // 冲突检测
            $class = basename($classfile,$ext);
            if(isset($_class[strtolower($class)])) {
                throw_exception(L('_CLASS_CONFLICT_').':'.$_class[strtolower($class)].' '.$classfile);
            }
            $_class[strtolower($class)] = $classfile;
        }
        //导入目录下的指定类库文件
        return require_cache($classfile);
    }
}

/**
 +----------------------------------------------------------
 * 根据PHP各种类型变量生成唯一标识号
 +----------------------------------------------------------
 * @param mixed $mix 变量
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function to_guid_string($mix)
{
    if(is_object($mix) && function_exists('spl_object_hash')) {
        return spl_object_hash($mix);
    }elseif(is_resource($mix)){
        $mix = get_resource_type($mix).strval($mix);
    }else{
        $mix = serialize($mix);
    }
    return md5($mix);
}

/**
 +----------------------------------------------------------
 * 判断是否为对象实例
 +----------------------------------------------------------
 * @param mixed $object 实例对象
 * @param mixed $className 对象名
 +----------------------------------------------------------
 * @return boolean
 +----------------------------------------------------------
 */
function is_instance_of($object, $className)
{
    if (!is_object($object) && !is_string($object)) {
        return false;
    }
    return $object instanceof $className;
}

/**
 +----------------------------------------------------------
 * 字符串截取，支持中文和其他编码
 +----------------------------------------------------------
 * @static
 * @access public
 +----------------------------------------------------------
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function msubstr($str, $length, $start=0, $suffix=true, $charset="utf-8")
{
    if($suffix)
    $suffixStr = "…";
    else
    $suffixStr = "";

    if(function_exists("mb_substr")) {
        if(mb_strlen(mb_substr($str, $start), $charset) > $length) {
            return mb_substr($str, $start, $length, $charset).$suffixStr;
        }else{
            return mb_substr($str, $start, $length, $charset);
        }
    }elseif(function_exists('iconv_substr')) {
        if(iconv_strlen(iconv_substr($str, $start), $charset) > $length) {
            return iconv_substr($str,$start,$length,$charset).$suffixStr;
        }else{
            return iconv_substr($str,$start,$length,$charset);
        }
    }
    $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("",array_slice($match[0], $start, $length));
    if(count(array_slice($match[0], $start)) > $length) {
        return $slice.$suffixStr;
    }else{
        return $slice;
    }
}

/**
 +---------------------------------------------
 * 对一般字符进行原样输出，用于显示
 +---------------------------------------------
 * @param string $string
 * @param bool $html  html代码输出
 +---------------------------------------------
 * @return string
 +---------------------------------------------
 */
function text($value){
    return stripslashes($value);
}
function html($value){
    return htmlspecialchars(stripslashes($value), ENT_QUOTES);
}
function intro($value, $length=300){
    return msubstr(nl2br(strip_tags(stripslashes($value))), $length);
}
/**
 +---------------------------------------------
 * 创建静态缓存
 +---------------------------------------------
 * @return bool
 +---------------------------------------------
 */
function buildHTML($content, $file, $path='', $ext='.html')
{
    $path = $path?$path:CACHE_PATH;
    $path = IXCORE_PATH.'../'.$path;
    if(!is_dir($path)) mk_dir($path);
    if(false === file_put_contents($path.$file.$ext, $content))
    {
        return true;
    }else{
        return false;
    }
}

/**
 +----------------------------------------------------------
 * addslashes_deep 可用于数组
 +----------------------------------------------------------
 * @param mixed $value 变量
 +----------------------------------------------------------
 * @return mixed
 +----------------------------------------------------------
 */
function addslashes_deep($value) {
    $value = is_array($value) ? array_map('addslashes_deep', $value) : addslashes($value);
    return $value;
}

//常规函数
function D($DBName='', $relation=false, $tablePrefix=false, $tableSuffix=false)
{
    if($relation)
    {
        import('IXCore.Core.Model.RelationModel');
        return new RelationModel($DBName, $tablePrefix, $tableSuffix);
    }else{
        static $_model = array();
        if(isset($_model[$DBName])) {
            return $_model[$DBName];
        }
        if(empty($DBName)) {
            return new Model();
        }else{
            $model = new Model($DBName, $tablePrefix, $tableSuffix);
            $_model[$DBName] =  $model;
            return $model;
        }
    }
}

function A($className,$appName='@')
{
    static $_action = array();
    if(isset($_action[$appName.$className])) {
        return $_action[$appName.$className];
    }
    $OriClassName = $className;
    if(strpos($className,C('COMPONENT_DEPR'))) {
        $array  =   explode(C('COMPONENT_DEPR'),$className);
        $className = array_pop($array);
        $className =  C('CONTR_CLASS_PREFIX').$className.C('CONTR_CLASS_SUFFIX');
        if(C('COMPONENT_TYPE')==1) {
            import($appName.'.'.implode('.',$array).'.Action.'.$className);
        }else{
            import($appName.'.Action.'.implode('.',$array).'.'.$className);
        }
    }else{
        $className =  C('CONTR_CLASS_PREFIX').$className.C('CONTR_CLASS_SUFFIX');
        if(!import($appName.'.Action.'.$className)) {
            // 如果加载失败 尝试加载组件类库
            if(C('COMPONENT_TYPE')==1) {
                import($appName.'.*.Action.'.$className);
            }else{
                import($appName.'.Action.*.'.$className);
            }
        }
    }
    if(class_exists($className)) {
        $action = new $className();
        $_action[$appName.$OriClassName] = $action;
        return $action;
    }else {
        return false;
    }
}

// 获取语言定义
function L($name='',$value=null) {
    static $_lang = array();
    if(!is_null($value)) {
        $_lang[strtolower($name)]   =   $value;
        return;
    }
    if(empty($name)) {
        return $_lang;
    }
    if(is_array($name)) {
        $_lang = array_merge($_lang,array_change_key_case($name));
        return;
    }
    if(isset($_lang[strtolower($name)])) {
        return $_lang[strtolower($name)];
    }else{
        return false;
    }
}

// 获取配置值
function C($name='',$value=null) {
    static $_config = array();
    if(!is_null($value)) {
        if(strpos($name,'.')) {
            $array   =  explode('.',strtolower($name));
            $_config[$array[0]][$array[1]] =   $value;
        }else{
            $_config[strtolower($name)] =   $value;
        }
        return ;
    }
    if(empty($name)) {
        return $_config;
    }
    // 缓存全部配置值
    if(is_array($name)) {
        $_config = array_merge($_config,array_change_key_case($name));
        return $_config;
    }
    if(strpos($name,'.')) {
        $array   =  explode('.',strtolower($name));
        return $_config[$array[0]][$array[1]];
    }elseif(isset($_config[strtolower($name)])) {
        return $_config[strtolower($name)];
    }else{
        return NULL;
    }
}

// 全局缓存设置和读取
function S($name,$value='',$expire='',$type='') {
    static $_cache = array();
    import('IXCore.Util.Cache');
    //取得缓存对象实例
    $cache  = Cache::getInstance($type);
    if('' !== $value) {
        if(is_null($value)) {
            // 删除缓存
            $result =   $cache->rm($name);
            if($result) {
                unset($_cache[$type.'_'.$name]);
            }
            return $result;
        }else{
            // 缓存数据
            $cache->set($name,$value,$expire);
            $_cache[$type.'_'.$name]     =   $value;
        }
        return ;
    }
    if(isset($_cache[$type.'_'.$name])) {
        return $_cache[$type.'_'.$name];
    }
    // 获取缓存数据
    $value      =  $cache->get($name);
    $_cache[$type.'_'.$name]     =   $value;
    return $value;
}

// 快速文件数据读取和保存 针对简单类型数据 字符串、数组
function F($name, $value='', $path=DATA_PATH) {
    static $_cache = array();
    $filename   =   $path.$name.'.php';
    if('' !== $value) {
        if(is_null($value)) {
            // 删除缓存
            if(file_exists($filename)) {
                $result = unlink($filename);
                if($result) {
                    unset($_cache[$name]);
                }
                return $result;
            }
            unset($_cache[$name]);
            return true;
        }else{
            mk_dirs($path);//echo $filename;
            // 缓存数据
            $content = "<?php\nif (!defined('IXCORE_PATH')) exit();\nreturn ".var_export($value,true).";\n?>";
            $result = file_put_contents($filename, $content);
            $_cache[$name]   =   $value;
            return $result;
        }
        return false;
    }
    if(isset($_cache[$name])) {
        return $_cache[$name];
    }
    // 获取缓存数据
    if(is_file($filename))
    $value    = include $filename;
    $_cache[$name]   =   $value;
    return $value;
}

// 远程调用模块的操作方法
function R($module,$action,$app='@') {
    $class = A($module,$app);
    if($class)
    return call_user_func(array(&$class,$action));
    else
    return false;
}

// 快速创建一个对象实例
function I($class,$baseUrl = '',$ext='.class.php') {
    static $_class = array();
    if(isset($_class[$baseUrl.$class])) {
        return $_class[$baseUrl.$class];
    }
    $class_strut = explode(".",$class);
    $className  =   array_pop($class_strut);
    if($className != '*') {
        import($class,$baseUrl,$ext,false);
        if(class_exists($className)) {
            $_class[$baseUrl.$class] = new $className();
            return $_class[$baseUrl.$class];
        }else{
            return false;
        }
    }else {
        return false;
    }
}

// xml编码
function xml_encode($data,$encoding='utf-8',$root="ixcore") {
    $xml = '<?xml version="1.0" encoding="'.$encoding.'"?>';
    $xml.= '<'.$root.'>';
    $xml.= data_to_xml($data);
    $xml.= '</'.$root.'>';
    return $xml;
}

function data_to_xml($data) {
    if(is_object($data)) {
        $data = get_object_vars($data);
    }
    $xml = '';
    foreach($data as $key=>$val) {
        is_numeric($key) && $key="item id=\"$key\"";
        $xml.="<$key>";
        $xml.=(is_array($val)||is_object($val))?data_to_xml($val):$val;
        list($key,)=explode(' ',$key);
        $xml.="</$key>";
    }
    return $xml;
}

function mk_dir($dir, $mode = 0755)
{
    if (is_dir($dir) || @mkdir($dir,$mode)) {
        if(defined("BUILD_DIR_SECURE")) file_put_contents("{$dir}/index.html", ""); //by page7 未测试
        return true;
    }
    if (!mk_dir(dirname($dir),$mode)) return false;
    return @mkdir($dir,$mode);
}

function mk_dirs($dir)
{
    if (is_dir($dir)) return true;
    $dir = explode('/', $dir);
    $temp = '';
    foreach ($dir as $value)
    {
        $temp .= $value.'/';
        if($value=='.' || $value == '..' || !$value) continue;
        $return = mk_dir($temp);
    }
    return $return;
}

// 清除缓存目录
function clearCache($type=0,$path=NULL)
{
    if(is_null($path)) {
        switch($type) {
            case 0:// 模版缓存目录
            $path   =   CACHE_PATH;
            break;
            case 1:// 数据缓存目录
            $path   =   TEMP_PATH;
            break;
            case 2://  日志目录
            $path   =   LOG_PATH;
            break;
            case 3://  数据目录
            $path   =   DATA_PATH;
        }
    }
    import("Extend.Io.Dir");
    Dir::del($path);
}
?>