<div class="grid_12">
	<h2 id="page-heading">Training</h2>
</div>
<div class="clear"></div>

<div class="grid_3">
	<div id="sort_options" class="align_right">
			<label>Sort by: </label>
			<select id="sort_select">
				<option value="t_date">Date</option>
				<option value="t_dist">Distance</option>
				<option value="t_pace">Pace</option>
<!--				<option value="t_cal">Calories</option>-->
				<option value="t_name">Route Name</option>
				<option value="t_time">Time</option>
			</select>
			<a href="#" id="reverse_sort" class="sort_desc"><img src="/img/icon/sort_desc.png" /> DESC</a>
	</div>
</div>
<div class="grid_9">
	<div class="actions">
		<a href="/training/create" class="icon"><img src="/img/icon/training_plus.png"/>New Training Item</a>
		<a href="/training/browse" class="icon"><img src="/img/icon_cards_stack.png"/>Search Training Items</a>
	</div>
</div>
<div class="clear"></div>

<div class="grid_3">
	<div id="training_items_list">
		{{counter start=-1 print=false}}
		{{foreach from=$training_index_items item=training_item}}
		<div id="item_{{counter}}" class="training_item">
			{{if $training_item.r_name}}<div><a href="/routes/view?rid={{$training_item.t_rid}}" class="t_name icon"><img src="/img/icon/route.png" />{{$training_item.r_name}}</a></div>{{/if}}
				<div class="icon float_left"><img src="/img/icon/distance.png" /><span class="t_dist dist-val">{{$training_item.t_distance}} mi</span></div>
				<div class="icon float_right">{{$training_item.t_time|time_format}}<span class="t_time" style="display:none">{{$training_item.t_time}}</span> <img src="/img/icon/clock.png" /></div>
			<div class="clear"></div>
				<div class="icon float_left"><img src="/img/icon/dashboard.png" /><span class="t_pace">{{$training_item.t_pace}} mi/h</span></div>
				<div class="t_cal icon float_right"></div>
			<div class="clear"></div>
			<div class="t_date icon"><img src="/img/icon/calendar.png" />{{$training_item.t_date|date_format}}</div>
<!--			<div class="align_right"><a href="/training/view?tid={{$training_item.t_tid}}" class="icon"><img src="/img/icon/training.png" />View in Detail</a></div>-->
		</div>
		{{foreachelse}}
		<div>
			<p>You do not currently have any training items. <a href="/training/create" class="icon"><img src="/img/icon/training_plus.png" />Create</a> a new training item to enable advanced features.</p>
		</div>
		{{/foreach}}
	</div>
</div>

<div class="grid_9">
	<div id="chart_select" class="align_right">
		<p>Chart type: 		
			<input id="dist_rad" type="radio" checked="checked" /><label>Distance</label>
			<input id="pace_rad" type="radio" value="pac" /><label>Pace</label>
		</p>
	</div>
	<div id="chart_placeholder"><h5 style="color: red;">Training chart is currently disabled while we work through our server issues.</h5></div>
<!--	<div class="training_overview">-->
<!--		<p class="notice bold align_center mar_top_10 mar_bot_0">Overview</p>-->
<!--		<div id="chart_overview"></div>-->
<!--		<p class="notice bold align_center">Drag above in order to zoom / change the timeframe.</p>-->
<!--	</div>-->
</div>
<div class="clear"></div>

<script type="text/javascript">
sorter = {
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
		}
	};

	sorter.settings = {
			classes: {
				t_name: "alpha",
				t_dist: "numeric",
				t_time: "numeric",
				t_date: "date",
				t_pace: "numeric"
			},
			parent: "#training_items_list",
			item: ".training_item",
			sort_desc: -1,
			sort_key: "t_date"
	};

	$(document).ready(function(){
		$("#sort_select").change(function(){
			sorter.sort($(this).val());
		});
		$("#reverse_sort").click(function(){
			sorter.reverse();
			if($(this).hasClass("sort_asc")){
				$(this).html('<img src="/img/icon/sort_desc.png" /> DESC</a>');
				$(this).addClass("sort_desc");
				$(this).removeClass("sort_asc");
			}else{
				$(this).html('<img src="/img/icon/sort_asc.png" /> ASC</a>');
				$(this).addClass("sort_asc");
				$(this).removeClass("sort_desc");
			}
			return false;
		});
	});

	</script>
<!--CONTENT GOES HERE-->
<!--<div class="grid_12">-->
<!--	<h2 id="page-heading">Coming Soon</h2>-->
<!--</div>-->
<!--<div class="clear"></div>-->
<!---->
<!--<div class="grid_12">-->
<!--	<div class="actions"><a href="#" class="icon"><img src="/img/icon_trophy_plus.png" />New Goal</a></div>-->
<!--</div>-->
<!--<div class="clear"></div>-->
<!---->
<!--<div class="grid_3">-->
<!--	<div class="box">-->
<!--		<h2>Charts and Plots</h2>-->
<!--		<p>Weekly, monthly, etc. distance</p>-->
<!--		<p>Weekly, monthly, etc. pace</p>-->
<!--	</div>-->
<!--</div>-->
<!--<div class="grid_4">-->
<!--	<div class="box">-->
<!--		<h2>Recent Training Activity</h2>-->
<!--		<p>Similar to the other index pages, cid specific recent activity</p>-->
<!--	</div>-->
<!--</div>-->
<!--<div class="clear"></div>-->