{{if $item->cid == 1}}
<li class="recent_activity_item">You {{$item->desc}} <a href="/routes/view?rid={{$item->route->id}}">{{$item->route->name}}</a>. {{$item->familiar}}.</li>
{{elseif $item->cid == 3}}
<li>You <a href="/training/view?tid={{$item->tid}}">{{$item->desc}}</a>. {{$item->familiar}}.</li>
{{elseif $item->cid == 4}}
<li>You added <a href="/community/view_user?uid={{$item->xid}}">User</a> as a friend. {{$item->familiar}}.</li>
{{elseif $item->cid == 2}}
<li>cid is 2</li>
{{/if}}