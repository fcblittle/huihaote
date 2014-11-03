<?php
class testAction extends Action
{
    public function index()
    {
        $list = D('Stock') -> where("`goods_name` like '%(%'") -> findAll();

        foreach ($list as $k=>$v)
        {
            $goodName = substr($v['goods_name'], 0, strpos('(', $v['goods_name']));

            echo $v['goods_name'].'----'.$goodName.'<br />';
            //D('Stock') -> where("`id`={$v['id']}") -> setField('goods_name', $goodName);
        }
    }
}