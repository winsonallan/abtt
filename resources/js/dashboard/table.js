import { getElements } from "./elements";
import { formatNumber, formatPercent } from "./formatters";

function cell(className, child) {
    const td = document.createElement("td");
    td.className = className;
    td.append(child);
    return td;
}

function machineCell(machine) {
    const wrap = document.createElement("div");

    const name = document.createElement("div");
    name.className = "font-medium text-slate-900";
    name.textContent = machine.machine_name;

    const code = document.createElement("div");
    code.className = "text-xs text-slate-500";
    code.textContent = machine.machine_code;

    wrap.append(name, code);
    return wrap;
}

function achievementCell(value) {
    const wrap = document.createElement("div");
    wrap.className = "flex items-center gap-3";

    const track = document.createElement("div");
    track.className = "h-2 w-24 overflow-hidden rounded-full bg-slate-100";

    const bar = document.createElement("div");
    const tone =
        value >= 100
            ? "bg-emerald-500"
            : value >= 90
              ? "bg-indigo-500"
              : "bg-amber-500";
    bar.className = `h-full rounded-full ${tone}`;
    bar.style.width = `${Math.min(Math.max(value, 0), 100)}%`;
    track.append(bar);

    const label = document.createElement("span");
    label.className = "tabular-nums text-slate-700";
    label.textContent = formatPercent(value);

    wrap.append(track, label);
    return wrap;
}

export function renderTopMachines(machines) {
    const elements = getElements();
    const body = elements.machineTable;
    body.replaceChildren();

    if (!machines.length) {
        const row = document.createElement("tr");
        const td = document.createElement("td");
        td.colSpan = 4;
        td.className = "py-8 text-center text-slate-400";
        td.textContent = "No production data yet.";
        row.append(td);
        body.append(row);
        return;
    }

    machines.forEach((machine, index) => {
        const row = document.createElement("tr");
        row.className = "hover:bg-slate-50";
        row.append(
            cell(
                "py-3 pr-3 text-slate-500 tabular-nums",
                document.createTextNode(String(index + 1)),
            ),
            cell("py-3 pr-3", machineCell(machine)),
            cell(
                "py-3 pr-3 text-right tabular-nums font-medium",
                document.createTextNode(formatNumber(machine.good_qty)),
            ),
            cell("py-3", achievementCell(machine.achievement)),
        );
        body.append(row);
    });
}
