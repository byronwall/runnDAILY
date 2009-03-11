<div id="monthly_distance_plot_placeholder"></div>

<script language="javascript" type="text/javascript">
$(function () {
	var d1 = [];
	var d2 = [];
{{foreach from=$training_plot_distance_data item=distance_data key=day_index}}
	d1.push([{{$day_index}},{{$distance_data}}]);
{{/foreach}}
{{foreach from=$training_plot_pace_data item=pace_data key=day_index}}
	d2.push([{{$day_index}},{{$pace_data}}]);
{{/foreach}}
	$.plot($("#monthly_distance_plot_placeholder"), [
		{label: "Distance", data: d1, bars: {show: true}},
		{label: "Pace", data: d2, lines: {show: true}}
		], {
			xaxis: {min: 1, max: 31},
			yaxis: {min: 0}
		}
	);
});
</script>
	