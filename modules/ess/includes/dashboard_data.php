<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/auth_employee.php';
require_once __DIR__ . '/../../../config/config.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_announcements':
        getAnnouncements();
        break;

    default:
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action.'
        ]);
        exit;
}

function getAnnouncements(): void
{
    /*
     |--------------------------------------------------------------------------
     | TEMP ANNOUNCEMENT SOURCE
     |--------------------------------------------------------------------------
     | Replace this later with a real database table when you add one.
     */
    $rows = [
        [
            'AnnouncementID' => 1,
            'title' => 'Payroll Availability Reminder',
            'message' => 'Your payslip for the current cutoff period will be available once payroll processing is completed.',
            'priority' => 'high',
            'posted_by' => 'HR Department',
            'posted_at' => '2026-03-15'
        ],
        [
            'AnnouncementID' => 2,
            'title' => 'Face Attendance Reminder',
            'message' => 'Please make sure your facial profile is updated to avoid attendance capture issues.',
            'priority' => 'medium',
            'posted_by' => 'ESS Administration',
            'posted_at' => '2026-03-14'
        ],
        [
            'AnnouncementID' => 3,
            'title' => 'ESS Self-Service Update',
            'message' => 'You may now view your work hours, leave records, claims, and payslip directly in the ESS portal.',
            'priority' => 'low',
            'posted_by' => 'System Admin',
            'posted_at' => '2026-03-13'
        ]
    ];

    $formatted = array_map(function ($row) {
        return [
        'AnnouncementID' => (int)($row['AnnouncementID'] ?? 0),
        'title' => $row['title'] ?? '',
        'message' => $row['message'] ?? '',
        'priority' => normalizePriority($row['priority'] ?? 'low'),
        'posted_by' => $row['posted_by'] ?? 'System',
        'posted_at' => $row['posted_at'] ?? '',
        'posted_at_label' => formatDateLabel($row['posted_at'] ?? '')
        ];
    }, $rows);

    usort($formatted, function ($a, $b) {
        return strcmp($b['posted_at'], $a['posted_at']);
    });

    echo json_encode([
        'success' => true,
        'announcements' => $formatted
    ]);
    exit;
}

function normalizePriority(string $priority): string
{
    $value = strtolower(trim($priority));

    if (in_array($value, ['high', 'medium', 'low'], true)) {
        return $value;
    }

    return 'low';
}

function formatDateLabel(string $date): string
{
    if (empty($date)) {
        return '-';
    }

    $timestamp = strtotime($date);
    if (!$timestamp) {
        return $date;
    }

    return date('F d, Y', $timestamp);
}