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
