import { fetchDashboard } from "./api";
import { createCharts } from "./chart";
import { getElements } from "./elements";
import { formatLongDate, formatNumber, formatPercent } from "./formatters";
import { renderTopMachines } from "./table";

const REFRESH_INTERVAL_MS = 60_000;

const formatters = {
    total_machine: formatNumber,
    running_order: formatNumber,
    finished_order: formatNumber,
    achievement: formatPercent,
    today_good: formatNumber,
    today_reject: formatNumber,
};

function init() {
    const elements = getElements();
    const updateCharts = createCharts();

    let controller = null;

    function setLoading(loading) {
        elements.refreshBtn.disabled = loading;
        elements.refreshIcon.classList.toggle("animate-spin", loading);
        elements.content.classList.toggle("opacity-60", loading);
    }

    function showError(message) {
        elements.errorText.textContent = message;
        elements.errorBox.classList.remove("hidden");
        elements.errorBox.classList.add("flex");
    }

    function hideError() {
        elements.errorBox.classList.add("hidden");
        elements.errorBox.classList.remove("flex");
    }

    function render(data) {
        renderKpis(data.summary);
        updateCharts(data);
        renderTopMachines(data.top_machines);

        const latest = data.trend_7_days.at(-1)?.date;
        elements.asOf.textContent = latest ? formatLongDate(latest) : "—";
        elements.updatedAt.textContent = new Date().toLocaleTimeString("en-GB");
    }

    function renderKpis(summary) {
        document.querySelectorAll("[data-kpi]").forEach((el) => {
            const key = el.dataset.kpi;
            const format = formatters[key] ?? String;
            el.textContent = format(summary[key]);
        });
    }

    async function load() {
        controller?.abort();
        const current = new AbortController();
        controller = current;

        setLoading(true);

        try {
            const data = await fetchDashboard(current.signal);
            console.log(data);
            render(data);
            hideError();
        } catch (error) {
            if (error.name === "AbortError") return;
            showError(`Failed to load dashboard data (${error.message}).`);
        } finally {
            if (current === controller) setLoading(false);
        }
    }

    elements.refreshBtn.addEventListener("click", load);
    elements.retryBtn.addEventListener("click", load);
    setInterval(() => !document.hidden && load(), REFRESH_INTERVAL_MS);

    load();
}

if (document.getElementById("dashboard")) {
    init();
}
