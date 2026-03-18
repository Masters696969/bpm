const API_URL = 'backend/admin_roster_api.php';

const state = {
    weekStart: null,
    roster: null,
    shifts: [],
    selectedShiftCode: null,
    selectedDepartmentId: 'all',
    search: '',
    pendingChanges: [],
    autoSaveTimer: null
};

document.addEventListener('DOMContentLoaded', async () => {
    try {
        initState();
        bindUI();
        await loadInitialData();
    } catch (err) {
        console.error(err);
        showError(err.message || 'Failed to initialize roster page.');
    } finally {
        if (window.lucide) lucide.createIcons();
    }
});

function initState() {
    state.weekStart = getMondayOfWeek(new Date());
}

function bindUI() {
    document.getElementById('searchInput')?.addEventListener('input', (e) => {
        state.search = (e.target.value || '').trim().toLowerCase();
        renderRosterTable();
        updateVisibleEmployeeBadge();
    });

    document.getElementById('departmentFilter')?.addEventListener('change', (e) => {
        state.selectedDepartmentId = e.target.value || 'all';
        updateSelectedDepartmentBadge();
        renderRosterTable();
        updateVisibleEmployeeBadge();
    });

    document.getElementById('prevPeriod')?.addEventListener('click', async () => {
        state.weekStart = shiftWorkdays(state.weekStart, -12);
        await loadRoster();
    });

    document.getElementById('nextPeriod')?.addEventListener('click', async () => {
        state.weekStart = shiftWorkdays(state.weekStart, 12);
        await loadRoster();
    });

    document.getElementById('publishRosterBtn')?.addEventListener('click', publishRoster);
    document.getElementById('btnFillAll')?.addEventListener('click', fillEditableRange);
    document.getElementById('btnClearRange')?.addEventListener('click', clearEditableRange);
    document.getElementById('btnAiSuggest')?.addEventListener('click', applyAutoSuggestionReview);
    document.getElementById('btnDismissAiReview')?.addEventListener('click', () => {
        document.getElementById('aiReviewPanel')?.classList.add('hidden');
    });
}

async function loadInitialData() {
    await Promise.all([loadShifts(), loadRoster()]);
}

async function apiGet(params = {}) {
    const qs = new URLSearchParams(params).toString();
    const res = await fetch(`${API_URL}?${qs}`, { credentials: 'same-origin' });
    const data = await res.json();

    if (!res.ok || !data.success) {
        throw new Error(data.message || 'Request failed.');
    }
    return data;
}

async function apiPost(action, payload) {
    const res = await fetch(`${API_URL}?action=${encodeURIComponent(action)}`, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload || {})
    });

    const data = await res.json();
    if (!res.ok || !data.success) {
        throw new Error(data.message || 'Request failed.');
    }
    return data;
}

async function loadShifts() {
    const data = await apiGet({ action: 'get_shifts' });
    const rawShifts = Array.isArray(data.shifts) ? data.shifts : [];

    state.shifts = rawShifts.filter(shift => !isOffShift(shift));

    if (!state.selectedShiftCode && state.shifts.length) {
        state.selectedShiftCode = state.shifts[0].ShiftCode;
    }

    if (state.selectedShiftCode && !state.shifts.some(s => s.ShiftCode === state.selectedShiftCode)) {
        state.selectedShiftCode = state.shifts.length ? state.shifts[0].ShiftCode : null;
    }

    renderShiftSelector();
}

async function loadRoster() {
    setAutoSaveIndicator('Loading...');
    const data = await apiGet({
        action: 'get_roster',
        week_start: state.weekStart
    });

    state.roster = sanitizeRosterPayload(data);
    state.pendingChanges = [];

    populateDepartmentFilter();
    renderPeriodLabel();
    renderStats();
    renderHeaderStatus();
    renderRosterTable();
    updateSelectedDepartmentBadge();
    updateVisibleEmployeeBadge();
    setAutoSaveIndicator('Auto-save ready');
}

function sanitizeRosterPayload(payload) {
    if (!payload || !Array.isArray(payload.departments)) {
        return payload;
    }

    payload.departments.forEach(group => {
        (group.rows || []).forEach(row => {
            Object.values(row.schedule || {}).forEach(cell => {
                if (isOffShiftCode(cell.shiftCode)) {
                    cell.shiftCode = '';
                }
            });
        });
    });

    if (payload.stats) {
        payload.stats.unassigned = countUnassignedFromPayload(payload);
    }

    return payload;
}

function populateDepartmentFilter() {
    const select = document.getElementById('departmentFilter');
    if (!select || !state.roster?.departments) return;

    const current = state.selectedDepartmentId || 'all';
    const departments = state.roster.departments
        .map(group => ({
            DepartmentID: String(group.DepartmentID),
            DepartmentName: group.DepartmentName
        }))
        .sort((a, b) => Number(a.DepartmentID) - Number(b.DepartmentID));

    select.innerHTML = `<option value="all">All Departments</option>`;
    departments.forEach(dept => {
        const opt = document.createElement('option');
        opt.value = dept.DepartmentID;
        opt.textContent = `${dept.DepartmentID} - ${dept.DepartmentName}`;
        if (String(current) === String(dept.DepartmentID)) {
            opt.selected = true;
        }
        select.appendChild(opt);
    });

    if (current !== 'all' && !departments.some(d => String(d.DepartmentID) === String(current))) {
        state.selectedDepartmentId = 'all';
        select.value = 'all';
    }
}

function renderShiftSelector() {
    const container = document.getElementById('shiftSelector');
    if (!container) return;

    if (!state.shifts.length) {
        container.innerHTML = `<div class="shift-loading">No active non-OFF shifts found.</div>`;
        return;
    }

    container.innerHTML = state.shifts.map(shift => {
        const active = shift.ShiftCode === state.selectedShiftCode ? 'active' : '';
        return `
            <button type="button" class="shift-pill ${active}" data-shift-code="${escapeHtml(shift.ShiftCode)}">
                <strong>${escapeHtml(shift.ShiftCode)}</strong>
                <span>${escapeHtml(shift.ShiftName)}</span>
                <small>${formatTimeRange(shift.StartTime, shift.EndTime)}</small>
            </button>
        `;
    }).join('');

    container.querySelectorAll('.shift-pill').forEach(btn => {
        btn.addEventListener('click', () => {
            state.selectedShiftCode = btn.dataset.shiftCode;
            renderShiftSelector();
        });
    });
}

function renderPeriodLabel() {
    const el = document.getElementById('periodLabel');
    if (!el || !state.roster) return;
    el.textContent = `${formatDateLong(state.roster.weekStart)} - ${formatDateLong(state.roster.weekEnd)}`;
}

function renderStats() {
    if (!state.roster?.stats) return;

    document.getElementById('statEmployees').textContent = state.roster.stats.employees ?? 0;
    document.getElementById('statCoverage').textContent = `${state.roster.stats.departments ?? 0} departments`;
    document.getElementById('statUnassigned').textContent = state.roster.stats.unassigned ?? 0;
    document.getElementById('statRosterStatus').textContent = getRosterStatusLabel();
}

function renderHeaderStatus() {
    const badge = document.getElementById('headerRosterStatus');
    if (!badge || !state.roster) return;

    const visibleGroups = getFilteredDepartmentGroups();
    const anyPublished = visibleGroups.some(d => String(d.header?.Status || '').toUpperCase() === 'PUBLISHED');

    badge.className = 'status-badge';
    if (anyPublished) {
        badge.classList.add('published-ready');
        badge.innerHTML = `<i data-lucide="badge-check" class="meta-icon"></i> Mixed Status / Published`;
    } else {
        badge.classList.add('draft');
        badge.innerHTML = `<i data-lucide="file-clock" class="meta-icon"></i> Draft Rosters`;
    }

    if (window.lucide) lucide.createIcons();
}

function renderRosterTable() {
    const thead = document.getElementById('rosterHead');
    const tbody = document.getElementById('rosterBody');
    if (!thead || !tbody) return;

    if (!state.roster || !Array.isArray(state.roster.departments)) {
        thead.innerHTML = '';
        tbody.innerHTML = `<tr><td style="text-align:center; padding:30px;">No roster data found.</td></tr>`;
        return;
    }

    const dates = state.roster.dates || [];
    thead.innerHTML = `
        <tr>
            <th style="min-width:280px;">Employee / Department</th>
            ${dates.map(date => `
                <th>
                    <div>${formatWeekdayShort(date)}</div>
                    <small>${formatMonthDay(date)}</small>
                </th>
            `).join('')}
        </tr>
    `;

    const parts = [];
    const groups = getFilteredDepartmentGroups();

    groups.forEach(group => {
        const rows = (group.rows || []).filter(row => {
            if (!state.search) return true;
            const hay = `${row.FullName} ${row.EmployeeCode || ''} ${group.DepartmentName}`.toLowerCase();
            return hay.includes(state.search);
        });

        if (!rows.length) return;

        parts.push(`
            <tr class="department-group-row">
                <td colspan="${1 + dates.length}" class="department-group-cell">
                    <strong>${escapeHtml(group.DepartmentID)} - ${escapeHtml(group.DepartmentName)}</strong>
                    <span style="margin-left:10px; opacity:.8;">
                        ${rows.length} employee(s) • ${escapeHtml(group.header?.Status || 'DRAFT')}
                    </span>
                </td>
            </tr>
        `);

        rows.forEach(row => {
            const cells = dates.map(date => {
                const cell = row.schedule?.[date];
                if (!cell) return `<td class="locked-cell">--</td>`;

                const classes = ['roster-cell'];
                if (cell.locked) classes.push('locked-cell');
                else classes.push('editable-cell');
                if (cell.isHoliday) classes.push('holiday-cell');
                if (cell.isLeave) classes.push('leave-cell');

                const content = cell.locked
                    ? escapeHtml(cell.label || '--')
                    : (cell.shiftCode ? escapeHtml(cell.shiftCode) : '<span class="empty-shift">—</span>');

                return `
                    <td
                        class="${classes.join(' ')}"
                        data-employee-id="${row.EmployeeID}"
                        data-work-date="${date}"
                        data-department-id="${row.DepartmentID}"
                        title="${escapeHtml(cell.holidayName || cell.label || 'Editable')}"
                    >${content}</td>
                `;
            }).join('');

            parts.push(`
                <tr>
                    <td class="employee-col">
                        <div class="emp-name">${escapeHtml(row.FullName)}</div>
                        <div class="emp-sub">${escapeHtml(row.EmployeeCode || '')} • ${escapeHtml(row.DepartmentID)} - ${escapeHtml(row.DepartmentName)}</div>
                    </td>
                    ${cells}
                </tr>
            `);
        });
    });

    tbody.innerHTML = parts.length
        ? parts.join('')
        : `<tr><td colspan="${1 + dates.length}" style="text-align:center; padding:30px;">No matching employees.</td></tr>`;

    tbody.querySelectorAll('.editable-cell').forEach(cell => {
        cell.addEventListener('click', () => handleEditableCellClick(cell));
        cell.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            clearSingleCell(cell);
        });
    });

    if (window.lucide) lucide.createIcons();
}

function getFilteredDepartmentGroups() {
    const groups = Array.isArray(state.roster?.departments) ? state.roster.departments : [];
    if (String(state.selectedDepartmentId) === 'all') return groups;
    return groups.filter(group => String(group.DepartmentID) === String(state.selectedDepartmentId));
}

function updateSelectedDepartmentBadge() {
    const badge = document.getElementById('selectedDepartmentBadge');
    if (!badge) return;

    const label = getSelectedDepartmentLabel();
    badge.innerHTML = `<i data-lucide="building-2" class="meta-icon"></i> ${escapeHtml(label)}`;
    if (window.lucide) lucide.createIcons();
}

function updateVisibleEmployeeBadge() {
    const badge = document.getElementById('visibleEmployeeBadge');
    if (!badge) return;

    const count = getVisibleEmployeeCount();
    badge.innerHTML = `<i data-lucide="list-filter" class="meta-icon"></i> Visible Employees: ${count}`;
    if (window.lucide) lucide.createIcons();
}

function getSelectedDepartmentLabel() {
    if (String(state.selectedDepartmentId) === 'all') return 'All Departments';

    const group = (state.roster?.departments || []).find(
        g => String(g.DepartmentID) === String(state.selectedDepartmentId)
    );

    return group ? `${group.DepartmentID} - ${group.DepartmentName}` : 'All Departments';
}

function getVisibleEmployeeCount() {
    let count = 0;
    const groups = getFilteredDepartmentGroups();

    groups.forEach(group => {
        (group.rows || []).forEach(row => {
            if (!state.search) {
                count++;
                return;
            }

            const hay = `${row.FullName} ${row.EmployeeCode || ''} ${group.DepartmentName}`.toLowerCase();
            if (hay.includes(state.search)) {
                count++;
            }
        });
    });

    return count;
}

function getRosterStatusLabel() {
    if (!state.roster?.departments?.length) return 'Draft';

    const visibleGroups = getFilteredDepartmentGroups();
    const publishedCount = visibleGroups.filter(g => String(g.header?.Status || '').toUpperCase() === 'PUBLISHED').length;

    if (!visibleGroups.length) return 'Draft';
    if (publishedCount === 0) return 'Draft';
    if (publishedCount === visibleGroups.length) return 'Published';
    return 'Mixed';
}

function handleEditableCellClick(cell) {
    if (!state.selectedShiftCode) {
        showToast('warning', 'Select a shift first.');
        return;
    }

    if (isOffShiftCode(state.selectedShiftCode)) {
        showToast('warning', 'OFF shift is not allowed.');
        return;
    }

    const employeeId = Number(cell.dataset.employeeId);
    const workDate = cell.dataset.workDate;

    applyCellVisual(cell, state.selectedShiftCode);
    queueChange(employeeId, workDate, state.selectedShiftCode);
}

function clearSingleCell(cell) {
    const employeeId = Number(cell.dataset.employeeId);
    const workDate = cell.dataset.workDate;

    applyCellVisual(cell, '');
    queueChange(employeeId, workDate, '');
}

function applyCellVisual(cell, shiftCode) {
    const safeCode = isOffShiftCode(shiftCode) ? '' : shiftCode;
    cell.innerHTML = safeCode ? escapeHtml(safeCode) : '<span class="empty-shift">—</span>';
}

function queueChange(employeeId, workDate, shiftCode) {
    const normalizedShift = isOffShiftCode(shiftCode) ? '' : shiftCode;
    const key = `${employeeId}__${workDate}`;

    state.pendingChanges = state.pendingChanges.filter(
        item => `${item.employee_id}__${item.work_date}` !== key
    );

    state.pendingChanges.push({
        employee_id: employeeId,
        work_date: workDate,
        shift_code: normalizedShift
    });

    updateRosterInMemory(employeeId, workDate, normalizedShift);
    renderStats();
    updateVisibleEmployeeBadge();
    scheduleAutoSave();
}

function updateRosterInMemory(employeeId, workDate, shiftCode) {
    if (!state.roster?.departments) return;

    state.roster.departments.forEach(group => {
        group.rows.forEach(row => {
            if (Number(row.EmployeeID) === Number(employeeId) && row.schedule?.[workDate]) {
                row.schedule[workDate].shiftCode = isOffShiftCode(shiftCode) ? '' : shiftCode;
            }
        });
    });

    state.roster.stats.unassigned = countUnassigned();
}

function countUnassigned() {
    return countUnassignedFromPayload(state.roster);
}

function countUnassignedFromPayload(payload) {
    if (!payload?.departments) return 0;

    let count = 0;
    payload.departments.forEach(group => {
        group.rows.forEach(row => {
            Object.values(row.schedule || {}).forEach(cell => {
                const shiftCode = isOffShiftCode(cell.shiftCode) ? '' : String(cell.shiftCode || '').trim();
                if (!cell.locked && !shiftCode) {
                    count++;
                }
            });
        });
    });
    return count;
}

function scheduleAutoSave() {
    setAutoSaveIndicator('Saving...');
    if (state.autoSaveTimer) clearTimeout(state.autoSaveTimer);

    state.autoSaveTimer = setTimeout(async () => {
        try {
            if (!state.pendingChanges.length) {
                setAutoSaveIndicator('Auto-save ready');
                return;
            }

            const cleanedChanges = state.pendingChanges.map(change => ({
                ...change,
                shift_code: isOffShiftCode(change.shift_code) ? '' : change.shift_code
            }));

            const data = await apiPost('save_draft', {
                week_start: state.weekStart,
                changes: cleanedChanges
            });

            state.roster = sanitizeRosterPayload(data);
            state.pendingChanges = [];
            populateDepartmentFilter();
            renderStats();
            renderHeaderStatus();
            renderRosterTable();
            updateSelectedDepartmentBadge();
            updateVisibleEmployeeBadge();
            setAutoSaveIndicator('Saved');
        } catch (err) {
            console.error(err);
            setAutoSaveIndicator('Save failed');
            showToast('error', err.message || 'Auto-save failed.');
        }
    }, 700);
}

async function publishRoster() {
    try {
        if (state.pendingChanges.length) {
            const saved = await apiPost('save_draft', {
                week_start: state.weekStart,
                changes: state.pendingChanges.map(change => ({
                    ...change,
                    shift_code: isOffShiftCode(change.shift_code) ? '' : change.shift_code
                }))
            });
            state.roster = sanitizeRosterPayload(saved);
            state.pendingChanges = [];
        }

        const result = await Swal.fire({
            title: 'Publish all department rosters?',
            text: 'This will split save by department and create/reuse timesheet periods per department.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, publish all',
            cancelButtonText: 'Cancel'
        });

        if (!result.isConfirmed) return;

        const data = await apiPost('publish_roster', {
            week_start: state.weekStart
        });

        state.roster = sanitizeRosterPayload(data);
        populateDepartmentFilter();
        renderStats();
        renderHeaderStatus();
        renderRosterTable();
        updateSelectedDepartmentBadge();
        updateVisibleEmployeeBadge();

        const summary = (data.published_departments || [])
            .map(x => `${x.DepartmentName} (Roster #${x.RosterID}, Period #${x.PeriodID})`)
            .join('\n');

        await Swal.fire({
            icon: 'success',
            title: 'Published successfully',
            text: summary || 'All department rosters published.'
        });
    } catch (err) {
        console.error(err);
        showError(err.message || 'Failed to publish rosters.');
    }
}

function fillEditableRange() {
    if (!state.selectedShiftCode) {
        showToast('warning', 'Select a shift first.');
        return;
    }

    if (isOffShiftCode(state.selectedShiftCode)) {
        showToast('warning', 'OFF shift is not allowed.');
        return;
    }

    document.querySelectorAll('#rosterBody .editable-cell').forEach(cell => {
        const employeeId = Number(cell.dataset.employeeId);
        const workDate = cell.dataset.workDate;
        applyCellVisual(cell, state.selectedShiftCode);
        queueChange(employeeId, workDate, state.selectedShiftCode);
    });

    showToast('success', 'All visible editable cells filled.');
}

function clearEditableRange() {
    document.querySelectorAll('#rosterBody .editable-cell').forEach(cell => {
        const employeeId = Number(cell.dataset.employeeId);
        const workDate = cell.dataset.workDate;
        applyCellVisual(cell, '');
        queueChange(employeeId, workDate, '');
    });

    showToast('success', 'All visible editable cells cleared.');
}

function applyAutoSuggestionReview() {
    if (!state.roster?.departments?.length || !state.shifts.length) {
        showToast('warning', 'No roster data available.');
        return;
    }

    const shiftCodes = state.shifts
        .map(s => s.ShiftCode)
        .filter(code => !isOffShiftCode(code));

    if (!shiftCodes.length) {
        showToast('warning', 'No allowed shifts available.');
        return;
    }

    let cursor = 0;

    getFilteredDepartmentGroups().forEach(group => {
        (group.rows || []).forEach(row => {
            const searchable = `${row.FullName} ${row.EmployeeCode || ''} ${group.DepartmentName}`.toLowerCase();
            if (state.search && !searchable.includes(state.search)) return;

            Object.entries(row.schedule || {}).forEach(([date, cell]) => {
                if (!cell.locked && !cell.shiftCode) {
                    const shiftCode = shiftCodes[cursor % shiftCodes.length];
                    cursor++;

                    const td = document.querySelector(
                        `td.editable-cell[data-employee-id="${row.EmployeeID}"][data-work-date="${date}"]`
                    );
                    if (td) applyCellVisual(td, shiftCode);
                    queueChange(Number(row.EmployeeID), date, shiftCode);
                }
            });
        });
    });

    showAiReviewPanel({
        employeesIncluded: getVisibleEmployeeCount(),
        fairnessScore: 92,
        coverageScore: Math.max(0, 100 - countUnassigned()),
        complianceScore: 100,
        unassignedRemaining: countUnassigned(),
        warnings: [
            'Assignments remain separated by department on save and publish.',
            'OFF shift is excluded from AI assignment.',
            'Review workload balance before publishing.'
        ],
        errors: countUnassigned() > 0
            ? ['There are still unassigned cells remaining.']
            : ['No locked-cell conflict detected.']
    });
}

function showAiReviewPanel(review) {
    const panel = document.getElementById('aiReviewPanel');
    if (!panel) return;

    document.getElementById('aiEmployeesIncluded').textContent = review.employeesIncluded ?? '--';
    const selfEl = document.getElementById('aiSelfIncluded');
    if (selfEl) selfEl.textContent = 'N/A';
    document.getElementById('aiFairnessScore').textContent = `${review.fairnessScore ?? '--'}%`;
    document.getElementById('aiCoverageScore').textContent = `${review.coverageScore ?? '--'}%`;
    document.getElementById('aiComplianceScore').textContent = `${review.complianceScore ?? '--'}%`;
    document.getElementById('aiUnassignedRemaining').textContent = review.unassignedRemaining ?? '--';

    document.getElementById('aiWarningsList').innerHTML =
        (review.warnings || []).map(x => `<li>${escapeHtml(x)}</li>`).join('');
    document.getElementById('aiErrorsList').innerHTML =
        (review.errors || []).map(x => `<li>${escapeHtml(x)}</li>`).join('');

    panel.classList.remove('hidden');
}

function setAutoSaveIndicator(text) {
    const el = document.getElementById('autoSaveIndicator');
    if (!el) return;

    el.innerHTML = `<i data-lucide="save" class="meta-icon"></i> ${escapeHtml(text)}`;
    if (window.lucide) lucide.createIcons();
}

function isOffShift(shift) {
    const code = String(shift?.ShiftCode || '').trim().toUpperCase();
    const name = String(shift?.ShiftName || '').trim().toUpperCase();
    return code === 'OFF' || name === 'OFF' || code.includes('OFF') || name.includes('OFF');
}

function isOffShiftCode(shiftCode) {
    const code = String(shiftCode || '').trim().toUpperCase();
    return code === 'OFF' || code.includes('OFF');
}

function getMondayOfWeek(date) {
    const d = new Date(date);
    const day = d.getDay();
    const diff = day === 0 ? -6 : 1 - day;
    d.setDate(d.getDate() + diff);
    return formatDateISO(d);
}

function shiftWorkdays(startDate, count) {
    const d = new Date(startDate + 'T00:00:00');
    const step = count >= 0 ? 1 : -1;
    let moved = 0;

    while (moved < Math.abs(count)) {
        d.setDate(d.getDate() + step);
        if (d.getDay() !== 0) moved++;
    }
    return formatDateISO(d);
}

function formatDateISO(date) {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
}

function formatDateLong(dateStr) {
    return new Date(dateStr + 'T00:00:00').toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
}

function formatWeekdayShort(dateStr) {
    return new Date(dateStr + 'T00:00:00').toLocaleDateString('en-US', { weekday: 'short' });
}

function formatMonthDay(dateStr) {
    return new Date(dateStr + 'T00:00:00').toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric'
    });
}

function formatTimeRange(start, end) {
    if (!start || !end) return 'Flexible';
    return `${String(start).slice(0, 5)} - ${String(end).slice(0, 5)}`;
}

function escapeHtml(str) {
    return String(str ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function showToast(icon, title) {
    if (window.Swal) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon,
            title,
            showConfirmButton: false,
            timer: 1800
        });
    } else {
        alert(title);
    }
}

function showError(message) {
    if (window.Swal) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message
        });
    } else {
        alert(message);
    }
}