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
<h3>角色赋权</h3>
<script type="text/javascript" src="/./Tpl/default/Public/JS/tree.js"></script>
<script language=javascript>
function unselectall(obj){
if(!$(obj).attr("checked")){
$(obj).parents("tr").prev("tr").find(":checkbox").attr("checked", false);
}
}
function CheckAll(obj, id){
var type = $(obj).attr("checked");
$("#box_"+id).find("input:checkbox").attr("checked", type);
}
</script>
<table cellpadding="2" cellspacing="0" border="0">
<tr class="white">
<td style="vertical-align:top;">
<select id="treebox" multiple="multiple" style="width:250px; height:300px;">
<?php if(is_array($role)): ?><?php $i = 0;?><?php $__LIST__ = $role?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo['id']); ?>"><?php echo ($vo['levelstr']); ?> <?php echo ($vo['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>
</td>
<td style="vertical-align:top;">
<form  name="myform" action="/index.php/Role/node" method="post">
<input id="role" type="hidden" name="role" value="" />
<table cellpadding="2" cellspacing="0" border="0" style="font-size:14px;">
<?php if(is_array($node)): ?><?php $k = 0;?><?php $__LIST__ = $node?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$no): ?><?php ++$k;?><?php $mod = (($k % 2 )==0)?><tr>
<th class="tf"><input type="hidden" name="node[<?php echo ($no['id']); ?>]" value="0" /><?php echo ($no['name']); ?><input type='checkbox' onclick='CheckAll(this,<?php echo ($k); ?>)' /></th>
</tr>
<tr id="box_<?php echo ($k); ?>">
<td>
<?php if(is_array($no['child'])): ?><?php $i = 0;?><?php $__LIST__ = $no['child']?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$so): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><label style="width:20%; padding-left:5%; float:left; text-align:left;">
<input type="checkbox" onclick="unselectall(this)" name="node[<?php echo ($so['id']); ?>]" value="1" /> <?php echo ($so['name']); ?>
</label><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</td>
</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
<tr>

<td class="tr">
<input type="submit" class="button" value="保 存" />
<input type="button" onclick="allchecked()" class="button" value="全 选" />
</td>
</tr>
</table>
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
</td>
</tr>
</table>
<script type="text/javascript">
function allchecked(){
$("form :checkbox").attr("checked", "checked");
$("form th input:hidden").val(1);
}
$("#treebox").change(function(){
if($(this).children(":selected").length > 1){
$(this).children(":selected").attr("selected", false);
alert('不能使用多选角色');
return;
}
var id = $(this).children(":selected").val();
ajax("/index.php/Role/node", {id:id}, 'post', function(data){
if(data.status == 1){
$(":checkbox").removeAttr("checked");
$("th input:hidden").val(0);
$("#role").val(id);
for(x in data.data){
$(":checkbox[name='node["+data.data[x]['node']+"]']").attr("checked","checked");
$(":checkbox[name='node["+data.data[x]['node']+"]']").closest('tr').prev().find("input:hidden").val(1);
}
}else{
alert(data.info);
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