<div class="grid_12">
	<h2 id="page-heading">Messages</h2>
</div>
<div class="clear"></div>

<div class="grid_2">
	<p>{{$currentUser->msg_new}} New Message{{if $currentUser->msg_new != 1}}s{{/if}}</p>
</div>

<div class="grid_10">
<div class="actions">
<a href="#">New</a>
<a href="#">Mark Read</a>
</div>
</div>

<div class="clear"></div>
<div class="grid_12">
	<h3>Inbox</h3>
	<div class="block">
	<table>
		<thead>
			<tr>
				<td />
				<th>From</th>
				<th>Date</th>
				<th>Subject</th>
			</tr>
		</thead>
		<tbody>
			{{foreach from=$messages_to item=message}}
			
			<tr>
				<td>
					<a href="#">Reply</a>
					{{if $message->new}}
					<a href="#">Mark Read</a>
					{{/if}}
					<a href="#">Delete</a>
				</td>
				<td>{{$message->user->username}}</td>
				<td>{{$message->date|date_format}}</td>
				<td>Subject</td>
			</tr>
			<tr class="odd">
				<td />
				<td colspan="3">{{$message->msg}}</td>
			</tr>
			
			{{foreachelse}}
			
			<tr>
				<td colspan="4">There are no messages in your inbox.</td>
			</tr>
			
			{{/foreach}}
			
		</tbody>
	</table>
	</div>
</div>
<div class="clear"></div>

<div class="grid_12">
	<h3>Outbox</h3>
	<div class="block">
	<table>
		<thead>
			<tr>
				<td />
				<th>To</th>
				<th>Date</th>
				<th>Subject</th>
			</tr>
		</thead>
		<tbody>
			{{foreach from=$messages_from item=message}}
			
			<tr>
				<td>
					{{if $message->new}}
					<a href="#">Mark Read</a>
					{{/if}}
					<a href="#">Delete</a>
				</td>
				<td>{{$message->user->username}}</td>
				<td>{{$message->date|date_format}}</td>
				<td>Subject</td>
			</tr>
			<tr class="odd">
				<td />
				<td colspan="3">{{$message->msg}}</td>
			</tr>
			
			{{foreachelse}}
			
			<tr>
				<td colspan="4">There are no messages in your inbox.</td>
			</tr>
			
			{{/foreach}}
			
		</tbody>
	</table>
	</div>
</div>
<div class="clear"></div>

<!--<ul>-->
<!--	<h2>to you</h2>-->
<!--	{{foreach from=$messages_to item=message}}-->
<!--		<li>-->
<!--			<div>from: {{$message->user->username}}</div>-->
<!--			<div>date: {{$message->date|date_format}}</div>-->
<!--			<div>message: {{$message->msg}}</div>-->
<!--			<ul class="message_action">-->
<!--				{{if $message->new}}-->
<!--					<li><a href="#">coming: mark as read</a></li>-->
<!--				{{/if}}-->
<!--				<li><a href="#">coming: reply to message</a></li>-->
<!--				<li><a href="#">coming: delete message</a></li>-->
<!--				-->
<!--			</ul>-->
<!--		</li>-->
<!--	{{/foreach}}-->
<!--</ul>-->
<!---->
<!--<ul>-->
<!--	<h2>from you</h2>-->
<!--	{{foreach from=$messages_from item=message}}-->
<!--		<li>-->
<!--			<div>from: {{$message->user->username}}</div>-->
<!--			<div>date: {{$message->date|date_format}}</div>-->
<!--			<div>message: {{$message->msg}}</div>-->
<!--			<ul class="message_action">-->
<!--				{{if $message->new}}-->
<!--					<li><a href="#">coming: mark as read</a></li>-->
<!--				{{/if}}-->
<!--				<li><a href="#">coming: reply to message</a></li>-->
<!--				<li><a href="#">coming: delete message</a></li>-->
<!--				-->
<!--			</ul>-->
<!--		</li>-->
<!--	{{/foreach}}-->
<!--	-->
<!--</ul>-->