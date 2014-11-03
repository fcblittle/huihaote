<?php
/**
* 系统设置Action
*
* @author Page7
* @category Police OA 2012 pack1
* @copyright Copyright(c) 2011 Interidea.org
* @version $Id$
*/

class SystemAction extends Action
{
    public function _initialize()
    {
        include_once(LIB_PATH.'_initialize.php');
    }

    /**
    + -----------------------------------------------------------------------
    *  系统设置
    + -----------------------------------------------------------------------
    */

    public function index()
    {
        $System = D('system_config');

        //保存设置
        if($_POST)
        {

            $ip = array();
            foreach($_POST['ipstart'] as $key => $value)
                if($value)
                    $ip[] = $value.($_POST['ipend'][$key] ? '--'.$_POST['ipend'][$key] : '');

            $ip = implode(',', $ip);
            $data = array('id'=>6 ,'name' => 'ip', 'value' => $ip);
            $result = $System -> save($data);
            if($result)
            {
                $this -> success("保存成功！", 1);
            }else{
                $this -> success("保存失败，请重试", 3);
            }
        }

        //允许访问ip
        $ip = $System -> find("`id`=6");

        if($ip['value'])
        {
            $ip = explode(',', $ip['value']);
            foreach($ip as $key => $value)
            {
                
                $ip[$key] = explode('--', $value);
                if(!isset($ip[$key][1])) $ip[$key][1] = '';
            }
        }else{
            $ip = array();
        }
        $this -> assign('ip', $ip);


        $this -> display();
    }




    /**
    + -----------------------------------------------------------------------
    *  资料扩展
    + -----------------------------------------------------------------------
    */
    public function info()
    {
        $DB = D('system_config');
        if($_POST)
        {
            $data = array();
            foreach($_POST['name'] as $key => $value)
            {
                if($value)
                {
                    $data[] = array('name'=>$value, 'type'=>$_POST['type'][$key]);
                }
            }
            $data = serialize($data);
            $type = $_POST['key'];
            if($DB -> save(array('id'=> $type, 'value'=>$data)))
            {
                $this -> success("保存成功！", 1, '/System/info/type/'.$type);
            }else{
                $this -> success("保存失败，请重试", 3);
            }
        }

        $type = isset($_GET['type']) ? $_GET['type'] : 1;
        switch($type)
        {
            case 1:
                $this -> assign('name', '用户');
                break;
            case 2:
                $this -> assign('name', '仓库');
                break;
            case 3:
                $this -> assign('name', '货品');
                break;
            case 4:
                $this -> assign('name', '客户');
                break;
            case 5:
                $this -> assign('name', '供应商');
                break;
        }
        $this -> assign('type', $type);

        $data = $DB -> find("`id`='{$type}'");
        $data = unserialize($data['value']);
        $this -> assign('list', $data);
        $this -> display();
    }




    /**
    + -----------------------------------------------------------------------
    *  系统节点设置
    + -----------------------------------------------------------------------
    */
    public function node()
    {
        $DB = D('system_node');

        //操作
        if($_POST)
        {
            switch($_POST['type'])
            {
                case 'add':
                    $count = $DB -> count();
                    if(isset($_POST['module']))
                    {
                        $data = array(
                            'name' => htmlspecialchars($_POST['name']),
                            'm'    => $_POST['module'],
                            'a'    => '',
                            'hide' => 0,
                            'sort' => $count,
                        );
                    }else{
                        $parentid = (int)$_POST['parent'];
                        if(!$parentid) $this -> error('数据错误！', true);
                        if(!$parent = $DB -> find("`id`='{$parentid}'")) $this -> error('数据错误！', true);
                        $data = array(
                            'name' => htmlspecialchars($_POST['name']),
                            'm'    => $parent['m'],
                            'a'    => $_POST['action'],
                            'hide' => $_POST['hide'],
                            'sort' => $count,
                        );
                    }
                    //是否已经存在
                    if($DB -> find(array('m'=>$data['m'], 'a'=>$data['a'])))
                        $this -> error('已存在相同节点！', true);
                    if($result = $DB -> add($data))
                    {
                        $data['id'] = $result;
                        $this -> ajaxReturn($data, '', 1);
                    }else{
                        $this -> error('添加失败，请重试！', true);
                    }
                break;
                case 'edit':
                    if($id = (int)$_POST['id'])
                    {
                        $node = $DB -> find("`id`='{$id}'");
                        if(!$node) $this -> error('数据错误！', true);
                        $data = array(
                            'id' => $id,
                            'name' => htmlspecialchars($_POST['name']),
                            'hide' => $node['a'] ? (int)$_POST['hide'] : 0
                        );
                        if($result = $DB -> save($data))
                        {
                            $this -> ajaxReturn($result, '', 1);
                        }else{
                            $this -> error('修改失败！', true);
                        }
                    }else{
                        $this -> error('数据错误！', true);
                    }
                break;
                case 'del':
                    if($id = (int)$_POST['id'])
                    {
                        $node = $DB -> find("`id`='{$id}'");
                        if(!$node) $this -> error('数据错误！', true);
                        if($node['a'])
                        {
                            $con = "`id`='{$id}'";
                        }else{
                            $con = "`m`='{$node['m']}'";
                        }
                        if($result = $DB -> delete($con))
                        {
                            $this -> ajaxReturn($result, '', 1);
                        }else{
                            $this -> error('删除失败！', true);
                        }
                    }else{
                        $this -> error('数据错误！', true);
                    }
                break;
                default:
                    if(!isset($_POST['m_sort'])) $this -> success('数据错误！', 3);
                    asort($_POST['m_sort']);
                    $i = 1;
                    $m = array();
                    foreach($_POST['m_sort'] as $key => $value)
                    {
                        $m[] = array('id'=>$key, 'sort'=>$i);
                        $i++;
                        asort($_POST['a_sort'][$key]);
                        foreach($_POST['a_sort'][$key] as $k => $v)
                        {
                            $m[] = array('id'=>$k, 'sort'=>$i);
                            $i++;
                        }
                    }
                    if($DB -> save($m))
                    {
                        $this -> success('保存成功！', 1, 'System/node');
                    }else{
                        $this -> success('保存失败！', 3);
                    }
            }
        }

        //查询
        $list = $DB -> order("sort asc, id asc") -> findAll();
        $data = array();
        foreach($list as $value)
        {
            if(!isset($data[$value['m']])) $data[$value['m']] = array('id'=>'', 'name'=>'', 'child'=>array());
            if($value['a'])
            {
                $data[$value['m']]['child'][] = $value;
            }else{
                $data[$value['m']]['id'] = $value['id'];
                $data[$value['m']]['name'] = $value['name'];
            }
        }
        $this -> assign('list', $data);
        $this -> display();
    }


    /**
    + -----------------------------------------------------------------------
    *  系统数据表优化
    + -----------------------------------------------------------------------
    */
    public function optimize()
    {
        $db = D() -> query('SHOW TABLE STATUS FROM '.C('DB_NAME'));
        $free = 0;
        $need = array();
        foreach($db as $key => $value)
        {
            if($value['Data_free'])
            {
                $free += $value['Data_free'];
                $need[] = $value['Name'];
            }
        }
        if(isset($_GET['size']) && $free == $_GET['size'])
        {
            $result = D() -> query('OPTIMIZE TABLE `'.implode('`,`', $need).'`');
            if($result)
            {
                $this -> success('优化成功。', 1, 'System/optimize');
            }else{
                $this -> success('优化失败，请重试！', 3, 'System/optimize');
            }
        }

        $this -> assign('free', $free);
        $this -> display();
    }


    /**
     * 系统配置
     */
    public function config()
    {
        if($_POST)
        {
            $configs = array();
            foreach ($_POST['intro'] as $key=>$val)
            {
                //删除
                if(!trim($val) && $_POST['id'][$key])
                {
                    D("System_config") -> delete("`id`={$_POST['id']} AND `type`='config'");

                //修改
                }else if(trim($val) && $_POST['id'][$key]){
                    $data = array(
                        'name' => $_POST['name'][$key],
                        'intro' => $val,
                        'value' => $_POST['value'][$key],
                    );
                    D("System_config") -> where("`id`={$_POST['id'][$key]}") -> save($data);

                //添加
                }else if(trim($val) && !$_POST['id'][$key]){
                    $data = array(
                        'name' => $_POST['name'][$key],
                        'intro' => $val,
                        'value' => $_POST['value'][$key],
                        'type'  => 'config',
                    );
                    D("System_config") -> add($data);

                }else if(!trim($val)){
                    continue;
                }
            }

             $this -> success("操作成功", 1, 'System/config');
        }

        $this -> assign('list', D("System_config")->findAll("`type`='config'", '*', 'id asc'));

        $this -> assign('name', '基础数据');
        $this -> display('config');
    }
}
?>