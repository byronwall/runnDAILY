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
	<h2 class="heading float_left"><?php echo $this->_vars["route_view"]->name; ?></h2>
	<h2 class="align_right heading float_right"><span class="dist-val"><?php echo $this->_vars["route_view"]->distance; ?> mi</span></h2>
	<hr class="heading">
</div>
<div class="clear"></div>
<div class="grid_3">
	<p>Created by: <a href="/community/view_user/<?php echo $this->_vars["route_view"]->uid; ?>/<?php echo $this->_vars["route_view"]->data.u_username; ?>"><?php echo $this->_vars["route_view"]->data.u_username; ?></a></p>
</div>
<div class="grid_9">
	<div class="actions">
		<?php if($this->_vars["training_items"]): ?><a href="#assoc_training_items" class="icon"><img src="/img/icon/training.png" />View Training Items</a><?php endif ?>
		<a href="#route_train_modal" class="facebox icon"><img src="/img/icon/training_plus.png" />Record Time</a>
		<a href="#copy_modal" class="facebox icon"><img src="/img/icon/route_copy.png" />Copy</a>
	<?php if($this->_vars["route_view"]->getCanEdit()): ?>
		<a href="/routes/create?rid=<?php echo $this->_vars["route_view"]->id; ?>" class="icon"><img src="/img/icon/edit.png" />Edit</a>
		<a href="#delete_modal" class="facebox icon"><img src="/img/icon/delete.png" />Delete</a>
	
		<div id="delete_modal" style="display:none">
			<h4>Are you sure you wan to delete the current route?</h4>
				<p class="alert_red">Once a route has been deleted, there is no way to
				recover it! Only delete a route that you are sure you no longer want!</p>
				<form method="POST" action="/routes/action_delete">
				<p>
					<input type="hidden" name="action" value="delete" />
					<input type="hidden" name="r_rid" value="<?php echo $this->_vars["route_view"]->id; ?>" />
					<input type="submit" value="Delete" />
					<input type="button" value="Cancel" onclick="$.facebox.close()" />
				</p>
			</form>
		</div>
	<?php endif ?>
	</div>
	
	<div id="copy_modal" style="display:none">
		<h4>Create a Copy of "<?php echo $this->_vars["route_view"]->name; ?>"</h4>
		<p>This action will place a copy of the current route and add it to your route list.
		Doing so will allow you to edit the copied route. Alternatively you may copy a
		route into your account without modifying it. Routes that have training items
		associated with them must be copied before they can be edited. This is required
		in order to maintain the integrity of the training items associated with the
		route.</p>
		<p>Copying the current route is also useful if you would like to use the
		current route as a template for a new route. Simply copy the current route and
		then change the appropriate points or add additional points as necessary.</p>
		
		<form id="form_copy" action="/routes/create" method="post">
			<div id="copy_error_box"></div>
			<input type="hidden" name="r_id" value="<?php echo $this->_vars["route_view"]->id; ?>">
			<p><label>New Route Name: </label><input type="text" name="r_name" value="<?php echo $this->_vars["route_view"]->name; ?> (Copy)" size="50"></p>
			<p><a href="/routes/action_copy_edit" class="submit icon"><img src="/img/icon/route_copy.png" /> Copy then Edit</a> the New Route</p>
			<p><a href="/routes/action_copy_view" class="submit icon"><img src="/img/icon/route_copy_view.png" /> Copy then View</a> the New Route</p>
			<p><input type="button" onclick="$.facebox.close()" value="Cancel"></p>
		</form>
	</div>
	
	<div id="route_train_modal" style="display:none">
		<h4>Record a Time</h4>
		<p class="notify">Create a training item for the current route.</p>
		<form action="/training/action_save" method="post" id="route_train_form">
			<div id="train_error_box"></div>
			<input type="hidden" name="t_rid" value="<?php echo $this->_vars["route_view"]->id; ?>">
			<p><label>Time: </label><input type="text" name="t_time" value="00:00:00" size="10"></p>
			<p><label>Activity Type: </label>
				<select name="t_type" id="training_type">
				<?php if(count($this->_vars["t_types"])): foreach($this->_vars["t_types"] as $this->_vars['type']): ?>
					<option value="<?php echo $this->_vars["type"]['id']; ?>"><?php echo $this->_vars["type"]['name']; ?></option>
				<?php endforeach; endif; ?>
				</select>
			</p>
			<p><label>Date: </label><input type="text" name="t_date" value="Today" size="15"></p>
			<p><label>Distance: </label><input type="text" name="t_distance" value="<?php echo $this->_vars["route_view"]->distance; ?>" size="6"> mi</p>
			<p>Comment:</p>
			<p><textarea rows="5" cols="25" name="t_comment"></textarea></p>
			<p>
				<input type="submit" value="Create">
				<input type="button" value="Cancel" onclick="$.facebox.close()" />
			</p>
		</form>
	</div>
</div>
<div class="clear"></div>

<?php if($this->_vars["route_view"]->description): ?>
<div class="grid_12">
	<p><span class="bold">Description:</span> <?php echo $this->_vars["route_view"]->description; ?></p>
</div>
<div class="clear"></div>
<?php endif ?>

<div class="grid_12">
	<div id="map_placeholder" class="map large"></div>
</div>
<div class="clear"></div>

<?php if($this->_vars["route_view"]->elevation): ?>
<div class="grid_12">
	<h5>Elevation Profile</h5>
	<div id="elev_chart" style="width:100%;height:200px"></div>
</div>
<div class="clear"></div>
<?php endif ?>

<?php if($this->_vars["training_items"]): ?>
<div class="grid_12">
	<h5 id="assoc_training_items">Associated Training Items</h5>
	<!--Template does not support tag for counter yet-->
	<?php if(count($this->_vars["training_items"])): foreach($this->_vars["training_items"] as $this->_vars['training_item']): ?>
	<div id="item_<!--Template does not support tag for counter yet-->" class="training_item">
			<div class="icon float_left"><img src="/img/icon/distance.png" /><span class="t_dist dist-val"><?php echo $this->_vars["training_item"]['t_distance']; ?> mi</span></div>
			<div class="t_date icon float_right"><?php echo $this->_vars["training_item"]['t_date']; ?> <img src="/img/icon/calendar.png" /></div>
		<div class="clear"></div>
			<div class="icon float_left"><img src="/img/icon/dashboard.png" /><span class="t_pace"><?php echo $this->_vars["training_item"]['t_pace']; ?> mi/h</span></div>
			<div class="icon float_right"><?php echo $this->_vars["training_item"]['t_time']; ?><span class="t_time" style="display:none"><?php echo $this->_vars["training_item"]['t_time']; ?></span> <img src="/img/icon/clock.png" /></div>
		<div class="clear"></div>
		<?php if($this->_vars["training_item"]['t_comment']): ?>
		<div class="align_left italic"><?php echo $this->_vars["training_item"]['t_comment']; ?></div>
		<?php endif ?>
	</div>
	<?php endforeach; endif; ?>
</div>
<div class="clear"></div>
<?php endif ?>

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAYZcibhuwr8GMgCWYwqU-RxTwKQnnvD1T4H7IjqlIr-cK4JGBGBR9nTuCz-u_Of2k2UEZ7khhybXPyw" type="text/javascript"></script>
<!--<script src="/js/map.labeledmarker.js" type="text/javascript"></script>-->
<!--<script src="/js/map.js" type="text/javascript"></script>-->
<!--<script src="/js/PolylineEncoder.pack.js" type="text/javascript"></script>-->
<script src="/js/runndaily-maps-9.6.18.ycomp.js" type="text/javascript"></script>
<script type="text/javascript">

$(document).ready( function(){
	<?php if($this->_vars["route_view"]->elevation): ?>
	var elevation = <?php echo $this->_vars["route_view"]->elevation; ?>;
	var plot_options = {
		yaxis:{
			tickFormatter: function(number){ return number + "m"; }
		},
		legend:{
			show:false
		}
	}
	$.plot($("#elev_chart"), [{label:"meters", data:elevation}], plot_options);
	<?php endif ?> 
	Map.load("map_placeholder", null);
	MapData.loadRoute(<?php echo $this->_vars["route_view"]->points; ?>, {
		draggable: false,
		show_points: false
	});

	$("#route_train_form").validate({
		rules: {
			t_time: {required: true},
			t_date: {required: true},
			t_distance: {
				required: true,
				number: true
			}
		},
		messages: {
			t_time: {required: "Please enter a time."},
			t_date: {required: "Please enter a date."},
			t_distance: {
				required: "Please enter a distance.",
				number: "Distance must be a number."
			}
		},
		errorLabelContainer: "#train_error_box",
		errorElement: "p"
	});

	$("#form_copy").validate({
		rules: {
			r_name: {required: true}
		},
		messages: {
			r_name: {required: "Please enter a name." }
		},
		errorLabelContainer: "#copy_error_box",
		errorElement: "p"
	});	
	$("a.submit").click(function(){
		var form = $("#form_copy");
		form.attr("action", this.href);
		if(form.valid()){
			form.submit();
		}
		return false;
	});
});

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