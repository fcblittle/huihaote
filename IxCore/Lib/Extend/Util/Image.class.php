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

//扩展imagecreatefrombmp方法
function imagebmp($img, $file = "", $RLE = 0) {

    $ColorCount = imagecolorstotal($img);

    $Transparent = imagecolortransparent($img);
    $IsTransparent = $Transparent != -1;

    if ($IsTransparent)
       $ColorCount--;

    if ($ColorCount == 0) {
       $ColorCount = 0;
       $BitCount = 24;
    };
    if (($ColorCount > 0) and ($ColorCount <= 2)) {
       $ColorCount = 2;
       $BitCount = 1;
    };
    if (($ColorCount > 2) and ($ColorCount <= 16)) {
       $ColorCount = 16;
       $BitCount = 4;
    };
    if (($ColorCount > 16) and ($ColorCount <= 256)) {
       $ColorCount = 0;
       $BitCount = 8;
    };

    $Width = imagesx($img);
    $Height = imagesy($img);

    $Zbytek = (4 - ($Width / (8 / $BitCount)) % 4) % 4;
    $palsize = 0; // cid added
    if ($BitCount < 24)
       $palsize = pow(2, $BitCount) * 4;

    $size = (floor($Width / (8 / $BitCount)) + $Zbytek) * $Height +54;
    $size += $palsize;
    $offset = 54 + $palsize;

    // Bitmap File Header
    $ret = 'BM'; // header (2b)
    $ret .= int_to_dword($size); // size of file (4b)
    $ret .= int_to_dword(0); // reserved (4b)
    $ret .= int_to_dword($offset); // byte location in the file which is first byte of IMAGE (4b)
    // Bitmap Info Header
    $ret .= int_to_dword(40); // Size of BITMAPINFOHEADER (4b)
    $ret .= int_to_dword($Width); // width of bitmap (4b)
    $ret .= int_to_dword($Height); // height of bitmap (4b)
    $ret .= int_to_word(1); // biPlanes = 1 (2b)
    $ret .= int_to_word($BitCount); // biBitCount = {1 (mono) or 4 (16 clr ) or 8 (256 clr) or 24 (16 Mil)} (2b)
    $ret .= int_to_dword($RLE); // RLE COMPRESSION (4b)
    $ret .= int_to_dword(0); // width x height (4b)
    $ret .= int_to_dword(0); // biXPelsPerMeter (4b)
    $ret .= int_to_dword(0); // biYPelsPerMeter (4b)
    $ret .= int_to_dword(0); // Number of palettes used (4b)
    $ret .= int_to_dword(0); // Number of important colour (4b)
    // image data

    $CC = $ColorCount;
    $sl1 = strlen($ret);
    if ($CC == 0)
       $CC = 256;
    if ($BitCount < 24) {
       $ColorTotal = imagecolorstotal($img);
       if ($IsTransparent)
        $ColorTotal--;

       for ($p = 0; $p < $ColorTotal; $p++) {
        $color = imagecolorsforindex($img, $p);
        $ret .= inttobyte($color["blue"]);
        $ret .= inttobyte($color["green"]);
        $ret .= inttobyte($color["red"]);
        $ret .= inttobyte(0); //RESERVED
       };

       $CT = $ColorTotal;
       for ($p = $ColorTotal; $p < $CC; $p++) {
        $ret .= inttobyte(0);
        $ret .= inttobyte(0);
        $ret .= inttobyte(0);
        $ret .= inttobyte(0); //RESERVED
       };
    };

    $retd = ''; // cid added
    if ($BitCount <= 8) {

       for ($y = $Height -1; $y >= 0; $y--) {
        $bWrite = "";
        for ($x = 0; $x < $Width; $x++) {
         $color = imagecolorat($img, $x, $y);
         $bWrite .= decbinx($color, $BitCount);
         if (strlen($bWrite) == 8) {
          $retd .= inttobyte(bindec($bWrite));
          $bWrite = "";
         };
        };

        if ((strlen($bWrite) < 8) and (strlen($bWrite) != 0)) {
         $sl = strlen($bWrite);
         for ($t = 0; $t < 8 - $sl; $t++)
          $sl .= "0";
         $retd .= inttobyte(bindec($bWrite));
        };
        for ($z = 0; $z < $Zbytek; $z++)
         $retd .= inttobyte(0);
       };
    };

    if (($RLE == 1) and ($BitCount == 8)) {
       for ($t = 0; $t < strlen($retd); $t += 4) {
        if ($t != 0)
         if (($t) % $Width == 0)
          $ret .= chr(0) .
          chr(0);

        if (($t +5) % $Width == 0) {
         $ret .= chr(0) . chr(5) . substr($retd, $t, 5) . chr(0);
         $t += 1;
        }
        if (($t +6) % $Width == 0) {
         $ret .= chr(0) . chr(6) . substr($retd, $t, 6);
         $t += 2;
        } else {
         $ret .= chr(0) . chr(4) . substr($retd, $t, 4);
        };
       };
       $ret .= chr(0) . chr(1);
    } else {
       $ret .= $retd;
    };

    if ($BitCount == 24) {
       $Dopl = ''; // cid added
       for ($z = 0; $z < $Zbytek; $z++)
        $Dopl .= chr(0);

       for ($y = $Height -1; $y >= 0; $y--) {
        for ($x = 0; $x < $Width; $x++) {
         $color = imagecolorsforindex($img, ImageColorAt($img, $x, $y));
         $ret .= chr($color["blue"]) . chr($color["green"]) . chr($color["red"]);
        }
        $ret .= $Dopl;
       };

    };

    if ($file != "") {
       $r = ($f = fopen($file, "w"));
       $r = $r and fwrite($f, $ret);
       $r = $r and fclose($f);
       return $r;
    } else {
       echo $ret;
    };
};


function imagecreatefrombmp($file)
{
    global  $CurrentBit, $echoMode;
    $f=fopen($file,"r");
    $Header=fread($f,2);

    if($Header=="BM")
    {
        $Size=freaddword($f);
        $Reserved1=freadword($f);
        $Reserved2=freadword($f);
        $FirstByteOfImage=freaddword($f);

        $SizeBITMAPINFOHEADER=freaddword($f);
        $Width=freaddword($f);
        $Height=freaddword($f);
        $biPlanes=freadword($f);
        $biBitCount=freadword($f);
        $RLECompression=freaddword($f);
        $WidthxHeight=freaddword($f);
        $biXPelsPerMeter=freaddword($f);
        $biYPelsPerMeter=freaddword($f);
        $NumberOfPalettesUsed=freaddword($f);
        $NumberOfImportantColors=freaddword($f);

        if($biBitCount<24)
        {
            $img=imagecreate($Width,$Height);
            $Colors=pow(2,$biBitCount);
            for($p=0;$p<$Colors;$p++)
            {
                    $B=freadbyte($f);
                    $G=freadbyte($f);
                    $R=freadbyte($f);
                    $Reserved=freadbyte($f);
                    $Palette[]=imagecolorallocate($img,$R,$G,$B);
            }

            if($RLECompression==0)
            {
                $Zbytek=(4-ceil(($Width/(8/$biBitCount)))%4)%4;

                for($y=$Height-1;$y>=0;$y--)
                {
                    $CurrentBit=0;
                    for($x=0;$x<$Width;$x++)
                    {
                            $C=freadbits($f,$biBitCount);
                            imagesetpixel($img,$x,$y,$Palette[$C]);
                    }
                    if($CurrentBit!=0) {freadbyte($f);}
                    for($g=0;$g<$Zbytek;$g++)
                    freadbyte($f);
                }
            }
        }


        if($RLECompression==1) //$BI_RLE8
        {
            $y=$Height;

            $pocetb=0;

            while(true)
            {
                $y--;
                $prefix=freadbyte($f);
                $suffix=freadbyte($f);
                $pocetb+=2;

                $echoit=false;

                if($echoit)echo "Prefix: $prefix Suffix: $suffix<BR>";
                if(($prefix==0)and($suffix==1)) break;
                if(feof($f)) break;

                while(!(($prefix==0)and($suffix==0)))
                {
                    if($prefix==0)
                    {
                        $pocet=$suffix;
                        $Data.=fread($f,$pocet);
                        $pocetb+=$pocet;
                        if($pocetb%2==1) {freadbyte($f); $pocetb++;}
                    }
                    if($prefix>0)
                    {
                        $pocet=$prefix;
                        for($r=0;$r<$pocet;$r++)
                        $Data.=chr($suffix);
                    }
                    $prefix=freadbyte($f);
                    $suffix=freadbyte($f);
                    $pocetb+=2;
                    if($echoit) echo "Prefix: $prefix Suffix: $suffix<BR>";
                }

                for($x=0;$x<strlen($Data);$x++)
                {
                        imagesetpixel($img,$x,$y,$Palette[ord($Data[$x])]);
                }
                $Data="";

            }

        }


        if($RLECompression==2) //$BI_RLE4
        {
            $y=$Height;
            $pocetb=0;

            /*while(!feof($f))
            echo freadbyte($f)."_".freadbyte($f)."<BR>";*/
            while(true)
            {
                //break;
                $y--;
                $prefix=freadbyte($f);
                $suffix=freadbyte($f);
                $pocetb+=2;

                $echoit=false;

                if($echoit)echo "Prefix: $prefix Suffix: $suffix<BR>";
                if(($prefix==0)and($suffix==1)) break;
                if(feof($f)) break;

                while(!(($prefix==0)and($suffix==0)))
                {
                    if($prefix==0)
                    {
                        $pocet=$suffix;

                        $CurrentBit=0;
                        for($h=0;$h<$pocet;$h++)
                        $Data.=chr(freadbits($f,4));
                        if($CurrentBit!=0) freadbits($f,4);
                        $pocetb+=ceil(($pocet/2));
                        if($pocetb%2==1) {freadbyte($f); $pocetb++;}
                    }
                    if($prefix>0)
                    {
                        $pocet=$prefix;
                        $i=0;
                        for($r=0;$r<$pocet;$r++)
                        {
                            if($i%2==0)
                            {
                                    $Data.=chr($suffix%16);
                            }
                            else
                            {
                                    $Data.=chr(floor($suffix/16));
                            }
                            $i++;
                        }
                    }
                    $prefix=freadbyte($f);
                    $suffix=freadbyte($f);
                    $pocetb+=2;
                    if($echoit) echo "Prefix: $prefix Suffix: $suffix<BR>";
                }

                for($x=0;$x<strlen($Data);$x++)
                {
                        imagesetpixel($img,$x,$y,$Palette[ord($Data[$x])]);
                }
                $Data="";

            }

        }


        if($biBitCount==24)
        {
            $img=imagecreatetruecolor($Width,$Height);
            $Zbytek=$Width%4;

            for($y=$Height-1;$y>=0;$y--)
            {
                for($x=0;$x<$Width;$x++)
                {
                    $B=freadbyte($f);
                    $G=freadbyte($f);
                    $R=freadbyte($f);
                    $color=imagecolorexact($img,$R,$G,$B);
                    if($color==-1) $color=imagecolorallocate($img,$R,$G,$B);
                    imagesetpixel($img,$x,$y,$color);
                }
                for($z=0;$z<$Zbytek;$z++)
                freadbyte($f);
            }
        }
            return $img;

    }

    fclose($f);

}

/*
* imagecreatefrombmp扩展需要方法:
*-------------------------
* freadbyte($file) - reads 1 byte from $file
* freadword($file) - reads 2 bytes (1 word) from $file
* freaddword($file) - reads 4 bytes (1 dword) from $file
* freadlngint($file) - same as freaddword($file)
* decbin8($d) - returns binary string of d zero filled to 8
* RetBits($byte,$start,$len) - returns bits $start->$start+$len from $byte
* freadbits($file,$count) - reads next $count bits from $file
* RGBToHex($R,$G,$B) - convert $R, $G, $B to hex
* int_to_dword($n) - returns 4 byte representation of $n
* int_to_word($n) - returns 2 byte representation of $n
*/

function freadbyte($f) {
    return ord(fread($f, 1));
};

function freadword($f) {
    $b1 = freadbyte($f);
    $b2 = freadbyte($f);
    return $b2 * 256 + $b1;
};

function freadlngint($f) {
    return freaddword($f);
};

function freaddword($f) {
    $b1 = freadword($f);
    $b2 = freadword($f);
    return $b2 * 65536 + $b1;
};

function RetBits($byte, $start, $len) {
    $bin = decbin8($byte);
    $r = bindec(substr($bin, $start, $len));
    return $r;

};

$CurrentBit = 0;
function freadbits($f, $count) {
    global $CurrentBit, $SMode;
    $Byte = freadbyte($f);
    $LastCBit = $CurrentBit;
    $CurrentBit += $count;
    if ($CurrentBit == 8) {
       $CurrentBit = 0;
    } else {
       fseek($f, ftell($f) - 1);
    };
    return RetBits($Byte, $LastCBit, $count);
};

function RGBToHex($Red, $Green, $Blue) {
    $hRed = dechex($Red);
    if (strlen($hRed) == 1)
       $hRed = "0".$hRed;
    $hGreen = dechex($Green);
    if (strlen($hGreen) == 1)
       $hGreen = "0".$hGreen;
    $hBlue = dechex($Blue);
    if (strlen($hBlue) == 1)
       $hBlue = "0".$hBlue;
    return ($hRed . $hGreen . $hBlue);
};

function int_to_dword($n) {
    return chr($n & 255) . chr(($n >> 8) & 255) . chr(($n >> 16) & 255) . chr(($n >> 24) & 255);
}
function int_to_word($n) {
    return chr($n & 255) . chr(($n >> 8) & 255);
}

function decbin8($d) {
    return decbinx($d, 8);
};

function decbinx($d, $n) {
    $bin = decbin($d);
    $sbin = strlen($bin);
    for ($j = 0; $j < $n - $sbin; $j++)
       $bin = "0".$bin;
    return $bin;
};

function inttobyte($n) {
    return chr($n);
};

//----扩 展 结 束

/**
 +------------------------------------------------------------------------------
 * 图像操作类库
 +------------------------------------------------------------------------------
 * @category   Extend
 * @package  Extend
 * @subpackage  Util
 * @author    page7 <zhounan0120@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */

class Image extends Base
{//类定义开始

    /**
     +----------------------------------------------------------
     * 取得图像信息
     *
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @param string $image 图像文件名
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    static function getImageInfo($img) {
        $imageInfo = getimagesize($img);
        if( $imageInfo!== false) {
            if(function_exists('image_type_to_extension')){
                $imageType = strtolower(substr(image_type_to_extension($imageInfo[2]),1));
            }else{
                $imageType = strtolower(substr(image_type_to_mime_type($img),strrpos($img,'/')+1));
            }
            $imageSize = filesize($img);
            $info = array(
                "width"=>$imageInfo[0],
                "height"=>$imageInfo[1],
                "type"=>$imageType,
                "size"=>$imageSize,
                "mime"=>$imageInfo['mime']
            );
            return $info;
        }else {
            return false;
        }
    }

    /**
     +----------------------------------------------------------
     * 显示服务器图像文件
     * 支持URL方式
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @param string $imgFile 图像文件名
     * @param string $text 文字字符串
     * @param string $width 图像宽度
     * @param string $height 图像高度
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    static function showImg($imgFile,$text='',$width=80,$height=30) {
        //获取图像文件信息
        $info = Image::getImageInfo($imgFile);
        if($info !== false) {
            $createFun  =   str_replace('/','createfrom',$info['mime']);
            $im = $createFun($imgFile);
            if($im) {
                $ImageFun= str_replace('/','',$info['mime']);
                if(!empty($text)) {
                    $tc  = imagecolorallocate($im, 0, 0, 0);
                    imagestring($im, 3, 5, 5, $text, $tc);
                }
                if($info['type']=='png' || $info['type']=='gif') {
                imagealphablending($im, false);//取消默认的混色模式
                imagesavealpha($im,true);//设定保存完整的 alpha 通道信息
                }
                Header("Content-type: ".$info['mime']);
                $ImageFun($im);
                @ImageDestroy($im);
                return ;
            }
        }
        //获取或者创建图像文件失败则生成空白PNG图片
        $im  = imagecreatetruecolor($width, $height);
        $bgc = imagecolorallocate($im, 255, 255, 255);
        $tc  = imagecolorallocate($im, 0, 0, 0);
        imagefilledrectangle($im, 0, 0, 150, 30, $bgc);
        imagestring($im, 4, 5, 5, "NO PICTURE", $tc);
        Image::output($im);
        return ;
    }

    /**
     +----------------------------------------------------------
     * 生成缩略图
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @param string $image  原图
     * @param string $totype 图像格式
     * @param string $filename 缩略图文件名
     * @param string $maxWidth  宽度
     * @param string $maxHeight  高度
     * @param string $position 缩略图保存目录
     * @param boolean $interlace 启用隔行扫描
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws IXCoreExecption
     +----------------------------------------------------------
     */
    static function thumb($image, $totype='', $filename='',$maxWidth=200, $maxHeight=50, $interlace=true,$suffix='_thumb', $lockSize=false)
    {
        // 获取原图信息
        $info  = Image::getImageInfo($image);
         if($info !== false) {
            $srcWidth  = $info['width'];
            $srcHeight = $info['height'];
            $pathinfo = pathinfo($image);
            $type =  $pathinfo['extension'];
            $type = empty($type)?$info['type']:$type;
            $type = strtolower($type);
            $interlace  =  $interlace? 1:0;
            unset($info);
            $scale = min($maxWidth/$srcWidth, $maxHeight/$srcHeight); // 计算缩放比例
            $x = 0; $y = 0;
            if($scale>=1) {
                // 小于原图大小不再缩略
                $width   =  $srcWidth;
                $height  =  $srcHeight;
            }else{
                // 缩略图尺寸
                $width  = (int)($srcWidth*$scale);
                $height = (int)($srcHeight*$scale);
            }
            if($lockSize) {
                if($maxWidth/$srcWidth >= $maxHeight/$srcHeight) {
                    $x = ( $height * $maxWidth / $maxHeight - $width ) / 2;
                    $y = 0;
                    $width = $height * $maxWidth / $maxHeight;
                }else{
                    $x = 0;
                    $y = ( $width * $maxHeight / $maxWidth - $height) / 2;
                    $height = $width * $maxHeight / $maxWidth;
                }
            }

            // 载入原图
            $createFun = 'ImageCreateFrom'.($type=='jpg'?'jpeg':$type);
            $srcImg     = $createFun($image);

            //创建缩略图
            if($totype!='gif' && function_exists('imagecreatetruecolor'))
                $thumbImg = imagecreatetruecolor($width, $height);
            else
                $thumbImg = imagecreate($width, $height);
            $bg = imagecolorallocate($thumbImg, 255, 255, 255); //白色底色
            imagefilledrectangle($thumbImg, 0, 0, $width, $height, $bg);

            //重置填充尺寸
            if($lockSize) {
                if($maxWidth/$srcWidth >= $maxHeight/$srcHeight) {
                    $width = $srcWidth * $height / $srcHeight;
                }else{
                    $height = $srcHeight * $width / $srcWidth;
                }
            }

            // 复制图片
            if(function_exists("ImageCopyResampled"))
                ImageCopyResampled($thumbImg, $srcImg, $x, $y, 0, 0, $width, $height, $srcWidth, $srcHeight);
            else
                ImageCopyResized($thumbImg, $srcImg, $x, $y, 0, 0, $width, $height,  $srcWidth, $srcHeight);
            if('gif'==$totype || 'png'==$totype) {
                //imagealphablending($thumbImg, false);//取消默认的混色模式
                //imagesavealpha($thumbImg,true);//设定保存完整的 alpha 通道信息
                $background_color  =  imagecolorallocate($thumbImg,  0,255,0);  //  指派一个绿色
                imagecolortransparent($thumbImg, $background_color);  //  设置为透明色，若注释掉该行则输出绿色的图
            }

            // 对jpeg图形设置隔行扫描
            if('jpg'==$totype || 'jpeg'==$totype)   imageinterlace($thumbImg,$interlace);

            //$gray=ImageColorAllocate($thumbImg,255,0,0);
            //ImageString($thumbImg,2,5,5,"IXCore",$gray);
            // 生成图片
            $imageFun = 'image'.($totype=='jpg'?'jpeg':$totype);

            $filename  = empty($filename)? substr($image,0,strrpos($image, '.')).$suffix.'.'.$totype : substr($filename,0,strrpos($filename, '.')).$suffix.'.'.$totype;

            $imageFun($thumbImg,$filename);
            ImageDestroy($thumbImg);
            ImageDestroy($srcImg);
            return $filename;
         }
         return false;
    }

    /**
     +-----------------------------------------
     * 验证码图片生成
     +-----------------------------------------
     * @param int $length   字符长度
     * @param int $strtype  字符类型
     * @param string $type  图片类型
     * @param int $width    宽度
     * @param int $height   高度
     * @param array $color  颜色配置
     * @param bool $rand    是否开启动态颜色
     * @param string $fontface    字体路径
     * @param string $verifyName  验证字段
     +-----------------------------------------
     */
    static function buildVerify($length=4, $strtype=5, $type='png', $width=180, $height=50, $color=array(), $rand=true, $fontface='simhei.ttf', $verifyName='verify') {
        import('Extend.Text.String');
        $code  =  String::rand_string($length, $strtype);
        $_SESSION[$verifyName] = md5($code);
        $im=imagecreatetruecolor($width,$height);
        //初始化颜色
        $color['bg'] = !empty($color['bg'])?array_map('hexdec', str_split($color['bg'],2)):array(250,250,250);
        $color['border'] = !empty($color['border'])?array_map('hexdec', str_split($color['border'],2)):array(100,100,100);
        $color['string'] = !empty($color['string'])?array_map('hexdec', str_split($color['string'],2)):'';
        // 创建背景
        $bkcolor=imagecolorallocate($im,$color['bg'][0],$color['bg'][1],$color['bg'][2]);
        imagefill($im,0,0,$bkcolor);

        // 干扰
        $obcolor = empty($color['string'])?false:true;
        if($obcolor){
            $r = $color['string'][0];
            $g = $color['string'][1];
            $b = $color['string'][2];
        }
        for($i=0; $i<5; $i++){
            $fontcolor=imagecolorallocate(
                $im,
                !$obcolor?mt_rand(0,255):mt_rand($r*0.7,$r*2),
                !$obcolor?mt_rand(0,255):mt_rand($g*0.7,$g*2),
                !$obcolor?mt_rand(0,255):mt_rand($b*0.7,$b*2)
            );
            imagearc($im,mt_rand(-10,$width),mt_rand(-10,$height),mt_rand(30,300),mt_rand(20,200),55,44,$fontcolor);
        }
        for($i=0; $i<50; $i++){
            $fontcolor=imagecolorallocate(
                $im,
                !$obcolor?mt_rand(0,255):mt_rand($r*0.7,$r*1.4),
                !$obcolor?mt_rand(0,255):mt_rand($g*0.7,$g*1.4),
                !$obcolor?mt_rand(0,255):mt_rand($b*0.7,$b*1.4)
            );
            imagesetpixel($im,mt_rand(0,$width),mt_rand(0,$height),$fontcolor);
        }
        // 文字
        if(!is_file($fontface)) {
            $fontface = dirname(__FILE__)."/".$fontface;
        }
        for($i=0; $i<$length; $i++){
            if($rand) {
                if(!$color['string']) {
                    $fontcolor=imagecolorallocate($im, mt_rand(0,120), mt_rand(0,120), mt_rand(0,120)); //这样保证随机出来的颜色较深。
                }else{
                    $r = $color['string'][0]; $g = $color['string'][1]; $b = $color['string'][2];
                    $fontcolor=imagecolorallocate($im, mt_rand($r*0.9,$r), mt_rand($g*0.9,$g), mt_rand($b*0.9,$b));
                }
            }else{
                if(!$color['string']) $color['string'] = array(mt_rand(60,120),mt_rand(60,120),mt_rand(60,120));
                $fontcolor=imagecolorallocate($im, $color['string'][0], $color['string'][1], $color['string'][2]);
            }
            $codex= msubstr($code, 1, $i, false);
            $fontsize = round($width/($length+1));
            $size = $strtype == 4 ? mt_rand($fontsize*1, $fontsize*1.5) : mt_rand($fontsize*1.2, $fontsize*1.8);    //字体大小
            $angle = mt_rand(-15, 15);                      //角 度
            $x = isset($strWidth) ? $strWidth : 5;         //上一个字体位置决定
            if($x + $size > $fontsize * ($i + 2))
                $size = $fontsize * ($i + 1.5) - $x;
            $y = $size + mt_rand(0, abs($height - $size));
            $strWidth = $x + $size * ($strtype == 4 ? 1.1 : 0.7);
            imagettftext($im, $size, $angle, $x, $y, $fontcolor, $fontface, $codex);
        }
        // 绘制边框
        $borderColor = imagecolorallocate($im,$color['border'][0],$color['border'][1],$color['border'][2]);
        @imagerectangle($im, 0, 0, $width-1, $height-1, $borderColor);
        Image::output($im,$type);
    }

    /**
     +----------------------------------------------------------
     * 把图像转换成字符显示
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @param string $image  要显示的图像
     * @param string $type  图像类型，默认自动获取
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws IXCoreExecption
     +----------------------------------------------------------
     */
    static function showASCIIImg($image,$string='',$type='')
    {
        $info  = Image::getImageInfo($image);
        if($info !== false) {
            $type = empty($type)?$info['type']:$type;
            unset($info);
            // 载入原图
            $createFun = 'ImageCreateFrom'.($type=='jpg'?'jpeg':$type);
            $im     = $createFun($image);
            $dx = imagesx($im);
            $dy = imagesy($im);
            $i  =   0;
            $out   =  '<span style="padding:0px;margin:0;line-height:100%;font-size:1px;">';
            set_time_limit(0);
            for($y = 0; $y < $dy; $y++) {
              for($x=0; $x < $dx; $x++) {
                  $col = imagecolorat($im, $x, $y);
                  $rgb = imagecolorsforindex($im,$col);
                  $str   =   empty($string)?'*':$string[$i++];
                  $out .= sprintf('<span style="margin:0px;color:#%02x%02x%02x">'.$str.'</span>',$rgb['red'],$rgb['green'],$rgb['blue']);
             }
             $out .= "<br>\n";
            }
            $out .=  '</span>';
            imagedestroy($im);
            return $out;
        }
        return false;
    }

    /**
     +----------------------------------------------------------
     * 生成高级图像验证码
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @param string $type 图像格式
     * @param string $width  宽度
     * @param string $height  高度
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws IXCoreExecption
     +----------------------------------------------------------
     */
    static function showAdvVerify($type='png',$width=180,$height=40)
    {
        $rand   =   range('a','z');
        shuffle($rand);
        $verifyCode =   array_slice($rand,0,10);
        $letter = implode(" ",$verifyCode);
        $_SESSION['verifyCode'] = $verifyCode;
        $im = imagecreate($width,$height);
        $r = array(225,255,255,223);
        $g = array(225,236,237,255);
        $b = array(225,236,166,125);
        $key = mt_rand(0,3);
        $backColor = imagecolorallocate($im, $r[$key],$g[$key],$b[$key]);
        $borderColor = imagecolorallocate($im, 100, 100, 100);                    //边框色
        imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
        imagerectangle($im, 0, 0, $width-1, $height-1, $borderColor);
        $numberColor = imagecolorallocate($im, 255,rand(0,100), rand(0,100));
        $stringColor = imagecolorallocate($im, rand(0,100), rand(0,100), 255);
        // 添加干扰
        for($i=0;$i<10;$i++){
            $fontcolor=imagecolorallocate($im,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
            imagearc($im,mt_rand(-10,$width),mt_rand(-10,$height),mt_rand(30,300),mt_rand(20,200),55,44,$fontcolor);
        }
        for($i=0;$i<255;$i++){
            $fontcolor=imagecolorallocate($im,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
            imagesetpixel($im,mt_rand(0,$width),mt_rand(0,$height),$fontcolor);
        }
        imagestring($im, 5, 5, 1, "0 1 2 3 4 5 6 7 8 9", $numberColor);
        imagestring($im, 5, 5, 20, $letter, $stringColor);
        Image::output($im,$type);
    }

    /**
     +----------------------------------------------------------
     * 生成UPC-A条形码
     +----------------------------------------------------------
     * @static
     +----------------------------------------------------------
     * @param string $code 编码
     * @param string $type 图像格式
     * @param string $lw  单元宽度
     * @param string $hi   条码高度
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws IXCoreExecption
     +----------------------------------------------------------
     */
    static function UPCA($code, $type='png', $lw=2, $hi=100) {
        static $Lencode = array('0001101','0011001','0010011','0111101','0100011',
                         '0110001','0101111','0111011','0110111','0001011');
        static $Rencode = array('1110010','1100110','1101100','1000010','1011100',
                         '1001110','1010000','1000100','1001000','1110100');
        $ends = '101';
        $center = '01010';
        /* UPC-A Must be 11 digits, we compute the checksum. */
        if ( strlen($code) != 11 ) { return false; }
        /* Compute the EAN-13 Checksum digit */
        $ncode = '0'.$code;
        $even = 0; $odd = 0;
        for ($x=0;$x<12;$x++) {
          if ($x % 2) { $odd += $ncode[$x]; } else { $even += $ncode[$x]; }
        }
        $code.=(10 - (($odd * 3 + $even) % 10)) % 10;
        /* Create the bar encoding using a binary string */
        $bars=$ends;
        $bars.=$Lencode[$code[0]];
        for($x=1;$x<6;$x++) {
          $bars.=$Lencode[$code[$x]];
        }
        $bars.=$center;
        for($x=6;$x<12;$x++) {
          $bars.=$Rencode[$code[$x]];
        }
        $bars.=$ends;
        /* Generate the Barcode Image */
        if ( $type!='gif' && function_exists('imagecreatetruecolor')) {
            $im = imagecreatetruecolor($lw*95+30,$hi+30);
        }else {
            $im = imagecreate($lw*95+30,$hi+30);
        }
        $fg = ImageColorAllocate($im, 0, 0, 0);
        $bg = ImageColorAllocate($im, 255, 255, 255);
        ImageFilledRectangle($im, 0, 0, $lw*95+30, $hi+30, $bg);
        $shift=10;
        for ($x=0;$x<strlen($bars);$x++) {
          if (($x<10) || ($x>=45 && $x<50) || ($x >=85)) { $sh=10; } else { $sh=0; }
          if ($bars[$x] == '1') { $color = $fg; } else { $color = $bg; }
          ImageFilledRectangle($im, ($x*$lw)+15,5,($x+1)*$lw+14,$hi+5+$sh,$color);
        }
        /* Add the Human Readable Label */
        ImageString($im,4,5,$hi-5,$code[0],$fg);
        for ($x=0;$x<5;$x++) {
          ImageString($im,5,$lw*(13+$x*6)+15,$hi+5,$code[$x+1],$fg);
          ImageString($im,5,$lw*(53+$x*6)+15,$hi+5,$code[$x+6],$fg);
        }
        ImageString($im,4,$lw*95+17,$hi-5,$code[11],$fg);
        /* Output the Header and Content. */
        Image::output($im,$type);
    }

    /**
     +----------------------------------------------------------
     * 生成EAN-13条形码
     +----------------------------------------------------------
     * @static
     +----------------------------------------------------------
     * @param string $code 编码
     * @param string $type 图像格式
     * @param string $lw  单元宽度
     * @param string $hi   条码高度
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws IXCoreExecption
     +----------------------------------------------------------
     */
    static function EAN13($code, $type='png', $lw=2, $hi=100){
        /* 数据guide用于确定左边编码 */
        $guide = array(1=>'aaaaaa', 'aababb', 'aabbab', 'abaabb', 'abbaab', 'abbbaa', 'ababab', 'ababba', 'abbaba');
        $start = '101';
        $Lencode = array(
            "a" => array('0001101','0011001','0010011','0111101','0100011','0110001','0101111','0111011','0110111','0001011'),
            "b" => array('0100111','0110011','0011011','0100001','0011101','0111001','0000101','0010001','0001001','0010111'),
        );
        $Rencode = array('1110010','1100110','1101100','1000010','1011100','1001110','1010000','1000100','1001000','1110100');
        $ends = '101';
        $center = '01010';

        /* 位数检测 */
        if ( strlen($code) != 13 ){ return false; }

        /* 效验码检测 */
        $even = 0; $odd = 0;
        for ($x=0;$x<12;$x++) {
          if ($x % 2) { $odd += $code[$x]; } else { $even += $code[$x]; }
        }
        $csum = $odd * 3 + $even;
        if($code[12] != (10-($csum % 10))) { return false;}

        /* 建立码位2进制 */
        $bars = $start;
        for($x=1; $x<7; $x++) {
          $bars .= $Lencode[$guide[$code[0]][$x-1]][$code[$x]];
        }
        $bars .= $center;
        for($x=7; $x<13; $x++) {
          $bars .= $Rencode[$code[$x]];
        }
        $bars .= $ends;

        /* 创建图像 */
        if ( $type!='gif' && function_exists('imagecreatetruecolor')) {
            $im = imagecreatetruecolor($lw*95+30,$hi+30);
        }else {
            $im = imagecreate($lw*95+30,$hi+30);
        }
        $fg = ImageColorAllocate($im, 0, 0, 0);
        $bg = ImageColorAllocate($im, 255, 255, 255);
        ImageFilledRectangle($im, 0, 0, $lw*95+30, $hi+30, $bg);

        $shift=10;
        for ($x=0;$x<strlen($bars);$x++) {
            if (($x<3) || ($x>=45 && $x<49) || ($x>=92)) { $sh=10; } else { $sh=0; }
            if ($bars[$x] == '1') { $color = $fg; } else { $color = $bg; }
            ImageFilledRectangle($im, ($x*$lw)+15, 5, ($x+1)*$lw+14, $hi+5+$sh, $color);
        }

        /* 底部数字码 */
        ImageString($im, 6, 2, $hi+5, $code[0], $fg);
        for ($x=0;$x<6;$x++) {
          ImageString($im, 5, $lw*(10+$x*7)+5, $hi+5, $code[$x+1], $fg);
          ImageString($im, 5, $lw*(51+$x*7)+15, $hi+5, $code[$x+7], $fg);
        }

        /* Output the Header and Content. */
        Image::output($im,$type);

    }
    /**
     +----------------------------------------------------------
     * 为图片添加水印
     +----------------------------------------------------------
     * @static public
     +----------------------------------------------------------
     * @param string $source 原文件名
     * @param string $water  水印图片
     * @param string $$savename  添加水印后的图片名
     * @param string $alpha  水印的透明度
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws IXCoreExecption
     +----------------------------------------------------------
     */

    static public function water($source,$water,$savename=null,$alpha=80)
    {
        //检查文件是否存在
        if(!file_exists($source)||!file_exists($water))
            return false;

        //图片信息
        $sInfo=self::getImageInfo($source);
        $wInfo=self::getImageInfo($water);

        //如果图片小于水印图片，不生成图片
        if($sInfo["width"]<$wInfo["width"] || $sInfo['height']<$wInfo['height'])
            return false;

        //建立图像
        $sCreateFun="imagecreatefrom".$sInfo['type'];
        $sImage=$sCreateFun($source);
        $wCreateFun="imagecreatefrom".$wInfo['type'];
        $wImage=$wCreateFun($water);

        //设定图像的混色模式
        imagealphablending($wImage, true);

        //图像位置,默认为右下角右对齐
        $posY=$sInfo["height"]-$wInfo["height"];
        $posX=$sInfo["width"]-$wInfo["width"];

        //生成混合图像
         imagecopymerge($sImage, $wImage, $posX, $posY, 0, 0, $wInfo['width'],$wInfo['height'],$alpha);

        //输出图像
        $ImageFun='Image'.$sInfo['type'];
        //如果没有给出保存文件名，默认为原图像名
        if(!$savename){
            $savename=$source;
            @unlink($source);
        }
        //保存图像
        $ImageFun($sImage,$savename);
        imagedestroy($sImage);

    }

    static function output($im, $type='png')
    {
        header("Content-type: image/".$type);
        $ImageFun='Image'.$type;
        $ImageFun($im);
        imagedestroy($im);
    }

}//类定义结束
?>