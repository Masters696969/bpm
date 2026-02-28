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

    if (themeToggle) {
        themeToggle.addEventListener("click", () => {
            body.classList.toggle("dark-mode");
            localStorage.setItem("theme", body.classList.contains("dark-mode") ? "dark" : "light");
        });
    }

    // 2. Sidebar & Mobile Logic
    if (sidebarToggle) {
        sidebarToggle.addEventListener("click", () => {
            sidebar.classList.toggle("collapsed");
            localStorage.setItem("sidebarCollapsed", sidebar.classList.contains("collapsed"));
        });
    }

    if (localStorage.getItem("sidebarCollapsed") === "true" && sidebar) sidebar.classList.add("collapsed");

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener("click", () => sidebar.classList.toggle("mobile-open"));
    }

    // 3. Submenu Logic
    document.querySelectorAll(".nav-item.has-submenu").forEach((item) => {
        item.addEventListener("click", (e) => {
            const module = item.getAttribute("data-module");
            const submenu = document.getElementById(`submenu-${module}`);
            if (submenu) {
                submenu.classList.toggle("active");
                item.classList.toggle("active");
            }
        });
    });

    // 4. Edit Profile Modal Logic
    const editModal = document.getElementById("editProfileModal");
    const btnEditProfile = document.getElementById("btnEditProfile");
    const btnCloseModal = document.getElementById("closeModal");
    const btnCancel = document.getElementById("btnCancel");
    const editForm = document.getElementById("editProfileForm");

    const showModal = () => {
        if (editModal) {
            editModal.classList.add("show");
            document.body.style.overflow = "hidden"; // Prevent scrolling
        }
    };

    const hideModal = () => {
        if (editModal) {
            editModal.classList.remove("show");
            document.body.style.overflow = ""; // Restore scrolling
        }
    };

    if (btnEditProfile) btnEditProfile.addEventListener("click", showModal);
    if (btnCloseModal) btnCloseModal.addEventListener("click", hideModal);
    if (btnCancel) btnCancel.addEventListener("click", hideModal);

    // Close on overlay click
    if (editModal) {
        editModal.addEventListener("click", (e) => {
            if (e.target === editModal) hideModal();
        });
    }

    // 5. Profile Photo Preview Logic
    const photoInput = document.getElementById("ProfilePhotoInput");
    const photoPreviewBox = document.getElementById("photo-preview-box");
    const modalImgPreview = document.getElementById("modal-img-preview");
    const modalPreviewInitials = document.getElementById("modal-preview-initials");

    if (photoInput && photoPreviewBox) {
        photoInput.addEventListener("change", function () {
            const file = this.files[0];
            if (file) {
                // Validate size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'File too large',
                        text: 'Please select an image smaller than 2MB.'
                    });
                    this.value = "";
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    if (modalImgPreview) {
                        modalImgPreview.src = e.target.result;
                        modalImgPreview.style.display = "block";
                    } else {
                        // Create img element if it doesn't exist (initials case)
                        const img = document.createElement("img");
                        img.id = "modal-img-preview";
                        img.src = e.target.result;
                        img.alt = "Preview";
                        photoPreviewBox.innerHTML = "";
                        photoPreviewBox.appendChild(img);

                        // Re-add overlay since we cleared innerHTML
                        const overlay = document.createElement("div");
                        overlay.className = "upload-overlay";
                        overlay.onclick = () => photoInput.click();
                        overlay.innerHTML = '<i data-lucide="image-plus"></i><span>Change Photo</span>';
                        photoPreviewBox.appendChild(overlay);
                        if (lucide) lucide.createIcons();
                    }
                    if (modalPreviewInitials) modalPreviewInitials.style.display = "none";
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // Handle form submission
    if (editForm) {
        editForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            const formData = new FormData(editForm);
            formData.append("action", "update_profile");

            Swal.fire({
                title: 'Saving changes...',
                text: 'Updating your profile information',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const response = await fetch("profile_action.php", {
                    method: "POST",
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Profile Updated!',
                        text: result.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: result.message || 'Something went wrong.'
                    });
                }
            } catch (error) {
                console.error("Error updating profile:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'System Error',
                    text: 'An unexpected error occurred while saving.'
                });
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
