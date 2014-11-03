﻿<?php
/**
 * 供应商管理Action
 *
 * @author Page7
 * @category Police OA 2012 pack1
 * @copyright Copyright(c) 2011 Interidea.org
 * @version $Id$
 */


class SupplierAction extends Action
{
    public function _initialize()
    {
        $exception = array('index');
        include_once(LIB_PATH.'_initialize.php');
    }


	/**
	 + -----------------------------------------------------------------------
	 *  客户分类
	 + -----------------------------------------------------------------------
	 */
	 public function type()
    {
        if($this -> isAjax())
        {
            $type = $_GET['type'];
            switch($type)
            {
                case 'add':
                    if(!$_POST['val']) $this -> error('名称不能为空！', true);

                    $Type = D('supplier_type');
                    $result = $Type -> add(array('name'=>$_POST['val'], 'parentid'=>$_POST['parent']));
                    if($result)
                        $this -> ajaxReturn($result, '', 1);
                    else
                        $this -> error('添加失败，请重试', true);
                    break;

                case 'edit':
                    if(!$_POST['val'] || !$_POST['id']) $this -> error('名称不能为空！', true);

                    $Type = D('supplier_type');
                    $result = $Type -> save(array('name'=>$_POST['val'], 'id'=>$_POST['id']));
                    if($result)
                        $this -> ajaxReturn($result, '', 1);
                    else
                        $this -> error('保存失败，请重试', true);
                    break;

                case 'delete':
                    if(!$_POST['id']) $this -> error('数据错误！', true);

                    $Type = D('supplier_type');
                    $result = $Type -> delete(array('id'=>$_POST['id']));
                    if($result)
                        $this -> ajaxReturn($result?"true":"false", '', 1);
                    else
                        $this -> error('删除失败，请重试', true);
                    break;
            }
        }

        $types = $this -> getTypes();
		//dump($types);exit;
        $this -> assign('list', $types);
        $this -> display();
    }
    //over

	  //内部函数。分类
    public function getTypes($strmode = false)
    {
        $Type = D('supplier_type');
        $list = $Type -> field('id,name,parentid,lock') -> order("id desc")  -> findAll();
        //提取0级部门
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
        $data = dataTree($level0, $other, 'parentid', $strmode);


        return $data;
    }
    //over



    //内部函数。获取分类子类
    public function getChild($id, $data)
    {
        $children = array();
        $start = false;
        foreach($data as $k => $v)
        {
            if($v['id'] == $id)
            {
                $value['level'] = $v['level'];
                $children[] = $v['id'];
                $start = true;
                continue;
            }
            if($start)
            {
                if($v['level'] > $value['level'])
                {
                    $children[] = $v['id'];
                }else{
                    break;
                }
            }
        }
        return $children;
    }
    //over

    /**
     + -----------------------------------------------------------------------
     *  供应商列表
     + -----------------------------------------------------------------------
     */
    public function index()
    {
		$type = 1;
        $condition = array();

        $types = $this -> getTypes(true);
        $this -> assign('types', $types);

        if(isset($_GET['type']) && $_GET['type'])
        {
            $type = (int)$_GET['type'];
            $_type = $this -> getChild($type, $types);
            $condition[] = "`type` IN (".implode(',', $_type).")";
            $this -> assign('type', $type);
        }

        if(isset($_GET['search']))
        {
            $condition = array();
            if(isset($_GET['name']) && $_GET['name'])
                if($this -> isAjax())
                    $condition[] = "`name` LIKE '%".urldecode($_GET['name'])."%'";
                else
                    $condition[] = "`name` LIKE '%".$_GET['name']."%'";
            $this -> assign('search', 1);
        }

        if($this -> isAjax())
        {
			$Supplier = D('Supplier');
            //$Supplier -> link('info', 'HAS_ONE', 'id', 'Supplier_info', 'id');
            $list = $Supplier -> where($condition) -> field('id,name') -> order("`type` asc,`name` asc") -> findAll();
            $this -> ajaxReturn($list, '', 1);
        }else{
            //权限获取
			$Supplier = D('Supplier', true);
            $can_do = $this -> get('can_do');
            if(!isset($can_do['Supplier']['index'])) $this -> success('您无权执行该操作！', 3, 'Index/index');

            import('Extend.Util.Page');
			$Supplier -> link('type', 'HAS_ONE', 'type', 'Supplier_type', 'id');
            $Supplier -> link('info', 'HAS_ONE', 'id', 'Supplier_info', 'id');
			$Page = new Page($Supplier->count($condition), 15);
            $list = $Supplier -> xfindAll($condition, 'id,name,type', "`type` asc,`name` asc", $Page->firstRow.','.$Page->listRows);

            foreach ($list as $k => $v) {
                $list[$k]['info'] = unserialize($v['info']['value']);
            }

            $page = $Page -> show();
            $this -> assign('page', $page);
            $this -> assign('list', $list);

            if($_GET['p'])
                Cookie::set('supplierPage', $_GET['p']);
            else
                Cookie::set('supplierPage', 1);

            $this -> display();
        }
    }

    /**
     + -----------------------------------------------------------------------
     *  供应商添加
     + -----------------------------------------------------------------------
     */
    public function add()
    {
        if($_POST)
        {
            if(!$_POST['name'])
                $this -> success("供应商名不能为空！", 2);
            $data = array(
                'name'     => $_POST['name'],
				'type'     => $_POST['type'],
            );
            $DB_Supplier = D('Supplier');
            $id = $DB_Supplier -> add($data);
            if($id)
            {
                $info = array('id'=>$id, 'value'=>serialize($_POST['info']));
                $DB_Info = D('Supplier_info');
                if($DB_Info -> add($info))
                {
                    if(!$_GET['url'])
                    {
                        $page = ceil($DB_Supplier -> count("`id` <= {$id}") / 15);
                        $this -> redirect('index', 'Supplier', '', APP_NAME, array(C('VAR_PAGE')=>$page));
                    }else{
                        $this -> redirect('add', $_GET['url'], '', APP_NAME, array('do'=>1));
                    }
                }else{
                    $this -> success('扩展资料错误，请修改', 3, 'Supplier/edit/id/'.$id);
                }
            }else{
                $this -> success('添加失败，请重试', 3);
            }

        }
        //扩展数据
        $DB_Config = D('system_config');
        $data = $DB_Config -> find("`id`=5");
        if($data){
            $data = unserialize($data['value']);
            $this -> assign('extend', $data);
        }

        //获取返回值
        $this -> assign('url', isset($_GET['return']) ? $_GET['return'] : '');

		$types = $this -> getTypes(true);
	    $this -> assign('types', $types);

		$type = isset($_GET['type']) ? $_GET['type'] : 1;
        $this -> assign('type', $type);
        $this -> display();
    }



    /**
     + -----------------------------------------------------------------------
     *  供应商修改
     + -----------------------------------------------------------------------
     */
    public function edit()
    {
        if($_POST)
        {
            $id = (int)$_POST['id'];
            if(!$id) $this -> success("数据访问错误，请重试。", 3);
            if(!$id || !$_POST['name'])
                $this -> success("关键数据错误！", 2);

            $data = array(
                'id'       => $id,
                'name'     => $_POST['name'],
				'type'     => $_POST['type'],
            );

            $DB_Supplier = D('Supplier');
            if($DB_Supplier -> save($data))
            {
                $info = array('id'=>$id, 'value'=>serialize($_POST['info']));
                $DB_Info = D('Supplier_info');
               if(!$DB_Info -> find("`id`={$id}"))
                {
                    $info['id'] = $id;
                    $result = $DB_Info -> add($info);
                }else{
                    $result = $DB_Info -> save($info, "`id`={$id}");
                }
                if($result)
                {
                    $page = Cookie::get('supplierPage');
                    $this -> success('保存成功！', 2, 'Supplier/index/'.C('VAR_PAGE').'/'.$page);
                }else{
                    $this -> success('扩展资料保存失败，请重试', 3);
                }
            }else{
                $this -> success('保存失败，请重试', 3);
            }

        }
        //获取数据
        $id = (int)$_GET['id'];
        $DB_Supplier = D('Supplier', true);
        $DB_Supplier -> link('info', 'HAS_ONE', 'id', 'Supplier_info', 'id');
        $data = $DB_Supplier -> xfind("`id`='{$id}'");
        $data['info'] = unserialize($data['info']['value']);
        $this -> assign($data);
//var_dump($data);exit;
        //扩展数据
        $DB_Config = D('system_config');
        $data = $DB_Config -> find("`id`=5");
        if($data){
            $data = unserialize($data['value']);
            $this -> assign('extend', $data);
        }
		$types = $this -> getTypes(true);
	    $this -> assign('types', $types);

        $this -> display();
    }



    /**
     + -----------------------------------------------------------------------
     *  供应商查看
     + -----------------------------------------------------------------------
     */
    public function view()
    {
        //获取数据
        $id = (int)$_GET['id'];
        $DB_Supplier = D('Supplier', true);
        $DB_Supplier -> link('info', 'HAS_ONE', 'id', 'Supplier_info', 'id');
        $data = $DB_Supplier -> xfind("`id`='{$id}'");
        $data['info'] = unserialize($data['info']['value']);
        $this -> assign($data);

        //扩展数据
        $DB_Config = D('system_config');
        $data = $DB_Config -> find("`id`=5");
        if($data){
            $data = unserialize($data['value']);
            $this -> assign('extend', $data);
        }
		$types = $this -> getTypes(true);
	    $this -> assign('types', $types);
        $this -> display();
    }


    /**
     + -----------------------------------------------------------------------
     *  供应商删除
     + -----------------------------------------------------------------------
     */
    public function delete()
    {
        $id = (int)$_GET['id'];
        if(!$id) $this -> success("数据访问错误，请重试。", 3);
        $DB_Supplier = D('Supplier');
        if($DB_Supplier -> delete("`id`='{$id}'"))
        {
            $DB_Info = D('Supplier_info');
            $DB_Info -> delete("`id`='{$id}'");
            $this -> success('删除成功！', 2, 'Supplier/index');
        }else{
            $this -> success('操作失败，请重试！',3);
        }
    }

}
?>