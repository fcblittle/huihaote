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
<h3>货品添加</h3>

<form action="/index.php/Goods/add/url/<?php echo ($url); ?>" method="post" enctype="multipart/form-data">
<table cellpadding="2" cellspacing="1" border="0" style="width:500px;">
<tr>
<th colspan="2">基本资料</th>
</tr>
<tr>
<td width="100">品 名<em>*</em></td>
<td class="tf"><input type="text" style="width:250px;" name="name" /></td>
</tr>
<tr>
<td>编 码</td>
<td class="tf"><input type="text" style="width:250px;" name="num" /></td>
</tr>
<tr>
<td>分 类</td>
<td class="tf">
<select name="type">
<?php if(is_array($types)): ?><?php $i = 0;?><?php $__LIST__ = $types?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo['id']); ?>"><?php echo ($vo['levelstr']); ?> <?php echo ($vo['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>
</td>
</tr>
<tr>
<td>仓 库</td>
<td style="text-align:left;">
<select multiple="multiple" style="width:250px;" name="group[]">
<?php if(is_array($groups)): ?><?php $i = 0;?><?php $__LIST__ = $groups?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo['gid']); ?>"><?php echo ($vo['levelstr']); ?> <?php echo ($vo['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>
</td>
</tr>
<tr>
<td>型 号</td>
<td class="tf"><input type="text" style="width:250px;" name="model" /></td>
</tr>
<tr>
<td>销售价格</td>
<td class="tf"><input type="text" style="width:250px;" name="price" /></td>
</tr>
<tr>
<td>采购价格</td>
<td class="tf"><input type="text" style="width:250px;" name="cost" /></td>
</tr>
<tr>
<td>数量单位</td>
<td class="tf"><input type="text" style="width:250px;" name="unit" value="" /></td>
</tr>
<?php if(isset($extend)): ?><tr>
<th colspan="2">扩展资料</th>
</tr>
<?php if(is_array($extend)): ?><?php $i = 0;?><?php $__LIST__ = $extend?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
<td><?php echo ($vo['name']); ?></td>
<td class="tf">
<?php switch($vo['type']): ?><?php case "text":  ?><input type="text" style="width:250px;" name="info[]" /><?php break;?>
<?php case "textarea":  ?><textarea name="info[]" style="width:350px; height:200px;"></textarea><?php break;?>
<?php case "file":  ?><input type="hidden" name="info[]" value="" /><input type="file" id="file_<?php echo ($key); ?>" name="file_<?php echo ($key); ?>" onchange="upload('<?php echo ($key); ?>', 'file', '/index.php/Public/ajaxUpload')" value="" /><?php break;?>
<?php case "pic":  ?><input type="hidden" name="info[]" value="" /><input type="file" id="file_<?php echo ($key); ?>" name="file_<?php echo ($key); ?>" onchange="upload('<?php echo ($key); ?>', 'pic', '/index.php/Public/ajaxUpload')" value="" /><?php break;?><?php endswitch;?>
</td>
</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?><?php endif; ?>
</table>
<input type="submit" class="button" value="保 存" />
<?php if(($url)  ==  ""): ?><input type="button" class="button" onclick="location.href='/index.php/Goods/index'" value="返 回" />
<?php else: ?>
<input type="button" class="button" onclick="location.href='/index.php/<?php echo ($url); ?>/add/do/1'" value="返 回" /><?php endif; ?>
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