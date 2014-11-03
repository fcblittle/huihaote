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
<h3><?php echo ($tname); ?>查看</h3>

<table cellpadding="2" cellspacing="1" border="0" style="width:500px;">
<tr>
<th colspan="2">基本数据</th>
</tr>
<tr>
<td>单 号</td>
<td class="tf"><?php echo ($num); ?></td>
</tr>
<tr>
<td>日 期</td>
<td class="tf"><?php echo (date('Y-m-d', $time)); ?></td>
</tr>
<tr>
<td>方 式</td>
<td class="tf"><?php if(($way)  ==  "0"): ?>现金<?php else: ?><?php echo ($bank['name']); ?>(<?php echo ($bank['account']); ?>)<?php endif; ?></td>
</tr>
<tr>
<td>员工/经办人</td>
<td class="tf"><?php echo ($operator['name']); ?></td>
</tr>

<tr>
<td colspan="2">
<table cellpadding="0" cellspacing="0" border="0">
<?php foreach($price as $k=>$v){?>
<tr>
<td style="border-right:0px; text-align:left; line-height:30px;">
科目: <?php echo ($subjects[$subject[$k]]['name']); ?>
<br />
收支：<?php if(($income[$k])  ==  "1"): ?>收入<?php else: ?>支出<?php endif; ?>　
金额: <?php echo sprintf('%0.2f', $v / 100) ?>　
摘要: <?php echo ($comment[$k]); ?>
</td>
</tr>
<?php }?>
</table>
</td>
</tr>
</table>
<div style="width:500px;">
<div class="f">
<input type="button" class="button" onclick="history.go(-1);" value="返 回" />
</div>

<div class="r">
打印选项：<select id="title">
<option value="收款" <?php if(($title)  ==  "收款"): ?>selected="selected"<?php endif; ?>>收款</option>
<option value="付款" <?php if(($title)  ==  "付款"): ?>selected="selected"<?php endif; ?>>付款</option>
<option value="转账" <?php if(($title)  ==  "转账"): ?>selected="selected"<?php endif; ?>>转账</option>
</select>
<select id="in">
<option value="借方" <?php if(($in)  ==  "借方"): ?>selected="selected"<?php endif; ?>>借方</option>
<option value="贷方" <?php if(($in)  ==  "贷方"): ?>selected="selected"<?php endif; ?>>贷方</option>
</select>
<select id="su">
<option>选择</option>
<option value="现金" <?php if(($su)  ==  "现金"): ?>selected="selected"<?php endif; ?>>现金</option>
<?php if(is_array($banks)): ?><?php $i = 0;?><?php $__LIST__ = $banks?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo['name']); ?>" <?php if(($su)  ==  $vo['name']): ?>selected="selected"<?php endif; ?>><?php echo ($vo['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>
<!--select id="su" style="width:100px;">
<?php if(is_array($subjects)): ?><?php $i = 0;?><?php $__LIST__ = $subjects?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo['name']); ?>"><?php echo ($vo['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select-->
<input type="button" class="button" onclick="print()" value="打 印" />
</div>
</div>

<script type="text/javascript">
function print()
{
var t = $("#title").val();
var i = $("#in").val();
var s = $("#su").val();
location.href='/index.php/Financial/printDaily/id/<?php echo ($id); ?>/t/'+t+'/i/'+i+'/s/'+s;
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