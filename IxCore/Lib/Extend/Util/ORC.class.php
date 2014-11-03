<?php
// +----------------------------------------------------------------------
// | IxChange
// +----------------------------------------------------------------------
// | Copyright (c) 2008 http://interidea.org All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: page7 <zhounan0120@gmail.com>
// +----------------------------------------------------------------------
// $Id$

/**
 +------------------------------------------------------------------------------
 * 图像识别类库
 +------------------------------------------------------------------------------
 * @category   Extend
 * @package  Extend
 * @subpackage  Util
 * @author    page7 <zhounan0120@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */

include_once("Image.class.php");

class ORC extends Base
{//类定义开始
    protected $imageInfo;

    protected $data;

    public $maxfontwith = 16;


    public function study($info)
    {
        // 做成字符串
        $data = array();
        $i = 0;
        foreach($this->data as $key => $value)
        {
            $data[$i] = "";
            foreach($value as $skey => $svalue)
            {
                $data[$i] .= implode("",$svalue);
            }
            if(strlen($data[$i]) > $maxfontwith)
                ++$i;
        }

        if(count($data) != count($info))
        {
            echo "设置数据库数据出错";
            print_r($data);
            return false;
        }

        // 设置N级匹配模式
        foreach($info as $key => $value)
        {
            if(isset($this->Keys[0][$value])){
                $percent=0.0;
                similar_text($this->Keys[0][$value], $data[$key],$percent);
                if(intval($percent) < 96)
                {
                    $i=1;
                    $OK = false;
                    while(isset($this->Keys[$i][$value]))
                    {
                        $percent=0.0;
                        similar_text($this->Keys[$i][$value], $data[$key],$percent);
                        if(intval($percent) > 96){
                            $OK = true;
                            break;
                        }
                        ++$i;
                    }
                    if(!$OK){
                        $this->Keys[$i][$value] = $data[$key];
                    }
                }

            }else{
                $this->Keys[0][$value] = $data[$key];
            }
        }
        return true;
    }



    public function run()
    {
        $result="";

        // 做成字符串
        $data = array();
        $i = 0;
        foreach($this->data as $key => $value)
        {
            $data[$i] = "";
            foreach($value as $skey => $svalue)
            {
                $data[$i] .= implode("",$svalue);
            }
            if(strlen($data[$i]) > $maxfontwith)
                ++$i;
        }

        // 进行关键字匹配
        foreach($data as $numKey => $numString)
        {
            $max=0.0;
            $num = 0;
            foreach($this->Keys as $key => $value)
            {
                $FindOk = false;
                foreach($value as $skey => $svalue)
                {
                    $percent=0.0;
                    similar_text($svalue, $numString, $percent);
                    if(intval($percent) > $max)
                    {
                        $max = $percent;
                        $num = $skey;
                        if(intval($percent) > 96){
                            $FindOk = true;
                            break;
                        }
                    }
                }
                if($FindOk)
                    break;
            }
            $result.=$num;
        }

        // 查找最佳匹配数字
        return $result;
    }

    /**
     +----------------------------------------------------------
     * 对生成的字符图形进行处理
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @param int $maxfontwith   字符最大宽度
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function subImg($maxfontwith = 15, $minfontarea = 50)
    {
        //独立单词字符
        $txtData = array();
        //临时用数据
        $x = 0;
        $y = 0;
        //纵向扫描 X 坐标
        $overtxt = false;
        $x_start = false;
        for($i=0; $i<$this->imageInfo['width']; ++$i)
        {
            $x++;
            // Y 坐标
            $y_start = false;
            for($j=0; $j<$this->imageInfo['height']; ++$j)
            {
                if($this -> data[$j][$i] == 1)
                {
                    $y_start = true;
                    $y ++;
                }else{
                    if($y_start){
                        $x_start = true; //换下一列
                        break;
                    }
                    if($j == $this -> imageInfo['height'] - 1 && $y_start == false)
                    {
                        $overtxt = true;
                        break;
                    }
                }
            }
            if($overtxt)
                break;
        }
        exit();

        // 粘连字符分割
        $inum = 0;
        for($num =0; $num < count($data); ++$num)
        {
            $itemp = 5;
            $str = implode("",$data[$num][$itemp]);
            // 超过标准长度
            if(strlen($str) > $this->maxfontwith)
            {
                $len = (strlen($str)+1)/2;
                $flen = strlen($str);
                $ih = 0;
//                $iih = 0;
                foreach($data[$num] as $key => $value)
                {
                    $ix = 0;
                    $ixx = 0;
                    foreach($value as $skey=>$svalue)
                    {
                        if($skey < $len)
                        {
                            $this->data[$inum][$ih][$ix] = $svalue;
                            ++$ix;
                        }
                        if($skey > ($flen-$len-1))
                        {
                            $this->data[$inum+1][$ih][$ixx] = $svalue;
                            ++$ixx;
                        }
                    }
                    ++$ih;
                }
                ++$inum;
            }else{
                $i = 0;
                foreach($data[$num] as $key => $value){
                    $this->data[$inum][$i] = $value;
                    ++$i;
                }

            }
            ++$inum;
        }

        // 去掉0数据
        for($num = 0; $num < count($this->data); ++$num)
        {
            if(count($this->data[$num]) != $this->ImageSize[1])
            {
                echo "分割字符错误";
                die();
            }

            for($i=0; $i < $this->ImageSize[1]; ++$i)
            {
                $str = implode("",$this->data[$num][$i]);
                $pos = strpos($str, "1");
                if($pos === false)
                {
                    unset($this->data[$num][$i]);
                }
            }
        }
    }

    /**
     +----------------------------------------------------------
     * 设置图像地址,并解析
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @param string $Image 图像文件名
     * @param int $filter   干扰过滤程度
     * @param int $wave     jpeg图像色彩容差系数
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function setImage($Image, $filter=4, $wave=40, $precision=5)
    {
        $this -> imageInfo = $imageInfo = Image::getImageInfo($Image);
        $fun = "imagecreatefrom{$imageInfo['type']}";

        file_put_contents(dirname(__FILE__).'/ORC_study/temp.'.$imageInfo['type'], file_get_contents($Image));

        $res = $fun(dirname(__FILE__).'/ORC_study/temp.'.$imageInfo['type']);
        $data = array();
        for($i=0; $i < $imageInfo['height']; ++$i)
        {
            for($j=0; $j < $imageInfo['width']; ++$j)
            {
                $rgb = imagecolorat($res,$j,$i);
                $data[$i][$j] = imagecolorsforindex($res, $rgb);//获取颜色
                if(isset($colors[$rgb]))
                {
                    $colors[$rgb]++;
                }else{
                    $colors[$rgb] = 1;
                }
            }
        }
        arsort($colors);
        $this -> imageInfo['bgcolor'] =  $bgcolor = imagecolorsforindex($res, key($colors));

        //不同背景色标记为1
        for($i=0; $i < $imageInfo['height']; ++$i)
        {
            for($j=0; $j < $imageInfo['width']; ++$j)
            {
                if(!$this -> colorwave($data[$i][$j], $bgcolor, $wave)) //当前点异于背景色
                {
                    $newdata[$i][$j] = 1;
                }else{
                    $newdata[$i][$j] = 0;
                }
            }
        }
        unset($data);

        $this -> data = $newdata;

        // 1的周围数字之和,提高精确度
        for($n=1; $n < $precision; $n++)
        {
            $temp = $this -> data;
            for($i=0; $i < $imageInfo['height']; ++$i)
            {
                for($j=0; $j < $imageInfo['width']; ++$j)
                {
                    if($this -> data[$i][$j] >= 1)
                    {
                        if($this -> conchar($temp, $j, $i, array(1,2,3,7,8,9)) >= 3 * $n
                            && $this -> conchar($temp, $j, $i, array(1,3,4,6,7,9)) >= 3 * $n
                            && $this -> conchar($temp, $j, $i, array(2,3,4,6,7,8)) >= 3 * $n
                            && $this -> conchar($temp, $j, $i, array(1,2,4,6,8,9)) >= 3 * $n
                        )
                        $this -> data[$i][$j] = $this -> conchar($temp, $j, $i); //当前点周围数字为1的个数
                    }
                }
            }
        }

        //去掉干扰点
        $this -> removeDisturb($filter);
    }

    private function removeDisturb($filter)
    {
        for($i=0; $i < $this -> imageInfo['height']; ++$i)
        {
            for($j=0; $j < $this -> imageInfo['width']; ++$j)
            {
                if($this -> data[$i][$j] <= $filter) //当前点独立
                {
                    $this -> data[$i][$j] = 0;
                }else{
                    $this -> data[$i][$j] = 1;
                }
            }
        }
    }

    /**
     +----------------------------------------------------------
     * 获取连续的色彩点个数
     +----------------------------------------------------------
     * @static
     * @access private
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    private function conchar($data, $x, $y, $direction = array(1,2,3,4,6,7,8,9))
    {
        if(isset($data[$y][$x]))
        {
            $point = $data[$y][$x];
            $num = 0;
            // 上
            if(isset($data[$y-1][$x]) && in_array(2,$direction)){
                $num = $num + $data[$y-1][$x];
            }
            // 下
            if(isset($data[$y+1][$x]) && in_array(8,$direction)){
                $num = $num + $data[$y+1][$x];
            }
            // 左
            if(isset($data[$y][$x-1]) && in_array(4,$direction)){
                $num = $num + $data[$y][$x-1];
            }
            // 右
            if(isset($data[$y][$x+1]) && in_array(6,$direction)){
                $num = $num + $data[$y][$x+1];
            }
            // 上左
            if(isset($data[$y-1][$x-1]) && in_array(1,$direction)){
                $num = $num + $data[$y-1][$x-1];
            }
            // 上右
            if(isset($data[$y-1][$x+1]) && in_array(3,$direction)){
                $num = $num + $data[$y-1][$x+1];
            }
            // 下左
            if(isset($data[$y+1][$x-1]) && in_array(7,$direction)){
                $num = $num + $data[$y+1][$x-1];
            }
            // 下右
            if(isset($data[$y+1][$x+1]) && in_array(9,$direction)){
                $num = $num + $data[$y+1][$x+1];
            }
            return $num;
        }
        return 0;
    }

    /**
     +----------------------------------------------------------
     * 获取色彩点与比较色彩的容差
     +----------------------------------------------------------
     * @static
     * @access private
     +----------------------------------------------------------
     * @param int $color 当前点颜色
     * @param int $other 被比较颜色
     * @param int $wave  波动容差
     +----------------------------------------------------------
     * @return mixed  波动范围内返回1，否则为0
     +----------------------------------------------------------
     */
    private function colorwave($color, $other, $wave)
    {
        $rgb_check = 0;
        if($color['red']   > ($other['red'] - $wave)   && $color['red']   < ($other['red']   + $wave)) $rgb_check++;
        if($color['green'] > ($other['green'] - $wave) && $color['green'] < ($other['green'] + $wave)) $rgb_check++;
        if($color['blue']  > ($other['blue'] - $wave)  && $color['blue']  < ($other['blue']  + $wave)) $rgb_check++;
        if($rgb_check >= 2)
        {
            return 1;
        }else{
            return 0;
        }
    }

    /**
     +----------------------------------------------------------
     * 获取数据
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @param int $test   测试输出
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function getData($test=false)
    {
        if($test)
        {
            echo "Image : <img src='".IXCORE_PATH."/Lib/Extend/Util/ORC_study/temp.{$this->imageInfo['type']}' /><br /><br />";
            echo "Image Type : {$this->imageInfo['type']}<br /><br />";
            echo "Image BgColor : <span style='background-color:rgb({$this->imageInfo['bgcolor']['red']},{$this->imageInfo['bgcolor']['green']},{$this->imageInfo['bgcolor']['blue']})'>&nbsp;&nbsp;&nbsp;&nbsp;</span><br /><br />";
            echo "<span style='font-size:9px; line-height:5px;'>";
            foreach($this->data as $key => $value)
            {
                foreach($value as $k => $v)
                {
                    echo $v;
                }
                echo '<br />';
            }
            echo "</span>";
        }
        return $this->data;
    }

}//类定义结束
?>