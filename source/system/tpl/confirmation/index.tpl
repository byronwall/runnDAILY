<div class="grid_12">
	<h2 id="page-heading">Pending Requests</h2>
</div>
<div class="clear"></div>

<div class="grid_12">
	<h4>Inbound</h4>
	{{foreach from=$confirm_to item=items key=type}}
		<h5>{{$type}}</h5>
		{{foreach from=$items item=confirm}}
			<div>
				<div id="confirm_{{$confirm->cid}}">
				from : {{$confirm->user_from->username}}
				date : {{$confirm->date_created|date_format}}
				cid : {{$confirm->cid}}
				</div>
				<a href="#confirm_{{$confirm->cid}}" rel="{{$confirm->cid}}" class="box-confirm">confirm</a>
				<a rel="#confirm_{{$confirm->cid}}" rel="{{$confirm->cid}}" class="box-deny">deny</a>
			</div>
		{{/foreach}}
	{{foreachelse}}
		There are currently no requests for you to handle.
	{{/foreach}}
	<h4>Outbound</h4>
	{{foreach from=$confirm_from item=items key=type}}
		<h5>{{$type}}</h5>
		{{foreach from=$items item=confirm}}
			<div>
				<div id="confirm_{{$confirm->cid}}">
				to : {{$confirm->user_to->username}}
				date : {{$confirm->date_created|date_format}}
				cid : {{$confirm->cid}}
				</div>
				<a href="#confirm_{{$confirm->cid}}" rel="{{$confirm->cid}}" class="box-cancel">cancel</a>
			</div>
		{{/foreach}}
	{{foreachelse}}
		You do not have any requests for others.
	{{/foreach}}
</div>
<div class="clear"></div>

<div id="confirm-message" style="display:none">
	<form action="/confirmation/actionProcess" method="post" class="ajax">
		<input type="hidden" name="cid" value="">
		<input type="hidden" name="confirm" value="true">
		<p>Are you sure to want to confirm this request?</p>
		<div></div>
		<p><input type="submit" value="confirm"></p>
		<p><input type="button" value="cancel" onclick="$.facebox.close()"></p>
	</form>
</div>
<div id="deny-message" style="display:none">
	<form action="/confirmation/actionProcess" method="post" class="ajax">
		<input type="hidden" name="cid" value="">
		<input type="hidden" name="confirm" value="false">
		<p>Are you sure to want to deny this request?</p>
		<div></div>
		<p><input type="submit" value="deny"></p>
		<p><input type="button" value="cancel" onclick="$.facebox.close()"></p>
	</form>
</div>
<div id="cancel-message" style="display:none">
	<form action="/confirmation/actionCancel" method="post" class="ajax">
		<input type="hidden" name="cid" value="">
		<p>Are you sure to want to cancel this request?</p>
		<div></div>
		<p><input type="submit" value="cancel request"></p>
		<p><input type="button" value="do not cancel" onclick="$.facebox.close()"></p>
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