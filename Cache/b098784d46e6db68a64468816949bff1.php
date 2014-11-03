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
<script type="text/javascript" src="/./Tpl/default/Public/JS/datebox.js"></script>
<h3>售后统计列表</h3>

<form id="Search" action="/index.php/Service/statistics" method="get" style="background:#FFF; padding:10px; margin-top:20px; width:100%;">
姓 名:  <select name="search" id="search">
<option <?php if(($search)  ==  "0"): ?>selected="selected"<?php endif; ?> value="0">全部</option>
<?php if(is_array($serviceman)): ?><?php $i = 0;?><?php $__LIST__ = $serviceman?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo['id']); ?>" <?php if(($search)  ==  $vo['id']): ?>selected="selected"<?php endif; ?>><?php echo ($vo['intro']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>　　
客 户：
<select  id="searchselect" style="width:150px;">
<?php if(is_array($DB_type)): ?><?php $i = 0;?><?php $__LIST__ = $DB_type?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo['id']); ?>"><?php echo ($vo['levelstr']); ?><?php echo ($vo['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>
<select id="showlist" name="cors" style="width:250px;">
<option value="">请选择...</option>
<?php if(is_array($cors)): ?><?php $i = 0;?><?php $__LIST__ = $cors?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$to): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($to['id']); ?>" <?php if(($_cors)  ==  $to['id']): ?>selected="selected"<?php endif; ?>><?php echo ($to['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>

时 间:  <input name="start" id="start" style="width:100px;" type="input" value="<?php echo ($start); ?>" onclick="showdatebox(this,'')" readonly="readonly" />
至 <input name="end" id="end" style="width:100px;" type="input" value="<?php echo ($end); ?>" onclick="showdatebox(this,'')" readonly="readonly" />
<input class="button" type="submit" value="搜 索" />
<input type="button" class="button" onclick="printStatistics();" value="打 印" />
<input type="button" class="button" onclick="printStatistics('export');" value="导 出" />
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>

<table cellpadding="2" cellspacing="1" border="0" class="needhover">
<tr>
<th>单 号</th>
<th><?php if(($abs)  ==  "1"): ?>供应商<?php else: ?>客 户<?php endif; ?></th>
<th width="100">金额</th>
<th width="100">税率</th>
<th width="120">维修人</th>
<th width="100">提成</th>
<th width="120">申请时间</th>
<th width="80">状 态</th>
<th width="170">操 作</th>
</tr>
<?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
<td><?php echo (is_array($vo)?$vo["num"]:$vo->num); ?></td>
<td><?php echo ($vo['cors']['name']); ?></td>
<td><?php echo (showPrice($vo['total']+$vo['tax_total'])); ?></td>
<td><?php echo ($vo['tax']); ?>%
</td>
<td><?php echo ($vo['sorts']['intro']); ?></td>
<td><?php echo (showPrice($vo['income2'])); ?></td>
<td><?php echo (date('Y-m-d　H:i',is_array($vo)?$vo["time"]:$vo->time)); ?></td>
<td>
<?php if(($vo['over'])  ==  "0"): ?><span style="color:red">未结</span>
<?php else: ?>
<span style="color:#CCC">完结</span><?php endif; ?>
</td>
<td>
<?php if(isset($can_do[$mname]['view'])): ?>&nbsp;┊&nbsp;<a href="/index.php/Service/view/id/<?php echo (is_array($vo)?$vo["id"]:$vo->id); ?>">查 看</a><?php endif; ?>
<!--
<?php if(isset($can_do[$mname]['edit'])): ?>&nbsp;┊&nbsp;<a href="/index.php/Service/edit/id/<?php echo (is_array($vo)?$vo["id"]:$vo->id); ?>">修 改</a><?php endif; ?>
<?php if(isset($can_do[$mname]['delete'])): ?>&nbsp;┊&nbsp;<a href="/index.php/Service/delete/id/<?php echo (is_array($vo)?$vo["id"]:$vo->id); ?>">删 除</a><?php endif; ?>-->
&nbsp;┊&nbsp;
</td>
</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
<tr class="white page">
<td colspan="5" class="tl">金额总计：<?php echo (showPrice($total+$taxTotal)); ?>&nbsp;&nbsp;&nbsp;&nbsp;提成总计：<?php echo (showPrice($income)); ?>&nbsp;&nbsp;&nbsp;&nbsp;含税维修总计：<?php echo (showPrice($havetax)); ?></td>
<td colspan="4" class="tr"><?php echo ($page); ?></td>
</tr>
</table>

<script type="text/javascript">
function printStatistics(ac = null)
{
var search = $("#search").val();
search = search ? '/search/'+search : '';

var start = $("#start").val();
start = start ? '/start/'+start : '';

var end = $("#end").val();
end = end ? '/end/'+end : '';

var cors = $("#showlist").val();
cors = cors ? '/cors/'+cors : '';
var ac = ac;
ac = ac ? '/ac/'+ac : '';
location.href= '/index.php/Service/printStatistics' + search + start + end + cors + ac;
}

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