<h4>Search Results</h4>
{{foreach from=$user_list item=user}}
	<div class="float_left">
		<p>
			<a href="/community/view_user?uid={{$user.u_uid}}" class="icon"><img src="/img/icon/user.png" />{{$user.u_username}}</a>
		</p>
	</div>
{{foreachelse}}
	<p>No results were found. Please search again.</p>
{{/foreach}}
<div class="clear"></div>