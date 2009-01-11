<h1>messages</h1>

<ul class="message_cp">
	<h2>message control panel</h2>
	<li><a href="#">coming: compose message</a></li>
</ul>

<ul>
	<h2>to you</h2>
	{{foreach from=$messages_to item=message}}
		<li>
			<div>from: {{$message->user->username}}</div>
			<div>date: {{$message->date|date_format}}</div>
			<div>message: {{$message->msg}}</div>
			<ul class="message_action">
				{{if $message->new}}
					<li><a href="#">coming: mark as read</a></li>
				{{/if}}
				<li><a href="#">coming: reply to message</a></li>
				<li><a href="#">coming: delete message</a></li>
				
			</ul>
		</li>
	{{/foreach}}
</ul>

<ul>
	<h2>from you</h2>
	{{foreach from=$messages_from item=message}}
		<li>
			<div>from: {{$message->user->username}}</div>
			<div>date: {{$message->date|date_format}}</div>
			<div>message: {{$message->msg}}</div>
			<ul class="message_action">
				{{if $message->new}}
					<li><a href="#">coming: mark as read</a></li>
				{{/if}}
				<li><a href="#">coming: reply to message</a></li>
				<li><a href="#">coming: delete message</a></li>
				
			</ul>
		</li>
	{{/foreach}}
	
</ul>