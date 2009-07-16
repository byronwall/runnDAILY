<?php
function mod_string_format($string, $format) {
		return sprintf ( $format, $string );
	}
?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
    
    <!--SYTLE SHEETS-->
<!--	<link href="/css/reset.css" rel="stylesheet" type="text/css" />-->
<!--	<link href="/css/combine.css" rel="stylesheet" type="text/css" />-->
<!--    <link href="/css/facebox.css" rel="stylesheet" type="text/css" />-->
    <link href="/css/runndaily-9.6.18.css" rel="stylesheet" type="text/css">
    
    <!--FAVORITE ICON-->
    <link rel="icon" type="image/png" href="/img/favico.png">
    
    <!--JAVASCRIPT-->
<!--    <script src="/js/site.js" type="text/javascript"></script>-->
<!--    <script src="/js/jquery.facebox.js" type="text/javascript"></script>-->
<!--    <script src="/js/excanvas.js" type="text/javascript"></script>-->
<!--    <script src="/js/jquery.flot.js" type="text/javascript"></script>-->
<!--    <script src="/js/jquery.runndaily.js" type="text/javascript"></script>-->
    <script src="/js/runndaily-9.6.18.ycomp.js" type="text/javascript"></script>
    <!--TITLE-->
    <title><?php echo $this->_vars["page"]->title; ?></title>
</head>

<body id="<?php echo $this->_vars["engine"]->getCommonName(); ?>">
<div class="container_12 top">
<div class="grid_2 prefix_5 suffix_5 bg_top"><a href="/index"><img class="logo" src="/img/logo.png"></a></div>
<div class="clear"></div>
<div class="grid_12">
<ul class="header nav main">
		<li><a href="/index" class="icon"><img src="/img/icon/home.png" />Home</a></li>
		<li><a href="/routes/index" class="icon"><img src="/img/icon/route.png" />Routes</a>
			<ul>
				<li><a href="/routes/create">New Route</a></li>
<!--				<li><a href="/routes/browse">Search</a></li>-->
			</ul>
		</li>
		<li><a href="/training/index" class="icon"><img src="/img/icon/training.png" />Training</a>
			<ul>
					<li><a href="/training/create">New Training Item</a></li>
					<li><a href="/goals">View Goals</a></li>
					<li><a href="/goals/create">New Goal</a></li>
<!--					<li><a href="/training/browse">Search</a></li>-->
			</ul>
		</li>
		<li><a href="/community/index" class="icon"><img src="/img/icon/community.png" />Community</a>
			<ul>
				<li><a href="/confirmation">Requests</a></li>
			</ul>
		</li>
		
		<li><a href="/about" class="icon"><img src="/img/icon/runndaily.png"/>runnDAILY</a>
		<ul>
				<li><a href="/about/index">About Us</a></li>
				<li><a href="/about/contact">Contact</a></li>
				<li><a href="/about/credits">Credits</a></li>
		</ul>
		</li>
		<li class="secondary">
		<?php if($this->_vars["engine"]->requirePermission("PV__300")): ?>
			<a href="/community/view_user/<?php echo $this->_vars["currentUser"]->uid; ?>/<?php echo $this->_vars["currentUser"]->username; ?>" class="icon"><img src="/img/icon/user_friend.png" /><?php echo $this->_vars["currentUser"]->username; ?><?php if($this->_vars["currentUser"]->msg_new > 0): ?><img src="/img/icon/mail_new.png" /><?php endif ?></a>
		<ul>
				<li><a href="/messages" class="icon">Messages<?php if($this->_vars["currentUser"]->msg_new > 0): ?> (<?php echo $this->_vars["currentUser"]->msg_new; ?>)<?php endif ?></a></li>
				<li><a href="/settings" class="icon">Settings</a></li>
				<li><a href="/user/logout" class="icon">Logout</a></li>
		</ul>
		<?php else: ?>
		<li class="secondary"><a href="#login_modal" class="facebox icon"><img src="/img/icon_login.png" />Login</a></li>
		<li class="secondary"><a href="/register" class="icon"><img src="/img/icon/register.png" />Register</a></li>
		<?php endif ?> <?php if($this->_vars["engine"]->requirePermission("PV__100")): ?>
		<li class="secondary"><a href="/admin/index" class="icon"><img src="/img/icon_application_monitor.png" />Admin</a></li>
		<?php endif ?>
		<li class="secondary"><a href="/help/view/<?php echo $this->_vars["engine"]->getCommonName(); ?>" class="facebox icon"><img src="/img/icon/help.png" />Help</a></li>
		<li class="secondary"><a href="#feedback_modal" class="facebox icon"><img src="/img/icon/feedback.png" />Feedback </a></li>
</ul>
</div>
<div class="clear"></div>



<!--Template does not support tag for  yet-->
<div class="grid_12">
<?php if($this->_vars["is_edit"]): ?>
<h2 id="page-heading">Editing <?php echo $this->_vars["route_edit"]->name; ?></h2>
<?php else: ?>
<h2 id="page-heading">New Route</h2>
<?php endif ?>
</div>
<div class="clear"></div>

<div class="grid_12">
	<div class="actions">
		<a href="#" onclick="MapActions.clearAllPoints();return false;" class="icon"><img src="/img/icon/delete.png"/>Clear All Points</a>
		<a href="#" onclick="MapActions.undoLastPoint();return false;" class="icon"><img src="/img/icon_arrow_undo.png"/>Undo Last Point</a>
		<a href="#" onclick="MapActions.outAndBack(); return false;" class="icon"><img src="/img/icon/out_back.png"/>Out and Back</a>
		<a href="#" onclick="Display.toggle_fullscreen();return false;" class="icon"><img src="/img/icon/fullscreen.png"/>Full Screen</a>
		<a href="#settings_modal" class="facebox icon"><img src="/img/icon/settings.png" />Settings</a>
	</div>
</div>
<div class="clear"></div>

<div class="grid_2">
<div class="route_distance">
	<p class="r_distance_disp dist-num">0.00</p>
	<p class="units dist-unit">mi</p>
</div>
<hr>
<div class="" id="route_name_desc">
<div class="delete_box">
<h4>Route Name & Description</h4>
	<form action="/routes/action_create" method="post" id="r_form_save">
		<div id="route_error_box"></div>
		<p><label>Route Name: </label><input type="text" name="r_name" value="<?php echo $this->_vars["route_edit"]->name; ?>" class="field"/></p>
		<p><label>Description:</label></p>
		<p><textarea rows="3" name="r_description" class="field"><?php echo $this->_vars["route_edit"]->description; ?></textarea></p>
		<input type="hidden" name="r_distance" value=""/>
		<input type="hidden" name="r_points" value=""/>
		<input type="hidden" name="r_start_lat" value=""/>
		<input type="hidden" name="r_start_lng" value=""/>
		<?php if($this->_vars["engine"]->requirePermission("PV__300")): ?>
			<?php if($this->_vars["is_edit"]): ?>
				<input type="hidden" name="r_id" value="<?php echo $this->_vars["route_edit"]->id; ?>"/>
				<input type="hidden" name="action" value="update"/>
				<p><input type="submit" value="Update Route"/></p>
			<?php else: ?>
				<input type="hidden" name="action" value="save"/>
				<p><input type="submit" value="Create Route"/></p>
			<?php endif ?>
		<?php endif ?>
	</form>
</div>
</div>
<div class="" id="route_re_center">
	<h4>Re-center the Map</h4>
	<form action="#" method="get" onsubmit="Geocoder.showAddress('#txt_address');return false;" class="search">
		<p class="notice">You may re-center the map to an address, city, state, or ZIP.</p>
		<p><input type="text" id="txt_address" value="" class="field"></p>
		<p><input type="submit" value="Re-center"></p>
		<p id="location_msg" class=""></p>
	</form>
</div>
<!--<div class="" id="route_options">-->
<!--	<div class="delete_box">-->
<!--		<a href="#settings_modal" class="facebox icon">Settings</a>-->
<!--	</div>-->
<!--</div>-->

</div>

<div class="grid_10">
	<div id="r_map" class="map map_fix"></div>
</div>
<div id="results" style="display:none"></div>
<div id="map_overlay" style="display:none">
	<img src="/img/logo.png">
	<p><a href="#" onclick="MapActions.clearAllPoints();return false;" class="icon"><img src="/img/icon/delete.png"/>Clear All Points</a></p>
	<p><a href="#" onclick="MapActions.undoLastPoint();return false;" class="icon"><img src="/img/icon_arrow_undo.png"/>Undo Last Point</a></p>
	<p><a href="#" onclick="MapActions.outAndBack()" class="icon"><img src="/img/icon/out_back.png"/>Out and Back</a></p>
	<p><a href="#settings_modal" class="facebox icon"><img src="/img/icon/settings.png" />Settings</a></p>
	<p><a href="#route_name_desc" class="facebox icon"><img src="/img/icon/save.png" />Save</a></p>
	<p><a href="#" onclick="Display.toggle_fullscreen();return false;" class="icon"><img src="/img/icon/fullscreen.png"/>Close Full Screen</a></p>
	
	<div class="route_distance">
		<p class="r_distance_disp dist-num">0.00</p>
		<p class="units dist-unit">miles</p>
	</div>
</div>
<div class="clear"></div>

<div id="settings_modal" style="display: none">
	<h4>Additional Map Options</h4>
	<div id="errors_box" style="display:none"></div>
	<form action="/user/action_map_settings" method="post" id="r_form_settings">
		<p class="notice">Set a few options for the map!</p>
		<p>
			Map type:
			<select id="settings_map_type">
				<option value="G_NORMAL_MAP">Map</option>
				<option value="G_SATELLITE_MAP">Satellite</option>
				<option value="G_HYBRID_MAP">Hybrid</option>
				<option value="G_PHYSICAL_MAP">Terrain</option>
			</select>
		</p>
		<p><label>Mile Marker Distance: </label><input name="mile_dist" type="text" id="u_mile_marker" class="number" value="1.0"/ size="5"><span class="dist-unit"> mi</span></p>
		<p><label>Circular Radius: </label><input type="text" name="circ_dist" id="u_circle_dist" class="number" value="5.0" size="5"/><span class="dist-unit"> mi</span></p>
		<p><label>Display Circular Perimeter? </label><input name="circ_enable" type="checkbox" id="input_circle_show"/></p>
		<p><label>Follow Roads? </label><input type="checkbox" name="dir_enable" id="input_follow_roads"/></p>
		<p>
			<input type="button" value="Apply Once" onclick="check_apply()"/>
			<?php if($this->_vars["engine"]->requirePermission("PV__300")): ?>
			<input type="submit" value="Apply Default" />
			<?php endif ?>
		</p>
		<input type="hidden" name="map_settings" >
	</form>
</div>

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAYZcibhuwr8GMgCWYwqU-RxTwKQnnvD1T4H7IjqlIr-cK4JGBGBR9nTuCz-u_Of2k2UEZ7khhybXPyw" type="text/javascript"></script>
<!--<script src="/js/map.labeledmarker.js" type="text/javascript"></script>-->
<!--<script src="/js/map.js" type="text/javascript"></script>-->
<!--<script src="/js/PolylineEncoder.pack.js" type="text/javascript"></script>-->
<script src="/js/runndaily-maps-9.6.18.ycomp.js" type="text/javascript"></script>
<script type="text/javascript">

$(document).ready( function(){
	Map.load("r_map", Map.event_click, {full_height:true});
	GEvent.addListener(Map.instance, "singlerightclick",
		function(point, src, overlay){
			Directions.click(null, Map.instance.fromContainerPixelToLatLng(point), null);
		}
	);

	<?php if($this->_vars["currentUser"]->settings.map_settings): ?>
		MapSettings = $.extend({}, MapSettings, <?php echo $this->_vars["currentUser"]->settings.map_settings; ?>);
		Map.instance.setMapType(eval(MapSettings.MapType));
	<?php endif ?>
	
	<?php if(!$this->_vars["is_edit"] and !$this->_vars["currentUser"]->location_lat|@is_null): ?>
		Map.setHomeLocation(<?php echo $this->_vars["currentUser"]->location_lat; ?>, <?php echo $this->_vars["currentUser"]->location_lng; ?>);
	<?php endif ?>

	<?php if($this->_vars["is_edit"]): ?>
		MapData.loadRoute(<?php echo $this->_vars["route_edit"]->points; ?>, {
			draggable:true
		});
	<?php endif ?>

	$("#r_form_save").validate({
		rules: {
			r_name: {required: true}
		},
		messages: {
			r_name: {required: "Please enter a name."}
		},
		errorLabelContainer: "#route_error_box",
		errorElement: "p",
		submitHandler: function(form){
			MapSave.submitHandler(form);
			
			form.submit();
		}
	});

	settings_var = $("#r_form_settings").validate({
		rules: {
			mile:{
				required:true,
				number: true
			},
			circ:{
				required:true,
				number:true
			}		
		},		
		submitHandler : function(form){
			$("[name=map_settings]").val($.toJSON(MapSettings));
			$(form).ajaxSubmit({
				success: function(data){
					if(data){
						$.facebox("Your settings have been saved.", 1000);
					}
					else{
						$.facebox("There was an error, try again.", 1000);
					}
				},
				dataType: "json"
			});
			return false;
		},
		errorLabelContainer: "#errors_box",
		errorElement: "p"
	});
	
	$("#u_mile_marker").change(function(){
		MapSettings.MileMarkers.distance = $("#u_mile_marker").val();
		Map.refresh();
	});
	$("#input_circle_show").click(function(){
		MapSettings.DistanceCircle.enable = $(this).attr("checked");
		Map.refresh();
	});
	$("#input_follow_roads").click(function(){
		MapSettings.Directions.enable = $(this).attr("checked");
	});
	$("#u_circle_dist").change(function(){
		MapSettings.DistanceCircle.radius = $("#u_circle_dist").val();
		Map.refresh();
	});
	$("#settings_map_type").change(function(){
		MapSettings.MapType = $(this).val();
		Map.instance.setMapType(eval($(this).val()))
	});

	var form_init = {
		"#input_circle_show": MapSettings.DistanceCircle.enable,
		"#input_follow_roads": MapSettings.Directions.enable,
		"#u_circle_dist": MapSettings.DistanceCircle.radius,
		"#u_mile_marker": MapSettings.MileMarkers.distance,
		"#settings_map_type": MapSettings.MapType
	}

	$.each(form_init, function(key){
		if($(key).is(":checkbox")){
			if(this == true){
				$(key).attr("checked", true);
			}
			else{
				$(key).removeAttr("checked");
			}
		}
		else if($(key).is(":text")){
			$(key).val(this);
		}
		else if($(key).is("select")){
			$(key).val(this.toString());
		}
	});

	Units.init({
		callback:function(){
			Map.refresh();
		}
	});
});

function check_apply(){
	if(settings_var.numberOfInvalids() == 0){
		$.facebox.close();
	}
	return;
}

document.body.onunload = GUnload;
</script>
</div>
<div id="footer" class="container_12 bottom"><div class="grid_2 prefix_2">
	<h2>Routes</h2>
<ul>
	<li><a href="/routes/index">View Routes</a></li>
	<li><a href="/routes/create">New Route</a></li>
<!--	<li><a href="/routes/browse">Search</a></li>-->
</ul>
</div>
<div class="grid_2">

	<h2>Training</h2>
<ul>
	<li><a href="/training/index">View Training Items</a></li>
	<li><a href="/training/create">New Training Item</a></li>
	<li><a href="/goals">View Goals</a></li>
	<li><a href="/goals/create">New Goal</a></li>
<!--	<li><a href="/training/browse">Search</a></li>-->
</ul>
</div>
<div class="grid_2">
	<h2>Community</h2>
<ul>
	<li><a href="/community/index">Index</a></li>
</ul>

</div>
<div class="grid_2 suffix_2">

	<h2>runnDAILY</h2>
<ul>
	<li><a href="/about/index">About Us</a></li>
	<li><a href="/about/contact">Contact</a></li>
	<li><a href="/about/credits">Credits</a></li>
</ul>
</div>
<div class="clear"></div>
	<div class="grid_12" id="site_info"><?php if($this->_vars["currentUser"]->checkPermissions(100, false)): ?>
	<p>page generated in <?php echo mod_string_format($this->_vars["engine"]->getPageTime(), "%.4f"); ?> seconds</p>
	<?php endif ?>
	<p>&copy; 2008-2009 runnDAILY LLC</p>
</div>
<div class="clear"></div>
</div>

<div id="feedback_modal" style="display: none">
		<h4>Feedback</h4>
		<h5>Let us know what you think:</h5>
	<form id="feedback_form_modal" action="/feedback/create" method="post">
		<p><textarea name="msg_message"></textarea></p>
		<div id="feedback_modal_error_box"></div>
		<input type="hidden" name="msg_type" value="2" />
		<p class="align_right">
			<input type="submit" value="Submit" />
			<input type="button" value="Cancel" onclick="$.facebox.close()" />
		</p>
	</form>
</div>
<?php if(!$this->_vars["engine"]->requirePermission("PV__300")): ?>
<div id="login_modal" style="display:none">
	<form id="login_form_modal" action="/user/login" method="post">
		<p class="notice">Please enter your username and password.</p>
		<div id="login_modal_error_box"></div>
		<p><label>Username: </label><input type="text" name="username"></p>
		<p><label>Password: </label><input type="password" name="password"></p>
		<p><label>Stay Logged In? </label><input type="checkbox" name="remember" value="1"></p>
		<p><input class="login" type="submit" value="Login"></p>
	</form>
</div>
<?php endif ?>

<script type="text/javascript">
	$(document).ready(
		function(){
			$("input:first").focus();
			Units.init();
			$("#login_form_modal").validate({
				onkeyup: false,
				onclick: false,
				onfocusout: false,
				rules: {
					username: {
						required: true
					},
					password: {
						required: true
					}
				},
				messages: {
					username: {
						required: "Please enter your username."
					},
					password: {
						required: "Please enter your password."
					}
				},
				errorLabelContainer: "#login_modal_error_box",
				errorElement: "p"
			});

			$("#feedback_form_modal").validate({
				onkeyup: false,
				onclick: false,
				onfocusout: false,
				rules: {
					msg_message: {
						required: true
					}
				},
				messages: {
					msg_message: {
						required: "Please enter your feedback before submitting."
					}
				},
				errorLabelContainer: "#feedback_modal_error_box",
				errorElement: "p",
				//errorClass: "alert_red",
				submitHandler : function(form){
					$(form).ajaxSubmit({
						success: function(data){
							$(form).clearForm();
							$.facebox("Feedback received.", 1000);
						}
					});
				}
			});

			$("a.notify").click(function(){
				var a = $(this);
				if(this.rel){
					$.post(
						"/user/ajax_remove_notification",
						{id:this.rel},
						function(data){
							a.closest(".notification").remove();
						}
					);
				}
				else{
					a.closest(".notification").remove();
				}
				return false;
			});
		}
	);
</script>
</body>
</html>