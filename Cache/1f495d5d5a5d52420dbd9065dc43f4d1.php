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

<form action="/index.php/Sorder/audit" method="post">
<table cellpadding="2" cellspacing="1" border="0" style="width:700px;">
<tr>
<th colspan="2">基本信息</th>
</tr>
<tr>
<td width="90">单 号</td>
<td class="tf"><?php echo ($num); ?></td>
</tr>
<?php if(($mname)  !=  "Wastage"): ?><tr>
<td><?php if(($abs)  ==  "1"): ?>供应商<?php else: ?>客 户<?php endif; ?></td>
<td class="tf">
<?php if(($abs)  ==  "1"): ?><?php if(isset($can_do['Supplier']['view'])): ?><a href="/index.php/Supplier/view/id/<?php echo ($cors['id']); ?>"><?php echo ($cors['name']); ?></a><?php else: ?><?php echo ($cors['name']); ?><?php endif; ?>
<?php else: ?>
<?php if(isset($can_do['Client']['view'])): ?><a href="/index.php/Client/view/id/<?php echo ($cors['id']); ?>"><?php echo ($cors['name']); ?></a><?php else: ?><?php echo ($cors['name']); ?><?php endif; ?><?php endif; ?>
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
<?php echo ($allgoods[$vo['goods']]['name']); ?>(<?php echo ($allgoods[$vo['goods']]['model']); ?>)<br />
<div class="tr" style="color:#999; padding:5px 0px; border-bottom:#CCC dashed 1px;">
数量：<?php echo ($vo['num']); ?><?php echo ($allgoods[$vo['goods']]['unit']); ?>
<?php if(($needprice)  ==  "1"): ?>&nbsp; ┊ &nbsp;单价：<?php echo (showPrice($vo['price'])); ?>&nbsp; ┊ &nbsp;金额：<?php echo (showPrice($vo['total'])); ?>&nbsp; ┊ &nbsp;税率：<?php echo ($vo['tax']); ?>%&nbsp; ┊ &nbsp;税额：<?php echo (showPrice($vo['tax_total'])); ?><br />
总金额：<?php echo (showPrice($vo['total']+$vo['tax_total'])); ?><?php endif; ?>
<?php if(($mname)  ==  "Morder"): ?>&nbsp; ┊ &nbsp;总金额：<?php echo (showPrice($vo['total'])); ?>&nbsp; ┊ &nbsp;单价：<?php echo (showPrice($vo['price'])); ?><?php endif; ?><br />
备注：<?php echo (html($vo['com'])); ?>
</div>
</div><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</td>
</tr><?php endif; ?>

<?php if(($mname)  ==  "Wastage"): ?><tr >
<td >原材料</td>
<td class="tf">
<?php if(is_array($used)): ?><?php $i = 0;?><?php $__LIST__ = $used?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$oo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><div style="padding:10px 5px; line-height:22px; border-bottom:#666 dashed 1px; ">
<b style="font-size:14px;"><?php echo ($allgoods[$oo['goods']]['name']); ?>（<?php echo ($group[$oo['group']]['name']); ?>）</b><br />
　使用量：<?php echo ($oo['used']); ?> <?php echo ($allgoods[$oo['goods']]['unit']); ?><br />
　作废量：<?php echo ($oo['surplus']); ?> <?php echo ($allgoods[$oo['goods']]['unit']); ?><br />
</div><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</td>
</tr><?php endif; ?>

<?php if(($audit)  >  "0"): ?><tr>
<th colspan="2">审核结果</th>
</tr>
<tr>
<td>发货方式</td>
<td class="tf"><?php echo (text($express)); ?></td>
</tr>
<tr>
<td>发货单号</td>
<td class="tf"><?php echo (text($code)); ?></td>
</tr>
<tr>
<td>备 注</td>
<td class="tf"><?php echo (text($comment)); ?></td>
</tr>
<tr>
<td>货品储存</td>
<td class="tf">
<?php if(is_array($goods)): ?><?php $i = 0;?><?php $__LIST__ = $goods?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><div style="padding:5px; border-bottom:#CCC dashed 1px;">
<?php echo ($allgoods[$vo['goods']]['name']); ?>(<?php echo ($allgoods[$vo['goods']]['model']); ?>)&nbsp; ┊ &nbsp;数量：<?php echo ($vo['num']); ?>
<?php if(is_array($vo['group'])): ?><?php $i = 0;?><?php $__LIST__ = $vo['group']?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$so): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><div class="vBox" style="padding:5px 5px; background:#EEE;">
仓 库：<?php echo ($so['group']['name']); ?>&nbsp;
数 量：<?php echo ($so['num']); ?>&nbsp;
<?php if(($so['audit'])  ==  "0"): ?><span style="color:red">未确认</span><?php endif; ?>
<?php foreach ($coststock[$vo['goods']][$so['group']['gid']]['detail'] as $k => $v) {
echo "<p>"."时间批次：".date('Y-m-d',$v['rtime'])." 数量：".$v['num']."个 成本单价：".showPrice($v['price'])."元</p>";
}?>
</div><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</div><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</td>
</tr>

<?php if(($needprice)  ==  "1"): ?><tr>
<td>财务入账</td>
<td class="tf">
订单金额：<?php echo (showPrice($total+$tax_total)); ?><br />
审核金额：<?php echo (showPrice($money['price'])); ?>
<?php if(($money['audit'])  ==  "0"): ?><span style="color:red"> 未确认</span>
<?php else: ?>
<?php if(($money['notto'])  !=  "0"): ?><br /><span style="color:red">欠 帐：<?php echo (showPrice($money['notto'])); ?></span>
<?php else: ?>
<span style="color:#CCC"> 已结清</span><?php endif; ?><?php endif; ?>
</td>
</tr><?php endif; ?>

<?php else: ?>

<?php if(($audit)  ==  "0"): ?><?php if(isset($can_do[$mname]['audit'])): ?><tr>
<th colspan="2">审核信息</th>
</tr>
<tr>
<td>发货方式</td>
<td style="text-align:left;">
<select name="express">
<option value="自取">自取</option>
<option value="发车">发车</option>
<option value="增益 ">增益 </option>
<option value="韵达">韵达</option>
<option value="顺丰">顺丰</option>
<option value="德邦">德邦</option>
<option value="博达">博达</option>
<option value="华宇">华宇</option>
<option value="申通">申通</option>
<option value="中通">中通</option>
<option value="圆通">圆通</option>
<option value="天天EMS">天天EMS</option>
<option value="联邦">联邦</option>
<option value="DHL">DHL</option>
<option value="快捷">快捷</option>
</select>
</td>
</tr>
<tr>
<td>发货单号</td>
<td class="tf"><input name="code" style="width:100px;" value=""></td>
</tr>
<tr>
<td>备 注</td>
<td class="tf">
<textarea name="comment" style="width:400px; height:60px;"><?php echo (text($comment)); ?></textarea>
</td>
</tr>
<tr>
<td>货品储存</td>
<td class="tf">
<?php foreach($goods as $k=>$vo){?>
<div style="padding:5px; border-bottom:#CCC dashed 1px;">
<?php echo ($allgoods[$vo['goods']]['name']); ?>(<?php echo ($allgoods[$vo['goods']]['model']); ?>)&nbsp; ┊ &nbsp;数量：<?php echo ($vo['num']); ?>
<input type="hidden" class="goods" value="<?php echo ($vo['goods']); ?>" />
<input type="hidden" class="num" value="<?php echo ($vo['num']); ?>" />
<div class="vBox" style="padding:10px 5px; background:#EEE;">
仓 库：
<select name="group[<?php echo ($k); ?>][]">
<?php if(is_array($group)): ?><?php $i = 0;?><?php $__LIST__ = $group?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$go): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($go['gid']); ?>"><?php echo ($go['levelstr']); ?><?php echo ($go['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>&nbsp;
数 量：<input type="text" style="width:50px;" name="num[<?php echo ($k); ?>][]" value='<?php echo ($vo["num"]); ?>' />
</div>
<div class="tr" style="padding:5px;"><a href="javascript:vol();" onclick="addGroup(this)">+添加仓库分配</a></div>
</div>
<?php } ?>
</td>
</tr>
<?php if(($needprice)  ==  "1"): ?><tr>
<td>财务收支</td>
<td class="tf"><input type="text" id="price" name="price" value="<?php echo showPrice($total+$tax_total); ?>" /></td>
</tr>
<?php if($mname == 'Sorder' || $mname == 'Service'){?>
<tr>
<td>类型</td>
<td class="tf">
<select name="sort" onchange="getPercent()" id="sort">
<option value="0">请选择</option>
<?php if(is_array($percent)): ?><?php $i = 0;?><?php $__LIST__ = $percent?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo['id']); ?>" title="<?php echo ($vo['value']); ?>" <?php if(($sort)  ==  $vo['id']): ?>selected='selected'<?php endif; ?>><?php echo ($vo['intro']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>
</td>
</tr>
<tr>
<td>提成</td>
<td class="tf">
<input type="text" name="income" id="income" value="<?php echo (showPrice($income)); ?>" style="width:100px;" />
比率：<span id="percent"></span>%
</td>
</tr>
<tr>
<td>负责人</td>
<td class="tf">
<?php echo ($response['name']); ?>
</td>
</tr>
<?php } ?><?php endif; ?><?php endif; ?>
<?php else: ?>
<tr>
<th colspan="2">审核状态</th>
</tr>
<tr>
<td>发货方式</td>
<td class="tf"><?php echo (text($express)); ?></td>
</tr>
<tr>
<td>发货单号</td>
<td class="tf"><?php echo (text($code)); ?></td>
</tr>
<tr>
<td>备 注</td>
<td class="tf"><?php echo (text($comment)); ?></td>
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
<input type="hidden" name="id" value="<?php echo ($id); ?>">
<input type="submit" class="button" value="审核通过" />
<input type="button" class="button_red" onclick="location.href='/index.php/Sorder/audit/refuse/<?php echo ($id); ?>'" value="审核拒绝" />
</div><?php endif; ?>
<?php else: ?>

<div class="r">
<?php if($audit > 0): ?><?php if(isset($can_do[$mname]['printOrder'])): ?><input type="button" class="button" onclick="location.href='/index.php/Sorder/printOrder/id/<?php echo ($id); ?>'" value="打 印" />　<?php endif; ?><?php endif; ?>
<?php if($audit < 0): ?><input type="button" class="button_red" onclick="location.href='/index.php/Sorder/edit/id/<?php echo ($id); ?>'" value="修 改" /><?php endif; ?>
</div><?php endif; ?>
</div>
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>

<script type="text/javascript">
var total = '<?php echo ($total+$tax_total); ?>';

function getPercent()
{
var sort = $("#sort").val();
var percent = $("#sort option:selected").attr('title');
if(sort > 0)
{
$("#percent").html(percent);
$("#income").val(parseInt(total, 10)*parseInt(percent, 10)/10000);
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