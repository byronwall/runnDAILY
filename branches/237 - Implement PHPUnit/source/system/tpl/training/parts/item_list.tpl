{{foreach from=$t_items item=t_item}}
	<li class="route_recent_list">
		<div>distance: {{$t_item->distance}}</div>
		<div>time: {{$t_item->time|time_format}}</div>
		<div>date: {{$t_item->date|date_format}}</div>
		<div><a href="/training/view?tid={{$t_item->tid}}">view entry</a></div>
	</li>
{{/foreach}}