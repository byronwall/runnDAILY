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
	<p><a href="/messages/view_convo/{{$convo.msg_convo_id}}_modal" class="facebox icon">{{if $convo.msg_uid_to == $currentUser->uid && $convo.msg_new}}<img src="/img/icon/mail_new.png" />{{else}}<img src="/img/icon/mail.png" />{{/if}}{{$convo.msg_subject}}</a> 	with
	{{if $currentUser->uid == $convo.msg_uid_to}}
		<a href="/community/view_user/{{$convo.msg_uid_from}}" class="icon"><img src="/img/icon/user_friend.png" />{{$convo.msg_username_from}}</a>
	{{/if}}
	{{if $currentUser->uid == $convo.msg_uid_from}}
		<a href="/community/view_user/{{$convo.msg_uid_to}}" class="icon"><img src="/img/icon/user_friend.png" />{{$convo.msg_username_to}}</a>
	{{/if}}</p>
	<p>last activity: {{$convo.msg_date_created|date_format:"l F j, Y g:i A"}}</p>
	{{foreachelse}}
	<p>You do not currently have any active conversations.</p>
	{{/foreach}}
</div>
<div class="clear"></div>