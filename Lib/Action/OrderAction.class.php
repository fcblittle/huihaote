<?php
/**
 * 单据管理Action
 *
 * @author Page7
 * @category Police OA 2012 pack1
 * @copyright Copyright(c) 2011 Interidea.org
 * @version $Id$
 */


class OrderAction extends Action
{
    protected $mname = '';

    //单据类型
    protected $type = 0;

    //标题
    protected $title = '';

    //数字正负，入库为正
    protected $abs = false;

    //是否需要计价
    protected $price = false;



    public function _initialize()
    {
        $exception = array('view','edit','delete','replay');
        include_once(LIB_PATH.'_initialize.php');
        $this -> assign('needprice', $this -> price);
        $this -> assign('mname', $this -> mname);
        $this -> assign('tname', $this -> title);
        $this -> assign('abs', $this -> abs);
    }



    /**
     + -----------------------------------------------------------------------
     *  单据全部列表
     + -----------------------------------------------------------------------
     */
    public function index()
    {
        $condition = array();
        if(isset($_GET['search']))
        {

            if(isset($_GET['num']) && $_GET['num'])
                    $condition[] = "`num` LIKE '%".$_GET['num']."%'";
            if(isset($_GET['cors']) && $_GET['cors'])
                    $condition[] = "`cors` = '".(int)$_GET['cors']."'";
            $this -> assign('search', 1);
        }
        $condition[] = "`audit` > '0'";

        $condition = implode(' AND ', $condition);

        $data = $this -> getlist($condition);

        //供应商.客户
        if($this -> abs)
        {
            $DB_Cors = D('Supplier');
        }else{
            $DB_Cors = D('Client');
        }
        $cors = $DB_Cors -> findAll();
        $this -> assign('cors', $cors);

        $this -> assign($data);
		//供商厂类型。客户类型
		 if($this -> abs)
            {
                $DB_type = D('Supplier_type');
				$this ->assign('types',1);
            }else{
                $DB_type = D('Client_type');
				$this ->assign('types',2);
            }
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

		$_allUsers = D('user') -> field('uid, name') -> findAll();
		$allUsers = array();
		foreach ($_allUsers as $key=>$val)
		{
            $allUsers[$val['uid']] = $val;
		}
		$this -> assign('allUsers', $allUsers);

		$this -> assign('p', $_GET['p'] ? (int)$_GET['p'] : 1);

        $this -> display("Order:index");
    }
    //over


    //产品的购买价格
    public function productCost($id, $num, $group=0)
    {
        $condition = "`audit`>0 AND `goods`={$id}". ((int)$group > 0 ? " AND `group`=". $group : '');
        //购买总数
        $buyTotal = D("Stock") -> sum($condition ." AND `num`>0", "num");
        //销售总数
        $saleTotal = D("Stock") -> sum($condition ." AND `num`<0", "num");
        //库存
        $stockTotal = $buyTotal + $saleTotal;

        if($num > $stockTotal)
        {
            $product = D("Goods") -> where("`id`={$id}") -> find();
            $_group = D("Group") -> where("`gid`={$group}") -> find();
            $this -> success("仓库：{$_group['name']} 中的 {$product['name']}({$product['num']}) 库存不足{$num}！", 3);
        }

        //库存产品的记录  
        $newBuy = array('total'=>0, 'stock'=>array());
        $info = array();

        while($newBuy['total'] < $stockTotal)
        {
            $_condition = $condition ." AND `num`>0". ($newBuy['stock'] ? " AND `id` NOT IN (". implode(',', array_keys($newBuy['stock'])) .")" : '');
            $_newBuy = D("Stock") -> field("id, goods, goods_name, group, num, price ,rtime") -> where($_condition) -> order("time desc") -> find();
            if(!$_newBuy)
                break;

            //$_stockInfo = D("Stock_info") -> where("`stock` = '{$_newBuy['id']}'") -> sum("`num`");
            $stockInfo = D("Stock_info") -> sum("`stock` = '{$_newBuy['id']}'", 'num');
            /*
            if(($_newBuy['num'] + $_stockInfo['num']) <= 0)
                break;
            */
            //把总价转为单价

            $_newBuy['price'] = $_newBuy['price']/$_newBuy['num'];

            $_newBuy['num'] = ($_newBuy['num'] - $stockInfo);

            $newBuy['total'] += $_newBuy['num'];
            //$newBuy['total'] += ($_newBuy['num'] + $_stockInfo['num']);
            $newBuy['stock'][$_newBuy['id']] = $_newBuy;
            /*
            $info[] = array(
                        'stock'      => $_newBuy['id'], 
                        'goods'      => $_newBuy['goods'],
                        'goods_name' => $_newBuy['goods_name'],
                        'group'      => $_newBuy['group'], 
                        'num'        => ($_newBuy['num'] + $_stockInfo['num']),
                        'price'      => $_newBuy['price'],
                    );
            */
        }

        $stocks = array_reverse($newBuy['stock']);

        //过滤价格  从最后一次出库 开始
        $return = array('total'=>0, 'price'=>0, 'stock'=>array());
        
        //$stocks[0]['num'] -= $newBuy['total']-$stockTotal;

        foreach($stocks as $stock)
        {
            if($stock['num'] == 0)
                continue;
            if($return['total'] >= $num)
                break;

            //如果总数超出需要的数量
            if($return['total'] + $stock['num'] > $num)
                $stock['num'] = $num - $return['total'];

            $return['total'] += $stock['num'];
            $return['price'] += $stock['num'] * abs($stock['price']);
            $return['stock'][$stock['id']] = $stock;

            $return['info'][] = array(
                        'stock'      => $stock['id'], 
                        'goods'      => $stock['goods'],
                        'goods_name' => $stock['goods_name'],
                        'group'      => $stock['group'], 
                        'num'        => $stock['num'],
                        'price'      => $stock['price'],
                    );
        }

        return $return;
    }


    /**
     + -----------------------------------------------------------------------
     *  单据添加
     + -----------------------------------------------------------------------
     */
    public function add()
    {
        //生产出库
        if($_POST)
        {
            if(!$_POST['num'])// || !$_POST['cors'] || !$_POST['goods'])
                $this -> success("单号不能为空！", 2);

            $tax = false;
            $totalPrice = $taxTotal = $costTotal = 0;
            $goods = $haveGoods = array();
            foreach($_POST['goods'] as $key => $value)
            {
                if(isset($haveGoods[$value]))
                    $haveGoods[$value] += 1;
                else
                    $haveGoods[$value] = 1;

                $_key = $value.'_'.$haveGoods[$value];

                if($value && (float)$_POST['goods_num'][$key])
                {
                    $num = abs((float)$_POST['goods_num'][$key]);
                    $price = inputPrice($_POST['goods_price'][$key]);
                    $_taxTotal = inputPrice($this -> price ? $_POST['tax_total'][$key] : 0);

                    $goods[$_key] = array(
                        'goods'     => $value,
                        'num'       => $num,
                        'price'     => $price,
                        'total'     => $num * $price - $_taxTotal,
                        'repair'    => $_POST['goods_repair'][$key],
                        'com'       => $_POST['goods_com'][$key],
                        'tax'       => $_POST['tax'],
                        'tax_total' => $_taxTotal,
                    );
                    
                    $costTotal += $num * $goods[$_key]['cost'];
                    $totalPrice += $num * $price - $_taxTotal;
                    $taxTotal += $_taxTotal;
                }
            }

            //if(!$goods) $this -> success('没有添加相关货品，或数据不正确！', 3);

            $way = (isset($_POST['way']) && $_POST['way']) ? (int)$_POST['way'] : 0;
            $bank = $way ? $_POST['bank'] : 0;

            $data = array(
                'num'      => $_POST['num'],
                'cors'     => $_POST['cors'],
                'type'     => $this -> type,
                'goods'    => serialize($goods),
                'comment'  => $_POST['comment'],
                'uid'      => Session::get('uid'),
                'time'     => $_POST['time'] ? strtotime($_POST['time']) : NOW,
                'sale'     => (int)$_POST['sale'],
                'total'    => $totalPrice,
                'tax_total'=> $taxTotal,
                'audit'    => 0,
                'bill'     => (isset($_POST['bill']) && $_POST['bill']) ? strtotime($_POST['bill']) : 0,
                'spare'    => (isset($_POST['spare']) && $_POST['spare']) ? strtotime($_POST['spare']) : 0,
                'way'      => $way,
                'bank'     => $bank,
                'havemoney'=> inputPrice(trim($_POST['havemoney'])),
                'tax'      => (float)$_POST['tax'],
            );

            //开始计算用料
            $DB_Stock = D('stock');
            if($this -> mname == "Wastage" )
            {
                $useds = array();
                foreach($_POST['material']['group'] as $key => $value)
                {
                    $used_goods = (int)$_POST['material']['goods'][$key];
                    if((int)$_POST['material']['goods'][$key])
                    {
                        $useds[] =array(
                            'group' => $value,
                            'goods' => $used_goods,
                            'used'  => (float)$_POST['material']['used'][$key],
                            'surplus' => (float)$_POST['material']['surplus'][$key],
                        );
                        /**
                        $sub = (float)$_POST['material']['used'][$key] + (float)$_POST['material']['surplus'][$key];
                        $condition = "`audit` != '0' AND `group` = '{$value}' AND `goods` = '{$used_goods}'";
                        $sum = $DB_Stock -> sum($condition, 'num');
                        if($sum < $sub)
                        {
                            $DB_Group = D('Group');
                            $group = $DB_Group -> find("`gid`='{$value}'");
                            $DB_Goods = D('Goods');
                            $goods = $DB_Goods -> find("`id`='{$used_goods}'");
                            $this -> success('“'.$group['name'].'”仓库“'.$goods['name'].'”产品数量不足！', 3);
                        }
                        */
                    }
                }
                $data['used'] = serialize($useds);
            }
            //over


            $DB_Order = D('Order');
            //订单号唯一检查 并生成唯一值
            if($DB_Order -> find("`num` = '{$_POST['num']}' AND `type` = '{$this -> type}'"))
            {
                $postNum = explode('-', $_POST['num']);
                $num_prefix = $postNum[0].'-'.substr($postNum[1], 0, 8);
    			$order = D("Order") -> order("`num` desc") -> find("`type`='{$this->type}' AND `num` LIKE '$num_prefix%'");
    			$ids = explode('-', $order['num']);
    		    $seq = substr($ids[1], 8);
    		    $seq = $seq ? str_pad((int)$seq+1, 4, '0', STR_PAD_LEFT) : '0001';
    		    $_POST['num'] = $num_prefix.$seq;
            }
            /**
            if($data['num'] < 0)
            {
                $condition = "`audit` != '0' AND `group` = '{$_POST['group']}' AND `goods` = '{$_POST['goods']}'";
                $sum = $DB_Stock -> sum($condition, 'num');
                if($sum < abs($data['num']))
                    $this -> success('该仓库（不包含子仓库）数量不足！', 3);
            }
            */

            $id = $DB_Order -> add($data);
            if($id)
            {
                $data['id'] = $id;
                $data['user'] = array('uid'=>Session::get('uid'), 'name'=>Session::get('name'));
                $this -> mail('request', $data);
                if($data['bill']) $this -> mail('alertbill',$data);
                if($data['spare']) $this -> mail('alertspare',$data);
                $this -> success('添加成功', 3, $this->mname.'/add');
            }else{
                $this -> success('添加失败，请重试', 3);
            }

        }

        $this -> assign('banks', D("Bank") -> findAll());

        if(isset($_GET['do']))
        {
            //供应商.客户
            if($this -> abs)
            {
                $DB_Cors = D('Supplier');
            }else{
                $DB_Cors = D('Client');
            }
            $cors = $DB_Cors -> findAll();
            $this -> assign('cors', $cors);
			//供应商类型.客户类型
            if($this -> abs)
            {
                $DB_type = D('Supplier_type');
				$this ->assign('types',1);
            }else{
                $DB_type = D('Client_type');
				$this ->assign('types',2);
            }
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

			//仓库
			$DB_Group = D('Group');
			$list = $DB_Group ->findAll();
            //提取0级部门
            $level0 = array();
            $other = array();
            foreach ($list as $key => $value)
            {
                if($value['parentid']==0)
                {
                    $value['level'] = 0;
                    $level0[$value['gid']] = $value;
                }else{
                    $other[$value['gid']] = $value;
                }
            }
            ksort($level0);
            $group = dataTree($level0, $other, 'parentid', true);
			$this ->assign('group', $group);

			//自动生成单号
			//订单id desc排序 第一条数据+1
			$num_prefix = $this->pinyin.'-'.date("Ymd");
			$order = D("Order") -> order("`num` desc") -> find("`type`='{$this->type}' AND `num` LIKE '$num_prefix%'");
			$ids = explode('-', $order['num']);
		    $seq = substr($ids[1], 8);
		    $seq = $seq ? str_pad((int)$seq+1, 4, '0', STR_PAD_LEFT) : '0001';
			$this -> assign('num', $num_prefix.$seq);

			//销售人员 默认为使用者
			$this -> assign('nowUid', Session::get('uid'));
            //$this -> assign('allUsers', D('User') -> field('`uid`, `name`') -> findAll());
            $this -> assign('allUsers', D('User') -> field('`uid`, `name`') -> where("`del` = 0") -> findAll());
			$this -> assign('time', time());

            $this -> display("Order:add");
        }

        //重审
        if(isset($_GET['id']))
        {
            $id = (int)$_GET['id'];
            $uid = Session::get('uid');
            $DB_Order = D('Order');
            if($DB_Order -> save(array('audit'=>0, 'time'=>NOW), "`id`='{$id}' AND `uid`='{$uid}'"))
            {
                $data = $DB_Order -> find("`id`='{$id}'");
                $data['user'] = array('uid'=>Session::get('uid'), 'name'=>Session::get('name'));
                $this -> mail('request', $data);
                $this -> success('已提交重审！', 3, $this->mname.'/add');
            }else{
                $this -> success('提交失败，请重试', 3);
            }
        }
        $this -> assign('url', 'add');

        $uid = Session::get('uid');
        $data = $this -> getlist("`uid`='{$uid}'");

        $this -> assign($data);
        $this -> display("Order:my");
    }
    //over


    /**
     + -----------------------------------------------------------------------
     *  单据修改
     + -----------------------------------------------------------------------
     */
    public function edit($id = 0)
    {
        if($_POST)
        {
            $id = (int)$_POST['id'];
            if(!$id) $this -> success("数据访问错误，请重试。", 3);

            if(!$_POST['num'] || !$_POST['goods'])
                $this -> success("单号、产品不能为空！", 2);

            $DB_Order = D('Order');
            $data = $DB_Order -> find("`id`='{$id}'");

            //权限
            $can_do = $this -> get('can_do');
            if(!($data['uid'] == Session::get('uid') && $data['audit'] <= 0) && !isset($can_do[$this->mname]['edit']))
                $this -> success('您无权执行该操作！', 3, 'Index/index');

            //处理数据
            $totalPrice = $taxTotal = 0;
            $goods = $haveGoods = array();
            foreach($_POST['goods'] as $key => $value)
            {
                if(isset($haveGoods[$value]))
                    $haveGoods[$value] += 1;
                else
                    $haveGoods[$value] = 1;

                $_key = $value.'_'.$haveGoods[$value];

                if($value && (float)$_POST['goods_num'][$key] && (!$this -> price || ( $this -> price && $_POST['goods_price'][$key])))
                {
                    $num = abs((float)$_POST['goods_num'][$key]);
                    $price = inputPrice($_POST['goods_price'][$key]);

                    $_taxTotal = inputPrice($this -> price ? $_POST['tax_total'][$key] : 0);
                    $goods[$_key] = array(
                        'goods'     => $value,
                        'num'       => $num,
                        'price'     => $price,
                        'total'     => $num * $price - $_taxTotal,
                        'repair'    => $_POST['goods_repair'][$key],
                        'com'       => $_POST['goods_com'][$key],
                        'tax'       => $_POST['tax'],
                        'tax_total' => $_taxTotal,
                    );

                    $totalPrice += $num * $price - $_taxTotal;
                    $taxTotal += $_taxTotal;
                }
            }

            if(!$goods) $this -> success('没有添加相关货品，或数据不正确！', 3);

            $data = array(
                'id'       => $id,
                'num'      => $_POST['num'],
                'cors'     => $_POST['cors'],
                'type'     => $this -> type,
                'goods'    => serialize($goods),
                'comment'  => $_POST['comment'],
                'time'     => $_POST['time'] ? strtotime($_POST['time']) : NOW,
                'total'    => $totalPrice,
                'tax_total'=> $taxTotal,
                'sale'     => (int)$_POST['sale'],
                'bill'     => (isset($_POST['bill']) && $_POST['bill']) ? strtotime($_POST['bill']) : 0,
                'spare'    => (isset($_POST['spare']) && $_POST['spare']) ? strtotime($_POST['spare']) : 0,
                'havemoney'=> inputPrice(trim($_POST['havemoney'])),
                'tax'      => (float)$_POST['tax'],
            );

            //开始计算用料
            $DB_Stock = D('stock');
            if($this -> mname == "Wastage")
            {
                $useds = array();
                foreach($_POST['material']['group'] as $key => $value)
                {
                    $used_goods = (int)$_POST['material']['goods'][$key];
                    if((int)$_POST['material']['goods'][$key])
                    {
                        $useds[] =array(
                            'group' => $value,
                            'goods' => $used_goods,
                            'used'  => (float)$_POST['material']['used'][$key],
                            'surplus' => (float)$_POST['material']['surplus'][$key],
                        );
                        $sub = (float)$_POST['material']['used'][$key] + (float)$_POST['material']['surplus'][$key];
                        $condition = "`audit` != '0' AND `group` = '{$value}' AND `goods` = '{$used_goods}'";
                        $sum = $DB_Stock -> sum($condition, 'num');
                        if($sum < $sub)
                        {
                            $DB_Group = D('Group');
                            $group = $DB_Group -> find("`gid`='{$value}'");
                            $DB_Goods = D('Goods');
                            $goods = $DB_Goods -> find("`id`='{$used_goods}'");
                            $this -> success('“'.$group['name'].'”仓库“'.$goods['name'].'”产品数量不足！', 3);
                        }
                    }
                }
                $data['used'] = serialize($useds);
            }
            //over

            if($DB_Order -> find("`num` = '{$_POST['num']}' AND `type` = '{$this -> type}' AND `id` != '{$id}'"))
                $this -> success('单号已存在！', 3);

            if($DB_Order -> save($data))
            {
                $data['user'] = array('uid'=>Session::get('uid'), 'name'=>Session::get('name'));
                $this -> mail('request', $data);

                //删除原先提醒邮件
                $title1 = '订单'. $data['num'] .'的发票提醒';
                $title2 = '订单'. $data['num'] .'的余款提醒';
                D("User_mail") -> delete("`title`='{$title1}' OR `title`='{$title2}'");

                if($data['bill']) $this -> mail('alertbill',$data);
                if($data['spare']) $this -> mail('alertspare',$data);

                $url = isset($_GET['url']) ? $_GET['url'] : 'index';
                $this -> assign('url', $url);
                $this -> success('保存成功！', 2, $this->mname.'/'. $url . ($_POST['p'] ? '/p/'.$_POST['p'] : ''));
            }else{
                $this -> success('保存失败，请重试', 3);
            }
        }
        //over post

        //获取数据
        if(!$id) $id = (int)$_GET['id'];
        $DB_Order = D('Order');
        $data = $DB_Order -> find("`id`='{$id}'");
        $data['goods'] = unserialize($data['goods']);
        if($this -> mname == 'Wastage') $data['used'] = unserialize($data['used']);


        //权限
        $can_do = $this -> get('can_do');
        if(!($data['uid'] == Session::get('uid') && $data['audit'] <= 0) && !isset($can_do[$this->mname]['edit']))
            $this -> success('您无权执行该操作！', 3, 'Index/index');

        $this -> assign($data);

        //供应商.客户
        if($this -> abs)
        {
            $DB_Cors = D('Supplier');
        }else{
            $DB_Cors = D('Client');
        }
        $cors = $DB_Cors -> findAll();
        $this -> assign('corss', $cors);
//dump($cors);exit;
		//供应商类型.客户类型
		if($this -> abs)
		{
			$DB_type = D('Supplier_type');
			$this ->assign('types',1);
		}else{
			$DB_type = D('Client_type');
			$this ->assign('types',2);
		}
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
		$this -> assign('_type', $type);


        //仓库
        $DB_Group = D('Group');
        $list = $DB_Group ->findAll();
        //提取0级部门
        $level0 = array();
        $other = array();
        foreach ($list as $key => $value)
        {
            if($value['parentid']==0)
            {
                $value['level'] = 0;
                $level0[$value['gid']] = $value;
            }else{
                $other[$value['gid']] = $value;
            }
        }
        ksort($level0);
        $group = dataTree($level0, $other, 'parentid', true);
        $this ->assign('group', $group);

        $url = isset($_GET['url']) ? $_GET['url'] : 'index';
        $this -> assign('url', $url);

        $this -> assign('allUsers', D('User') -> field('`uid`, `name`') ->where("`del` = 0") -> findAll());

        $this -> assign('banks', D("Bank") -> findAll());

        $this -> assign('p', $_GET['p'] ? (int)$_GET['p'] : 1);

        $this -> display('Order:edit');
    }
    //over




    /**
     + -----------------------------------------------------------------------
     *  单据查看
     + -----------------------------------------------------------------------
     */
    public function view($id = 0)
    {
        //获取数据
        if(!$id) $id = (int)$_GET['id'];
        $DB_Order = D('Order', true);
        $DB_Order -> link('user', 'HAS_ONE', 'uid', 'user', 'uid', 'uid,name');
        $DB_Order -> link('response', 'HAS_ONE', 'sale', 'user', 'uid', 'uid,name');
        $table = $this->abs ? 'Supplier' : 'Client';
        $DB_Order -> link('cors', 'HAS_ONE', 'cors', $table, 'id', 'id,name');
		$data = $DB_Order -> xfind("`id`='{$id}'");

        //权限
        $can_do = $this -> get('can_do');
        if(!($data['uid'] == Session::get('uid')) && !isset($can_do[$this->mname]['view']))
            $this -> success('您无权执行该操作！', 3, 'Index/index');

        $data['goods'] = unserialize($data['goods']);
        if($this -> mname == 'Wastage'){
            $data['used'] = unserialize($data['used']);
            $this -> assign('needprice', 0);
        }

        $data['coststock'] = unserialize($data['coststock']);
        

        //需要入账
        if($this -> price)
        {
            $sum = 0;
            foreach($data['goods'] as $value)
            {
                $sum += $this -> abs ? -abs($value['price']) : abs($value['price']) ;
            }
            $this -> assign('sum', $sum);
        }

        $this -> assign($data);
        //$this -> assign('bank', $data['way'] ? D("Bank")->where("`id`='{$data['bank']}'")->find() : array());

        //货品
        $DB_Goods = D('Goods');
        $_goods = $DB_Goods -> findAll('', 'id,name,price,unit,model');
        $goods = array();
        foreach($_goods as $v)
            $goods[$v['id']] = $v;
        $this -> assign('allgoods', $goods);

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
        $_group = dataTree($level0, $other, 'parentid', true);
        $group = array();
        foreach($_group as $v)
            $group[$v['gid']] = $v;
        $this -> assign('group', $group);

        if($data['audit'] > 0){

            //仓库库存
            $DB_Stock = D('stock', true);
            $DB_Stock -> link('group', 'HAS_ONE', 'group', 'group', 'gid');
            $stock = $DB_Stock -> xfindAll("`order`='{$data['id']}' AND `come`='order'");
            foreach($data['goods'] as $key => $value)
            {
                foreach($stock as $k => $v)
                {
                    if($value['goods'] == $v['goods'])
                    {
                        $data['goods'][$key]['group'][] = $v;
                    }
                }
            }
            $this -> assign('goods' ,$data['goods']);

            //财务入账
            $DB_Financial = D('Financial');
            $financial = $DB_Financial -> find("`order`='{$data['id']}' AND `come`='order'");
            $this -> assign('money', $financial);
        }

        $url = isset($_GET['url']) ? $_GET['url'] : 'index';
        $this -> assign('url', $url);

        //提成比率
        $_percent = D("System_config") -> findAll("`name`='sorder' AND `type`='config'");
        $percent = array();
        foreach($_percent as $val)
        {
            $percent[$val['id']] = $val;
        }
        $this -> assign('percent', $percent);

        $this -> display('Order:view');
    }
    //over


    /**
     + -----------------------------------------------------------------------
     *  单据查看
     + -----------------------------------------------------------------------
     */
    public function costView($id = 0)
    {
        //获取数据
        if(!$id) $id = (int)$_GET['id'];
        $DB_Order = D('Order', true);
        $DB_Order -> link('user', 'HAS_ONE', 'uid', 'user', 'uid', 'uid,name');
        $DB_Order -> link('response', 'HAS_ONE', 'sale', 'user', 'uid', 'uid,name');
        $table = $this->abs ? 'Supplier' : 'Client';
        $DB_Order -> link('cors', 'HAS_ONE', 'cors', $table, 'id', 'id,name');
        $data = $DB_Order -> xfind("`id`='{$id}'");

        //权限
        $can_do = $this -> get('can_do');
        if(!($data['uid'] == Session::get('uid')) && !isset($can_do[$this->mname]['view']))
            $this -> success('您无权执行该操作！', 3, 'Index/index');

        $data['goods'] = unserialize($data['goods']);
        if($this -> mname == 'Wastage'){
            $data['used'] = unserialize($data['used']);
            $this -> assign('needprice', 0);
        }

        $data['coststock'] = unserialize($data['coststock']);
// var_dump($data['coststock']);die;
        //需要入账
        if($this -> price)
        {
            $sum = 0;
            foreach($data['goods'] as $value)
            {
                $sum += $this -> abs ? -abs($value['price']) : abs($value['price']) ;
            }
            $this -> assign('sum', $sum);
        }

        $this -> assign($data);
        //$this -> assign('bank', $data['way'] ? D("Bank")->where("`id`='{$data['bank']}'")->find() : array());

        //货品
        $DB_Goods = D('Goods');
        $_goods = $DB_Goods -> findAll('', 'id,name,price,unit,model');
        $goods = array();
        foreach($_goods as $v)
            $goods[$v['id']] = $v;
        $this -> assign('allgoods', $goods);

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
        $_group = dataTree($level0, $other, 'parentid', true);
        $group = array();
        foreach($_group as $v)
            $group[$v['gid']] = $v;
        $this -> assign('group', $group);

        if($data['audit'] > 0){

            //仓库库存
            $DB_Stock = D('stock', true);
            $DB_Stock -> link('group', 'HAS_ONE', 'group', 'group', 'gid');
            $stock = $DB_Stock -> xfindAll("`order`='{$data['id']}' AND `come`='order'");
            foreach($data['goods'] as $key => $value)
            {
                foreach($stock as $k => $v)
                {
                    if($value['goods'] == $v['goods'])
                    {
                        $data['goods'][$key]['group'][] = $v;
                    }
                }
            }
            var_dump($data);
            $this -> assign('goods' ,$data['goods']);

            //财务入账
            $DB_Financial = D('Financial');
            $financial = $DB_Financial -> find("`order`='{$data['id']}' AND `come`='order'");
            $this -> assign('money', $financial);
        }

        $url = isset($_GET['url']) ? $_GET['url'] : 'index';
        $this -> assign('url', $url);

        //提成比率
        $_percent = D("System_config") -> findAll("`name`='sorder' AND `type`='config'");
        $percent = array();
        foreach($_percent as $val)
        {
            $percent[$val['id']] = $val;
        }
        $this -> assign('percent', $percent);

        $this -> display('Order:costView');
    }
    //over


    /**
     + -----------------------------------------------------------------------
     *  单据删除
     + -----------------------------------------------------------------------
     */
    public function delete($id = 0)
    {
        if(!$id) $id = (int)$_GET['id'];
        if(!$id) $this -> success("数据访问错误，请重试。", 3);
        $DB_Order = D('Order');
        $data = $DB_Order -> find("`id`='{$id}'");

        //权限
        $can_do = $this -> get('can_do');
        if(!($data['uid'] == Session::get('uid') && $data['audit'] <= 0) && !isset($can_do[$this->mname]['delete']))
            $this -> success('您无权执行该操作！', 3, 'Index/index');

        if($DB_Order -> delete("`id`='{$id}'"))
        {
            //删除提醒邮件
            if($data['bill'] || $data['spare'])
            {
                $title1 = '订单'. $data['num'] .'的发票提醒';
                $title2 = '订单'. $data['num'] .'的余款提醒';
                D("User_mail") -> delete("`title`='{$title1}' OR `title`='{$title2}'");
            }


            $url = isset($_GET['url']) ? $_GET['url'] : 'index';
            $this -> success('删除成功！', 2, $this->mname.'/'.$url);
        }else{
            $this -> success('操作失败，请重试！',3);
        }
    }
    //over


    /**
     + -----------------------------------------------------------------------
     *  单据未审核列表
     + -----------------------------------------------------------------------
     */
    public function audit()
    {
        if($_POST)
        {
            if(isset($_GET['edit']))
                $this -> edit((int)$_GET['edit']);
            else
                $this -> pass();
        }

        if(isset($_GET['view']))
            $this -> view((int)$_GET['view']);

        if(isset($_GET['edit']))
            $this -> edit((int)$_GET['edit']);

        if(isset($_GET['delete']))
            $this -> delete((int)$_GET['delete']);

        if(isset($_GET['refuse']))
        {
            $id = (int)$_GET['refuse'];
            $DB_Order = D('Order');
            if($DB_Order -> save(array('audit' => '-'.Session::get('uid')), "`id`='{$id}'"))
            {
                $this -> mail('feedback', $DB_Order -> find("`id`='{$id}'"));
                $this -> success("提交成功！", 3, $this->mname.'/audit');
            }else{
                $this -> success("提交失败，请重试。", 3);
            }
        }

        $condition = "`audit`=0";
        $data = $this -> getlist($condition);
        $this -> assign($data);

        $_allUsers = D('user') -> field('uid, name') -> findAll();
		$allUsers = array();
		foreach ($_allUsers as $key=>$val)
		{
            $allUsers[$val['uid']] = $val;
		}
		$this -> assign('allUsers', $allUsers);

        $this -> display("Order:audit");
    }
    //over


    //申和通过
    protected function pass($table = 'Order')
    {
        //原始数据
        $id = (int)$_POST['id'];
        if(!$id) $this -> success("数据访问错误，请重试。", 3);
        $DB_Order = D($table, true);
        $DB_Order -> link('user', 'HAS_ONE', 'uid', 'user', 'uid', 'uid,name');
        $order = $DB_Order -> xfind("`id` = '{$id}'");
        $order_goods = unserialize($order['goods']);

        //全部产品
        $DB_Goods = D('Goods');
        $goods = $DB_Goods -> findAll();
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
                if(!(int)$_POST['num'][$key][$k]) continue;
                if(!isset($group[$v])) $group[$v] = array();
                if($group[$v][$key])
                {
                    $num = $group[$v][$key]['num'] + (int)$_POST['num'][$key][$k];
                }else{
                    $num = (int)$_POST['num'][$key][$k];
                }

                list($goods, $list) = explode('_', $key);
                $group[$v][$key] = array('goods'=>$goods, 'num'=>$num);
            }
        }

        //if(!$group) $this -> success('操作失败，请重试！',3);

        //生成数据
        $DB_Stock = D('Stock');
        $data = array();

        //存放在stock_info表里的出库记录信息
        $info = array();
        //成本计算
        //$cost = array('totalPrice'=>0, 'stock'=>array());
        $cost = array('total'=>0, 'stock'=>array());
        foreach($group as $key => $value)
        {
            //$key 是仓库id
            foreach($value as $k=>$v)
            {
                $num = abs($v['num']);
                if($num)
                {
                    $_goodsInfo = $order_goods[$k];
                    $data[] = array(
                        'goods'     => $v['goods'],
                        'goods_name' => $allgoods[$v['goods']]['name'],
                        'group'     => $key,
                        'num'       => $this -> abs ? $num : -$num,
                        'price'     => (int)($_goodsInfo['price']*$num) * ($this -> abs ? -1 : 1),
                        'time'      => NOW,
                        'uid'       => $order['uid'],
                        'rtime'     => NOW,
                        'comment'   => str_replace('单', '', $this -> title) . ' ('.$order['num'].')',
                        'order'     => $order['id'],
                        'audit'     => 0,
                        'come'      => 'order',
                    );

                    //计算成本
                    if($this -> mname == 'Sorder')
                    {

                        $productCost = $this -> productCost($v['goods'], $num, $key);
                    //var_dump($productCost);exit;
                        $cost['total'] += $productCost['price'];
                        //销售的产品 购买时的入库信息
                        $cost['stock'][$v['goods']][$key] = array(
                            'detail' => $productCost['stock'],
                            'num'    => $num,
                        );
                //var_dump($productCost['info']);exit;
                        //stock表的附加信息，stock_info表里的记录
                        $info = array_merge($info,$productCost['info']);
                    }


                    //检查库存
                    // $condition = "`audit` != '0' AND `group` = '{$key}' AND `goods` = '{$v['goods']}'";
                    // $sum = $DB_Stock -> sum($condition, 'num');
                    // if(!$this -> abs && $sum < $num)
                    //     $this -> success('库存不足，无法分配！', 3);
                }
            }
        }
        // var_dump($cost);die;
        //用料提交
        if($this -> mname == 'Wastage')
        {
            $used = unserialize($order['used']);
            foreach($used as $v)
            {
                $data[] = array(
                    'goods'     => $v['goods'],
                    'goods_name' => $allgoods[$v['goods']]['name'],
                    'group'     => $v['group'],
                    'num'       => -($v['used']+$v['surplus']),
                    'price'     => 0,
                    'time'      => NOW,
                    'uid'       => $order['uid'],
                    'rtime'     => NOW,
                    'comment'   => '生产出库 ('.$order['num'].')',
                    'order'     => $order['id'],
                    'audit'     => 0,
                );
            }
        }

        //计入库存审核
        $DB_Stock = D('Stock');
        $DB_Stock -> startTrans();
        $stock = $DB_Stock -> addAll($data);
        if($stock)
        {
            if(!empty($info))
            {
                $sinfo = D("Stock_info") -> addAll($info);

                if($sinfo){
                    $DB_Stock -> commit();
                }else{
                    $DB_Stock -> rollback();
                    $this -> success('操作失败，请重试！',3);
                }
            }else{
                $DB_Stock -> commit();
            }
            
        }else{
            $DB_Stock -> rollback();
            $this -> success('操作失败，请重试！',3);
        }

        $error = '';
        $corstable = array('', 'supplier', 'supplier', 'client');
        //需要记账
        if($this -> price) //生产入库，不记账
        {
            $_POST['price'] = $this -> abs ? -abs(inputPrice($_POST['price'])) : abs(inputPrice($_POST['price']));
            $data = array(
                'name'     => str_replace('单', '', $this -> title) . ' ('.$order['num'].')'.($table == 'Return' ? '(退货)' : ''),
                'price'    => $_POST['price'],
                'time'     => NOW,
                'arrive'   => 0,
                'notto'    => $_POST['price'],
                'uid'      => $order['uid'],
                'rtime'    => NOW,
                'order'    => $order['id'],
                'income'   => $_POST['income']*100,
                'audit'    => 0,
                'come'     => 'order',
                'cors'     => $order['cors'],
                'corstable'=> $corstable[$order['type']],
                'cost'     => $this -> mname == 'Sorder' ? $cost['total'] : 0,
                //'way'      => $order['way'],
                //'bank'     => $order['bank'],
            );
            $DB_Financial = D('Financial');
            if(!$DB_Financial -> add($data))
            {
                $DB_Stock -> deleteAll("`order` = '{$order['id']}'");
                $$this -> success('财务收支提交失败，请重试！', 3, $this->mname.'/audit');
            }
        }

        $DB_Order = D('Order');
        if($this -> mname == 'Sorder')
            $result = $DB_Order -> save(array('audit'=>Session::get('uid'), 'express'=>$_POST['express'], 'code'=>$_POST['code'], 'sort'=>$_POST['sort'], 'comment'=>$_POST['comment'], 'income'=>$_POST['income']*100, 'cost'=>$cost['total'], 'coststock'=>serialize($cost['stock'])), "`id`='{$order['id']}'");
        else
            $result = $DB_Order -> save(array('audit'=>Session::get('uid'), 'express'=>$_POST['express'], 'code'=>$_POST['code'], 'comment'=>$_POST['comment']), "`id`='{$order['id']}'");

        if(false !== $result)
        {
            $order['audit'] = 1;
            $this -> mail('feedback', $order);
            $this -> success('审核成功！', 3, $this->mname.'/audit');
        }else{
            $DB_Stock -> deleteAll("`order` = '{$order['id']}'");
            $DB_Financial -> deleteAll("`order` = '{$order['id']}'");
            $this -> success('审核结果提交失败，请重试！',3);
        }
    }
    //over


    //检测是否完成交易
    public function over($id, $type='')
    {
        if(!isset($id)) $this -> redirect('index', $this->mname);

        if(!$type || !in_array($type, array('order', 'service')))
            return false;

        $DB_Order = D(ucfirst($type));
        $data = $DB_Order -> find("`id`='{$id}'");
        if($data['over']) return true;

        $_goods = unserialize($order['goods']);
        $goods = array();
        foreach($_goods as $v)
            $goods[$v['goods']] = $v['num'];


        //仓库库存
        $DB_Stock = D('stock');
        $sumlist = $DB_Stock -> findAll("`order` = '{$id}' AND `audit` != '0'", 'SUM(num) as sum,goods', '', '', 'goods');
        foreach($sumlist as $key => $value)
        {
            if($goods[$value['goods']] > $value['sum'])
                return false;
        }

        //财务入账
        $DB_Financial = D('Financial');
        $financial = $DB_Financial -> find("`order`='{$data['id']}' && `come`='{$type}'");
        if(!$financial || ($financial['notto'] == 0 && $financial['audit'] != 0))
        {
            $DB_Order -> save(array('over'=>1), "`id`='{$id}'");
            return true;
        }
        return false;

    }
    //over


    //单据记录列表 内部用
    public function getlist($condition = '', $return=false)
    {
        $prefix = C("DB_PREFIX");
        if($condition) $condition .= " AND `type` = '{$this->type}'";
        else $condition = "`type` = '{$this->type}'";

        // $total = D("order") -> sum($condition, 'total');
        // $taxTotal = D("order") -> sum($condition, 'tax_total');
        // $income = D("order") -> sum($condition, 'income');
        $sql = "SELECT 
                    SUM(`total`) as total,
                    SUM(`tax_total`) as taxTotal,
                    SUM(`income`) as income,
                    SUM(CASE WHEN `tax`>0 THEN total ELSE 0 END) as haveTax
                FROM {$prefix}order 
                WHERE {$condition}";
        $totals = D() -> query($sql);
        extract($totals[0]);

        //echo $taxTotal;

        $DB_Order = D('Order', true);
        $DB_Order -> link('user', 'HAS_ONE', 'uid', 'user', 'uid', 'uid,name');
        $DB_Order -> link('cors', 'HAS_ONE', 'cors', $this->abs ? 'Supplier' : 'Client', 'id', 'id,name');

        import('Extend.Util.Page');
        $Page = new Page($DB_Order->count($condition), 15);
        $limit =  $Page->firstRow.','.$Page->listRows;
        $list = $DB_Order -> xfindAll($condition, 'id,num,cors,type,uid,time,audit,over,sale,total,income,tax, tax_total,cost', 'time Desc', $limit);
        
        //退换货
        if($return)
        {
            $condition = str_replace(array('sale', 'time'), array('uid', 'rtime'), $condition);
            $condition = (strpos($condition, '`') == 0 ? 'r.' : '') . str_replace(' `', ' r.`', $condition);
            $sql = "SELECT
                        SUM(r.`price`) as reback, 
                        SUM(CASE WHEN o.tax>0 THEN r.`price` ELSE 0 END) as taxreback 
                    FROM {$prefix}return r 
                    LEFT JOIN {$prefix}order o ON o.num=r.num 
                    WHERE {$condition}";
            $returnData = D("Return") -> query($sql);
            extract($returnData[0]);
            //$reback = D("Return") -> sum($condition, 'price');
        }
            
        return array('list' => $list, 'page' => $Page -> show(), 'total'=>$total, 'taxTotal'=>$taxTotal, 'haveTax'=>$haveTax, 'income'=>$income, 'reback'=>$reback, 'taxreback'=>$taxreback);
    }
    //over


    //邮件发送
    protected function mail($type, $arg)
    {
        switch($type)
        {
            case 'feedback'://反馈
                //发送邮件
                $mails[] = array(
                    'uid'     => $arg['uid'],
                    'title'   => '“'.$this -> title.'”审批结果',
                    'content' => '您提交了的“'.$this -> title.'”，'.
                                ($arg['audit'] > 0 ? '审核通过' : '审核未通过').
                                '。单号：<a href="'.__APP__.'/'.$this->mname.'/view/id/'.$arg['id'].'">《'.$arg['num'].'》</a>',
                    'read'    => 0,
                    'reply'   => 0,
                    'time'    => NOW,
                    'system'  => 1,
                );

                if($arg['audit'] > 0)
                {
                    $mail2 = array(
                        'uid'     => 0,
                        'title'   => '“'.$this -> title.'”审核通过，等待货/款确认',
                        'content' => $arg['user']['name'].'提交了的“'.$this -> title.'”审核通过'.
                                    '。单号：<a href="'.__APP__.'/'.$this->mname.'/view/id/'.$arg['id'].'">《'.$arg['num'].'》</a>',
                        'read'    => 0,
                        'reply'   => 0,
                        'time'    => NOW,
                        'system'  => 1,
                    );

                    //仓管、财务提醒
                    $mails += $this -> mailUser('Stock', 'audit', $mail2);
                    $mails += $this -> mailUser('Financial', 'audit', $mail2);
                }

                $DB_Mail = D('user_mail');
                return $DB_Mail -> addAll($mails);

            case 'request'://请求

                //准备邮件
                $mail = array(
                    'uid'     => 0,
                    'title'   => '新的“'.$this -> title.'”需要审批',
                    'content' => $arg['user']['name'].'提交了新的“'.$this -> title.'”需要您审批，单号：<a href="'.__APP__.'/'.$this->mname.'/view/id/'.$arg['id'].'">《'.$arg['num'].'》</a>',
                    'read'    => 0,
                    'reply'   => 0,
                    'time'    => NOW,
                    'system'  => 1,
                );

                $mails = $this -> mailUser($this->mname, 'audit', $mail);

                $DB_Mail = D('user_mail');
                return $DB_Mail -> addAll($mails);
            case 'alertbill': //发票提醒
                //准备邮件
                $mail = array(
                    'uid'     => Session::get('uid'),
                    'title'   => '订单'. $arg['num'] .'的发票提醒',
                    'content' => '订单'. $arg['num'] .'的发票需要发送，查看：<a href="'.__APP__.'/'.$this->mname.'/view/id/'.$arg['id'].'">《'.$arg['num'].'》</a>',
                    'read'    => 0,
                    'reply'   => 0,
                    'time'    => $arg['bill'],
                    'system'  => 1,
                );

                $DB_Mail = D('user_mail');
                return $DB_Mail -> add($mail);

            case 'alertspare': //余款提醒
                //准备邮件
                $mail = array(
                    'uid'     => Session::get('uid'),
                    'title'   => '订单'. $arg['num'] .'的余款提醒',
                    'content' => '订单'. $arg['num'] .'的余款需要收回，查看：<a href="'.__APP__.'/'.$this->mname.'/view/id/'.$arg['id'].'">《'.$arg['num'].'》</a>',
                    'read'    => 0,
                    'reply'   => 0,
                    'time'    => $arg['spare'],
                    'system'  => 1,
                );

                $DB_Mail = D('user_mail');
                return $DB_Mail -> add($mail);

            case 'back': //退货
                //准备邮件
                $mail = array(
                    'uid'     => Session::get('uid'),
                    'title'   => '订单'. $arg['num'] .'的有退货申请',
                    'content' => $arg['user']['name'].'提交了新的退货需您审批，查看：<a href="'.__APP__.'/'.$this->mname.'/viewSwap/id/'.$arg['id'].'">《'.$arg['num'].'》</a>',
                    'read'    => 0,
                    'reply'   => 0,
                    'time'    => NOW,
                    'system'  => 1,
                );

                $mails = $this -> mailUser($this->mname, 'auditSwap', $mail);
                $DB_Mail = D('user_mail');
                return $DB_Mail -> addAll($mails);

            case 'change': //换货
                //准备邮件
                $mail = array(
                    'uid'     => Session::get('uid'),
                    'title'   => '订单'. $arg['num'] .'的换货申请',
                    'content' => $arg['user']['name'].'提交了新的换货需您审批，查看：<a href="'.__APP__.'/'.$this->mname.'/viewSwap/id/'.$arg['id'].'">《'.$arg['num'].'》</a>',
                    'read'    => 0,
                    'reply'   => 0,
                    'time'    => NOW,
                    'system'  => 1,
                );

                $mails = $this -> mailUser($this->mname, 'auditSwap', $mail);
                $DB_Mail = D('user_mail');
                return $DB_Mail -> addAll($mails);
        }
    }
    //mail over


    protected function mailUser($m, $a, $mail)
    {
        //查询下一步操作的节点...
        $_Node = D('system_node');
        $node = $_Node -> find(array('m'=>$m, 'a'=>$a), 'id');
        if(!$node) return array();

        //查询节点所需角色 并 关联查询符合的用户
        $DB_Role = D('role_node', true);
        $DB_Role -> link('users', 'HAS_MANY', 'role', 'user_role', 'role', 'uid,role,time', '`time` > \''.NOW.'\' OR `time` = \'0\'');
        $roles = $DB_Role -> xfindAll("`node` = '{$node['id']}'");
        $mails = array();
        foreach($roles as $value)
        {
            foreach($value['users'] as $v)
            {
                $mail['uid'] = $v['uid'];
                $mails[] = $mail;
            }
        }
        return $mails;
    }
}
?>