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
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --card-border: rgba(44, 160, 120, 0.1);
        }
        .apply-container {
            padding: 4rem 5%;
            max-width: 900px;
            margin: 0 auto;
        }
        .apply-card {
            background: var(--surface);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        .job-summary {
            background: rgba(44, 160, 120, 0.05);
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            border-left: 4px solid var(--brand-green);
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .form-section-title {
            margin: 2rem 0 1.5rem;
            border-bottom: 1px solid var(--card-border);
            padding-bottom: 0.5rem;
            color: var(--brand-green);
            font-size: 1.25rem;
        }
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-group label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-color-right-light);
            margin-bottom: 0.5rem;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid var(--input-border);
            border-radius: 8px;
            background: var(--input-bg-light);
            outline: none;
            transition: all 0.3s ease;
            font-family: inherit;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            border-color: var(--brand-green);
            box-shadow: 0 0 0 3px rgba(44, 160, 120, 0.1);
        }
        .file-upload-help {
            font-size: 0.75rem;
            color: var(--label-text-light);
            margin-top: 0.3rem;
        }
        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: var(--brand-green);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            margin-top: 2rem;
        }
        .btn-submit:hover {
            background: var(--button-hover);
            transform: translateY(-2px);
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--label-text-light);
            text-decoration: none;
            font-size: 0.9rem;
            margin-bottom: 2rem;
            transition: color 0.3s ease;
        }
        .btn-back:hover {
            color: var(--brand-green);
        }

        @media (max-width: 600px) {
            .apply-card {
                padding: 1.5rem;
            }
        }
    </style>
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

    <script>
        lucide.createIcons();

        document.getElementById('applyForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            const originalText = btn.innerText;

            btn.disabled = true;
            btn.innerText = 'Submitting...';

            const formData = new FormData(e.target);

            try {
                const response = await fetch('apply_external_action.php', {
                    method: 'POST',
                    body: formData
                });

                const text = await response.text();
                let result;
                try {
                    result = JSON.parse(text);
                } catch (e) {
                    console.error('Server response was not JSON:', text);
                    throw new Error('Server returned an invalid response. Please check the server logs.');
                }

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Application Received!',
                        text: result.message,
                        confirmButtonColor: '#2ca078'
                    }).then(() => {
                        window.location.href = 'jobposting.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Submission Failed',
                        text: result.message,
                        confirmButtonColor: '#ef4444'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'System Error',
                    text: error.message || 'An unexpected error occurred. Please try again later.',
                    confirmButtonColor: '#ef4444'
                });
            } finally {
                btn.disabled = false;
                btn.innerText = originalText;
            }
        });
    </script>
</body>
</html>
