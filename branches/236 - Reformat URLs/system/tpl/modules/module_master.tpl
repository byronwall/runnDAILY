<div class="grid_3">
	{{foreach from=$modules.3 item=module}}
		{{include file="modules/module_single.tpl" module=$module}}
	{{foreachelse}}
	&nbsp;
	{{/foreach}}
</div>

<div class="grid_5">
	{{foreach from=$modules.5 item=module}}
		{{include file="modules/module_single.tpl" module=$module}}
	{{foreachelse}}
	&nbsp;
	{{/foreach}}
</div>

<div class="grid_4">
	{{foreach from=$modules.4 item=module}}
		{{include file="modules/module_single.tpl" module=$module}}
	{{foreachelse}}
	&nbsp;
	{{/foreach}}
</div>