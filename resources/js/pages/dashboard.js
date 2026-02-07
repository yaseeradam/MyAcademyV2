import Chart from 'chart.js/auto';

function renderDonut() {
    const canvas = document.getElementById('examPerformanceChart');
    if (!canvas) return;

    // eslint-disable-next-line no-new
    new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: ['Pass', 'Fail'],
            datasets: [
                {
                    data: [78, 22],
                    backgroundColor: ['#22c55e', '#e5e7eb'],
                    borderWidth: 0,
                },
            ],
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 10, boxHeight: 10, usePointStyle: true },
                },
            },
        },
    });
}

function renderLine() {
    const canvas = document.getElementById('attendanceTrendChart');
    if (!canvas) return;

    // eslint-disable-next-line no-new
    new Chart(canvas, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [
                {
                    label: 'Present',
                    data: [240, 252, 248, 260, 255, 210, 198],
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.12)',
                    tension: 0.35,
                    fill: true,
                    pointRadius: 2,
                },
                {
                    label: 'Absent',
                    data: [18, 14, 16, 12, 15, 40, 46],
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249, 115, 22, 0.10)',
                    tension: 0.35,
                    fill: true,
                    pointRadius: 2,
                },
            ],
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
            },
            scales: {
                x: { grid: { display: false } },
                y: { grid: { color: 'rgba(229, 231, 235, 1)' } },
            },
        },
    });
}

renderDonut();
renderLine();
