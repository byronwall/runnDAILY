<div class="grid_12">
	<h2 id="page-heading">{{$user->username}}</h2>
</div>
<div class="clear"></div>
<div class="grid_12">
	<div class="actions">
		<a href="#message_modal" class="facebox icon"><img src="/img/icon_mail_plus.png" />Send a Message</a>
		<a href="#addFriend" id="a_addfriend" rel="{{$user->uid}}" class="icon"><img src="/img/icon_user_plus.png" />Add as Friend</a>
	</div>
</div>
<div class="clear"></div>

<div class="grid_3">
	<div class="box">
		<h2>Details</h2>
		<ul>
			<li>last seen: {{$user->date_access|date_format}}</li>
			<li>member since: coming soon!</li>
		</ul>
	</div>
</div>


<div class="grid_5">
	<div class="box">
		<h2>Recently Created Routes</h2>
		<ul id="route_recent_con">
			{{include file="routes/parts/route_list.tpl" routes=$user_routes query=$r_query }}
		</ul>
	</div>
</div>


<div class="grid_4">
	<div class="box">
		<h2>Recently Added Activity</h2>
		<ul id="route_recent_con">
			{{include file="log/log_list.tpl" logs=$user_log uid=$user->uid page_no=1}}
			<li class="route_recent_list"><a href="#">coming soon: see all activity</a></li>
		</ul>
	</div>
	
	<div class="box">
		<h2>Recently Created Training Entries</h2>
		<ul id="route_recent_con">
			{{include file="training/parts/item_list.tpl" t_items=$user_training query=$t_query}}	
			<li class="route_recent_list"><a href="/training/browse?u_uid={{$user->uid}}">coming soon: see all training</a></li>
		</ul>
	</div>
</div>

<div class="clear"></div>

<div id="message_modal" style="display:none">
	<h1>Send {{$user->username}} a message.</h1>
	<form action="/message/create" method="POST" name="user_message">
		<textarea name="m_msg">Message text.</textarea>
		<input type="submit" value="Send" />
		<input type="button" value="Cancel" onclick="$.facebox.close()" />
		<input type="hidden" name="m_uid_to" value="{{$user->uid}}" />
		<input type="hidden" name="m_uid_from" value="{{$currentUser->uid}}" />
	</form>
</div>

<script type="text/javascript">
var anchorCall;
$(document).ready( function(){
	prep_ajax($("a.ajax"));
});

function prep_ajax(DOM){
	DOM.click( function(){
		anchorCall = $(this).parent().before("<li><img src='/img/loadingAnimation.gif' /></li>");
		anchorCall.fadeOut("slow");
 		$.get(this.href, function(data){
			anchorCall.prev("li").remove();
			anchorCall.before(data);
			prep_ajax(anchorCall.prev().find("a.ajax"));
			anchorCall.remove();
		}, "html");
		return false;
	});
	
}

$("#a_addfriend").bind("click", click_addFriend);

function click_addFriend(){
	var friend_uid = this.rel;
	$(this).text("adding friend...");
	$.post(
		"/community/add_friend",
		{f_uid:friend_uid},
		function(data){
			if(data > 0){
				alert(data);
				$("#a_addfriend").text("friend added");
				$("#a_addfriend").unbind("click", click_addFriend);
				$("#a_addfriend").bind("click", function(){ return false; });
			}
			else{
				$("#a_addfriend").text("try adding again");
				$("#a_addfriend").hide();
				$("#a_addfriend").fadeIn("slow");
			}
		},
		"text"
	);
	return false;
}

</script>