<?php

namespace App\Http\Controllers;

use App\Models\Series;
use Illuminate\Http\Response;

class PublicSeriesController extends Controller
{
    /**
     * Display a list of published series.
     */
    public function index(): Response
    {
        $series = Series::published()
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return response()->view('series.index', compact('series'));
    }

    /**
     * Display a published series.
     */
    public function show(string $slug): Response
    {
        $series = Series::where('slug', $slug)->published()->first();

        if (! $series) {
            abort(404);
        }

        $posts = $series->posts()->paginate(20);

        return response()->view('series.show', compact('series', 'posts'));
    }
}
