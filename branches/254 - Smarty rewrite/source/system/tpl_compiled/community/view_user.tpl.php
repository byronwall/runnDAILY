<?php
function mod_date_format($variable, $param = "F j, Y") {
		if (! is_int ( $variable )) {
			$variable = strtotime ( $variable );
		}
		return date ( $param, $variable );
	}
function mod_round($value, $precision) {
		return round ( $value, $precision );
	}
function mod_time_format($seconds, $familiar = true){
	if($familiar){
		$formatted = date("H:i:s", mktime(0,0,$seconds, 1, 1, 2009));
		$familiar = explode(":", $formatted);
		$output = "";
		$unit = array('hour','min','sec');
		
		for ($i = 0; $i < count($familiar); $i++){
			if($familiar[$i] != 0){
				$familiar[$i] += 0;
				$output .= $familiar[$i];
				$output .= " " . $unit[$i];
				if($familiar[$i] != 1){
					$output .= "s";
				}
				if ($i < 2){
					$output .= " ";
				}
			}
		}
		
		return $output;
	}
	else{
		return date("H:i:s", mktime(0,0,$seconds, 1, 1, 2009));
	}
}
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



<div class="grid_12">
	<h2 id="page-heading"><?php echo $this->_vars["user"]->username; ?></h2>
</div>
<div class="clear"></div>
<?php if($this->_vars["currentUser"]->uid != $this->_vars["user"]->uid): ?>
<div class="grid_12">
	<div class="actions">
		<?php if(!$this->_vars["currentUser"]->checkFriendsWith($this->_vars["user"]->uid)): ?>
		<a href="#requestFriend" id="a_add" class="facebox icon"><img src="/img/icon/user_plus.png" />Add as Friend</a>
		<?php else: ?>
		<a href="/messages/create/<?php echo $this->_vars["user"]->uid; ?>" class="facebox icon"><img src="/img/icon/mail_plus.png" />New Message</a>
		<a href="#removeFriend" id="a_remove" class="facebox icon"><img src="/img/icon/user_minus.png" />Remove Friend</a>
		<?php endif ?>
	</div>
</div>
<div class="clear"></div>
<?php endif ?>

<?php if($this->_vars["currentUser"]->checkFriendsWith($this->_vars["user"]->uid) || $this->_vars["currentUser"]->uid == $this->_vars["user"]->uid): ?>
<div class="grid_5">
<h4>Routes</h4>
	<div id="sort_options" class="align_right">
			<label>Sort by: </label>
			<select id="route_sort_select">
				<option value="r_date">Date</option>
				<option value="r_dist">Distance</option>
				<option value="r_name">Route Name</option>
			</select>
			<a href="#" id="route_reverse_sort" class="sort_desc"><img src="/img/icon/sort_desc.png" /> DESC</a>
	</div>
	<div id="route_list" class="route_list">
	<?php if(count($this->_vars["routes"])): foreach($this->_vars["routes"] as $this->_vars['route']): ?>
		<div id="route_<?php echo $this->_vars["route"]['r_id']; ?>" class="route_item">
			<div><a href="/routes/view/<?php echo $this->_vars["route"]['r_id']; ?>/<?php echo $this->_vars["route"]['r_name']; ?>" class="r_name icon"><img src="/img/icon/route.png" /><?php echo $this->_vars["route"]['r_name']; ?></a></div>
			<div class="r_date icon"><img src="/img/icon/calendar.png" /><?php echo mod_date_format($this->_vars["route"]['r_creation']); ?></div>
			<div class="icon float_right"><img src="/img/icon/distance.png" /><span class="r_dist dist-val"><?php echo mod_round($this->_vars["route"]['r_distance'], "2"); ?> mi</span></div>
			<div class="clear"></div>
		</div>
	<?php endforeach; else: ?>
		<div><?php echo $this->_vars["user"]->username; ?> does not currently have any routes.</div>
	<?php endif; ?>
	</div>
</div>

<div class="grid_5">
<h4>Training Items</h4>
	<div id="sort_options" class="align_right">
			<label>Sort by: </label>
			<select id="training_sort_select">
				<option value="t_date">Date</option>
				<option value="t_dist">Distance</option>
				<option value="t_pace">Pace</option>
				<option value="t_name">Route Name</option>
				<option value="t_time">Time</option>
			</select>
			<a href="#" id="training_reverse_sort" class="sort_desc"><img src="/img/icon/sort_desc.png" /> DESC</a>
	</div>
	<div id="training_items_list">
		<!--Template does not support tag for counter yet-->
		<?php if(count($this->_vars["training_index_items"])): foreach($this->_vars["training_index_items"] as $this->_vars['training_item']): ?>
		<div id="item_<!--Template does not support tag for counter yet-->" class="training_item">
			<?php if($this->_vars["training_item"]['r_name']): ?><div><a href="/routes/view/<?php echo $this->_vars["training_item"]['t_rid']; ?>/<?php echo $this->_vars["training_item"]['r_name']; ?>" class="t_name icon"><img src="/img/icon/route.png" /><?php echo $this->_vars["training_item"]['r_name']; ?></a></div><?php endif ?>
				<div class="icon float_left"><img src="/img/icon/distance.png" /><span class="t_dist dist-val"><?php echo mod_round($this->_vars["training_item"]['t_distance'], "2"); ?> mi</span></div>
			<div class="clear"></div>
				<div class="t_date icon float_right"><?php echo mod_date_format($this->_vars["training_item"]['t_date']); ?> <img src="/img/icon/calendar.png" /></div>
				<div class="icon float_left"><img src="/img/icon/dashboard.png" /><span class="t_pace"><?php echo mod_round($this->_vars["training_item"]['t_pace'], "2"); ?> mi/h</span></div>
			<div class="clear"></div>
				<div class="icon align_right"><?php echo mod_time_format($this->_vars["training_item"]['t_time']); ?><span class="t_time" style="display:none"><?php echo $this->_vars["training_item"]['t_time']; ?></span> <img src="/img/icon/clock.png" /></div>
				<?php if($this->_vars["training_item"]['t_comment']): ?>
				<div class="align_left italic"><?php echo $this->_vars["training_item"]['t_comment']; ?></div>
				<?php endif ?>
		</div>
		<?php endforeach; else: ?>
		<div>
			<p><?php echo $this->_vars["user"]->username; ?> does not currently have any training items.</p>
		</div>
		<?php endif; ?>
	</div>
</div>
<?php else: ?>
<div class="grid_10">
	<p>You are not currently friends with <?php echo $this->_vars["user"]->username; ?>. You must be friends to see route and training information.</p>
</div>
<?php endif ?>

<div class="grid_2 align_right">
	<h4>Details</h4>
		<h5>Last Seen</h5>
		<p><?php if($this->_vars["user"]->date_access): ?><img src="/img/icon/calendar.png" class="icon" /> <?php echo mod_date_format($this->_vars["user"]->date_access); ?><?php else: ?>Not seen recently.<?php endif ?></p>
	<h5>Member Since</h5>
		<p><?php if($this->_vars["user"]->join): ?><img src="/img/icon/calendar.png" class="icon" /> <?php echo mod_date_format($this->_vars["user"]->join); ?><?php else: ?>The beginning.<?php endif ?></p>
</div>

<div class="clear"></div>

<div id="message_modal" style="display:none">
	<h1>Send <?php echo $this->_vars["user"]->username; ?> a message.</h1>
	<form action="/message/create" method="POST" name="user_message">
		<textarea name="m_msg">Message text.</textarea>
		<input type="submit" value="Send" />
		<input type="button" value="Cancel" onclick="$.facebox.close()" />
		<input type="hidden" name="m_uid_to" value="<?php echo $this->_vars["user"]->uid; ?>" />
		<input type="hidden" name="m_uid_from" value="<?php echo $this->_vars["currentUser"]->uid; ?>" />
	</form>
</div>

<div id="requestFriend" style="display:none">
	<form action="/confirmation/actionCreate" method="post" class="ajax">
		<input type="hidden" name="type" value="1">
		<input type="hidden" name="uid_to" value="<?php echo $this->_vars["user"]->uid; ?>">
		<p>Do you want to request to be friends?</p>
		<p>
			<input type="submit" value="Request">
			<input type="button" value="cancel" onclick="$.facebox.close()">
		</p>
	</form>
</div>
<div id="removeFriend" style="display:none">
	<form action="/community/ajax_remove_friend" method="post" class="ajax">
		<input type="hidden" name="f_uid" value="<?php echo $this->_vars["user"]->uid; ?>">
		<p>Do you want to remove <?php echo $this->_vars["user"]->username; ?> as a friend?</p>
		<p>
			<input type="submit" value="Remove">
			<input type="button" value="Cancel" onclick="$.facebox.close()">
		</p>
	</form>
</div>

<script type="text/javascript">

$(function(){
	var actions = {
		"/confirmation/actionCreate": function(data){
			//this function expects a JSON object with [result]
			if(data.result){
				$.facebox("Your request was sent.", 500);
				$("#a_add").fadeOut("slow").remove();
			}
		},
		"/community/ajax_remove_friend": function(data){
			//this function expects a JSON object with [result]
			if(data.result){
				$.facebox("Your are no longer friends.", 500);
				$("#a_remove").fadeOut("slow").remove();
			}
		}
	};
	
	$("form.ajax").ajaxForm({
		success: function(data){
			if(actions[this.url]){
				actions[this.url](data);
			}
		},
		dataType: "json"
	});

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
		reverse: "#route_reverse_sort",
		selector: "#route_sort_select"
	});
	
	$.sorter.add("training", {
		classes: {
			t_name: "alpha",
			t_dist: "numeric",
			t_time: "numeric",
			t_date: "date",
			t_pace: "numeric"
		},
		parent: "#training_items_list",
		item: ".training_item",
		sort_desc: -1,
		sort_key: "t_date",
		reverse: "#training_reverse_sort",
		selector: "#training_sort_select"
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