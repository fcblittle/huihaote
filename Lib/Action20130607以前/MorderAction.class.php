<?php
/**
 * 生产入库单据管理Action
 *
 * @author Page7
 * @category Police OA 2012 pack1
 * @copyright Copyright(c) 2011 Interidea.org
 * @version $Id$
 */

include_once(dirname(__FILE__).'/OrderAction.class.php');

class MorderAction extends OrderAction
{
    protected $mname = 'Morder';

    protected $type = 1;

    protected $title = '生产入库单';

    protected $abs = true;

    protected $price = true;

    protected $pinyin = 'scrk';


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

        $order['cMoney'] = getRMB($order['total']/100);
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