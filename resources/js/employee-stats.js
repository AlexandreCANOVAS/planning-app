import Chart from 'chart.js/auto';

// Initialisation des graphiques des statistiques employé
export function initEmployeeStats(workByLocationData, workByMonthData) {
    const colors = [
        'rgba(54, 162, 235, 0.8)',   // Bleu
        'rgba(255, 99, 132, 0.8)',   // Rouge
        'rgba(75, 192, 192, 0.8)',   // Vert
        'rgba(255, 206, 86, 0.8)',   // Jaune
        'rgba(153, 102, 255, 0.8)',  // Violet
        'rgba(255, 159, 64, 0.8)'    // Orange
    ];

    // Graphique par lieu
    const locationChart = document.getElementById('workByLocationChart');
    if (locationChart && Object.keys(workByLocationData).length > 0) {
        new Chart(locationChart, {
            type: 'pie',
            data: {
                labels: Object.keys(workByLocationData),
                datasets: [{
                    data: Object.values(workByLocationData),
                    backgroundColor: colors
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });
    }

    // Graphique par mois
    const monthChart = document.getElementById('workByMonthChart');
    if (monthChart && Object.keys(workByMonthData).length > 0) {
        const months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                      'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        
        new Chart(monthChart, {
            type: 'bar',
            data: {
                labels: Object.keys(workByMonthData).map(month => months[parseInt(month) - 1]),
                datasets: [{
                    label: 'Heures travaillées',
                    data: Object.values(workByMonthData),
                    backgroundColor: colors[0],
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Heures',
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        },
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
}
