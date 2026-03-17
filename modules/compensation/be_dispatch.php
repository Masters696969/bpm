<?php
require_once '../../config/config.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) && !isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Initialize the table
initializeMasterDataDispatchesTable($conn);

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? $_POST['action'] ?? $_GET['action'] ?? '';

try {
    if ($action === 'fetch_new_hires') {
        // Fetch employees with 'Probationary' status who haven't been dispatched or are pending
        $sql = "SELECT 
                    e.EmployeeID, e.EmployeeCode, e.FirstName, e.LastName, 
                    ei.HiringDate, ei.EmploymentStatus, d.DepartmentName, p.PositionName
                FROM employee e
                JOIN employmentinformation ei ON e.EmployeeID = ei.EmployeeID
                LEFT JOIN department d ON ei.DepartmentID = d.DepartmentID
                LEFT JOIN positions p ON ei.PositionID = p.PositionID
                LEFT JOIN master_data_dispatches mdd ON e.EmployeeID = mdd.EmployeeID
                WHERE (mdd.DispatchID IS NULL OR mdd.Status = 'Rejected')
                ORDER BY ei.HiringDate DESC";
        
        $result = $conn->query($sql);
        if (!$result) {
            $sql = str_replace('positions p', 'position p', $sql);
            $result = $conn->query($sql);
        }

        $employees = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $employees[] = $row;
            }
        }

        // Fetch Stats
        $stats = [
            'total_new' => count($employees),
            'today' => 0,
            'pending' => 0
        ];

        $todayResult = $conn->query("SELECT COUNT(*) as count FROM master_data_dispatches WHERE DATE(DispatchDate) = CURDATE()");
        if ($todayResult) $stats['today'] = $todayResult->fetch_assoc()['count'];

        $pendingResult = $conn->query("SELECT COUNT(*) as count FROM master_data_dispatches WHERE Status = 'Pending'");
        if ($pendingResult) $stats['pending'] = $pendingResult->fetch_assoc()['count'];

        echo json_encode([
            'success' => true, 
            'data' => $employees,
            'stats' => $stats
        ]);
        exit;

    } elseif ($action === 'dispatch_employee') {
        $employeeId = $input['employee_id'] ?? null;
        $dispatchedBy = $_SESSION['username'] ?? 'System';

        if (!$employeeId) {
            throw new Exception("Employee ID is required.");
        }

        // Check if already pending
        $check = $conn->prepare("SELECT DispatchID FROM master_data_dispatches WHERE EmployeeID = ? AND Status = 'Pending'");
        $check->bind_param("i", $employeeId);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            throw new Exception("This employee is already dispatched and pending intake.");
        }

        $stmt = $conn->prepare("INSERT INTO master_data_dispatches (EmployeeID, DispatchedBy, Status) VALUES (?, ?, 'Pending')");
        $stmt->bind_param("is", $employeeId, $dispatchedBy);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Employee data dispatched to Intake successfully.']);
        } else {
            throw new Exception("Failed to dispatch: " . $conn->error);
        }
        exit;

    } elseif ($action === 'dispatch_all') {
        $dispatchedBy = $_SESSION['username'] ?? 'System';

        // Fetch all employees that are NOT currently 'Received' or 'Pending' in master_data_dispatches
        $sql = "SELECT e.EmployeeID 
                FROM employee e
                JOIN employmentinformation ei ON e.EmployeeID = ei.EmployeeID
                LEFT JOIN master_data_dispatches mdd ON e.EmployeeID = mdd.EmployeeID
                WHERE (mdd.DispatchID IS NULL OR mdd.Status = 'Rejected')";
        
        $result = $conn->query($sql);
        if (!$result) {
            throw new Exception("Error fetching employees: " . $conn->error);
        }

        $insertedCount = 0;
        $now = date('Y-m-d H:i:s');
        if ($result->num_rows > 0) {
            $conn->begin_transaction();
            try {
                $stmt = $conn->prepare("INSERT INTO master_data_dispatches (EmployeeID, DispatchedBy, Status, DispatchDate) VALUES (?, ?, 'Pending', ?)");
                while ($row = $result->fetch_assoc()) {
                    $empId = $row['EmployeeID'];
                    $stmt->bind_param("iss", $empId, $dispatchedBy, $now);
                    $stmt->execute();
                    $insertedCount++;
                }
                $conn->commit();
            } catch (Exception $ex) {
                $conn->rollback();
                throw $ex;
            }
        }

        echo json_encode(['success' => true, 'message' => "Successfully dispatched $insertedCount employee records to Intake."]);
        exit;

    } elseif ($action === 'fetch_pending_dispatches') {
        $sql = "SELECT DraftID, CycleName, UpdatedAt, EmployeeData, Status 
                FROM simulation_drafts 
                WHERE Status = 'Submitted' 
                ORDER BY UpdatedAt DESC";
        $result = $conn->query($sql);
        $batches = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $employeeData = json_decode($row['EmployeeData'], true);
                $employeeCount = is_array($employeeData) ? count($employeeData) : 0;
                
                $batches[] = [
                    'DispatchedBy' => $row['CycleName'],
                    'DispatchDate' => $row['UpdatedAt'] ? $row['UpdatedAt'] : date('Y-m-d H:i:s'),
                    'EmployeeCount' => $employeeCount,
                    'Status' => $row['Status'],
                    'DraftID' => $row['DraftID']
                ];
            }
        }
        echo json_encode(['success' => true, 'data' => $batches]);
        exit;

    } elseif ($action === 'fetch_batch_employees') {
        $dispatchedBy = $_GET['dispatched_by'] ?? '';

        if (!$dispatchedBy) {
            throw new Exception("Cycle name is required.");
        }

        $sql = "SELECT EmployeeData FROM simulation_drafts WHERE CycleName = ? AND Status = 'Submitted'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $dispatchedBy);
        $stmt->execute();
        $result = $stmt->get_result();

        $employees = [];
        if ($row = $result->fetch_assoc()) {
            $employeeData = json_decode($row['EmployeeData'], true);
            if (is_array($employeeData)) {
                // Map to what the UI grid expects (FirstName, LastName, EmployeeCode, DepartmentName, PositionName)
                foreach ($employeeData as $emp) {
                    $nameParts = explode(' ', $emp['name'], 2);
                    $employees[] = [
                        'FirstName' => $nameParts[0],
                        'LastName' => $nameParts[1] ?? '',
                        'EmployeeCode' => 'ID: ' . $emp['EmployeeID'],
                        'DepartmentName' => $emp['department'],
                        'PositionName' => 'Prop. Salary: ₱' . number_format($emp['new_salary'], 2)
                    ];
                }
            }
        }
        echo json_encode(['success' => true, 'data' => $employees]);
        exit;

    } elseif ($action === 'process_batch_intake') {
        $dispatchedBy = $input['dispatched_by'] ?? null;
        $status = $input['status'] ?? null; // 'Received' or 'Rejected'
        $remarks = $input['remarks'] ?? '';

        if (!$dispatchedBy || !$status) {
            throw new Exception("Missing required batch information.");
        }

        $conn->begin_transaction();
        try {
            // If approved, update employmentinformation
            if ($status === 'Received') {
                $sql = "SELECT EmployeeData FROM simulation_drafts WHERE CycleName = ? AND Status = 'Submitted'";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $dispatchedBy);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($row = $result->fetch_assoc()) {
                    $employeeData = json_decode($row['EmployeeData'], true);
                    if (is_array($employeeData)) {
                        $update_stmt = $conn->prepare("UPDATE employmentinformation SET BaseSalary = ?, SalaryGradeID = ? WHERE EmployeeID = ?");
                        foreach ($employeeData as $emp) {
                            $baseSalary = $emp['new_salary'];
                            $gradeId = $emp['GradeID'];
                            $employeeId = $emp['EmployeeID'];
                            $update_stmt->bind_param("dii", $baseSalary, $gradeId, $employeeId);
                            $update_stmt->execute();
                        }
                    }
                }
            }

            // Update simulation status
            $newStatus = ($status === 'Received') ? 'Approved' : 'Rejected';
            $update_sim = $conn->prepare("UPDATE simulation_drafts SET Status = ? WHERE CycleName = ? AND Status = 'Submitted'");
            $update_sim->bind_param("ss", $newStatus, $dispatchedBy);
            $update_sim->execute();
            
            $conn->commit();
            $msg = ($status === 'Received') ? "Simulation data synced to Master Files." : "Simulation rejected.";
            echo json_encode(['success' => true, 'message' => $msg]);
        } catch (Exception $ex) {
            $conn->rollback();
            throw new Exception("Failed to process simulation: " . $ex->getMessage());
        }
        exit;
    } elseif ($action === 'fetch_employee_details') {
        $employeeId = $input['employee_id'] ?? $_GET['employee_id'] ?? null;
        if (!$employeeId) throw new Exception("Employee ID is required.");

        $sql = "SELECT e.*, ei.*, d.DepartmentName, p.PositionName 
                FROM employee e
                JOIN employmentinformation ei ON e.EmployeeID = ei.EmployeeID
                LEFT JOIN department d ON ei.DepartmentID = d.DepartmentID
                LEFT JOIN positions p ON ei.PositionID = p.PositionID
                WHERE e.EmployeeID = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $employeeId);
        $stmt->execute();
        $employee = $stmt->get_result()->fetch_assoc();

        if ($employee) {
            echo json_encode(['success' => true, 'data' => $employee]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Employee not found.']);
        }
        exit;
    } elseif ($action === 'fetch_dispatcher_summary') {
        $username = $_SESSION['username'] ?? 'Red Gin Baldon';
        $position = "Administrator"; 
        
        // Count pending
        $query = "SELECT COUNT(*) as count FROM simulation_drafts WHERE Status = 'Submitted'";
        $stmt = $conn->query($query);
        $pendingCount = $stmt->fetch_assoc()['count'];

        echo json_encode([
            'success' => true,
            'dispatcher' => [
                'name' => $username,
                'position' => $position,
                'date' => date('Y-m-d'),
                'time' => date('H:i:s'),
                'status' => 'Pending',
                'pending_count' => $pendingCount
            ]
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Invalid action: ' . $action]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
