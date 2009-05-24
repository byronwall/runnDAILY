<div class="grid_12">
	<h2 id="page-heading">Goals</h2>
</div>
<div class="clear"></div>

<div class="grid_12">
	<div class="actions">
		<a href="/goals/create" class="icon"><img src="/img/icon.png" />New Goal</a>
	</div>
</div>
<div class="clear"></div>

<div class="grid_12">
	{{foreach from=$goal_list item=goal}}
		<p><a href="/goals/view?id={{$goal->id}}">{{if $goal->name}}{{$goal->name}}{{else}}Unamed{{/if}} ({{$goal->percent|round:"1"}}%)</a></p>
		{{if $goal->desc}}<p>{{$goal->desc}}</p>{{/if}}
	{{foreachelse}}
		<p>You do no currently have any goals! Create a <a href="/goals/create" class="icon"><img src="/img/icon.png" />New Goal</a>.</p>
	{{/foreach}}
</div>
<div class="clear"></div>