<h4>Training Summary</h4>
<p>A brief summary of your total distance, total time, and average pace is listed below for selected time periods.</p>

{{if $data_this_week}}
<h5>This Week</h5>
<p class="bold">{{$this_week->start|date_format:"l, F d"}} to {{$this_week->end|date_format:"l, F d"}}</p>
<p>Total Distance: <span class="t_dist dist-val">{{$data_this_week.dist|round:"2"}} mi</span></p>
<p>Total Time: {{$data_this_week.time|time_format}}</p>
<p>Average Pace: {{$data_this_week.pace/$data_this_week.count|round:"2"}} mph</p>
{{/if}}

{{if $data_last_week}}
<h5>Last Week</h5>
<p class="bold">{{$last_week->start|date_format:"l, F d"}} to {{$last_week->end|date_format:"l, F d"}}</p>
<p>Distance: <span class="t_dist dist-val">{{$data_last_week.dist|round:"2"}} mi</span></p>
<p>Total Time: {{$data_last_week.time|time_format}}</p>
<p>Average Pace: {{$data_last_week.pace/$data_last_week.count|round:"2"}} mph</p>
{{/if}}