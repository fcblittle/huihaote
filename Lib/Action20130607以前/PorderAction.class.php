<?php
/**
 * 采购入库单据管理Action
 *
 * @author Page7
 * @category Police OA 2012 pack1
 * @copyright Copyright(c) 2011 Interidea.org
 * @version $Id$
 */

include_once(dirname(__FILE__).'/OrderAction.class.php');

class PorderAction extends OrderAction
{
    protected $mname = 'Porder';

    protected $type = 2;

    protected $title = '采购入库单';

    protected $abs = true;

    protected $price = true;

    protected $pinyin = 'cgrk';


    //采购统计
    public function statistics()
    {
        $condition = array();
        if(isset($_GET['search']) && (int)$_GET['search'] > 0)
            $condition[] = "`sale`='{$_GET['search']}'";

        if(isset($_GET['tax']) && (float)$_GET['tax'] > -1)
            $condition[] = "`tax`='{$_GET['tax']}'";

        if(isset($_GET['cors']) && $_GET['cors'])
            $condition[] = "`cors` = '".(int)$_GET['cors']."'";

        if (trim($_GET['start']) && trim($_GET['end']))
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
            $data['list'] = $DB_Order -> xfindAll($condition, 'id,num,cors,type,uid,time,audit,over,sale,total, tax_total,income', 'time Desc');
        }else{
            $data = $this -> getlist($condition);
        }
        $this -> assign($data);

        //供应商.客户
        $DB_Cors = D('Supplier');
        $cors = $DB_Cors -> findAll();
        $this -> assign('cors', $cors);
		//供商厂类型。客户类型
        $DB_type = D('Supplier_type');
		$this ->assign('types',1);
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
        $this -> assign('tax', isset($_GET['tax']) ? $_GET['tax'] : '');
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

        if(isset($_GET['ac']) && $_GET['ac']=='打 印'){
		    $this -> display("printStatistics");
        }elseif(isset($_GET['ac']) && $_GET['ac'] == '导 出'){
            foreach($data['list'] as $key=>$val)
            {
                $list[$key]['num'] = $val['num'];
                $list[$key]['corsname'] = $val['cors']['name'];
                $list[$key]['salename'] = $allUsers[$val['sale']]['name'];
                $list[$key]['totalPrice'] = showPrice($val['total']+$val['tax_total']);
                $list[$key]['tax'] = $val['tax'].'%';
                $list[$key]['income'] = showPrice($val['income']);
                $list[$key]['over'] = $val['over'] ? '完结' : '未结';
            }

            $titles = array('A'=>'单 号', 'B'=>'客 户', 'C'=>'销售人员', 'D'=>'总 价', 'E'=>'税 率', 'F'=>'提成', 'G'=>'状态');
            $fields = array('A'=>'num', 'B'=>'corsname', 'C'=>'salename', 'D'=>'totalPrice', 'E'=>'tax', 'F'=>'income', 'G'=>'over');
            $this -> sqlToExcel($list, $fields, $titles);
            exit;
        }else
		    $this -> display("statistics");
    }

    /**
     * 导出表格
     */
    protected function sqlToExcel($data, $fields, $titles)
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

        $width = array('A'=>30, 'B'=>20);

        foreach ($titles as $key=>$val)
        {
            $_width = isset($width[$key]) ? $width[$key] : 20;
            $objPHPExcel->getActiveSheet()->getColumnDimension($key)->setWidth($_width);
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

        $this -> assign('tname', "采购退/货");
        $this -> display('swap');
    }


    public function addSwap()
    {
        if($_POST)
        {
            $order = D("Order") -> find("`num`='{$this->pinyin}-{$_POST['num']}' AND `type`='{$this -> type}'");
            if(!$order)
                $this -> error("该订单已删除");

            /**
            //是否存在
            $haveReturn = D("Return") -> find("`num`='{$this->pinyin}-{$_POST['num']}' AND `sort`='{$_POST['sort']}'");
            if($haveReturn)
                $this -> error("该单退货已存在");
            */

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

            $data = array(
                'sort'      => $_POST['sort'],
                'num'       => $order['num'],
                'type'      => $order['type'],
                'goods'     => serialize($goods),
                'comment'   => $_POST['comment'],
                'time'      => $_POST['time'] ? strtotime($_POST['time']) : time(),
                'rtime'     => time(),
                'uid'       => Session::get('uid'),
                'cors'      => $order['cors'],
                //'way'       => $_POST['way'],
                //'bank'      => $_POST['way'] ? $_POST['bank'] : 0,
                'price'     => $total,
            );

            $result = D("Return") -> add($data);
            if(false !== $result)
            {
                $data['id'] = $result;
                $data['user']['name'] = Session::get('name');
                $type = $data['sort'] == '退货' ? 'back' : 'change';
                $this -> mail($type, $data);

                if(false !== $result)
                    $this -> success("操作成功！", 1, $this -> mname. '/swap');
                else
                    $this -> error("仓库出入记录失败！");
            }else{
                $this -> error("退/换货添加失败！");
            }
        }

        //$this -> assign("banks", D("Bank") -> findAll());
        A("Stock") -> group();
        $this -> assign('tname', "采购退/换货");
        $this -> display('addswap');
    }


    public function editSwap()
    {
        if($_POST)
        {
            $order = D("Order") -> find("`num`='{$this->pinyin}-{$_POST['num']}' AND `type`='{$this -> type}'");
            if(!$order)
                $this -> error("该订单已删除");

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
                'time'      => $_POST['time'] ? strtotime($_POST['time']) : time(),
                //'way'       => $_POST['way'],
                //'bank'      => $_POST['way'] ? $_POST['bank'] : 0,
                'price'     => $total,
            );

            $result = D("Return") -> save($data);
            if(false !== $result)
            {
                $data['user'] = D("User") -> where("`uid`={$return['uid']}") -> find();
                $type = $data['sort'] == '退货' ? 'back' : 'change';
                $this -> mail($type, $data);

                if(false !== $result)
                    $this -> success("操作成功！", 1, $this -> mname. '/swap');
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
        $return['cors'] = D("Supplier") -> find("`id`='{$order['cors']}'");
        $this -> assign($return);

        A("Stock") -> group();

        //$this -> assign("banks", D("Bank") -> findAll());
        $this -> assign('tname', "采购退/换货");
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

            $this -> success('操作成功！', 1, $this -> mname. '/swap');
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

            $_return_goods = unserialize($return['goods']);

            /*兼容以前的写法*/
            $return_goods = array();
            foreach ($_return_goods as $key=>$val)
            {
                $return_goods[$val['goods']] = $val;
            }

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
                                'num'       => -$num,
                                'price'     => $return_goods[$v['goods']]['price'] * $num * 1,
                                'time'      => NOW,
                                'uid'       => $return['uid'],
                                'rtime'     => NOW,
                                'comment'   => str_replace(array('单', '入'), array('', '退货出'), $this -> title) . ' ('.$return['num'].')',
                                'order'     => $return['id'],
                                'audit'     => 0,
                                'come'      => 'return',
                            );

                            //检查库存
                            $condition = "`audit` != '0' AND `group` = '{$key}' AND `goods` = '{$v['goods']}'";
                            $sum = $DB_Stock -> sum($condition, 'num');
                            if(!$this -> abs && $sum < $num)
                                $this -> success('库存不足，无法分配！', 3);
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
                $corstable = array('', 'supplier', 'supplier', 'client');
                $data = array(
                    'name'     => str_replace('单', '', $this -> title) . ' ('.$return['num'].')'.($table == 'Return' ? '(退货)' : ''),
                    'price'    => abs($_POST['price']),
                    'time'     => NOW,
                    'arrive'   => 0,
                    //'notto'    => $_POST['price'],
                    'uid'      => $return['uid'],
                    'rtime'    => NOW,
                    'order'    => $return['id'],
                    //'income'   => $_POST['income']*100,
                    'audit'    => 0,
                    'come'     => 'return',
                    'cors'     => $order['cors'],
                    'corstable'=> $corstable[$return['type']],
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

            $result = D('Return') -> save(array('audit'=>Session::get('uid'), "`id`='{$return['id']}'"));
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

        $this -> assign('tname', "采购退/货");
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
        $return['client'] = D("Supplier") -> find("`id`='{$order['cors']}'");
        $this -> assign($return);

        A("Stock") -> group();
        //$this -> assign('bank', D("Bank") -> where("`id`='{$return['bank']}'") -> find());


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

        $this -> assign('tname', "采购退/换货");
        $this -> display();
    }


    public function printSwap()
    {
        //获取数据
        if(!$id) $id = (int)$_GET['id'];

        //订单信息
        $DB_Order = D('Return', true);
        $DB_Order -> link("supply", "HAS_ONE", 'cors', 'Supplier', 'id', '*');
        $DB_Order -> link("user", "HAS_ONE", 'uid', 'User', 'uid', '*');
        $DB_Order -> link("aud", "HAS_ONE", 'audit', 'User', 'uid', '*');
		$order = $DB_Order -> xfind("`id`='{$id}'", '*');


        //订单包含产品
        $_goods = unserialize($order['goods']);
        $order['goods'] = $_goods;

        $order['cMoney'] = getRMB(abs($order['price'])/100);
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
        $DB -> link('corsInfo', 'HAS_ONE', 'cors', 'Supplier', 'id', '*');
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
        $DB_Order -> link("supply", "HAS_ONE", 'cors', 'Supplier', 'id', '*');
        $DB_Order -> link("user", "HAS_ONE", 'uid', 'User', 'uid', '*');
        $DB_Order -> link("aud", "HAS_ONE", 'audit', 'User', 'uid', '*');
		$order = $DB_Order -> xfind("`id`='{$id}'");
        //订单包含产品
        $_goods = unserialize($order['goods']);
        $order['goods'] = $_goods;
        $order['total'] = abs($order['total']);
        $order['tax_total'] = abs($order['tax_total']);

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