let filteredJobs = [...jobs];


function renderJobs() {
    const jobsList = document.getElementById('jobsList');
    if (filteredJobs.length === 0) {
        jobsList.innerHTML = '<div style="text-align: center; padding: 3rem; color: var(--label-text-light);">No jobs found.</div>';
        return;
    }

    jobsList.innerHTML = filteredJobs.map(job => `
                <div class="job-card" onclick="openModal(${job.id})">
                    <div class="job-card-header">
                        <div class="job-card-title">
                            <h3>${job.title}</h3>
                            <div class="job-company">${job.company}</div>
                        </div>
                        <div class="job-tags">
                            ${job.featured ? '<span class="tag featured">Featured</span>' : ''}
                            <span class="tag">${job.type}</span>
                            <span class="tag" style="background: rgba(44, 160, 120, 0.15);">${job.department}</span>
                        </div>
                    </div>
                    <p class="job-desc">${job.description}</p>
                    <div class="job-meta">
                        <div class="meta-item"><i data-lucide="map-pin"></i> ${job.location}</div>
                        <div class="meta-item"><i data-lucide="banknote"></i> ${job.salary} / ${job.salaryType}</div>
                        <div class="meta-item"><i data-lucide="clock"></i> ${job.posted}</div>
                    </div>
                </div>
            `).join('');
}

function handleSearch() {
    const query = document.getElementById('searchInput').value.toLowerCase();
    const dept = document.getElementById('deptFilter').value;

    filteredJobs = jobs.filter(job => {
        const matchesQuery = job.title.toLowerCase().includes(query) || job.description.toLowerCase().includes(query);
        const matchesDept = dept === "" || job.department === dept;
        return matchesQuery && matchesDept;
    });

    renderJobs();
}

function openModal(id) {
    const job = jobs.find(j => j.id === id);
    if (!job) return;

    const modalContent = document.getElementById('modalContent');
    modalContent.innerHTML = `
                <div class="minimal-modal-header">
                    <div class="header-main-info">
                        <h2>${job.title}</h2>
                        <div class="company-badge-minimal">${job.company}</div>
                    </div>
                    <div class="header-meta-grid">
                        <div class="meta-pill"><i data-lucide="map-pin"></i> ${job.location}</div>
                        <div class="meta-pill"><i data-lucide="banknote"></i> ${job.salary} <small>/ ${job.salaryType}</small></div>
                        <div class="meta-pill"><i data-lucide="briefcase"></i> ${job.type}</div>
                    </div>
                </div>
                <div class="minimal-modal-body">
                    <section class="modal-section-minimal">
                        <h3>Overview</h3>
                        <p>${job.description}</p>
                    </section>
                    
                    <div class="qual-grid-minimal">
                        <section class="modal-section-minimal">
                            <h3>Key Responsibilities</h3>
                            <ul class="sleek-list">
                                ${job.responsibilities.map(r => `<li>${r}</li>`).join('')}
                            </ul>
                        </section>
                        
                        <section class="modal-section-minimal">
                            <h3>Qualifications</h3>
                            <ul class="sleek-list">
                                ${job.requirements.map(r => `<li>${r}</li>`).join('')}
                            </ul>
                        </section>
                    </div>
                    
                    <div class="modal-footer-minimal">
                        <a href="apply.php?id=${job.id}" class="btn-apply-minimal">Proceed and Apply</a>
                    </div>
                </div>
            `;
    document.getElementById('jobModal').style.display = 'flex';
    initIcons();
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function openCompanyModal() {
    const modalContent = document.getElementById('modalContent');
    modalContent.innerHTML = `
                <div class="modal-header">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div class="mini-logo" style="margin: 0;">
                            <img src="img/logo.png" alt="Microfinance" style="width: 32px; height: 32px;">
                        </div>
                        <h2 style="margin: 0;">About Microfinance Inc.</h2>
                    </div>
                </div>
                <div class="modal-body">
                    <p style="color: var(--label-text-light); line-height: 1.7; margin-bottom: 1.5rem;">
                        Microfinance Inc. is a leading financial institution dedicated to providing accessible and inclusive lending solutions to Filipino communities. Since our founding, we have been committed to empowering micro-entrepreneurs, families, and individuals to achieve their financial goals through transparent and ethical microfinance.
                    </p>
                    
                    <h4 style="margin-bottom: 0.5rem; color: var(--brand-green);">Our Mission</h4>
                    <p style="color: var(--label-text-light); margin-bottom: 1.5rem;">
                        To foster economic growth and improve the quality of life for all Filipinos by delivering fast, affordable, and reliable financial services.
                    </p>

                    <h4 style="margin-bottom: 0.5rem; color: var(--brand-green);">Why Work With Us?</h4>
                    <ul style="color: var(--label-text-light); line-height: 1.8;">
                        <li>Professional growth and continuous learning opportunities</li>
                        <li>Inclusive and supportive work culture</li>
                        <li>Making a tangible positive impact in Filipino communities</li>
                        <li>Competitive benefits and work-life balance</li>
                    </ul>

                    <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--card-border); display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 0.9rem; color: var(--label-text-light);">📍 Head Office: Metro Manila, PH</span>
                        <a href="index.php" style="color: var(--brand-green); text-decoration: none; font-weight: 600;">Visit Main Website</a>
                    </div>
                </div>
            `;
    document.getElementById('jobModal').style.display = 'flex';
    initIcons();
}

// Close modal on overlay click
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal-overlay')) {
            e.target.style.display = 'none';
        }
    });
});

// Initial render
renderJobs();

// Theme Toggle Logic matching Dashboard
const themeToggle = document.getElementById("themeToggle");
const body = document.body;
const sunIcon = themeToggle.querySelector('.sun-icon');
const moonIcon = themeToggle.querySelector('.moon-icon');

const updateThemeUI = (isDark) => {
    if (isDark) {
        sunIcon.style.display = 'none';
        moonIcon.style.display = 'block';
    } else {
        sunIcon.style.display = 'block';
        moonIcon.style.display = 'none';
    }
};

const savedTheme = localStorage.getItem("theme");
if (savedTheme === "dark") {
    body.classList.add("dark-mode");
    updateThemeUI(true);
}

themeToggle.addEventListener("click", () => {
    const isDark = body.classList.toggle("dark-mode");
    localStorage.setItem("theme", isDark ? "dark" : "light");
    updateThemeUI(isDark);
});

// Lucide Icon Initialization
function initIcons() {
    if (window.lucide) {
        window.lucide.createIcons();
    }
}

// Wrap renderJobs to re-init icons
const originalRenderJobs = renderJobs;
renderJobs = function () {
    originalRenderJobs();
    initIcons();
};

// Initialize icons on load
document.addEventListener('DOMContentLoaded', initIcons);
renderJobs(); // Initial call to render and init icons

// Cross-Device Sync (The Pulse)
let lastPulse = null;

async function checkPulse() {
    try {
        const response = await fetch('sync_pulse.php');
        const data = await response.json();

        if (lastPulse === null) {
            lastPulse = data.last_update;
        } else if (lastPulse !== data.last_update) {
            console.log('Update detected via pulse. Refreshing...');
            location.reload();
        }
    } catch (error) {
        console.error('Pulse check failed:', error);
    }
}

// Check every 5 seconds for cross-device responsiveness
setInterval(checkPulse, 5000);
checkPulse(); // Initial check