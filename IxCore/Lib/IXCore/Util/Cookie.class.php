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
 * Cookie管理类
 +------------------------------------------------------------------------------
 * @category   IXCore
 * @package  IXCore
 * @subpackage  Util
 * @author    page7 <zhounan0120@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class Cookie extends Base
{
    // 判断Cookie是否存在
    static function is_set($name) {
        return isset($_COOKIE[C('COOKIE_PREFIX').$name]);
    }

    // 获取某个Cookie值
    static function get($name) {
        $value   = $_COOKIE[C('COOKIE_PREFIX').$name];
        if(C('COOKIE_SECRET_KEY')) {
            $value   =  self::_decrypt($value,C('COOKIE_SECRET_KEY'));
        }else{
            $Input = Input::getInstance();
            $value   = $Input->cookie(C('COOKIE_PREFIX').$name);
        }
        return $value;
    }

    // 设置某个Cookie值
    static function set($name,$value,$expire='',$path='',$domain='') {
        if($expire=='') {
            $expire =   C('COOKIE_EXPIRE');
        }
        if(empty($path)) {
            $path = C('COOKIE_PATH');
        }
        if(empty($domain)) {
            $domain =   C('COOKIE_DOMAIN');
        }
        $expire =   !empty($expire)?    NOW+$expire   :  0;
        if(C('COOKIE_SECRET_KEY')) {
            $value   =  self::_encrypt($value,C('COOKIE_SECRET_KEY'));
        }
        setcookie(C('COOKIE_PREFIX').$name, $value,$expire,$path,$domain);
        $_COOKIE[C('COOKIE_PREFIX').$name]  =   $value;
    }

    // 删除某个Cookie值
    static function delete($name) {
        Cookie::set($name,'',NOW-3600);
        unset($_COOKIE[C('COOKIE_PREFIX').$name]);
    }

    // 清空Cookie值
    static function clear() {
        $prefix = strlen(C('COOKIE_PREFIX'));
        foreach ($_COOKIE as $key => $value){
            self::delete(substr($key,$prefix));
        }
        unset($_COOKIE);
    }

    static private function _encrypt($value,$key){
       $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
       $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
       $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $value, MCRYPT_MODE_ECB, $iv);
       return trim(base64_encode($crypttext));
    }

    static private function _decrypt($value,$key){
       $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
       $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
       $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($value), MCRYPT_MODE_ECB, $iv);
       return trim($decrypttext);
    }
}
?>