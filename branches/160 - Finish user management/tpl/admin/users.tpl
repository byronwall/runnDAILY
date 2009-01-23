<h1>Manage Users</h1>

<form id="users_form" action="/admin/users.php" method="get">
	<ul>
		<li>username: <input type="text" name="u_username" value="{{$smarty.get.u_username}}"/></li>
		<li>email: <input type="text" name="u_email" value="{{$smarty.get.u_email}}"/></li>
		<li>uid: <input type="text" name="u_uid" value="{{$smarty.get.u_uid}}"/></li>
		<li>last access: 
			<input type="text" name="u_date_access[0]" value="{{$smarty.get.u_date_access[0]}}"/>
			<input type="text" name="u_date_access[1]" value="{{$smarty.get.u_date_access[1]}}" />
		</li>
		<li><input type="submit" value="search"/></li>
	</ul>
</form>

<div id="result" style="background-color:green"></div>
<h2>updating does not do anything yet!</h2>
<table>
<thead>
	<td>username</td><td>email</td><td>type</td>
</thead>
{{foreach from=$users item=user}}
<form class="user_item" action="/lib/action_users.php" method="post">
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="format" value="ajax" />
	<input type="hidden" name="u_uid" value="{{$user->uid}}" />
	<tr class="{{cycle values=' ,alt_row'}}">
		<td>{{$user->username}}</td>
		<td><input type="text" value="{{$user->email}}" name="u_email" class="email"/></td>
		<td><input type="text" value="{{$user->type}}" name="u_type" class="number required"/></td>
		<td><input type="submit" value="update" /></td>
		<td><a href="/lib/action_users.php" class="form" >delete</a></td>
	</tr>
</form>
{{foreachelse}}
No stats found!
{{/foreach}}
</table>

<script type="text/javascript">

$(document).ready(	function(){
	$("form.user_item").each(function(){
		$(this).validate({
			submitHandler: function(form){
				$(form).ajaxSubmit({
					target: "#result"
				});
			}
		});
	});
	$("#users_form").validate({
		submitHandler : function(form){
			$("#users_form input[type=text]").each( function(){
				if($(this).val() == "") $(this).attr("disabled", true);
			});
			form.submit();
		}
	});
	$("a.form").click(	function(){
		alert("clciked");
		$(this).parents().each( function(){alert($(this).html())});
		return false;
	});
});

</script>