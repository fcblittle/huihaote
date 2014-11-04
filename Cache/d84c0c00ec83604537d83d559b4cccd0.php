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
<h3>个人信箱</h3>
<div class="tab">
`   <a href="/index.php/Home/mail">收件箱<span class="red fb"></span></a>
<a href="/index.php/Home/mail/type/send" class="action">发件箱<span class="red fb"></span></a>
<a href="/index.php/Home/mail/type/new">+新邮件</a>
</div>
<link href="/./Tpl/default/Public/Style/mail.css" rel="stylesheet" type="text/css" />

<table cellpadding="2" cellspacing="0" border="0" class="needhover">
<?php if((count($list))  ==  "0"): ?><tr id="empty">
<td style="border-bottom:#CCC dashed 1px;">您的发件箱为空。</td>
</tr><?php endif; ?>
<?php if(is_array($list)): ?><?php $i = 0;?><?php $__LIST__ = $list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><tr onclick="openMail(<?php echo (is_array($vo)?$vo["id"]:$vo->id); ?>)" id="mail_<?php echo (is_array($vo)?$vo["id"]:$vo->id); ?>">
<td class="tf mail">
<div class="mailtitle">
<p>
<?php if((is_array($vo)?$vo["read"]:$vo->read)  ==  "0"): ?><span class="red">【未查看】</span><?php endif; ?>
<?php if(isset($vo['newreply'])): ?><span class="red">【新回复】</span><?php endif; ?>
<?php if((is_array($vo)?$vo["system"]:$vo->system)  ==  "1"): ?><b>系统消息：</b><?php endif; ?>
<?php echo ($vo['title']); ?>
</p>
<span class="info">
收件人：<?php echo ($vo['user']['name']); ?>&nbsp; | &nbsp;
发送时间：<?php echo (timestr($vo['time'],1)); ?>&nbsp; | &nbsp;
<a href="/index.php/Home/mail/type/del/id/<?php echo ($vo['id']); ?>">删 除</a>
</span>
</div>
<div class="mailcontent">
<div class="content"><?php echo (text(is_array($vo)?$vo["content"]:$vo->content)); ?></div>
<dl class="reply">
<?php if((is_array($vo)?$vo["reply"]:$vo->reply)  !=  "0"): ?><?php if(is_array(is_array($vo)?$vo["reply"]:$vo->reply)): ?><?php $i = 0;?><?php $__LIST__ = is_array($vo)?$vo["reply"]:$vo->reply?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$so): ?><?php ++$i;?><?php $mod = (($i % 2 )==0)?><dt><span><?php echo ($so['writer']['name']); ?></span> <?php echo ($so['title']); ?></dt>
<dd>
<?php echo (text($so['content'])); ?>
</dd><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?><?php endif; ?>
<dt>快捷回复：</dt>
<dd>
<textarea id="reply_<?php echo (is_array($vo)?$vo["id"]:$vo->id); ?>"></textarea><br />
<input class="button" type="button" style="margin:0px;" onclick="reply(<?php echo (is_array($vo)?$vo["id"]:$vo->id); ?>)" value="发 送" />
</dd>
</dl>
</div>
</td>
</tr><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
</table>
<script type="text/javascript">
var nic = [];
function openMail(id){
$("#mail_"+id).find(".mailcontent").slideDown();
$("#mail_"+id).find(".red").remove();
if(nic[id] === undefined)
nic[id] = new nicEditor({buttonList : ['fontFormat','fontSize','bold','italic','underline','forecolor','smiley','image']}).panelInstance('reply_'+id);
ajax('/index.php/Home/mail', {id:id}, 'get', function(data){});
}
function reply(id){
var content = nic[id].instanceById('reply_'+id).getContent();
ajax('/index.php/Home/mail/', {reply:id, content:content}, 'post', function(data){
if(data.status == 1){
$("#reply_"+id).parent().prev("dt").before("<dt><span>我</span> 回复:"+$("#mail_"+id+" .mailtitle p").text()+"</dt><dd>"+content+"</dd>");
}else{
alert(data.info);
}
});
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