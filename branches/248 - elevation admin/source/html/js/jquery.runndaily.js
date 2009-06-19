/*!
 * runnDAILY Javascript Library
 * 
 */ 
(function($){
	var resize_items = [];
	$.fn.heightBrowser = function(options){
		var settings = $.extend({}, {scale:0.92}, options);
		var DOM = $(this).selector;
		if($(this).data("resize") && !settings.clear){
			var index = $(this).data("resize") - 1;
			settings.scale = resize_items[index].scale;
			settings.callback = resize_items[index].callback;
		}
		else{
			resize_items.push({DOM: DOM, scale:settings.scale, callback: settings.callback});
			$(this).data("resize", resize_items.length);
		}
				
		$(this).height($(window).height() * settings.scale);
	
		if(settings.callback){
			settings.callback();
		}
		return $(this);
	}
	$(window).bind("resize.runndaily", function(){
		$.each(resize_items, function(){
			$(this.DOM).heightBrowser();
		});
	});
	$.fn.heightEqual = function(DOM){
		$(this).height($(DOM).height());
		return $(this);
	};
	$.debug = function(message, iterate){
		if(window.console){
			console.log(message);
			if(iterate){
				$.each(message, function(k){
					console.log(k + ":" + this);
				});
			}
		}
	}
	$.template = function(template, params, as_string){
		var node = template.replace(/{([^{}]*)}/g, function (a, b) {
			var r = params[b];
			return typeof r === 'string' || typeof r === 'number' ? r : a;
		});
		if(as_string) return node;
		return $(node);
	};
})(jQuery);

var	Units = {
	convert: 1.6,
	is_mile: true,
	unit_value: "mi",
	unit_class: ".dist-unit",
	dist_class: ".dist-num",
	combined_class: ".dist-val",
	callback: null,
	changeUnits: function(){
		if(Units.is_mile){
			Units.unit_value = "km";
			Units.convert = 1.609344;
		}
		else{
			Units.unit_value = "mi";
			Units.convert = 0.6214;
		}
		Units.is_mile = !Units.is_mile;

		$(Units.unit_class).text(Units.unit_value);
		$(Units.dist_class).each(function(){
			var dist = parseFloat($(this).text()) * Units.convert
				$(this).text(dist.toFixed(2));
		});
		$(Units.combined_class).each(function(){
			var dist = parseFloat($(this).text().replace(/^[^\d.]*/, '')) * Units.convert;
			$(this).text(dist.toFixed(2) + " " + Units.unit_value);
		});
		if(Units.callback){
			Units.callback();
		}
	},
	
	init: function(options){
		Units = $.extend({}, Units, options);
		$(Units.dist_class).die("click").live("click", Units.changeUnits)
		$(Units.combined_class).die("click").live("click", Units.changeUnits)
		$(Units.unit_class).die("click").live("click", Units.changeUnits)
	},
	textWithUnits: function(options){
		opts = $.extend({}, {from_miles:true, add_units: false}, options);
		
		if(!Units.is_mile){
			opts.dist *= Units.convert;
		}

		$(opts.target).text(opts.dist.toFixed(2)).addClass(Units.dist_class.replace(".",""));
	}
};

(function($){
	var sorters = {};
	$.sorter = function(){}
	$.extend($.sorter,{ 
		add: function(id, settings){
			sorters[id] = $.extend({}, $.sorter.defaultSettings, settings);
			if(settings.reverse){
				$(settings.reverse).click(function(){
					$.sorter.reverse(id);
					if($(this).hasClass("sort_asc")){
						$(this).html('<img src="/img/icon/sort_desc.png" /> DESC</a>').addClass("sort_desc").removeClass("sort_asc");
					}
					else{
						$(this).html('<img src="/img/icon/sort_asc.png" /> ASC</a>').addClass("sort_asc").removeClass("sort_desc");
					}
					return false;
				});
			}
			if(settings.selector){
				$(settings.selector).change(function(){
					$.sorter.sort(id, $(this).val());
				});
			}
		},
		reverse: function(id){
			sorters[id].sort_desc *= -1;
			sort(sorters[id]);
		},
		sort: function(id, key){
			sort(sorters[id], key);
			
		},
		defaultSettings: {
			classes: null,
			parent: null,
			item: null,
			sort_desc: -1,
			sort_key: null,
			reverse: null,
			selector: null
		}
	});
	function sort(sorter, key){
		if(!key) key = sorter.sort_key;
		if(!sorter.classes[key]) return false;
		sorter.sort_key = key;

		var items = $(sorter.item, sorter.parent).get();
		items.sort(function(a, b) {
			var a_val = $(a).find("."+key).eq(0).text().toUpperCase();
			var b_val = $(b).find("."+key).eq(0).text().toUpperCase();

			if(sorter.classes[key] == "numeric"){
				a_val = parseFloat(a_val.replace(/^[^\d.]*/, ''));
				b_val = parseFloat(b_val.replace(/^[^\d.]*/, ''));
			}
			else if(sorter.classes[key] == "date"){
				a_val = Date.parse(a_val);
				b_val = Date.parse(b_val);
			}
			if (a_val < b_val ) return -sorter.sort_desc;
			if (a_val > b_val ) return sorter.sort_desc;

			return 0;
		});
		$.each(items, function(){
			$(sorter.parent).append(this);
		});
	}
	
})(jQuery);