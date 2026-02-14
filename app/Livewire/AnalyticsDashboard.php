<?php

namespace App\Livewire;

use App\Models\AnalyticsEvent;
use Illuminate\View\View;
use Livewire\Component;

class AnalyticsDashboard extends Component
{
    public int $totalVisits = 0;

    public int $uniqueVisitors = 0;

    public int $todayVisits = 0;

    public array $topPages = [];

    public array $recentVisits = [];

    public function mount(): void
    {
        $this->loadStats();
    }

    protected function loadStats(): void
    {
        $this->totalVisits = AnalyticsEvent::count();
        $this->uniqueVisitors = AnalyticsEvent::distinct('ip_address')->count('ip_address');
        $this->todayVisits = AnalyticsEvent::whereDate('created_at', today())->count();

        $this->topPages = AnalyticsEvent::select('url')
            ->selectRaw('COUNT(*) as visits')
            ->groupBy('url')
            ->orderByDesc('visits')
            ->limit(10)
            ->get()
            ->toArray();

        $this->recentVisits = AnalyticsEvent::with('user')
            ->latest()
            ->limit(20)
            ->get()
            ->toArray();
    }

    public function render(): View
    {
        return view('livewire.analytics-dashboard');
    }
}
