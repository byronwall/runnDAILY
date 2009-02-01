<h1>Admin Control Panel</h1>

<h2>Actions</h2>
<ul>
	<li><a href="/admin/update_stats" class="post">Update Stats (this really does update them)</a></li>
	<li><a href="/admin/pages">Manage Pages</a></li>
	<li><a href="/admin/users">Manage Users</a></li>
	<li><a href="/admin/feedback">See User Feedback</a></li>
	<li><a href="/admin/stats">Site Stats</a></li>
</ul>

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