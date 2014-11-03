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
<h3>库存统计</h3>
<script type="text/javascript" src="/./Tpl/default/Public/JS/datebox.js"></script>

<div style="padding:0px 10px 10px 0px; margin-bottom:10px; border-bottom:#CCC dashed 1px; ">
<a href="/index.php/Stock/statistics/">仓库存量</a> &nbsp;| &nbsp;
<a href="/index.php/Stock/statistics/type/inout">货品进出量</a> &nbsp;| &nbsp;
<a href="/index.php/Stock/statistics/type/trend">货品进出量走势</a>
</div>

<form action="/index.php/Stock/statistics/" >
仓 库：
<select name="group" style="margin-bottom:10px;">
<?php if(is_array($group)): ?><?php $i = 0;?><?php $__LIST__ = $group?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo['gid']); ?>" <?php if(($vo['gid'])  ==  $gid): ?>selected="selected"<?php endif; ?> ><?php echo ($vo['levelstr']); ?><?php echo ($vo['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>
货物类型：<select name="goods_type" class="goods_type"></select>
货 品：<select name="goods" class="goods"></select>
日 期：<input type="text" style="width:100px;" name="stime" onclick="showdatebox(this,'')" /> --
<input type="text" style="width:100px;" name="etime" onclick="showdatebox(this,'')" />
<input type="submit" name="ac" value="查 询" />
<input type="submit" name="ac" value="导 出" />
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>


<style type="text/css">
.cl{ display:block; #padding-left:30%; #text-align:left; text-align:center;}
</style>

<table cellpadding="2" cellspacing="1" border="0" class="needhover">
<tr>
<th>产品名称</th>
<th width="100">总 量</th>
<?php if(($colspan)  ==  "6"): ?><th>初始量</th>
<th>进量</th>
<!--
<th>采购价格</th>
-->
<th>采购总价<?php $payTotal = 0; ?></th>
<th>出量</th>
<!--
<th >销售价格</th>
-->
<th>销售总价<?php $incomeTotal = 0; ?></th>                                                             
<?php else: ?>
<!--<th width="100">采购价格</th>-->
<th width="200">采购总价<?php $payTotal = 0; ?></th>
<!--<th width="100">销售价格<?php $incomeTotal = 0; ?></th>-->
<th width="200">销售总价</th><?php endif; ?>

<th width="150">操 作</th>
</tr>
<?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
<td>
<span class="cl">
<?php if(isset($can_do['Goods']['view'])): ?><a href="/index.php/Goods/view/id/<?php echo (is_array($vo)?$vo["goods"]:$vo->goods); ?>"><?php echo ($allgoods[$vo['goods']]['name']); ?></a>
<?php else: ?>
<?php echo ($allgoods[$vo['goods']]['name']); ?><?php endif; ?>
(<?php echo ($allgoods[$vo['goods']]['model']); ?>)
</span>
</td>
<td><?php echo (is_array($vo)?$vo["sum"]:$vo->sum); ?> <?php echo ($allgoods[$vo['goods']]['unit']); ?></td>
<?php if(($colspan)  ==  "6"): ?><td><?php echo (is_array($vo)?$vo["start"]:$vo->start); ?> <?php echo ($allgoods[$vo['goods']]['unit']); ?></td>

<td><?php echo (is_array($vo)?$vo["in"]:$vo->in); ?> <?php echo ($allgoods[$vo['goods']]['unit']); ?></td>
<!--
<td><?php echo showPrice($allgoods[$vo['goods']]['cost']); ?></td>
-->
<td><?php echo showPrice($vo['inprice']); /*$costTotal += $vo['in'] * $allgoods[$vo['goods']]['cost']; */ $costTotal += $vo['inprice'];?></td>
<td><?php echo (is_array($vo)?$vo["out"]:$vo->out); ?> <?php echo ($allgoods[$vo['goods']]['unit']); ?></td>
<!--
<td><?php echo showPrice($allgoods[$vo['goods']]['price']); ?></td>
-->
<td><?php echo showPrice($vo['outprice']); /*$priceTotal += $vo['out'] * $allgoods[$vo['goods']]['price'];*/ $priceTotal += $vo['outprice']; ?></td>
<?php else: ?>
<!--<td><?php echo showPrice($allgoods[$vo['goods']]['cost']); ?></td>-->
<td><?php echo showPrice($vo['pay']); $payTotal += $vo['pay']; ?></td>
<!--<td><?php echo showPrice($allgoods[$vo['goods']]['price']); ?></td>-->
<td><?php echo showPrice($vo['income']); $incomeTotal += $vo['income']; ?></td><?php endif; ?>
<td>
<!--
<?php if(isset($can_do['Stock']['inorde'])): ?>&nbsp;┊&nbsp; <a href="/index.php/Stock/inorde/group/<?php echo ($gid); ?>/goods/<?php echo ($vo['goods']); ?>/type/de">报 损</a>
&nbsp;┊&nbsp; <a href="/index.php/Stock/inorde/group/<?php echo ($gid); ?>/goods/<?php echo ($vo['goods']); ?>/type/in">报 溢</a>
&nbsp;┊&nbsp;<?php endif; ?>
-->
<a href="/index.php/Stock/detail/group/<?php echo ($gid); ?>/goods/<?php echo ($vo['goods']); ?>">详 情</a>
</td>
</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
<?php if(($colspan)  !=  "6"): ?><tr>
<th colspan="2">合 计</th>
<th><?php echo (showPrice($payTotal)); ?></th>
<th><?php echo (showPrice($incomeTotal)); ?></th>
<th>&nbsp;</th>
</tr>
<?php else: ?>
<tr>
<th colspan="4">合 计</th>
<th><?php echo (showPrice($costTotal)); ?></th>
<th colspan="1">&nbsp;</th>
<th><?php echo (showPrice($priceTotal)); ?></th>
<th colspan="2">&nbsp;</th>
</tr><?php endif; ?>
</table>

<script type="text/javascript">
$(document).ready(function(){
ajax("/index.php/Goods/index", {type:1}, 'post', function(data){
if(data.status){
var select = '';
for(x in data.data.type){
var t = data.data.type[x];
select = (t.id == '<?php echo (int)$_GET['goods_type']; ?>') ? 'selected="selected"' : '';
$(".goods_type").append("<option value='"+t.id+"' "+select+">"+t.levelstr+t.name+"</option>");
}
$(".goods").append("<option value=''>请选择...</option>");
var type = '<?php echo (int)$_GET['goods_type']; ?>';
for(x in data.data.list){
var t = data.data.list[x];
select = (t.id == '<?php echo (int)$_GET['goods']; ?>') ? 'selected="selected"' : '';
if(type && t.type == type)
$(".goods").append("<option key='"+t.unit+"' title='"+(t.price/100)+"' value='"+t.id+"' "+select+">"+t.name+'('+t.model+')'+"</option>");
}
}else{
alert("查询失败，请重试！");
}
});

$(".goods_type").change(function(){
var obj = $(this);
ajax("/index.php/Goods/index/type/"+$(this).val(), {type:$(this).val()}, 'post', function(data){
if(data.status){
obj.nextAll(".goods").html("");
obj.nextAll(".goods").append("<option key='' value=''>请选择...</option>");
for(x in data.data.list){
var t = data.data.list[x];
obj.next(".goods").append("<option key='"+t.unit+"' title='"+(t.price/100)+"' value='"+t.id+"'>"+t.name+'('+t.model+')'+"</option>");
}
}else{
alert("查询失败，请重试！");
}
});
});

$(".goods").change(function(){
$(this).siblings(".unit").html($(this).children("option:selected").attr("key"));
$(this).siblings(".cprice").val($(this).children("option:selected").attr("title"));
$(this).siblings(".goods_name").val($(this).children("option:selected").html());
});
});
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