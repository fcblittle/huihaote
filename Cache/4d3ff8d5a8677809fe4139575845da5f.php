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
<h3>库管人员设置</h3>
<script type="text/javascript" src="/./Tpl/default/Public/JS/tree.js"></script>
<style type="text/css">
#userBox{ vertical-align:top; border-left:#CCC solid 1px; text-align:left;}
#userBox span{ display:inline-block; margin-top:5px; padding:2px 5px 2px 10px; border:#666 solid 1px; background:#EEE; margin-left:5px; color:#333;}
#userBox span a{font-size:12px; font-family:Tahoma,Arial,sans-serif; color:#333; display:inline-block; width:14px; height:14px;  text-align:center; vertical-align:top; margin-left:5px;}
#userBox span a:hover{color:#000; text-decoration:none;}
#userBox .glay{ border:#999999 solid 1px; background:#fafafa; margin-left:5px; color:#999999;}
#userBox .glay a{color:#999999;}
#userBox .glay a:hover{color:#666666;}
</style>
<table cellpadding="2" cellspacing="0" border="0">
<tr class="white">
<td style="vertical-align:top;">
<select id="treebox" multiple="multiple" style="width:250px; height:300px;">
<?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo['gid']); ?>"><?php echo ($vo['levelstr']); ?> <?php echo ($vo['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>
</td>
<td width="80%" id="userBox">
</td>
</tr>
<tr>
<td colspan="2" class="tr" style=" border-top:#CCC solid 1px;">添加：
<select id="Select">
<option value="0">ID</option>
<option value="1" selected="selected">名称</option>
<option value="2">编号</option>
</select>
<input id="searchVal" type="text" value="" />
<input type="button" class="button" style="margin-top:0px;" onclick="searchUser()" value="搜 素" />
</td>
</tr>
</table>
<script type="text/javascript">
function addMem(good){
if(!$("#treebox").children(":selected").length) {
alert("请选择部门后操作！");
return;
}
var group = $("#treebox").children(":selected").val();
ajax('/index.php/Group/goods', {type:'add',good:good,group:group}, 'post', function(data){
if(data.status == 1){
if(data.data){
var obj = $("#usr_"+good);
$("#userBox").append("<span id='mem_"+good+"' title='"+obj.attr("title")+"'>"+obj.text()+"<a title='删除' href='javascript:delMem("+good+")'>x</a></span>");
closeWin();
}else{
alert("添加失败，请重试~");
}
}else{
alert(data.info);
}
});
}

function delMem(good){
if(!$("#treebox").children(":selected").length) {
alert("请选择部门后操作！");
return;
}
var group = $("#treebox").children(":selected").val();
ajax('/index.php/Group/goods', {type:'del',good:good,group:group}, 'post', function(data){
if(data.status == 1){
if(data.data){
$("#userBox").find("#mem_"+good).remove();
}else{
alert("添加失败，请重试~");
}
}else{
alert(data.info);
}
});
}

function searchUser(){
if(!$("#treebox").children(":selected").length) {
alert("请选择部门后操作！");
return;
}
var type = $("#Select").val();
var val = $("#searchVal").val();
if(!val) alert("请输入搜索内容！");
var post = '';
switch(type){
case '2':
post = 'num/'+encodeURI(val);
break;
case '1':
post = 'name/'+encodeURI(val);
break;
default:
post = 'id/'+encodeURI(val);
}
ajax("/index.php/Goods/index/search/1/"+post, {search:1}, 'post', function(data){
if(data.status == 1){
var html = '';
for(x in data.data.list){
var d = data.data.list[x];
html += '<a id="usr_'+d.id+'" title="编号：'+d.num+'" href="javascript:addMem('+d.id+')">'+d.name+'</a>&nbsp;'
}
win('请选择搜索用户结果', html);
}else{
alert("数据错误，请重试！");
}
});
}

$("#treebox").change(function(){
if($(this).children(":selected").length > 1){
$(this).children(":selected").attr("selected", false);
alert('不能使用多选部门');
return;
}
var id = $(this).children(":selected").val();
ajax("/index.php/Group/goods", {id:id}, 'get', function(data){
if(data.status == 1){
$("#userBox").html("");
for(x in data.data){
var blue = '';
var glay = '';
if(data.data[x].group == 1){
blue += "<span id='mem_"+data.data[x].id+"' title='编号："+data.data[x].num+"'>"+data.data[x].name+"<a title='删除' href='javascript:delMem("+data.data[x].id+")'>x</a></span>";
}else{
glay += "<span id='mem_"+data.data[x].id+"' class='glay' title='编号："+data.data[x].num+"'>"+data.data[x].name+"<a title='删除' href='javascript:delMem("+data.data[x].id+")'>x</a></span>";
}

$("#userBox").append(blue+glay);
}
}else{
alert("查询失败，请重试！");
}
});
});
$("form :checkbox").click(function(){ if($(this).attr("checked")) $(this).closest('tr').prev().find("input:hidden").val(1); });
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