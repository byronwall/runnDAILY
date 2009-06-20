<div class="grid_12">
	<h2 id="page-heading">{{if $goal->name}}{{$goal->name}}{{else}}Unamed{{/if}}</h2>
</div>
<div class="clear"></div>

<div class="grid_12">
	<div class="actions">
		<a href="#delete_modal" class="facebox icon"><img src="/img/icon/delete.png" />Delete Goal</a>
	</div>
</div>
<div class="clear"></div>

<div class="grid_12">
	{{if $goal->desc}}<p>Description: {{$goal->desc}}</p>{{/if}}
	<h4>Goal Details</h4>
	<p>Percent Complete: {{$goal->percent|round:"1"}}%</p>
	<p>{{$goal->start|date_format}} - {{$goal->end|date_format}}</p>
	{{foreach from=$goal->metadata item=metadata}}
		<p>{{$metadata.desc}}: {{$metadata.current|round:"2"}} / {{$metadata.value}}</p>
	{{/foreach}}
	<div id="delete_modal" style="display:none">
	<h5>Are you sure you wan to delete the current goal?</h5>
		<p class="alert_red">Once a goal has been deleted, there is no way to
		recover it! Only delete a goal that you are sure you no longer want!</p>
		<form method="POST" action="/goals/action_delete">
		<p>
			<input type="hidden" name="go_id" value="{{$goal->id}}" />
			<input type="submit" value="Delete" />
			<input type="button" value="Cancel" onclick="$.facebox.close()" />
		</p>
		</form>
	</div>
	<h4>Associated Training Items</h4>
	{{counter start=-1 print=false}}
	{{foreach from=$training_items item=training_item}}
	<div id="item_{{counter}}" class="training_item">
		{{if $training_item.r_name}}<div><a href="/routes/view?rid={{$training_item.t_rid}}" class="t_name icon"><img src="/img/icon/route.png" />{{$training_item.r_name}}</a></div>{{/if}}
			<div class="icon float_left"><img src="/img/icon/distance.png" /><span class="t_dist dist-val">{{$training_item.t_distance|round:"2"}} mi</span></div>
			<div class="t_date icon float_right">{{$training_item.t_date|date_format}} <img src="/img/icon/calendar.png" /></div>
		<div class="clear"></div>
			<div class="icon float_left"><img src="/img/icon/dashboard.png" /><span class="t_pace">{{$training_item.t_pace|round:"2"}} mi/h</span></div>
			<div class="icon float_right">{{$training_item.t_time|time_format}}<span class="t_time" style="display:none">{{$training_item.t_time}}</span> <img src="/img/icon/clock.png" /></div>
		<div class="clear"></div>
		{{if $training_item.t_comment}}
		<div class="align_left italic">{{$training_item.t_comment}}</div>
		{{/if}}
	</div>
	{{foreachelse}}
	<div>
		<p>You do not currently have any training items within this goal's date range. <a href="/training/create" class="icon"><img src="/img/icon/training_plus.png" />Create</a> a new training item to update this goal.</p>
	</div>
	{{/foreach}}
	
</div>
<div class="clear"></div>