document.addEventListener("DOMContentLoaded", () => {
    // 1. Tab Switching Logic
    const tabButtons = document.querySelectorAll(".tab-btn");
    const tabPanels = document.querySelectorAll(".tab-panel");

    tabButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            const target = btn.getAttribute("data-tab");

            // Update buttons
            tabButtons.forEach(b => b.classList.remove("active"));
            btn.classList.add("active");

            // Update panels
            tabPanels.forEach(p => p.classList.remove("active"));
            document.getElementById(target).classList.add("active");

            // Re-create icons for new content
            if (window.lucide) window.lucide.createIcons();
        });
    });

    // 2. Search Logic (Simple Filter)
    const searchInput = document.querySelector('input[type="search"]');
    if (searchInput) {
        searchInput.addEventListener("input", (e) => {
            const term = e.target.value.toLowerCase();
            const rows = document.querySelectorAll(".payroll-table tbody tr");

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(term) ? "" : "none";
            });
        });
    }

    // 3. New Payroll Run Modal (Simulated)
    const runPayrollBtn = document.getElementById("runPayrollBtn");
    if (runPayrollBtn && window.Swal) {
        runPayrollBtn.addEventListener("click", () => {
            Swal.fire({
                title: 'Initialize Payroll Run',
                text: "Select the payroll period for the new run.",
                input: 'select',
                inputOptions: {
                    '1st_half': 'March 1 - March 15 (Semi-Monthly)',
                    '2nd_half': 'March 16 - March 31 (Semi-Monthly)',
                    'monthly': 'March Full Month'
                },
                inputPlaceholder: 'Select a period',
                showCancelButton: true,
                confirmButtonColor: '#2ca078',
                confirmButtonText: 'Initialize Batch',
                background: document.body.classList.contains('dark-mode') ? '#1a1a1a' : '#fff',
                color: document.body.classList.contains('dark-mode') ? '#f9fafb' : '#111827'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Batch Created!',
                        text: 'Payroll processing has been initialized for the selected period.',
                        icon: 'success',
                        confirmButtonColor: '#2ca078'
                    });
                }
            });
        });
    }

    // 4. Handle Empty Content States
    tabPanels.forEach(panel => {
        const table = panel.querySelector('table tbody');
        if (table && table.children.length === 0) {
            // Add empty state if needed
        }
    });

    // Re-fetch Lucide icons
    if (window.lucide) window.lucide.createIcons();
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
