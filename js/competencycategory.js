document.addEventListener("DOMContentLoaded", () => {
    const lucide = window.lucide;
    const body = document.body;
    const themeToggle = document.getElementById("themeToggle");
    const sidebarToggle = document.getElementById("sidebarToggle");
    const sidebar = document.getElementById("sidebar");
    const mobileMenuBtn = document.getElementById("mobileMenuBtn");

    // 1. Theme Logic
    const savedTheme = localStorage.getItem("theme");
    if (savedTheme === "dark") body.classList.add("dark-mode");

    themeToggle.addEventListener("click", () => {
        body.classList.toggle("dark-mode");
        localStorage.setItem("theme", body.classList.contains("dark-mode") ? "dark" : "light");
    });

    // 2. Sidebar & Mobile Logic
    sidebarToggle.addEventListener("click", () => {
        sidebar.classList.toggle("collapsed");
        localStorage.setItem("sidebarCollapsed", sidebar.classList.contains("collapsed"));
    });

    if (localStorage.getItem("sidebarCollapsed") === "true") sidebar.classList.add("collapsed");

    mobileMenuBtn.addEventListener("click", () => sidebar.classList.toggle("mobile-open"));

    // 3. Submenu Logic
    document.querySelectorAll(".nav-item.has-submenu").forEach((item) => {
        item.addEventListener("click", (e) => {
            const module = item.getAttribute("data-module");
            const submenu = document.getElementById(`submenu-${module}`);
            submenu.classList.toggle("active");
            item.classList.toggle("active");
        });
    });

    // 4. Table Selection & Search Filter
    const selectAll = document.getElementById("selectAll");
    const rowCheckboxes = document.querySelectorAll(".row-checkbox");
    const searchInput = document.getElementById("roleSearch");
    const tableRows = document.querySelectorAll(".role-row-item");

    if (selectAll) {
        selectAll.addEventListener("change", () => {
            rowCheckboxes.forEach(cb => {
                if (cb.closest('tr').style.display !== 'none') {
                    cb.checked = selectAll.checked;
                }
            });
        });
    }

    if (searchInput) {
        searchInput.addEventListener("keyup", () => {
            const query = searchInput.value.toLowerCase();
            tableRows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? "" : "none";
            });
        });
    }

    // 5. Modal Logic
    const modal = document.getElementById("marketModal");
    const marketBtns = document.querySelectorAll(".market-salary-btn");
    const closeModal = document.getElementById("closeModal");
    const confirmSync = document.getElementById("confirmSync");
    let currentRole = "";

    marketBtns.forEach(btn => {
        btn.addEventListener("click", (e) => {
            const row = e.target.closest("tr");
            currentRole = row.querySelector(".client-name").innerText;
            document.getElementById("modalTitle").innerText = `Sync ${currentRole}`;
            modal.style.display = "flex";
        });
    });

    if (closeModal) closeModal.addEventListener("click", () => modal.style.display = "none");
    if (confirmSync) {
        confirmSync.addEventListener("click", () => {
            alert(`Success: ${currentRole} queued for analysis.`);
            modal.style.display = "none";
        });
    }

    if (typeof lucide !== "undefined") lucide.createIcons();
});

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


// Real-time Clock Functionality
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
        hours = hours % 12;
        hours = hours ? hours : 12;
        const formattedHours = hours.toString().padStart(2, '0');

        clockEl.textContent = `${dayName}, ${monthName} ${date}, ${year}, ${formattedHours}:${minutes}:${seconds} ${ampm}`;
    };

    setInterval(updateClock, 1000);
    updateClock();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initClock);
} else {
    initClock();
}
// ---------------------------------------------------------
// Competency Category Management Logic
// ---------------------------------------------------------
document.addEventListener("DOMContentLoaded", () => {
    const addCatBtn = document.getElementById("addCategoryBtn");
    const addModal = document.getElementById("addCategoryModal");
    const closeAddModal = document.getElementById("closeAddModal");
    const cancelAdd = document.getElementById("cancelAdd");
    const addForm = document.getElementById("addCategoryForm");

    const editModal = document.getElementById("editCategoryModal");
    const closeEditModal = document.getElementById("closeEditModal");
    const cancelEdit = document.getElementById("cancelEdit");
    const editForm = document.getElementById("editCategoryForm");

    // Modal Control Functions
    const toggleModal = (modal, show) => {
        if (show) {
            modal.classList.add("active");
            document.body.style.overflow = "hidden";
        } else {
            modal.classList.remove("active");
            document.body.style.overflow = "";
        }
    };

    // Add Category
    if (addCatBtn) {
        addCatBtn.addEventListener("click", () => toggleModal(addModal, true));
    }
    if (closeAddModal) closeAddModal.addEventListener("click", () => toggleModal(addModal, false));
    if (cancelAdd) cancelAdd.addEventListener("click", () => toggleModal(addModal, false));

    if (addForm) {
        addForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            const formData = new FormData(addForm);
            formData.append("action", "add");

            try {
                const response = await fetch("backend/category_action.php", { method: "POST", body: formData });
                const result = await response.json();
                if (result.success) {
                    Swal.fire({ 
                        title: "Success!", 
                        text: result.message, 
                        icon: "success", 
                        timer: 1500, 
                        showConfirmButton: false,
                        target: 'body',
                        customClass: { container: 'swal-on-top' }
                    }).then(() => location.reload());
                } else {
                    Swal.fire({ title: "Error", text: result.message, icon: "error", target: 'body', customClass: { container: 'swal-on-top' } });
                }
            } catch (error) {
                console.error(error);
                Swal.fire({ title: "Error", text: "Server error occurred.", icon: "error", target: 'body', customClass: { container: 'swal-on-top' } });
            }
        });
    }

    // Edit Category
    document.querySelectorAll(".edit-btn-cat").forEach(btn => {
        btn.addEventListener("click", () => {
            document.getElementById("edit_cat_id").value = btn.dataset.id;
            document.getElementById("edit_cat_name").value = btn.dataset.name;
            document.getElementById("edit_cat_subtitle").value = btn.dataset.subtitle;
            toggleModal(editModal, true);
        });
    });

    if (closeEditModal) closeEditModal.addEventListener("click", () => toggleModal(editModal, false));
    if (cancelEdit) cancelEdit.addEventListener("click", () => toggleModal(editModal, false));

    if (editForm) {
        editForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            const formData = new FormData(editForm);
            formData.append("action", "update");

            try {
                const response = await fetch("backend/category_action.php", { method: "POST", body: formData });
                const result = await response.json();
                if (result.success) {
                    Swal.fire({ 
                        title: "Updated!", 
                        text: result.message, 
                        icon: "success", 
                        timer: 1500, 
                        showConfirmButton: false,
                        target: 'body',
                        customClass: { container: 'swal-on-top' }
                    }).then(() => location.reload());
                } else {
                    Swal.fire({ title: "Error", text: result.message, icon: "error", target: 'body', customClass: { container: 'swal-on-top' } });
                }
            } catch (error) {
                console.error(error);
                Swal.fire({ title: "Error", text: "Server error occurred.", icon: "error", target: 'body', customClass: { container: 'swal-on-top' } });
            }
        });
    }

    // Delete Category
    document.querySelectorAll(".delete-btn-cat").forEach(btn => {
        btn.addEventListener("click", () => {
            const id = btn.dataset.id;
            Swal.fire({
                title: "Are you sure?",
                text: "Removing this category cannot be undone.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#ef4444",
                cancelButtonColor: "#6b7280",
                confirmButtonText: "Yes, delete it!",
                target: 'body',
                customClass: { container: 'swal-on-top' }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append("action", "delete");
                    formData.append("id", id);

                    try {
                        const response = await fetch("backend/category_action.php", { method: "POST", body: formData });
                        const data = await response.json();
                        if (data.success) {
                            Swal.fire({ 
                                title: "Deleted!", 
                                text: data.message, 
                                icon: "success", 
                                timer: 1500, 
                                showConfirmButton: false,
                                target: 'body',
                                customClass: { container: 'swal-on-top' }
                            }).then(() => location.reload());
                        } else {
                            Swal.fire({ title: "Delete Failed", text: data.message, icon: "error", target: 'body', customClass: { container: 'swal-on-top' } });
                        }
                    } catch (error) {
                        console.error(error);
                        Swal.fire({ title: "Error", text: "Check console for details.", icon: "error", target: 'body', customClass: { container: 'swal-on-top' } });
                    }
                }
            });
        });
    });

    // Close modals on overlay click
    window.addEventListener("click", (e) => {
        if (e.target.classList.contains("modal-overlay-cat")) {
            toggleModal(e.target, false);
        }
    });

    if (typeof lucide !== "undefined") lucide.createIcons();
});
