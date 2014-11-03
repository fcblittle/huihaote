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
 * IXCore Action控制器基类 抽象类
 +------------------------------------------------------------------------------
 * @category   IXCore
 * @package  IXCore
 * @subpackage  Core
 * @author   page7 <zhounan0120@gmail.com>
 * @version  $Id$
 +------------------------------------------------------------------------------
 */
abstract class Action extends Base
{//类定义开始

    // Action控制器名称
    protected $name;

    // 视图实例对象
    protected $view;

    // 上次错误信息
    protected $error;

   /**
     +----------------------------------------------------------
     * 架构函数 取得模板对象实例
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    public function __construct()
    {
        //实例化视图类
        $this->view       = View::getInstance();
        $this->name     =   $this->getActionName();
        //控制器初始化
        $this->_initialize();
    }

    /**
     +----------------------------------------------------------
     * 得到当前的Action对象名称
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    protected function getActionName() {
        if(empty($this->name)) {
            $prefix     =   C('CONTR_CLASS_PREFIX');
            $suffix     =   C('CONTR_CLASS_SUFFIX');
            if($suffix) {
                $this->name =   substr(substr(get_class($this),strlen($prefix)),0,-strlen($suffix));
            }else{
                $this->name =   substr(get_class($this),strlen($prefix));
            }
        }
        return $this->name;
    }

    /**
     +----------------------------------------------------------
     * 得到传递的参数
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param string $type 输入数据类型
     * @param string $name 参数名称
     * @param string $filter 参数过滤方法
     * @param string $default 参数默认值
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    protected function getParam($type,$name='',$filter='',$default='') {
            $Input   = Input::getInstance();
            $value   =  $Input->{$type}($name,$filter,$default);
            return $value;
    }

    /**
     +----------------------------------------------------------
     * 控制器初始化操作
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws IXCoreExecption
     +----------------------------------------------------------
     */
    protected function _initialize()
    {
        return ;
    }


    // 判断是否为AjAX提交
    protected function isAjax() {
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) ) {
            if(strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])=='xmlhttprequest')
                return true;
        }
        if(!empty($_POST[C('VAR_AJAX_SUBMIT')]) || !empty($_GET[C('VAR_AJAX_SUBMIT')])) {
            // 判断Ajax方式提交
            return true;
        }
        return false;
    }

    /**
     +----------------------------------------------------------
     * 是否POST请求
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @return bool
     +----------------------------------------------------------
     */
    protected function isPost()
    {
        return strtolower($_SERVER['REQUEST_METHOD']) == 'post';
    }

    /**
     +----------------------------------------------------------
     * 是否GET请求
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @return bool
     +----------------------------------------------------------
     */
    protected function isGet()
    {
        return strtolower($_SERVER['REQUEST_METHOD']) == 'get';
    }

    /**
     +----------------------------------------------------------
     * 是否Head请求
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @return bool
     +----------------------------------------------------------
     */
    protected function isHead()
    {
        return strtolower($_SERVER['REQUEST_METHOD']) == 'head';
    }

    /**
     +----------------------------------------------------------
     * 是否Put请求
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @return bool
     +----------------------------------------------------------
     */
    protected function isPut()
    {
        return strtolower($_SERVER['REQUEST_METHOD']) == 'put';
    }

    /**
     +----------------------------------------------------------
     * 是否Delete请求
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @return bool
     +----------------------------------------------------------
     */
    protected function isDelete()
    {
        return strtolower($_SERVER['REQUEST_METHOD']) == 'delete';
    }


    /**
     +----------------------------------------------------------
     * 取得操作成功后要返回的URL地址
     * 默认返回当前模块的默认操作
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws IXCoreExecption
     +----------------------------------------------------------
     */
    protected function getReturnUrl()
    {
        return url(C('DEFAULT_ACTION'));
    }

    /**
     +----------------------------------------------------------
     * 模板显示
     * 调用内置的模板引擎显示方法，
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $templateFile 指定要调用的模板文件
     * 默认为空 由系统自动定位模板文件
     * @param string $charset 输出编码
     * @param string $contentType 输出类型
     * @param string $varPrefix 模板变量前缀
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function display($templateFile='',$charset='',$contentType='text/html',$varPrefix='')
    {
        if(C('ACTION_CACHE_ON') && in_array(ACTION_NAME,$this->_cacheAction,true)) {
            // 启用Action缓存
            $content    =   $this->fetch($templateFile,$charset,$contentType,$varPrefix);
            S(md5(__SELF__),$content);
            echo $content;
        }else{
            $this->view->display($templateFile,$charset,$contentType,$varPrefix);
        }
        exit();
    }

    /**
     +----------------------------------------------------------
     *  获取输出页面内容
     * 调用内置的模板引擎fetch方法，
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $templateFile 指定要调用的模板文件
     * 默认为空 由系统自动定位模板文件
     * @param string $charset 输出编码
     * @param string $contentType 输出类型
     * @param string $varPrefix 模板变量前缀
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function fetch($templateFile='',$charset='',$contentType='text/html',$varPrefix='')
    {
        return $this->view->fetch($templateFile,$charset,$contentType,$varPrefix,false);
    }

    /**
     +----------------------------------------------------------
     *  获取输出页面内容，生成静态页面
     *  调用内置的模板引擎fetch方法，
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $path 静态地址路径
     * @param string $file 静态文件名
     * @param string $templateFile 指定要调用的模板文件
     * 默认为空 由系统自动定位模板文件
     * @param string $charset 输出编码
     * @param string $contentType 输出类型
     * @param string $varPrefix 模板变量前缀
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function buildHTML($path, $file, $templateFile='',$charset='',$contentType='text/html',$varPrefix='')
    {
        if(!file_exists($path)) {
            if(!mk_dir($path))
                return false;
        }
        $filename = $path.$file.C('HTML_FILE_SUFFIX');
        $content = $this->view->fetch($templateFile,$charset,$contentType,$varPrefix,false);
        return (false === file_put_contents($filename, $content)) ? false : true;
    }

    /**
     +----------------------------------------------------------
     *  输出布局页面内容
     * 调用内置的模板引擎fetch方法，
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $templateFile 指定要调用的布局模板文件
     * @param string $charset 输出编码
     * @param string $contentType 输出类型
     * @param string $varPrefix 模板变量前缀
     * @param boolean $display 是否输出
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function layout($templateFile,$charset='',$contentType='text/html',$varPrefix='',$display=true)
    {
        return $this->view->layout($templateFile,$charset,$contentType,$varPrefix,$display);
    }

    /**
     +----------------------------------------------------------
     * 模板变量赋值
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $name 要显示的模板变量
     * @param mixed $value 变量的值
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function assign($name,$value='')
    {
        $this->view->assign($name,$value);
    }

    /**
     +----------------------------------------------------------
     * Trace变量赋值
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $name 要显示的模板变量
     * @param mixed $value 变量的值
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function trace($name,$value='')
    {
        $this->view->trace($name,$value);
    }

    /**
     +----------------------------------------------------------
     * 取得模板显示变量的值
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $name 模板显示变量
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function get($name)
    {
        return $this->view->get($name);
    }

    public function __set($name,$value) {
        $this->assign($name,$value);
    }

    public function __get($name) {
        return $this->get($name);
    }

    /**
     +----------------------------------------------------------
     * 魔术方法 有不存在的操作的时候执行
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $name 模板显示变量
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function __call($method,$parms) {
        if(strtolower($method) == strtolower(ACTION_NAME.C('ACTION_SUFFIX'))) {
            // 如果定义了_empty操作 则调用
            if(method_exists($this,'_empty')) {
                $this->_empty($method,$parms);
            }else {
                // 检查是否存在模版 如果有直接输出模版
                if(file_exists_case(C('TMPL_FILE_NAME'))) {
                    $this->display();
                }else{
                    // 调试模式抛出异常
                    throw_exception(L('_ERROR_ACTION_').ACTION_NAME);
                }
            }
        }
    }

    /**
     +----------------------------------------------------------
     * 操作错误跳转的快捷方法
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $errorMsg 错误信息
     * @param string $diy 是否为自定义模式(Public下的error模板)
     * @param Boolean $ajax 是否为Ajax方式
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function error($errorMsg,$ajax=false)
    {
        if($ajax || $this->isAjax()) {
            $this->ajaxReturn('',$errorMsg,0);
        }else {
            $this->assign('error',$errorMsg);
            $this->forward();
        }
    }

    /**
     +----------------------------------------------------------
     * 操作成功跳转的快捷方法
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $message 提示信息
     * @param int $time 提示显示时间
     * @param string $url  跳转方向
     * @param string $diy 是否为自定义模式(Public下的success模板)
     * @param Boolean $ajax 是否为Ajax方式
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function success($message, $time=0, $url='', $ajax=false)
    {

        if($ajax || $this->isAjax()) {
            $this->ajaxReturn('',$message,1);
        }else{

            $this -> assign('jumpUrl', $url);
            $this -> assign('waitSecond', $time);
            $this -> assign('message',$message);

            $this -> forward();
        }
    }

    /**
     +----------------------------------------------------------
     * Ajax方式返回数据到客户端
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $data 要返回的数据
     * @param String $info 提示信息
     * @param String $status 返回状态
     * @param String $status ajax返回类型 JSON XML
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function ajaxReturn($data='',$info='',$status='',$type='')
    {
        // 保证AJAX返回后也能保存日志
        if(C('WEB_LOG_RECORD') || C('SQL_DEBUG_LOG')) Log::save();

        $result  =  array();
        if($status === '') {
            $status  = $this->get('error')?0:1;
        }
        if($info=='') {
            if($this->get('error')) {
                $info =   $this->get('error');
            }elseif($this->get('message')) {
                $info =   $this->get('message');
            }
        }
        $result['status']  =  $status;
        $result['info'] =  $info;
        $result['data'] = $data;
        if(empty($type)) $type  =   C('AJAX_RETURN_TYPE');
        if(strtoupper($type)=='JSON') {
            // 返回JSON数据格式到客户端 包含状态信息
            header("Content-Type:text/html; charset=".C('OUTPUT_CHARSET'));
            exit(json_encode($result));
        }elseif(strtoupper($type)=='XML'){
            // 返回xml格式数据
            header("Content-Type:text/xml; charset=".C('OUTPUT_CHARSET'));
            exit(xml_encode($result));
        }elseif(strtoupper($type)=='EVAL'){
            // 返回可执行的js脚本
            header("Content-Type:text/html; charset=".C('OUTPUT_CHARSET'));
            exit($data);
        }else{
            // TODO 增加其它格式
        }
    }

    /**
     +----------------------------------------------------------
     * 执行某个Action操作（隐含跳转） 支持指定模块和延时执行
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $action 要跳转的Action 默认为_dispatch_jump
     * @param string $module 要跳转的Module 默认为当前模块
     * @param string $app 要跳转的App 默认为当前项目
     * @param boolean $exit  是否继续执行
     * @param integer $delay 延时跳转的时间 单位为秒
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function forward($action='_dispatch_jump',$module='',$app=APP_NAME,$exit=false,$delay=0)
    {
        if(!empty($delay)) {
            //指定延时跳转 单位为秒
            sleep(intval($delay));
        }
        if(is_array($action)) {
            //通过类似 array(&$module,$action)的方式调用
            call_user_func($action);
        }else {
            if(empty($module)) {
                $module = defined('C_MODULE_NAME')?C_MODULE_NAME:MODULE_NAME;
            }
            if( MODULE_NAME!= $module) {
                $class =     A($module,$app);
                call_user_func(array(&$class,$action));
            }else {
                // 执行当前模块操作
                $this->{$action}();
            }
        }
        if($exit) {
            exit();
        }else {
            return ;
        }
    }

    /**
     +----------------------------------------------------------
     * Action跳转(URL重定向） 支持指定模块和延时跳转
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $action 要跳转的Action
     * @param string $module 要跳转的Module 默认为当前模块
     * @param string $app 要跳转的App 默认为当前项目
     * @param string $route 路由名
     * @param array $params 其它URL参数
     * @param integer $delay 延时跳转的时间 单位为秒
     * @param string $msg 跳转提示信息
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function redirect($action,$module='',$route='',$app=APP_NAME,$params=array(),$delay=0,$msg='') {
        if(empty($module)) {
            $module = defined('C_MODULE_NAME')?C_MODULE_NAME:MODULE_NAME;
        }
        $url    =   url($action,$module,$route,$app,$params);
        redirect($url,$delay,$msg);
    }

    /**
     +----------------------------------------------------------
     * 默认跳转操作 支持错误导向和正确跳转
     * 调用模板显示 默认为public目录下面的success页面
     * 提示页面为可配置 支持模板标签
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws IXCoreExecption
     +----------------------------------------------------------
     */
    private function _dispatch_jump()
    {
        if($this->isAjax() ) {
            // 用于Ajax附件上传 显示信息
            if($this->get('_ajax_upload_')) {
                header("Content-Type:text/html; charset=".C('OUTPUT_CHARSET'));
                exit($this->get('_ajax_upload_'));
            }else {
                $this->ajaxReturn();
            }
        }
        if($this->get('error') ) {
            $msgTitle    =   L('_OPERATION_FAIL_');
        }else {
            $msgTitle    =   L('_OPERATION_SUCCESS_');
        }
        //提示标题
        $this->assign('msgTitle',$msgTitle);
        if($this->get('message')) { //发送成功信息
            //成功操作后停留1秒
            if(!$this->get('waitSecond'))
                $this->assign('waitSecond',"1");
            //默认操作成功自动返回操作前页面
            if(!$this->get('jumpUrl'))
                $this->assign("back",$_SERVER["HTTP_REFERER"]);
        }
        if($this->get('error')) { //发送错误信息
            //发生错误时候停留3秒
            if(!$this->get('waitSecond'))
                $this->assign('waitSecond',"3");
            //默认发生错误的话自动返回上页
            if(!$this->get('jumpUrl'))
                $this->assign('back',$_SERVER["HTTP_REFERER"]);
        }
        //如果设置了关闭窗口，则提示完毕后自动关闭窗口
        if($this->get('closeWin')) {
            $this->assign('jumpUrl','javascript:window.close();');
        }

        $this->display(C('ACTION_JUMP_TMPL'));
        // 中止执行  避免出错后继续执行
        exit ;
    }

    // 404 错误定向
    protected function _404($message='',$jumpUrl='',$waitSecond=3) {
        $this->assign('msg',$message);
        if(!empty($jumpUrl)) {
            $this->assign('jumpUrl',$jumpUrl);
            $this->assign('waitSecond',$waitSecond);
        }
        $this->display(C('ACTION_404_TMPL'));
    }

    // 生成令牌
    protected function saveToken(){
        $tokenType = C('TOKEN_TYPE');
        $token = $tokenType(microtime(TRUE));
        Session::set(C('TOKEN_NAME'), $token);
    }

    // 验证令牌
    protected function isValidToken($reset=false){
        $tokenName   =  C('TOKEN_NAME');
        if($_REQUEST[$tokenName]==Session::get($tokenName)){
            $valid=true;
            $this->saveToken();
        }else {
            $valid=false;
            if($reset)    $this->saveToken();
        }
        return $valid;
    }

}//类定义结束
?>