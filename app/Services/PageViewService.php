<?php

namespace App\Services;

use App\Models\AnalyticsEvent;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PageViewService
{
    /**
     * Count the number of views for a specific URL.
     */
    public function countViews(string $url): int
    {
        return AnalyticsEvent::where('url', $url)->count();
    }

    /**
     * Get the top pages by view count.
     *
     * @return Collection<int, object{url: string, views: int}>
     */
    public function topPages(int $limit = 10): Collection
    {
        return AnalyticsEvent::query()
            ->select('url', DB::raw('COUNT(*) as views'))
            ->groupBy('url')
            ->orderByDesc('views')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => (object) [
                'url' => $row->url,
                'views' => (int) $row->views,
            ]);
    }
}
