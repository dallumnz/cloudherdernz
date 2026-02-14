<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ config('app.name', 'Cloudherder') }}</title>
        <link>{{ config('app.url') }}</link>
        <description>Latest posts from {{ config('app.name', 'Cloudherder') }}</description>
        <language>en</language>
        <lastBuildDate>{{ now()->toRssString() }}</lastBuildDate>
        <atom:link href="{{ route('feed') }}" rel="self" type="application/rss+xml" />

        @foreach ($posts as $post)
            <item>
                <title>{{ $post->title }}</title>
                <link>{{ route('posts.show', $post) }}</link>
                <guid isPermaLink="true">{{ route('posts.show', $post) }}</guid>
                <pubDate>{{ $post->published_at->toRssString() }}</pubDate>
                <description><![CDATA[{{ $post->excerpt ?? $post->content }}]]></description>

                @foreach ($post->taxonomyTerms as $term)
                    <category>{{ $term->name }}</category>
                @endforeach
            </item>
        @endforeach
    </channel>
</rss>
