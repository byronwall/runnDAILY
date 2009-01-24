<h1>{{$user->username}}</h1>

<h2>Details</h2>
<ul>
	<li>last seen: {{$user->date_access|date_format}}</li>
	<li>member since: coming soon!</li>
</ul>

<h2>Recently Created Routes</h2>
<ul id="route_recent_con">
	{{include file="routes/parts/route_list.tpl" routes=$user_routes query=$r_query }}
</ul>

<h2>Recently Created Training Entries</h2>
<ul id="route_recent_con">
	{{include file="training/parts/item_list.tpl" t_items=$user_training query=$t_query}}	
	<li class="route_recent_list"><a href="/training/browse.php?u_uid={{$user->userID}}">coming soon: see all training</a></li>
</ul>

<h2>Recently Added Activity</h2>
<ul id="route_recent_con">
	{{include file="log/log_list.tpl" logs=$user_log uid=$user->userID page_no=1}}
	<li class="route_recent_list"><a href="#">coming soon: see all activity</a></li>
</ul>

<h2>Actions</h2>
<ul>
	<li><a href="#TB_inline?&height=300&width=300&inlineId=message_modal&modal=true" class="thickbox">send message</a></li>
	<li><a href="#addFriend" id="a_addfriend" rel="{{$user->userID}}">add as friend</a></li>
</ul>

<div id="message_modal" style="display:none">
	<h1>send {{$user->username}} a message</h1>
	<form action="/lib/action_message_create.php" method="POST" name="user_message">
		<textarea name="m_msg">enter message</textarea>
		<input type="submit" value="send" />
		<input type="button" value="cancel" onclick="tb_remove()" />
		<input type="hidden" name="m_uid_to" value="{{$user->userID}}" />
		<input type="hidden" name="m_uid_from" value="{{$currentUser->userID}}" />
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
		"/lib/ajax_addFriend.php",
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