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
<h3>人员资料修改<span>(修改用户资料，鼠标悬停将有部分提示。)</span></h3>
<script type="text/javascript" src="/./Tpl/default/Public/JS/datebox.js"></script>

<form action="/index.php/User/edit" method="post" enctype="multipart/form-data">
<table cellpadding="2" cellspacing="1" border="0" style="width:500px;">
<tr>
<th colspan="2">基本资料</th>
</tr>
<tr>
<td width="100">用户名</td>
<td class="tf"><input type="text" style="width:250px;" name="username" value="<?php echo ($username); ?>" /><input type="hidden" name="id" value="<?php echo ($uid); ?>" /></td>
</tr>
<tr title="留空则不修改密码">
<td>新密码</td>
<td class="tf"><input type="password" style="width:250px;" name="password" /></td>
</tr>
<tr>
<td>确认密码</td>
<td class="tf"><input type="password" style="width:250px;" name="passwordcheck" /></td>
</tr>
<tr>
<td>姓 名</td>
<td class="tf"><input type="text" style="width:250px;" name="name" readonly="readonly" value="<?php echo ($name); ?>" /></td>
</tr>
<tr title="用于快捷查询用户的职工号或身份证号码等。">
<td>编 号</td>
<td class="tf"><input type="text" style="width:250px;" name="num" value="<?php echo ($num); ?>" /></td>
</tr>
<tr>
<td>生日类型</td>
<td class="tf">
<select name="btype">
<option value="阳历" <?php if(($btype)  ==  "阳历"): ?>selected="selected"<?php endif; ?>>阳历</option>
<option value="阴历" <?php if(($btype)  ==  "阴历"): ?>selected="selected"<?php endif; ?>>阴历</option>
</select>
</td>
</tr>
<tr>
<td>生 日</td>
<td class="tf"><input type="text" style="width:250px;" name="birthday" value="<?php echo ($birthday); ?>" readonly="readonly" onclick="showdatebox(this,'')" /></td>
</tr>
<tr title="被锁定的用户将只能够由管理员进行资料修改。">
<td>锁 定</td>
<td class="tf">
<select name="lock">
<?php if(($lock)  ==  "1"): ?><option value="0">不锁定</option>
<option value="1" selected="selected">锁定</option>
<?php else: ?>
<option value="0" selected="selected">不锁定</option>
<option value="1">锁定</option><?php endif; ?>
</select>
</td>
</tr>
<tr title="选择用户角色">
<td>角 色</td>
<td class="tf">
<select name="role[]" style="width:250px;" class="treeselect" multiple="multiple">
<?php if(is_array($role)): ?><?php $i = 0;?><?php $__LIST__ = $role?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo['id']); ?>" <?php if(($vo['selected'])  ==  "1"): ?>selected="selected"<?php endif; ?> <?php if(($vo['can'])  ==  ""): ?>style="color:#CCC;"<?php endif; ?>><?php echo ($vo['levelstr']); ?> <?php echo ($vo['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>
</td>
</tr>
<?php if(isset($extend)): ?><tr>
<th colspan="2">扩展资料</th>
</tr>
<?php if(is_array($extend)): ?><?php $i = 0;?><?php $__LIST__ = $extend?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
<td><?php echo ($vo['name']); ?></td>
<td class="tf">
<?php switch($vo['type']): ?><?php case "text":  ?><input type="text" style="width:250px;" name="info[]" value="<?php echo ($info[$key]); ?>" /><?php break;?>
<?php case "textarea":  ?><textarea name="info[]" style="width:350px; height:200px;"><?php echo ($info[$key]); ?></textarea><?php break;?>
<?php case "file":  ?><?php if(($info[$key])  !=  ""): ?><a class="img" href=".<?php echo ($info[$key]); ?>">点击查看文件</a>
<input type="hidden" name="info[]" value="" /><input type="file" id="file_<?php echo ($key); ?>" onchange="upload('<?php echo ($key); ?>', 'file', '/index.php/Public/ajaxUpload')" />
<?php else: ?>
<input type="hidden" name="info[]" value="" /><input type="file" id="file_<?php echo ($key); ?>" onchange="upload('<?php echo ($key); ?>', 'file', '/index.php/Public/ajaxUpload')" /><?php endif; ?><?php break;?>
<?php case "pic":  ?><?php if(($info[$key])  !=  ""): ?><a class="img" href=".<?php echo ($info[$key]); ?>"><img src=".<?php echo ($info[$key]); ?>" /></a>
<input type="hidden" name="info[]" value="" /><input type="file" id="file_<?php echo ($key); ?>" name="file_<?php echo ($key); ?>" onchange="upload('<?php echo ($key); ?>', 'pic', '/index.php/Public/ajaxUpload')" />
<?php else: ?>
<input type="hidden" name="info[]" value="" /><input type="file" id="file_<?php echo ($key); ?>" name="file_<?php echo ($key); ?>" onchange="upload('<?php echo ($key); ?>', 'pic', '/index.php/Public/ajaxUpload')" /><?php endif; ?><?php break;?><?php endswitch;?>
</td>
</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?><?php endif; ?>
</table>
<input type="submit" class="button" value="保 存" />
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
<script type="text/javascript">
new nicEditors.allTextAreas({buttonList : ['fontFormat','fontSize','bold','italic','underline','forecolor','image','upload'], uploadURI : '/index.php/Public/upload/'});
function changeInput(obj){
var hide = $(obj).parent().find("input:hidden");
var name = hide.attr("name");
hide.remove();
$(obj).parent().append('<input type="file" style="width:250px;" name="'+name+'" />');
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