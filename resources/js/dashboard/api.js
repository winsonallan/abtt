export async function fetchDashboard(signal) {
    const response = await fetch("/api/dashboard", {
        headers: { Accept: "application/json" },
        signal,
    });

    if (!response.ok) {
        throw new Error(`Request failed with status ${response.status}`);
    }

    return response.json();
}
