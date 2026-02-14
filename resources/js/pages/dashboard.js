import Chart from 'chart.js/auto';

function renderDonut() {
    const canvas = document.getElementById('examPerformanceChart');
    if (!canvas) return;

    const data = window.dashboardData || {};
    const pass = data.examPass || 0;
    const fail = data.examFail || 0;

    // eslint-disable-next-line no-new
    new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: ['Pass', 'Fail'],
            datasets: [
                {
                    data: [pass, fail],
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

    const data = window.dashboardData || {};
    const attendance = data.attendance || [];

    const labels = attendance.map(d => d.label);
    const presentData = attendance.map(d => d.present);
    const absentData = attendance.map(d => d.absent);

    // eslint-disable-next-line no-new
    new Chart(canvas, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Present',
                    data: presentData,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.12)',
                    tension: 0.35,
                    fill: true,
                    pointRadius: 2,
                },
                {
                    label: 'Absent',
                    data: absentData,
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
