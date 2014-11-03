<?php
define('IXCORE_PATH', './IxCore');
//定义项目名称和路径 默认放在目录下
define('APP_NAME', '.');
define('APP_PATH', dirname(__FILE__));
define('CACHE_RUNTIME', false); //关闭缓存
// define('APP_DEBUG',TRUE);

// 加载框架入口文件
require(IXCORE_PATH."/IxCore.php");
//实例化一个网站应用实例
$App = new App();
//应用程序初始化
$App->run();
?>