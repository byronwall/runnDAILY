{{foreach from=$group_list item=group}}
	<p><a href="/group/view?gid={{$group->gid}}"><img src="/img/group/{{$group->imgsrc}}" style="width: 75px;" />{{$group->name}}</a></p>
{{/foreach}}