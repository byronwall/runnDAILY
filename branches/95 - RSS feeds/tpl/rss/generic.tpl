<?xml version="1.0" encoding="ISO-8859-1"?>
<rss version="2.0">
	<channel>
	
		<title>{{$RssFeed->title}}</title>
		<link>{{$RssFeed->link}}</link>
		<description>{{$RssFeed->description}}</description>
		<language>en-us</language>
		<copyright>© Byron and Chandler</copyright>
		<pubDate>{{"r"|@date:$smarty.now}}</pubDate>
		<ttl>5</ttl>
		
		{{if $RssFeed->hasImage}}
		<image>
		<title>{{$RssFeed->image->title}}</title>
		<link>{{$RssFeed->image->link}}</link>
		<url>{{$RssFeed->image->url}}</url>
		<width>{{$RssFeed->image->width}}</width>
		<height>{{$RssFeed->image->height}}</height>
		<description>{{$RssFeed->image->description}}</description>
		</image>
		{{/if}}
		
		{{foreach from=$RssFeed->items item=feedItem}}
		<item>
		<title>{{$feedItem->title}}</title>
		<guid isPermaLink="false">{{$feedItem->guid}}</guid>
		<link>{{$feedItem->link}}</link>
		<description>{{$feedItem->description}}</description>
		<pubDate>{{"r"|@date:$smarty.now}}</pubDate>
		</item>
		{{/foreach}}
	
	</channel>
</rss>
