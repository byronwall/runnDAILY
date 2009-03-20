{{foreach from=$notifications->getNotifications() item=notification}}
<div class="grid_12 notification">
	<p>
		{{$notification->message}}
		<a href="#" class="notify" rel="{{$notification->id}}">remove</a>
	</p>
</div>
<div class="clear"></div>
{{/foreach}}