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
<h3>财务收支列表</h3>

<form id="Search" action="/index.php/Financial/index" method="get" style="background:#FFF; padding:10px; margin-top:20px; width:100%;">
来 源：
<select name="come" id="come">
<option value="1" <?php if(($come)  ==  "1"): ?>selected="selected"<?php endif; ?>>全部</option>
<option value="order2" <?php if(($come)  ==  "order2"): ?>selected="selected"<?php endif; ?>>销售出库</option>
<option value="order1" <?php if(($come)  ==  "order1"): ?>selected="selected"<?php endif; ?>>采购入库</option>
<option value="service" <?php if(($come)  ==  "service"): ?>selected="selected"<?php endif; ?>>维修管理</option>
<option value="daily" <?php if(($come)  ==  "daily"): ?>selected="selected"<?php endif; ?>>日常支出</option>
<option value="worker" <?php if(($come)  ==  "worker"): ?>selected="selected"<?php endif; ?>>员工费用</option>
<option value="return1" <?php if(($come)  ==  "return1"): ?>selected="selected"<?php endif; ?>>采购入库(退货)</option>
<option value="return2" <?php if(($come)  ==  "return2"): ?>selected="selected"<?php endif; ?>>销售出库(退货)</option>
</select>　
状 态:
<select name="status">
<option value="0" <?php if(($status)  ==  "0"): ?>selected="selected"<?php endif; ?>>全部</option>
<option value="1" <?php if(($status)  ==  "1"): ?>selected="selected"<?php endif; ?>>已审核</option>
<option value="-1" <?php if(($status)  ==  "-1"): ?>selected="selected"<?php endif; ?>>未审核</option>
</select>　
发 票：
<select name="bill">
<option value="0" <?php if(($bill)  ==  "0"): ?>selected="selected"<?php endif; ?>>全部</option>
<option value="已开" <?php if(($bill)  ==  "已开"): ?>selected="selected"<?php endif; ?>>已开</option>
<option value="未开" <?php if(($bill)  ==  "未开"): ?>selected="selected"<?php endif; ?>>未开</option>
<option value="不含税" <?php if(($bill)  ==  "不含税"): ?>selected="selected"<?php endif; ?>>不含税</option>
</select>　
时 间：<input name="start" id="start" style="width:100px;" type="input" value="<?php echo (date('Y-m-d',$start)); ?>" onclick="showdatebox(this,'')" readonly="readonly" />
至 <input name="end" id="end" style="width:100px;" type="input" value="<?php echo (date('Y-m-d',$end)); ?>" onclick="showdatebox(this,'')" readonly="readonly" />　
C&S类型：<select name="corstable">
<option>请选择</option>
<option value="client" <?php if(($corstable)  ==  "client"): ?>selected="selected"<?php endif; ?>>客户</option>
<option value="supplier" <?php if(($corstable)  ==  "supplier"): ?>selected="selected"<?php endif; ?>>供应商</option>
</select>　
C&S名称：<input name="corsname" id="corsname" style="width:150px;" type="input" value="<?php echo ($corsname); ?>" /><br />
<input class="button" name="ac" type="submit" value="搜 索" />
<input class="button" name="ac" type="submit" value="导 出" />
<input class="button" name="ac" type="submit" value="打 印" />　
<?php if(isset($can_do['Financial']['add'])): ?><input type="button" class="button" onclick="location.href='/index.php/Financial/add'" value="添 加" /> &nbsp;<?php endif; ?>
<!--a href="">日常收支添加</a>　
<a href="">员工费用添加</a-->
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>

<table cellpadding="2" cellspacing="1" border="0" class="needhover">
<tr>
<th>收支内容</th>
<th>收支金额</th>
<th>提成</th>
<th width="70">做账日期</th>
<th>到账情况</th>
<th>发票</th>
<th>发票编号</th>
<th width="50">做账人</th>
<th>C&S类型</th>
<th>C&S名称</th>
<th width="200">操作</th>
</tr>
<?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
<td><?php echo (text(is_array($vo)?$vo["name"]:$vo->name)); ?></td>
<td>
<?php if((is_array($vo)?$vo["price"]:$vo->price)  >  "0"): ?><span style="color:green"><?php echo showPrice(abs($vo['price'])-abs($vo['notto'])); ?></span>
<?php else: ?>
<span style="color:red">-<?php echo showPrice(abs($vo['price'])-abs($vo['notto'])); ?></span><?php endif; ?>
</td>
<td><?php echo (showPrice(is_array($vo)?$vo["income"]:$vo->income)); ?></td>
<td><?php echo (date('Y-m-d',is_array($vo)?$vo["time"]:$vo->time)); ?></td>
<td>
<?php if((is_array($vo)?$vo["audit"]:$vo->audit)  ==  "0"): ?><span style="color:red">未审核</span>
<?php else: ?>
<?php if((is_array($vo)?$vo["notto"]:$vo->notto)  !=  "0"): ?><span style="color:#000">欠账：<?php echo (showPrice(is_array($vo)?$vo["notto"]:$vo->notto)); ?></span>
<?php else: ?>
<span style="color:#CCC">已<?php if((is_array($vo)?$vo["price"]:$vo->price)  >  "0"): ?>到帐<?php else: ?>付清<?php endif; ?></span><?php endif; ?><?php endif; ?>
</td>
<td><?php echo ($vo['bill']); ?></td>
<td><?php echo ($vo['billnum']); ?></td>
<td>
<?php if(isset($can_do['User']['view'])): ?><a href="/index.php/User/view/id/<?php echo (is_array($vo)?$vo["uid"]:$vo->uid); ?>"><?php echo ($vo['user']['name']); ?></a>
<?php else: ?>
<?php echo ($vo['user']['name']); ?><?php endif; ?>
</td>
<td>
<?php if($vo['corstable']): ?><?php if(($vo['corstable'])  ==  "client"): ?>客户<?php else: ?>供应商<?php endif; ?>
<?php else: ?>
暂无<?php endif; ?>
</td>
<td>
<?php echo ($allCors[$vo['corstable']][$vo['cors']]); ?></td>
<td>
<?php if(isset($can_do['Financial']['view'])): ?>&nbsp;┊&nbsp;<a href="/index.php/Financial/view/id/<?php echo (is_array($vo)?$vo["id"]:$vo->id); ?>">查 看</a><?php endif; ?>
<?php if(isset($can_do['Financial']['edit'])): ?>&nbsp;┊&nbsp;<a href="/index.php/Financial/edit/id/<?php echo (is_array($vo)?$vo["id"]:$vo->id); ?>">修 改</a><?php endif; ?>
<?php if(isset($can_do['Financial']['delete'])): ?>&nbsp;┊&nbsp;<a href="/index.php/Financial/delete/id/<?php echo (is_array($vo)?$vo["id"]:$vo->id); ?>">删 除</a><?php endif; ?>
<?php if((is_array($vo)?$vo["audit"]:$vo->audit)  ==  "0"): ?><?php if(isset($can_do['Financial']['audit'])): ?>&nbsp;┊&nbsp;<a href="javascript:audit(<?php echo (is_array($vo)?$vo["id"]:$vo->id); ?>)">审 核</a><?php endif; ?><?php endif; ?>
&nbsp;┊&nbsp;
</td>
</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
<tr class="white page">
<td colspan="11" class="tr"><?php echo ($page); ?></td>
<script src="/./Tpl/default/Public/JS/datebox.js"></script>
</tr>
</table>

<form id="Search" action="/index.php/Financial/index" method="get" style="border:#CCC solid 1px; background:#FFF; padding:10px; margin-top:20px; width:300px; display:none;">
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
location.href='/index.php/Financial/audit/id/'+id;
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