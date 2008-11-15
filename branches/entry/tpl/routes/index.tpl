{{* This is the template for the index of the routes folder. *}}
<h1>Routes Page</h1>

<a href="/routes/create.php">create new route!</a>

<h2>list of routes on the site</h2>

<ul>
{{foreach from=$route_list item=route}}
<li><a href="/routes/view.php?id={{$route->id}}">view {{$route->name}}</a></li>
{{/foreach}}
</ul>