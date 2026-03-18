document.addEventListener('DOMContentLoaded', () => {
    fetchMyDetails();
    lucide.createIcons();
    const form = document.getElementById('myInfoForm');
    if (form && typeof submitMyInfo === 'function') {
        form.addEventListener('submit', submitMyInfo);
    }

    // Modal Logic
    const btnRequestEdit = document.getElementById('btnRequestEdit');
    const requestEditModal = document.getElementById('requestEditModal');
    const closeRequestEditModal = document.getElementById('closeRequestEditModal');
    const requestEditForm = document.getElementById('requestEditForm');

    // Dropzone Elements
    const dropZone = document.getElementById('imDropZone');
    const fileInput = document.getElementById('ProofFile');
    const dropContent = document.getElementById('imDropContent');
    const filePreview = document.getElementById('imFilePreview');

    if (btnRequestEdit) {
        btnRequestEdit.addEventListener('click', () => {
            console.log('Request Edit button clicked');
            resetDropzone();
            requestEditModal.classList.remove('hidden');
        });
    }

    if (closeRequestEditModal) {
        closeRequestEditModal.addEventListener('click', () => {
            requestEditModal.classList.add('hidden');
        });
    }

    // Drag & Drop Logic
    function resetDropzone() {
        if (fileInput) fileInput.value = '';
        if (dropContent) dropContent.style.display = 'flex';
        if (filePreview) {
            filePreview.style.display = 'none';
            filePreview.innerHTML = '';
        }
    }

    function handleFile(file) {
        if (!file) return;

        // Size validation
        if (file.size > 15 * 1024 * 1024) {
            Swal.fire({ icon: 'error', title: 'Too Large', text: 'Maximum file size is 15 MB.', confirmButtonColor: '#d33' });
            return;
        }

        const dt = new DataTransfer();
        dt.items.add(file);
        if (fileInput) fileInput.files = dt.files;

        const sizeKB = (file.size / 1024).toFixed(1);
        const sizeLabel = sizeKB >= 1024 ? (sizeKB / 1024).toFixed(1) + ' MB' : sizeKB + ' KB';

        if (dropContent) dropContent.style.display = 'none';
        if (filePreview) {
            filePreview.style.display = 'flex';
            const escName = file.name.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            filePreview.innerHTML = `
                <i data-lucide="file-check" style="color:var(--brand-green)"></i>
                <span class="im-fp-name">${escName}</span>
                <span class="im-fp-size">${sizeLabel}</span>
                <button type="button" class="im-fp-clear">&#x2715;</button>
            `;
            lucide.createIcons();
            const clearBtn = filePreview.querySelector('.im-fp-clear');
            if (clearBtn) clearBtn.addEventListener('click', resetDropzone);
        }
    }

    if (dropZone && fileInput) {
        dropZone.addEventListener('click', () => fileInput.click());
        dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('im-dz-hover'); });
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('im-dz-hover'));
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('im-dz-hover');
            if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                handleFile(e.dataTransfer.files[0]);
            }
        });
        fileInput.addEventListener('change', () => {
            if (fileInput.files && fileInput.files[0]) {
                handleFile(fileInput.files[0]);
            }
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
            const dataObj = {};
            formData.forEach((value, key) => {
                if (value.trim() !== '') {
                    dataObj[key] = value.trim();
                }
            });

            try {
                const response = await fetch('employee_action.php?action=request_update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(dataObj)
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
    // Populate Hero Banner
    const nameEl = document.getElementById('employeeName');
    const posEl = document.getElementById('employeePosition');
    const deptEl = document.getElementById('employeeDepartment');
    const codeEl = document.getElementById('employeeCode');
    const avatarEl = document.getElementById('avatarPlaceholder');

    const fullName = `${data.FirstName || ''} ${data.MiddleName ? data.MiddleName + ' ' : ''}${data.LastName || ''}`.trim();
    if (nameEl) nameEl.textContent = fullName || 'Unknown Employee';
    if (posEl) posEl.textContent = data.PositionName || 'No Position';
    if (deptEl) deptEl.textContent = data.DepartmentName || 'No Department';
    if (codeEl) codeEl.textContent = data.EmployeeCode || data.EmployeeID || 'N/A';

    // Avatar initials
    if (avatarEl) {
        const initials = `${(data.FirstName || 'U').charAt(0)}${(data.LastName || 'U').charAt(0)}`.toUpperCase();
        avatarEl.textContent = initials;
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

