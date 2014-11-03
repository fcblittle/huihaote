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
<h3>银行调拨列表</h3>
<form action="/index.php/Financial/allot" style="padding-bottom:10px;">
经办人：
<select name="user">
<option value="0">全部</option>
<?php if(is_array($users)): ?><?php $i = 0;?><?php $__LIST__ = $users?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo['uid']); ?>" <?php if(($user)  ==  $vo['uid']): ?>selected="selected"<?php endif; ?>><?php echo ($vo['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>　　
时 间：
<input type="text" style="width:100px;" name="stime" readonly="readonly" onclick="showdatebox(this,'')" value="<?php echo ($stime); ?>" /> --
<input type="text" style="width:100px;" name="etime" readonly="readonly" onclick="showdatebox(this,'')" value="<?php echo ($etime); ?>" />
<input type="submit" value="查 询" />
<!--input type="submit" name="ac" value="导 出" />
<input type="submit" name="ac" value="打 印" /-->　
<?php if(isset($can_do['Financial']['addAllot'])): ?><input type="button" class="button" onclick="location.href='/index.php/Financial/addAllot'" value="添 加" /> &nbsp;<?php endif; ?>
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>

<!--div style="height:30px; line-height:30px; padding-left:20px;">
期初余额: <?php echo (showPrice($begin)); ?>　
收入：<?php echo (showPrice($income)); ?>　
支出：<?php echo (showPrice($pay)); ?>　
阶段总额：
<?php if(($sum)  >  "0"): ?><span style="color:green"><?php echo (showPrice($sum)); ?></span>
<?php else: ?>
<span style="color:red">-<?php echo (showPrice($sum)); ?></span><?php endif; ?>　
结存余额:
<?php if(($total)  >  "0"): ?><span style="color:green"><?php echo (showPrice($total)); ?></span>
<?php else: ?>
<span style="color:red">-<?php echo (showPrice($total)); ?></span><?php endif; ?>
</div-->
<table cellpadding="2" cellspacing="1" border="0" class="needhover">
<tr>
<th width="140">单 号</th>
<th>调出银行</th>
<th>调入银行</th>
<th>调拨金额</th>
<th width="100">日期</th>
<th width="80">经办人</th>
<th width="140">操作</th>
</tr>
<?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
<td><?php echo (is_array($vo)?$vo["num"]:$vo->num); ?></td>
<td>
<?php if(($vo['from'])  >  "0"): ?><?php echo ($banks[$vo['from']]['name']); ?>
<?php else: ?>
库存现金<?php endif; ?>
</td>
<td>
<?php if(($vo['to'])  >  "0"): ?><?php echo ($banks[$vo['to']]['name']); ?>
<?php else: ?>
库存现金<?php endif; ?>
</td>
<td><?php echo (showPrice($vo['money'])); ?></td>
<td><?php echo (date("Y-m-d", $vo['time'])); ?></td>
<td><?php echo ($vo['user']['name']); ?></td>
<td>

<?php if($vo['audit'] > 0): ?><?php if(isset($can_do['Financial']['viewAllot'])): ?><a href="/index.php/Financial/viewAllot/id/<?php echo (is_array($vo)?$vo["id"]:$vo->id); ?>">查 看</a><?php endif; ?>
<?php else: ?>
<?php if(isset($can_do['Financial']['auditAllot'])): ?><a href="/index.php/Financial/auditAllot/id/<?php echo (is_array($vo)?$vo["id"]:$vo->id); ?>">审 核</a>&nbsp;┊&nbsp;<?php endif; ?>
<?php if(isset($can_do['Financial']['editAllot'])): ?><a href="/index.php/Financial/editAllot/id/<?php echo (is_array($vo)?$vo["id"]:$vo->id); ?>">修 改</a>&nbsp;┊&nbsp;<?php endif; ?>
<?php if(isset($can_do['Financial']['delAllot'])): ?><a href="/index.php/Financial/delAllot/id/<?php echo (is_array($vo)?$vo["id"]:$vo->id); ?>">删 除</a><?php endif; ?><?php endif; ?>
</td>
</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
<tr class="white page">
<td colspan="7" class="tr"><?php echo ($page); ?></td>
</tr>
</table>


<!--<?php if(isset($can_do['Financial']['printDaily'])): ?><input type="button" class="button" onclick="openSearch()" value="打 印" /> &nbsp;<?php endif; ?>-->

<script src="/./Tpl/default/Public/JS/datebox.js"></script>
<form id="Search" action="/index.php/Financial/printDaily" method="get" style="border:#CCC solid 1px; background:#FFF; padding:10px; margin-top:20px; width:300px; display:none;">
时 间：
<input type="text" style="width:100px;" name="date1" readonly="readonly" onclick="showdatebox(this,'')" /> -- <input type="text" style="width:100px;" name="date2" readonly="readonly" onclick="showdatebox(this,'')" />
<br /><br />
从第<input type="text" style="width:40px;" name="firstrow" value="0" />行开始,打印<input type="text" style="width:40px;" name="listrow" value="5" />行！
<input type="hidden" name="type" value="daily" />
<br /><br />
<input class="button"  type="submit" value="打 印" />
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>

<script type="text/javascript">
function openSearch(){
$("#Search").show();
}
function audit(id){
if(window.confirm("该款项已经结清，是否确认？\n如有欠款，可编辑修改")){
location.href='/index.php/Financial/audit/id/'+id;
}
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