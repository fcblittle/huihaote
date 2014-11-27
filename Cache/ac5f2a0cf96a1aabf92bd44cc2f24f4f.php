<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>打印</title>
<style>
.limit{width:620px; margin-top:20px; color:#000; font-size:12px;}
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

<h1 style="text-align:center; font-size:20px; margin:5px 0px;">退/换货（销售）</h1><br />

<div style="height:30px; line-height:30px; width:100%; margin:5px;">
<div style="width:38%; float:left;">供应商名称：<?php echo ($client['name']); ?></div>
<div style="width:32%; float:left; text-align:center;">开单日期：<?php echo date("Y-m-d", $rtime); ?>　</div>
<div style="width:30%; float:left; text-align:right;">编　　号：<?php echo ($num); ?>　</div>
</div>
<table width="100%" cellpadding="0" cellspacing="0" class="table">
<tr height="30">
<td>序号</td>
<td>货品名称</td>
<td>规格型号</td>
<td>单位</td>
<td>数量</td>
<td>单价</td>
<td>金额</td>
<td width="180">备注</td>
</tr>
<?php $i = 1;?>
<?php if(is_array($goods)): ?><?php $i = 0;?><?php $__LIST__ = $goods?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
<td><?php echo $i;  $i++;?></td>
<td><?php echo ($p[$vo['goods']]['name']); ?></td>
<td><?php echo ($p[$vo['goods']]['model']); ?></td>
<td><?php echo ($p[$vo['goods']]['unit']); ?></td>
<td><?php echo ($vo['num']); ?></td>
<td><?php echo (showPrice($vo['price'])); ?></td>
<td><?php echo (showPrice($vo['total'])); ?></td>
<td><?php echo ($vo['com']); ?></td>
</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
<tr>
<td >产品<br />编号</td>
<td colspan="7" style="text-align:left; padding-left:10px;"><?php echo ($comment); ?></td>
</tr>
<tr>
<td colspan="8" style="text-align:left; padding-left:10px;">
金额大写(合计)： <?php echo ($cMoney); ?>　　
<span style="float:right; padding-right:50px;"><?php echo (showPrice($price)); ?></span>
</td>
</tr>
</table>
<div style="height:20px; line-height:20px; width:100%; margin:5px;">
<div style="width:30%; float:left;">开单：<?php echo ($user['name']); ?></div>
<div style="width:30%; float:left;">审批：<?php echo ($aud['name']); ?></div>
<div style="width:30%; float:left;">经办人：<?php echo ($allUsers[$uid]['name']); ?></div>
</div>
</div>
</body>
</html>