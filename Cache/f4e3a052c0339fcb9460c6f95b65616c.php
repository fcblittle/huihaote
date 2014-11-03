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
<h3>库存统计</h3>
<script type="text/javascript" src="/./Tpl/default/Public/JS/excanvas.pack.js"></script>
<script type="text/javascript" src="/./Tpl/default/Public/JS/flot.js"></script>
<script type="text/javascript" src="/./Tpl/default/Public/JS/datebox.js"></script>


<div style="padding:0px 10px 10px 0px; margin-bottom:10px; border-bottom:#CCC dashed 1px;">
<a href="/index.php/Stock/statistics/">仓库存量</a> &nbsp;| &nbsp;
<a href="/index.php/Stock/statistics/type/inout">货品进出量</a> &nbsp;| &nbsp;
<a href="/index.php/Stock/statistics/type/trend">货品进出量走势</a>
</div>

<form action="/index.php/Stock/statistics/type/inout">
仓 库：
<select style="margin-bottom:10px;" name="group">
<?php if(is_array($group)): ?><?php $i = 0;?><?php $__LIST__ = $group?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo['gid']); ?>" <?php if(($vo['gid'])  ==  $gid): ?>selected="selected"<?php endif; ?> ><?php echo ($vo['levelstr']); ?><?php echo ($vo['name']); ?></option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>

货 品：
<select style="margin-bottom:10px;" name="goods">
<?php if(is_array($allgoods)): ?><?php $i = 0;?><?php $__LIST__ = $allgoods?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><option value="<?php echo ($vo['id']); ?>" <?php if(($vo['id'])  ==  $goods): ?>selected="selected"<?php endif; ?> ><?php echo ($vo['name']); ?>(<?php echo ($vo['model']); ?>)</option><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</select>

时 间：
<input type="text" style="width:100px;" name="stime" readonly="readonly" onclick="showdatebox(this,'')" /> --
<input type="text" style="width:100px;" name="etime" readonly="readonly" onclick="showdatebox(this,'')" />

<input type="submit" value="查 询" />
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>


<div id="testbox" style="width:500px; height:400px; margin-top:10px; float:left;"></div>

<script type="text/javascript">

function analyze(data){
var data = eval("("+data+")");
var i = 0;
var andata = new Array();
var anticks = new Array();
anticks[0] = [0,0];
for(x in data){
i ++;
andata[i] = [i, data[x]];
anticks[i] = [i, x == 'in' ? '入库量' : '出库量'];
}
$.plot($("#testbox"),
[{data:andata, bars:{barWidth:0.8, align:"center", shadowSize:6, show:true}}],
{
grid: {hoverable: true},
selection:{mode: "xy"},
xaxis:{ticks:anticks},
yaxis:{tickDecimals:0, autoscaleMargin:0.1, min:0}
});

}
analyze('<?php echo ($json); ?>');

function showTooltip(x, y, contents) {
$('<div id="tooltip">' + contents + '</div>').css( {
position: 'absolute',
display: 'none',
top: y + 5,
left: x + 5,
border: '1px solid #333',
padding: '2px','background-color': '#FFF',
opacity: 0.80
}).appendTo("body").fadeIn(200);
}

var previousPoint = null;
$("#testbox").bind("plothover", function (event, pos, item) {
if (item) {
if (previousPoint != item.datapoint) {
previousPoint = item.datapoint;
$("#tooltip").remove();
var y = item.datapoint[1];
var x = item.series.xaxis.ticks[item.datapoint[0]]['label'];
showTooltip(item.pageX, item.pageY, x+"为" + y + "<?php echo ($unit); ?>");
}
}else {
$("#tooltip").remove();
previousPoint = null;
}
});
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