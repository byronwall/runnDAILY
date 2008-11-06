    <div class="map_wrapper">

      <div id="map" class="map">
<div id="map_nav">
        THIS WILL INCLUDE THE MAP NAVAGATION
      </div>
      </div>
      
    </div>
    
    <div id="other_content">
    
    <div id="distance_holder">
      distance wil go here
    </div>
    <div id="map_control_panel">
      <a href="#" onclick="clearAllPoints();return false;">clear all</a> <a href="#" onclick="undoLastPoint();return false;">undo</a> <a href="#" onclick="outAndBack()">out and back</a>
    </div>
    {{if $activeUser}} <a href="#TB_inline?height=300&amp;width=300&amp;inlineId=active_user_controls" title="save" class="thickbox">save</a> 
    <div id="active_user_controls" class="rh_login">
      <form action="php/routes.php?action=save" method="post" onsubmit="saveSubmit(this)">
        <input type="text" value="routeName" name="routeName"> <input type="submit" value="save"> <input type="hidden" name="distance"> <input type="hidden" name="points"> <input type="hidden" name="comments"> "
      </form>
    </div>
    {{/if}} 
    <form action="#" method="get">
      <input type="text" id="txt_address" value="purdue university"> <input type="button" onclick="show_address($('#txt_address').val());return false;" value="map it">
    </form>
    <a href="#" onclick="fullscreen_map();">full screen map</a> 
    </div>

<script type="text/javascript">

$(document).ready( function(){
load();
});
document.body.onunload = GUnload();

</script>


