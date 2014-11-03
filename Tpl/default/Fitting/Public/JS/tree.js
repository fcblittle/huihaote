// JavaScript Document
;(function ($) {

$.fn.Ptree = function(options){

	var opts = $.extend({}, $.fn.Ptree.defaults, options);

	//check is there next same level dom
	var getLevel = function(o){
		if(o.get()=="") return 0;
		return parseInt(o.attr("class").slice(5));
	};

	//return the Element's index
	var getIndex = function(o){
		if(o!==undefined)
		return o.parent().children("li").index(o);
	};

	//return insertion point
	var getInsertPoint = function(obj){
		var level = getLevel(obj);
		var data = 0;
		for(var i = level; i>=0; i--){
			var pobj = obj.nextAll(".level"+i+":first");
			if(pobj.get() !=  "" && getIndex(pobj)>=1){
				if(getIndex(pobj)<=data || data==0)
				data = getIndex(pobj);
			}
		}
		if(data==0){
			return obj.parent().children("li:last");
		}else{
			return obj.parent().children("li:eq("+(data-1)+")");
		}
	};

	//create a line
	$.fn.Ptree.createBox = function(obj, txt){
		var parent = obj.parent("ul");
		var level = getLevel(obj);
		var clone = parent.children("li:eq(0)").clone("true").attr({"class":"level"+(level+1),"id":"addtemp"});
		clone.children("span[class^='line']").remove();
		clone.children(opts.txt).html(txt);
		var point = getInsertPoint(obj);
		point.after(clone);
		$.fn.Ptree.addLine(clone);
		return clone;
	};

	//delete it
	$.fn.Ptree.deleteBox = function(o){
		var p = o.parent("ul");
		$.fn.Ptree.findChildren(o).remove();
		o.closest("li").remove();
		p.children("li").each(function(){
			$(this).find(".line0,.line1,.line2").remove();
			$.fn.Ptree.addLine($(this));
		});
	};

	//find all children
	$.fn.Ptree.findChildren = function(o){
		var point = getIndex(getInsertPoint(o));
		return o.parent().children("li").slice(getIndex(o), point+1);
	}

	//add a tree style
	$.fn.Ptree.addLine = function(obj){
		var level = getLevel(obj);
		if(level > 0){
			var parent = obj.prevAll(".level"+(level-1)+":first");
			parent.children(".line0,.line1,.line2").clone().prependTo(obj);
			obj.children(".line1").html("&nbsp;");
			obj.children(".line2").html("&nbsp;").attr("class","line0");
			obj.children(opts.txt).before("<span class=\""+opts.prefix+"2\"><span class=\""+opts.prefix+"3\">&nbsp;</span></span>");
		}
		var start = getIndex(parent);
		var end = getIndex(obj);
		var n = obj.find(".line2").prevAll().length;
		if(start>=0)
		obj.parent().children("li").slice(start+1,end).each(function(){
			$(this).find("span:eq("+n+")").attr("class", "line1");
		});
	};

	//init tree
	return this.each(function(){

		$(this).children("li").each(function(){$.fn.Ptree.addLine($(this));});
		$("li:has("+opts.txt+")", this).live("mouseover", function(){$.fn.Ptree.mouseover(this)});
		$("li:has("+opts.txt+")", this).live("mouseout", function(){$.fn.Ptree.mouseout(this)});
		$(this).find(opts.txt).live("click", function(){$.fn.Ptree.onclick(this)});
        $(this).find(opts.txt).live("dblclick", function(){$.fn.Ptree.ondblclick(this)});
	});
};

$.fn.addLine = function(options){
	$.fn.Ptree.deleteBox($("#addtemp").closest("li"));
	var defaults = {obj:"", data:"", cla:""};
	var opts = $.extend(defaults, options);
	var txt = opts.data ? opts.data : "<input type=\"text\" class=\""+opts.cla+"\" value=\"\" />";
	return obj = $.fn.Ptree.createBox(this.children(opts.obj), txt);
};

$.fn.delLine = function(obj){
	$.fn.Ptree.deleteBox(this.children(obj));
};

//extend fun

$.fn.Ptree.onclick = function(){};

$.fn.Ptree.ondblclick = function(){};

$.fn.Ptree.defaults = {prefix:"line",txt:".txt"};

})(jQuery);