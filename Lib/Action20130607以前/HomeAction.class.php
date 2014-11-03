<?php
/**
 * 用户个人管理Action
 *
 * @author Page7
 * @category Police OA 2012 pack1
 * @copyright Copyright(c) 2011 Interidea.org
 * @version $Id$
 */

class HomeAction extends Action
{
    public function _initialize()
    {
        $exception = array('inform');
        include_once(LIB_PATH.'_initialize.php');
    }



    /**
     + -----------------------------------------------------------------------
     *  用户修改密码
     + -----------------------------------------------------------------------
     */
    public function password()
    {
        if($_POST)
        {
            if(!$_POST['oldpassword'] || !$_POST['password'] || !$_POST['passwordcheck'])
                $this -> success('密码信息不完整，请准确填写新旧密码！', 3, 'User/password');
            if($_POST['password'] != $_POST['passwordcheck'])
                $this -> success('两次密码输入不一致，请重新输入！', 3, 'User/password');
            //取用户信息
            $DB_User = D('user');
            $uid = Session::get('uid');
            $user = $DB_User -> find("`uid`='{$uid}'");
            //验证旧密码
            $md = strlen($user['username'])>3 ? substr($user['username'],0,4) : $user['username'];
            $oldpassword = md5($_POST['oldpassword'].$md);
            if($oldpassword != $user['password'])
                $this -> success('旧密码不正确！', 3, 'User/password');
            //新密码
            $newpassword = md5($_POST['password'].$md);
            if($DB_User -> save(array('uid'=>$uid, 'password'=>$newpassword)))
            {
                $this -> success('密码修改成功！', 3, 'User/password');
            }else{
                $this -> success('密码修改失败！', 3, 'User/password');
            }
        }
        $this -> display();
    }
    //over password



    /**
     + -----------------------------------------------------------------------
     *  个人信箱
     + -----------------------------------------------------------------------
     */
    public function mail()
    {
        $uid = Session::get('uid');
        //发信 或 回复
        if($_POST)
        {
            $DB_Mail = D('user_mail');
            //--------------------------------------------- 发 信
            if(!isset($_POST['reply']))
            {
                $name = array_filter(explode(',', $_POST['name']));
                if(!$name)
                    $this -> success('收件人填写不规范！', 3);
                if(!$_POST['title'])
                    $this -> success('邮件标题不能为空！', 3);
                $_user = array();
                foreach($name as $value)
                {
                    if(strpos($value, '('))
                    {
                        $value = explode('(', substr($value, 0, -1));
                        $_user[] = "`name`='{$value[0]}'".($value[1] ? " AND `num`='{$value[1]}'" : "");
                    }else{
                        if(intval($value))
                        {
                            $_user[] = "`num`='{$value}'";
                        }else{
                            $_user[] = "`name`='{$value}'";
                        }
                    }
                }
                $condition = '('.implode(')OR(', $_user).')';
                $DB_User = D('user');
                $users = $DB_User -> findAll($condition, 'uid');
                $mail = array();
                foreach($users as $value)
                {
                    if($value['uid'] != $uid)
                        $mail[] = array('uid'=>$value['uid'], 'title'=>$_POST['title'], 'content'=>$_POST['content'], 'writer'=>$uid, 'time'=>NOW);
                }
                if($DB_Mail -> addAll($mail))
                {
                    $this -> success('发送成功！', 2, 'Home/mail/type/send');
                }else{
                    $this -> success('发送失败！请重试~', 3);
                }
            }else{
                //---------------------------------------------------------------- 回 复
                if(!$_POST['content']) $this -> error('内容不能为空！', true);
                $reply = (int)$_POST['reply'];
                $rmail = $DB_Mail -> find("`id`='{$reply}'", 'id,uid,title,writer,reply');
                if(!$rmail) $this -> error('数据错误，该邮件不存在！', true);
                $mail = array(
                    'uid' => $rmail['writer'] == $uid ? $rmail['uid'] : $rmail['writer'],
                    'writer' => $uid,
                    'title' => strpos($rmail['title'], '回复:') === 0 ? $rmail['title'] : '回复:'.$rmail['title'],
                    'content' => $_POST['content'],
                    'read'  => 0,
                    'reply' => $rmail['reply'] ? $rmail['reply'] : $rmail['id'],
                    'time' => NOW,
                );
                if($id = $DB_Mail -> add($mail))
                {
                    $this -> ajaxReturn($id, '', 1);
                }else{
                    $this -> error('发送失败！请重试~', true);
                }
            }
        }

        $type = isset($_GET['type']) ? $_GET['type'] : '';

        if($type == 'new'){
            $this -> display('mail_new');

        }else if($type == 'send'){

            //------------------------------------------------------ 发 件 箱
            $DB_Mail = D('user_mail', true);
            $DB_Mail -> link('writer', 'BELONG_TO', 'writer', 'user', 'uid', 'uid,username,name,num');
            $DB_Mail -> link('user', 'BELONG_TO', 'uid', 'user', 'uid', 'uid,username,name,num');
            $list = $DB_Mail -> xfindAll('`writer`='.$uid.' AND `reply`=0', 'id,uid,title,content,writer,time,read,reply,system', 'read desc, id desc');

        }else if($type == 'del'){

            // ----------------------------------------------------- 删 除
            $id = isset($_GET['id']) ? (int)$_GET['id'] : '';
            if($id){
                $DB_Mail = D('user_mail');
                $result = $DB_Mail -> delete("(`uid`='{$uid}' OR `writer`='{$uid}') AND (`id`='{$id}' OR `reply`='{$id}')");
                if($result)
                {
                    $this -> redirect("mail", "Home");
                }else{
                    $this -> success('删除失败，请重试', 3);
                }
            }else{
                $DB_Mail = D('user_mail');
                if($DB_Mail -> delete("`uid`='{$uid}'") )
                {
                    $this -> redirect("mail", "Home");
                }else{
                    $this -> success('删除失败，请重试', 3);
                }
            }

        }else{
            //------------------------------------------------------ 标 记 已 读
            $id = isset($_GET['id']) ? $_GET['id'] : '';
            if(is_numeric($id))
            {
                $DB_Mail = D('user_mail');
                if($mail = $DB_Mail -> find("`id`='{$id}'"))
                {
                    $result = $DB_Mail -> setField('read', 1, "(`reply`='{$id}' AND `uid`='{$uid}')".($mail['uid'] == $uid ? " OR `id`='{$id}'" : ""));
                    $this -> ajaxReturn($result, '', 1);
                }else{
                    $this -> error('查询数据失败，请重试！', true);
                }
            }else{
                if($id == 'all')
                {
                    $DB_Mail = D('user_mail');
                    if( $DB_Mail -> setField('read', 1, "`uid`='{$uid}'") )
                    {
                        $this -> redirect("mail", "Home");
                    }else{
                        $this -> success('设置失败！', 3);
                    }
                }
            }

            //------------------------------------------------------ 收 件 箱
            $DB_Mail = D('user_mail', true);
            $DB_Mail -> link('writer', 'HAS_ONE', 'writer', 'user', 'uid', 'uid,username,name,num');
            $list = $DB_Mail -> xfindAll('`uid`='.$uid.' AND `reply`=0', 'id,title,content,writer,time,read,reply,system', 'read asc, id desc');
        }

        if($list)
        {
            $_list = array();
            foreach($list as $key => $value)
            {
                $_list[$value['id']] = $value;
            }

            $list = $DB_Mail -> xfindAll('`reply` IN ('.implode(',', array_keys($_list)).')', 'id,uid,title,content,writer,time,read,reply,system', 'id asc');
            if($list)
                foreach($list as $key => $value)
                {
                    if(!is_array($_list[$value['reply']]['reply'])) $_list[$value['reply']]['reply'] = array();
                    if($value['writer']['uid'] == $uid)
                        $value['writer']['name'] = '我';
                    $_list[$value['reply']]['reply'][] = $value;
                    if($value['read'] == 0 && $value['writer']['uid']!=$uid)
                        $_list[$value['reply']]['newreply'] = 1;
                }
        }
        $this -> assign('list', $_list);
        $this -> display($type == 'send' ? 'mail_send' : 'mail');

    }
    //end mail


    /**
     + -----------------------------------------------------------------------
     *  个人通知
     + -----------------------------------------------------------------------
     */
    public function inform()
    {
        $uid = Session::get('uid');

        //设置已读
        if(isset($_GET['read']))
        {
            $DB_Inform = D('user_inform');
            $result = $DB_Inform -> save(array('read'=>1), "`uid`='{$uid}'");
            $this -> ajaxReturn($result, '', 1);
        }

        //获取权限
        if(isset($_GET['ajax']))
        {
            $can_do = $this -> get('can_do');
            $mail = 0;
            if(isset($can_do['Home']['mail']))
            {
                if(!$_GET['more'])
                {
                    //所有未读数量
                    $DB_Mail = D('user_mail');
                    $mail = $DB_Mail -> count("`uid`='{$uid}' AND `read`='0'");
                }else{
                    $DB_Mail = D('user_mail', true);
                    $DB_Mail -> link('mail', 'HAS_ONE', 'reply', 'user_mail', 'id', 'id,uid,writer');
                    $list = $DB_Mail -> xfindAll("`uid`='{$uid}' AND `read`='0'", 'id,reply,uid,writer,read');
                    $to = 0; $form = 0;
                    foreach($list as $key => $value)
                    {
                        if($value['mail'])
                        {
                            if($value['mail']['uid'] == $uid)
                                $form++;
                            else
                                $to++;
                        }else{
                            if($value['uid'] == $uid)
                                $form++;
                            else
                                $to++;
                        }
                    }
                    $mail = array('count'=>$form+$to, 'form'=>$form, 'to'=>$to);
                }
            }

            //通告
            $DB_Inform = D('user_inform', true);
            $DB_Inform -> link('', 'BELONG_TO', 'inform', 'inform', 'id');
            $inform = $DB_Inform -> xfindAll("`uid`='{$uid}' AND `read`='0'");
            $this -> ajaxReturn(array('inform'=>$inform, 'mail'=>$mail), '', 1);

        }else{

            if(isset($_GET['view']))
            {
                $id = (int)$_GET['view'];
                //通告内容
                $DB_Inform = D('user_inform', true);
                $DB_Inform -> link('', 'BELONG_TO', 'inform', 'inform', 'id');
                $inform = $DB_Inform -> xfind("`uid`='{$uid}' AND `inform`='{$id}'");
                $this -> assign($inform);
                $this -> display('inform_view');
            }

            if(isset($_GET['delete']))
            {
                $id = $_GET['delete'];
                //删除通告
                $DB_Inform = D('user_inform');
                $result = $DB_Inform -> delete("`uid`='{$uid}'" . ($id == 'all' ? '' : " AND `inform`='{$id}'"));
                if(false !== $result)
                {
                    $this -> success('删除成功！', 2, 'Home/inform');
                }else{
                    $this -> success('删除失败，请重试！', 3);
                }
            }

            //简单通告列表
            $DB_Inform = D('user_inform', true);
            $DB_Inform -> link('', 'BELONG_TO', 'inform', 'inform', 'id', 'id,title,time');

            import('Extend.Util.Page');
            $Page = new Page($DB_Inform->count("`uid`='{$uid}'"), 15);

            $inform = $DB_Inform -> xfindAll("`uid`='{$uid}'", 'uid,inform,read', 'inform desc', $Page->firstRow.','.$Page->listRows);
            $page = $Page -> show();
            $this -> assign('page', $page);
            $this -> assign('list', $inform);
            $this -> display();
        }
    }
    //over inform


    /**
     + -----------------------------------------------------------------------
     *  个人资料
     + -----------------------------------------------------------------------
     */
     public function intro()
     {
        if($_POST)
        {
            $uid = Session::get('uid');
            $data = array(
                'uid'      => $uid,
                'birthday' => $_POST['birthday'],
            );
            $DB_User = D('user');
            if($DB_User -> save($data))
            {
                $info = array('uid'=>$uid, 'value'=>serialize($_POST['info']));
                $DB_Info = D('user_info');
                if(!$DB_Info -> find("`uid`={$uid}"))
                {
                    $info['uid'] = $uid;
                    $result = $DB_Info -> add($info);
                }else{
                    $result = $DB_Info -> save($info, "`uid`={$uid}");
                }
                if($result)
                {
                    $this -> success('保存成功！', 2, 'Home/intro/');
                }else{
                    $this -> success('扩展资料保存失败，请重试', 3);
                }
            }else{
                $this -> success('保存失败，请重试', 3);
            }
        }
        //获取数据
        $id = Session::get('uid');
        $DB_User = D('user', true);
        $DB_User -> link('info', 'HAS_ONE', 'uid', 'user_info', 'uid');
        $user = $DB_User -> xfind("`uid`='{$id}'");
        $user['info'] = unserialize($user['info']['value']);
        $this -> assign($user);

        //扩展数据
        $DB_Config = D('system_config');
        $data = $DB_Config -> find("`name`='user_info'");
        if($data){
            $data = unserialize($data['value']);
            $this -> assign('extend', $data);
        }

        $this -> display();
     }
     //over intro


}
?>