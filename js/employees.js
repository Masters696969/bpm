/**
 * Global variables
 */
let receiveEmployeesData = [];

document.addEventListener("DOMContentLoaded", () => {
    if (window.lucide) {
        window.lucide.createIcons();
    }

    initClock();
    bindEvents();
    fetchPendingEmployees();
});

/**
 * Main page event bindings
 */
function bindEvents() {
    const refreshBtn = document.getElementById("refreshBtn");
    const receiveSelectedBtn = document.getElementById("receiveSelectedBtn");
    const searchInput = document.getElementById("searchInput");
    const departmentFilter = document.getElementById("departmentFilter");
    const selectAll = document.getElementById("selectAllEmployees");

    if (refreshBtn) {
        refreshBtn.addEventListener("click", fetchPendingEmployees);
    }

    if (receiveSelectedBtn) {
        receiveSelectedBtn.addEventListener("click", receiveSelectedEmployees);
    }

    if (searchInput) {
        searchInput.addEventListener("input", filterTable);
    }

    if (departmentFilter) {
        departmentFilter.addEventListener("change", filterTable);
    }

    if (selectAll) {
        selectAll.addEventListener("change", function () {
            document.querySelectorAll(".receive-checkbox:not(:disabled)").forEach(cb => {
                cb.checked = this.checked;
            });
            syncSelectAllState();
        });
    }
}

/**
 * Fetch pending dispatched employees
 */
async function fetchPendingEmployees() {
    const tbody = document.getElementById("receiveTableBody");

    if (tbody) {
        tbody.innerHTML = `<tr><td colspan="7" class="empty-state-cell">Loading employees...</td></tr>`;
    }

    try {
        const response = await fetch("backend/employees_data.php?action=fetch_pending_dispatch");
        const result = await response.json();

        if (result.success) {
            receiveEmployeesData = result.data || [];
            populateDepartmentFilter(receiveEmployeesData);
            renderTable(receiveEmployeesData);
            updateStats(receiveEmployeesData);
        } else {
            receiveEmployeesData = [];
            populateDepartmentFilter([]);
            renderTable([]);
            updateStats([]);
            Swal.fire("Error", result.message || "Failed to load employees.", "error");
        }
    } catch (error) {
        console.error(error);
        receiveEmployeesData = [];
        populateDepartmentFilter([]);
        renderTable([]);
        updateStats([]);
        Swal.fire("Error", "Failed to fetch employees.", "error");
    }
}

/**
 * Populate department dropdown filter
 */
function populateDepartmentFilter(data) {
    const departmentFilter = document.getElementById("departmentFilter");
    if (!departmentFilter) return;

    const currentValue = departmentFilter.value || "";

    const departments = [...new Set(
        data.map(emp => String(emp.DepartmentName || "").trim()).filter(Boolean)
    )].sort((a, b) => a.localeCompare(b));

    departmentFilter.innerHTML = `<option value="">All Departments</option>`;

    departments.forEach(dept => {
        const option = document.createElement("option");
        option.value = dept;
        option.textContent = dept;
        departmentFilter.appendChild(option);
    });

    const valueStillExists = departments.includes(currentValue);
    departmentFilter.value = valueStillExists ? currentValue : "";
}

/**
 * Render employee table
 */
function renderTable(data) {
    const tbody = document.getElementById("receiveTableBody");
    if (!tbody) return;

    tbody.innerHTML = "";

    if (!data.length) {
        tbody.innerHTML = `<tr><td colspan="7" class="empty-state-cell">No pending dispatched employees found.</td></tr>`;
        syncSelectAllState();
        return;
    }

    data.forEach(emp => {
        const firstName = emp.FirstName || "";
        const lastName = emp.LastName || "";
        const initials = `${firstName.charAt(0) || ""}${lastName.charAt(0) || ""}`.toUpperCase() || "--";

        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td style="text-align:center;">
                <input type="checkbox" class="receive-checkbox" value="${emp.EmployeeID}">
            </td>
            <td class="emp-column">
                <div class="emp-cell">
                    <div class="emp-avatar">${escapeHtml(initials)}</div>
                    <div>
                        <div class="emp-name">${escapeHtml(firstName)} ${escapeHtml(lastName)}</div>
                        <div class="emp-sub">${escapeHtml(emp.EmployeeCode || emp.EmployeeID || "—")}</div>
                    </div>
                </div>
            </td>
            <td>${escapeHtml(emp.PositionName || "—")}</td>
            <td>${escapeHtml(emp.DepartmentName || "—")}</td>
            <td>
                <span class="badge ${getEmploymentBadgeClass(emp.EmploymentStatus)}">
                    ${escapeHtml(emp.EmploymentStatus || "Unknown")}
                </span>
            </td>
            <td>
                <span class="badge badge-dispatch-pending">
                    ${escapeHtml(emp.DispatchStatus || "Pending")}
                </span>
            </td>
            <td>
                <div class="row-actions">
                    <button type="button" class="btn-sm btn-receive" onclick="receiveSingleEmployee(${emp.EmployeeID})">Receive</button>
                </div>
            </td>
        `;

        const cb = tr.querySelector(".receive-checkbox");
        if (cb) {
            cb.addEventListener("change", syncSelectAllState);
        }

        tbody.appendChild(tr);
    });

    syncSelectAllState();

    if (window.lucide) {
        window.lucide.createIcons();
    }
}

/**
 * Update statistics cards
 */
function updateStats(data) {
    const total = data.length;
    const departments = new Set(
        data.map(emp => String(emp.DepartmentName || "").trim()).filter(Boolean)
    ).size;

    setText("statTotal", total);
    setText("statReady", total);
    setText("statDepartments", departments);
}

function setText(id, value) {
    const el = document.getElementById(id);
    if (el) {
        el.textContent = value;
    }
}

/**
 * Search + Department filter
 */
function filterTable() {
    const keyword = (document.getElementById("searchInput")?.value || "").toLowerCase().trim();
    const selectedDepartment = (document.getElementById("departmentFilter")?.value || "").toLowerCase().trim();

    const filtered = receiveEmployeesData.filter(emp => {
        const matchesKeyword = !keyword || [
            emp.FirstName,
            emp.LastName,
            emp.EmployeeCode,
            emp.DepartmentName,
            emp.PositionName
        ].some(val => String(val || "").toLowerCase().includes(keyword));

        const empDepartment = String(emp.DepartmentName || "").toLowerCase().trim();
        const matchesDepartment = !selectedDepartment || empDepartment === selectedDepartment;

        return matchesKeyword && matchesDepartment;
    });

    renderTable(filtered);
}

/**
 * Checkbox sync
 */
function syncSelectAllState() {
    const all = Array.from(document.querySelectorAll(".receive-checkbox"));
    const checked = all.filter(cb => cb.checked);
    const selectAll = document.getElementById("selectAllEmployees");

    if (selectAll) {
        selectAll.checked = all.length > 0 && all.length === checked.length;
        selectAll.indeterminate = checked.length > 0 && checked.length < all.length;
    }
}

/**
 * Receive selected employees
 */
async function receiveSelectedEmployees() {
    const ids = Array.from(document.querySelectorAll(".receive-checkbox:checked"))
        .map(cb => parseInt(cb.value, 10));

    if (!ids.length) {
        Swal.fire("No Selection", "Please select at least one employee.", "warning");
        return;
    }

    processReceive(ids);
}

/**
 * Receive single employee
 */
async function receiveSingleEmployee(id) {
    processReceive([id]);
}

/**
 * Process receive request
 */
async function processReceive(employeeIds) {
    const result = await Swal.fire({
        title: "Receive Employees?",
        text: `You are about to receive ${employeeIds.length} employee(s).`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Yes, Receive",
        confirmButtonColor: "#2ca078"
    });

    if (!result.isConfirmed) return;

    try {
        const formData = new FormData();
        formData.append("action", "receive_employees");
        formData.append("employee_ids", JSON.stringify(employeeIds));

        const response = await fetch("backend/employees_data.php", {
            method: "POST",
            body: formData
        });

        const res = await response.json();

        if (res.success) {
            Swal.fire("Success", res.message, "success").then(() => {
                fetchPendingEmployees();
            });
        } else {
            Swal.fire("Error", res.message || "Failed to receive employees.", "error");
        }
    } catch (error) {
        console.error(error);
        Swal.fire("Error", "An error occurred.", "error");
    }
}

/**
 * Helpers
 */
function escapeHtml(value) {
    return String(value ?? "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function getEmploymentBadgeClass(status) {
    const s = String(status || "").toLowerCase();

    if (s === "regular") return "badge-status-regular";
    if (s === "probationary") return "badge-status-probationary";

    return "badge-status-other";
}

/**
 * Realtime clock
 */
function initClock() {
    const el = document.getElementById("realTimeClock");
    if (!el) return;

    const update = () => {
        el.textContent = new Date().toLocaleString("en-US", {
            weekday: "short",
            month: "short",
            day: "2-digit",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
            hour12: true
        }).toUpperCase();
    };

    setInterval(update, 1000);
    update();
}