<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}
require_once '../../config/config.php';

$applicant_id = $_GET['id'] ?? null;
if (!$applicant_id) {
    die("Invalid Access: Applicant ID missing.");
}

// Fetch Applicant Details for the Header
$stmt = $conn->prepare("SELECT a.FirstName, a.LastName, j.Title as JobTitle 
                        FROM applicants a 
                        JOIN job_postings j ON a.PostID = j.PostID 
                        WHERE a.ApplicantID = ?");
$stmt->bind_param("i", $applicant_id);
$stmt->execute();
$applicant = $stmt->get_result()->fetch_assoc();

if (!$applicant) {
    die("Invalid Access: Applicant not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examination Center - Microfinance</title>
    <link rel="stylesheet" href="../../css/applicationmgt.css?v=<?php echo time(); ?>">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../../css/take_exam.css?v=<?php echo time(); ?>">
</head>
<body data-id="<?php echo $applicant_id; ?>">
    <!-- Fullscreen Prompt Overlay -->
    <div id="fullscreenOverlay" class="fullscreen-overlay">
        <i data-lucide="maximize" style="width: 64px; height: 64px; margin-bottom: 1.5rem;"></i>
        <h1>Ready to start the exam?</h1>
        <p style="color: rgba(255,255,255,0.7); margin-top: 1rem;">The examination must be taken in full-screen mode for security.</p>
        <button id="enterFullscreenBtn" class="fullscreen-btn">Enter Full Screen & Begin</button>
    </div>

    <!-- Exam Header -->
    <header class="exam-header">
        <div class="candidate-info">
            <div class="initials">
                <?php echo strtoupper(substr($applicant['FirstName'], 0, 1) . substr($applicant['LastName'], 0, 1)); ?>
            </div>
            <div>
                <div style="font-weight: 700;"><?php echo htmlspecialchars($applicant['FirstName'] . ' ' . $applicant['LastName']); ?></div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);"><?php echo htmlspecialchars($applicant['JobTitle']); ?></div>
            </div>
        </div>

        <div class="timer-box" id="timerContainer" style="display: none;">
            <i data-lucide="clock" style="width: 18px;"></i>
            <span id="timerVal">15:00</span>
        </div>
    </header>

    <!-- Main Exam Area -->
    <main class="exam-main">
        <div id="introScreen" class="exam-card intro-screen">
            <i data-lucide="file-check-2" style="width: 64px; height: 64px; color: var(--brand-green); margin-bottom: 1.5rem;"></i>
            <h2>Examination Instructions</h2>
            <div style="text-align: left; margin: 2rem 0; line-height: 1.6; color: var(--text-secondary);">
                <ul style="padding-left: 1.5rem;">
                    <li>There are <strong>15 multiple-choice questions</strong> in total.</li>
                    <li>You have <strong>15 minutes</strong> to complete the exam.</li>
                    <li>Questions cover common competencies and your applied department.</li>
                    <li>The exam will auto-submit when the timer reaches zero.</li>
                    <li>Do not refresh or leave the page during the exam.</li>
                </ul>
            </div>
            <button id="startExamBtn" class="btn btn-next" style="width: 100%; justify-content: center; padding: 1.25rem;">
                Begin Examination Now
            </button>
        </div>

        <div id="questionCard" class="exam-card" style="display: none;">
            <div class="question-meta" id="questionCounter">Question 1 of 15</div>
            <div class="question-text" id="qText">Loading question...</div>
            <div class="options-grid" id="optionsList">
                <!-- Options will be injected here -->
            </div>
        </div>
    </main>

    <!-- Exam Footer -->
    <footer class="exam-footer" id="examFooter" style="display: none;">
        <button id="prevBtn" class="btn btn-prev">
            <i data-lucide="chevron-left"></i> Previous
        </button>
        <div style="display: flex; gap: 1rem;">
            <button id="nextBtn" class="btn btn-next">
                Next Question <i data-lucide="chevron-right"></i>
            </button>
            <button id="submitBtn" class="btn btn-submit" style="display: none;">
                Submit Assessment <i data-lucide="send"></i>
            </button>
        </div>
    </footer>

    <script src="../../js/take_exam.js?v=<?php echo time(); ?>"></script>
    <script>
        if (window.lucide) lucide.createIcons();
    </script>
</body>
</html>
