<h1>Training front page</h1>

<h2>{{$calendar->getFirstDayOnCalendar()|date_format}}</h2>
<h2>{{$calendar->getLastDayOnCalendar()|date_format}}</h2>

<ul id="training_cal">
{{foreach from=$calendar->getDayHeaders() item=header}}
<li class="month_header">{{$header}}</li>
{{/foreach}}

{{foreach from=$calendar->getLastMonthDays() item=day}}
<li class="inactiveMonth">{{$day}}</li>
{{/foreach}}

{{foreach from=$calendar->days item=act key=day}}
<li class="activeMonth">
	<div class="day_header">{{$day}}</div>
	{{if $act}}
	
	{{foreach from=$act item=item}}
	
	<div class="item">{{$item}}</div>
	
	{{/foreach}}
	
	{{/if}}
</li>
{{/foreach}}

{{foreach from=$calendar->getNextMonthDays() item=day}}
<li class="inactiveMonth">{{$day}}</li>
{{/foreach}}

</ul>