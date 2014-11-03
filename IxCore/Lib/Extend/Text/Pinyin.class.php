<?php
// +----------------------------------------------------------------------
// | IxChange
// +----------------------------------------------------------------------
// | Copyright (c) 2008 http://interidea.org All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: page7 <page7@interidea.org>
// +----------------------------------------------------------------------
// $Id$

/**
 +-----------------------------------------------------------
 * 汉字转拼音类
 * 仅支持UTF-8类型，需要加载py.dat
 +-----------------------------------------------------------
 * @category   Extend
 * @package  Extend
 * @subpackage  Text
 * @author    page7 <page7@interidea.org>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */

class Pinyin extends Base
{//类定义开始

    var $_dat = 'py.dat';
    var $_fd  = false;

    function __construct($pdat = '')
    {
        if ('' == $pdat)
            $pdat = dirname(__FILE__).'/'.$this->_dat;

        $this -> _fd = @fopen($pdat, 'rb');
        if (!$this -> _fd)
        {
            trigger_error("unable to load PinYin data file `$pdat`", E_USER_WARNING);
            return false;
        }
        return true;
    }

     function __destruct()
     {
        if ($this -> _fd)
        {
            @fclose($this -> _fd);
            $this -> _fd = false;
        }
     }

    /**
     +-----------------------------------------
     * 获取拼音
     +-----------------------------------------
     * @param string $zh
     +-----------------------------------------
     * @return string
     +-----------------------------------------
     */
    function get($zh)
    {
        if (strlen($zh) > 3)
        {
            $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
            preg_match_all($pa, $zh, $pazh);
            $ret[0] = '';
            foreach ($pazh[0] as $v)
            {
                if(preg_match('/[\xe4-\xe9]/', $v))
                $ret[0] .= $this -> get($v);
                else
                $ret[0] .= $v;
            }
            return $ret[0];

        }else{

            $zh = iconv('UTF-8', 'GBK', $zh);
            if (!$this -> _fd && !$this -> load())
            return false;

            $high = ord($zh[0]) - 0x81;
            $low  = ord($zh[1]) - 0x40;

            $off = ($high<<8) + $low - ($high * 0x40);

            fseek($this -> _fd, $off * 8, SEEK_SET);
            $ret = fread($this ->_fd, 8);
            $ret = preg_split("/[\s,]+/", $ret);
            return $ret[0];
        }
    }

}
?>