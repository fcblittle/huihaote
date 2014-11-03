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
<h3><?php echo ($tname); ?>修改</h3>
<script type="text/javascript" src="/./Tpl/default/Public/JS/datebox.js"></script>

<form action="/index.php/Financial/editDaily" method="post" enctype="multipart/form-data">
<table cellpadding="2" cellspacing="1" border="0" style="width:600px;">
<tr>
<th colspan="2">基本数据</th>
</tr>
<tr>
<td>单 号<em>*</em></td>
<td class="tf"><input type="text" style="width:250px;" name="num" value="<?php echo ($num); ?>" /></td>
</tr>
<tr>
<td>日 期</td>
<td class="tf"><input type="text" style="width:150px;" name="time" readonly="readonly" value="<?php echo (date('Y-m-d', $time)); ?>" onclick="showdatebox(this,'')" /></td>
</tr>
<!--
<tr>
<td>方 式<em>*</em></td>
<td class="tf">
<select id="way" name="way" onchange="showBank()">
<option value="0" <?php if(($way)  ==  "0"): ?>selected="selected"<?php endif; ?>>现金</option>
<option value="1" <?php if(($way)  ==  "1"): ?>selected="selected"<?php endif; ?>>银行</option>
</select>
</td>
</tr>
<tr id="bank" style="display:none;">
<td>银 行</td>
<td class="tf">
<select name="bank">
<option>-请选择-</option>
<?php if(is_array($banks)): ?><?php $i = 0;?><?php $__LIST__ = $banks?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo['id']); ?>" <?php if(($bank)  ==  $vo['id']): ?>selected<?php endif; ?>><?php echo ($vo['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>
</td>
</tr>
-->
<tr>
<td>员工/经办人</td>
<td class="tf">
<select name="user">
<?php if(is_array($users)): ?><?php $i = 0;?><?php $__LIST__ = $users?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo['uid']); ?>" <?php if(($user)  ==  $vo['uid']): ?>selected="selected"<?php endif; ?>><?php echo ($vo['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>
</td>
</tr>
<tr>
<td>日 期</td>
<td class="tf"><input type="text" style="width:150px;" name="time" readonly="readonly" value="<?php echo (date('Y-m-d', $time)); ?>" onclick="showdatebox(this,'')" /></td>
</tr>
<tr>
<td colspan="2">
<table cellpadding="0" cellspacing="0" border="0">
<?php foreach ($price as $k=>$v){?>
<tr>
<td style="border-right:0px; text-align:left; line-height:30px;">
科目:
<select name="subject[]" style="width:250px;">
<option value="0">-请选择-</option>
<?php if(is_array($subjects)): ?><?php $i = 0;?><?php $__LIST__ = $subjects?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo['id']); ?>" title="<?php echo ($vo['name']); ?>" <?php if(($subject[$k])  ==  $vo['id']): ?>selected="selected"<?php endif; ?>><?php echo ($vo['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>　
<br /><br />
收支:
<select name="income[]" style="width:50px;">
<option value="1" <?php if(($income[$k])  ==  "1"): ?>selected="selected"<?php endif; ?>>收入</option>
<option value="2" <?php if(($income[$k])  ==  "2"): ?>selected="selected"<?php endif; ?>>支出</option>
</select>　
金额:<input type="text" style="width:70px;" name="price[]" value="<?php if($price[$k] < 0): ?>-<?php endif; ?><?php echo (showPrice($price[$k])); ?>" />　
摘要:<input name="comment[]" style="width:175px;" value="<?php echo ($comment[$k]); ?>" />
</td>
</tr>
<?php } ?>
<tr>
<td style="border:0px; text-align:left;"><a href="#" onclick="addOne(this)">再添加一个？</a></td>
</tr>
</table>
</td>
</tr>
</table>
<input type="hidden" name="id" value="<?php echo ($id); ?>" />
<input type="hidden" name="type" value="<?php echo ($type); ?>" />
<input type="submit" class="button" value="保 存" />
<input type="button" class="button" onclick="history.go(-1);" value="返 回" />
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>

<script type="text/javascript">
function showBank()
{
var way = $("#way").val();
if(way > 0){
$("#bank").show();
}else{
$("#bank").hide();
}
}

showBank();

function addOne(obj)
{
var clone = $(obj).parent().parent().prev().clone();
$(obj).parent().parent().before(clone);
$(obj).parent().parent().prev().find('input').each(function(x){
$(this).val('');
});
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