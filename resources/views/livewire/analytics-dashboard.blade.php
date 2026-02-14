<div class="space-y-6">
    <flux:heading size="xl">Analytics</flux:heading>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <flux:card>
            <div class="flex items-center justify-between">
                <div>
                    <flux:text variant="secondary" size="sm">Total Visits</flux:text>
                    <flux:heading size="2xl">{{ number_format($totalVisits) }}</flux:heading>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <flux:icon name="chart-bar" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex items-center justify-between">
                <div>
                    <flux:text variant="secondary" size="sm">Unique Visitors</flux:text>
                    <flux:heading size="2xl">{{ number_format($uniqueVisitors) }}</flux:heading>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                    <flux:icon name="users" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex items-center justify-between">
                <div>
                    <flux:text variant="secondary" size="sm">Today's Visits</flux:text>
                    <flux:heading size="2xl">{{ number_format($todayVisits) }}</flux:heading>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                    <flux:icon name="calendar" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                </div>
            </div>
        </flux:card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Top Pages --}}
        <flux:card>
            <flux:heading size="lg" class="mb-4">Top Pages</flux:heading>
            @if (count($topPages) > 0)
                <div class="space-y-3">
                    @foreach ($topPages as $page)
                        <div class="flex items-center justify-between py-2 border-b last:border-0">
                            <span class="truncate max-w-xs">{{ $page['url'] }}</span>
                            <span class="text-gray-600">{{ $page['visits'] }} visits</span>
                        </div>
                    @endforeach
                </div>
            @else
                <flux:text variant="secondary">No data yet.</flux:text>
            @endif
        </flux:card>

        {{-- Recent Visits --}}
        <flux:card>
            <flux:heading size="lg" class="mb-4">Recent Visits</flux:heading>
            @if (count($recentVisits) > 0)
                <div class="space-y-3">
                    @foreach ($recentVisits as $visit)
                        <div class="flex items-center justify-between py-2 border-b last:border-0">
                            <div class="truncate max-w-xs">
                                <span class="text-sm">{{ $visit['url'] }}</span>
                                <div class="text-xs text-gray-500">{{ $visit['ip_address'] }}</div>
                            </div>
                            <span class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($visit['created_at'])->diffForHumans() }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <flux:text variant="secondary">No data yet.</flux:text>
            @endif
        </flux:card>
    </div>
</div>
