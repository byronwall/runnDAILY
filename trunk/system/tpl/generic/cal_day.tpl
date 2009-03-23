<td class="activeMonth">
	<div class="day_header">
		{{$day->timestamp|date_format}}
	</div>
	
	{{foreach from=$day->items item=item}}
	<div class="item">
		<a href="/training/view?tid={{$item->tid}}">{{$item->distance}}</a>
	</div>
	{{/foreach}}
</td>