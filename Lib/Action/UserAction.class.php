<?php
/**
 * 用户管理Action
 *
 * @author Page7
 * @category Police OA 2012 pack1
 * @copyright Copyright(c) 2011 Interidea.org
 * @version $Id$
 */

class UserAction extends Action
{
    public function _initialize()
    {
        $exception = array('index');
        include_once(LIB_PATH.'_initialize.php');
    }

    /**
     + -----------------------------------------------------------------------
     *  用户列表
     + -----------------------------------------------------------------------
     */
    public function index()
    {
        if(isset($_GET['search']))
        {
            $condition = array('`del` = "0"');
            if(isset($_GET['username']) && $_GET['username'])
                $condition[] = "`username` LIKE '%{$_GET['username']}%'";
            if(isset($_GET['name']) && $_GET['name'])
                if($this -> isAjax())
                    $condition[] = "`name` LIKE '%".urldecode($_GET['name'])."%'";
                else
                    $condition[] = "`name` LIKE '%".$_GET['name']."%'";
            if(isset($_GET['num']) && $_GET['num'])
                $condition[] = "`num` = '{$_GET['num']}'";
            if($condition) $condition = implode(' AND ', $condition);
            if(isset($_GET['all'])) $condition = '`uid`!='.Session::get('uid');

            $this -> assign('search', 1);
        }else{
            $condition = '`del` = "0"';
        }

        $User = D('user');
        if($this -> isAjax())
        {
            $list = $User -> where($condition) -> field('uid,username,name,num,lock') -> order("`uid` asc") -> findAll();
            $this -> ajaxReturn($list, '', 1);
        }else{
            //权限获取
            $can_do = $this -> get('can_do');
            if(!isset($can_do['User']['index'])) $this -> success('您无权执行该操作！', 3, 'Index/index');

            import('Extend.Util.Page');
            $Page = new Page($User->count($condition), 15);
            $user = $User -> where($condition) -> field('uid,username,name,num,lasttime,lock')  -> order("`uid` asc") -> limit($Page->firstRow.','.$Page->listRows) -> findAll();
            if($user)
            {
                //用户角色
                $_user = array();
                foreach($user as $key => $value)
                {
                    $_user[$value['uid']] = $value;
                    $_user[$value['uid']]['role'] = array();
                }

                $User_role = D('user_role', true);
                $User_role -> link('', 'BELONG_TO', 'role', 'role', 'id');
                $role = $User_role -> xfindAll("`uid` IN (".implode(',', array_keys($_user)).") AND `time`='0'", 'uid,role', 'role asc');
                foreach($role as $value)
                {
                    $_user[$value['uid']]['role'][] = $value['name'];
                }
            }
            $page = $Page -> show();
            $this -> assign('page', $page);
            $this -> assign('list', $_user);
            $this -> display();
        }
    }

    /**
     + -----------------------------------------------------------------------
     *  用户添加
     + -----------------------------------------------------------------------
     */
    public function add()
    {
        if($_POST)
        {
            if(!$_POST['username'] || !$_POST['password'])
                $this -> success("用户名与密码不能为空！", 2);
            if($_POST['password'] != $_POST['passwordcheck'])
                $this -> success("两次密码输入不一致！", 2);
            $md = strlen($_POST['username'])>3 ? substr($_POST['username'],0,4) : $_POST['username'];
            $data = array(
                'username' => $_POST['username'],
                'password' => md5($_POST['password'].$md),
                'name'     => $_POST['name'],
                'num'      => $_POST['num'],
                'birthday' => $_POST['birthday'],
                'lasttime' => NOW,
                'lock'     => $_POST['lock'],
                'btype'    => $_POST['btype'],
            );
            $DB_User = D('user');
            $uid = $DB_User -> add($data);
            if($uid)
            {
                $info = array('uid'=>$uid, 'value'=>serialize($_POST['info']));
                $DB_Info = D('user_info');
                if($DB_Info -> add($info))
                {
                    $post = array('id'=>$uid, 'role'=>$_POST['role']);
                    $this -> role(false, $post);
                    $page = ceil($DB_User -> count("`uid` <= {$uid}") / 15);
                    $this -> redirect('index', 'User', '', APP_NAME, array(C('VAR_PAGE')=>$page));
                }else{
                    $this -> success('扩展资料错误，请修改', 3, 'User/edit/id/'.$uid);
                }
            }else{
                $this -> success('添加失败，请重试', 3);
            }

        }
        //扩展数据
        $DB_Config = D('system_config');
        $data = $DB_Config -> find("`id`=1");
        if($data){
            $data = unserialize($data['value']);
            $this -> assign('extend', $data);
        }

        $this -> assign('role', $this -> role(true));

        $this -> display();
    }



    /**
     + -----------------------------------------------------------------------
     *  用户修改
     + -----------------------------------------------------------------------
     */
    public function edit()
    {
        if($_POST)
        {
            $uid = (int)$_POST['id'];
            if(!$uid) $this -> success("数据访问错误，请重试。", 3);
            if($_POST['password'] != $_POST['passwordcheck'])
                $this -> success("两次密码输入不一致！", 2);
            $data = array(
                'uid'      => $uid,
                'username' => $_POST['username'],
                'name'     => $_POST['name'],
                'num'      => $_POST['num'],
                'birthday' => $_POST['birthday'],
                'lock'     => $_POST['lock'],
                'nowtime'  => NOW,
                'btype'    => $_POST['btype'],
            );
            if($_POST['password'])
            {
                $md = strlen($_POST['username'])>3 ? substr($_POST['username'],0,4) : $_POST['username'];
                $data['password'] = md5($_POST['password'].$md);
            }
            $DB_User = D('user');
            if($DB_User -> save($data))
            {
                $info = array('uid'=>$uid, 'value'=>serialize($_POST['info']));
                $DB_Info = D('user_info');
               if(!$DB_Info -> find("`uid`={$uid}"))
                {
                    $info['uid'] = $uid;
                    $result = $DB_Info -> add($info);
                }else{
                    $result = $DB_Info -> save($info, "`uid`={$uid}");
                }
                if($result)
                {
                    $this -> role(false, array('id'=>$uid, 'role'=>$_POST['role']));
                    $page = ceil($DB_User -> count("`uid` <= {$uid}") / 15);
                    $this -> success('保存成功！', 2, 'User/index/'.C('VAR_PAGE').'/'.$page);
                }else{
                    $this -> success('扩展资料保存失败，请重试', 3);
                }
            }else{
                $this -> success('保存失败，请重试', 3);
            }

        }
        //获取数据
        $id = (int)$_GET['id'];
        $DB_User = D('user', true);
        $DB_User -> link('info', 'HAS_ONE', 'uid', 'user_info', 'uid');
        $user = $DB_User -> xfind("`uid`='{$id}'");
        $user['info'] = unserialize($user['info']['value']);
        $this -> assign($user);

        //扩展数据
        $DB_Config = D('system_config');
        $data = $DB_Config -> find("`id`=1");
        if($data){
            $data = unserialize($data['value']);
            $this -> assign('extend', $data);
        }

        //角色表
        $this -> assign('role', $this -> role($id));

        $this -> display();
    }



    /**
     + -----------------------------------------------------------------------
     *  用户查看
     + -----------------------------------------------------------------------
     */
    public function view()
    {
        //获取数据
        $id = (int)$_GET['id'];
        $DB_User = D('user', true);
        $DB_User -> link('info', 'HAS_ONE', 'uid', 'user_info', 'uid');
        $user = $DB_User -> xfind("`uid`='{$id}'");
        $user['info'] = unserialize($user['info']['value']);
        $this -> assign($user);

        //扩展数据
        $DB_Config = D('system_config');
        $data = $DB_Config -> find("`id`=1");
        if($data){
            $data = unserialize($data['value']);
            $this -> assign('extend', $data);
        }

        //角色表
        $this -> assign('role', $this -> role($id));

        $this -> display();
    }


    /**
     + -----------------------------------------------------------------------
     *  用户删除
     + -----------------------------------------------------------------------
     */
    public function delete()
    {
        $uid = (int)$_GET['id'];
        if(!$uid) $this -> success("数据访问错误，请重试。", 3);
        $DB_User = D('user');
        $user = $DB_User -> find("uid='{$uid}'");
        $data = array(
            'del'      => 1,
            'username' => 'dl_' . $user['username'],
        );

        if($DB_User -> where("`uid` = '{$uid}'") -> save($data))
        {
            $this -> success('删除成功！', 2, 'User/index');
        }else{
            $this -> success('操作失败，请重试！',3);
        }
    }




    /**
     + -----------------------------------------------------------------------
     *  用户角色修改
     + -----------------------------------------------------------------------
     */
    public function role($get=false, $post=array())
    {
        //获取所有角色
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

        //判断当前用户权限能否赋值
        $User_role = D('user_role');
        $_roles = $User_role -> find('`uid`=\''.Session::get('uid').'\' AND `time`=\'0\'');

        if($_roles['role'] != 1)
        {
            $check = false;
            foreach($role as $key => $value)
            {
                if(in_array($value['id'], $_role))
                {
                    $check = $value;
                }else{
                    if($check && $value['level'] > $check['level'])
                        $role[$key]['can'] = 1;
                    else
                        $check = false;
                }
            }
        }else{
            foreach($role as $key => $value)
                $role[$key]['can'] = 1;
        }

        if($_POST || $post)
        {
            if($post) $_POST = $post;
            if(!$id = (int)$_POST['id']) $this -> error('参数错误！', true);
            $canDelete = array();
            $canAdd = array();
            foreach($role as $value)
            {
                if($value['can'])
                    $canDelete[] = $value['id'];
                if($_POST['role'])
                {
                    if(in_array($value['id'], $_POST['role']))
                        $canAdd[] = array('uid'=>$id, 'role'=>$value['id']);
                }
            }
            $result = true;
            if($canDelete)
                $User_role -> where("`uid`='{$id}' AND `role` IN (".implode(',', $canDelete).") AND `time`='0'") -> delete();
            if($canAdd)
                $result = $User_role -> addAll($canAdd);
            if($post)
            {
                return $result;
            }else{
                $this -> ajaxReturn($result ? 'true' : 'false', '', 1);
            }
        }

        //查找被查看用户固有角色
        if($get && is_numeric($get))
        {
            $_GET['id'] = $get;
            if(!$id = (int)$_GET['id']) $this -> error('参数错误！', true);
            $user = $User_role -> where("`uid`='{$id}' AND `time`='0'") -> findAll();
            $_user = array();
            foreach($user as $value)
            {
                $_user[] = $value['role'];
            }

            foreach($role as $key => $value)
            {
                if(in_array($value['id'], $_user))
                    $role[$key]['selected'] = 1;
            }
        }

        if($get)
        {
            return $role;
        }else{
            $this -> ajaxReturn($role, '', 1);
        }
    }
    //over Role
}
?>