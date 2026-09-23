import {
    Chart,
    LineController,
    LineElement,
    PointElement,
    LinearScale,
    CategoryScale,
    BarController,
    BarElement,
    DoughnutController,
    ArcElement,
    Tooltip,
    Filler,
} from 'chart.js';

Chart.register(
    LineController, LineElement, PointElement, LinearScale, CategoryScale,
    BarController, BarElement, DoughnutController, ArcElement, Tooltip, Filler
);

Chart.defaults.color = '#8B93A7';
Chart.defaults.borderColor = '#26314A';
Chart.defaults.font.family = '"Inter", sans-serif';

const palette = ['#E5A94B', '#4FD1C5', '#E06B6B', '#8B93A7', '#6C8CF5', '#B892E8', '#5AD69C', '#E88BC6'];

function readData(el) {
    return JSON.parse(el.dataset.chart);
}

document.querySelectorAll('[data-chart-type="line"]').forEach((el) => {
    const { labels, values, label } = readData(el);
    new Chart(el, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label,
                data: values,
                borderColor: '#E5A94B',
                backgroundColor: 'rgba(229, 169, 75, 0.12)',
                fill: true,
                tension: 0.25,
                pointRadius: 0,
                borderWidth: 2,
            }],
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false } },
                y: { grid: { color: '#1C2438' }, beginAtZero: true },
            },
        },
    });
});

document.querySelectorAll('[data-chart-type="bar"]').forEach((el) => {
    const { labels, values } = readData(el);
    new Chart(el, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                data: values,
                backgroundColor: '#4FD1C5',
                borderRadius: 2,
                maxBarThickness: 22,
            }],
        },
        options: {
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: '#1C2438' }, beginAtZero: true },
                y: { grid: { display: false } },
            },
        },
    });
});

document.querySelectorAll('[data-chart-type="doughnut"]').forEach((el) => {
    const { labels, values } = readData(el);
    new Chart(el, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data: values,
                backgroundColor: palette,
                borderColor: '#0B1220',
                borderWidth: 2,
            }],
        },
        options: {
            cutout: '68%',
            plugins: { legend: { position: 'right', labels: { boxWidth: 10, padding: 12 } } },
        },
    });
});

const refreshBtn = document.getElementById('refresh-data');
if (refreshBtn) {
    refreshBtn.addEventListener('click', async () => {
        const token = prompt('Admin refresh token:');
        if (!token) return;
        refreshBtn.disabled = true;
        refreshBtn.textContent = 'Refreshing…';
        try {
            const res = await fetch('/api/stats/refresh', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ token }),
            });
            if (res.ok) {
                location.reload();
            } else {
                alert('Refresh failed — check the token.');
            }
        } finally {
            refreshBtn.disabled = false;
            refreshBtn.textContent = 'Refresh data';
        }
    });
}
