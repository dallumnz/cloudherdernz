<?php

namespace App\Http\Controllers;

use App\Models\NewsletterPost;
use App\Models\Post;
use Illuminate\Http\Request;

class NewsletterViewController extends Controller
{
    public function show(string $id)
    {
        $newsletterPost = NewsletterPost::find($id);

        if (!$newsletterPost) {
            abort(404, 'Newsletter not found');
        }

        // Get the parent post
        $post = $newsletterPost->post ?? null;

        if (!$post) {
            abort(404, 'Newsletter post not found');
        }

        // Record open if not already tracked
        $newsletterPost->recordOpen();

        return view('newsletters.show', compact('post', 'newsletterPost'));
    }
}
