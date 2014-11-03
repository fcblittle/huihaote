<?php
/**
 * 角色权限管理Action
 *
 * @author Page7
 * @category Police OA 2012 pack1
 * @copyright Copyright(c) 2011 Interidea.org
 * @version $Id$
 */

class RoleAction extends Action
{
    public function _initialize()
    {
        include_once(LIB_PATH.'_initialize.php');
    }

    public function index()
    {
        $Role = D('Role');
        $list = $Role -> field('id,name,parentid,type,lock') -> order("id desc")  -> findAll();
        //提取0级权限用户
        $level0 = array();
        $other = array();
        foreach ($list as $key=>$value){
        	if($value['parentid']==0){
        		$value['level'] = 0;
                $level0[$value['id']] = $value;
        	}else{
        		$other[$value['id']] = $value;
        	}
        }
        ksort($level0);
        $data = dataTree($level0, $other);
        $this -> assign('list', $data);
        $this -> display();
    }

    public function add()
    {
        if($this -> isAjax())
        {
            if(!$_POST['val']) $this -> error('名称不能为空！', true);

            $Role = D('Role');
            $result = $Role -> add(array('name'=>$_POST['val'], 'parentid'=>$_POST['parent']));
            if($result)
                $this -> ajaxReturn($result, '', 1);
            else
                $this -> error('添加失败，请重试', true);
        }else{
            $this -> redirect('index');
        }
    }

    public function edit()
    {
        if($this -> isAjax())
        {
            if(!$_POST['val'] || !$_POST['id']) $this -> error('名称不能为空！', true);

            $Role = D('Role');
            $result = $Role -> save(array('name'=>$_POST['val'], 'id'=>$_POST['id']));
            if($result)
                $this -> ajaxReturn($result, '', 1);
            else
                $this -> error('保存失败，请重试', true);
        }else{
            $this -> redirect('index');
        }
    }

    public function delete()
    {
        if($this -> isAjax())
        {
            if(!$_POST['id']) $this -> error('数据错误！', true);

            $Role = D('Role');
            $result = $Role -> delete(array('id'=>$_POST['id'],''));
            if($result)
                $this -> ajaxReturn($result?"true":"false", '', 1);
            else
                $this -> error('删除失败，请重试', true);
        }else{
            $this -> redirect('index');
        }
    }

    //角色赋权
    public function node()
    {
        $Role = D('Role');
        $list = $Role -> field('id,name,parentid,type') -> order("id desc")  -> findAll();
        //提取0级权限用户
        $level0 = array();
        $other = array();
        foreach ($list as $key=>$value){
        	if($value['parentid']==0){
        		$value['level'] = 0;
                $level0[$value['id']] = $value;
        	}else{
        		$other[$value['id']] = $value;
        	}
        }
        ksort($level0);
        $role = dataTree($level0, $other, 'parentid', true);
        $this -> assign('role', $role);

        if($_POST)
        {
            //判断当前用户权限能否赋值
            $User_role = D('user_role');
            $_roles = $User_role -> findAll('`uid`=\''.Session::get('uid').'\' AND ( `time`>\''.NOW.'\' OR `time`=\'0\' )');
            foreach($_roles as $key => $value)
                $_role[] = $value['role'];

            if(!in_array('1', $_role))
            {
                $_can = array(); //可操作的角色
                $check = false;
                foreach($role as $value)
                {
                    if(in_array($value['id'], $_role))
                    {
                        $check = $value;
                    }else{
                        if($check && $value['level'] > $check['level'])
                        {
                            $_can[] = $value['id'];
                        }else{
                            $check = false;
                        }
                    }
                }
            }else{
                $_can = array();
                foreach($role as $value)
                {
                    $_can[] = $value['id'];
                }
            }


            if(isset($_POST['id']))
            {
                if(!$id = (int)$_POST['id']) $this -> error('参数错误，请重试~', true);
                if(!in_array($id, $_can)) $this -> error('您无权查看该层级角色！', true);

                $Role_Node = D('role_node');
                if($result = $Role_Node -> where("`role`='{$id}'") -> findAll())
                {
                    $this -> ajaxReturn($result, '', 1);
                }else{
                    $this -> ajaxReturn(array(), '', 1);
                }
            }else{
                if(!isset($_POST['node']) || !isset($_POST['role'])) $this -> success('数据错误，请重试~', true);
                $role = (int)$_POST['role'];
                if(!in_array($role, $_can)) $this -> success('您无权修改该层级角色！', true);

                $condition = "`role`={$role}";
                $Role_Node = D('role_node');
                $Role_Node -> delete($condition);
                if(!$result = $Role_Node -> find($condition))
                {
                    $data = array();
                    foreach($_POST['node'] as $key => $value)
                    {
                        $data[] = array('role'=>$role, 'node'=>$key);
                    }
                    if($data)
                    {
                        if($Role_Node -> addAll($data))
                        {
                            $this -> success('保存成功', 3, 'Role/node');
                        }else{
                            $this -> success('保存失败，请重试', 3);
                        }
                    }else{
                        $this -> success('保存成功', 3, 'Role/node');
                    }
                }else{
                    $this -> success('删除旧节点失败，请重试！', 3);
                }
            }
        }

        //查询系统操作
        $DB = D('system_node');
        $list = $DB -> order("sort asc,id asc") -> findAll();
        $data = array();
        foreach($list as $value)
        {
            if(!isset($data[$value['m']])) $data[$value['m']] = array('id'=>'', 'name'=>'', 'child'=>array());
            if($value['a'])
            {
                $data[$value['m']]['child'][] = $value;
            }else{
                $data[$value['m']]['id'] = $value['id'];
                $data[$value['m']]['name'] = $value['name'];
            }
        }
        $this -> assign('node', $data);

        $this -> display();
    }

}
?>