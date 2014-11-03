//JavaScript 通用文件
function vol(){return;}

function tMenu(){
    $("#main").toggleClass("hidemenu");
}

//ajax
function ajax(url, data, type, success){
	$.ajax({
		url : url,
		type : type?type:"get",
		async : false,
        cache : false,
		beforeSend : function(){$("#ajaxmsg").html("正在提交…").show();},
		data : data?data:"",
		dataType : "json",
		error : function(){alert("请求提交失败，请重试~");$("#ajaxmsg").hide();},
		complete : function(){$("#ajaxmsg").hide();},
		success : success
	});
}

//window
var winData = new Array();
function win(title, content, fun, arg){
    winData.push(new Array(title, content));
    $("#shadow").height($("body").height()).show();
    $("#window .title span").text(title);
    $("#window .content").html(content);
    if(fun === undefined){
        $("#window .control input:eq(0)").one("click", function(){closeWin();});
    }else{
        $("#window .control input:eq(0)").one("click", function(){fun();});
    }
    $("#window").css("top", $(window).scrollTop()+'px').show();
}

function closeWin(){
    $("#shadow").hide();
    $("#window").hide();
    $("#window dt span").text('');
    $("#window dd:eq(0)").html('');
    $("#window .control input:eq(0)").unbind("click");
}

function titleMessage(title){
    if($(document).data("mail") == 1 && title === undefined){
        $(document).data("mail",0);
        title = document.title;
        pre = "【新邮件】";
        document.title = pre + title;
        setInterval("titleMessage('"+title+"')", 1000);
    }else{
        if(document.title == "【新邮件】" + title){
            document.title = "【　　　】" + title;
        }else{
            document.title = "【新邮件】" + title;
        }
    }
}

function getMessage(url, action){
    var more = 0;
    if(action == 'Home/mail')
        more = 1;
    $.get(url, {more:more, _:new Date().getTime()}, function(data){
        if(data.status == 1){
            $("#topNav").find(".mail span").html("").removeClass("num");
            if(data.data.mail){
                if(!more){
                    if(data.data.mail > 0) {
                        if($(document).data("mail") !== 0) {$(document).data("mail",1); titleMessage();}
                        $("#topNav").find(".mail span").html(data.data.mail).addClass("num");
                        $("#footer").html('<object id="sms_sound" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="0" height="0" align="middle"><param name="allowScriptAccess" value="sameDomain" /><param name="movie" value="/Tpl/default/Public/Media/smsrrrrrrr.swf" /><param name="quality" value="high" /><param name="wmode" value="Opaque" /><embed name="sms_sound" src="/Tpl/default/Public/Media/smsrrrrrr.swf" quality="high" width="0" height="0" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" /></object>');
                    }
                }else{
                    $(".tab a:eq(0) .red").html("");
                    $(".tab a:eq(1) .red").html("");
                    if(data.data.mail.count) $("#topNav").find(".mail span").html(data.data.mail.count).addClass("num");
                    if(data.data.mail.form) $(".tab a:eq(0) .red").html(" ["+data.data.mail.form+"]");
                    if(data.data.mail.to) $(".tab a:eq(1) .red").html(" ["+data.data.mail.to+"]");
                }
            }else{
                $("#topNav").find(".mail span").removeClass("num");
            }
            if(data.data.inform){
                var title = '';
                var content = '';
                for(x in data.data.inform){
                    title += "<div class='inf_t' style='font-size:14px;font-weight:bold;text-align:center;padding:10px 0px;'>"+data.data.inform[x].title+"</div>";
                    content += "<div class='inf_c'>"+data.data.inform[x].content+"</div>";
                }
                var win_content = title+content+"<div style='height:20px;padding-top:20px;'><a class='inf_prev f' style='color:#999;' href='javascript:infPage(0)'><<上一条通知</a> <a class='inf_next r' style='color:#999;' href='javascript:infPage(1)'>下一条通知>></a><span style='clear:both'>&nbsp;</span></div>";
                var readAllInform = function(){
                    $.get(url, {read:1, _:new Date().getTime()});
                    closeWin();
                    setTimeout("getMessage('"+url+"', '"+action+"')", 10000);
                }
                win("系统新通知", win_content, readAllInform);
                $(".inf_t:gt(0), .inf_c:gt(0), .inf_prev").hide();
                if(data.data.inform.length == 1) $(".inf_next").hide();
            }else{
                setTimeout("getMessage('"+url+"', '"+action+"')", 10000);
            }
        }else{
            setTimeout("getMessage('"+url+"', '"+action+"')", 10000);
        }
    }, 'json');
}

function infPage(type){
    var title = $(".inf_t:visible");
    var content = $(".inf_c:visible");
    if(type){
        title.next(".inf_t").show();
        content.next(".inf_c").show();
        if(title.next(".inf_t").nextAll(".inf_t").length)
            $(".inf_next").show();
        else
            $(".inf_next").hide();
        $(".inf_prev").show();
    }else{
        title.prev(".inf_t").show();
        content.prev(".inf_c").show();
        if(title.prev(".inf_t").prevAll(".inf_t").length)
            $(".inf_prev").show();
        else
            $(".inf_prev").hide();
        $(".inf_next").show();
    }
    title.hide();
    content.hide();
}

function informAnt(){
    var num = $("#msg div").children("a").length;
    var top = parseInt($("#msg div").css("top"));
    if(-top > (num - 2) * 30)
        top = 30;
    $("#msg div").animate({"top":top-30+"px"}, 1000);
    setTimeout("informAnt()", 3000);
}


function upload(obj, type, url){
    $("#file_"+obj).after("<span><img src='./Tpl/default/Public/Images/loading.gif' />上传中,请稍后...</span>");
    $.ajaxFileUpload({
        url:url,
        fileElementId:"file_"+obj,
        secureuri:false,
        dataType:"json",
        data:{key:obj},
        success: function(data) {
            $("#file_"+obj).next("span").remove();
            if(data.status == 1){
                $("#file_"+obj).prev("input:hidden").val(data.data);
                if($("#file_"+obj).prev("a").length == 0){
                    if(type == 'pic')
                        $("#file_"+obj).prev("input:hidden").before("<a class='img' target='_blank' href='."+data.data+"'><img src='."+data.data+"' /></a>");
                    else
                        $("#file_"+obj).prev("input:hidden").before("<a href='."+data.data+"'>点击查看文件</a>");
                }else{
                    $("#file_"+obj).prev("a").attr("href", data.data);
                    if(type == 'pic')
                        $("#file_"+obj).prev("a").html("<img src='."+data.data+"' />");
                }
            }else{
                alert(data.info);
            }
        },
        error: function (data, status, e){
            alert(e);
            $(obj).next("span").remove();
        }
    });
}

function setCookie(c_name,value,expiredays){
    var exdate=new Date()
    exdate.setDate(exdate.getDate()+expiredays)
    document.cookie=c_name+ "=" +escape(value)+((expiredays==null) ? "" : ";expires="+exdate.toGMTString())
}

function getCookie(c_name){
    if (document.cookie.length>0){
        c_start=document.cookie.indexOf(c_name + "=")
        if (c_start!=-1){
            c_start=c_start + c_name.length+1
            c_end=document.cookie.indexOf(";",c_start)
            if (c_end==-1) c_end=document.cookie.length
            return unescape(document.cookie.substring(c_start,c_end))
        }
    }
    return ""
}

$(document).ready(function(){
    $("#menu dt").click(function(){
        $(this).toggleClass("off");
        $(this).next("dd").slideToggle(100);
        var on = getCookie("onDt");
        on = on.split(',');
        var index = $("#menu .tree dt").index($(this));

        if($(this).attr("class") != "off"){
            //记录打开的目录
            on.unshift(index);
        }else{
            var newon = [];
            for(i in on){
                if(on[i] != index && on[i]) newon.unshift(on[i]);
            }
            on = newon;
        }
        setCookie("onDt", on.join(','));
    });

    $("#table table tr:has(td):odd").addClass("b");
    $("#table .needhover tr:has(td):not('.page')").live("mouseover mouseout", function(){
        if($(this).is(".hover")){$(this).removeClass("hover");}else{$(this).addClass("hover");}
    });
    $(".button").live("mouseover mouseout", function(){$(this).toggleClass("button_h");});
    $(".button_red").live("mouseover mouseout", function(){$(this).toggleClass("button_redh");});

    var on = getCookie("onDt").split(',');
    for(i in on){
        if(!on[i] && on[i]!==0) continue;
        $("#menu dt:eq("+on[i]+")").removeClass("off");
        $("#menu dd:eq("+on[i]+")").show();
    }
    informAnt();
});