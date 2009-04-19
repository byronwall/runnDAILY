{{foreach from=$notifications->getNotifications() item=notification}}
<div class="grid_12 notification">
	<p>
		{{$notification->message}}
		{{if $notification->persist == 2}}
		<a href="#" class="notify" rel="{{$notification->id}}">remove</a>
		{{else}}
		<a href="#" class="notify">remove</a>
		{{/if}}
	</p>
</div>
<div class="clear"></div>
{{/foreach}}