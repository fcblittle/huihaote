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
<h3>拆分列表</h3>
<table cellpadding="2" cellspacing="1" border="0" class="needhover">
<tr>
<th>单 号</th>
<th>备注</th>
<th width="80">申请人</th>
<th width="120">申请时间</th>
<th width="120">状态</th>
<th width="170">操 作</th>
</tr>
<?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
<td><?php echo (is_array($vo)?$vo["num"]:$vo->num); ?></td>
<td><?php echo (is_array($vo)?$vo["comment"]:$vo->comment); ?></td>
<td><?php echo ($vo['user']['name']); ?></td>
<td><?php echo (date('Y-m-d　H:i',is_array($vo)?$vo["time"]:$vo->time)); ?></td>
<td><?php if(($vo['audit'])  ==  "0"): ?>未审核<?php else: ?>已审核<?php endif; ?></td>
<td>
<?php if(isset($can_do[$mname]['view'])): ?>&nbsp;┊&nbsp;<a href="/index.php/Split/view/id/<?php echo (is_array($vo)?$vo["id"]:$vo->id); ?>">查 看</a><?php endif; ?>
<?php if(isset($can_do[$mname]['edit'])): ?>&nbsp;┊&nbsp;<a href="/index.php/Split/edit/id/<?php echo (is_array($vo)?$vo["id"]:$vo->id); ?>">修 改</a><?php endif; ?>
<?php if(isset($can_do[$mname]['delete'])): ?>&nbsp;┊&nbsp;<a href="/index.php/Split/delete/id/<?php echo (is_array($vo)?$vo["id"]:$vo->id); ?>">删 除</a><?php endif; ?>
&nbsp;┊&nbsp;
</td>
</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
<tr class="white page">
<td colspan="6" class="tr"><?php echo ($page); ?></td>
</tr>
</table>
<?php if(isset($can_do[$mname]['add'])): ?><input type="button" class="button" onclick="location.href='/index.php/Split/add/do/1'" value="添 加" /> &nbsp;<?php endif; ?>
<?php if(!isset($search)): ?><input class="button" type="button" onclick="openSearch()" value="搜 索" />
<?php else: ?>
<input class="button" onclick="location.href='/index.php/Split/<?php if(isset($url)): ?><?php echo ($url); ?><?php else: ?>index<?php endif; ?>'" type="button" value="返 回" /><?php endif; ?>

<form id="Search" action="/index.php/Split/index" method="get" style="border:#CCC solid 1px; background:#FFF; padding:10px; margin-top:20px; width:300px; display:none;">
<label style="display:block; height:30px;">单　号：<input name="num" style="width:200px;" type="input" /></label>
<?php if(($abs)  ==  "1"): ?>供应商<?php else: ?>客　户<?php endif; ?>：
<select id="searchselect">
<?php if(is_array($DB_type)): ?><?php $i = 0;?><?php $__LIST__ = $DB_type?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo['id']); ?>"><?php echo ($vo['levelstr']); ?><?php echo ($vo['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>

<select name="cors" id="showlist">
<option value="">请选择...</option>
<?php if(is_array($cors)): ?><?php $i = 0;?><?php $__LIST__ = $cors?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo['id']); ?>"><?php echo ($vo['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>
<input class="button" name="search" type="submit" value="搜 索" />
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
<script type="text/javascript">
function openSearch(){
$("#Search").show();
}
$(document).ready(function(){
$("#searchselect").change(function(){
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