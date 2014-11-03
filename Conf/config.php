<?php
if (!defined('IXCORE_PATH')) exit();
return array(
'DB_TYPE'       		=> 'mysql',
'DB_HOST'       		=> 'localhost',
'DB_NAME'       		=> 'huihaote_net',
'DB_USER'       		=> 'root',
'DB_PWD'        		=> 'root',
'DB_PORT'       		=> '3306',
'DB_PREFIX'    			=> 'ix_',
'DB_CHARSET'			=> 'utf8',
'URL_MODEL'             => 0,
'HTML_URL_SUFFIX'		=>	'.html',
'DEBUG_MODE'			=> false,
'APP_DOMAIN_DEPLOY'     => false,
'SHOW_PAGE_TRACE'       => false,
//缓存关闭
'TMPL_CACHE_ON'         => false,
'ACTION_CACHE_ON'       => false,
'DATA_CACHE_TIME'       => -1,
'TMPL_L_DELIM'			=>	'<{',
'TMPL_R_DELIM'			=>	'}>',
//'LANG_SWITCH_ON'			=>	  true,
'ERROR_PAGE'            => './index.php',	// 错误定向页面
'SESSION_NAME'          => 'now',
);
?>
