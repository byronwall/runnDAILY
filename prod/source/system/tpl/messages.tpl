<div class="grid_12">
	<h2 id="page-heading">Messages</h2>
</div>
<div class="clear"></div>

<div class="grid_2">
	<p>{{$currentUser->msg_new}} New Message{{if $currentUser->msg_new != 1}}s{{/if}}</p>
</div>

<div class="grid_10">
	<div class="actions">
		<a href="#" class="icon"><img src="/img/icon_mail_plus.png" />New Message</a>
		<a href="#" class="icon"><img src="/img/icon_inbox_exclamation.png" />Mark Read</a>
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

