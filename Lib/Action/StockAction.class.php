<?php
/**
 * 库存管理Action
 *
 * @author Page7
 * @category Police OA 2012 pack1
 * @copyright Copyright(c) 2011 Interidea.org
 * @version $Id$
 */

class StockAction extends Action
{
    public function _initialize()
    {
        $exception = array('getsum');
        include_once(LIB_PATH.'_initialize.php');
    }

    //销售价格管理
    public function sprice()
    {
        $this -> assign('tname', '销售价格');
        $this -> assign('act', 'sprice');
        $this -> price('s');
    }

    //采购价格管理
    public function pprice()
    {
        $this -> assign('tname', '采购价格');
        $this -> assign('act', 'pprice');

        $this -> price('p');
    }


    //采购价格管理
    public function servprice()
    {
        $this -> assign('tname', '维修价格');
        $this -> assign('act', 'servprice');

        $this -> price('serv');
    }
    
    //$type     p=采购 s=销售
    public function price($t='p')
    {
        $gid = isset($_GET['gid']) ? $_GET['gid'] : 0;
        $group = $this -> group($gid);

        $this -> assign('t', $t.'audit');

        //判断权限
        if($this -> get('can_manage') == 0)
            $this -> success('对不起，您无权管理该仓库库存。', 3);

        //data
        $condition = '`group` IN ('.implode(',', $group).')';

        if(isset($_GET['goods']) && $_GET['goods'])
        {
            $condition .= " AND `goods` = '{$_GET['goods']}'";
        }else{
            if(isset($_GET['goods_type']) && $_GET['goods_type'] != 1)
            {
                $GoodsAction = A('Goods');
                $types = $GoodsAction -> getTypes(true);
                $_type = $GoodsAction -> getChild($_GET['goods_type'], $types);

                $DB_Goods = D('Goods');
                $_goods = $DB_Goods ->findAll('', 'id,name,type,price,unit');
                $allgoods = array();
                foreach($_goods as $v)
                {
                    if(isset($_GET['goods_type']) && in_array($v['type'], $_type) )
                    {
                        $allgoods[] = $v['id'];
                    }
                }
                if($allgoods) $condition .= " AND `goods` IN (".implode(',', $allgoods).")";
                else $condition .= " AND `goods` = '0'";
            }
        }
        if(isset($_GET['stime']) && $_GET['stime'])
        {
            if(!$stime = strtotime($_GET['stime'].' 00:00:00')) $stime = 0;
            $condition .= " AND `time` >= '{$stime}'";
        }
        if(isset($_GET['etime']) && $_GET['etime'])
        {
            if(!$etime = strtotime($_GET['etime'].' 23:59:59')) $etime = NOW;
            $condition .= " AND `time` < '{$etime}'";
        }

        if($t == 's')
        {
            $orderPrefix = 'xsck';
        }elseif($t == 'p')
        {
            $orderPrefix = 'cgrk';
        }elseif($t == 'serv'){
            $orderPrefix = 'wxgl';
        }else{
            $this -> error("无权限操作！");
        }
            
        //区别采购、销售
        $condition .= " AND `audit`>0 AND `order`>0 AND `comment` LIKE '%{$orderPrefix}%'";
        $data = $this -> getlist($condition, true);
        $this -> assign($data);

        if($_GET['p'])
            Cookie::set('stockPage', $_GET['p']);
        else
            Cookie::set('stockPage', 1);

        $this -> display('price');
    }

    //修改价格管理
    public function editPrice()
    {
        if($_POST)
        {
            $id = (int)$_POST['id'];
            if(!$id) $this -> success("数据访问错误，请重试。", 3);
            //if(!$_POST['time'])
                //$this -> success('必填信息不完整，请重试！', 3);

            $data = array(
                'id'        => $id,
                'group'     => $_POST['group'],
                'price'     => inputPrice($_POST['price']) * ($_POST['num']>0 ? -1 : 1),
                'priceuid'  => Session::get('uid'),
                'rtime'     => NOW,
                'comment'   => $_POST['comment'],
                'mType'     => $_POST['mType'],
                'money'     => inputPrice($_POST['money']),
            );

            //添加/修改应收应付
            $leftMoney = ($data['price'] - $data['money']) * ($_POST['mType']=='实收' ? 1 : -1);

            $DB_Stock = D('stock');
            //保存库存价格信息
            if($DB_Stock -> save($data))
            {
                $data = $DB_Stock -> find("`id`='{$id}'");
                $order = D(ucfirst($data['come'])) -> where("`id`={$data['order']}") -> find();

                if($data['order'])
                {
                    //修改、添加 应收 应付
                    if($leftMoney)
                    {
                        $cType = $order['type'] == 2 ? 'supplier' : 'client';
                        //添加 修改
                        $this -> doDebt($leftMoney, $data['goods'], $data['order'], $data['come'], $order['cors'], $cType, $_POST['comment']);
                    }else{
                        //删除
                        $this -> delDebt($data['goods'], $data['order'], $data['come']);
                    }
                }
                if (preg_match('#cgrk#i', $data['comment'])) {
                    $a = 'pprice';
                } elseif (preg_match('#xsck#i', $data['comment'])) {
                    $a = 'sprice';
                } elseif (preg_match('#wxgl#i', $data['comment'])) {
                    $a = 'servprice';
                }
                $page = Cookie::get('stockPage');
                $this -> success('保存成功！', 2, "Stock/{$a}/".C('VAR_PAGE').'/'.$page);
            }else{
                $this -> success('扩展资料保存失败，请重试', 3);
            }

        }

        //获取数据
        $id = (int)$_GET['id'];
        if($id > 1)
            $condition = "`id`='{$id}'";
        else
            $condition = "`come`='{$_GET['come']}' AND `order`='{$_GET['order']}' AND `goods`='{$_GET['goods']}'";

        $DB_Stock = D('Stock', true);
        $DB_Stock -> link('user', 'HAS_ONE', 'uid', 'user', 'uid', 'uid,name');
        $DB_Stock -> link('goods', 'HAS_ONE', 'goods', 'goods', 'id');
        $data = $DB_Stock -> xfind($condition);
        $this -> assign($data);

        //判断权限
        $this -> group($data['group']);
        if($this -> get('can_manage') == 0)
            $this -> success('对不起，您无权管理该仓库库存。', 3);

        $this -> assign('gid', $data['group']);

        $this -> display('editPrice');
    }



    /**
     + -----------------------------------------------------------------------
     *  出入库情况列表
     + -----------------------------------------------------------------------
     */
    public function index()
    {
        $gid = isset($_GET['gid']) ? $_GET['gid'] : 0;
        $group = $this -> group($gid);

        //判断权限
        if($this -> get('can_manage') == 0)
            $this -> success('对不起，您无权管理该仓库库存。', 3);

        //data
        $this -> assign('type', '');
        $condition = '`group` IN ('.implode(',', $group).')';
        if(isset($_GET['type']) && $_GET['type'] != 'all')
        {
            if($_GET['type'] == "out")
            {
                $condition .= ' AND `num` < 0';
                $this -> assign('type', 'out');
            }else{
                $condition .= ' AND `num` > 0';
                $this -> assign('type', 'in');
            }
        }

        if(isset($_GET['goods']) && $_GET['goods'])
        {
            $condition .= " AND `goods` = '{$_GET['goods']}'";
        }else{
            if(isset($_GET['goods_type']) && $_GET['goods_type'] != 1)
            {
                $GoodsAction = A('Goods');
                $types = $GoodsAction -> getTypes(true);
                $_type = $GoodsAction -> getChild($_GET['goods_type'], $types);

                $DB_Goods = D('Goods');
                $_goods = $DB_Goods ->findAll('', 'id,name,type,price,unit');
                $allgoods = array();
                foreach($_goods as $v)
                {
                    if(isset($_GET['goods_type']) && in_array($v['type'], $_type) )
                    {
                        $allgoods[] = $v['id'];
                    }
                }
                if($allgoods) $condition .= " AND `goods` IN (".implode(',', $allgoods).")";
                else $condition .= " AND `goods` = '0'";
            }
        }
        if(isset($_GET['stime']) && $_GET['stime'])
        {
            if(!$stime = strtotime($_GET['stime'].' 00:00:00')) $stime = 0;
            $condition .= " AND `time` >= '{$stime}'";
        }
        if(isset($_GET['etime']) && $_GET['etime'])
        {
            if(!$etime = strtotime($_GET['etime'].' 23:59:59')) $etime = NOW;
            $condition .= " AND `time` < '{$etime}'";
        }
        $data = $this -> getlist($condition, true);

        $this -> assign($data);

        if($_GET['p'])
            Cookie::set('stockPage', $_GET['p']);
        else
            Cookie::set('stockPage', 1);

        $this -> display();
    }
    //over


    /**
     + -----------------------------------------------------------------------
     *  库存添加
     + -----------------------------------------------------------------------
     */
    public function add()
    {
        if($_POST)
        {
            if(!$_POST['goods'] || !(float)$_POST['num'] || !$_POST['time'])
                $this -> success('必填信息不完整，请重试！', 3);

            $data = array(
                'goods'     => $_POST['goods'],
                'goods_name' => $_POST['goods_name'],
                'group'     => $_POST['group'],
                'num'       => (float)$_POST['num'],
                'price'     => inputPrice($_POST['price']),
                'time'      => strtotime($_POST['time']),
                'uid'       => Session::get('uid'),
                'rtime'     => NOW,
                'comment'   => $_POST['comment'],
                'audit'     => Session::get('uid'),
                'money'     => inputPrice($_POST['price']) * -1,
                'mType'     => $_POST['price'] > 0 ? '实付' : '实收',
            );

            //判断权限
            $this -> group($data['group']);
            if($this -> get('can_manage') == 0)
                $this -> success('对不起，您无权管理该仓库库存。', 3);

            $DB_Stock = D('stock');
            if($data['num'] < 0)
            {
                $condition = "`audit` != '0' AND `group` = '{$_POST['group']}' AND `goods` = '{$_POST['goods']}'";
                $sum = $DB_Stock -> sum($condition, 'num');
                if($sum < abs($data['num']))
                    $this -> success('该仓库（不包含子仓库）数量不足！', 3);
            }

            if($id = $DB_Stock -> add($data))
            {
                $this -> dowarn($_POST['goods']);
                $this -> success('保存成功！', 2, 'Stock/index/');
            }else{
                $this -> success('保存失败，请重试', 3);
            }
        }
        $group = $this -> group();
        $this -> display();
    }
    //over



    /**
     + -----------------------------------------------------------------------
     *  出入库修改
     + -----------------------------------------------------------------------
     */
    public function edit()
    {
        if($_POST)
        {
            $id = (int)$_POST['id'];
            if(!$id) $this -> success("数据访问错误，请重试。", 3);
            if(!$_POST['num'] || !$_POST['time'])
                $this -> success('必填信息不完整，请重试！', 3);

            $data = array(
                'id'        => $id,
                'group'     => $_POST['group'],
                'num'       => $_POST['num'],
                'price'     => inputPrice($_POST['price']) * ($_POST['num']>0 ? -1 : 1),
                'time'      => strtotime($_POST['time']),
                'uid'       => Session::get('uid'),
                'rtime'     => NOW,
                'comment'   => $_POST['comment'],
                'mType'     => $_POST['mType'],
                'money'     => inputPrice($_POST['money']) * ($_POST['mType']=='实收' ? 1 : -1),
            );

            //添加/修改应收应付
            $leftMoney = $data['price'] - $data['money'];

            $DB_Stock = D('stock');
            if($DB_Stock -> save($data))
            {
                $data = $DB_Stock -> find("`id`='{$id}'");
                $order = D(ucfirst($data['come'])) -> where("`id`={$data['order']}") -> find();

                if($data['order'])
                {
                    //修改、添加 应收 应付
                    if($leftMoney)
                    {
                        $cType = $order['type'] == 2 ? 'supplier' : 'client';
                        //添加 修改
                        $this -> doDebt($leftMoney, $data['goods'], $data['order'], $data['come'], $order['cors'], $cType, $_POST['comment']);
                    }else{
                        //删除
                        $this -> delDebt($data['goods'], $data['order'], $data['come']);
                    }
                }

                $this -> dowarn($data['goods']);
                $page = Cookie::get('stockPage');
                $this -> success('保存成功！', 2, 'Stock/index/'.C('VAR_PAGE').'/'.$page);
            }else{
                $this -> success('扩展资料保存失败，请重试', 3);
            }

        }

        //获取数据
        $id = (int)$_GET['id'];
        if($id > 1)
            $condition = "`id`='{$id}'";
        else
            $condition = "`come`='{$_GET['come']}' AND `order`='{$_GET['order']}' AND `goods`='{$_GET['goods']}'";

        $DB_Stock = D('Stock', true);
        $DB_Stock -> link('user', 'HAS_ONE', 'uid', 'user', 'uid', 'uid,name');
        $DB_Stock -> link('goods', 'HAS_ONE', 'goods', 'goods', 'id');
        $data = $DB_Stock -> xfind($condition);
        $this -> assign($data);

        //判断权限
        $this -> group($data['group']);
        if($this -> get('can_manage') == 0)
            $this -> success('对不起，您无权管理该仓库库存。', 3);

        $this -> assign('gid', $data['group']);
        $this -> display();
    }
    //over


    /**
     * 应收应付 款
     */
    public function doDebt($money, $goods, $order, $come, $cors, $cType, $comment)
    {
        $data = array(
            'money'     => $money,
            'goods'     => $goods,
            'order'     => $order,
            'come'      => $come,
            'cors'      => $cors,
            'cType'     => $cType,
            'comment'   => $comment,
            'time'      => time(),
            'uid'       => Session::get('uid'),
        );

        $have = D("Debt") -> where("`goods`={$goods} AND `order`={$order} AND `come`='{$come}'") -> find();
        if($have)
        {
            $result = D("Debt") -> where("`id`={$have['id']}") -> save($data);
        }else{
            $result = D("Debt") -> add($data);
        }

        return $result;
    }


    /**
     * 删除 应收应付
     */
    public function delDebt($goods, $order, $come)
    {
        return false === D("Debt") -> where("`goods`={$goods} AND `order`={$order} AND `come`='{$come}'") -> delete();
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
        if($id > 1)
            $condition = "`id`='{$id}'";
        else
            $condition = "`come`='{$_GET['come']}' AND `order`='{$_GET['order']}' AND `goods`='{$_GET['goods']}'";

        $DB_Stock = D('Stock', true);
        $DB_Stock -> link('user', 'HAS_ONE', 'uid', 'user', 'uid', 'uid,name');
        $DB_Stock -> link('group', 'HAS_ONE', 'group', 'group', 'gid');
        $DB_Stock -> link('goods', 'HAS_ONE', 'goods', 'goods', 'id');
        $data = $DB_Stock -> xfind($condition);

        //判断权限
        $this -> group($data['group']['gid']);
        if($this -> get('can_manage') == 0)
            $this -> success('对不起，您无权管理该仓库库存。', 3);


        $this -> assign($data);

        $this -> display();
    }
    //over


    /**
     + -----------------------------------------------------------------------
     *  记录删除
     + -----------------------------------------------------------------------
     */
    public function delete()
    {
        $id = (int)$_GET['id'];
        if(!$id) $this -> success("数据访问错误，请重试。", 3);
        $DB_Stock = D('Stock');
        $data = $DB_Stock -> find("`id`='{$id}'");

        //判断权限
        $this -> group($data['group']);
        if($this -> get('can_manage') == 0)
            $this -> success('对不起，您无权管理该仓库库存。', 3);

        if($DB_Stock -> delete("`id`='{$id}'"))
        {
            if($data['order'])
                $this -> delDebt($data['goods'], $data['order'], $data['come']);

            $this -> dowarn($data['goods']);
            $this -> success('删除成功！', 2, 'Stock/index');
        }else{
            $this -> success('操作失败，请重试！',3);
        }
    }
    //over


    /**
     + -----------------------------------------------------------------------
     *  记录审核
     + -----------------------------------------------------------------------
     */
    public function audit()
    {
        $id = (int)$_GET['id'];
        if(!$id) $this -> auditall();

        $DB_Stock = D('Stock');
        $data = $DB_Stock -> find("`id`='{$id}'");

        //判断权限
        $this -> group($data['group']);
        if($this -> get('can_manage') == 0)
            $this -> success('对不起，您无权管理该仓库库存。', 3);

        //默认 账目已清
        $auditData['audit'] = Session::get('uid');
        $auditData['mType'] = $data['num'] > 0 ? '实付' : '实收';
        $auditData['money'] = abs($data['price']) * ($auditData['mType']=='实收' ? 1 : -1);

        if($DB_Stock -> save($auditData, "`id`='{$id}'"))
        {
            //添加应收/应付款
            if($data['order'])
            {
                $leftMoney = $data['price'] - $auditData['money'];
                $order = D(ucfirst($data['come'])) -> where("`id`={$data['order']}") -> find();
                $cType = $data['come']=='service' ? 'client' : (($order['type'] == 2 || $order['type'] == 1) ? 'supplier' : 'client');
                $this -> doDebt($leftMoney, $data['goods'], $data['order'], $data['come'], $order['cors'], $cType, $data['comment']);
            }

            $this -> dowarn($data['goods']);
            if($data['order'])
            {
                $A = A('Order');
                $A -> over($data['order']);
            }

            $sum = $DB_Stock -> sum("`goods`='{$data['goods']}' AND `audit` != '0'", 'num');

            $this -> success('确认成功！当前“'.$data['goods_name'].'”总库存量为'.$sum, 2, 'Stock/index');
        }else{
            $this -> success('操作失败，请重试！',3);
        }
    }
    //over


    public function auditall()
    {
        $DB_Stock = D('Stock');
        $data = $DB_Stock -> findAll("`audit`='0'");

        $id = array();
        foreach($data as $key => $value)
        {
            //判断权限
            $this -> group($value['group']);
            if($this -> get('can_manage') == 0)
                continue;

            //默认 账目已清
            $auditData['audit'] = Session::get('uid');
            $auditData['mType'] = $value['num'] > 0 ? '实付' : '实收';
            $auditData['money'] = abs($value['price']) * ($auditData['mType']=='实收' ? 1 : -1);

            if($DB_Stock -> save($auditData, "`id`={$value['id']}"))
            {
                $this -> dowarn($value['goods']);
                if($value['order'])
                {
                    //应收应付
                    $leftMoney = $value['price'] - $value['money'];
                    $order = D(ucfirst($value['come'])) -> where("`id`={$value['order']}") -> find();
                    $cType = $value['come']=='service' ? 'client' : (($order['type'] == 2 || $order['type'] == 1) ? 'supplier' : 'client');
                    $this -> doDebt($leftMoney, $value['goods'], $value['order'], $value['come'], $order['cors'], $cType, $value['comment']);

                    $A = A('Order');
                    $A -> over($value['order']);
                }
            }
        }
        $this -> success('全部确认成功！', 2, 'Stock/index');
    }




    /**
     + -----------------------------------------------------------------------
     *  库存调拨
     + -----------------------------------------------------------------------
     */
    public function flitting()
    {
        if($_POST)
        {
            $group = (int)$_POST['group'];
            $goods = (int)$_POST['goods'];
            $condition = "`audit` != '0' AND `group` = '{$group}' AND `goods`='{$goods}'";
            $DB_Stock = D('Stock');
            $sum = $DB_Stock -> sum($condition, 'num');
            $good = $DB_Stock -> find($condition, 'goods, goods_name', 'goods');

            if(!$good) $this -> success('数据不正确！', 3);
            if($_POST['num'] > $sum) $this -> success('提交数量不能超过库存数！', 3);

            $data = array(
                array(
                    'goods'      => $good['goods'],
                    'goods_name' => $good['goods_name'],
                    'group'     => $group,
                    'num'       => -(float)$_POST['num'],
                    'price'     => 0,
                    'time'      => strtotime($_POST['time']),
                    'uid'       => Session::get('uid'),
                    'rtime'     => NOW,
                    'comment'   => '库存调拨'.$_POST['comment'],
                    'audit'     => Session::get('uid'),
                ),
                array(
                    'goods'      => $good['goods'],
                    'goods_name' => $good['goods_name'],
                    'group'     => (int)$_POST['group_in'],
                    'num'       => (float)$_POST['num'],
                    'price'     => 0,
                    'time'      => strtotime($_POST['time']),
                    'uid'       => Session::get('uid'),
                    'rtime'     => NOW,
                    'comment'   => '库存调拨'.$_POST['comment'],
                    'audit'     => Session::get('uid'),
                )
            );
            if($DB_Stock -> addAll($data))
            {
                $this -> success('调拨成功！', 2, 'Stock/index');
            }else{
                $this -> success('操作失败，请重试！',3);
            }
        }

        $group = 0;
        if(isset($_GET['group']) && $_GET['group'])
        {
            $group = (int)$_GET['group'];
            $this -> assign('step', 2);
            $DB_Stock = D('stock');
            $condition = "`audit` != '0' AND `group` = '{$group}'";
            $goods = $DB_Stock -> findAll($condition, 'SUM(`num`) AS sum, id, goods_name ', '', '', 'goods');
            $this -> assign('goods', $goods);
            $this -> assign('good', '');
            $this -> assign('num', '');

            if(isset($_GET['goods']) && $_GET['goods'])
            {
                $good = (int)$_GET['goods'];
                foreach($goods as $value)
                {
                    if($value['id'] == $good)
                    {
                        if($_GET['num'] > $value['sum'])
                            $this -> success('提交数量不能超过库存数！', 3);
                        $good_name = $value['goods_name'];
                        $this -> assign('step', 'end');
                        $this -> assign('good', $value['id']);
                        $this -> assign('num', $_GET['num']);
                        break;
                    }
                }
            }

        }
        $this -> group($group);
        $this -> display();
    }



    /**
     + -----------------------------------------------------------------------
     *  货品统计
     + -----------------------------------------------------------------------
     */
    public function statistics()
    {
        if($_GET['ac'] == '打 印')
        {
            $this -> printStatics();
            exit;
        }

        if($_GET['ac'] == '导 出')
        {
            $this -> export();
            exit;
        }

        //货品分类
        $GoodsAction = A('Goods');
        $types = $GoodsAction -> getTypes(true);
        $_type = $GoodsAction -> getChild($_GET['goods_type'], $types);

        $DB_Goods = D('Goods');
        //所有商品
        $_goods = $DB_Goods ->findAll('', 'id,name,type,price,cost,unit,model', "`name` asc, `id` asc");
        $allgoods = array();
        $type_goods = array();
        foreach($_goods as $v)
        {
            if(isset($_GET['goods_type']) && in_array($v['type'], $_type))
            {
                $type_goods[] = $v['id'];
            }
            $allgoods[$v['id']] = $v;
        }
        $this -> assign('allgoods', $allgoods);


        $group = 0;
        $goods = 0;
        $stime = 0;
        $etime = NOW;
        $type = isset($_GET['type']) ? $_GET['type'] : '';


        //仓库
        if(isset($_GET['group']))
        $group = (int)$_GET['group'];
        $group = $this -> group($group);
        //货品
        if(isset($_GET['goods']))
            $goods = (int)$_GET['goods'];
        $this -> assign('goods', $goods);

        //时间
        if(isset($_GET['stime']) && $_GET['stime'])
            $stime = strtotime($_GET['stime']);
        if(isset($_GET['etime']) && $_GET['etime'])
            $etime = strtotime($_GET['etime'].' 23:59:59');


        $condition = array("`audit` != '0'");
        switch($type)
        {
            //进出统计
            case 'inout':
                $goods = $goods ? $goods : $_goods[0]['id'];
                $condition[] = "`time` >= '{$stime}' AND `time` <= '{$etime}'";
                $condition[] = "`group` IN (".implode(',', $group).")";
                $condition[] = "`goods` = {$goods}";

                $condition = implode(' AND ', $condition);

                $DB_Stock = D('Stock');

                //in
                $condition1 = $condition." AND `num` > 0";
                $insum = $DB_Stock -> sum($condition1, 'num');

                //out
                $condition2 = $condition." AND `num` < 0";
                $outsum = $DB_Stock -> sum($condition2, 'num');

                $data = array(
                    'in'  => abs($insum),
                    'out' => abs($outsum),
                );
                $this -> assign('json', json_encode($data));
                $this -> assign('unit', $allgoods[$goods]['unit']);

                $this -> display('statistics_inout');
                break;

            //进出量走势
            case 'trend':
                if(!$stime) $stime = strtotime("previous month");

                $goods = $goods ? $goods : $_goods[0]['id'];
                $condition[] = "`time` >= '{$stime}' AND `time` <= '{$etime}'";
                $condition[] = "`group` IN (".implode(',', $group).")";
                $condition[] = "`goods` = '{$goods}'";

                $condition = implode(' AND ', $condition);

                $DB_Stock = D('Stock');

                //in
                $insumlist = $DB_Stock -> findAll($condition." AND `num` > 0", 'SUM(num) as sum,time', '', '', 'time');
                $indata = array();
                if($insumlist)
                foreach($insumlist as $key => $value)
                    $indata[date("Y-m-d", $value['time'])] = isset($indata[date("Y-m-d", $value['time'])]) ? $indata[date("Y-m-d", $value['time'])]+$value['sum'] : $value['sum'];

                //out
                $outsumlist = $DB_Stock -> findAll($condition." AND `num` < 0", 'SUM(num) as sum,time', '', '', 'time');
                $outdata = array();
                if($outsumlist)
                foreach($outsumlist as $key => $value)
                    $outdata[date("Y-m-d", $value['time'])] = isset($outdata[date("Y-m-d", $value['time'])]) ? $outdata[date("Y-m-d", $value['time'])]+$value['sum'] : $value['sum'];

                $data = array();
                $Y = 0;
                $m = 0;

                for($i = $stime; $i <= $etime; $i = $i + 3600*24)
                {
                    $dk = date("Y-m-d", $i);
                    $key = date("d", $i);
                    if(!$data) $key = $dk;
                    if($m != date("m", $i)) $key = date("d m", $i);
                    if($Y != date("Y", $i)) $key = date("d m Y", $i);
                    $Y =  date("Y", $i);
                    $m =  date("m", $i);
                    $data[$key] = array( 'in'=>isset($indata[$dk])?$indata[$dk]:0 , 'out'=>isset($outdata[$dk])? abs($outdata[$dk]):0);
                }

                $this -> assign('json', json_encode($data));
                $this -> assign('unit', $allgoods[$goods]['unit']);

                $this -> display('statistics_trend');
                break;

            default:
                //货品分类 当货品分类为1时 获取全部商品
                if(isset($_GET['goods_type']) && !$goods)
                {
                    $listGoods = implode(',', $type_goods);

                    if((int)$_GET['goods_type'] != 1)
                        $goods = implode(',', $type_goods);
                }elseif($goods){
                    $listGoods = $goods;
                }
                    
                //$this -> assign('colspan', 3);
                $this -> assign('colspan', 6);
                $condition[] = "`group` IN (".implode(',', $group).")";
                if($goods)
                    $condition[] = "`goods` IN ({$goods})";

                // if($stime)
                //     $condition[] = "`time`>={$stime}";
                // if($etime)
                //     $condition[] = "`time`<={$etime}";

                $condition_str = implode(' AND ', $condition);

                //结束量
                $DB_Stock = D('Stock');

                //采购 一段时间内
                $_sumlist = $DB_Stock -> findAll($condition_str." AND `time` <= {$etime} AND ((`num`>0 AND `come`='order') or (`num`<0 AND `come`='return'))", 'SUM(`num`) AS sum, goods, SUM(`price`) as price', '', '', '`goods`');
                foreach($_sumlist as $v)
                {
                    $sumlist[$v['goods']] = array(
                        'num'       => $v['sum'],
                        'pay'       => $v['price'],
                    );
                }

                //销售
                $_sumlist = $DB_Stock -> findAll($condition_str." AND `time` <= {$etime} AND ((`num`<0 AND `come`='order') or (`num`>0 AND `come`='return'))", 'SUM(`num`) AS sum, goods, SUM(`price`) as price', '', '', '`goods`');
                foreach($_sumlist as $v)
                {
                    if(isset($sumlist[$v['goods']]))
                    {
                        $sumlist[$v['goods']]['num'] += $v['sum'];
                    }else{
                        $sumlist[$v['goods']]['num'] = $v['sum'];
                    }

                    $sumlist[$v['goods']]['income'] = $v['price'];
                }


                //自己录入  计入采购总价
                $_sumlist = $DB_Stock -> findAll($condition_str." AND `time` <= {$etime} AND `come`=''", 'SUM(`num`) AS sum, goods, SUM(`price`) as price', '', '', '`goods`');
                foreach($_sumlist as $v)
                {
                    if(isset($sumlist[$v['goods']]))
                    {
                        $sumlist[$v['goods']]['num'] += $v['sum'];
                        $sumlist[$v['goods']]['pay'] += $v['price'];
                    }else{
                        $sumlist[$v['goods']]['num'] = $v['sum'];
                        $sumlist[$v['goods']]['pay'] = $v['price'];
                    }
                }

                //自带
                $_sumlist = $DB_Stock -> findAll($condition_str." AND `time` <= {$etime} AND (`come`!='order' AND `come`!='return' AND `come`!='')", 'SUM(`num`) AS sum, goods, price', '', '', '`goods`');
                foreach($_sumlist as $v)
                {
                    if(isset($sumlist[$v['goods']]))
                    {
                        $sumlist[$v['goods']]['num'] += $v['sum'];
                    }else{
                        $sumlist[$v['goods']]['num'] = $v['sum'];
                    }
                }

                //初始量
                $_sumslist = $DB_Stock -> findAll($condition_str." AND `time` < {$stime}", 'SUM(`num`) AS sum, goods, SUM(`price`) as price', '', '', '`goods`');
                $sumslist = array();
                foreach($_sumslist as $v)
                {
                    $sumslist[$v['goods']] = array(
                        'num'       => $v['sum'],
                        'price'     => $v['price'],
                    );
                }
                /*和上面的3对应
                if($stime)
                {
                    $this -> assign('colspan', 6);
                }
                */
                $condition_str = $condition_str." AND `time` <= {$etime} AND `time` > {$stime}";

                //时间段进量
                $_suminlist = $DB_Stock -> findAll($condition_str.' AND `num` > 0', 'SUM(`num`) AS sum, goods, SUM(`price`) as price', '', '', '`goods`');
                $suminlist = array();
                foreach($_suminlist as $v)
                {
                    $suminlist[$v['goods']] = array(
                        'num'       => $v['sum'],
                        'price'     => $v['price'],
                    );
                }

                //时间段出量
                $_sumoutlist = $DB_Stock -> findAll($condition_str.' AND `num` < 0', 'SUM(`num`) AS sum, goods, price', '', '', '`goods`');
                $sumoutlist = array();
                foreach($_sumoutlist as $v)
                {
                    $sumoutlist[$v['goods']] = array(
                        'num'       => $v['sum'],
                        'price'     => $v['price'],
                    );
                }


                $list = array();
                $listGoods = explode(',', $listGoods);
                foreach($listGoods as $key => $value)
                {
                    $list[$key]['goods'] = $value;
                    $list[$key]['start'] = isset($sumslist[$value]) ? $sumslist[$value]['num'] : 0;
                    $list[$key]['sum'] = isset($sumlist[$value]) ? $sumlist[$value]['num'] : 0;
                    $list[$key]['in'] = isset($suminlist[$value]) ? $suminlist[$value]['num'] : 0;
                    $list[$key]['inprice'] = isset($suminlist[$value]) ? $suminlist[$value]['price'] : 0;
                    $list[$key]['out'] = isset($sumoutlist[$value]) ? $sumoutlist[$value]['num'] : 0;
                    $list[$key]['outprice'] = isset($sumoutlist[$value]) ? $sumoutlist[$value]['price'] : 0;

                    //excel
                    $list[$key]['goodsname'] = $allgoods[$value]['name'];
                    //$list[$key]['cost'] = $allgoods[$value]['cost'];
                    $list[$key]['income'] = $sumlist[$value]['income'];
                    $list[$key]['pay'] = $sumlist[$value]['pay'];

                    //$list[$key]['costTotal'] = $list[$key]['cost']*$list[$key]['sum'];
                    //$list[$key]['priceTotal'] = $sumlist[$value]['price'];//$list[$key]['price']*$list[$key]['sum'];
                }

                $this -> assign('list', $list);

                $this -> display();
        }
    }
    //over

    /**
     + -----------------------------------------------------------------------
     *  详细信息
     + -----------------------------------------------------------------------
     */
    public function detail()
    {
        $group = $_GET['group'];
        $goods = $_GET['goods'];

        $goodsInfo = D("Goods") -> where("`id` = '{$goods}'") -> field("id,name,model") -> find();
        $groupInfo = D("Group") -> where("`gid` = '{$group}'") -> field("gid,name") -> find();
        $this -> assign('goodsInfo',$goodsInfo);
        $this -> assign('groupInfo',$groupInfo);
        //data
        $condition = 1;
        $condition .= " AND `audit` != 0 AND `num` > 0 AND `goods` = '{$goods}'" . ((int)$group > 1 ? " AND `group`=". $group : '');

        $data = $this -> getlist($condition, true);
        $list = $data['list'];
        $condition1 = "`goods` = '{$goods}'". ((int)$group > 1 ? " AND `group`=". $group : '');
        $info = D("Stock_info") -> where($condition1) -> findAll();

        //$list = array();
        foreach ($list as $k => $v) {
            $list[$k]['perprice'] = $v['price']/$v['num'];
            foreach ($info as $k1 => $v1) {

                if($v['id'] == $v1['stock'])
                    $list[$k]['num'] -= $v1['num'];
            }
        }
//var_dump($list);exit;
        $this -> assign('list',$list);
/*
        if($_GET['p'])
            Cookie::set('stockPage', $_GET['p']);
        else
            Cookie::set('stockPage', 1);
*/
        $this -> display();
    }

    public function printStatics()
    {
        //货品
        $GoodsAction = A('Goods');
        $types = $GoodsAction -> getTypes(true);
        $_type = $GoodsAction -> getChild($_GET['goods_type'], $types);

        $DB_Goods = D('Goods');
        $_goods = $DB_Goods ->findAll('', 'id,name,type,price,cost,unit', "`name` asc, `id` asc");
        $allgoods = array();
        $type_goods = array();
        foreach($_goods as $v)
        {
            if(isset($_GET['goods_type']) && in_array($v['type'], $_type))
            {
                $type_goods[] = $v['id'];
            }
            $allgoods[$v['id']] = $v;
        }
        $this -> assign('allgoods', $allgoods);


        $group = 0;
        $goods = 0;
        $stime = 0;
        $etime = NOW;
        $type = isset($_GET['type']) ? $_GET['type'] : '';


        //仓库
        if(isset($_GET['group']))
        $group = (int)$_GET['group'];
        $group = $this -> group($group);

        //货品
        if(isset($_GET['goods']))
        $goods = (int)$_GET['goods'];
        $this -> assign('goods', $goods);

        //时间
        if(isset($_GET['stime']) && $_GET['stime'])
            $stime = strtotime($_GET['stime']);
        if(isset($_GET['etime']) && $_GET['etime'])
            $etime = strtotime($_GET['etime'].' 23:59:59');


        $condition = array("`audit` != '0'");

        //货品分类
        if(isset($_GET['goods_type']) && !$goods)
        {
            $goods = implode(',', $type_goods);
        }

        $this -> assign('colspan', 3);
        $condition[] = "`group` IN (".implode(',', $group).")";
        if($goods)
        {
            $condition[] = "`goods` IN ({$goods})";
        }else if(isset($_GET['goods_type']) && $_GET['goods_type'] != 1){
            $condition[] = "`goods` = '0'";
        }


        $condition_str = implode(' AND ', $condition);

        //结束量
        $DB_Stock = D('Stock');
        $_sumlist = $DB_Stock -> findAll($condition_str." AND `time` <= {$etime}", 'SUM(`num`) AS sum, goods', '', '', '`goods`');
        foreach($_sumlist as $v)
            $sumlist[$v['goods']] = $v['sum'];

        //初始量
        $_sumslist = $DB_Stock -> findAll($condition_str." AND `time` < {$stime}", 'SUM(`num`) AS sum, goods', '', '', '`goods`');
        $sumslist = array();
        foreach($_sumslist as $v)
            $sumslist[$v['goods']] = $v['sum'];


        if($stime)
        {
            $this -> assign('colspan', 6);
        }

        $condition_str = $condition_str." AND `time` <= {$etime} AND `time` > {$stime}";

        //时间段进量
        $_suminlist = $DB_Stock -> findAll($condition_str.' AND `num` > 0', 'SUM(`num`) AS sum, goods', '', '', '`goods`');
        $suminlist = array();
        foreach($_suminlist as $v)
            $suminlist[$v['goods']] = $v['sum'];

        //时间段出量
        $_sumoutlist = $DB_Stock -> findAll($condition_str.' AND `num` < 0', 'SUM(`num`) AS sum, goods', '', '', '`goods`');
        $sumoutlist = array();
        foreach($_sumoutlist as $v)
            $sumoutlist[$v['goods']] = $v['sum'];


        $list = array();
        $goods = explode(',', $goods);
        foreach($goods as $key => $value)
        {
            $list[$key]['goods'] = $value;
            $list[$key]['start'] = isset($sumslist[$value]) ? $sumslist[$value] : 0;
            $list[$key]['sum'] = isset($sumlist[$value]) ? $sumlist[$value] : 0;
            $list[$key]['in'] = isset($suminlist[$value]) ? $suminlist[$value] : 0;
            $list[$key]['out'] = isset($sumoutlist[$value]) ? $sumoutlist[$value] : 0;

            //excel
            $list[$key]['goodsname'] = $allgoods[$value]['name'];
            $list[$key]['cost'] = $allgoods[$value]['cost'];
            $list[$key]['price'] = $allgoods[$value]['price'];
            $list[$key]['costTotal'] = $list[$key]['cost']*$list[$key]['sum'];
            $list[$key]['priceTotal'] = $list[$key]['price']*$list[$key]['sum'];
        }

        $this -> assign('list', $list);

        if($_GET['ac'] == '导 出')
        {
            $this -> sqlToExcel($list);
        }

        $this -> assign('_group', isset($_GET['group']) ? D('Group')->where("`gid`={$_GET['group']}")->find() : '');
        $this -> assign('_goods_type', isset($_GET['goods_type']) ? D('goods_type')->where("`id`={$_GET['goods_type']}")->find() : '');
        $this -> assign('_goods', (int)$_GET['goods'] ? D('goods')->where("`id`={$_GET['goods']}")->find() : '');
        $this -> assign('start', isset($_GET['stime']) ? $_GET['stime'] : '');
        $this -> assign('end', isset($_GET['etime']) ? $_GET['etime'] : '');

        $this -> display('printStatics');
    }


    protected function sqlToExcel($data)
    {

		error_reporting(E_ALL);
		date_default_timezone_set('Asia/Shanghai');

		import("@.ORG.PHPExcel", '', '.php');

		$objPHPExcel = new PHPExcel();


		$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
									 ->setLastModifiedBy("Maarten Balliauw")
									 ->setTitle("Office 2007 XLSX Test Document")
									 ->setSubject("Office 2007 XLSX Test Document")
									 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Test result file");


		$objPHPExcel->setActiveSheetIndex(0);

		$fields = array('A'=>'goodsname', 'B'=>'sum');
        $titles = array('A'=>'产品名称', 'B'=>'总量');
        $width = array('A'=>27.50, 'B'=>10);

        foreach ($titles as $key=>$val)
        {

			$objPHPExcel->getActiveSheet()->getColumnDimension($key)->setWidth($width[$key]);
			$objPHPExcel->getActiveSheet()->getStyle($key)->getAlignment()->setWrapText(true);

            $objPHPExcel->getActiveSheet()->setCellValue($key.'1', $val);
        }

		$x = 2;
		foreach ($data as $key=>$val)
		{
			foreach ($fields as $m=>$n)
			{
				$objPHPExcel->getActiveSheet()->setCellValue($m.$x, $val[$n]);
			}
			$x++;
		}

		import("@.ORG.PHPExcel.IOFactory", '', '.php');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

		header('Content-Type: application/vnd.ms-excel');
		$time = date("Y-m-d");
		header('Content-Disposition: attachment;filename="book_'.$time.'.xls"');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
    }


    /**
     + -----------------------------------------------------------------------
     *  货品损益
     + -----------------------------------------------------------------------
     */
    public function inorde()
    {
        if($_POST)
        {
            if(!$_POST['goods'] || !$_POST['num'] || !$_POST['price'])
                $this -> success('必填信息不完整，请重试！', 3);

            $data = array(
                'goods'     => $_POST['goods'],
                'goods_name' => $_POST['goods_name'],
                'group'     => $_POST['group'],
                'num'       => $_POST['type'] == 'in' ? abs($_POST['num']) : -abs($_POST['num']),
                'price'     => inputPrice($_POST['price']),
                'time'      => strtotime(date('Y-m-d')),
                'uid'       => Session::get('uid'),
                'rtime'     => NOW,
                'comment'   => $_POST['type'] == 'in' ? '库存报溢' : '库存报损',
                'audit'     => Session::get('uid'),
            );

            $DB_Stock = D('stock');
            if($data['num'] < 0)
            {
                $condition = "`audit` != '0' AND `group` = '{$_POST['group']}' AND `goods` = '{$_POST['goods']}'";
                $sum = $DB_Stock -> sum($condition, 'num');
                if($sum < abs($data['num']))
                    $this -> success('该仓库（不包含子仓库）报损数量不足！', 3);
            }

            //判断权限
            $this -> group($data['group']);
            if($this -> get('can_manage') == 0)
                $this -> success('对不起，您无权管理该仓库库存。', 3);

            if($id = $DB_Stock -> add($data))
            {
                $this -> dowarn($_POST['goods']);
                $this -> success('保存成功！', 2, 'Stock/index');
            }else{
                $this -> success('保存失败，请重试', 3);
            }
        }

        $group = 0;
        $g = 0;
        if(isset($_GET['group'])) $this -> assign('gid',$group = $_GET['group']);
        if(isset($_GET['goods'])) $this -> assign('goods', $g = $_GET['goods']);

        //货品
        $DB_Goods = D('Goods');
        $goods = $DB_Goods -> findAll('', 'id,name,price,unit,model');
        $this -> assign('allgoods', $goods);
        if(!$g)
        {
            $this -> assign('g', $goods[0]);
        }else{
            foreach($goods as $v)
            {
                if($v['id'] == $g)
                {
                    $g = $v;
                    break;
                }
            }
            if(!$g) $this -> assign('g', $goods[0]);
            $this -> assign('g', $g);
        }

        $type = (isset($_GET['type']) && $_GET['type'] == 'in') ? 'in' : 'de';
        $this -> assign('type', $type);

        $group = $this -> group($group);
        $this -> display();
    }
    //over


    /**
     + -----------------------------------------------------------------------
     *  货品损益
     + -----------------------------------------------------------------------
     */
    public function report()
    {
        if($_POST)
        {
            //if(!$_POST['goods'] || !$_POST['num'] || !$_POST['price'])
            if(!$_POST['goods'] || !$_POST['num'])
                $this -> success('必填信息不完整，请重试！', 3);

            $data = array(
                'goods'     => $_POST['goods'],
                'goods_name' => $_POST['goods_name'],
                'group'     => $_POST['group'],
                'num'       => $_POST['type'] == 'in' ? abs($_POST['num']) : -abs($_POST['num']),
                //'price'     => inputPrice($_POST['price']),
                'price'     => 0,
                'time'      => strtotime(date('Y-m-d')),
                'uid'       => Session::get('uid'),
                'rtime'     => NOW,
                'comment'   => $_POST['type'] == 'in' ? '库存报溢' : '库存报损',
                'audit'     => Session::get('uid'),
            );

            $DB_Stock = D('stock');
            if($data['num'] < 0)
            {
                $condition = "`audit` != '0' AND `group` = '{$_POST['group']}' AND `goods` = '{$_POST['goods']}'";
                $sum = $DB_Stock -> sum($condition, 'num');
                if($sum < abs($data['num']))
                    $this -> success('该仓库（不包含子仓库）报损数量不足！', 3);
            }

            //判断权限
            $this -> group($data['group']);
            if($this -> get('can_manage') == 0)
                $this -> success('对不起，您无权管理该仓库库存。', 3);


            if($_POST['type'] == 'de')
            {
                $info = array();

                $cost = A("Order")->productCost($data['goods'],abs($data['num']),$data['group']);
                $info = $cost['info'];
                $DB_Stock->startTrans();
                if($id = $DB_Stock -> add($data))
                {

                    $stock_info = D("Stock_info") -> addAll($info);
                    if($stock_info)
                    {
                        $DB_Stock -> commit();
                        $this -> success('保存成功！', 2, 'Stock/report');
                    }else{
                        $DB_Stock -> rollback();
                        $this -> success('保存失败，请重试', 3);
                    }
                }else{
                    $DB_Stock -> rollback();
                    $this -> success('保存失败，请重试', 3);
                }
            }else{

                if($id = $DB_Stock -> add($data))
                {
                    $this -> dowarn($_POST['goods']);
                    $this -> success('保存成功！', 2, 'Stock/report');
                }else{
                    $this -> success('保存失败，请重试', 3);
                }
            }
           
           
        }

        $group = 0;
        $g = 0;
        if(isset($_GET['group'])) $this -> assign('gid',$group = $_GET['group']);
        if(isset($_GET['goods'])) $this -> assign('goods', $g = $_GET['goods']);

        //货品
        $DB_Goods = D('Goods');
        $goods = $DB_Goods -> findAll('', 'id,name,price,unit,model');
        $this -> assign('allgoods', $goods);
        if(!$g)
        {
            $this -> assign('g', $goods[0]);
        }else{
            foreach($goods as $v)
            {
                if($v['id'] == $g)
                {
                    $g = $v;
                    break;
                }
            }
            if(!$g) $this -> assign('g', $goods[0]);
            $this -> assign('g', $g);
        }

        //$type = (isset($_GET['type']) && $_GET['type'] == 'in') ? 'in' : 'de';
        //$this -> assign('type', $type);

        $group = $this -> group($group);
        $this -> display();
    }
    //over

    //库存警告邮件发送
    public function warn()
    {
        $DB_Warn = D('Stock_warn');
        $uid = Session::get('uid');

        if($_POST)
        {
            $data = array();
            foreach($_POST['goods'] as $key => $value)
            {
                if($value && $_POST['num'][$key])
                    $data[] = array(
                        'goods' => $value,
                        'group' => $_POST['group'][$key],
                        'num'   => $_POST['num'][$key],
                        'uid'   => $uid,
                    );
            }
            $DB_Warn -> deleteAll("`uid`='{$uid}'");
            if($DB_Warn -> addAll($data))
            {
                $this -> success('保存成功！', 2, 'Stock/warn');
            }else{
                $this -> success('保存失败，请重试！', 3);
            }
        }

        $list = $DB_Warn -> findAll("`uid` = '{$uid}'");
        $this -> assign('list', $list);

        //货品
        $DB_Goods = D('Goods');
        $_goods = $DB_Goods -> findAll('', 'id,name,price,unit');
        $allgoods = array();
        foreach($_goods as $v)
            $allgoods[$v['id']] = $v;
        $this -> assign('allgoods', $allgoods);

        $group = $this -> group();
        $this -> display();
    }
    //over



    //邮件发送
    protected function dowarn($goods)
    {
        $goods = (int)$goods;
        $DB_Warn = D('Stock_warn');
        $list = $DB_Warn -> findAll("`goods` = '{$goods}'");

        //货品
        $DB_Goods = D('Goods');
        $_goods = $DB_Goods -> findAll('', 'id,name,price,unit,model');
        $allgoods = array();
        foreach($_goods as $v)
            $allgoods[$v['id']] = $v;

        //获取群组信息
        $DB_Group = D('group');
        $_group = $DB_Group -> field('gid,name') -> findAll();
        $group = array();
        foreach ($_group as $value)
            $group[$value['gid']] = $value;

        $DB_Stock = D('Stock');
        $inform_list = array();
        foreach($list as $value)
        {
            $condition = "`goods` = '{$goods}' AND `group` IN (".implode(',', $this->group($value['group'])).") AND `audit` != 0";
            $sum = $DB_Stock -> sum($condition, 'num');
            if($sum <= $value['num'])
            {
                $inform = array(
                    'title'   => '库存警告！“'.$group[$value['group']]['name'].'”库中的“'.$allgoods[$value['goods']]['name'].'('.$allgoods[$value['goods']]['model'].')'.'”不足！',
                    'content' => '“'.$group[$value['group']]['name'].'”库中的“'.$allgoods[$value['goods']]['name'].'('.$allgoods[$value['goods']]['model'].')'.'”数量已经低于'.$value['num'].$allgoods[$value['goods']]['unit'].'！',
                    'time'    => NOW,
                    'hide'    => 0,
                );
                $DB_Inform = D('Inform');
                if($iid = $DB_Inform -> add($inform)){
                    $inform_list[] = array(
                        'uid' => $value['uid'],
                        'inform' => $iid,
                        'read'   => 0,
                    );
                }
            }
        }
        if(!$inform_list) return true;
        return D('user_inform') -> addAll($inform_list);
    }
    //mail over


    //导出库存
    public function export()
    {
        //货品
        $GoodsAction = A('Goods');
        $types = $GoodsAction -> getTypes(true);
        $_type = $GoodsAction -> getChild($_GET['goods_type'], $types);

        $DB_Goods = D('Goods');
        $_goods = $DB_Goods ->findAll('', 'id,name,type,price,cost,unit,model', "`name` asc, `id` asc");
        $allgoods = array();
        $type_goods = array();
        foreach($_goods as $v)
        {
            if(isset($_GET['goods_type']) && in_array($v['type'], $_type))
            {
                $type_goods[] = $v['id'];
            }
            $allgoods[$v['id']] = $v;
        }
        $this -> assign('allgoods', $allgoods);


        $group = 0;
        $goods = 0;
        $stime = 0;
        $etime = NOW;
        $type = isset($_GET['type']) ? $_GET['type'] : '';


        //仓库
        if(isset($_GET['group']))
        $group = (int)$_GET['group'];
        $group = $this -> group($group);

        //货品
        if(isset($_GET['goods']))
        $goods = (int)$_GET['goods'];
        $this -> assign('goods', $goods);

        //时间
        if(isset($_GET['stime']) && $_GET['stime'])
            $stime = strtotime($_GET['stime']);
        if(isset($_GET['etime']) && $_GET['etime'])
            $etime = strtotime($_GET['etime'].' 23:59:59');

        $condition = array("`audit` != '0'");

        //货品分类
        if(isset($_GET['goods_type']) && !$goods)
        {
            $goods = implode(',', $type_goods);
        }

        $this -> assign('colspan', 3);
        $condition[] = "`group` IN (".implode(',', $group).")";
        if($goods)
        {
            $condition[] = "`goods` IN ({$goods})";
        }else if(isset($_GET['goods_type']) && $_GET['goods_type'] != 1){
            $condition[] = "`goods` = '0'";
        }


        $condition_str = implode(' AND ', $condition);

        //结束量
        $DB_Stock = D('Stock');
        $_sumlist = $DB_Stock -> findAll($condition_str." AND `time` <= {$etime}", 'SUM(`num`) AS sum, goods', '', '', '`goods`');
        foreach($_sumlist as $v)
            $sumlist[$v['goods']] = $v['sum'];

        //初始量
        $_sumslist = $DB_Stock -> findAll($condition_str." AND `time` < {$stime}", 'SUM(`num`) AS sum, goods', '', '', '`goods`');
        $sumslist = array();
        foreach($_sumslist as $v)
            $sumslist[$v['goods']] = $v['sum'];


        if($stime)
        {
            $this -> assign('colspan', 6);
        }

        $condition_str = $condition_str." AND `time` <= {$etime} AND `time` > {$stime}";

        $_suminlist = $DB_Stock -> findAll($condition_str.' AND `num`!=0', 'SUM(`num`) AS sum, goods', '', '', '`goods`');
        $suminlist = array();
        foreach($_suminlist as $v)
            $suminlist[$v['goods']] = $v['sum'];


        $list = array();
        $goods = explode(',', $goods);
        foreach($goods as $key => $value)
        {
            if(isset($sumlist[$value]) && abs((int)$sumlist[$value]) > 0)
            {
                $list[$key]['goods'] = $value;
                $list[$key]['sum'] = isset($sumlist[$value]) ? $sumlist[$value] : 0;

                //excel
                $list[$key]['goodsname'] = $allgoods[$value]['name'].'('.$allgoods[$value]['model'].')';
            }
        }

        $this -> assign('list', $list);

        if($_GET['ac'] == '导 出')
        {
            $this -> sqlToExcel($list);
        }

        exit;
    }



    // ======================================  辅助 =================================================


    /**
     + -----------------------------------------------------------------------
     *  仓库库存Ajax获取
     + -----------------------------------------------------------------------
     */
    public function getsum()
    {
        if($_GET['group'] && $_GET['goods'])
        {
            $DB_Stock = D('Stock');

            $condition = "`audit` != '0' AND `group` = '{$_GET['group']}' AND `goods` = '{$_GET['goods']}'";
            $sum = $DB_Stock -> sum($condition, 'num');
            $this -> ajaxReturn($sum, '', 1);
        }else{
            $this -> success('参数不正确！', 3, 'Index/index');
        }
    }
    //over



    //仓库获取
    public function group($gid = 0)
    {
        //获取当前用户所在组
        $uid = Session::get('uid');
        $Member = D('group_member');
        $user_groups = $Member -> findAll("`uid`='{$uid}'");
        $_usergroups = array();
        foreach($user_groups as $value)
        {
            $_usergroups[] = $value['gid'];
        }
        if(!$_usergroups) $this -> success('您没有被设置于任何仓库管理内，请联系管理员！', 2, 'Index/index');
        if(!$gid) $gid = $_usergroups[0];

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
        $_group = dataTree($level0, $other, 'parentid', true);
        $group = array();
        foreach ($_group as $key => $value) 
        {
            $group[$value['gid']] = $value;
        }
        $this -> assign('group', $group);

        //gid
        $this -> assign('gid', $gid);

        //all group parent
        $parent = array();
        foreach($group as $v)
        {
            $parent[$v['level']] = $v['gid'];
            if($v['gid'] == $gid)
            {
                break;
            }
        }

        if(array_intersect($_usergroups, $parent))
        {
            $this -> assign('can_manage', 1);
        }else{
            $this -> assign('can_manage', 0);
        }

        //all group children
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
        return $group_child;
    }
    //over


    //库存记录列表 内部用
    public function getlist($condition = '', $page=false)
    {
        $DB_Stock = D('Stock',true);
        $DB_Stock -> link('user', 'HAS_ONE', 'uid', 'user', 'uid', 'uid,name');
        $DB_Stock -> link('good', 'HAS_ONE', 'goods', 'Goods', 'id', '*');
        $DB_Stock -> link('groupname', 'HAS_ONE', 'group', 'Group', 'gid', 'name');

        if($page)
        {
            import('Extend.Util.Page');
            $Page = new Page($DB_Stock->count($condition), 15);
            $limit =  $Page->firstRow.','.$Page->listRows;
        }else{
            $limit = '';
        }

        $list = $DB_Stock -> xfindAll($condition, 'id,goods,goods_name,group,num,price,time,uid,rtime,audit,money,comment', 'id desc', $limit);

        if($page)
        {
            return array('list' => $list, 'page' => $Page -> show());
        }else{
            return $list;
        }
    }
    //over

     //获取商品剩余信息
    public function remain()
    {
        $goods = $_POST['goods'];
        $group = $_POST['group'];

        $condition = "`audit`>0 AND `goods`={$goods}". ((int)$group > 0 ? " AND `group`=". $group : '');
        //剩余总数
        $data = D("Stock") -> sum($condition, "num");

        $this->ajaxReturn($data,"操作成功！",1);

    }

  
}
?>