{{*
This is the master template which holds all of the main layout.
*}}
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
    
    <!--SYTLE SHEETS-->
	<link href="/css/reset.css" rel="stylesheet" type="tet/css">
	<link href="/css/combine.css" rel="stylesheet" type="text/css">
    <link href="/css/thickbox.css" rel="stylesheet" type="text/css">
    
    <!--FAVORITE ICON-->
    <link rel="icon" type="image/png" href="/img/favico.png">
    
    <!--JAVASCRIPT-->
    <script src="/js/site.js" type="text/javascript"></script>
    
    <!--TITLE-->
    <title>{{$page->title}}</title>
</head>

<body id="{{$body_id}}">
	<div class="container_12 top">
		<div class="grid_2 prefix_5 suffix_5 bg_top">
			<a href="/index.php"><img class="logo" src="/img/logo.png"></a>
		</div>
		<div class="clear"></div>
		<div class="grid_12">
			<ul class="nav main">
				<li><a href="/index">Home</a></li>
				<li><a href="/routes/index">Routes</a></li>
				<li><a href="/training/index">Training</a></li>
				<li><a href="/community/index">Community</a></li>
				<li>
					<a href="#">runn Daily</a>
					<ul>
						<li><a href="/about/index">About Us</a></li>
						<li><a href="">Contact</a></li>
					</ul>
				</li>
				<li class="secondary">
					{{if $currentUser->isAuthenticated}}
					<a href="/community/view_user?uid={{$currentUser->uid}}">{{$currentUser->username}}{{if $currentUser->msg_new}}<img class="icon" src="/img/alert.png" />{{/if}}</a>
					<ul>
						<li><a href="/settings">Settings</a></li>
						<li><a href="/messages">Messages ({{$currentUser->msg_new}})</a></li>
						<li><a href="/user/logout">Logout</a></li>
					</ul>
					{{else}}
					<a href="/login.php">Login</a>
					{{/if}}
				</li>
				{{if $currentUser->checkPermissions(100, false)}}
				<li class="secondary">
					<a href="/admin/index.php">Admin</a>
					<ul>
						{{if $page->common}}<li><a href="/help/view.php?common={{$page->common}}&height=500&width=800" class="thickbox">Help </a></li>{{/if}}
						<li><a href="#TB_inline?&inlineId=feedback_modal&modal=true" class="thickbox">Feedback</a></li>
					</ul>
				</li>
				{{/if}}
			</ul>
		</div>
		<div class="clear"></div>
		{{$page_content}}
	</div>
	<div id="footer" class="container_12 bottom">
		{{include file="footer.tpl"}}
		<div class="clear"></div>
		<div class="grid_12" id="site_info">
		{{if $currentUser->checkPermissions(100, false)}}
			<p>page generated in {{$engine->getPageTime()|string_format:"%.5f"}} seconds</p>
		{{/if}}
			<p>&copy; 2008-2009 Byron and Chandler</p>
		</div>
		<div class="clear"></div>
	</div>

<div id="feedback_modal" style="display:none">
	<form action="/lib/action_feedback.php" method="post" id="form_feedback">
		<h2>tell us what you think</h2>
		<textarea name="m_msg" class="required"></textarea>
		<input type="submit" value="send it!" />
		<input type="button" value="cancel" onclick="tb_remove()" />
		<input type="hidden" name="action" value="create" />
	</form>
</div>

<script type="text/javascript">
	$(document).ready(
		function(){
			$("#tab_{{$page->tab}}").removeClass("tab_inactive");
			$("#tab_{{$page->tab}}").addClass("tab_active");
			$("#form_feedback").validate({
				submitHandler : function(form){$(form).ajaxSubmit();$(form).clearForm();tb_remove();}
			});
		}
	);
</script>
{{include file="google.tpl"}}
</body>
</html>

