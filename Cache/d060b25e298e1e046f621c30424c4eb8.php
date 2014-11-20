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
<h3><?php echo ($tname); ?>修改</h3>

<form action="/index.php/Porder/editSwap" method="post" enctype="multipart/form-data">
<table cellpadding="2" cellspacing="1" border="0" style="width:700px;">
<tr>
<th colspan="2">填写信息</th>
</tr>
<tr>
<td>类型</td>
<td class="tf">
<select name="sort">
<option value="换货" <?php if(($sort)  ==  "换货"): ?>selected="selected"<?php endif; ?>>换货</option>
<option value="退货" <?php if(($sort)  ==  "退货"): ?>selected="selected"<?php endif; ?>>退货</option>
</select>
</td>
</tr>
<tr>
<td width="90">单 号</td>
<td class="tf" style="position:relative;">
xsck-<input type="text" id="num" style="width:180px;"  autocomplete="off" onkeyup="getOrders(this)" name="num" value="<?php echo (substr($num, 5)); ?>" />
<style>
ul, li {list-style:none outside none; padding:0px; vertical-align:top}
.orders {border:1px solid #7F9DB9; position:absolute; z-index:100; background:#FFFFFF; width:200px; left:158px; top:150px;}
.orders a{margin-top:5px; border-top:1px solid #FFF; border-bottom:1px dashed #CCC; height:20px; color:#0055AA; display:block; line-height:20px; text-indent:10px;}
.orders a:hover{border-top:1px solid #0055AA; border-bottom:1px solid #0055AA; background:#68adf3; font-weight:blod;}
</style>
<ul class="orders" style="display:none"></ul>
</td>
</tr>
<!--
<tr>
<td>方 式<em>*</em></td>
<td class="tf">
<select id="way" name="way" onchange="showBank()">
<option value="0" <?php if(($way)  ==  "0"): ?>selected="selected"<?php endif; ?>>现金</option>
<option value="1" <?php if(($way)  ==  "1"): ?>selected="selected"<?php endif; ?>>银行</option>
</select>
</td>
</tr>
<tr id="bank" style="display:none;">
<td>银 行</td>
<td class="tf">
<select name="bank">
<option>-请选择-</option>
<?php if(is_array($banks)): ?><?php $i = 0;?><?php $__LIST__ = $banks?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo['id']); ?>" <?php if(($bank)  ==  $vo['id']): ?>selected<?php endif; ?>><?php echo ($vo['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>
</td>
</tr>
-->
<tr>
<td>仓　库</td>
<td class="tf">
<select name="group">
<?php if(is_array($group)): ?><?php $i = 0;?><?php $__LIST__ = $group?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo['gid']); ?>" <?php if(($vo['gid'])  ==  $gid): ?>selected="selected"<?php endif; ?> ><?php echo ($vo['levelstr']); ?><?php echo ($vo['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>
</td>
</tr>

<tr>
<td>销售时间</td>
<td class="tf"><input type="text" style="width:250px;" name="time" id="time" readonly /></td>
</tr>

<tr>
<td>客 户</td>
<td class="tf">
<input type="text" style="width:120px;" name="cors" id="cors" readonly />
</td>
</tr>
<tr>
<td>货 品</td>
<td class="tf" id="goods">请填写订单号，先！ </td>
</tr>

<tr>
<td>备 注</td>
<td class="tf"><textarea name="comment" style="width:500px; height:150px;"><?php echo ($comment); ?></textarea></td>
</tr>
</table>
<input type="hidden" name="id" value="<?php echo ($id); ?>" />
<input type="submit" class="button" value="保 存" />
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>

<script type="text/javascript">
new nicEditors.allTextAreas({buttonList : ['fontFormat','fontSize','bold','italic','underline','forecolor','image','upload'], uploadURI : '/index.php/Public/upload/'})

function getOrders(obj)
{
var order = $(obj).val();

if(order === undefined){
return '';
}

//查询
$.post("/index.php/Porder/swap/do/getOrders",
{num:'xsck-'+order},
function(data){
data = eval('('+data+')');
$(".orders").html('');
var html = '';
if(data){
for(var i in data){
html = html + '<li><a onmousedown="chooseOrder(this)" href="javascript:;">'+data[i]['num'].substr(5)+'</a></li>';
}
}
$(".orders").html(html);
$(".orders").show();
}
);
}

var num = $("#num").val();
chooseOrder();
function chooseOrder(obj)
{
if(!num){
var order = $(obj).html();
$(".orders").hide();
$("#num").val(order);

$.post("/index.php/Porder/swap/do/getProducts",
{num:'xsck-'+order, id:id},
function(data){
data = eval('('+data+')');
$('#time').val(data['time']);
$('#cors').val(data['corsInfo']['name']);

//货品
var html = '';
for(var i in data['goods'])
{
html = html + '<div style="padding:10px 5px; border-bottom:#666 dashed 1px;">';
html = html + '名称：' + data['goods'][i]['info']['name']+'('+data['goods'][i]['info']['model']+')'+'　　<input type="hidden" name="goods[]" value="'+data['goods'][i]['goods']+'" />共 <span style="font-weight:800;">'+data['goods'][i]['num']+'</span> 个<br />'+
'数量：<input type="text" style="width:60px; height:14px; font-size:12px;" name="good_num[]" value="" /><br />'+
'备注：<input type="text" style=" height:14px; font-size:12px;" name="good_com[]" value="" /><br />'+
'<input type="hidden" name="good_price[]" value="'+data['goods'][i]['price']+'" />'+
'<input type="hidden" name="good_cost[]" value="'+data['goods'][i]['cost']+'" />'+
'<input type="hidden" name="good_name[]" value="'+data['goods'][i]['info']['name']+'" />';
html = html + '</div>'
}

$("#goods").html(html);
}
);
}else{
var order = num;
num = '';

$('#time').val('<?php echo ($time); ?>');
$('#cors').val("<?php echo ($cors['name']); ?>");

//货品
var data = eval('('+'<?php echo ($goods); ?>'+')');
var html = '';
for(var i in data)
{
html = html + '<div style="padding:10px 5px; border-bottom:#666 dashed 1px;">';
html = html + '名称：' + data[i]['name']+'　　<input type="hidden" name="goods[]" value="'+data[i]['goods']+'" />共 <span style="font-weight:800;">'+data[i]['num']+'</span> 个<br />'+
'数量：<input type="text" style="width:60px; height:14px; font-size:12px;" name="good_num[]" value="'+data[i]['num']+'" /><br />'+
'备注：<input type="text" style=" height:14px; font-size:12px;" name="good_com[]" value="'+data[i]['com']+'" /><br />'+
'<input type="hidden" name="good_price[]" value="'+data[i]['price']+'" />'+
'<input type="hidden" name="good_cost[]" value="'+data[i]['cost']+'" />'+
'<input type="hidden" name="good_name[]" value="'+data[i]['name']+'" />';
html = html + '</div>'
}

$("#goods").html(html);
}


}

function addGoods(){
var clone = $("#tempAdd").prev("div").clone();
clone.children("input,select").val('');
clone.children(".unit").html('');
$("#tempAdd").before(clone);
}
function addMaterial(){
var clone = $("#tempAdd2").prev("div").clone();
clone.children("input,select").val('');
clone.children(".unit").html('');
$("#tempAdd2").before(clone);
}
function sumPrice(obj){
var num = parseInt($(obj).val(), 10); if(!num) return;
var price = $(obj).siblings(".goods").children("option:selected").attr("title");
var total = Math.round(num * price * 100)/100;
$(obj).siblings(".total").val(total);
}
$(document).ready(function(){
ajax("/index.php/Goods/index", {type:1}, 'post', function(data){
if(data.status){
for(x in data.data.type){
var t = data.data.type[x];
$(".goods_type").append("<option value='"+t.id+"'>"+t.levelstr+t.name+"</option>");
}
$(".goods").append("<option value=''>请选择...</option>");
var status = '<?php echo ($abs); ?>';
for(x in data.data.list){
var t = data.data.list[x];
var p = status ? t.cost : t.price;
p = p/100;
$(".goods").append("<option key='"+t.unit+"' title='"+ p +"' value='"+t.id+"'>"+t.name+"</option>");
}
}else{
alert("查询失败，请重试！");
}
});

$(".goods_type").change(function(){
var obj = $(this);
ajax("/index.php/Goods/index/type/"+$(this).val(), {type:$(this).val()}, 'post', function(data){
if(data.status){
var status = '<?php echo ($abs); ?>';
obj.nextAll(".goods").html("");
obj.nextAll(".goods").append("<option key='' value=''>请选择...</option>");
for(x in data.data.list){
var t = data.data.list[x];
var p = status ? t.cost : t.price;
p = p/100;
obj.nextAll(".goods").append("<option key='"+t.unit+"' title='"+p+"' value='"+t.id+"'>"+t.name+"</option>");
}
}else{
alert("查询失败，请重试！");
}
});
});

$(".goods").change(function(){
$(this).siblings(".unit").html($(this).children("option:selected").attr("key"));
$(this).siblings(".price").val($(this).children("option:selected").attr("title"));
$(this).siblings(".goods_name").val($(this).children("option:selected").html());
sumPrice( $(this).siblings(".num").get() );
});

$("#cors").change(function(){
$("#showlist option").remove();
var type = $(this).val();
if("<?php echo ($types); ?>" ==1)
var types = "/index.php/Supplier/index";
else
var types = "/index.php/Client/index";
ajax(types,
{type:type},
'get',
function(data){
if(data.status){
if(false ==data.data) return;
$("#showlist").append("<option key='' value=''>请选择...</option>");
for(x in data.data){
var d = data.data[x];
$("#showlist").append("<option  value='"+d.id+"'>"+d.name+"</option>");
};
}else{
alert("121");
}
},
'json'
);
});

});

function showBank()
{
var way = $("#way").val();
if(way > 0){
$("#bank").show();
}else{
$("#bank").hide();
}
}

showBank();
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