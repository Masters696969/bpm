document.addEventListener("DOMContentLoaded", () => {
    // Basic setup
    const lucide = window.lucide;
    if (lucide) lucide.createIcons();

    // ==========================================
    // --- Allowance Saving Logic (Optional based on design) ---
    // ==========================================
    document.querySelectorAll(".allowance-val-input").forEach(input => {
        input.addEventListener("change", () => {
            const gradeId = input.getAttribute("data-grade");
            const typeId = input.getAttribute("data-type");
            const amount = input.value;

            input.style.opacity = '0.5';

            const params = new URLSearchParams();
            params.append('grade_id', gradeId);
            params.append('type_id', typeId);
            params.append('amount', amount);

            fetch('save_allowance.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: params
            })
                .then(res => res.json())
                .then(data => {
                    input.style.opacity = '1';
                    if (data.success) {
                        console.log('Allowance saved successfully');
                    } else {
                        console.error('Save failed:', data.error);
                    }
                })
                .catch(err => {
                    input.style.opacity = '1';
                    console.error('Fetch error:', err);
                });
        });
    });

    // ==========================================
    // --- Allowance Propose Change Logic (REMOVED) ---
    // ==========================================

    // ==========================================
    // --- Allowance Tracking Logic (REMOVED) ---
    // ==========================================

    // Modal Global Close
    document.addEventListener('click', (e) => {
        if (e.target.closest('.rp-close-modal')) {
            const modal = e.target.closest('.modal');
            if (modal) modal.style.display = 'none';
        }
        if (e.target.classList.contains('modal')) {
            e.target.style.display = 'none';
        }
    });

});
