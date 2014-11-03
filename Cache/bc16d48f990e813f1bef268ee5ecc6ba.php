<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>打印</title>
<style>
.limit{width:640px; margin-top:20px; color:#000; font-size:12px;}
.clear{ clear:both; height:0px; overflow:hidden; }
.table {border-left:1px solid #333; border-top:1px solid #333;}
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

<h1 style="text-align:center; font-size:20px; margin:5px 0px;">售后统计</h1>

<div style="height:20px; line-height:20px; width:100%; margin:5px;">
<div style="width:100%; float:left;">时间： <?php echo ($start); ?> 至 <?php echo ($end); ?>　</div>
</div>

<table width="100%" cellpadding="0" cellspacing="0" border="0" class="table">
<tr>
<td>单 号</td>
<td><?php if(($abs)  ==  "1"): ?>供应商<?php else: ?>客 户<?php endif; ?></td>
<td width="60">维修人</td>
<td width="70">金额</td>
<td width="60">税率</td>
<td width="70">提成</td>
<td width="80">状 态</td>
</tr>
<?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
<td><?php echo (is_array($vo)?$vo["num"]:$vo->num); ?></td>
<td><?php echo ($vo['cors']['name']); ?></td>
<td><?php echo ($vo['sorts']['intro']); ?></td>
<td><?php echo (showPrice($vo['total']+$vo['tax_total'])); ?></td>
<td><?php echo ($vo['tax']); ?>%
</td>
<td><?php echo (showPrice($vo['income2'])); ?></td>
<td>
<?php if(($vo['over'])  ==  "0"): ?><span style="color:red">未结</span>
<?php else: ?>
<span style="color:#CCC">完结</span><?php endif; ?>
</td>
</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
<tr>
<td colspan="3">总计</td>
<td><?php echo (showPrice($total+$taxTotal)); ?></td>
<td></td>
<td><?php echo (showPrice($income)); ?></td>
<td width="80">&nbsp;</td>
</tr>
</table>
</body>
</html>