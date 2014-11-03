<?php
//菜单输出 + 当前操作权限判断

    if(Session::get('uid') && Session::get('type')=='neixiao')
    {
        if(MODULE_NAME != 'Index')
        {
            // === 检测用户权限
            $_Node = D('system_node');
            $node = $_Node -> find(array('m'=>MODULE_NAME, 'a'=>ACTION_NAME), 'id');
            if(!$node) $this -> success('操作不存在！', 3, 'Index/index');

            $node = $node['id'];
            $this -> assign('_node', $node);

            if($node)
            {
                //获取角色
                $_Role = D('role_node', true);
                $_Role -> link('', 'BELONG_TO', 'role', 'user_role', 'role', 'role,uid', '`uid`=\''.Session::get('uid').'\' AND ( `time`>\''.NOW.'\' OR `time`=\'0\' )');
                $roles = $_Role -> xfindAll(array('`node` = "'.$node.'"'));
                if(!$roles && !(isset($exception) && in_array(ACTION_NAME, $exception)))
                {
                    $this -> success('您无权执行该操作！', 3, 'Index/index');
                }
            }else{
                $this -> success('您无权执行该操作！', 3, 'Index/index');
            }

        }else{
            $_Node = D('system_node');
            $_Role = D('role_node', true);
            $_Role -> link('', 'BELONG_TO', 'role', 'user_role', 'role', 'role,uid', '`uid`=\''.Session::get('uid').'\' AND ( `time`>\''.NOW.'\' OR `time`=\'0\' )');
        }

        // === 刷新菜单
        $node_all = $_Role -> xfindAll();
        $menu = array();
        if($node_all)
            foreach($node_all as $value)
                $menu[] = $value['node'];

        if($menu)
        {
            $menu = $_Node -> order('sort asc, id asc') -> findAll('`id` IN ('.implode(',', $menu).')');
            //菜单列表
            $menu_tree = array();
            //可执行操作列表,简单判断
            $can_do = array();
            foreach($menu as $value)
            {
                if(!isset($can_do[$value['m']])) $can_do[$value['m']] = array();
                if($value['a']) $can_do[$value['m']][$value['a']] = $value['name'];

                if($value['hide'] == 1) continue;
                if(!isset($menu_tree[$value['m']])) $menu_tree[$value['m']] = array('name'=>'', 'child'=>array());
                if($value['a'])
                {
                    $menu_tree[$value['m']]['child'][] = $value;
                }else{
                    $menu_tree[$value['m']]['name'] = $value['name'];
                }
            }

            foreach($menu_tree as $key => $value)
            {
                if(!$menu_tree[$key]['child'])
                    unset($menu_tree[$key]);
            }
        }else{
            $can_do = array();
            $menu_tree = array();
        }
        $this -> assign('can_do', $can_do);
        $this -> assign('menu', $menu_tree);

        //提取公告
        $Inform = D('user_inform', true);
        $Inform -> link('', 'BELONG_TO', 'inform', 'inform', 'id', 'id,title,hide', '`hide` = "0"');
        $informs = $Inform -> xfindAll('`uid`="'.Session::get('uid').'"', 'uid,inform', 'inform desc');
        $this -> assign('inform', $informs);
        if(MODULE_NAME != 'Index') $this -> assign('title', $menu_tree[MODULE_NAME]['name']);
    }else{

        //访问无记录
        include_once(LIB_PATH.'_allow.php');
        $this -> success('您尚未登录或系统超时，请重新登录。', 3, 'Public/login');
    }





?>