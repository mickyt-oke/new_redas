document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('passportChart');
    
    if (ctx) {
        const myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Passport Applications Processed',
                    data: [1200, 1900, 3000, 5000, 2300, 3400],
                    borderColor: '#009d63', // Primary Green
                    backgroundColor: 'rgba(0, 157, 99, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Visas Issued',
                    data: [800, 1200, 1500, 2000, 1800, 2500],
                    borderColor: '#0d6efd', // Bootstrap Primary Blue
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Monthly Issuance Statistics (2026)'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Function to simulate real-time data updates
        function updateChartData() {
            // Simulate fetching new data (e.g., for the next month or updating current)
            // For this example, we'll just randomize the last data point to show movement
            const datasets = myChart.data.datasets;
            
            datasets.forEach(dataset => {
                const lastIndex = dataset.data.length - 1;
                // Add a random value between -500 and 500 to the last data point
                const change = Math.floor(Math.random() * 1000) - 500;
                dataset.data[lastIndex] = Math.max(0, dataset.data[lastIndex] + change);
            });

            myChart.update();
        }

        // Update every 3 seconds
        setInterval(updateChartData, 3000);
    }
});