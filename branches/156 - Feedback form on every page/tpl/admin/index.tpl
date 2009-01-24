<h1>Admin Control Panel</h1>

<h2>Actions</h2>
<ul>
	<li><a href="/lib/action_admin.php" rel="update_stats" class="post">Update Stats (this really does update them)</a></li>
	<li><a href="/admin/pages.php">Manage Pages</a></li>
	<li><a href="/admin/users.php">Manage Users</a></li>
	<li><a href="/admin/stats.php">Site Stats</a></li>
</ul>

<script type="text/javascript">

$(document).ready(	function(){
	$("a.post").click(	function(){
		$.post( this.href, {action:this.rel, ajax:true}, function(data){
			alert("updater says:" + data);
		});
		return false;
	});
});

</script>