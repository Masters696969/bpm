<?php
require_once __DIR__ . '/../../../config/config.php';
session_start();

header('Content-Type: application/json; charset=utf-8');

function respond($ok, $data = [], $httpCode = 200) {
    http_response_code($httpCode);
    echo json_encode(array_merge(['ok' => $ok], $data));
    exit;
}

if (!isset($_SESSION['username'])) {
    respond(false, ['error' => 'Unauthorized'], 401);
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'save_timesheet') {
    $employeeId           = (int)($_POST['EmployeeID']             ?? 0);
    $periodId             = (int)($_POST['PeriodID']               ?? 0);
    $departmentId         = (int)($_POST['DepartmentID']           ?? 0);
    $positionId           = (int)($_POST['PositionID']             ?? 0);
    $isEligible           = (int)($_POST['IsEligibleForHolidayPay'] ?? 1);
    $regularHours         = (float)($_POST['RegularHours']         ?? 0);
    $overtimeHours        = (float)($_POST['OvertimeHours']        ?? 0);
    $nightDiffHours       = (float)($_POST['NightDiffHours']       ?? 0);
    $regHolidayHours      = (float)($_POST['RegHolidayHours']      ?? 0);
    $specHolidayHours     = (float)($_POST['SpecHolidayHours']     ?? 0);
    $unworkedHolidayHours = (float)($_POST['UnworkedHolidayHours'] ?? 0);
    $holidayOtHours       = (float)($_POST['HolidayOvertimeHours'] ?? 0);
    $lateMinutes          = (int)($_POST['LateMinutes']            ?? 0);
    $undertimeMinutes     = (int)($_POST['UndertimeMinutes']       ?? 0);
    $absencesHours        = (float)($_POST['AbsencesHours']        ?? 0);
    $paidLeaveHours       = (float)($_POST['PaidLeaveHours']       ?? 0);
    $unpaidLeaveHours     = (float)($_POST['UnpaidLeaveHours']     ?? 0);
    $notes                = trim((string)($_POST['Notes']          ?? ''));

    // Auto-compute TotalPayableHours
    $totalPayableHours = max(0.0, round(
        $regularHours + $overtimeHours + $nightDiffHours
        + $regHolidayHours + $specHolidayHours + $unworkedHolidayHours + $holidayOtHours
        + $paidLeaveHours - $absencesHours - $unpaidLeaveHours, 2
    ));

    if ($employeeId <= 0 || $periodId <= 0) {
        respond(false, ['error' => 'EmployeeID and PeriodID are required'], 400);
    }

    // Auto-fill dept/position from employment record
    $empInfo = $conn->query('SELECT DepartmentID, PositionID FROM employmentinformation WHERE EmployeeID=' . (int)$employeeId . ' ORDER BY EmploymentID DESC LIMIT 1');
    if ($empInfo && ($ei = $empInfo->fetch_assoc())) {
        if ($departmentId <= 0) $departmentId = (int)$ei['DepartmentID'];
        if ($positionId   <= 0) $positionId   = (int)$ei['PositionID'];
    }
    if ($departmentId <= 0 || $positionId <= 0) {
        respond(false, ['error' => 'Could not determine Department/Position for this employee.'], 400);
    }

    // Upsert: update if employee+period record exists, insert if not
    $existRes = $conn->query("SELECT SummaryID FROM timesheet_employee_summary WHERE EmployeeID=$employeeId AND PeriodID=$periodId LIMIT 1");
    if ($existRes && $existRes->num_rows > 0) {
        $sid = (int)$existRes->fetch_assoc()['SummaryID'];
        $stmt = $conn->prepare('UPDATE timesheet_employee_summary SET
            DepartmentID=?, PositionID=?, IsEligibleForHolidayPay=?,
            RegularHours=?, OvertimeHours=?, NightDiffHours=?,
            RegHolidayHours=?, SpecHolidayHours=?, UnworkedHolidayHours=?,
            HolidayOvertimeHours=?, LateMinutes=?, UndertimeMinutes=?,
            AbsencesHours=?, PaidLeaveHours=?, UnpaidLeaveHours=?,
            TotalPayableHours=?, Notes=?, UpdatedAt=NOW()
            WHERE SummaryID=?');
        $stmt->bind_param('iiiddddddiidddddsi',
            $departmentId, $positionId, $isEligible,
            $regularHours, $overtimeHours, $nightDiffHours,
            $regHolidayHours, $specHolidayHours, $unworkedHolidayHours,
            $holidayOtHours, $lateMinutes, $undertimeMinutes,
            $absencesHours, $paidLeaveHours, $unpaidLeaveHours,
            $totalPayableHours, $notes, $sid
        );
    } else {
        $nextId = 1;
        $idRes = $conn->query('SELECT COALESCE(MAX(SummaryID),0)+1 AS nid FROM timesheet_employee_summary');
        if ($idRes && ($idRow = $idRes->fetch_assoc())) $nextId = (int)$idRow['nid'];
        $stmt = $conn->prepare('INSERT INTO timesheet_employee_summary
            (SummaryID,PeriodID,EmployeeID,DepartmentID,PositionID,IsEligibleForHolidayPay,
             RegularHours,OvertimeHours,NightDiffHours,RegHolidayHours,SpecHolidayHours,
             UnworkedHolidayHours,HolidayOvertimeHours,LateMinutes,UndertimeMinutes,
             AbsencesHours,PaidLeaveHours,UnpaidLeaveHours,TotalPayableHours,Notes)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $stmt->bind_param('iiiiiiiddddddiidddds',
            $nextId,$periodId,$employeeId,$departmentId,$positionId,$isEligible,
            $regularHours,$overtimeHours,$nightDiffHours,$regHolidayHours,$specHolidayHours,
            $unworkedHolidayHours,$holidayOtHours,$lateMinutes,$undertimeMinutes,
            $absencesHours,$paidLeaveHours,$unpaidLeaveHours,$totalPayableHours,$notes
        );
    }

    if (!$stmt->execute()) {
        respond(false, ['error' => $stmt->error], 500);
    }
    $stmt->close();
    respond(true, ['message' => 'Timesheet saved successfully.']);
}

if ($action === 'create_batch') {
    $periodId   = isset($_POST['period_id']) ? (int)$_POST['period_id'] : 0;
    $periodType = $_POST['period_type'] ?? ''; // legacy fallback

    $now   = new DateTime('now');
    $year  = (int)$now->format('Y');
    $month = (int)$now->format('m');

    if ($periodId > 0) {
        // ── New flow: resolve dates directly from timesheet_period ──────────
        $pRow = $conn->query("SELECT StartDate, EndDate FROM timesheet_period WHERE PeriodID = $periodId AND IsArchived = 0 AND Status IN ('APPROVED','FINALIZED') LIMIT 1")->fetch_assoc();
        if (!$pRow) {
            respond(false, ['error' => 'Selected timesheet period not found or not eligible.'], 400);
        }
        $start   = new DateTime($pRow['StartDate']);
        $end     = new DateTime($pRow['EndDate']);
        $days    = (int)$start->diff($end)->days;
        // Determine pay type from period length
        $payType = ($days <= 16) ? 'Semi-Monthly' : 'Monthly';
    } elseif ($periodType === '1st_half') {
        $start   = new DateTime(sprintf('%04d-%02d-01', $year, $month));
        $end     = new DateTime(sprintf('%04d-%02d-15', $year, $month));
        $payType = 'Semi-Monthly';
    } elseif ($periodType === '2nd_half') {
        $start   = new DateTime(sprintf('%04d-%02d-16', $year, $month));
        $end     = (new DateTime(sprintf('%04d-%02d-01', $year, $month)))->modify('last day of this month');
        $payType = 'Semi-Monthly';
    } elseif ($periodType === 'monthly') {
        $start   = new DateTime(sprintf('%04d-%02d-01', $year, $month));
        $end     = (new DateTime(sprintf('%04d-%02d-01', $year, $month)))->modify('last day of this month');
        $payType = 'Monthly';
    } else {
        respond(false, ['error' => 'Invalid period type or period_id.'], 400);
    }


    // Find next available serial for this year to avoid duplicates
    $resSerial = $conn->query("SELECT MAX(CAST(SUBSTRING_INDEX(batch_code, '-', -1) AS UNSIGNED)) as last_serial FROM payroll_batches WHERE batch_code LIKE 'PR-$year-%'");
    $nextSerial = 1;
    if ($resSerial && ($rowSerial = $resSerial->fetch_assoc())) {
        $nextSerial = (int)$rowSerial['last_serial'] + 1;
    }
    $batchCode = sprintf('PR-%04d-%03d', $year, $nextSerial);

    $conn->begin_transaction();
    try {
        // Ensure simulation tax table exists (used to pull TRAIN W.Tax from simulation outputs)
        $conn->query("CREATE TABLE IF NOT EXISTS payroll_tax_simulation (
            employee_id INT(11) NOT NULL,
            tax_monthly DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            expected_monthly_net DECIMAL(15,2) DEFAULT NULL,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (employee_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        // Add expected_monthly_net column if it doesn't exist (for older table versions)
        @$conn->query("ALTER TABLE payroll_tax_simulation ADD COLUMN expected_monthly_net DECIMAL(15,2) DEFAULT NULL");

        // Attempt to hydrate payroll_tax_simulation from latest Approved simulation draft when the JSON contains it
        $draftRes = $conn->query("SELECT EmployeeData FROM simulation_drafts WHERE Status='Approved' ORDER BY LastSaved DESC LIMIT 1");
        if ($draftRes && ($drow = $draftRes->fetch_assoc()) && !empty($drow['EmployeeData'])) {
            $decoded = json_decode($drow['EmployeeData'], true);
            if (is_array($decoded)) {
                foreach ($decoded as $emp) {
                    $eid = isset($emp['EmployeeID']) ? (int)$emp['EmployeeID'] : 0;
                    if ($eid <= 0) continue;
                    $taxMonthly = null;
                    if (isset($emp['WTaxMonthly'])) $taxMonthly = (float)$emp['WTaxMonthly'];
                    if (isset($emp['WTAX_MONTHLY'])) $taxMonthly = (float)$emp['WTAX_MONTHLY'];
                    if (isset($emp['w_tax_monthly'])) $taxMonthly = (float)$emp['w_tax_monthly'];
                    if (isset($emp['tax_monthly'])) $taxMonthly = (float)$emp['tax_monthly'];

                    $netMonthly = null;
                    if (isset($emp['NetMonthly'])) $netMonthly = (float)$emp['NetMonthly'];
                    if (isset($emp['NET_MONTHLY'])) $netMonthly = (float)$emp['NET_MONTHLY'];
                    if (isset($emp['net_monthly'])) $netMonthly = (float)$emp['net_monthly'];
                    if (isset($emp['net_pay'])) $netMonthly = (float)$emp['net_pay'];

                    if ($taxMonthly === null && $netMonthly === null) continue;

                    $stmtHyd = $conn->prepare('INSERT INTO payroll_tax_simulation (employee_id, tax_monthly, expected_monthly_net) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE tax_monthly=VALUES(tax_monthly), expected_monthly_net=VALUES(expected_monthly_net)');
                    $taxMonthlyVal = $taxMonthly !== null ? round((float)$taxMonthly, 2) : 0.0;
                    $netMonthlyVal = $netMonthly !== null ? round((float)$netMonthly, 2) : null;
                    $stmtHyd->bind_param('idd', $eid, $taxMonthlyVal, $netMonthlyVal);
                    $stmtHyd->execute();
                    $stmtHyd->close();
                }
            }
        }

        $stmt = $conn->prepare('INSERT INTO payroll_batches (batch_code, period_start, period_end, pay_type, status, created_by) VALUES (?, ?, ?, ?, \'Processing\', ?)');
        $periodStart = $start->format('Y-m-d');
        $periodEnd = $end->format('Y-m-d');
        $createdBy = (int)($_SESSION['account_id'] ?? 0);
        $stmt->bind_param('ssssi', $batchCode, $periodStart, $periodEnd, $payType, $createdBy);
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
        $batchId = (int)$stmt->insert_id;
        $stmt->close();

        $employeesRes = $conn->query('SELECT e.EmployeeID, ei.BaseSalary, ei.SalaryGradeID, ei.SalaryType FROM employee e INNER JOIN employmentinformation ei ON ei.EmployeeID = e.EmployeeID');
        if (!$employeesRes) {
            throw new Exception($conn->error);
        }

        $sss = $conn->query('SELECT employee_share_pct, employer_share_pct, max_msc_monthly, wisp_threshold FROM sss_settings LIMIT 1')->fetch_assoc();
        $phil = $conn->query('SELECT employee_share_pct, employer_share_pct, salary_ceiling FROM philhealth_settings LIMIT 1')->fetch_assoc();
        $pag = $conn->query('SELECT employee_rate_pct, monthly_cap_ee, monthly_cap_er FROM pagibig_settings LIMIT 1')->fetch_assoc();
        $bir = $conn->query('SELECT tax_exempt_limit FROM bir_tax_settings LIMIT 1')->fetch_assoc();

        $sssEePct = $sss ? (float)$sss['employee_share_pct'] : 0.0;
        $sssErPct = $sss ? (float)$sss['employer_share_pct'] : 0.0;
        $sssMaxMscMonthly = $sss ? (float)$sss['max_msc_monthly'] : 0.0;
        $sssWispThreshold = $sss ? (float)$sss['wisp_threshold'] : 0.0;

        $philEePct = $phil ? (float)$phil['employee_share_pct'] : 0.0;
        $philErPct = $phil ? (float)$phil['employer_share_pct'] : 0.0;
        $philCeiling = $phil ? (float)$phil['salary_ceiling'] : 0.0;

        $pagEePct = $pag ? (float)$pag['employee_rate_pct'] : 0.0;
        $pagCapEeMonthly = $pag ? (float)$pag['monthly_cap_ee'] : 0.0;
        $pagCapErMonthly = $pag ? (float)$pag['monthly_cap_er'] : 0.0;

        $annualExempt = $bir ? (float)$bir['tax_exempt_limit'] : 0.0;

        $itemStmt = $conn->prepare('INSERT INTO payroll_batch_items (batch_id, employee_id, basic_pay, allowances_total, sss_regular_ee, sss_regular_er, sss_wisp_ee, sss_wisp_er, philhealth_ee, philhealth_er, pagibig_ee, pagibig_er, deductions_total, withholding_tax, net_pay, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $componentStmt = $conn->prepare('INSERT INTO payroll_item_components (item_id, component_type, component_name, amount) VALUES (?, ?, ?, ?)');

        while ($row = $employeesRes->fetch_assoc()) {
            $employeeId = (int)$row['EmployeeID'];
            $baseSalary = (float)$row['BaseSalary'];
            $salaryGradeId = (int)$row['SalaryGradeID'];
            $salaryType = $row['SalaryType'] ?? 'Monthly';

            // Determine matching timesheet periods for this batch date range
            $matchingPeriodIds = [];
            $pSql = "SELECT PeriodID FROM timesheet_period WHERE (StartDate BETWEEN '$periodStart' AND '$periodEnd') OR (EndDate BETWEEN '$periodStart' AND '$periodEnd') OR ('$periodStart' BETWEEN StartDate AND EndDate)";
            $pRes = $conn->query($pSql);
            if ($pRes) {
                while ($pRow = $pRes->fetch_assoc()) {
                    $matchingPeriodIds[] = (int)$pRow['PeriodID'];
                }
            }

            // Fetch timesheet data for the specific employee and matching periods
            $ts = null;
            if (!empty($matchingPeriodIds)) {
                $ids = implode(',', $matchingPeriodIds);
                $tsRes = $conn->query("SELECT
                    SUM(TotalPayableHours)      as TotalPayableHours,
                    SUM(RegularHours)            as RegularHours,
                    SUM(OvertimeHours)           as OvertimeHours,
                    SUM(NightDiffHours)          as NightDiffHours,
                    SUM(RegHolidayHours)         as RegHolidayHours,
                    SUM(SpecHolidayHours)        as SpecHolidayHours,
                    SUM(UnworkedHolidayHours)    as UnworkedHolidayHours,
                    SUM(HolidayOvertimeHours)    as HolidayOvertimeHours,
                    SUM(LateMinutes)             as LateMinutes,
                    SUM(UndertimeMinutes)        as UndertimeMinutes,
                    SUM(AbsencesHours)           as AbsencesHours,
                    SUM(PaidLeaveHours)          as PaidLeaveHours,
                    SUM(UnpaidLeaveHours)        as UnpaidLeaveHours
                    FROM timesheet_employee_summary
                    WHERE EmployeeID = $employeeId AND PeriodID IN ($ids)");
                if ($tsRes) {
                    $ts = $tsRes->fetch_assoc();
                    // If no rows were found, SUM returns NULL; check if any relevant value exists
                    if ($ts['RegularHours'] === null) $ts = null;
                }
            }

            // Fallback: if no matching period found, try the absolutely latest one (previous behavior)
            if (!$ts) {
                $tsRes = $conn->query('SELECT TotalPayableHours, RegularHours, OvertimeHours,
                    NightDiffHours, RegHolidayHours, SpecHolidayHours, UnworkedHolidayHours,
                    HolidayOvertimeHours, LateMinutes, UndertimeMinutes, AbsencesHours,
                    PaidLeaveHours, UnpaidLeaveHours
                    FROM timesheet_employee_summary
                    WHERE EmployeeID = ' . (int)$employeeId . ' ORDER BY UpdatedAt DESC LIMIT 1');
                if ($tsRes) {
                    $ts = $tsRes->fetch_assoc();
                }
            }

            $regularHours         = $ts ? (float)$ts['RegularHours']          : 0.0;
            $overtimeHours        = $ts ? (float)$ts['OvertimeHours']         : 0.0;
            $nightDiffHours       = $ts ? (float)$ts['NightDiffHours']        : 0.0;
            $regHolidayHours      = $ts ? (float)$ts['RegHolidayHours']       : 0.0;
            $specHolidayHours     = $ts ? (float)$ts['SpecHolidayHours']      : 0.0;
            $unworkedHolidayHours = $ts ? (float)$ts['UnworkedHolidayHours']  : 0.0;
            $holidayOtHours       = $ts ? (float)$ts['HolidayOvertimeHours']  : 0.0;
            $lateMinutes          = $ts ? (int)$ts['LateMinutes']             : 0;
            $undertimeMinutes     = $ts ? (int)$ts['UndertimeMinutes']        : 0;
            $absencesHours        = $ts ? (float)$ts['AbsencesHours']         : 0.0;
            $paidLeaveHours       = $ts ? (float)$ts['PaidLeaveHours']        : 0.0;
            $unpaidLeaveHours     = $ts ? (float)$ts['UnpaidLeaveHours']      : 0.0;

            // Calculate Basic Pay based on SalaryType
            $basicPay = 0.0;
            if ($salaryType === 'Monthly') {
                // Monthly Formula: If 'Monthly', take BaseSalary and divide by 2 (for semi-monthly payout).
                $basicPay = round($baseSalary / 2, 2);
            } elseif ($salaryType === 'Daily') {
                // Daily Formula: If 'Daily', calculate (BaseSalary / 22) * days_present. (Assume a standard 22-day work month).
                $daysPresent = $regularHours / 8;
                $basicPay = round(($baseSalary / 22) * $daysPresent, 2);
            } elseif ($salaryType === 'Hourly') {
                // Hourly Formula: If 'Hourly', calculate ((BaseSalary / 22) / 8) * hours_worked.
                $basicPay = round((($baseSalary / 22) / 8) * $regularHours, 2);
            } else {
                // Fallback / default
                $basicPay = $payType === 'Semi-Monthly' ? round($baseSalary / 2, 2) : round($baseSalary, 2);
            }

            // Timesheet adjustments (OT pay + late/undertime deductions + all additional pays)
            $standardHoursMonthly = 160.0;
            $hourlyRate = $standardHoursMonthly > 0 ? round($baseSalary / $standardHoursMonthly, 6) : 0.0;

            // Philippine Labor Code multipliers
            $overtimeMultiplier        = 1.25;  // Regular day OT: 125%
            $nightDiffRate             = 0.10;  // Night differential premium: +10%
            $regHolidayRate            = 2.00;  // Regular holiday: 200%
            $specHolidayRate           = 1.30;  // Special holiday: 130%
            $unworkedHolidayRate       = 1.00;  // Unworked regular holiday: 100% of daily rate
            $holidayOtMultiplier       = 2.60;  // OT on regular holiday: 260%

            // Compute all additional pays (monthly)
            $lateDeduction        = round((($lateMinutes + $undertimeMinutes) / 60.0) * $hourlyRate, 2);
            $overtimePay          = round($overtimeHours        * $hourlyRate * $overtimeMultiplier, 2);
            $nightDiffPay         = round($nightDiffHours       * $hourlyRate * $nightDiffRate, 2);
            $regHolidayPay        = round($regHolidayHours      * $hourlyRate * $regHolidayRate, 2);
            $specHolidayPay       = round($specHolidayHours     * $hourlyRate * $specHolidayRate, 2);
            $unworkedHolidayPay   = round($unworkedHolidayHours * $hourlyRate * $unworkedHolidayRate, 2);
            $holidayOtPay         = round($holidayOtHours       * $hourlyRate * $holidayOtMultiplier, 2);
            $absenceDeduction     = round($absencesHours        * $hourlyRate, 2);
            // Paid leave: employee is paid at regular rate, no deduction
            $paidLeavePay         = round($paidLeaveHours       * $hourlyRate, 2);
            // Unpaid leave: deducted at regular rate
            $unpaidLeaveDeduction = round($unpaidLeaveHours     * $hourlyRate, 2);

            // Scale to selected payroll period (timesheet values assumed monthly)
            if ($payType === 'Semi-Monthly') {
                $lateDeduction        = round($lateDeduction        / 2, 2);
                $overtimePay          = round($overtimePay          / 2, 2);
                $nightDiffPay         = round($nightDiffPay         / 2, 2);
                $regHolidayPay        = round($regHolidayPay        / 2, 2);
                $specHolidayPay       = round($specHolidayPay       / 2, 2);
                $unworkedHolidayPay   = round($unworkedHolidayPay   / 2, 2);
                $holidayOtPay         = round($holidayOtPay         / 2, 2);
                $absenceDeduction     = round($absenceDeduction     / 2, 2);
                $paidLeavePay         = round($paidLeavePay         / 2, 2);
                $unpaidLeaveDeduction = round($unpaidLeaveDeduction / 2, 2);
            }

            $allowancesTotal = 0.0;
            $allowanceRows = $conn->query('SELECT at.AllowanceName, ga.Amount FROM grade_allowances ga INNER JOIN allowance_types at ON at.AllowanceTypeID = ga.AllowanceTypeID WHERE ga.SalaryGradeID = ' . (int)$salaryGradeId);
            if ($allowanceRows) {
                while ($a = $allowanceRows->fetch_assoc()) {
                    $amt = (float)$a['Amount'];
                    if ($payType === 'Semi-Monthly') {
                        $amt = round($amt / 2, 2);
                    }
                    $allowancesTotal += $amt;
                }
            }

            $sssRegularBaseMonthly = $baseSalary;
            if ($sssWispThreshold > 0) {
                $sssRegularBaseMonthly = min($sssRegularBaseMonthly, $sssWispThreshold);
            }
            if ($sssMaxMscMonthly > 0) {
                $sssRegularBaseMonthly = min($sssRegularBaseMonthly, $sssMaxMscMonthly);
            }
            $sssRegularEeMonthly = round($sssRegularBaseMonthly * ($sssEePct / 100.0), 2);
            $sssRegularErMonthly = round($sssRegularBaseMonthly * ($sssErPct / 100.0), 2);

            $sssWispBaseMonthly = 0.0;
            if ($sssWispThreshold > 0 && $baseSalary > $sssWispThreshold) {
                $upper = $sssMaxMscMonthly > 0 ? min($baseSalary, $sssMaxMscMonthly) : $baseSalary;
                $sssWispBaseMonthly = max(0.0, $upper - $sssWispThreshold);
            }
            $sssWispEeMonthly = round($sssWispBaseMonthly * ($sssEePct / 100.0), 2);
            $sssWispErMonthly = round($sssWispBaseMonthly * ($sssErPct / 100.0), 2);

            $philBaseMonthly = $philCeiling > 0 ? min($baseSalary, $philCeiling) : $baseSalary;
            // PhilHealth settings are stored as shares (EE and ER). Total premium is EE+ER.
            $philEeMonthly = round($philBaseMonthly * ($philEePct / 100.0), 2);
            $philErMonthly = round($philBaseMonthly * ($philErPct / 100.0), 2);

            // Pag-IBIG: by 2026 standard capped values are used
            $pagEeMonthly = $pagCapEeMonthly > 0 ? $pagCapEeMonthly : round($baseSalary * ($pagEePct / 100.0), 2);
            $pagErMonthly = $pagCapErMonthly > 0 ? $pagCapErMonthly : $pagEeMonthly;
            $pagEeMonthly = round($pagEeMonthly, 2);
            $pagErMonthly = round($pagErMonthly, 2);

            $sssRegularEe = $payType === 'Semi-Monthly' ? round($sssRegularEeMonthly / 2, 2) : $sssRegularEeMonthly;
            $sssRegularEr = $payType === 'Semi-Monthly' ? round($sssRegularErMonthly / 2, 2) : $sssRegularErMonthly;
            $sssWispEe = $payType === 'Semi-Monthly' ? round($sssWispEeMonthly / 2, 2) : $sssWispEeMonthly;
            $sssWispEr = $payType === 'Semi-Monthly' ? round($sssWispErMonthly / 2, 2) : $sssWispErMonthly;
            $philEe = $payType === 'Semi-Monthly' ? round($philEeMonthly / 2, 2) : $philEeMonthly;
            $philEr = $payType === 'Semi-Monthly' ? round($philErMonthly / 2, 2) : $philErMonthly;
            $pagEe = $payType === 'Semi-Monthly' ? round($pagEeMonthly / 2, 2) : $pagEeMonthly;
            $pagEr = $payType === 'Semi-Monthly' ? round($pagErMonthly / 2, 2) : $pagErMonthly;

            // Withholding tax: use simulation value when available, else compute TRAIN monthly then adapt to semi-monthly.
            // NOTE: Tax base should subtract Late/UT before computing tax (prevents over-taxation).
            $withholdingTax = 0.0;

            // Total additional earnings from timesheet
            $timesheetEarnings = $overtimePay + $nightDiffPay + $regHolidayPay
                + $specHolidayPay + $unworkedHolidayPay + $holidayOtPay + $paidLeavePay;
            // Total additional deductions from timesheet
            $timesheetDeductions = $lateDeduction + $absenceDeduction + $unpaidLeaveDeduction;

            $taxableBase = ($basicPay + $allowancesTotal + $timesheetEarnings)
                - ($sssRegularEe + $sssWispEe + $philEe + $pagEe)
                - $timesheetDeductions;
            $taxableBase = round(max(0.0, $taxableBase), 2);

            // Optional: pull simulation W.Tax if table exists and has value
            $simTax = null;
            $simTaxRes = @$conn->query('SELECT tax_monthly FROM payroll_tax_simulation WHERE employee_id=' . (int)$employeeId . ' LIMIT 1');
            if ($simTaxRes) {
                $simRow = $simTaxRes->fetch_assoc();
                if ($simRow && $simRow['tax_monthly'] !== null) {
                    $simTax = (float)$simRow['tax_monthly'];
                }
            }

            if ($simTax !== null) {
                $withholdingTax = $payType === 'Semi-Monthly' ? round($simTax / 2, 2) : round($simTax, 2);
            } else {
                // TRAIN (monthly) brackets from your simulation JS, then convert to semi-monthly by dividing by 2.
                // Also enforce exempt cutoff derived from annualExempt (e.g., 20,833.33 semi-monthly => 0).
                $taxMonthly = 0.0;
                $t = $taxableBase;
                if ($payType === 'Semi-Monthly') {
                    $t = round($taxableBase * 2, 2);
                }

                if ($t > 666667) {
                    $taxMonthly = 183541.67 + ($t - 666667) * 0.35;
                } else if ($t > 166667) {
                    $taxMonthly = 33541.67 + ($t - 166667) * 0.30;
                } else if ($t > 66667) {
                    $taxMonthly = 8541.80 + ($t - 66667) * 0.25;
                } else if ($t > 33333) {
                    $taxMonthly = 1875 + ($t - 33333) * 0.20;
                } else if ($t > 20833) {
                    $taxMonthly = ($t - 20833) * 0.15;
                }
                $taxMonthly = round(max(0.0, $taxMonthly), 2);

                if ($payType === 'Semi-Monthly') {
                    $semiMonthlyExempt = $annualExempt > 0 ? round($annualExempt / 12 / 2, 2) : 20833.33;
                    if ($taxableBase <= $semiMonthlyExempt) {
                        $withholdingTax = 0.0;
                    } else {
                        $withholdingTax = round($taxMonthly / 2, 2);
                    }
                } else {
                    $withholdingTax = $taxMonthly;
                }
            }

            $deductionsTotal = round(
                $sssRegularEe +
                $sssWispEe +
                $philEe +
                $pagEe +
                $timesheetDeductions +
                $withholdingTax,
                2
            );

            $netPay = round(($basicPay + $allowancesTotal + $timesheetEarnings) - $deductionsTotal, 2);

            $status = 'Computed';
            $itemStmt->bind_param(
                'iiddddddddddddds',
                $batchId,
                $employeeId,
                $basicPay,
                $allowancesTotal,
                $sssRegularEe,
                $sssRegularEr,
                $sssWispEe,
                $sssWispEr,
                $philEe,
                $philEr,
                $pagEe,
                $pagEr,
                $deductionsTotal,
                $withholdingTax,
                $netPay,
                $status
            );
            if (!$itemStmt->execute()) {
                throw new Exception($itemStmt->error);
            }
            $itemId = (int)$itemStmt->insert_id;

            // Components: allowances
            if ($allowanceRows) {
                $allowanceRows->data_seek(0);
                while ($a = $allowanceRows->fetch_assoc()) {
                    $compType = 'Allowance';
                    $compName = $a['AllowanceName'];
                    $compAmt = (float)$a['Amount'];
                    $componentStmt->bind_param('issd', $itemId, $compType, $compName, $compAmt);
                    if (!$componentStmt->execute()) {
                        throw new Exception($componentStmt->error);
                    }
                }
            }

            // --- All timesheet-sourced earnings as components ---
            $tsEarningComponents = [
                'Overtime Pay'             => $overtimePay,
                'Night Differential Pay'   => $nightDiffPay,
                'Regular Holiday Pay'      => $regHolidayPay,
                'Special Holiday Pay'      => $specHolidayPay,
                'Unworked Holiday Pay'     => $unworkedHolidayPay,
                'Holiday Overtime Pay'     => $holidayOtPay,
                'Paid Leave Pay'           => $paidLeavePay,
            ];
            foreach ($tsEarningComponents as $compLabel => $compAmt) {
                if ($compAmt > 0) {
                    $cType = 'Allowance';
                    $componentStmt->bind_param('issd', $itemId, $cType, $compLabel, $compAmt);
                    if (!$componentStmt->execute()) {
                        throw new Exception($componentStmt->error);
                    }
                }
            }

            // Components: deductions
            $dType = 'Deduction';
            if ($sssRegularEe > 0) {
                $n1 = 'SSS Regular (EE)';
                $componentStmt->bind_param('issd', $itemId, $dType, $n1, $sssRegularEe);
                if (!$componentStmt->execute()) {
                    throw new Exception($componentStmt->error);
                }
            }
            if ($sssRegularEr > 0) {
                $n1e = 'SSS Regular (ER)';
                $componentStmt->bind_param('issd', $itemId, $dType, $n1e, $sssRegularEr);
                if (!$componentStmt->execute()) {
                    throw new Exception($componentStmt->error);
                }
            }
            if ($sssWispEe > 0) {
                $nW = 'SSS WISP (EE)';
                $componentStmt->bind_param('issd', $itemId, $dType, $nW, $sssWispEe);
                if (!$componentStmt->execute()) {
                    throw new Exception($componentStmt->error);
                }
            }
            if ($sssWispEr > 0) {
                $nWe = 'SSS WISP (ER)';
                $componentStmt->bind_param('issd', $itemId, $dType, $nWe, $sssWispEr);
                if (!$componentStmt->execute()) {
                    throw new Exception($componentStmt->error);
                }
            }
            $n2 = 'PhilHealth (EE)';
            $componentStmt->bind_param('issd', $itemId, $dType, $n2, $philEe);
            if (!$componentStmt->execute()) {
                throw new Exception($componentStmt->error);
            }
            if ($philEr > 0) {
                $n2e = 'PhilHealth (ER)';
                $componentStmt->bind_param('issd', $itemId, $dType, $n2e, $philEr);
                if (!$componentStmt->execute()) {
                    throw new Exception($componentStmt->error);
                }
            }
            $n3 = 'Pag-IBIG (EE)';
            $componentStmt->bind_param('issd', $itemId, $dType, $n3, $pagEe);
            if (!$componentStmt->execute()) {
                throw new Exception($componentStmt->error);
            }
            if ($pagEr > 0) {
                $n3e = 'Pag-IBIG (ER)';
                $componentStmt->bind_param('issd', $itemId, $dType, $n3e, $pagEr);
                if (!$componentStmt->execute()) {
                    throw new Exception($componentStmt->error);
                }
            }

            // --- All timesheet-sourced deductions as components ---
            $tsDeductionComponents = [
                'Late/Undertime'     => $lateDeduction,
                'Absences'           => $absenceDeduction,
                'Unpaid Leave'       => $unpaidLeaveDeduction,
            ];
            foreach ($tsDeductionComponents as $dLabel => $dAmt) {
                if ($dAmt > 0) {
                    $componentStmt->bind_param('issd', $itemId, $dType, $dLabel, $dAmt);
                    if (!$componentStmt->execute()) {
                        throw new Exception($componentStmt->error);
                    }
                }
            }

            if ($withholdingTax > 0) {
                $n5 = 'Withholding Tax';
                $componentStmt->bind_param('issd', $itemId, $dType, $n5, $withholdingTax);
                if (!$componentStmt->execute()) {
                    throw new Exception($componentStmt->error);
                }
            }
        }

        $employeesRes->free();
        $itemStmt->close();
        $componentStmt->close();

        // ── Mark the source timesheet period as consumed so it no longer
        //    appears in the "Initialize Payroll" dropdown (list_periods only
        //    shows APPROVED/FINALIZED — PAYROLL_PROCESSED is excluded).
        if ($periodId > 0) {
            $conn->query("UPDATE timesheet_period SET Status = 'PAYROLL_PROCESSED' WHERE PeriodID = $periodId");
        }

        $conn->commit();
        respond(true, ['batch_id' => $batchId, 'batch_code' => $batchCode]);


    } catch (Throwable $e) {
        $conn->rollback();
        respond(false, ['error' => $e->getMessage()], 500);
    }
}

if ($action === 'list_periods') {
    // Return timesheet periods that have summary data and are eligible for payroll
    $res = $conn->query("
        SELECT tp.PeriodID, tp.StartDate, tp.EndDate, tp.Status,
               d.DepartmentName,
               COUNT(tes.SummaryID) as EmpCount
        FROM timesheet_period tp
        INNER JOIN timesheet_employee_summary tes ON tes.PeriodID = tp.PeriodID
        LEFT JOIN department d ON d.DepartmentID = tp.DepartmentID
        WHERE tp.IsArchived = 0
          AND tp.Status IN ('APPROVED', 'FINALIZED')
        GROUP BY tp.PeriodID
        ORDER BY tp.StartDate DESC
    ");
    if (!$res) {
        respond(false, ['error' => $conn->error], 500);
    }
    $rows = [];
    while ($r = $res->fetch_assoc()) $rows[] = $r;
    respond(true, ['periods' => $rows]);
}

if ($action === 'list_batches') {

    $res = $conn->query('SELECT b.id, b.batch_code, b.period_start, b.period_end, b.pay_type, b.status, COALESCE(SUM(i.net_pay),0) AS total_distributed FROM payroll_batches b LEFT JOIN payroll_batch_items i ON i.batch_id = b.id GROUP BY b.id ORDER BY b.id DESC');
    if (!$res) {
        respond(false, ['error' => $conn->error], 500);
    }
    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $rows[] = $r;
    }
    respond(true, ['batches' => $rows]);
}

if ($action === 'stats') {
    $totalPayroll = 0.0;
    $totalRes = $conn->query("SELECT COALESCE(SUM(net_pay),0) AS total FROM payroll_batch_items");
    if ($totalRes && ($tRow = $totalRes->fetch_assoc())) {
        $totalPayroll = (float)$tRow['total'];
    }

    $employeeCount = 0;
    $empRes = $conn->query("SELECT COUNT(DISTINCT employee_id) AS cnt FROM payroll_batch_items");
    if ($empRes && ($eRow = $empRes->fetch_assoc())) {
        $employeeCount = (int)$eRow['cnt'];
    }

    $pendingCount = 0;
    $pendRes = $conn->query("SELECT COUNT(*) AS cnt FROM payroll_batches WHERE status IN ('Processing','Pending')");
    if ($pendRes && ($pRow = $pendRes->fetch_assoc())) {
        $pendingCount = (int)$pRow['cnt'];
    }

    $nextRun = '--';
    $nextRes = $conn->query("SELECT period_start FROM payroll_batches WHERE status IN ('Processing','Pending') ORDER BY period_start ASC LIMIT 1");
    if ($nextRes && ($nRow = $nextRes->fetch_assoc()) && !empty($nRow['period_start'])) {
        $nextDate = new DateTime($nRow['period_start']);
        $now = new DateTime('now');
        $diff = $now->diff($nextDate);
        if ($diff->invert) {
            $nextRun = 'Overdue';
        } else {
            $days = $diff->days;
            if ($days === 0) {
                $nextRun = 'Today';
            } else {
                $nextRun = "In {$days} Days";
            }
        }
    }

    respond(true, [
        'total_payroll' => round($totalPayroll, 2),
        'employees' => $employeeCount,
        'pending' => $pendingCount,
        'next_run' => $nextRun
    ]);
}

if ($action === 'list_employees') {
    $batchId = (int)($_GET['batch_id'] ?? 0);
    if ($batchId <= 0) {
        respond(false, ['error' => 'batch_id required'], 400);
    }

    $sql = "SELECT i.id AS item_id, i.employee_id, i.basic_pay, i.allowances_total,
                   i.sss_regular_ee, i.sss_regular_er, i.sss_wisp_ee, i.sss_wisp_er,
                   i.philhealth_ee, i.philhealth_er, i.pagibig_ee, i.pagibig_er,
                   i.withholding_tax,
                   i.deductions_total, i.net_pay, i.status,
                   COALESCE(ot.amount, 0) AS overtime_pay,
                   COALESCE(lu.amount, 0) AS late_undertime,
                   NULL AS expected_monthly_net,
                   b.pay_type, b.budget_status, b.budget_requested_amount, b.budget_approved_amount, b.budget_finance_ref,
                   e.FirstName, e.LastName, e.EmployeeCode
            FROM payroll_batch_items i
            INNER JOIN employee e ON e.EmployeeID = i.employee_id
            INNER JOIN payroll_batches b ON b.id = i.batch_id
            LEFT JOIN (
                SELECT c.item_id, SUM(c.amount) AS amount
                FROM payroll_item_components c
                WHERE c.component_type='Allowance' AND c.component_name='Overtime Pay'
                GROUP BY c.item_id
            ) ot ON ot.item_id = i.id
            LEFT JOIN (
                SELECT c.item_id, SUM(c.amount) AS amount
                FROM payroll_item_components c
                WHERE c.component_type='Deduction' AND c.component_name='Late/Undertime'
                GROUP BY c.item_id
            ) lu ON lu.item_id = i.id
            WHERE i.batch_id = $batchId
            ORDER BY e.LastName, e.FirstName";

    $res = $conn->query($sql);
    if (!$res) {
        respond(false, ['error' => $conn->error], 500);
    }
    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $rows[] = $r;
    }
    respond(true, [
        'employees' => $rows,
        'batch_budget' => !empty($rows) ? [
            'status' => $rows[0]['budget_status'],
            'requested' => $rows[0]['budget_requested_amount'],
            'approved' => $rows[0]['budget_approved_amount'],
            'ref' => $rows[0]['budget_finance_ref']
        ] : null
    ]);
}

if ($action === 'finalize_batch') {
    $batchId = (int)($_POST['batch_id'] ?? 0);
    if ($batchId <= 0) {
        respond(false, ['error' => 'batch_id required'], 400);
    }

    // Update status to Pending Approval (handle both 'Processing' and empty status)
    $stmt = $conn->prepare("UPDATE payroll_batches SET status='Pending Approval' WHERE id=? AND (status='Processing' OR status='' OR status IS NULL)");
    $stmt->bind_param('i', $batchId);
    if (!$stmt->execute()) {
        respond(false, ['error' => $stmt->error], 500);
    }
    $affectedRows = $stmt->affected_rows;
    $stmt->close();
    
    if ($affectedRows === 0) {
        // Check if batch exists
        $checkRes = $conn->query("SELECT id, status FROM payroll_batches WHERE id=$batchId");
        if ($checkRes && ($checkRow = $checkRes->fetch_assoc())) {
            respond(false, ['error' => 'Batch already finalized or has different status', 'current_status' => $checkRow['status']], 400);
        }
        respond(false, ['error' => 'Batch not found'], 404);
    }
    
    respond(true, ['message' => 'Batch finalized successfully', 'affected_rows' => $affectedRows]);
}

if ($action === 'list_pending_approvals') {
    // Debug: show all batch statuses
    $allBatchesRes = $conn->query("SELECT id, batch_code, status FROM payroll_batches ORDER BY id DESC LIMIT 10");
    $allBatches = [];
    if ($allBatchesRes) {
        while ($bRow = $allBatchesRes->fetch_assoc()) {
            $allBatches[] = $bRow;
        }
    }
    
    // Include 'Pending Approval', 'Finalized', and empty status (needs finalization)
    $res = $conn->query("SELECT b.id, b.batch_code, b.period_start, b.period_end, b.pay_type, b.status, COALESCE(SUM(i.net_pay),0) AS total_distributed, COUNT(i.id) AS employee_count FROM payroll_batches b LEFT JOIN payroll_batch_items i ON i.batch_id = b.id WHERE b.status IN ('Pending Approval', 'Finalized') OR b.status='' OR b.status IS NULL GROUP BY b.id ORDER BY b.id DESC");
    if (!$res) {
        respond(false, ['error' => $conn->error, 'debug_all_batches' => $allBatches], 500);
    }
    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $rows[] = $r;
    }
    respond(true, ['batches' => $rows, 'debug_all_batches' => $allBatches]);
}

if ($action === 'approve_batch') {
    $batchId = (int)($_POST['batch_id'] ?? 0);
    if ($batchId <= 0) {
        respond(false, ['error' => 'batch_id required'], 400);
    }

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("UPDATE payroll_batches SET status='Approved' WHERE id=?");
        $stmt->bind_param('i', $batchId);
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
        $affectedRows = $stmt->affected_rows;
        $stmt->close();

        if ($affectedRows > 0) {
            // Accounts Payable generation moved to Disbursement flow
        }

        $conn->commit();
        
        // Verify the update
        $checkRes = $conn->query("SELECT id, batch_code, status FROM payroll_batches WHERE id=$batchId");
        $updatedBatch = $checkRes ? $checkRes->fetch_assoc() : null;
        
        respond(true, ['message' => 'Batch approved and AP vouchers generated', 'affected_rows' => $affectedRows, 'updated_batch' => $updatedBatch]);
    } catch (Exception $e) {
        $conn->rollback();
        respond(false, ['error' => $e->getMessage()], 500);
    }
}

if ($action === 'disburse_batch') {
    $batchId = (int)($_POST['batch_id'] ?? 0);
    if ($batchId <= 0) {
        respond(false, ['error' => 'batch_id required'], 400);
    }

    // 1. Check if budget is approved
    $check = $conn->prepare("SELECT budget_status, status FROM payroll_batches WHERE id=?");
    $check->bind_param('i', $batchId);
    $check->execute();
    $batch = $check->get_result()->fetch_assoc();
    $check->close();

    if (!$batch) {
        respond(false, ['error' => 'Batch not found'], 404);
    }

    if ($batch['budget_status'] !== 'Approved' && $batch['budget_status'] !== 'Final') {
        respond(false, ['error' => 'Cannot disburse. Budget must be approved by Finance first.'], 400);
    }

    // 2. Perform Disbursement
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("UPDATE payroll_batches SET status='Disbursed', budget_status='Completed' WHERE id=?");
        $stmt->bind_param('i', $batchId);
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
        $stmt->close();

        // 3. Update all items in this batch to 'Disbursed'
        $stmt = $conn->prepare("UPDATE payroll_batch_items SET status='Disbursed' WHERE batch_id=?");
        $stmt->bind_param('i', $batchId);
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
        $stmt->close();

        // 4. Generate Accounts Payable Vouchers for Statutory Obligations
        $batchInfo = $conn->query("SELECT batch_code FROM payroll_batches WHERE id=$batchId")->fetch_assoc();
        $batchCode = $batchInfo['batch_code'] ?? "Batch #$batchId";

        // Aggregate statutory components
        $sqlAgg = "SELECT 
                    SUM(basic_pay + allowances_total) as gross_total,
                    SUM(sss_regular_ee + sss_regular_er + sss_wisp_ee + sss_wisp_er) as sss_total,
                    SUM(philhealth_ee + philhealth_er) as philhealth_total,
                    SUM(pagibig_ee + pagibig_er) as pagibig_total,
                    SUM(withholding_tax) as tax_total,
                    SUM(net_pay) as net_total
                   FROM payroll_batch_items 
                   WHERE batch_id = $batchId";
        
        $aggRes = $conn->query($sqlAgg);
        $totals = $aggRes->fetch_assoc();
        
        $grossTotal = (float)($totals['gross_total'] ?? 0);
        $netTotal = (float)($totals['net_total'] ?? 0);
        
        // Prepare Journal Entry data
        $journalNo = 'JV-PAY-' . date('Ymd') . '-' . substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 4);
        $journalData = [
            'journal_number' => $journalNo,
            'journal_date' => date('Y-m-d'),
            'reference_number' => $batchCode,
            'description' => "Payroll Disbursement for Batch $batchCode",
            'journal_type' => 'Payroll',
            'source_type' => 'disbursement',
            'total_debits' => $grossTotal,
            'total_credits' => $grossTotal, // Balanced
            'status' => 'Posted'
        ];
        
        // 1. Fetch individual employee components for detailed AP sync
        $sqlDetails = "SELECT 
                           e.FirstName, e.LastName,
                           i.sss_regular_ee, i.sss_regular_er, i.sss_wisp_ee, i.sss_wisp_er,
                           i.philhealth_ee, i.philhealth_er,
                           i.pagibig_ee, i.pagibig_er,
                           i.withholding_tax, i.net_pay
                       FROM payroll_batch_items i
                       JOIN employee e ON e.EmployeeID = i.employee_id
                       WHERE i.batch_id = $batchId";
        
        $detailsRes = $conn->query($sqlDetails);
        if (!$detailsRes) {
            throw new Exception("Details fetch failed: " . $conn->error);
        }

        $payables = [];
        while ($row = $detailsRes->fetch_assoc()) {
            $fullName = $row['LastName'] . ', ' . $row['FirstName'];
            
            // SSS
            $sss = (float)$row['sss_regular_ee'] + (float)$row['sss_regular_er'] + (float)$row['sss_wisp_ee'] + (float)$row['sss_wisp_er'];
            if ($sss > 0) {
                $payables[] = [
                    'employee_name' => $fullName,
                    'payee_name' => 'Social Security System',
                    'category' => 'SSS',
                    'description' => "SSS Contribution for $batchCode",
                    'amount' => $sss
                ];
            }

            // PhilHealth
            $ph = (float)$row['philhealth_ee'] + (float)$row['philhealth_er'];
            if ($ph > 0) {
                $payables[] = [
                    'employee_name' => $fullName,
                    'payee_name' => 'PhilHealth',
                    'category' => 'PhilHealth',
                    'description' => "PhilHealth Contribution for $batchCode",
                    'amount' => $ph
                ];
            }

            // Pag-IBIG
            $pi = (float)$row['pagibig_ee'] + (float)$row['pagibig_er'];
            if ($pi > 0) {
                $payables[] = [
                    'employee_name' => $fullName,
                    'payee_name' => 'Pag-IBIG Fund',
                    'category' => 'PagIBIG',
                    'description' => "Pag-IBIG Contribution for $batchCode",
                    'amount' => $pi
                ];
            }

            // Tax
            $tax = (float)$row['withholding_tax'];
            if ($tax > 0) {
                $payables[] = [
                    'employee_name' => $fullName,
                    'payee_name' => 'Bureau of Internal Revenue',
                    'category' => 'BIR',
                    'description' => "Withholding Tax for $batchCode",
                    'amount' => $tax
                ];
            }

            // Net Pay (Optional, usually batch total is enough but user asked for detailed)
            // For now, let's keep net pay as individual entries too if requested
            $net = (float)$row['net_pay'];
            if ($net > 0) {
                $payables[] = [
                    'employee_name' => $fullName,
                    'payee_name' => 'Employee Disbursement',
                    'category' => 'Other',
                    'description' => "Net Payroll for $batchCode",
                    'amount' => $net
                ];
            }
        }

        // 1. SYNC ACCOUNTS PAYABLE (Detailed Employee Deductions)
        $apUrl = 'http://10.112.107.207/microfinancee/modules/finance/receive_accounts_payable.php';
        $apPayload = [
            'batch_id' => $batchId,
            'payables' => $payables
        ];
        
        $chAP = curl_init($apUrl);
        curl_setopt_array($chAP, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($apPayload),
            CURLOPT_TIMEOUT => 20
        ]);
        $apResponse = curl_exec($chAP);
        $apError = curl_error($chAP);
        curl_close($chAP);

        // 2. SYNC GENERAL LEDGER (Payroll Cost Summary)
        $glUrl = 'http://10.112.107.207/microfinancee/modules/ledger/receive_hr_summarycost.php';
        $glPayload = [
            'batch_id' => $batchId,
            'total_amount' => $journalData['total_debits']
        ];

        $chGL = curl_init($glUrl);
        curl_setopt_array($chGL, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($glPayload),
            CURLOPT_TIMEOUT => 10
        ]);
        $glResponse = curl_exec($chGL);
        $glError = curl_error($chGL);
        curl_close($chGL);

        $apSuccess = false;
        if ($apResponse) {
            $apData = json_decode($apResponse, true);
            if (isset($apData['success']) && $apData['success']) {
                $apSuccess = true;
            } else {
                error_log("AP Sync Rejection: " . $apResponse);
            }
        } else {
            error_log("AP Sync cURL Error: " . $apError);
        }

        $glSuccess = false;
        if ($glResponse) {
            $glData = json_decode($glResponse, true);
            if (isset($glData['success']) && $glData['success']) {
                $glSuccess = true;
            } else {
                error_log("GL Sync Rejection: " . $glResponse);
            }
        } else {
            error_log("GL Sync cURL Error: " . $glError);
        }

        // Local Persistence: Accounts Payable
        $apInsert = $conn->prepare("INSERT INTO accounts_payable (batch_id, employee_name, payee_name, category, description, amount, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
        foreach ($payables as $ap) {
            $apInsert->bind_param('issssd', $batchId, $ap['employee_name'], $ap['payee_name'], $ap['category'], $ap['description'], $ap['amount']);
            $apInsert->execute();
        }
        $apInsert->close();

        // Local Persistence: Journal Entry
        $jeInsert = $conn->prepare("INSERT INTO journal_entries (journal_number, journal_date, reference_number, description, journal_type, source_type, source_id, total_debits, total_credits, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $jeInsert->bind_param('ssssssiddd', 
            $journalData['journal_number'],
            $journalData['journal_date'],
            $journalData['reference_number'],
            $journalData['description'],
            $journalData['journal_type'],
            $journalData['source_type'],
            $batchId,
            $journalData['total_debits'],
            $journalData['total_credits'],
            $journalData['status']
        );
        $jeInsert->execute();
        $jeInsert->close();

        $conn->commit();
        
        $msg = 'Payroll disbursed successfully!';
        if ($apSuccess && $glSuccess) {
            $msg .= ' Both AP and GL entries synced to Finance.';
        } else {
            $errors = [];
            if (!$apSuccess) $errors[] = "AP: " . ($apError ?: ($apResponse ?: "Remote rejection"));
            if (!$glSuccess) $errors[] = "GL: " . ($glError ?: ($glResponse ?: "Remote rejection"));
            $msg .= ' Note: Finance sync partial/failed: ' . implode('; ', $errors) . '. Recorded locally.';
        }
        respond(true, ['message' => $msg, 'item_count' => count($payables)]);

    } catch (Exception $e) {
        $conn->rollback();
        respond(false, ['error' => $e->getMessage()], 500);
    }
}

if ($action === 'reject_batch') {
    $batchId = (int)($_POST['batch_id'] ?? 0);
    if ($batchId <= 0) {
        respond(false, ['error' => 'batch_id required'], 400);
    }

    $stmt = $conn->prepare("UPDATE payroll_batches SET status='Rejected' WHERE id=?");
    $stmt->bind_param('i', $batchId);
    if (!$stmt->execute()) {
        respond(false, ['error' => $stmt->error], 500);
    }
    $stmt->close();
    respond(true);
}

if ($action === 'employee_payslips') {
    $employeeId = 0;
    
    // Get employee_id from useraccounts using session user_id
    $userId = (int)($_SESSION['user_id'] ?? 0);
    $username = $_SESSION['username'] ?? '';
    
    // Debug info
    $debug = [
        'session_user_id' => $userId,
        'session_username' => $username
    ];
    
    if ($userId > 0) {
        $empRes = $conn->query("SELECT EmployeeID FROM useraccounts WHERE AccountID = $userId LIMIT 1");
        if ($empRes && ($empRow = $empRes->fetch_assoc())) {
            $employeeId = (int)$empRow['EmployeeID'];
            $debug['found_employee_id'] = $employeeId;
        }
    }
    
    // Fallback: try session username matching employee name
    if ($employeeId <= 0 && $username) {
        $usernameEsc = $conn->real_escape_string($username);
        // Try matching by FirstName or combining FirstName + LastName
        $empRes = $conn->query("SELECT EmployeeID, FirstName, LastName FROM employee WHERE FirstName LIKE '%$usernameEsc%' OR CONCAT(FirstName, ' ', LastName) LIKE '%$usernameEsc%' LIMIT 1");
        if ($empRes && ($empRow = $empRes->fetch_assoc())) {
            $employeeId = (int)$empRow['EmployeeID'];
            $debug['fallback_match'] = $empRow;
        }
    }
    
    if ($employeeId <= 0) {
        respond(false, ['error' => 'Employee not identified. Please re-login.', 'debug' => $debug], 400);
    }

    // Check all batch statuses
    $batchStatusRes = $conn->query("SELECT id, batch_code, status FROM payroll_batches ORDER BY id DESC LIMIT 10");
    $batchStatuses = [];
    if ($batchStatusRes) {
        while ($bRow = $batchStatusRes->fetch_assoc()) {
            $batchStatuses[] = $bRow;
        }
    }
    $debug['all_batch_statuses'] = $batchStatuses;
    
    // Check if there are any approved batches first
    $approvedCheck = $conn->query("SELECT COUNT(*) as cnt FROM payroll_batches WHERE status IN ('Approved', 'Disbursed')");
    $approvedCount = $approvedCheck ? (int)$approvedCheck->fetch_assoc()['cnt'] : 0;
    $debug['approved_batches'] = $approvedCount;
    
    // Check if employee has any payroll items
    $itemsCheck = $conn->query("SELECT COUNT(*) as cnt FROM payroll_batch_items WHERE employee_id = $employeeId");
    $itemsCount = $itemsCheck ? (int)$itemsCheck->fetch_assoc()['cnt'] : 0;
    $debug['employee_payroll_items'] = $itemsCount;

    $sql = "SELECT i.id AS item_id, i.basic_pay, i.allowances_total, i.deductions_total, i.net_pay,
                   i.sss_regular_ee, i.sss_wisp_ee, i.philhealth_ee, i.pagibig_ee, i.withholding_tax,
                   b.batch_code, b.period_start, b.period_end, b.pay_type, b.status,
                   COALESCE(ot.amount, 0) AS overtime_pay,
                   COALESCE(lu.amount, 0) AS late_undertime
            FROM payroll_batch_items i
            INNER JOIN payroll_batches b ON b.id = i.batch_id
            LEFT JOIN (
                SELECT c.item_id, SUM(c.amount) AS amount
                FROM payroll_item_components c
                WHERE c.component_type='Allowance' AND c.component_name='Overtime Pay'
                GROUP BY c.item_id
            ) ot ON ot.item_id = i.id
            LEFT JOIN (
                SELECT c.item_id, SUM(c.amount) AS amount
                FROM payroll_item_components c
                WHERE c.component_type='Deduction' AND c.component_name='Late/Undertime'
                GROUP BY c.item_id
            ) lu ON lu.item_id = i.id
            WHERE i.employee_id = $employeeId AND b.status IN ('Approved', 'Disbursed')
            ORDER BY b.period_start DESC";
    
    $res = $conn->query($sql);
    if (!$res) {
        respond(false, ['error' => $conn->error, 'debug' => $debug], 500);
    }
    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $rows[] = $r;
    }
    
    // Include debug info in response for troubleshooting
    respond(true, ['payslips' => $rows, 'debug' => $debug, 'employee_id' => $employeeId]);
}

respond(false, ['error' => 'Unknown action'], 400);
