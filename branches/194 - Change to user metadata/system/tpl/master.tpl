{{*
This is the master template which holds the main layout components.
*}}
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
    
    <!--SYTLE SHEETS-->
	<link href="/css/reset.css" rel="stylesheet" type="text/css">
	<link href="/css/combine.css" rel="stylesheet" type="text/css">
    <link href="/css/facebox.css" rel="stylesheet" type="text/css">
    
    <!--FAVORITE ICON-->
    <link rel="icon" type="image/png" href="/img/favico.png">
    
    <!--JAVASCRIPT-->
    <script src="/js/site.js" type="text/javascript"></script>
    <script src="/js/facebox.js" type="text/javascript"></script>
    
    <!--TITLE-->
    <title>{{$page->title}}</title>
</head>

<body id="{{$page->common}}">
<div class="container_12 top">
<div class="grid_2 prefix_5 suffix_5 bg_top"><a href="/index"><img class="logo" src="/img/logo.png"></a></div>
<div class="clear"></div>
<div class="grid_12">
<ul class="header nav main">
		<li><a href="/index" class="icon"><img src="/img/icon_home.png" />Home</a></li>
		<li><a href="/routes/index" class="icon"><img src="/img/icon_route.png" />Routes</a></li>
		<li><a href="/training/index" class="icon"><img src="/img/icon_training.png" />Training</a></li>
		<li><a href="/community/index" class="icon"><img src="/img/icon_community.png" />Community</a></li>
		<li><a href="#" class="icon"><img src="/img/icon_runndaily.png"/>runn Daily</a>
		<ul>
				<li><a href="/about/index">About Us</a></li>
				<li><a href="">Contact</a></li>
		</ul>
		</li>
		<li class="secondary">
		{{if $currentUser->isAuthenticated}}
			<a href="/community/view_user?uid={{$currentUser->uid}}" class="icon">
			{{if !($currentUser->gender)}}
				<img src="/img/icon_user_male.png"/>
			{{else}}
				<img src="/img/icon_user_female.png"/>
			{{/if}}
			{{$currentUser->username}}
			{{if $currentUser->msg_new}}
			<img class="notification" src="/img/icon_mail_exclamation.png" />
			{{/if}}</a>
		<ul>
				<li><a href="/settings" class="icon"><img src="/img/icon_wrench_screwdriver.png" />Settings</a></li>
				<li><a href="/messages" class="icon">{{if $currentUser->msg_new}}<img src="/img/icon_mail_open_document.png" />{{else}}<img src="/img/icon_mail_open.png" />{{/if}}Messages ({{$currentUser->msg_new}})</a></li>
				<li><a href="/user/logout" class="icon"><img src="/img/icon_logout.png" />Logout</a></li>
		</ul>
		{{else}}
		<li class="secondary"><a href="/login" class="icon"><img src="/img/icon_login.png" />Login</a></li>
		<li class="secondary"><a href="/register" class="icon"><img src="/img/icon_register.png" />Register</a></li>
		{{/if}} {{if $currentUser->checkPermissions(100, false)}}
		<li class="secondary"><a href="/admin/index" class="icon"><img src="/img/icon_application_monitor.png" />Admin</a></li>
		{{/if}}
		{{if $page->common}}
		<li class="secondary"><a href="/help/view?common={{$page->common}}" class="facebox icon"><img src="/img/icon_help.png" /></a></li>
		{{/if}}
		<li class="secondary"><a href="#feedback_modal" class="facebox icon"><img src="/img/icon_feedback.png" /></a></li>
</ul>
</div>
<div class="clear"></div>

{{$page_content}}
</div>
<div id="footer" class="container_12 bottom">{{include file="footer.tpl"}}
<div class="clear"></div>
	<div class="grid_12" id="site_info">{{if $currentUser->checkPermissions(100, false)}}
	<p>page generated in {{$engine->getPageTime()|string_format:"%.5f"}} seconds</p>
	{{/if}}
	<p>&copy; 2008-2009 Byron and Chandler</p>
</div>
<div class="clear"></div>
</div>

<div id="feedback_modal" style="display: none">
	<h2>Let us know what you think:</h2>
	<form action="/feedback/create" method="post" id="form_feedback">
		<p><textarea name="m_msg" class="required"></textarea></p>
		<p><input type="submit" value="Submit" /> <input type="button" value="Cancel" onclick="$.facebox.close()" /></p>
		<input type="hidden" name="action" value="create" />
	</form>
</div>

<script type="text/javascript">
	$(document).ready(
		function(){
			$("#form_feedback").validate({
				submitHandler : function(form){
					$(form).ajaxSubmit();
					$(form).clearForm();
					$.facebox.close();
				}
			});
		}
	);
</script>
{{include file="google.tpl"}}
</body>
</html>