import Chart from 'chart.js/auto';

window.createPieChart = function(canvas, data) {
    new Chart(canvas, {
        type: 'pie',
        data: data,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Pending Pledges by Campaign'
                }
            }
        }
    });
};