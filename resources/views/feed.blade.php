<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ config('app.name') }} — Blog</title>
        <link>{{ route('blog.index') }}</link>
        <atom:link href="{{ route('blog.feed') }}" rel="self" type="application/rss+xml" />
        <description>Artikel terbaru dari {{ config('app.name') }}</description>
        <language>id-ID</language>
        <lastBuildDate>{{ now()->toRfc2822String() }}</lastBuildDate>

        @foreach ($posts as $post)
            <item>
                <title>{{ $post->title }}</title>
                <link>{{ route('blog.show', $post->slug) }}</link>
                <guid isPermaLink="true">{{ route('blog.show', $post->slug) }}</guid>
                <pubDate>{{ $post->published_at->toRfc2822String() }}</pubDate>
                <author>{{ $post->author->email }} ({{ $post->author->name }})</author>
                @if ($post->category)
                    <category>{{ $post->category->name }}</category>
                @endif
                <description>
                    <![CDATA[{{ $post->excerpt }}]]>
                </description>
                <content:encoded xmlns:content="http://purl.org/rss/1.0/modules/content/">
                    <![CDATA[{!! $post->content !!}]]>
                </content:encoded>
            </item>
        @endforeach
    </channel>
</rss>
