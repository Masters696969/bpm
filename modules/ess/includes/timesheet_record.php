<?php
declare(strict_types=1);

function minutesBetween(?string $start, ?string $end): int
{
    if (!$start || !$end) {
        return 0;
    }

    $startTs = strtotime($start);
    $endTs = strtotime($end);

    if ($startTs === false || $endTs === false) {
        return 0;
    }

    if ($endTs < $startTs) {
        return 0;
    }

    return (int) floor(($endTs - $startTs) / 60);
}

function combineWorkDateTime(string $workDate, ?string $timeValue): ?string
{
    if (!$timeValue) {
        return null;
    }

    return $workDate . ' ' . $timeValue;
}

function computeScheduledDateTimes(string $workDate, ?string $startTime, ?string $endTime): array
{
    if (!$startTime || !$endTime) {
        return [null, null];
    }

    $start = combineWorkDateTime($workDate, $startTime);
    $end = combineWorkDateTime($workDate, $endTime);

    if (!$start || !$end) {
        return [null, null];
    }

    $startTs = strtotime($start);
    $endTs = strtotime($end);

    if ($startTs === false || $endTs === false) {
        return [null, null];
    }

    if ($endTs <= $startTs) {
        $endTs = strtotime($end . ' +1 day');
        if ($endTs === false) {
            return [null, null];
        }
    }

    return [
        date('Y-m-d H:i:s', $startTs),
        date('Y-m-d H:i:s', $endTs)
    ];
}

function overlapMinutes(int $aStart, int $aEnd, int $bStart, int $bEnd): int
{
    $start = max($aStart, $bStart);
    $end = min($aEnd, $bEnd);

    if ($end <= $start) {
        return 0;
    }

    return (int) floor(($end - $start) / 60);
}

function computeNightMinutes(?string $actualIn, ?string $actualOut, ?string $breakIn = null, ?string $breakOut = null): int
{
    if (!$actualIn || !$actualOut) {
        return 0;
    }

    $inTs = strtotime($actualIn);
    $outTs = strtotime($actualOut);

    if ($inTs === false || $outTs === false || $outTs <= $inTs) {
        return 0;
    }

    $segments = [
        [$inTs, $outTs]
    ];

    if ($breakIn && $breakOut) {
        $breakInTs = strtotime($breakIn);
        $breakOutTs = strtotime($breakOut);

        if ($breakInTs !== false && $breakOutTs !== false && $breakOutTs > $breakInTs) {
            $newSegments = [];

            foreach ($segments as [$segStart, $segEnd]) {
                if ($breakOutTs <= $segStart || $breakInTs >= $segEnd) {
                    $newSegments[] = [$segStart, $segEnd];
                    continue;
                }

                if ($breakInTs > $segStart) {
                    $newSegments[] = [$segStart, $breakInTs];
                }

                if ($breakOutTs < $segEnd) {
                    $newSegments[] = [$breakOutTs, $segEnd];
                }
            }

            $segments = $newSegments;
        }
    }

    $nightMinutes = 0;

    $firstDay = strtotime(date('Y-m-d 00:00:00', $inTs) . ' -1 day');
    $lastDay = strtotime(date('Y-m-d 00:00:00', $outTs) . ' +1 day');

    for ($dayTs = $firstDay; $dayTs <= $lastDay; $dayTs += 86400) {
        $window1Start = strtotime(date('Y-m-d 22:00:00', $dayTs));
        $window1End = strtotime(date('Y-m-d 23:59:59', $dayTs)) + 1;

        $window2Start = strtotime(date('Y-m-d 00:00:00', $dayTs));
        $window2End = strtotime(date('Y-m-d 06:00:00', $dayTs));

        foreach ($segments as [$segStart, $segEnd]) {
            $nightMinutes += overlapMinutes($segStart, $segEnd, $window1Start, $window1End);
            $nightMinutes += overlapMinutes($segStart, $segEnd, $window2Start, $window2End);
        }
    }

    return $nightMinutes;
}

function fetchLatestEventTime(mysqli $conn, int $sessionId, string $eventType): ?string
{
    $sql = "
        SELECT EventTime
        FROM attendance_event
        WHERE SessionID = ?
          AND EventType = ?
        ORDER BY EventTime DESC, EventID DESC
        LIMIT 1
    ";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return null;
    }

    $stmt->bind_param("is", $sessionId, $eventType);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return $row['EventTime'] ?? null;
}

function recomputeEmployeeSummary(mysqli $conn, int $periodId, int $employeeId): void
{
    $empSql = "
        SELECT DepartmentID, PositionID
        FROM employmentinformation
        WHERE EmployeeID = ?
        ORDER BY EmploymentID DESC
        LIMIT 1
    ";
    $empStmt = $conn->prepare($empSql);

    if (!$empStmt) {
        throw new Exception("Failed to prepare employment lookup: " . $conn->error);
    }

    $empStmt->bind_param("i", $employeeId);
    $empStmt->execute();
    $empResult = $empStmt->get_result();
    $empRow = $empResult ? $empResult->fetch_assoc() : null;
    $empStmt->close();

    if (!$empRow) {
        throw new Exception("Employment information not found for employee summary.");
    }

    $departmentId = (int) ($empRow['DepartmentID'] ?? 0);
    $positionId = (int) ($empRow['PositionID'] ?? 0);

    $sumSql = "
        SELECT
            COALESCE(SUM(RegularMinutes), 0) AS TotalRegularMinutes,
            COALESCE(SUM(OvertimeMinutes), 0) AS TotalOvertimeMinutes,
            COALESCE(SUM(NightDiffMinutes), 0) AS TotalNightDiffMinutes,
            COALESCE(SUM(LateMinutes), 0) AS TotalLateMinutes,
            COALESCE(SUM(UndertimeMinutes), 0) AS TotalUndertimeMinutes
        FROM timesheet_daily
        WHERE PeriodID = ?
          AND EmployeeID = ?
    ";
    $sumStmt = $conn->prepare($sumSql);

    if (!$sumStmt) {
        throw new Exception("Failed to prepare timesheet summary totals query: " . $conn->error);
    }

    $sumStmt->bind_param("ii", $periodId, $employeeId);
    $sumStmt->execute();
    $sumResult = $sumStmt->get_result();
    $sumRow = $sumResult ? $sumResult->fetch_assoc() : null;
    $sumStmt->close();

    $regularHours = round(((int) ($sumRow['TotalRegularMinutes'] ?? 0)) / 60, 2);
    $overtimeHours = round(((int) ($sumRow['TotalOvertimeMinutes'] ?? 0)) / 60, 2);
    $nightDiffHours = round(((int) ($sumRow['TotalNightDiffMinutes'] ?? 0)) / 60, 2);
    $lateMinutes = (int) ($sumRow['TotalLateMinutes'] ?? 0);
    $undertimeMinutes = (int) ($sumRow['TotalUndertimeMinutes'] ?? 0);

    $regHolidayHours = 0.00;
    $specHolidayHours = 0.00;
    $unworkedHolidayHours = 0.00;
    $holidayOvertimeHours = 0.00;
    $absencesHours = 0.00;
    $paidLeaveHours = 0.00;
    $unpaidLeaveHours = 0.00;

    $totalPayableHours = round(
        $regularHours +
        $overtimeHours +
        $nightDiffHours +
        $regHolidayHours +
        $specHolidayHours +
        $unworkedHolidayHours +
        $holidayOvertimeHours +
        $paidLeaveHours,
        2
    );

    $existingSql = "
        SELECT SummaryID
        FROM timesheet_employee_summary
        WHERE PeriodID = ?
          AND EmployeeID = ?
        LIMIT 1
    ";
    $existingStmt = $conn->prepare($existingSql);

    if (!$existingStmt) {
        throw new Exception("Failed to prepare summary existence check: " . $conn->error);
    }

    $existingStmt->bind_param("ii", $periodId, $employeeId);
    $existingStmt->execute();
    $existingResult = $existingStmt->get_result();
    $existingRow = $existingResult ? $existingResult->fetch_assoc() : null;
    $existingStmt->close();

    if ($existingRow) {
        $summaryId = (int) $existingRow['SummaryID'];

        $updateSql = "
            UPDATE timesheet_employee_summary
            SET
                DepartmentID = ?,
                PositionID = ?,
                IsEligibleForHolidayPay = 1,
                RegularHours = ?,
                OvertimeHours = ?,
                NightDiffHours = ?,
                RegHolidayHours = ?,
                SpecHolidayHours = ?,
                UnworkedHolidayHours = ?,
                HolidayOvertimeHours = ?,
                LateMinutes = ?,
                UndertimeMinutes = ?,
                AbsencesHours = ?,
                PaidLeaveHours = ?,
                UnpaidLeaveHours = ?,
                TotalPayableHours = ?,
                Notes = ?
            WHERE SummaryID = ?
        ";
        $updateStmt = $conn->prepare($updateSql);

        if (!$updateStmt) {
            throw new Exception("Failed to prepare summary update: " . $conn->error);
        }

        $notes = "Auto-recomputed from attendance and timesheet daily.";

        $updateStmt->bind_param(
            "iidddddddiiddddsi",
            $departmentId,
            $positionId,
            $regularHours,
            $overtimeHours,
            $nightDiffHours,
            $regHolidayHours,
            $specHolidayHours,
            $unworkedHolidayHours,
            $holidayOvertimeHours,
            $lateMinutes,
            $undertimeMinutes,
            $absencesHours,
            $paidLeaveHours,
            $unpaidLeaveHours,
            $totalPayableHours,
            $notes,
            $summaryId
        );

        if (!$updateStmt->execute()) {
            throw new Exception("Failed to update timesheet employee summary: " . $updateStmt->error);
        }

        $updateStmt->close();
    } else {
        $insertSql = "
            INSERT INTO timesheet_employee_summary
            (
                PeriodID,
                EmployeeID,
                DepartmentID,
                PositionID,
                IsEligibleForHolidayPay,
                RegularHours,
                OvertimeHours,
                NightDiffHours,
                RegHolidayHours,
                SpecHolidayHours,
                UnworkedHolidayHours,
                HolidayOvertimeHours,
                LateMinutes,
                UndertimeMinutes,
                AbsencesHours,
                PaidLeaveHours,
                UnpaidLeaveHours,
                TotalPayableHours,
                Notes
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                1,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?
            )
        ";
        $insertStmt = $conn->prepare($insertSql);

        if (!$insertStmt) {
            throw new Exception("Failed to prepare summary insert: " . $conn->error);
        }

        $notes = "Auto-created from attendance and timesheet daily.";

        $insertStmt->bind_param(
            "iiiidddddddiidddds",
            $periodId,
            $employeeId,
            $departmentId,
            $positionId,
            $regularHours,
            $overtimeHours,
            $nightDiffHours,
            $regHolidayHours,
            $specHolidayHours,
            $unworkedHolidayHours,
            $holidayOvertimeHours,
            $lateMinutes,
            $undertimeMinutes,
            $absencesHours,
            $paidLeaveHours,
            $unpaidLeaveHours,
            $totalPayableHours,
            $notes
        );

        if (!$insertStmt->execute()) {
            throw new Exception("Failed to insert timesheet employee summary: " . $insertStmt->error);
        }

        $insertStmt->close();
    }
}

function recordTimesheetAttendance(
    mysqli $conn,
    int $employeeId,
    string $workDate,
    int $sessionId,
    string $eventType,
    string $eventTime,
    ?int $assignmentId,
    ?int $departmentId,
    ?string $shiftCode,
    ?string $scheduledStart,
    ?string $scheduledEnd,
    int $breakMinutesPlanned,
    int $graceMinutes
): array {
    $timesheetUpdated = false;
    $timesheetSummaryUpdated = false;
    $timesheetMessage = "No matching timesheet period found for work date.";
    $summaryMessage = "No summary update performed.";

    if (!$departmentId) {
        return [
            'timesheet_updated' => $timesheetUpdated,
            'timesheet_message' => $timesheetMessage,
            'timesheet_summary_updated' => $timesheetSummaryUpdated,
            'summary_message' => $summaryMessage
        ];
    }

    $periodSql = "
        SELECT PeriodID
        FROM timesheet_period
        WHERE DepartmentID = ?
          AND ? BETWEEN StartDate AND EndDate
        ORDER BY PeriodID DESC
        LIMIT 1
    ";
    $periodStmt = $conn->prepare($periodSql);

    if (!$periodStmt) {
        throw new Exception("Failed to prepare timesheet period lookup: " . $conn->error);
    }

    $periodStmt->bind_param("is", $departmentId, $workDate);
    $periodStmt->execute();
    $periodResult = $periodStmt->get_result();
    $periodRow = $periodResult ? $periodResult->fetch_assoc() : null;
    $periodStmt->close();

    if (!$periodRow) {
        return [
            'timesheet_updated' => false,
            'timesheet_message' => "No matching timesheet period found for work date.",
            'timesheet_summary_updated' => false,
            'summary_message' => "No summary update performed."
        ];
    }

    $periodId = (int) $periodRow['PeriodID'];
    $timesheetDayId = null;
    $dayRow = null;

    if ($eventType === 'TIME_IN') {
        $findSql = "
            SELECT *
            FROM timesheet_daily
            WHERE PeriodID = ?
              AND EmployeeID = ?
              AND WorkDate = ?
            LIMIT 1
        ";
        $findStmt = $conn->prepare($findSql);

        if (!$findStmt) {
            throw new Exception("Failed to prepare TIME_IN day lookup: " . $conn->error);
        }

        $findStmt->bind_param("iis", $periodId, $employeeId, $workDate);
        $findStmt->execute();
        $findResult = $findStmt->get_result();
        $existingDay = $findResult ? $findResult->fetch_assoc() : null;
        $findStmt->close();

        if ($existingDay) {
            $timesheetDayId = (int) $existingDay['TimesheetDayID'];

            $updateSql = "
                UPDATE timesheet_daily
                SET
                    AssignmentID = ?,
                    SessionID = ?,
                    ShiftCode = ?,
                    ScheduledStart = ?,
                    ScheduledEnd = ?,
                    BreakMinutesPlanned = ?,
                    ActualTimeIn = COALESCE(ActualTimeIn, ?),
                    DayStatus = ?
                WHERE TimesheetDayID = ?
            ";
            $updateStmt = $conn->prepare($updateSql);

            if (!$updateStmt) {
                throw new Exception("Failed to prepare TIME_IN update existing row: " . $conn->error);
            }

            $dayStatus = (!empty($shiftCode) && !empty($scheduledStart) && !empty($scheduledEnd)) ? 'INCOMPLETE' : 'NO_SCHEDULE';

            $updateStmt->bind_param(
                "iisssissi",
                $assignmentId,
                $sessionId,
                $shiftCode,
                $scheduledStart,
                $scheduledEnd,
                $breakMinutesPlanned,
                $eventTime,
                $dayStatus,
                $timesheetDayId
            );

            if (!$updateStmt->execute()) {
                throw new Exception("Failed to update existing timesheet row for TIME_IN: " . $updateStmt->error);
            }

            $updateStmt->close();
        } else {
            $dayStatus = (!empty($shiftCode) && !empty($scheduledStart) && !empty($scheduledEnd)) ? 'INCOMPLETE' : 'NO_SCHEDULE';
            $actualTimeOut = null;
            $breakMinutesActual = 0;

            $insertSql = "
                INSERT INTO timesheet_daily
                (
                    PeriodID,
                    EmployeeID,
                    WorkDate,
                    AssignmentID,
                    SessionID,
                    ShiftCode,
                    ScheduledStart,
                    ScheduledEnd,
                    BreakMinutesPlanned,
                    ActualTimeIn,
                    ActualTimeOut,
                    BreakMinutesActual,
                    RegularMinutes,
                    OvertimeMinutes,
                    NightDiffMinutes,
                    LateMinutes,
                    UndertimeMinutes,
                    DayStatus
                )
                VALUES
                (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0, 0, 0, 0, ?
                )
            ";
            $insertStmt = $conn->prepare($insertSql);

            if (!$insertStmt) {
                throw new Exception("Failed to prepare TIME_IN insert row: " . $conn->error);
            }

            $insertStmt->bind_param(
                "iisiisssissis",
                $periodId,
                $employeeId,
                $workDate,
                $assignmentId,
                $sessionId,
                $shiftCode,
                $scheduledStart,
                $scheduledEnd,
                $breakMinutesPlanned,
                $eventTime,
                $actualTimeOut,
                $breakMinutesActual,
                $dayStatus
            );

            if (!$insertStmt->execute()) {
                throw new Exception("Failed to insert timesheet row for TIME_IN: " . $insertStmt->error);
            }

            $timesheetDayId = $insertStmt->insert_id;
            $insertStmt->close();
        }
    } else {
        $daySql = "
            SELECT *
            FROM timesheet_daily
            WHERE PeriodID = ?
              AND EmployeeID = ?
              AND WorkDate = ?
            LIMIT 1
        ";
        $dayStmt = $conn->prepare($daySql);

        if (!$dayStmt) {
            throw new Exception("Failed to prepare timesheet daily lookup: " . $conn->error);
        }

        $dayStmt->bind_param("iis", $periodId, $employeeId, $workDate);
        $dayStmt->execute();
        $dayResult = $dayStmt->get_result();
        $dayRow = $dayResult ? $dayResult->fetch_assoc() : null;
        $dayStmt->close();

        if (!$dayRow) {
            throw new Exception("No timesheet_daily row found for this work date. Please TIME_IN first.");
        }

        $timesheetDayId = (int) $dayRow['TimesheetDayID'];
    }

    if ($eventType === 'BREAK_IN' || $eventType === 'BREAK_OUT') {
        $updateSql = "
            UPDATE timesheet_daily
            SET
                AssignmentID = ?,
                SessionID = ?,
                ShiftCode = ?,
                ScheduledStart = ?,
                ScheduledEnd = ?,
                BreakMinutesPlanned = ?,
                DayStatus = ?
            WHERE TimesheetDayID = ?
        ";
        $updateStmt = $conn->prepare($updateSql);

        if (!$updateStmt) {
            throw new Exception("Failed to prepare break update: " . $conn->error);
        }

        $dayStatus = (!empty($shiftCode) && !empty($scheduledStart) && !empty($scheduledEnd)) ? 'INCOMPLETE' : 'NO_SCHEDULE';

        $updateStmt->bind_param(
            "iisssisi",
            $assignmentId,
            $sessionId,
            $shiftCode,
            $scheduledStart,
            $scheduledEnd,
            $breakMinutesPlanned,
            $dayStatus,
            $timesheetDayId
        );

        if (!$updateStmt->execute()) {
            throw new Exception("Failed to update timesheet during break event: " . $updateStmt->error);
        }

        $updateStmt->close();
    }

    if ($eventType === 'BREAK_OUT') {
        $breakInTime = fetchLatestEventTime($conn, $sessionId, 'BREAK_IN');
        $breakOutTime = $eventTime;

        if ($breakInTime && $breakOutTime) {
            $breakMinutesActual = minutesBetween($breakInTime, $breakOutTime);

            $breakSql = "
                UPDATE timesheet_daily
                SET BreakMinutesActual = ?
                WHERE TimesheetDayID = ?
            ";
            $breakStmt = $conn->prepare($breakSql);

            if (!$breakStmt) {
                throw new Exception("Failed to prepare BreakMinutesActual update: " . $conn->error);
            }

            $breakStmt->bind_param("ii", $breakMinutesActual, $timesheetDayId);

            if (!$breakStmt->execute()) {
                throw new Exception("Failed to update BreakMinutesActual: " . $breakStmt->error);
            }

            $breakStmt->close();
        }
    }

    if ($eventType === 'TIME_OUT') {
        $loadSql = "
            SELECT *
            FROM timesheet_daily
            WHERE TimesheetDayID = ?
            LIMIT 1
        ";
        $loadStmt = $conn->prepare($loadSql);

        if (!$loadStmt) {
            throw new Exception("Failed to prepare final timesheet load: " . $conn->error);
        }

        $loadStmt->bind_param("i", $timesheetDayId);
        $loadStmt->execute();
        $loadResult = $loadStmt->get_result();
        $loadedDay = $loadResult ? $loadResult->fetch_assoc() : null;
        $loadStmt->close();

        if (!$loadedDay) {
            throw new Exception("Timesheet daily row not found during TIME_OUT finalization.");
        }

        $actualTimeIn = !empty($loadedDay['ActualTimeIn']) ? $loadedDay['ActualTimeIn'] : null;
        $actualTimeOut = $eventTime;
        $breakMinutesActual = isset($loadedDay['BreakMinutesActual']) ? (int) $loadedDay['BreakMinutesActual'] : 0;

        $shiftCodeFinal = !empty($shiftCode)
            ? $shiftCode
            : (!empty($loadedDay['ShiftCode']) ? $loadedDay['ShiftCode'] : null);

        $scheduledStartFinal = !empty($scheduledStart)
            ? $scheduledStart
            : (!empty($loadedDay['ScheduledStart']) ? $loadedDay['ScheduledStart'] : null);

        $scheduledEndFinal = !empty($scheduledEnd)
            ? $scheduledEnd
            : (!empty($loadedDay['ScheduledEnd']) ? $loadedDay['ScheduledEnd'] : null);

        $breakMinutesPlannedFinal = $breakMinutesPlanned > 0
            ? $breakMinutesPlanned
            : (int) ($loadedDay['BreakMinutesPlanned'] ?? 0);

        [$scheduledStartDT, $scheduledEndDT] = computeScheduledDateTimes(
            $workDate,
            $scheduledStartFinal,
            $scheduledEndFinal
        );

        $regularMinutes = 0;
        $overtimeMinutes = 0;
        $nightDiffMinutes = 0;
        $lateMinutes = 0;
        $undertimeMinutes = 0;
        $dayStatus = 'NO_SCHEDULE';

        if ($shiftCodeFinal && $scheduledStartFinal && $scheduledEndFinal && $actualTimeIn && $actualTimeOut && $scheduledStartDT && $scheduledEndDT) {
            $scheduledGrossMinutes = minutesBetween($scheduledStartDT, $scheduledEndDT);
            $scheduledNetMinutes = max(0, $scheduledGrossMinutes - max(0, (int) $breakMinutesPlannedFinal));

            $actualWorkedGrossMinutes = minutesBetween($actualTimeIn, $actualTimeOut);
            $actualWorkedNetMinutes = max(0, $actualWorkedGrossMinutes - max(0, $breakMinutesActual));

            $graceAdjustedStart = date('Y-m-d H:i:s', strtotime($scheduledStartDT . " +{$graceMinutes} minutes"));

            if ($actualTimeIn) {
                $actualInTs = strtotime($actualTimeIn);
                $graceStartTs = strtotime($graceAdjustedStart);

                if ($actualInTs !== false && $graceStartTs !== false && $actualInTs > $graceStartTs) {
                    $lateMinutes = (int) floor(($actualInTs - $graceStartTs) / 60);
                }
            }

            if ($scheduledEndDT && $actualTimeOut) {
                $actualOutTs = strtotime($actualTimeOut);
                $schedEndTs = strtotime($scheduledEndDT);

                if ($actualOutTs !== false && $schedEndTs !== false && $actualOutTs < $schedEndTs) {
                    $rawUndertime = (int) floor(($schedEndTs - $actualOutTs) / 60);

                    // hindi pwedeng lumampas sa required net shift minutes
                    $undertimeMinutes = max(0, min($rawUndertime, $scheduledNetMinutes));
                }
            }

            // regular hours should only be within required shift
            $regularMinutes = max(0, min($actualWorkedNetMinutes, $scheduledNetMinutes));

            // overtime only beyond required shift
            $overtimeMinutes = max(0, $actualWorkedNetMinutes - $scheduledNetMinutes);

            $breakInTime = fetchLatestEventTime($conn, $sessionId, 'BREAK_IN');
            $breakOutTime = fetchLatestEventTime($conn, $sessionId, 'BREAK_OUT');

            $nightDiffMinutes = computeNightMinutes($actualTimeIn, $actualTimeOut, $breakInTime, $breakOutTime);

            // optional safety: night diff should not exceed actual worked net minutes
            $nightDiffMinutes = max(0, min($nightDiffMinutes, $actualWorkedNetMinutes));

            $dayStatus = 'OK';
        } elseif ($actualTimeIn && $actualTimeOut) {
            $dayStatus = 'NO_SCHEDULE';
        } else {
            $dayStatus = 'INCOMPLETE';
        }

        $finalSql = "
            UPDATE timesheet_daily
            SET
                AssignmentID = ?,
                SessionID = ?,
                ShiftCode = ?,
                ScheduledStart = ?,
                ScheduledEnd = ?,
                BreakMinutesPlanned = ?,
                ActualTimeOut = ?,
                BreakMinutesActual = ?,
                RegularMinutes = ?,
                OvertimeMinutes = ?,
                NightDiffMinutes = ?,
                LateMinutes = ?,
                UndertimeMinutes = ?,
                DayStatus = ?
            WHERE TimesheetDayID = ?
        ";
        $finalStmt = $conn->prepare($finalSql);

        if (!$finalStmt) {
            throw new Exception("Failed to prepare final timesheet update: " . $conn->error);
        }

        $finalAssignmentId = $assignmentId ?? (isset($loadedDay['AssignmentID']) ? (int) $loadedDay['AssignmentID'] : null);
        $finalSessionId = $sessionId ?: (isset($loadedDay['SessionID']) ? (int) $loadedDay['SessionID'] : 0);

        $finalStmt->bind_param(
            "iisssisiiiiiisi",
            $finalAssignmentId,
            $finalSessionId,
            $shiftCodeFinal,
            $scheduledStartFinal,
            $scheduledEndFinal,
            $breakMinutesPlannedFinal,
            $actualTimeOut,
            $breakMinutesActual,
            $regularMinutes,
            $overtimeMinutes,
            $nightDiffMinutes,
            $lateMinutes,
            $undertimeMinutes,
            $dayStatus,
            $timesheetDayId
        );

        if (!$finalStmt->execute()) {
            throw new Exception("Failed to finalize timesheet daily: " . $finalStmt->error);
        }

        $finalStmt->close();
    }

    $timesheetUpdated = true;
    $timesheetMessage = "Timesheet daily updated successfully.";

    recomputeEmployeeSummary($conn, $periodId, $employeeId);
    $timesheetSummaryUpdated = true;
    $summaryMessage = "Timesheet employee summary updated successfully.";

    return [
        'timesheet_updated' => $timesheetUpdated,
        'timesheet_message' => $timesheetMessage,
        'timesheet_summary_updated' => $timesheetSummaryUpdated,
        'summary_message' => $summaryMessage
    ];
}