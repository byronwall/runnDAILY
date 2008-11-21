{{*
This is the master template which holds all of the main layout.
*}}
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
    
    <!--SYTLE SHEETS-->
    <link href="/css/style.css"rel="stylesheet" type="text/css">
    
    <!--FAVORITE ICON-->
    <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon">
    
    <!--JAVASCRIPT-->
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAYZcibhuwr8GMgCWYwqU-RxQzNv4mzrEKtvvUg4SKGFnPU6pUNBTkQL_qSiLmJQ3qE-zNxRFJgRZM8g" type="text/javascript"></script>
    <script src="/js/jquery-latest.pack.js" type="text/javascript"></script>
    <script src="/js/json.js" type="text/javascript"></script>
    <script src="/js/map.js" type="text/javascript"></script>
    <script src="/js/PolylineEncoder.js" type="text/javascript"></script>
    <script src="/js/thickbox-compressed.js" type="text/javascript"></script>
    
    <!--TITLE-->
    <title>
        {{$page_title}}
    </title>
</head>

<body>

<!--TOP BAR-->
<div id="top_container">

<!--NAVIGATION-->
    <div id="navi_container">
	   	<ul id="navi_content">
        	<li><a href="/index.php">Home</a></li>
            <li><a href="/routes/">Routes</a></li>
            <li><a href="/training/">Training</a></li>
            <li><a href="/community/">Community</a></li>
        </ul>
    </div>
    
<!--USER BAR-->
	<div id="user_container">
	{{if $currentUser->isAuthenticated}}
    	<div id="user_content">You are currently logged in as {{$currentUser->username}}.</div>
        <div id="user_actions"><a href="/settings.php">Settings</a> | <a href="/lib/action_login.php?action=logout">Logout</a></div>
    {{else}}
    	<div id="user_actions"><a href="/login.php?refer={{$smarty.server.SCRIPT_NAME}}">Login</a></div>
    {{/if}}
    </div>
</div>

<!--CONTENT-->
<div id="content_container">
    <div id="content_content">
    	{{$page_content}}
    </div>
</div>

<!--FOOTER-->
<div id="footer_container">
	<div id="footer_content">Copyright Byron & Chandler 2008</div>
</div>

</body>
</html>

