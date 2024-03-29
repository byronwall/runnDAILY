<div class="grid_12">
	<h2 id="page-heading">{{$user->username}}</h2>
</div>
<div class="clear"></div>
{{if $currentUser->uid != $user->uid}}
<div class="grid_12">
	<div class="actions">
		{{if !$currentUser->checkFriendsWith($user->uid)}}
		<a href="#requestFriend" id="a_add" class="facebox icon"><img src="/img/icon/user_plus.png" />Add as Friend</a>
		{{else}}
		<a href="/messages/create/{{$user->uid}}" class="facebox icon"><img src="/img/icon/mail_plus.png" />New Message</a>
		<a href="#removeFriend" id="a_remove" class="facebox icon"><img src="/img/icon/user_minus.png" />Remove Friend</a>
		{{/if}}
	</div>
</div>
<div class="clear"></div>
{{/if}}

{{if $currentUser->checkFriendsWith($user->uid) || $currentUser->uid == $user->uid}}
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
	{{foreach from=$routes item=route}}
		<div id="route_{{$route.r_id}}" class="route_item">
			<div><a href="/routes/view/{{$route.r_id}}/{{$route.r_name}}" class="r_name icon"><img src="/img/icon/route.png" />{{$route.r_name}}</a></div>
			<div class="r_date icon"><img src="/img/icon/calendar.png" />{{$route.r_creation|date_format}}</div>
			<div class="icon float_right"><img src="/img/icon/distance.png" /><span class="r_dist dist-val">{{$route.r_distance|@round:"2"}} mi</span></div>
			<div class="clear"></div>
		</div>
	{{foreachelse}}
		<div>{{$user->username}} does not currently have any routes.</div>
	{{/foreach}}
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
		{{foreach from=$training_index_items item=training_item}}
		<div id="item_{{counter}}" class="training_item">
			{{if $training_item.r_name}}<div><a href="/routes/view/{{$training_item.t_rid}}/{{$training_item.r_name}}" class="t_name icon"><img src="/img/icon/route.png" />{{$training_item.r_name}}</a></div>{{/if}}
				<div class="icon float_left"><img src="/img/icon/distance.png" /><span class="t_dist dist-val">{{$training_item.t_distance|round:"2"}} mi</span></div>
			<div class="clear"></div>
				<div class="t_date icon float_right">{{$training_item.t_date|date_format}} <img src="/img/icon/calendar.png" /></div>
				<div class="icon float_left"><img src="/img/icon/dashboard.png" /><span class="t_pace">{{$training_item.t_pace|round:"2"}} mi/h</span></div>
			<div class="clear"></div>
				<div class="icon align_right">{{$training_item.t_time|time_format}}<span class="t_time" style="display:none">{{$training_item.t_time}}</span> <img src="/img/icon/clock.png" /></div>
				{{if $training_item.t_comment}}
				<div class="align_left italic">{{$training_item.t_comment}}</div>
				{{/if}}
		</div>
		{{foreachelse}}
		<div>
			<p>{{$user->username}} does not currently have any training items.</p>
		</div>
		{{/foreach}}
	</div>
</div>
{{else}}
<div class="grid_10">
	<p>You are not currently friends with {{$user->username}}. You must be friends to see route and training information.</p>
</div>
{{/if}}

<div class="grid_2 align_right">
	<h4>Details</h4>
		<h5>Last Seen</h5>
		<p>{{if $user->date_access}}<img src="/img/icon/calendar.png" class="icon" /> {{$user->date_access|date_format}}{{else}}Not seen recently.{{/if}}</p>
	<h5>Member Since</h5>
		<p>{{if $user->join}}<img src="/img/icon/calendar.png" class="icon" /> {{$user->join|date_format}}{{else}}The beginning.{{/if}}</p>
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

<div id="requestFriend" style="display:none">
	<form action="/confirmation/actionCreate" method="post" class="ajax">
		<input type="hidden" name="type" value="1">
		<input type="hidden" name="uid_to" value="{{$user->uid}}">
		<p>Do you want to request to be friends?</p>
		<p>
			<input type="submit" value="Request">
			<input type="button" value="cancel" onclick="$.facebox.close()">
		</p>
	</form>
</div>
<div id="removeFriend" style="display:none">
	<form action="/community/ajax_remove_friend" method="post" class="ajax">
		<input type="hidden" name="f_uid" value="{{$user->uid}}">
		<p>Do you want to remove {{$user->username}} as a friend?</p>
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