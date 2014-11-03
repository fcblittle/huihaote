<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>打印</title>
<style>
.limit{width:680px; margin-top:20px; color:#000; font-size:12px;}
.clear{ clear:both; height:0px; overflow:hidden; }
.table {border-left:1px solid #333; border-top:1px solid #333; width:100%;}
.table td{border-right:1px solid #333; border-bottom:1px solid #333; line-height:20px; height:20px; font-size:12px; text-align:center;}
#ixcore_run_time{ display:none;}
#ixcore_page_trace{ display:none;}
</style>
</head>

<body class="limit">
<div style="margin-top:10px; font-size:12px;">
<img src="./logo.gif" width="50" style="vertical-align:middle;" />
<span>青岛惠好特自动化设备有限公司<span>
</div>

<h1 style="text-align:center; font-size:20px; margin:5px 0px;"><?php echo ($name); ?>收支明细</h1>

<div style="height:30px; line-height:30px; width:100%; margin:5px;">
<div style="width:100%; float:left;">时间： <?php if($stime): ?><?php echo ($stime); ?> 至 <?php echo ($etime); ?><?php endif; ?></div>
</div>
<div style="height:30px; line-height:30px; width:100%; margin:5px;">
<div style="width:100%; float:left;">
期初余额: <?php echo (showPrice($begin)); ?>　
收入：<?php echo (showPrice($income)); ?>　
支出：<?php echo (showPrice($pay)); ?>　
阶段总额：
<?php if(($sum)  >  "0"): ?><span style="color:green"><?php echo (showPrice($sum)); ?></span>
<?php else: ?>
<span style="color:red">-<?php echo (showPrice($sum)); ?></span><?php endif; ?>　
结存余额:
<?php if(($total)  >  "0"): ?><span style="color:green"><?php echo (showPrice($total)); ?></span>
<?php else: ?>
<span style="color:red">-<?php echo (showPrice($total)); ?></span><?php endif; ?>
</div>
</div>

<table cellpadding="2" cellspacing="1" border="0" class="table">
<tr>
<td>价格</td>
<td>说明</td>
<td>类型</td>
<td>记录时间</td>
</tr>
<?php if(is_array($lists)): ?><?php $i = 0;?><?php $__LIST__ = $lists?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
<td>
<?php if(($vo['price'])  >  "0"): ?><span style="color:green;"><?php echo (showPrice(is_array($vo)?$vo["price"]:$vo->price)); ?></span>
<?php else: ?>
<span style="color:red;">-<?php echo (showPrice(is_array($vo)?$vo["price"]:$vo->price)); ?></span><?php endif; ?>
</td>
<td><?php echo (is_array($vo)?$vo["comment"]:$vo->comment); ?></td>
<td>
<?php switch($vo['type']): ?><?php case "daily":  ?>日常费用<?php break;?>
<?php case "worker":  ?>员工费用<?php break;?>
<?php case "order":  ?>采购/销售<?php break;?>
<?php case "service":  ?>售后费用<?php break;?>
<?php case "return":  ?>采购/销售退货<?php break;?>
<?php case "fixed":  ?>固定资产<?php break;?><?php endswitch;?>
</td>
<td><?php echo (date('Y-m-d',is_array($vo)?$vo["time"]:$vo->time)); ?></td>
</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</table>

</body>
</html>