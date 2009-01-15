<h1>Training front page</h1>

<h2>{{$calendar->getFirstDayOnCalendar()|date_format}}</h2>
<h2>{{$calendar->getLastDayOnCalendar()|date_format}}</h2>

{{
	include file=generic/calendar_small.tpl 
	calendar=$calendar
	day_week_template="generic/cal_day_small.tpl" 
	day_mon_template="generic/cal_day_small.tpl"
}}