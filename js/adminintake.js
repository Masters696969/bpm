/* adminintake.js */
document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.getElementById('intakeTableBody');
    const loadingState = document.getElementById('loadingState');
    const batchModal = document.getElementById('batchReviewModal');
    const modalEmployeeList = document.getElementById('modalBatchEmployeeList');

    let currentBatchData = null;

    // 1. Fetch Pending Dispatches
    async function fetchPendingDispatches() {
        try {
            const response = await fetch('backend/be_dispatch.php?action=fetch_pending_dispatches');
            const responseText = await response.text();

            let result = null;
            try {
                // Find the first '{' and the last '}'
                const firstBrace = responseText.indexOf('{');
                const lastBrace = responseText.lastIndexOf('}');

                if (firstBrace === -1 || lastBrace === -1) {
                    throw new Error("No JSON structure found.");
                }

                const cleanJsonStr = responseText.substring(firstBrace, lastBrace + 1);
                result = JSON.parse(cleanJsonStr);
            } catch (jsonError) {
                console.error("JSON PARSE ERROR:", jsonError, "\nRAW TEXT:\n", responseText);
                Swal.fire('Data Error', 'Invalid data block received. Server output: ' + responseText.substring(0, 50) + '...', 'error');
                return;
            }

            // If we successfully parsed, handle the data
            if (result && result.success) {
                try {
                    updateStats(result.data);
                    renderTable(result.data);
                } catch (renderError) {
                    console.error("RENDER ERROR:", renderError);
                    Swal.fire('Render Error', 'Failed to render table data: ' + renderError.message, 'error');
                }
            } else if (result && !result.success) {
                Swal.fire('Error', result.message, 'error');
            }
        } catch (error) {
            console.error('Fetch Error:', error);
            Swal.fire('Connection Error', 'Failed to connect to the server.', 'error');
        } finally {
            if (loadingState) loadingState.style.display = 'none';
        }
    }

    // Update Stats Cards
    function updateStats(batches) {
        const totalRecords = batches.reduce((sum, b) => sum + parseInt(b.EmployeeCount), 0);
        document.getElementById('statPendingBatches').textContent = batches.length;
        document.getElementById('statTotalRecords').textContent = totalRecords;

        // Update Last Sync (if any batch was processed recently)
        const lastSync = localStorage.getItem('last_intake_sync') || '--:--';
        document.getElementById('statLastSync').textContent = lastSync;
    }

    // 2. Render Table (Batch View - Consolidated)
    function renderTable(batches) {
        if (!tableBody) return;
        tableBody.innerHTML = '';

        if (batches.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 100px 32px; color: var(--text-tertiary);"><div style="display: flex; flex-direction: column; align-items: center; gap: 16px;"><div style="width: 64px; height: 64px; border-radius: 20px; background: var(--background); display: flex; align-items: center; justify-content: center; border: 1px solid var(--border-color);"><i data-lucide="inbox" style="width: 32px; height: 32px; opacity: 0.5;"></i></div><div style="text-align: center;"><p style="font-size: 18px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px;">Intake Queue is Clear</p><p style="font-size: 14px;">All employee master data dispatches have been synchronized.</p></div></div></td></tr>';
            if (window.lucide) window.lucide.createIcons();
            return;
        }

        batches.forEach(b => {
            const dateObj = new Date(b.DispatchDate);
            const timeAgo = getTimeAgo(dateObj);

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <div class="emp-info">
                        <div class="emp-avatar" style="background: linear-gradient(135deg, rgba(44, 160, 120, 0.2), rgba(59, 130, 246, 0.1)); color: var(--brand-green); width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                            ${b.DispatchedBy.charAt(0).toUpperCase()}
                        </div>
                        <div class="emp-details" style="margin-left: 12px;">
                            <span style="font-weight: 700; color: var(--text-primary); display: block; font-size: 15px;">${b.DispatchedBy}</span>
                            <span style="font-size: 11px; color: var(--text-tertiary); text-transform: uppercase; letter-spacing: 0.05em;">Authorized Dispatcher</span>
                        </div>
                    </div>
                </td>
                <td>
                    <span style="font-weight: 500; color: var(--text-secondary);">Human Resource Admin</span>
                </td>
                <td style="text-align: center;">
                    <div style="display: inline-flex; flex-direction: column; align-items: center;">
                        <span style="font-weight: 700; font-size: 16px; color: var(--brand-green);">${b.EmployeeCount}</span>
                        <span style="font-size: 10px; color: var(--text-tertiary); font-weight: 700; text-transform: uppercase;">Records</span>
                    </div>
                </td>
                <td style="text-align: center;">
                    <div style="display: inline-flex; flex-direction: column; align-items: center;">
                        <span style="font-weight: 600; color: var(--text-secondary); font-size: 13px;">${timeAgo}</span>
                        <span style="font-size: 11px; color: var(--text-tertiary);">${dateObj.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
                    </div>
                </td>
                <td style="text-align: center;">
                    <span class="badge-status pending" style="justify-content: center;">
                        <div style="width: 6px; height: 6px; border-radius: 50%; background: #b45309; animation: pulse 2s infinite;"></div>
                        ${b.Status}
                    </span>
                </td>
                <td>
                    <div class="actions-cell" style="justify-content: center;">
                        <button class="btn-view" onclick='reviewBatch("${b.DispatchedBy}")'>
                            <i data-lucide="eye" style="width: 16px;"></i>
                            View
                        </button>
                        <button class="btn-sync" onclick='quickSync("${b.DispatchedBy}")'>
                            <i data-lucide="refresh-cw" style="width: 16px;"></i>
                            Sync
                        </button>
                    </div>
                </td>
            `;
            tableBody.appendChild(tr);
        });

        if (window.lucide) window.lucide.createIcons();
    }

    function getTimeAgo(date) {
        const seconds = Math.floor((new Date() - date) / 1000);
        let interval = seconds / 31536000;
        if (interval > 1) return Math.floor(interval) + " years ago";
        interval = seconds / 2592000;
        if (interval > 1) return Math.floor(interval) + " months ago";
        interval = seconds / 86400;
        if (interval > 1) return Math.floor(interval) + " days ago";
        interval = seconds / 3600;
        if (interval > 1) return Math.floor(interval) + " hours ago";
        interval = seconds / 60;
        if (interval > 1) return Math.floor(interval) + " minutes ago";
        return Math.floor(seconds) + " seconds ago";
    }

    // 3. Review Batch Action
    window.reviewBatch = async (dispatchedBy) => {
        currentBatchData = { dispatchedBy };
        modalEmployeeList.innerHTML = '<tr><td colspan="4" style="text-align:center; padding: 60px;"><div style="display: flex; flex-direction: column; align-items: center; gap: 12px;"><i data-lucide="loader-2" class="animate-spin" style="width: 32px; color: var(--brand-green);"></i><p style="color: var(--text-secondary); font-weight: 500;">Retrieving batch records...</p></div></td></tr>';
        batchModal.style.display = 'flex';
        if (window.lucide) window.lucide.createIcons();

        try {
            const response = await fetch(`backend/be_dispatch.php?action=fetch_batch_employees&dispatched_by=${encodeURIComponent(dispatchedBy)}`);
            const responseText = await response.text();

            try {
                // Find the first '{' and the last '}'
                const firstBrace = responseText.indexOf('{');
                const lastBrace = responseText.lastIndexOf('}');

                if (firstBrace === -1 || lastBrace === -1) {
                    throw new Error("No JSON found in response.");
                }

                const cleanJsonStr = responseText.substring(firstBrace, lastBrace + 1);
                const res = JSON.parse(cleanJsonStr);

                if (res.success) {
                    modalEmployeeList.innerHTML = '';
                    res.data.forEach(emp => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td style="padding: 16px 20px;">
                                <span style="font-weight: 700; color: var(--text-primary);">${emp.FirstName} ${emp.LastName}</span>
                            </td>
                            <td style="padding: 16px 20px; font-family: monospace; font-weight: 600; color: var(--brand-green);">${emp.EmployeeCode || 'NEW_ENTRY'}</td>
                            <td style="padding: 16px 20px;">${emp.DepartmentName || 'N/A'}</td>
                            <td style="padding: 16px 20px;">
                                <span style="padding: 4px 10px; background: rgba(59, 130, 246, 0.1); color: #3b82f6; border-radius: 8px; font-size: 12px; font-weight: 600;">${emp.PositionName || 'N/A'}</span>
                            </td>
                        `;
                        modalEmployeeList.appendChild(tr);
                    });
                    document.getElementById('batchReviewSummary').textContent = `Total of ${res.data.length} records ready for final verification.`;
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            } catch (jsonError) {
                console.error('JSON Parse Error inside ReviewBatch.', jsonError, '\nRaw Server Response:', responseText);
                Swal.fire('Server Error', 'Invalid data format received. Check console.', 'error');
            }
        } catch (error) {
            console.error('Fetch Batch Error:', error);
            Swal.fire('Connection Error', 'Failed to fetch batch details.', 'error');
        }

        if (window.lucide) window.lucide.createIcons();
    };

    window.closeBatchModal = () => {
        batchModal.style.display = 'none';
    };

    // 4. Process Batch (Accept/Reject)
    window.processBatch = async (status) => {
        const remarks = document.getElementById('batchRemarks').value;
        const actionText = status === 'Received' ? 'Sync' : 'Reject';

        const confirm = await Swal.fire({
            title: `Confirm Batch ${actionText}`,
            text: `Are you sure you want to ${actionText.toLowerCase()} this batch?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: status === 'Received' ? '#2ca078' : '#ef4444',
            confirmButtonText: `Yes, ${actionText}`
        });

        if (confirm.isConfirmed) {
            Swal.fire({ title: 'Processing...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

            try {
                const response = await fetch('backend/be_dispatch.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'process_batch_intake',
                        dispatched_by: currentBatchData.dispatchedBy,
                        status: status,
                        remarks: remarks
                    })
                });
                const res = await response.json();

                if (res.success) {
                    // Update Last Sync
                    const now = new Date();
                    const syncTime = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    localStorage.setItem('last_intake_sync', syncTime);

                    await Swal.fire({ icon: 'success', title: 'Success', text: res.message, timer: 2000, showConfirmButton: false });
                    closeBatchModal();
                    fetchPendingDispatches();
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            } catch (error) {
                console.error('Batch Process Error:', error);
                Swal.fire('Error', 'Batch process failed.', 'error');
            }
        }
    };

    window.quickSync = (dispatchedBy) => {
        currentBatchData = { dispatchedBy };
        document.getElementById('batchRemarks').value = 'Quick Sync';
        processBatch('Received');
    };

    // Theme Toggle Initialization
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });

        // Load saved theme
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-mode');
        }
    }

    // Initial Fetch
    fetchPendingDispatches();
});
