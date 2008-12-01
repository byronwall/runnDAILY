<h1>{{$user->username}}</h1>

This page will soon show all sorts of content for {{$user->username}}

<h2>routes that {{$user->username}} has created.</h2>

<div id="user_route_list">

<ul>
{{foreach from=$user_routes item=route}}

<li><a href="routes.php?action=view&id={{$route->id}}">{{$route->name}}</a></li>
{{foreachelse}}
no routes to display
{{/foreach}}
</ul>
</div>

<h2>recent times that {{$user->username}} has posted.</h2>