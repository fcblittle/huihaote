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
 * IXCore公共文件
 +------------------------------------------------------------------------------
 */

if(version_compare(PHP_VERSION,'5.0.0','<') ) {
    die('IXCore 1.* require PHP > 5.0 !');
}
// 记录开始运行时间
$GLOBALS['_beginTime'] = microtime(TRUE);

//if (!get_magic_quotes_gpc()) {}
// IXCore系统目录定义
if(!defined('IXCORE_PATH')) define('IXCORE_PATH', dirname(__FILE__));
if(!defined('APP_NAME')) define('APP_NAME', md5(IXCORE_PATH));
if(!defined('APP_PATH')) define('APP_PATH', dirname(IXCORE_PATH).'/'.APP_NAME);
if(!defined('RUNTIME_PATH')) define('RUNTIME_PATH',APP_PATH.'/Temp/');

if(file_exists(RUNTIME_PATH.'~runtime.php')) {
    // 加载框架核心缓存文件
    // 如果有修改核心文件请删除该缓存
    require RUNTIME_PATH.'~runtime.php';
}else{
    // 加载系统定义文件
    require IXCORE_PATH."/Common/defines.php";
    // 系统函数库
    require IXCORE_PATH."/Common/functions.php";
    // 加载编译需要的函数文件
    require IXCORE_PATH."/Common/runtime.php";
    // 第一次运行检查项目目录结构 如果不存在则自动创建
    if(!file_exists(RUNTIME_PATH)) {
        // 创建项目目录结构
        buildAppDir();
    }

    // 加载IXCore基类
    import("IXCore.Core.Base");
    // 加载异常处理类
    import("IXCore.Exception.IXCoreException");
    // 加载日志类
    import("IXCore.Util.Log");
    // 加载IXCore核心类
    import("IXCore.Core.App");
    import("IXCore.Core.Action");
    import("IXCore.Core.Model");
    import("IXCore.Core.View");


    // 是否生成核心缓存
    $cache  =   ( !defined('CACHE_RUNTIME') || CACHE_RUNTIME == true );
    if($cache) {
        if(defined('STRIP_RUNTIME_SPACE') && STRIP_RUNTIME_SPACE == false ) {
            $fun    =   'file_get_contents';
        }else{
            $fun    =   'php_strip_whitespace';
        }
        // 生成核心文件的缓存 去掉文件空白以减少大小
        $content     =   $fun(IXCORE_PATH.'/Common/defines.php');
        $content    .=   $fun(IXCORE_PATH.'/Common/functions.php');
        $content    .=   $fun(IXCORE_PATH.'/Lib/IXCore/Core/Base.class.php');
        $content    .=   $fun(IXCORE_PATH.'/Lib/IXCore/Exception/IXCoreException.class.php');
        $content    .=   $fun(IXCORE_PATH.'/Lib/IXCore/Util/Log.class.php');
        $content    .=   $fun(IXCORE_PATH.'/Lib/IXCore/Core/App.class.php');
        $content    .=   $fun(IXCORE_PATH.'/Lib/IXCore/Core/Action.class.php');
        $content    .=   $fun(IXCORE_PATH.'/Lib/IXCore/Core/Model.class.php');
        $content    .=   $fun(IXCORE_PATH.'/Lib/IXCore/Core/View.class.php');
    }
    if(version_compare(PHP_VERSION,'5.2.0','<') ) {
        // 加载兼容函数
        require IXCORE_PATH.'/Common/compat.php';
        if($cache) {
            $content .=  $fun(IXCORE_PATH.'/Common/compat.php');
        }
    }
    if($cache) {
        file_put_contents(RUNTIME_PATH.'~runtime.php',$content);
        unset($content);
    }
}

// 记录加载文件时间
$GLOBALS['_loadTime'] = microtime(TRUE);
?>