<?php
require_once 'config/config.php';

$postId = $_GET['id'] ?? null;
if (!$postId) {
    header("Location: jobposting.php");
    exit;
}

// Fetch job details
$stmt = $conn->prepare("SELECT * FROM job_postings WHERE PostID = ? AND Status = 'Live'");
$stmt->bind_param("i", $postId);
$stmt->execute();
$job = $stmt->get_result()->fetch_assoc();

if (!$job) {
    header("Location: jobposting.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for <?php echo htmlspecialchars($job['Title']); ?> - Microfinance</title>
    <link rel="icon" type="image/png" href="img/logo.png">
    <link rel="stylesheet" href="css/landing.css">
    <link rel="stylesheet" href="css/apply.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <header style="padding: 1.5rem 5%; display: flex; justify-content: space-between; align-items: center; background: var(--surface); border-bottom: 1px solid var(--card-border);">
        <a href="jobposting.php" class="logo" style="text-decoration: none; display: flex; align-items: center; gap: 0.75rem; font-weight: 700; font-size: 1.25rem; color: var(--text-color-right-light);">
            <img src="img/logo.png" alt="Logo" style="width: 28px; height: 28px;">
            Microfinance Careers
        </a>
    </header>

    <main class="apply-container">
        <a href="jobposting.php" class="btn-back">
            <i data-lucide="arrow-left"></i> Back to Job Openings
        </a>

        <div class="apply-card">
            <div class="job-summary">
                <h1 style="font-size: 1.8rem; color: var(--brand-green); margin-bottom: 0.5rem;"><?php echo htmlspecialchars($job['Title']); ?></h1>
                <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; color: var(--label-text-light); font-size: 0.95rem;">
                    <span style="display: flex; align-items: center; gap: 0.4rem;"><i data-lucide="map-pin" style="width: 16px;"></i> <?php echo htmlspecialchars($job['Location']); ?></span>
                    <span style="display: flex; align-items: center; gap: 0.4rem;"><i data-lucide="banknote" style="width: 16px;"></i> <?php echo htmlspecialchars($job['SalaryRange']); ?> / <?php echo htmlspecialchars($job['SalaryType']); ?></span>
                    <span style="display: flex; align-items: center; gap: 0.4rem;"><i data-lucide="briefcase" style="width: 16px;"></i> <?php echo htmlspecialchars($job['JobType']); ?></span>
                </div>
            </div>

            <form id="applyForm" enctype="multipart/form-data">
                <input type="hidden" name="post_id" value="<?php echo $job['PostID']; ?>">
                
                <h3 class="form-section-title">Personal Information</h3>
                
                <div class="form-grid" style="grid-template-columns: 1fr 1fr 1fr;">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" required placeholder="John">
                    </div>
                    <div class="form-group">
                        <label>Middle Name</label>
                        <input type="text" name="middle_name" placeholder="Quincy">
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" required placeholder="Doe">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" required placeholder="john.doe@example.com">
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" required placeholder="09123456789">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Gender</label>
                        <select name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Permanent Address</label>
                    <textarea name="address" required rows="2" placeholder="Unit/House No., Street, Barangay, City, Province"></textarea>
                </div>

                <h3 class="form-section-title">Emergency Contact Information</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Contact Name</label>
                        <input type="text" name="emergency_name" required placeholder="Full Name">
                    </div>
                    <div class="form-group">
                        <label>Relationship</label>
                        <input type="text" name="emergency_relationship" required placeholder="Spouse, Parent, etc.">
                    </div>
                    <div class="form-group">
                        <label>Contact Phone</label>
                        <input type="tel" name="emergency_phone" required placeholder="09123456789">
                    </div>
                </div>


                <h3 class="form-section-title">Required Documents</h3>
                <div class="form-group">
                    <label>Updated Resume/CV</label>
                    <input type="file" name="resume" accept=".pdf,.doc,.docx" required>
                    <p class="file-upload-help">PDF or Word document (Max 5MB)</p>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Valid Government ID (Optional)</label>
                        <input type="file" name="gov_id" accept="image/*,.pdf">
                    </div>
                    <div class="form-group">
                        <label>NBI or Police Clearance (Optional)</label>
                        <input type="file" name="clearance" accept="image/*,.pdf">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Transcript of Records (TOR) (Optional)</label>
                        <input type="file" name="tor" accept="image/*,.pdf">
                    </div>
                    <div class="form-group">
                        <label>2x2 ID Picture (Optional)</label>
                        <input type="file" name="id_picture" accept="image/*">
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">Submit Application</button>
            </form>
        </div>
    </main>

    <script src="js/apply.js"></script>
</body>
</html>
