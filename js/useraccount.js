/**
 * User Account Management - v1.0
 * Handles modal, theme toggle, sidebar, and account actions (Edit/Delete)
 * Last Updated: February 8, 2026
 */

function initUserAccount() {

// Fallback for SweetAlert2 when blocked by browser tracking prevention
if (typeof window.Swal === 'undefined') {
    window.Swal = {
        fire: (opts) => {
            alert((opts.title || '') + '\n' + (opts.text || ''));
            return Promise.resolve({ isConfirmed: true });
        },
        showLoading: () => { },
        close: () => { }
    };
}

// Fallback for Lucide icons when blocked by browser tracking prevention
if (typeof window.lucide === 'undefined') {
    window.lucide = { createIcons: () => { } };
}

// Redundant UI logic removed (handled by admin_common.js)

    // =====================
    // 4. MODAL MANAGEMENT
    // =====================
    const modal = document.getElementById("addUserModal");
    const addUserBtn = document.getElementById("addUserBtn");
    const closeModalBtn = document.getElementById("closeModalBtn");
    const cancelCreate = document.getElementById("cancelCreate");
    const createUserForm = document.getElementById("createUserForm");

    // Helper to open modal
    const openModal = (shouldReset = true) => {
        if (!modal || !createUserForm) {
            console.error("Modal or form not found");
            return;
        }
        if (shouldReset) {
            createUserForm.reset();
            document.getElementById("accountId").value = "";
            const empRow = document.getElementById("employeeLinkRow");
            if(empRow) empRow.style.display = "block";
        }
        modal.style.display = "flex";
        modal.classList.add("show");
        modal.setAttribute("aria-hidden", "false");
    };

    // Helper to close modal
    const closeModal = () => {
        if (!modal || !createUserForm) return;
        modal.style.display = "none";
        modal.classList.remove("show");
        modal.setAttribute("aria-hidden", "true");
        createUserForm.reset();
        document.getElementById("accountId").value = "";
    };

    const openAddModal = () => {
        const modalTitle = document.getElementById('modalTitle');
        const submitLabel = document.getElementById('submitBtnLabel');
        if (modalTitle) modalTitle.textContent = 'Add New Account';
        if (submitLabel) submitLabel.textContent = 'Create Account';
        const pwd = document.getElementById("password");
        const cpwd = document.getElementById("confirmPassword");
        if (pwd) pwd.required = true;
        if (cpwd) cpwd.required = true;
        openModal(true);
    };

    // Add button click (single binding)
    if (addUserBtn) {
        addUserBtn.addEventListener("click", (e) => {
            e.preventDefault();
            openAddModal();
        });
    }

    // Global fallback for inline/backup handlers
    window.openAddAccountModal = openAddModal;

    // Close buttons
    if (closeModalBtn) closeModalBtn.addEventListener("click", closeModal);
    if (cancelCreate) cancelCreate.addEventListener("click", closeModal);

    // Close when clicking outside modal
    if (modal) {
        modal.addEventListener("click", (e) => {
            if (e.target === modal) closeModal();
        });
    }

    // =====================
    // 5. FORM SUBMISSION
    // =====================
    if (createUserForm) {
        createUserForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            const username = document.getElementById("username").value.trim();
            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirmPassword").value;
            const rolesSelect = document.getElementById("roles");
            const accountStatus = document.getElementById("accountStatus").value;
            const employeeId = document.getElementById("employeeId") ? document.getElementById("employeeId").value : "";

            const accountId = document.getElementById("accountId").value;
            const isEdit = !!accountId;

            if ((!isEdit || password) && password !== confirmPassword) {
                await Swal.fire({
                    icon: "error",
                    title: "Password Mismatch",
                    text: "Passwords do not match",
                    confirmButtonColor: "#2ca078"
                });
                return;
            }

            const roles = Array.from(rolesSelect.selectedOptions).map(option => option.value);
            if (roles.length === 0) {
                await Swal.fire({
                    icon: "error",
                    title: "Roles Required",
                    text: "Please select at least one role",
                    confirmButtonColor: "#2ca078"
                });
                return;
            }

            Swal.fire({
                title: isEdit ? "Updating Account..." : "Creating Account...",
                text: "Please wait",
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const params = new URLSearchParams();
                const actionType = isEdit ? "update_account" : "add_account";
                params.append("action", actionType);

                if (isEdit) {
                    params.append("account_id", accountId);
                }

                params.append("username", username);
                params.append("email", email);
                if (password) {
                    params.append("password", password);
                    params.append("confirm_password", confirmPassword);
                }
                params.append("account_status", accountStatus);
                if (employeeId) {
                    params.append("employee_id", employeeId);
                }
                roles.forEach(roleId => {
                    params.append("roles[]", roleId);
                });

                const response = await fetch("backend/account_action.php", {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: params
                });

                const result = await response.json();

                if (result.success) {
                    closeModal(); // Close modal first
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: isEdit ? "Account Updated" : "Account Created",
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    await Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: result.message || "Failed to create account",
                        confirmButtonColor: "#2ca078"
                    });
                }
            } catch (error) {
                console.error("Form submission error:", error);
                await Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Something went wrong. Please try again.",
                    confirmButtonColor: "#2ca078"
                });
            }
        });
    }

    // =====================
    // 6. PASSWORD TOGGLE
    // =====================
    window.togglePassword = function (inputId) {
        const input = document.getElementById(inputId);
        if (!input) return;

        const parent = input.parentElement;
        const icon = parent?.querySelector(".eye-icon");

        if (input.type === "password") {
            input.type = "text";
            if (icon) icon.setAttribute("data-lucide", "eye-off");
        } else {
            input.type = "password";
            if (icon) icon.setAttribute("data-lucide", "eye");
        }

        lucide.createIcons();
    };

    // =====================
    // 7. EDIT & DELETE
    // =====================
    window.editUser = async function (accountId) {
        try {
            const response = await fetch(`backend/account_action.php?action=get_account&account_id=${accountId}`);
            const result = await response.json();

            if (result.success) {
                const data = result.data;

                document.getElementById("accountId").value = data.AccountID;
                document.getElementById("username").value = data.Username;
                document.getElementById("email").value = data.Email;
                document.getElementById("accountStatus").value = data.AccountStatus;

                const rolesSelect = document.getElementById("roles");
                Array.from(rolesSelect.options).forEach(option => {
                    option.selected = data.Roles.includes(parseInt(option.value));
                });

                const modalTitle = document.getElementById('modalTitle');
                const submitLabel = document.getElementById('submitBtnLabel');
                if (modalTitle) modalTitle.textContent = 'Edit Account';
                if (submitLabel) submitLabel.textContent = 'Update Account';

                document.getElementById("password").required = false;
                document.getElementById("confirmPassword").required = false;

                const empRow = document.getElementById("employeeLinkRow");
                if(empRow) empRow.style.display = "none";

                openModal(false);
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: result.message || "Failed to fetch account details",
                    confirmButtonColor: "#2ca078"
                });
            }
        } catch (error) {
            console.error("Error fetching account:", error);
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "An error occurred while fetching account details",
                confirmButtonColor: "#2ca078"
            });
        }
    };

    async function performDelete(id, username) {
        const confirmed = await Swal.fire({
            icon: "warning",
            title: "Delete Account",
            text: `Are you sure you want to delete the account "${username}"? This action cannot be undone.`,
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Delete",
            cancelButtonText: "Cancel"
        });

        if (!confirmed.isConfirmed) return;

        try {
            const formData = new FormData();
            formData.append("action", "delete_account");
            formData.append("account_id", id);

            const response = await fetch("backend/account_action.php", {
                method: "POST",
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: "Account Deleted",
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                }).then(() => {
                    location.reload();
                });
            } else {
                await Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: result.message || "Failed to delete account",
                    confirmButtonColor: "#2ca078"
                });
            }
        } catch (error) {
            console.error("Delete error:", error);
            await Swal.fire({
                icon: "error",
                title: "Error",
                text: "Something went wrong. Please try again.",
                confirmButtonColor: "#2ca078"
            });
        }
    }

    const usersTable = document.getElementById("usersTable");
    if (usersTable) {
        usersTable.addEventListener("click", (e) => {
            const editBtn = e.target.closest(".btn-edit");
            if (editBtn) {
                const id = editBtn.getAttribute("data-account-id");
                editUser(parseInt(id, 10));
                return;
            }

            const delBtn = e.target.closest(".btn-delete");
            if (delBtn) {
                const id = delBtn.getAttribute("data-account-id");
                const username = delBtn.getAttribute("data-username");
                performDelete(id, username);
                return;
            }
        });
    }

    lucide.createIcons();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initUserAccount);
} else {
    initUserAccount();
}

// Redundant clock and link logic removed
