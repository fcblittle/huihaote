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
<h3>业务流程</h3>

<link href="/./Tpl/default/Public/Style/index.css" rel="stylesheet" type="text/css" />
<div style="width:100%; overflow:auto">
<div id="doTree">
<?php if(isset($can_do['Supplier']['index'])): ?><a id="Supplier" href="/index.php/Supplier/index" title="供货商列表">&nbsp;</a><?php endif; ?>

<?php if(isset($morder)): ?><a id="Morder" href="/index.php/Morder/audit" title="生产入库单审核">&nbsp;<span><?php echo ($morder); ?>件待审核</span></a>
<?php else: ?>
<?php if(isset($can_do['Morder']['add'])): ?><a id="Morder" href="/index.php/Morder/add" title="添加生产入库单">&nbsp;</a><?php endif; ?><?php endif; ?>

<?php if(isset($porder)): ?><a id="Porder" href="/index.php/Porder/audit" title="采购入库单审核">&nbsp;<span><?php echo ($porder); ?>件待审核</span></a>
<?php else: ?>
<?php if(isset($can_do['Porder']['add'])): ?><a id="Porder" href="/index.php/Porder/add" title="添加采购入库单">&nbsp;</a><?php endif; ?><?php endif; ?>

<?php if(isset($stock_in)): ?><a id="Stock_in" href="/index.php/Stock/index" title="入库审核">&nbsp;<?php if(($stock_in)  !=  "0"): ?><span><?php echo ($stock_in); ?>件待审核</span><?php endif; ?></a><?php endif; ?>

<?php if(isset($financial_out)): ?><a id="Financial_out" href="/index.php/Financial/index" title="支出审核">&nbsp;<?php if(($financial_out)  !=  "0"): ?><span><?php echo ($financial_out); ?>件待审核</span><?php endif; ?></a><?php endif; ?>

<?php if(isset($can_do['Stock']['index'])): ?><a id="Stock" href="/index.php/Stock/index" title="库存管理">&nbsp;</a><?php endif; ?>

<?php if(isset($can_do['Financial']['index'])): ?><a id="Financial" href="/index.php/Financial/index" title="财务管理">&nbsp;</a><?php endif; ?>

<?php if(isset($stock_out)): ?><a id="Stock_out" href="/index.php/Stock/index" title="出库审核">&nbsp;<?php if(($stock_out)  !=  "0"): ?><span><?php echo ($stock_out); ?>件待审核</span><?php endif; ?></a><?php endif; ?>

<?php if(isset($sorder)): ?><a id="Sorder" href="/index.php/Sorder/audit" title="销售出库单审核">&nbsp;<span><?php echo ($sorder); ?>件待审核</span></a>
<?php else: ?>
<?php if(isset($can_do['Porder']['add'])): ?><a id="Sorder" href="/index.php/Sorder/add" title="添加销售出库单">&nbsp;</a><?php endif; ?><?php endif; ?>

<?php if(isset($financial_in)): ?><a id="Financial_in" href="/index.php/Financial/index" title="收入审核">&nbsp;<?php if(($financial_in)  !=  "0"): ?><span><?php echo ($financial_in); ?>件待审核</span><?php endif; ?></a><?php endif; ?>

<?php if(isset($can_do['Client']['index'])): ?><a id="Client" href="/index.php/Client/index" title="客户管理">&nbsp;</a><?php endif; ?>

</div>
</div>
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