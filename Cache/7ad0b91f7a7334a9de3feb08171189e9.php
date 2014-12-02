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
<h3>库存报溢报损</h3>
<script type="text/javascript" src="/./Tpl/default/Public/JS/datebox.js"></script>

<form action="/index.php/Stock/report" method="post">
<table id="mytable" cellpadding="2" cellspacing="1" border="0" style="width:500px;">
<tr>
<th colspan="2"><?php if(($type)  ==  "in"): ?>报溢<?php else: ?>报损<?php endif; ?>信息</th>
</tr>
<tr>
<td width="100">产　品<em>*</em></td>
<td class="tf">
<input type="hidden" name="goods_name" id="goods_name" value="<?php echo ($g['name']); ?>" />
<select name="goods" id="goods" class="remain">
<option value="">请选择...</option>
<?php if(is_array($allgoods)): ?><?php $i = 0;?><?php $__LIST__ = $allgoods?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo['id']); ?>" key="<?php echo ($vo['unit']); ?>" title="<?php echo ($vo['price']/100); ?>" <?php if(($vo['id'])  ==  $goods): ?>selected="selected"<?php endif; ?> ><?php echo ($vo['name']); ?>(<?php echo ($vo['model']); ?>)</option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>
</td>
</tr>
<tr>
<td>仓　库<em>*</em></td>
<td class="tf">
<select name="group" id="group" class="remain">
<?php if(is_array($group)): ?><?php $i = 0;?><?php $__LIST__ = $group?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo['gid']); ?>" <?php if(($vo['gid'])  ==  $gid): ?>selected="selected"<?php endif; ?> ><?php echo ($vo['levelstr']); ?><?php echo ($vo['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>
</td>
</tr>
<tr>
<td>报溢报损</td>
<td class="tf">
<select name="type" id="type">
<option value="in">报溢</option>
<option value="de">报损</option>
</select>
</td>
</tr>
<tr>
<td>剩余总数</td>
<td class="tf remaintotal" >0个</td>
</tr>
<tr>
<td>数　量<em>*</em></td>
<td class="tf"><input type="text" style="width:200px;" name="num" onkeyup="changenum(this);" /> <span id="unit"><?php echo ($g['unit']); ?></span></td>
</tr>

<tr id="totalValue">
<td>总 价 值<em>*</em></td>
<td class="tf">
<input type="text" style="width:200px;" id="price" name="price" /> <a href="javascript:showCalculator();">>计算器</a>
<div id="calculator" style="background:#EEE; padding:10px; margin:10px; display:none;">
<input type="text" id="cprice" style="width:50px;" value="<?php echo ($g['price']/100); ?>" />（单价） * <input id="cnum" type="text" readonly="readonly" style="width:50px;" />（数量） <input type="button" value="计算" onclick="calculator()" />
</div>
</td>
</tr>

</table>
<!--<input type="hidden" name="type" value="<?php echo ($type); ?>" />-->
<input type="submit" class="button" value="保 存" />
<input type="button" class="button" onclick="location.href='/index.php/Stock/statistics'" value="返 回" />
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>

<script type="text/javascript">
$(document).ready(function(){
var goods = $("#goods").val();
var group = $("#group").val();
ajax("/index.php/Stock/remain", {goods:goods,group:group}, 'post', function(data){
if(data.status){
$(".remaintotal").text(data.data+"个");
}else{
alert("查询失败，请重试！");
}
});  

$("#goods").change(function(){
$("#unit").html($(this).children("option:selected").attr("key"));
$("#cprice").val($(this).children("option:selected").attr("title"));
$("#goods_name").val($(this).children("option:selected").html());
});
$('#type').change(function(){
if($("#type").val()=='de') {
$('#totalValue').hide();
} else {
$("#totalValue").show();
}
});
$(".remain").change(function(){
var goods = $("#goods").val();
var group = $("#group").val();
ajax("/index.php/Stock/remain", {goods:goods,group:group}, 'post', function(data){
if(data.status){
$(".remaintotal").text(data.data+"个");
}else{
alert("查询失败，请重试！");
}
});  
});
});
function showCalculator(){
$("#calculator").toggle();
}
function changenum(obj){
$("#cnum").val($(obj).val());
}

function calculator(){
if(isNaN(parseFloat($("#cnum").val())) || isNaN(parseFloat($("#cprice").val()))){
alert("单价与数量必须为数字！");
return;
}
$("#price").val(parseFloat($("#cnum").val()) * parseFloat($("#cprice").val()));
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