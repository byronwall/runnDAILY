var Chart = {
	Data: {
		Distance: {
			
		},
		Pace: {
			
		}		
	},
	Load: function(chart_holder_id) {
		Chart.instance = $.plot($("#" + chart_holder_id), [[15, 5.25]]);
		//console.log(Chart_Data);
	}
};

var ChartSettings = {
	Primary: {
		Distance: {
			xaxis: 	{
						//mode: "time",
						//timeformat: "%b %d",
						//minTickSize: [1, "day"]
					},
			yaxis: 	{
						min: 0
						//label: "Distance"
					},
			bars: 	{
						show: true,
						barWidth: (24 * 60 * 60 * 1000),
						align: "center"
					}
		}
	},
	Overview: {
		Distance: {
			
		},
		Pace: {
			
		}
	}
};

/*
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
			grid:	{
						backgroundColor: "#ffffff"
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
      });
	  plot = $.plot($("#chart_placeholder"), [dis],
              $.extend(true, {}, distance_plot_options, {
                  xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to },
                  yaxis: { min: 0, max: max_dist }
              }));
      $.each(dis, function(i){
	      if (active[i]){
              plot.highlight(0, i);
          }
      });
      
  });
  
  $("#chart_overview").bind("plotselected", function (event, ranges) {
      plot.setSelection(ranges);
  });

  $("#chart_placeholder").bind("plotclick", function (event, pos, item) {
      if (item) {
        if ($("#item_" + item.dataIndex).hasClass("active_row")){
            plot.unhighlight(item.series, item.datapoint);
        	$("#item_" + item.dataIndex).removeClass("active_row");
            active[item.dataIndex] = 0;
        }else{
	        plot.highlight(item.series, item.datapoint);
        	$("#item_" + item.dataIndex).addClass("active_row");
        	active[item.dataIndex] = 1;
        }
      }
  });
  * 
  * */