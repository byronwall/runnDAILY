<h2>manage a log</h2>

<form action="/lib/action_training_delete.php" method="post">
<input type="radio" name="action" value="edit" checked="checked">edit</input>
<div>
	<div><label>time</label><input type="text" name="t_time" value="{{$t_item->time|time_format}}" /></div>
	<div><label>date</label><input type="text" name="t_date" value="{{$t_item->date}}" /></div>
	<div><label>distance</label><input type="text" name="t_distance" value="{{$t_item->distance}}" /></div>
</div>
<input type="radio" name="action" value="delete">delete</input>
<input type="submit" value="submit"/>

<input type="hidden" name="tid" value="{{$t_item->tid}}" />
</form>