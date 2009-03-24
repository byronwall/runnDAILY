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
	<div id="sort_options" class="align_right">
		<form>
			<label>Sort by: </label>
			<select id="sort_select">
				<option value="t_date">Date</option>
				<option value="t_dist">Distance</option>
				<option value="t_pace">Pace</option>
				<option value="t_cal">Calories</option>
				<option value="t_name">Route Name</option>
				<option value="t_time">Time</option>
			</select>
		</form>
	</div>
	<div id="training_items_list">
		{{counter start=-1 print=false}}
		{{foreach from=$training_index_items item=training_item}}
		<div id="item_{{counter}}" class="training_item">
			<p><a href="/routes/view?rid={{$training_item.t_rid}}" class="t_name">{{$training_item.r_name}}</a> (<span class="t_dist dist-mi">{{$training_item.t_distance}} mi</span>.)</p>
			<p><span class="t_time">{{$training_item.t_time}}</span> / <span class="t_pace">{{$training_item.t_pace}}</span> / Cal</p>
			<p class="t_date">{{$training_item.t_date|date_format}}</p>
			<p><a href="/training/view?tid={{$training_item.t_tid}}">View in Detail</a></p>
		</div>
		{{foreachelse}}
		<div>
			<p>You do not currently have any training items.</p>
		</div>
		{{/foreach}}
	</div>
</div>

<div class="grid_9">
	<div id="chart_select" class="align_right">
		<form>
		<p>Chart type: 		
			<input type="radio" name="chart_type" value="dis" checked="checked" /><label for="chart_distance">Distance</label>
			<input type="radio" name="chart_type" value="pac" /><label for="chart_pace">Pace</label>
			<input type="radio" name="chart_type" value="cal" /><label for="chart_calories">Calories</label>
		</p>
		</form>
	</div>
	<div id="chart_placeholder"></div>
	<div id="chart_overview"></div>
</div>
<div class="clear"></div>

<script type="text/javascript">
$(document).ready(function(){
//console.log({{$json_training_items}});
	var encoded = {{$json_training_items}};
	var dis = encoded.distance;
	var active = [];
	
	for(i = 0; i < dis.length; i++){
		active[i] = 0;
	};
	
	var max_h = encoded.max_dis;
	var min_date = encoded.min_date;
	var max_date = encoded.max_date;
	var distance_plot_options = {
			xaxis:	{
						mode: "time",
						timeformat: "%b %d",
						//tickSize: [1, "day"],
						minTickSize: [1, "day"],
						min: (max_date - (8.75 * 24 * 60 * 60 * 1000)),
						max: max_date
					},
			yaxis:	{
						max: max_h,
						min: 0,
						label: "Distance"
					},
			bars:	{
						show: true,
						barWidth: (24 * 60 * 60 * 1000),
						align: "center"
					},
			grid:	{
						clickable: true
					}
	};
	
	var plot = $.plot($("#chart_placeholder"), [dis], distance_plot_options);

	var distance_overview_options = {
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
	};
	
	var overview = $.plot($("#chart_overview"), [dis], distance_overview_options);
	overview.setSelection({ xaxis: { from: (max_date - (8.75 * 24 * 60 * 60 * 1000)), to: max_date } });
  $("#chart_placeholder").bind("plotselected", function(event, ranges) {
      overview.setSelection(ranges, true);
      var max_dist = 1;
		$.each(dis, function(i){
			if(this[0] > ranges.xaxis.from && this[0] < ranges.xaxis.to && this[1] > max_dist){
				max_dist = Math.ceil(this[1]);
			}
	      if (active[i]){
              plot.highlight(0, i);
          }
      });
	  plot = $.plot($("#chart_placeholder"), [dis],
              $.extend(true, {}, distance_plot_options, {
                  xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to },
                  yaxis: { min: 0, max: max_dist }
              }));
      
  });
  
  $("#chart_overview").bind("plotselected", function (event, ranges) {
      plot.setSelection(ranges);
  });

  $("#chart_placeholder").bind("plotclick", function (event, pos, item) {
      //console.log("You clicked at " + item.dataIndex);
      // secondary axis coordinates if present are in pos.x2, pos.y2,
      // if you need global screen coordinates, they are pos.pageX, pos.pageY
     // console.log("#item_" + item.dataIndex);
      //$("#item_" + item.dataIndex).addClass("active_row");
      if (item) {
        plot.highlight(item.series, item.datapoint);
        //console.log("#item_");
        if (!active[item.dataIndex]){
        active[item.dataIndex] = 1;
        $("#item_" + item.dataIndex).addClass("active_row");
        }else{
            plot.unhighlight(item.series, item.datapoint);
        	$("#item_" + item.dataIndex).removeClass("active_row");
            active[item.dataIndex] = 0;
        }
        //alert(item.dataIndex);
      }
    	//else{
 //         $("div[id*='item']").removeClass("active_row");
  //        for(i = 0; i < dis.length; i++){
 //             if (active[i]){
  //                plot.unhighlight(0, i);
  //                active[i] = 0;
 //             }
    //      };
   // $("input[name*='man']").val("has man in it!");
      //console.log(active);
  });
  });

sorter = {
	sort: function(key){
		if(!sorter.settings.classes[key]) return false;

		if(sorter.settings.sort_key == key){
			sorter.settings.sort_desc = -sorter.settings.sort_desc;
		}
		else{
			sorter.settings.sort_desc = 1;
			sorter.settings.sort_key = key;
		}
		var items = $(sorter.settings.item, sorter.settings.parent).get();
		items.sort(function(a, b) {
			var a_val = $(a).find("."+key).eq(0).text();
			var b_val = $(b).find("."+key).eq(0).text();

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
			return 0
		});
		$.each(items, function(){
			$(sorter.settings.parent).append(this);
		});
	},
	reverse: function(){
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
		sort_desc: 1,
		sort_key: null
};

$(document).ready(function(){
	$("#sort_select").change(function(){
		sorter.sort($(this).val());
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