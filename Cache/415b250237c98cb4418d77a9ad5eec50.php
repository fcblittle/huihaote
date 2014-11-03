<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Encoding" content="gzip" />
<title>青岛惠好特--企业进销存管理系统 &rsaquo; <?php echo ($title); ?></title>
<meta name="author" content="Interidea Team" />
<meta name="copyright" content="2012 Interidea Co.,Ltd" />
<link href="/./Tpl/default/Public/Style/main.css" rel="stylesheet" type="text/css" />
<link href="/./Tpl/default/Public/Style/login.css" rel="stylesheet" type="text/css" />
<script src="/./Tpl/default/Public/JS/jquery.js" type="text/javascript"></script>
<script src="/./Tpl/default/Public/JS/fileuploader.js" type="text/javascript"></script>
<script src="/./Tpl/default/Public/JS/main.js" type="text/javascript"></script>
<script src="/./Tpl/default/Public/JS/Editor/nicEdit.js" type="text/javascript"></script>
</head>


<body>
<div id="header">
<div id="logo"><img src="/./Tpl/default/Public/Images/logo.jpg" /></div>
<div id="product"> </div>
</div>
<div id="main">
<div id="topNav">
<span id="sayWhat">您好，<?php echo $_SESSION["name"];?>！</span>
<span class="r">
<a class="home" href="/index.php/Index/index">返回首页</a>
<?php if(isset($can_do['Home']['mail'])): ?><a class="mail" href="/index.php/Home/mail">我的邮箱<span></span></a><?php endif; ?>
<?php if(isset($can_do['Home']['password'])): ?><a class="password" href="/index.php/Home/password">修改密码</a><?php endif; ?>
<a class="logout" href="/index.php/Public/logout">退出系统</a>
</span>
</div>
<div id="body">
<div id="menu" class="">
<dl class="tree">
<?php if(is_array($menu)): ?><?php $i = 0;?><?php $__LIST__ = $menu?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$mo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><dt class="off"><?php echo ($mo['name']); ?></dt>
<dd>
<ul>
<?php if(is_array($mo['child'])): ?><?php $i = 0;?><?php $__LIST__ = $mo['child']?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$mli): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><li><a href="/index.php/<?php echo ($mli['m']); ?>/<?php echo ($mli['a']); ?>"><?php echo ($mli['name']); ?></a></li><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</ul>
</dd><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</dl>
<div id="hide_control"><span onclick="javascript:tMenu();">&nbsp;</span></div>
</div>
<div id="content">
<div id="msg">
<div>
<?php if(is_array($inform)): ?><?php $i = 0;?><?php $__LIST__ = $inform?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$in): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><a href="/index.php/Home/inform/view/<?php echo ($in['id']); ?>"><?php echo ($in['title']); ?></a><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</div>
</div>
<div id="table">
<h3>利润统计</h3>
<script type="text/javascript" src="/./Tpl/default/Public/JS/datebox.js"></script>

<form action="/index.php/Financial/profit" style="padding-bottom:10px;">　
时 间：
<input type="text" style="width:100px;" name="stime" readonly="readonly" onclick="showdatebox(this,'')" value="<?php echo ($stime); ?>" /> --
<input type="text" style="width:100px;" name="etime" readonly="readonly" onclick="showdatebox(this,'')" value="<?php echo ($etime); ?>" />

<input class="button" name="ac" type="submit" value="搜 索" />　
<input class="button" name="ac" type="submit" value="打 印" />　
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>

<table cellpadding="2" cellspacing="1" border="0" class="needhover">
<tr>
<th>销售总计</th>
<td colspan="2">
<?php if(($order['income']+$order['taxincome']-$swap['sale']) < 0){?>
<span style="color:red;">-<?php echo (showPrice($order['income']+$order['taxincome']-$swap['sale'])); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($order['income']+$order['taxincome']-$swap['sale'])); ?></span>
<?php } ?>
</td>
<th>含税销售</th>
<td colspan="3">
<?php if(($order['incometaxtotal']+$order['taxincome']-$swap['taxsale']) < 0){?>
<span style="color:red;">-<?php echo (showPrice($order['incometaxtotal']+$order['taxincome']-$swap['taxsale'])); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($order['incometaxtotal']+$order['taxincome']-$swap['taxsale'])); ?></span>
<?php } ?>
</td>
<th>应收账款</th>
<td colspan="3">
<?php if($debt['income'] < 0){?>
<span style="color:red;">-<?php echo (showPrice($debt['income'])); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($debt['income'])); ?></span>
<?php } ?>
</td>
</tr>
<tr>
<th>维修总计</th>
<td colspan="2">
<?php if($serviceTotal < 0){?>
<span style="color:red;">-<?php echo (showPrice($serviceTotal)); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($serviceTotal)); ?></span>
<?php } ?>
</td>
<th>含税维修</th>
<td colspan="3">
<?php if($taxService < 0){?>
<span style="color:red;">-<?php echo (showPrice($taxService)); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($taxService)); ?></span>
<?php } ?>
</td>
<th>应收账款</th>
<td colspan="3">
<?php if($serviceDebt < 0){?>
<span style="color:red;">-<?php echo (showPrice($serviceDebt)); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($serviceDebt)); ?></span>
<?php } ?>
</td>
</tr>
<tr>
<th>采购总计</th>
<td colspan="2">
<?php if(($order['pay']+$order['taxpay']-$swap['buy']) < 0){?>
<span style="color:red;">-<?php echo (showPrice($order['pay']+$order['taxpay']-$swap['buy'])); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($order['pay']+$order['taxpay']-$swap['buy'])); ?></span>
<?php } ?>
</td>
<th>含税采购</th>
<td colspan="3">
<?php if(($order['paytaxtotal']+$order['taxpay']-$swap['taxbuy']) < 0){?>
<span style="color:red;">-<?php echo (showPrice($order['paytaxtotal']+$order['taxpay']-$swap['taxbuy'])); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($order['paytaxtotal']+$order['taxpay']-$swap['taxbuy'])); ?></span>
<?php } ?>
</td>
<th>应付账款</th>
<td colspan="3">
<?php if($debt['pay'] < 0){?>
<span style="color:red;">-<?php echo (showPrice($debt['pay'])); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($debt['pay'])); ?></span>
<?php } ?>
</td>
</tr>
<tr>
<td colspan="15" height="10">&nbsp;</td>
</tr>
<tr>
<th>经办人</th>
<th>销售金额</th>
<th>含税销售金额</th>
<th>维修金额</th>
<th>含税维修金额</th>
<th>应收金额</th>
<th>销售成本</th>
<th>维修成本</th>
<th>费用合计</th>
<th>毛利润</th>
<th>员工费用</th>
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
<span style="color:red;">-<?php echo (showPrice($list[$vo['uid']]['total']-$list[$vo['uid']]['return'])); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($list[$vo['uid']]['total']-$list[$vo['uid']]['return'])); ?></span>
<?php } ?>

</td>
<td>
<?php if(($list[$vo['uid']]['tax_total']+$list[$vo['uid']]['have_tax']-$list[$vo['uid']]['taxReturn']) < 0){?>
<span style="color:red;">-<?php echo (showPrice($list[$vo['uid']]['tax_total']+$list[$vo['uid']]['have_tax']-$list[$vo['uid']]['taxReturn'])); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($list[$vo['uid']]['tax_total']+$list[$vo['uid']]['have_tax']-$list[$vo['uid']]['taxReturn'])); ?></span>
<?php } ?>
</td>
<td>
<?php if($list[$vo['uid']]['service'] < 0){?>
<span style="color:red;">-<?php echo (showPrice($list[$vo['uid']]['service'])); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($list[$vo['uid']]['service'])); ?></span>
<?php } ?>
</td>
<td>
<?php if($list[$vo['uid']]['haveTaxService'] < 0){?>
<span style="color:red;">-<?php echo (showPrice($list[$vo['uid']]['haveTaxService'])); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($list[$vo['uid']]['haveTaxService'])); ?></span>
<?php } ?>
</td>
<td>
<?php if(($list[$vo['uid']]['debt']+$list[$vo['uid']]['debt_service']) < 0){?>
<span style="color:red;">-<?php echo (showPrice($list[$vo['uid']]['debt']+$list[$vo['uid']]['debt_service'])); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($list[$vo['uid']]['debt']+$list[$vo['uid']]['debt_service'])); ?></span>
<?php } ?>
</td>


<td>
<?php if(($list[$vo['uid']]['cost_total']-$list[$vo['uid']]['cost_return']) < 0) {?>
<span style="color:red;">-<?php echo (showPrice($list[$vo['uid']]['cost_total']-$list[$vo['uid']]['cost_return'])); ?></span>
<?php }else{?>
<span style="color:green;"><?php echo (showPrice($list[$vo['uid']]['cost_total']-$list[$vo['uid']]['cost_return'])); ?></span>
<?php } ?>
<?php 
$saleCost += ($list[$vo['uid']]['cost_total']-$list[$vo['uid']]['cost_return']); ?>
</td>
<td>
<?php if(($list[$vo['uid']]['cost_service']) < 0){?>
<span style="color:red;">-<?php echo (showPrice($list[$vo['uid']]['cost_service'])); ?>
<?php }else{?>
<span style="color:green;"><?php echo (showPrice($list[$vo['uid']]['cost_service'])); ?>
<?php }?>
<?php 
$serviceCost += ($list[$vo['uid']]['cost_service']); ?>
</td>
<td>
<!--<?php if(($list[$vo['uid']]['daily'])  <  "0"): ?>-<?php endif; ?>
<?php echo (showPrice($list[$vo['uid']]['daily'])); ?> 
<?php $dailyTotal += $list[$vo['uid']]['daily']; ?>-->
<?php if($list[$vo['uid']]['daily'] < 0){?>
<span style="color:red;">-<?php echo (showPrice($list[$vo['uid']]['daily'])); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($list[$vo['uid']]['daily'])); ?></span>
<?php } ?>
</td>
<td>
<?php 
$profit = ($list[$vo['uid']]['total']+$list[$vo['uid']]['service']-$list[$vo['uid']]['return']) - ($list[$vo['uid']]['cost_total']-$list[$vo['uid']]['cost_return']+$list[$vo['uid']]['cost_service']) + $list[$vo['uid']]['daily'];
//echo ($profit < 0 ? '-' : '') .showPrice($profit); 
if($profit < 0){
?>
<span style="color:red;">-<?php echo (showPrice($profit)); ?></span>
<?php 
}else{
?>
<span style="color:green;"><?php echo (showPrice($profit)); ?></span>
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
<span style="color:red;">-<?php echo (showPrice($list[$vo['uid']]['worker'])); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($list[$vo['uid']]['worker'])); ?></span>
<?php } ?>
</td>
</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
<tr>
<td>合计：</td>
<td>
<?php if(($order['income']+$order['taxincome']-$swap['sale']) < 0){?>
<span style="color:red;">-<?php echo (showPrice($order['income']+$order['taxincome']-$swap['sale'])); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($order['income']+$order['taxincome']-$swap['sale'])); ?></span>
<?php } ?>
</td>
<td
<?php if(($order['incometaxtotal']+$order['taxincome']-$swap['taxsale']) < 0){?>
<span style="color:red;">-<?php echo (showPrice($order['incometaxtotal']+$order['taxincome']-$swap['taxsale'])); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($order['incometaxtotal']+$order['taxincome']-$swap['taxsale'])); ?></span>
<?php } ?>
</td>
<td>
<?php if($serviceTotal < 0){?>
<span style="color:red;">-<?php echo (showPrice($serviceTotal)); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($serviceTotal)); ?></span>
<?php } ?>
</td>
<td>
<?php if($taxService < 0){?>
<span style="color:red;">-<?php echo (showPrice($taxService)); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($taxService)); ?></span>
<?php } ?>
</td>
<td>
<?php if(($debt['income']+$serviceDebt) < 0){?>
<span style="color:red;">-<?php echo (showPrice($debt['income']+$serviceDebt)); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($debt['income']+$serviceDebt)); ?></span>
<?php } ?>
</td>
<td>
<?php if($saleCost < 0){?>
<span style="color:red;">-<?php echo (showPrice($saleCost)); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($saleCost)); ?></span>
<?php } ?>
</td>
<td>
<?php if($serviceCost < 0){?>
<span style="color:red;">-<?php echo (showPrice($serviceCost)); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($serviceCost)); ?></span>
<?php } ?>
</td>
<td>
<?php if($dailyTotal < 0){?>
<span style="color:red;">-<?php echo (showPrice($dailyTotal)); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($dailyTotal)); ?></span>
<?php } ?>
</td>
<td>
<?php if($grossProfit < 0){?>
<span style="color:red;">-<?php echo (showPrice($grossProfit)); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($grossProfit)); ?></span>
<?php } ?>
</td>
<td>
<?php if($workerTotal < 0){?>
<span style="color:red;">-<?php echo (showPrice($workerTotal)); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($workerTotal)); ?></span>
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
<span style="color:red;">-<?php echo (showPrice($order['income']+$order['taxincome']-$swap['sale'])); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($order['income']+$order['taxincome']-$swap['sale'])); ?></span>
<?php } ?>
) + 
维修总计
(
<?php if($serviceTotal < 0){?>
<span style="color:red;">-<?php echo (showPrice($serviceTotal)); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($serviceTotal)); ?></span>
<?php } ?>
) - 
采购总计
(
<?php if(($order['pay']+$order['taxpay']-$swap['buy']) < 0){?>
<span style="color:red;">-<?php echo (showPrice($order['pay']+$order['taxpay']-$swap['buy'])); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($order['pay']+$order['taxpay']-$swap['buy'])); ?></span>
<?php } ?>
) - 
费用总计
(
<?php if($dailyTotal < 0){?>
<span style="color:red;">-<?php echo (showPrice($dailyTotal)); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($dailyTotal)); ?></span>
<?php } ?>
) - 
员工费用
(
<?php if($workerTotal < 0){?>
<span style="color:red;">-<?php echo (showPrice($workerTotal)); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($workerTotal)); ?></span>
<?php } ?>
) = 
<?php 
$total = ($order['income']+$order['taxincome']-$swap['sale']) + ($serviceTotal) - ($order['pay']+$order['taxpay']-$swap['buy']) - $dailyTotal - $workerTotal; 
if($total < 0){
?>
<span style="color:red;">-<?php echo (showPrice($total)); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($total)); ?></span>
<?php } ?>
<br />
仓库结余：
期初结余
(
<?php if($_stockRemain < 0){?>
<span style="color:red;">-<?php echo (showPrice($_stockRemain)); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($_stockRemain)); ?></span>
<?php } ?>
) + 
时间段内购买
(
<?php if(($order['pay']+$order['taxpay']-$swap['buy']) < 0){?>
<span style="color:red;">-<?php echo (showPrice($order['pay']+$order['taxpay']-$swap['buy'])); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($order['pay']+$order['taxpay']-$swap['buy'])); ?></span>
<?php } ?>
) - 
时间段内销售成本
(
<?php if($saleCost < 0){?>
<span style="color:red;">-<?php echo (showPrice($saleCost)); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($saleCost)); ?></span>
<?php } ?>
) - 
时间段内维修成本
(
<?php if($serviceCost < 0){?>
<span style="color:red;">-<?php echo (showPrice($serviceCost)); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($serviceCost)); ?></span>
<?php } ?>
) =  
<?php if(($stockRemain - $saleCost-$serviceCost) < 0){?>
<span style="color:red;">-<?php echo (showPrice($stockRemain - $saleCost-$serviceCost)); ?></span>
<?php }else{ ?>
<span style="color:green;"><?php echo (showPrice($stockRemain - $saleCost-$serviceCost)); ?></span>
<?php } ?>
</td>
</tr>
</table>
<script type="text/javascript">
function getSum()
{
var st = parseInt($("#st").val());
if(!st)
st = 0;
var su = $("#sum").val();
$("#total").html('￥'+(st*100+parseInt(su))/100);
}
</script>

</div>
</div>
</div>
</div>
<div id="footer"><!-- 技术支持：青岛互联创想信息技术有限公司 --></div>
<div id="shadow"></div>
<div id="window">
<dl id="win_main">
<dt class="title"><span>标题</span><a href="javascript:closeWin()">x</a></dt>
<dd class="content"></dd>
<dd class="control"><input type="button" value="确 定" /><input type="button" class="cancel" onclick="closeWin()" value="取 消" /></dd>
</dl>
</div>
<script type="text/javascript">
getMessage("/index.php/Home/inform/ajax/1", "<?php echo constant("MODULE_NAME");?>/<?php echo constant("ACTION_NAME");?>");
</script>
</body>
</html>