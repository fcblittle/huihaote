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
<h3>收支统计</h3>
<script type="text/javascript" src="/./Tpl/default/Public/JS/datebox.js"></script>


<div style="padding:0px 10px 10px 0px; margin-bottom:10px; border-bottom:#CCC dashed 1px;">
<a href="/index.php/Financial/statistics/">收支合计</a> &nbsp;| &nbsp;
<a href="/index.php/Financial/statistics/type/trend">收支走势</a> &nbsp;| &nbsp;
<a href="/index.php/Financial/statistics/type/goods">产品收支统计</a>
</div>

<form action="/index.php/Financial/statistics/" style="padding-bottom:10px;">
来 源：
<select name="come" id="come" style="width:180px;">
<option value="1" <?php if(($come)  ==  "1"): ?>selected="selected"<?php endif; ?>>全部</option>
<option value="order2" <?php if(($come)  ==  "order2"): ?>selected="selected"<?php endif; ?>>销售出库</option>
<option value="order1" <?php if(($come)  ==  "order1"): ?>selected="selected"<?php endif; ?>>采购入库</option>
<option value="service" <?php if(($come)  ==  "service"): ?>selected="selected"<?php endif; ?>>维修管理</option>
<option value="daily" <?php if(($come)  ==  "daily"): ?>selected="selected"<?php endif; ?>>日常支出</option>
<option value="worker" <?php if(($come)  ==  "worker"): ?>selected="selected"<?php endif; ?>>员工费用</option>
</select>　　
时 间：
<input type="text" style="width:100px;" name="stime" readonly="readonly" onclick="showdatebox(this,'')" value="<?php echo ($stime); ?>" /> --
<input type="text" style="width:100px;" name="etime" readonly="readonly" onclick="showdatebox(this,'')" value="<?php echo ($etime); ?>" />

<input class="button" name="ac" type="submit" value="搜 索" />
<input class="button" name="ac" type="submit" value="导 出" />
<input class="button" name="ac" type="submit" value="打 印" />
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>

<div style="height:30px; line-height:30px; padding-left:20px;">
期初余额: <?php echo (showPrice($begin)); ?>　
现金支出：<?php echo (showPrice($moneyexpense)); ?>　
银行支出：<?php echo (showPrice($bankexpense)); ?>　
现金收入：<?php echo (showPrice($moneyrevenue)); ?>　
银行收入：<?php echo (showPrice($bankrevenue)); ?>　
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
<th>收支内容</th>
<th>收支金额</th>
<th>提成</th>
<th width="100">做账日期</th>
<th>到账情况</th>
<th width="100">做账人</th>
<th width="100">操作</th>
</tr>
<?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
<td><?php echo (text(is_array($vo)?$vo["name"]:$vo->name)); ?></td>
<td>
<?php if((is_array($vo)?$vo["price"]:$vo->price)  >  "0"): ?><span style="color:green"><?php echo showPrice(abs($vo['price'])-abs($vo['notto'])); ?></span>
<?php else: ?>
<span style="color:red">-<?php echo showPrice(abs($vo['price'])-abs($vo['notto'])); ?></span><?php endif; ?>
</td>
<td><?php echo (showPrice(is_array($vo)?$vo["income"]:$vo->income)); ?></td>
<td><?php echo (date('Y-m-d',is_array($vo)?$vo["time"]:$vo->time)); ?></td>
<td>
<?php if((is_array($vo)?$vo["audit"]:$vo->audit)  ==  "0"): ?><span style="color:red">未审核</span>
<?php else: ?>
<?php if((is_array($vo)?$vo["notto"]:$vo->notto)  !=  "0"): ?><span style="color:#000">
<?php if(($vo['price'])  >  "0"): ?>应收<?php else: ?>应付<?php endif; ?>：<?php echo (showPrice(is_array($vo)?$vo["notto"]:$vo->notto)); ?></span>
<?php else: ?>
<span style="color:#CCC">已<?php if((is_array($vo)?$vo["price"]:$vo->price)  >  "0"): ?>到帐<?php else: ?>付清<?php endif; ?></span><?php endif; ?><?php endif; ?>
</td>
<td>
<?php if(isset($can_do['User']['view'])): ?><a href="/index.php/User/view/id/<?php echo (is_array($vo)?$vo["uid"]:$vo->uid); ?>"><?php echo ($vo['user']['name']); ?></a>
<?php else: ?>
<?php echo ($vo['user']['name']); ?><?php endif; ?>
</td>

<td>
<a href="/index.php<?php echo ($vo['url']); ?>">查看明细</a>
<!--
<?php if(isset($can_do['Financial']['edit'])): ?>&nbsp;┊&nbsp;<a href="/index.php/Financial/edit/id/<?php echo (is_array($vo)?$vo["id"]:$vo->id); ?>">修 改</a><?php endif; ?>
<?php if(isset($can_do['Financial']['delete'])): ?>&nbsp;┊&nbsp;<a href="/index.php/Financial/delete/id/<?php echo (is_array($vo)?$vo["id"]:$vo->id); ?>">删 除</a><?php endif; ?>
<?php if((is_array($vo)?$vo["audit"]:$vo->audit)  ==  "0"): ?><?php if(isset($can_do['Financial']['audit'])): ?>&nbsp;┊&nbsp;<a href="javascript:audit(<?php echo (is_array($vo)?$vo["id"]:$vo->id); ?>)">审 核</a><?php endif; ?><?php endif; ?>
&nbsp;┊&nbsp;
-->
</td>

</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
<tr>
<td colspan="7" style="text-align:right;"><?php echo ($page); ?></td>
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