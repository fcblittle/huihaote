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
<h3>仓库管理<span>双击可查看仓库详细信息</span></h3>
<script type="text/javascript" src="/./Tpl/default/Public/JS/tree.js"></script>
<table cellpadding="2" cellspacing="0" border="0">
<tr class="white">
<td>
<ul id="treebox">
<?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><li class="level<?php echo ($vo['level']); ?>" id="id<?php echo ($vo['gid']); ?>">
<span class="txt"><?php echo (html($vo['name'])); ?><?php if(($vo['lock'])  !=  "0"): ?><input type="hidden" /><?php endif; ?></span>
<span class="cbox">
<?php if(isset($can_do['Group']['add'])): ?><a class="but add" href="javascript:add(<?php echo ($vo['gid']); ?>);">&nbsp;</a><?php endif; ?>
<?php if(($vo['lock'])  ==  "0"): ?><?php if(isset($can_do['Group']['delete'])): ?><a class="but del" href="javascript:del(<?php echo ($vo['gid']); ?>);">&nbsp;</a><?php endif; ?><?php endif; ?>
</span>
</li><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</ul>
</td>
</tr>
</table>
<script type="text/javascript">
function vol(){return;}
var stclose;
function closecon(obj){$(".cbox").hide();};
$.fn.Ptree.mouseover = function(obj){$(".cbox").hide(); clearTimeout(stclose); $(obj).find(".cbox").show();};
$.fn.Ptree.mouseout = function(obj){stclose = setTimeout(closecon,500);};
$.fn.Ptree.ondblclick = function(obj){
obj = $(obj);
var id = parseInt(obj.closest("li").attr("id").slice(2));
location.href='/index.php/Group/index/id/'+id;
};
/*<?php if(isset($can_do['Group']['edit'])): ?>*/
//edit line
$.fn.Ptree.onclick = function(obj){
obj = $(obj);
if(obj.find("input").get()!="") return;
var txt = $.trim(obj.text());
obj.html('<input class="linput" value="'+txt+'" />');
obj.find("input").focus();
var id = parseInt(obj.closest("li").attr("id").slice(2));
if(!id) return;
//add input blur function
obj.find("input").blur(function(){
if( $(this).val() !="" && $(this).val() != txt){
save(this,id,txt,obj);
}else{
obj.text(txt);
}
});
obj.find("input").keydown(function(event){if(event.which==13) save(this,id);});
}
/*<?php endif; ?>*/
/*<?php if(isset($can_do['Group']['add'])): ?>*/
function add(id){
var obj = $("#treebox").addLine({obj:("#id"+id),cla:"linput"});
var fun = function(o){
if( $(o).val() == ""){
$.fn.Ptree.deleteBox(obj);
}else{
save(o,'',id,obj);
}
}
obj.find("input").focus().blur(function(){fun(this)});
obj.find("input").keydown(function(event){if(event.which==13){fun(this)}});
}
/*<?php endif; ?>*/
/*<?php if(isset($can_do['Group']['delete'])): ?>*/
function del(id){
if(window.confirm("确认删除该部门项么？")){
ajax("/index.php/Group/delete", {id:id}, 'post', function(data){
if(data.status==1 && data.data=="true"){$("#treebox").delLine("#id"+id);}else{alert("错误提示:"+data.data);}
});
}
}
/*<?php endif; ?>*/
//save li
function save(obj,id,parent,other){
var chi = $(obj);
var txt = chi.val();
//ajax get
if(id){
/*<?php if(isset($can_do['Group']['edit'])): ?>*/
var url = "/index.php/Group/edit";
var data = {id:id,val:txt};
var fun = function(data){
if(data.status==1 && data.info==""){
chi.parent().text(txt);
return;
}else{
if(data.info) alert("错误提示："+data.info)
else if(data.data) alert("错误提示："+data.data);
else alert("修改失败，请刷新重试");
other.text(parent);
return;
}};
/*<?php endif; ?>*/
}else{
/*<?php if(isset($can_do['Group']['add'])): ?>*/
var url = "/index.php/Group/add";
var data = {val:txt,parent:parent};
var fun = function(data){
if(data.status==1 && data.info==""){
var o = chi.closest(".txt");
o.text(txt);
o.parent().attr("id","id"+data.data);
var html = '<a class="but add" href="javascript:add('+data.data+');">&nbsp;</a>';
/*<?php if(isset($can_do['Group']['delete'])): ?>*/
html += '<a class="but del" href="javascript:del('+data.data+');">&nbsp;</a>';
/*<?php endif; ?>*/
o.parent().children(".cbox").html(html);
o.parent().mouseover(function(){$.fn.Ptree.mouseover(this)});
o.parent().mouseout(function(){$.fn.Ptree.mouseout(this)});
o.click(function(){$.fn.Ptree.onclick(this);});
return;
}else{
$.fn.Ptree.deleteBox(other);
if(data.info) alert("错误提示："+data.info);
else if(data.data) alert("错误提示："+data.data);
else alert("修改失败，请刷新重试");
return;
}
};
/*<?php endif; ?>*/
}
ajax(url, data, "post", fun);
}
$("#treebox").Ptree();
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