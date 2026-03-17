document.addEventListener('DOMContentLoaded', () => {
    // Initialize theme and sidebar
    initializeThemeAndSidebar();
    
    fetchEmployees();
    lucide.createIcons();
    
    // Initialize pagination and filters
    initializePagination();
});

function initializeThemeAndSidebar() {
    const body = document.body;
    const themeToggle = document.getElementById("themeToggle");
    const sidebarToggle = document.getElementById("sidebarToggle");
    const sidebar = document.getElementById("sidebar");
    const mobileMenuBtn = document.getElementById("mobileMenuBtn");

    // Theme Logic
    const savedTheme = localStorage.getItem("theme");
    if (savedTheme === "dark") body.classList.add("dark-mode");

    themeToggle.addEventListener("click", () => {
        body.classList.toggle("dark-mode");
        localStorage.setItem("theme", body.classList.contains("dark-mode") ? "dark" : "light");
    });

    // Sidebar & Mobile Logic
    sidebarToggle.addEventListener("click", () => {
        sidebar.classList.toggle("collapsed");
        localStorage.setItem("sidebarCollapsed", sidebar.classList.contains("collapsed"));
    });

    if (localStorage.getItem("sidebarCollapsed") === "true") sidebar.classList.add("collapsed");

    mobileMenuBtn.addEventListener("click", () => sidebar.classList.toggle("mobile-open"));

    // Submenu Logic
    document.querySelectorAll(".nav-item.has-submenu").forEach((item) => {
        item.addEventListener("click", (e) => {
            const module = item.getAttribute("data-module");
            const submenu = document.getElementById(`submenu-${module}`);
            if (submenu) {
                submenu.classList.toggle("active");
                item.classList.toggle("active");
            }
        });
    });

    // Active Page Highlighting
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
                if (submenu) {
                    submenu.classList.add('active');
                    submenu.style.maxHeight = '500px';
                }
                const btn = parentGroup.querySelector('.nav-item.has-submenu');
                if (btn) btn.classList.add('active');
            }
            return;
        }

        const navMatch = document.querySelector(`.sidebar a.nav-item[href$="${current}"]`);
        if (navMatch) navMatch.classList.add('active');
    })();

    // Real-time clock
    function updateClock() {
        const now = new Date();
        const options = { 
            weekday: 'short', year: 'numeric', month: 'short', day: 'numeric',
            hour: '2-digit', minute: '2-digit', second: '2-digit'
        };
        const clockElement = document.getElementById('realTimeClock');
        if (clockElement) {
            clockElement.textContent = now.toLocaleDateString('en-US', options);
        }
    }
    setInterval(updateClock, 1000);
    updateClock();
}

// Pagination variables
let currentPage = 1;
let itemsPerPage = 10;
let allEmployees = [];
let filteredEmployees = [];

function initializePagination() {
    const departmentFilter = document.getElementById('departmentFilter');
    const searchInput = document.getElementById('empTableSearch');
    const prevBtn = document.getElementById('prevPageBtn');
    const nextBtn = document.getElementById('nextPageBtn');

    // Department filter event
    if (departmentFilter) {
        departmentFilter.addEventListener('change', (e) => {
            currentPage = 1;
            applyFiltersAndRender();
        });
    }

    // Search event
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            currentPage = 1;
            applyFiltersAndRender();
        });
    }

    // Pagination button events
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                applyFiltersAndRender();
            }
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            const totalPages = Math.ceil(filteredEmployees.length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                applyFiltersAndRender();
            }
        });
    }
}

function applyFiltersAndRender() {
    const departmentFilter = document.getElementById('departmentFilter');
    const searchInput = document.getElementById('empTableSearch');
    
    // Apply filters
    filteredEmployees = allEmployees.filter(emp => {
        const matchesDepartment = !departmentFilter?.value || emp.DepartmentName === departmentFilter.value;
        const matchesSearch = !searchInput?.value || 
            emp.FirstName.toLowerCase().includes(searchInput.value.toLowerCase()) ||
            emp.LastName.toLowerCase().includes(searchInput.value.toLowerCase()) ||
            emp.EmployeeCode?.toLowerCase().includes(searchInput.value.toLowerCase());
        
        return matchesDepartment && matchesSearch;
    });

    renderPagination();
    renderFilteredTable();
}

function renderPagination() {
    const totalPages = Math.ceil(filteredEmployees.length / itemsPerPage);
    const paginationInfo = document.getElementById('paginationInfo');
    const paginationNumbers = document.getElementById('paginationNumbers');
    const prevBtn = document.getElementById('prevPageBtn');
    const nextBtn = document.getElementById('nextPageBtn');

    if (!paginationInfo || !paginationNumbers || !prevBtn || !nextBtn) return;

    // Update info text
    const startItem = filteredEmployees.length === 0 ? 0 : (currentPage - 1) * itemsPerPage + 1;
    const endItem = Math.min(currentPage * itemsPerPage, filteredEmployees.length);
    paginationInfo.textContent = `Showing ${startItem} to ${endItem} of ${filteredEmployees.length} entries`;

    // Update button states
    prevBtn.disabled = currentPage === 1;
    nextBtn.disabled = currentPage === totalPages || totalPages === 0;

    // Generate page numbers
    paginationNumbers.innerHTML = '';
    
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
            const button = document.createElement('button');
            button.style.cssText = 'display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border: 1px solid var(--border-color, #e5e7eb); border-radius: 8px; background: ' + (i === currentPage ? 'var(--brand-green, #2ca078)' : 'var(--surface, #ffffff)') + '; color: ' + (i === currentPage ? 'white' : 'var(--text-primary, #111827)') + '; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.2s;';
            button.textContent = i;
            button.addEventListener('click', () => {
                currentPage = i;
                applyFiltersAndRender();
            });
            paginationNumbers.appendChild(button);
        } else if (i === currentPage - 2 || i === currentPage + 2) {
            const ellipsis = document.createElement('span');
            ellipsis.textContent = '...';
            ellipsis.style.cssText = 'padding: 0 8px; color: var(--text-tertiary);';
            paginationNumbers.appendChild(ellipsis);
        }
    }

    // Re-initialize Lucide icons for new buttons
    lucide.createIcons();
}

function renderFilteredTable() {
    const tbody = document.querySelector('#employeeTable tbody');
    if (!tbody) return;

    tbody.innerHTML = '';

    if (filteredEmployees.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No employees found</td></tr>';
        return;
    }

    // Calculate pagination
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const paginatedEmployees = filteredEmployees.slice(startIndex, endIndex);

    paginatedEmployees.forEach(emp => {
        const initials = emp.FirstName.charAt(0) + emp.LastName.charAt(0);
        const tr = document.createElement('tr');
        tr.className = 'emp-row';
        tr.innerHTML = `
            <td>
                <div class="emp-cell">
                    <div class="emp-avatar">${initials.toUpperCase()}</div>
                    <div>
                        <div class="emp-name">${emp.FirstName} ${emp.LastName}</div>
                        <div class="emp-dept">${emp.EmployeeCode || emp.EmployeeID}</div>
                    </div>
                </div>
            </td>
            <td style="font-size:13px;color:var(--text-secondary)">${emp.PositionName || '—'}</td>
            <td style="font-size:13px;color:var(--text-secondary)">${emp.DepartmentName || '—'}</td>
            <td><span class="badge badge-${getStatusClass(emp.EmploymentStatus)}">${emp.EmploymentStatus || 'Unknown'}</span></td>
            <td style="font-size:13px;color:var(--text-secondary)">${emp.GradeLevel || '—'}</td>
            <td>
                <button class="btn-review" onclick="viewProfile(${emp.EmployeeID})">
                    <i data-lucide="file-user"></i> View File
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    // Re-initialize Lucide icons for new content
    lucide.createIcons();
}

async function fetchEmployees() {
    try {
        const response = await fetch('backend/be_employeemaster.php?action=fetch_employees');
        const result = await response.json();

        if (result.success) {
            allEmployees = result.data;
            filteredEmployees = allEmployees;
            
            // Update stat cards
            const emps = allEmployees;
            const el = id => document.getElementById(id);
            if (el('statTotal')) el('statTotal').textContent = emps.length;
            if (el('statRegular')) el('statRegular').textContent = emps.filter(e => e.EmploymentStatus === 'Regular').length;
            if (el('statProbationary')) el('statProbationary').textContent = emps.filter(e => e.EmploymentStatus === 'Probationary').length;

            // Apply filters and render
            applyFiltersAndRender();
        } else {
            console.error('Failed to fetch employees:', result.message);
        }
    } catch (error) {
        console.error('Error fetching employees:', error);
    }
}

function renderTable(employees) {
    const tbody = document.querySelector('#employeeTable tbody');
    tbody.innerHTML = '';

    if (employees.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No employees found</td></tr>';
        return;
    }

    employees.forEach(emp => {
        const initials = emp.FirstName.charAt(0) + emp.LastName.charAt(0);
        const tr = document.createElement('tr');
        tr.className = 'emp-row';
        tr.innerHTML = `
            <td>
                <div class="emp-cell">
                    <div class="emp-avatar">${initials.toUpperCase()}</div>
                    <div>
                        <div class="emp-name">${emp.FirstName} ${emp.LastName}</div>
                        <div class="emp-dept">${emp.EmployeeCode || emp.EmployeeID}</div>
                    </div>
                </div>
            </td>
            <td style="font-size:13px;color:var(--text-secondary)">${emp.PositionName || 'â€”'}</td>
            <td style="font-size:13px;color:var(--text-secondary)">${emp.DepartmentName || 'â€”'}</td>
            <td><span class="badge badge-${getStatusClass(emp.EmploymentStatus)}">${emp.EmploymentStatus || 'Unknown'}</span></td>
            <td style="font-size:13px;color:var(--text-secondary)">${emp.GradeLevel || 'â€”'}</td>
            <td>
                <button class="btn-review" onclick="viewProfile(${emp.EmployeeID})">
                    <i data-lucide="file-user"></i> View File
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
    lucide.createIcons();
}

async function viewProfile(id) {
    try {
        const response = await fetch(`backend/be_employeemaster.php?action=get_employee_details&id=${id}`);
        const result = await response.json();

        if (result.success) {
            renderResumeModal(result.data);
            const modal = document.getElementById('employeeModal');
            // Restore full-width for the view modal
            const dlg = modal.querySelector('.modal-dialog');
            if (dlg) dlg.classList.remove('ep-edit-dialog');
            modal.style.display = 'flex';
            modal.classList.add('show');
        } else {
            alert('Failed to load profile: ' + result.message);
        }
    } catch (error) {
        console.error('Error fetching profile:', error);
        alert('An error occurred while loading the profile.');
    }
}

function renderResumeModal(data) {
    const modalBody = document.getElementById('modalBody');
    const modalTitle = document.getElementById('modalTitle');

    // Clear header title content if we want a cleaner look, or keep it
    modalTitle.textContent = ""; // Clearing it because we'll have a close button and header inside

    const initials = data.FirstName.charAt(0) + data.LastName.charAt(0);
    const statusClass = getStatusClass(data.EmploymentStatus);
    const statusColors = { active: '#059669', unverified: '#d97706', inactive: '#dc2626' };
    const statusColor = statusColors[statusClass] || '#6b7280';

    modalBody.style.padding = '0';

    modalBody.innerHTML = `
    <div class="ep-container">

        <!-- Hero Banner -->
        <div class="ep-hero">
            <button class="ep-close" onclick="closeModal()" title="Close">&times;</button>
            <div class="ep-hero-content">
                <div class="ep-avatar-wrap">
                    <div class="ep-avatar">${initials.toUpperCase()}</div>
                    <button class="ep-avatar-edit" onclick="document.getElementById('profileUpload').click()" title="Change photo">
                        <i data-lucide="camera"></i>
                    </button>
                    <input type="file" id="profileUpload" style="display:none" accept="image/*">
                </div>
                <div class="ep-hero-info">
                    <h2 class="ep-name">${data.FirstName} ${data.MiddleName ? data.MiddleName + ' ' : ''}${data.LastName}</h2>
                    <p class="ep-position">${data.PositionName || 'No Position'}</p>
                    <div class="ep-meta">
                        <span class="ep-meta-chip"><i data-lucide="building-2"></i>${data.DepartmentName || 'No Department'}</span>
                        <span class="ep-meta-chip"><i data-lucide="hash"></i>${data.EmployeeCode || data.EmployeeID}</span>
                        <span class="ep-status-badge" style="background:${statusColor}20;color:${statusColor};border:1px solid ${statusColor}40">
                          <span class="ep-status-dot" style="background:${statusColor}"></span>${data.EmploymentStatus || 'â€”'}
                        </span>
                    </div>
                </div>
                <button class="ep-edit-btn" onclick="editEmployee(${data.EmployeeID})">
                    <i data-lucide="pencil"></i> Edit Profile
                </button>
            </div>
        </div>

        <!-- Quick Stats Bar -->
        <div class="ep-stats-bar">
            <div class="ep-stat">
                <i data-lucide="calendar"></i>
                <div><span class="ep-stat-val">${data.HiringDate ? new Date(data.HiringDate).toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' }) : 'â€”'}</span><span class="ep-stat-lbl">Date Hired</span></div>
            </div>
            <div class="ep-stat-divider"></div>
            <div class="ep-stat">
                <i data-lucide="layers"></i>
                <div><span class="ep-stat-val">${data.GradeLevel || 'â€”'}</span><span class="ep-stat-lbl">Salary Grade</span></div>
            </div>
            <div class="ep-stat-divider"></div>
            <div class="ep-stat">
                <i data-lucide="mail"></i>
                <div><span class="ep-stat-val" style="font-size:12px">${data.WorkEmail || 'â€”'}</span><span class="ep-stat-lbl">Work Email</span></div>
            </div>
            <div class="ep-stat-divider"></div>
            <div class="ep-stat">
                <i data-lucide="phone"></i>
                <div><span class="ep-stat-val">${data.PhoneNumber || 'â€”'}</span><span class="ep-stat-lbl">Phone</span></div>
            </div>
        </div>

        <!-- Section Grid -->
        <div class="ep-body">

            <!-- Personal Information -->
            <div class="ep-section">
                <div class="ep-section-hdr ep-hdr-blue"><i data-lucide="user"></i> Personal Information</div>
                <div class="ep-fields">
                    <div class="ep-field"><label>Date of Birth</label><span>${data.DateOfBirth || 'â€”'}</span></div>
                    <div class="ep-field"><label>Gender</label><span>${data.Gender || 'â€”'}</span></div>
                    <div class="ep-field"><label>Personal Email</label><span>${data.PersonalEmail || 'â€”'}</span></div>
                    <div class="ep-field full"><label>Permanent Address</label><span>${data.PermanentAddress || 'â€”'}</span></div>
                </div>
            </div>

            <!-- Government Numbers -->
            <div class="ep-section">
                <div class="ep-section-hdr ep-hdr-purple"><i data-lucide="landmark"></i> Government Numbers</div>
                <div class="ep-fields">
                    <div class="ep-field"><label>TIN</label><span>${data.TINNumber || 'â€”'}</span></div>
                    <div class="ep-field"><label>SSS</label><span>${data.SSSNumber || 'â€”'}</span></div>
                    <div class="ep-field"><label>PhilHealth</label><span>${data.PhilHealthNumber || 'â€”'}</span></div>
                    <div class="ep-field"><label>Pag-IBIG</label><span>${data.PagIBIGNumber || 'â€”'}</span></div>
                </div>
            </div>

            <!-- Bank & Compensation -->
            <div class="ep-section">
                <div class="ep-section-hdr ep-hdr-green"><i data-lucide="credit-card"></i> Bank & Compensation</div>
                <div class="ep-fields">
                    <div class="ep-field"><label>Bank Name</label><span style="color:var(--brand-green);font-weight:600">BDO</span></div>
                    <div class="ep-field"><label>Account Number</label><span>${data.BankAccountNumber || 'â€”'}</span></div>
                    <div class="ep-field"><label>Account Type</label><span style="color:var(--brand-green);font-weight:600">Payroll</span></div>
                    <div class="ep-field"><label>Base Salary</label><span>${(data.BaseSalary !== null && data.BaseSalary !== undefined) ? formatCurrency(data.BaseSalary) : 'â€”'}</span></div>
                    <div class="ep-field full"><label>Salary Range</label><span>${data.MinSalary ? formatCurrency(data.MinSalary) + ' â€“ ' + formatCurrency(data.MaxSalary) : 'â€”'}</span></div>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="ep-section">
                <div class="ep-section-hdr ep-hdr-red"><i data-lucide="heart-pulse"></i> Emergency Contact</div>
                <div class="ep-fields">
                    <div class="ep-field"><label>Contact Name</label><span>${data.ContactName || 'â€”'}</span></div>
                    <div class="ep-field"><label>Relationship</label><span>${data.Relationship || 'â€”'}</span></div>
                    <div class="ep-field full"><label>Phone</label><span>${data.EmergencyPhone || 'â€”'}</span></div>
                </div>
            </div>

        </div>
    </div>
    `;
    lucide.createIcons();
}

function getStatusClass(status) {
    if (!status) return 'inactive'; // Default
    switch (status.toLowerCase()) {
        case 'regular': return 'active';
        case 'probationary': return 'unverified';
        case 'resigned': return 'inactive';
        case 'terminated': return 'inactive';
        default: return 'active';
    }
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(amount);
}

// Store current employee data for edit reference if needed
let currentEmployeeData = null;

async function editEmployee(id) {
    try {
        // Switch modal to compact edit width
        const dlg = document.querySelector('#employeeModal .modal-dialog');
        if (dlg) dlg.classList.add('ep-edit-dialog');

        // Reuse get_employee_details to fetch fresh data
        const response = await fetch(`backend/be_employeemaster.php?action=get_employee_details&id=${id}`);
        const result = await response.json();

        if (result.success) {
            currentEmployeeData = result.data;
            renderEditForm(result.data);
        } else {
            alert('Failed to load employee data for editing.');
        }
    } catch (error) {
        console.error('Error fetching data for edit:', error);
        alert('An error occurred.');
    }
}

function renderEditForm(data) {
    const modalBody = document.getElementById('modalBody');
    const initials = data.FirstName.charAt(0) + data.LastName.charAt(0);
    const statusClass = getStatusClass(data.EmploymentStatus);
    const statusColors = { active: '#059669', unverified: '#d97706', inactive: '#dc2626' };
    const statusColor = statusColors[statusClass] || '#6b7280';

    modalBody.style.padding = '0';

    modalBody.innerHTML = `
    <div class="ep-container ep-edit">

        <!-- Hero Banner â€” edit variant -->
        <div class="ep-hero">
            <button class="ep-close" onclick="closeModal()" title="Close">&times;</button>
            <div class="ep-hero-content">
                <div class="ep-avatar-wrap">
                    <div class="ep-avatar">${initials.toUpperCase()}</div>
                </div>
                <div class="ep-hero-info">
                    <h2 class="ep-name">Edit Profile</h2>
                    <p class="ep-position">${data.FirstName} ${data.LastName}</p>
                    <div class="ep-meta">
                        <span class="ep-meta-chip"><i data-lucide="building-2"></i>${data.DepartmentName || 'No Department'}</span>
                        <span class="ep-meta-chip"><i data-lucide="hash"></i>${data.EmployeeCode || data.EmployeeID}</span>
                        <span class="ep-status-badge" style="background:${statusColor}20;color:${statusColor};border:1px solid ${statusColor}40">
                          <span class="ep-status-dot" style="background:${statusColor}"></span>${data.EmploymentStatus || 'â€”'}
                        </span>
                    </div>
                </div>
                <button class="ep-edit-btn" onclick="viewProfile(${data.EmployeeID})">
                    <i data-lucide="arrow-left"></i> Back to Profile
                </button>
            </div>
        </div>

        <!-- Edit Form Body -->
        <div class="ep-body">
            <form id="editEmployeeForm" onsubmit="submitEditForm(event)" style="display:contents">
                <input type="hidden" name="EmployeeID"   value="${data.EmployeeID}">
                <input type="hidden" name="EmploymentID" value="${data.EmploymentID || ''}">

                <!-- Personal Information -->
                <div class="ep-section">
                    <div class="ep-section-hdr ep-hdr-blue"><i data-lucide="user"></i> Personal Information</div>
                    <div class="ep-fields">
                        <div class="ep-field"><label>First Name</label><input type="text"  name="FirstName"        class="ep-input" value="${data.FirstName || ''}" required></div>
                        <div class="ep-field"><label>Last Name</label> <input type="text"  name="LastName"         class="ep-input" value="${data.LastName || ''}" required></div>
                        <div class="ep-field"><label>Middle Name</label><input type="text" name="MiddleName"       class="ep-input" value="${data.MiddleName || ''}"></div>
                        <div class="ep-field"><label>Date of Birth</label><input type="date" name="DateOfBirth"   class="ep-input" value="${data.DateOfBirth || ''}"></div>
                        <div class="ep-field"><label>Gender</label>
                            <select name="Gender" class="ep-input">
                                <option value="Male"   ${data.Gender === 'Male' ? 'selected' : ''}>Male</option>
                                <option value="Female" ${data.Gender === 'Female' ? 'selected' : ''}>Female</option>
                            </select>
                        </div>
                        <div class="ep-field"><label>Personal Email</label><input type="email" name="PersonalEmail" class="ep-input" value="${data.PersonalEmail || ''}"></div>
                        <div class="ep-field full"><label>Permanent Address</label><input type="text" name="PermanentAddress" class="ep-input" value="${data.PermanentAddress || ''}"></div>
                    </div>
                </div>

                <!-- Government Numbers -->
                <div class="ep-section">
                    <div class="ep-section-hdr ep-hdr-purple"><i data-lucide="landmark"></i> Government Numbers</div>
                    <div class="ep-fields">
                        <div class="ep-field"><label>TIN</label>      <input type="text" name="TINNumber"       class="ep-input" value="${data.TINNumber || ''}"></div>
                        <div class="ep-field"><label>SSS</label>      <input type="text" name="SSSNumber"       class="ep-input" value="${data.SSSNumber || ''}"></div>
                        <div class="ep-field"><label>PhilHealth</label><input type="text" name="PhilHealthNumber" class="ep-input" value="${data.PhilHealthNumber || ''}"></div>
                        <div class="ep-field"><label>Pag-IBIG</label> <input type="text" name="PagIBIGNumber"   class="ep-input" value="${data.PagIBIGNumber || ''}"></div>
                    </div>
                </div>

                <!-- Employment Information -->
                <div class="ep-section">
                    <div class="ep-section-hdr ep-hdr-indigo"><i data-lucide="briefcase"></i> Employment Information</div>
                    <div class="ep-fields">
                        <div class="ep-field">
                            <label>Employment Status</label>
                            <select name="EmploymentStatus" class="ep-input">
                                <option value="Regular"      ${data.EmploymentStatus === 'Regular' ? 'selected' : ''}>Regular</option>
                                <option value="Probationary" ${data.EmploymentStatus === 'Probationary' ? 'selected' : ''}>Probationary</option>
                                <option value="Resigned"     ${data.EmploymentStatus === 'Resigned' ? 'selected' : ''}>Resigned</option>
                                <option value="Terminated"   ${data.EmploymentStatus === 'Terminated' ? 'selected' : ''}>Terminated</option>
                            </select>
                        </div>
                        <div class="ep-field"><label>Date Hired</label>  <input type="date"  name="HiringDate" class="ep-input" value="${data.HiringDate || ''}"></div>
                        <div class="ep-field"><label>Work Email</label>  <input type="email" name="WorkEmail"  class="ep-input" value="${data.WorkEmail || ''}"></div>
                        <div class="ep-field"><label>Phone (Work)</label><input type="text"  name="PhoneNumber" class="ep-input" value="${data.PhoneNumber || ''}"></div>
                    </div>
                </div>

                <!-- Bank & Compensation -->
                <div class="ep-section">
                    <div class="ep-section-hdr ep-hdr-green"><i data-lucide="credit-card"></i> Bank & Compensation</div>
                    <div class="ep-fields">
                        <div class="ep-field"><label>Bank Name</label>      <input type="text" name="BankName"      class="ep-input" value="BDO" readonly style="background:var(--background-alt);font-weight:600;color:var(--brand-green)"></div>
                        <div class="ep-field"><label>Account Number</label> <input type="text" name="BankAccountNumber" class="ep-input" value="${data.BankAccountNumber || ''}"></div>
                        <div class="ep-field"><label>Account Type</label>
                            <input type="text" name="AccountType" class="ep-input" value="Payroll" readonly style="background:var(--background-alt);font-weight:600;color:var(--brand-green)">
                        </div>
                        <div class="ep-field"><label>Base Salary</label>    <input type="number" name="BaseSalary" class="ep-input" value="${data.BaseSalary || 0}"></div>
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="ep-section">
                    <div class="ep-section-hdr ep-hdr-red"><i data-lucide="heart-pulse"></i> Emergency Contact</div>
                    <div class="ep-fields">
                        <div class="ep-field"><label>Contact Name</label>  <input type="text" name="ContactName"    class="ep-input" value="${data.ContactName || ''}"></div>
                        <div class="ep-field"><label>Relationship</label>  <input type="text" name="Relationship"   class="ep-input" value="${data.Relationship || ''}"></div>
                        <div class="ep-field full"><label>Phone</label>    <input type="text" name="EmergencyPhone" class="ep-input" value="${data.EmergencyPhone || ''}"></div>
                    </div>
                </div>

            </form>
        </div>

        <!-- Sticky Save Bar -->
        <div class="ep-save-bar">
            <span class="ep-save-hint"><i data-lucide="info"></i> All changes are saved immediately to the employee record.</span>
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

    // Append action
    formData.append('action', 'update_employee');

    try {
        const response = await fetch('backend/be_employeemaster.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            closeModal(); // Hide modal first
            Swal.fire({
                title: 'Success!',
                text: 'Employee updated successfully!',
                icon: 'success',
                confirmButtonColor: '#2ca078'
            }).then(() => {
                fetchEmployees(); // Refresh table
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: result.message || 'Error updating employee',
                icon: 'error',
                confirmButtonColor: '#d33'
            });
        }
    } catch (error) {
        console.error('Error updating employee:', error);
        Swal.fire({
            title: 'Error!',
            text: 'An error occurred while saving.',
            icon: 'error',
            confirmButtonColor: '#d33'
        });
    }
}

async function editEmployee(id) {
    try {
        const dlg = document.querySelector('#employeeModal .modal-dialog');
        if (dlg) dlg.classList.add('ep-edit-dialog');

        // Reuse get_employee_details to fetch fresh data
        const response = await fetch(`backend/be_employeemaster.php?action=get_employee_details&id=${id}`);
        const result = await response.json();

        if (result.success) {
            currentEmployeeData = result.data;
            renderEditForm(result.data);
        } else {
            alert('Failed to load employee data for editing.');
        }
    } catch (error) {
        console.error('Error fetching data for edit:', error);
        alert('An error occurred.');
    }
}

function renderEditForm(data) {
    const modalBody = document.getElementById('modalBody');
    const initials = data.FirstName.charAt(0) + data.LastName.charAt(0);
    const statusClass = getStatusClass(data.EmploymentStatus);
    const statusColors = { active: '#059669', unverified: '#d97706', inactive: '#dc2626' };
    const statusColor = statusColors[statusClass] || '#6b7280';

    modalBody.style.padding = '0';

    modalBody.innerHTML = `
    <div class="ep-container ep-edit">

        <!-- Hero Banner â€” edit variant -->
        <div class="ep-hero">
            <button class="ep-close" onclick="closeModal()" title="Close">&times;</button>
            <div class="ep-hero-content">
                <div class="ep-avatar-wrap">
                    <div class="ep-avatar">${initials.toUpperCase()}</div>
                </div>
                <div class="ep-hero-info">
                    <h2 class="ep-name">Edit Profile</h2>
                    <p class="ep-position">${data.FirstName} ${data.LastName}</p>
                    <div class="ep-meta">
                        <span class="ep-meta-chip"><i data-lucide="building-2"></i>${data.DepartmentName || 'No Department'}</span>
                        <span class="ep-meta-chip"><i data-lucide="hash"></i>${data.EmployeeCode || data.EmployeeID}</span>
                        <span class="ep-status-badge" style="background:${statusColor}20;color:${statusColor};border:1px solid ${statusColor}40">
                          <span class="ep-status-dot" style="background:${statusColor}"></span>${data.EmploymentStatus || 'â€”'}
                        </span>
                    </div>
                </div>
                <button class="ep-edit-btn" onclick="viewProfile(${data.EmployeeID})">
                    <i data-lucide="arrow-left"></i> Back to Profile
                </button>
            </div>
        </div>

        <!-- Edit Form Body -->
        <div class="ep-body">
            <form id="editEmployeeForm" onsubmit="submitEditForm(event)" style="display:contents">
                <input type="hidden" name="EmployeeID"   value="${data.EmployeeID}">
                <input type="hidden" name="EmploymentID" value="${data.EmploymentID || ''}">

                <!-- Personal Information -->
                <div class="ep-section">
                    <div class="ep-section-hdr ep-hdr-blue"><i data-lucide="user"></i> Personal Information</div>
                    <div class="ep-fields">
                        <div class="ep-field"><label>First Name</label><input type="text"  name="FirstName"        class="ep-input" value="${data.FirstName || ''}" required></div>
                        <div class="ep-field"><label>Last Name</label> <input type="text"  name="LastName"         class="ep-input" value="${data.LastName || ''}" required></div>
                        <div class="ep-field"><label>Middle Name</label><input type="text" name="MiddleName"       class="ep-input" value="${data.MiddleName || ''}"></div>
                        <div class="ep-field"><label>Date of Birth</label><input type="date" name="DateOfBirth"   class="ep-input" value="${data.DateOfBirth || ''}"></div>
                        <div class="ep-field"><label>Gender</label>
                            <select name="Gender" class="ep-input">
                                <option value="Male"   ${data.Gender === 'Male' ? 'selected' : ''}>Male</option>
                                <option value="Female" ${data.Gender === 'Female' ? 'selected' : ''}>Female</option>
                            </select>
                        </div>
                        <div class="ep-field"><label>Personal Email</label><input type="email" name="PersonalEmail" class="ep-input" value="${data.PersonalEmail || ''}"></div>
                        <div class="ep-field full"><label>Permanent Address</label><input type="text" name="PermanentAddress" class="ep-input" value="${data.PermanentAddress || ''}"></div>
                    </div>
                </div>

                <!-- Government Numbers -->
                <div class="ep-section">
                    <div class="ep-section-hdr ep-hdr-purple"><i data-lucide="landmark"></i> Government Numbers</div>
                    <div class="ep-fields">
                        <div class="ep-field"><label>TIN</label>      <input type="text" name="TINNumber"       class="ep-input" value="${data.TINNumber || ''}"></div>
                        <div class="ep-field"><label>SSS</label>      <input type="text" name="SSSNumber"       class="ep-input" value="${data.SSSNumber || ''}"></div>
                        <div class="ep-field"><label>PhilHealth</label><input type="text" name="PhilHealthNumber" class="ep-input" value="${data.PhilHealthNumber || ''}"></div>
                        <div class="ep-field"><label>Pag-IBIG</label> <input type="text" name="PagIBIGNumber"   class="ep-input" value="${data.PagIBIGNumber || ''}"></div>
                    </div>
                </div>

                <!-- Employment Information -->
                <div class="ep-section">
                    <div class="ep-section-hdr ep-hdr-indigo"><i data-lucide="briefcase"></i> Employment Information</div>
                    <div class="ep-fields">
                        <div class="ep-field">
                            <label>Employment Status</label>
                            <select name="EmploymentStatus" class="ep-input">
                                <option value="Regular"      ${data.EmploymentStatus === 'Regular' ? 'selected' : ''}>Regular</option>
                                <option value="Probationary" ${data.EmploymentStatus === 'Probationary' ? 'selected' : ''}>Probationary</option>
                                <option value="Resigned"     ${data.EmploymentStatus === 'Resigned' ? 'selected' : ''}>Resigned</option>
                                <option value="Terminated"   ${data.EmploymentStatus === 'Terminated' ? 'selected' : ''}>Terminated</option>
                            </select>
                        </div>
                        <div class="ep-field"><label>Date Hired</label>  <input type="date"  name="HiringDate" class="ep-input" value="${data.HiringDate || ''}"></div>
                        <div class="ep-field"><label>Work Email</label>  <input type="email" name="WorkEmail"  class="ep-input" value="${data.WorkEmail || ''}"></div>
                        <div class="ep-field"><label>Phone (Work)</label><input type="text"  name="PhoneNumber" class="ep-input" value="${data.PhoneNumber || ''}"></div>
                    </div>
                </div>

                <!-- Bank & Compensation -->
                <div class="ep-section">
                    <div class="ep-section-hdr ep-hdr-green"><i data-lucide="credit-card"></i> Bank & Compensation</div>
                    <div class="ep-fields">
                        <div class="ep-field"><label>Bank Name</label>      <input type="text" name="BankName"      class="ep-input" value="BDO" readonly style="background:var(--background-alt);font-weight:600;color:var(--brand-green)"></div>
                        <div class="ep-field"><label>Account Number</label> <input type="text" name="BankAccountNumber" class="ep-input" value="${data.BankAccountNumber || ''}"></div>
                        <div class="ep-field"><label>Account Type</label>
                            <input type="text" name="AccountType" class="ep-input" value="Payroll" readonly style="background:var(--background-alt);font-weight:600;color:var(--brand-green)">
                        </div>
                        <div class="ep-field"><label>Base Salary</label>    <input type="number" name="BaseSalary" class="ep-input" value="${data.BaseSalary || 0}"></div>
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="ep-section">
                    <div class="ep-section-hdr ep-hdr-red"><i data-lucide="heart-pulse"></i> Emergency Contact</div>
                    <div class="ep-fields">
                        <div class="ep-field"><label>Contact Name</label>  <input type="text" name="ContactName"    class="ep-input" value="${data.ContactName || ''}"></div>
                        <div class="ep-field"><label>Relationship</label>  <input type="text" name="Relationship"   class="ep-input" value="${data.Relationship || ''}"></div>
                        <div class="ep-field full"><label>Phone</label>    <input type="text" name="EmergencyPhone" class="ep-input" value="${data.EmergencyPhone || ''}"></div>
                    </div>
                </div>

            </form>
        </div>

        <!-- Sticky Save Bar -->
        <div class="ep-save-bar">
            <span class="ep-save-hint"><i data-lucide="info"></i> All changes are saved immediately to the employee record.</span>
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

    // Append action
    formData.append('action', 'update_employee');

    try {
        const response = await fetch('backend/be_employeemaster.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            closeModal(); // Hide modal first
            Swal.fire({
                title: 'Success!',
                text: 'Employee updated successfully!',
                icon: 'success',
                confirmButtonColor: '#2ca078'
            }).then(() => {
                fetchEmployees(); // Refresh table
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: result.message || 'Error updating employee',
                icon: 'error',
                confirmButtonColor: '#d33'
            });
        }
    } catch (error) {
        console.error('Error updating employee:', error);
        Swal.fire({
            title: 'Error!',
            text: 'An error occurred while saving.',
            icon: 'error',
            confirmButtonColor: '#d33'
        });
    }
}

function openAddEmployeeModal() {
    alert('Add Employee Modal - To Be Implemented');
}

function closeModal() {
    const modal = document.getElementById('employeeModal');
    if (modal) {
        modal.style.display = 'none';
        modal.classList.remove('show');
    }
}

// Redundant UI logic removed (handled by admin_common.js)
