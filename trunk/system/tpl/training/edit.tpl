<h4>Edit Entry</h4>

<div id="training_edit_modal">
	<form action="/training/action_edit" method="post" id="training_edit_form">
		<input type="hidden" name="t_tid" value="{{$t_item->tid}}" />
		{{if $t_item->rid}}
			<input type="hidden" name="t_rid" value="{{$t_item->rid}}" />
		{{/if}}
		<ul id="train_errors" class="error_box"></ul>
		
		<p>
			<label>Time</label>
			<input type="text" name="t_time" value="{{$t_item->time|time_format:false}}" />
		</p>
		<p>
			<label>Date</label>
			<input type="text" name="t_date" value="{{$t_item->date|date_format}}" />
		</p>
		<p>
			<label>Distance</label>
			<input type="text" name="t_distance" value="{{$t_item->distance}}" />
		</p>
		<p><label>Activity Type: </label>
			{{html_options name="t_type" options=$t_types selected=$t_item->type}}
		</p>
		<p>
			<input type="submit" value="Update" />
			<input type="button" value="Cancel" onclick="$.facebox.close()" />
		</p>
	</form>
</div>
<h4>Delete Entry</h4>

<div id="training_delete_modal">
	<form action="/training/action_delete" method="post">
		<input type="hidden" name="t_tid" value="{{$t_item->tid}}" />
		<p>
			<input type="checkbox" id="delete_check" value="1">
			<label for="delete_check">I would like to delete this entry.</label>		
		</p>
		<p>
			<input type="submit" value="Delete" id="delete_submit" disabled/>
		</p>
	</form>
</div>

<script type="text/javascript">

$(document).ready(function(){
	$("#training_edit_form").validate({
		onkeyup: false,
		onclick: true,
		onfocusout: false,
		rules: {
			t_time: {required: true},
			t_date: {required: true},
			t_distance: {
				required: true,
				number: true
			}
		},
		messages: {
			t_time: {required: "Please enter a time"},
			t_date: {required: "Please enter a date"},
			t_distance: {
				required: "Please enter a distance.",
				number: "Distance must be a number."
			}
		},
		errorLabelContainer: "#train_errors",
		wrapper: "li",
		errorClass: "error",
		submitHandler: function(form){
			$("input[type=text]").each( function(){
				if($(this).val() == "") $(this).attr("disabled", true);
			});
			form.submit();
		}
	});
	$("#delete_check").click(function(){
		if($(this).is(":checked")){
			$("#delete_submit").removeAttr("disabled");
		}
		else{
			$("#delete_submit").attr("disabled", true);
		}
	});			
});
</script>