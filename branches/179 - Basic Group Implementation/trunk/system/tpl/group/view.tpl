<div class="grid_12">
	<h2 id="page-heading">{{$group_view->name}}</h2>
</div>
<div class="clear"></div>

<div class="grid_12">
	<div class="actions">
		<a href="#new_event_modal" class="facebox icon"><img src="/img/icon.png" />New Event</a>
		<a href="#new_announcement_modal" class="facebox icon"><img src="/img/icon.png" />New Announement</a>
		<a href="#" class="icon"><img src="/img/icon.png" />Edit Group</a>
		{{if $user_is_member}}
		<a href="#leave_group_modal" class="facebox icon"><img src="/img/icon.png" />Leave Group</a>
		{{elseif $group_view->private}}
		<a href="#" class="icon"><img src="/img/icon.png" />Request to Join Group</a>
		{{else}}
		<a href="#join_group_modal" class="facebox icon"><img src="/img/icon.png" />Join Group</a>
		{{/if}}
	</div>
</div>
<div class="clear"></div>

<div class="grid_3">
	<img src="/img/group/{{$group_view->imgsrc}}" class="fillx"/>
	<div class="box">
		<h2>Details</h2>
		<p>Established: 2009*</p>
		<p>Active Group Since: {{$group_view->join}}</p>
		<p>Total Members: 1*</p>
		<p>Total Routes: 0*</p>
	</div>
	<div class="box">
		<h2>Description</h2>
		<p>{{$group_view->desc}}</p>
	</div>
</div>
<div class="grid_5">
	<div class="box">
		<h2>Announcements</h2>
		{{if $group_view_anoun}}
		<p>On {{$group_view_anoun.anoun_date|date_format}}</p>
		<p>{{$group_view_anoun.anoun}}</p>
		{{/if}}
	</div>
	<div class="box">
		<h2>Events</h2>
	</div>
	<div class="box">
		<h2>Routes</h2>
	</div>
	<div class="box">
		<h2>Members</h2>
	</div>
</div>
<div class="grid_4">
	<div class="box">
		<h2>Recent Activity</h2>
	</div>
</div>
<div class="clear"></div>

<div id="new_announcement_modal" style="display:none">
	<h2>New Announcement</h2>
	<form action="/group/action_new_announcement" method="post" id="group_announcement_form">
		<input type="hidden" name="gid" value="{{$group_view->gid}}">
		<input type="hidden" name="action" value="new_announcement" />
		
		<p><label>Announcement: </label></p>
		<p><textarea rows="5" cols="35" name="gm_anoun"></textarea></p>
		
		<p>
			<input type="submit" value="Create">
			<input type="button" value="Cancel" onclick="$.facebox.close()" />
		</p>
	</form>
</div>
<div id="new_event_modal" style="display:none">
	<h2>New Event</h2>
	<form action="/group/action_new_event" method="post" id="group_event_form">
		<input type="hidden" name="g_id" value="{{$group_view->gid}}">
		<input type="hidden" name="action" value="new_event" />
		<p><label>Title: </label></p>
		<p><input type="text" name="e_title"></p>
		
		<p><label>Date: </label></p>
		<p><input type="text" name="e_date"></p>
		
		<p><label>Description: </label></p>
		<p><textarea rows="5" cols="35" name="e_desc"></textarea></p>
		
		<p>
			<input type="submit" value="Create">
			<input type="button" value="Cancel" onclick="$.facebox.close()" />
		</p>
	</form>
</div>
<div id="join_group_modal" style="display:none">
	<h2>Are you sure you want to join {{$group_view->name}}</h2>
	<form action="/group/join" method="post" id="group_join_form">
		<input type="hidden" name="gid" value="{{$group_view->gid}}">
		<p>
			<input type="submit" value="Yes">
			<input type="button" value="No" onclick="$.facebox.close()" />
		</p>
	</form>
</div>
<div id="leave_group_modal" style="display:none">
	<h2>Are you sure you want to leave {{$group_view->name}}</h2>
	<form action="/group/leave" method="post" id="group_join_form">
		<input type="hidden" name="gid" value="{{$group_view->gid}}">
		<p>
			<input type="submit" value="Yes">
			<input type="button" value="No" onclick="$.facebox.close()" />
		</p>
	</form>
</div>