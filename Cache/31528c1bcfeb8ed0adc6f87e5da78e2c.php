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
<script type="text/javascript" src="/./Tpl/default/Public/JS/datebox.js"></script>

<form action="<?php if(($url)  ==  "audit"): ?>/index.php/Sorder/audit/edit/<?php echo ($id); ?>/url/<?php echo ($url); ?><?php else: ?>/index.php/Sorder/edit/url/<?php echo ($url); ?><?php endif; ?>" method="post">
<table cellpadding="2" cellspacing="1" border="0" style="width:700px;">
<tr>
<th colspan="2">填写信息</th>
</tr>
<tr>
<td width="90">单 号</td>
<td class="tf"><input type="text" style="width:250px;" name="num" value="<?php echo ($num); ?>" /></td>
</tr>
<tr>
<td>税 率</td>
<td class="tf"><input type="text" style="width:100px;" name="tax" id="tax" value="<?php echo ($tax); ?>" onkeyup="changeTax()" />%</td>
</tr>
<tr>
<td width="90">时 间</td>
<td class="tf"><input type="text" style="width:250px;" name="time" value="<?php echo (date('Y-m-d H:i',$time)); ?>" /></td>
</tr>
<?php if($mname=='Sorder' || $mname=='Porder'){ ?>
<tr>
<td>人员</td>
<td class="tf">
<select name="sale">
<?php if(is_array($allUsers)): ?><?php $i = 0;?><?php $__LIST__ = $allUsers?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo['uid']); ?>" <?php if(($sale)  ==  $vo['uid']): ?>selected="selected"<?php endif; ?>><?php echo ($vo['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>
<!--<input type="text" style="width:150px;" name="sale" value="<?php echo (substr($sale, 1, -1)); ?>" />-->
</td>
</tr>
<tr>
<td>发票提醒</td>
<td class="tf">
<input type="text" style="width:120px;" name="bill" value="<?php if($bill): ?><?php echo (date('Y-m-d H:i', $bill)); ?><?php endif; ?>" />
格式：2012-09-09 14:25，不需要为空
</td>
</tr>
<tr>
<td>余款提醒</td>
<td class="tf">
<input type="text" style="width:120px;" name="spare" value="<?php if($spare): ?><?php echo (date('Y-m-d H:i', $spare)); ?><?php endif; ?>" />
格式：2012-09-09 14:25，不需要为空
</td>
</tr>
<!--
<tr>
<td>已付/收款</td>
<td class="tf">
<input type="text" style="width:80px;" name="havemoney" value="<?php echo (showPrice($havemoney)); ?>" />
</td>
</tr>

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
<?php } ?>
<?php if(($mname)  !=  "Wastage"): ?><tr>
<td><?php if(($abs)  ==  "1"): ?>供应商<?php else: ?>客 户<?php endif; ?></td>
<td class="tf">
<select id="cors">
<?php if(is_array($_type)): ?><?php $i = 0;?><?php $__LIST__ = $_type?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo['id']); ?>"><?php echo ($vo['levelstr']); ?><?php echo ($vo['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>
<select name="cors" id="showlist">
<option value="">请选择...</option>
<?php if(is_array($corss)): ?><?php $i = 0;?><?php $__LIST__ = $corss?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$cco): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><?php if(($cco['id'])  ==  $cors): ?><option value="<?php echo ($cco['id']); ?>" title="<?php echo ($cco['type']); ?>" selected="selected"><?php echo ($cco['name']); ?></option>
<?php else: ?>
<option value="<?php echo ($cco['id']); ?>"><?php echo ($cco['name']); ?></option><?php endif; ?><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>
<?php if(($abs)  ==  "1"): ?><?php if(isset($can_do['Supplier']['add'])): ?><a href="/index.php/Supplier/add/return/<?php echo ($mname); ?>">+添加供应商</a><?php endif; ?>
<?php else: ?>
<?php if(isset($can_do['Client']['add'])): ?><a href="/index.php/Client/add/return/<?php echo ($mname); ?>">+添加客户</a><?php endif; ?><?php endif; ?>
</td>
</tr>
<tr>
<td>货 品</td>
<td class="tf">
<?php if(is_array($goods)): ?><?php $i = 0;?><?php $__LIST__ = $goods?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><div class="products" style="padding:10px 5px; border-bottom:#666 dashed 1px;">
货品类型：<select class="goods_type" style="width:150px;"></select>
货品：<select name="goods[]" class="goods" id="<?php echo ($vo['goods']); ?>" style="width:300px;"></select>
<br /><br />
　　单 价：<input autocomplete='off' class="price" type="text" style="width:80px;" name="goods_price[]" onkeyup="<?php if(($needprice)  ==  "1"): ?>sumPrice(this)<?php endif; ?>" value="<?php echo ($vo['price']/100); ?>" /> 元　 　
　　数 量：<input autocomplete='off' type="text" class="num" style="width:50px;" name="goods_num[]" onkeyup="<?php if(($needprice)  ==  "1"): ?>sumPrice(this)<?php endif; ?>" value="<?php echo ($vo['num']); ?>" /> <span class="unit"><?php echo ($allgoods[$vo['goods']]['unit']); ?></span>
<br /><br />
　　税 额：<input autocomplete='off' type="text" class="tax_total" style="width:80px;" name="tax_total[]" value="<?php echo (showPrice($vo['tax_total'])); ?>" /> 元
<?php if(($needprice)  ==  "1"): ?>　　总金额：<input autocomplete='off' type="text" class="all_total" style="width:80px;" name="total[]" value="<?php echo (showPrice($vo['total']+$vo['tax_total'])); ?>" /> 元<?php endif; ?>
<br /><br />

备 注：<input autocomplete='off' type="text" style="width:300px;" name="goods_com[]" value="<?php echo ($vo['com']); ?>" />
</div><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
<div id="tempAdd" class="tr" style="padding:5px;"><a href="javascript:addGoods()">+添加货品</a></div>
</td>
</tr>
<tr>
<td>总 价</td>
<td class="tf" id="allTotal"><?php echo ($total+$tax_total)/100; ?>　元</td>
</tr><?php endif; ?>

<?php if(($mname)  ==  "Wastage"): ?><tr >
<td >原材料</td>
<td class="tf">
<?php if(is_array($used)): ?><?php $i = 0;?><?php $__LIST__ = $used?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$so): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><div style="padding:10px 5px; border-bottom:#666 dashed 1px;">
来 源：
<select name="material[group][]" style="width:100px;">
<?php if(is_array($group)): ?><?php $i = 0;?><?php $__LIST__ = $group?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option <?php if(($vo['gid'])  ==  $so['group']): ?>selected="selected"<?php endif; ?> value="<?php echo ($vo['gid']); ?>"><?php echo ($vo['levelstr']); ?><?php echo ($vo['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>
原 料：
<select class="goods" name="material[goods][]" id="<?php echo ($so['goods']); ?>" style="width:200px;"></select>
<br /><br />
使用量：
<input type="text" style="width:50px;" name="material[used][]" value="<?php echo ($so['used']); ?>" />
<span class="unit"><?php echo ($allgoods[$so["goods"]]['unit']); ?></span>　
作废量：
<input type="text" style="width:50px;" name="material[surplus][]" value="<?php echo ($so['surplus']); ?>" />
<span class="unit"><?php echo ($allgoods[$so["goods"]]['unit']); ?></span>
</div><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
<div id="tempAdd2" class="tr" style="padding:5px;"><a href="javascript:addMaterial()">+添加原材料</a></div>
</td>
</tr><?php endif; ?>

<tr>
<td>备 注</td>
<td class="tf"><textarea name="comment" style="width:500px; height:150px;"><?php echo ($comment); ?></textarea></td>
</tr>
</table>
<input type="hidden" name="id" value="<?php echo ($id); ?>" />
<input type="hidden" name="p" value="<?php echo ($p); ?>" />
<input type="submit" class="button" value="保 存" />
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
<script type="text/javascript">
new nicEditors.allTextAreas({buttonList : ['fontFormat','fontSize','bold','italic','underline','forecolor','image','upload'], uploadURI : '/index.php/Public/upload/'});
function addGoods(){
var clone = $("#tempAdd").prev("div").html();
//clone.children("input,select").val('');
//clone.children(".unit").html('');
$("#tempAdd").before('<div style="padding:10px 5px; border-bottom:#666 dashed 1px;">'+clone+"</div>");

var obj = $("#tempAdd").prev("div");
obj.find("input").each(function(){
$(this).val('');
});
obj.find("option[selected='selected']").each(function(){
$(this).removeAttr('selected');
});
}
function addMaterial(){
var clone = $("#tempAdd2").prev("div").clone();
clone.children("input,select").val('');
clone.children(".unit").html('');
$("#tempAdd2").before(clone);
}
function sumPrice(obj){
//数量
var num = parseInt($(obj).parents().find(".num").val(), 10);
if(!num)
num = 0;
//含税价格
var price = $(obj).parents().find(".price").val();
if(!price){
return '';
}

//总价
var total = Math.round(num * price * 100000000)/100000000;
$(obj).parent().find(".all_total").val(total);

//税率
var tax = parseFloat($("#tax").val(), 10);
if(tax>0)
{
var noTaxPrice = total/(1+tax/100);
var tax_total = parseInt(Math.round(noTaxPrice*tax), 10)/100;
$(obj).parent().find(".tax_total").val(tax_total);
}

//订单总价
var allTotal = 0;
$(".all_total").each(function(i){
var _total = parseInt($(this).val());
allTotal += _total;
}   );
$("#allTotal").html(allTotal+'　元');
}

function changeTax()
{
$(".products").each(function(i){
sumPrice(this);
});
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
$(".goods").append("<option class='"+t.type+"' key='"+t.unit+"' title='"+(p/100)+"' value='"+t.id+"'>"+t.name+'('+t.model+')'+"</option>");
}
$(".goods").each(function(){
var opt = $(this).find("option[value='"+$(this).attr("id")+"']");
opt.attr("selected", true);
var type = opt.attr("class");
$(this).find("option[class!='"+type+"']").remove();
$(this).prev(".goods_type").find("option[value='"+type+"']").attr("selected", true);
})
}else{
alert("查询失败，请重试！");
}
});

$(".goods_type").live('change', function(){
var obj = $(this);
ajax("/index.php/Goods/index/type/"+$(this).val(), {type:$(this).val()}, 'post', function(data){
if(data.status){
var status = '<?php echo ($abs); ?>';
obj.nextAll(".goods").html("");
obj.nextAll(".goods").append("<option key='' value=''>请选择...</option>");
for(x in data.data.list){
var t = data.data.list[x];
var p = status ? t.cost : t.price;
obj.nextAll(".goods").append("<option key='"+t.unit+"' title='"+(p/100)+"' value='"+t.id+"'>"+t.name+'('+t.model+')'+"</option>");
}
}else{
alert("查询失败，请重试！");
}
});
});

$(".goods").change(function(){
$(this).siblings(".unit").html($(this).children("option:selected").attr("key"));
//$(this).siblings(".price").html("单价"+$(this).children("option:selected").attr("title"));
$(this).siblings(".goods_name").val($(this).children("option:selected").html());
sumPrice( $(this).siblings(".num").get() );
});

$("#cors").change(function(){
$("#showlist option").remove();
var type = $(this).val();
if("<?php echo ($types); ?>"==1)
types = "/index.php/Supplier/index";
else
types = "/index.php/Client/index";
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
alert("查询失败！");
}
},
'json'
);

});

var corsv = $("#showlist option:selected").attr("title");
$("#cors option[value='"+corsv+"']").attr("selected", true);
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