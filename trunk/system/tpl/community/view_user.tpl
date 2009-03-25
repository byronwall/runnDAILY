<div class="grid_12">
	<h2 id="page-heading">{{$user->username}}</h2>
</div>
<div class="clear"></div>
<div class="grid_12">
	<div class="actions">
		{{if !$currentUser->checkFriendsWith($user->uid)}}
		<a href="#addFriend" id="a_addfriend" class="icon"><img src="/img/icon/user_plus.png" />Add as Friend</a>
		{{else}}
		<a href="#addFriend" id="a_removefriend" class="icon"><img src="/img/icon/user_minus.png" />Remove Friend</a>
		{{/if}}
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
var f_uid = {{$user->uid}};

$("#a_addfriend").live("click", function(){
	var a = $(this);
	a.text("adding friend...");
	
	$.post(
		"/community/add_friend",
		{f_uid:f_uid},
		function(data){
			if(data){
				a.replaceWith('<a href="#addFriend" id="a_removefriend" class="icon"><img src="/img/icon/user_minus.png" />Remove Friend</a>');
			}
			else{
				a.text("Try To Add Again");
				a.hide();
				a.fadeIn("slow");
			}
		},
		"text"
	);
	return false;	
});

$("#a_removefriend").live("click", function(){
	var a = $(this);
	a.text("removing friend...");
	
	$.post(
		"/community/ajax_remove_friend",
		{f_uid:f_uid},
		function(data){
			if(data){
				a.replaceWith('<a href="#addFriend" id="a_removefriend" class="icon"><img src="/img/icon/user_plus.png" />Add as Friend</a>');
			}
			else{
				a.text("Try To Remove Again");
				a.hide();
				a.fadeIn("slow");
			}
		},
		"text"
	);
	return false;	
});

</script>