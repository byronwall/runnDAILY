<div class="grid_12">
	<h2 id="page-heading">Pending Requests</h2>
</div>
<div class="clear"></div>

<div class="grid_12">
	<h2>Inbound</h2>
	{{foreach from=$confirm_to item=items key=type}}
		<h5>{{$type}}</h5>
		{{foreach from=$items item=confirm}}
			<p>
				<span id="confirm_{{$confirm->cid}}"><img src="/img/icon/user_friend.png" /> <span class="bold">{{$confirm->user_from->username}}</span> on {{$confirm->date_created|date_format}}</span>
				<a href="#confirm_{{$confirm->cid}}" rel="{{$confirm->cid}}" class="box-confirm icon"><img src="/img/icon/check.png" />Accept</a>
				<a href="#confirm_{{$confirm->cid}}" rel="{{$confirm->cid}}" class="box-deny icon"><img src="/img/icon/delete.png" />Deny</a>
			</p>
		{{/foreach}}
	{{foreachelse}}
		<p>There are currently no requests for you to handle.</p>
	{{/foreach}}
	
	<h2>Outbound</h2>
	{{foreach from=$confirm_from item=items key=type}}
		<h5>{{$type}}</h5>
		{{foreach from=$items item=confirm}}
			<p>
				<span id="confirm_{{$confirm->cid}}"><img src="/img/icon/user_friend.png" /> <span class="bold">{{$confirm->user_to->username}}</span> on {{$confirm->date_created|date_format}}</span>
				<a href="#confirm_{{$confirm->cid}}" rel="{{$confirm->cid}}" class="box-cancel icon"><img src="/img/icon/delete.png" />Delete</a>
			</p>
		{{/foreach}}
	{{foreachelse}}
		<p>You have not made any requests to other users.</p>
	{{/foreach}}
</div>
<div class="clear"></div>

<div id="confirm-message" style="display:none">
	<form action="/confirmation/actionProcess" method="post" class="ajax">
		<input type="hidden" name="cid" value="">
		<input type="hidden" name="confirm" value="true">
		<h5>Are you sure to want to confirm this request?</h5>
		<div></div>
		<p>
			<input type="submit" value="Confirm">
			<input type="button" value="Cancel" onclick="$.facebox.close()">
		</p>
	</form>
</div>
<div id="deny-message" style="display:none">
	<form action="/confirmation/actionProcess" method="post" class="ajax">
		<input type="hidden" name="cid" value="">
		<input type="hidden" name="confirm" value="false">
		<h5>Are you sure to want to deny this request?</h5>
		<div></div>
		<p>
			<input type="submit" value="Deny">
			<input type="button" value="Cancel" onclick="$.facebox.close()">
		</p>
	</form>
</div>
<div id="cancel-message" style="display:none">
	<form action="/confirmation/actionCancel" method="post" class="ajax">
		<input type="hidden" name="cid" value="">
		<h5>Are you sure to want to delete this request?</h5>
		<div></div>
		<p>
			<input type="submit" value="Delete">
			<input type="button" value="Cancel" onclick="$.facebox.close()">
		</p>
	</form>
</div>

<script type="text/javascript">
$(function(){
	var actions = {
		"/confirmation/actionCancel": function(data){
			//this function expects a JSON object with [result, cid]
			if(data.result){
				var element = "#confirm_" + data.cid;
				$.facebox("Your request has been cancelled.", 500);
				$(element).parent().fadeOut("slow").remove();
			}
		},
		"/confirmation/actionProcess": function(data){
			//this function expects a JSON object with [result, cid]
			if(data.result){
				var element = "#confirm_" + data.cid;
				$.facebox("Your request has been processed.", 500);
				$(element).parent().fadeOut("slow").remove();
			}
		}
	};

	var forms = ["confirm", "deny", "cancel"];
	$.each(forms, function(){
		var form_element = "#"+this+"-message";
		$("a.box-"+this).click(function(){
			var element = "#" + this.href.split("#")[1];
			$(form_element + " div").replaceWith($(element).clone());
			$(form_element + " [name=cid]").val(this.rel);
			$.facebox({div:form_element});
			return false;
		});
	});
	
	$("form.ajax").ajaxForm({
		success: function(data){
			if(actions[this.url]){
				actions[this.url](data);
			}
		},
		dataType: "json"
	});
});
</script>