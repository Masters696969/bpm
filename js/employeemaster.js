let selectionMode = false;
let currentEmployeeData = null;

document.addEventListener('DOMContentLoaded', () => {
    // Initialize theme and sidebar
    initializeThemeAndSidebar();
    
    fetchEmployees();
    lucide.createIcons();
    bindTopButtons();
    initClock();
});

function bindTopButtons() {
    const toggleBtn = document.getElementById('toggleSelectionModeBtn');
    const dispatchBtn = document.getElementById('dispatchSelectedBtn');
    const selectAll = document.getElementById('selectAllEmployees');

    if (toggleBtn) {
        toggleBtn.addEventListener('click', toggleSelectionMode);
    }

    if (dispatchBtn) {
        dispatchBtn.addEventListener('click', dispatchSelectedEmployees);
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            document.querySelectorAll('.emp-checkbox').forEach(cb => {
                if (cb.disabled) return;
                cb.checked = this.checked;
                const row = cb.closest('tr');
                if (row) row.classList.toggle('row-selected', cb.checked);
            });
            syncSelectAllState();
        });
    }
}

async function fetchEmployees() {
    try {
        const url = 'backend/be_employeemaster.php?action=fetch_employees';
        console.log('Fetching employees from:', url);

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });

        const result = await response.json();
        console.log('fetch_employees response:', result);

        if (!result.success) {
            Swal.fire('Error', result.message || 'Failed to fetch employees.', 'error');
            return;
        }

        renderTable(result.data || []);

        const emps = result.data || [];
        const el = id => document.getElementById(id);

        if (el('statTotal')) el('statTotal').textContent = emps.length;
        if (el('statRegular')) {
            el('statRegular').textContent = emps.filter(
                e => (e.EmploymentStatus || '').toLowerCase() === 'regular'
            ).length;
        }
        if (el('statProbationary')) {
            el('statProbationary').textContent = emps.filter(
                e => (e.EmploymentStatus || '').toLowerCase() === 'probationary'
            ).length;
        }

    } catch (error) {
        console.error(error);
        Swal.fire('Error', 'An error occurred while fetching employees.', 'error');
    }
}

function renderTable(employees) {
    const tbody = document.querySelector('#employeeTable tbody');
    if (!tbody) return;

    tbody.innerHTML = '';

    if (!employees.length) {
        const colSpan = selectionMode ? 8 : 7;
        tbody.innerHTML = `<tr><td colspan="${colSpan}" class="text-center">No employees found</td></tr>`;
        updateSelectionUI();
        return;
    }

    employees.forEach(emp => {
        const firstName = emp.FirstName || '';
        const lastName = emp.LastName || '';
        const initials = `${firstName.charAt(0) || ''}${lastName.charAt(0) || ''}`.toUpperCase() || '--';

        const dispatchStatus = String(emp.DispatchStatus || '').trim().toLowerCase();
        const alreadyPending = dispatchStatus === 'pending';
        const hasReceived = dispatchStatus === 'received';
        const isLockedDispatch = alreadyPending || hasReceived;
        const hasDispatch = ['pending', 'received', 'rejected'].includes(dispatchStatus);

        let dispatchBadge = '';
        if (!hasDispatch) {
            dispatchBadge = `<span style="display:inline-block;padding:6px 10px;border-radius:999px;background:#ecfeff;color:#155e75;font-size:12px;font-weight:700;">Ready</span>`;
        } else if (dispatchStatus === 'pending') {
            dispatchBadge = `<span style="display:inline-block;padding:6px 10px;border-radius:999px;background:#fef3c7;color:#92400e;font-size:12px;font-weight:700;">Pending</span>`;
        } else if (dispatchStatus === 'received') {
            dispatchBadge = `<span style="display:inline-block;padding:6px 10px;border-radius:999px;background:#dcfce7;color:#166534;font-size:12px;font-weight:700;">Received</span>`;
        } else if (dispatchStatus === 'rejected') {
            dispatchBadge = `<span style="display:inline-block;padding:6px 10px;border-radius:999px;background:#fee2e2;color:#991b1b;font-size:12px;font-weight:700;">Rejected</span>`;
        } else {
            dispatchBadge = `<span style="display:inline-block;padding:6px 10px;border-radius:999px;background:#ecfeff;color:#155e75;font-size:12px;font-weight:700;">Ready</span>`;
        }

        const tr = document.createElement('tr');
        tr.className = 'emp-row';
        tr.dataset.employeeId = emp.EmployeeID;

        tr.innerHTML = `
            <td class="select-cell" style="text-align:center; display:${selectionMode ? '' : 'none'};">
                <input type="checkbox" class="emp-checkbox" value="${emp.EmployeeID}" ${isLockedDispatch ? 'disabled' : ''}>
            </td>
            <td>
                <div class="emp-cell">
                    <div class="emp-avatar">${escapeHtml(initials)}</div>
                    <div>
                        <div class="emp-name">${escapeHtml(firstName)} ${escapeHtml(lastName)}</div>
                        <div class="emp-dept">${escapeHtml(emp.EmployeeCode || emp.EmployeeID)}</div>
                    </div>
                </div>
            </td>
            <td style="font-size:13px;color:var(--text-secondary)">${escapeHtml(emp.PositionName || '—')}</td>
            <td style="font-size:13px;color:var(--text-secondary)">${escapeHtml(emp.DepartmentName || '—')}</td>
            <td><span class="badge badge-${getStatusClass(emp.EmploymentStatus)}">${escapeHtml(emp.EmploymentStatus || 'Unknown')}</span></td>
            <td style="font-size:13px;color:var(--text-secondary)">${escapeHtml(emp.GradeLevel || '—')}</td>
            <td class="dispatch-status-cell" style="display:${selectionMode ? '' : 'none'};">${dispatchBadge}</td>
            <td>
                <button class="btn-review view-btn" onclick="event.stopPropagation(); viewProfile(${parseInt(emp.EmployeeID, 10)})">
                    <i data-lucide="file-user"></i> View File
                </button>
            </td>
        `;

        const checkbox = tr.querySelector('.emp-checkbox');

        if (checkbox) {
            checkbox.addEventListener('click', function (e) {
                e.stopPropagation();
                if (checkbox.disabled) return;
                tr.classList.toggle('row-selected', checkbox.checked);
                syncSelectAllState();
            });
        }

        tr.addEventListener('click', function (e) {
            if (!selectionMode) return;
            if (e.target.closest('.view-btn')) return;
            if (e.target.closest('.emp-checkbox')) return;
            if (isLockedDispatch) return;

            const rowCheckbox = tr.querySelector('.emp-checkbox');
            if (!rowCheckbox || rowCheckbox.disabled) return;

            rowCheckbox.checked = !rowCheckbox.checked;
            tr.classList.toggle('row-selected', rowCheckbox.checked);
            syncSelectAllState();
        });

        tr.style.cursor = selectionMode && !isLockedDispatch ? 'pointer' : 'default';

        tbody.appendChild(tr);
    });

    updateSelectionUI();
    syncSelectAllState();
    lucide.createIcons();
}

function toggleSelectionMode() {
    selectionMode = !selectionMode;

    if (!selectionMode) {
        document.querySelectorAll('.emp-checkbox').forEach(cb => cb.checked = false);
        document.querySelectorAll('#employeeTable tbody tr').forEach(tr => tr.classList.remove('row-selected'));

        const selectAll = document.getElementById('selectAllEmployees');
        if (selectAll) {
            selectAll.checked = false;
            selectAll.indeterminate = false;
        }
    }

    fetchEmployees();
}

function updateSelectionUI() {
    const header = document.getElementById('selectColumnHeader');
    const toolbar = document.getElementById('selectionToolbar');
    const dispatchBtn = document.getElementById('dispatchSelectedBtn');
    const toggleText = document.getElementById('toggleSelectionModeText');
    const selectAll = document.getElementById('selectAllEmployees');
    const dispatchHeader = document.getElementById('dispatchStatusHeader');

    if (header) header.style.display = selectionMode ? '' : 'none';
    if (toolbar) toolbar.style.display = selectionMode ? 'block' : 'none';
    if (dispatchBtn) dispatchBtn.style.display = selectionMode ? 'inline-flex' : 'none';
    if (toggleText) toggleText.textContent = selectionMode ? 'Exit Selection' : 'Select Employees';
    if (selectAll) {
        selectAll.checked = false;
        selectAll.indeterminate = false;
    }
    if (dispatchHeader) dispatchHeader.style.display = selectionMode ? '' : 'none';

    document.querySelectorAll('.select-cell').forEach(td => {
        td.style.display = selectionMode ? '' : 'none';
    });

    document.querySelectorAll('.dispatch-status-cell').forEach(td => {
        td.style.display = selectionMode ? '' : 'none';
    });
}

function syncSelectAllState() {
    const all = Array.from(document.querySelectorAll('.emp-checkbox:not(:disabled)'));
    const checked = all.filter(cb => cb.checked);

    const selectAll = document.getElementById('selectAllEmployees');
    if (selectAll) {
        selectAll.checked = all.length > 0 && all.length === checked.length;
        selectAll.indeterminate = checked.length > 0 && checked.length < all.length;
    }
}

function getSelectedEmployeeIds() {
    return Array.from(document.querySelectorAll('.emp-checkbox:checked'))
        .map(cb => parseInt(cb.value, 10))
        .filter(id => !isNaN(id) && id > 0);
}

async function dispatchSelectedEmployees() {
    const employeeIds = getSelectedEmployeeIds();

    if (!employeeIds.length) {
        Swal.fire('No Selection', 'Please select at least one employee.', 'warning');
        return;
    }

    const confirmResult = await Swal.fire({
        title: 'Dispatch selected employees?',
        text: `You selected ${employeeIds.length} employee(s).`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Dispatch',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#2ca078'
    });

    if (!confirmResult.isConfirmed) return;

    try {
        const formData = new FormData();
        formData.append('action', 'dispatch_employees');
        formData.append('employee_ids', JSON.stringify(employeeIds));

        console.log('dispatch_employees payload:', {
            action: 'dispatch_employees',
            employee_ids: employeeIds
        });

        const response = await fetch('backend/be_employeemaster.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        console.log('dispatch_employees response:', result);

        if (!result.success) {
            Swal.fire('Error', result.message || 'Dispatch failed.', 'error');
            return;
        }

        Swal.fire({
            icon: 'success',
            title: 'Dispatch Completed',
            text: `Inserted New: ${result.inserted_count || 0} | Updated to Pending: ${result.updated_count || 0} | Already Pending: ${result.already_pending_count || 0}`
        }).then(() => {
            selectionMode = false;
            fetchEmployees();
        });

    } catch (error) {
        console.error(error);
        Swal.fire('Error', 'An error occurred while dispatching employees.', 'error');
    }
}

async function viewProfile(id) {
    try {
        const response = await fetch(`backend/be_employeemaster.php?action=get_employee_details&id=${id}`);
        const result = await response.json();

        if (!result.success) {
            Swal.fire('Error', result.message || 'Failed to load profile.', 'error');
            return;
        }

        renderResumeModal(result.data);
        const modal = document.getElementById('employeeModal');
        const dlg = modal?.querySelector('.modal-dialog');
        if (dlg) dlg.classList.remove('ep-edit-dialog');
        if (modal) {
            modal.style.display = 'flex';
            modal.classList.add('show');
        }

    } catch (error) {
        console.error(error);
        Swal.fire('Error', 'An error occurred while loading the profile.', 'error');
    }
}

function renderResumeModal(data) {
    const modalBody = document.getElementById('modalBody');
    const modalTitle = document.getElementById('modalTitle');

    if (!modalBody || !modalTitle) return;

    modalTitle.textContent = '';

    const initials = `${data.FirstName?.charAt(0) || ''}${data.LastName?.charAt(0) || ''}`;
    const statusClass = getStatusClass(data.EmploymentStatus);
    const statusColors = { active: '#059669', unverified: '#d97706', inactive: '#dc2626' };
    const statusColor = statusColors[statusClass] || '#6b7280';

    modalBody.style.padding = '0';

    modalBody.innerHTML = `
    <div class="ep-container">
        <div class="ep-hero">
            <button class="ep-close" onclick="closeModal()" title="Close">&times;</button>
            <div class="ep-hero-content">
                <div class="ep-avatar-wrap">
                    <div class="ep-avatar">${escapeHtml(initials.toUpperCase())}</div>
                </div>
                <div class="ep-hero-info">
                    <h2 class="ep-name">${escapeHtml(data.FirstName || '')} ${escapeHtml(data.MiddleName ? data.MiddleName + ' ' : '')}${escapeHtml(data.LastName || '')}</h2>
                    <p class="ep-position">${escapeHtml(data.PositionName || 'No Position')}</p>
                    <div class="ep-meta">
                        <span class="ep-meta-chip"><i data-lucide="building-2"></i>${escapeHtml(data.DepartmentName || 'No Department')}</span>
                        <span class="ep-meta-chip"><i data-lucide="hash"></i>${escapeHtml(data.EmployeeCode || data.EmployeeID || '—')}</span>
                        <span class="ep-status-badge" style="background:${statusColor}20;color:${statusColor};border:1px solid ${statusColor}40">
                          <span class="ep-status-dot" style="background:${statusColor}"></span>${escapeHtml(data.EmploymentStatus || '—')}
                        </span>
                    </div>
                </div>
                <button class="ep-edit-btn" onclick="editEmployee(${parseInt(data.EmployeeID, 10)})">
                    <i data-lucide="pencil"></i> Edit Profile
                </button>
            </div>
        </div>

        <div class="ep-stats-bar">
            <div class="ep-stat">
                <i data-lucide="calendar"></i>
                <div><span class="ep-stat-val">${data.HiringDate ? new Date(data.HiringDate).toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' }) : '—'}</span><span class="ep-stat-lbl">Date Hired</span></div>
            </div>
            <div class="ep-stat-divider"></div>
            <div class="ep-stat">
                <i data-lucide="layers"></i>
                <div><span class="ep-stat-val">${escapeHtml(data.GradeLevel || '—')}</span><span class="ep-stat-lbl">Salary Grade</span></div>
            </div>
            <div class="ep-stat-divider"></div>
            <div class="ep-stat">
                <i data-lucide="mail"></i>
                <div><span class="ep-stat-val" style="font-size:12px">${escapeHtml(data.WorkEmail || '—')}</span><span class="ep-stat-lbl">Work Email</span></div>
            </div>
            <div class="ep-stat-divider"></div>
            <div class="ep-stat">
                <i data-lucide="phone"></i>
                <div><span class="ep-stat-val">${escapeHtml(data.PhoneNumber || '—')}</span><span class="ep-stat-lbl">Phone</span></div>
            </div>
        </div>

        <div class="ep-body">
            <div class="ep-section">
                <div class="ep-section-hdr ep-hdr-blue"><i data-lucide="user"></i> Personal Information</div>
                <div class="ep-fields">
                    <div class="ep-field"><label>Date of Birth</label><span>${escapeHtml(data.DateOfBirth || '—')}</span></div>
                    <div class="ep-field"><label>Gender</label><span>${escapeHtml(data.Gender || '—')}</span></div>
                    <div class="ep-field"><label>Personal Email</label><span>${escapeHtml(data.PersonalEmail || '—')}</span></div>
                    <div class="ep-field full"><label>Permanent Address</label><span>${escapeHtml(data.PermanentAddress || '—')}</span></div>
                </div>
            </div>

            <div class="ep-section">
                <div class="ep-section-hdr ep-hdr-purple"><i data-lucide="landmark"></i> Government Numbers</div>
                <div class="ep-fields">
                    <div class="ep-field"><label>TIN</label><span>${escapeHtml(data.TINNumber || '—')}</span></div>
                    <div class="ep-field"><label>SSS</label><span>${escapeHtml(data.SSSNumber || '—')}</span></div>
                    <div class="ep-field"><label>PhilHealth</label><span>${escapeHtml(data.PhilHealthNumber || '—')}</span></div>
                    <div class="ep-field"><label>Pag-IBIG</label><span>${escapeHtml(data.PagIBIGNumber || '—')}</span></div>
                </div>
            </div>

            <div class="ep-section">
                <div class="ep-section-hdr ep-hdr-green"><i data-lucide="credit-card"></i> Bank & Compensation</div>
                <div class="ep-fields">
                    <div class="ep-field"><label>Bank Name</label><span style="color:var(--brand-green);font-weight:600">${escapeHtml(data.BankName || 'BDO')}</span></div>
                    <div class="ep-field"><label>Account Number</label><span>${escapeHtml(data.BankAccountNumber || '—')}</span></div>
                    <div class="ep-field"><label>Account Type</label><span style="color:var(--brand-green);font-weight:600">${escapeHtml(data.AccountType || 'Payroll')}</span></div>
                    <div class="ep-field"><label>Base Salary</label><span>${(data.BaseSalary !== null && data.BaseSalary !== undefined && data.BaseSalary !== '') ? formatCurrency(data.BaseSalary) : '—'}</span></div>
                    <div class="ep-field full"><label>Salary Range</label><span>${(data.MinSalary !== null && data.MinSalary !== undefined && data.MinSalary !== '') ? formatCurrency(data.MinSalary) + ' – ' + formatCurrency(data.MaxSalary) : '—'}</span></div>
                </div>
            </div>

            <div class="ep-section">
                <div class="ep-section-hdr ep-hdr-red"><i data-lucide="heart-pulse"></i> Emergency Contact</div>
                <div class="ep-fields">
                    <div class="ep-field"><label>Contact Name</label><span>${escapeHtml(data.ContactName || '—')}</span></div>
                    <div class="ep-field"><label>Relationship</label><span>${escapeHtml(data.Relationship || '—')}</span></div>
                    <div class="ep-field full"><label>Phone</label><span>${escapeHtml(data.EmergencyPhone || '—')}</span></div>
                </div>
            </div>
        </div>
    </div>
    `;

    lucide.createIcons();
}

function getStatusClass(status) {
    if (!status) return 'inactive';
    switch (String(status).toLowerCase()) {
        case 'regular': return 'active';
        case 'probationary': return 'unverified';
        case 'resigned':
        case 'terminated':
        case 'inactive':
        case 'rejected':
            return 'inactive';
        default:
            return 'active';
    }
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(Number(amount) || 0);
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

async function editEmployee(id) {
    try {
        const dlg = document.querySelector('#employeeModal .modal-dialog');
        if (dlg) dlg.classList.add('ep-edit-dialog');

        const response = await fetch(`backend/be_employeemaster.php?action=get_employee_details&id=${id}`);
        const result = await response.json();

        if (result.success) {
            currentEmployeeData = result.data;
            renderEditForm(result.data);
        } else {
            Swal.fire('Error', 'Failed to load employee data for editing.', 'error');
        }
    } catch (error) {
        console.error(error);
        Swal.fire('Error', 'An error occurred.', 'error');
    }
}

function renderEditForm(data) {
    const modalBody = document.getElementById('modalBody');
    if (!modalBody) return;

    const initials = `${data.FirstName?.charAt(0) || ''}${data.LastName?.charAt(0) || ''}`.toUpperCase();
    const statusClass = getStatusClass(data.EmploymentStatus);
    const statusColors = { active: '#059669', unverified: '#d97706', inactive: '#dc2626' };
    const statusColor = statusColors[statusClass] || '#6b7280';

    modalBody.style.padding = '0';
    modalBody.innerHTML = `
    <div class="ep-container ep-edit">
        <div class="ep-hero">
            <button class="ep-close" onclick="closeModal()" title="Close">&times;</button>
            <div class="ep-hero-content">
                <div class="ep-avatar-wrap">
                    <div class="ep-avatar">${escapeHtml(initials)}</div>
                </div>
                <div class="ep-hero-info">
                    <h2 class="ep-name">Edit Profile</h2>
                    <p class="ep-position">${escapeHtml(data.FirstName || '')} ${escapeHtml(data.LastName || '')}</p>
                    <div class="ep-meta">
                        <span class="ep-meta-chip"><i data-lucide="building-2"></i>${escapeHtml(data.DepartmentName || 'No Department')}</span>
                        <span class="ep-meta-chip"><i data-lucide="hash"></i>${escapeHtml(data.EmployeeCode || data.EmployeeID || '—')}</span>
                        <span class="ep-status-badge" style="background:${statusColor}20;color:${statusColor};border:1px solid ${statusColor}40">
                          <span class="ep-status-dot" style="background:${statusColor}"></span>${escapeHtml(data.EmploymentStatus || '—')}
                        </span>
                    </div>
                </div>
                <button class="ep-edit-btn" onclick="viewProfile(${parseInt(data.EmployeeID, 10)})">
                    <i data-lucide="arrow-left"></i> Back to Profile
                </button>
            </div>
        </div>

        <div class="ep-body">
            <form id="editEmployeeForm" onsubmit="submitEditForm(event)" style="display:contents">
                <input type="hidden" name="EmployeeID" value="${escapeHtml(data.EmployeeID || '')}">
                <div class="ep-section">
                    <div class="ep-section-hdr ep-hdr-blue"><i data-lucide="user"></i> Personal Information</div>
                    <div class="ep-fields">
                        <div class="ep-field"><label>First Name</label><input type="text" name="FirstName" class="ep-input" value="${escapeHtml(data.FirstName || '')}" required></div>
                        <div class="ep-field"><label>Last Name</label><input type="text" name="LastName" class="ep-input" value="${escapeHtml(data.LastName || '')}" required></div>
                        <div class="ep-field"><label>Middle Name</label><input type="text" name="MiddleName" class="ep-input" value="${escapeHtml(data.MiddleName || '')}"></div>
                        <div class="ep-field"><label>Date of Birth</label><input type="date" name="DateOfBirth" class="ep-input" value="${escapeHtml(data.DateOfBirth || '')}"></div>
                        <div class="ep-field"><label>Gender</label>
                            <select name="Gender" class="ep-input">
                                <option value="Male" ${data.Gender === 'Male' ? 'selected' : ''}>Male</option>
                                <option value="Female" ${data.Gender === 'Female' ? 'selected' : ''}>Female</option>
                            </select>
                        </div>
                        <div class="ep-field"><label>Personal Email</label><input type="email" name="PersonalEmail" class="ep-input" value="${escapeHtml(data.PersonalEmail || '')}"></div>
                        <div class="ep-field full"><label>Permanent Address</label><input type="text" name="PermanentAddress" class="ep-input" value="${escapeHtml(data.PermanentAddress || '')}"></div>
                    </div>
                </div>

                <div class="ep-section">
                    <div class="ep-section-hdr ep-hdr-purple"><i data-lucide="landmark"></i> Government Numbers</div>
                    <div class="ep-fields">
                        <div class="ep-field"><label>TIN</label><input type="text" name="TINNumber" class="ep-input" value="${escapeHtml(data.TINNumber || '')}"></div>
                        <div class="ep-field"><label>SSS</label><input type="text" name="SSSNumber" class="ep-input" value="${escapeHtml(data.SSSNumber || '')}"></div>
                        <div class="ep-field"><label>PhilHealth</label><input type="text" name="PhilHealthNumber" class="ep-input" value="${escapeHtml(data.PhilHealthNumber || '')}"></div>
                        <div class="ep-field"><label>Pag-IBIG</label><input type="text" name="PagIBIGNumber" class="ep-input" value="${escapeHtml(data.PagIBIGNumber || '')}"></div>
                    </div>
                </div>

                <div class="ep-section">
                    <div class="ep-section-hdr ep-hdr-indigo"><i data-lucide="briefcase"></i> Employment Information</div>
                    <div class="ep-fields">
                        <div class="ep-field">
                            <label>Employment Status</label>
                            <select name="EmploymentStatus" class="ep-input">
                                <option value="Regular" ${data.EmploymentStatus === 'Regular' ? 'selected' : ''}>Regular</option>
                                <option value="Probationary" ${data.EmploymentStatus === 'Probationary' ? 'selected' : ''}>Probationary</option>
                                <option value="Resigned" ${data.EmploymentStatus === 'Resigned' ? 'selected' : ''}>Resigned</option>
                                <option value="Terminated" ${data.EmploymentStatus === 'Terminated' ? 'selected' : ''}>Terminated</option>
                            </select>
                        </div>
                        <div class="ep-field"><label>Date Hired</label><input type="date" name="HiringDate" class="ep-input" value="${escapeHtml(data.HiringDate || '')}"></div>
                        <div class="ep-field"><label>Work Email</label><input type="email" name="WorkEmail" class="ep-input" value="${escapeHtml(data.WorkEmail || '')}"></div>
                        <div class="ep-field"><label>Phone (Work)</label><input type="text" name="PhoneNumber" class="ep-input" value="${escapeHtml(data.PhoneNumber || '')}"></div>
                    </div>
                </div>

                <div class="ep-section">
                    <div class="ep-section-hdr ep-hdr-green"><i data-lucide="credit-card"></i> Bank & Compensation</div>
                    <div class="ep-fields">
                        <div class="ep-field"><label>Bank Name</label><input type="text" name="BankName" class="ep-input" value="BDO" readonly></div>
                        <div class="ep-field"><label>Account Number</label><input type="text" name="BankAccountNumber" class="ep-input" value="${escapeHtml(data.BankAccountNumber || '')}"></div>
                        <div class="ep-field"><label>Account Type</label><input type="text" name="AccountType" class="ep-input" value="Payroll" readonly></div>
                        <div class="ep-field"><label>Base Salary</label><input type="number" step="0.01" name="BaseSalary" class="ep-input" value="${escapeHtml(data.BaseSalary || 0)}"></div>
                    </div>
                </div>

                <div class="ep-section">
                    <div class="ep-section-hdr ep-hdr-red"><i data-lucide="heart-pulse"></i> Emergency Contact</div>
                    <div class="ep-fields">
                        <div class="ep-field"><label>Contact Name</label><input type="text" name="ContactName" class="ep-input" value="${escapeHtml(data.ContactName || '')}"></div>
                        <div class="ep-field"><label>Relationship</label><input type="text" name="Relationship" class="ep-input" value="${escapeHtml(data.Relationship || '')}"></div>
                        <div class="ep-field full"><label>Phone</label><input type="text" name="EmergencyPhone" class="ep-input" value="${escapeHtml(data.EmergencyPhone || '')}"></div>
                    </div>
                </div>
            </form>
        </div>

        <div class="ep-save-bar">
            <button type="submit" form="editEmployeeForm" class="ep-save-btn">
                <i data-lucide="save"></i> Save Changes
            </button>
        </div>
    </div>
    `;
    lucide.createIcons();
}

async function submitEditForm(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    formData.append('action', 'update_employee');

    try {
        const response = await fetch('backend/be_employeemaster.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            closeModal();
            Swal.fire('Success', 'Employee updated successfully!', 'success').then(() => fetchEmployees());
        } else {
            Swal.fire('Error', result.message || 'Error updating employee', 'error');
        }
    } catch (error) {
        console.error(error);
        Swal.fire('Error', 'An error occurred while saving.', 'error');
    }
}

function closeModal() {
    const modal = document.getElementById('employeeModal');
    if (modal) {
        modal.style.display = 'none';
        modal.classList.remove('show');
    }
}

(function () {
    const path = window.location.pathname;
    const page = path.split('/').pop() || 'dashboard.php';
    const current = page.split('?')[0];

    document.querySelectorAll('.sidebar .nav-item, .sidebar .submenu-item').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.sidebar .nav-item-group').forEach(group => group.classList.remove('active'));

    const submenuMatch = document.querySelector(`.sidebar a.submenu-item[href$="${current}"]`);
    if (submenuMatch) {
        submenuMatch.classList.add('active');
        const parentGroup = submenuMatch.closest('.nav-item-group');
        if (parentGroup) {
            parentGroup.classList.add('active');
            const submenu = parentGroup.querySelector('.submenu');
            if (submenu) submenu.style.maxHeight = '500px';
            const btn = parentGroup.querySelector('.nav-item.has-submenu');
            if (btn) btn.classList.add('active');
        }
        return;
    }

    const navMatch = document.querySelector(`.sidebar a.nav-item[href$="${current}"]`);
    if (navMatch) navMatch.classList.add('active');
})();

document.addEventListener('DOMContentLoaded', () => {
    const nameEl = document.querySelector('.sidebar-footer .user-name');
    const roleEl = document.querySelector('.sidebar-footer .user-role');
    const umdName = document.getElementById('umdName');
    const umdRole = document.getElementById('umdRole');
    const umdAvatar = document.getElementById('umdAvatar');

    if (nameEl && umdName) {
        const name = nameEl.textContent.trim();
        umdName.textContent = name;
        if (umdAvatar) umdAvatar.textContent = name.charAt(0).toUpperCase();
    }

    if (roleEl && umdRole) umdRole.textContent = roleEl.textContent.trim();

    const btn = document.getElementById('userMenuBtn');
    const dd = document.getElementById('userMenuDropdown');

    if (btn && dd) {
        btn.addEventListener('click', e => {
            e.stopPropagation();
            dd.classList.toggle('umd-open');
        });

        document.addEventListener('click', e => {
            if (!dd.contains(e.target) && e.target !== btn) dd.classList.remove('umd-open');
        });

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') dd.classList.remove('umd-open');
        });
    }
});

function initClock() {
    const clockEl = document.getElementById('realTimeClock');
    if (!clockEl) return;

    const updateClock = () => {
        const days = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
        const months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
        const now = new Date();
        const dayName = days[now.getDay()];
        const monthName = months[now.getMonth()];
        const date = now.getDate();
        const year = now.getFullYear();
        let hours = now.getHours();
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const seconds = now.getSeconds().toString().padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12;
        const formattedHours = hours.toString().padStart(2, '0');

        clockEl.textContent = `${dayName}, ${monthName} ${date}, ${year}, ${formattedHours}:${minutes}:${seconds} ${ampm}`;
    };

    setInterval(updateClock, 1000);
    updateClock();
}
