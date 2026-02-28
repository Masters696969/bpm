/* ============================================================
   applybank.js — ESS Apply Bank Account JS
   Handles: drag-drop, submission upload AJAX
============================================================ */

const AB = {
    actionUrl: 'applybank_action.php',

    form: null,
    dropZone: null,
    fileInput: null,
    dropContent: null,
    filePreview: null,
    submitBtn: null,

    init() {
        this.form = document.getElementById('submitForm');
        this.dropZone = document.getElementById('abDropZone');
        this.fileInput = document.getElementById('filledPdf');
        this.dropContent = document.getElementById('abDropContent');
        this.filePreview = document.getElementById('abFilePreview');
        this.submitBtn = document.getElementById('submitBtn');

        if (!this.form) return;

        // Drag & drop
        if (this.dropZone) {
            this.dropZone.addEventListener('click', () => this.fileInput.click());
            this.dropZone.addEventListener('dragover', e => { e.preventDefault(); this.dropZone.classList.add('ab-dz-hover'); });
            this.dropZone.addEventListener('dragleave', () => this.dropZone.classList.remove('ab-dz-hover'));
            this.dropZone.addEventListener('drop', e => {
                e.preventDefault();
                this.dropZone.classList.remove('ab-dz-hover');
                const file = e.dataTransfer.files[0];
                if (file) this.handleFile(file);
            });
            this.fileInput.addEventListener('change', () => {
                if (this.fileInput.files[0]) this.handleFile(this.fileInput.files[0]);
            });
        }

        this.form.addEventListener('submit', e => {
            e.preventDefault();
            this.submit();
        });
    },

    handleFile(file) {
        if (file.type !== 'application/pdf') {
            Swal.fire({ icon: 'error', title: 'Invalid File', text: 'Only PDF files are accepted.' });
            return;
        }
        if (file.size > 15 * 1024 * 1024) {
            Swal.fire({ icon: 'error', title: 'Too Large', text: 'Maximum file size is 15 MB.' });
            return;
        }
        const dt = new DataTransfer();
        dt.items.add(file);
        this.fileInput.files = dt.files;

        const sizeKB = (file.size / 1024).toFixed(1);
        const sizeLabel = sizeKB >= 1024 ? (sizeKB / 1024).toFixed(1) + ' MB' : sizeKB + ' KB';
        this.dropContent.style.display = 'none';
        this.filePreview.style.display = 'flex';
        this.filePreview.innerHTML = `
      <i data-lucide="file-check" style="color:var(--brand-green)"></i>
      <span class="ab-fp-name">${this.escHtml(file.name)}</span>
      <span class="ab-fp-size">${sizeLabel}</span>
      <button type="button" class="ab-fp-clear">&#x2715;</button>
    `;
        lucide.createIcons();
        this.filePreview.querySelector('.ab-fp-clear').addEventListener('click', () => this.resetDrop());
    },

    resetDrop() {
        if (this.fileInput) this.fileInput.value = '';
        if (this.dropContent) this.dropContent.style.display = '';
        if (this.filePreview) { this.filePreview.style.display = 'none'; this.filePreview.innerHTML = ''; }
    },

    async submit() {
        if (!this.fileInput.files.length) {
            Swal.fire({ icon: 'warning', title: 'No file selected', text: 'Please select the completed PDF before submitting.' });
            return;
        }

        const result = await Swal.fire({
            title: 'Submit your form?',
            text: 'Your completed PDF will be sent to HR for processing.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2ca078',
            confirmButtonText: 'Yes, submit',
        });
        if (!result.isConfirmed) return;

        const fd = new FormData(this.form);
        fd.append('action', 'submit_application');

        const orig = this.submitBtn.innerHTML;
        this.submitBtn.disabled = true;
        this.submitBtn.innerHTML = '<i data-lucide="loader"></i> Submitting…';
        lucide.createIcons();

        try {
            const res = await fetch(this.actionUrl, { method: 'POST', body: fd });
            const data = await res.json();
            if (data.success) {
                await Swal.fire({ icon: 'success', title: 'Submitted!', text: data.message, timer: 2500, showConfirmButton: false });
                location.reload();
            } else {
                Swal.fire({ icon: 'error', title: 'Submission Failed', text: data.message });
            }
        } catch {
            Swal.fire({ icon: 'error', title: 'Network Error', text: 'Could not reach the server.' });
        } finally {
            this.submitBtn.disabled = false;
            this.submitBtn.innerHTML = orig;
            lucide.createIcons();
        }
    },

    escHtml(str) {
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    },
};

document.addEventListener('DOMContentLoaded', () => AB.init());

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
