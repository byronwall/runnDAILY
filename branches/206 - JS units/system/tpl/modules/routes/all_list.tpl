<ul class="recent_activity_list">
	{{foreach from=$all_route_list item=route_all}}
		<li class="recent_activity_item"><a href="/routes/view?rid={{$route_all->id}}">{{$route_all->name}}</a></li>
	{{foreachelse}}
		<li class="recent_activity_item">No routes, <a href="/routes/create">create a route</a>.</li>
	{{/foreach}}
</ul>