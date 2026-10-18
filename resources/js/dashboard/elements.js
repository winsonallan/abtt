export function getElements() {
    const refreshBtn = document.getElementById(
        "dashboard__header__refresh-btn",
    );
    const refreshIcon = document.getElementById(
        "dashboard__header__refresh-btn__icon",
    );

    const asOf = document.getElementById("dashboard__header__as-of");
    const updatedAt = document.getElementById("dashboard__header__updated-at");

    // Error box elements
    const errorBox = document.getElementById("dashboard__error");
    const errorText = document.getElementById("dashboard__error__error-text");
    const retryBtn = document.getElementById("dashboard__error__retry-btn");

    const content = document.getElementById("dashboard__content");

    // Charts
    const charts = {
        trend: document.getElementById("dashboard__content__trend-chart"),
        status: document.getElementById("dashboard__content__status-chart"),
        machine: document.getElementById("dashboard__content__machine-chart"),
    };

    const machineTable = document.getElementById(
        "dashboard__content__machine-table",
    );

    return {
        asOf,
        updatedAt,
        content,
        errorBox,
        refreshBtn,
        refreshIcon,
        errorText,
        retryBtn,
        charts,
        machineTable,
    };
}
