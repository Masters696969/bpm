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

    if(closeModal) closeModal.addEventListener("click", () => modal.style.display = "none");
    if(confirmSync) {
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
