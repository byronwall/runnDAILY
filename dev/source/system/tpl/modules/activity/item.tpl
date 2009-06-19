{{if $item.l_cid == 1}}
	<li class="recent_activity_item">You {{$item.l_desc}} <a href="/routes/view/{{$item.r_id}}">{{$item.r_name}}</a>. {{$item.familiar}}.</li>
{{elseif $item.l_cid == 3}}
	<li>You {{$item.l_desc}} a <a href="/training/view?tid={{$item.t_tid}}">training entry</a>{{if $item.r_name}} for <a href="/routes/view/{{$item.r_id}}">{{$item.r_name}}</a>{{/if}}. {{$item.familiar}}.</li>
{{elseif $item.l_cid == 4}}
	<li>You added <a href="/community/view_user?uid={{$item.x_uid}}">{{$item.x_username}}</a> as a friend. {{$item.familiar}}.</li>
{{elseif $item.cid == 2}}
	<li>cid is 2</li>
{{elseif $item.l_cid == 5}}
	<li><a href="/group/view?gid={{$item.g_gid}}">{{$item.g_name}}</a> {{$item.l_desc}}. {{$item.familiar}}.</li>
{{/if}}