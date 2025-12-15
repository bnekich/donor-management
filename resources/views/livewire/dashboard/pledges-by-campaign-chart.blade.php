<div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-lg font-semibold mb-4">Pending Pledges by Campaign</h3>
    @if (empty($chartData['labels']))
        <p class="text-gray-600">No pending pledges with campaigns to display.</p>
    @else
        <canvas id="pledgesChart" width="400" height="200"></canvas>
    @endif
</div>

@script
    <script>
        $wire.on('render-chart', () => {
            const canvas = document.getElementById('pledgesChart');
            if (canvas) {
                const data = @js($chartData);
                window.createPieChart(canvas, data);
            }
        });
    </script>
@endscript
