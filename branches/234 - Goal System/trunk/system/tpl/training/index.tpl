<div class="grid_12">
	<h2 id="page-heading">Training</h2>
</div>
<div class="clear"></div>
<div class="grid_12">
	<div class="actions">
		<a href="/goals" class="icon"><img src="/img/icon/trophy_bronze.png"/>View Goals</a>
		<a href="/goals/create" class="icon"><img src="/img/icon/trophy_plus.png"/>New Goal</a>
		<a href="/training/summary?modal=true" class="icon facebox"><img src="/img/icon/training_summary.png"/>View Training Summary</a>
		<a href="/training/create" class="icon"><img src="/img/icon/training_plus.png"/>New Training Item</a>
<!--		<a href="/training/browse" class="icon"><img src="/img/icon_cards_stack.png"/>Search Training Items</a>-->
	</div>
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
	<div id="training_items_list">
		{{counter start=-1 print=false}}
		{{foreach from=$training_index_items item=training_item}}
		<div id="item_{{counter}}" class="training_item">
			{{if $training_item.r_name}}<div><a href="/routes/view?rid={{$training_item.t_rid}}" class="t_name icon"><img src="/img/icon/route.png" />{{$training_item.r_name}}</a></div>{{/if}}
				<div class="icon float_left"><img src="/img/icon/distance.png" /><span class="t_dist dist-val">{{$training_item.t_distance|round:"2"}} mi</span></div>
			<div class="clear"></div>
				<div class="t_date icon float_right">{{$training_item.t_date|date_format}} <img src="/img/icon/calendar.png" /></div>
				<div class="icon float_left"><img src="/img/icon/dashboard.png" /><span class="t_pace">{{$training_item.t_pace|round:"2"}} mi/h</span></div>
			<div class="clear"></div>
				<div class="icon align_right">{{$training_item.t_time|time_format}}<span class="t_time" style="display:none">{{$training_item.t_time}}</span> <img src="/img/icon/clock.png" /></div>
				{{if $training_item.t_comment}}
				<div class="align_left italic">{{$training_item.t_comment}}</div>
				{{/if}}
				<div class="align_right"><a href="/training/edit?tid={{$training_item.t_tid}}&modal=true" class="facebox icon"><img src="/img/icon/training_pencil.png" />Edit / Delete</a></div>
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
			<input id="distance_radio" type="radio" name="chart_type" value="dis" checked="checked" /><label>Distance</label>
			<input id="pace_radio" type="radio" name="chart_type" value="pac" /><label>Pace</label>
		</p>
	</div>
	<div id="primary_chart"></div>
	<div class="training_overview">
		<p class="notice bold align_center mar_top_10 mar_bot_0">Overview</p>
		<div id="overview_chart"></div>
		<p class="notice bold align_center">Drag above in order to zoom / change the timeframe.</p>
	</div>
</div>
<div class="clear"></div>
<script src="/js/chart.js" type="text/javascript"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$.sorter.add("#training_items_list",{
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
			sort_key: "t_date",
			reverse: "#reverse_sort",
			selector: "#sort_select"
		});
		$("#overview_chart").bind("plotselected", function(event, ranges) {
			Chart.CurrentRange = ranges;
			Chart.UpdatePrimary();
			Chart.UpdateOverviewSelection();
		});

		  $("#primary_chart").bind("plotclick", function (event, pos, item) {
			  Chart.ToggleItem(event, pos, item);
		  });

		  $("#distance_radio").click(function(){
			  Chart.Type = "distance";
			Chart.UpdatePrimary();
			Chart.UpdateOverview();
			  });
		  $("#pace_radio").click(function(){
			  Chart.Type = "pace";
			Chart.UpdatePrimary();
			Chart.UpdateOverview();
			  });
		
		Chart.LoadData({{$JSON_Chart_Data}});
		Chart.UpdatePrimary();
		Chart.UpdateOverview();
	});
</script>