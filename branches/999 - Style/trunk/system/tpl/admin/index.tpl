<div class="grid_12">
	<h2 id="page-heading">Administrative Control Panel</h2>
</div>
<div class="clear"></div>
<div class="grid_12">
	<div class="actions">
		<a href="/admin/update_stats">Update Stats</a>
		<a href="/admin/pages">Manage Pages</a>
		<a href="/admin/users">Manage Users</a>
		<a href="/admin/feedback">See User Feedback</a>
		<a href="/admin/stats">Site Stats</a>
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