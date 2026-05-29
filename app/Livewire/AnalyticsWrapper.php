<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class AnalyticsWrapper extends Component
{
    public function render(): View
    {
        // Get the same data the analytics controller gets
        $params = [];

        if (request()->has('start_date') && request()->has('end_date')) {
            $params['start_date'] = request('start_date');
            $params['end_date'] = request('end_date');
        } else {
            $dateRangeInput = request('date_range', 30);
            $dateRange = is_numeric($dateRangeInput) && (int) $dateRangeInput > 0
                ? (int) $dateRangeInput
                : 30;
            $params['date_range'] = $dateRange;
        }

        $params['request_category'] = request('request_category', null);

        $service = app(\MeShaon\RequestAnalytics\Services\DashboardAnalyticsService::class);
        $data = $service->getDashboardData($params);

        return view('livewire.analytics', $data);
    }
}
