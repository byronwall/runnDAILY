<!--



-->
<h1>viewing the details for {{$route->name}}</h1>

<div class="map_wrapper">

      <div id="map" class="map">
<div id="map_nav">
        THIS WILL INCLUDE THE MAP NAVAGATION
      </div>
      </div>
      
    </div>
    
<script type="text/javascript">

$(document).ready( function(){
load();
var polyline = new GPolyline.fromEncoded({{$route->points}});
map.addOverlay(polyline);
});
document.body.onunload = GUnload();

</script>