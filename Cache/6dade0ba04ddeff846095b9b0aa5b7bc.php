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
<h3>银行账号修改</h3>
<script type="text/javascript" src="/./Tpl/default/Public/JS/datebox.js"></script>

<form action="/index.php/Financial/editBank" method="post" enctype="multipart/form-data">
<table cellpadding="2" cellspacing="1" border="0" style="width:500px;">
<tr>
<th colspan="2">基本数据</th>
</tr>
<tr>
<td>银行类型</td>
<td class="tf">
<select name="name">
<option>--请选择--</option>
<option value="中国银行" <?php if(($name)  ==  "中国银行"): ?>selected="selected"<?php endif; ?>>中国银行</option>
<option value="中国建设银行" <?php if(($name)  ==  "中国建设银行"): ?>selected="selected"<?php endif; ?>>中国建设银行</option>
<option value="中国农业银行" <?php if(($name)  ==  "中国农业银行"): ?>selected="selected"<?php endif; ?>>中国农业银行</option>
<option value="中国工商银行" <?php if(($name)  ==  "中国工商银行"): ?>selected="selected"<?php endif; ?>>中国工商银行</option>
<option value="外贸部银行" <?php if(($name)  ==  "外贸部银行"): ?>selected="selected"<?php endif; ?>>外贸部银行</option>
<option value="中国招商银行" <?php if(($name)  ==  "中国招商银行"): ?>selected="selected"<?php endif; ?>>中国招商银行</option>
<option value="交通银行" <?php if(($name)  ==  "交通银行"): ?>selected="selected"<?php endif; ?>>交通银行</option>
<option value="中国光大银行" <?php if(($name)  ==  "中国光大银行"): ?>selected="selected"<?php endif; ?>>中国光大银行</option>
<option value="兴业银行" <?php if(($name)  ==  "兴业银行"): ?>selected="selected"<?php endif; ?>>兴业银行</option>
<option value="调账银行" <?php if(($name)  ==  "调账银行"): ?>selected="selected"<?php endif; ?>>调账银行</option>
<option value="中国民生银行" <?php if(($name)  ==  "中国民生银行"): ?>selected="selected"<?php endif; ?>>中国民生银行</option>
<option value="青岛银行" <?php if(($name)  ==  "青岛银行"): ?>selected="selected"<?php endif; ?>>青岛银行</option>
</select>
</td>
</tr>
<tr>
<td>银行账号</td>
<td class="tf"><input type="text" style="width:250px;" name="account" value="<?php echo ($account); ?>" /></td>
</tr>
<tr>
<td>开户人</td>
<td class="tf"><input type="text" style="width:150px;" name="holder"  value="<?php echo ($holder); ?>" /></td>
</tr>
<tr>
<td>开户时间</td>
<td class="tf"><input type="text" style="width:150px;" name="time" readonly="readonly" value="<?php echo (date('Y-m-d', $time)); ?>" onclick="showdatebox(this,'')" /></td>
</tr>
<tr>
<td>开户银行</td>
<td class="tf"><input type="text" style="width:250px;" name="address" value="<?php echo ($address); ?>" /></td>
</tr>
</table>
<input type="hidden" name="id" value="<?php echo ($id); ?>" />
<input type="submit" class="button" value="保 存" />
<input type="button" class="button" onclick="history.go(-1);" value="返 回" />
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>

<script type="text/javascript">
new nicEditors.allTextAreas({buttonList : ['fontFormat','fontSize','bold','italic','underline','forecolor','image','upload'], uploadURI : '/index.php/Public/upload/'})
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