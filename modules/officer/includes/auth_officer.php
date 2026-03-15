<?php
// modules/officer/includes/auth_officer.php

require_once __DIR__ . '/../../../config/config.php';

// -----------------------------
// Secure session bootstrap
// -----------------------------
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)
    );

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();
}

// -----------------------------
// Security headers
// -----------------------------
if (!headers_sent()) {
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
}

// -----------------------------
// Helper functions
// -----------------------------
if (!function_exists('officer_force_logout')) {
    function officer_force_logout(string $redirect = '../../login.php?expired=1'): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];

            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }

            session_destroy();
        }

        if (!headers_sent()) {
            header("Location: {$redirect}");
        }
        exit;
    }
}

if (!function_exists('officer_deny')) {
    function officer_deny(string $message = 'Forbidden: Officer access only.'): void
    {
        http_response_code(403);
        exit($message);
    }
}

// -----------------------------
// DB check
// -----------------------------
if (!isset($conn) || !($conn instanceof mysqli)) {
    http_response_code(500);
    exit('Database connection not available.');
}

$conn->set_charset('utf8mb4');

// -----------------------------
// Session timeout settings
// -----------------------------
$inactiveLimit = 900;    // 15 minutes inactivity
$absoluteLimit = 28800;  // 8 hours total lifetime

if (isset($_SESSION['last_activity']) && is_numeric($_SESSION['last_activity'])) {
    if ((time() - (int) $_SESSION['last_activity']) > $inactiveLimit) {
        officer_force_logout('../../login.php?expired=1');
    }
}
$_SESSION['last_activity'] = time();

if (!isset($_SESSION['login_time']) || !is_numeric($_SESSION['login_time'])) {
    $_SESSION['login_time'] = time();
} elseif ((time() - (int) $_SESSION['login_time']) > $absoluteLimit) {
    officer_force_logout('../../login.php?expired=1');
}

// -----------------------------
// Basic user-agent binding
// -----------------------------
$currentUserAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

if (!isset($_SESSION['user_agent'])) {
    $_SESSION['user_agent'] = $currentUserAgent;
} elseif (!hash_equals((string) $_SESSION['user_agent'], (string) $currentUserAgent)) {
    officer_force_logout('../../login.php?expired=1');
}

// -----------------------------
// Must be logged in
// -----------------------------
if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id']) || (int) $_SESSION['user_id'] <= 0) {
    officer_force_logout('../../login.php');
}

$accountId = (int) $_SESSION['user_id'];

// -----------------------------
// Allowed roles for officer pages
// -----------------------------
$allowedRoles = [
    'Department Officer',
    'Supervisor',
    'Financial Officer',
    'Finance Officer',
    'Logistics Officer',
    'HR Officer',
    'HR Manager',
    'HR Staff'
];

// -----------------------------
// Get trusted account + employee + department + position + roles from DB
// Source of truth:
// useraccounts -> EmployeeID
// employmentinformation -> DepartmentID, PositionID
// useraccountroles + roles -> RoleName
// -----------------------------
$sql = "
    SELECT
        ua.AccountID,
        ua.EmployeeID,
        ua.AccountStatus,
        ei.DepartmentID,
        ei.PositionID,
        GROUP_CONCAT(DISTINCT r.RoleName ORDER BY r.RoleName SEPARATOR '||') AS RoleNames
    FROM useraccounts ua
    INNER JOIN employmentinformation ei
        ON ei.EmployeeID = ua.EmployeeID
    LEFT JOIN useraccountroles uar
        ON uar.AccountID = ua.AccountID
    LEFT JOIN roles r
        ON r.RoleID = uar.RoleID
    WHERE ua.AccountID = ?
    GROUP BY ua.AccountID, ua.EmployeeID, ua.AccountStatus, ei.DepartmentID, ei.PositionID
    LIMIT 1
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    exit('Failed to prepare officer auth query.');
}

$stmt->bind_param('i', $accountId);
$stmt->execute();
$result = $stmt->get_result();
$row = ($result && $result->num_rows === 1) ? $result->fetch_assoc() : null;
$stmt->close();

if (!$row) {
    officer_force_logout('../../login.php?error=account_not_linked');
}

if (($row['AccountStatus'] ?? '') !== 'Active') {
    officer_force_logout('../../login.php?error=inactive_account');
}

$employeeId   = isset($row['EmployeeID']) ? (int) $row['EmployeeID'] : 0;
$departmentId = isset($row['DepartmentID']) ? (int) $row['DepartmentID'] : 0;
$positionId   = isset($row['PositionID']) ? (int) $row['PositionID'] : 0;
$roleNamesRaw = $row['RoleNames'] ?? '';

if ($employeeId <= 0 || $departmentId <= 0) {
    officer_force_logout('../../login.php?error=employment_not_configured');
}

// -----------------------------
// Parse roles
// -----------------------------
$userRoles = [];
if ($roleNamesRaw !== '') {
    $userRoles = array_values(array_filter(array_map('trim', explode('||', $roleNamesRaw))));
}

if (empty($userRoles)) {
    officer_deny('Forbidden: No assigned role found.');
}

// -----------------------------
// Check if user has any allowed officer role
// -----------------------------
$isAllowed   = false;
$matchedRole = '';

foreach ($userRoles as $roleName) {
    foreach ($allowedRoles as $allowedRole) {
        if (strcasecmp(trim($roleName), trim($allowedRole)) === 0) {
            $isAllowed   = true;
            $matchedRole = $roleName;
            break 2;
        }
    }
}

if (!$isAllowed) {
    officer_deny();
}

// -----------------------------
// Refresh trusted session values from DB
// -----------------------------
$_SESSION['user_id']       = $accountId;
$_SESSION['account_id']    = $accountId;
$_SESSION['employee_id']   = $employeeId;
$_SESSION['department_id'] = $departmentId;
$_SESSION['position_id']   = $positionId;
$_SESSION['user_roles']    = $userRoles;
$_SESSION['user_role']     = $matchedRole;

// -----------------------------
// Optional constants
// -----------------------------
if (!defined('OFFICER_ACCOUNT_ID')) {
    define('OFFICER_ACCOUNT_ID', $accountId);
}
if (!defined('OFFICER_EMPLOYEE_ID')) {
    define('OFFICER_EMPLOYEE_ID', $employeeId);
}
if (!defined('OFFICER_DEPARTMENT_ID')) {
    define('OFFICER_DEPARTMENT_ID', $departmentId);
}
if (!defined('OFFICER_POSITION_ID')) {
    define('OFFICER_POSITION_ID', $positionId);
}
if (!defined('OFFICER_ROLE')) {
    define('OFFICER_ROLE', $matchedRole);
}
?>