{{foreach from=$logs item=log}}
	<li>
		{{$log->aid}}
	</li>
{{/foreach}}
{{if count($logs)}}
	<li>
		<a href="/log/browse?uid={{$uid}}&page={{$page_no}}&format=ajax" class="ajax">see more in this table</a>
	</li>
{{/if}}