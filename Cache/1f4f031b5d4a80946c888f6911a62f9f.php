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

<h1 style="text-align:center; font-size:20px; margin:5px 0px;">利润统计明细</h1>

<div style="height:30px; line-height:30px; width:100%; margin:5px;">
<div style="width:100%; float:left;">
时间： <?php if($stime): ?><?php echo ($stime); ?> 至 <?php echo ($etime); ?><?php endif; ?>
</div>
</div>

<table cellpadding="2" cellspacing="1" border="0" class="table">
<tr>
<td>销售总计</td>
<td colspan="2">
<?php if(($order['income']+$order['taxincome']-$swap['sale']) < 0){?>
-<?php echo (showPrice($order['income']+$order['taxincome']-$swap['sale'])); ?>
<?php }else{ ?>
<?php echo (showPrice($order['income']+$order['taxincome']-$swap['sale'])); ?>
<?php } ?>
</td>
<td>含税销售</td>
<td colspan="3">
<?php if(($order['incometaxtotal']+$order['taxincome']-$swap['taxsale']) < 0){?>
-<?php echo (showPrice($order['incometaxtotal']+$order['taxincome']-$swap['taxsale'])); ?>
<?php }else{ ?>
<?php echo (showPrice($order['incometaxtotal']+$order['taxincome']-$swap['taxsale'])); ?>
<?php } ?>
</td>
<td>应收账款</td>
<td colspan="3">
<?php if($debt['income'] < 0){?>
-<?php echo (showPrice($debt['income'])); ?>
<?php }else{ ?>
<?php echo (showPrice($debt['income'])); ?>
<?php } ?>
</td>
</tr>
<tr>
<td>维修总计</td>
<td colspan="2">
<?php if($serviceTotal < 0){?>
-<?php echo (showPrice($serviceTotal)); ?>
<?php }else{ ?>
<?php echo (showPrice($serviceTotal)); ?>
<?php } ?>
</td>
<td>含税维修</td>
<td colspan="3">
<?php if($taxService < 0){?>
-<?php echo (showPrice($taxService)); ?>
<?php }else{ ?>
<?php echo (showPrice($taxService)); ?>
<?php } ?>
</td>
<td>应收账款</td>
<td colspan="3">
<?php if($serviceDebt < 0){?>
-<?php echo (showPrice($serviceDebt)); ?>
<?php }else{ ?>
<?php echo (showPrice($serviceDebt)); ?>
<?php } ?>
</td>
</tr>
<tr>
<td>采购总计</td>
<td colspan="2">
<?php if(($order['pay']+$order['taxpay']-$swap['buy']) < 0){?>
-<?php echo (showPrice($order['pay']+$order['taxpay']-$swap['buy'])); ?>
<?php }else{ ?>
<?php echo (showPrice($order['pay']+$order['taxpay']-$swap['buy'])); ?>
<?php } ?>
</td>
<td>含税采购</td>
<td colspan="3">
<?php if(($order['paytaxtotal']+$order['taxpay']-$swap['taxbuy']) < 0){?>
-<?php echo (showPrice($order['paytaxtotal']+$order['taxpay']-$swap['taxbuy'])); ?>
<?php }else{ ?>
<?php echo (showPrice($order['paytaxtotal']+$order['taxpay']-$swap['taxbuy'])); ?>
<?php } ?>
</td>
<td>应付账款</td>
<td colspan="3">
<?php if($debt['pay'] < 0){?>
-<?php echo (showPrice($debt['pay'])); ?>
<?php }else{ ?>
<?php echo (showPrice($debt['pay'])); ?>
<?php } ?>
</td>
</tr>
<tr>
<td colspan="15" height="10">&nbsp;</td>
</tr>
<tr>
<td>经办人</td>
<td>销售金额</td>
<td>含税销售金额</td>
<td>维修金额</td>
<td>含税维修金额</td>
<td>应收金额</td>
<td>销售成本</td>
<td>维修成本</td>
<td>费用合计</td>
<td>毛利润</td>
<td>员工费用</td>
</tr>
<?php $dailyTotal = $workerTotal = $saleCost = $grossProfit = $serviceCost = 0; ?>
<?php if(is_array($users)): ?><?php $i = 0;?><?php $__LIST__ = $users?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><?php 
//过滤没有数据的人员
if(!($list[$vo['uid']]['total'] || $list[$vo['uid']]['return'] || $list[$vo['uid']]['service'] 
|| $list[$vo['uid']]['tax_total'] || $list[$vo['uid']]['debt'] || $list[$vo['uid']]['debt_service']  || $list[$vo['uid']]['taxReturn'] || $list[$vo['uid']]['have_tax'] || $list[$vo['uid']]['cost_total'] || $list[$vo['uid']]['cost_return']
|| $list[$vo['uid']]['cost'] || $list[$vo['uid']]['cost_service'] || $list[$vo['uid']]['daily']
|| $list[$vo['uid']]['worker'])) continue; 
?>

<tr>
<td><?php echo ($vo['name']); ?></td>
<td>
<?php if(($list[$vo['uid']]['total']-$list[$vo['uid']]['return']) < 0){?>
-<?php echo (showPrice($list[$vo['uid']]['total']-$list[$vo['uid']]['return'])); ?>
<?php }else{ ?>
<?php echo (showPrice($list[$vo['uid']]['total']-$list[$vo['uid']]['return'])); ?>
<?php } ?>

</td>
<td>
<?php if(($list[$vo['uid']]['tax_total']+$list[$vo['uid']]['have_tax']-$list[$vo['uid']]['taxReturn']) < 0){?>
-<?php echo (showPrice($list[$vo['uid']]['tax_total']+$list[$vo['uid']]['have_tax']-$list[$vo['uid']]['taxReturn'])); ?>
<?php }else{ ?>
<?php echo (showPrice($list[$vo['uid']]['tax_total']+$list[$vo['uid']]['have_tax']-$list[$vo['uid']]['taxReturn'])); ?>
<?php } ?>
</td>
<td>
<?php if($list[$vo['uid']]['service'] < 0){?>
-<?php echo (showPrice($list[$vo['uid']]['service'])); ?>
<?php }else{ ?>
<?php echo (showPrice($list[$vo['uid']]['service'])); ?>
<?php } ?>
</td>
<td>
<?php if($list[$vo['uid']]['haveTaxService'] < 0){?>
-<?php echo (showPrice($list[$vo['uid']]['haveTaxService'])); ?>
<?php }else{ ?>
<?php echo (showPrice($list[$vo['uid']]['haveTaxService'])); ?>
<?php } ?>
</td>
<td>
<?php if(($list[$vo['uid']]['debt']+$list[$vo['uid']]['debt_service']) < 0){?>
-<?php echo (showPrice($list[$vo['uid']]['debt']+$list[$vo['uid']]['debt_service'])); ?>
<?php }else{ ?>
<?php echo (showPrice($list[$vo['uid']]['debt']+$list[$vo['uid']]['debt_service'])); ?>
<?php } ?>
</td>


<td>
<?php if(($list[$vo['uid']]['cost_total']-$list[$vo['uid']]['cost_return']) < 0) {?>
-<?php echo (showPrice($list[$vo['uid']]['cost_total']-$list[$vo['uid']]['cost_return'])); ?>
<?php }else{?>
<?php echo (showPrice($list[$vo['uid']]['cost_total']-$list[$vo['uid']]['cost_return'])); ?>
<?php } ?>
<?php 
$saleCost += ($list[$vo['uid']]['cost_total']-$list[$vo['uid']]['cost_return']); ?>
</td>
<td>
<?php if(($list[$vo['uid']]['cost_service']) < 0){?>
-<?php echo (showPrice($list[$vo['uid']]['cost_service'])); ?>
<?php }else{?>
<?php echo (showPrice($list[$vo['uid']]['cost_service'])); ?>
<?php }?>
<?php 
$serviceCost += ($list[$vo['uid']]['cost_service']); ?>
</td>
<td>
<!--<?php if(($list[$vo['uid']]['daily'])  <  "0"): ?>-<?php endif; ?>
<?php echo (showPrice($list[$vo['uid']]['daily'])); ?> 
<?php $dailyTotal += $list[$vo['uid']]['daily']; ?>-->
<?php if($list[$vo['uid']]['daily'] < 0){?>
-<?php echo (showPrice($list[$vo['uid']]['daily'])); ?>
<?php }else{ ?>
<?php echo (showPrice($list[$vo['uid']]['daily'])); ?>
<?php } ?>
</td>
<td>
<?php 
$profit = ($list[$vo['uid']]['total']+$list[$vo['uid']]['service']-$list[$vo['uid']]['return']) - ($list[$vo['uid']]['cost_total']-$list[$vo['uid']]['cost_return']+$list[$vo['uid']]['cost_service']) + $list[$vo['uid']]['daily'];
//echo ($profit < 0 ? '-' : '') .showPrice($profit); 
if($profit < 0){
?>
-<?php echo (showPrice($profit)); ?>
<?php 
}else{
?>
<?php echo (showPrice($profit)); ?>
<?php
}
$grossProfit += $profit;
?>
</td>
<td>
<!--<?php if(($list[$vo['uid']]['worker'])  <  "0"): ?>-<?php endif; ?>
<?php echo (showPrice($list[$vo['uid']]['worker'])); ?> 
<?php $workerTotal += $list[$vo['uid']]['worker']; ?>-->
<?php if($list[$vo['uid']]['worker'] < 0){?>
-<?php echo (showPrice($list[$vo['uid']]['worker'])); ?>
<?php }else{ ?>
<?php echo (showPrice($list[$vo['uid']]['worker'])); ?>
<?php } ?>
</td>
</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
<tr>
<td>合计：</td>
<td>
<?php if(($order['income']+$order['taxincome']-$swap['sale']) < 0){?>
-<?php echo (showPrice($order['income']+$order['taxincome']-$swap['sale'])); ?>
<?php }else{ ?>
<?php echo (showPrice($order['income']+$order['taxincome']-$swap['sale'])); ?>
<?php } ?>
</td>
<td>
<?php if(($order['incometaxtotal']+$order['taxincome']-$swap['taxsale']) < 0){?>
-<?php echo (showPrice($order['incometaxtotal']+$order['taxincome']-$swap['taxsale'])); ?>
<?php }else{ ?>
<?php echo (showPrice($order['incometaxtotal']+$order['taxincome']-$swap['taxsale'])); ?>
<?php } ?>
</td>
<td>
<?php if($serviceTotal < 0){?>
-<?php echo (showPrice($serviceTotal)); ?>
<?php }else{ ?>
<?php echo (showPrice($serviceTotal)); ?>
<?php } ?>
</td>
<td>
<?php if($taxService < 0){?>
-<?php echo (showPrice($taxService)); ?>
<?php }else{ ?>
<?php echo (showPrice($taxService)); ?>
<?php } ?>
</td>
<td>
<?php if(($debt['income']+$serviceDebt) < 0){?>
-<?php echo (showPrice($debt['income']+$serviceDebt)); ?>
<?php }else{ ?>
<?php echo (showPrice($debt['income']+$serviceDebt)); ?>
<?php } ?>
</td>
<td>
<?php if($saleCost < 0){?>
-<?php echo (showPrice($saleCost)); ?>
<?php }else{ ?>
<?php echo (showPrice($saleCost)); ?>
<?php } ?>
</td>
<td>
<?php if($serviceCost < 0){?>
-<?php echo (showPrice($serviceCost)); ?>
<?php }else{ ?>
<?php echo (showPrice($serviceCost)); ?>
<?php } ?>
</td>
<td>
<?php if($dailyTotal < 0){?>
-<?php echo (showPrice($dailyTotal)); ?>
<?php }else{ ?>
<?php echo (showPrice($dailyTotal)); ?>
<?php } ?>
</td>
<td>
<?php if($grossProfit < 0){?>
-<?php echo (showPrice($grossProfit)); ?>
<?php }else{ ?>
<?php echo (showPrice($grossProfit)); ?>
<?php } ?>
</td>
<td>
<?php if($workerTotal < 0){?>
-<?php echo (showPrice($workerTotal)); ?>
<?php }else{ ?>
<?php echo (showPrice($workerTotal)); ?>
<?php } ?>
</td>
</tr>
<tr>
<td colspan="15" height="10">&nbsp;</td>
</tr>
<tr>
<td colspan="15" style="text-align: left;">
利　　润：
销售总计
(
<?php if(($order['income']+$order['taxincome']-$swap['sale']) < 0){?>
-<?php echo (showPrice($order['income']+$order['taxincome']-$swap['sale'])); ?>
<?php }else{ ?>
<?php echo (showPrice($order['income']+$order['taxincome']-$swap['sale'])); ?>
<?php } ?>
) + 
维修总计
(
<?php if($serviceTotal < 0){?>
-<?php echo (showPrice($serviceTotal)); ?>
<?php }else{ ?>
<?php echo (showPrice($serviceTotal)); ?>
<?php } ?>
) - 
采购总计
(
<?php if(($order['pay']+$order['taxpay']-$swap['buy']) < 0){?>
-<?php echo (showPrice($order['pay']+$order['taxpay']-$swap['buy'])); ?>
<?php }else{ ?>
<?php echo (showPrice($order['pay']+$order['taxpay']-$swap['buy'])); ?>
<?php } ?>
) - 
费用总计
(
<?php if($dailyTotal < 0){?>
-<?php echo (showPrice($dailyTotal)); ?>
<?php }else{ ?>
<?php echo (showPrice($dailyTotal)); ?>
<?php } ?>
) - 
员工费用
(
<?php if($workerTotal < 0){?>
-<?php echo (showPrice($workerTotal)); ?>
<?php }else{ ?>
<?php echo (showPrice($workerTotal)); ?>
<?php } ?>
) = 
<?php 
$total = ($order['income']+$order['taxincome']-$swap['sale']) + ($serviceTotal) - ($order['pay']+$order['taxpay']-$swap['buy']) - $dailyTotal - $workerTotal; 
if($total < 0){
?>
-<?php echo (showPrice($total)); ?>
<?php }else{ ?>
<?php echo (showPrice($total)); ?>
<?php } ?>
<br />
仓库结余：
期初结余
(
<?php if($_stockRemain < 0){?>
-<?php echo (showPrice($_stockRemain)); ?>
<?php }else{ ?>
<?php echo (showPrice($_stockRemain)); ?>
<?php } ?>
) + 
时间段内购买
(
<?php if(($order['pay']+$order['taxpay']-$swap['buy']) < 0){?>
-<?php echo (showPrice($order['pay']+$order['taxpay']-$swap['buy'])); ?>
<?php }else{ ?>
<?php echo (showPrice($order['pay']+$order['taxpay']-$swap['buy'])); ?>
<?php } ?>
) - 
时间段内销售成本
(
<?php if($saleCost < 0){?>
-<?php echo (showPrice($saleCost)); ?>
<?php }else{ ?>
<?php echo (showPrice($saleCost)); ?>
<?php } ?>
) - 
时间段内维修成本
(
<?php if($serviceCost < 0){?>
-<?php echo (showPrice($serviceCost)); ?>
<?php }else{ ?>
<?php echo (showPrice($serviceCost)); ?>
<?php } ?>
) =  
<?php if(($stockRemain - $saleCost-$serviceCost) < 0){?>
-<?php echo (showPrice($stockRemain - $saleCost-$serviceCost)); ?>
<?php }else{ ?>
<?php echo (showPrice($stockRemain - $saleCost-$serviceCost)); ?>
<?php } ?>
</td>
</tr>
</table>

</body>
</html>