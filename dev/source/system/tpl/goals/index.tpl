<div class="grid_12">
	<h2 id="page-heading">Goals</h2>
</div>
<div class="clear"></div>

<div class="grid_12">
	<div class="actions">
		<a href="/goals/create" class="icon"><img src="/img/icon/trophy_plus.png" />New Goal</a>
	</div>
</div>
<div class="clear"></div>

<div class="grid_12">
	<h2>Active Goals</h2>
	{{foreach from=$goal_list.active item=goal}}
		<p><a href="/goals/view/{{$goal->id}}/{{$goal->name}}" class="icon">{{if $goal->percent == 100}}<img src="/img/icon/trophy.png" />{{else}}<img src="/img/icon/trophy_silver.png" />{{/if}}{{if $goal->name}}{{$goal->name}}{{else}}Unamed{{/if}} ({{$goal->percent|round:"1"}}%)</a></p>
		<p>Ends {{$goal->end|date_format}}</p>
		{{if $goal->desc}}<p>{{$goal->desc}}</p>{{/if}}
	{{foreachelse}}
		<p>You do no have any active goals! Create a <a href="/goals/create" class="icon"><img src="/img/icon/trophy_plus.png" />New Goal</a>.</p>
	{{/foreach}}
	{{if $goal_list.past}}
	<h2>Past Goals</h2>
	{{foreach from=$goal_list.past item=goal}}
		<p><a href="/goals/view/{{$goal->id}}/{{$goal->name}}" class="icon">{{if $goal->percent == 100}}<img src="/img/icon/trophy.png" />{{else}}<img src="/img/icon/trophy_silver.png" />{{/if}}{{if $goal->name}}{{$goal->name}}{{else}}Unamed{{/if}} ({{$goal->percent|round:"1"}}%)</a></p>
		{{if $goal->desc}}<p>{{$goal->desc}}</p>{{/if}}
	{{/foreach}}
	{{/if}}
</div>
<div class="clear"></div>