<?php
/**
 * 公共访问端口判断Action
 *
 * @author Page7
 * @category Police OA 2012 pack1
 * @copyright Copyright(c) 2011 Interidea.org
 * @version $Id$
 */

class PublicAction extends Action
{

    public function login()
    {
        include_once(LIB_PATH.'_allow.php');

        if($_POST)
        {
            $username = $_POST['username'];
            $md = strlen($username)>3 ? substr($username,0,4) : $username;
            $password = md5($_POST['password'].$md);
            $DB_User = D("user");
            if($user = $DB_User -> find(array('username'=>$username, 'password'=>$password)))
            {
                Session::set('type', 'neixiao');
                Session::set('uid', $user['uid']);
                Session::set('name', $user['name']);
                //修改登录时间
                $data = array(
                    'uid' => $user['uid'],
                    'lasttime' => $user['nowtime'],
                    'nowtime' => NOW,
                );
                $DB_User -> save($data);
                $this -> redirect("index", "Index");
            }else{
                $this -> success("用户名或密码错误，请重试~", 3);
            }
        }
        $this -> display();
    }




    public function logout()
    {
        Session::destroy();
        Cookie::clear();
        $this -> redirect('login');
    }




    //文件上传
    public function upload()
    {
        if(!Session::is_set('uid'))
        {
            $data = array('error'=> '您还没有登录或登陆超时！');
        }else{

            if($_FILES)
            {
                import("Extend.Net.UploadFile");
                $File = new UploadFile();
                $File -> maxSize  = 2000000;
                $File -> savePath = APP_PATH.'/Files/editor/';
                $File -> allowExts = array('jpg', 'jpe', 'jpeg', 'bmp', 'png', 'gif');
                $File -> thumb = false;
                $File -> uploadReplace = true;
                $fileresult = $File -> upload();
                if($fileresult)
                {
                    $file = $File -> getUploadFileInfo();
                    $data = array('done'=>0, 'url'=>'./Files/editor/'.$file[0]['savename'], 'width'=>200);
                }else{
                    $data = array('error'=> $File -> getErrorMsg());
                }
            }else{
                $data = array('error'=> '没有选择上传的文件！');
            }
        }
        echo '<script type="text/javascript">try{parent.nicUploadButton.statusCb('.json_encode($data).')}catch(e){alert("illegal access error"); }</script>';
    }
    //over





    //ajax文件上次
    public function ajaxUpload()
    {
        if(!Session::is_set('uid'))
        {
            $this -> ajaxReturn('', '您还没有登录或登陆超时！', 0);
        }else{

            if($_FILES)
            {
                import("Extend.Net.UploadFile");
                $File = new UploadFile();
                $File -> maxSize  = 2000000;
                $File -> savePath = APP_PATH.'/Files/uploads/';
                $File -> thumb = false;
                $File -> uploadReplace = true;
                $fileresult = $File -> upload();
                if($fileresult)
                {
                    $file = $File -> getUploadFileInfo();
                    $this -> ajaxReturn('/Files/uploads/'.$file[0]['savename'], '', 1);
                }else{
                    $this -> ajaxReturn('', $File -> getErrorMsg(), 0);
                }
            }else{
                $this -> ajaxReturn('', '您没有选择上传的文件！', 0);
            }
        }
    }
}
?>