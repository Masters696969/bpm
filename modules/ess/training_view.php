<?php
session_start();
require_once '../../config/config.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../../index.php");
    exit();
}

$assignment_id = $_GET['assignment_id'] ?? null;

if (!$assignment_id) {
    echo "Invalid Assignment ID.";
    exit();
}

// Ensure the logged in user actually owns this assignment
$username = $_SESSION['username'];
$stmtUser = $conn->prepare("SELECT EmployeeID FROM useraccounts WHERE Username = ?");
$stmtUser->bind_param("s", $username);
$stmtUser->execute();
$userResult = $stmtUser->get_result()->fetch_assoc();

if (!$userResult) {
    echo "Employee profile not found.";
    exit();
}
$employeeId = $userResult['EmployeeID'];

// Fetch the training module and assignment data
$query = "
    SELECT 
        et.AssignmentID, 
        et.Status, 
        tm.ModuleName, 
        tm.Description, 
        tm.Content,
        tm.file_path
    FROM employee_training et
    JOIN training_modules tm ON et.ModuleID = tm.ModuleID
    WHERE et.AssignmentID = ? AND et.EmployeeID = ?
";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Database error: " . $conn->error . ". Note: You may need to run the ALTER TABLE query to add the 'file_path' column.");
}

$stmt->bind_param("ii", $assignment_id, $employeeId);
$stmt->execute();
$assignment = $stmt->get_result()->fetch_assoc();

if (!$assignment) {
    echo "Training assignment not found or access denied.";
    exit();
}

// ---------------------------------------------------------
// LOGIC: If status is Pending, update it to In Progress
// ---------------------------------------------------------
if ($assignment['Status'] === 'Pending') {
    $updateStmt = $conn->prepare("UPDATE employee_training SET Status = 'In Progress' WHERE AssignmentID = ?");
    $updateStmt->bind_param("i", $assignment_id);
    $updateStmt->execute();
    
    // Update local variable so the UI reflects the change immediately
    $assignment['Status'] = 'In Progress';
}

// ---------------------------------------------------------
// LOGIC: Handle "Mark as Completed" button submission
// ---------------------------------------------------------
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_completed'])) {
    $completeStmt = $conn->prepare("UPDATE employee_training SET Status = 'Completed', CompletedDate = NOW() WHERE AssignmentID = ?");
    $completeStmt->bind_param("i", $assignment_id);
    
    if ($completeStmt->execute()) {
        $assignment['Status'] = 'Completed';
        $message = "Training marked as completed successfully!";
    } else {
        $message = "Error updating training status.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training View - <?php echo htmlspecialchars($assignment['ModuleName']); ?></title>
    <!-- Basic styling for neatness -->
    <style>
        :root {
            --brand-green: #2ca078;
            --surface: #ffffff;
            --background: #f8fafc;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background);
            color: var(--text-primary);
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .header {
            margin-bottom: 24px;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            color: var(--text-secondary);
            text-decoration: none;
            margin-bottom: 16px;
            font-size: 14px;
        }
        .back-link:hover {
            color: var(--brand-green);
        }
        .training-card {
            background: var(--surface);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .title {
            margin-top: 0;
            font-size: 24px;
            color: var(--text-primary);
            margin-bottom: 8px;
        }
        .description {
            color: var(--text-secondary);
            font-size: 15px;
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--border-color);
        }
        .content {
            margin-bottom: 32px;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 16px;
            text-transform: uppercase;
        }
        .badge.in-progress { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .badge.completed { background: rgba(44, 160, 120, 0.1); color: var(--brand-green); }
        
        .btn-complete {
            background-color: var(--brand-green);
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s;
            width: 100%;
        }
        .btn-complete:hover {
            background-color: #238462;
        }
        .btn-download {
            background-color: transparent;
            color: #3b82f6;
            border: 1px solid #3b82f6;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .btn-download:hover {
            background-color: #3b82f6;
            color: white;
        }
        .success-msg {
            background: rgba(44, 160, 120, 0.1);
            color: var(--brand-green);
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 24px;
            text-align: center;
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <a href="learningmgt.php" class="back-link">← Back to Learning Management</a>
    </div>

    <?php if ($message): ?>
        <div class="success-msg"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="training-card">
        <?php 
            $badgeClass = $assignment['Status'] === 'Completed' ? 'completed' : 'in-progress';
        ?>
        <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($assignment['Status']); ?></span>
        
        <h1 class="title"><?php echo htmlspecialchars($assignment['ModuleName']); ?></h1>
        <div class="description"><?php echo htmlspecialchars($assignment['Description']); ?></div>

        <div class="content">
            <?php 
                // MOST IMPORTANT: PDF/File Logic
                if (!empty($assignment['file_path'])) {
                    echo '<h3 style="margin-top:0; font-size:18px; margin-bottom:12px;">Training Material</h3>';
                    echo '<iframe src="' . htmlspecialchars($assignment['file_path']) . '" width="100%" height="600px" style="border:1px solid var(--border-color); border-radius:8px; margin-bottom: 16px;"></iframe>';
                    echo '<div style="text-align:right; margin-bottom: 32px; padding-bottom: 24px; border-bottom: 1px solid var(--border-color);">';
                    echo '<a href="' . htmlspecialchars($assignment['file_path']) . '" download class="btn-download">Download File ⭳</a>';
                    echo '</div>';
                }

                // Display textual learning content if it exists
                if (!empty($assignment['Content'])) {
                    echo $assignment['Content'];
                }
                
                // If the module has absolutely nothing bound to it
                if (empty($assignment['file_path']) && empty($assignment['Content'])) {
                    echo "<p><em>No detailed content or files have been uploaded for this module yet. Please refer to your department head for resources.</em></p>";
                }
            ?>
        </div>

        <?php if ($assignment['Status'] !== 'Completed'): ?>
            <form method="POST" action="">
                <button type="submit" name="mark_completed" class="btn-complete">Mark as Completed</button>
            </form>
        <?php else: ?>
            <div style="text-align: center; color: var(--brand-green); font-weight: 600; padding: 12px; border: 1px solid var(--brand-green); border-radius: 8px; background: rgba(44,160,120,0.05);">
                ✓ You have successfully completed this module.
            </div>
        <?php endif; ?>

    </div>
</div>

</body>
</html>
