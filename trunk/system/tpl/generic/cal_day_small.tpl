<!--<td class="cal_day_current">-->
<!--	<a href="#" class="cal_head_link">{{$day->day_num}}</a>-->
<!--	-->
<!--	{{foreach from=$day->items item=item}}-->
<!--		<a href="/training/view/{{$item->tid}}" class="cal_box_link"></a>-->
<!--	{{/foreach}}-->
<!--</td>-->

<td class="activeMonth">
	<div class="day_header">
		{{$day->timestamp|date_format:"%d"}}
	</div>
	
	{{foreach from=$day->items item=item}}
	<div class="item">
		<a href="/training/view?tid={{$item->tid}}">{{$item->distance}}</a>
	</div>
	{{/foreach}}
</td>