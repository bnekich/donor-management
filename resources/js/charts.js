import Chart from 'chart.js/auto';

window.createPieChart = function(canvas, data) {
    return new Chart(canvas, {
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

window.createDonationsChart = function(canvas, data) {
    const colors = [
        '#FF6384',
        '#36A2EB',
        '#FFCE56',
        '#4BC0C0',
        '#9966FF',
        '#FF9F40',
    ];

    return new Chart(canvas, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: 'Total Amount ($)',
                    data: data.amounts,
                    backgroundColor: colors.slice(0, data.labels.length),
                    yAxisID: 'y',
                },
                {
                    label: 'Count',
                    data: data.counts,
                    type: 'line',
                    borderColor: '#9CA3AF',
                    backgroundColor: 'rgba(156, 163, 175, 0.1)',
                    yAxisID: 'y1',
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                },
            ],
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Donations by Campaign'
                },
                tooltip: {
                    callbacks: {
                        afterLabel: function(context) {
                            if (context.datasetIndex === 0) {
                                const index = context.dataIndex;
                                return `Count: ${data.counts[index]}`;
                            }
                            return '';
                        }
                    }
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Amount ($)'
                    },
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Count'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                },
            },
        }
    });
};