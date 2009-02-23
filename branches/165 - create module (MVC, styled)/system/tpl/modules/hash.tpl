Modules::hash = array(
	{{foreach from=$modules item=module}}
		{{$module->code}}=>{{$module->name}},
	{{/foreach}}
);