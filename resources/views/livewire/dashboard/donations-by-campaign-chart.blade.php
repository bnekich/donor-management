<div 
    x-data="{ 
        rendered: false,
        renderChart() {
            if (this.rendered) return;
            const canvas = document.getElementById('donationsChart');
            if (canvas) {
                if (window.donationsChartInstance) {
                    window.donationsChartInstance.destroy();
                }
                const data = @js($chartData);
                window.donationsChartInstance = window.createDonationsChart(canvas, data);
                this.rendered = true;
            }
        }
    }"
    x-intersect="renderChart()"
    class="bg-white dark:bg-zinc-800 p-6 rounded-lg shadow-md border border-neutral-200 dark:border-neutral-700"
>
    <h3 class="text-lg font-semibold mb-4 dark:text-white">Donations by Campaign</h3>
    @if (empty($chartData['labels']))
        <p class="text-gray-600 dark:text-gray-400">No donations with campaigns to display.</p>
    @else
        <canvas id="donationsChart" width="400" height="200"></canvas>
    @endif
</div>

@script
    <script>
        $wire.on('render-donations-chart', () => {
            const chartElement = document.querySelector('[id="donationsChart"]')?.closest('[x-data]');
            if (chartElement && chartElement.__x) {
                chartElement.__x.$data.rendered = false;
                chartElement.__x.$data.renderChart();
            }
        });
    </script>
@endscript
