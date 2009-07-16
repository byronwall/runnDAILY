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



<!--Template does not support tag for * yet-->
<div class="grid_12">
	<h2 id="page-heading">Community</h2>
</div>
<div class="clear"></div>

<div class="grid_12">
	<h2>Find a User</h2>
		<p class="notice">Search for a user by username or email.</p>
		<div id="error_box"></div>
		<form id="search_form" action="/community/search" method="post">
			<p>
				<input id="input_search" type="text" name="u_search" />
				<input id="search" type="submit" value="Search" />
			</p>
		</form>
		<div id="results"></div>
	<h2>Your Friends</h2>
	<?php if($this->_vars["users_friends"]): ?><p class="notice">A link exists between you and the following users. You may view their routes and training items or send them a message.</p><?php endif ?>
	<?php if(count($this->_vars["users_friends"])): foreach($this->_vars["users_friends"] as $this->_vars['friend']): ?>
		<div class="float_left">
			<p>
				<a href="/community/view_user/<?php echo $this->_vars["friend"]['u_uid']; ?>/<?php echo $this->_vars["friend"]['u_username']; ?>" class="icon"><img src="/img/icon/user_friend.png" /><?php echo $this->_vars["friend"]['u_username']; ?></a>
			</p>
		</div>
	<?php endforeach; else: ?>
		<div class="float_left">
			<p>You have not added any users as friends.</p>
		</div>
	<?php endif; ?>
	<div class="clear"></div>
	
	<h2>Recent New Users</h2>
	<?php if(count($this->_vars["users_recent"])): foreach($this->_vars["users_recent"] as $this->_vars['user']): ?>
		<div class="float_left">
			<p>
				<a href="/community/view_user/<?php echo $this->_vars["user"]['u_uid']; ?>/<?php echo $this->_vars["user"]['u_username']; ?>" class="icon"><img src="/img/icon/user.png" /><?php echo $this->_vars["user"]['u_username']; ?></a>
			</p>
		</div>
	<?php endforeach; else: ?>
		<div class="float_left">
			<p>There are no recent new users.</p>
		</div>
	<?php endif; ?>
</div>
<div class="clear"></div>

<script type="text/javascript">
$(document).ready(function(){
	$("#search_form").validate({
		onkeyup: false,
		onclick: false,
		onfocusout: false,
		rules: {
			u_search: {
				required: true,
				minlength: 3
			}
		},
		messages: {
			u_search: {
				required: "Please enter a username or email.",
				minlength: "Please enter at least 3 characters."
			}
		},
		errorLabelContainer: "#error_box",
		errorElement: "p",
		submitHandler : function(form){
			$(form).ajaxSubmit({"target" : "#results"});
		}
	});
});
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