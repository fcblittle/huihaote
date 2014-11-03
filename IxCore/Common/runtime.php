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

function mkdirs($dirs,$mode=0777) {
    if(is_string($dirs)) {
        $dirs  = explode(',',$dirs);
    }
    foreach ($dirs as $dir){
        if(!is_dir($dir))  mkdir($dir,$mode);
    }
}

// 创建项目目录结构
function buildAppDir() {
    // 没有创建项目目录的话自动创建
    if(!is_dir(APP_PATH)){
        mk_dir(APP_PATH,0777);
    }
    if(is_writeable(APP_PATH)) {
        mkdirs(array(
            LIB_PATH,
            CONFIG_PATH,
            COMMON_PATH,
            LANG_PATH,
            CACHE_PATH,
            TMPL_PATH,
            TMPL_PATH.'default/',
            LOG_PATH,
            TEMP_PATH,
            DATA_PATH,
            LIB_PATH.'Action/',
            ));
        // 目录安全写入
        if(!defined('BUILD_DIR_SECURE')) define('BUILD_DIR_SECURE',false);
        if(BUILD_DIR_SECURE) {
            if(!defined('DIR_SECURE_FILENAME')) define('DIR_SECURE_FILENAME','index.html');
            if(!defined('DIR_SECURE_CONTENT')) define('DIR_SECURE_CONTENT',' ');
            // 自动写入目录安全文件
            $content        =   DIR_SECURE_CONTENT;
            $a = explode(',', DIR_SECURE_FILENAME);
            foreach ($a as $filename){
                file_put_contents(LIB_PATH.$filename,$content);
                file_put_contents(LIB_PATH.'Action/'.$filename,$content);
                file_put_contents(CACHE_PATH.$filename,$content);
                file_put_contents(LANG_PATH.$filename,$content);
                file_put_contents(TEMP_PATH.$filename,$content);
                file_put_contents(TMPL_PATH.$filename,$content);
                file_put_contents(TMPL_PATH.'default/'.$filename,$content);
                file_put_contents(DATA_PATH.$filename,$content);
                file_put_contents(COMMON_PATH.$filename,$content);
                file_put_contents(CONFIG_PATH.$filename,$content);
                file_put_contents(LOG_PATH.$filename,$content);

            }
        }
    }else{
        header("Content-Type:text/html; charset=utf-8");
        exit('<div style=\'font-weight:bold;float:left;width:345px;text-align:center;border:1px solid silver;background:#EFEFEF;padding:8px;color:red;font-size:14px;font-family:Tahoma\'>项目目录不可写，目录无法自动生成！<BR>请使用项目生成器或者手动生成项目目录~</div>');
    }
}
?>