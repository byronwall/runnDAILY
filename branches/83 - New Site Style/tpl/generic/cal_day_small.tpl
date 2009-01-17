<td class="cal_day_current">
	<a href="#" class="cal_head_link">17</a>
	
	{{foreach from=$day->items item=item}}
		<a href="/training/view.php?tid={{$item->tid}}" class="cal_box_link"></a>
	{{/foreach}}
</td>