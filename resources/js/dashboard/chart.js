import {
    ArcElement,
    BarController,
    BarElement,
    CategoryScale,
    Chart,
    Filler,
    Legend,
    LinearScale,
    LineController,
    LineElement,
    PieController,
    PointElement,
    Tooltip,
} from "chart.js/auto";
import { getElements } from "./elements.js";
import {
    capitalize,
    formatNumber,
    formatPercent,
    formatShortDate,
} from "./formatters.js";

Chart.register(
    ArcElement,
    BarController,
    BarElement,
    CategoryScale,
    PieController,
    Filler,
    Legend,
    LineController,
    LineElement,
    LinearScale,
    PointElement,
    Tooltip,
);
Chart.defaults.font.family = "inherit";
Chart.defaults.color = "#64748b";

const STATUS_COLORS = {
    RUNNING: "blue",
    FINISHED: "green",
    OPEN: "gold",
    CANCELLED: "red",
};
const GRID = "#e2e8f0";
const elements = getElements();

function createTrendChart(canvas) {
    let rows = [];

    const chart = new Chart(canvas, {
        type: "line",
        data: {
            labels: [],
            datasets: [
                {
                    label: "Good Qty",
                    data: [],
                    yAxisID: "y",
                    borderColor: "#10b981",
                    backgroundColor: "rgba(16, 185, 129, 0.15)",
                    fill: true,
                    tension: 0.35,
                },
                {
                    label: "Reject Qty",
                    data: [],
                    yAxisID: "y1",
                    borderColor: "#f43f5e",
                    backgroundColor: "#f43f5e",
                    tension: 0.35,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: "index", intersect: false },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: "Good" },
                    grid: { color: GRID },
                    ticks: { callback: (v) => formatNumber(v) },
                },
                y1: {
                    beginAtZero: true,
                    position: "right",
                    title: { display: true, text: "Reject" },
                    grid: { drawOnChartArea: false },
                    ticks: { callback: (v) => formatNumber(v) },
                },
                x: { grid: { display: false } },
            },
            plugins: {
                legend: { position: "bottom", labels: { usePointStyle: true } },
                tooltip: {
                    callbacks: {
                        label: (ctx) =>
                            `${ctx.dataset.label}: ${formatNumber(ctx.parsed.y)}`,
                        afterBody: (items) =>
                            `Achievement: ${formatPercent(rows[items[0].dataIndex]?.achievement)}`,
                    },
                },
            },
        },
    });

    return (trend) => {
        rows = trend;
        chart.data.labels = trend.map((d) => formatShortDate(d.date));
        chart.data.datasets[0].data = trend.map((d) => d.good_qty);
        chart.data.datasets[1].data = trend.map((d) => d.reject_qty);
        chart.update();
    };
}

function createStatusChart(canvas) {
    const chart = new Chart(canvas, {
        type: "pie",
        data: {
            labels: [],
            datasets: [
                {
                    data: [],
                    backgroundColor: [],
                    borderWidth: 2,
                    borderColor: "#fff",
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: "0",
            plugins: {
                legend: { position: "bottom", labels: { usePointStyle: true } },
                tooltip: {
                    callbacks: {
                        label: (ctx) => {
                            const l = ctx.label ?? "Unknown";
                            const displayedLabel = capitalize(l);
                            return `${displayedLabel}: ${formatNumber(ctx.parsed)}`;
                        },
                    },
                },
            },
        },
    });

    return (breakdown) => {
        chart.data.labels = breakdown.map((s) => {
            const l = capitalize(s.status);
            return l;
        });
        chart.data.datasets[0].data = breakdown.map((s) => s.total);
        chart.data.datasets[0].backgroundColor = breakdown.map(
            (s) => STATUS_COLORS[s.status] ?? "#94a3b8",
        );
        chart.update();
    };
}

function createMachineChart(canvas) {
    const chart = new Chart(canvas, {
        type: "bar",
        data: {
            labels: [],
            datasets: [
                {
                    label: "Good Qty",
                    data: [],
                    backgroundColor: "#0d542b",
                    borderRadius: 4,
                },
            ],
        },
        options: {
            indexAxis: "y",
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { color: GRID },
                    ticks: { callback: (v) => formatNumber(v) },
                },
                y: { grid: { display: false } },
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) =>
                            `Good Qty: ${formatNumber(ctx.parsed.x)}`,
                    },
                },
            },
        },
    });

    return (machines) => {
        chart.data.labels = machines.map((m) => m.machine_name);
        chart.data.datasets[0].data = machines.map((m) => m.good_qty);
        chart.update();
    };
}

export function createCharts() {
    const updateTrend = createTrendChart(elements.charts.trend);
    const updateStatus = createStatusChart(elements.charts.status);
    const updateMachines = createMachineChart(elements.charts.machine);

    return (data) => {
        updateTrend(data.trend_7_days);
        updateStatus(data.status_breakdown);
        updateMachines(data.top_machines);
    };
}
