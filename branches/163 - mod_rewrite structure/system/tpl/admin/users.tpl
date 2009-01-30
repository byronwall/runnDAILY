<h1>Manage Users</h1>

<form id="users_form" action="/admin/users.php" method="get">
	<ul>
		<li>username: <input type="text" name="u_username" value="{{$smarty.get.u_username}}"/></li>
		<li>email: <input type="text" name="u_email" value="{{$smarty.get.u_email}}"/></li>
		<li>uid: <input type="text" name="u_uid" value="{{$smarty.get.u_uid}}"/></li>
		<li>type: 
			<input type="text" name="u_type[0]" value="{{$smarty.get.u_type[0]}}"/>
			<input type="text" name="u_type[1]" value="{{$smarty.get.u_type[1]}}" />
		</li>
		<li>last access: 
			<input type="text" name="u_date_access[0]" value="{{$smarty.get.u_date_access[0]}}"/>
			<input type="text" name="u_date_access[1]" value="{{$smarty.get.u_date_access[1]}}" />
		</li>
		<li><input type="submit" value="search"/></li>
	</ul>
</form>

<div id="result" style="background-color:green"></div>
<table>
<thead>
	<td>username</td><td>last access</td><td>email</td><td>type</td>
</thead>
{{foreach from=$users item=user}}
<form class="user_item" action="/user/update" method="post">
	<input type="hidden" name="format" value="ajax" />
	<input type="hidden" name="u_uid" value="{{$user->uid}}" />
	<tr class="{{cycle values=' ,alt_row'}}">
		<td>{{$user->username}}</td>
		<td>{{$user->date_access|date_format}}</td>
		<td><input type="text" value="{{$user->email}}" name="u_email" class="email"/></td>
		<td><input type="text" value="{{$user->type}}" name="u_type" class="number required"/></td>
		<td><input type="submit" value="update" /></td>
		<td><a href="#TB_inline?&inlineId=delete_modal&modal=true" class="form" rel="{{$user->uid}}">delete</a></td>
	</tr>
</form>
{{foreachelse}}
No stats found!
{{/foreach}}
</table>

<div id="delete_modal" style="display:none">
	<form action="/user/delete" method="post" id="form_delete">
		<h1>Are you sure you want to delete that user?  This really will delete it.</h1>
		<input type="hidden" name="action" value="delete" />
		<input type="hidden" name="u_uid" value="-1" />		
		<input type="submit" value="yes, delete" />
		<input type="button" value="cancel" onclick="tb_remove()" />
	</form>
</div>

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
		$("#form_delete [name=u_uid]").val(this.rel);
		tb_show("", this.href, false);
		return false;
	});
	$("#form_delete").validate({
		submitHandler: function(form){
			$(form).ajaxSubmit({
				target: "#result"
			});
			tb_remove();
		}
	});
});

</script>