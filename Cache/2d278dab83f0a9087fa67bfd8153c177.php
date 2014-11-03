<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php if(($jumpUrl)  !=  ""): ?><meta http-equiv="refresh" content="<?php echo ($waitSecond); ?>; url=/index.php/<?php echo ($jumpUrl); ?>" /><?php endif; ?>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Encoding" content="gzip" />
<title>青岛惠好特--企业进销存管理系统 &rsaquo; 系统提示</title>
<meta name="author" content="Interidea Team" />
<meta name="copyright" content="2012 Interidea Co.,Ltd" />
<link href="/./Tpl/default/Public/Style/main.css" rel="stylesheet" type="text/css" />
<link href="/./Tpl/default/Public/Style/login.css" rel="stylesheet" type="text/css" />
<script src="/./Tpl/default/Public/JS/jquery.js" type="text/javascript"></script>
<script src="/./Tpl/default/Public/JS/Editor/nicEdit.js" type="text/javascript"></script>
</head>


<body>
<div id="message">
<input type="hidden" name="hiddenField" id="clock" value="<?php echo ($waitSecond); ?>"/>
<div class="con">
<div class="msg"><?php echo ($message); ?></div>
<div class="link">
系统将在
<span id="time" style="color:#C00; font-weight:bold;"><?php echo ($waitSecond); ?></span>
秒后自动跳转，如果不想等待，请直接
<?php if(($jumpUrl)  !=  ""): ?><a href="/index.php/<?php echo ($jumpUrl); ?>.html" target="_self">点击此处</a>
<?php else: ?>
<a href="javascript:history.go(-1);" target="_self">点击此处</a><?php endif; ?>
</div>
</div>
</div>
<script language="JavaScript" type="text/javascript">
var time = document.getElementById('clock').value;
window.onload = function timec(){
if(time>=0) {
document.getElementById('time').innerHTML = time;
}else{
/*<?php if(($jumpUrl)  ==  ""): ?>*/
history.go(-1);
/*<?php endif; ?>*/
}
time--;
var i = setTimeout(timec,1000);
}
</script>
</body>
</html>