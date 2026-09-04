<div>
    <main class="space-y-6">
        <div class="flex items-center justify-between mb-8">
            <flux:heading size="xl">Analytics</flux:heading>
        </div>

        {{-- Filters --}}
        <div class="flex items-center gap-2 justify-end ml-auto">
            <form method="GET" action="{{ route('admin.analytics') }}" class="flex items-center gap-2">
                <flux:input type="date" name="start_date" :value="request('start_date', now()->subDays(30)->format('Y-m-d'))" placeholder="Start Date" />
                <flux:input type="date" name="end_date" :value="request('end_date', now()->format('Y-m-d'))" placeholder="End Date" />
                <flux:select name="request_category" :value="request('request_category')">
                    <flux:select.option value="">All Requests</flux:select.option>
                    <flux:select.option value="web">Web Only</flux:select.option>
                    <flux:select.option value="api">API Only</flux:select.option>
                </flux:select>
                <flux:button type="submit" variant="primary">Apply</flux:button>
            </form>
            <a href="{{ route('admin.analytics.export', request()->only(['start_date', 'end_date', 'request_category'])) }}"
               class="inline-flex items-center justify-center px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-white text-sm font-medium rounded-md transition-colors">
                Export CSV
            </a>
        </div>

        {{-- Key Metrics Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <flux:card>
                <div class="text-center sm:text-left">
                    <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $average['views'] ?? 0 }}</div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Views</p>
                </div>
            </flux:card>
            <flux:card>
                <div class="text-center sm:text-left">
                    <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $average['visitors'] ?? 0 }}</div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Visitors</p>
                </div>
            </flux:card>
            <flux:card>
                <div class="text-center sm:text-left">
                    <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $average['bounce_rate'] ?? '0%' }}</div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Bounce Rate</p>
                </div>
            </flux:card>
            <flux:card>
                <div class="text-center sm:text-left">
                    <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $average['average_visit_time'] ?? '0s' }}</div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Average Visit Time</p>
                </div>
            </flux:card>
        </div>

        {{-- Chart Section --}}
        <flux:card>
            <div class="mb-4">
                <flux:heading size="lg">Traffic Overview</flux:heading>
                <flux:text variant="secondary" size="sm">Daily visitor and page view trends</flux:text>
            </div>
            <div class="relative">
                <canvas id="stats-chart" class="w-full" style="max-height: 400px;" data-labels='@json($labels)' data-datasets='@json($datasets)'></canvas>
            </div>
        </flux:card>

        {{-- Analytics Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <flux:card>
                <div class="p-6 min-h-[400px] flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pages</h3>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Views</span>
                    </div>
                    <div class="flex-1 flex flex-col space-y-3">
                        @forelse(array_slice($pages, 0, 5) as $page)
                            <div class="flex items-center justify-between py-2 px-1 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                                <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ $page['path'] }}</span>
                                <div class="flex items-center gap-3 flex-shrink-0">
                                    <span class="font-semibold text-sm text-gray-900 dark:text-white">{{ $page['views'] }}</span>
                                    <div class="flex items-center gap-2">
                                        <div class="w-12 h-2 bg-gray-100 dark:bg-zinc-700 rounded-full overflow-hidden">
                                            <div class="h-full bg-blue-500 rounded-full" style="width: {{ $page['percentage'] }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-8 text-right">{{ $page['percentage'] }}%</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-5">No pages</p>
                        @endforelse
                    </div>
                </div>
            </flux:card>

            <flux:card>
                <div class="p-6 min-h-[400px] flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Referrers</h3>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Views</span>
                    </div>
                    <div class="flex-1 flex flex-col space-y-3">
                        @forelse(array_slice($referrers, 0, 5) as $referrer)
                            <div class="flex items-center justify-between py-2 px-1 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                                <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ $referrer['domain'] }}</span>
                                <div class="flex items-center gap-3 flex-shrink-0">
                                    <span class="font-semibold text-sm text-gray-900 dark:text-white">{{ $referrer['visits'] }}</span>
                                    <div class="flex items-center gap-2">
                                        <div class="w-12 h-2 bg-gray-100 dark:bg-zinc-700 rounded-full overflow-hidden">
                                            <div class="h-full bg-blue-500 rounded-full" style="width: {{ $referrer['percentage'] }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-8 text-right">{{ $referrer['percentage'] }}%</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-5">No referrers</p>
                        @endforelse
                    </div>
                </div>
            </flux:card>
        </div>

        {{-- Additional Analytics --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <flux:card>
                <div class="p-6 min-h-[400px] flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Browsers</h3>
                    </div>
                    <div class="flex-1 flex flex-col space-y-3">
                        @php
                            $browserImage = fn ($browser) => match(strtolower($browser)) {
                                'chrome' => asset('vendor/request-analytics/browsers/chrome.png'),
                                'firefox' => asset('vendor/request-analytics/browsers/firefox.png'),
                                'safari' => asset('vendor/request-analytics/browsers/safari.png'),
                                'edge' => asset('vendor/request-analytics/browsers/ms-edge.png'),
                                default => asset('vendor/request-analytics/browsers/unknown.png'),
                            };
                        @endphp
                        @forelse(array_slice($browsers, 0, 5) as $browser)
                            <div class="flex items-center justify-between py-2 px-1 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <img alt="" class="w-5 h-5 rounded object-cover flex-shrink-0" src="{{ $browserImage($browser['browser']) }}"/>
                                    <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ $browser['browser'] }}</span>
                                </div>
                                <div class="flex items-center gap-3 flex-shrink-0">
                                    <span class="font-semibold text-sm text-gray-900 dark:text-white">{{ $browser['count'] }}</span>
                                    <div class="flex items-center gap-2">
                                        <div class="w-12 h-2 bg-gray-100 dark:bg-zinc-700 rounded-full overflow-hidden">
                                            <div class="h-full bg-blue-500 rounded-full" style="width: {{ $browser['percentage'] }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-8 text-right">{{ $browser['percentage'] }}%</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-5">No browsers</p>
                        @endforelse
                    </div>
                </div>
            </flux:card>

            <flux:card>
                <div class="p-6 min-h-[400px] flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Operating Systems</h3>
                    </div>
                    <div class="flex-1 flex flex-col space-y-3">
                        @php
                            $osImage = function ($os) {
                                $normalized = str_replace(' ', '', strtolower($os));
                                if (str_starts_with($normalized, 'windows')) {
                                    return asset('vendor/request-analytics/operating-systems/windows-logo.png');
                                }
                                return match($normalized) {
                                    'linux' => asset('vendor/request-analytics/operating-systems/linux.png'),
                                    'macos', 'macosx' => asset('vendor/request-analytics/operating-systems/mac-logo.png'),
                                    'android' => asset('vendor/request-analytics/operating-systems/android-os.png'),
                                    'ios' => asset('vendor/request-analytics/operating-systems/iphone.png'),
                                    default => asset('vendor/request-analytics/operating-systems/unknown.png'),
                                };
                            };
                        @endphp
                        @forelse(array_slice($operatingSystems, 0, 5) as $os)
                            <div class="flex items-center justify-between py-2 px-1 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <img alt="" class="w-5 h-5 rounded object-cover flex-shrink-0" src="{{ $osImage($os['name']) }}"/>
                                    <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ $os['name'] }}</span>
                                </div>
                                <div class="flex items-center gap-3 flex-shrink-0">
                                    <span class="font-semibold text-sm text-gray-900 dark:text-white">{{ $os['count'] }}</span>
                                    <div class="flex items-center gap-2">
                                        <div class="w-12 h-2 bg-gray-100 dark:bg-zinc-700 rounded-full overflow-hidden">
                                            <div class="h-full bg-blue-500 rounded-full" style="width: {{ $os['percentage'] }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-8 text-right">{{ $os['percentage'] }}%</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-5">No operating systems</p>
                        @endforelse
                    </div>
                </div>
            </flux:card>

            <flux:card>
                <div class="p-6 min-h-[400px] flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Devices</h3>
                    </div>
                    <div class="flex-1 flex flex-col space-y-3">
                        @php
                            $deviceImage = fn ($device) => match(strtolower($device)) {
                                'mobile' => asset('vendor/request-analytics/devices/smartphone.png'),
                                'tablet' => asset('vendor/request-analytics/devices/ipad.png'),
                                'desktop' => asset('vendor/request-analytics/devices/laptop.png'),
                                'tv' => asset('vendor/request-analytics/devices/tv.png'),
                                default => asset('vendor/request-analytics/devices/unknown.png'),
                            };
                        @endphp
                        @forelse(array_slice($devices, 0, 5) as $device)
                            <div class="flex items-center justify-between py-2 px-1 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <img alt="" class="w-5 h-5 rounded object-cover flex-shrink-0" src="{{ $deviceImage($device['name']) }}"/>
                                    <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ $device['name'] }}</span>
                                </div>
                                <div class="flex items-center gap-3 flex-shrink-0">
                                    <span class="font-semibold text-sm text-gray-900 dark:text-white">{{ $device['count'] }}</span>
                                    <div class="flex items-center gap-2">
                                        <div class="w-12 h-2 bg-gray-100 dark:bg-zinc-700 rounded-full overflow-hidden">
                                            <div class="h-full bg-blue-500 rounded-full" style="width: {{ $device['percentage'] }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-8 text-right">{{ $device['percentage'] }}%</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-5">No devices</p>
                        @endforelse
                    </div>
                </div>
            </flux:card>

            <flux:card>
                <div class="p-6 min-h-[400px] flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Countries</h3>
                    </div>
                    <div class="flex-1 flex flex-col space-y-3">
                        @forelse(array_slice($countries, 0, 5) as $country)
                            <div class="flex items-center justify-between py-2 px-1 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <img alt="" class="w-5 h-5 rounded object-cover flex-shrink-0" src="https://www.worldatlas.com/r/w236/img/flag/{{ $country['code'] }}-flag.jpg"/>
                                    <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ $country['name'] }}</span>
                                </div>
                                <div class="flex items-center gap-3 flex-shrink-0">
                                    <span class="font-semibold text-sm text-gray-900 dark:text-white">{{ $country['count'] }}</span>
                                    <div class="flex items-center gap-2">
                                        <div class="w-12 h-2 bg-gray-100 dark:bg-zinc-700 rounded-full overflow-hidden">
                                            <div class="h-full bg-blue-500 rounded-full" style="width: {{ $country['percentage'] }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-8 text-right">{{ $country['percentage'] }}%</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-5">No countries</p>
                        @endforelse
                    </div>
                </div>
            </flux:card>
        </div>
    </main>
</div>
