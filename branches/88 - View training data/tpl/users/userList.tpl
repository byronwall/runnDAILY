<ul>

{{foreach from=$users item=user}}
    <li><a href="users.php?username={{$user->username}}&action=profile">view details for {{$user->username}}</a></li>
{{foreachelse}}
<li>Nothing was found</li>
{{/foreach}}


</ul>