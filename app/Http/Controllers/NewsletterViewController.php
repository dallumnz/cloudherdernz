<?php

namespace App\Http\Controllers;

use App\Models\NewsletterPost;
use Illuminate\Http\Response;

class NewsletterViewController extends Controller
{
    public function show(string $uuid)
    {
        $newsletterPost = NewsletterPost::find($uuid);

        if (! $newsletterPost) {
            abort(404, 'Newsletter not found');
        }

        // Get the parent post
        $post = $newsletterPost->posts()->first();

        if (! $post) {
            abort(404, 'Newsletter post not found');
        }

        // Record open if not already tracked
        $newsletterPost->recordOpen();

        return view('newsletters.show', compact('post', 'newsletterPost'));
    }

    public function trackOpen(string $uuid): Response
    {
        $newsletterPost = NewsletterPost::find($uuid);

        if ($newsletterPost) {
            $newsletterPost->recordOpen();
        }

        // Return 1x1 transparent GIF
        $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');

        return response($pixel, 200, [
            'Content-Type' => 'image/gif',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
