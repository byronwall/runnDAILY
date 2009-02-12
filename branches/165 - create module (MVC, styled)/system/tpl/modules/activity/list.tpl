<ul class="recent_activity_list">
	{{foreach from=$activity item=recent}}
		{{include file="modules/activity/item.tpl" item=$recent}}
	{{foreachelse}}
		<li class="recent_activity_item">No recent activity, do something!</li>
	{{/foreach}}
</ul>