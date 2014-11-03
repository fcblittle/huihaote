<?php
/**
 * 入口判断Action
 *
 * @author Page7
 * @category Police OA 2012 pack1
 * @copyright Copyright(c) 2011 Interidea.org
 * @version $Id$
 */

class IndexAction extends Action
{
    public function index()
    {
        include_once(LIB_PATH.'_allow.php');

        if(Session::get('uid'))
        {
            include_once(LIB_PATH.'_initialize.php');

            $can_do = $this -> get('can_do');

            $DB_Order = D('Order');
            //销售出库审核
            if(isset($can_do['Sorder']['audit']))
            {
                $sorder = $DB_Order -> count("`type`='3' AND `audit`='0'");
                if($sorder) $this -> assign('sorder', $sorder);
            }
            //采购入库审核
            if(isset($can_do['Porder']['audit']))
            {
                $porder = $DB_Order -> count("`type`='2' AND `audit`='0'");
                if($porder) $this -> assign('porder', $porder);
            }
            //生产出库审核
            if(isset($can_do['Morder']['audit']))
            {
                $morder = $DB_Order -> count("`type`='1' AND `audit`='0'");
                if($morder) $this -> assign('morder', $morder);
            }
            //库存审核
            if(isset($can_do['Stock']['audit']))
            {
                $DB_Stock = D('Stock');
                $stock_in = $DB_Stock -> count("`num`>0 AND `audit`='0'");
                $this -> assign('stock_in', $stock_in);

                $stock_out = $DB_Stock -> count("`num`<0 AND `audit`='0'");
                $this -> assign('stock_out', $stock_out);
            }
            //财务审核
            if(isset($can_do['Financial']['audit']))
            {
                $DB_Financial = D('Financial');
                $financial_in = $DB_Financial -> count("`price`>0 AND `audit`='0'");
                $this -> assign('financial_in', $financial_in);

                $financial_out = $DB_Financial -> count("`price`<0 AND `audit`='0'");
                $this -> assign('financial_out', $financial_out);
            }

            $this -> assign('title', '登录首页');
            $this -> display();
        }else{
            $this -> redirect("login", "Public");
        }
    }

}
?>