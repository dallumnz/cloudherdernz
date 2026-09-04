<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsExportController extends Controller
{
    public function export(Request $request): StreamedResponse
    {
        $this->authorize('view analytics');

        $table = config('request-analytics.database.table', 'request_analytics');

        $query = DB::table($table);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('visited_at', [
                $request->date('start_date')->startOfDay(),
                $request->date('end_date')->endOfDay(),
            ]);
        } else {
            $days = (int) $request->input('date_range', 30);
            $query->where('visited_at', '>=', now()->subDays($days)->startOfDay());
        }

        if ($request->filled('request_category')) {
            $query->where('request_category', $request->input('request_category'));
        }

        $query->orderBy('visited_at', 'desc');

        $filename = 'analytics_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $columns = [
            'id',
            'visited_at',
            'path',
            'page_title',
            'ip_address',
            'http_method',
            'request_category',
            'referrer',
            'country',
            'city',
            'language',
            'operating_system',
            'browser',
            'device',
            'screen',
            'response_time',
            'session_id',
            'visitor_id',
            'user_id',
            'query_params',
        ];

        return response()->stream(function () use ($query, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $query->chunk(1000, function ($rows) use ($file, $columns) {
                foreach ($rows as $row) {
                    $line = [];
                    foreach ($columns as $col) {
                        $line[] = $row->$col ?? '';
                    }
                    fputcsv($file, $line);
                }
            });

            fclose($file);
        }, 200, $headers);
    }
}
