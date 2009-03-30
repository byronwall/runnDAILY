<div class="grid_12">
	<h2 id="page-heading">Edit Your Modules</h2>
</div>

<div class="clear"></div>

<div class="grid_12">
	<div class="box">
		<h2>Available Modules</h2>
		<form action="/user/action_update_modules" method="post">
		<table>
			<thead>
				<tr>
					<th>Name</th>
					<th>Home</th>
					<th>Routes</th>
					<th>Training</th>
					<th>Community</th>
				</tr>
			</thead>
			{{foreach from=$modules item=module key=code}}
				<tr>
					<td>
						{{$module->title}}					
					</td>
					<td><input type="checkbox" name="modules[home][]" value="{{$module->code}}" {{if $user_modules.home[$code]}}checked{{/if}}></td>
					<td><input type="checkbox" name="modules[routes][]" value="{{$module->code}}" {{if $user_modules.routes[$code]}}checked{{/if}}></td>
					<td><input type="checkbox" name="modules[training][]" value="{{$module->code}}" {{if $user_modules.training[$code]}}checked{{/if}}></td>
					<td><input type="checkbox" name="modules[community][]" value="{{$module->code}}" {{if $user_modules.community[$code]}}checked{{/if}}></td>
				</tr>
			{{/foreach}}
		</table>
		
		<input type="submit" value="save settings">
		
		</form>
	</div>
</div>

<div class="clear"></div>