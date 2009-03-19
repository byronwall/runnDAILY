<div class="grid_12">
	<h2 id="page-heading">Administrative Control Panel</h2>
</div>
<div class="clear"></div>

<div class="grid_12">
	<div class="actions">
		<a href="/group/create" class="icon"><img src="/img/icon.png" />Create Group</a>
		<a href="/admin/update_stats" class="icon"><img	src="/img/icon.png" />Update Stats</a>
		<a href="/admin/pages" class="icon"><img src="/img/icon.png" />Manage Pages</a>
		<a href="/admin/users" class="icon"><img src="/img/icon.png" />Manage Users</a>
		<a href="/admin/feedback" class="icon"><img src="/img/icon.png" />See User Feedback</a>
		<a	href="/admin/stats" class="icon"><img src="/img/icon.png" />Site Stats</a>
	</div>
</div>
<div class="clear"></div>

<script type="text/javascript">

$(document).ready(	function(){
	$("a.post").click(	function(){
		$.post( this.href, {ajax:true}, function(data){
			alert("updater says:" + data);
		});
		return false;
	});
});

</script>