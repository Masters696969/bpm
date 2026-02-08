/**
 * User Account Management - v1.0
 * Handles modal, theme toggle, sidebar, and account actions (Edit/Delete)
 * Last Updated: February 8, 2026
 */

// Fallback for SweetAlert2 when blocked by browser tracking prevention
if (typeof window.Swal === 'undefined') {
    window.Swal = {
        fire: (opts) => {
            alert((opts.title || '') + '\n' + (opts.text || ''));
            return Promise.resolve({ isConfirmed: true });
        },
        showLoading: () => {},
        close: () => {}
    };
}

// Fallback for Lucide icons when blocked by browser tracking prevention
if (typeof window.lucide === 'undefined') {
    window.lucide = { createIcons: () => {} };
}

function initUserAccount() {
    const body = document.body;
    const lucide = window.lucide;

    // =====================
    // 1. THEME TOGGLE
    // =====================
    const themeToggle = document.getElementById("themeToggle");
    if (themeToggle) {
        const savedTheme = localStorage.getItem("theme");
        if (savedTheme === "dark") body.classList.add("dark-mode");
        
        themeToggle.addEventListener("click", () => {
            body.classList.toggle("dark-mode");
            localStorage.setItem("theme", body.classList.contains("dark-mode") ? "dark" : "light");
        });
    }

    // =====================
    // 2. SIDEBAR TOGGLE
    // =====================
    const sidebarToggle = document.getElementById("sidebarToggle");
    const sidebar = document.getElementById("sidebar");
    const mobileMenuBtn = document.getElementById("mobileMenuBtn");

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener("click", () => {
            sidebar.classList.toggle("collapsed");
            localStorage.setItem("sidebarCollapsed", sidebar.classList.contains("collapsed"));
        });

        if (localStorage.getItem("sidebarCollapsed") === "true") {
            sidebar.classList.add("collapsed");
        }
    }

    if (mobileMenuBtn && sidebar) {
        mobileMenuBtn.addEventListener("click", () => sidebar.classList.toggle("mobile-open"));
    }

    // =====================
    // 3. SUBMENU TOGGLE
    // =====================
    document.querySelectorAll(".nav-item.has-submenu").forEach((item) => {
        item.addEventListener("click", () => {
            const module = item.getAttribute("data-module");
            const submenu = document.getElementById(`submenu-${module}`);
            if (submenu) {
                submenu.classList.toggle("active");
                item.classList.toggle("active");
            }
        });
    });

    // =====================
    // 4. MODAL MANAGEMENT
    // =====================
    const modal = document.getElementById("addUserModal");
    const addUserBtn = document.getElementById("addUserBtn");
    const closeModalBtn = document.getElementById("closeModalBtn");
    const cancelCreate = document.getElementById("cancelCreate");
    const createUserForm = document.getElementById("createUserForm");

    // Helper to open modal
    const openModal = () => {
        if (!modal || !createUserForm) return;
        createUserForm.reset();
        modal.style.display = "flex";
        modal.setAttribute("aria-hidden", "false");
    };

    // Helper to close modal
    const closeModal = () => {
        if (!modal || !createUserForm) return;
        modal.style.display = "none";
        modal.setAttribute("aria-hidden", "true");
        createUserForm.reset();
    };

    // Add button click
    if (addUserBtn) {
        addUserBtn.addEventListener("click", openModal);
    }

    // Global fallback for inline onclick
    window.openAddAccountModal = openModal;

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

            // Validate passwords match
            if (password !== confirmPassword) {
                await Swal.fire({
                    icon: "error",
                    title: "Password Mismatch",
                    text: "Passwords do not match",
                    confirmButtonColor: "#2ca078"
                });
                return;
            }

            // Validate roles selected
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

            // Show loading
            await Swal.fire({
                title: "Creating Account...",
                text: "Please wait",
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const formData = new FormData();
                formData.append("action", "add_account");
                formData.append("username", username);
                formData.append("email", email);
                formData.append("password", password);
                formData.append("confirm_password", confirmPassword);
                formData.append("account_status", accountStatus);
                roles.forEach(roleId => {
                    formData.append("roles[]", roleId);
                });

                const response = await fetch("account_action.php", {
                    method: "POST",
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    await Swal.fire({
                        icon: "success",
                        title: "Account Created",
                        text: "New account has been created successfully",
                        confirmButtonColor: "#2ca078"
                    });
                    closeModal();
                    location.reload();
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
    window.togglePassword = function(inputId) {
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
    window.editUser = function(accountId) {
        Swal.fire({
            icon: "info",
            title: "Edit Account",
            text: "Edit account functionality coming soon",
            confirmButtonColor: "#2ca078"
        });
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

            const response = await fetch("account_action.php", {
                method: "POST",
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                await Swal.fire({
                    icon: "success",
                    title: "Account Deleted",
                    text: "Account has been deleted successfully",
                    confirmButtonColor: "#2ca078"
                });
                location.reload();
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

    // Table delegated click handler
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

    // Initialize icons
    lucide.createIcons();
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initUserAccount);
} else {
    initUserAccount();
}