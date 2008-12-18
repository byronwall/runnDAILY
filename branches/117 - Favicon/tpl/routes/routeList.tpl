<!-- 
This template is used to display a list of routes.

$routes : this is an array of Route types

 -->

<ul>
{{foreach from=$routes item=route}}
    <div>
    	<a href="routes.php?action=view&id={{$route->id}}">view {{$route->name}}</a><br>
    	distance: {{$route->distance|string_format:"%.2f"}} miles    
    </div>
{{foreachelse}}
<li>Nothing was found</li>
{{/foreach}}
</ul>
