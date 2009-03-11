<div id="monthly_distance_plot_placeholder"></div>

<script language="javascript" type="text/javascript">
$(function () {
	var d1 = [];
{{foreach from=$training_plot_data item=daily_data key=day_index}}
	d1.push([{{$day_index}},{{$daily_data}}]);
{{/foreach}}
	$.plot($("#monthly_distance_plot_placeholder"), [
		{label: "Distance", data: d1}
		], {
			bars: {show: true},
			xaxis: {min: 1, max: 31},
			yaxis: {min: 0}
		}
	);
});
</script>
	