{{* This is the template for the index of the routes folder. *}}
<h1>Routes Page</h1>

<a href="/routes/create.php">create new route!</a>

<h2>list of routes on the site</h2>

<ul>
{{foreach from=$route_list item=route}}
<li>
	<a href="/routes/view.php?id={{$route->id}}">
		<img src="/lib/image_route.php?encoded={{$route->getEncodedString()}}">
		<ul>
			<li>view {{$route->name}}</li>
			<li>distance {{$route->distance}}</li>
			<li>created on {{$route->date_creation}}</li>
		</ul>
	</a>
</li>
{{/foreach}}
</ul>