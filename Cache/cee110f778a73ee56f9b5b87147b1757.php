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

<h1 style="text-align:center; font-size:20px; margin:5px 0px;">出库单</h1><br />

<div style="height:20px; line-height:20px; width:100%; margin:5px;">
<div style="width:30%; float:left;">编号：<?php echo ($num); ?>　</div>
<div style="width:40%; float:left;">客户名称：<?php echo ($client['name']); ?></div>
<div style="width:28%; float:left; text-align:right;">开单日期：<?php echo date("Y-m-d", $stock['time']); ?>　</div>
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
<td>税率</td>
<td>税额</td>
</tr>
<?php if(is_array($goods)): ?><?php $i = 0;?><?php $__LIST__ = $goods?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
<td><?php echo $i; ?></td>
<td><?php echo ($p[$vo['goods']]['name']); ?></td>
<td><?php echo ($p[$vo['goods']]['model']); ?></td>
<td><?php echo ($p[$vo['goods']]['unit']); ?></td>
<td><?php echo ($vo['num']); ?></td>
<td><?php echo (showPrice($vo['price'])); ?></td>
<td><?php echo (showPrice($vo['total'])); ?></td>
<td><?php echo ($vo['tax']); ?>%</td>
<td><?php echo (showPrice($vo['tax_total'])); ?></td>
</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
<tr>
<td>合　计</td>
<td colspan="5">&nbsp;</td>
<td><?php echo (showPrice($total)); ?></td>
<td>&nbsp;</td>
<td><?php echo (showPrice($tax_total)); ?></td>
</tr>
<tr>
<td colspan="9" style="text-align:left; padding-left:10px;">
金额大写(合计)： <?php echo ($cMoney); ?>　　
<span style="float:right; padding-right:50px;"><?php echo (showPrice($total+$tax_total)); ?></span>
</td>
</tr>
<tr>
<td >产品<br />编号</td>
<td colspan="8" style="text-align:left; padding-left:10px;"><?php echo ($comment); ?></td>
</tr>
</table>
<div style="height:20px; line-height:20px; width:100%; margin:5px; text-align:center;">
<div style="width:30%; float:left;">开单：<?php echo ($user['name']); ?></div>
<div style="width:30%; float:left;">审批：<?php echo ($aud['name']); ?></div>
<div style="width:30%; float:left;">负责人：<?php echo ($allUsers[$sale]['name']); ?></div>
</div>
</div>
</body>
</html>