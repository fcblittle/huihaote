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
<h3><?php echo ($tname); ?>查看</h3>

<form action="/index.php/Porder/auditSwap" method="post">
<table cellpadding="2" cellspacing="1" border="0" style="width:700px;">
<tr>
<th colspan="2">基本信息</th>
</tr>
<tr>
<td width="90">单 号</td>
<td class="tf"><?php echo ($num); ?></td>
</tr>
<tr>
<td width="90">类 型</td>
<td class="tf"><?php echo ($sort); ?></td>
</tr>
<tr>
<td>供应商</td>
<td class="tf">
<?php if(($abs)  ==  "1"): ?><?php if(isset($can_do['Supplier']['view'])): ?><a href="/index.php/Supplier/view/id/<?php echo ($cors['id']); ?>"><?php echo ($client['name']); ?></a><?php else: ?><?php echo ($client['name']); ?><?php endif; ?>
<?php else: ?>
<?php if(isset($can_do['Client']['view'])): ?><a href="/index.php/Client/view/id/<?php echo ($cors['id']); ?>"><?php echo ($client['name']); ?></a><?php else: ?><?php echo ($client['name']); ?><?php endif; ?><?php endif; ?>
</td>
</tr>
<!--
<tr>
<td width="90">方 式</td>
<td class="tf">
<?php if(($way)  ==  "1"): ?><?php echo ($bank['name']); ?>(<?php echo ($bank['account']); ?>)
<?php else: ?>
现金<?php endif; ?>
</td>
</tr>
-->
<tr>
<td>货 品</td>
<td class="tf">
<?php if(is_array($goods)): ?><?php $i = 0;?><?php $__LIST__ = $goods?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><div style="padding:5px;">
<?php echo ($vo['name']); ?>(<?php echo ($allgoods[$vo['goods']]['model']); ?>)<br />
<div class="tr" style="color:#999; padding:5px 0px; border-bottom:#CCC dashed 1px;">
数量：<?php echo ($vo['num']); ?><?php echo ($allgoods[$vo['goods']]['unit']); ?>
<?php if(($needprice)  ==  "1"): ?>&nbsp; ┊ &nbsp;总金额：<?php echo (showPrice($vo['total'])); ?>&nbsp; ┊ &nbsp;单价：<?php echo (showPrice($vo['price'])); ?><?php endif; ?>
<?php if(($mname)  ==  "Morder"): ?>&nbsp; ┊ &nbsp;总金额：<?php echo (showPrice($vo['total'])); ?>&nbsp; ┊ &nbsp;单价：<?php echo (showPrice($vo['price'])); ?><?php endif; ?><br />
备注：<?php echo (html($vo['com'])); ?>
</div>
</div><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</td>
</tr>

<tr>
<td>备 注</td>
<td class="tf"><?php echo (text($comment)); ?></td>
</tr>

<?php if(($audit)  >  "0"): ?><tr>
<th colspan="2">审核结果</th>
</tr>
<tr>
<td>货品储存</td>
<td class="tf">
<?php if(is_array($goods)): ?><?php $i = 0;?><?php $__LIST__ = $goods?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><div style="padding:5px; border-bottom:#CCC dashed 1px;">
<?php echo ($vo['name']); ?>&nbsp; ┊ &nbsp;数量：<?php echo ($vo['num']); ?>
<?php if(is_array($vo['group'])): ?><?php $i = 0;?><?php $__LIST__ = $vo['group']?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$so): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><div class="vBox" style="padding:5px 5px; background:#EEE;">
仓 库：<?php echo ($so['group']['name']); ?>&nbsp;
数 量：<?php echo ($so['num']); ?>&nbsp;
<?php if(($so['audit'])  ==  "0"): ?><span style="color:red">未确认</span><?php endif; ?>
</div><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</div><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</td>
</tr>

<?php if(($needprice)  ==  "1"): ?><tr>
<td>财务入账</td>
<td class="tf">
<?php if(($money['audit'])  ==  "0"): ?><span style="color:red"> 未确认</span>
<?php else: ?>
<?php if(($money['notto'])  !=  "0"): ?><br /><span style="color:red">欠 帐：<?php echo (showPrice($money['notto'])); ?></span>
<?php else: ?>
<span style="color:#CCC"> 已结清</span><?php endif; ?><?php endif; ?>
</td>
</tr><?php endif; ?>

<?php else: ?>

<?php if(($audit)  ==  "0"): ?><?php if(isset($can_do[$mname]['auditSwap'])): ?><tr>
<th colspan="2">审核信息</th>
</tr>
<tr>
<td>货品储存</td>
<td class="tf">
<?php if(is_array($goods)): ?><?php $i = 0;?><?php $__LIST__ = $goods?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><div style="padding:5px; border-bottom:#CCC dashed 1px;">
<?php echo ($vo['name']); ?>&nbsp; ┊ &nbsp;数量：<?php echo ($vo['num']); ?>
<input type="hidden" class="goods" value="<?php echo ($vo['goods']); ?>" />
<input type="hidden" class="num" value="<?php echo ($vo['num']); ?>" />

<div class="vBox" style="padding:10px 5px; background:#EEE;">
<select name="group[<?php echo ($vo['goods']); ?>][]">
<?php if(is_array($group)): ?><?php $i = 0;?><?php $__LIST__ = $group?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$go): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($go['gid']); ?>"><?php echo ($go['levelstr']); ?><?php echo ($go['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>&nbsp;
数 量：<input type="text" style="width:50px;" name="num[<?php echo ($vo['goods']); ?>][]" value='<?php echo ($vo["num"]); ?>' />
</div>
</div><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</td>
</tr>

<?php if(($needprice)  ==  "1"): ?><tr>
<td>财务收支</td>
<td class="tf"><input type="text" id="price" name="price" value="<?php echo (showPrice($price)); ?>" /></td>
</tr><?php endif; ?><?php endif; ?>
<?php else: ?>
<tr>
<th colspan="2">审核状态</th>
</tr>
<tr>
<td>状 态</td>
<td>未通过</td>
</tr><?php endif; ?><?php endif; ?>
</table>

<div style="width:500px;">
<div class="f">
<input type="button" class="button" onclick="history.go(-1);" value="返 回" />
</div>

<?php if(($audit)  ==  "0"): ?><?php if(isset($can_do[$mname]['audit'])): ?><div class="r">
<?php if(isset($can_do[$mname]['printOrder'])): ?><!--<input type="button" class="button" onclick="location.href='/index.php/Porder/printOrder/id/<?php echo ($id); ?>'" value="打 印" />-->　　<?php endif; ?>
<input type="hidden" name="id" value="<?php echo ($id); ?>">
<input type="submit" class="button" value="审核通过" />
<input type="button" class="button_red" onclick="location.href='/index.php/Porder/audit/refuse/<?php echo ($id); ?>'" value="审核拒绝" />
</div><?php endif; ?>
<?php else: ?>

<div class="r">
<?php if(($audit)  >  "0"): ?><?php if(isset($can_do[$mname]['printSwap'])): ?><input type="button" class="button" onclick="location.href='/index.php/Porder/printSwap/id/<?php echo ($id); ?>'" value="打 印" /><?php endif; ?><?php endif; ?>
<?php if(($audit)  <  "0"): ?><input type="button" class="button_red" onclick="location.href='/index.php/Porder/edit/id/<?php echo ($id); ?>'" value="修 改" /><?php endif; ?>
</div><?php endif; ?>
</div>
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>

<script type="text/javascript">
var percent = eval('('+'<?php echo ($_percent); ?>'+')');
var total = '<?php echo ($total); ?>';

function getPercent()
{
var sort = $("#sort").val();
if(sort > 0)
{
$("#percent").html(percent['sorder'+sort]);
$("#income").val(parseInt(total, 10)*parseInt(percent['sorder'+sort], 10)/10000);
}
}

function addGroup(obj){
obj = $(obj).parent();
var clone = obj.prev("div").clone();
clone.children("input,select").val('');
obj.before(clone);
}
/*<?php if(($abs)  ==  "1"): ?>*/
//入库数不超过订单
$(".vBox input:text").keyup(function(){
var obj = $(this);
var sum = parseFloat(obj.parent().parent().children(".num").val());
var count = 0;
obj.parent().parent().find("input:text").each(function(i){
count += parseFloat($(this).val());
});
if(count > sum) {
alert("设定数量超过订单总数！");
obj.val(sum - (count - parseFloat($(this).val())));
}
});

/*<?php else: ?>*/
//出库数不超过库存
$(".vBox input:text").keyup(function(){
var obj = $(this);
var group = $(this).prevAll("select:eq(0)").val();
var goods = parseInt(obj.parent().parent().children(".goods").val());
ajax("/index.php/Stock/getsum/", {group:group,goods:goods}, 'get', function(data){
if(data.status == 1){
if(parseFloat(data.data) < parseFloat(obj.val()) ){
alert("设定数量超过库存数目！");
obj.val(parseFloat(data.data));
return;
}
}else{
alert("读取仓库库存数据错误！");
}
});
var sum = parseFloat(obj.parent().parent().children(".num").val());
var count = 0;
obj.parent().parent().find("input:text").each(function(i){
count += parseFloat($(this).val());
});
if(count > sum) {
alert("设定数量超过订单总数！");
obj.val(sum - (count - parseFloat($(this).val())));
return;
}
});

/*<?php endif; ?>*/
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