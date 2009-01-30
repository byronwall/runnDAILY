{{*
This is the master template which holds all of the main layout.
*}}
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
    
    <!--SYTLE SHEETS-->
    <link href="/css/style.css"rel="stylesheet" type="text/css">
    <link href="/css/thickbox.css"rel="stylesheet" type="text/css">
    
    <!--FAVORITE ICON-->
    <link rel="icon" type="image/png" href="/img/favico.png">
    
    <!--JAVASCRIPT-->
    <script src="/js/site.js" type="text/javascript"></script>
    
    <!--TITLE-->
    <title>
        {{$page->title}}
    </title>
</head>

<body id="{{$body_id}}">

<!-- HEADER -->
<div id="header_ctain">

<div id="logo_ctain"><a id="logo" href="/index"><img src="/img/logo.png" /></a></div>

<!-- NAVIGATION -->
    <div id="nav_ctain">
    	<ul>
    		<li id="tab_home" class="tab_inactive"><a href="/index">Home</a></li>
    		<li id="tab_routes" class="tab_inactive"><a href="/routes/index">Routes</a></li>
    		<li id="tab_training" class="tab_inactive"><a href="/training/index">Training</a></li>
    		<li id="tab_community" class="tab_inactive"><a href="/community/index">Community</a></li>
    		{{if $currentUser->checkPermissions(100, false)}}
    			<li id="tab_admin" class="tab_inactive"><a href="/admin/index">Admin</a></li>
    		{{/if}}
    		<li id="tab_feedback" class="tab_inactive">
    			<a href="#TB_inline?&inlineId=feedback_modal&modal=true" class="thickbox">Feedback</a>
    		</li>
    	</ul>
    </div>
    
<!-- USER BAR -->
	<div id="user_ctain">
        {{if $currentUser->isAuthenticated}}
		<ul>
			<li id="tab_profile" class="tab_special"><a href="/profile.php">My Profile</a></li>
		</ul>
		<div id="user_panel">
			You are currently logged in as {{$currentUser->username}}.
			<a href="/settings">Settings</a> | 
			<a href="/user/logout">Logout</a> |
			<a href="/messages">Messages ({{$currentUser->msg_new}})</a>
			{{if $page->common}}
        		| <a href="/help/view?common={{$page->common}}&height=500&width=800" class="thickbox">Help </a>
        	{{/if}}
		</div>
        {{else}}
		<ul>
			<li id="tab_profile" class="tab_special"><a href="/login">Login</a></li>
		</ul>
        {{/if}}
	</div>
</div>

<!-- CONTENT -->
<div id="content_container">
    <div id="content_content">
    	{{$page_content}}
    </div>
</div>

<!-- FOOTER -->
<div id="footer_ctain">
	<div id="footer_con">
		{{include file="footer.tpl"}}
	</div>
    <div id="footer_copyright">&copy Byron & Chandler 2008</div>
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

