<div class="grid_12">
	<h2 id="page-heading">Messages</h2>
</div>
<div class="clear"></div>

<div class="grid_12">
	<div class="actions">
		<a href="/messages/create" class="facebox icon"><img src="/img/icon/mail_plus.png" />New Message</a>
	</div>
</div>
<div class="clear"></div>

<div class="grid_12">
	<h4>Conversations</h4>
	{{foreach from=$convo_list item=convo}}
	<p><a href="/messages/view_convo/{{$convo.msg_convo_id}}" class="facebox">{{$convo.msg_convo_id}}</a></p>
	<p>{{$convo.msg_subject}}</p>
	<p>{{$convo.msg_date_created|date_format:"l F j, Y g:i A"}}</p>
	<p>
	with
	{{if $currentUser->uid == $convo.msg_uid_to}}
		{{$convo.msg_username_from}}
	{{/if}}
	{{if $currentUser->uid == $convo.msg_uid_from}}
		{{$convo.msg_username_to}}
	{{/if}}
	</p>
	{{foreachelse}}
	<p>You do not currently have any active conversations.</p>
	{{/foreach}}
</div>
<div class="clear"></div>