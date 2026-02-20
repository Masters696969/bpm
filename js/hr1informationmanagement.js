document.addEventListener('DOMContentLoaded', () => {
    fetchMyDetails();
    lucide.createIcons();
    const form = document.getElementById('myInfoForm');
    if (form) {
        form.addEventListener('submit', submitMyInfo);
    }

    // Modal Logic
    const btnRequestEdit = document.getElementById('btnRequestEdit');
    const requestEditModal = document.getElementById('requestEditModal');
    const btnCloseRequestModal = document.getElementById('btnCloseRequestModal');
    const requestEditForm = document.getElementById('requestEditForm');

    if (btnRequestEdit && requestEditModal) {
        console.log("Attaching click listener to Request Edit button");
        btnRequestEdit.addEventListener('click', () => {
            console.log("Request Edit button clicked");
            requestEditModal.classList.remove('hidden');
        });
    } else {
        console.error("Request Edit button or modal not found:", { btnRequestEdit, requestEditModal });
    }

    if (btnCloseRequestModal && requestEditModal) {
        btnCloseRequestModal.addEventListener('click', () => {
            requestEditModal.classList.add('hidden');
        });
    }

    // Close on click outside
    window.addEventListener('click', (e) => {
        if (e.target === requestEditModal) {
            requestEditModal.classList.add('hidden');
        }
    });

    if (requestEditForm) {
        requestEditForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(requestEditForm);
            const requestData = Object.fromEntries(formData.entries());

            try {
                const response = await fetch('employee_action.php?action=request_update', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(requestData)
                });
                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        title: 'Request Sent!',
                        text: 'Your update request has been submitted to HR for approval.',
                        icon: 'success',
                        confirmButtonColor: '#2ca078'
                    });
                    requestEditModal.classList.add('hidden');
                    requestEditForm.reset();
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: result.message || 'Failed to submit request.',
                        icon: 'error',
                        confirmButtonColor: '#d33'
                    });
                }
            } catch (error) {
                console.error('Error submitting request:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while submitting request.',
                    icon: 'error',
                    confirmButtonColor: '#d33'
                });
            }
        });
    }
});

async function fetchMyDetails() {
    try {
        const response = await fetch('employee_action.php?action=get_my_details');
        const text = await response.text();
        let result;
        try {
            result = JSON.parse(text);
        } catch (e) {
            console.error('Invalid JSON:', text);
            Swal.fire({
                title: 'Error!',
                text: 'Server returned invalid data.',
                icon: 'error',
                confirmButtonColor: '#d33'
            });
            return;
        }

        if (result.success) {
            renderMyInfo(result.data);
        } else {
            console.warn('Fetch success=false:', result);
            Swal.fire({
                title: 'Profile Not Found',
                text: result.message || 'Please contact HR to link your employee record.',
                icon: 'warning',
                confirmButtonColor: '#d33'
            });
        }
    } catch (error) {
        console.error('Error fetching details:', error);
        Swal.fire({
            title: 'Error!',
            text: 'An error occurred while loading your profile.',
            icon: 'error',
            confirmButtonColor: '#d33'
        });
    }
}

function renderMyInfo(data) {
    // Populate Header
    const nameEl = document.getElementById('employeeName');
    const posEl = document.getElementById('employeePosition');
    const deptEl = document.getElementById('employeeDepartment');
    const statusEl = document.getElementById('employeeStatus');
    const photoContainer = document.getElementById('profilePhotoContainer');

    if (nameEl) nameEl.textContent = `${data.FirstName} ${data.MiddleName ? data.MiddleName + ' ' : ''}${data.LastName}`;
    if (posEl) posEl.textContent = data.PositionName || 'No Position';
    if (deptEl) deptEl.innerHTML = `<i data-lucide="building-2"></i> ${data.DepartmentName || 'No Department'}`;

    if (statusEl) {
        statusEl.textContent = data.EmploymentStatus || 'Active';
        statusEl.className = `badge badge-${getStatusClass(data.EmploymentStatus)}`;
    }

    if (photoContainer) {
        if (data.IDPicture) {
            photoContainer.innerHTML = `<img src="${data.IDPicture}" alt="Profile">`;
        } else {
            photoContainer.innerHTML = `<div class="avatar-placeholder">${data.FirstName.charAt(0)}${data.LastName.charAt(0)}</div>`;
        }
    }

    // Display Employee Code
    const empCodeEl = document.getElementById('employeeCodeDisplay'); // Assuming an element exists or we append it
    if (statusEl) {
        // Create a subtitle for Employee Code if not exists
        let codeContainer = document.querySelector('.employee-code-container');
        if (!codeContainer) {
            const headerText = document.querySelector('.header-text');
            if (headerText) {
                codeContainer = document.createElement('p');
                codeContainer.className = 'employee-code-container';
                codeContainer.style.fontSize = '0.9em';
                codeContainer.style.color = 'var(--text-secondary)';
                codeContainer.style.marginTop = '4px';
                headerText.appendChild(codeContainer);
            }
        }
        if (codeContainer) codeContainer.innerHTML = `<i data-lucide="hash"></i> ${data.EmployeeCode || 'N/A'}`;
    }

    // Populate Form Fields
    setFieldValue('FirstName', data.FirstName);
    setFieldValue('LastName', data.LastName);
    setFieldValue('MiddleName', data.MiddleName);
    setFieldValue('DateOfBirth', data.DateOfBirth);
    setFieldValue('Gender', data.Gender); // Select
    setFieldValue('PermanentAddress', data.PermanentAddress);
    setFieldValue('PhoneNumber', data.PhoneNumber);
    setFieldValue('PersonalEmail', data.PersonalEmail);

    // Populate Read-Only Fields
    setFieldValue('PersonalEmail', data.PersonalEmail);

    // Emergency Contact
    setFieldValue('ContactName', data.ContactName);
    setFieldValue('Relationship', data.Relationship);
    setFieldValue('EmergencyPhone', data.EmergencyPhone);

    // Populate Read-Only Fields
    setFieldValue('EmployeeCode', data.EmployeeCode || data.EmployeeID);
    setFieldValue('HiringDate', data.HiringDate);
    setFieldValue('WorkEmail', data.WorkEmail);
    setFieldValue('GradeLevel', data.GradeLevel);
    setFieldValue('SalaryRange', data.MinSalary ? formatCurrency(data.MinSalary) + ' - ' + formatCurrency(data.MaxSalary) : '-');
    setFieldValue('BankName', data.BankName);
    setFieldValue('BankAccountNumber', data.BankAccountNumber);
    setFieldValue('AccountType', data.AccountType);
    setFieldValue('TINNumber', data.TINNumber);
    setFieldValue('SSSNumber', data.SSSNumber);
    setFieldValue('PhilHealthNumber', data.PhilHealthNumber);
    setFieldValue('PagIBIGNumber', data.PagIBIGNumber);

    // Resume Link
    const resumeContainer = document.getElementById('DigitalResumeContainer');
    if (resumeContainer) {
        resumeContainer.innerHTML = data.DigitalResume
            ? `<a href="${data.DigitalResume}" target="_blank">View Resume</a>`
            : 'No resume uploaded';
    }

    lucide.createIcons();
}

function setFieldValue(id, value) {
    const el = document.getElementById(id);
    if (el) {
        el.value = value || '';
    }
}

async function submitMyInfo(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);

    // Append action
    formData.append('action', 'update_my_details');

    try {
        const response = await fetch('employee_action.php', {
            method: 'POST',
            body: formData
        });

        const text = await response.text();
        let result;
        try {
            result = JSON.parse(text);
        } catch (e) {
            console.error('Invalid JSON:', text);
            Swal.fire({
                title: 'Error!',
                text: 'Server error during save.',
                icon: 'error',
                confirmButtonColor: '#d33'
            });
            return;
        }

        if (result.success) {
            Swal.fire({
                title: 'Success!',
                text: result.message,
                icon: 'success',
                confirmButtonColor: '#2ca078'
            }).then(() => {
                fetchMyDetails(); // Refresh data
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: result.message || 'Error updating information',
                icon: 'error',
                confirmButtonColor: '#d33'
            });
        }
    } catch (error) {
        console.error('Error updating info:', error);
        Swal.fire({
            title: 'Error!',
            text: 'An error occurred while saving.',
            icon: 'error',
            confirmButtonColor: '#d33'
        });
    }
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
