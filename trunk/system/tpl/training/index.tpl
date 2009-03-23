<div class="grid_12">
	<h2 id="page-heading">Training</h2>
</div>
<div class="clear"></div>

<div class="grid_12">
	<div class="actions">
		<a href="/training/create" class="icon"><img src="/img/icon_training_plus.png"/>New Training Item</a>
		<a href="/training/browse" class="icon"><img src="/img/icon_cards_stack.png"/>Browse Training Items</a>
	</div>
</div>
<div class="clear"></div>

<div class="grid_3">
	{{foreach from=$training_index_items item=training_item}}
	<div id="item_{{$training_item.t_tid}}" class="training_index_item">
		<p><a href="/routes/view?rid={{$training_item.t_rid}}">{{$training_item.r_name}}</a> ({{$training_item.t_distance}} mi.)</p>
		<p>{{$training_item.t_time}} / {{$training_item.t_pace}} / Cal</p>
		<p>{{$training_item.t_date|date_format}}</p>
		<p><a href="/training/view?tid={{$training_item.t_tid}}">View in Detail</a></p>
	</div>
	{{foreachelse}}
	<div>
		<p>You do not currently have any training items.</p>
	</div>
	{{/foreach}}
</div>

<div class="grid_9">
	<div id="chart_placeholder"></div>
	<div id="chart_overview"></div>
</div>
<div class="clear"></div>

<script type="text/javascript">
	console.log({{$json_training_items}});
	var encoded = {{$json_training_items}}
	var dis = encoded.distance;
	var max_h = encoded.max_dis;
	var min_date = encoded.min_date;
	var max_date = encoded.max_date;
	var plot = $.plot($("#chart_placeholder"), [dis],
			{
				xaxis:	{
							mode: "time",
							timeformat: "%b %d",
							//tickSize: [1, "day"],
							minTickSize: [1, "day"],
							//min: min_date
						},
				yaxis:	{
							max: max_h,
							min: 0
						},
				bars:	{
							show: true,
							barWidth: (24 * 60 * 60 * 1000),
							align: "center"
						}
			}
	);
	var overview = $.plot($("#chart_overview"), [dis],
			{
				xaxis:	{
							mode: "time",
							timeformat: "%b %d",
							//tickSize: [1, "day"],
							minTickSize: [.5, "month"],
							min: min_date,
							max: max_date
						},
				yaxis:	{
							ticks: null,
							min: 0,
							max: max_h
						},
				bars:	{
							show: true,
							//barWidth: (24 * 60 * 60 * 1000),
							align: "center"
						},
				selection:	{
								mode: "x"
							}
			}
	);
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