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

    // 4. Onboarding Modal Logic
    const onboardModal = document.getElementById("onboardModal");
    const onboardForm = document.getElementById("onboardForm");
    const closeButtons = document.querySelectorAll(".close-modal, .close-modal-btn, .btn-secondary");

    document.querySelectorAll(".btn-finalize").forEach(btn => {
        btn.addEventListener("click", async () => {
            const data = btn.dataset;
            document.getElementById("onboardAppId").value = data.id;
            document.getElementById("ppName").innerText = `${data.first} ${data.last}`;
            document.getElementById("ppEmail").innerText = data.email;
            document.getElementById("ppAvatar").innerText = (data.first[0] + data.last[0]).toUpperCase();

            // Populate Verification Preview
            const addrEl = document.getElementById("ppAddress");
            const emerEl = document.getElementById("ppEmergency");
            if (addrEl) addrEl.innerText = data.address || 'Not specified';
            if (emerEl) emerEl.innerText = data.emergency_name ? `${data.emergency_name} (${data.emergency_rel}) - ${data.emergency_phone}` : 'No emergency contact provided';

            // PRE-POPULATE EMPLOYMENT FIELDS (Smart Mapping)
            if (data.pos_id) document.getElementById("onboardPosition").value = data.pos_id;
            if (data.grade_id) document.getElementById("onboardSalaryGrade").value = data.grade_id;
            if (data.salary_type) {
                const salaryTypeSelect = document.querySelector('select[name="salary_type"]');
                if (salaryTypeSelect) salaryTypeSelect.value = data.salary_type;
            }

            // DYNAMIC EMPLOYEE CODE DISPLAY
            const year = new Date().getFullYear();
            const displayCodeEl = document.getElementById("displayEmployeeCode");

            if (displayCodeEl) {
                // Fetch next ID from server or use a placeholder
                try {
                    const response = await fetch('onboard_action.php?get_next_id=1');
                    const resData = await response.json();
                    const nextId = resData.next_id || '1011'; // Fallback
                    const posCode = data.pos_code || 'EMP';
                    displayCodeEl.value = `${posCode}${year}${nextId}`;
                } catch (e) {
                    displayCodeEl.value = `${data.pos_code || 'EMP'}${year}XXXX`;
                }
            }

            onboardModal.style.display = "flex";
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    });

    closeButtons.forEach(btn => {
        btn.addEventListener("click", () => onboardModal.style.display = "none");
    });

    const onboardPosition = document.getElementById("onboardPosition");
    const onboardSalaryGrade = document.getElementById("onboardSalaryGrade");

    if (onboardPosition && onboardSalaryGrade) {
        onboardPosition.addEventListener("change", () => {
            const selectedOption = onboardPosition.options[onboardPosition.selectedIndex];
            const gradeId = selectedOption.getAttribute("data-grade");
            if (gradeId) {
                onboardSalaryGrade.value = gradeId;
            }
        });
    }

    if (onboardForm) {
        onboardForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            const result = await Swal.fire({
                title: 'Confirm Onboarding?',
                text: "This will create the employee record and remove the candidate from this list.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2ca078',
                confirmButtonText: 'Yes, Confirm'
            });

            if (result.isConfirmed) {
                const formData = new FormData(onboardForm);
                try {
                    const response = await fetch('onboard_action.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        await Swal.fire('Success!', data.message, 'success');
                        location.reload();
                    } else {
                        Swal.fire('Error', data.message || 'Onboarding failed', 'error');
                    }
                } catch (error) {
                    Swal.fire('Error', 'An unexpected error occurred.', 'error');
                }
            }
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
