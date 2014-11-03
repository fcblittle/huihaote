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
<div id="ajaxmsg"></div>
<h3>系统节点<span>(该操作为高级系统设置，请注意使用)</span></h3>
<form action="/index.php/System/node" method="post">
<table cellpadding="2" cellspacing="1" border="0" class="needhover" id="listbox">
<tr>
<th>节点名称</th>
<th width="200">Action路径</th>
<th width="100">菜单显示</th>
<th width="200">操 作</th>
<th width="100">排 序</th>
</tr>
<?php if(is_array($list)): ?><?php $vk = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$vk;?><?php $mod = (($vk % 2 )==0)?><tr id="list_<?php echo ($vo['id']); ?>" class="parent">
<td colspan="3" class="tf bgblue" onclick="showChild(this);" title="点击打开"><b><?php echo ($vo['name']); ?></b></td>
<td class="bgblue"><a href="javascript:add(<?php echo ($vo['id']); ?>);">添加</a> ┊ <a href="javascript:edit(<?php echo ($vo['id']); ?>)">修改</a> ┊ <a href="javascript:del(<?php echo ($vo['id']); ?>)">删除</a></td>
<td class="bgblue"><input type="text" name="m_sort[<?php echo ($vo['id']); ?>]" style="width:20px; font-size:10px;" value="<?php echo ($vk); ?>" /></td>
</tr>
<?php if(is_array($vo['child'])): ?><?php $sk = 0;?><?php $__LIST__ = $vo['child']?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$so): ?><?php ++$sk;?><?php $mod = (($sk % 2 )==0)?><tr id="list_<?php echo ($so['id']); ?>" class="child" style="display:none;">
<td><?php echo ($so['name']); ?></td>
<td><?php echo ($so['m']); ?>/<?php echo ($so['a']); ?><?php if(($so['arg'])  !=  ""): ?>(<?php echo ($so['arg']); ?>)<?php endif; ?></td>
<td><?php if(($so['hide'])  ==  "1"): ?>隐藏<?php else: ?>显示<?php endif; ?></td>
<td><a href="javascript:edit(<?php echo ($so['id']); ?>)">修改</a> ┊ <a href="javascript:del(<?php echo ($so['id']); ?>)">删除</a></td>
<td class="tr"><input type="text" name="a_sort[<?php echo ($vo['id']); ?>][<?php echo ($so['id']); ?>]" style="width:20px; font-size:10px;" value="<?php echo ($sk); ?>" /></td>
</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</table>
<input type="button" class="button" onclick="addline()" value="添 加" />
<input type="submit" class="button" value="保存排序" />
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>

<script type="text/javascript">
function showChild(obj){
$(obj).parent().nextUntil(".parent").toggle();
}
function addline(){
var content = '<label class="big">请输入节点类型名称：<br /><input id="name" type="text" style="width:220px;" value="" /></label>'+
'<label class="big">请输入节点MODULE：<br /><input id="module" type="text" style="width:220px;" value="" /></label>';
var fun = function(){
var name = $("#window #name").val();
var module = $("#window #module").val();
var sort = $("#listbox .parent").length+1;
ajax('/index.php/System/node',{type:'add', module:module, name:name},'post',function(data){
if(data.status==1){
$("#listbox").append('<tr id="list_'+data.data.id+'"><td colspan="3" class="tf bgblue"><b>'+data.data.name+'</b></td><td class="bgblue"><a href="javascript:add('+data.data.id+');">添加</a> ┊ <a href="javascript:edit('+data.data.id+')">修改</a> ┊ <a href="javascript:del('+data.data.id+')">删除</a></td><td class="bgblue"><input type="text" name="m_sort['+data.data.id+']" style="width:20px; font-size:10px;" value="'+sort+'" /></td></tr>');
closeWin();
}else{
alert(data.info);
}
});
}
win('添加新的节点分类', content, fun);
}
function edit(id){
if($("#list_"+id).children().length <= 2){
var name = $("#list_"+id+" td:eq(0)").text();
var content = '<label class="big">请输入节点类型名称：<br /><input id="name" type="text" style="width:220px;" value="'+name+'" /></label>';
}else{
var name = $("#list_"+id+" td:eq(0)").text();
var hide = $("#list_"+id+" td:eq(2)").text() == '显示'?0:1;
if(hide){
var select1 = 'selected="selected"'; var select0 = '';
}else{
var select1 = ''; var select0 = 'selected="selected"';
}
var content = '<label class="big">请输入节点名称：<br /><input id="name" type="text" style="width:220px;" value="'+name+'" /></label>'+
'<label class="big">是否在菜单中显示：<select id="hide"><option '+select1+'value="1">隐藏</option><option '+select0+'value="0">显示</option></select></label>';
}
var fun = function(){
var hiden = $("#window #hide").val();
ajax('/index.php/System/node',{type:'edit', id:id, name:$("#window #name").val(), hide:hiden},'post',function(data){
if(data.status == 1){
$("#list_"+id+" td:eq(0)").text($("#window #name").val());
if($("#list_"+id+" td:eq(2)").html()) $("#list_"+id+" td:eq(2)").text(hiden==1?'隐藏':'显示');
closeWin();
}else{
alert(data.info);
}
});
}
win("修改节点", content, fun);
};
function add(id){
var content = '<label class="big">请输入节点名称：<br /><input id="name" type="text" style="width:220px;" value="" /></label>'+
'<label class="big">请输入节点ACTION：<br /><input id="action" type="text" style="width:220px;" value="" /></label>'+
'<label class="big">是否在菜单中显示：<select id="hide"><option value="1">隐藏</option><option selected="selected" value="0">显示</option></select></label>'; //参数这里应该抓取各个Module下的参数设置
var fun = function(){
var name = $("#window #name").val();
var action = $("#window #action").val();
var hide = $("#window #hide").val();
ajax('/index.php/System/node',{type:'add', action:action, parent:id, hide:hide, name:name},'post',function(data){
if(data.status==1){
var node = $("#list_"+id).nextUntil(".parent").last();
if(!node.attr("id")) node = $("#listbox tr").last();
var sort = node.prevUntil(".parent").length+2;
node.after('<tr id="list_'+data.data.id+'">'+
'<td>'+data.data.name+'</td>'+
'<td>'+data.data.m+'/'+data.data.a+'</td>'+
'<td>'+(data.data.hide==1?'隐藏':'显示')+'</td>'+
'<td><a href="javascript:edit('+data.data.id+')">修改</a> ┊ <a href="javascript:del('+data.data.id+')">删除</a></td>'+
'<td class="tr"><input type="text" name="a_sort['+id+']['+data.data.id+']" style="width:20px; font-size:10px;" value="'+sort+'" /></td>'+
'</tr>');
closeWin();
}else{
alert(data.info);
}
});
}
win('添加新的节点', content, fun);
}
function del(id){
var name = $("#list_"+id+" td:eq(0)").text();
if(window.confirm("确认要删除'"+name+"'节点吗？"))
{
ajax('/index.php/System/node',{type:'del', id:id},'post',function(data){
if(data.status==1){
if($("#list_"+id).children().length > 2){
$("#list_"+id).remove();
}else{
var node = $("#list_"+id).nextAll("tr:has('.bgblue')").first().prev();
if(!node.attr("id")) node = $("#listbox tr").last();
var start = $("#list_"+id).prevAll().length;
var last = node.prevAll().length - start;
$("#list_"+id).nextAll(":lt("+last+")").remove();
$("#list_"+id).remove();
}
}else{
alert(data.info);
}
});
}
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