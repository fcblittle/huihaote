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
<h3>现金明细 列表</h3>

<form action="/index.php/Financial/money/" style="padding-bottom:10px;">
来 源：
<select name="come" id="come" style="width:180px;">
<option value="1" <?php if(($come)  ==  "1"): ?>selected="selected"<?php endif; ?>>全部</option>
<option value="order2" <?php if(($come)  ==  "order2"): ?>selected="selected"<?php endif; ?>>销售出库</option>
<option value="order1" <?php if(($come)  ==  "order1"): ?>selected="selected"<?php endif; ?>>采购入库</option>
<option value="service" <?php if(($come)  ==  "service"): ?>selected="selected"<?php endif; ?>>维修管理</option>
<option value="daily" <?php if(($come)  ==  "daily"): ?>selected="selected"<?php endif; ?>>日常支出</option>
<option value="worker" <?php if(($come)  ==  "worker"): ?>selected="selected"<?php endif; ?>>员工费用</option>
<option value="allot" <?php if(($come)  ==  "allot"): ?>selected="selected"<?php endif; ?>>银行调拨</option>
</select>　　
时 间：
<input type="text" style="width:100px;" name="stime" readonly="readonly" onclick="showdatebox(this,'')" value="<?php echo ($stime); ?>" /> --
<input type="text" style="width:100px;" name="etime" readonly="readonly" onclick="showdatebox(this,'')" value="<?php echo ($etime); ?>" />
<input type="hidden" name="id" value="<?php echo ($bank); ?>" />
<input type="submit" name='ac' value="查 询" />
<input type="submit" name='ac' value="导 出" />
<input type="submit" name='ac' value="打 印" />
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>

<div style="height:30px; line-height:30px; padding-left:20px; margin-bottom:5px;">
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

<table cellpadding="2" cellspacing="1" border="0" class="needhover">
<tr>
<th>价格</th>
<th>说明</th>
<th>类型</th>
<th>记录时间</th>
<th width="250">操作</th>
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
<?php case "fixed":  ?>固定资产<?php break;?>
<?php case "allot":  ?>银行调拨<?php break;?><?php endswitch;?>
</td>
<td><?php echo (date('Y-m-d',is_array($vo)?$vo["time"]:$vo->time)); ?></td>
<td>
<?php if(isset($can_do['Financial']['delBankRecord'])): ?><a href="/index.php/Financial/delBankRecord/id/<?php echo ($vo['id']); ?>">删 除</a>&nbsp;┊&nbsp;<?php endif; ?>
<?php
$_url = '';
switch ($vo['type'])
{
case 'daily':
$_url = 'Financial/editDaily/id/'. $vo['pid'];
break;
case 'worker':
$_url = 'Financial/editDaily/id/'. $vo['pid'];
break;
case 'order':
if(false !== strpos($vo['comment'], 'xsck'))
$_url = 'Sorder/view/id/'. $vo['pid'];
else
$_url = 'Porder/view/id/'. $vo['pid'];
break;
case 'service':
$_url = 'Service/view/id/'. $vo['pid'];
break;
case 'return':
if(false !== strpos($vo['comment'], 'xsck'))
$_url = 'Sorder/viewSwap/id/'. $vo['pid'];
else
$_url = 'Porder/viewSwap/id/'. $vo['pid'];
break;
case 'fixed':
$_url = 'Fixed/edit/id/'. $vo['pid'];
break;
case 'allot':
$_url = 'Financial/viewAllot/id/'. $vo['pid'];
break;    
default:
$_url = '';
}
?>
<a href="/index.php/<?php echo ($_url); ?>">查 看</a>
</td>
</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
<tr>
<td colspan="5" style="text-align:right;"><?php echo ($page); ?></td>
</tr>
</table>
<script src="/./Tpl/default/Public/JS/datebox.js"></script>
<script type="text/javascript">
function openSearch(){
$("#Search").show();
}
function audit(id){
if(window.confirm("该款项已经结清，是否确认？\n如有欠款，可编辑修改")){
location.href='/index.php/Financial/audit/id/'+id;
}
}
$("#selectName").change(function(){if($(this).val() == 'other'){$("#nameOther").show();}else{$("#nameOther").hide();} });
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