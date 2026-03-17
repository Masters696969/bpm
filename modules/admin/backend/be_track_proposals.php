<?php
session_start();
header('Content-Type: application/json');
require_once '../../../config/config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$user_id = $_SESSION['user_id'];

if ($action === 'check_active') {
    // Check for salary proposals
    $stmt1 = $conn->prepare("SELECT COUNT(*) as count FROM salary_grade_proposals WHERE ProposedBy = ? AND Status != 'Applied' AND Status != 'Rejected'");
    $stmt1->bind_param("i", $user_id);
    $stmt1->execute();
    $c1 = $stmt1->get_result()->fetch_assoc()['count'];

    // Check for statutory proposals
    $stmt2 = $conn->prepare("SELECT COUNT(*) as count FROM statutory_proposals WHERE ProposedBy = ? AND Status != 'Applied' AND Status != 'Rejected'");
    $stmt2->bind_param("i", $user_id);
    $stmt2->execute();
    $c2 = $stmt2->get_result()->fetch_assoc()['count'];
    
    echo json_encode(['success' => true, 'has_salary' => $c1 > 0, 'has_statutory' => $c2 > 0]);
    exit();
}

if ($action === 'fetch_all_statutory') {
    $stmt = $conn->prepare("
        SELECT BatchReference, Status, MAX(CreatedAt) as SubmittedDate, COUNT(ProposalID) as TotalChanges, Reason, Category
        FROM statutory_proposals 
        WHERE ProposedBy = ?
        GROUP BY BatchReference, Status, Reason, Category
        ORDER BY MAX(CreatedAt) DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $batches = [];
    while ($row = $result->fetch_assoc()) {
        $batches[] = $row;
    }
    
    echo json_encode(['success' => true, 'batches' => $batches]);
    exit();
}

if ($action === 'fetch_statutory_details') {
    $batch_ref = $_GET['batch_reference'] ?? '';
    $stmt = $conn->prepare("SELECT * FROM statutory_proposals WHERE BatchReference = ?");
    $stmt->bind_param("s", $batch_ref);
    $stmt->execute();
    $res = $stmt->get_result();
    $details = [];
    while ($row = $res->fetch_assoc()) { $details[] = $row; }
    echo json_encode(['success' => true, 'data' => $details]);
    exit();
}

// Existing Salary Scale Logic
if ($action === 'fetch_all') {
    $stmt = $conn->prepare("
        SELECT BatchReference, Status, MAX(CreatedAt) as SubmittedDate, COUNT(ProposalID) as TotalChanges 
        FROM salary_grade_proposals 
        WHERE ProposedBy = ?
        GROUP BY BatchReference, Status
        ORDER BY MAX(CreatedAt) DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $batches = [];
    while ($row = $result->fetch_assoc()) { $batches[] = $row; }
    echo json_encode(['success' => true, 'batches' => $batches]);
    exit();
}

if ($action === 'fetch_details') {
    $batch_ref = $_GET['batch_reference'] ?? '';
    $stmt = $conn->prepare("SELECT p.*, u1.Username as ProposedByName FROM salary_grade_proposals p LEFT JOIN useraccounts u1 ON p.ProposedBy = u1.AccountID WHERE p.BatchReference = ? LIMIT 1");
    $stmt->bind_param("s", $batch_ref);
    $stmt->execute();
    $details = $stmt->get_result()->fetch_assoc();
    if ($details) {
        $grades_stmt = $conn->prepare("SELECT p.ProposedMinSalary, p.ProposedMaxSalary, sg.GradeLevel, sg.GradeName, sg.MinSalary as OldMin, sg.MaxSalary as OldMax FROM salary_grade_proposals p JOIN salary_grades sg ON p.SalaryGradeID = sg.SalaryGradeID WHERE p.BatchReference = ?");
        $grades_stmt->bind_param("s", $batch_ref);
        $grades_stmt->execute();
        $grades_res = $grades_stmt->get_result();
        $grades = [];
        while ($row = $grades_res->fetch_assoc()) { $grades[] = $row; }
        echo json_encode(['success' => true, 'details' => $details, 'grades' => $grades]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Not found']);
    }
    exit();
}

// ==========================
// MERIT MATRIX TRACKING
// ==========================
if ($action === 'fetch_all_merit') {
    $stmt = $conn->prepare("
        SELECT BatchReference, Status, MAX(CreatedAt) as SubmittedDate, COUNT(ProposalID) as TotalChanges, Reason
        FROM merit_proposals 
        WHERE ProposedBy = ?
        GROUP BY BatchReference, Status, Reason
        ORDER BY MAX(CreatedAt) DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $batches = [];
    while ($row = $result->fetch_assoc()) {
        $batches[] = $row;
    }
    
    echo json_encode(['success' => true, 'batches' => $batches]);
    exit();
}

if ($action === 'fetch_merit_details') {
    $batch_ref = $_GET['batch_reference'] ?? '';
    $stmt = $conn->prepare("
        SELECT p.*, u.Username as ProposedByName,
               COALESCE(m.min_increase_pct, 0) as OldMinIncreasePct,
               COALESCE(m.max_increase_pct, 0) as OldMaxIncreasePct
        FROM merit_proposals p 
        LEFT JOIN useraccounts u ON p.ProposedBy = u.AccountID 
        LEFT JOIN merit_matrix_settings m ON p.performance_rating = m.performance_rating AND p.compa_ratio_range = m.compa_ratio_range AND m.period_id = 1
        WHERE p.BatchReference = ?
    ");
    $stmt->bind_param("s", $batch_ref);
    $stmt->execute();
    $res = $stmt->get_result();
    $details = [];
    while ($row = $res->fetch_assoc()) { $details[] = $row; }
    echo json_encode(['success' => true, 'data' => $details]);
    exit();
}

// ==========================
// ALLOWANCE TRACKING
// ==========================
if ($action === 'fetch_all_allowance') {
    $stmt = $conn->prepare("
        SELECT BatchReference, Status, MAX(CreatedAt) as SubmittedDate, COUNT(ProposalID) as TotalChanges, Reason
        FROM allowance_proposals 
        WHERE ProposedBy = ?
        GROUP BY BatchReference, Status, Reason
        ORDER BY MAX(CreatedAt) DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $batches = [];
    while ($row = $result->fetch_assoc()) {
        $batches[] = $row;
    }
    
    echo json_encode(['success' => true, 'batches' => $batches]);
    exit();
}

if ($action === 'fetch_allowance_details') {
    $batch_ref = $_GET['batch_reference'] ?? '';
    // We join to get Grade Name and Allowance Type Name, and get OldAmount from the current matrix
    $stmt = $conn->prepare("
        SELECT p.*, sg.GradeLevel, sg.GradeName, at.AllowanceName, u.Username as ProposedByName,
               COALESCE(m.Amount, 0) as OldAmount
        FROM allowance_proposals p 
        LEFT JOIN salary_grades sg ON p.SalaryGradeID = sg.SalaryGradeID
        LEFT JOIN allowance_types at ON p.AllowanceTypeID = at.AllowanceTypeID
        LEFT JOIN useraccounts u ON p.ProposedBy = u.AccountID 
        LEFT JOIN grade_allowances m ON p.AllowanceTypeID = m.AllowanceTypeID AND p.SalaryGradeID = m.SalaryGradeID
        WHERE p.BatchReference = ?
    ");
    $stmt->bind_param("s", $batch_ref);
    $stmt->execute();
    $res = $stmt->get_result();
    $details = [];
    while ($row = $res->fetch_assoc()) { $details[] = $row; }
    echo json_encode(['success' => true, 'data' => $details]);
    exit();
}

if ($action === 'fetch_simulation_details') {
    $cycle_name = $_GET['cycle_name'] ?? '';
    // Fetch the most recent proposal for this cycle
    $stmt = $conn->prepare("SELECT p.*, u.Username as ProposedByName FROM simulation_proposals p LEFT JOIN useraccounts u ON p.ProposedBy = u.AccountID WHERE p.CycleName = ? ORDER BY p.ProposalID DESC LIMIT 1");
    $stmt->bind_param("s", $cycle_name);
    $stmt->execute();
    $details = $stmt->get_result()->fetch_assoc();

    if ($details) {
        $proposalId = $details['ProposalID'];
        // Fetch line items
        $items_stmt = $conn->prepare("
            SELECT spi.OriginalSalary AS OldSalary, spi.MarketAdjustment, spi.MeritPct, spi.MeritAmount AS IncreaseAmount, spi.NewSalary,
                   e.FirstName, e.LastName, e.EmployeeCode, pos.PositionName
            FROM simulation_proposal_items spi 
            JOIN employee e ON spi.EmployeeID = e.EmployeeID
            JOIN employmentinformation ei ON e.EmployeeID = ei.EmployeeID
            LEFT JOIN positions pos ON ei.PositionID = pos.PositionID
            WHERE spi.ProposalID = ?
        ");
        $items_stmt->bind_param("i", $proposalId);
        $items_stmt->execute();
        $items_res = $items_stmt->get_result();
        $items = [];
        while ($row = $items_res->fetch_assoc()) {
            $items[] = $row;
        }

        echo json_encode(['success' => true, 'details' => $details, 'items' => $items]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Proposal not found for this cycle']);
    }
    exit();
}

if ($action === 'fetch_draft_details') {
    $draft_id = (int)($_GET['draft_id'] ?? 0);
    $stmt = $conn->prepare("SELECT * FROM simulation_drafts WHERE DraftID = ?");
    $stmt->bind_param("i", $draft_id);
    $stmt->execute();
    $draft = $stmt->get_result()->fetch_assoc();

    if ($draft) {
        $items = json_decode($draft['EmployeeData'] ?? '[]', true);
        // Normalize fields for the UI
        foreach ($items as &$item) {
            // Map common simulation fields to consistent keys
            $item['IncreaseAmount'] = $item['increaseAmount'] ?? $item['IncreaseAmount'] ?? 0;
            $item['NewSalary'] = $item['newSalary'] ?? $item['NewSalary'] ?? 0;
            $item['OldSalary'] = $item['baseSalary'] ?? $item['OldSalary'] ?? 0;
            $item['MeritPct'] = $item['meritPct'] ?? $item['MeritPct'] ?? 0;
            $item['FirstName'] = $item['firstName'] ?? $item['FirstName'] ?? '';
            $item['LastName'] = $item['lastName'] ?? $item['LastName'] ?? '';
            $item['EmployeeCode'] = $item['employeeCode'] ?? $item['EmployeeCode'] ?? '';
        }

        echo json_encode([
            'success' => true, 
            'details' => [
                'CycleName' => $draft['CycleName'],
                'Status' => $draft['Status'] ?? 'Draft',
                'CreatedAt' => $draft['DateStarted'],
                'UpdatedAt' => $draft['LastSaved'],
                'TotalBudget' => 5000000, // Default or fetch from config
                'TotalImpact' => 0, // Should be calculated or stored
                'Department' => 'Draft View',
                'ProposedByName' => $_SESSION['username'],
                'ProposalID' => 'DRAFT-' . $draft_id
            ], 
            'items' => $items
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Draft not found']);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>
