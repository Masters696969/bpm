document.addEventListener('DOMContentLoaded', () => {
    fetchEmployees();
    lucide.createIcons();
});

async function fetchEmployees() {
    try {
        const response = await fetch('employee_action.php?action=fetch_employees');
        const result = await response.json();

        if (result.success) {
            renderTable(result.data);
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
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <div class="user-info">
                    <div class="user-avatar-sm">${emp.FirstName.charAt(0)}${emp.LastName.charAt(0)}</div>
                    <div>
                        <div class="font-bold">${emp.FirstName} ${emp.LastName}</div>
                    </div>
                </div>
            </td>
            <td>${emp.PositionName || '-'}</td>
            <td>${emp.DepartmentName || '-'}</td>
            <td><span class="badge badge-${getStatusClass(emp.EmploymentStatus)}">${emp.EmploymentStatus || 'Unknown'}</span></td>
            <td>${emp.GradeLevel || '-'}</td>
            <td>
                <button class="btn btn-sm btn-file" onclick="viewProfile(${emp.EmployeeID})">
                    <i data-lucide="file-user"></i>
                    Employee File
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
    lucide.createIcons();
}

async function viewProfile(id) {
    try {
        const response = await fetch(`employee_action.php?action=get_employee_details&id=${id}`);
        const result = await response.json();

        if (result.success) {
            renderResumeModal(result.data);
            const modal = document.getElementById('employeeModal');
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

    modalBody.innerHTML = `
        <div class="resume-container">
        <div class="resume-container">
            <div class="resume-header">
                <div class="header-content">
                    <div class="profile-photo-wrapper" onclick="document.getElementById('profileUpload').click()">
                        <div class="profile-photo">
                            ${data.IDPicture ? `<img src="${data.IDPicture}" alt="Profile">` : `<div class="avatar-placeholder">${data.FirstName.charAt(0)}${data.LastName.charAt(0)}</div>`}
                        </div>
                        <div class="profile-edit-overlay">
                            <i data-lucide="camera"></i>
                        </div>
                        <input type="file" id="profileUpload" style="display: none;" accept="image/*" onchange="alert('Upload functionality to be implemented')">
                    </div>
                    <div class="header-text">
                        <h2>${data.FirstName} ${data.MiddleName ? data.MiddleName + ' ' : ''}${data.LastName}</h2>
                        <p class="position">${data.PositionName || 'No Position'}</p>
                        <p class="department"><i data-lucide="building-2"></i> ${data.DepartmentName || 'No Department'}</p>
                    </div>
                </div>
                <button class="btn btn-sm btn-edit" onclick="editEmployee(${data.EmployeeID})">
                    <i data-lucide="pencil"></i> Edit Profile
                </button>
            </div>

            <div class="resume-grid">
                <!-- Personal Information -->
                <div class="resume-section">
                    <h3><i data-lucide="user"></i> Personal Information</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Date of Birth</label>
                            <span>${data.DateOfBirth || '-'}</span>
                        </div>
                        <div class="info-item">
                            <label>Gender</label>
                            <span>${data.Gender || '-'}</span>
                        </div>
                        <div class="info-item">
                            <label>Phone</label>
                            <span>${data.PhoneNumber || '-'}</span>
                        </div>
                        <div class="info-item">
                            <label>Personal Email</label>
                            <span>${data.PersonalEmail || '-'}</span>
                        </div>
                        <div class="info-item full-width">
                            <label>Permanent Address</label>
                            <span>${data.PermanentAddress || '-'}</span>
                        </div>
                    </div>
                </div>

                <!-- Employment Details -->
                <div class="resume-section">
                    <h3><i data-lucide="briefcase"></i> Employment Details</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Employee ID</label>
                            <span>${data.EmployeeID}</span>
                        </div>
                         <div class="info-item">
                            <label>Employment Status</label>
                            <span class="badge badge-${getStatusClass(data.EmploymentStatus)}">${data.EmploymentStatus || '-'}</span>
                        </div>
                        <div class="info-item">
                            <label>Date Hired</label>
                            <span>${data.HiringDate || '-'}</span>
                        </div>
                        <div class="info-item">
                            <label>Work Email</label>
                            <span>${data.WorkEmail || '-'}</span>
                        </div>
                         <div class="info-item full-width">
                            <label>Digital Resume</label>
                            <span>${data.DigitalResume ? `<a href="${data.DigitalResume}" target="_blank" class="file-link"><i data-lucide="file-text"></i> View Resume</a>` : '<span class="text-muted">No resume uploaded</span>'}</span>
                        </div>
                    </div>
                </div>

                <!-- Compensation & Benefits -->
                <div class="resume-section">
                    <h3><i data-lucide="wallet"></i> Compensation & Benefits</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Salary Grade</label>
                            <span>${data.GradeLevel || '-'} (${data.MinSalary ? formatCurrency(data.MinSalary) : ''} - ${data.MaxSalary ? formatCurrency(data.MaxSalary) : ''})</span>
                        </div>
                        <div class="info-item">
                            <label>Bank Name</label>
                            <span>${data.BankName || '-'}</span>
                        </div>
                        <div class="info-item">
                            <label>Account Number</label>
                            <span>${data.BankAccountNumber || '-'}</span>
                        </div>
                         <div class="info-item">
                            <label>Account Type</label>
                            <span>${data.AccountType || '-'}</span>
                        </div>
                    </div>
                </div>

                <!-- Government & Tax -->
                <div class="resume-section">
                    <h3><i data-lucide="landmark"></i> Government Numbers</h3>
                    <div class="info-grid">
                         <div class="info-item">
                            <label>TIN</label>
                            <span>${data.TINNumber || '-'}</span>
                        </div>
                         <div class="info-item">
                            <label>SSS</label>
                            <span>${data.SSSNumber || '-'}</span>
                        </div>
                         <div class="info-item">
                            <label>PhilHealth</label>
                            <span>${data.PhilHealthNumber || '-'}</span>
                        </div>
                         <div class="info-item">
                            <label>Pag-IBIG</label>
                            <span>${data.PagIBIGNumber || '-'}</span>
                        </div>
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
        // Reuse get_employee_details to fetch fresh data
        const response = await fetch(`employee_action.php?action=get_employee_details&id=${id}`);
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

    modalBody.innerHTML = `
        <div class="resume-container">
            <div class="resume-header" style="padding: 20px; justify-content: flex-start; gap: 20px;">
                <button class="btn btn-sm btn-file" onclick="viewProfile(${data.EmployeeID})">
                    <i data-lucide="arrow-left"></i> Back
                </button>
                <h2 style="margin: 0; font-size: 20px;">Edit Profile: ${data.FirstName} ${data.LastName}</h2>
            </div>

            <form id="editEmployeeForm" onsubmit="submitEditForm(event)">
                <input type="hidden" name="EmployeeID" value="${data.EmployeeID}">
                <input type="hidden" name="EmploymentID" value="${data.EmploymentID}">
                
                <div class="resume-grid">
                    <!-- Personal Info Edit -->
                    <div class="resume-section">
                        <h3><i data-lucide="user"></i> Personal Information</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>First Name</label>
                                <input type="text" name="FirstName" class="form-control" value="${data.FirstName}" required>
                            </div>
                            <div class="info-item">
                                <label>Last Name</label>
                                <input type="text" name="LastName" class="form-control" value="${data.LastName}" required>
                            </div>
                             <div class="info-item">
                                <label>Middle Name</label>
                                <input type="text" name="MiddleName" class="form-control" value="${data.MiddleName || ''}">
                            </div>
                            <div class="info-item">
                                <label>Date of Birth</label>
                                <input type="date" name="DateOfBirth" class="form-control" value="${data.DateOfBirth || ''}">
                            </div>
                            <div class="info-item">
                                <label>Gender</label>
                                <select name="Gender" class="form-control">
                                    <option value="Male" ${data.Gender === 'Male' ? 'selected' : ''}>Male</option>
                                    <option value="Female" ${data.Gender === 'Female' ? 'selected' : ''}>Female</option>
                                </select>
                            </div>
                            <div class="info-item">
                                <label>Phone</label>
                                <input type="text" name="PhoneNumber" class="form-control" value="${data.PhoneNumber || ''}">
                            </div>
                             <div class="info-item full-width">
                                <label>Personal Email</label>
                                <input type="email" name="PersonalEmail" class="form-control" value="${data.PersonalEmail || ''}">
                            </div>
                            <div class="info-item full-width">
                                <label>Permanent Address</label>
                                <input type="text" name="PermanentAddress" class="form-control" value="${data.PermanentAddress || ''}">
                            </div>
                        </div>
                    </div>

                    <!-- Employment Details Edit -->
                    <div class="resume-section">
                        <h3><i data-lucide="briefcase"></i> Employment Details</h3>
                        <div class="info-grid">
                             <div class="info-item">
                                <label>Date Hired</label>
                                <input type="date" name="HiringDate" class="form-control" value="${data.HiringDate || ''}">
                            </div>
                            <div class="info-item">
                                <label>Work Email</label>
                                <input type="email" name="WorkEmail" class="form-control" value="${data.WorkEmail || ''}">
                            </div>
                             <div class="info-item">
                                <label>Employment Status</label>
                                <select name="EmploymentStatus" class="form-control">
                                    <option value="Regular" ${data.EmploymentStatus === 'Regular' ? 'selected' : ''}>Regular</option>
                                    <option value="Probationary" ${data.EmploymentStatus === 'Probationary' ? 'selected' : ''}>Probationary</option>
                                    <option value="Resigned" ${data.EmploymentStatus === 'Resigned' ? 'selected' : ''}>Resigned</option>
                                    <option value="Terminated" ${data.EmploymentStatus === 'Terminated' ? 'selected' : ''}>Terminated</option>
                                </select>
                            </div>
                        </div>
                    </div>
                
                    <!-- Government Numbers Edit -->
                    <div class="resume-section">
                        <h3><i data-lucide="landmark"></i> Government Numbers</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>TIN</label>
                                <input type="text" name="TINNumber" class="form-control" value="${data.TINNumber || ''}">
                            </div>
                            <div class="info-item">
                                <label>SSS</label>
                                <input type="text" name="SSSNumber" class="form-control" value="${data.SSSNumber || ''}">
                            </div>
                            <div class="info-item">
                                <label>PhilHealth</label>
                                <input type="text" name="PhilHealthNumber" class="form-control" value="${data.PhilHealthNumber || ''}">
                            </div>
                            <div class="info-item">
                                <label>Pag-IBIG</label>
                                <input type="text" name="PagIBIGNumber" class="form-control" value="${data.PagIBIGNumber || ''}">
                            </div>
                        </div>
                    </div>

                    <div class="resume-section" style="justify-content: center; align-items: center;">
                         <button type="submit" class="btn btn-edit" style="width: 100%; justify-content: center; background: var(--brand-green); color: white;">
                            <i data-lucide="save"></i> Save Changes
                        </button>
                    </div>
                </div>
            </form>
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
        const response = await fetch('employee_action.php', {
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
