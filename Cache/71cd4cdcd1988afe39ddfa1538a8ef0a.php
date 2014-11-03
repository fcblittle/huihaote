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
<h3><?php echo ($name); ?>资料设置<span>(设置数据扩展资料信息，修改将影响所有已有资料)</span></h3>
<div style="padding:10px;">
<a href="/index.php/System/info/type/1">用户资料</a> &nbsp;| &nbsp;
<a href="/index.php/System/info/type/2">仓库资料</a> &nbsp;| &nbsp;
<a href="/index.php/System/info/type/3">货品资料</a> &nbsp;| &nbsp;
<a href="/index.php/System/info/type/4">客户资料</a> &nbsp;| &nbsp;
<a href="/index.php/System/info/type/5">供应商资料</a>
</div>

<form action="/index.php/System/info" method="post">
<table cellpadding="2" cellspacing="1" border="0" style="width:300px;" id="linebox">
<tr>
<th>添加项</th>
<th>类型</th>
</tr>
<?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
<td><input name="name[]" type="text" value="<?php echo ($vo['name']); ?>" /></td>
<td>
<select name="type[]">
<?php if(($vo['type'])  ==  "text"): ?><option value="text" selected="selected">填空</option>
<?php else: ?>
<option value="text">填空</option><?php endif; ?>
<?php if(($vo['type'])  ==  "textarea"): ?><option value="textarea" selected="selected">文本区</option>
<?php else: ?>
<option value="textarea">文本区</option><?php endif; ?>
<?php if(($vo['type'])  ==  "file"): ?><option value="file" selected="selected">文件</option>
<?php else: ?>
<option value="file">文件</option><?php endif; ?>
<?php if(($vo['type'])  ==  "pic"): ?><option value="pic" selected="selected">图片</option>
<?php else: ?>
<option value="pic">图片</option><?php endif; ?>
</select>
</td>
</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
<tr>
<td><input name="name[]" type="text" value="" /></td>
<td>
<select name="type[]">
<option value="text">填空</option>
<option value="textarea">文本区</option>
<option value="file">文件</option>
<option value="pic">图片</option>
</select>
</td>
</tr>
</table>
<input type="hidden" name="key" value="<?php echo ($type); ?>" />
<input type="button" class="button" onclick="addline()" value="增 加" />
<input type="submit" class="button" value="保 存" />
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
<script type="text/javascript">
function addline(){
$("#linebox").append($("#linebox tr:last").clone());
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