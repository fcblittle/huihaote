<?php
/**
 * 部门管理Action
 *
 * @author Page7
 * @category Police OA 2012 pack1
 * @copyright Copyright(c) 2011 Interidea.org
 * @version $Id$
 */

class GroupAction extends Action
{
    public function _initialize()
    {
        include_once(LIB_PATH.'_initialize.php');
    }

    /**
     + -----------------------------------------------------------------------
     *  仓库列表
     + -----------------------------------------------------------------------
     */
    public function index()
    {
        $Group = D('group');
        if(!isset($_GET['id']))
        {
            $list = $Group -> field('gid,name,parentid,lock') -> order("gid desc")  -> findAll();
            //提取0级部门
            $level0 = array();
            $other = array();
            foreach ($list as $key=>$value){
                if($value['parentid']==0){
                    $value['level'] = 0;
                    $level0[$value['gid']] = $value;
                }else{
                    $other[$value['gid']] = $value;
                }
            }
            ksort($level0);
            $data = dataTree($level0, $other);
            $this -> assign('list', $data);
            $this -> display();
        }else{
            $id = (int)$_GET['id'];

            //扩展数据
            $DB_Config = D('system_config');
            $data = $DB_Config -> find("`name`='group_info'");
            if($data){
                $data = unserialize($data['value']);
                $this -> assign('extend', $data);
            }

            $Group = D('group', true);
            $Group -> link('info', 'HAS_ONE', 'gid', 'group_info', 'gid');
            $data = $Group -> xfind("`gid`='{$id}'");
            $data['info'] = unserialize($data['info']['value']);
            $this -> assign($data);
            $this -> display('view');
        }
    }
    //over


    /**
     + -----------------------------------------------------------------------
     *  仓库添加
     + -----------------------------------------------------------------------
     */
    public function add()
    {
        if($this -> isAjax())
        {
            if(!$_POST['val']) $this -> error('名称不能为空！', true);

            $Group = D('group');
            $result = $Group -> add(array('name'=>$_POST['val'], 'parentid'=>$_POST['parent']));
            if($result)
                $this -> ajaxReturn($result, '', 1);
            else
                $this -> error('添加失败，请重试', true);
        }else{
            $this -> redirect('index');
        }
    }
    //over


    /**
     + -----------------------------------------------------------------------
     *  仓库编辑
     + -----------------------------------------------------------------------
     */
    public function edit()
    {
        if($this -> isAjax())
        {
            if(!$_POST['val'] || !$_POST['id']) $this -> error('名称不能为空！', true);

            $Group = D('group');
            $result = $Group -> save(array('name'=>$_POST['val'], 'gid'=>$_POST['id']));
            if($result)
                $this -> ajaxReturn($result, '', 1);
            else
                $this -> error('保存失败，请重试', true);
        }else{
            if($_POST)
            {
                if(!$_POST['name'] || !$_POST['id']) $this -> error('名称不能为空！', true);
                $gid = (int)$_POST['id'];

                $Group = D('group');
                $result = $Group -> save(array('name'=>$_POST['name'], 'tel'=>$_POST['tel'], 'address'=>$_POST['address'], 'gid'=>$gid));
                if($result)
                {
                    $info = array('gid'=>$gid, 'value'=>serialize($_POST['info']));
                    $DB_Info = D('group_info');
                    if(!$DB_Info -> find("`gid`={$gid}"))
                    {
                        $info['gid'] = $gid;
                        $result = $DB_Info -> add($info);
                    }else{
                        $result = $DB_Info -> save($info, "`gid`={$gid}");
                    }
                    if($result)
                    {
                        $this -> success("保存成功！", 3, '/Group/index/id/'.$_POST['id']);
                    }else{
                        $this -> success("保存失败，请重试！", 3);
                    }
                }else{
                    $this -> success("保存失败，请重试！", 3);
                }
            }


            $id = (int)$_GET['id'];
            if(!$id) $this -> success("参数有误！", 3);

            //扩展数据
            $DB_Config = D('system_config');
            $data = $DB_Config -> find("`name`='group_info'");
            if($data){
                $data = unserialize($data['value']);
                $this -> assign('extend', $data);
            }

            $Group = D('group', true);
            $Group -> link('info', 'HAS_ONE', 'gid', 'group_info', 'gid');
            $data = $Group -> xfind("`gid`='{$id}'");
            $data['info'] = unserialize($data['info']['value']);
            $this -> assign($data);
            $this -> display();
        }
    }
    //over



    /**
     + -----------------------------------------------------------------------
     *  仓库删除
     + -----------------------------------------------------------------------
     */
    public function delete()
    {
        if($this -> isAjax())
        {
            if(!$_POST['id']) $this -> error('数据错误！', true);

            $Group = D('group');
            $result = $Group -> delete(array('gid'=>$_POST['id']));
            if($result)
            {
                $DB_Info = D('group_info');
                $DB_Info -> delete("`id`='{$id}'");
                $this -> ajaxReturn($result?"true":"false", '', 1);
            }else{
                $this -> error('删除失败，请重试', true);
            }
        }else{
            $this -> redirect('index');
        }
    }
    //over


    /**
     + -----------------------------------------------------------------------
     *  仓库人员
     + -----------------------------------------------------------------------
     */
    public function member()
    {
        //部门结构现行
        $Group = D('group');
        $list = $Group -> field('gid,name,parentid,lock') -> order("gid desc")  -> findAll();
        //提取0级部门
        $level0 = array();
        $other = array();
        foreach ($list as $key=>$value){
        	if($value['parentid']==0){
        		$value['level'] = 0;
                $level0[$value['gid']] = $value;
        	}else{
        		$other[$value['gid']] = $value;
        	}
        }
        ksort($level0);
        $group = dataTree($level0, $other, 'parentid', true);

        //成员修改
        if($_POST)
        {
            $type = $_POST['type'];
            $uid = $_POST['user'];
            $gid = $_POST['group'];
            if(!$type || !$uid || !$gid) $this -> error('参数错误！', true);

            //提取全部子部门
            $group_child = array();
            $start = false;
            foreach($group as $value)
            {
                if($start !== false)
                {
                    if($value['level'] <= $start){ break; }
                    $group_child[] = $value['gid'];
                }else{
                    if($value['gid'] == $gid)
                        $start = $value['level'];
                }
            }
            $group_child[] = $gid;
            $condition = "`gid` IN (".implode(',', $group_child).") AND `uid`='{$uid}'";

            $DB = D("group_member");
            switch($type)
            {
                case 'add':
                    if($DB -> count($condition)) $this -> error('该用户已经属于该群组', true);
                    $this -> ajaxReturn($DB -> add(array('gid'=>$gid, 'uid'=>$uid)), '', 1);
                    break;
                case 'del':
                    $this -> ajaxReturn($DB -> delete($condition), '', 1);
                break;
                case 'role':
                break;
            }
        }

        //提供
        if(isset($_GET['id']))
        {
            if(!$id = (int)$_GET['id']) $this -> error('参数错误！', true);
            //提取全部子部门
            $group_child = array();
            $start = false;
            foreach($group as $value)
            {
                if($start !== false)
                {
                    if($value['level'] <= $start){ break; }
                    $group_child[] = $value['gid'];
                }else{
                    if($value['gid'] == $id)
                        $start = $value['level'];
                }
            }
            $group_child[] = $id;

            $Memeber = D('group_member', true);
            $Memeber -> link('', 'BELONG_TO', 'uid', 'user', 'uid', 'uid,username,name,num');
            $member = $Memeber -> xfindAll("`gid` IN (".implode(',', $group_child).")");
            //提取本组Group显示
            foreach($member as $key => $value)
            {
                if($value['gid'] == $id)
                    $member[$key]['group'] = 1;
            }
            $this -> ajaxReturn($member, '', 1);
        }
        $this -> assign('list', $group);
        $this -> display();
    }
    //over


    //商品 仓库
    public function goods()
    {
        //部门结构现行
        $Group = D('group');
        $list = $Group -> field('gid,name,parentid,lock') -> order("gid desc")  -> findAll();
        //提取0级部门
        $level0 = array();
        $other = array();
        foreach ($list as $key=>$value){
        	if($value['parentid']==0){
        		$value['level'] = 0;
                $level0[$value['gid']] = $value;
        	}else{
        		$other[$value['gid']] = $value;
        	}
        }
        ksort($level0);
        $group = dataTree($level0, $other, 'parentid', true);

        //成员修改
        if($_POST)
        {
            $type = $_POST['type'];
            $good = $_POST['good'];
            $group = $_POST['group'];
            if(!$type || !$good || !$group) $this -> error('参数错误！', true);

            //提取全部子部门
            $group_child = array();
            $start = false;
            foreach($group as $value)
            {
                if($start !== false)
                {
                    if($value['level'] <= $start){ break; }
                    $group_child[] = $value['gid'];
                }else{
                    if($value['gid'] == $group)
                        $start = $value['level'];
                }
            }
            $group_child[] = $group;
            $condition = "`group` IN (".implode(',', $group_child).") AND `good`='{$good}'";

            $DB = D("group_good");
            switch($type)
            {
                case 'add':
                    if($DB -> count($condition)) $this -> error('该用户已经属于该群组', true);
                    $this -> ajaxReturn($DB -> add(array('group'=>$group, 'good'=>$good)), '', 1);
                    break;
                case 'del':
                    $this -> ajaxReturn($DB -> delete($condition), '', 1);
                break;
                case 'role':
                break;
            }
        }

        //提供
        if(isset($_GET['id']))
        {
            if(!$id = (int)$_GET['id']) $this -> error('参数错误！', true);

            //提取全部子部门
            $group_child = array();
            $start = false;
            foreach($group as $value)
            {
                if($start !== false)
                {
                    if($value['level'] <= $start){ break; }
                    $group_child[] = $value['gid'];
                }else{
                    if($value['gid'] == $id)
                        $start = $value['level'];
                }
            }
            $group_child[] = $id;

            $Good = D('group_good', true);
            $Good -> link('', 'BELONG_TO', 'good', 'Goods', 'id', '*');
            $good = $Good -> xfindAll("`group` IN (".implode(',', $group_child).")");
            //提取本组Group显示
            foreach($good as $key => $value)
            {
                if($value['gid'] == $id)
                    $good[$key]['group'] = 1;
            }

            $this -> ajaxReturn($good, '', 1);
        }
        $this -> assign('list', $group);

        $this -> display();
    }
}
?>