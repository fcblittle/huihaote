<?php
 function creatOrderNum($table, $prefix='', $date='Ymd') { if(!$table) return false; $site = $date == 'Ymd' ? 8 : 6; $DB = D(strtolower($table)); $num_prefix = $prefix .'-'. date($date); $expenses =$DB -> order("`num` desc") -> find("`num` LIKE '$num_prefix%'"); $ids = explode('-', $expenses['num']); $seq = substr($ids[1], $site); $seq = $seq ? str_pad((int)$seq+1, 4, '0', STR_PAD_LEFT) : '0001'; $num = $num_prefix.$seq; return $num; } function timestr($time, $mode=0, $sub='前') { if(!$time) return '无记录'; switch($mode){ case 1: $now = NOW; if($now - $time > 259200){ $date = date('n月j日', $time); }elseif($now - $time > 86400){ $date = floor(($now - $time)/86400).'天'.$sub; }elseif ($now - $time > 3600){ $date = floor(($now - $time)/3600).'小时'.$sub; }elseif ($now - $time > 60){ $date = floor(($now - $time)/60).'分钟'.$sub; }else{ $date = floor($now - $time).'秒'.$sub; } break; case 2: $date = date('Y-n-j H:i', $time);break; default: $date = date('Y年n月j日 H:i', $time); break; } return $date; } function showPrice($price, $prefix='') { $price = abs($price); return $prefix . strval(number_format(round($price/100,2),2,'.','')); } function inputPrice($price) { if(stripos($price, '￥') !== false) $price = str_replace('￥', '', $price); $price = $price * 100; return $price; } function addSuffix($file, $suffix='_s') { return substr_replace($file, $suffix, -4, 0); } function getDir($id) { $uid = abs(intval($id)); $uid = sprintf("%09d", $id); $dir1 = substr($id, 0, 3); $dir2 = substr($id, 3, 2); $dir3 = substr($id, 5, 2); $id = substr($id, -2); return "{$dir1}/{$dir2}/{$dir3}/{$id}/"; } function dataTree($result, $data, $keyname='parentid', $strmode=false) { foreach ($data as $key => $value) { if(!$parent_key = $value[$keyname]) continue; if(isset($result[$parent_key])) { unset($data[$key]); $value['level'] = $result[$parent_key]['level'] + 1; $offset = array_search($value[$keyname], array_keys($result))+1; $front = array_slice($result, 0, $offset, true); $back = array_slice($result, $offset, count($result), true); $result = $front + array($key => $value) + $back; $check = true; } } if(!isset($check)) { if($strmode) { $result = array_values($result); $result[0]['levelstr'] = ''; foreach($result as $key => $value) { if($key == 0) continue; $check = true; for($i=$key+1; $i<=count($result)-1; $i++) { if($result[$i]['level'] < $value['level']) { $check = true; break; }else if($result[$i]['level'] == $value['level']){ $check = false; break; } } if($value['level'] > $result[$key-1]['level']) { $result[$key]['levelstr'] = str_replace(array('├', '└'), array('│', '　'), $result[$key-1]['levelstr']).($check?'└':'├'); }else if($value['level'] <= $result[$key-1]['level'] && $value['level']){ $result[$key]['levelstr'] = msubstr($result[$key-1]['levelstr'], $value['level']-1, 0, false).($check?'└':'├'); } $prevData[$value['level']] = $key; } } return $result; }else{ return dataTree($result, $data, $keyname, $strmode); } } function test($data){ var_dump($data); exit(); } function getRMB($price) { $arr1 = array('零','壹','贰','叁','肆','伍','陆','柒','捌','玖'); $arr2 = array('拾','佰','仟'); $arr = explode(".", $price); $rmb_len=strlen($arr[0]); $j=0; for ($i=0; $i<$rmb_len; $i++) { $bit = $arr[0][$rmb_len-$i-1]; $cn = $arr1[$bit]; $unit = $arr2[$j]; if ($i==0) { $re=$cn; } elseif ($i==4){ $re=$cn."万".$re; $j=0; } elseif ($i==8) { $re=$cn."亿".$re; $j=0; }else{ $j++; $re = $bit==0 ? "零".$re : $cn.$unit.$re; } } if ($arr[1]) { $arr[1][0]==0?$re=$re."元零":$re=$re."元".$arr1[$arr[1][0]]."角"; $arr[1][1]==0?$re=$re."零分":$re=$re.$arr1[$arr[1][1]]."分"; } $re=preg_replace(array("/(零)+$/","/(零)+/","/零万/","/零亿/"),array("","零","万","亿"),$re); $arr[1] ? $re : $re .= "元整"; return $re; } ?><?php
return array (
  'dispatch_on' => true,
  'dispatch_name' => 'IXCore',
  'url_model' => 0,
  'path_model' => 2,
  'path_depr' => '/',
  'router_on' => true,
  'sub_path' => false,
  'component_depr' => '@',
  'component_type' => 1,
  'url_case_insensitive' => true,
  'check_file_case' => false,
  'web_log_record' => false,
  'log_file_size' => 2097152,
  'ixcore_plugin_on' => false,
  'app_auto_setup' => false,
  'limit_resflesh_on' => false,
  'limit_reflesh_times' => 3,
  'debug_mode' => false,
  'error_message' => '您浏览的页面暂时发生了错误！请稍后再试～',
  'error_page' => './index.php',
  'show_error_msg' => true,
  'var_pathinfo' => 'i',
  'var_module' => 'm',
  'var_action' => 'a',
  'var_router' => 'r',
  'var_file' => 'f',
  'var_page' => 'p',
  'var_language' => 'lang',
  'var_template' => 't',
  'var_ajax_submit' => 'ajax',
  'var_resflesh' => 'h',
  'default_module' => 'Index',
  'default_action' => 'index',
  'module_redirect' => '',
  'action_redirect' => '',
  'tmpl_cache_on' => false,
  'tmpl_cache_time' => -1,
  'tmpl_switch_on' => true,
  'default_template' => 'default',
  'template_suffix' => '.html',
  'cachfile_suffix' => '.php',
  'template_charset' => 'utf-8',
  'output_charset' => 'utf-8',
  'default_layout' => 'Layout:index',
  'contr_class_prefix' => '',
  'contr_class_suffix' => 'Action',
  'action_prefix' => '',
  'action_suffix' => '',
  'table_name_identify' => true,
  'html_file_suffix' => '.html',
  'html_cache_on' => false,
  'html_cache_time' => 60,
  'html_read_type' => 1,
  'html_url_suffix' => '.html',
  'lang_switch_on' => false,
  'lang_cache_on' => false,
  'default_language' => 'zh-cn',
  'time_zone' => 'PRC',
  'session_name' => 'now',
  'session_path' => '',
  'session_type' => 'File',
  'session_expire' => 600,
  'session_table' => 'ixcore_session',
  'session_callback' => '',
  'db_charset' => 'utf8',
  'db_deploy_type' => 0,
  'sql_debug_log' => false,
  'db_fields_cache' => true,
  'db_fieldtype_check' => true,
  'sql_mode' => '',
  'fields_depr' => ',',
  'table_describe_sql' => '',
  'db_trigger_prefix' => 'tr_',
  'db_sequence_prefix' => 'seq_',
  'db_case_lower' => true,
  'data_cache_time' => -1,
  'data_cache_compress' => false,
  'data_cache_check' => false,
  'data_cache_type' => 'File',
  'data_cache_subdir' => false,
  'data_cache_table' => 'ixcore_cache',
  'cache_serial_header' => '<?php
//',
  'cache_serial_footer' => '
?>',
  'share_mem_size' => 1048576,
  'show_run_time' => false,
  'show_adv_time' => false,
  'show_db_times' => false,
  'show_cache_times' => false,
  'show_use_mem' => false,
  'show_page_trace' => false,
  'tmpl_engine_type' => 'IXCore',
  'tmpl_deny_func_list' => 'echo,exit',
  'tmpl_l_delim' => '<{',
  'tmpl_r_delim' => '}>',
  'taglib_begin' => '<',
  'taglib_end' => '>',
  'tag_nested_level' => 3,
  'cookie_expire' => 3000000,
  'cookie_domain' => '',
  'cookie_path' => '/',
  'cookie_prefix' => 'IxC_',
  'cookie_secret_key' => '',
  'page_numbers' => 9,
  'list_numbers' => 20,
  'ajax_return_type' => 'JSON',
  'data_result_type' => 0,
  'auto_load_path' => 'IXCore.Util.',
  'auto_load_class' => '',
  'callback_load_path' => '',
  'upload_file_rule' => 'uniqid',
  'like_match_fields' => '',
  'action_jump_tmpl' => 'Public:success',
  'action_404_tmpl' => 'Public:404',
  'token_on' => true,
  'token_name' => 'ixc_token',
  'token_type' => 'md5',
  'app_domain_deploy' => false,
  'db_type' => 'mysql',
  'db_host' => 'localhost',
  'db_name' => 'huihaote_net',
  'db_user' => 'root',
  'db_pwd' => 'root',
  'db_port' => '3306',
  'db_prefix' => 'ix_',
  'action_cache_on' => false,
);
?>