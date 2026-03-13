<?php
require_once '../../../config/config.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) && !isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Function to ensure the master_data_dispatches table exists
function initializeMasterDataDispatchesTable($conn) {
    $tableCheck = $conn->query("SHOW TABLES LIKE 'master_data_dispatches'");
    if ($tableCheck->num_rows == 0) {
        $createTableQuery = "
            CREATE TABLE `master_data_dispatches` (
              `DispatchID` int(11) NOT NULL AUTO_INCREMENT,
              `EmployeeID` int(11) NOT NULL,
              `DispatchedBy` varchar(255) NOT NULL,
              `DispatchDate` datetime DEFAULT current_timestamp(),
              `Status` enum('Pending','Received','Rejected') DEFAULT 'Pending',
              `Remarks` text DEFAULT NULL,
              PRIMARY KEY (`DispatchID`),
              KEY `EmployeeID` (`EmployeeID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ";
        if (!$conn->query($createTableQuery)) {
            error_log("Failed to create master_data_dispatches table: " . $conn->error);
        }
    }
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
        // Group by DispatchedBy ONLY to ensure 1 row per dispatcher
        $sql = "SELECT 
                    mdd.DispatchedBy, 
                    MAX(mdd.DispatchDate) as DispatchDate,
                    COUNT(mdd.EmployeeID) as EmployeeCount,
                    mdd.Status
                FROM master_data_dispatches mdd
                WHERE mdd.Status = 'Pending'
                GROUP BY mdd.DispatchedBy
                ORDER BY DispatchDate DESC";
        
        $result = $conn->query($sql);
        $batches = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $batches[] = $row;
            }
        }
        echo json_encode(['success' => true, 'data' => $batches]);
        exit;

    } elseif ($action === 'fetch_batch_employees') {
        $dispatchedBy = $_GET['dispatched_by'] ?? '';

        if (!$dispatchedBy) {
            throw new Exception("Dispatcher name is required.");
        }

        $sql = "SELECT 
                    e.FirstName, e.LastName, e.EmployeeCode,
                    d.DepartmentName, p.PositionName
                FROM master_data_dispatches mdd
                JOIN employee e ON mdd.EmployeeID = e.EmployeeID
                JOIN employmentinformation ei ON e.EmployeeID = ei.EmployeeID
                LEFT JOIN department d ON ei.DepartmentID = d.DepartmentID
                LEFT JOIN positions p ON ei.PositionID = p.PositionID
                WHERE mdd.DispatchedBy = ? AND mdd.Status = 'Pending'";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $dispatchedBy);
        $stmt->execute();
        $result = $stmt->get_result();

        $employees = [];
        while ($row = $result->fetch_assoc()) {
            $employees[] = $row;
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

        $stmt = $conn->prepare("UPDATE master_data_dispatches SET Status = ?, Remarks = ? WHERE DispatchedBy = ? AND Status = 'Pending'");
        $stmt->bind_param("sss", $status, $remarks, $dispatchedBy);

        if ($stmt->execute()) {
            $msg = ($status === 'Received') ? "Batch synced successfully." : "Batch rejected.";
            echo json_encode(['success' => true, 'message' => $msg]);
        } else {
            throw new Exception("Failed to process batch: " . $conn->error);
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
        $query = "SELECT COUNT(e.EmployeeID) as count 
                  FROM employee e
                  JOIN employmentinformation ei ON e.EmployeeID = ei.EmployeeID
                  LEFT JOIN master_data_dispatches mdd ON e.EmployeeID = mdd.EmployeeID
                  WHERE (mdd.DispatchID IS NULL OR mdd.Status = 'Rejected')";
        
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
