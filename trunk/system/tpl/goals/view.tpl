<div class="grid_12">
	<h2 id="page-heading">{{if $goal->name}}{{$goal->name}}{{else}}Unamed{{/if}}</h2>
</div>
<div class="clear"></div>

<div class="grid_12">
	<div class="actions">
		<a href="#" class="icon"><img src="/img/icon.png" />Edit Goal</a>
		<a href="#" class="icon"><img src="/img/icon.png" />Delete Goal</a>
	</div>
</div>
<div class="clear"></div>

<div class="grid_12">
	{{if $goal->desc}}<p>{{$goal->desc}}</p>{{/if}}
	<h5>Goal Details</h5>
	<p>Percent Complete: {{$goal->percent|round:"1"}}%</p>
	<p>{{$goal->start|date_format}} - {{$goal->end|date_format}}</p>
	{{foreach from=$goal->metadata item=metadata}}
		<p>{{$metadata.desc}}: {{$metadata.current|round:"2"}} / {{$metadata.value}}</p>
	{{/foreach}}
	
	<h5>Associated Training Items</h5>
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
	</div>
	{{foreachelse}}
	<div>
		<p>You do not currently have any training items within this goal's date range. <a href="/training/create" class="icon"><img src="/img/icon/training_plus.png" />Create</a> a new training item to update this goal.</p>
	</div>
	{{/foreach}}
	
</div>
<div class="clear"></div>