{{foreach from=$t_items item=t_item}}
	<li class="route_recent_list">
		<div>distance: {{$t_item->distance}}</div>
		<div>time: {{$t_item->time|time_format}}</div>
		<div>date: {{$t_item->date|date_format}}</div>
		<div><a href="/training/view.php?tid={{$t_item->tid}}">view entry</a></div>
	</li>
{{/foreach}}
{{if count($t_items)}}
	<li class="route_recent_list">
		<div class="route_item_content"><a href="/training/browse.php?uid={{$uid}}&page={{$page_no}}&format=ajax" class="ajax">see more in this table</a></div>
	</li>
{{/if}}