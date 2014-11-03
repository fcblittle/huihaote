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
<h3><?php echo ($tname); ?>记录<span><?php if(isset($can_do['Stock'][$t])): ?>(点击未确认状态可进行确认审核)<?php endif; ?></span></h3>

<script type="text/javascript" src="/./Tpl/default/Public/JS/datebox.js"></script>

<form action="/index.php/Stock/<?php echo ($act); ?>/" style="margin-bottom:10px;" >
<select name="gid">
<?php if(is_array($group)): ?><?php $i = 0;?><?php $__LIST__ = $group?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo['gid']); ?>" <?php if(($vo['gid'])  ==  $gid): ?>selected="selected"<?php endif; ?> ><?php echo ($vo['levelstr']); ?> <?php echo ($vo['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>
货物类型：<select name="goods_type" class="goods_type"></select>
货 品：<select name="goods" class="goods"></select>
日 期：<input type="text" style="width:100px;" name="stime" onclick="showdatebox(this,'')" /> --
<input type="text" style="width:100px;" name="etime" onclick="showdatebox(this,'')" />
<input type="submit" value="查 询" />
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>

<table cellpadding="2" cellspacing="1" border="0" class="needhover">
<tr>
<th width="200">备注</th>
<th>产品名称</th>
<th width="100">出入库时间</th>
<th width="100">价 值</th>
<th width="100">应收/付</th>
<th width="80">登 记 人</th>
<th width="50">状 态</th>
<th width="180">操 作</th>
</tr>
<?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
<td><?php echo ($vo['comment']); ?></td>
<td>
<span>
<?php if(isset($can_do['Goods']['view'])): ?><a href="/index.php/Goods/view/id/<?php echo (is_array($vo)?$vo["goods"]:$vo->goods); ?>"><?php echo (is_array($vo)?$vo["goods_name"]:$vo->goods_name); ?></a>
<?php else: ?>
<?php echo (is_array($vo)?$vo["goods_name"]:$vo->goods_name); ?><?php endif; ?>
(<?php echo ($vo['good']['model']); ?>)
</span>
</td>
<td><?php echo (date('Y-m-d',is_array($vo)?$vo["time"]:$vo->time)); ?></td>
<td><?php echo (showPrice(is_array($vo)?$vo["price"]:$vo->price)); ?></td>
<td><?php $owe = abs($vo['price'])-abs($vo['money']); echo ($owe>=0 ? '' : '-').showPrice($owe); ?></td>
<td>
<?php if(isset($can_do['User']['view'])): ?><a href="/index.php/User/view/id/<?php echo (is_array($vo)?$vo["uid"]:$vo->uid); ?>"><?php echo ($vo['user']['name']); ?></a>
<?php else: ?>
<?php echo ($vo['user']['name']); ?><?php endif; ?>
</td>
<td width="50">
<?php if((is_array($vo)?$vo["audit"]:$vo->audit)  ==  "0"): ?><?php if(isset($can_do['Stock']['audit'])): ?><a style="color:red" href="javascript:audit(<?php echo (is_array($vo)?$vo["id"]:$vo->id); ?>)">未确认</a>
<?php else: ?>
<span style="color:red">未确认</span><?php endif; ?>
<?php else: ?>
<span style="color:#CCC">已确认</span><?php endif; ?>
</td>
<td>
<?php if(($can_manage)  ==  "1"): ?><!--present name="can_do['Stock']['viewPrice']">&nbsp;┊&nbsp;<a href="/index.php/Stock/viewPrice/id/<?php echo (is_array($vo)?$vo["id"]:$vo->id); ?>">查 看</a></present-->
<?php if(isset($can_do['Stock']['editPrice'])): ?>&nbsp;┊&nbsp;<a href="/index.php/Stock/editPrice/id/<?php echo (is_array($vo)?$vo["id"]:$vo->id); ?>">修 改</a><?php endif; ?>
<?php if(isset($can_do['Stock']['delPrice'])): ?>&nbsp;┊&nbsp;<a href="/index.php/Stock/delPrice/id/<?php echo (is_array($vo)?$vo["id"]:$vo->id); ?>">删 除</a><?php endif; ?>
&nbsp;┊&nbsp;<?php endif; ?>
</td>
</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
<tr class="white page">
<td colspan="8" class="tr"><?php echo ($page); ?></td>
</tr>
</table>
<?php if(isset($can_do['Stock']['add'])): ?><input type="button" class="button" onclick="location.href='/index.php/Stock/add/'" value="添 加" /><?php endif; ?>&nbsp;&nbsp;
<!---<?php if(isset($can_do['Stock']['audit'])): ?><input type="button" class="button" onclick="location.href='/index.php/Stock/audit/'" value="全部审核" /><?php endif; ?>-->
<script type="text/javascript">
function audit(id){
if(window.confirm("该记录货品已按数量进/出仓库，是否确认？")){
location.href='/index.php/Stock/audit/id/'+id;
}
}
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
for(x in data.data.list){
var t = data.data.list[x];
select = (t.id == '<?php echo (int)$_GET['goods']; ?>') ? 'selected="selected"' : '';
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
obj.nextAll(".goods").append("<option key='"+t.unit+"' title='"+(t.price/100)+"' value='"+t.id+"'>"+t.name+'('+t.model+')'+"</option>");
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