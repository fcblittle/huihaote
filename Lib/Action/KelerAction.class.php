<?php
class KelerAction extends Action
{
    protected $table = 'Financial';
    //订单处理
    public function orders()
    {
        header("Content-type:text/html;charset=utf-8");
        $prefix = C("DB_PREFIX");
        $corstable = array('', 'supplier', 'supplier', 'client');

        $DB = D($this->table);

        $orders = $DB -> query("SELECT f.id, f.name, f.cors, f.corstable, o.cors as orderCors, o.type 
            FROM {$prefix}{$this->table} f 
            LEFT JOIN {$prefix}order o ON o.id=f.order 
            WHERE f.`come`='order'");

        foreach ($orders as $key => $val) 
        {
            if($val['orderCors'] < 1)
            {
                echo '<span style="color:blue;">'. $val['name'] .'----------------对应订单异常</span><br />';
                continue;
            }    

            if((int)$val['cors'] > 0)
            {
                echo '<span style="color:gray;">'. $val['name'] .'----------------已存在</span><br />';
                continue;
            }

            $result = $DB -> where("`id`={$val['id']}") -> save(array('cors'=>$val['orderCors'], 'corstable'=>$corstable[$val['type']]));
            if(false !== $result)
                echo '<span style="color:green;">'. $val['name'] .'----------------成功</span><br />';
            else
                echo '<span style="color:red;">'. $val['name'] .'----------------失败</span><br />';   
        }
    }

    //退换处理
    public function returns()
    {
        header("Content-type:text/html;charset=utf-8");
        $prefix = C("DB_PREFIX");
        $corstable = array('', 'supplier', 'supplier', 'client');

        $DB = D($this->table);

        $orders = $DB -> query("SELECT f.id, f.name, f.cors, f.corstable, o.cors as orderCors, o.type 
            FROM {$prefix}{$this->table} f 
            LEFT JOIN {$prefix}return o ON o.id=f.order 
            WHERE f.`come`='return'");

        foreach ($orders as $key => $val) 
        {
            if($val['orderCors'] < 1)
            {
                echo '<span style="color:blue;">'. $val['name'] .'----------------对应订单异常</span><br />';
                continue;
            }    

            if((int)$val['cors'] > 0)
            {
                echo '<span style="color:gray;">'. $val['name'] .'----------------已存在</span><br />';
                continue;
            }

            $result = $DB -> where("`id`={$val['id']}") -> save(array('cors'=>$val['orderCors'], 'corstable'=>$corstable[$val['type']]));
            if(false !== $result)
                echo '<span style="color:green;">'. $val['name'] .'----------------成功</span><br />';
            else
                echo '<span style="color:red;">'. $val['name'] .'----------------失败</span><br />';   
        }
    }

    //售后处理
    public function services()
    {
        header("Content-type:text/html;charset=utf-8");
        $prefix = C("DB_PREFIX");

        $DB = D($this->table);

        $orders = $DB -> query("SELECT f.id, f.name, f.cors, f.corstable, o.cors as orderCors 
            FROM {$prefix}{$this->table} f 
            LEFT JOIN {$prefix}service o ON o.id=f.order 
            WHERE f.`come`='service'");

        foreach ($orders as $key => $val) 
        {
            if($val['orderCors'] < 1)
            {
                echo '<span style="color:blue;">'. $val['name'] .'----------------对应订单异常</span><br />';
                continue;
            }    

            if((int)$val['cors'] > 0)
            {
                echo '<span style="color:gray;">'. $val['name'] .'----------------已存在</span><br />';
                continue;
            }

            $result = $DB -> where("`id`={$val['id']}") -> save(array('cors'=>$val['orderCors'], 'corstable'=>'client'));
            if(false !== $result)
                echo '<span style="color:green;">'. $val['name'] .'----------------成功</span><br />';
            else
                echo '<span style="color:red;">'. $val['name'] .'----------------失败</span><br />';   
        }
    }

}