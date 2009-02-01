<h1>site stats</h1>

{{foreach from=$stats item=stat}}
<li>
	<div>date:{{$stat->date|date_format}}</div>
	<div>users:{{$stat->users}}</div>
	<div>routes:{{$stat->routes}}</div>
	<div>parent routes:{{$stat->routes_parent}}</div>
	<div>training entries:{{$stat->trainings}}</div>
</li>
{{foreachelse}}
No stats found!
{{/foreach}}