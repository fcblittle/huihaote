<?php
// +----------------------------------------------------------------------
// | IXCore
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://www.interidea.org All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: page7 <zhounan0120@gmail.com>
// +----------------------------------------------------------------------
// $Id$

/**
 +------------------------------------------------------------------------------
 * Email 发送类，基于phpmailer
 +------------------------------------------------------------------------------
 * @category   Extend
 * @package  Extend
 * @subpackage  Net
 * @author    page7 <zhounan0120@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class Email extends Base
{//类定义开始

    protected $formMail = '';

    protected $Mail = '';

    public function __construct($formMail='', $host='', $port=0, $username='', $password='')
    {
        //判断邮件来源及主机
        $this -> formMail = self::checkMail($formMail ? $formMail : C('SMTP_ADDRESS'));
        $form = explode('@', $this -> formMail['email']);
        $host = $host ? $host : (C('SMTP_HOST') ? C('SMTP_HOST') :'smtp.'.$form[1]);
        $port = $port ? $port : C('SMTP_PORT');

        //实例化PHPMailer
        require_once(dirname(__FILE__).'/PHPMailer/class.phpmailer.php');
        $this -> Mail = new PHPMailer();
        $this -> Mail -> IsSMTP();
        if($host) $this -> Mail -> Host = $host;
        if($port) $this -> Mail -> Port = $port;
        $this -> Mail -> SMTPAuth = true;
        $this -> Mail -> Username = $username ? $username : (C('SMTP_ADMIN_USERNAME') ? C('SMTP_ADMIN_USERNAME') : $form[0]);
        $this -> Mail -> Password = $password ? $password : C('SMTP_ADMIN_PWD');
        if(defined("LANG_SET"))
            $this -> Mail -> SetLanguage(LANG_SET);
    }

    //发送邮件
    public function send($to, $subject="", $body="", $altbody="", $cc="", $bcc="")
    {
        //添加发送者
        $this -> Mail -> FromName = $this -> formMail['name'];
        $this -> Mail -> From = $this -> formMail['email'];

        $this -> Mail -> ClearAllRecipients();
        //添加收件人
        if(!is_array($to)) $to = explode(',', $to);
        foreach ($to as $val)
            if($touser = self::checkMail($val))
                $this -> Mail -> AddAddress($touser['email'], $touser['name']);

        //添加抄送人
        if(!is_array($cc)) $cc = explode(',', $cc);
        if($cc)
            foreach ($cc as $val)
                if($touser = self::checkMail($val))
                    $this -> Mail -> AddCC($touser['email'], $touser['name']);

        //添加暗送
        if(!is_array($bcc)) $bcc = explode(',', $bcc);
        if($bcc)
            foreach ($bcc as $val)
                if($touser = self::checkMail($val))
                    $this -> Mail -> AddBCC($touser['email'], $touser['name']);

        $this -> Mail -> Subject = $subject;
        $this -> Mail -> Body = $body;
        $this -> Mail -> AltBody = $altbody;
        return $this -> Mail -> Send();
    }

    //添加附件
    public function addAttachment($path, $name, $encoding = 'base64', $type = 'application/octet-stream')
    {
        $this -> Mail -> AddAttachment($path, $name, $encoding, $type);
    }

    //设定发送内容为html
    public function isHTML()
    {
        $this -> Mail -> IsHTML();
    }

    //设定编码
    public function setChar($char)
    {
        $this -> Mail -> $CharSet = $char;
    }

    //检测邮箱地址是否正确
    static function checkMail($Address)
    {
        if(strpos($Address, '>') === false) //检测是否有称呼
            return array('email' => $Address, 'name' => '');
        $Address = explode('<', substr($Address, 0, -1));
        if(count($Address) == 2)                             //邮件和用户名必须合法
            return array('email' => $Address[1], 'name' => $Address[0]);
        return false;
    }

    //获取错误
    public function getError()
    {
        return $this -> Mail -> ErrorInfo;
    }

//类定义结束
}
?>