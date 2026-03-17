const periodSelect = document.getElementById("periodSelect");
const btnRefresh = document.getElementById("btnRefresh");
const searchInput = document.getElementById("searchInput");
const tsBody = document.getElementById("tsBody");

const statRegularHours = document.getElementById("statRegularHours");
const statOtHours = document.getElementById("statOtHours");
const statLateMinutes = document.getElementById("statLateMinutes");
const statUndertimeMinutes = document.getElementById("statUndertimeMinutes");
const statNightDiffHours = document.getElementById("statNightDiffHours");

const selectedPeriodLabel = document.getElementById("selectedPeriodLabel");
const periodStatusBadge = document.getElementById("periodStatusBadge");

let allRows = [];

document.addEventListener("DOMContentLoaded", () => {
    bindEvents();
    loadPeriods();
});

function bindEvents() {
    periodSelect.addEventListener("change", loadTimesheet);
    btnRefresh.addEventListener("click", loadTimesheet);
    searchInput.addEventListener("input", renderRows);
}

async function fetchJson(url) {
    const res = await fetch(url);
    const text = await res.text();

    try {
        return JSON.parse(text);
    } catch (e) {
        console.error("Invalid JSON response:", text);
        throw new Error("Invalid server response.");
    }
}

async function loadPeriods() {
    try {
        const data = await fetchJson("includes/timesheet_data.php?action=get_periods");

        if (!Array.isArray(data) || data.length === 0) {
            periodSelect.innerHTML = `<option value="">No periods found</option>`;
            tsBody.innerHTML = `<tr><td colspan="13" class="empty-row">No available timesheet periods.</td></tr>`;
            return;
        }

        periodSelect.innerHTML = data.map((p) => `
            <option value="${escapeHtml(p.PeriodID)}"
                    data-start="${escapeHtml(p.StartDate || '')}"
                    data-end="${escapeHtml(p.EndDate || '')}"
                    data-status="${escapeHtml(p.Status || 'OPEN')}">
                ${escapeHtml(formatPeriodLabel(p.StartDate, p.EndDate))}
            </option>
        `).join("");

        updateSelectedPeriodMeta();
        loadTimesheet();
    } catch (err) {
        console.error(err);
        Swal.fire("Error", "Failed to load periods.", "error");
    }
}

async function loadTimesheet() {
    const periodId = periodSelect.value;

    updateSelectedPeriodMeta();

    if (!periodId) {
        tsBody.innerHTML = `<tr><td colspan="13" class="empty-row">Please select a period.</td></tr>`;
        resetStats();
        return;
    }

    try {
        tsBody.innerHTML = `<tr><td colspan="13" class="empty-row">Loading timesheet...</td></tr>`;

        const data = await fetchJson(`includes/timesheet_data.php?action=get_timesheet&period_id=${encodeURIComponent(periodId)}`);
        allRows = Array.isArray(data) ? data : [];

        computeStats(allRows);
        renderRows();
    } catch (err) {
        console.error(err);
        Swal.fire("Error", "Failed to load timesheet data.", "error");
        tsBody.innerHTML = `<tr><td colspan="13" class="empty-row">Failed to load timesheet.</td></tr>`;
        resetStats();
    }
}

function renderRows() {
    const keyword = (searchInput.value || "").toLowerCase().trim();

    let filtered = allRows;

    if (keyword) {
        filtered = allRows.filter(row => {
            return [
                row.WorkDate,
                row.WorkDateFormatted,
                row.DayName,
                row.ShiftCode,
                row.ShiftName,
                row.DayStatus,
                row.Remarks,
                row.ActualTimeIn,
                row.ActualTimeOut,
                row.ScheduledDisplay
            ].join(" ").toLowerCase().includes(keyword);
        });
    }

    if (!filtered.length) {
        tsBody.innerHTML = `<tr><td colspan="13" class="empty-row">No timesheet records found.</td></tr>`;
        return;
    }

    tsBody.innerHTML = filtered.map(row => `
        <tr>
            <td>${escapeHtml(row.WorkDateFormatted || row.WorkDate || '-')}</td>
            <td>${escapeHtml(row.DayName || '-')}</td>
            <td title="${escapeHtml(row.ShiftName || '')}">${escapeHtml(row.ShiftCode || '-')}</td>
            <td>${escapeHtml(row.ScheduledDisplay || '-')}</td>
            <td>${escapeHtml(row.BreakMinutes ?? 0)} min</td>
            <td>${escapeHtml(row.ActualTimeInDisplay || row.ActualTimeIn || '-')}</td>
            <td>${escapeHtml(row.ActualTimeOutDisplay || row.ActualTimeOut || '-')}</td>
            <td>${escapeHtml(row.RegularHours || '0.00')}</td>
            <td>${escapeHtml(row.OvertimeHours || '0.00')}</td>
            <td>${escapeHtml(row.LateMinutes ?? '0')}</td>
            <td>${escapeHtml(row.UndertimeMinutes ?? '0')}</td>
            <td>${renderStatusBadge(row.DayStatus)}</td>
            <td>${escapeHtml(row.Remarks || '-')}</td>
        </tr>
    `).join("");
}

function computeStats(rows) {
    let reg = 0;
    let ot = 0;
    let late = 0;
    let under = 0;
    let night = 0;

    rows.forEach(row => {
        reg += Number(row.RegularMinutes || 0);
        ot += Number(row.OvertimeMinutes || 0);
        late += Number(row.LateMinutes || 0);
        under += Number(row.UndertimeMinutes || 0);
        night += Number(row.NightDiffMinutes || 0);
    });

    statRegularHours.textContent = (reg / 60).toFixed(2);
    statOtHours.textContent = (ot / 60).toFixed(2);
    statLateMinutes.textContent = late.toFixed(0);
    statUndertimeMinutes.textContent = under.toFixed(0);
    statNightDiffHours.textContent = (night / 60).toFixed(2);
}

function resetStats() {
    statRegularHours.textContent = "0.00";
    statOtHours.textContent = "0.00";
    statLateMinutes.textContent = "0";
    statUndertimeMinutes.textContent = "0";
    statNightDiffHours.textContent = "0.00";
}

function updateSelectedPeriodMeta() {
    const opt = periodSelect.options[periodSelect.selectedIndex];

    if (!opt || !opt.value) {
        selectedPeriodLabel.innerHTML = `<i data-lucide="calendar"></i> No period selected`;
        periodStatusBadge.textContent = "No Period";
        periodStatusBadge.className = "status-badge draft";
        if (window.lucide) lucide.createIcons();
        return;
    }

    const start = opt.dataset.start || "";
    const end = opt.dataset.end || "";
    const status = (opt.dataset.status || "OPEN").toUpperCase();

    selectedPeriodLabel.innerHTML = `<i data-lucide="calendar"></i> ${escapeHtml(formatPeriodLabel(start, end))}`;
    periodStatusBadge.textContent = status;
    periodStatusBadge.className = "status-badge draft";

    if (window.lucide) lucide.createIcons();
}

function renderStatusBadge(status) {
    const normalized = (status || "").toUpperCase();

    if (normalized === "PRESENT") {
        return `<span class="status-badge badge-present">Present</span>`;
    }

    if (normalized === "ABSENT") {
        return `<span class="status-badge badge-absent">Absent</span>`;
    }

    if (normalized.includes("LEAVE")) {
        return `<span class="status-badge badge-leave">Leave</span>`;
    }

    if (normalized === "REST_DAY" || normalized === "REST DAY") {
        return `<span class="status-badge badge-rest">Rest Day</span>`;
    }

    if (normalized === "HOLIDAY") {
        return `<span class="status-badge badge-holiday">Holiday</span>`;
    }

    if (normalized === "FLAGGED" || normalized === "INCOMPLETE" || normalized === "NO_SCHEDULE") {
        return `<span class="status-badge badge-flagged">${escapeHtml(status || 'Flagged')}</span>`;
    }

    return `<span class="status-badge draft">${escapeHtml(status || '—')}</span>`;
}

function formatPeriodLabel(start, end) {
    const startText = formatDateLabel(start);
    const endText = formatDateLabel(end);

    if (!startText && !endText) return "Unknown Period";
    if (!endText) return startText;
    return `${startText} - ${endText}`;
}

function formatDateLabel(value) {
    if (!value) return "";
    const date = new Date(value + "T00:00:00");
    if (isNaN(date.getTime())) return value;

    return date.toLocaleDateString("en-US", {
        month: "short",
        day: "2-digit",
        year: "numeric"
    });
}

function escapeHtml(value) {
    return String(value ?? "").replace(/[&<>"']/g, s => ({
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#39;"
    })[s]);
}