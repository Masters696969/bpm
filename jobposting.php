<?php
require_once 'config/config.php';
session_start();

// Fetch all live job postings
$query = "SELECT * FROM job_postings WHERE Status = 'Live' ORDER BY CreatedAt DESC";
$result = $conn->query($query);
$jobsList = [];

// Fetch unique departments for the filter
$deptQuery = "SELECT DISTINCT Department FROM job_postings WHERE Status = 'Live' ORDER BY Department";
$deptResult = $conn->query($deptQuery);
$departments = [];
while ($d = $deptResult->fetch_assoc()) {
    $departments[] = $d['Department'];
}

while ($row = $result->fetch_assoc()) {
    // Convert new-line separated strings to arrays for the frontend
    $row['responsibilities'] = array_filter(explode("\n", str_replace("\r", "", $row['Responsibilities'])));
    $row['requirements'] = array_filter(explode("\n", str_replace("\r", "", $row['Requirements'])));
    
    // Map database fields to the frontend's expected format
    $jobsList[] = [
        'id' => (int)$row['PostID'],
        'title' => $row['Title'],
        'company' => "Microfinance Inc.", 
        'location' => $row['Location'],
        'type' => $row['JobType'],
        'department' => $row['Department'],
        'salary' => $row['SalaryRange'],
        'salaryType' => $row['SalaryType'] ?? 'Monthly',
        'posted' => date('M d, Y', strtotime($row['CreatedAt'])),
        'description' => $row['Description'],
        'featured' => false,
        'responsibilities' => $row['responsibilities'],
        'requirements' => $row['requirements']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Openings - Microfinance</title>
    <link rel="icon" type="image/png" href="img/logo.png">
    <link rel="stylesheet" href="css/landing.css">
    <link rel="stylesheet" href="css/jobposting.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
       
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
            <select id="deptFilter" class="filter-select">
                <option value="">All Departments</option>
                <?php foreach ($departments as $dept): ?>
                    <option value="<?php echo htmlspecialchars($dept); ?>"><?php echo htmlspecialchars($dept); ?></option>
                <?php endforeach; ?>
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
            <button class="modal-close" onclick="closeModal('jobModal')">&times;</button>
            <div id="modalContent">
                <!-- Javascript will populate this -->
            </div>
        </div>    </div>
    <footer style="padding: 2rem; text-align: center; color: var(--label-text-light); border-top: 1px solid var(--card-border); background: var(--surface);">
        <p style="font-size: 0.9rem;">&copy; <?php echo date('Y'); ?> Microfinance Inc. All rights reserved.</p>
        <p style="font-size: 0.8rem; margin-top: 0.5rem;">Empowering Filipino entrepreneurs with accessible lending.</p>
    </footer>

    <script>
        const jobs = <?php echo json_encode($jobsList); ?>;
    </script>
    <script src="js/jobposting.js"></script>
</body>
</html>
