{{if $calendar->cal_type == CAL_MONTH}}
	<table id="cal_month_small">
		<tr>
			{{foreach from=$calendar->getDayHeaders() item=header}}
				<td class="cal_month_head">{{$header}}</td>
			{{/foreach}}
		</tr>
		
		{{foreach from=$calendar->days item=day}}
			{{if ($day->day_num%7) eq 1}}
				<tr class="cal_day_head">
			{{/if}}
				
			{{include file="$day_mon_template"}}
			
			{{if $day->day_num is div by 7}}
				</tr>
			{{/if}}
		{{/foreach}}	
	</table>
{{elseif $calendar->cal_type == CAL_WEEK}}	
	<table id="training_cal">
		<tr>
			{{foreach from=$calendar->getDayHeaders() item=day}}
				<td class="month_header">{{$day}}</td>
			{{/foreach}}
			</tr>
		<tr>
			{{foreach from=$calendar->days item=day}}
				{{include file="$day_week_template"}}	
			{{/foreach}}
		</tr>
	</table>
{{/if}}
