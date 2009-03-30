/*!
 * runnDAILY JavaScript Library v9.3.30
 * http://runndaily.com/
 *
 * Copyright (c) 2008 - 2009 Byron Wall and Chandler
 *
 */
 
 /*
  * Height functions are used to alter the height of selected elements.
  * heightEqual		Sets the height of selected elements to another element
  * heightBrowse	Sets the height of selected elements to a scale of the window height
  */
$.fn.heightEqual = function(DOM){
	$(this).height($(DOM).height());
}
$.fn.heightBrowser = function(options){
	var settings = $.extend({}, {scale:0.95}, options);

	$(this).height($(window).height() * settings.scale);

	if(settings.callback){
		settings.callback();
	}
}
$.debug = function(message){
	if(window.console){
		console.log(message);
	}
}

/*
 * Units object is used to handle the global unit conversions.
 * 
 * Units.init		Called on every to load up the units engine.
 */
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

/*
 * sorter object is uesd to handle sorting.
 * 
 * This will sooon be abstracted into a plugin.
 * 
 */
var sorter = {
	sort: function(key){
		if(!sorter.settings.classes[key]) return false;
		sorter.settings.sort_key = key;

		var items = $(sorter.settings.item, sorter.settings.parent).get();
		items.sort(function(a, b) {
			var a_val = $(a).find("."+key).eq(0).text().toUpperCase();
			var b_val = $(b).find("."+key).eq(0).text().toUpperCase();


			if(sorter.settings.classes[key] == "numeric"){
				a_val = parseFloat(a_val.replace(/^[^\d.]*/, ''));
				b_val = parseFloat(b_val.replace(/^[^\d.]*/, ''));
			}
			else if(sorter.settings.classes[key] == "date"){
				a_val = Date.parse(a_val);
				b_val = Date.parse(b_val);
			}
			if (a_val < b_val ) return -sorter.settings.sort_desc;
			if (a_val > b_val ) return sorter.settings.sort_desc;

			return 0;
		});
		$.each(items, function(){
			$(sorter.settings.parent).append(this);
		});
	},
	reverse: function(){
		sorter.settings.sort_desc = -sorter.settings.sort_desc;
		sorter.sort(sorter.settings.sort_key);
	},
	init: function(settings){
		sorter.settings = $.extend({}, sorter.settings, settings);
	},
	settings: {
		classes: {},
		parent: null,
		item: null,
		sort_desc: -1,
		sort_key: null
	}
};