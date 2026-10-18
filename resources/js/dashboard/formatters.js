const numberFormat = new Intl.NumberFormat("id-ID");

export const formatNumber = (value) => numberFormat.format(Number(value ?? 0));

export const formatPercent = (value) => `${Number(value ?? 0).toFixed(2)}%`;

/** "2026-12-31" -> "31 Dec" (parsed as local date to avoid timezone shifts) */
export const formatShortDate = (iso) =>
    new Date(`${iso}T00:00:00`).toLocaleDateString("en-GB", {
        day: "2-digit",
        month: "short",
    });

export const formatLongDate = (iso) =>
    new Date(`${iso}T00:00:00`).toLocaleDateString("en-GB", {
        day: "2-digit",
        month: "long",
        year: "numeric",
    });

export const capitalize = (str) =>
    str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
