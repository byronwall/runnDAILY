<h1>Training front page</h1>

<h2>{{$calendar->getFirstDayOnCalendar()|date_format}}</h2>
<h2>{{$calendar->getLastDayOnCalendar()|date_format}}</h2>

{{
	include file=generic/calendar.tpl 
	calendar=$calendar
	day_week_template="training/cal_day.tpl" 
	day_mon_template="training/cal_day.tpl"
}}