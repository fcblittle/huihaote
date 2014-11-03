<?php
class SplitAction extends Action
{
    protected $mname = 'Split';   //模块名称

    protected $type = 'split';

    protected $title = '拆分';

    protected $abs = false;         //供应商1  客户0

    protected $price = false;        //生产记账

    protected $pinyin = 'cfgl';     //生成单号用

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
            Cookie::set('splitPage', $_GET['p']);
        else
            Cookie::set('splitPage', 1);

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

            //货品
            $goods = array();
            foreach($_POST['goods'] as $key => $value)
            {
                if($value && (float)$_POST['goods_num'][$key])
                $goods[$value] = array(
                    'goods' => $value,
                    'num'   => abs((float)$_POST['goods_num'][$key]),
                    'com'   => $_POST['goods_com'][$key],
                );
            }
            unset($key, $value);

            //原材料
            $material = array();
            foreach($_POST['material']['goods'] as $key => $value)
            {
                if($value && (float)$_POST['material']['used'][$key])
                    $material[$value] = array(
                        'goods'     => $value,
                        'used'      => abs($_POST['material']['used'][$key]),
                        'com'       => $_POST['material_com'][$key],
                    );
            }

            //if(!$goods) $this -> success('没有添加相关货品，或数据不正确！', 3);
            $DB = D('Split');
            //订单号唯一检查 并生成唯一值
            if($DB -> find("`num` = '{$_POST['num']}' AND `type`='{$this->type}'"))
            {
                $postNum = explode('-', $_POST['num']);
                $num_prefix = $postNum[0].'-'.substr($postNum[1], 0, 8);
    			$expenses = $DB -> order("`num` desc") -> find("`type`='{$this->type}' AND `num` LIKE '$num_prefix%'");
    			$ids = explode('-', $expenses['num']);
    		    $seq = substr($ids[1], 8);
    		    $seq = $seq ? str_pad((int)$seq+1, 4, '0', STR_PAD_LEFT) : '0001';
    		    $_POST['num'] = $num_prefix.$seq;
            }


            $data = array(
                'num'      => $_POST['num'],
                //'cors'     => $_POST['cors'],
                'type'     => $this -> type,
                'goods'    => serialize($goods),
                'used'     => serialize($material),
                'comment'  => $_POST['comment'],
                'uid'      => Session::get('uid'),
                'time'     => $_POST['time'] ? strtotime($_POST['time']) : NOW,
                'audit'    => 0,
            );

            $DB_Order = D('Split');
            $id = $DB_Order -> add($data);
            if($id)
            {
                $data['id'] = $id;
                $data['user'] = array('uid'=>Session::get('uid'), 'name'=>Session::get('name'));
                $this -> mail('request', $data);
                $this -> success('添加成功', 3, $this->mname.'/add');
            }else{
                $this -> success('添加失败，请重试', 3);
            }
        }

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

		//唯一单号
        $DB = D('Split');
        $num_prefix = $this->pinyin . '-' . date('Ymd');
		$expenses =$DB -> order("`num` desc") -> find("`type`='{$this->type}' AND `num` LIKE '$num_prefix%'");
		$ids = explode('-', $expenses['num']);
	    $seq = substr($ids[1], 8);
	    $seq = $seq ? str_pad((int)$seq+1, 4, '0', STR_PAD_LEFT) : '0001';
	    $num = $num_prefix.$seq;
        $this -> assign('num', $num);

		$this -> assign('time', time());

        $this -> display("add");

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

            $DB_Order = D('Split');
            $data = $DB_Order -> find("`id`='{$id}'");
            $audit = $data['audit'] < 0 ? 0 : $data['audit'];

            //权限
            $can_do = $this -> get('can_do');
            if(!($data['uid'] == Session::get('uid') && $data['audit'] <= 0) && !isset($can_do[$this->mname]['edit']))
                $this -> success('您无权执行该操作！', 3, 'Index/index');

            //处理数据
            $goods = array();
            foreach($_POST['goods'] as $key => $value)
            {
                if($value && (float)$_POST['goods_num'][$key])
                    $goods[$value] = array(
                        'goods' => $value,
                        'num'   => abs((float)$_POST['goods_num'][$key]),
                        'com'   => $_POST['goods_com'][$key],
                    );
            }
            if(!$goods) $this -> success('没有添加相关货品，或数据不正确！', 3);

            //原材料
            $material = array();
            foreach($_POST['material']['goods'] as $key => $value)
            {
                if($value && (float)$_POST['material']['used'][$key])
                    $material[$value] = array(
                        'goods'     => $value,
                        'used'      => (float)$_POST['material']['used'][$key],
                        'com'       => $_POST['material_com'][$key],
                    );
            }

            $data = array(
                'id'       => $id,
                'num'      => $_POST['num'],
                //'cors'     => $_POST['cors'],
                //'type'     => $this -> type,
                'goods'    => serialize($goods),
                'used'     => serialize($material),
                'comment'  => $_POST['comment'],
                'audit'    => $audit,
                //'time'     => $_POST['time'] ? strtotime($_POST['time']) : NOW,
            );

            if($DB_Order -> save($data))
            {
                $data['user'] = array('uid'=>Session::get('uid'), 'name'=>Session::get('name'));
                //$this -> mail('request', $data);
                $url = isset($_GET['url']) ? $_GET['url'] : 'index';
                $this -> assign('url', $url);
                $page = Cookie::get('splitPage');
                $this -> success('保存成功！', 2, $this->mname.'/'.$url.'/p/'.$page);
            }else{
                $this -> success('保存失败，请重试', 3);
            }
        }
        //over post

        //获取数据
        if(!$id) $id = (int)$_GET['id'];
        $DB_Order = D('Split');
        $data = $DB_Order -> find("`id`='{$id}'");
        $data['goods'] = unserialize($data['goods']);
        $data['used'] = unserialize($data['used']);


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

        $this -> display('edit');
        exit;
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
        $DB_Order = D('Split', true);
        $DB_Order -> link('user', 'HAS_ONE', 'uid', 'user', 'uid', 'uid,name');
        $DB_Order -> link('cors', 'HAS_ONE', 'cors', $this->abs ? 'Supplier' : 'Client', 'id', 'id,name');
		$data = $DB_Order -> xfind("`id`='{$id}'");
        //权限
        $can_do = $this -> get('can_do');
        if(!($data['uid'] == Session::get('uid')) && !isset($can_do[$this->mname]['view']))
            $this -> success('您无权执行该操作！', 3, 'Index/index');

        $data['goods'] = unserialize($data['goods']);

        $data['used'] = unserialize($data['used']);
        $this -> assign('needprice', 0);

        $this -> assign($data);

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
            $stock = $DB_Stock -> xfindAll("`order`='{$data['id']}'");
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
            $financial = $DB_Financial -> find("`order`='{$data['id']}'");
            $this -> assign('money', $financial);
        }

        $url = isset($_GET['url']) ? $_GET['url'] : 'index';
        $this -> assign('url', $url);

        $this -> display('view');
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
        $DB_Order = D('Split');
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
            {
                $this -> edit((int)$_GET['edit']);
            }else
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
            $DB_Order = D('Split');
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

        //A('Service') -> getGroup();

        $this -> assign('tname', $this -> title);

        $this -> display("audit");
    }
    //over


    //申和通过
    protected function pass()
    {
        //原始数据
        $id = (int)$_POST['id'];
        if(!$id) $this -> success("数据访问错误，请重试。", 3);
        $DB_Order = D('Split', true);
        $DB_Order -> link('user', 'HAS_ONE', 'uid', 'user', 'uid', 'uid,name');
        $order = $DB_Order -> xfind("`id` = '{$id}'");

        //出入库 记录
        $_used = unserialize($order['used']);
        $_goods = unserialize($order['goods']);

        $stock = array();
        //原材料 入库
        foreach ($_used as $goods => $val)
        {
            $_good = D("Goods") -> find("`id`='{$goods}'");
            foreach($_POST['in_group'][$goods] as $k=>$v)
            {
                $_used[$goods]['stock'][] = array('group'=>$v, 'num'=>abs($_POST['in_num'][$goods][$k]));

                $stock[] = array(
                    'goods'     => $goods,
                    'goods_name'=> $_good['name'],
                    'group'     => $v,
                    'num'       => abs($_POST['in_num'][$goods][$k]),
                    'price'     => 0,
                    'time'      => $order['time'],
                    'uid'       => Session::get('uid'),
                    'rtime'     => NOW,
                    'comment'   => '拆分--原材料--'.$val['com'],
                    'order'     => $order['id'],
                    'come'      => 'split',
                );
            }
        }

        //成品 入库
        foreach ($_goods as $goods => $val)
        {
            $_good = D("Goods") -> find("`id`='{$goods}'");
            foreach($_POST['out_group'][$goods] as $k=>$v)
            {
                $sum = D("Stock") -> sum("`goods`='{$goods}' AND `group`={$v}", 'num');
                if($sum < abs($_POST['out_num'][$goods][$k]))
                {
                    $group = D("Group") -> where("`gid`={$v}") -> find();
                    $this -> success("{$group['name']} 仓库 {$_good['name']} 数量不足 ".abs($_POST['out_num'][$goods][$k]));
                }

                $_goods[$goods]['stock'][] = array('group'=>$v, 'num'=>abs($_POST['out_num'][$goods][$k]));
                $stock[] = array(
                    'goods'     => $goods,
                    'goods_name'=> $_good['name'],
                    'group'     => $v,
                    'num'       => -abs($_POST['out_num'][$goods][$k]),
                    'price'     => 0,
                    'time'      => $order['time'],
                    'uid'       => Session::get('uid'),
                    'rtime'     => NOW,
                    'comment'   => '拆分--成品--'.$val['com'],
                    'order'     => $order['id'],
                    'come'      => 'split',
                );
            }
        }

        D("Stock") -> addAll($stock);

        $DB_Order = D('Split');
        if($DB_Order -> save(array('audit'=>Session::get('uid'), 'used'=>serialize($_used), 'goods'=>serialize($_goods)), "`id`='{$order['id']}'"))
        {
            $order['audit'] = 1;
            $this -> mail('feedback', $order);
            $this -> success('审核成功！', 3, $this->mname.'/audit');
        }else{
            $this -> success('审核结果提交失败，请重试！',3);
        }
    }
    //over


    //单据记录列表 内部用
    public function getlist($condition = '')
    {
        if($condition) $condition .= " AND `type` = '{$this->type}'";
        else $condition = "`type` = '{$this->type}'";

        $DB_Order = D('Split', true);
        $DB_Order -> link('user', 'HAS_ONE', 'uid', 'User', 'uid', 'uid,name');
        $DB_Order -> link('cors', 'HAS_ONE', 'cors', $this->abs ? 'Supplier' : 'Client', 'id', 'id,name');

        import('Extend.Util.Page');
        $Page = new Page($DB_Order->count($condition), 15);
        $limit =  $Page->firstRow.','.$Page->listRows;
        $list = $DB_Order -> xfindAll($condition, 'id,num,cors,type,uid,time,audit,comment', 'time Desc', $limit);
        return array('list' => $list, 'page' => $Page -> show());
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
                        'title'   => '“'.$this -> title.'”审核通过',
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