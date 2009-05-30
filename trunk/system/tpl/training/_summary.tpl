<h4>Training Summary</h4>
	<p>A brief summary of your total distance, total time, and average pace is listed below for the selected time periods.</p>

<h5>This Week</h5>
	<p class="bold">{{$this_week->start|date_format:"l, F d"}} to {{$this_week->end|date_format:"l, F d"}}</p>
{{if $data_this_week.count}}
	<p>Total Distance: <span class="t_dist dist-val">{{$data_this_week.dist|round:"2"}} mi</span></p>
	<p>Total Time: {{$data_this_week.time|time_format}}</p>
	<p>Average Pace: {{$data_this_week.pace/$data_this_week.count|round:"2"}} mph</p>
{{else}}
	<p>No data for this week.</p>
{{/if}}

<h5>Last Week</h5>
	<p class="bold">{{$last_week->start|date_format:"l, F d"}} to {{$last_week->end|date_format:"l, F d"}}</p>
{{if $data_last_week.count}}
	<p>Total Distance: <span class="t_dist dist-val">{{$data_last_week.dist|round:"2"}} mi</span></p>
	<p>Total Time: {{$data_last_week.time|time_format}}</p>
	<p>Average Pace: {{$data_last_week.pace/$data_last_week.count|round:"2"}} mph</p>
{{else}}
	<p>No data for last week.</p>
{{/if}}

<h5>Overall</h5>
{{if $overall.count}}
	<p>Total Distance: <span class="t_dist dist-val">{{$overall.dist|round:"2"}} mi</span></p>
	<p>Total Time: {{$overall.time|time_format}}</p>
	<p>Average Pace: {{$overall.pace/$overall.count|round:"2"}} mph</p>
{{else}}
	<p>No data.</p>
{{/if}}