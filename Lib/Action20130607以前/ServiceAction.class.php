<?php
class ServiceAction extends Action
{
    protected $mname = 'Service';   //模块名称

    protected $type = 1;

    protected $title = '维修管理';

    protected $abs = false;         //供应商1  客户0

    protected $price = true;        //生产记账

    protected $pinyin = 'wxgl';     //生成单号用

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

		if($_GET['p'])
            Cookie::set('servicePage', $_GET['p']);
        else
            Cookie::set('servicePage', 1);

        $this -> display("index");
    }
    //over

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

            $totalPrice = $taxTotal =0;
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
                        'goods' => $value,
                        'num'   => $num,
                        'price' => $price,
                        'total' => $price * $num - $_taxTotal,
                        'tax'   => $_POST['tax'][$key],
                        'tax_total' => $_taxTotal,
                        'repair'=> '', //$_POST['goods_repair'][$key],
                        'rePrice'=> 0, //inputPrice($_POST['repair_price'][$key]),
                        'com'   => $_POST['goods_com'][$key],
                    );

                    $totalPrice += $price * $num - $_taxTotal; // + inputPrice($_POST['repair_price'][$key]);
                    $taxTotal += $_taxTotal;
                }
            }
            //if(!$goods) $this -> success('没有添加相关货品，或数据不正确！', 3);

            $DB_Order = D('Service');
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

            $data = array(
                'num'      => $_POST['num'],
                'cors'     => $_POST['cors'],
                'type'     => $this -> type,
                'goods'    => serialize($goods),
                'comment'  => $_POST['comment'],
                'uid'      => Session::get('uid'),
                'time'     => $_POST['time'] ? strtotime($_POST['time']) : NOW,
                'responsible'   => $_POST['responsible'] ? ','.$_POST['responsible'].',' : '',
                'audit'    => 0,
                'total'    => $totalPrice,
                'tax_total'=> $taxTotal,
                'way'      => $_POST['way'],
                'bank'     => $_POST['way'] ? $_POST['bank'] : 0,
                'havemoney'=> inputPrice(trim($_POST['havemoney'])),
                'service'   => $_POST['service'] ? ','.$_POST['service'].',' : '',
            );

            $DB_Stock = D('stock');

            //开始计算用料
            $useds = array();
            foreach($_POST['goods'] as $key => $value)
            {
                if((int)$value)
                {
                    $useds[] =array(
                        'group' => 3,
                        'goods' => $value,
                        'used'  => (float)$_POST['goods_num'][$key],
                    );
                    /**
                    $sub = (float)$_POST['goods_num'][$key];
                    $condition = "`audit` != '0' AND `group` = '3' AND `goods` = '{$value}'";
                    $sum = $DB_Stock -> sum($condition, 'num');
                    if($sum < $sub)
                    {
                        $DB_Group = D('Group');
                        $group = $DB_Group -> find("`gid`='3'");
                        $DB_Goods = D('Goods');
                        $goods = $DB_Goods -> find("`id`='{$value}'");
                        $this -> success('“'.$group['name'].'”仓库“'.$goods['name'].'”产品数量不足！', 3);
                    }
                    */
                }
            }
            $data['used'] = serialize($useds);
            //over

            if($data['num'] < 0)
            {
                $condition = "`audit` != '0' AND `group` = '{$_POST['group']}' AND `goods` = '{$_POST['goods']}'";
                $sum = $DB_Stock -> sum($condition, 'num');
                if($sum < abs($data['num']))
                    $this -> success('该仓库（不包含子仓库）数量不足！', 3);
            }

            $id = $DB_Order -> add($data);
            if($id)
            {
                $data['id'] = $id;
                $data['user'] = array('uid'=>Session::get('uid'), 'name'=>Session::get('name'));
                $this -> mail('request', $data);

                //发货提醒
                $data['send'] = $_POST['send'] ? strtotime($_POST['send']) : 0;
                if($_POST['send']) $this -> mail('alertsend', $data);

                $this -> success('添加成功', 3, $this->mname.'/add');
            }else{
                $this -> success('添加失败，请重试', 3);
            }
        }


        $this -> getCors();

        $this -> getGroup();

		//自动生成单号
		//订单id desc排序 第一条数据+1
		$num_prefix = $this->pinyin.'-'.date("Ymd");
		$order = D("Service") -> order("`num` desc") -> find("`type`='{$this->type}' AND `num` LIKE '$num_prefix%'");
		$ids = explode('-', $order['num']);
	    $seq = substr($ids[1], 8);
	    $seq = $seq ? str_pad((int)$seq+1, 4, '0', STR_PAD_LEFT) : '0001';
		$this -> assign('num', $num_prefix.$seq);

		$this -> assign('responsible', session::get('name'));
		$this -> assign('time', time());

		$this -> assign('banks', D("Bank") -> findAll());

        $this -> display("add");
    }
    //over

    public function getCors($edit = false)
    {
        //供应商.客户
        if($this -> abs)
        {
            $DB_Cors = D('Supplier');
        }else{
            $DB_Cors = D('Client');
        }
        $cors = $DB_Cors -> findAll();

        if($edit)
            //修改用？
            $this -> assign('corss', $cors);
        else
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

		if($edit)
            //修改用？
            $this -> assign('_type', $type);
        else
            $this -> assign('DB_type', $type);

    }

    public function getGroup()
    {
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
    }

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

            if(!$_POST['num'] || !$_POST['cors'] || !$_POST['goods'])
                $this -> success("单号、产品不能为空！", 2);

            $DB_Order = D('Service');
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
                        'goods' => $value,
                        'num'   => $num,
                        'price' => $price,
                        'total' => $price * $num - $_taxTotal,
                        'tax'   => $_POST['tax'][$key],
                        'tax_total' => $_taxTotal,
                        'repair'=> '', //$_POST['goods_repair'][$key],
                        'rePrice'=> 0, //inputPrice($_POST['repair_price'][$key]),
                        'com'   => $_POST['goods_com'][$key],
                    );

                    $totalPrice += $price * $num - $_taxTotal; // + inputPrice($_POST['repair_price'][$key]);
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
                'responsible'   => $_POST['responsible'] ? ','.$_POST['responsible'].',' : '',
                'time'     => $_POST['time'] ? strtotime($_POST['time']) : NOW,
                'total'    => $totalPrice,
                'tax_total'=> $taxTotal,
                'way'      => $_POST['way'],
                'bank'     => $_POST['way'] ? $_POST['bank'] : 0,
                'havemoney'=> inputPrice(trim($_POST['havemoney'])),
                'service'  => $_POST['service'] ? ','.$_POST['service'].',' : '',
            );

            //开始计算用料
            $DB_Stock = D('stock');

            foreach($_POST['goods'] as $key => $value)
            {
                if((int)$value)
                {
                    $useds[] =array(
                        'group' => 3,
                        'goods' => $value,
                        'used'  => (float)$_POST['goods_num'][$key],
                    );
                    $sub = (float)$_POST['goods_num'][$key];
                    $condition = "`audit` != '0' AND `group` = '3' AND `goods` = '{$value}'";
                    $sum = $DB_Stock -> sum($condition, 'num');
                    /**
                    if($sum < $sub)
                    {
                        $DB_Group = D('Group');
                        $group = $DB_Group -> find("`gid`='3'");
                        $DB_Goods = D('Goods');
                        $goods = $DB_Goods -> find("`id`='{$value}'");
                        $this -> success('“'.$group['name'].'”仓库“'.$goods['name'].'”产品数量不足！', 3);
                    }*/
                }
            }
            $data['used'] = serialize($useds);
            //over

            if($DB_Order -> find("`num` = '{$_POST['num']}' AND `type` = '{$this -> type}' AND `id` != '{$id}'"))
                $this -> success('单号已存在！', 3);

            if($DB_Order -> save($data))
            {
                $data['user'] = array('uid'=>Session::get('uid'), 'name'=>Session::get('name'));
                $this -> mail('request', $data);

                //删除原先提醒邮件
                $title = '售后服务'. $data['num'] .'的发货提醒';
                D("User_mail") -> delete("`title`='{$title}'");

                $data['send'] = $_POST['send'] ? strtotime($_POST['send']) : 0;
                if($data['send']) $this -> mail('alertsend',$data);

                $url = isset($_GET['url']) ? $_GET['url'] : 'index';
                $this -> assign('url', $url);

                $page = Cookie::get('servicePage');
                $this -> success('保存成功！', 2, $this->mname.'/'.$url.'/p/'.$page);
            }else{
                $this -> success('保存失败，请重试', 3);
            }
        }
        //over post

        //获取数据
        if(!$id) $id = (int)$_GET['id'];
        $DB_Order = D('Service');
        $data = $DB_Order -> find("`id`='{$id}'");
        $data['goods'] = unserialize($data['goods']);
        if($this -> mname == 'Wastage') $data['used'] = unserialize($data['used']);


        //权限
        $can_do = $this -> get('can_do');
        if(!($data['uid'] == Session::get('uid') && $data['audit'] <= 0) && !isset($can_do[$this->mname]['edit']))
            $this -> success('您无权执行该操作！', 3, 'Index/index');

        $this -> assign($data);


        $this -> getCors(true);
        $this -> getGroup();

        $url = isset($_GET['url']) ? $_GET['url'] : 'index';
        $this -> assign('url', $url);

        $this -> assign('banks', D("Bank") -> findAll());

        $this -> display('edit');
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
        $DB_Order = D('Service', true);
        $DB_Order -> link('user', 'HAS_ONE', 'uid', 'user', 'uid', 'uid,name');
        $DB_Order -> link('cors', 'HAS_ONE', 'cors', $this->abs ? 'Supplier' : 'Client', 'id', 'id,name');
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

        if($data['sort1'] && $data['sort2'])
        {
            //维修人
            $this -> assign('serviceman', D('System_config')->where("`id`={$data['sort1']}")->find());
            //维修类型
            $this -> assign('servicetype', D('System_config')->where("`id`={$data['sort2']}")->find());
        }

        //需要入账
        if($this -> price)
        {
            $sum = 0;
            foreach($data['goods'] as $value)
            {
                $sum += $this -> abs ? -abs($value['total']) : abs($value['total']) ;
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
            $stock = $DB_Stock -> xfindAll("`order`='{$data['id']}' AND `come`='service'");
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
            $financial = $DB_Financial -> find("`order`='{$data['id']}' AND `come`='service'");
            $this -> assign('money', $financial);
        }

        $url = isset($_GET['url']) ? $_GET['url'] : 'index';
        $this -> assign('url', $url);

        //提成比率
        $_percent = D() -> query("SELECT * FROM ix_system_config WHERE `name` like 'service%' AND `type`='config'");

        $percent1 = $precent2 = array();
        foreach($_percent as $val)
        {
            if($val['name'] == 'service1')
                $percent1[$val['id']] = $val;
            else
                $percent2[$val['id']] = $val;
        }
        $this -> assign('percent1', $percent1);
        $this -> assign('percent2', $percent2);

        $this -> display('view');
    }
    //over


    /**
     * 统计
     *
     */
    public function statistics()
    {
        $condition = array();
        if(isset($_GET['search']) && (int)$_GET['search'] > 0)
            $condition[] = "`sort1`={$_GET['search']}";

        if (isset($_GET['cors']) && (int)$_GET['cors'])
            $condition[] = "`cors`=".(int)$_GET['cors'];

        if (trim($_GET['start']) && trim($_GET['end']))
            $condition[] = "`time`>=".strtotime($_GET['start'])." AND `time`<=".strtotime($_GET['end'].' 23:59:59');

        $condition[] = "`audit` > '0'";
        $condition = implode(' AND ', $condition);

        $data = $this -> getlist($condition);
        $this -> assign($data);

        //供应商.客户
        $DB_Cors = D('Client');
        $cors = $DB_Cors -> findAll();
        $this -> assign('cors', $cors);
		//供商厂类型。客户类型
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

		$this -> assign('search', isset($_GET['search']) ? $_GET['search'] : 0);
		$this -> assign('_cors', isset($_GET['cors']) ? $_GET['cors'] : '');
		$this -> assign('start', isset($_GET['start']) ? $_GET['start'] : '');
		$this -> assign('end', isset($_GET['end']) ? $_GET['end'] : '');

		$this -> assign('serviceman', D("System_config")->where("`name`='service1'")->findAll());

        $this -> display("statistics");
    }


    public function printStatistics()
    {
        $condition = array();
        $search = array('intro'=>'');
        if(isset($_GET['search']) && $_GET['search'] > 0)
        {
            $condition[] = "`sort1`={$_GET['search']}";
            $search = D("System_config") -> where("`id`={$_GET['search']}") -> find();
        }

        if(isset($_GET['cors']) && $_GET['cors'])
            $condition[] = "`cors` = '".(int)$_GET['cors']."'";

        $this -> assign('search', $search);

        if (trim($_GET['start']) && trim($_GET['end']))
            $condition[] = "`time`>=".strtotime($_GET['start'])." AND `time`<=".strtotime($_GET['end'].' 23:59:59');

        $condition[] = "`audit` > '0'";
        $condition = implode(' AND ', $condition);

        if($condition) $condition .= " AND `type` = '{$this->type}'";
        else $condition = "`type` = '{$this->type}'";

        $data['total'] = D("Service") -> sum($condition, 'total');
        $data['income'] = D("Service") -> sum($condition, 'income2');
        $data['taxTotal'] = D("Service") -> sum($condition, 'tax_total');

        $DB_Order = D('Service', true);
        $DB_Order -> link('user', 'HAS_ONE', 'uid', 'user', 'uid', 'uid,name');
        $DB_Order -> link('cors', 'HAS_ONE', 'cors', $this->abs ? 'Supplier' : 'Client', 'id', 'id,name');
        $DB_Order -> link('sorts', 'HAS_ONE', 'sort1', 'System_config', 'id', 'id,intro');
        $data['list'] = $DB_Order -> xfindAll($condition, '*', 'time Desc');

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


		$this -> assign('start', isset($_GET['start']) ? $_GET['start'] : '');
		$this -> assign('end', isset($_GET['end']) ? $_GET['end'] : '');

        $this -> display("printStatistics");
    }



    /**
     + -----------------------------------------------------------------------
     *  单据删除
     + -----------------------------------------------------------------------
     */
    public function delete($id = 0)
    {
        if(!$id) $id = (int)$_GET['id'];
        if(!$id) $this -> success("数据访问错误，请重试。", 3);
        $DB_Order = D('Service');
        $data = $DB_Order -> find("`id`='{$id}'");

        //权限
        $can_do = $this -> get('can_do');
        if(!($data['uid'] == Session::get('uid') && $data['audit'] <= 0) && !isset($can_do[$this->mname]['delete']))
            $this -> success('您无权执行该操作！', 3, 'Index/index');

        if($DB_Order -> delete("`id`='{$id}'"))
        {

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
            $DB_Order = D('Service');
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


        $this -> display("audit");
    }
    //over


    //审核通过
    protected function pass()
    {
        //原始数据
        $id = (int)$_POST['id'];
        if(!$id) $this -> success("数据访问错误，请重试。", 3);
        $DB_Order = D('Service', true);
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
                $num = isset($group[$v][$key]) ? ((float)$group[$v][$key]['num'] + (float)$_POST['num'][$key][$k]) : (float)$_POST['num'][$key][$k];

                list($goods, $list) = explode('_', $key);
                $group[$v][$key] = array('goods'=>$goods, 'num'=>$num);
            }
        }
        //if(!$group) $this -> success('操作失败，请重试！',3);

        //生成数据
        $DB_Stock = D('Stock');
        $data = array();
        foreach($group as $key => $value)
        {
            //$key 是仓库id
            foreach($value as $k=>$v)
            {
                $num = abs($v['num']);
                if($num)
                {
                    $_goods = $order_goods[$k];
                    $data[] = array(
                        'goods'     => $v['goods'],
                        'goods_name' => $allgoods[$v['goods']]['name'],
                        'group'     => $key,
                        'num'       => $this -> abs ? $num : -$num,
                        'price'     => ($_goods['price']*$num) * ($this -> abs ? -1 : 1),
                        'time'      => NOW,
                        'uid'       => $order['uid'],
                        'rtime'     => NOW,
                        'comment'   => str_replace('单', '', $this -> title) . ' ('.$order['num'].')',
                        'order'     => $order['id'],
                        'audit'     => 0,
                        'come'      => 'service',
                    );
                    //检查库存
                    $condition = "`audit` != '0' AND `group` = '{$key}' AND `goods` = '{$v['goods']}'";
                    $sum = $DB_Stock -> sum($condition, 'num');
                    if(!$this -> abs && $sum < $num)
                        $this -> success('库存不足，无法分配！', 3);
                }
            }
        }

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
        $DB_Stock = D('stock');
        if(!$DB_Stock -> addAll($data))
        {
            $this -> success('操作失败，请重试！',3);
        }

        $error = '';
        //需要记账
        if($this -> price && $this -> mname != 'Morder') //生产入库，不记账
        {
            $_POST['price'] = $this -> abs ? -abs(inputPrice($_POST['price'])) : abs(inputPrice($_POST['price']));
            $data = array(
                'name'     => str_replace('单', '', $this -> title) . ' ('.$order['num'].')',
                'price'    => $_POST['price'],
                'time'     => NOW,
                'arrive'   => 0,
                'notto'    => $_POST['price'],
                'uid'      => $order['uid'],
                'rtime'    => NOW,
                'order'    => $order['id'],
                'income'   => $_POST['income2']*100,
                'audit'    => 0,
                'come'     => 'service',
                'cors'     => $order['cors'],
                'corstable'=> 'client',
                //'way'       => $order['way'],
                //'bank'      => $order['bank'],
            );
            $DB_Financial = D('Financial');
            if(!$DB_Financial -> add($data))
            {
                $DB_Stock -> deleteAll("`order` = '{$order['id']}'");
                $$this -> success('财务收支提交失败，请重试！', 3, $this->mname.'/audit');
            }
        }

        $DB_Order = D('Service');
        if($DB_Order -> save(array('audit'=>Session::get('uid'), 'express'=>$_POST['express'], 'code'=>$_POST['code'], 'comment'=>$_POST['comment'], 'sort1'=>$_POST['sort1'],  'income1'=>$_POST['income1']*100, 'sort2'=>$_POST['sort2'],  'income2'=>$_POST['income2']*100), "`id`='{$order['id']}'"))
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
    public function over($id)
    {
        if(!isset($id)) $this -> redirect('index', $this->mname);

        $DB_Order = D('Service');
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
        $financial = $DB_Financial -> find("`order`='{$data['id']}'");
        if(!$financial || ($financial['notto'] == 0 && $financial['audit'] != 0))
        {
            $DB_Order = D('Service');
            $DB_Order -> save(array('over'=>1), "`id`='{$id}'");
            return true;
        }
        return false;

    }
    //over


    //单据记录列表 内部用
    public function getlist($condition = '')
    {
        if($condition) $condition .= " AND `type` = '{$this->type}'";
        else $condition = "`type` = '{$this->type}'";

        $total = D("Service") -> sum($condition, 'total');
        $taxTotal = D("Service") -> sum($condition, 'tax_total');
        $income = D("Service") -> sum($condition, 'income2');

        $DB_Order = D('Service', true);
        $DB_Order -> link('user', 'HAS_ONE', 'uid', 'user', 'uid', 'uid,name');
        $DB_Order -> link('cors', 'HAS_ONE', 'cors', $this->abs ? 'Supplier' : 'Client', 'id', 'id,name');
        $DB_Order -> link('sorts', 'HAS_ONE', 'sort1', 'System_config', 'id', 'id,intro');

        import('Extend.Util.Page');
        $Page = new Page($DB_Order->count($condition), 15);
        $limit =  $Page->firstRow.','.$Page->listRows;
        $list = $DB_Order -> xfindAll($condition, '*', 'time Desc', $limit);
        return array('list' => $list, 'page' => $Page -> show(), 'total'=>$total, 'taxTotal'=> $taxTotal, 'income'=>$income);
    }
    //over


    //邮件发送
    protected function mail($type, $arg, $a='view')
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
                                '。单号：<a href="'.__APP__.'/'.$this->mname.'/'.$a.'/id/'.$arg['id'].'">《'.$arg['num'].'》</a>',
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
                                    '。<a href="'.__APP__.'/'.$this->mname.'/'.$a.'/id/'.$arg['id'].'">《'.$arg['num'].'》</a>',
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
                    'content' => $arg['user']['name'].'提交了新的“'.$this -> title.'”需要您审批，单号：<a href="'.__APP__.'/'.$this->mname.'/'.$a.'/id/'.$arg['id'].'">《'.$arg['num'].'》</a>',
                    'read'    => 0,
                    'reply'   => 0,
                    'time'    => NOW,
                    'system'  => 1,
                );
                $mails = $this -> mailUser($this->mname, 'audit', $mail);

                $DB_Mail = D('user_mail');
                return $DB_Mail -> addAll($mails);

            case 'alertsend': //发货提醒
                //准备邮件
                $mail = array(
                    'uid'     => Session::get('uid'),
                    'title'   => '售后服务'. $arg['num'] .'的发货提醒',
                    'content' => '售后服务'. $arg['num'] .'需要发货，查看：<a href="'.__APP__.'/'.$this->mname.'/'.$a.'/id/'.$arg['id'].'">《'.$arg['num'].'》</a>',
                    'read'    => 0,
                    'reply'   => 0,
                    'time'    => $arg['send'],
                    'system'  => 1,
                );

                $DB_Mail = D('user_mail');
                return $DB_Mail -> add($mail);
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
        /**
        $DB_Role = D('role_node', true);
        $DB_Role -> link('users', 'HAS_MANY', 'role', 'user_role', 'role', 'uid,role,time', '`time` > \''.NOW.'\' OR `time` = \'0\'');
        $roles = $DB_Role -> xfindAll("`node` = '{$node['id']}'");
        */
        $roles = D("Role_node") -> findAll("`node` = '{$node['id']}'");
        $mails = array();
        foreach($roles as $value)
        {
            $value['users'] = D("User_role") -> findAll("`role`={$value['role']}"); //-> query("select b.* from user_role a  where `role`={$value['role']}");
            foreach($value['users'] as $v)
            {
                $mail['uid'] = $v['uid'];
                $mails[] = $mail;
            }
        }
        return $mails;
    }



    //配件管理
    public function parts()
    {
        if(Session::get('uid') == 1)
            $condition = "1";
        else
            $condition = "`uid`=".Session::get('uid');

        $condition .= trim($_GET['search']) ? " AND `operator` LIKE '%{$name}%'" : '';
        $DB = D('Parts');
        import('Extend.Util.Page');
        $Page = new Page($DB->count($condition), 15);
        $list = $DB -> findAll($condition, '*', 'id desc', $Page->firstRow.','.$Page->listRows);
        $this -> assign('page', $Page -> show());
        $this -> assign('list', $list);

        $this -> assign('search', trim($_GET['search']));

        if($_GET['p'])
            Cookie::set('partsPage', $_GET['p']);
        else
            Cookie::set('partsPage', 1);

        $this -> display();
    }


    //添加配件
    public function addParts()
    {
        if($_POST)
        {
            if(!$_POST['operator'])
                $this -> success("领用人不能为空！", 2);

            $DB_Goods = D('Goods');
            $DB_Group = D('Group');
            $goods = $stock = $in = array();
            foreach($_POST['goods'] as $key => $value)
            {
                if($value && (float)$_POST['goods_num'][$key])
                {
                    $num = abs($_POST['goods_num'][$key]);

                    $_goods = $DB_Goods -> find("`id`='{$value}'");
                    $goods[$value] = array(
                        'goods' => $value,
                        'num'   => $num,
                        'name'  => $_goods['name'],
                        'bad'   => abs($_POST['goods_bad'][$key]),
                        'out_group' => $_POST['out_group'][$key],
                        'in_group' => $_POST['in_group'][$key],
                        'com'   => $_POST['goods_com'][$key],
                        'unit'  => $_goods['unit'],
                        'model' => $_goods['model'],
                    );

                    //耗用
                    $in[$value] = array(
                        'goods'     => $value,
                        'goods_name'=> $_goods['name'],
                        'group'     => $_POST['in_group'][$key],
                        'num'       => abs($_POST['goods_bad'][$key]),
                        'price'     => 0,
                        'time'      => strtotime($_POST['time']),
                        'uid'       => Session::get('uid'),
                        'rtime'     => NOW,
                        'comment'   => '耗用--'.$_POST['goods_com'][$key],
                    );

                    //出库
                    $stock[$value] = array(
                        'goods'     => $value,
                        'goods_name'=> $_goods['name'],
                        'group'     => $_POST['out_group'][$key],
                        'num'       => -$num,
                        'price'     => 0,
                        'time'      => strtotime($_POST['time']),
                        'uid'       => Session::get('uid'),
                        'rtime'     => NOW,
                        'comment'   => $_POST['goods_com'][$key],
                    );

                    $condition = "`audit` != '0' AND `group` = {$_POST['out_group'][$key]} AND `goods` = '{$value}'";
                    $sum = D('stock') -> sum($condition, 'num');
                    if($sum < $num)
                    {
                        $group = $DB_Group -> find("`gid`={$_POST['out_group'][$key]}");
                        $this -> success('“'.$group['name'].'”仓库“'.$_goods['name'].'”产品数量不足！', 3);
                    }
                }
            }
            if(!$goods) $this -> success('没有添加相关货品，或数据不正确！', 3);

            $data = array(
                'operator' => $_POST['operator'],       //领用人
                'time'     => strtotime($_POST['time']),
                'status'   => 1,
                'comment'  => $_POST['comment'],
                'goods'    => serialize($goods),
                'stock'    => serialize($stock), //库存出入记录
                'uid'      => Session::get('uid'),
                'addtime'  => NOW,
                'audit'    => 0,
            );


            $id = D('Parts') -> add($data);

            if(false !== $id)
            {
                $data['id'] = $id;
                $data['user'] = array('uid'=>Session::get('uid'), 'name'=>Session::get('name'));
                $data['num'] = $data['comment'];
                $this -> title = '配件领用';
                $this -> mail('request', $data);

                //修改耗用入库
                $inStock = array();
                foreach ($in as $k=>$v)
                {
                    $v['order'] = $id;
                    $v['come'] = 'parts';
                    $inStock[] = $v;
                }
                D("Stock") -> addAll($inStock);

                $this -> success('添加成功', 3, $this->mname.'/parts');
            }else{
                $this -> success('添加失败，请重试', 3);
            }
        }

        $this -> getGroup();

        $this -> display('addParts');
    }


    //修改配件
    public function editParts()
    {
        if($_POST)
        {
            if(!$_POST['id'])
                $this -> success("数据错误, 请重试", 2);

            $DB_Goods = D('Goods');
            $goods = $stock = array();
            foreach($_POST['goods'] as $key => $value)
            {
                if($value && (float)$_POST['goods_num'][$key])
                {
                    $num = abs((float)$_POST['goods_num'][$key]);

                    $_goods = $DB_Goods -> find("`id`='{$value}'");
                    $goods[$value] = array(
                        'goods' => $value,
                        'num'   => $num,
                        'name'  => $_goods['name'],
                        'bad'   => abs((float)$_POST['goods_bad'][$key]),
                        'out_group' => $_POST['out_group'][$key],
                        'in_group' => $_POST['in_group'][$key],
                        'com'   => $_POST['goods_com'][$key],
                        'unit'  => $_goods['unit'],
                        'model' => $_goods['model'],
                    );

                    //耗用
                    $in[$value] = array(
                        'goods'     => $value,
                        'goods_name'=> $_goods['name'],
                        'group'     => $_POST['in_group'][$key],
                        'num'       => abs($_POST['goods_bad'][$key]),
                        'price'     => 0,
                        'time'      => strtotime($_POST['time']),
                        'uid'       => Session::get('uid'),
                        'rtime'     => NOW,
                        'comment'   => '耗用--'.$_POST['goods_com'][$key],
                    );

                    $stock[$value] = array(
                        'goods'     => $value,
                        'goods_name'=> $_goods['name'],
                        'group'     => $_POST['out_group'][$key],
                        'num'       => -$num,
                        'price'     => 0,
                        'time'      => strtotime($_POST['time']),
                        'uid'       => Session::get('uid'),
                        'rtime'     => NOW,
                        'comment'   => $_POST['goods_com'][$key],
                    );

                    $condition = "`audit` != '0' AND `group` = {$_POST['goods_group'][$key]} AND `goods` = '{$value}'";
                    $sum = D('stock') -> sum($condition, 'num');
                    if($sum < $num)
                    {
                        $DB_Group = D('Group');
                        $group = $DB_Group -> find("`gid`={$_POST['goods_group'][$key]}");

                        $this -> success('“'.$group['name'].'”仓库“'.$_goods['name'].'”产品数量不足！', 3);
                    }
                }
            }
            if(!$goods) $this -> success('没有添加相关货品，或数据不正确！', 3);

            $data = array(
                'operator' => $_POST['operator'],       //领用人
                'time'     => strtotime($_POST['time']),
                'comment'  => $_POST['comment'],
                'goods'    => serialize($goods),
                'stock'    => serialize($stock), //库存出入记录
            );


            $result = D('Parts') -> where("`id`=".(int)$_POST['id']) -> save($data);

            if(false !== $id)
            {
                $data['id'] = (int)$_POST['id'];
                $data['user'] = array('uid'=>Session::get('uid'), 'name'=>Session::get('name'));
                $data['num'] = $data['comment'];
                $this -> title = '配件领用';
                $this -> mail('request', $data, 'viewParts');

                D("Stock") -> delete("`order`='{$_POST['id']}' AND `come`='parts'");

                //修改耗用入库
                $inStock = array();
                foreach ($in as $k=>$v)
                {
                    $v['order'] = (int)$_POST['id'];
                    $v['come'] = 'parts';
                    $inStock[] = $v;
                }
                D("Stock") -> addAll($inStock);
                $page = Cookie::get('partsPage');
                $this -> success('操作成功', 3, $this->mname.'/parts/p/'.$page);
            }else{
                $this -> success('操作失败，请重试', 3);
            }
        }

        if((int)$_GET['id'] < 1)
            $this -> success("未选择需修改的数据", 2);

        $parts = D("parts") -> find("`id`=".(int)$_GET['id']);
        $parts['goods'] = unserialize($parts['goods']);
        $this -> assign($parts);

        $this -> getGroup();

        $this -> display('editParts');
    }


    //删除配件
    public function deleteParts()
    {
        if(!$id) $id = (int)$_GET['id'];
        if(!$id) $this -> success("数据访问错误，请重试。", 3);
        $DB = D('parts');
        $data = $DB -> find("`id`='{$id}'");

        //权限
        $can_do = $this -> get('can_do');
        if(!($data['uid'] == Session::get('uid') && $data['audit'] <= 0) && !isset($can_do[$this->mname]['deleteParts']))
            $this -> success('您无权执行该操作！', 3, 'Index/index');

        if($DB -> delete("`id`='{$id}'"))
        {
            D("Stock") -> delete("`order`={$id} AND (`come`='parts' OR `come`='back')");
            $this -> success('删除成功！', 2, $this->mname.'/parts');
        }else{
            $this -> success('操作失败，请重试！',3);
        }
    }


    //退回
    public function backParts()
    {
        if((int)$_GET['id'] < 1)
            $this -> error('未选择需退回的配件');

        $parts = D("Parts") -> find("`id`=".(int)$_GET['id']);
        if(!$parts)
            $this -> error('该记录已被删除');

        if($parts['audit'] < 1 || $parts['status']==2)
            $this -> error("无需退回");

        $goods = unserialize($parts['goods']);
        $stock = unserialize($parts['stock']);

        $backStock = array();
        foreach ($stock as $key => $val)
        {
            $val['num'] = abs($goods[$val['goods']]['bad'] + $val['num']);
            if($val['num'] <= 0)
                continue;

            $val['order'] = $parts['id'];
            $val['come'] = "back";
            $val['time'] = $val['rtime'] = NOW;
            $val['comment'] = '退回--'.$val['comment'];

            $backStock[] = $val;
        }

        $result = $backStock ? D("Stock") -> addAll($backStock) : true;
        if(false !== $result)
        {
            D("Parts") -> where("`id`=".$parts['id']) -> setField('status', 2);
            $this -> success("操作成功", 2, $this->mname.'/parts');
        }else{
            $this -> success("操作失败");
        }
    }


    //查看配件
    public function viewParts()
    {
        if((int)$_GET['id'] < 1)
            $this -> success("未选择需修改的数据", 2);

        $parts = D("parts") -> find("`id`=".(int)$_GET['id']);
        $parts['goods'] = unserialize($parts['goods']);
        $this -> assign($parts);

        $_groups = D("Group") -> findAll();
        $groups = array();
        foreach ($_groups as $key=>$val)
        {
            $groups[$val['gid']] = $val;
        }
        $this -> assign('groups', $groups);

        $this -> display('viewParts');
    }


    //打印配件
    public function printParts()
    {
        if((int)$_GET['id'] < 1)
            $this -> success("未选择需修改的数据", 2);

        $parts = D("parts") -> find("`id`=".(int)$_GET['id']);
        $parts['goods'] = unserialize($parts['goods']);
        $parts['stock'] = unserialize($parts['stock']);
        $this -> assign($parts);

        //仓库
        $_groups = D("Group") -> findAll();
        $groups = array();
        foreach ($_groups as $key=>$val)
        {
            $groups[$val['gid']] = $val;
        }
        $this -> assign('groups', $groups);

        $goodsId = array();
        foreach ($parts['goods'] as $key=>$val)
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

        $this -> display('printParts');
    }


    //审核配件
    public function auditParts()
    {
        if($_POST)
        {
            if((int)$_POST['id'] < 1)
                $this -> error('');

            $parts = D("Parts") -> find("`id`=".(int)$_POST['id']);
            if(!$parts)
                $this -> error();

            $audit = $_POST['type'] == '审核通过' ? session::get('uid') : -1;
            $result = D("Parts") -> where("`id`=".(int)$_POST['id']) -> setField('audit', $audit);
            if(false !== $result)
            {
                $stock = unserialize($parts['stock']);
                //添加出入库记录
                if($audit > 0 && $stock)
                    D("Stock") -> addAll($stock);

                $this -> success("操作成功", 3, $this->mname.'/auditParts');
            }else{
                $this -> error("操作失败");
            }
        }

        $condition = "`audit`=0";

        $DB = D('Parts');
        import('Extend.Util.Page');
        $Page = new Page($DB->count($condition), 15);
        $list = $DB -> findAll($condition, '*', 'id desc', $Page->firstRow.','.$Page->listRows);
        $this -> assign('page', $Page -> show());
        $this -> assign('list', $list);

        $this -> display();
    }


    public function printService()
    {
        //获取数据
        if(!$id) $id = (int)$_GET['id'];

        //订单信息
        $DB_Order = D('Service', true);
        $DB_Order -> link("client", "HAS_ONE", 'cors', 'Client', 'id', '*');
        $DB_Order -> link("user", "HAS_ONE", 'uid', 'User', 'uid', '*');
        $DB_Order -> link("aud", "HAS_ONE", 'audit', 'User', 'uid', '*');
        $DB_Order -> link("sorts", "HAS_ONE", 'sort1', 'System_config', 'id', '*');
		$order = $DB_Order -> xfind("`id`='{$id}'", '*');

        //订单包含产品
        $_goods = unserialize($order['goods']);
        $order['goods'] = $_goods;

        $order['cMoney'] = $this -> getRMB(($order['total']+$order['tax_total'])/100);
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

        $this -> display('print');
    }

    function getRMB($price)
    {
        $arr1 = array('零','壹','贰','叁','肆','伍','陆','柒','捌','玖');
        $arr2 = array('拾','佰','仟');
        $arr = explode(".", $price);

        $rmb_len=strlen($arr[0]); //整数部分
        $j=0;
        for ($i=0; $i<$rmb_len; $i++)
        {
            $bit = $arr[0][$rmb_len-$i-1];
            $cn = $arr1[$bit];
            $unit = $arr2[$j];
            if ($i==0) {
                $re=$cn;
            } elseif ($i==4){
                $re=$cn."万".$re;
                $j=0;
            } elseif ($i==8) {
                $re=$cn."亿".$re;
                $j=0;
            }else{
                $j++;
                $re = $bit==0 ? "零".$re : $cn.$unit.$re;
            }
        }

        if ($arr[1])
        {
            $arr[1][0]==0?$re=$re."元零":$re=$re."元".$arr1[$arr[1][0]]."角"; //角
            $arr[1][1]==0?$re=$re."零分":$re=$re.$arr1[$arr[1][1]]."分";      //分
        }

        $re=preg_replace(array("/(零)+$/","/(零)+/","/零万/","/零亿/"),array("","零","万","亿"),$re); //替换一些数据
        $arr[1] ? $re : $re .= "元整";
        return $re;
    }
}