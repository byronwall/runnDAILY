<div class="grid_12">
	<h2 id="page-heading">Administrative Control Panel</h2>
</div>
<div class="clear"></div>
<div class="grid_12">
	<div class="actions">
		<a href="/admin/update_stats"><img class="icon" src="/img/icon.png" />Update Stats</a>
		<a href="/admin/pages"><img class="icon" src="/img/icon.png" />Manage Pages</a>
		<a href="/admin/users"><img class="icon" src="/img/icon.png" />Manage Users</a>
		<a href="/admin/modules"><img class="icon" src="/img/icon.png" />Manage Modules</a>
		<a href="/admin/feedback"><img class="icon" src="/img/icon.png" />See User Feedback</a>
		<a href="/admin/stats"><img class="icon" src="/img/icon.png" />Site Stats</a>
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