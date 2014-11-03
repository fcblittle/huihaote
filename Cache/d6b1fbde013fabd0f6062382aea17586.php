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
<h3>库存报警</h3>
<script type="text/javascript" src="/./Tpl/default/Public/JS/datebox.js"></script>

<form action="/index.php/Stock/warn" method="post">
<?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$lo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><table cellpadding="2" cellspacing="1" border="0" style="width:700px;">
<tr>
<th colspan="2">报警信息</th>
</tr>
<tr>
<td width="100">产　品<em>*</em></td>
<td class="tf">
<select class="goods_type" style="width:150px;"></select>
<select name="goods[]" class="goods" id="<?php echo ($lo['goods']); ?>" style="width:300px;"></select>
</td>
</tr>
<tr>
<td>仓　库<em>*</em></td>
<td class="tf">
<select name="group[]">
<?php if(is_array($group)): ?><?php $i = 0;?><?php $__LIST__ = $group?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><?php if(($vo['gid'])  ==  $lo['group']): ?><option value="<?php echo ($vo['gid']); ?>" selected="selected"><?php echo ($vo['levelstr']); ?><?php echo ($vo['name']); ?></option>
<?php else: ?>
<option value="<?php echo ($vo['gid']); ?>"><?php echo ($vo['levelstr']); ?><?php echo ($vo['name']); ?></option><?php endif; ?><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>
</td>
</tr>
<tr>
<td>数　量<em>*</em></td>
<td class="tf"><input type="text" style="width:200px;" name="num[]" value="<?php echo ($lo['num']); ?>" /></td>
</tr>
</table><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>


<table cellpadding="2" cellspacing="1" border="0" style="width:700px;">
<tr>
<th colspan="2">报警信息</th>
</tr>
<tr>
<td width="100">产　品<em>*</em></td>
<td class="tf">
<select class="goods_type" style="width:150px;"></select>
<select name="goods[]" class="goods" style="width:300px;"></select>
</td>
</tr>
<tr>
<td>仓　库<em>*</em></td>
<td class="tf">
<select name="group[]">
<?php if(is_array($group)): ?><?php $i = 0;?><?php $__LIST__ = $group?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo['gid']); ?>"><?php echo ($vo['levelstr']); ?><?php echo ($vo['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>
</td>
</tr>
<tr>
<td>数　量<em>*</em></td>
<td class="tf"><input type="text" style="width:200px;" name="num[]" /></td>
</tr>
<tr id="tempAdd">
<td colspan="2" class="tr"><a href="javascript:addWarn();">添加新报警</a></td>
</tr>
</table>
<input type="submit" class="button" value="保 存" />
<input type="button" class="button" onclick="location.href='/index.php/Stock/index'" value="返 回" />
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>

<script type="text/javascript">
function addWarn(){
var clone = $("#tempAdd").parentsUntil("table").clone();
$("#tempAdd").parentsUntil("table").after( clone );
$("#tempAdd").parentsUntil("table").find('#tempAdd').remove();
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
$(".goods").append("<option  class='"+t.type+"' key='"+t.unit+"' title='"+ p/100 +"'  value='"+t.id+"'>"+t.name+'('+t.model+')'+"</option>");
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

$(".goods_type").change(function(){
var obj = $(this);
ajax("/index.php/Goods/index/service/1/type/"+$(this).val(), {type:$(this).val()}, 'post', function(data){
if(data.status){
var status = '<?php echo ($abs); ?>';
obj.nextAll(".goods").html("");
obj.nextAll(".goods").append("<option key='' value=''>请选择...</option>");
for(x in data.data.list){
var t = data.data.list[x];
var p = status ? t.cost : t.price;
p = p/100;
obj.nextAll(".goods").append("<option key='"+t.unit+"' title='"+p+"' value='"+t.id+"'>"+t.name+'('+t.model+')'+"</option>");
}
}else{
alert("查询失败，请重试！");
}
});
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