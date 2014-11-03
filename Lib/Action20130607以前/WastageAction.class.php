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

class WastageAction extends OrderAction
{
    protected $mname = 'Wastage';

    protected $type = 4;

    protected $title = '原材料损耗';

    protected $abs = true;

    protected $price = true;

    protected $pinyin = 'yclsh';
}
?>