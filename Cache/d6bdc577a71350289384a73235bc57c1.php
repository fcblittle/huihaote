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
<h3>财务收支修改</h3>
<script type="text/javascript" src="/./Tpl/default/Public/JS/datebox.js"></script>

<form action="/index.php/Financial/edit" method="post" enctype="multipart/form-data">
<table cellpadding="2" cellspacing="1" border="0" style="width:500px;">
<tr>
<th colspan="2">基本数据</th>
</tr>
<tr>
<td>编号</td>
<td class="tf"><input type="text" style="width:250px;" name="num" value="<?php echo ($num); ?>" /></td>
</tr>
<tr>
<td>收支项目</td>
<td class="tf"><input type="text" style="width:250px;" name="name" value="<?php echo (text($name)); ?>" /></td>
</tr>
<tr>
<td>金　额</td>
<td class="tf"><input type="text" style="width:250px;" name="price" value="<?php echo (showPrice($price)); ?>" /></td>
</tr>
<tr>
<td>做账日期</td>
<td class="tf"><input type="text" style="width:250px;" name="time" value="<?php echo (date('Y-m-d',$time)); ?>" readonly="readonly" onclick="showdatebox(this,'')" /></td>
</tr>
<tr>
<td>发票<em>*</em></td>
<td class="tf">
<select name="bill">
<option value="未开" <?php if(($bill)  ==  "未开"): ?>selected="selected"<?php endif; ?>>未开</option>
<option value="已开" <?php if(($bill)  ==  "已开"): ?>selected="selected"<?php endif; ?>>已开</option>
<option value="不含税" <?php if(($bill)  ==  "不含税"): ?>selected="selected"<?php endif; ?>>不含税</option>
</select>
</td>
</tr>
<tr>
<td>发票编号</td>
<td class="tf"><input type="text" name="billnum" value="<?php echo ($billnum); ?>" /></td>
</tr>
<tr>
<td>未<?php if(($price)  >  "0"): ?>到<?php else: ?>付<?php endif; ?>金额</td>
<td class="tf"><input type="text" style="width:250px;" name="notto" value="<?php echo (showPrice($notto)); ?>" /></td>
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
<input type="hidden" name="id" value="<?php echo ($id); ?>" />
<input type="submit" class="button" value="保 存" />
<input type="button" class="button" onclick="location.href='/index.php/Financial/index'" value="返 回" />
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
<script type="text/javascript">
new nicEditors.allTextAreas({buttonList : ['fontFormat','fontSize','bold','italic','underline','forecolor','image','upload'], uploadURI : '/index.php/Public/upload/'});
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