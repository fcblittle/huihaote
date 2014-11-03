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
<h3>应收应付 统计</h3>

<script src="/./Tpl/default/Public/JS/datebox.js"></script>
<div>
<form method="GET" action="/index.php/Financial/debt">
类型：
<select style="margin-bottom:10px;" id="cType" name="type">
<option value="supplier" <?php if(($type)  ==  "supplier"): ?>selected="selected"<?php endif; ?> >供应商</option>
<option value="client" <?php if(($type)  ==  "client"): ?>selected="selected"<?php endif; ?> >客户</option>
</select>　　
公司名：
<input type="text" name="name" value="<?php echo ($name); ?>" />
时间：
时 间：<input name="start" id="start" style="width:100px;" type="input" value="<?php if($start): ?><?php echo (date('Y-m-d',$start)); ?><?php endif; ?>" onclick="showdatebox(this,'')"  />
至 <input name="end" id="end" style="width:100px;" type="input" value="<?php if($end): ?><?php echo (date('Y-m-d',$end)); ?><?php endif; ?>" onclick="showdatebox(this,'')" />
<input type="submit" name='ac' value="搜 索" />
<input type="submit" name='ac' value="导 出" />
<input type="submit" name='ac' value="打 印" />
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
</div>
<table cellpadding="2" cellspacing="1" border="0" class="needhover" style="width:780px;">
<tr>
<th width="300">公司名</th>
<th width="180">累计应收款</th>
<th width="180">累计应付款</th>
<th width="120">操作</th>
</tr>
<?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr>
<td><?php echo ($vo['name']); ?></td>
<td><?php echo (showPrice($vo['income'])); ?></td>
<td><?php echo (showPrice($vo['pay'])); ?></td>
<td>
<a href="/index.php/Financial/debtList/id/<?php echo ($vo['id']); ?>/type/<?php echo ($type); ?>">查看明细</a>
</td>
</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
<tr class="white page">
<td colspan="2">总计应收款：<?php echo (showPrice($total['income'])); ?>　　总计应付款：<?php echo (showPrice($total['pay'])); ?></td>
<td colspan="2" class="tr"><?php echo ($page); ?></td>
</tr>
</table>

<?php if(isset($can_do['Financial']['addDebt'])): ?><input type="button" class="button" onclick="location.href='/index.php/Financial/addDebt'" value="添 加" /> &nbsp;<?php endif; ?>

<script type="text/javascript">
function openSearch(){
$("#Search").show();
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