<div class="grid_12">
	<h2 id="page-heading">Site Statistics</h2>
</div>
<div class="clear"></div>
<div class="grid_12">
	<div class="actions">
	</div>
</div>
<div class="clear"></div>

<div class="grid_12">
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
</div>

<div class="clear"></div>