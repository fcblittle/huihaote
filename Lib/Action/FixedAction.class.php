<?php
/**
 * 固定资产
 */
class FixedAction extends Action
{
    public function _initialize()
    {
        include_once(LIB_PATH.'_initialize.php');
    }


    //
    public function index()
    {
        $condition = "`low`=0";

        $DB = D('Fixed');
        if(isset($_GET['ac']) && $_GET['ac'] == 'export')
        {
            $list = $DB -> findAll($condition, '*', 'addtime desc');

            foreach ($list as $key=>$val)
            {
                $list[$key]['price'] = $val['type'] == 'old' ? '-'.showPrice($val['price']) : showPrice($val['price']);
                $list[$key]['time'] = date("Y-m-d", $val['time']);
            }

            $titles = array('A'=>'收支内容', 'B'=>'数量', 'C'=>'收支金额', 'D'=>'日期', 'E'=>'经办人');
            $fields = array('A'=>'title', 'B'=>'num', 'C'=>'price', 'D'=>'time', 'E'=>'user');

            $this -> sqlToExcel($list, $fields, $titles);
            exit;
        }else{
            import('Extend.Util.Page');
            $Page = new Page($DB->count($condition), 15);
            $list = $DB -> findAll($condition, '*', 'addtime desc', $Page->firstRow.','.$Page->listRows);
            $this -> assign('page', $Page -> show());
            $this -> assign('list', $list);

            //原有
            $exist = $DB -> sum("`type`='exist' AND `low`=0", 'price');
            $this -> assign('exist', $exist);
            //折旧
            $old = $DB -> sum("`type`='old' AND `low`=0", 'price');
            $this -> assign('old', $old);
            //变卖
            $sell = $DB -> sum("`type`='sell' AND `low`=0", 'price');
            $this -> assign('sell', $sell);

            //现有
            $this -> assign('now', $exist-abs($old)-abs($sell));

            if($_GET['p'])
                Cookie::set('fixedPage', $_GET['p']);
            else
                Cookie::set('fixedPage', 1);

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


    public function low()
    {
        $condition = "`low`=1";

        $DB = D('Fixed');
        if(isset($_GET['ac']) && $_GET['ac'] == 'export')
        {
            $list = $DB -> findAll($condition, '*', 'addtime desc');

            foreach ($list as $key=>$val)
            {
                $list[$key]['price'] = $val['type'] == 'old' ? '-'.showPrice($val['price']) : showPrice($val['price']);
                $list[$key]['time'] = date("Y-m-d", $val['time']);
            }

           $titles = array('A'=>'收支内容', 'B'=>'数量', 'C'=>'收支金额', 'D'=>'日期', 'E'=>'经办人');
            $fields = array('A'=>'title', 'B'=>'num', 'C'=>'price', 'D'=>'time', 'E'=>'user');

            $this -> sqlToExcel($list, $fields, $titles);
            exit;
        }else{
            import('Extend.Util.Page');
            $Page = new Page($DB->count($condition), 15);
            $list = $DB -> findAll($condition, '*', 'addtime desc', $Page->firstRow.','.$Page->listRows);
            $this -> assign('page', $Page -> show());
            $this -> assign('list', $list);

            //原有
            $exist = $DB -> sum("`type`='exist' AND `low`=1", 'price');
            $this -> assign('exist', $exist);
            //折旧
            $old = $DB -> sum("`type`='old' AND `low`=1", 'price');
            $this -> assign('old', $old);
            //变卖
            $sell = $DB -> sum("`type`='sell' AND `low`=1", 'price');
            $this -> assign('sell', $sell);

            //现有
            $this -> assign('now', $exist-abs($old)-abs($sell));

            if($_GET['p'])
                Cookie::set('lowPage', $_GET['p']);
            else
                Cookie::set('lowPage', 1);

            $this -> display();
        }
    }


    public function add()
    {
        if($_POST)
        {
            if(!$_POST['title'] || !$_POST['price'] || !$_POST['comment'])
            {
                $this -> success("请认真填写资料");
            }

            $DB = D('Fixed');

            $data = array(
                'title'     => $_POST['title'],
                'time'      => strtotime($_POST['time']),
                'comment'   => trim($_POST['comment']),
                'addtime'   => time(),
                'type'      => $_POST['type'],
                'audit'     => session::get("uid"),
                'user'      => $_POST['user'] ? $_POST['user'] : '',
                //'way'       => $_POST['way'],
                'low'       => $_POST['low'],
                'num'       => $_POST['num'],
                'money'     => inputPrice($_POST['money']),
                'old'       => inputPrice($_POST['old']),
                //'bank'      => $_POST['way'] ? $_POST['bank'] : 0,
            );

            $data['price'] = ($data['type']=='buy' || $data['type']=='old') ? -inputPrice($_POST['price']) : inputPrice($_POST['price']);

            $result = $DB -> add($data);
            if(false !== $result)
            {
                /**
                //加入银行的操作记录
                if('type' !== 'exist')
                    A('Financial') -> bankRecord($data['price'], $data['bank'], $result, 'fixed', $data['comment']);
                */
                $this -> success("操作成功！", 1, 'Fixed/'. ($_POST['from'] ? 'low' : 'index'));
            }else{
                $this -> error("操作失败！");
            }
        }

        $this -> assign('from', isset($_GET['from']) ? 1 : 0);
        $this -> assign('banks', D("Bank") -> findAll());
        $this -> display('add');
    }


    public function edit()
    {
        if($_POST)
        {
            if((int)$_POST['id'] < 1)
                $this -> error();

            $have = D('Fixed') -> where("`id`=".(int)$_POST['id']) -> find();
            if(!$have)
                $this -> error();

            $data = array(
                'title'     => $_POST['title'],
                'time'      => strtotime($_POST['time']),
                'comment'   => trim($_POST['comment']),
                'type'      => $_POST['type'],
                'user'      => $_POST['user'] ? $_POST['user'] : '',
                'old'       => inputPrice($_POST['old']),
                //'way'       => $_POST['way'],
                //'bank'      => $_POST['way'] ? $_POST['bank'] : 0,
                'num'       => $_POST['num'],
                'money'     => inputPrice($_POST['money']),
            );
            $data['price'] = ($data['type']=='buy' || $data['type']=='old') ? -inputPrice($_POST['price']) : inputPrice($_POST['price']);

            $result = D('Fixed') -> where("`id`=".(int)$_POST['id']) -> save($data);
            if(false !== $result)
            {
                /**
                //加入银行的操作记录
                if('type' !== 'exist')
                    A('Financial') -> bankRecord($data['price'], $data['bank'], (int)$_POST['id'], 'fixed', $data['comment'], true);
                */
                $page = $have['low'] ? Cookie::get('lowPage') : Cookie::get('fixedPage');
                $this -> success("操作成功！", 1, 'Fixed/'. ($have['low'] ? 'low' : 'index').'/p/'. $page);
            }else{
                $this -> error("操作失败！");
            }
        }

        if((int)$_GET['id'] < 1)
            $this -> error('未选择修改内容');

        $this -> assign(D("Fixed") -> where("`id`=".(int)$_GET['id']) -> find());

        $this -> assign('banks', D("Bank") -> findAll());
        $this -> display();
    }


    //折旧
    public function old()
    {
        if($_POST)
        {
            if((int)$_POST['id'] < 1)
                $this -> error("未选择需折旧的产品！");

            $have = D('Fixed') -> where("`id`=".(int)$_POST['id']) -> find();
            if(!$have)
                $this -> error("未选择需折旧的产品！");

            $DB = D('Fixed');
            $data = array(
                'title'     => $have['title'],
                'time'      => strtotime($_POST['time']),
                'comment'   => trim($_POST['comment']),
                'addtime'   => time(),
                'type'      => 'old',
                'audit'     => session::get("uid"),
                'user'      => $_POST['user'] ? $_POST['user'] : '',
                //'way'       => $_POST['way'],
                'low'       => 0,
                'num'       => $have['num'],
                'money'     => $have['money'],
                //'bank'      => $_POST['way'] ? $_POST['bank'] : 0,
            );

            $data['price'] = -inputPrice($_POST['price']);

            $result = $DB -> add($data);
            if(false !== $result)
            {
                $this -> success("操作成功！", 1, 'Fixed/index');
            }else{
                $this -> error("操作失败！");
            }
        }

        if((int)$_GET['id'] < 1)
            $this -> error('未选择修改内容');

        $this -> assign(D("Fixed") -> where("`id`=".(int)$_GET['id']) -> find());

        $this -> assign('banks', D("Bank") -> findAll());

        $this -> display('old');
    }


    public function delete()
    {
        if((int)$_GET['id'] < 1)
            $this -> error('未选择修改内容');

        $fixed = D("Fixed") -> where("`id`=".(int)$_GET['id']) -> find();
        if(false !== D("Fixed") -> where("`id`=".(int)$_GET['id']) -> delete())
        {
            A("Financial") -> deleteBankRecord($fixed['id'], 'fixed');
            $this -> success("操作成功！", 1, 'Fixed/index');
        }else{
            $this -> success("操作失败！");
        }
    }


    public function sort()
    {
        if($_POST)
        {
            foreach ($_POST['name'] as $key=>$val)
            {
                if((int)$_POST['id'][$key] > 0 && $val)
                {
                    D("Fixed_sort") -> where("`id`={$_POST['id']} AND `type`='fixed'") -> setField("name", $val);
                }elseif((int)$_POST['id'][$key] > 1 && !$val){
                    D("Fixed_sort") -> where("`id`={$_POST['id']} AND `type`='fixed'") -> delete();
                }elseif((int)$_POST['id'][$key] < 1 && $val){
                    D("Fixed_sort") -> add(array('name'=>$val, 'type'=>'fixed'));
                }
            }
        }

        $this -> assign("list", D("Fixed_sort") -> findAll("`type`='fixed'"));

        $this -> display('sort');
    }
}