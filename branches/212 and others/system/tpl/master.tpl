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
    <script src="/js/byron.sort.js" type="text/javascript"></script>
    <script src="/js/excanvas.js" type="text/javascript"></script>
    <script src="/js/jquery.flot.js" type="text/javascript"></script>
    
    <!--TITLE-->
    <title>{{$page->title}}</title>
</head>

<body id="{{$page->common}}">
<div class="container_12 top">
<div class="grid_2 prefix_5 suffix_5 bg_top"><a href="/index"><img class="logo" src="/img/logo.png"></a></div>
<div class="clear"></div>
<div class="grid_12">
{{include file="menu.tpl"}}
</div>
<div class="clear"></div>

{{include file="notifications.tpl"}}

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
			Units.init();
			$("#form_feedback").validate({
				submitHandler : function(form){
					$(form).ajaxSubmit();
					$(form).clearForm();
					$.facebox.close();
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