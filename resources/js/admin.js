// Admin JavaScript
console.log('Admin JS loaded');

// Markdown Editor
import '../../vendor/mckenziearts/livewire-markdown-editor/resources/js/markdown-editor.js';

// Analytics Chart Initializer
function initStatsChart() {
    const canvas = document.getElementById('stats-chart');
    if (!canvas || canvas._chartInstance) return;

    // Clean up orphaned chart instances
    Object.values(Chart.instances || {}).forEach(chart => {
        if (chart.canvas && !document.contains(chart.canvas)) {
            chart.destroy();
        }
    });

    const labelsRaw = canvas.dataset.labels;
    const datasetsRaw = canvas.dataset.datasets;
    if (!labelsRaw || !datasetsRaw) return;

    let labels, datasets;
    try {
        labels = JSON.parse(labelsRaw);
        datasets = JSON.parse(datasetsRaw);
    } catch (e) {
        console.error('Failed to parse chart data', e);
        return;
    }

    if (!labels.length || !datasets.length) return;

    const ctx = canvas.getContext('2d');
    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(255,255,255,0.1)' : '#f3f4f6';
    const textColor = isDark ? '#9ca3af' : '#6b7280';

    canvas._chartInstance = new Chart(ctx, {
        type: 'line',
        data: { labels, datasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'line',
                        padding: 20,
                        font: { size: 14, weight: 'bold' },
                        color: textColor,
                        generateLabels: function(chart) {
                            return Chart.defaults.plugins.legend.labels.generateLabels(chart).map(function(label, index) {
                                const dataset = chart.data.datasets[index];
                                label.lineWidth = 4;
                                label.strokeStyle = dataset.borderColor;
                                label.fillStyle = dataset.borderColor;
                                if (dataset.borderDash && dataset.borderDash.length > 0) {
                                    label.lineDash = dataset.borderDash;
                                    label.lineDashOffset = 0;
                                }
                                return label;
                            });
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor, borderDash: [2, 2] },
                    ticks: { font: { size: 11 }, color: textColor, precision: 0, stepSize: 1 }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 }, color: textColor }
                }
            },
            interaction: { intersect: false, mode: 'index' },
            elements: {
                point: { radius: 4, hoverRadius: 6, pointStyle: 'circle' },
                line: { tension: 0.3 }
            },
            datasets: {
                line: {
                    pointStyle: 'circle',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    borderWidth: 3
                }
            }
        }
    });
}

// Run on initial load and after every wire:navigate
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initStatsChart);
} else {
    initStatsChart();
}

document.addEventListener('livewire:navigated', initStatsChart);
