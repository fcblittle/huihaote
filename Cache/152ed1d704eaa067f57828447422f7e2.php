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
<h3>收支详单查看</h3>
<table cellpadding="2" cellspacing="1" border="0" style="width:500px;">
<tr>
<th colspan="2">基本数据</th>
</tr>
<tr>
<td>编号</td>
<td class="tf"><?php echo ($num); ?></td>
</tr>
<tr>
<td>收支项目</td>
<td class="tf"><?php echo (text($name)); ?></td>
</tr>
<tr>
<td>金　额</td>
<td class="tf"><?php echo (showPrice($price)); ?></td>
</tr>
<tr>
<td>方　式</td>
<td class="tf">
<?php if(($way)  ==  "1"): ?><?php echo ($bank['name']); ?>(<?php echo ($bank['account']); ?>)
<?php else: ?>
现金<?php endif; ?>
</td>
</tr>
<tr>
<td>做账日期</td>
<td class="tf"><?php echo (date('Y-m-d',$time)); ?></td>
</tr>
<tr>
<td>发票</td>
<td class="tf"><?php echo ($bill); ?></td>
</tr>
<tr>
<td>发票编号</td>
<td class="tf"><?php echo ($billnum); ?></td>
</tr>
<tr>
<td>到账状态</td>
<td class="tf">
<?php if(($audit)  ==  "0"): ?><span style="color:red">未审查</span>
<?php else: ?>
<?php if(($notto)  ==  "0"): ?>已<?php if(($price)  >  "0"): ?>到帐<?php else: ?>付清<?php endif; ?>
<?php else: ?>
<span style="color:red">欠账：<?php echo (showPrice($notto)); ?></span><?php endif; ?><?php endif; ?>
</td>
</tr>
<?php if(isset($extend)): ?><tr>
<th colspan="2">订单资料</th>
</tr>
<?php if(is_array($extend)): ?><?php $i = 0;?><?php $__LIST__ = $extend?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
<td><?php echo ($vo['name']); ?></td>
<td class="tf">
</td>
</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?><?php endif; ?>

<tr>
<td>明细</td>
<td class="tf"> <a href="/index.php<?php echo ($url); ?>">点击查看明细</a></td>
</tr>
</table>
<div style="width:500px; text-align:right; margin:5px 0px; color:#999;">
录入者：
<?php if(isset($can_do['User']['view'])): ?><a href="/index.php/User/view/id/<?php echo ($uid); ?>" style="color:#999;"><?php echo ($user['name']); ?></a>
<?php else: ?>
<?php echo ($user['name']); ?><?php endif; ?>&nbsp;┊&nbsp;
录入时间：
<?php echo (timestr($rtime)); ?>
</div>
<input type="button" class="button" onclick="location.href='/index.php/Financial/index'" value="返 回" />
<?php if($audit > 0): ?>　　　　　
科目：
<select id="su" style="width:100px;">
<?php if(is_array($subjects)): ?><?php $i = 0;?><?php $__LIST__ = $subjects?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo['name']); ?>"><?php echo ($vo['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>
<input type="button" class="button" onclick="printFinancial(<?php echo ($id); ?>)" value="打 印" /><?php endif; ?>
<script type="text/javascript">
function printFinancial(id)
{
var s = $("#su").val();
location.href='/index.php/Financial/printFinancial/id/'+id+'/s/'+s;
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