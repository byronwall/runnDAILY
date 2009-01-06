<h1>viewing details</h1>

<h2>general info</h2>
<div>distance: {{$item->distance}} mi</div>
<div>time: {{$item->time|time_format}}</div>
<div>pace: {{$item->pace}} mph</div>
<div>date: {{$item->date|date_format}}</div>
<a href="/routes/view.php?id={{$item->route->id}}">view {{$item->route->name}}</a>

<h2>better info</h2>
<h3>other logs for the user that week</h3>
{{
	include file=generic/calendar.tpl 
	calendar=$calendar 
	day_week_template="training/cal_day.tpl" 
	day_mon_template="training/cal_day.tpl"
}}
{{if $item->getIsOwnedBy($currentUser->userID)}}
<h2>manage the log</h2>
<a href="/training/manage.php?tid={{$item->tid}}">manage log entry</a>
{{/if}}
<h3>coming soon</h3>
<h4>general info</h4>
pace<br>
distance<br>
date run<br>
calories burned<br>
<h4>more detailed info</h4>
line charts of this log versus others (pace vs min/max)<br>
calendar for the week of the event<br>
milestones affected by the log<br>
links to schedule the same event at a later date<br>
<h4>community</h4>
links to send the log to a friend<br>
preview of the route run<br>
graph of the elevation<br>
charts of how this log affects overall stats<br>
graph of this time to other runs<br>