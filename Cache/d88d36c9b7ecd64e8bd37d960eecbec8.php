<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Encoding" content="gzip" />
<title>青岛惠好特--企业进销存管理系统 &rsaquo; 用户登录</title>
<meta name="author" content="Interidea Team" />
<meta name="copyright" content="2012 Interidea Co.,Ltd" />
<link href="/./Tpl/default/Public/Style/main.css" rel="stylesheet" type="text/css" />
<link href="/./Tpl/default/Public/Style/login.css" rel="stylesheet" type="text/css" />
<script src="/./Tpl/default/Public/JS/jquery.js" type="text/javascript"></script>
<script src="/./Tpl/default/Public/JS/Editor/nicEdit.js" type="text/javascript"></script>
</head>


<body>
<div id="login">
<form action="/index.php/Public/login" method="post">
<div class="text">
<input type="text" name="username" value="" />
</div>
<div class="text">
<input type="password" name="password" value="" />
</div>
<div>
<input class="submit" type="submit" value="" />
<input class="close" type="button" onclick="closewin();" value="" />
</div>
<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>
<script type="text/javascript">
function closewin(){
self.opener = null;
self.close();
location.href = '/index.php/Public/logout'
}
$(".text input").val("");
$(".text input").focus(function(){$(this).parent().addClass("hover")});
$(".text input").blur(function(){$(this).parent().removeClass("hover")});
$(".submit").hover(function(){$(this).addClass("submit_h");}, function(){$(this).removeClass("submit_h")});
$(".close").hover(function(){$(this).addClass("close_h");}, function(){$(this).removeClass("close_h")});
</script>
</div>
</body>
</html>