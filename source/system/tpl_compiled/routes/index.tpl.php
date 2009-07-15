<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN" "http://www.w3.org/TR/html4/strict.dtd">
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



<div class="grid_12">
<h2 id="page-heading">Routes</h2>
</div>
<div class="clear"></div>
<div class="grid_12">
<div class="actions">
	<a href="/routes/create" class="icon"><img src="/img/icon/route_plus.png"/>New Route</a>
<!--	<a href="/routes/browse" class="icon"><img src="/img/icon_cards_stack.png"/>Search Routes</a>-->
</div>
</div>
<div class="clear"></div>

<div class="grid_3" id="route_sidebar">
	<div id="sort_options" class="align_right">
		<label>Sort by: </label>
		<select id="sort_select">
			<option value="r_date">Date</option>
			<option value="r_dist">Distance</option>
			<option value="r_name">Route Name</option>
		</select>
		<a href="#" id="reverse_sort" class="sort_desc"><img src="/img/icon/sort_desc.png" /> DESC</a>
	</div>
	<div id="route_list">
	<?php if(count($this->_vars["routes"])): foreach($this->_vars["routes"] as $this->_vars['route']): ?>
		<div id="route_<?php echo $this->_vars["route"]['r_id']; ?>" class="route_item">
			<div><a href="/routes/view/<?php echo $this->_vars["route"]['r_id']; ?>/<?php echo $this->_vars["route"]['r_name']; ?>" class="r_name icon"><img src="/img/icon/route.png" /><?php echo $this->_vars["route"]['r_name']; ?></a></div>
			<div class="r_date icon"><img src="/img/icon/calendar.png" /><?php echo $this->_vars["route"]['r_creation']; ?></div>
			<div class="icon float_right"><img src="/img/icon/distance.png" /><span class="r_dist dist-val"><?php echo $this->_vars["route"]['r_distance']; ?> mi</span></div>
			<div class="clear"></div>
			<div><a href="#" rel=<?php echo $this->_vars["route"]['r_id']; ?> class="route icon"><img src="/img/icon/arrow.png" /> Show in place</a></div>
		</div>
	<?php endforeach; else: ?>
		<div>You do not have any routes.<a href="/routes/create" class="icon"><img src="/img/icon/route_plus.png" />Create</a> a new route to enable advanced features.</div>
	<?php endif; ?>
	</div>
	<div id="route_info" style="display:none">
		<h4 id="info_name"></h4>
		<p id="info_distance"></p>
		<p id="info_date"></p>
		<p id="info_desc"></p>
		<p><a href="#" class="list icon"><img src="/img/icon/arrow_back.png" />Return</a></p>
	</div>
</div>

<div class="grid_9">
	<div id="route_map" class="map"></div>
</div>
<div class="clear"></div>

<div id="loading_overlay" style="display:none">
</div>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAYZcibhuwr8GMgCWYwqU-RxTwKQnnvD1T4H7IjqlIr-cK4JGBGBR9nTuCz-u_Of2k2UEZ7khhybXPyw" type="text/javascript"></script>
<!--<script src="/js/map.labeledmarker.js" type="text/javascript"></script>-->
<!--<script src="/js/map.js" type="text/javascript"></script>-->
<!--<script src="/js/PolylineEncoder.pack.js" type="text/javascript"></script>-->
<script src="/js/runndaily-maps-9.6.18.ycomp.js" type="text/javascript"></script>
<script type="text/javascript">
var RouteIndex = {
	view_route : false,
	init_location: null,
	temp_rid: null,
	switchToRoute: function(rid){
		RouteIndex.view_route = true;
		RouteIndex.temp_rid = rid;
		//add loading screen
		var div = $("#route_map");
		$("#loading_overlay").show().height(div.height()).width(div.width());
		$("#loading_overlay").css({
			"position":"absolute",
			"top":div.position().top,
			"left":div.position().left,
			"background-color":"#000",
			"opacity":0.5
		});
		//get route data
		if(routes[rid].polyline){
			RouteIndex.route_data_callback(routes[rid].polyline);
		}
		else{
			$.get(
				"/routes/ajax_route_data",
				{rid:rid},
				RouteIndex.route_data_callback,
				"json"
			);
		}
	},
	route_data_callback: function(polyline){
		//remove loading screen
		$("#loading_overlay").hide();
		
		//show route
		MapData.loadRoute(polyline, {
			draggble: false,
			show_points: false
		});
		
		//change to route info panel
		$("#route_list, #route_settings").hide();
		$("#route_info").show();

		var rid = RouteIndex.temp_rid;
		routes[rid].polyline = polyline;
		$("#info_name").html('<a href="/routes/view/'+rid+'" class="r_name icon"><img src="/img/icon/route.png" />'+routes[rid].r_name+'</a>');
		$("#info_distance").html('<img src="/img/icon/distance.png" /> Distance: <span class="dist-val">' + routes[rid].r_distance.toFixed(2) + ' mi</span>');
		$("#info_date").html('<img src="/img/icon/calendar.png" /> ' + routes[rid].r_creation);
		$("#info_date").text(routes[rid].r_description);
		$("#sort_options").hide();
	},
	switchToAll: function(){
		RouteIndex.route_view = false;
		$("#loading_overlay").show();
		$("#route_list, #route_settings").show();
		$("#route_info").hide();
		$("#sort_options").show();

		Map.instance.clearOverlays();
		$.each(routes, function(){
			Map.instance.addOverlay(this.marker);
		});
		Map.instance.setCenter(RouteIndex.init_location);
		$("#loading_overlay").hide();
	},
	moveend_event: function(){
		if(RouteIndex.route_view) return false;
		return false;
		var center = Map.instance.getCenter();
		$.each(routes, function(){
			var id = "#route_" + this.r_id;
			var dist = center.distanceFrom(this.latlng) * meters_to_miles;
			$(id).text(dist.toFixed(2));
		});
	},
	selected_rid: null,
	marker_click_event: function(latlng){
		RouteIndex.switchToRoute(this.id);
		return;
		$(".active_row").removeClass("active_row");
		if(RouteIndex.selected_rid == this.id){
			RouteIndex.selected_rid = null
		}
		else{
			RouteIndex.selected_rid = this.id;
			var id = "#route_" + this.id;
			$(id).addClass("active_row");
		}
	},
	ready_event: function(){
		$("a.route").click(function(){
			RouteIndex.switchToRoute(this.rel);
			return false;
		});
		$("a.list").click(function(){
			RouteIndex.switchToAll();
			return false;
		});
	
		Map.load("route_map", null, {full_height:true});
		GEvent.addListener(Map.instance, "moveend", RouteIndex.moveend_event);
		var init = null;
		$.each(routes, function(){
			this.latlng = new GLatLng(this.r_start_lat, this.r_start_lng);
			this.marker = new GMarker(this.latlng);
			this.marker.id = this.r_id;
			GEvent.addListener(this.marker, "click", RouteIndex.marker_click_event);
			Map.instance.addOverlay(this.marker);
			if(!RouteIndex.init_location){
				RouteIndex.init_location = this.latlng;
			}
		});
		if(RouteIndex.init_location){
			Map.instance.setCenter(RouteIndex.init_location, 12);
		}
		else{
			Map.instance.setCenter(new GLatLng(39.229984356582, -95.2734375), 4);
		}
		$("#route_list").heightBrowser().css("overflow", "auto");

		$.sorter.add("routes", {
			classes: {
				r_name: "alpha",
				r_dist: "numeric",
				r_date: "date"
			},
			parent: "#route_list",
			item: ".route_item",
			sort_desc: -1,
			sort_key: "r_date",
			reverse: "#reverse_sort",
			selector: "#sort_select"
		});
	}
};
var routes = <?php echo $this->_vars["routes_js"]; ?>;

$(document).ready(RouteIndex.ready_event);
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
	<p>page generated in <?php echo $this->_vars["engine"]->getPageTime(); ?> seconds</p>
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