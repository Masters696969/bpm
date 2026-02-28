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

// Sidebar Active Link Logic (Merged)
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

// User Menu Dropdown Logic (Merged)
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
            if (!dd.contains(e.target) && e.target !== btn) {
                dd.classList.remove('umd-open');
            }
        });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') dd.classList.remove('umd-open');
        });
    }

    const signOutLinks = document.querySelectorAll('.umd-sign-out');
    signOutLinks.forEach(link => {
        link.addEventListener('click', async e => {
            e.preventDefault();
            const dest = link.getAttribute('href');
            const result = await Swal.fire({
                title: 'Sign Out?',
                text: 'You are about to sign out of your account.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="swal-icon-logout"></i> Yes, Sign Out',
                cancelButtonText: 'Stay',
                reverseButtons: true,
                customClass: {
                    popup: 'swal-signout-popup',
                    title: 'swal-signout-title',
                }
            });
            if (result.isConfirmed) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Signed Out',
                    text: 'You have been signed out successfully.',
                    timer: 1500,
                    showConfirmButton: false,
                });
                window.location.href = dest;
            }
        });
    });
});
