<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;

class RssFeedController extends Controller
{
    /**
     * Generate and return the RSS feed XML.
     */
    public function index(): Response
    {
        $posts = Post::query()
            ->published()
            ->with(['taxonomyTerms', 'media'])
            ->orderByDesc('published_at')
            ->limit(20)
            ->get();

        return response()->view('rss.feed', [
            'posts' => $posts,
        ], 200, [
            'Content-Type' => 'application/rss+xml',
        ]);
    }
}
