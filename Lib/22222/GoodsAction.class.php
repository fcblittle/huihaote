<?php
/**
 * 货品管理Action
 *
 * @author Page7
 * @category Police OA 2012 pack1
 * @copyright Copyright(c) 2011 Interidea.org
 * @version $Id$
 */

class GoodsAction extends Action
{
    public function _initialize()
    {
        $exception = array('index','code');
        include_once(LIB_PATH.'_initialize.php');
    }


    /**
     + -----------------------------------------------------------------------
     *  货品分类
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

                    $Type = D('goods_type');
                    $result = $Type -> add(array('name'=>$_POST['val'], 'parentid'=>$_POST['parent']));
                    if($result)
                        $this -> ajaxReturn($result, '', 1);
                    else
                        $this -> error('添加失败，请重试', true);
                    break;

                case 'edit':
                    if(!$_POST['val'] || !$_POST['id']) $this -> error('名称不能为空！', true);

                    $Type = D('goods_type');
                    $result = $Type -> save(array('name'=>$_POST['val'], 'id'=>$_POST['id']));
                    if($result)
                        $this -> ajaxReturn($result, '', 1);
                    else
                        $this -> error('保存失败，请重试', true);
                    break;

                case 'delete':
                    if(!$_POST['id']) $this -> error('数据错误！', true);

                    $Type = D('goods_type');
                    $result = $Type -> delete(array('id'=>$_POST['id']));
                    if($result)
                        $this -> ajaxReturn($result?"true":"false", '', 1);
                    else
                        $this -> error('删除失败，请重试', true);
                    break;
            }
        }

        $types = $this -> getTypes();
        $this -> assign('list', $types);
        $this -> display();
    }
    //over



    //内部函数。分类
    public function getTypes($strmode = false, $table='goods_type', $pk='id', $id=0)
    {
        $Type = D($table);
        $list = $Type -> field($pk.',name,parentid,lock') -> order("{$pk} desc") -> findAll();

        //提取0级部门
        $level0 = array();
        $other = array();
        foreach ($list as $key=>$value){
            if($value['parentid']==0){
                $value['level'] = 0;
                $level0[$value[$pk]] = $value;
            }else{
                $other[$value[$pk]] = $value;
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
     *  货品列表
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

        if(isset($_GET['search']) || isset($_POST['search']))
        {
            if(isset($_GET['name']) && $_GET['name'])
                $condition[] = "`name` LIKE '%".$_GET['name']."%'";
            if(isset($_GET['num']) && $_GET['num'])
                $condition[] = "`num` = '{$_GET['num']}'";
            $this -> assign('search', 1);
        }

        if($this -> isAjax())
        {
            //用户所管理仓库
            $uid = Session::get('uid');
            $_group = D("Group_member") -> where("`uid`='{$uid}'") -> findAll();
            $group = array();
            foreach ($_group as $k=>$v)
            {
                $group[] = $v['gid'];
            }

            /**
            if(!isset($_POST['service']))
                $condition[] = "`group` in (".implode(', ', $group).")";
            */

            $condition = implode(' AND ', $condition);
            $Goods = D('goods');
            $list = $Goods -> where($condition) -> field('id,name,type,num,price,cost,unit,model')  -> order("`name` asc, `id` asc") -> findAll();
            $this -> ajaxReturn(array('type'=>$types, 'list'=>$list), '', 1);
        }



        if($condition) $condition = implode(' AND ', $condition);

        $Goods = D('goods');

        import('Extend.Util.Page');
        $Page = new Page($Goods->count($condition), 15);
        $list = $Goods -> where($condition) -> field('id,name,num,price,cost,model')  -> order("`num` asc") -> limit($Page->firstRow.','.$Page->listRows) -> findAll();

        $page = $Page -> show();
        $this -> assign('page', $page);
        $this -> assign('list', $list);
        $this -> assign('type', $type);

        if($_GET['p'])
            Cookie::set('goodsPage', $_GET['p']);
        else
            Cookie::set('goodsPage', 1);

        $this -> display();
    }
    //over



    /**
     + -----------------------------------------------------------------------
     *  货品添加
     + -----------------------------------------------------------------------
     */
    public function add()
    {
        if($_POST)
        {
            if(!$_POST['name'])
                $this -> success("货品名不能为空！", 2);

            $data = array(
                'name'     => $_POST['name'],
                'num'      => $_POST['num'],
                'type'     => $_POST['type'],
                'group'    => $_POST['group'],
                'price'    => inputPrice($_POST['price']),
                'cost'     => inputPrice($_POST['cost']),
                'unit'     => $_POST['unit'],
                'model'    => $_POST['model'],
            );

            $Goods = D('goods');

            /**
            if($Goods -> find("`name`='{$_POST['name']}'"))
            {
                $this -> success('已存在同名商品，请更改！', 3);
            }
            */

            $id = $Goods -> add($data);
            if($id)
            {
                if($_POST['group'])
                {
                    $groupGood = array();
                    foreach($_POST['group'] as $k=>$v)
                    {
                        $groupGood[] = array(
                            'group' => $v,
                            'good'  => $id,
                        );
                    }

                    D("Group_good") -> addAll($groupGood);
                }

                $info = array('id'=>$id, 'value'=>serialize($_POST['info']));
                $DB_Info = D('goods_info');
                if($DB_Info -> add($info))
                {
                    if(!$_GET['url'])
                    {
                        $page = ceil($Goods -> count("`id` <= {$id}") / 15);
                        $this -> redirect('index', 'Goods', '', APP_NAME, array(C('VAR_PAGE')=>$page));
                    }else{
                        $this -> redirect('add', $_GET['url'], '', APP_NAME, array('do'=>1));
                    }
                }else{
                    $this -> success('扩展资料错误，请修改', 3, 'Goods/edit/id/'.$id);
                }
            }else{
                $this -> success('添加失败，请重试', 3);
            }

        }
        //扩展数据
        $DB_Config = D('system_config');
        $data = $DB_Config -> find("`id`=3");
        if($data){
            $data = unserialize($data['value']);
            $this -> assign('extend', $data);
        }

        //获取返回值
        $this -> assign('url', isset($_GET['return']) ? $_GET['return'] : '');

        $type = isset($_GET['type']) ? $_GET['type'] : 1;
        $this -> assign('type', $type);

        $types = $this -> getTypes(true);
        $this -> assign('types', $types);

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
        $groups = dataTree($level0, $other, 'parentid', true);
        $this -> assign('groups', $groups);

        $this -> display();
    }



    /**
     + -----------------------------------------------------------------------
     *  货品修改
     + -----------------------------------------------------------------------
     */
    public function edit()
    {
        if($_POST)
        {
            $id = (int)$_POST['id'];
            if(!$id) $this -> success("数据访问错误，请重试。", 3);
            if(!$_POST['name'])
                $this -> success("关键数据错误！", 2);

            $data = array(
                'id'       => $id,
                'name'     => $_POST['name'],
                'num'      => $_POST['num'],
                'type'     => $_POST['type'],
                'group'    => $_POST['group'],
                'price'    => inputPrice($_POST['price']),
                'cost'     => inputPrice($_POST['cost']),
                'unit'     => $_POST['unit'],
                'model'    => $_POST['model'],
            );

            $Goods = D('goods');
            if($Goods -> save($data))
            {

                 D("Group_good") -> delete("`good`={$id}");
                if($_POST['group'])
                {
                    $groupGood = array();
                    foreach($_POST['group'] as $k=>$v)
                    {
                        $groupGood[] = array(
                            'group' => $v,
                            'good'  => $id,
                        );
                    }

                    D("Group_good") -> addAll($groupGood);
                }

                $info = array('id'=>$id, 'value'=>serialize($_POST['info']));
                $DB_Info = D('goods_info');
                if(!$DB_Info -> find("`id`={$id}"))
                {
                    $info['id'] = $id;
                    $result = $DB_Info -> add($info);
                }else{
                    $result = $DB_Info -> save($info, "`id`={$id}");
                }
                if($result)
                {
                    $page = Cookie::get("goodsPage");
                    $this -> success('保存成功！', 2, 'Goods/index/'.C('VAR_PAGE').'/'.$page);
                }else{
                    $this -> success('扩展资料保存失败，请重试', 3);
                }
            }else{
                $this -> success('保存失败，请重试', 3);
            }
        }

        //获取数据
        $id = (int)$_GET['id'];
        $DB_Goods = D('goods', true);
        $DB_Goods -> link('info', 'HAS_ONE', 'id', 'goods_info', 'id');
        $data = $DB_Goods -> xfind("`id`='{$id}'");
        $data['info'] = unserialize($data['info']['value']);
        $this -> assign($data);

        //扩展数据
        $DB_Config = D('system_config');
        $data = $DB_Config -> find("`id`=3");
        if($data){
            $data = unserialize($data['value']);
            $this -> assign('extend', $data);
        }

        $types = $this -> getTypes(true);
        $this -> assign('types', $types);

        //仓库
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
        $groups = dataTree($level0, $other, 'parentid', true);
        $this -> assign('groups', $groups);

        //货品存在的仓库
        $_groupGood = D("Group_good") -> findAll("`good`={$id}");
        $groupGood = array();
        foreach($_groupGood as $k=>$v)
        {
            $groupGood[] = $v['group'];
        }
        $this -> assign('groupGood', $groupGood);

        $this -> display();
    }



    /**
     + -----------------------------------------------------------------------
     *  货品查看
     + -----------------------------------------------------------------------
     */
    public function view()
    {
        //获取数据
        $id = (int)$_GET['id'];
        $DB_Goods = D('goods', true);
        $DB_Goods -> link('info', 'HAS_ONE', 'id', 'goods_info', 'id');
        $data = $DB_Goods -> xfind("`id`='{$id}'");
        $data['info'] = unserialize($data['info']['value']);
        $this -> assign($data);

        //扩展数据
        $DB_Config = D('system_config');
        $data = $DB_Config -> find("`id`=3");
        if($data){
            $data = unserialize($data['value']);
            $this -> assign('extend', $data);
        }

        $this -> display();
    }


    /**
     + -----------------------------------------------------------------------
     *  货品删除
     + -----------------------------------------------------------------------
     */
    public function del()
    {
        $id = (int)$_GET['id'];
        if(!$id) $this -> success("数据访问错误，请重试。", 3);
        $DB_Goods = D('goods');
        if($DB_Goods -> delete("`id`='{$id}'"))
        {
            $DB_Info = D('goods_info');
            $DB_Info -> delete("`id`='{$id}'");
            $this -> success('删除成功！', 2, 'Goods/index');
        }else{
            $this -> success('操作失败，请重试！',3);
        }
    }



    /**
     + -----------------------------------------------------------------------
     *  货品条形码
     + -----------------------------------------------------------------------
     */
    public function code()
    {
        $code = $_GET['num'];
        import("Extend.Util.Image");
        $return = Image::EAN13($code, 'png', 2, 80);
        if($return === false)
            header('Location: ./'.TMPL_DIR.'/default/Public/Images/space.gif');
    }

}
?>