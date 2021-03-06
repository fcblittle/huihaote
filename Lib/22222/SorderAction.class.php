<?php
/**
 * 销售出库单据管理Action
 *
 * @author Page7
 * @category Police OA 2012 pack1
 * @copyright Copyright(c) 2011 Interidea.org
 * @version $Id$
 */

include_once(dirname(__FILE__).'/OrderAction.class.php');

class SorderAction extends OrderAction
{
    protected $mname = 'Sorder';

    protected $type = 3;

    protected $title = '销售出库单';

    protected $abs = false;

    protected $price = true;

    protected $pinyin = 'xsck';


    public function statistics()
    {
        $condition = array();
        if(isset($_GET['search']) && (int)$_GET['search'] > 0)
            $condition[] = "`sale`='{$_GET['search']}'";

        if(isset($_GET['cors']) && $_GET['cors'])
            $condition[] = "`cors` = '".(int)$_GET['cors']."'";

        if ( trim($_GET['start']) &&  trim($_GET['end']))
            $condition[] = "`time`>=".strtotime($_GET['start'])." AND `time`<=".strtotime($_GET['end']. ' 23:59:59');

        $condition[] = "`audit` > '0'";
        $condition = implode(' AND ', $condition);

        if(isset($_GET['ac']) && $_GET['ac']=='打 印')
        {
            if($condition) $condition .= " AND `type` = '{$this->type}'";
            else $condition = "`type` = '{$this->type}'";

            $data['total'] = D("order") -> sum($condition, 'total');
            $data['income'] = D("order") -> sum($condition, 'income');
            $data['taxTotal'] = D("order") -> sum($condition, 'tax_total');

            $DB_Order = D('Order', true);
            $DB_Order -> link('user', 'HAS_ONE', 'uid', 'user', 'uid', 'uid,name');
            $DB_Order -> link('cors', 'HAS_ONE', 'cors', $this->abs ? 'Supplier' : 'Client', 'id', 'id,name');
            $data['list'] = $DB_Order -> xfindAll($condition, 'id,num,cors,type,uid,time,audit,over,sale,total,income', 'time Desc');
        }else{
            $data = $this -> getlist($condition);
        }
        $this -> assign($data);

        //供应商.客户
        $DB_Cors = D('Client');
        $cors = $DB_Cors -> findAll();
        $this -> assign('cors', $cors);
        $DB_type = D('Client_type');
		$this ->assign('types',2);
        $type = $DB_type -> findAll();

		//提取0级部门
		$level0 = array();
		$other = array();
		foreach ($type as $key => $value)
		{
			if($value['parentid']==0)
			{
				$value['level'] = 0;
				$level0[$value['id']] = $value;
			}else{
				$other[$value['id']] = $value;
			}
		}
		ksort($level0);
		$type = dataTree($level0, $other, 'parentid', true);
		$this -> assign('DB_type', $type);

		$this -> assign('search', isset($_GET['search']) ? $_GET['search'] : '');
		$this -> assign('_cors', isset($_GET['cors']) ? $_GET['cors'] : '');
		$this -> assign('start', isset($_GET['start']) ? $_GET['start'] : '');
		$this -> assign('end', isset($_GET['end']) ? $_GET['end'] : '');

		$_allUsers = D('user') -> field('uid, name') -> findAll();
		$allUsers = array();
		foreach ($_allUsers as $key=>$val)
		{
            $allUsers[$val['uid']] = $val;
		}
		$this -> assign('allUsers', $allUsers);

		if(isset($_GET['ac']) && $_GET['ac']=='打 印')
		    $this -> display("printStatistics");
		else
		    $this -> display("statistics");
    }



    /**
     * 退换货处理
     *
     */
    public function swap()
    {
        if($_GET['do'])
        {
            $this -> $_GET['do']();
            exit;
        }

        $DB_Return = D('Return', true);
        $DB_Return -> link('user', 'HAS_ONE', 'uid', 'user', 'uid', 'uid,name');

        import('Extend.Util.Page');
        $Page = new Page($DB_Return->count("`type`='{$this->type}'"), 15);
        $limit =  $Page->firstRow.','.$Page->listRows;
        $list = $DB_Return -> xfindAll("`type`='{$this->type}'", '*', 'time Desc', $limit);
        $this -> assign('list', $list);
        $this -> assign('page', $Page->show());

        $this -> assign('tname', "销售退/货");
        $this -> display('swap');
    }


    public function addSwap()
    {
        if($_POST)
        {
            $order = D("Order") -> find("`num`='xsck-{$_POST['num']}' AND `type`='{$this -> type}'");
            if(!$order)
                $this -> error("该订单已删除");

            //是否存在
            /**
            $haveReturn = D("Return") -> find("`num`='{$this->pinyin}-{$_POST['num']}' AND `sort`='{$_POST['sort']}'");
            if($haveReturn)
                $this -> error("该单退货已存在");
            */

            $time = $_POST['time'] ? strtotime($_POST['time']) : time();

            $goods = $stocks = array();
            $total = 0;
            foreach ($_POST['goods'] as $key => $val)
            {
                if((int)$_POST['good_num'][$key]<1)
                    continue;

                $goods[$val] = array(
                    'goods'     => $val,
                    'num'       => $_POST['good_num'][$key],
                    'com'       => $_POST['good_com'][$key],
                    'price'     => $_POST['good_price'][$key],
                    'name'      => $_POST['good_name'][$key],
                    'total'     => $_POST['good_num'][$key] * $_POST['good_price'][$key],
                );

                $total +=  $goods[$val]['total'];
            }

            $data = array(
                'sort'      => $_POST['sort'],
                'num'       => $order['num'],
                'type'      => $order['type'],
                'goods'     => serialize($goods),
                'comment'   => $_POST['comment'],
                'time'      => $time,
                'rtime'     => time(),
                'uid'       => Session::get('uid'),
                //'way'       => $_POST['way'],
                //'bank'      => $_POST['way'] ? $_POST['bank'] : 0,
                'price'     => $total,
                'cors'      => $order['cors'],
            );

            $result = D("Return") -> add($data);
            if(false !== $result)
            {
                $data['id'] = $result;
                $data['user']['name'] = Session::get('name');
                $type = $data['sort'] == '退货' ? 'back' : 'change';
                $this -> mail($type, $data);

                if(false !== $result)
                    $this -> success("操作成功！", 1, 'Sorder/swap');
                else
                    $this -> error("仓库出入记录失败！");
            }else{
                $this -> error("退/换货添加失败！");
            }
        }

        //$this -> assign("banks", D("Bank") -> findAll());
        A("Stock") -> group();
        $this -> assign('tname', "销售退货");
        $this -> display('addswap');
    }


    public function editSwap()
    {
        if($_POST)
        {
            $order = D("Order") -> find("`num`='xsck-{$_POST['num']}' AND `type`='{$this -> type}'");
            if(!$order)
                $this -> error("该订单已删除");

            $time = $_POST['time'] ? strtotime($_POST['time']) : time();

            $goods = array();
            $total = 0;
            foreach ($_POST['goods'] as $key => $val)
            {
                if((int)$_POST['good_num'][$key]<1)
                    continue;

                $goods[$val] = array(
                    'goods'     => $val,
                    'num'       => $_POST['good_num'][$key],
                    'com'       => $_POST['good_com'][$key],
                    'price'     => $_POST['good_price'][$key],
                    'name'      => $_POST['good_name'][$key],
                    'total'     => $_POST['good_num'][$key] * $_POST['good_price'][$key],
                );


                $total += $_POST['good_num'][$key] * $_POST['good_price'][$key];
            }

            $return = D("Return") -> where("`id`='{$_POST['id']}'") -> find();
            if(!$return)
                $this -> error('该退/换货已被删除！');
            if($return['uid'] !== session::get('uid'))
                $this -> error("您无权修改该信息！");

            $data = array(
                'id'        => $_POST['id'],
                'sort'      => $_POST['sort'],
                'num'       => $order['num'],
                'type'      => $order['type'],
                'goods'     => serialize($goods),
                'comment'   => $_POST['comment'],
                'time'      => $time,
                'rtime'     => time(),
                //'way'       => $_POST['way'],
                //'bank'      => $_POST['way'] ? $_POST['bank'] : 0,
                'price'     => $total,
                'cors'      => $order['cors'],
            );

            $result = D("Return") -> save($data);
            if(false !== $result)
            {
                $data['user'] = D("User") -> where("`uid`=".$return['uid']) -> find();
                $type = $data['sort'] == '退货' ? 'back' : 'change';
                $this -> mail($type, $data);

                if(false !== $result)
                    $this -> success("操作成功！", 1, 'Sorder/swap');
                else
                    $this -> error("仓库出入记录失败！");
            }else{
                $this -> error("退/换货添加失败！");
            }
        }

        if((int)$_GET['id'] < 1)
            $this -> error("选择需要修改的数据");

        $return = D("Return") -> find("`id`='{$_GET['id']}'");

        $return['goods'] = json_encode(unserialize($return['goods']));
        $return['time'] = date("Y-m-d H:i", $return['time']);

        $order = D("Order") -> find("`num`='{$return['num']}' AND `type`='{$this->type}'");
        $return['cors'] = D("Client") -> find("`id`='{$order['cors']}'");
        $this -> assign($return);

        A("Stock") -> group();

        //$this -> assign("banks", D("Bank") -> findAll());

        $this -> assign('tname', "销售退货");
        $this -> display('editswap');
    }


    public function delSwap()
    {
        if((int)$_GET['id'] < 1)
            $this -> error();

        $return = D("Return") -> find("`id`=".(int)$_GET['id']);

        if(false !== D("Return") -> delete("`id`=".(int)$_GET['id']))
        {
            //删除原先库存出入记录
            $comment = "销售退货入库({$return['num']})";
            D('Stock') -> delete("`comment`='{$comment}'");

            $this -> success('操作成功！', 1, 'Sorder/swap');
        }else{
            $this -> error("删除失败！");
        }
    }


    public function auditSwap()
    {
        if($_POST)
        {
            $table = 'Return';
            //原始数据
            $id = (int)$_POST['id'];
            if(!$id)
                $this -> success("数据访问错误，请重试。", 3);

            $DB = D($table, true);
            $DB -> link('user', 'HAS_ONE', 'uid', 'user', 'uid', 'uid,name');
            $return = $DB -> xfind("`id` = '{$id}'");

            /*兼容以前的写法*/
            $_return_goods = unserialize($return['goods']);
            $return_goods = array();
            foreach ($_return_goods as $key=>$val)
            {
                $return_goods[$val['goods']] = $val;
            }

            var_dump($return_goods);
            //全部产品
            $goods = D('Goods') -> findAll();
            $allgoods = array();
            foreach($goods as $v)
                $allgoods[$v['id']] = $v;

            //解析
            $group = array();
            foreach($_POST['group'] as $key => $value)
            {
                //$key 是 产品id
                foreach($value as $k => $v)
                {
                    //$v 是 仓库id
                    if(!(float)$_POST['num'][$key][$k]) continue;
                    if(!isset($group[$v])) $group[$v] = array();
                    $num = isset($group[$v][$key]) ? ((float)$group[$v][$key]['num'] + (float)$_POST['num'][$key][$k]) : (float)$_POST['num'][$key][$k];
                    $group[$v][$key] = array('goods'=>$key, 'num'=>$num);
                }
            }
            //if(!$group) $this -> success('操作失败，请重试！',3);

            //生成数据
            $DB_Stock = D('Stock');
            if($return['sort']=='退货')
            {
                $data = array();
                foreach($group as $key => $value)
                {
                    //$key 是仓库id
                    foreach($value as $v)
                    {
                        $num = abs($v['num']);
                        if($num)
                        {
                            $data[] = array(
                                'goods'     => $v['goods'],
                                'goods_name' => $allgoods[$v['goods']]['name'],
                                'group'     => $key,
                                'num'       => $num,
                                'price'     => $return_goods[$v['goods']]['price'] * $num * -1,
                                'time'      => NOW,
                                'uid'       => $return['uid'],
                                'rtime'     => NOW,
                                'comment'   => str_replace(array('单', '出'), array('', '退货入'), $this -> title) . ' ('.$return['num'].')',
                                'order'     => $return['id'],
                                'audit'     => 0,
                                'come'      => 'return',
                            );

                            //检查库存
                            /**
                            $condition = "`audit` != '0' AND `group` = '{$key}' AND `goods` = '{$v['goods']}'";
                            $sum = $DB_Stock -> sum($condition, 'num');
                            if(!$this -> abs && $sum < $num)
                                $this -> success('库存不足，无法分配！', 3);
                            */
                        }
                    }
                }

                //计入库存审核
                $DB_Stock = D('stock');
                if(!$DB_Stock -> addAll($data))
                {
                    $this -> success('操作失败，请重试！',3);
                }

                //需要记账
                $_POST['price'] = abs(inputPrice($_POST['price']));
                $error = '';

                if($_POST['price']) //生产入库，不记账
                {
                    $data = array(
                        'name'  => str_replace('单', '', $this -> title) . ' ('.$return['num'].')'.($table == 'Return' ? '(退货)' : ''),
                        'price'    => -abs($_POST['price']),
                        'time'     => NOW,
                        'arrive'   => 0,
                        //'notto'    => $_POST['price'],
                        'uid'      => $return['uid'],
                        'rtime'    => NOW,
                        'order'    => $return['id'],
                        //'income'   => $_POST['income']*100,
                        'audit'    => 0,
                        'come'     => 'return',
                        //'way'      => $return['way'],
                        //'bank'     => $return['bank'],
                    );
                    $DB_Financial = D('Financial');
                    if(!$DB_Financial -> add($data))
                    {
                        $DB_Stock -> deleteAll("`order` = '{$order['id']}' AND `come`='return'");
                        $$this -> success('财务收支提交失败，请重试！', 3, $this->mname.'/audit');
                    }
                }
            }


            $result = D('Return') -> save(array('audit'=>Session::get('uid')), "`id`='{$return['id']}'");
            if(false !== $result)
            {
                $order['audit'] = 1;
                //$this -> mail('feedback', $return);
                $this -> success('审核成功！', 3, $this->mname.'/auditSwap');
            }else{
                $DB_Stock -> deleteAll("`order` = '{$return['id']}' AND `come`='return'");
                $DB_Financial -> deleteAll("`order` = '{$return['id']}' AND `come`='return'");
                $this -> success('审核结果提交失败，请重试！',3);
            }
        }


        $DB_Return = D('Return', true);
        $DB_Return -> link('user', 'HAS_ONE', 'uid', 'user', 'uid', 'uid,name');

        import('Extend.Util.Page');
        $Page = new Page($DB_Return->count("`type`='{$this->type}' AND `audit`=0"), 15);
        $limit =  $Page->firstRow.','.$Page->listRows;
        $list = $DB_Return -> xfindAll("`type`='{$this->type}' AND `audit`=0", '*', 'time Desc', $limit);
        $this -> assign('list', $list);
        $this -> assign('page', $Page->show());

        $this -> assign('tname', "销售退/货");
        $this -> display();
    }


    public function viewSwap()
    {

        if((int)$_GET['id'] < 1)
            $this -> error("选择需要修改的数据");

        $return = D("Return") -> find("`id`='{$_GET['id']}'");

        $return['goods'] = unserialize($return['goods']);
        $return['time'] = date("Y-m-d H:i", $return['time']);

        $order = D("Order") -> find("`num`='{$return['num']}' AND `type`='{$this->type}'");
        $return['cors'] = D("Client") -> find("`id`='{$order['cors']}'");
        $this -> assign($return);

        //$this -> assign('bank', $return['way'] ? D('bank')->where("`id`='{$return['bank']}'")->find() : array());
        A("Stock") -> group();

        if($return['audit'] > 0)
        {
            //仓库库存
            $DB_Stock = D('stock', true);
            $DB_Stock -> link('group', 'HAS_ONE', 'group', 'group', 'gid');
            $stock = $DB_Stock -> xfindAll("`order`='{$return['id']}' AND `come`='return'");
            foreach($return['goods'] as $key => $value)
            {
                foreach($stock as $k => $v)
                {
                    if($value['goods'] == $v['goods'])
                    {
                        $return['goods'][$key]['group'][] = $v;
                    }
                }
            }
            $this -> assign('goods' ,$return['goods']);
            $this -> assign('needprice', $return['sort'] == '退货' ? true : false);
            if($return['sort'] == '退货')
            {
                //财务入账
                $DB_Financial = D('Financial');
                $financial = $DB_Financial -> find("`order`='{$return['id']}' AND `come`='return'");
                $this -> assign('money', $financial);
            }
        }

        //所有货品
        $_allGoods = D("Goods") -> findAll();
        $allGoods = array();
        foreach ($_allGoods as $key=>$val)
        {
            $allGoods[$val['id']] = $val;
        }
        $this -> assign('allgoods', $allGoods);

        $this -> assign('tname', "销售退货");
        $this -> display();
    }


    public function printSwap()
    {
        //获取数据
        if(!$id) $id = (int)$_GET['id'];

        //订单信息
        $DB_Order = D('Return', true);
        $DB_Order -> link("client", "HAS_ONE", 'cors', 'Client', 'id', '*');
        $DB_Order -> link("user", "HAS_ONE", 'uid', 'User', 'uid', '*');
        $DB_Order -> link("aud", "HAS_ONE", 'audit', 'User', 'uid', '*');
		$order = $DB_Order -> xfind("`id`='{$id}'", '*');


        //订单包含产品
        $_goods = unserialize($order['goods']);
        $order['goods'] = $_goods;
        $order['price'] = abs($order['price']);

        $order['cMoney'] = getRMB($order['price']/100);
        $this -> assign($order);


        $goodsId = array();
        foreach ($_goods as $key => $val)
        {
            $goodsId[] = $val['goods'];
        }

        //产品信息
        $_products = D("Goods") -> findAll("`id` IN (".implode(', ', $goodsId).")");
        $products = array();
        foreach($_products as $k=>$v)
        {
            $products[$v['id']] = $v;
        }
        $this -> assign('p', $products);

        $_allUsers = D('user') -> field('uid, name') -> findAll();
		$allUsers = array();
		foreach ($_allUsers as $key=>$val)
		{
            $allUsers[$val['uid']] = $val;
		}
		$this -> assign('allUsers', $allUsers);

        $this -> display();
    }




    public function getOrders()
    {
        if(!trim($_POST['num']))
            $this -> error();


        $DB = D("Order");
        $orders = $DB -> findAll("`num` LIKE '{$_POST['num']}%' AND `type`='{$this -> type}'");
        $orders = $orders ? $orders : array();
        echo json_encode($orders);
    }


    public function getProducts()
    {
        if(!trim($_POST['num']))
            $this -> error();

        $DB = D("Order", true);
        $DB -> link('corsInfo', 'HAS_ONE', 'cors', 'Client', 'id', '*');
        $order = $DB -> xfind("`num`='{$_POST['num']}' AND `type`='{$this -> type}'");
        $order['time'] = date("Y-m-d H:i", $order['time']);

        $products = $order['goods'] = unserialize($order['goods']);
        foreach ($products as $key=>$val)
        {
            $order['goods'][$key]['info'] = D("Goods") -> find("`id`='{$val['goods']}'");
        }

        echo json_encode($order);
    }


    public function printOrder()
    {
        //获取数据
        if(!$id) $id = (int)$_GET['id'];

        //订单信息
        $DB_Order = D('Order', true);
        $DB_Order -> link("client", "HAS_ONE", 'cors', 'Client', 'id', '*');
        $DB_Order -> link("user", "HAS_ONE", 'uid', 'User', 'uid', '*');
        $DB_Order -> link("aud", "HAS_ONE", 'audit', 'User', 'uid', '*');
		$order = $DB_Order -> xfind("`id`='{$id}'", '*');


        //订单包含产品
        $_goods = unserialize($order['goods']);
        $order['goods'] = $_goods;
        $order['total'] = abs($order['total']);

        $order['cMoney'] = getRMB(($order['total']+$order['tax_total'])/100);
        $this -> assign($order);


        $goodsId = array();
        foreach ($_goods as $key => $val)
        {
            $goodsId[] = $val['goods'];
        }

        //产品信息
        $_products = D("Goods") -> findAll("`id` IN (".implode(', ', $goodsId).")");
        $products = array();
        foreach($_products as $k=>$v)
        {
            $products[$v['id']] = $v;
        }
        $this -> assign('p', $products);

        $_allUsers = D('user') -> field('uid, name') -> findAll();
		$allUsers = array();
		foreach ($_allUsers as $key=>$val)
		{
            $allUsers[$val['uid']] = $val;
		}
		$this -> assign('allUsers', $allUsers);

        $this -> display('print');
    }

}
?>