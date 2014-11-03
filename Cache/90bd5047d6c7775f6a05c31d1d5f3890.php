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
<h3>固定资产列表</h3>

<div style="height:30px; line-height:30px; padding-left:20px;">
资产原值: <?php echo (showPrice($exist)); ?>　
累计折旧金额： <span style="color:red">-<?php echo (showPrice($old)); ?></span>　
变卖金额： <?php echo (showPrice($sell)); ?>　
资产现值：<span <?php if(($now)  >  "0"): ?>style="color:green"<?php else: ?>style="color:red"<?php endif; ?>><?php if(($now)  <  "0"): ?>-<?php endif; ?><?php echo (showPrice($now)); ?></span>
</div>

<table cellpadding="2" cellspacing="1" border="0" class="needhover">
<tr>
<th>收支内容</th>
<th width="80">数量</th>
<th>收支金额</th>
<th width="100">日期</th>
<th width="100">经办人</th>
<th width="250">操作</th>
</tr>
<?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
<td><?php echo (text(is_array($vo)?$vo["title"]:$vo->title)); ?></td>
<td><?php echo (is_array($vo)?$vo["num"]:$vo->num); ?></td>
<td>
<?php if((is_array($vo)?$vo["type"]:$vo->type)  !=  "old"): ?><?php echo (showPrice(is_array($vo)?$vo["price"]:$vo->price)); ?>
<?php else: ?>
<span style="color:red">-<?php echo (showPrice(is_array($vo)?$vo["price"]:$vo->price)); ?></span><?php endif; ?>
</td>
<td><?php echo (date('Y-m-d',is_array($vo)?$vo["time"]:$vo->time)); ?></td>
<td><?php echo ($vo['user']); ?></td>
<td>
<?php if(isset($can_do['Fixed']['edit'])): ?><a href="/index.php/Fixed/edit/id/<?php echo (is_array($vo)?$vo["id"]:$vo->id); ?>">修 改</a><?php endif; ?>
<?php if(isset($can_do['Fixed']['delete'])): ?>&nbsp;┊&nbsp;<a href="/index.php/Fixed/delete/id/<?php echo (is_array($vo)?$vo["id"]:$vo->id); ?>">删 除</a><?php endif; ?>
</td>
</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
<tr class="white page">
<td colspan="7" class="tr"><?php echo ($page); ?></td>
<script src="/./Tpl/default/Public/JS/datebox.js"></script>
</tr>
</table>

<?php if(isset($can_do['Fixed']['add'])): ?><input type="button" class="button" onclick="location.href='/index.php/Fixed/add/from/1'" value="添 加" /> &nbsp;<?php endif; ?>
<input type="button" class="button" onclick="location.href='/index.php/Fixed/low/ac/export'" value="导 出" />
<!--
<?php if(!isset($search)): ?><input class="button" type="button" onclick="openSearch()" value="搜 索" />
<?php else: ?>
<input class="button" onclick="location.href='/index.php/Fixed/index'" type="button" value="返 回" /><?php endif; ?>
-->

<form id="Search" action="/index.php/Fixed/index" method="get" style="border:#CCC solid 1px; background:#FFF; padding:10px; margin-top:20px; width:300px; display:none;">
时 间：
<input type="text" style="width:100px;" name="date1" readonly="readonly" onclick="showdatebox(this,'')" /> -- <input type="text" style="width:100px;" name="date2" readonly="readonly" onclick="showdatebox(this,'')" />
<br /><br />
内 容：
<select name="name" id="selectName" style="width:100px;">
<option value="采购入库">采购入库</option>
<option value="销售出库">销售出库</option>
<option value="other">其他</option>
</select>
<input id="nameOther" name="other" style="width:100px; margin-top:5px; display:none;" type="input" /><br />
<input class="button" name="search" type="submit" value="搜 索" />
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
<script type="text/javascript">
function openSearch(){
$("#Search").show();
}
function audit(id){
//if(window.confirm("该款项已经结清，是否确认？\n如有欠款，可编辑修改")){
location.href='/index.php/Fixed/audit/id/'+id;
//}
}
$("#selectName").change(function(){if($(this).val() == 'other'){$("#nameOther").show();}else{$("#nameOther").hide();} });
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