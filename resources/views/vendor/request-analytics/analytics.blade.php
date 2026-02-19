<!-- Chart.js for analytics charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Analytics</flux:heading>
    </div>

    <main class="space-y-6">
        <!-- Filter Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="w-full sm:w-auto">
                <flux:text variant="secondary">Track your website performance and user insights</flux:text>
            </div>
            <div class="w-full sm:w-auto">
                <form method="GET" action="{{ route('admin.analytics') }}" class="flex items-center gap-2 flex-wrap">
                    <flux:input type="date" name="start_date" :value="request('start_date', now()->subDays(30)->format('Y-m-d'))" placeholder="Start Date" />
                    <flux:input type="date" name="end_date" :value="request('end_date', now()->format('Y-m-d'))" placeholder="End Date" />
                    <flux:select name="request_category" :value="request('request_category')">
                        <flux:select.option value="">All Requests</flux:select.option>
                        <flux:select.option value="web">Web Only</flux:select.option>
                        <flux:select.option value="api">API Only</flux:select.option>
                    </flux:select>
                    <flux:button type="submit" variant="primary">Apply</flux:button>
                </form>
            </div>
        </div>

        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <flux:card>
                <x-request-analytics::stats.count label="Views" :value='$average["views"]'/>
            </flux:card>
            <flux:card>
                <x-request-analytics::stats.count label="Visitors" :value='$average["visitors"]'/>
            </flux:card>
            <flux:card>
                <x-request-analytics::stats.count label="Bounce Rate" :value='$average["bounce_rate"]'/>
            </flux:card>
            <flux:card>
                <x-request-analytics::stats.count label="Average Visit Time" :value='$average["average_visit_time"]'/>
            </flux:card>
        </div>

        <!-- Chart Section -->
        <flux:card>
            <div class="mb-4">
                <flux:heading size="lg">Traffic Overview</flux:heading>
                <flux:text variant="secondary" size="sm">Daily visitor and page view trends</flux:text>
            </div>
            <x-request-analytics::stats.chart :labels='$labels' :datasets='$datasets' type="line"/>
        </flux:card>

        <!-- Analytics Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <flux:card>
                <x-request-analytics::analytics.pages :pages='$pages'/>
            </flux:card>
            <flux:card>
                <x-request-analytics::analytics.referrers :referrers='$referrers'/>
            </flux:card>
        </div>

        <!-- Additional Analytics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <flux:card>
                <x-request-analytics::analytics.broswers :browsers='$browsers'/>
            </flux:card>
            <flux:card>
                <x-request-analytics::analytics.operating-systems :operatingSystems='$operatingSystems'/>
            </flux:card>
            <flux:card>
                <x-request-analytics::analytics.devices :devices='$devices'/>
            </flux:card>
            <flux:card>
                <x-request-analytics::analytics.countries :countries='$countries'/>
            </flux:card>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('stats-chart');
    if (canvas && typeof Chart !== 'undefined') {
        const ctx = canvas.getContext('2d');
        const labels = @js($labels);
        const datasets = @js($datasets);
        
        // Detect dark mode
        const isDark = document.documentElement.classList.contains('dark');
        const gridColor = isDark ? 'rgba(255,255,255,0.1)' : '#f3f4f6';
        const textColor = isDark ? '#9ca3af' : '#6b7280';
        
        new Chart(ctx, {
            type: 'line',
            data: { labels, datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: textColor }
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: gridColor },
                        ticks: { color: textColor }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { color: textColor }
                    }
                }
            }
        });
    }
});
</script>
