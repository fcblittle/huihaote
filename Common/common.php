<?php

//单号生成
function creatOrderNum($table, $prefix='', $date='Ymd')
{
    if(!$table)
        return false;

    $site = $date == 'Ymd' ? 8 : 6;

    $DB = D(strtolower($table));
    $num_prefix = $prefix .'-'. date($date);
    $expenses =$DB -> order("`num` desc") -> find("`num` LIKE '$num_prefix%'");
    $ids = explode('-', $expenses['num']);
    $seq = substr($ids[1], $site);
    $seq = $seq ? str_pad((int)$seq+1, 4, '0', STR_PAD_LEFT) : '0001';
    $num = $num_prefix.$seq;

    return $num;
}


/*
 +----------------------------------------------------------
 * 时间戳处理
 +----------------------------------------------------------
 */
function timestr($time, $mode=0, $sub='前')
{
    if(!$time) return '无记录';
    switch($mode){
        case 1:
            $now = NOW;
            if($now - $time > 259200){
                $date = date('n月j日', $time);
            }elseif($now - $time > 86400){
                $date = floor(($now - $time)/86400).'天'.$sub;
            }elseif ($now - $time > 3600){
                $date = floor(($now - $time)/3600).'小时'.$sub;
            }elseif ($now - $time > 60){
                $date = floor(($now - $time)/60).'分钟'.$sub;
            }else{
                $date = floor($now - $time).'秒'.$sub;
            }
            break;
        case 2:
            $date = date('Y-n-j H:i', $time);break;
        default:
            $date = date('Y年n月j日 H:i', $time);
            break;
    }
    return $date;
}
/*
function showPrice($price, $prefix='')
{
    $sign = $price < 0 ? '-' : '';
    $price = abs($price);
    //$price = $price;
    //return $prefix.strval((int)(abs($price)/100)).'.'.sprintf("%02d", fmod($price,100)); //原写法取余出错
    return $prefix . strval($sign . number_format(round($price/100,2),2,'.',''));
}
*/
function showPrice($price, $prefix='')
{
    $price = abs($price);
    //$price = $price;
    //return $prefix.strval((int)(abs($price)/100)).'.'.sprintf("%02d", fmod($price,100)); //原写法取余出错
    return $prefix . strval(number_format(round($price/100,2),2,'.',''));
}

function inputPrice($price)
{
    if(stripos($price, '￥') !== false) $price = str_replace('￥', '', $price);
    $price = $price * 100;
    return $price;
}
/**
 +-----------------------------------------
 * 给文件增加后缀
 +-----------------------------------------
 */
function addSuffix($file, $suffix='_s')
{
    return substr_replace($file, $suffix, -4, 0);
}

/**
 +----------------------------------------------------
 * 路径生成
 +----------------------------------------------------
 */
function getDir($id) {
    $uid = abs(intval($id));
    $uid = sprintf("%09d", $id);
    $dir1 = substr($id, 0, 3);
    $dir2 = substr($id, 3, 2);
    $dir3 = substr($id, 5, 2);
    $id = substr($id, -2);
    return "{$dir1}/{$dir2}/{$dir3}/{$id}/";
}

/**
 + ----------------------------------
 * 对数据进行树状结构解析
 + ----------------------------------
 * @param string $result    结果数据，第一次输入0级数据
 * @param string $data      原始数据
 * @param string $$keyname  默认键参数
 + ----------------------------------
 * @return mix
 + ----------------------------------
 */
function dataTree($result, $data, $keyname='parentid', $strmode=false)
{
    foreach ($data as $key => $value)
    {
        if(!$parent_key = $value[$keyname]) continue; //父级key
        //判断上级是否存在level
        if(isset($result[$parent_key]))
        {
            unset($data[$key]);
            //添加该层level
            $value['level'] = $result[$parent_key]['level'] + 1;
            //找插入位置
            $offset = array_search($value[$keyname], array_keys($result))+1; //父级所在位置
            //数组插入
            $front = array_slice($result, 0, $offset, true);
            $back = array_slice($result, $offset, count($result), true);
            $result = $front + array($key => $value) + $back;
            //当前操作被执行
            $check = true;
        }
    }
    if(!isset($check))
    {
        if($strmode)
        {
            //该部分有待优化
            $result = array_values($result);
            $result[0]['levelstr'] = '';
            foreach($result as $key => $value)
            {
                //检测以下点有没有同父同级条
                if($key == 0) continue;
                $check = true;
                for($i=$key+1; $i<=count($result)-1; $i++)
                {
                    if($result[$i]['level'] < $value['level'])
                    {
                        $check = true; break;
                    }else if($result[$i]['level'] == $value['level']){
                        $check = false; break;
                    }
                }
                if($value['level'] > $result[$key-1]['level'])
                {
                    $result[$key]['levelstr'] = str_replace(array('├', '└'), array('│', '　'), $result[$key-1]['levelstr']).($check?'└':'├');
                }else if($value['level'] <= $result[$key-1]['level'] && $value['level']){
                    $result[$key]['levelstr'] = msubstr($result[$key-1]['levelstr'], $value['level']-1, 0, false).($check?'└':'├');
                }
                $prevData[$value['level']] = $key;
            }
        }
        return $result;
    }else{
        return dataTree($result, $data, $keyname, $strmode);
    }
}

function test($data){
    var_dump($data);
    exit();
}


function getRMB($price)
{
    $arr1 = array('零','壹','贰','叁','肆','伍','陆','柒','捌','玖');
    $arr2 = array('拾','佰','仟');
    $arr = explode(".", $price);

    $rmb_len=strlen($arr[0]); //整数部分
    $j=0;
    for ($i=0; $i<$rmb_len; $i++)
    {
        $bit = $arr[0][$rmb_len-$i-1];
        $cn = $arr1[$bit];
        $unit = $arr2[$j];
        if ($i==0) {
            $re=$cn;
        } elseif ($i==4){
            $re=$cn."万".$re;
            $j=0;
        } elseif ($i==8) {
            $re=$cn."亿".$re;
            $j=0;
        }else{
            $j++;
            $re = $bit==0 ? "零".$re : $cn.$unit.$re;
        }
    }

    if ($arr[1])
    {
        $arr[1][0]==0?$re=$re."元零":$re=$re."元".$arr1[$arr[1][0]]."角"; //角
        $arr[1][1]==0?$re=$re."零分":$re=$re.$arr1[$arr[1][1]]."分";      //分
    }

    $re=preg_replace(array("/(零)+$/","/(零)+/","/零万/","/零亿/"),array("","零","万","亿"),$re); //替换一些数据
    $arr[1] ? $re : $re .= "元整";
    return $re;
}
?>