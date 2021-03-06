<?php
/**
 * 财务管理Action
 *
 * @author Page7
 * @category Police OA 2012 pack1
 * @copyright Copyright(c) 2011 Interidea.org
 * @version $Id$
 */
class FinancialAction extends Action
{
    public function _initialize()
    {
        include_once(LIB_PATH.'_initialize.php');
    }



    /**
     + -----------------------------------------------------------------------
     *  财务列表
     + -----------------------------------------------------------------------
     */
    public function index()
    {
        $condition = array();
        if(isset($_GET['come']) && $_GET['come'] != 1)
        {
            if($_GET['come'] == 'order1')
            {
                $condition[] = "`come`='order' AND `name` LIKE '%cgrk%'";
            }elseif($_GET['come'] == 'order2'){
                $condition[] = "`come`='order' AND `name` LIKE '%xsck%'";
            }elseif($_GET['come'] == 'return1'){
                $condition[] = "`come`='return' AND `name` LIKE '%cgrk%'";
            }elseif($_GET['come'] == 'return2'){
                $condition[] = "`come`='return' AND `name` LIKE '%xsck%'";
            }else{
                $condition[] = "`come`='".$_GET['come']."'";
            }
        }
        $this -> assign('come', $_GET['come']);

        //供应商、客户搜索
        if(isset($_GET['corstable']) && in_array($_GET['corstable'], array('client', 'supplier')))
        {
            $condition[] = "`corstable`='{$_GET['corstable']}'";

            if(isset($_GET['corsname']) && trim($_GET['corsname']))
            {
                $corname = trim($_GET['corsname']);
                $cors = D(ucfirst($_GET['corstable'])) -> field('id') -> where("`name` LIKE '%{$corname}%'") -> findAll();
                if($cors)
                {
                    $corsid = array();
                    foreach ($cors as $v) {
                        $corsid[] = $v['id'];
                    }
                    $condition[] = "`cors` IN (". implode(',', $corsid) .")";
                }else{
                    $condition[] = "`cors`='0'";
                }
            }
        }
        $this -> assign('corstable', $_GET['corstable']);
        $this -> assign('corsname', $_GET['corsname']);

        if(isset($_GET['status']) && $_GET['status'])
        {
            if($_GET['status'] == 1)
                $condition[] = "`audit`>0";
            else
                $condition[] = "`audit`=0";
        }
        $this -> assign('status', $_GET['status']);

        if(isset($_GET['bill']) && $_GET['bill'])
        {
            $condition[] = "`bill`='". $_GET['bill'] ."'";
        }
        $this -> assign('bill', $_GET['bill']);

        if(isset($_GET['price']) && $_GET['price'])
                $condition[] = "`price` = '".((float)$_GET['price']*100)."'";

        $time1 = $time2_end = '';
		if(isset($_GET['start']) && $_GET['start']&&isset($_GET['end']) && $_GET['end'])
		{
				$time1 =  strtotime($_GET['start']);
				$time2_end = strtotime($_GET['end'])+24*60*60-1;
				$condition[] =" `time` BETWEEN {$time1} AND {$time2_end}";
		}
		$this -> assign('start', $time1);
		$this -> assign('end', $time2_end);
        if($condition) $condition = implode(' AND ', $condition);

        //var_dump($condition); exit;
        $DB_Financial = D('Financial', true);
        $DB_Financial -> link('user', 'HAS_ONE', 'uid', 'user', 'uid', 'uid,name');

        //所有供应商客户
        $_allSupplier = D("Supplier") -> field("id, name") -> findAll();
        $allSupplier = array();
        foreach ($_allSupplier as $key => $val) {
            $allSupplier[$val['id']] = $val['name'];
        }
        $_allClient = D("Client") -> field("id, name") -> findAll();
        $allClient = array();
        foreach ($_allClient as $key => $val) {
            $allClient[$val['id']] = $val['name'];
        }
        $allCors = array('supplier'=>$allSupplier, 'client'=>$allClient);

        //导出
        if(isset($_GET['ac']) && $_GET['ac']=='导 出')
        {
            $list = $DB_Financial -> xfindAll($condition, 'id,uid,name,price,notto,audit,time,income,bill,billnum,cors,corstable', 'time desc, id desc');
            foreach ($list as $key=>$val)
            {
                $list[$key]['money'] = showPrice(abs($val['price'])-abs($val['notto']));
                $list[$key]['income'] = showPrice($val['income']);
                if($val['audit'] == 0)
                    $list[$key]['status'] = '未审核';
                else{
                    if($val['notto'] !== 0)
                    {
                        $list[$key]['status'] = "欠账：".showPrice($val['notto']);
                    }else{
                        $list[$key]['status'] = "已".($val['price'] > 0) ? '到账' : '付清';
                    }
                }

                $list[$key]['made'] = $val['user']['name'];
                $list[$key]['time'] = date('Y-m-d', $val['time']);
                $list[$key]['corsname'] = ($val['corstable'] && $val['cors']) ? $allCors[$val['corstable']][$val['cors']] : '';
            }

            $titles = array('A'=>'收支内容', 'B'=>'收支金额', 'C'=>'提成', 'D'=>'做账日期', 'E'=>'到账情况', 'F'=>'做账人', 'G'=>'发票', 'H'=>'发票编号');
            $fields = array('A'=>'name', 'B'=>'money', 'C'=>'income', 'D'=>'time', 'E'=>'status', 'F'=>'made', 'G'=>'bill', 'H'=>'billnum');

            $this -> sqlToExcel($list, $fields, $titles);
            exit;
        }else{
            import('Extend.Util.Page');
            $Page = new Page($DB_Financial->count($condition), 20);
            $list = $DB_Financial -> xfindAll($condition, 'id,uid,name,price,notto,audit,time,income,bill,billnum,cors,corstable', 'time desc, id desc', $Page->firstRow.','.$Page->listRows);
            $page = $Page -> show();
            $this -> assign('page', $page);
            $this -> assign('list', $list);

            $this -> assign('allCors', $allCors);

            if($_GET['p'])
                Cookie::set('financialPage', $_GET['p']);
            else
                Cookie::set('financialPage', 1);

            if($_GET['ac'] == '打 印')
                $this -> display('printIndexList');
            else
                $this -> display();
        }
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
     + -----------------------------------------------------------------------
     *  财务添加
     + -----------------------------------------------------------------------
     */
    public function add()
    {
        if($_POST)
        {
            if(!$_POST['name'] || !$_POST['price'] || !$_POST['time'])
                $this -> success("名称、价格、时间不能为空！", 2);

            $DB = $DB_Financial = D('Financial');
            //订单号唯一检查 并生成唯一值
            if($DB -> find("`num` = '{$_POST['num']}'"))
            {
                $postNum = explode('-', $_POST['num']);
                $num_prefix = $postNum[0].'-'.substr($postNum[1], 0, 8);
    			$expenses = $DB -> order("`num` desc") -> find("`num` LIKE '$num_prefix%'");
    			$ids = explode('-', $expenses['num']);
    		    $seq = substr($ids[1], 8);
    		    $seq = $seq ? str_pad((int)$seq+1, 4, '0', STR_PAD_LEFT) : '0001';
    		    $_POST['num'] = $num_prefix.$seq;
            }

            $data = array(
                'name'     => $_POST['name'],
                'price'    => inputPrice($_POST['price']),
                'time'     => strtotime($_POST['time']),
                'notto'    => inputPrice($_POST['notto']),
                'uid'      => Session::get('uid'),
                'rtime'    => NOW,
                'audit'    => Session::get('uid'),
                'num'      => $_POST['num'],
                'way'      => $_POST['way'],
                'bill'     => in_array($_POST['bill'], array('已开', '未开', '不含税')) ? $_POST['bill'] : '未开',
                'billnum'  => $_POST['billnum'],
                'bank'     => $_POST['way'] ? $_POST['bank'] : 0,
            );
            $DB_Financial = D('Financial');
            $id = $DB_Financial -> add($data);
            if($id)
            {
                $money = ($data['price'] > 0 ? 1 : -1) * (abs($data['price']) - abs($data['notto']));
                $this -> bankRecord($id, $money, $data['bank'], 0, 'finanical', $data['name']);

                $page = ceil($DB_Financial -> count("`id` <= {$id}") / 15);
                $this -> redirect('index', 'Financial', '', APP_NAME, array(C('VAR_PAGE')=>$page));
            }else{
                $this -> success('添加失败，请重试', 3);
            }
        }


        //唯一单号
        $DB = D('Financial');
        $num_prefix = 'szgl-' . date('Ymd');
		$expenses =$DB -> order("`num` desc") -> find("`num` LIKE '$num_prefix%'");
		$ids = explode('-', $expenses['num']);
	    $seq = substr($ids[1], 8);
	    $seq = $seq ? str_pad((int)$seq+1, 4, '0', STR_PAD_LEFT) : '0001';
	    $num = $num_prefix.$seq;
        $this -> assign('num', $num);

        $this -> assign('banks', D("Bank") -> findAll());

        $this -> display();
    }


    /**
     + -----------------------------------------------------------------------
     *  财务修改
     + -----------------------------------------------------------------------
     */
    public function edit()
    {
        if($_POST)
        {
            $id = (int)$_POST['id'];
            if(!$id) $this -> success("数据访问错误，请重试。", 3);
            if(!$_POST['name'] || !$_POST['price'] || !$_POST['time'])
                $this -> success("关键数据错误！", 2);

            $DB = $DB_Financial = D('Financial');

            $have = $DB_Financial -> where("`id`={$id}") -> find();
            if(!$have)
                $this -> success("该数据已被删除！", 2);

            //订单号唯一检查 并生成唯一值
            $haveNum = $DB -> find("`num` = '{$_POST['num']}'");
            if($haveNum && $id!=$haveNum['id'])
            {
                $postNum = explode('-', $_POST['num']);
                $num_prefix = $postNum[0].'-'.substr($postNum[1], 0, 8);
    			$expenses = $DB -> order("`num` desc") -> find("`num` LIKE '$num_prefix%'");
    			$ids = explode('-', $expenses['num']);
    		    $seq = substr($ids[1], 8);
    		    $seq = $seq ? str_pad((int)$seq+1, 4, '0', STR_PAD_LEFT) : '0001';
    		    $_POST['num'] = $num_prefix.$seq;
            }

            $data = array(
                'id'       => $id,
                'name'     => $_POST['name'],
                'time'     => strtotime($_POST['time']),
                'notto'    => inputPrice($_POST['notto']),
  //              'uid'      => Session::get('uid'),
                'num'      => $_POST['num'],
                'bill'     =>  in_array($_POST['bill'], array('已开', '未开', '不含税')) ? $_POST['bill'] : '未开',
                'billnum'  => $_POST['billnum'],
            );

            if(false !== $DB_Financial -> save($data))
            {
                $A = A('Order');
                $A -> over($have['order'], $have['come']);

                $money = ($have['price'] > 0 ? 1 : -1) * (abs($have['price']) - abs($data['notto']));
                $this -> bankRecord($have['id'], $money, $have['bank'], $have['order'], $have['come'], $have['name'], true);

                $page = Cookie::get('financial');
                $this -> success('保存成功！', 2, 'Financial/index/'.C('VAR_PAGE').'/'.$page);
            }else{
                $this -> success('保存失败，请重试', 3);
            }

        }
        //获取数据
        $id = (int)$_GET['id'];
        $DB_Financial = D('Financial', true);
        $DB_Financial -> link('user', 'HAS_ONE', 'uid', 'user', 'uid', 'uid,name');
        $data = $DB_Financial -> xfind("`id`='{$id}'");
        $this -> assign($data);

        if(!$data['num'])
        {
            //唯一单号
            $DB = D('Financial');
            $num_prefix = 'szgl-' . date('Ymd');
    		$expenses =$DB -> order("`num` desc") -> find("`num` LIKE '$num_prefix%'");
    		$ids = explode('-', $expenses['num']);
    	    $seq = substr($ids[1], 8);
    	    $seq = $seq ? str_pad((int)$seq+1, 4, '0', STR_PAD_LEFT) : '0001';
    	    $num = $num_prefix.$seq;
            $this -> assign('num', $num);
        }

        $this -> display();
    }
    //over


    /**
     + -----------------------------------------------------------------------
     *  财务查看
     + -----------------------------------------------------------------------
     */
    public function view()
    {
        //获取数据
        $id = (int)$_GET['id'];
        $DB_Financial = D('Financial', true);
        $DB_Financial -> link('user', 'HAS_ONE', 'uid', 'user', 'uid', 'uid,name');
        $data = $DB_Financial -> xfind("`id`='{$id}'");
        $this -> assign($data);

        $this -> assign('bank', $data['way'] ? D('Bank')->where("`id`='{$data['bank']}'")-> find() : array());

        $_subjects = D("fixed_sort") -> where("`type`='subject'") -> findAll();
        $subjects = array();
        foreach ($_subjects as $key=>$val)
        {
            $subjects[$val['id']] = $val;
        }
        $this -> assign('subjects', $subjects);

        //前往明细
        switch ($data['come'])
        {
            case 'order':
                if(false !== strpos($data['name'], 'cgrk'))
                    $url = '/Porder/view/id/'. $data['order'];
                else
                    $url = '/Sorder/view/id/'. $data['order'];

                break;
            case 'return':
                if(false !== strpos($data['name'], 'cgrk'))
                    $url = '/Porder/viewSwap/id/'. $data['order'];
                else
                    $url = '/Sorder/viewSwap/id/'. $data['order'];

                break;
            case 'service':
                $url = '/Service/view/id/'. $data['order'];
                break;
            case 'worker':
                $url = '/Financial/viewDaily/id/'. $data['order'];
                break;
            case 'daily':
                $url = '/Financial/viewDaily/id/'. $data['order'];
                break;
            default:
                $url = '/Financial/view/id/'. $data['id'];
        }
        $this -> assign('url', $url);


        $this -> display();
    }


    /**
     + -----------------------------------------------------------------------
     *  财务删除
     + -----------------------------------------------------------------------
     */
    public function delete()
    {
        $id = (int)$_GET['id'];
        if(!$id) $this -> success("数据访问错误，请重试。", 3);
        $DB_Financial = D('Financial');
        if($DB_Financial -> delete("`id`='{$id}'"))
        {
            $this -> success('删除成功！', 2, 'Financial/index');
        }else{
            $this -> success('操作失败，请重试！',3);
        }
    }


    /**
     + -----------------------------------------------------------------------
     *  财务审核
     + -----------------------------------------------------------------------
     */
    public function audit()
    {
        if($_POST)
        {
            $id = (int)$_POST['id'];
            if(!$id) $this -> success("数据访问错误，请重试。", 3);
            $DB_Financial = D('Financial');
            $data = $DB_Financial -> find("`id`='{$id}'");
            $bank = $_POST['way'] ? $_POST['bank'] : 0;
            if($DB_Financial -> save(array('audit'=>Session::get('uid'), 'time'=>NOW, 'way'=>$_POST['way'], 'bank'=>$bank), "`id`='{$id}'"))
            {
                if($data['order'])
                {
                    $A = A('Order');
                    $A -> over($data['order'], $data['come']);
                }

                $money = ($data['price'] > 0 ? 1 : -1) * (abs($data['price']) - $data['notto']);
                $this -> bankRecord($data['id'], $money, $bank, $data['order'], $data['come'], $data['name']);

                $this -> success('审核成功！', 2, 'Financial/index');
            }else{
                $this -> success('操作失败，请重试！',3);
            }
        }

        //获取数据
        $id = (int)$_GET['id'];
        $DB_Financial = D('Financial', true);
        $DB_Financial -> link('user', 'HAS_ONE', 'uid', 'user', 'uid', 'uid,name');
        $data = $DB_Financial -> xfind("`id`='{$id}'");
        $this -> assign($data);

        $this -> assign("banks", D("Bank") -> findAll());

        $this -> display('audit');
    }


    /**
     + -----------------------------------------------------------------------
     *  财务统计
     + -----------------------------------------------------------------------
     */
    public function statistics()
    {
        //时间
        $stime = $etime = 0;
        if(isset($_GET['stime']) && $_GET['stime'])
            $stime = strtotime($_GET['stime']);
        if(isset($_GET['etime']) && $_GET['etime'])
            $etime = strtotime($_GET['etime'].' 23:59:59');

        $this -> assign('stime', $stime ? date("Y-m-d", $stime) : '');
        $this -> assign('etime', $etime ? date("Y-m-d", $etime) : '');

        $type = isset($_GET['type']) ? $_GET['type'] : '';

        switch($type)
        {
            //进出量走势
            case 'trend':
                if(!isset($stime)) $stime = strtotime("previous month");
                if(!isset($etime)) $etime = NOW;
                $this -> assign('stime', date('Y-m-d', $stime));
                $this -> assign('etime', date('Y-m-d', $etime));


                $condition = "`audit` != 0 AND `time` >= '{$stime}' AND `time` <= '{$etime}'";
                $DB_Financial = D('Financial');

                //out
                $list = $DB_Financial -> findAll($condition, 'price,time');
                $data = array();
                if($list)
                foreach($list as $key => $value)
                {
                    $k = date("Y-m-d", $value['time']);
                    if(!isset($data[$k]))
                        $data[$k] = array('expense'=>0, 'revenue'=>0, 'sum'=>0);
                    if($value['price'] > 0)
                        $data[$k]['revenue'] += $value['price'];
                    else
                        $data[$k]['expense'] += $value['price'];
                    $data[$k]['sum'] += $value['price'];
                }

                $newdata = array();
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
                    $newdata[] = array(
                        'date'   => $key,
                        'revenue'=> isset($data[$dk]['revenue']) ? $data[$dk]['revenue']/100 : 0 ,
                        'expense'=> isset($data[$dk]['expense']) ? $data[$dk]['expense']/100 : 0,
                        'sum'    => isset($data[$dk]['sum']) ? $data[$dk]['sum']/100 : 0,
                    );
                }

                $this -> assign('json', json_encode($newdata));

                $this -> display('statistics_trend');
                break;

            case 'goods':

                $this -> statistics_goods();
                break;

            default:
                if($stime)
                {
                    if(!$etime) $etime = NOW;
                    $condition = ' AND `time` >= '.$stime.' AND `time` <= '.$etime;
                }

                if(isset($_GET['come']) && $_GET['come'] != 1)
                {
                    if($_GET['come'] == 'order1')
                    {
                        $condition .= " AND `come`='order' AND `name` LIKE '%cgrk%'";
                    }elseif($_GET['come'] == 'order2'){
                        $condition .= " AND `come`='order' AND `name` LIKE '%xsck%'";
                    }else{
                        $condition .= " AND `come`='".$_GET['come']."'";
                    }
                }
                $this -> assign('come', $_GET['come']);


                $DB_Financial = D('Financial', true);
                $DB_Financial -> link('user', 'HAS_ONE', 'uid', 'user', 'uid', 'uid,name');

                import('Extend.Util.Page');
                $count = D('Financial')->count($condition);
                $listRows = (!isset($_GET['ac']) || $_GET['ac']) == '搜 索' ? 20 : $count;
                $Page = new Page($count, $listRows);
                $list = $DB_Financial -> xfindAll('`audit` != 0' .$condition, 'id,uid,name,price,notto,audit,time,income,come,order', 'time desc, id desc', $Page->firstRow.','.$Page->listRows);

                foreach ($list as $key=>$val)
                {
                    switch ($val['come'])
                    {
                        case 'order':
                            if(false !== strpos($val['name'], 'cgrk'))
                                $url = '/Porder/view/id/'. $val['order'];
                            else
                                $url = '/Sorder/view/id/'. $val['order'];

                            break;
                        case 'return':
                            if(false !== strpos($val['name'], 'cgrk'))
                                $url = '/Porder/viewSwap/id/'. $val['order'];
                            else
                                $url = '/Sorder/viewSwap/id/'. $val['order'];

                            break;
                        case 'service':
                            $url = '/Service/view/id/'. $val['order'];
                            break;
                        case 'worker':
                            $url = '/Financial/viewDaily/id/'. $val['order'];
                            break;
                        case 'daily':
                            $url = '/Financial/viewDaily/id/'. $val['order'];
                            break;
                        default:
                            $url = '/Financial/view/id/'. $val['id'];
                    }
                    $list[$key]['url'] = $url;

                    //导出用
                    if($_GET['ac'] == '导 出')
                    {
                        $list[$key]['money'] = showPrice(abs($val['price'])-abs($val['notto']));
                        $list[$key]['income'] = showPrice($val['income']);
                        if($val['audit'] == 0)
                            $list[$key]['status'] = '未审核';
                        else{
                            if($val['notto'] !== 0)
                            {
                                $list[$key]['status'] = "欠账：".showPrice($val['notto']);
                            }else{
                                $list[$key]['status'] = "已".($val['price'] > 0) ? '到账' : '付清';
                            }
                        }

                        $list[$key]['made'] = $val['user']['name'];
                        $list[$key]['time'] = date('Y-m-d', $val['time']);
                    }
                }


                if(isset($_GET['ac']) && $_GET['ac'] == '导 出')
                {
                    $titles = array('A'=>'收支内容', 'B'=>'收支金额', 'C'=>'提成', 'D'=>'做账日期', 'E'=>'到账情况', 'F'=>'做账人');
                    $fields = array('A'=>'name', 'B'=>'money', 'C'=>'income', 'D'=>'time', 'E'=>'status', 'F'=>'made');

                    $this -> sqlToExcel($list, $fields, $titles);
                    exit;
                }else{
                    $this -> assign('list', $list);

                    if($_GET['ac'] == '打 印')
                    {
                        $this -> display('printStaList');
                    }else{
                        $DB_Financial = D('Financial');

                        //期初余额
                        $begin = 0;
                        if($stime)
                            $begin = $DB_Financial -> sum('`audit` != 0 AND `time`<'.$stime, 'price');
                        $this -> assign("begin", $begin);

                        //现金支出
                        $moneyexpense = $DB_Financial -> sum('`audit` != 0 AND `way`=0 AND `price` < 0 '.$condition, 'price');
                        //现金未付
                        $menotto = $DB_Financial -> sum('`audit` != 0 AND `way`=0 AND `price` < 0 '.$condition, 'notto');
                        //银行支出
                        $bankexpense = $DB_Financial -> sum('`audit` != 0 AND `way`=1 AND `price` < 0 '.$condition, 'price');
                        //银行未付
                        $benotto = $DB_Financial -> sum('`audit` != 0 AND `way`=1 AND `price` < 0 '.$condition, 'notto');
                        $moneyexpense = (abs($moneyexpense)-abs($menotto)) * -1;
                        $this -> assign('moneyexpense', $moneyexpense);
                        $bankexpense = (abs($bankexpense)-abs($benotto)) * -1;
                        $this -> assign('bankexpense', $bankexpense);

                        //现金收入
                        $moneyrevenue = $DB_Financial -> sum('`audit` != 0 AND `way`=0 AND `price` > 0 '.$condition, 'price');
                        //现金未到
                        $mrnotto = $DB_Financial -> sum('`audit` != 0 AND `way`=0 AND `price` > 0 '.$condition, 'notto');
                        //银行收入
                        $bankrevenue = $DB_Financial -> sum('`audit` != 0 AND `way`=1 AND `price` > 0 '.$condition, 'price');
                        //银行未到
                        $brnotto = $DB_Financial -> sum('`audit` != 0 AND `way`=1 AND `price` > 0 '.$condition, 'notto');

                        $moneyrevenue = (abs($moneyrevenue)-abs($mrnotto));
                        $this -> assign('moneyrevenue', $moneyrevenue);
                        $bankrevenue = (abs($bankrevenue)-abs($brnotto));
                        $this -> assign('bankrevenue', $bankrevenue);

                        $sum = $moneyexpense + $bankexpense + $moneyrevenue + $bankrevenue;
                        $this -> assign('sum', $sum);

                        $this -> assign('total', $begin+$sum);
                        $this -> assign('page', $Page -> show());
                        $this -> display();
                    }
                }
        }
    }
    //over


    //单品销售统计。。。麻烦
    public function statistics_goods(){
        //* 货品
        $DB_Goods = D('Goods');
        $_goods = $DB_Goods ->findAll('', 'id,name,type,price,cost,unit');
        $allgoods = array();
        $type_goods = array();
        foreach($_goods as $v)
        {
            if(isset($_GET['goods_type']) && $v['type'] == $_GET['goods_type'])
            {
                $type_goods[] = $v['id'];
            }
            $allgoods[$v['id']] = $v;
        }
        $this -> assign('allgoods', $allgoods);

        //选择货品，输出单项
        if(isset($_GET['goods']))
        $goods = (int)$_GET['goods'];
        $this -> assign('goods', $goods);

        //未选择货品，则输出所有分类产品
        if(isset($_GET['goods_type']) && !$goods)
        {
            if($type_goods)
                $goods = $type_goods;
            else
                $goods = array_keys($allgoods);
        }else{
            $goods = $goods ? array($goods) : array_keys($allgoods);
        }

        //* 时间
        if(isset($_GET['stime']) && $_GET['stime'])
            $stime = strtotime($_GET['stime']);
        if(isset($_GET['etime']) && $_GET['etime'])
            $etime = strtotime($_GET['etime'].' 23:59:59');
        if(!isset($stime)) $stime = strtotime("1990-1-1 00:00:00");
        if(!isset($etime)) $etime = NOW;
        $this -> assign('stime', date('Y-m-d', $stime));
        $this -> assign('etime', date('Y-m-d', $etime));

        //* 获取所有时间段的销售/采购订单
        $DB_Order = D('Order');
        //组合语句
        $condition = array();
        foreach($goods as $v){
            $condition[] = '`goods` LIKE \'%s:5:"goods";s:'.strlen($v).':"'.$v.'"%\'';
        }
        $condition = $condition ? implode(' OR ', $condition) : '1=1';

        $orders = $DB_Order -> findAll("`time`>='{$stime}' AND `time`<='{$etime}' AND `audit`='1' AND (`type`='2' OR `type`='3') AND ({$condition})", 'goods,type');

        //格式化结果数组
        $list = array();
        foreach($goods as $value){
            $list[$value] = $allgoods[$value];
            $list[$value]['expense'] = 0;   //支出
            $list[$value]['revenue'] = 0;   //销售
            $list[$value]['sum'] = 0;
        }

        foreach($orders as $value){
            $v_goods = unserialize($value['goods']);
            foreach($v_goods as $v){
                $id = $v['goods'];
                if(in_array($id, $goods)){
                    if($value['type'] == 2){
                        //采购
                        $list[$id]['expense'] -= abs($v['price']);
                        $list[$id]['e_num'] += abs($v['num']);
                        $list[$id]['e_cost'] = round($list[$id]['expense'] / $list[$id]['e_num']);
                    }else{
                        //销售
                        $list[$id]['revenue'] += abs($v['price']);
                        $list[$id]['r_num'] += abs($v['num']);
                        $list[$id]['e_cost'] = round($list[$id]['expense'] / $list[$id]['e_num']);
                    }
                    $list[$id]['sum'] = $list[$id]['expense'] + $list[$id]['revenue'];
                }
            }
        }

        $this -> assign('list', $list);
        $this -> display('statistics_goods');
    }


    /**
     * 日常支出
     */
    public function daily()
    {
        $condition = "`type`='daily'";

        $stime = $etime = 0;
        if(isset($_GET['stime']) && $_GET['stime'] && isset($_GET['etime']) && $_GET['etime'])
        {
            $stime = strtotime($_GET['stime']);
            $etime = strtotime($_GET['etime']. " 23:59:59");

            $condition .= " AND (`time`>=$stime AND `time`<{$etime})";
        }
        $this -> assign('stime', $_GET['stime'] ? $_GET['stime'] : '');
        $this -> assign('etime', $_GET['etime'] ? $_GET['etime'] : '');

        if(isset($_GET['user']) && $_GET['user']>0)
        {
            $condition .= " AND `user`=". (int)$_GET['user'];
        }
        $this -> assign('user', $_GET['user'] ? $_GET['user'] : 0);

        $DB = D('Expenses', true);
        $DB -> link('user', 'HAS_ONE', 'user', 'user', 'uid', 'uid,name');
        if(isset($_GET['ac']) && $_GET['ac']=='导 出')
        {
            $list = $DB -> xfindAll($condition, '*', 'addtime desc');
            foreach ($list as $key=>$val)
            {
                //价格
                $list[$key]['total'] = showPrice($val['total']);
                //摘要
                $list[$key]['comment'] = self::arrayToStr(unserialize($val['comment']));
                //科目
                $subject = unserialize($val['subject']);
                $subjects = D('Fixed_sort') -> where("`id` IN (".implode(',', $subject).") AND `type`='subject'") -> findAll();
                $list[$key]['subject'] = self::arrayToStr($subjects, '', 'name');
                $list[$key]['time'] = date('Y-m-d', $val['time']);
                $list[$key]['name'] = $val['user']['name'];
            }

            $titles = array('A'=>'收支编号', 'B'=>'会计科目', 'C'=>'收支金额', 'D'=>'收支说明', 'E'=>'日期', 'F'=>'经办人');
            $fields = array('A'=>'num', 'B'=>'subject', 'C'=>'total', 'D'=>'comment', 'E'=>'time', 'F'=>'name');

            $this -> sqlToExcel($list, $fields, $titles);
            exit;
        }else{

            $_banks = D("bank") -> findAll();
            $banks = array();
            foreach ($_banks as $val)
            {
                $banks[$val['id']] = $val;
            }
            $this -> assign('banks', $banks);

            import('Extend.Util.Page');
            $Page = new Page($DB->count($condition), 15);
            $list = $DB -> xfindAll($condition, '*', 'addtime desc', $Page->firstRow.','.$Page->listRows);
            $this -> assign('page', $Page -> show());

            $beign = D('Expenses') -> sum("`type`='daily' AND `time`<$stime", 'total');
            $this -> assign('begin', $beign);
            $income = D('Expenses') -> sum($condition ." AND `total`>0", 'total');
            $this -> assign('income', $income);
            $pay = D('Expenses') -> sum($condition ." AND `total`<0", 'total');
            $this -> assign('pay', $pay);
            $sum = $income + $pay;
            $this -> assign('sum', $sum);
            $this -> assign('total', $sum+$beign);

            foreach ($list as $key=>$val)
            {
                //价格
                $list[$key]['price'] = self::arrayToStr(unserialize($val['price']), 'showPrice');
                //摘要
                $list[$key]['comment'] = self::arrayToStr(unserialize($val['comment']));
                //科目
                $subject = unserialize($val['subject']);
                $subjects = D('Fixed_sort') -> where("`id` IN (".implode(',', $subject).") AND `type`='subject'") -> findAll();
                $list[$key]['subject'] = self::arrayToStr($subjects, '', 'name');
            }

            $this -> assign('list', $list);

            $this -> assign('users', D("User") -> findAll());

            $this -> assign('type', 'daily');
            $this -> assign('tname', '日常收支');

            if($_GET['p'])
                Cookie::set('dailyPage', $_GET['p']);
            else
                Cookie::set('dailyPage', 1);

            $this -> display('daily');
        }
    }


    /**
     * 员工费用
     */
    public function worker()
    {
        $condition = "`type`='worker'";

        $stime = $etime = 0;
        if(isset($_GET['stime']) && $_GET['stime'] && isset($_GET['etime']) && $_GET['etime'])
        {
            $stime = strtotime($_GET['stime']);
            $etime = strtotime($_GET['etime']);

            $condition .= " AND (`time`>=$stime AND `time`<{$etime})";
        }

        $this -> assign('stime', $_GET['stime'] ? $_GET['stime'] : '');
        $this -> assign('etime', $_GET['etime'] ? $_GET['etime'] : '');

        if(isset($_GET['user']) && $_GET['user']>0)
        {
            $condition .= " AND `user`=". (int)$_GET['user'];
        }
        $this -> assign('user', $_GET['user'] ? $_GET['user'] : 0);

        $DB = D('Expenses', true);
        $DB -> link('user', 'HAS_ONE', 'user', 'user', 'uid', 'uid,name');

        if(isset($_GET['ac']) && $_GET['ac']=='导 出')
        {
            $list = $DB -> xfindAll($condition, '*', 'addtime desc');
            foreach ($list as $key=>$val)
            {
                //价格
                $list[$key]['total'] = showPrice($val['total']);
                //摘要
                $list[$key]['comment'] = self::arrayToStr(unserialize($val['comment']));
                //科目
                $subject = unserialize($val['subject']);
                $subjects = D('Fixed_sort') -> where("`id` IN (".implode(',', $subject).") AND `type`='subject'") -> findAll();
                $list[$key]['subject'] = self::arrayToStr($subjects, '', 'name');
                $list[$key]['time'] = date('Y-m-d', $val['time']);
                $list[$key]['name'] = $val['user']['name'];
            }

            $titles = array('A'=>'收支编号', 'B'=>'会计科目', 'C'=>'收支金额', 'D'=>'收支说明', 'E'=>'日期', 'F'=>'经办人');
            $fields = array('A'=>'num', 'B'=>'subject', 'C'=>'total', 'D'=>'comment', 'E'=>'time', 'F'=>'name');

            $this -> sqlToExcel($list, $fields, $titles);
            exit;
        }else{
            import('Extend.Util.Page');
            $Page = new Page($DB->count($condition), 15);
            $list = $DB -> xfindAll($condition, '*', 'addtime desc', $Page->firstRow.','.$Page->listRows);
            $this -> assign('page', $Page -> show());

            $beign = D('Expenses') -> sum("`type`='daily' AND `time`<$stime", 'total');
            $this -> assign('begin', $beign);
            $income = D('Expenses') -> sum($condition ." AND `total`>0", 'total');
            $this -> assign('income', $income);
            $pay = D('Expenses') -> sum($condition ." AND `total`<0", 'total');
            $this -> assign('pay', $pay);
            $sum = $income + $pay;
            $this -> assign('sum', $sum);
            $this -> assign('total', $sum+$beign);

            foreach ($list as $key=>$val)
            {
                //价格
                $list[$key]['price'] = self::arrayToStr(unserialize($val['price']), 'showPrice');
                //摘要
                $list[$key]['comment'] = self::arrayToStr(unserialize($val['comment']));
                //科目
                $subject = unserialize($val['subject']);
                $subjects = D('Fixed_sort') -> where("`id` IN (".implode(',', $subject).") AND `type`='subject'") -> findAll();
                $list[$key]['subject'] = self::arrayToStr($subjects, '', 'name');
            }

            $this -> assign('list', $list);

            $_banks = D("bank") -> findAll();
            $banks = array();
            foreach ($_banks as $val)
            {
                $banks[$val['id']] = $val;
            }
            $this -> assign('banks', $banks);

            $this -> assign('type', 'worker');
            $this -> assign('tname', '员工费用');

            if($_GET['p'])
                Cookie::set('workerPage', $_GET['p']);
            else
                Cookie::set('workerPage', 1);

            $this -> assign('users', D("User") -> findAll());

            $this -> display('worker');
        }
    }


    static function arrayToStr($array, $fun, $field)
    {
        $str = array();
        foreach ($array as $key=>$val)
        {
            $val = $field ? $val[$field] : $val;
            $str[] = $fun ? ((is_numeric($val) && $val < 0) ? '-' : '').$fun($val) : $val;
        }

        return implode(' || ', $str);
    }


    public function viewDaily()
    {
        if((int)$_GET['id'] < 1)
            $this -> error("为选择要修改的数据");

        $DB = D('Expenses', true);
        $DB -> link('operator', 'HAS_ONE', 'user', 'User', 'uid', '*');
        $expenses = $DB -> xfind("`id`=".(int)$_GET['id']);

        $expenses['subject'] = unserialize($expenses['subject']);
        $expenses['price'] = unserialize($expenses['price']);
        $expenses['income'] = unserialize($expenses['income']);
        $expenses['comment'] = unserialize($expenses['comment']);
        $this -> assign($expenses);

        //判断是收入还是支出
        $title = $expenses['income'][0] == 1 ? '收款' : '付款';
        $this -> assign('title', $title);
        $in = $title == '收款' ? '借方' : '贷方';
        $this -> assign('in', $in);
        //审核
        $audit = D('Financial') -> where('bank') -> where("`come`='daily' AND `order`='{$expenses['id']}' AND `audit`>0") -> find();
        if($audit){
            if($audit['bank'])
            {
                $bank = D('Bank') -> where("`id`={$audit['bank']}") -> find();
                $su = $bank['name'];
            }else{
                $su = '现金';    
            }
             
        }else
            $su = '';
        $this -> assign('su', $su);
        

        $this -> assign('bank', D('bank') -> where("`id`='{$expenses['bank']}'") -> find());

        $_subjects = D("fixed_sort") -> where("`type`='subject'") -> findAll();
        $subjects = array();
        foreach ($_subjects as $key=>$val)
        {
            $subjects[$val['id']] = $val;
        }
        $this -> assign('subjects', $subjects);

        //银行
        $this -> assign('banks', D("Bank") -> findAll());

        $this -> display();
    }


    public function printDaily()
    {
        if((int)$_GET['id'] < 1)
            $this -> error("为选择要打印的数据");

        $DB = D('Expenses', true);
        $DB -> link('operator', 'HAS_ONE', 'user', 'User', 'uid', '*');
        $expenses = $DB -> xfind("`id`=".(int)$_GET['id']);

        $expenses['subject'] = unserialize($expenses['subject']);
        $expenses['price'] = unserialize($expenses['price']);
        $expenses['income'] = unserialize($expenses['income']);
        $expenses['comment'] = unserialize($expenses['comment']);
        $expenses['total'] = abs($expenses['total']);
        $this -> assign($expenses);

        $this -> assign('cMoney', getRMB($expenses['total']/100));

        $_subjects = D("fixed_sort") -> where("`type`='subject'") -> findAll();
        $subjects = array();
        foreach ($_subjects as $key=>$val)
        {
            $subjects[$val['id']] = $val;
        }
        $this -> assign('subjects', $subjects);

        $this -> assign('t', $_GET['t'] ? $_GET['t'] : '收款');
        $this -> assign('i', $_GET['i'] ? $_GET['i'] : '借方');
        $this -> assign('s', $_GET['s'] ? $_GET['s'] : '');

        $this -> display('printDaily');
    }


    public function addDaily()
    {
        if($_POST)
        {
            if(!$_POST['num'] || !$_POST['price'] || !$_POST['comment'])
                $this -> success("请认真填写资料");

            if($_POST['way'] && !$_POST['bank'])
                $this -> success("请选择银行");


            $type = $_POST['type'];
            $DB = D('expenses');
            //订单号唯一检查 并生成唯一值
            if($DB -> find("`num` = '{$_POST['num']}' AND `type`='{$type}'"))
            {
                $postNum = explode('-', $_POST['num']);
                $num_prefix = $postNum[0].'-'.substr($postNum[1], 0, 6);
    			$expenses = $DB -> order("`num` desc") -> find("`num` LIKE '$num_prefix%'");// `type`='{$type}' AND
    			$ids = explode('-', $expenses['num']);
    		    $seq = substr($ids[1], 6);
    		    $seq = $seq ? (int)$seq+1 : 1;
    		    $_POST['num'] = $num_prefix.$seq;
            }

            //封装 科目 金额 及摘要
            $total = 0;
            $subject = $price = $income = $comment = array();
            foreach($_POST['price'] as $key=>$val)
            {
                if(!$val) continue;

                $subject[$key] = $_POST['subject'][$key];
                $income[$key] = $_POST['income'][$key];
                $price[$key] = inputPrice($val);
                $comment[$key] = trim($_POST['comment'][$key]);

                $total += ($income[$key] == 1 ? 1 : -1) * $price[$key];
            }

            $data = array(
                'num'       => $_POST['num'],
                'price'     => serialize($price),
                'income'    => serialize($income),
                'time'      => strtotime($_POST['time']),
                'comment'   => serialize($comment),
                'addtime'   => time(),
                'type'      => $type,
                'audit'     => session::get("uid"),
                'user'      => $_POST['user'] ? $_POST['user'] : '',
                //'bank'      => $_POST['way'] ? (int)$_POST['bank'] : 0,
                //'way'       => $_POST['way'],
                'subject'   => serialize($subject),
                'total'     => $total,
            );

            $result = $DB -> add($data);
            if(false !== $result)
            {
                $data['id'] = $result;
                //加入银行的操作记录
                $_comment = ($type=='worker' ? '员工费用' : '日常支出') . $data['num'];
                //$this -> bankRecord($total, $data['bank'], $result, $data['type'], $_comment);
                $this -> financialRecord($data);

                $this -> success("操作成功！", 1, 'Financial/addDaily/type/'.$type);
            }else{
                $this -> error("操作失败！");
            }
        }


        $type = $_GET['type'];

        //唯一单号
        $DB = D('Expenses');
        $num_prefix = 'rcfy'.'-'.date('Ym');
		$expenses =$DB -> order("`num` desc") -> find("`num` LIKE '$num_prefix%'"); //`type`='{$type}' AND
		$ids = explode('-', $expenses['num']);
	    $seq = substr($ids[1], 6);
	    $seq = $seq ? (int)$seq+1 : 1;
	    $num = $num_prefix.$seq;

        $this -> assign('num', $num);

        $this -> assign('tname', $_GET['type']=='daily' ? '日常支出' : '员工费用');
        $this -> assign('type', $type);

        $this -> assign('users', D("User") -> findAll());
        $this -> assign('banks', D("Bank") -> findAll());
        $this -> assign('subjects', D("fixed_sort") -> where("`type`='subject'") -> findAll());

        $this -> display();
    }


    public function editDaily()
    {
        if($_POST)
        {
            if(!$_POST['id'] || !$_POST['num'] || !$_POST['price'] || !$_POST['comment'])
                $this -> success("请认真填写资料");

            if($_POST['way'] && !$_POST['bank'])
                $this -> success("请选择银行");

            $DB = D('expenses');
            $old = $DB -> find("`id`=".(int)$_POST['id']);
            if($old['audit'] !== Session::get('uid') && Session::get('uid') != 1)
                $this -> error('您没有权限修改！');

            //封装 科目 金额 及摘要
            $total = 0;
            $subject = $price = $comment = array();
            foreach($_POST['price'] as $key=>$val)
            {
                if(!$val) continue;

                $subject[$key] = $_POST['subject'][$key];
                $income[$key] = $_POST['income'][$key];
                $price[$key] = inputPrice($val);
                $comment[$key] = trim($_POST['comment'][$key]);

                $total += ($income[$key] == 1 ? 1 : -1) * $price[$key];
            }

            $data = array(
                'id'        => (int)$_POST['id'],
                'num'       => $_POST['num'],
                'price'     => serialize($price),
                'income'    => serialize($income),
                'time'      => strtotime($_POST['time']),
                'comment'   => serialize($comment),
                'user'      => $_POST['user'] ? $_POST['user'] : '',
                //'bank'      => $_POST['way'] ? (int)$_POST['bank'] : 0,
                //'way'       => $_POST['way'],
                'subject'   => serialize($subject),
                'total'     => $total,
            );

            $result = $DB -> where("`id`=".(int)$_POST['id']) -> save($data);
            if(false !== $result)
            {
                //加入银行的操作记录
                //$_comment = ($type=='worker' ? '员工费用' : '日常支出') . $data['num'];
                //$this -> bankRecord($total, $data['bank'], (int)$_POST['id'], $old['type'], $_comment, true);
                $this -> financialRecord($data, true);
                $page = Cookie::get($old['type'].'Page');
                $this -> success("操作成功！", 1, 'Financial/'.$old['type'].'/p/'.$page);
            }else{
                $this -> error("操作失败！");
            }
        }


        if((int)$_GET['id'] < 1)
            $this -> error("为选择要修改的数据");

        $expenses = D("Expenses") -> find("`id`=".(int)$_GET['id']);
        $expenses['subject'] = unserialize($expenses['subject']);
        $expenses['price'] = unserialize($expenses['price']);
        $expenses['income'] = unserialize($expenses['income']);
        $expenses['comment'] = unserialize($expenses['comment']);
        $this -> assign($expenses);

        $this -> assign('tname', $expenses['type']=='daily' ? '日常支出' : '员工费用');

        $this -> assign('banks', D("Bank") -> findAll());
        $this -> assign('users', D("User") -> findAll());

        $_subjects = D("fixed_sort") -> where("`type`='subject'") -> findAll();
        $subjects = array();
        foreach ($_subjects as $key=>$val)
        {
            $subjects[$val['id']] = $val;
        }
        $this -> assign('subjects', $subjects);

        $this -> display();
    }


    public function deleteDaily()
    {
        if((int)$_GET['id'] < 1)
            $this -> error("为选择要修改的数据");


        $DB = D("Expenses");
        $old = $DB -> where("`id`=".(int)$_GET['id']) -> find();
        $result = $DB -> where("`id`=".(int)$_GET['id']) -> delete();
        if(false !== $result)
        {
            //加入银行的操作记录
            $this -> deleteBankRecord($old['id'], $old['type']);
            $this -> deleteFinancialRecord($old['id'], $old['type']);

            $this -> success("操作成功！", 1, 'Financial/'.$old['type']);
        }else{
            $this -> error("操作失败！");
        }
    }


    //银行账户列表
    public function bank()
    {
        $condition = '';

        $DB = D('Bank');
        import('Extend.Util.Page');
        $Page = new Page($DB->count($condition), 15);
        $list = $DB -> findAll($condition, '*', 'id asc', $Page->firstRow.','.$Page->listRows);
        $this -> assign('page', $Page -> show());
        $this -> assign('list', $list);

        $this -> display();
    }


    //添加银行
    public function addBank()
    {
        if($_POST)
        {
            if(!$_POST['name'] || !$_POST['account'] || !$_POST['time'] || !$_POST['holder'] || !$_POST['address'])
                $this -> error("请认真填写资料！");

            $data = array(
                'name'      => $_POST['name'],
                'account'   => $_POST['account'],
                'time'      => strtotime($_POST['time']),
                'holder'    => $_POST['holder'],
                'address'   => $_POST['address'],
            );

            $result = D('Bank') -> add($data);
            if(false != $result)
            {
                $this -> success("操作成功", 3, 'Financial/bank');
            }else{
                $this -> error("操作失败");
            }
        }

        $this -> display('addBank');
    }


    //修改银行信息
    public function editBank()
    {
        if($_POST)
        {
            if((int)$_POST['id'] < 1 || !$_POST['name'] || !$_POST['account'] || !$_POST['time'] || !$_POST['holder'] || !$_POST['address'])
                $this -> error("请认真填写资料！");


            $data = array(
                'name'      => $_POST['name'],
                'account'   => $_POST['account'],
                'time'      => strtotime($_POST['time']),
                'holder'    => $_POST['holder'],
                'address'   => $_POST['address'],
            );

            $result = D('Bank') -> where("`id`=".(int)$_POST['id']) -> save($data);
            if(false != $result)
            {
                $this -> success("操作成功", 3, 'Financial/bank');
            }else{
                $this -> error("操作失败");
            }
        }

        if((int)$_GET['id'] < 1)
            $this -> error('未选择需删除的银行账户');

        $this -> assign(D("Bank") -> where("`id`=".(int)$_GET['id']) -> find());

        $this -> display('editBank');
    }


    //删除银行
    public function deleteBank()
    {
        if((int)$_GET['id'] < 1)
            $this -> error('未选择需删除的银行账户');

        if(false !== D("Bank") -> where("`id`=".(int)$_GET['id']) -> delete())
        {
            $this -> success("操作成功", 3, 'Financial/bank');
        }else{
            $this -> error("操作失败");
        }
    }


    //查看银行 现金流向
    public function viewBank()
    {
        if((int)$_GET['id'] < 1)
            $this -> error("未选择查看的银行");

        $urls = array(
            'worker'    => 'Financial/editDaily/id/',
            'daily'     => 'Financial/editDaily/id/',
            'financial' => 'Financial/view/id/',
            'fixed'     => 'Fixed/edit/id/',
        );
        $this -> assign('urls', $urls);

        $bank = D("Bank") -> find("`id`=".(int)$_GET['id']);
        $this -> assign($bank);

        $condition = "`bank`=".$bank['id'];
        $stime = $etime = 0;
        if(isset($_GET['stime']) && $_GET['stime'] && isset($_GET['etime']) && $_GET['etime'])
        {
            $stime = strtotime($_GET['stime']);
            $etime = strtotime($_GET['etime']);

            $condition .= " AND (`time`>=$stime AND `time`<{$etime})";
        }
        $this -> assign('stime', $_GET['stime'] ? $_GET['stime'] : '');
        $this -> assign('etime', $_GET['etime'] ? $_GET['etime'] : '');

        if(isset($_GET['come']) && $_GET['come'] != 1)
        {
            if($_GET['come'] == 'order1')
            {
                $condition .= " AND `type`='order' AND `comment` LIKE '%cgrk%'";
            }elseif($_GET['come'] == 'order2'){
                $condition .= " AND `type`='order' AND `comment` LIKE '%xsck%'";
            }else{
                $condition .= " AND `type`='".$_GET['come']."'";
            }
        }
        $this -> assign('come', $_GET['come']);

        $lists = D("Bank_record") -> where($condition) -> findAll();
        $this -> assign('lists', $lists);
        if(isset($_GET['ac']) && $_GET['ac'] == '导 出')
        {
            foreach ($lists as $key=>$val)
            {
                switch($val['type'])
                {
                    case 'daily':
                        $lists[$key]['come'] = '日常费用';
                        break;
                    case 'worker':
                        $lists[$key]['come'] = '员工费用';
                        break;
                    case 'order':
                        $lists[$key]['come'] = '采购/销售';
                        break;
                    case 'service':
                        $lists[$key]['come'] = '售后费用';
                        break;
                    case 'return':
                        $lists[$key]['come'] = '采购/销售退货';
                        break;
                    case 'fixed':
                        $lists[$key]['come'] = '固定资产';
                        break;
                    default:
                        $lists[$key]['come'] = '其他';
                }
                $lists[$key]['time'] = date('Y-m-d', $val['time']);
                $lists[$key]['price'] = showPrice($val['price']);
            }

            $titles = array('A'=>'价格', 'B'=>'说明', 'C'=>'类型', 'D'=>'时间');
            $fields = array('A'=>'price', 'B'=>'comment', 'C'=>'come', 'D'=>'time');

            $this -> sqlToExcel($lists, $fields, $titles);
            exit;
        }else{
            $beign = D('Bank_record') -> sum("`bank`=".$bank['id']." AND `time`<$stime", 'price');
            $this -> assign('begin', $beign);
            $income = D('Bank_record') -> sum($condition ." AND `price`>0", 'price');
            $this -> assign('income', $income);
            $pay = D('Bank_record') -> sum($condition ." AND `price`<0", 'price');
            $this -> assign('pay', $pay);
            $sum = $income + $pay;
            $this -> assign('sum', $sum);
            $this -> assign('total', $sum+$beign);

            $this -> assign('bank', $_GET['id']);

            if($_GET['ac'] == '打 印')
                $this -> display('printBankList');
            else
                $this -> display();
        }
    }


    //现金收支明细
    public function money()
    {
        $urls = array(
            'worker'    => 'Financial/editDaily/id/',
            'daily'     => 'Financial/editDaily/id/',
            'financial' => 'Financial/view/id/',
            'fixed'     => 'Fixed/edit/id/',
        );
        $this -> assign('urls', $urls);

        $condition = "`bank`=0";
        $stime = $etime = 0;
        if(isset($_GET['stime']) && $_GET['stime'] && isset($_GET['etime']) && $_GET['etime'])
        {
            $stime = strtotime($_GET['stime']);
            $etime = strtotime($_GET['etime']);

            $condition .= " AND (`time`>=$stime AND `time`<{$etime})";
        }
        $this -> assign('stime', $_GET['stime'] ? $_GET['stime'] : '');
        $this -> assign('etime', $_GET['etime'] ? $_GET['etime'] : '');

        if(isset($_GET['come']) && $_GET['come'] != 1)
        {
            if($_GET['come'] == 'order1')
            {
                $condition .= " AND `type`='order' AND `comment` LIKE '%cgrk%'";
            }elseif($_GET['come'] == 'order2'){
                $condition .= " AND `type`='order' AND `comment` LIKE '%xsck%'";
            }else{
                $condition .= " AND `type`='".$_GET['come']."'";
            }
        }
        $this -> assign('come', $_GET['come']);

        import('Extend.Util.Page');
        $count = D('Bank_record')->count($condition);
        $listRows = (!isset($_GET['ac']) || $_GET['ac'] == '查 询') ? 20 : $count;
        $Page = new Page($count, $listRows);
        $lists = D("Bank_record") -> where($condition) -> limit($Page->firstRow.','.$Page->listRows) -> order("`id` desc") -> findAll();
        $this -> assign('lists', $lists);
        $this -> assign('page', $Page->show());

        if(isset($_GET['ac']) && $_GET['ac'] == '导 出')
        {
            foreach ($lists as $key=>$val)
            {
                switch($val['type'])
                {
                    case 'daily':
                        $lists[$key]['come'] = '日常费用';
                        break;
                    case 'worker':
                        $lists[$key]['come'] = '员工费用';
                        break;
                    case 'order':
                        $lists[$key]['come'] = '采购/销售';
                        break;
                    case 'service':
                        $lists[$key]['come'] = '售后费用';
                        break;
                    case 'return':
                        $lists[$key]['come'] = '采购/销售退货';
                        break;
                    case 'fixed':
                        $lists[$key]['come'] = '固定资产';
                        break;
                    default:
                        $lists[$key]['come'] = '其他';
                }
                $lists[$key]['time'] = date('Y-m-d', $val['time']);
                $lists[$key]['price'] = showPrice($val['price']);
            }

            $titles = array('A'=>'价格', 'B'=>'说明', 'C'=>'类型', 'D'=>'时间');
            $fields = array('A'=>'price', 'B'=>'comment', 'C'=>'come', 'D'=>'time');

            $this -> sqlToExcel($lists, $fields, $titles);
            exit;
        }else{
            $beign = D('Bank_record') -> sum("`bank`=0 AND `time`<$stime", 'price');
            $this -> assign('begin', $beign);
            $income = D('Bank_record') -> sum($condition ." AND `price`>0", 'price');
            $this -> assign('income', $income);
            $pay = D('Bank_record') -> sum($condition ." AND `price`<0", 'price');
            $this -> assign('pay', $pay);
            $sum = $income + $pay;
            $this -> assign('sum', $sum);
            $this -> assign('total', $sum+$beign);

            if($_GET['ac'] == '打 印')
                $this -> display('printMoneyList');
            else
                $this -> display();
        }
    }


    public function delBankRecord()
    {
        if((int)$_GET['id'])
        {
            $record = D("Bank_record") -> where("`id`='{$_GET['id']}'") -> find();
            if(false !== D("Bank_record") -> where("`id`='{$_GET['id']}'") -> delete())
            {
                $this -> success("删除成功", 1, 'Financial/'.($record['bank'] ? 'viewBank/id/'.$record['bank'] : 'money'));
            }
        }

        $this -> success("删除失败");
    }

    //写入银行的记录
    public function bankRecord($financial, $price, $bank, $pid, $type, $comment, $edit=false)
    {
        //
        $data = array(
            'financial' => $financial,
            'price'     => $price,
            'bank'      => $bank,
            'comment'   => $comment,
        );

        if($edit)
        {
            $result = D("Bank_record") -> where("`financial`={$financial}") -> save($data);
        }else{
            $data['pid'] = $pid;
            $data['type'] = $type;
            $data['time'] = time();

            $result = D("Bank_record") -> add($data);
        }

        if(false !== $result)
        {
            return true;
        }else{
            return false;
        }
    }


    public function deleteBankRecord($pid, $type, $financial)
    {
        $result = D("Bank_record") -> where("`pid`={$pid} AND `type`='{$type}'") -> delete();
        if(false !== $result)
        {
            return true;
        }else{
            return false;
        }
    }


    //日常支出写入收支记录
    public function financialRecord($data, $edit=false)
    {
        $finacial = array(
            'name'      => ($data['type']=='daily' ? '日常支出' : '员工费用').$data['num'],
            'price'     => $data['total'],
            'notto'     => 0,
            'time'      => $data['time'],
            'way'       => $data['way'],
            'bank'      => $data['bank'],
        );

        if($edit)
        {
            $have = D('financial') -> where("`order`='{$data['id']}' AND `come`='{$data['type']}'") -> find();
            if($have['audit'] != 0)
            {
                $this -> success("禁止修改");
            }

            $result = D('financial') -> where("`order`='{$data['id']}' AND `come`='{$data['type']}'") -> save($finacial);
        }else{
            $finacial['rtime'] = time();
            $finacial['order'] = $data['id'];
            $finacial['come'] = $data['type'];
            $finacial['uid'] = session::get('uid');
            $result = D('financial') -> add($finacial);
        }

        if(false !== $result)
            return true;
        else
            return false;
    }


    public function deleteFinancialRecord($order, $come)
    {
        $result = D("Financial") -> where("`order`={$order} AND `come`='{$come}'") -> delete();
        if(false !== $result)
        {
            return true;
        }else{
            return false;
        }
    }


    //科目管理
    public function sort()
    {
        if($_POST)
        {
            foreach ($_POST['name'] as $key=>$val)
            {
                if((int)$_POST['id'][$key] > 0 && $val)
                {
                    D("Fixed_sort") -> where("`id`={$_POST['id'][$key]} AND `type`='subject'") -> save(array('name'=>$val, 'num'=> $_POST['num'][$key]));
                }elseif((int)$_POST['id'][$key] > 1 && !$val){
                    D("Fixed_sort") -> where("`id`={$_POST['id'][$key]} AND `type`='subject'") -> delete();
                }elseif((int)$_POST['id'][$key] < 1 && $val){
                    D("Fixed_sort") -> add(array('name'=>$val, 'num'=> $_POST['num'][$key], 'type'=>'subject'));
                }
            }

            header('Location: ./sort');
        }

        $this -> assign("list", D("Fixed_sort") -> order("num asc") -> findAll("`type`='subject'"));

        $this -> display('sort');
    }


    //应收应付统计
    public function debt()
    {
        $table = isset($_GET['type']) ? $_GET['type'] : 'supplier';

        $condition = '';

        $time1 = $time2_end = '';
		if(isset($_GET['start']) && $_GET['start']&&isset($_GET['end']) && $_GET['end'])
		{
				$time1 =  strtotime($_GET['start']);
				$time2_end = strtotime($_GET['end'])+24*60*60-1;
				$condition ="(`time` BETWEEN {$time1} AND {$time2_end}) AND ";
		}
		$this -> assign('start', $time1);
		$this -> assign('end', $time2_end);

		$name = '';
		if(isset($_GET['name']) && $_GET['name'])
		{
		    $name = "`name` LIKE '%{$_GET['name']}%'";
		}
		$this -> assign("name", $_GET['name']);
        if(isset($_GET['ac']) && $_GET['ac']=='导 出')
        {
            $sc = D(ucfirst($table)) -> findAll($name, '*', 'id asc');
        }else{
            import('Extend.Util.Page');
            $Page = new Page(D(ucfirst($table))->count($name), 15);
            $sc = D(ucfirst($table)) -> findAll($name, '*', 'id asc',$Page->firstRow.','.$Page->listRows);
        }

        $list = $_list = array();
        foreach ($sc as $val)
        {
            $_list[$val['id']] = $table == 'supplier' ?
                                 D('Debt') -> sum("{$condition}money<0 AND `cors`={$val['id']} AND `cType`='{$table}'", 'money') :
                                 D('Debt') -> sum("{$condition}money>0 AND `cors`={$val['id']} AND `cType`='{$table}'",'money') ;

            $list[$val['id']] = $val;
            $list[$val['id']]['income'] = D('Debt') -> sum("{$condition}money>0 AND `cors`={$val['id']} AND `cType`='{$table}'",'money');
            $list[$val['id']]['pay'] = D('Debt') -> sum("{$condition}money<0 AND `cors`={$val['id']} AND `cType`='{$table}'", 'money');
        }

        asort($_list);
        if(isset($_GET['ac']) && $_GET['ac']=='导 出')
        {
            $lists = array();
            foreach ($_list as $key=>$val)
            {
                $lists[] = array(
                    'name'      => $list[$key]['name'],
                    'income'    => showPrice($list[$key]['income']),
                    'pay'       => showPrice($list[$key]['pay']),
                );
            }

            $titles = array('A'=>'公司名', 'B'=>'累计应收', 'C'=>'累计应付');
            $fields = array('A'=>'name', 'B'=>'income', 'C'=>'pay');

            $this -> sqlToExcel($lists, $fields, $titles);
            exit;
        }else{
            $this -> assign('income', D('Debt') -> sum("money>0 AND `cors`>0 AND `cType`='{$table}'",'money'));
            $this -> assign('pay', D('Debt') -> sum("money<0 AND `cors`>0 AND `cType`='{$table}'",'money'));


            $this -> assign('_list', $_list);
            $this -> assign("list", $list);
            $this -> assign("page", $Page->show());
            $this -> assign('type', $table);

            $this -> display('debt');
        }
    }


    //添加 应收应付
    public function addDebt()
    {
        if($_POST)
        {
            if(!$_POST['type'] || !$_POST['cors'])
                $this -> success("请认真填写资料");

            $data = array(
                'cType' => $_POST['type'],
                'cors'  => $_POST['cors'],
                'money' => inputPrice($_POST['money']),
                'hand'  => 1,
                'comment'   => $_POST['comment'],
                'uid'   => Session::get('uid'),
                'time'  => strtotime($_POST['time']),
            );

            $result = D('debt') -> add($data);
            if(false !== $result)
            {
                $this -> success("添加成功！", 3, 'Financial/debt');
            }else{
                $this -> success('操作失败！');
            }
        }

        $this -> assign('time', time());
        $this -> assign('client', A('Client')->getTypes(true));
        $this -> assign('supplier', A('Supplier')->getTypes(true));
        $this -> display('addDebt');
    }


    //修改 应收应付
    public function editDebt()
    {
        if($_POST)
        {
            if((int)$_POST['id'] < 1)
                $this -> success("未选择要修改的明细");

            $have = D("Debt") -> where("`id`=".(int)$_POST['id']) -> find();

            $data = array(
                'time'  => strtotime($_POST['time']),
                'money' => inputPrice($_POST['money']),
                'comment'   => $_POST['comment'],
            );

            $result = D("Debt") -> where("`id`=".(int)$_POST['id']) -> save($data);
            if(false != $result)
            {
                $this ->success("操作成功", 2, 'Financial/debtList/id/'.$have['cors']."/type/".$have['cType']);
            }else{
                $this ->success("操作失败");
            }
        }


        $id = (int)$_GET['id'];
        if($id < 1)
            $this -> success("未选择要修改的明细");

        $debt = D("Debt") -> where("`id`={$id}") -> find();
        $this -> assign($debt);

        $this -> assign('sc', D($debt['cType']) -> where("`id`={$debt['cors']}") -> find());

        $this -> display();
    }

    //应收应付 明细
    public function debtList()
    {
        $id = (int)$_GET['id'];
        if(!$id)
            $this -> error();

        $sc = D(ucfirst($_GET['type'])) -> where("`id`={$id}") -> find();
        $this -> assign('sc', $sc);

        $DB = D('Debt');
        $condition = "`cors`={$id} AND `cType`='{$_GET['type']}'";
        import('Extend.Util.Page');
        $Page = new Page($DB->count($condition), 15);
        $list = $DB -> findAll($condition, '*', 'id desc', $Page->firstRow.','.$Page->listRows);
        $this -> assign('page', $Page -> show());
        $this -> assign("list", $list);

        $this -> display('debtList');
    }


    public function printFinancial()
    {
        $id = (int)$_GET['id'];
        if($id < 0)
            $this -> error('未选择需打印的数据！');

        //财务收支
        $financial = D("Financial") -> where("`id`={$id}") -> find();
        if(!$financial['order'] || !in_array($financial['come'], array('order', 'return', 'service')))
            $this -> success("该数据不能打印！");
        $this -> assign('financial', $financial);

        //审核人
        $this -> assign('audit', D("User") -> where("`uid`={$financial['audit']}") -> find());

        //订单明细
        $order = D(ucfirst($financial['come'])) -> where("`id`={$financial['order']}") -> find();
        $order['goods'] = unserialize($order['goods']);
        foreach ($order['goods'] as $key=>$val)
        {
            $good = D('Goods') -> where("`id`={$val['goods']}") -> find();
            $order['goods'][$key]['name'] = $good['name'];
        }
        $this -> assign('order', $order);

        //科目
        $subjects = D("Fixed_sort") -> where("`type`='subject'") -> findAll();
        $this -> assign('subjects', $subjects);

        //大写金额
        $total = $financial['price'] > 0 ? $financial['price']-$financial['notto'] : $financial['price'] + $financial['notto'];
        $this -> assign('total', abs($total));
        $this -> assign('cMoney', getRMB(abs($total)/100));

        $this -> assign('s', $_GET['s']);

        $this -> display();
    }


    //利润统计
    public function profit()
    {
        $condition = '`audit` >= 1';
        //时间
        $stime = $etime = 0;
        if(isset($_GET['stime']) && $_GET['stime'])
            $stime = strtotime($_GET['stime']);
        if(isset($_GET['etime']) && $_GET['etime'])
            $etime = strtotime($_GET['etime'].' 23:59:59');

        $this -> assign('stime', $stime ? date("Y-m-d", $stime) : '');
        $this -> assign('etime', $etime ? date("Y-m-d", $etime) : '');

        if($stime)
        {
            if(!$etime) $etime = NOW;
            $condition .= ' AND `time` >= '.$stime.' AND `time` <= '.$etime;
        }

        if(isset($_GET['come']) && $_GET['come'] != 1)
        {
            if($_GET['come'] == 'order1')
            {
                $condition .= " AND `come`='order' AND `name` LIKE '%cgrk%'";
            }elseif($_GET['come'] == 'order2'){
                $condition .= " AND `come`='order' AND `name` LIKE '%xsck%'";
            }elseif($_GET['come'] == 'order3'){
                $condition .= " AND `come`='order' AND `name` LIKE '%scrk%'";
            }else{
                $condition .= " AND `come`='".$_GET['come']."'";
            }
        }
        $this -> assign('come', $_GET['come']);

        //支出总额
        $pay = D("Financial") -> field("SUM(`price`) as p, SUM(`notto`) as n, SUM(`income`) as i") -> where($condition .' AND `price`<0') -> find();
        $this -> assign('pay', $pay);

        //收入总额
        $income = D("Financial") -> field("SUM(`price`) as p, SUM(`notto`) as n, SUM(`income`) as i") -> where($condition .' AND `price`>0') -> find();
        $this -> assign('income', $income);

        //利润 = (收入-未收的) - (支出-未支的) - (收入提成+支出提成)
        $this -> assign('profit', ($income['p']-$income['n']) + ($pay['p']-$pay['n']) + ($pay['i']+$income['i']));

        $this -> display();
    }
}
?>