<?php
// jobposting.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Openings - Microfinance</title>
    <link rel="icon" type="image/png" href="img/logo.png">
    <link rel="stylesheet" href="css/landing.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* Minimalist Job Posting Styles */
        :root {
            --card-bg: var(--input-bg-light);
            --card-border: rgba(44, 160, 120, 0.1);
        }

        .job-section {
            padding: 4rem 5%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-header h2 {
            font-size: 2.5rem;
            color: var(--brand-green);
            margin-bottom: 0.5rem;
        }

        .section-header p {
            color: var(--label-text-light);
            font-size: 1.1rem;
        }

        .search-filter-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 3rem;
            background: var(--surface);
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--card-border);
        }

        .search-input-group {
            flex: 2;
            min-width: 300px;
            position: relative;
        }

        .search-input-group input {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid var(--input-border);
            border-radius: 8px;
            background: var(--input-bg-light);
            color: var(--text-color-right-light);
            font-size: 1rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .search-input-group input:focus {
            border-color: var(--brand-green);
            box-shadow: 0 0 0 3px rgba(44, 160, 120, 0.1);
        }

        .filter-select {
            flex: 1;
            min-width: 150px;
            padding: 0.8rem 1rem;
            border: 1px solid var(--input-border);
            border-radius: 8px;
            background: var(--input-bg-light);
            color: var(--text-color-right-light);
            cursor: pointer;
            outline: none;
        }

        .btn-search {
            background-color: var(--brand-green);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-search:hover {
            background-color: var(--button-hover);
        }

        .jobs-grid-layout {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 2rem;
        }

        .job-card {
            background: var(--input-bg-light);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .job-card:hover {
            transform: translateY(-4px);
            border-color: var(--brand-green);
            box-shadow: 0 10px 25px rgba(44, 160, 120, 0.1);
        }

        .job-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .job-card-title h3 {
            font-size: 1.4rem;
            color: var(--text-color-right-light);
            margin-bottom: 0.25rem;
        }

        .job-company {
            color: var(--brand-green);
            font-weight: 600;
            font-size: 0.95rem;
        }

        .job-tags {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .tag {
            background: rgba(44, 160, 120, 0.08);
            color: var(--brand-green);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .tag.featured {
            background: rgba(255, 193, 7, 0.1);
            color: #d97706;
        }

        .job-desc {
            color: var(--label-text-light);
            font-size: 0.95rem;
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .job-meta {
            display: flex;
            gap: 1.5rem;
            color: var(--label-text-light);
            font-size: 0.85rem;
            border-top: 1px solid var(--card-border);
            padding-top: 1rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        /* Sidebar Styles */
        .job-sidebar {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .sidebar-widget {
            background: var(--input-bg-light);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            padding: 1.5rem;
        }

        .sidebar-widget h4 {
            font-size: 1.1rem;
            margin-bottom: 1rem;
            color: var(--text-color-right-light);
            border-left: 4px solid var(--brand-green);
            padding-left: 0.75rem;
        }

        .company-mini-profile {
            text-align: center;
        }

        .mini-logo {
            width: 60px;
            height: 60px;
            background: #424242;
            border-radius: 12px;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1001;
            padding: 1rem;
        }

        .modal-container {
            background: var(--input-bg-light);
            width: 100%;
            max-width: 700px;
            border-radius: 16px;
            padding: 2.5rem;
            position: relative;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-close {
            position: absolute;
            top: 1.25rem;
            right: 1.25rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--label-text-light);
            cursor: pointer;
        }

        .modal-header h2 {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .modal-body {
            margin-top: 1.5rem;
        }

        .modal-body h4 {
            margin: 1.5rem 0 0.75rem;
            color: var(--brand-green);
        }

        .modal-body ul {
            padding-left: 1.25rem;
        }

        .modal-body li {
            margin-bottom: 0.5rem;
            color: var(--label-text-light);
        }

        .btn-apply {
            width: 100%;
            margin-top: 2rem;
            padding: 1rem;
            background: var(--brand-green);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            font-size: 1.1rem;
        }

        .btn-apply:hover {
            background: var(--button-hover);
        }

        /* Theme Toggle Box Styling */
        .theme-toggle {
            padding: 8px;
            border-radius: 8px;
            border: 1.5px solid var(--card-border);
            background: rgba(44, 160, 120, 0.05);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-color-right-light);
            cursor: pointer;
        }

        .theme-toggle:hover {
            border-color: var(--brand-green);
            background: rgba(44, 160, 120, 0.1);
        }

        /* Lucide Icon Alignment */
        .lucide {
            width: 18px;
            height: 18px;
            vertical-align: middle;
        }

        .meta-item .lucide {
            width: 14px;
            height: 14px;
            color: var(--brand-green);
        }

        @media (max-width: 900px) {
            .jobs-grid-layout {
                grid-template-columns: 1fr;
            }
            .job-sidebar {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Unique Minimalist Header -->
    <header style="padding: 1.5rem 5%; display: flex; justify-content: space-between; align-items: center; background: var(--surface); border-bottom: 1px solid var(--card-border);">
        <div class="logo" style="display: flex; align-items: center; gap: 0.75rem; font-weight: 700; font-size: 1.25rem; color: var(--text-color-right-light);">
            <div class="sidebar-logo-icon">
                <img src="img/logo.png" alt="Logo" style="width: 28px; height: 28px; object-fit: contain;">
            </div>
            Microfinance Careers
        </div>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
                <i data-lucide="sun" class="sun-icon"></i>
                <i data-lucide="moon" class="moon-icon" style="display:none;"></i>
            </button>
        </div>
    </header>

    <main class="job-section">
        <div class="section-header">
            <h2>Career Opportunities</h2>
            <p>Join our team and help us empower Filipino entrepreneurs.</p>
        </div>

        <div class="search-filter-container">
            <div class="search-input-group">
                <input type="text" id="searchInput" placeholder="Search by job title or keywords...">
            </div>
            <select id="categoryFilter" class="filter-select">
                <option value="">All Categories</option>
                <option value="Technology">Technology</option>
                <option value="Design">Design</option>
                <option value="Marketing">Marketing</option>
                <option value="Human Resources">Human Resources</option>
            </select>
            <button class="btn-search" onclick="handleSearch()">Search</button>
        </div>

        <div class="jobs-grid-layout">
            <div id="jobsList">
                <!-- Javascript will populate this -->
            </div>

            <aside class="job-sidebar">
                <div class="sidebar-widget">
                    <h4>Featured Company</h4>
                    <div class="company-mini-profile">
                        <div class="mini-logo">
                            <img src="img/logo.png" alt="Microfinance" style="width: 32px; height: 32px; object-fit: contain;">
                        </div>
                        <h5 style="font-size: 1.1rem; margin-bottom: 0.5rem;">Microfinance Inc.</h5>
                        <p style="font-size: 0.9rem; color: var(--label-text-light); margin-bottom: 1rem;">Empowering dreams through accessible lending solutions.</p>
                        <button class="btn-search" onclick="openCompanyModal()" style="width: 100%; font-size: 0.9rem; padding: 0.6rem;">View Company</button>
                    </div>
                </div>
                <div class="sidebar-widget">
                    <h4>Why Join Us?</h4>
                    <ul style="list-style: none; padding: 0; font-size: 0.9rem; color: var(--label-text-light);">
                        <li style="margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                            <i data-lucide="check-circle" style="width: 16px; height: 16px; color: var(--brand-green);"></i>
                            Competitive Salary
                        </li>
                        <li style="margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                            <i data-lucide="check-circle" style="width: 16px; height: 16px; color: var(--brand-green);"></i>
                            Health & Wellness Benefits
                        </li>
                        <li style="margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                            <i data-lucide="check-circle" style="width: 16px; height: 16px; color: var(--brand-green);"></i>
                            Professional Growth
                        </li>
                        <li style="display: flex; align-items: center; gap: 0.5rem;">
                            <i data-lucide="check-circle" style="width: 16px; height: 16px; color: var(--brand-green);"></i>
                            Positive Impact
                        </li>
                    </ul>
                </div>
                <div class="sidebar-widget" style="background: rgba(44, 160, 120, 0.05); border-color: var(--brand-green);">
                    <h4>Need a Loan?</h4>
                    <p style="font-size: 0.9rem; color: var(--label-text-light); margin-bottom: 1rem;">We empower entrepreneurs and families through accessible lending.</p>
                    <a href="index.php" style="display: flex; align-items: center; gap: 0.5rem; color: var(--brand-green); font-weight: 600; text-decoration: none; font-size: 0.9rem;">
                        Learn about our loans <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
                    </a>
                </div>
            </aside>
        </div>
    </main>

    <!-- Job Details Modal -->
    <div class="modal-overlay" id="jobModal">
        <div class="modal-container">
            <button class="modal-close" onclick="closeModal()">&times;</button>
            <div id="modalContent">
                <!-- Javascript will populate this -->
            </div>
        </div>
    </div>

    <footer style="padding: 2rem; text-align: center; color: var(--label-text-light); border-top: 1px solid var(--card-border); background: var(--surface);">
        <p style="font-size: 0.9rem;">&copy; 2025 Microfinance Inc. All rights reserved.</p>
        <p style="font-size: 0.8rem; margin-top: 0.5rem;">Empowering Filipino entrepreneurs with accessible lending.</p>
    </footer>

    <script>
        const jobs = [
            {
                id: 1,
                title: "Software Engineer",
                company: "Microfinance Inc.",
                location: "Metro Manila",
                type: "Full-time",
                category: "Technology",
                salary: "₱40k - ₱60k",
                posted: "Today",
                description: "We are looking for a dedicated Software Engineer to help us build the next generation of microfinance tools.",
                featured: true,
                responsibilities: [
                    "Develop and maintain PHP-based web applications",
                    "Collaborate with product designers to create seamless UIs",
                    "Optimize applications for maximum speed and scalability",
                    "Participate in code reviews and team meetings"
                ],
                requirements: [
                    "Strong knowledge of PHP, JavaScript, and MySQL",
                    "Experience with modern CSS frameworks",
                    "Good understanding of version control (Git)",
                    "Excellent problem-solving skills"
                ]
            },
            {
                id: 2,
                title: "Account Officer",
                company: "Microfinance Inc.",
                location: "Cebu City",
                type: "Full-time",
                category: "Marketing",
                salary: "₱25k - ₱35k",
                posted: "2 days ago",
                description: "Join our field team in helping local entrepreneurs access the capital they need to grow.",
                featured: false,
                responsibilities: [
                    "Identify and recruit potential clients",
                    "Conduct loan interviews and site visits",
                    "Evaluate creditworthiness and risk",
                    "Monitor loan repayment performance"
                ],
                requirements: [
                    "Degree in Business, Marketing, or related field",
                    "Excellent communication and interpersonal skills",
                    "Willingness to do field work",
                    "Integrity and high ethical standards"
                ]
            },
            {
                id: 3,
                title: "HR Generalist",
                company: "Microfinance Inc.",
                location: "Metro Manila",
                type: "Full-time",
                category: "Human Resources",
                salary: "₱30k - ₱45k",
                posted: "1 week ago",
                description: "Help us build a world-class team and maintain our positive company culture.",
                featured: false,
                responsibilities: [
                    "Manage the end-to-end recruitment process",
                    "Handle employee relations and engagement",
                    "Coordinate training and development programs",
                    "Ensure compliance with labor laws"
                ],
                requirements: [
                    "3+ years of HR experience",
                    "Strong knowledge of Philippine labor laws",
                    "Proactive and people-oriented approach",
                    "Strong organizational skills"
                ]
            }
        ];

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
                        </div>
                    </div>
                    <p class="job-desc">${job.description}</p>
                    <div class="job-meta">
                        <div class="meta-item"><i data-lucide="map-pin"></i> ${job.location}</div>
                        <div class="meta-item"><i data-lucide="banknote"></i> ${job.salary}</div>
                        <div class="meta-item"><i data-lucide="clock"></i> ${job.posted}</div>
                    </div>
                </div>
            `).join('');
        }

        function handleSearch() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            const category = document.getElementById('categoryFilter').value;

            filteredJobs = jobs.filter(job => {
                const matchesQuery = job.title.toLowerCase().includes(query) || job.description.toLowerCase().includes(query);
                const matchesCategory = category === "" || job.category === category;
                return matchesQuery && matchesCategory;
            });

            renderJobs();
        }

        function openModal(id) {
            const job = jobs.find(j => j.id === id);
            if (!job) return;

            const modalContent = document.getElementById('modalContent');
            modalContent.innerHTML = `
                <div class="modal-header">
                    <h2>${job.title}</h2>
                    <div class="job-company">${job.company}</div>
                    <div style="display: flex; gap: 1rem; margin-top: 0.5rem; color: var(--label-text-light); font-size: 0.9rem;">
                        <span style="display: flex; align-items: center; gap: 0.3rem;"><i data-lucide="map-pin" style="width: 14px; height: 14px;"></i> ${job.location}</span>
                        <span style="display: flex; align-items: center; gap: 0.3rem;"><i data-lucide="banknote" style="width: 14px; height: 14px;"></i> ${job.salary}</span>
                        <span style="display: flex; align-items: center; gap: 0.3rem;"><i data-lucide="file-text" style="width: 14px; height: 14px;"></i> ${job.type}</span>
                    </div>
                </div>
                <div class="modal-body">
                    <h4>About the Role</h4>
                    <p style="color: var(--label-text-light); line-height: 1.6;">${job.description}</p>
                    
                    <h4>Responsibilities</h4>
                    <ul>
                        ${job.responsibilities.map(r => `<li>${r}</li>`).join('')}
                    </ul>
                    
                    <h4>Requirements</h4>
                    <ul>
                        ${job.requirements.map(r => `<li>${r}</li>`).join('')}
                    </ul>
                    
                    <button class="btn-apply" onclick="alert('In a real application, this would open the application form for ${job.title}.')">Apply for this Position</button>
                </div>
            `;
            document.getElementById('jobModal').style.display = 'flex';
            initIcons();
        }

        function closeModal() {
            document.getElementById('jobModal').style.display = 'none';
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
        document.getElementById('jobModal').addEventListener('click', (e) => {
            if (e.target.id === 'jobModal') closeModal();
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
        renderJobs = function() {
            originalRenderJobs();
            initIcons();
        };

        // Initialize icons on load
        document.addEventListener('DOMContentLoaded', initIcons);
        renderJobs(); // Initial call to render and init icons
    </script>
</body>
</html>
