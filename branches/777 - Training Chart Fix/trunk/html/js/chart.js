var Chart = {
	Data: {
		Distance_Max: 0,
		Pace_Max: 0,
		Date_Max: 0,
		Date_Min: 0		
	},
	ActivePoint: {
		
	},
	Type: "distance",
	CurrentRange: {
		xaxis:{

		},
		yaxis:{
			from: 0
		}		
	},
	OverviewRange: {
		xaxis:{
			
		},
		yaxis:{
			from: 0	
		}
	},
	LoadData: function(chart_data){
		Chart.Data = chart_data;
		
		Chart.CurrentRange.xaxis.to = Chart.Data.Date_Max;
		Chart.CurrentRange.xaxis.from = Chart.Data.Date_Max - (8.75 * 24 * 60 * 60 * 1000);
		Chart.CurrentRange.yaxis.to = Chart.Data.Distance_Max;
		
		Chart.OverviewRange.xaxis.to = Chart.Data.Date_Max;
		Chart.OverviewRange.xaxis.from = Chart.Data.Date_Min;
		Chart.OverviewRange.yaxis.to = Chart.Data.Distance_Max;
	},
	UpdateOverviewSelection: function(){
	  Chart.overview.setSelection(Chart.CurrentRange, true);	
	},
	UpdatePrimary: function(){
		if (Chart.Type == "distance"){
			ChartData = Chart.Data.Distance_Data;
			ChartOptions = ChartSettings.Primary.Distance;
			ChartMax = Chart.Data.Distance_Max;
		}else{
			ChartData = Chart.Data.Pace_Data;
			ChartOptions = ChartSettings.Primary.Pace;
			ChartMax = Chart.Data.Pace_Max;
		}
		NewMax = 1;
		$.each(ChartData, function(i){
			if(this[0] > Chart.CurrentRange.xaxis.from && this[0] < Chart.CurrentRange.xaxis.to && this[1] > Chart.NewMax){
				Chart.NewMax = Math.ceil(this[1]);
			}
	  });
	  if (Chart.Type == "distance"){
	  	Chart.Distance_Max = NewMax;
	  }else{
	  	Chart.Pace_Max = NewMax;
	  }
		Chart.primary = $.plot($("#primary_chart"), [ChartData], $.extend(true, {}, ChartOptions, {
		                  xaxis: { min: Chart.CurrentRange.xaxis.from, max: Chart.CurrentRange.xaxis.to },
		                  yaxis: { min: 0, max: ChartMax }
						}));
		$.each(ChartData, function(i){
			if (Chart.ActivePoint[i]){
				Chart.primary.highlight(0, i);
			}
		});
	},
	UpdateOverview: function(){
		if (Chart.Type == "distance"){
			ChartData = Chart.Data.Distance_Data;
			ChartOptions = ChartSettings.Overview.Distance;
			ChartMax = Chart.Data.Distance_Max;
		}else{
			ChartData = Chart.Data.Pace_Data;
			ChartOptions = ChartSettings.Overview.Pace;
			ChartMax = Chart.Data.Pace_Max;
		}
		Chart.overview = $.plot($("#overview_chart"), [ChartData], $.extend(true, {}, ChartOptions, {
		                  xaxis: { min: Chart.OverviewRange.xaxis.from, max: Chart.OverviewRange.xaxis.to },
		                  yaxis: { min: 0, max: ChartMax }
						}));
		Chart.UpdateOverviewSelection();
	},
	  ToggleItem: function (event, pos, item) {
      if (item) {
        if ($("#item_" + item.dataIndex).hasClass("active_row")){
            Chart.primary.unhighlight(item.series, item.datapoint);
        	$("#item_" + item.dataIndex).removeClass("active_row");
            Chart.ActivePoint[item.dataIndex] = 0;
        }else{
	        Chart.primary.highlight(item.series, item.datapoint);
        	$("#item_" + item.dataIndex).addClass("active_row");
        	Chart.ActivePoint[item.dataIndex] = 1;
        }
      }
  }
};

var ChartSettings = {
	AdjustPrimaryRange: function(xmin, xmax, ymax){
		if (Chart.Type == "distance"){
			ChartOptions = ChartSettings.Primary.Distance;
		}else{
			ChartOptions = ChartSettings.Primary.Pace;
		}
		ChartOptions.xaxis.min = xmin;
		ChartOptions.xaxis.max = xmax;
		ChartOptions.yaxis.max = ymax;
	},
	AdjustOverviewRange: function(xmin, xmax, ymax){
		if (Chart.Type == "distance"){
			ChartOptions = ChartSettings.Overview.Distance;
		}else{
			ChartOptions = ChartSettings.Overview.Pace;
		}
		ChartOptions.xaxis.min = xmin;
		ChartOptions.xaxis.max = xmax;
		ChartOptions.yaxis.max = ymax;
	},
	Primary:{
		Distance:{
			xaxis:	{
						mode: "time",
						timeformat: "%b %d",
						minTickSize: [1, "day"],
						min: 0,
						max: 1
					},
			yaxis:	{
						max: 1,
						min: 0
					},
			bars:	{
						show: true,
						barWidth: (24 * 60 * 60 * 1000),
						align: "center"
					},
			grid:	{
						clickable: true
					}
		},
		Pace: {
			xaxis:	{
						mode: "time",
						timeformat: "%b %d",
						minTickSize: [1, "day"],
						min: 0,
						max: 1
					},
			yaxis:	{
						max: 1,
						min: 0
					},
			points:	{
						show: true,
						radius: 5
					},
			lines:	{
						show: true
					},
			grid:	{
						clickable: true
					}
		}
	},

	Overview: {
		Distance: {
			xaxis:	{
						mode: "time",
						timeformat: "%b %d",
						minTickSize: [.5, "month"],
						min: 0,
						max: 1
					},
			yaxis:	{
						ticks: null,
						min: 0,
						max: 1
					},
			bars:	{
						show: true,
						align: "center"
					},
			grid:	{
						backgroundColor: "#ffffff"
					},
			selection:	{
							mode: "x"
						}
		},
		Pace: {
			xaxis:	{
						mode: "time",
						timeformat: "%b %d",
						minTickSize: [.5, "month"],
						min: 0,
						max: 1
					},
			yaxis:	{
						ticks: null,
						min: 0,
						max: 1
					},
			lines:	{
						show: true
					},
			grid:	{
						backgroundColor: "#ffffff"
					},
			selection:	{
							mode: "x"
						}
		}
	}
};