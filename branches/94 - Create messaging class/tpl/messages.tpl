<h1>your messages</h1>

<ul>
<h2>to you</h2>
{{foreach from=$messages_to item=message}}
<li>{{$message->msg}}</li>
{{/foreach}}
</ul>