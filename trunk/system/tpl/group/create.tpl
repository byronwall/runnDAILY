<div class="grid_12">
	<h2 id="page-heading">Create Group</h2>
</div>
<div class="clear"></div>

<div class="grid_12">
	<div class="actions">
		<a href="#" class="icon"><img src="/img/icon.png" />Action</a>
	</div>
</div>
<div class="clear"></div>

<div class="grid_12">
	<form enctype="multipart/form-data" action="/group/action_create" method="post">
		<p><label>Name:</label></p>
		<p><input type="text" name="g_name" /></p>
		
		<p><label>Description:</label></p>
		<p><textarea rows="5" cols="35" name="g_desc"></textarea></p>
		
		<p><label>Group Image: </label></p>
		<input type="hidden" name="MAX_FILE_SIZE" value="30000" />
		<p><input type="file" name="img_up"></p>
		
		<p><input type="submit" value="Create" /></p>
	</form>
</div>
<div class="clear"></div>