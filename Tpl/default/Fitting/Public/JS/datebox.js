/*
-- 日期选择器
-- 青岛互联创想
-- 2009.06.30
*/
function showdatebox(obj, url, time){
	$("#datebox").remove();
	objdatebox = new Datebox(obj, 'objdatebox', url);
	objdatebox.create();
	var off = $(obj).offset();
	$('#datebox').css({top:off.top+$(obj).outerHeight()+'px', left:off.left}).show();
    if(time){
        objtimebox = new Timebox(obj, 'objtimebox');
        $('#timebox').css({top:off.top+$(obj).outerHeight()+'px', left:off.left+185}).show();
    }
}

//主函数,参数obj = this
function Datebox(obj, name, url){
this.returnobj = obj;
this.name = name;
this.url = url;
//配置属性
this.bgc = "#F4F4F4"; //背景色
this.tc  = "#999";    //标题色
this.bc  = "#FFF";  //日期色
this.bch = "#EEE";  //日期高亮
this.cc  = "#CCC";  //关闭按钮背景
this.txtc = "#666"; //字体色
this.monc = "#A0A0A0";//星期标题色
this.sunc = "#ec6100";//星期日
this.satc = "#8fc41f";//星期六
//生成年月
this.d = new Date();
this.yearstr = '';
this.monthstr = '';
for(i=1940; i<(this.d.getUTCFullYear()+10); i++){this.yearstr+=(i!=this.d.getUTCFullYear())?"<option value='"+i+"'>"+i+"</option>":"<option value='"+i+"' selected='selected'>"+i+"</option>"};
for(i=1; i<=12; i++){this.monthstr+=(i!=this.d.getMonth()+1)?"<option value="+i+">"+i+"</option>":"<option value='"+i+"' selected='selected'>"+i+"</option>"};
//生成星期
this.week = new Array('一','二','三','四','五');
this.weekstr = '<div class="week" style="background:'+this.sunc+';">日</div>';
for(i=0;i<=4;i++){this.weekstr+='<div class="week" style="background:'+this.monc+'">'+this.week[i]+'</div>'};
this.weekstr+='<div class="week" style="background:'+this.satc+';">六</div>';
//生成日期
this.datestr='';
//主页面
var box = '\
<style type="text/css">\
.week{width:20px; height:16px; line-height:16px; text-align:center; margin:3px; float:left; color:#FFF;}\
.days a{width:20px; height:16px; line-height:16px; text-align:center; margin:2px; color:'+this.txtc+'; background-color:'+this.bc+'; border:#999 dashed 1px; float:left;}\
.days a:hover{background-color:'+this.bch+'; font-weight:bold;}\
</style>\
<div id="datebox" style="position:absolute; width:183px!important; width:185px; z-index:9998; display:none;">\
<iframe style="width:100%; height:80%;" src="javascript:;"></iframe>\
<div style="position:absolute; left:0px; top:0px; width:100%; padding-bottom:3px; background-color:'+this.bgc+'; border:#FFF ridge 2px; overflow:hidden;">\
<div id="dateselect" style="width:100%; height:25px; line-height:25px; float:left; background-color:'+this.tc+';">\
<select id="boxyear" style="float:left; font-size:12px; width:60px; height:20px; margin:2px;" onchange="javascript:'+this.name+'.daylist();">'+this.yearstr+'</select>\
<select id="boxmonth" style="float:left; font-size:12px; height:20px; margin:2px;" onchange="javascript:'+this.name+'.daylist();">'+this.monthstr+'</select>\
<div style="width:17px; height:17px; line-height:16px; font-size:16px; text-align:center; margin:2px; float:right; cursor:pointer; background-color:'+this.cc+';" onclick="'+this.name+'.closebox();">×</div>\
</div>\
<div id="daylist" style="float:left; width:100%;"></div>\
</div>\
</div>';
    if (typeof Datebox._initialized == "undefined") {
		Datebox.prototype.create = function(){
			$('body').prepend(box);
			this.daylist();
		}
        Datebox.prototype.daylist = function() {
			//加载页面
			$("#datebox #daylist").html(this.weekstr);
			//生成星期空白
			this.month = $('#datebox #boxmonth').val();
			this.year = $('#datebox #boxyear').val();
			this.d.setYear(this.year);
			this.d.setMonth(this.month-1);
			this.d.setDate(1);
			this.nowweek = this.d.getDay();
			for(i=1;i<=this.nowweek;i++){$("#daylist").append('<div class="week"></div>');};
			//添加星期
			if((this.month<=7 && (this.month%2==1)) || (this.month>7 && (this.month%2==0))){this.days=31;}else{if(this.month==2 && this.year%4==0){this.days=29}else if(this.month==2){this.days=28}else{this.days=30;}}
			for(i=1;i<=this.days;i++){$("#daylist").append('<div class="days"><a href="javascript:'+this.name+'.datereturn(\''+i+'\');">'+i+'</a></div>')};
        };
		Datebox.prototype.closebox = function() {
			$("body #datebox").remove();
		}
		Datebox.prototype.datereturn = function(i) {
            var date = $('#boxyear').val()+'-'+$('#boxmonth').val()+'-'+i;
			$(this.returnobj).val(date);
			$("body #datebox").remove();
            if(this.url) location.href=this.url+"/date/"+date;
		}
        Datebox._initialized = true;
	}
}


//时间选择 obj = this
function Timebox(obj, name){
this.bgc = "#F4F4F4";
this.cc  = "#CCC";  //关闭按钮背景
this.name = name;
this.returnobj = obj;
this.val = $(obj).val();
if(this.val !== ''){var time = this.val.split(":");}
else{var time = [0,0]}
this.hourstr = '';
var selected, num;
for(i=0;i<=23;i++){
	if(i<10){num = '0' + i;}else{num = i;}
	if(i==time[0]){selected = 'selected="selected"';}else{selected = '';}
	this.hourstr += '<option value="'+num+'" '+selected+'>'+num+'</option>';
}
this.minutestr = '';
for(var i=0;i<=59;i++){
	if(i<10) num = '0' + i; else num = i;
	if(i==time[1]){selected = 'selected="selected"';}else{selected = '';}
	this.minutestr += '<option value="'+num+'" '+selected+'>'+num+'</option>';
}
var box = '\
<div id="timebox" style="position:absolute; float:left; width:110px; height:22px; z-index:9998; display:none;">\
<iframe style="width:100%; height:100%;" src="javascript:;"></iframe>\
<div style="position:absolute; height:100%; width:100%; float:left; padding:2px; left:0px; top:0px; background-color:'+this.bgc+'; border:#FFF ridge 2px; ">\
<select style="float:left;" id="boxhour" onchange="'+this.name+'.timereturn();">'+this.hourstr+'</select>\
<div style="float:left;"> : </div>\
<select style="float:left;" id="boxminute" onchange="'+this.name+'.timereturn();">'+this.minutestr+'</select>\
<div style="float:left; width:17px; height:17px; line-height:16px; font-size:16px; text-align:center; margin:2px; cursor:pointer; background-color:'+this.cc+';" onclick="'+this.name+'.closebox();">×</div>\
</div></div>';
$('body').prepend(box);
	    if (typeof Timebox._initialized == "undefined") {
		Timebox.prototype.closebox = function() {
			$("body #timebox").remove();
		}
		Timebox.prototype.timereturn = function() {
			this.hour = $('#boxhour').val();
			this.minute = $('#boxminute').val();
            var str = $(this.returnobj).val().split(" ");
            str = str[0];
            var date = str.split("-");
            $(this.returnobj).val(date[0] + '-' + date[1] + '-' + date[2] + ' ' + this.hour + ':'+this.minute)
		}
        Timebox._initialized = true;
	}
}