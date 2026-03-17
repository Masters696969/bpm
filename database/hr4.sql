-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 17, 2026 at 02:11 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hr4`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts_payable`
--

CREATE TABLE `accounts_payable` (
  `id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `payee_name` varchar(255) NOT NULL,
  `category` enum('SSS','PhilHealth','PagIBIG','BIR','Other') NOT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `status` enum('Pending','Paid') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts_payable`
--

INSERT INTO `accounts_payable` (`id`, `batch_id`, `payee_name`, `category`, `description`, `amount`, `status`, `created_at`, `updated_at`) VALUES
(1, 17, 'Social Security System', 'SSS', 'Payroll Deductions & Benefits for Batch PR-2026-524', 24375.00, 'Pending', '2026-03-10 11:13:01', '2026-03-10 11:13:01'),
(2, 17, 'PhilHealth Corporation', 'PhilHealth', 'Payroll Deductions & Benefits for Batch PR-2026-524', 11550.00, 'Pending', '2026-03-10 11:13:01', '2026-03-10 11:13:01'),
(3, 17, 'Pag-IBIG Fund', 'PagIBIG', 'Payroll Deductions & Benefits for Batch PR-2026-524', 2200.00, 'Pending', '2026-03-10 11:13:01', '2026-03-10 11:13:01'),
(4, 17, 'Bureau of Internal Revenue', 'BIR', 'Payroll Deductions & Benefits for Batch PR-2026-524', 38063.63, 'Pending', '2026-03-10 11:13:01', '2026-03-10 11:13:01'),
(5, 18, 'Social Security System', 'SSS', 'Payroll Deductions & Benefits for Batch PR-2026-943', 24375.00, 'Pending', '2026-03-10 17:02:37', '2026-03-10 17:02:37'),
(6, 18, 'PhilHealth Corporation', 'PhilHealth', 'Payroll Deductions & Benefits for Batch PR-2026-943', 11550.00, 'Pending', '2026-03-10 17:02:37', '2026-03-10 17:02:37'),
(7, 18, 'Pag-IBIG Fund', 'PagIBIG', 'Payroll Deductions & Benefits for Batch PR-2026-943', 2200.00, 'Pending', '2026-03-10 17:02:37', '2026-03-10 17:02:37'),
(8, 18, 'Bureau of Internal Revenue', 'BIR', 'Payroll Deductions & Benefits for Batch PR-2026-943', 38063.63, 'Pending', '2026-03-10 17:02:37', '2026-03-10 17:02:37'),
(9, 19, 'Social Security System', 'SSS', 'Payroll Deductions & Benefits for Batch PR-2026-393', 24375.00, 'Pending', '2026-03-11 07:55:42', '2026-03-11 07:55:42'),
(10, 19, 'PhilHealth Corporation', 'PhilHealth', 'Payroll Deductions & Benefits for Batch PR-2026-393', 11550.00, 'Pending', '2026-03-11 07:55:42', '2026-03-11 07:55:42'),
(11, 19, 'Pag-IBIG Fund', 'PagIBIG', 'Payroll Deductions & Benefits for Batch PR-2026-393', 2200.00, 'Paid', '2026-03-11 07:55:42', '2026-03-11 08:17:13'),
(12, 19, 'Bureau of Internal Revenue', 'BIR', 'Payroll Deductions & Benefits for Batch PR-2026-393', 38063.63, 'Paid', '2026-03-11 07:55:42', '2026-03-11 08:14:38'),
(13, 17, 'Social Security System', 'SSS', 'Payroll Deductions & Benefits for Batch PR-2026-524', 24375.00, 'Pending', '2026-03-11 13:25:36', '2026-03-11 13:25:36'),
(14, 17, 'PhilHealth Corporation', 'PhilHealth', 'Payroll Deductions & Benefits for Batch PR-2026-524', 11550.00, 'Pending', '2026-03-11 13:25:36', '2026-03-11 13:25:36'),
(15, 17, 'Pag-IBIG Fund', 'PagIBIG', 'Payroll Deductions & Benefits for Batch PR-2026-524', 2200.00, 'Pending', '2026-03-11 13:25:36', '2026-03-11 13:25:36'),
(16, 17, 'Bureau of Internal Revenue', 'BIR', 'Payroll Deductions & Benefits for Batch PR-2026-524', 38063.63, 'Pending', '2026-03-11 13:25:36', '2026-03-11 13:25:36'),
(17, 19, 'Social Security System', 'SSS', 'Payroll Deductions & Benefits for Batch PR-2026-393', 24375.00, 'Pending', '2026-03-11 13:34:37', '2026-03-11 13:34:37'),
(18, 19, 'PhilHealth Corporation', 'PhilHealth', 'Payroll Deductions & Benefits for Batch PR-2026-393', 11550.00, 'Pending', '2026-03-11 13:34:37', '2026-03-11 13:34:37'),
(19, 19, 'Pag-IBIG Fund', 'PagIBIG', 'Payroll Deductions & Benefits for Batch PR-2026-393', 2200.00, 'Pending', '2026-03-11 13:34:37', '2026-03-11 13:34:37'),
(20, 19, 'Bureau of Internal Revenue', 'BIR', 'Payroll Deductions & Benefits for Batch PR-2026-393', 38063.63, 'Pending', '2026-03-11 13:34:37', '2026-03-11 13:34:37'),
(21, 20, 'Social Security System', 'SSS', 'Payroll Deductions & Benefits for Batch PR-2026-638', 24375.00, 'Pending', '2026-03-11 16:43:59', '2026-03-11 16:43:59'),
(22, 20, 'PhilHealth Corporation', 'PhilHealth', 'Payroll Deductions & Benefits for Batch PR-2026-638', 11550.00, 'Pending', '2026-03-11 16:43:59', '2026-03-11 16:43:59'),
(23, 20, 'Pag-IBIG Fund', 'PagIBIG', 'Payroll Deductions & Benefits for Batch PR-2026-638', 2200.00, 'Pending', '2026-03-11 16:43:59', '2026-03-11 16:43:59'),
(24, 20, 'Bureau of Internal Revenue', 'BIR', 'Payroll Deductions & Benefits for Batch PR-2026-638', 38063.63, 'Pending', '2026-03-11 16:43:59', '2026-03-11 16:43:59'),
(25, 21, 'Social Security System', 'SSS', 'Payroll Deductions & Benefits for Batch PR-2026-083', 24375.00, 'Pending', '2026-03-11 18:35:11', '2026-03-11 18:35:11'),
(26, 21, 'PhilHealth Corporation', 'PhilHealth', 'Payroll Deductions & Benefits for Batch PR-2026-083', 11550.00, 'Pending', '2026-03-11 18:35:11', '2026-03-11 18:35:11'),
(27, 21, 'Pag-IBIG Fund', 'PagIBIG', 'Payroll Deductions & Benefits for Batch PR-2026-083', 2200.00, 'Pending', '2026-03-11 18:35:11', '2026-03-11 18:35:11'),
(28, 21, 'Bureau of Internal Revenue', 'BIR', 'Payroll Deductions & Benefits for Batch PR-2026-083', 38063.63, 'Pending', '2026-03-11 18:35:11', '2026-03-11 18:35:11'),
(29, 22, 'Social Security System', 'SSS', 'Payroll Deductions & Benefits for Batch PR-2026-199', 26550.00, 'Pending', '2026-03-12 00:42:51', '2026-03-12 00:42:51'),
(30, 22, 'PhilHealth Corporation', 'PhilHealth', 'Payroll Deductions & Benefits for Batch PR-2026-199', 12275.00, 'Paid', '2026-03-12 00:42:51', '2026-03-12 02:51:54'),
(31, 22, 'Pag-IBIG Fund', 'PagIBIG', 'Payroll Deductions & Benefits for Batch PR-2026-199', 2400.00, 'Paid', '2026-03-12 00:42:51', '2026-03-12 02:51:42'),
(32, 22, 'Bureau of Internal Revenue', 'BIR', 'Payroll Deductions & Benefits for Batch PR-2026-199', 39290.33, 'Paid', '2026-03-12 00:42:51', '2026-03-12 02:51:28');

-- --------------------------------------------------------

--
-- Table structure for table `allowance_proposals`
--

CREATE TABLE `allowance_proposals` (
  `ProposalID` int(11) NOT NULL,
  `BatchReference` varchar(50) DEFAULT NULL,
  `SalaryGradeID` int(11) DEFAULT NULL,
  `AllowanceTypeID` int(11) DEFAULT NULL,
  `ProposedAmount` decimal(12,2) DEFAULT NULL,
  `Reason` text DEFAULT NULL,
  `ProposedBy` int(11) DEFAULT NULL,
  `Status` enum('Pending','Endorsed','Manager Approved','Applied','Rejected') DEFAULT 'Pending',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `allowance_proposals`
--

INSERT INTO `allowance_proposals` (`ProposalID`, `BatchReference`, `SalaryGradeID`, `AllowanceTypeID`, `ProposedAmount`, `Reason`, `ProposedBy`, `Status`, `CreatedAt`, `UpdatedAt`) VALUES
(3, 'ALW_69A693C6C5F61', 5, 1, 3000.00, 'test', 7, 'Endorsed', '2026-03-03 07:54:46', '2026-03-03 07:57:07'),
(4, 'ALW_69A7FD3F49A3A', 1, 1, 3000.00, 'test', 7, 'Manager Approved', '2026-03-04 09:37:03', '2026-03-04 10:04:04'),
(5, 'ALW_69A7FD3F49A3A', 2, 1, 3000.00, 'test', 7, 'Manager Approved', '2026-03-04 09:37:03', '2026-03-04 10:04:04'),
(6, 'ALW_69A7FD3F49A3A', 3, 1, 3000.00, 'test', 7, 'Manager Approved', '2026-03-04 09:37:03', '2026-03-04 10:04:04'),
(7, 'ALW_69A7FD3F49A3A', 4, 1, 3000.00, 'test', 7, 'Manager Approved', '2026-03-04 09:37:03', '2026-03-04 10:04:04'),
(8, 'ALW_69A7FD3F49A3A', 5, 1, 3000.00, 'test', 7, 'Manager Approved', '2026-03-04 09:37:03', '2026-03-04 10:04:04');

-- --------------------------------------------------------

--
-- Table structure for table `allowance_types`
--

CREATE TABLE `allowance_types` (
  `AllowanceTypeID` int(11) NOT NULL,
  `AllowanceName` varchar(100) NOT NULL,
  `IsTaxable` tinyint(1) DEFAULT 0,
  `Frequency` enum('Monthly','Annual','Daily') DEFAULT 'Monthly',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `allowance_types`
--

INSERT INTO `allowance_types` (`AllowanceTypeID`, `AllowanceName`, `IsTaxable`, `Frequency`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'Rice Subsidy', 0, 'Monthly', '2026-02-25 09:41:43', '2026-02-25 09:41:43'),
(2, 'Meal Allowance', 1, 'Monthly', '2026-02-25 09:41:43', '2026-02-25 17:53:50'),
(3, 'Laundry Allowance', 0, 'Monthly', '2026-02-25 09:41:43', '2026-02-25 09:41:43'),
(4, 'Travel Allowance', 1, 'Monthly', '2026-02-25 09:41:43', '2026-02-25 17:53:50'),
(6, 'Communication Allowance', 1, 'Monthly', '2026-02-25 09:41:43', '2026-02-25 17:53:50');

-- --------------------------------------------------------

--
-- Table structure for table `applicants`
--

CREATE TABLE `applicants` (
  `ApplicantID` int(11) NOT NULL,
  `PostID` int(11) NOT NULL,
  `FirstName` varchar(100) NOT NULL,
  `MiddleName` varchar(100) DEFAULT NULL,
  `LastName` varchar(100) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Phone` varchar(20) NOT NULL,
  `Gender` varchar(20) DEFAULT NULL,
  `DateOfBirth` date DEFAULT NULL,
  `PermanentAddress` text DEFAULT NULL,
  `EmergencyContactName` varchar(200) DEFAULT NULL,
  `EmergencyRelationship` varchar(50) DEFAULT NULL,
  `EmergencyPhone` varchar(20) DEFAULT NULL,
  `ResumePath` varchar(255) DEFAULT NULL,
  `GovIDPath` varchar(255) DEFAULT NULL,
  `ClearancePath` varchar(255) DEFAULT NULL,
  `TORPath` varchar(255) DEFAULT NULL,
  `IDPicturePath` varchar(255) DEFAULT NULL,
  `Status` enum('New','Reviewed','Shortlisted','Interview','Rejected','Accepted') DEFAULT 'New',
  `AppliedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `ApprovalStatus` varchar(50) DEFAULT 'Pending Manager Approval',
  `ExamScore` int(11) DEFAULT NULL,
  `ExamStatus` varchar(50) DEFAULT 'Pending',
  `ResumeScore` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applicants`
--

INSERT INTO `applicants` (`ApplicantID`, `PostID`, `FirstName`, `MiddleName`, `LastName`, `Email`, `Phone`, `Gender`, `DateOfBirth`, `PermanentAddress`, `EmergencyContactName`, `EmergencyRelationship`, `EmergencyPhone`, `ResumePath`, `GovIDPath`, `ClearancePath`, `TORPath`, `IDPicturePath`, `Status`, `AppliedAt`, `ApprovalStatus`, `ExamScore`, `ExamStatus`, `ResumeScore`) VALUES
(1, 1, 'Joshua', 'Rivero', 'Suruiz', 'suruizjoshuaandrierivero@gmail.com', '09223311333', 'Male', '2004-04-06', 'congressional', 'joshua', 'Father', '09334455667', 'uploads/applications/Resume_Suruiz_1772957354.pdf', 'uploads/applications/GovID_Suruiz_1772957354.jpg', 'uploads/applications/Clearance_Suruiz_1772957354.jpg', 'uploads/applications/TOR_Suruiz_1772957354.jpg', 'uploads/applications/IDPic_Suruiz_1772957354.jpg', 'Accepted', '2026-03-08 08:09:14', 'Hired', 15, 'Completed', 100),
(2, 1, 'Joshua', 'Rivero', 'Suruiz', 'suruizjoshua72@gmail.com', '09223311333', 'Male', '2004-04-06', 'Quezon City', 'joshua', 'Father', '09334455667', 'uploads/applications/Resume_Suruiz_1772968966.pdf', 'uploads/applications/GovID_Suruiz_1772968966.jpg', 'uploads/applications/Clearance_Suruiz_1772968966.jpg', 'uploads/applications/TOR_Suruiz_1772968966.jpg', 'uploads/applications/IDPic_Suruiz_1772968966.jpg', 'Accepted', '2026-03-08 11:22:46', 'Hired', 15, 'Completed', 100),
(6, 1, 'Upgrade', NULL, 'Test', 'v2@test.com', '09123456789', 'Female', '1995-05-15', '456 Upgrade Ave', 'John Doe', 'Father', '09887776665', NULL, NULL, NULL, NULL, NULL, 'Accepted', '2026-03-08 16:14:02', 'Approved', 15, 'Completed', 0),
(9, 2, 'test', 'lang', 'three', 'suruizjoshua72@gmail.com', '092211333444', 'Male', '2004-04-06', 'dyan lang', 'Suruiz Joshua Andrie Rivero', 'father', '09103840798', 'uploads/applications/Resume_three_1773035846.pdf', 'uploads/applications/GovID_three_1773035846.png', 'uploads/applications/Clearance_three_1773035846.png', 'uploads/applications/TOR_three_1773035846.png', 'uploads/applications/IDPic_three_1773035846.jpg', 'Accepted', '2026-03-09 05:57:26', 'Hired', 15, 'Completed', 100),
(10, 3, 'Jonnar', 'S.', 'Solis', 'Solis@gmail.com', '09111240798', 'Male', '1996-01-09', 'FAIRVIEW', 'SOLIS', 'FATHER', '0922113344', 'uploads/applications/Resume_Solis_1773069431.pdf', 'uploads/applications/GovID_Solis_1773069431.jpg', 'uploads/applications/Clearance_Solis_1773069431.jpg', 'uploads/applications/TOR_Solis_1773069431.jpg', 'uploads/applications/IDPic_Solis_1773069431.jpg', 'Accepted', '2026-03-09 15:17:11', 'Hired', 15, 'Completed', 100),
(11, 5, 'EARL', '', 'ALARCON', 'lawrence@gmail.com', '0921111333', 'Male', '2004-04-06', 'bcp', 'joshua', 'Father', '09334455667', 'uploads/applications/Resume_ALARCON_1773160934.pdf', NULL, NULL, NULL, NULL, 'Accepted', '2026-03-10 16:42:14', 'Hired', 15, 'Completed', 100),
(12, 6, 'Joshua', '', 'Garcia', 'joshua@gmail.com', '09103840798', 'Male', '2026-03-11', 'B2 L5 APRIL EXT. CONGRESS. AVENUE BRGY. BAHAY TORO', 'Suruiz Joshua Andrie Rivero', 'Father', '09103840798', 'uploads/applications/Resume_Garcia_1773225411.docx', NULL, NULL, NULL, NULL, 'New', '2026-03-11 10:36:51', 'Pending Manager Approval', 15, 'Completed', 100),
(13, 6, 'John', '', 'Vibar', 'john@gmail.com', '09103840798', 'Male', '2026-03-11', 'B2 L5 APRIL EXT. CONGRESS. AVENUE BRGY. BAHAY TORO', 'Suruiz Joshua Andrie Rivero', 'Father', '09103840798', 'uploads/applications/Resume_Vibar_1773226642.docx', NULL, NULL, NULL, NULL, 'New', '2026-03-11 10:57:22', 'Pending Manager Approval', 15, 'Completed', 100),
(14, 7, 'example', 'S.', 'Rivero', 'suruizjoshuaandrierivero@gmail.com', '09103840798', 'Female', '2026-03-12', 'B2 L5 APRIL EXT. CONGRESS. AVENUE BRGY. BAHAY TORO', 'Suruiz Joshua Andrie Rivero', 'FATHER', '09103840798', 'uploads/applications/Resume_Rivero_1773275635.pdf', NULL, NULL, NULL, NULL, 'New', '2026-03-12 00:33:55', 'Pending Manager Approval', 15, 'Completed', 100),
(15, 7, 'Earl', '', 'Alarcon', 'earllaurencealarcon@gmail.com', '12346789111', 'Male', '2026-03-12', '12345 brgy', 'earl', 'Father', '12345678911', 'uploads/applications/Resume_Alarcon_1773275721.pdf', NULL, NULL, NULL, NULL, 'Accepted', '2026-03-12 00:35:21', 'Hired', 15, 'Completed', 100),
(16, 8, 'EARL', '', 'ALARCON', 'lunlunny407@gmail.com', '09123245498', 'Male', '2026-03-12', 'brgy 123', 'Earl', 'Uncle', '1209308445', 'uploads/applications/Resume_ALARCON_1773285156.pdf', NULL, NULL, NULL, NULL, 'Interview', '2026-03-12 03:12:36', 'Pending Manager Approval', 15, 'Completed', 100),
(17, 11, 'buya', 'b', 'buya', 'buya@gmail.com', '09123456678', 'Male', '2004-04-15', 'testing', 'GLORY JEAN JOB', 'example', '09127381825', 'uploads/applications/Resume_buya_1773557679.docx', NULL, NULL, NULL, NULL, 'Accepted', '2026-03-15 06:54:39', 'Hired', 15, 'Completed', 100),
(18, 12, 'buyaaaa', 'b', 'buya', 'buya@gmail.com', '09123456678', 'Female', '2004-04-15', 'testing', 'GLORY JEAN JOB', 'example', '09127381825', 'uploads/applications/Resume_buya_1773589319.docx', NULL, NULL, NULL, NULL, 'Accepted', '2026-03-15 15:41:59', 'Hired', 14, 'Completed', 100),
(19, 12, 'denzel', 'g', 'Ortiz', 'Ortiz@gmail.com', '09123456678', 'Male', '2026-03-16', 'testing', 'GLORY JEAN JOB', 'example', '09127381825', 'uploads/applications/Resume_Ortiz_1773592385.pdf', NULL, NULL, NULL, NULL, 'Accepted', '2026-03-15 16:33:05', 'Hired', 15, 'Completed', 100),
(20, 11, 'Miguel', 'b', 'Padre', 'juanmiguelerdap69@gmail.com', '0920502123', 'Male', '2004-03-03', 'testing', 'GLORY JEAN JOB', 'example', '09127381825', 'uploads/applications/Resume_Padre_1773593370.pdf', NULL, NULL, NULL, NULL, 'Accepted', '2026-03-15 16:49:30', 'Hired', 15, 'Completed', 100);

-- --------------------------------------------------------

--
-- Table structure for table `bankdetails`
--

CREATE TABLE `bankdetails` (
  `BankDetailID` int(11) NOT NULL,
  `EmployeeID` int(11) DEFAULT NULL,
  `BankName` varchar(100) DEFAULT NULL,
  `AccountNumber` varchar(50) DEFAULT NULL,
  `AccountType` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bankdetails`
--

INSERT INTO `bankdetails` (`BankDetailID`, `EmployeeID`, `BankName`, `AccountNumber`, `AccountType`) VALUES
(1, 1, 'BDO', '001234567890', 'payroll'),
(2, 2, 'BDO', '229-411-332-222', 'Payroll'),
(3, 3, 'BDO', '222-444-332-222', 'Payroll'),
(4, 4, 'BDO', '323235566', 'Payroll'),
(5, 7, 'BDO', '321-313-321', 'Payroll'),
(6, 6, 'BDO', '230-31125-2026', 'Payroll'),
(7, 2, 'BDO', '229-411-332-222', 'Payroll'),
(8, 8, 'BDO', '888-444-555-111', 'Payroll'),
(9, 10, 'BDO', '101-010-202-303', 'Payroll'),
(10, 14, 'BDO', '141-414-141-414', 'Payroll'),
(11, 15, 'BDO', '151-515-151-515', 'Payroll'),
(12, 16, 'BDO', '161-616-161-616', 'Payroll'),
(13, 18, 'BDO', '001234567890', 'payroll');

-- --------------------------------------------------------

--
-- Table structure for table `bank_applications`
--

CREATE TABLE `bank_applications` (
  `AppID` int(11) NOT NULL,
  `EmployeeID` int(11) NOT NULL,
  `FormID` int(11) DEFAULT NULL,
  `UploadedPDF` varchar(500) NOT NULL,
  `Status` enum('Pending','Sent to Bank','Confirmed') NOT NULL DEFAULT 'Pending',
  `Notes` text DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bank_applications`
--

INSERT INTO `bank_applications` (`AppID`, `EmployeeID`, `FormID`, `UploadedPDF`, `Status`, `Notes`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 3, 1, 'uploads/bank_submissions/emp3_1771691387.pdf', 'Confirmed', NULL, '2026-02-21 16:29:47', '2026-03-01 09:37:05'),
(2, 2, 1, 'uploads/bank_submissions/emp2_1771694089.pdf', 'Confirmed', NULL, '2026-02-21 17:14:49', '2026-02-21 17:33:11');

-- --------------------------------------------------------

--
-- Table structure for table `bank_forms_master`
--

CREATE TABLE `bank_forms_master` (
  `FormID` int(11) NOT NULL,
  `FormName` varchar(255) NOT NULL,
  `FilePath` varchar(500) NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `UploadedBy` varchar(100) DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bank_forms_master`
--

INSERT INTO `bank_forms_master` (`FormID`, `FormName`, `FilePath`, `IsActive`, `UploadedBy`, `CreatedAt`) VALUES
(1, 'BDO', 'uploads/bank_forms/BDO_1771691221.pdf', 1, 'Red Gin Baldon', '2026-02-21 16:27:01');

-- --------------------------------------------------------

--
-- Table structure for table `bir_tax_settings`
--

CREATE TABLE `bir_tax_settings` (
  `period_id` int(11) NOT NULL,
  `tax_exempt_limit` decimal(15,2) DEFAULT NULL,
  `de_minimis_cap` decimal(15,2) DEFAULT NULL,
  `thirteenth_month_cap` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bir_tax_settings`
--

INSERT INTO `bir_tax_settings` (`period_id`, `tax_exempt_limit`, `de_minimis_cap`, `thirteenth_month_cap`) VALUES
(1, 250000.00, 90000.00, 90000.00);

-- --------------------------------------------------------

--
-- Table structure for table `compensation_period`
--

CREATE TABLE `compensation_period` (
  `period_id` int(11) NOT NULL,
  `period_name` varchar(100) NOT NULL,
  `start_date` date NOT NULL DEFAULT '2026-01-01',
  `end_date` date NOT NULL DEFAULT '2026-02-15',
  `effective_date` date NOT NULL,
  `status` enum('Active','Inactive','Draft') DEFAULT 'Draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `budget_requested_amount` decimal(15,2) DEFAULT 0.00,
  `budget_approved_amount` decimal(15,2) DEFAULT 0.00,
  `budget_status` varchar(20) DEFAULT 'Draft',
  `finance_ref` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `compensation_period`
--

INSERT INTO `compensation_period` (`period_id`, `period_name`, `start_date`, `end_date`, `effective_date`, `status`, `created_at`, `budget_requested_amount`, `budget_approved_amount`, `budget_status`, `finance_ref`) VALUES
(1, 'FY2026', '2026-01-01', '2026-02-15', '2026-03-01', 'Active', '2026-02-23 17:21:48', 5000000.00, 0.00, 'Pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `competencies`
--

CREATE TABLE `competencies` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `competencies`
--

INSERT INTO `competencies` (`id`, `category_id`, `name`, `description`) VALUES
(1, 1, 'Communication Skills', 'Ability to communicate clearly with colleagues and clients'),
(2, 1, 'Teamwork', 'Ability to work cooperatively with others'),
(3, 1, 'Integrity', 'Honesty and ethical behavior in work'),
(4, 1, 'Professionalism', 'Maintaining professional conduct and attitude'),
(5, 1, 'Time Management', 'Ability to organize and manage work efficiently'),
(6, 1, 'Problem Solving', 'Identifying issues and providing solutions'),
(7, 1, 'Adaptability', 'Adjusting to new processes or changes'),
(8, 1, 'Customer Service Orientation', 'Providing quality service to clients'),
(9, 1, 'Accountability', 'Taking responsibility for tasks and outcomes'),
(10, 1, 'Attention to Detail', 'Ensuring accuracy in work and documentation'),
(11, 1, 'Compliance / Policy Adherence', 'Following company rules and procedures'),
(12, 1, 'Digital Literacy', 'Ability to use company systems and software'),
(13, 2, 'Recruitment Management', 'Managing hiring processes'),
(14, 2, 'Interviewing Skills', 'Conducting applicant interviews'),
(15, 2, 'Employee Relations', 'Handling employee concerns and disputes'),
(16, 2, 'HR Policy Management', 'Implementing HR policies'),
(17, 2, 'Performance Evaluation', 'Assessing employee performance'),
(18, 2, 'Training and Development', 'Managing employee training programs'),
(19, 2, 'HR Documentation', 'Maintaining employee records'),
(20, 3, 'Financial Reporting', 'Preparing financial reports'),
(21, 3, 'Budget Management', 'Managing department or company budgets'),
(22, 3, 'Accounting Principles', 'Knowledge of accounting standards'),
(23, 3, 'Cash Management', 'Handling company cash flow'),
(24, 3, 'Financial Analysis', 'Analyzing financial information'),
(25, 3, 'Audit Compliance', 'Ensuring financial records follow regulations'),
(26, 3, 'Loan Accounting', 'Recording loan transactions properly'),
(27, 4, 'Inventory Management', 'Managing supplies and inventory'),
(28, 4, 'Procurement', 'Purchasing goods and services'),
(29, 4, 'Supplier Coordination', 'Working with vendors and suppliers'),
(30, 4, 'Asset Management', 'Tracking company assets'),
(31, 4, 'Warehouse Operations', 'Managing storage and distribution'),
(32, 4, 'Logistics Planning', 'Planning supply movement and delivery'),
(33, 5, 'Loan Evaluation', 'Assessing loan applications'),
(34, 5, 'Credit Investigation', 'Verifying borrower information'),
(35, 5, 'Risk Assessment', 'Identifying financial risks'),
(36, 5, 'Client Interviewing', 'Interviewing loan applicants'),
(37, 5, 'Loan Processing', 'Processing loan documents'),
(38, 5, 'Loan Monitoring', 'Monitoring loan repayment'),
(39, 5, 'Debt Collection', 'Handling overdue payments'),
(40, 5, 'Field Investigation', 'Verifying client businesses in the field'),
(41, 6, 'Office Administration', 'Managing daily office operations'),
(42, 6, 'Document Management', 'Handling company documents'),
(43, 6, 'Scheduling', 'Organizing meetings and activities'),
(44, 6, 'Records Management', 'Maintaining company records'),
(45, 6, 'Internal Coordination', 'Communicating between departments');

-- --------------------------------------------------------

--
-- Table structure for table `competency_categories`
--

CREATE TABLE `competency_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `subtitle` varchar(100) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `competency_categories`
--

INSERT INTO `competency_categories` (`id`, `name`, `subtitle`, `department_id`) VALUES
(1, 'Common Competencies', 'All Employees', NULL),
(2, 'HR Department Competencies', 'Human Resources', 2),
(3, 'Finance Department Competencies', 'Accounting & Finance', 3),
(4, 'Logistics Department Competencies', 'Supply Chain & Operations', 4),
(5, 'Microfinance / CORE TRANSACTION', 'Core Lending Operations', 5),
(6, 'Administration Competencies', 'Office Management', 1);

-- --------------------------------------------------------

--
-- Table structure for table `competency_levels`
--

CREATE TABLE `competency_levels` (
  `id` int(11) NOT NULL,
  `rank_level` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `competency_levels`
--

INSERT INTO `competency_levels` (`id`, `rank_level`, `name`, `description`, `created_at`) VALUES
(1, 1, 'Basic', 'Has limited knowledge and requires close supervision', '2026-03-13 16:18:50'),
(2, 2, 'Intermediate', 'Can perform standard tasks with occasional guidance', '2026-03-13 16:18:50'),
(3, 3, 'Advanced', 'Can perform tasks independently and accurately', '2026-03-13 16:18:50'),
(4, 4, 'Expert', 'Can lead others, solve complex issues, and train staff', '2026-03-13 16:18:50');

-- --------------------------------------------------------

--
-- Table structure for table `competency_questions`
--

CREATE TABLE `competency_questions` (
  `id` int(11) NOT NULL,
  `competency_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('multiple_choice','true_false') NOT NULL DEFAULT 'multiple_choice',
  `option_a` varchar(255) DEFAULT NULL,
  `option_b` varchar(255) DEFAULT NULL,
  `option_c` varchar(255) DEFAULT NULL,
  `option_d` varchar(255) DEFAULT NULL,
  `correct_answer` varchar(10) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `competency_questions`
--

INSERT INTO `competency_questions` (`id`, `competency_id`, `question_text`, `question_type`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_answer`, `is_active`, `created_at`) VALUES
(1, 1, 'What is the most effective way to communicate instructions to coworkers?', 'multiple_choice', 'Ignore questions', 'Provide clear and direct explanations', 'Send incomplete messages', 'Avoid discussion', 'B', 1, '2026-03-15 10:47:53'),
(2, 1, 'Which behavior demonstrates good workplace communication?', 'multiple_choice', 'Interrupting others', 'Active listening', 'Ignoring feedback', 'Avoiding meetings', 'B', 1, '2026-03-15 10:47:53'),
(3, 2, 'What is an important part of teamwork?', 'multiple_choice', 'Working alone', 'Cooperation among members', 'Avoiding responsibility', 'Ignoring team goals', 'B', 1, '2026-03-15 10:47:53'),
(4, 2, 'Which action supports teamwork?', 'multiple_choice', 'Sharing ideas with teammates', 'Ignoring others', 'Refusing help', 'Working separately', 'A', 1, '2026-03-15 10:47:53'),
(5, 3, 'Integrity in the workplace means:', 'multiple_choice', 'Being dishonest', 'Being ethical and honest', 'Ignoring company rules', 'Blaming coworkers', 'B', 1, '2026-03-15 10:47:53'),
(6, 3, 'Which situation shows integrity?', 'multiple_choice', 'Hiding mistakes', 'Admitting errors honestly', 'Blaming colleagues', 'Ignoring problems', 'B', 1, '2026-03-15 10:47:53'),
(7, 5, 'Which practice improves time management?', 'multiple_choice', 'Delaying tasks', 'Prioritizing tasks', 'Ignoring deadlines', 'Working without planning', 'B', 1, '2026-03-15 10:47:53'),
(8, 5, 'What tool helps manage work schedules?', 'multiple_choice', 'Task planner', 'Random guessing', 'Ignoring tasks', 'Avoiding planning', 'A', 1, '2026-03-15 10:47:53'),
(9, 6, 'What is the first step in solving a workplace problem?', 'multiple_choice', 'Blaming coworkers', 'Identifying the problem', 'Ignoring the issue', 'Making random decisions', 'B', 1, '2026-03-15 10:47:53'),
(10, 6, 'Good problem solving requires:', 'multiple_choice', 'Careful analysis', 'Ignoring information', 'Avoiding responsibility', 'Guessing solutions', 'A', 1, '2026-03-15 10:47:53'),
(11, 1, 'What is the most effective way to communicate instructions at work?', 'multiple_choice', 'Ignore feedback', 'Use clear and direct language', 'Avoid discussion', 'Send incomplete messages', 'B', 1, '2026-03-15 11:29:25'),
(12, 2, 'Which behavior supports teamwork?', 'multiple_choice', 'Working alone', 'Helping colleagues finish tasks', 'Ignoring others', 'Refusing help', 'B', 1, '2026-03-15 11:29:25'),
(13, 3, 'Integrity in the workplace means:', 'multiple_choice', 'Honesty and ethical conduct', 'Blaming others', 'Ignoring rules', 'Taking credit for others work', 'A', 1, '2026-03-15 11:29:25'),
(14, 4, 'Professionalism means:', 'multiple_choice', 'Arriving late', 'Maintaining respectful conduct', 'Ignoring policies', 'Arguing with coworkers', 'B', 1, '2026-03-15 11:29:25'),
(15, 5, 'What improves time management?', 'multiple_choice', 'Delaying tasks', 'Prioritizing work tasks', 'Ignoring deadlines', 'Working without planning', 'B', 1, '2026-03-15 11:29:25'),
(16, 6, 'Problem solving begins with:', 'multiple_choice', 'Identifying the problem', 'Ignoring the issue', 'Blaming coworkers', 'Guessing solutions', 'A', 1, '2026-03-15 11:29:25'),
(17, 7, 'Adaptability means:', 'multiple_choice', 'Resisting change', 'Adjusting to new situations', 'Avoiding tasks', 'Ignoring instructions', 'B', 1, '2026-03-15 11:29:25'),
(18, 8, 'Customer service orientation means:', 'multiple_choice', 'Ignoring customers', 'Providing helpful service', 'Avoiding interaction', 'Refusing assistance', 'B', 1, '2026-03-15 11:29:25'),
(19, 9, 'Accountability means:', 'multiple_choice', 'Avoiding responsibility', 'Taking responsibility for actions', 'Blaming others', 'Ignoring tasks', 'B', 1, '2026-03-15 11:29:25'),
(20, 10, 'Attention to detail ensures:', 'multiple_choice', 'Accurate work', 'More mistakes', 'Ignoring information', 'Rushed results', 'A', 1, '2026-03-15 11:29:25'),
(21, 13, 'Recruitment management mainly involves:', 'multiple_choice', 'Hiring employees', 'Ignoring applicants', 'Avoiding interviews', 'Deleting resumes', 'A', 1, '2026-03-15 11:29:53'),
(22, 14, 'What is the purpose of an interview?', 'multiple_choice', 'Assess applicant suitability', 'Delay hiring', 'Ignore candidate skills', 'Cancel recruitment', 'A', 1, '2026-03-15 11:29:53'),
(23, 15, 'Employee relations focuses on:', 'multiple_choice', 'Managing employee concerns', 'Ignoring complaints', 'Avoiding communication', 'Reducing teamwork', 'A', 1, '2026-03-15 11:29:53'),
(24, 16, 'HR policy management ensures:', 'multiple_choice', 'Employees follow company rules', 'Ignoring procedures', 'Avoiding compliance', 'Changing policies randomly', 'A', 1, '2026-03-15 11:29:53'),
(25, 17, 'Performance evaluation helps:', 'multiple_choice', 'Measure employee performance', 'Ignore productivity', 'Avoid feedback', 'Reduce training', 'A', 1, '2026-03-15 11:29:53'),
(26, 20, 'Financial reporting involves:', 'multiple_choice', 'Preparing financial statements', 'Ignoring finances', 'Avoiding accounting', 'Deleting records', 'A', 1, '2026-03-15 11:30:05'),
(27, 21, 'Budget management means:', 'multiple_choice', 'Tracking expenses and income', 'Ignoring spending', 'Avoiding financial planning', 'Deleting transactions', 'A', 1, '2026-03-15 11:30:05'),
(28, 22, 'Accounting principles ensure:', 'multiple_choice', 'Accurate financial records', 'Random accounting', 'Ignoring standards', 'Deleting reports', 'A', 1, '2026-03-15 11:30:05'),
(29, 23, 'Cash management refers to:', 'multiple_choice', 'Managing cash flow', 'Ignoring funds', 'Avoiding transactions', 'Deleting cash records', 'A', 1, '2026-03-15 11:30:05'),
(30, 24, 'Financial analysis mean:', 'multiple_choice', 'Evaluating financial data', 'Ignoring reports', 'Deleting budgets', 'Avoiding calculations', 'A', 1, '2026-03-15 11:30:05'),
(31, 27, 'Inventory management ensures:', 'multiple_choice', 'Tracking stock levels', 'Ignoring supplies', 'Deleting inventory', 'Avoiding stock control', 'A', 1, '2026-03-15 11:30:17'),
(32, 28, 'Procurement involves:', 'multiple_choice', 'Purchasing goods and services', 'Ignoring suppliers', 'Deleting orders', 'Avoiding purchasing', 'A', 1, '2026-03-15 11:30:17'),
(33, 29, 'Supplier coordination means:', 'multiple_choice', 'Working with vendors', 'Ignoring suppliers', 'Deleting contracts', 'Avoiding communication', 'A', 1, '2026-03-15 11:30:17'),
(34, 30, 'Asset management focuses on:', 'multiple_choice', 'Tracking company assets', 'Ignoring equipment', 'Deleting records', 'Avoiding maintenance', 'A', 1, '2026-03-15 11:30:17'),
(35, 33, 'Loan evaluation involves:', 'multiple_choice', 'Assessing borrower eligibility', 'Ignoring applications', 'Deleting records', 'Avoiding investigation', 'A', 1, '2026-03-15 11:30:31'),
(36, 34, 'Credit investigation means:', 'multiple_choice', 'Verifying borrower information', 'Ignoring clients', 'Deleting files', 'Avoiding interviews', 'A', 1, '2026-03-15 11:30:31'),
(37, 35, 'Risk assessment means:', 'multiple_choice', 'Identifying potential financial risk', 'Ignoring danger', 'Avoiding analysis', 'Deleting reports', 'A', 1, '2026-03-15 11:30:31'),
(38, 36, 'Client interviewing helps:', 'multiple_choice', 'Understand borrower information', 'Ignore applicants', 'Avoid communication', 'Cancel loan process', 'A', 1, '2026-03-15 11:30:31'),
(39, 41, 'Office administration mainly involves:', 'multiple_choice', 'Managing office operations', 'Ignoring workflows', 'Avoiding meetings', 'Deleting schedules', 'A', 1, '2026-03-15 11:30:48'),
(40, 42, 'Document management ensures:', 'multiple_choice', 'Organized file storage', 'Deleting documents', 'Ignoring records', 'Random storage', 'A', 1, '2026-03-15 11:30:48'),
(41, 43, 'Scheduling meetings helps:', 'multiple_choice', 'Organize activities efficiently', 'Delay tasks', 'Avoid coordination', 'Ignore deadlines', 'A', 1, '2026-03-15 11:30:49'),
(42, 44, 'Records management ensures:', 'multiple_choice', 'Accurate record keeping', 'Deleting files', 'Ignoring records', 'Avoiding documentation', 'A', 1, '2026-03-15 11:30:49'),
(43, 45, 'Internal coordination means:', 'multiple_choice', 'Communication between departments', 'Ignoring coworkers', 'Avoiding meetings', 'Working separately', 'A', 1, '2026-03-15 11:30:49');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `DepartmentID` int(11) NOT NULL,
  `DepartmentName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`DepartmentID`, `DepartmentName`) VALUES
(1, 'Administration'),
(2, 'HR Department'),
(3, 'Finance Department'),
(4, 'Logistics Department'),
(5, 'Core Transaction Department');

-- --------------------------------------------------------

--
-- Table structure for table `emergency_contacts`
--

CREATE TABLE `emergency_contacts` (
  `ContactID` int(11) NOT NULL,
  `EmployeeID` int(11) NOT NULL,
  `ContactName` varchar(200) NOT NULL,
  `Relationship` varchar(50) DEFAULT NULL,
  `PhoneNumber` varchar(20) NOT NULL,
  `IsPrimary` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emergency_contacts`
--

INSERT INTO `emergency_contacts` (`ContactID`, `EmployeeID`, `ContactName`, `Relationship`, `PhoneNumber`, `IsPrimary`) VALUES
(1, 1, 'Andrie Suruiz', 'Father', '09223344556', 1),
(2, 2, 'Hero Baldon', 'Father', '09334455667', 1),
(3, 3, 'Daniela Magtangob', 'Wife', '09445566778', 1),
(4, 4, 'Jhustine', 'Father', '09312355667', 1),
(5, 7, 'Miguel', 'Father', '09132131212', 1),
(6, 6, 'Jean', 'Mother', '09204132131', 1),
(10, 14, 'joshua', 'Father', '09334455667', 1),
(11, 15, 'Suruiz Joshua Andrie Rivero', 'father', '09103840798', 1),
(12, 16, 'SOLIS', 'FATHER', '0922113344', 1),
(14, 18, 'earl', 'Father', '12345678911', 1),
(15, 19, 'joshua', 'Father', '09334455667', 1),
(16, 20, 'GLORY JEAN JOB', 'example', '09127381825', 1),
(19, 23, 'GLORY JEAN JOB', 'example', '09127381825', 1),
(20, 24, 'GLORY JEAN JOB', 'example', '09127381825', 1);

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `EmployeeID` int(11) NOT NULL,
  `EmployeeCode` varchar(20) DEFAULT NULL,
  `FirstName` varchar(100) NOT NULL,
  `MiddleName` varchar(100) DEFAULT NULL,
  `LastName` varchar(100) NOT NULL,
  `DateOfBirth` date NOT NULL,
  `Gender` varchar(20) DEFAULT NULL,
  `PersonalEmail` varchar(150) DEFAULT NULL,
  `PhoneNumber` varchar(20) DEFAULT NULL,
  `PermanentAddress` text DEFAULT NULL,
  `ProfilePhoto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`EmployeeID`, `EmployeeCode`, `FirstName`, `MiddleName`, `LastName`, `DateOfBirth`, `Gender`, `PersonalEmail`, `PhoneNumber`, `PermanentAddress`, `ProfilePhoto`) VALUES
(1, 'ADM20261001', 'Joshua', 'Rivero', 'Suruiz', '2004-04-06', 'Male', 'suruizandrie@gmail.com', '09111223344', 'Quezon City', NULL),
(2, 'ADM20261002', 'Red Gin', 'B', 'Baldon', '2005-04-06', 'Male', 'red@gmail.comm', '09111223344', 'Quezon City', 'img/profiles/profile_2_1771761386.jpg'),
(3, 'HRDS20261003', 'Noriel', 'H', 'Dimailig', '2004-05-06', 'Male', 'riverojosh19@gmail.com', '09555223344', 'Quezon City', NULL),
(4, 'HRS20261004', 'Earl', 'J.', 'Caber', '2004-04-02', 'Male', 'earl@gmail.com', '09321223344', 'Quezon City', NULL),
(6, 'HRM20261006', 'Glory', 'J', 'Job', '2001-04-04', 'Male', 'glory@gmail.comm', '09531223344', 'Quezon City', NULL),
(7, 'CA20261007', 'Miguel', 'M', 'Padre', '2005-05-03', 'Male', 'padre@gmail.com', '09535223344', 'Quezon City', NULL),
(8, 'PAY20261008', 'Daniella', 'M', 'Magtangob', '2004-02-03', 'Female', 'Daniella@gmail.com', '09532223344', 'Quezon City', NULL),
(10, 'SV20261009', 'Mike', NULL, 'Dabu', '0000-00-00', NULL, NULL, NULL, NULL, NULL),
(14, 'L-OFF20260009', 'Joshua', 'Rivero', 'Suruiz', '2004-04-06', 'Male', 'suruizjoshuaandrierivero@gmail.com', '09223311333', 'congressional', 'uploads/applications/IDPic_Suruiz_1772957354.jpg'),
(15, 'LM20260010', 'test', 'lang', 'three', '2004-04-06', 'Male', 'suruizjoshua72@gmail.com', '092211333444', 'dyan lang', 'uploads/applications/IDPic_three_1773035846.jpg'),
(16, 'HRO20260011', 'Johnmar', 'S.', 'Solis', '1996-01-09', 'Male', 'Solis@gmail.com', '09111240798', 'FAIRVIEW', 'uploads/applications/IDPic_Solis_1773069431.jpg'),
(18, 'LOG-OFF20260012', 'Earl', '', 'Alarcon', '2026-03-12', 'Male', 'earllaurencealarcon@gmail.com', '12346789111', '12345 brgy', NULL),
(19, 'LSA20260013', 'EARL', '', 'ALARCON', '2004-04-06', 'Male', 'lawrence@gmail.com', '0921111333', 'bcp', NULL),
(20, 'FACT20260014', 'buya', 'b', 'buya', '2004-04-15', 'Male', 'buya@gmail.com', '09123456678', 'testing', NULL),
(23, 'example20260015', 'denzel', 'g', 'Ortiz', '2026-03-16', 'Male', 'Ortiz@gmail.com', '09123456678', 'testing', NULL),
(24, 'FACT20260016', 'Miguel', 'b', 'Padre', '2004-03-03', 'Male', 'juanmiguelerdap69@gmail.com', '0920502123', 'testing', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employee_competencies`
--

CREATE TABLE `employee_competencies` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `competency_id` int(11) NOT NULL,
  `level_id` int(11) NOT NULL,
  `assessed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_competencies`
--

INSERT INTO `employee_competencies` (`id`, `employee_id`, `competency_id`, `level_id`, `assessed_at`) VALUES
(1, 18, 7, 1, '2026-03-14 16:57:23'),
(2, 18, 27, 1, '2026-03-14 16:57:24'),
(3, 18, 32, 1, '2026-03-14 16:57:24'),
(4, 18, 28, 1, '2026-03-14 16:57:24'),
(5, 18, 29, 2, '2026-03-14 16:57:24'),
(6, 18, 31, 2, '2026-03-14 16:57:24'),
(7, 18, 9, 1, '2026-03-14 16:57:23'),
(8, 18, 10, 1, '2026-03-14 16:57:24'),
(9, 18, 1, 1, '2026-03-14 16:57:24'),
(10, 18, 11, 1, '2026-03-14 16:57:24'),
(11, 18, 8, 2, '2026-03-14 16:57:24'),
(12, 18, 12, 1, '2026-03-14 16:57:24'),
(13, 18, 3, 3, '2026-03-14 16:57:24'),
(14, 18, 6, 2, '2026-03-14 16:57:24'),
(15, 18, 4, 1, '2026-03-14 16:57:24'),
(16, 18, 2, 3, '2026-03-14 16:57:24'),
(17, 18, 5, 2, '2026-03-14 16:57:24'),
(18, 18, 30, 2, '2026-03-14 16:57:24'),
(19, 19, 9, 1, '2026-03-14 17:00:17'),
(20, 2, 42, 4, '2026-03-15 16:05:13');

-- --------------------------------------------------------

--
-- Table structure for table `employee_leave_balances`
--

CREATE TABLE `employee_leave_balances` (
  `BalanceID` int(11) NOT NULL,
  `EmployeeID` int(11) NOT NULL,
  `LeaveTypeID` int(11) NOT NULL,
  `Year` year(4) NOT NULL,
  `TotalCredits` decimal(5,2) NOT NULL DEFAULT 0.00,
  `UsedCredits` decimal(5,2) NOT NULL DEFAULT 0.00,
  `RemainingCredits` decimal(5,2) NOT NULL DEFAULT 0.00,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_leave_balances`
--

INSERT INTO `employee_leave_balances` (`BalanceID`, `EmployeeID`, `LeaveTypeID`, `Year`, `TotalCredits`, `UsedCredits`, `RemainingCredits`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 10, 1, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(2, 10, 2, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(3, 10, 3, '2026', 3.00, 0.00, 3.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(4, 10, 4, '2026', 105.00, 0.00, 105.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(5, 10, 5, '2026', 0.00, 0.00, 0.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(6, 20, 1, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(7, 20, 2, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(8, 20, 3, '2026', 3.00, 0.00, 3.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(9, 20, 4, '2026', 105.00, 0.00, 105.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(10, 20, 5, '2026', 0.00, 0.00, 0.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(11, 8, 1, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(12, 8, 2, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(13, 8, 3, '2026', 3.00, 0.00, 3.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(14, 8, 4, '2026', 105.00, 0.00, 105.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(15, 8, 5, '2026', 0.00, 0.00, 0.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(16, 4, 1, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(17, 4, 2, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(18, 4, 3, '2026', 3.00, 0.00, 3.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(19, 4, 4, '2026', 105.00, 0.00, 105.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(20, 4, 5, '2026', 0.00, 0.00, 0.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(21, 18, 1, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(22, 18, 2, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(23, 18, 3, '2026', 3.00, 0.00, 3.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(24, 18, 4, '2026', 105.00, 0.00, 105.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(25, 18, 5, '2026', 0.00, 0.00, 0.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(26, 6, 1, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(27, 6, 2, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(28, 6, 3, '2026', 3.00, 0.00, 3.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(29, 6, 4, '2026', 105.00, 0.00, 105.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(30, 6, 5, '2026', 0.00, 0.00, 0.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(31, 24, 1, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(32, 24, 2, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(33, 24, 3, '2026', 3.00, 0.00, 3.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(34, 24, 4, '2026', 105.00, 0.00, 105.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(35, 24, 5, '2026', 0.00, 0.00, 0.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(36, 19, 1, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(37, 19, 2, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(38, 19, 3, '2026', 3.00, 0.00, 3.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(39, 19, 4, '2026', 105.00, 0.00, 105.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(40, 19, 5, '2026', 0.00, 0.00, 0.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(41, 23, 1, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(42, 23, 2, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(43, 23, 3, '2026', 3.00, 0.00, 3.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(44, 23, 4, '2026', 105.00, 0.00, 105.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(45, 23, 5, '2026', 0.00, 0.00, 0.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(46, 7, 1, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(47, 7, 2, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(48, 7, 3, '2026', 3.00, 0.00, 3.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(49, 7, 4, '2026', 105.00, 0.00, 105.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(50, 7, 5, '2026', 0.00, 0.00, 0.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(51, 2, 1, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(52, 2, 2, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(53, 2, 3, '2026', 3.00, 0.00, 3.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(54, 2, 4, '2026', 105.00, 0.00, 105.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(55, 2, 5, '2026', 0.00, 0.00, 0.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(56, 3, 1, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(57, 3, 2, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(58, 3, 3, '2026', 3.00, 0.00, 3.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(59, 3, 4, '2026', 105.00, 0.00, 105.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(60, 3, 5, '2026', 0.00, 0.00, 0.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(61, 16, 1, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(62, 16, 2, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(63, 16, 3, '2026', 3.00, 0.00, 3.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(64, 16, 4, '2026', 105.00, 0.00, 105.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(65, 16, 5, '2026', 0.00, 0.00, 0.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(66, 1, 1, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(67, 1, 2, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(68, 1, 3, '2026', 3.00, 0.00, 3.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(69, 1, 4, '2026', 105.00, 0.00, 105.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(70, 1, 5, '2026', 0.00, 0.00, 0.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(71, 15, 1, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(72, 15, 2, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(73, 15, 3, '2026', 3.00, 0.00, 3.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(74, 15, 4, '2026', 105.00, 0.00, 105.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(75, 15, 5, '2026', 0.00, 0.00, 0.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(76, 14, 1, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(77, 14, 2, '2026', 15.00, 0.00, 15.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(78, 14, 3, '2026', 3.00, 0.00, 3.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(79, 14, 4, '2026', 105.00, 0.00, 105.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34'),
(80, 14, 5, '2026', 0.00, 0.00, 0.00, '2026-03-15 17:14:34', '2026-03-15 17:14:34');

-- --------------------------------------------------------

--
-- Table structure for table `employee_update_requests`
--

CREATE TABLE `employee_update_requests` (
  `RequestID` int(11) NOT NULL,
  `EmployeeID` int(11) NOT NULL,
  `RequestType` varchar(100) NOT NULL DEFAULT 'Update Information',
  `RequestData` text NOT NULL,
  `ProofPath` varchar(255) DEFAULT NULL,
  `Status` varchar(50) DEFAULT 'Pending Supervisor',
  `RequestDate` datetime NOT NULL DEFAULT current_timestamp(),
  `ReviewedBy` int(11) DEFAULT NULL,
  `ReviewDate` datetime DEFAULT NULL,
  `SupervisorID` int(11) DEFAULT NULL,
  `SupervisorDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_update_requests`
--

INSERT INTO `employee_update_requests` (`RequestID`, `EmployeeID`, `RequestType`, `RequestData`, `ProofPath`, `Status`, `RequestDate`, `ReviewedBy`, `ReviewDate`, `SupervisorID`, `SupervisorDate`) VALUES
(1, 3, 'Update Information', '{\"BankName\":\"BDO\",\"BankAccountNumber\":\"222-444-332-222\"}', NULL, 'Approved', '2026-02-20 23:03:38', 3, '2026-02-20 23:13:16', NULL, NULL),
(2, 3, 'Update Information', '{\"BankName\":\"BDO\",\"BankAccountNumber\":\"222-444-332-222\"}', NULL, 'Approved', '2026-02-21 01:08:30', 3, '2026-02-21 01:09:23', NULL, NULL),
(3, 3, 'Update Information', '{\"BankName\":\"BDO\",\"BankAccountNumber\":\"222-444-332-222\",\"AccountType\":\"Payroll\"}', NULL, 'Approved', '2026-02-21 01:19:33', 3, '2026-02-21 01:22:16', NULL, NULL),
(4, 2, 'Update Information', '{\"BankName\":\"BDO\",\"BankAccountNumber\":\"222-411-332-222\",\"AccountType\":\"Payroll\",\"TINNumber\":\"321-436-789-000\",\"SSSNumber\":\"65-1214567-8\",\"PhilHealthNumber\":\"14-050223456-7\",\"PagIBIGNumber\":\"1332-3434-5656\"}', NULL, 'Approved', '2026-03-01 18:21:01', 6, '2026-03-01 18:34:32', 2, '2026-03-01 18:31:44'),
(5, 2, 'Update Information', '{\"BankName\":\"BDO\",\"BankAccountNumber\":\"229-411-332-222\",\"AccountType\":\"Checking\",\"TINNumber\":\"361-436-789-000\",\"SSSNumber\":\"95-1214567-8\",\"PhilHealthNumber\":\"94-050223456-7\",\"PagIBIGNumber\":\"9332-3434-5656\"}', 'uploads/proofs/proof_1772364521_69a422e9e584c.jpg', 'Approved', '2026-03-01 19:28:41', 6, '2026-03-01 20:41:13', 10, '2026-03-01 19:59:51');

-- --------------------------------------------------------

--
-- Table structure for table `employmentinformation`
--

CREATE TABLE `employmentinformation` (
  `EmploymentID` int(11) NOT NULL,
  `EmployeeID` int(11) DEFAULT NULL,
  `DepartmentID` int(11) DEFAULT NULL,
  `PositionID` int(11) DEFAULT NULL,
  `SalaryGradeID` int(11) DEFAULT NULL,
  `BaseSalary` decimal(15,2) NOT NULL DEFAULT 0.00,
  `SalaryType` enum('Monthly','Daily','Hourly') NOT NULL DEFAULT 'Monthly',
  `HiringDate` date NOT NULL,
  `WorkEmail` varchar(150) DEFAULT NULL,
  `EmploymentStatus` varchar(50) DEFAULT NULL,
  `DigitalResume` text DEFAULT NULL,
  `IDPicture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employmentinformation`
--

INSERT INTO `employmentinformation` (`EmploymentID`, `EmployeeID`, `DepartmentID`, `PositionID`, `SalaryGradeID`, `BaseSalary`, `SalaryType`, `HiringDate`, `WorkEmail`, `EmploymentStatus`, `DigitalResume`, `IDPicture`) VALUES
(1, 1, 1, 1, 6, 92882.00, 'Monthly', '2026-02-08', 'suruiz.joshuabcp@gmail.com', 'Regular', NULL, NULL),
(2, 2, 1, 1, 6, 94694.00, 'Monthly', '2026-02-09', 'suruizandrie@gmail.com', 'Regular', NULL, NULL),
(3, 3, 2, 2, 2, 25500.00, 'Hourly', '2026-02-09', 'riverojosh19@gmail.com', 'Regular', NULL, NULL),
(4, 4, 2, 4, 1, 17146.00, 'Hourly', '2026-02-08', 'earl@gmail.com', 'Regular', NULL, NULL),
(5, 6, 2, 3, 5, 60158.00, 'Monthly', '2026-02-09', 'glory@gmail.com', 'Regular', NULL, NULL),
(6, 7, 2, 5, 4, 45676.00, 'Monthly', '2026-02-09', 'padre@gmail.com', 'Regular', NULL, NULL),
(7, 8, 2, 6, 3, 29000.00, 'Hourly', '2026-02-09', 'Daniella@gmail.com', 'Regular', NULL, NULL),
(8, 10, 2, 7, 4, 41000.00, 'Monthly', '2026-02-09', 'mike@gmail.com', 'Regular', NULL, NULL),
(12, 14, 5, 13, 3, 29000.00, 'Monthly', '2026-03-09', NULL, 'Probationary', 'uploads/applications/Resume_Suruiz_1772957354.pdf', 'uploads/applications/IDPic_Suruiz_1772957354.jpg'),
(13, 15, 4, 15, 5, 54000.00, 'Monthly', '2026-03-09', NULL, 'Probationary', 'uploads/applications/Resume_three_1773035846.pdf,uploads/applications/GovID_three_1773035846.png,uploads/applications/Clearance_three_1773035846.png,uploads/applications/TOR_three_1773035846.png', 'uploads/applications/IDPic_three_1773035846.jpg'),
(14, 16, 2, 16, 3, 29000.00, 'Monthly', '2026-03-09', NULL, 'Probationary', 'uploads/applications/Resume_Solis_1773069431.pdf,uploads/applications/GovID_Solis_1773069431.jpg,uploads/applications/Clearance_Solis_1773069431.jpg,uploads/applications/TOR_Solis_1773069431.jpg', 'uploads/applications/IDPic_Solis_1773069431.jpg'),
(16, 18, 4, 10, 3, 29000.00, 'Monthly', '2026-03-12', NULL, 'Probationary', 'uploads/applications/Resume_Alarcon_1773275721.pdf', NULL),
(17, 19, 5, 12, 6, 85000.00, 'Monthly', '2026-03-12', NULL, 'Probationary', 'uploads/applications/Resume_ALARCON_1773160934.pdf', NULL),
(18, 20, 1, 26, 1, 16000.00, 'Monthly', '2026-03-15', NULL, 'Probationary', 'uploads/applications/Resume_buya_1773557679.docx', NULL),
(21, 23, 1, 27, 6, 85000.00, 'Monthly', '2026-03-15', NULL, 'Probationary', 'uploads/applications/Resume_Ortiz_1773592385.pdf', NULL),
(22, 24, 1, 26, 1, 16000.00, 'Monthly', '2026-03-15', NULL, 'Probationary', 'uploads/applications/Resume_Padre_1773593370.pdf', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `final_performance_rating`
--

CREATE TABLE `final_performance_rating` (
  `EvaluationID` int(11) NOT NULL,
  `EmployeeID` int(11) NOT NULL,
  `period_id` int(11) NOT NULL,
  `FinalRating` decimal(4,2) NOT NULL,
  `EvaluationStatus` enum('Finalized') DEFAULT 'Finalized',
  `FinalApproverID` int(11) DEFAULT NULL,
  `FinalizedDate` datetime DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `final_performance_rating`
--

INSERT INTO `final_performance_rating` (`EvaluationID`, `EmployeeID`, `period_id`, `FinalRating`, `EvaluationStatus`, `FinalApproverID`, `FinalizedDate`, `UpdatedAt`) VALUES
(1, 1, 1, 4.00, 'Finalized', 1, '2026-02-25 00:00:00', '2026-02-25 14:23:23'),
(2, 2, 1, 5.00, 'Finalized', 1, '2026-02-25 00:00:00', '2026-02-25 14:23:23'),
(3, 3, 1, 3.00, 'Finalized', 1, '2026-02-24 00:00:00', '2026-02-25 14:23:23'),
(4, 4, 1, 4.00, 'Finalized', 1, '2026-02-24 00:00:00', '2026-02-25 14:23:23'),
(5, 6, 1, 5.00, 'Finalized', 1, '2026-02-24 00:00:00', '2026-02-25 14:23:23'),
(6, 7, 1, 5.00, 'Finalized', 1, '2026-02-25 00:00:00', '2026-02-25 14:23:23');

-- --------------------------------------------------------

--
-- Table structure for table `general_ledger`
--

CREATE TABLE `general_ledger` (
  `id` int(11) NOT NULL,
  `transaction_date` datetime DEFAULT current_timestamp(),
  `reference_id` varchar(50) DEFAULT NULL,
  `account_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `debit` decimal(15,2) DEFAULT 0.00,
  `credit` decimal(15,2) DEFAULT 0.00,
  `balance` decimal(15,2) DEFAULT 0.00,
  `status` enum('Posted','Pending','Voided') DEFAULT 'Posted',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `general_ledger`
--

INSERT INTO `general_ledger` (`id`, `transaction_date`, `reference_id`, `account_name`, `description`, `debit`, `credit`, `balance`, `status`, `created_at`) VALUES
(1, '2026-03-11 01:02:37', 'PAY-PR-2026-943', 'Salaries & Wages Payable', 'Payroll Disbursement for Batch PR-2026-943', 275615.74, 0.00, 0.00, 'Posted', '2026-03-10 17:02:37'),
(2, '2026-03-11 15:55:42', 'PAY-PR-2026-393', 'Salaries & Wages Payable', 'Payroll Disbursement for Batch PR-2026-393', 275615.74, 0.00, 0.00, 'Posted', '2026-03-11 07:55:42'),
(3, '2026-03-11 21:25:36', 'PAY-PR-2026-524', 'Salaries & Wages Payable', 'Payroll Disbursement for Batch PR-2026-524', 275615.74, 0.00, 0.00, 'Posted', '2026-03-11 13:25:36'),
(4, '2026-03-11 21:34:37', 'PAY-PR-2026-393', 'Salaries & Wages Payable', 'Payroll Disbursement for Batch PR-2026-393', 275615.74, 0.00, 0.00, 'Posted', '2026-03-11 13:34:37'),
(5, '2026-03-12 00:43:59', 'PAY-PR-2026-638', 'Salaries & Wages Payable', 'Payroll Disbursement for Batch PR-2026-638', 275615.74, 0.00, 0.00, 'Posted', '2026-03-11 16:43:59'),
(6, '2026-03-12 02:35:11', 'PAY-PR-2026-083', 'Salaries & Wages Payable', 'Payroll Disbursement for Batch PR-2026-083', 275615.74, 0.00, 0.00, 'Posted', '2026-03-11 18:35:11'),
(7, '2026-03-12 08:42:51', 'PAY-PR-2026-199', 'Salaries & Wages Payable', 'Payroll Disbursement for Batch PR-2026-199', 292501.54, 0.00, 0.00, 'Posted', '2026-03-12 00:42:51');

-- --------------------------------------------------------

--
-- Table structure for table `grade_allowances`
--

CREATE TABLE `grade_allowances` (
  `GradeAllowanceID` int(11) NOT NULL,
  `period_id` int(11) DEFAULT NULL,
  `SalaryGradeID` int(11) NOT NULL,
  `AllowanceTypeID` int(11) NOT NULL,
  `Amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grade_allowances`
--

INSERT INTO `grade_allowances` (`GradeAllowanceID`, `period_id`, `SalaryGradeID`, `AllowanceTypeID`, `Amount`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 1, 1, 1, 2500.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15'),
(2, 1, 1, 2, 1000.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15'),
(3, 1, 1, 3, 400.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15'),
(4, 1, 1, 4, 1500.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15'),
(5, 1, 1, 6, 500.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15'),
(6, 1, 2, 1, 2500.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15'),
(7, 1, 2, 2, 1500.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15'),
(8, 1, 2, 3, 400.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15'),
(9, 1, 2, 4, 2500.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15'),
(10, 1, 2, 6, 800.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15'),
(11, 1, 3, 1, 2500.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15'),
(12, 1, 3, 2, 2000.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15'),
(13, 1, 3, 3, 400.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15'),
(14, 1, 3, 4, 3500.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15'),
(15, 1, 3, 6, 1200.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15'),
(16, 1, 4, 1, 2500.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15'),
(17, 1, 4, 2, 2500.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15'),
(18, 1, 4, 3, 400.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15'),
(19, 1, 4, 4, 5000.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15'),
(20, 1, 4, 6, 1500.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15'),
(21, 1, 5, 1, 2500.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15'),
(22, 1, 5, 2, 3000.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15'),
(23, 1, 5, 3, 400.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15'),
(24, 1, 5, 4, 7000.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15'),
(25, 1, 5, 6, 2000.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15'),
(26, 1, 6, 1, 2500.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15'),
(27, 1, 6, 2, 3500.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15'),
(28, 1, 6, 3, 400.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15'),
(29, 1, 6, 4, 10000.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15'),
(30, 1, 6, 6, 3000.00, '2026-02-25 17:30:06', '2026-03-01 04:19:15');

-- --------------------------------------------------------

--
-- Table structure for table `holidays`
--

CREATE TABLE `holidays` (
  `HolidayID` int(11) NOT NULL,
  `HolidayDate` date NOT NULL,
  `HolidayName` varchar(150) NOT NULL,
  `HolidayTypeID` int(11) NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `holiday_type`
--

CREATE TABLE `holiday_type` (
  `HolidayTypeID` int(11) NOT NULL,
  `TypeCode` varchar(20) NOT NULL,
  `TypeName` varchar(100) NOT NULL,
  `PayMultiplier` decimal(5,2) NOT NULL DEFAULT 0.00,
  `IsPaid` tinyint(1) NOT NULL DEFAULT 0,
  `Description` text DEFAULT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `holiday_type`
--

INSERT INTO `holiday_type` (`HolidayTypeID`, `TypeCode`, `TypeName`, `PayMultiplier`, `IsPaid`, `Description`, `IsActive`) VALUES
(1, 'REG', 'Regular Holiday', 2.00, 1, 'Legal regular holiday', 1),
(2, 'SPEC', 'Special Non-Working', 1.30, 1, 'Special non-working holiday', 1),
(3, 'UNWRK', 'Unworked Regular Holiday', 1.00, 1, 'Paid holiday not worked', 1),
(4, 'FORCE', 'Force No Work', 0.00, 0, 'No work due to declaration', 1);

-- --------------------------------------------------------

--
-- Table structure for table `interview_evaluations`
--

CREATE TABLE `interview_evaluations` (
  `EvaluationID` int(11) NOT NULL,
  `ApplicantID` int(11) NOT NULL,
  `InterviewerID` int(11) NOT NULL,
  `TechnicalRating` int(11) DEFAULT 0,
  `CommunicationRating` int(11) DEFAULT 0,
  `FinancialRating` int(11) DEFAULT 0,
  `ReliabilityRating` int(11) DEFAULT 0,
  `AverageRating` decimal(3,2) DEFAULT 0.00,
  `Rating` int(11) NOT NULL,
  `Comments` text NOT NULL,
  `Decision` varchar(50) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `interview_evaluations`
--

INSERT INTO `interview_evaluations` (`EvaluationID`, `ApplicantID`, `InterviewerID`, `TechnicalRating`, `CommunicationRating`, `FinancialRating`, `ReliabilityRating`, `AverageRating`, `Rating`, `Comments`, `Decision`, `CreatedAt`) VALUES
(1, 1, 2, 4, 5, 5, 4, 4.50, 0, 'goods', 'Strong Hire', '2026-03-08 15:16:02'),
(2, 9, 2, 4, 4, 5, 3, 4.00, 0, 'tst', 'Strong Hire', '2026-03-09 06:01:03'),
(3, 10, 2, 4, 3, 4, 3, 3.50, 0, 'NICE', 'Potential Hire', '2026-03-09 15:19:26'),
(4, 2, 2, 3, 3, 3, 5, 3.50, 0, 'Test', 'Potential Hire', '2026-03-10 03:29:47'),
(5, 2, 2, 3, 3, 3, 5, 3.50, 0, 'Test', 'Potential Hire', '2026-03-10 03:29:51'),
(6, 15, 4, 5, 3, 5, 4, 4.25, 0, 'good enough.', 'Strong Hire', '2026-03-12 00:37:02'),
(7, 11, 4, 5, 4, 5, 4, 4.50, 0, 'Good', 'Strong Hire', '2026-03-12 03:14:37'),
(8, 17, 2, 3, 5, 4, 5, 4.25, 0, 'GOOD', 'Strong Hire', '2026-03-15 13:11:44'),
(9, 18, 2, 4, 5, 5, 5, 4.75, 0, 'good', 'Strong Hire', '2026-03-15 15:43:42'),
(10, 19, 2, 5, 5, 5, 5, 5.00, 0, 'good', 'Strong Hire', '2026-03-15 16:34:50'),
(11, 20, 2, 5, 5, 5, 5, 5.00, 0, 'test', 'Strong Hire', '2026-03-15 16:52:27');

-- --------------------------------------------------------

--
-- Table structure for table `interview_schedules`
--

CREATE TABLE `interview_schedules` (
  `ScheduleID` int(11) NOT NULL,
  `ApplicantID` int(11) NOT NULL,
  `InterviewerID` int(11) NOT NULL,
  `InterviewDate` date NOT NULL,
  `InterviewTime` time NOT NULL,
  `InterviewMode` enum('Online','Face-to-Face') NOT NULL,
  `LocationOrLink` text DEFAULT NULL,
  `Notes` text DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `interview_schedules`
--

INSERT INTO `interview_schedules` (`ScheduleID`, `ApplicantID`, `InterviewerID`, `InterviewDate`, `InterviewTime`, `InterviewMode`, `LocationOrLink`, `Notes`, `CreatedAt`) VALUES
(1, 1, 2, '2026-03-09', '19:00:00', 'Online', 'https://meet.google.com/kjy-bfsv-uhx', 'link', '2026-03-08 10:32:54'),
(2, 9, 11, '2026-03-09', '14:01:00', 'Online', 'https://meet.google.com/exq-ndjb-jmz', '', '2026-03-09 05:59:30'),
(9, 10, 1, '2026-03-10', '09:00:00', 'Face-to-Face', 'https://meet.google.com/exq-ndjb-jmz', '', '2026-03-09 15:18:22'),
(10, 2, 4, '2026-03-11', '09:00:00', 'Online', 'Https', '', '2026-03-10 03:29:14'),
(11, 11, 1, '2026-03-12', '09:00:00', 'Online', 'https://meet.google.com/kjy-bfsv-uhx', '', '2026-03-11 11:06:29'),
(12, 15, 4, '2026-03-13', '09:00:00', 'Face-to-Face', '090913', '', '2026-03-12 00:36:08'),
(13, 16, 4, '2026-03-13', '09:00:00', 'Face-to-Face', '123445', '', '2026-03-12 03:13:40'),
(14, 17, 10, '2026-03-16', '09:00:00', 'Face-to-Face', 'https://meet.google.com/kjy-bfsv-uhx', '', '2026-03-15 13:11:06'),
(15, 18, 10, '2026-03-16', '09:00:00', 'Online', 'https://meet.google.com/kjy-bfsv-uhx', '', '2026-03-15 15:42:58'),
(16, 19, 20, '2026-03-16', '09:00:00', 'Face-to-Face', '11', '', '2026-03-15 16:33:46'),
(17, 20, 10, '2026-03-16', '09:00:00', 'Face-to-Face', 'https://meet.google.com/kjy-bfsv-uhx', '', '2026-03-15 16:50:10');

-- --------------------------------------------------------

--
-- Table structure for table `job_postings`
--

CREATE TABLE `job_postings` (
  `PostID` int(11) NOT NULL,
  `RequisitionID` int(11) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Department` varchar(100) DEFAULT NULL,
  `Location` varchar(100) DEFAULT 'Metro Manila',
  `JobType` enum('Full-time','Part-time','Contract','Temporary') DEFAULT 'Full-time',
  `SalaryType` varchar(50) DEFAULT NULL,
  `SalaryRange` varchar(100) DEFAULT NULL,
  `Category` enum('Technology','Design','Marketing','Human Resources','Finance','Operations') DEFAULT 'Operations',
  `Description` text DEFAULT NULL,
  `Responsibilities` text DEFAULT NULL,
  `Requirements` text DEFAULT NULL,
  `Status` enum('Live','Closed','Archived') DEFAULT 'Live',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_postings`
--

INSERT INTO `job_postings` (`PostID`, `RequisitionID`, `Title`, `Department`, `Location`, `JobType`, `SalaryType`, `SalaryRange`, `Category`, `Description`, `Responsibilities`, `Requirements`, `Status`, `CreatedAt`) VALUES
(1, 1, 'Loan Officers', 'Core Transaction Department', 'Quezon City', 'Full-time', 'Monthly', '₱29k - 42k', '', 'joshhhhhhhhh', 'test', 'test', 'Live', '2026-03-08 05:32:48'),
(2, 2, 'Logistic Manager', 'Logistics Department', 'Quezon City', 'Full-time', 'Monthly', '₱54k - 75k', '', 'TEST', 'TEST', 'TEST', 'Live', '2026-03-09 05:49:47'),
(3, 4, 'HR Officer', 'HR Department', 'Quezon City', 'Full-time', 'Monthly', '₱29k - 42k', '', 'TEST', 'TEST', 'TEST', 'Live', '2026-03-09 15:05:25'),
(4, 10, 'Logistics Officer', 'Logistics Department', 'Quezon City', 'Full-time', 'Monthly', '₱29k - 42k', '', 'Example', 'Example ', 'Example ', 'Live', '2026-03-10 03:11:20'),
(5, 11, 'Loan Service Associates', 'Core Transaction Department', 'Quezon City', 'Full-time', 'Monthly', '₱81k - 120k', '', 'Example', 'Exampld', 'Example', 'Live', '2026-03-10 03:19:34'),
(6, 8, 'Finance Manager', 'Finance Department', 'Quezon City', 'Full-time', 'Monthly', '₱54k - 75k', '', 'test', 'test', 'test', 'Live', '2026-03-11 10:04:02'),
(7, 13, 'Logistics Officer', 'Logistics Department', 'Quezon City', 'Full-time', 'Monthly', '₱29k - 42k', '', 'Hiring', 'Approval', 'Documents', 'Live', '2026-03-12 00:30:42'),
(8, 14, 'Finance Manager', 'Finance Department', 'Quezon City', 'Full-time', 'Monthly', '₱54k - 75k', '', 'Hiring', 'Example', 'Resume', 'Live', '2026-03-12 03:11:22'),
(11, 20, 'SYSTEM ADMINISTRATOR', 'Administration', 'Quezon City', 'Full-time', 'Monthly', '₱16k - 20k', '', 'Manages an organization\'s IT infrastructure, including servers, networks, and software, ensuring they run securely, efficiently, and with high uptime.', 'Office Administration: Managing daily office operations\r\nDocument Management: Handling company documents\r\nScheduling: Organizing meetings and activities\r\nRecords Management: Maintaining company records\r\nInternal Coordination: Communicating between departments', 'Bachelor’s degree in a relevant field\r\nAt least 1–2 years of experience in a related field\r\nMinimum 3 years of work experience\r\nFresh graduates are welcome to apply\r\nRelevant professional certifications are an advantage but not required\r\nGood moral character and professional attitude', 'Live', '2026-03-15 06:42:33'),
(12, 21, 'example', 'Administration', 'Quezon City', 'Full-time', 'Monthly', '₱81k - 120k', '', 'testing', 'Office Administration: Managing daily office operations\r\nDocument Management: Handling company documents\r\nScheduling: Organizing meetings and activities\r\nRecords Management: Maintaining company records\r\nInternal Coordination: Communicating between departments', 'Bachelor’s degree in a relevant field\r\nAt least 1–2 years of experience in a related field\r\nMinimum 3 years of work experience\r\nFresh graduates are welcome to apply\r\nRelevant professional certifications are an advantage but not required\r\nGood moral character and professional attitude', 'Live', '2026-03-15 15:40:48');

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `LeaveRequestID` int(11) NOT NULL,
  `EmployeeID` int(11) NOT NULL,
  `LeaveTypeID` int(11) NOT NULL,
  `StartDate` date NOT NULL,
  `EndDate` date NOT NULL,
  `TotalDays` decimal(5,2) NOT NULL,
  `Reason` text DEFAULT NULL,
  `Status` enum('PENDING','APPROVED_BY_OFFICER','APPROVED_BY_HR','REJECTED','CANCELLED') NOT NULL DEFAULT 'PENDING',
  `OfficerApprovedBy` int(11) DEFAULT NULL,
  `HRApprovedBy` int(11) DEFAULT NULL,
  `OfficerNotes` text DEFAULT NULL,
  `HRNotes` text DEFAULT NULL,
  `AttachmentPath` varchar(500) DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_requests`
--

INSERT INTO `leave_requests` (`LeaveRequestID`, `EmployeeID`, `LeaveTypeID`, `StartDate`, `EndDate`, `TotalDays`, `Reason`, `Status`, `OfficerApprovedBy`, `HRApprovedBy`, `OfficerNotes`, `HRNotes`, `AttachmentPath`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 2, 1, '2026-03-19', '2026-03-23', 5.00, 'test', 'PENDING', NULL, NULL, NULL, NULL, 'uploads/leaves/leave_1773595102_2.jpg', '2026-03-15 17:18:22', '2026-03-15 17:18:22');

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

CREATE TABLE `leave_types` (
  `LeaveTypeID` int(11) NOT NULL,
  `LeaveName` varchar(50) NOT NULL,
  `IsPaid` tinyint(1) NOT NULL DEFAULT 1,
  `DefaultCredits` decimal(5,2) NOT NULL DEFAULT 0.00,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_types`
--

INSERT INTO `leave_types` (`LeaveTypeID`, `LeaveName`, `IsPaid`, `DefaultCredits`, `CreatedAt`) VALUES
(1, 'Vacation Leave', 1, 15.00, '2026-03-15 17:14:34'),
(2, 'Sick Leave', 1, 15.00, '2026-03-15 17:14:34'),
(3, 'Emergency Leave', 1, 3.00, '2026-03-15 17:14:34'),
(4, 'Maternity/Paternity Leave', 1, 105.00, '2026-03-15 17:14:34'),
(5, 'Leave Without Pay', 0, 0.00, '2026-03-15 17:14:34');

-- --------------------------------------------------------

--
-- Table structure for table `master_data_dispatches`
--

CREATE TABLE `master_data_dispatches` (
  `DispatchID` int(11) NOT NULL,
  `EmployeeID` int(11) NOT NULL,
  `DispatchedBy` varchar(100) NOT NULL,
  `DispatchDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `Status` enum('Pending','Received','Rejected') DEFAULT 'Pending',
  `Remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_data_dispatches`
--

INSERT INTO `master_data_dispatches` (`DispatchID`, `EmployeeID`, `DispatchedBy`, `DispatchDate`, `Status`, `Remarks`) VALUES
(1, 1, 'Red Gin Baldon', '2026-03-09 17:03:56', '', NULL),
(2, 2, 'Red Gin Baldon', '2026-03-09 17:03:56', 'Pending', NULL),
(3, 3, 'Red Gin Baldon', '2026-03-09 17:03:56', 'Pending', NULL),
(4, 4, 'Red Gin Baldon', '2026-03-09 17:03:56', 'Pending', NULL),
(5, 6, 'Red Gin Baldon', '2026-03-09 17:03:57', 'Pending', NULL),
(6, 7, 'Red Gin Baldon', '2026-03-09 17:03:57', 'Pending', NULL),
(7, 8, 'Red Gin Baldon', '2026-03-09 17:03:57', 'Pending', NULL),
(8, 10, 'Red Gin Baldon', '2026-03-09 17:03:57', 'Pending', NULL),
(9, 14, 'Red Gin Baldon', '2026-03-09 17:03:57', 'Pending', NULL),
(10, 15, 'Red Gin Baldon', '2026-03-09 17:03:57', 'Pending', NULL),
(11, 16, 'Red Gin Baldon', '2026-03-09 17:03:57', 'Pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `merit_matrix_settings`
--

CREATE TABLE `merit_matrix_settings` (
  `matrix_id` int(11) NOT NULL,
  `period_id` int(11) NOT NULL,
  `performance_rating` decimal(3,1) DEFAULT NULL,
  `compa_ratio_range` enum('Low','Mid','High') DEFAULT NULL,
  `min_increase_pct` decimal(5,2) DEFAULT NULL,
  `max_increase_pct` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `merit_matrix_settings`
--

INSERT INTO `merit_matrix_settings` (`matrix_id`, `period_id`, `performance_rating`, `compa_ratio_range`, `min_increase_pct`, `max_increase_pct`) VALUES
(1, 1, 5.0, 'Low', 4.00, 5.00),
(2, 1, 5.0, 'Mid', 3.00, 4.00),
(3, 1, 5.0, 'High', 2.00, 3.00),
(4, 1, 4.0, 'Low', 3.00, 4.00),
(5, 1, 4.0, 'Mid', 2.00, 3.00),
(6, 1, 4.0, 'High', 1.00, 2.00),
(7, 1, 3.0, 'Low', 2.00, 3.00),
(8, 1, 3.0, 'Mid', 1.00, 2.00),
(9, 1, 3.0, 'High', 0.00, 1.00);

-- --------------------------------------------------------

--
-- Table structure for table `merit_proposals`
--

CREATE TABLE `merit_proposals` (
  `ProposalID` int(11) NOT NULL,
  `BatchReference` varchar(50) DEFAULT NULL,
  `period_id` int(11) DEFAULT NULL,
  `performance_rating` decimal(3,1) DEFAULT NULL,
  `compa_ratio_range` enum('Low','Mid','High') DEFAULT NULL,
  `ProposedMinIncrease` decimal(5,2) DEFAULT NULL,
  `ProposedMaxIncrease` decimal(5,2) DEFAULT NULL,
  `Reason` text DEFAULT NULL,
  `ProposedBy` int(11) DEFAULT NULL,
  `Status` enum('Pending','Endorsed','Manager Approved','Applied','Rejected') DEFAULT 'Pending',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `merit_proposals`
--

INSERT INTO `merit_proposals` (`ProposalID`, `BatchReference`, `period_id`, `performance_rating`, `compa_ratio_range`, `ProposedMinIncrease`, `ProposedMaxIncrease`, `Reason`, `ProposedBy`, `Status`, `CreatedAt`, `UpdatedAt`) VALUES
(19, 'MRT_69A69380F2AA9', 1, 5.0, 'Low', 4.00, 5.00, 'test', 7, 'Applied', '2026-03-03 07:53:36', '2026-03-04 09:34:28'),
(20, 'MRT_69A69380F2AA9', 1, 5.0, 'Mid', 3.00, 4.00, 'test', 7, 'Applied', '2026-03-03 07:53:37', '2026-03-03 10:23:27'),
(21, 'MRT_69A69380F2AA9', 1, 5.0, 'High', 2.00, 3.00, 'test', 7, 'Applied', '2026-03-03 07:53:37', '2026-03-03 10:23:27'),
(22, 'MRT_69A69380F2AA9', 1, 4.0, 'Low', 3.00, 4.00, 'test', 7, 'Applied', '2026-03-03 07:53:37', '2026-03-03 10:23:27'),
(23, 'MRT_69A69380F2AA9', 1, 4.0, 'Mid', 2.00, 3.00, 'test', 7, 'Applied', '2026-03-03 07:53:37', '2026-03-03 10:23:27'),
(24, 'MRT_69A69380F2AA9', 1, 4.0, 'High', 1.00, 2.00, 'test', 7, 'Applied', '2026-03-03 07:53:37', '2026-03-03 10:23:27'),
(25, 'MRT_69A69380F2AA9', 1, 3.0, 'Low', 2.00, 3.00, 'test', 7, 'Applied', '2026-03-03 07:53:37', '2026-03-03 10:23:27'),
(26, 'MRT_69A69380F2AA9', 1, 3.0, 'Mid', 1.00, 2.00, 'test', 7, 'Applied', '2026-03-03 07:53:37', '2026-03-03 10:23:27'),
(27, 'MRT_69A69380F2AA9', 1, 3.0, 'High', 0.00, 1.00, 'test', 7, 'Applied', '2026-03-03 07:53:37', '2026-03-03 10:23:27'),
(28, 'MRT_69A7FD863510F', 1, 5.0, 'Low', 4.00, 5.00, 'test', 7, 'Applied', '2026-03-04 09:38:14', '2026-03-04 14:22:42');

-- --------------------------------------------------------

--
-- Table structure for table `pagibig_settings`
--

CREATE TABLE `pagibig_settings` (
  `period_id` int(11) NOT NULL,
  `employee_rate_pct` decimal(5,2) DEFAULT NULL,
  `monthly_cap_ee` decimal(15,2) DEFAULT NULL,
  `monthly_cap_er` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pagibig_settings`
--

INSERT INTO `pagibig_settings` (`period_id`, `employee_rate_pct`, `monthly_cap_ee`, `monthly_cap_er`) VALUES
(1, 2.00, 200.00, 200.00);

-- --------------------------------------------------------

--
-- Table structure for table `payout_history`
--

CREATE TABLE `payout_history` (
  `id` int(11) NOT NULL,
  `reference_id` varchar(100) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `employee_name` varchar(200) DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `account_number` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `xendit_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payout_history`
--

INSERT INTO `payout_history` (`id`, `reference_id`, `employee_id`, `employee_name`, `amount`, `bank_name`, `account_number`, `status`, `xendit_id`, `created_at`) VALUES
(1, 'TEST-1773243330', 1, 'Test User', 100.00, 'PH_BDO', '123456789', 'TEST', 'X-123', '2026-03-11 15:35:30'),
(2, 'MIG-114', 1, 'Joshua Suruiz', 42106.22, 'BDO', '001234567890', 'SUCCESS', 'PRE-HISTORY', '2026-03-11 15:36:41'),
(3, 'MIG-115', 2, 'Red Gin Baldon', 41028.10, 'BDO', '229-411-332-222', 'SUCCESS', 'PRE-HISTORY', '2026-03-11 15:36:41'),
(4, 'MIG-116', 3, 'Noriel Dimailig', 19933.53, 'BDO', '222-444-332-222', 'SUCCESS', 'PRE-HISTORY', '2026-03-11 15:36:41'),
(5, 'MIG-117', 4, 'Earl Caber', 15486.23, 'BDO', '323235566', 'SUCCESS', 'PRE-HISTORY', '2026-03-11 15:36:41'),
(6, 'MIG-118', 6, 'Glory Job', 28245.80, 'BDO', '230-31125-2026', 'SUCCESS', 'PRE-HISTORY', '2026-03-11 15:36:41'),
(7, 'MIG-119', 7, 'Miguel Padre', 23400.80, 'BDO', '321-313-321', 'SUCCESS', 'PRE-HISTORY', '2026-03-11 15:36:41');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_batches`
--

CREATE TABLE `payroll_batches` (
  `id` int(11) NOT NULL,
  `batch_code` varchar(50) NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `pay_type` enum('Semi-Monthly','Monthly') NOT NULL DEFAULT 'Semi-Monthly',
  `status` enum('Processing','Pending Approval','Approved','Finance Approved','Rejected','Finalized','Disbursed','Archived') NOT NULL DEFAULT 'Processing',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll_batches`
--

INSERT INTO `payroll_batches` (`id`, `batch_code`, `period_start`, `period_end`, `pay_type`, `status`, `created_by`, `created_at`) VALUES
(17, 'PR-2026-524', '2026-03-16', '2026-03-31', 'Semi-Monthly', 'Disbursed', 0, '2026-03-10 11:12:23'),
(20, 'PR-2026-638', '2026-03-01', '2026-03-15', 'Semi-Monthly', 'Disbursed', 0, '2026-03-11 16:42:37'),
(21, 'PR-2026-083', '2026-03-01', '2026-03-15', 'Semi-Monthly', 'Disbursed', 0, '2026-03-11 18:29:01'),
(22, 'PR-2026-199', '2026-03-01', '2026-03-15', 'Semi-Monthly', 'Disbursed', 0, '2026-03-12 00:42:32');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_batch_items`
--

CREATE TABLE `payroll_batch_items` (
  `id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `basic_pay` decimal(15,2) NOT NULL DEFAULT 0.00,
  `allowances_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sss_regular_ee` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sss_regular_er` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sss_wisp_ee` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sss_wisp_er` decimal(15,2) NOT NULL DEFAULT 0.00,
  `philhealth_ee` decimal(15,2) NOT NULL DEFAULT 0.00,
  `philhealth_er` decimal(15,2) NOT NULL DEFAULT 0.00,
  `pagibig_ee` decimal(15,2) NOT NULL DEFAULT 0.00,
  `pagibig_er` decimal(15,2) NOT NULL DEFAULT 0.00,
  `deductions_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `withholding_tax` decimal(15,2) NOT NULL DEFAULT 0.00,
  `net_pay` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` varchar(30) NOT NULL DEFAULT 'Computed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll_batch_items`
--

INSERT INTO `payroll_batch_items` (`id`, `batch_id`, `employee_id`, `basic_pay`, `allowances_total`, `sss_regular_ee`, `sss_regular_er`, `sss_wisp_ee`, `sss_wisp_er`, `philhealth_ee`, `philhealth_er`, `pagibig_ee`, `pagibig_er`, `deductions_total`, `withholding_tax`, `net_pay`, `status`, `created_at`) VALUES
(103, 16, 1, 40000.00, 9700.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 10718.78, 8618.78, 42106.22, 'Computed', '2026-03-10 08:29:04'),
(104, 16, 2, 40000.00, 9700.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 10234.40, 8259.40, 41028.10, 'Computed', '2026-03-10 08:29:04'),
(105, 16, 3, 19090.91, 3850.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 3007.38, 1988.63, 19933.53, 'Computed', '2026-03-10 08:29:04'),
(106, 16, 4, 13636.36, 2950.00, 375.00, 750.00, 0.00, 0.00, 187.50, 187.50, 100.00, 100.00, 1568.88, 894.66, 15486.23, 'Computed', '2026-03-10 08:29:04'),
(107, 16, 6, 26500.00, 7450.00, 500.00, 1000.00, 375.00, 750.00, 662.50, 662.50, 100.00, 100.00, 5704.20, 4066.70, 28245.80, 'Computed', '2026-03-10 08:29:04'),
(108, 16, 7, 20000.00, 5950.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 4424.20, 2855.45, 23400.80, 'Computed', '2026-03-10 08:29:04'),
(109, 16, 8, 19090.91, 4800.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 3092.38, 2204.88, 20798.53, 'Computed', '2026-03-10 08:29:04'),
(110, 16, 10, 20000.00, 5950.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 4053.37, 2557.53, 22209.13, 'Computed', '2026-03-10 08:29:04'),
(111, 16, 14, 14500.00, 4800.00, 500.00, 1000.00, 225.00, 450.00, 362.50, 362.50, 100.00, 100.00, 2414.20, 1226.70, 16885.80, 'Computed', '2026-03-10 08:29:04'),
(112, 16, 15, 27000.00, 7450.00, 500.00, 1000.00, 375.00, 750.00, 675.00, 675.00, 100.00, 100.00, 5814.20, 4164.20, 28635.80, 'Computed', '2026-03-10 08:29:04'),
(113, 16, 16, 14500.00, 4800.00, 500.00, 1000.00, 225.00, 450.00, 362.50, 362.50, 100.00, 100.00, 2414.20, 1226.70, 16885.80, 'Computed', '2026-03-10 08:29:04'),
(114, 17, 1, 40000.00, 9700.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 10718.78, 8618.78, 42106.22, 'Paid', '2026-03-10 11:12:23'),
(115, 17, 2, 40000.00, 9700.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 10234.40, 8259.40, 41028.10, 'Paid', '2026-03-10 11:12:23'),
(116, 17, 3, 19090.91, 3850.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 3007.38, 1988.63, 19933.53, 'Paid', '2026-03-10 11:12:23'),
(117, 17, 4, 13636.36, 2950.00, 375.00, 750.00, 0.00, 0.00, 187.50, 187.50, 100.00, 100.00, 1568.88, 894.66, 15486.23, 'Paid', '2026-03-10 11:12:23'),
(118, 17, 6, 26500.00, 7450.00, 500.00, 1000.00, 375.00, 750.00, 662.50, 662.50, 100.00, 100.00, 5704.20, 4066.70, 28245.80, 'Paid', '2026-03-10 11:12:23'),
(119, 17, 7, 20000.00, 5950.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 4424.20, 2855.45, 23400.80, 'Paid', '2026-03-10 11:12:23'),
(120, 17, 8, 19090.91, 4800.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 3092.38, 2204.88, 20798.53, 'Paid', '2026-03-10 11:12:23'),
(121, 17, 10, 20000.00, 5950.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 4053.37, 2557.53, 22209.13, 'Paid', '2026-03-10 11:12:23'),
(122, 17, 14, 14500.00, 4800.00, 500.00, 1000.00, 225.00, 450.00, 362.50, 362.50, 100.00, 100.00, 2414.20, 1226.70, 16885.80, 'Paid', '2026-03-10 11:12:23'),
(123, 17, 15, 27000.00, 7450.00, 500.00, 1000.00, 375.00, 750.00, 675.00, 675.00, 100.00, 100.00, 5814.20, 4164.20, 28635.80, 'Paid', '2026-03-10 11:12:23'),
(124, 17, 16, 14500.00, 4800.00, 500.00, 1000.00, 225.00, 450.00, 362.50, 362.50, 100.00, 100.00, 2414.20, 1226.70, 16885.80, 'Paid', '2026-03-10 11:12:23'),
(125, 18, 1, 40000.00, 9700.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 10718.78, 8618.78, 42106.22, 'Computed', '2026-03-10 13:04:02'),
(126, 18, 2, 40000.00, 9700.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 10234.40, 8259.40, 41028.10, 'Computed', '2026-03-10 13:04:03'),
(127, 18, 3, 19090.91, 3850.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 3007.38, 1988.63, 19933.53, 'Computed', '2026-03-10 13:04:03'),
(128, 18, 4, 13636.36, 2950.00, 375.00, 750.00, 0.00, 0.00, 187.50, 187.50, 100.00, 100.00, 1568.88, 894.66, 15486.23, 'Computed', '2026-03-10 13:04:03'),
(129, 18, 6, 26500.00, 7450.00, 500.00, 1000.00, 375.00, 750.00, 662.50, 662.50, 100.00, 100.00, 5704.20, 4066.70, 28245.80, 'Computed', '2026-03-10 13:04:03'),
(130, 18, 7, 20000.00, 5950.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 4424.20, 2855.45, 23400.80, 'Computed', '2026-03-10 13:04:03'),
(131, 18, 8, 19090.91, 4800.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 3092.38, 2204.88, 20798.53, 'Computed', '2026-03-10 13:04:03'),
(132, 18, 10, 20000.00, 5950.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 4053.37, 2557.53, 22209.13, 'Computed', '2026-03-10 13:04:03'),
(133, 18, 14, 14500.00, 4800.00, 500.00, 1000.00, 225.00, 450.00, 362.50, 362.50, 100.00, 100.00, 2414.20, 1226.70, 16885.80, 'Computed', '2026-03-10 13:04:03'),
(134, 18, 15, 27000.00, 7450.00, 500.00, 1000.00, 375.00, 750.00, 675.00, 675.00, 100.00, 100.00, 5814.20, 4164.20, 28635.80, 'Computed', '2026-03-10 13:04:03'),
(135, 18, 16, 14500.00, 4800.00, 500.00, 1000.00, 225.00, 450.00, 362.50, 362.50, 100.00, 100.00, 2414.20, 1226.70, 16885.80, 'Computed', '2026-03-10 13:04:03'),
(136, 19, 1, 40000.00, 9700.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 10718.78, 8618.78, 42106.22, 'Computed', '2026-03-11 07:51:08'),
(137, 19, 2, 40000.00, 9700.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 10234.40, 8259.40, 41028.10, 'Computed', '2026-03-11 07:51:08'),
(138, 19, 3, 19090.91, 3850.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 3007.38, 1988.63, 19933.53, 'Computed', '2026-03-11 07:51:08'),
(139, 19, 4, 13636.36, 2950.00, 375.00, 750.00, 0.00, 0.00, 187.50, 187.50, 100.00, 100.00, 1568.88, 894.66, 15486.23, 'Computed', '2026-03-11 07:51:08'),
(140, 19, 6, 26500.00, 7450.00, 500.00, 1000.00, 375.00, 750.00, 662.50, 662.50, 100.00, 100.00, 5704.20, 4066.70, 28245.80, 'Computed', '2026-03-11 07:51:08'),
(141, 19, 7, 20000.00, 5950.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 4424.20, 2855.45, 23400.80, 'Computed', '2026-03-11 07:51:08'),
(142, 19, 8, 19090.91, 4800.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 3092.38, 2204.88, 20798.53, 'Computed', '2026-03-11 07:51:08'),
(143, 19, 10, 20000.00, 5950.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 4053.37, 2557.53, 22209.13, 'Computed', '2026-03-11 07:51:08'),
(144, 19, 14, 14500.00, 4800.00, 500.00, 1000.00, 225.00, 450.00, 362.50, 362.50, 100.00, 100.00, 2414.20, 1226.70, 16885.80, 'Computed', '2026-03-11 07:51:08'),
(145, 19, 15, 27000.00, 7450.00, 500.00, 1000.00, 375.00, 750.00, 675.00, 675.00, 100.00, 100.00, 5814.20, 4164.20, 28635.80, 'Computed', '2026-03-11 07:51:08'),
(146, 19, 16, 14500.00, 4800.00, 500.00, 1000.00, 225.00, 450.00, 362.50, 362.50, 100.00, 100.00, 2414.20, 1226.70, 16885.80, 'Computed', '2026-03-11 07:51:08'),
(147, 20, 1, 40000.00, 9700.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 10718.78, 8618.78, 42106.22, 'Paid', '2026-03-11 16:42:37'),
(148, 20, 2, 40000.00, 9700.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 10234.40, 8259.40, 41028.10, 'Paid', '2026-03-11 16:42:37'),
(149, 20, 3, 19090.91, 3850.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 3007.38, 1988.63, 19933.53, 'Paid', '2026-03-11 16:42:37'),
(150, 20, 4, 13636.36, 2950.00, 375.00, 750.00, 0.00, 0.00, 187.50, 187.50, 100.00, 100.00, 1568.88, 894.66, 15486.23, 'Paid', '2026-03-11 16:42:38'),
(151, 20, 6, 26500.00, 7450.00, 500.00, 1000.00, 375.00, 750.00, 662.50, 662.50, 100.00, 100.00, 5704.20, 4066.70, 28245.80, 'Paid', '2026-03-11 16:42:38'),
(152, 20, 7, 20000.00, 5950.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 4424.20, 2855.45, 23400.80, 'Paid', '2026-03-11 16:42:38'),
(153, 20, 8, 19090.91, 4800.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 3092.38, 2204.88, 20798.53, 'Paid', '2026-03-11 16:42:38'),
(154, 20, 10, 20000.00, 5950.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 4053.37, 2557.53, 22209.13, 'Paid', '2026-03-11 16:42:38'),
(155, 20, 14, 14500.00, 4800.00, 500.00, 1000.00, 225.00, 450.00, 362.50, 362.50, 100.00, 100.00, 2414.20, 1226.70, 16885.80, 'Paid', '2026-03-11 16:42:38'),
(156, 20, 15, 27000.00, 7450.00, 500.00, 1000.00, 375.00, 750.00, 675.00, 675.00, 100.00, 100.00, 5814.20, 4164.20, 28635.80, 'Paid', '2026-03-11 16:42:38'),
(157, 20, 16, 14500.00, 4800.00, 500.00, 1000.00, 225.00, 450.00, 362.50, 362.50, 100.00, 100.00, 2414.20, 1226.70, 16885.80, 'Paid', '2026-03-11 16:42:38'),
(158, 21, 1, 40000.00, 9700.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 10718.78, 8618.78, 42106.22, 'Paid', '2026-03-11 18:29:01'),
(159, 21, 2, 40000.00, 9700.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 10234.40, 8259.40, 41028.10, 'Paid', '2026-03-11 18:29:01'),
(160, 21, 3, 19090.91, 3850.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 3007.38, 1988.63, 19933.53, 'Paid', '2026-03-11 18:29:01'),
(161, 21, 4, 13636.36, 2950.00, 375.00, 750.00, 0.00, 0.00, 187.50, 187.50, 100.00, 100.00, 1568.88, 894.66, 15486.23, 'Paid', '2026-03-11 18:29:01'),
(162, 21, 6, 26500.00, 7450.00, 500.00, 1000.00, 375.00, 750.00, 662.50, 662.50, 100.00, 100.00, 5704.20, 4066.70, 28245.80, 'Paid', '2026-03-11 18:29:01'),
(163, 21, 7, 20000.00, 5950.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 4424.20, 2855.45, 23400.80, 'Paid', '2026-03-11 18:29:01'),
(164, 21, 8, 19090.91, 4800.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 3092.38, 2204.88, 20798.53, 'Paid', '2026-03-11 18:29:01'),
(165, 21, 10, 20000.00, 5950.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 4053.37, 2557.53, 22209.13, 'Paid', '2026-03-11 18:29:01'),
(166, 21, 14, 14500.00, 4800.00, 500.00, 1000.00, 225.00, 450.00, 362.50, 362.50, 100.00, 100.00, 2414.20, 1226.70, 16885.80, 'Paid', '2026-03-11 18:29:01'),
(167, 21, 15, 27000.00, 7450.00, 500.00, 1000.00, 375.00, 750.00, 675.00, 675.00, 100.00, 100.00, 5814.20, 4164.20, 28635.80, 'Paid', '2026-03-11 18:29:01'),
(168, 21, 16, 14500.00, 4800.00, 500.00, 1000.00, 225.00, 450.00, 362.50, 362.50, 100.00, 100.00, 2414.20, 1226.70, 16885.80, 'Paid', '2026-03-11 18:29:01'),
(169, 22, 1, 40000.00, 9700.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 10718.78, 8618.78, 42106.22, 'Paid', '2026-03-12 00:42:32'),
(170, 22, 2, 40000.00, 9700.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 10234.40, 8259.40, 41028.10, 'Paid', '2026-03-12 00:42:32'),
(171, 22, 3, 19090.91, 3850.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 3007.38, 1988.63, 19933.53, 'Paid', '2026-03-12 00:42:32'),
(172, 22, 4, 13636.36, 2950.00, 375.00, 750.00, 0.00, 0.00, 187.50, 187.50, 100.00, 100.00, 1568.88, 894.66, 15486.23, 'Paid', '2026-03-12 00:42:32'),
(173, 22, 6, 26500.00, 7450.00, 500.00, 1000.00, 375.00, 750.00, 662.50, 662.50, 100.00, 100.00, 5704.20, 4066.70, 28245.80, 'Paid', '2026-03-12 00:42:32'),
(174, 22, 7, 20000.00, 5950.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 4424.20, 2855.45, 23400.80, 'Paid', '2026-03-12 00:42:32'),
(175, 22, 8, 19090.91, 4800.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 3092.38, 2204.88, 20798.53, 'Paid', '2026-03-12 00:42:32'),
(176, 22, 10, 20000.00, 5950.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 4053.37, 2557.53, 22209.13, 'Paid', '2026-03-12 00:42:32'),
(177, 22, 14, 14500.00, 4800.00, 500.00, 1000.00, 225.00, 450.00, 362.50, 362.50, 100.00, 100.00, 2414.20, 1226.70, 16885.80, 'Paid', '2026-03-12 00:42:32'),
(178, 22, 15, 27000.00, 7450.00, 500.00, 1000.00, 375.00, 750.00, 675.00, 675.00, 100.00, 100.00, 5814.20, 4164.20, 28635.80, 'Paid', '2026-03-12 00:42:32'),
(179, 22, 16, 14500.00, 4800.00, 500.00, 1000.00, 225.00, 450.00, 362.50, 362.50, 100.00, 100.00, 2414.20, 1226.70, 16885.80, 'Paid', '2026-03-12 00:42:32'),
(180, 22, 18, 14500.00, 4800.00, 500.00, 1000.00, 225.00, 450.00, 362.50, 362.50, 100.00, 100.00, 2414.20, 1226.70, 16885.80, 'Paid', '2026-03-12 00:42:32');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_item_components`
--

CREATE TABLE `payroll_item_components` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `component_type` enum('Allowance','Deduction') NOT NULL,
  `component_name` varchar(100) NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll_item_components`
--

INSERT INTO `payroll_item_components` (`id`, `item_id`, `component_type`, `component_name`, `amount`) VALUES
(16, 4, 'Allowance', 'Rice Subsidy', 2500.00),
(17, 4, 'Allowance', 'Meal Allowance', 3500.00),
(18, 4, 'Allowance', 'Laundry Allowance', 400.00),
(19, 4, 'Allowance', 'Travel Allowance', 10000.00),
(20, 4, 'Allowance', 'Communication Allowance', 3000.00),
(21, 4, 'Deduction', 'SSS (EE)', 875.00),
(22, 4, 'Deduction', 'PhilHealth (EE)', 1000.00),
(23, 4, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(24, 5, 'Allowance', 'Rice Subsidy', 2500.00),
(25, 5, 'Allowance', 'Meal Allowance', 3500.00),
(26, 5, 'Allowance', 'Laundry Allowance', 400.00),
(27, 5, 'Allowance', 'Travel Allowance', 10000.00),
(28, 5, 'Allowance', 'Communication Allowance', 3000.00),
(29, 5, 'Deduction', 'SSS (EE)', 875.00),
(30, 5, 'Deduction', 'PhilHealth (EE)', 1000.00),
(31, 5, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(32, 6, 'Allowance', 'Rice Subsidy', 2500.00),
(33, 6, 'Allowance', 'Meal Allowance', 1500.00),
(34, 6, 'Allowance', 'Laundry Allowance', 400.00),
(35, 6, 'Allowance', 'Travel Allowance', 2500.00),
(36, 6, 'Allowance', 'Communication Allowance', 800.00),
(37, 6, 'Deduction', 'SSS (EE)', 525.00),
(38, 6, 'Deduction', 'PhilHealth (EE)', 262.50),
(39, 6, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(40, 7, 'Allowance', 'Rice Subsidy', 2500.00),
(41, 7, 'Allowance', 'Meal Allowance', 1000.00),
(42, 7, 'Allowance', 'Laundry Allowance', 400.00),
(43, 7, 'Allowance', 'Travel Allowance', 1500.00),
(44, 7, 'Allowance', 'Communication Allowance', 500.00),
(45, 7, 'Deduction', 'SSS (EE)', 375.00),
(46, 7, 'Deduction', 'PhilHealth (EE)', 187.50),
(47, 7, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(48, 8, 'Allowance', 'Rice Subsidy', 2500.00),
(49, 8, 'Allowance', 'Meal Allowance', 3000.00),
(50, 8, 'Allowance', 'Laundry Allowance', 400.00),
(51, 8, 'Allowance', 'Travel Allowance', 7000.00),
(52, 8, 'Allowance', 'Communication Allowance', 2000.00),
(53, 8, 'Deduction', 'SSS (EE)', 875.00),
(54, 8, 'Deduction', 'PhilHealth (EE)', 662.50),
(55, 8, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(56, 9, 'Allowance', 'Rice Subsidy', 2500.00),
(57, 9, 'Allowance', 'Meal Allowance', 2500.00),
(58, 9, 'Allowance', 'Laundry Allowance', 400.00),
(59, 9, 'Allowance', 'Travel Allowance', 5000.00),
(60, 9, 'Allowance', 'Communication Allowance', 1500.00),
(61, 9, 'Deduction', 'SSS (EE)', 875.00),
(62, 9, 'Deduction', 'PhilHealth (EE)', 500.00),
(63, 9, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(64, 10, 'Allowance', 'Rice Subsidy', 2500.00),
(65, 10, 'Allowance', 'Meal Allowance', 2000.00),
(66, 10, 'Allowance', 'Laundry Allowance', 400.00),
(67, 10, 'Allowance', 'Travel Allowance', 3500.00),
(68, 10, 'Allowance', 'Communication Allowance', 1200.00),
(69, 10, 'Deduction', 'SSS (EE)', 525.00),
(70, 10, 'Deduction', 'PhilHealth (EE)', 262.50),
(71, 10, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(72, 11, 'Allowance', 'Rice Subsidy', 2500.00),
(73, 11, 'Allowance', 'Meal Allowance', 2500.00),
(74, 11, 'Allowance', 'Laundry Allowance', 400.00),
(75, 11, 'Allowance', 'Travel Allowance', 5000.00),
(76, 11, 'Allowance', 'Communication Allowance', 1500.00),
(77, 11, 'Deduction', 'SSS (EE)', 875.00),
(78, 11, 'Deduction', 'PhilHealth (EE)', 500.00),
(79, 11, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(80, 12, 'Allowance', 'Rice Subsidy', 2500.00),
(81, 12, 'Allowance', 'Meal Allowance', 3500.00),
(82, 12, 'Allowance', 'Laundry Allowance', 400.00),
(83, 12, 'Allowance', 'Travel Allowance', 10000.00),
(84, 12, 'Allowance', 'Communication Allowance', 3000.00),
(85, 12, 'Allowance', 'Overtime Pay', 3125.00),
(86, 12, 'Deduction', 'SSS Regular (EE)', 500.00),
(87, 12, 'Deduction', 'SSS Regular (ER)', 1000.00),
(88, 12, 'Deduction', 'SSS WISP (EE)', 375.00),
(89, 12, 'Deduction', 'SSS WISP (ER)', 750.00),
(90, 12, 'Deduction', 'PhilHealth (EE)', 1000.00),
(91, 12, 'Deduction', 'PhilHealth (ER)', 1000.00),
(92, 12, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(93, 12, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(94, 12, 'Deduction', 'Late/Undertime', 125.00),
(95, 12, 'Deduction', 'Withholding Tax', 2958.33),
(96, 13, 'Allowance', 'Rice Subsidy', 2500.00),
(97, 13, 'Allowance', 'Meal Allowance', 3500.00),
(98, 13, 'Allowance', 'Laundry Allowance', 400.00),
(99, 13, 'Allowance', 'Travel Allowance', 10000.00),
(100, 13, 'Allowance', 'Communication Allowance', 3000.00),
(101, 13, 'Allowance', 'Overtime Pay', 1562.50),
(102, 13, 'Deduction', 'SSS Regular (EE)', 500.00),
(103, 13, 'Deduction', 'SSS Regular (ER)', 1000.00),
(104, 13, 'Deduction', 'SSS WISP (EE)', 375.00),
(105, 13, 'Deduction', 'SSS WISP (ER)', 750.00),
(106, 13, 'Deduction', 'PhilHealth (EE)', 1000.00),
(107, 13, 'Deduction', 'PhilHealth (ER)', 1000.00),
(108, 13, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(109, 13, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(110, 13, 'Deduction', 'Withholding Tax', 2958.33),
(111, 14, 'Allowance', 'Rice Subsidy', 2500.00),
(112, 14, 'Allowance', 'Meal Allowance', 1500.00),
(113, 14, 'Allowance', 'Laundry Allowance', 400.00),
(114, 14, 'Allowance', 'Travel Allowance', 2500.00),
(115, 14, 'Allowance', 'Communication Allowance', 800.00),
(116, 14, 'Deduction', 'SSS Regular (EE)', 500.00),
(117, 14, 'Deduction', 'SSS Regular (ER)', 1000.00),
(118, 14, 'Deduction', 'SSS WISP (EE)', 25.00),
(119, 14, 'Deduction', 'SSS WISP (ER)', 50.00),
(120, 14, 'Deduction', 'PhilHealth (EE)', 262.50),
(121, 14, 'Deduction', 'PhilHealth (ER)', 262.50),
(122, 14, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(123, 14, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(124, 14, 'Deduction', 'Late/Undertime', 131.25),
(125, 14, 'Deduction', 'Withholding Tax', 8.33),
(126, 15, 'Allowance', 'Rice Subsidy', 2500.00),
(127, 15, 'Allowance', 'Meal Allowance', 1000.00),
(128, 15, 'Allowance', 'Laundry Allowance', 400.00),
(129, 15, 'Allowance', 'Travel Allowance', 1500.00),
(130, 15, 'Allowance', 'Communication Allowance', 500.00),
(131, 15, 'Allowance', 'Overtime Pay', 468.75),
(132, 15, 'Deduction', 'SSS Regular (EE)', 375.00),
(133, 15, 'Deduction', 'SSS Regular (ER)', 750.00),
(134, 15, 'Deduction', 'PhilHealth (EE)', 187.50),
(135, 15, 'Deduction', 'PhilHealth (ER)', 187.50),
(136, 15, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(137, 15, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(138, 15, 'Deduction', 'Late/Undertime', 11.72),
(139, 16, 'Allowance', 'Rice Subsidy', 2500.00),
(140, 16, 'Allowance', 'Meal Allowance', 3000.00),
(141, 16, 'Allowance', 'Laundry Allowance', 400.00),
(142, 16, 'Allowance', 'Travel Allowance', 7000.00),
(143, 16, 'Allowance', 'Communication Allowance', 2000.00),
(144, 16, 'Deduction', 'SSS Regular (EE)', 500.00),
(145, 16, 'Deduction', 'SSS Regular (ER)', 1000.00),
(146, 16, 'Deduction', 'SSS WISP (EE)', 375.00),
(147, 16, 'Deduction', 'SSS WISP (ER)', 750.00),
(148, 16, 'Deduction', 'PhilHealth (EE)', 662.50),
(149, 16, 'Deduction', 'PhilHealth (ER)', 662.50),
(150, 16, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(151, 16, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(152, 16, 'Deduction', 'Withholding Tax', 1608.33),
(153, 17, 'Allowance', 'Rice Subsidy', 2500.00),
(154, 17, 'Allowance', 'Meal Allowance', 2500.00),
(155, 17, 'Allowance', 'Laundry Allowance', 400.00),
(156, 17, 'Allowance', 'Travel Allowance', 5000.00),
(157, 17, 'Allowance', 'Communication Allowance', 1500.00),
(158, 17, 'Allowance', 'Overtime Pay', 1875.00),
(159, 17, 'Deduction', 'SSS Regular (EE)', 500.00),
(160, 17, 'Deduction', 'SSS Regular (ER)', 1000.00),
(161, 17, 'Deduction', 'SSS WISP (EE)', 375.00),
(162, 17, 'Deduction', 'SSS WISP (ER)', 750.00),
(163, 17, 'Deduction', 'PhilHealth (EE)', 500.00),
(164, 17, 'Deduction', 'PhilHealth (ER)', 500.00),
(165, 17, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(166, 17, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(167, 17, 'Deduction', 'Late/Undertime', 93.75),
(168, 17, 'Deduction', 'Withholding Tax', 958.33),
(169, 18, 'Allowance', 'Rice Subsidy', 2500.00),
(170, 18, 'Allowance', 'Meal Allowance', 2000.00),
(171, 18, 'Allowance', 'Laundry Allowance', 400.00),
(172, 18, 'Allowance', 'Travel Allowance', 3500.00),
(173, 18, 'Allowance', 'Communication Allowance', 1200.00),
(174, 18, 'Deduction', 'SSS Regular (EE)', 500.00),
(175, 18, 'Deduction', 'SSS Regular (ER)', 1000.00),
(176, 18, 'Deduction', 'SSS WISP (EE)', 25.00),
(177, 18, 'Deduction', 'SSS WISP (ER)', 50.00),
(178, 18, 'Deduction', 'PhilHealth (EE)', 262.50),
(179, 18, 'Deduction', 'PhilHealth (ER)', 262.50),
(180, 18, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(181, 18, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(182, 18, 'Deduction', 'Withholding Tax', 8.33),
(183, 19, 'Allowance', 'Rice Subsidy', 2500.00),
(184, 19, 'Allowance', 'Meal Allowance', 2500.00),
(185, 19, 'Allowance', 'Laundry Allowance', 400.00),
(186, 19, 'Allowance', 'Travel Allowance', 5000.00),
(187, 19, 'Allowance', 'Communication Allowance', 1500.00),
(188, 19, 'Allowance', 'Overtime Pay', 312.50),
(189, 19, 'Deduction', 'SSS Regular (EE)', 500.00),
(190, 19, 'Deduction', 'SSS Regular (ER)', 1000.00),
(191, 19, 'Deduction', 'SSS WISP (EE)', 375.00),
(192, 19, 'Deduction', 'SSS WISP (ER)', 750.00),
(193, 19, 'Deduction', 'PhilHealth (EE)', 500.00),
(194, 19, 'Deduction', 'PhilHealth (ER)', 500.00),
(195, 19, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(196, 19, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(197, 19, 'Deduction', 'Late/Undertime', 20.84),
(198, 19, 'Deduction', 'Withholding Tax', 958.33),
(199, 20, 'Allowance', 'Rice Subsidy', 2500.00),
(200, 20, 'Allowance', 'Meal Allowance', 3500.00),
(201, 20, 'Allowance', 'Laundry Allowance', 400.00),
(202, 20, 'Allowance', 'Travel Allowance', 10000.00),
(203, 20, 'Allowance', 'Communication Allowance', 3000.00),
(204, 20, 'Allowance', 'Overtime Pay', 3125.00),
(205, 20, 'Deduction', 'SSS Regular (EE)', 500.00),
(206, 20, 'Deduction', 'SSS Regular (ER)', 1000.00),
(207, 20, 'Deduction', 'SSS WISP (EE)', 375.00),
(208, 20, 'Deduction', 'SSS WISP (ER)', 750.00),
(209, 20, 'Deduction', 'PhilHealth (EE)', 1000.00),
(210, 20, 'Deduction', 'PhilHealth (ER)', 1000.00),
(211, 20, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(212, 20, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(213, 20, 'Deduction', 'Late/Undertime', 125.00),
(214, 20, 'Deduction', 'Withholding Tax', 5331.28),
(215, 21, 'Allowance', 'Rice Subsidy', 2500.00),
(216, 21, 'Allowance', 'Meal Allowance', 3500.00),
(217, 21, 'Allowance', 'Laundry Allowance', 400.00),
(218, 21, 'Allowance', 'Travel Allowance', 10000.00),
(219, 21, 'Allowance', 'Communication Allowance', 3000.00),
(220, 21, 'Allowance', 'Overtime Pay', 1562.50),
(221, 21, 'Deduction', 'SSS Regular (EE)', 500.00),
(222, 21, 'Deduction', 'SSS Regular (ER)', 1000.00),
(223, 21, 'Deduction', 'SSS WISP (EE)', 375.00),
(224, 21, 'Deduction', 'SSS WISP (ER)', 750.00),
(225, 21, 'Deduction', 'PhilHealth (EE)', 1000.00),
(226, 21, 'Deduction', 'PhilHealth (ER)', 1000.00),
(227, 21, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(228, 21, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(229, 21, 'Deduction', 'Withholding Tax', 10684.40),
(230, 22, 'Allowance', 'Rice Subsidy', 2500.00),
(231, 22, 'Allowance', 'Meal Allowance', 1500.00),
(232, 22, 'Allowance', 'Laundry Allowance', 400.00),
(233, 22, 'Allowance', 'Travel Allowance', 2500.00),
(234, 22, 'Allowance', 'Communication Allowance', 800.00),
(235, 22, 'Deduction', 'SSS Regular (EE)', 500.00),
(236, 22, 'Deduction', 'SSS Regular (ER)', 1000.00),
(237, 22, 'Deduction', 'SSS WISP (EE)', 25.00),
(238, 22, 'Deduction', 'SSS WISP (ER)', 50.00),
(239, 22, 'Deduction', 'PhilHealth (EE)', 262.50),
(240, 22, 'Deduction', 'PhilHealth (ER)', 262.50),
(241, 22, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(242, 22, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(243, 22, 'Deduction', 'Late/Undertime', 131.25),
(244, 22, 'Deduction', 'Withholding Tax', 1040.45),
(245, 23, 'Allowance', 'Rice Subsidy', 2500.00),
(246, 23, 'Allowance', 'Meal Allowance', 1000.00),
(247, 23, 'Allowance', 'Laundry Allowance', 400.00),
(248, 23, 'Allowance', 'Travel Allowance', 1500.00),
(249, 23, 'Allowance', 'Communication Allowance', 500.00),
(250, 23, 'Allowance', 'Overtime Pay', 468.75),
(251, 23, 'Deduction', 'SSS Regular (EE)', 375.00),
(252, 23, 'Deduction', 'SSS Regular (ER)', 750.00),
(253, 23, 'Deduction', 'PhilHealth (EE)', 187.50),
(254, 23, 'Deduction', 'PhilHealth (ER)', 187.50),
(255, 23, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(256, 23, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(257, 23, 'Deduction', 'Late/Undertime', 11.72),
(258, 23, 'Deduction', 'Withholding Tax', 416.71),
(259, 24, 'Allowance', 'Rice Subsidy', 2500.00),
(260, 24, 'Allowance', 'Meal Allowance', 3000.00),
(261, 24, 'Allowance', 'Laundry Allowance', 400.00),
(262, 24, 'Allowance', 'Travel Allowance', 7000.00),
(263, 24, 'Allowance', 'Communication Allowance', 2000.00),
(264, 24, 'Deduction', 'SSS Regular (EE)', 500.00),
(265, 24, 'Deduction', 'SSS Regular (ER)', 1000.00),
(266, 24, 'Deduction', 'SSS WISP (EE)', 375.00),
(267, 24, 'Deduction', 'SSS WISP (ER)', 750.00),
(268, 24, 'Deduction', 'PhilHealth (EE)', 662.50),
(269, 24, 'Deduction', 'PhilHealth (ER)', 662.50),
(270, 24, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(271, 24, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(272, 24, 'Deduction', 'Withholding Tax', 5878.15),
(273, 25, 'Allowance', 'Rice Subsidy', 2500.00),
(274, 25, 'Allowance', 'Meal Allowance', 2500.00),
(275, 25, 'Allowance', 'Laundry Allowance', 400.00),
(276, 25, 'Allowance', 'Travel Allowance', 5000.00),
(277, 25, 'Allowance', 'Communication Allowance', 1500.00),
(278, 25, 'Allowance', 'Overtime Pay', 1875.00),
(279, 25, 'Deduction', 'SSS Regular (EE)', 500.00),
(280, 25, 'Deduction', 'SSS Regular (ER)', 1000.00),
(281, 25, 'Deduction', 'SSS WISP (EE)', 375.00),
(282, 25, 'Deduction', 'SSS WISP (ER)', 750.00),
(283, 25, 'Deduction', 'PhilHealth (EE)', 500.00),
(284, 25, 'Deduction', 'PhilHealth (ER)', 500.00),
(285, 25, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(286, 25, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(287, 25, 'Deduction', 'Late/Undertime', 93.75),
(288, 25, 'Deduction', 'Withholding Tax', 4045.45),
(289, 26, 'Allowance', 'Rice Subsidy', 2500.00),
(290, 26, 'Allowance', 'Meal Allowance', 2000.00),
(291, 26, 'Allowance', 'Laundry Allowance', 400.00),
(292, 26, 'Allowance', 'Travel Allowance', 3500.00),
(293, 26, 'Allowance', 'Communication Allowance', 1200.00),
(294, 26, 'Deduction', 'SSS Regular (EE)', 500.00),
(295, 26, 'Deduction', 'SSS Regular (ER)', 1000.00),
(296, 26, 'Deduction', 'SSS WISP (EE)', 25.00),
(297, 26, 'Deduction', 'SSS WISP (ER)', 50.00),
(298, 26, 'Deduction', 'PhilHealth (EE)', 262.50),
(299, 26, 'Deduction', 'PhilHealth (ER)', 262.50),
(300, 26, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(301, 26, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(302, 26, 'Deduction', 'Withholding Tax', 1446.70),
(303, 27, 'Allowance', 'Rice Subsidy', 2500.00),
(304, 27, 'Allowance', 'Meal Allowance', 2500.00),
(305, 27, 'Allowance', 'Laundry Allowance', 400.00),
(306, 27, 'Allowance', 'Travel Allowance', 5000.00),
(307, 27, 'Allowance', 'Communication Allowance', 1500.00),
(308, 27, 'Allowance', 'Overtime Pay', 312.50),
(309, 27, 'Deduction', 'SSS Regular (EE)', 500.00),
(310, 27, 'Deduction', 'SSS Regular (ER)', 1000.00),
(311, 27, 'Deduction', 'SSS WISP (EE)', 375.00),
(312, 27, 'Deduction', 'SSS WISP (ER)', 750.00),
(313, 27, 'Deduction', 'PhilHealth (EE)', 500.00),
(314, 27, 'Deduction', 'PhilHealth (ER)', 500.00),
(315, 27, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(316, 27, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(317, 27, 'Deduction', 'Late/Undertime', 20.84),
(318, 27, 'Deduction', 'Withholding Tax', 3747.53),
(319, 28, 'Allowance', 'Rice Subsidy', 2500.00),
(320, 28, 'Allowance', 'Meal Allowance', 3500.00),
(321, 28, 'Allowance', 'Laundry Allowance', 400.00),
(322, 28, 'Allowance', 'Travel Allowance', 10000.00),
(323, 28, 'Allowance', 'Communication Allowance', 3000.00),
(324, 28, 'Allowance', 'Overtime Pay', 3125.00),
(325, 28, 'Deduction', 'SSS Regular (EE)', 500.00),
(326, 28, 'Deduction', 'SSS Regular (ER)', 1000.00),
(327, 28, 'Deduction', 'SSS WISP (EE)', 375.00),
(328, 28, 'Deduction', 'SSS WISP (ER)', 750.00),
(329, 28, 'Deduction', 'PhilHealth (EE)', 1000.00),
(330, 28, 'Deduction', 'PhilHealth (ER)', 1000.00),
(331, 28, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(332, 28, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(333, 28, 'Deduction', 'Late/Undertime', 125.00),
(334, 28, 'Deduction', 'Withholding Tax', 5331.28),
(335, 29, 'Allowance', 'Rice Subsidy', 2500.00),
(336, 29, 'Allowance', 'Meal Allowance', 3500.00),
(337, 29, 'Allowance', 'Laundry Allowance', 400.00),
(338, 29, 'Allowance', 'Travel Allowance', 10000.00),
(339, 29, 'Allowance', 'Communication Allowance', 3000.00),
(340, 29, 'Allowance', 'Overtime Pay', 1562.50),
(341, 29, 'Deduction', 'SSS Regular (EE)', 500.00),
(342, 29, 'Deduction', 'SSS Regular (ER)', 1000.00),
(343, 29, 'Deduction', 'SSS WISP (EE)', 375.00),
(344, 29, 'Deduction', 'SSS WISP (ER)', 750.00),
(345, 29, 'Deduction', 'PhilHealth (EE)', 1000.00),
(346, 29, 'Deduction', 'PhilHealth (ER)', 1000.00),
(347, 29, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(348, 29, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(349, 29, 'Deduction', 'Withholding Tax', 10684.40),
(350, 30, 'Allowance', 'Rice Subsidy', 2500.00),
(351, 30, 'Allowance', 'Meal Allowance', 1500.00),
(352, 30, 'Allowance', 'Laundry Allowance', 400.00),
(353, 30, 'Allowance', 'Travel Allowance', 2500.00),
(354, 30, 'Allowance', 'Communication Allowance', 800.00),
(355, 30, 'Deduction', 'SSS Regular (EE)', 500.00),
(356, 30, 'Deduction', 'SSS Regular (ER)', 1000.00),
(357, 30, 'Deduction', 'SSS WISP (EE)', 25.00),
(358, 30, 'Deduction', 'SSS WISP (ER)', 50.00),
(359, 30, 'Deduction', 'PhilHealth (EE)', 262.50),
(360, 30, 'Deduction', 'PhilHealth (ER)', 262.50),
(361, 30, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(362, 30, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(363, 30, 'Deduction', 'Late/Undertime', 131.25),
(364, 30, 'Deduction', 'Withholding Tax', 1040.45),
(365, 31, 'Allowance', 'Rice Subsidy', 2500.00),
(366, 31, 'Allowance', 'Meal Allowance', 1000.00),
(367, 31, 'Allowance', 'Laundry Allowance', 400.00),
(368, 31, 'Allowance', 'Travel Allowance', 1500.00),
(369, 31, 'Allowance', 'Communication Allowance', 500.00),
(370, 31, 'Allowance', 'Overtime Pay', 468.75),
(371, 31, 'Deduction', 'SSS Regular (EE)', 375.00),
(372, 31, 'Deduction', 'SSS Regular (ER)', 750.00),
(373, 31, 'Deduction', 'PhilHealth (EE)', 187.50),
(374, 31, 'Deduction', 'PhilHealth (ER)', 187.50),
(375, 31, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(376, 31, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(377, 31, 'Deduction', 'Late/Undertime', 11.72),
(378, 31, 'Deduction', 'Withholding Tax', 416.71),
(379, 32, 'Allowance', 'Rice Subsidy', 2500.00),
(380, 32, 'Allowance', 'Meal Allowance', 3000.00),
(381, 32, 'Allowance', 'Laundry Allowance', 400.00),
(382, 32, 'Allowance', 'Travel Allowance', 7000.00),
(383, 32, 'Allowance', 'Communication Allowance', 2000.00),
(384, 32, 'Deduction', 'SSS Regular (EE)', 500.00),
(385, 32, 'Deduction', 'SSS Regular (ER)', 1000.00),
(386, 32, 'Deduction', 'SSS WISP (EE)', 375.00),
(387, 32, 'Deduction', 'SSS WISP (ER)', 750.00),
(388, 32, 'Deduction', 'PhilHealth (EE)', 662.50),
(389, 32, 'Deduction', 'PhilHealth (ER)', 662.50),
(390, 32, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(391, 32, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(392, 32, 'Deduction', 'Withholding Tax', 5878.15),
(393, 33, 'Allowance', 'Rice Subsidy', 2500.00),
(394, 33, 'Allowance', 'Meal Allowance', 2500.00),
(395, 33, 'Allowance', 'Laundry Allowance', 400.00),
(396, 33, 'Allowance', 'Travel Allowance', 5000.00),
(397, 33, 'Allowance', 'Communication Allowance', 1500.00),
(398, 33, 'Allowance', 'Overtime Pay', 1875.00),
(399, 33, 'Deduction', 'SSS Regular (EE)', 500.00),
(400, 33, 'Deduction', 'SSS Regular (ER)', 1000.00),
(401, 33, 'Deduction', 'SSS WISP (EE)', 375.00),
(402, 33, 'Deduction', 'SSS WISP (ER)', 750.00),
(403, 33, 'Deduction', 'PhilHealth (EE)', 500.00),
(404, 33, 'Deduction', 'PhilHealth (ER)', 500.00),
(405, 33, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(406, 33, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(407, 33, 'Deduction', 'Late/Undertime', 93.75),
(408, 33, 'Deduction', 'Withholding Tax', 4045.45),
(409, 34, 'Allowance', 'Rice Subsidy', 2500.00),
(410, 34, 'Allowance', 'Meal Allowance', 2000.00),
(411, 34, 'Allowance', 'Laundry Allowance', 400.00),
(412, 34, 'Allowance', 'Travel Allowance', 3500.00),
(413, 34, 'Allowance', 'Communication Allowance', 1200.00),
(414, 34, 'Deduction', 'SSS Regular (EE)', 500.00),
(415, 34, 'Deduction', 'SSS Regular (ER)', 1000.00),
(416, 34, 'Deduction', 'SSS WISP (EE)', 25.00),
(417, 34, 'Deduction', 'SSS WISP (ER)', 50.00),
(418, 34, 'Deduction', 'PhilHealth (EE)', 262.50),
(419, 34, 'Deduction', 'PhilHealth (ER)', 262.50),
(420, 34, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(421, 34, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(422, 34, 'Deduction', 'Withholding Tax', 1446.70),
(423, 35, 'Allowance', 'Rice Subsidy', 2500.00),
(424, 35, 'Allowance', 'Meal Allowance', 2500.00),
(425, 35, 'Allowance', 'Laundry Allowance', 400.00),
(426, 35, 'Allowance', 'Travel Allowance', 5000.00),
(427, 35, 'Allowance', 'Communication Allowance', 1500.00),
(428, 35, 'Allowance', 'Overtime Pay', 312.50),
(429, 35, 'Deduction', 'SSS Regular (EE)', 500.00),
(430, 35, 'Deduction', 'SSS Regular (ER)', 1000.00),
(431, 35, 'Deduction', 'SSS WISP (EE)', 375.00),
(432, 35, 'Deduction', 'SSS WISP (ER)', 750.00),
(433, 35, 'Deduction', 'PhilHealth (EE)', 500.00),
(434, 35, 'Deduction', 'PhilHealth (ER)', 500.00),
(435, 35, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(436, 35, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(437, 35, 'Deduction', 'Late/Undertime', 20.84),
(438, 35, 'Deduction', 'Withholding Tax', 3747.53),
(439, 36, 'Allowance', 'Rice Subsidy', 2500.00),
(440, 36, 'Allowance', 'Meal Allowance', 3500.00),
(441, 36, 'Allowance', 'Laundry Allowance', 400.00),
(442, 36, 'Allowance', 'Travel Allowance', 10000.00),
(443, 36, 'Allowance', 'Communication Allowance', 3000.00),
(444, 36, 'Allowance', 'Overtime Pay', 3125.00),
(445, 36, 'Deduction', 'SSS Regular (EE)', 500.00),
(446, 36, 'Deduction', 'SSS Regular (ER)', 1000.00),
(447, 36, 'Deduction', 'SSS WISP (EE)', 375.00),
(448, 36, 'Deduction', 'SSS WISP (ER)', 750.00),
(449, 36, 'Deduction', 'PhilHealth (EE)', 1000.00),
(450, 36, 'Deduction', 'PhilHealth (ER)', 1000.00),
(451, 36, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(452, 36, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(453, 36, 'Deduction', 'Late/Undertime', 125.00),
(454, 36, 'Deduction', 'Withholding Tax', 5331.28),
(455, 37, 'Allowance', 'Rice Subsidy', 2500.00),
(456, 37, 'Allowance', 'Meal Allowance', 3500.00),
(457, 37, 'Allowance', 'Laundry Allowance', 400.00),
(458, 37, 'Allowance', 'Travel Allowance', 10000.00),
(459, 37, 'Allowance', 'Communication Allowance', 3000.00),
(460, 37, 'Allowance', 'Overtime Pay', 1562.50),
(461, 37, 'Deduction', 'SSS Regular (EE)', 500.00),
(462, 37, 'Deduction', 'SSS Regular (ER)', 1000.00),
(463, 37, 'Deduction', 'SSS WISP (EE)', 375.00),
(464, 37, 'Deduction', 'SSS WISP (ER)', 750.00),
(465, 37, 'Deduction', 'PhilHealth (EE)', 1000.00),
(466, 37, 'Deduction', 'PhilHealth (ER)', 1000.00),
(467, 37, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(468, 37, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(469, 37, 'Deduction', 'Withholding Tax', 8259.40),
(470, 38, 'Allowance', 'Rice Subsidy', 2500.00),
(471, 38, 'Allowance', 'Meal Allowance', 1500.00),
(472, 38, 'Allowance', 'Laundry Allowance', 400.00),
(473, 38, 'Allowance', 'Travel Allowance', 2500.00),
(474, 38, 'Allowance', 'Communication Allowance', 800.00),
(475, 38, 'Deduction', 'SSS Regular (EE)', 500.00),
(476, 38, 'Deduction', 'SSS Regular (ER)', 1000.00),
(477, 38, 'Deduction', 'SSS WISP (EE)', 25.00),
(478, 38, 'Deduction', 'SSS WISP (ER)', 50.00),
(479, 38, 'Deduction', 'PhilHealth (EE)', 262.50),
(480, 38, 'Deduction', 'PhilHealth (ER)', 262.50),
(481, 38, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(482, 38, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(483, 38, 'Deduction', 'Late/Undertime', 131.25),
(484, 38, 'Deduction', 'Withholding Tax', 437.22),
(485, 39, 'Allowance', 'Rice Subsidy', 2500.00),
(486, 39, 'Allowance', 'Meal Allowance', 1000.00),
(487, 39, 'Allowance', 'Laundry Allowance', 400.00),
(488, 39, 'Allowance', 'Travel Allowance', 1500.00),
(489, 39, 'Allowance', 'Communication Allowance', 500.00),
(490, 39, 'Allowance', 'Overtime Pay', 468.75),
(491, 39, 'Deduction', 'SSS Regular (EE)', 375.00),
(492, 39, 'Deduction', 'SSS Regular (ER)', 750.00),
(493, 39, 'Deduction', 'PhilHealth (EE)', 187.50),
(494, 39, 'Deduction', 'PhilHealth (ER)', 187.50),
(495, 39, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(496, 39, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(497, 39, 'Deduction', 'Late/Undertime', 11.72),
(498, 40, 'Allowance', 'Rice Subsidy', 2500.00),
(499, 40, 'Allowance', 'Meal Allowance', 3000.00),
(500, 40, 'Allowance', 'Laundry Allowance', 400.00),
(501, 40, 'Allowance', 'Travel Allowance', 7000.00),
(502, 40, 'Allowance', 'Communication Allowance', 2000.00),
(503, 40, 'Deduction', 'SSS Regular (EE)', 500.00),
(504, 40, 'Deduction', 'SSS Regular (ER)', 1000.00),
(505, 40, 'Deduction', 'SSS WISP (EE)', 375.00),
(506, 40, 'Deduction', 'SSS WISP (ER)', 750.00),
(507, 40, 'Deduction', 'PhilHealth (EE)', 662.50),
(508, 40, 'Deduction', 'PhilHealth (ER)', 662.50),
(509, 40, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(510, 40, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(511, 40, 'Deduction', 'Withholding Tax', 4066.70),
(512, 41, 'Allowance', 'Rice Subsidy', 2500.00),
(513, 41, 'Allowance', 'Meal Allowance', 2500.00),
(514, 41, 'Allowance', 'Laundry Allowance', 400.00),
(515, 41, 'Allowance', 'Travel Allowance', 5000.00),
(516, 41, 'Allowance', 'Communication Allowance', 1500.00),
(517, 41, 'Allowance', 'Overtime Pay', 1875.00),
(518, 41, 'Deduction', 'SSS Regular (EE)', 500.00),
(519, 41, 'Deduction', 'SSS Regular (ER)', 1000.00),
(520, 41, 'Deduction', 'SSS WISP (EE)', 375.00),
(521, 41, 'Deduction', 'SSS WISP (ER)', 750.00),
(522, 41, 'Deduction', 'PhilHealth (EE)', 500.00),
(523, 41, 'Deduction', 'PhilHealth (ER)', 500.00),
(524, 41, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(525, 41, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(526, 41, 'Deduction', 'Late/Undertime', 93.75),
(527, 41, 'Deduction', 'Withholding Tax', 2855.45),
(528, 42, 'Allowance', 'Rice Subsidy', 2500.00),
(529, 42, 'Allowance', 'Meal Allowance', 2000.00),
(530, 42, 'Allowance', 'Laundry Allowance', 400.00),
(531, 42, 'Allowance', 'Travel Allowance', 3500.00),
(532, 42, 'Allowance', 'Communication Allowance', 1200.00),
(533, 42, 'Deduction', 'SSS Regular (EE)', 500.00),
(534, 42, 'Deduction', 'SSS Regular (ER)', 1000.00),
(535, 42, 'Deduction', 'SSS WISP (EE)', 25.00),
(536, 42, 'Deduction', 'SSS WISP (ER)', 50.00),
(537, 42, 'Deduction', 'PhilHealth (EE)', 262.50),
(538, 42, 'Deduction', 'PhilHealth (ER)', 262.50),
(539, 42, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(540, 42, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(541, 42, 'Deduction', 'Withholding Tax', 599.40),
(542, 43, 'Allowance', 'Rice Subsidy', 2500.00),
(543, 43, 'Allowance', 'Meal Allowance', 2500.00),
(544, 43, 'Allowance', 'Laundry Allowance', 400.00),
(545, 43, 'Allowance', 'Travel Allowance', 5000.00),
(546, 43, 'Allowance', 'Communication Allowance', 1500.00),
(547, 43, 'Allowance', 'Overtime Pay', 312.50),
(548, 43, 'Deduction', 'SSS Regular (EE)', 500.00),
(549, 43, 'Deduction', 'SSS Regular (ER)', 1000.00),
(550, 43, 'Deduction', 'SSS WISP (EE)', 375.00),
(551, 43, 'Deduction', 'SSS WISP (ER)', 750.00),
(552, 43, 'Deduction', 'PhilHealth (EE)', 500.00),
(553, 43, 'Deduction', 'PhilHealth (ER)', 500.00),
(554, 43, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(555, 43, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(556, 43, 'Deduction', 'Late/Undertime', 20.84),
(557, 43, 'Deduction', 'Withholding Tax', 2557.53),
(558, 44, 'Allowance', 'Rice Subsidy', 2500.00),
(559, 44, 'Allowance', 'Meal Allowance', 3500.00),
(560, 44, 'Allowance', 'Laundry Allowance', 400.00),
(561, 44, 'Allowance', 'Travel Allowance', 10000.00),
(562, 44, 'Allowance', 'Communication Allowance', 3000.00),
(563, 44, 'Allowance', 'Overtime Pay', 3125.00),
(564, 44, 'Deduction', 'SSS Regular (EE)', 500.00),
(565, 44, 'Deduction', 'SSS Regular (ER)', 1000.00),
(566, 44, 'Deduction', 'SSS WISP (EE)', 375.00),
(567, 44, 'Deduction', 'SSS WISP (ER)', 750.00),
(568, 44, 'Deduction', 'PhilHealth (EE)', 1000.00),
(569, 44, 'Deduction', 'PhilHealth (ER)', 1000.00),
(570, 44, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(571, 44, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(572, 44, 'Deduction', 'Late/Undertime', 125.00),
(573, 44, 'Deduction', 'Withholding Tax', 5331.28),
(574, 45, 'Allowance', 'Rice Subsidy', 2500.00),
(575, 45, 'Allowance', 'Meal Allowance', 3500.00),
(576, 45, 'Allowance', 'Laundry Allowance', 400.00),
(577, 45, 'Allowance', 'Travel Allowance', 10000.00),
(578, 45, 'Allowance', 'Communication Allowance', 3000.00),
(579, 45, 'Allowance', 'Overtime Pay', 1562.50),
(580, 45, 'Deduction', 'SSS Regular (EE)', 500.00),
(581, 45, 'Deduction', 'SSS Regular (ER)', 1000.00),
(582, 45, 'Deduction', 'SSS WISP (EE)', 375.00),
(583, 45, 'Deduction', 'SSS WISP (ER)', 750.00),
(584, 45, 'Deduction', 'PhilHealth (EE)', 1000.00),
(585, 45, 'Deduction', 'PhilHealth (ER)', 1000.00),
(586, 45, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(587, 45, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(588, 45, 'Deduction', 'Withholding Tax', 8259.40),
(589, 46, 'Allowance', 'Rice Subsidy', 2500.00),
(590, 46, 'Allowance', 'Meal Allowance', 1500.00),
(591, 46, 'Allowance', 'Laundry Allowance', 400.00),
(592, 46, 'Allowance', 'Travel Allowance', 2500.00),
(593, 46, 'Allowance', 'Communication Allowance', 800.00),
(594, 46, 'Deduction', 'SSS Regular (EE)', 500.00),
(595, 46, 'Deduction', 'SSS Regular (ER)', 1000.00),
(596, 46, 'Deduction', 'SSS WISP (EE)', 25.00),
(597, 46, 'Deduction', 'SSS WISP (ER)', 50.00),
(598, 46, 'Deduction', 'PhilHealth (EE)', 262.50),
(599, 46, 'Deduction', 'PhilHealth (ER)', 262.50),
(600, 46, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(601, 46, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(602, 46, 'Deduction', 'Late/Undertime', 131.25),
(603, 46, 'Deduction', 'Withholding Tax', 437.22),
(604, 47, 'Allowance', 'Rice Subsidy', 2500.00),
(605, 47, 'Allowance', 'Meal Allowance', 1000.00),
(606, 47, 'Allowance', 'Laundry Allowance', 400.00),
(607, 47, 'Allowance', 'Travel Allowance', 1500.00),
(608, 47, 'Allowance', 'Communication Allowance', 500.00),
(609, 47, 'Allowance', 'Overtime Pay', 468.75),
(610, 47, 'Deduction', 'SSS Regular (EE)', 375.00),
(611, 47, 'Deduction', 'SSS Regular (ER)', 750.00),
(612, 47, 'Deduction', 'PhilHealth (EE)', 187.50),
(613, 47, 'Deduction', 'PhilHealth (ER)', 187.50),
(614, 47, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(615, 47, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(616, 47, 'Deduction', 'Late/Undertime', 11.72),
(617, 48, 'Allowance', 'Rice Subsidy', 2500.00),
(618, 48, 'Allowance', 'Meal Allowance', 3000.00),
(619, 48, 'Allowance', 'Laundry Allowance', 400.00),
(620, 48, 'Allowance', 'Travel Allowance', 7000.00),
(621, 48, 'Allowance', 'Communication Allowance', 2000.00),
(622, 48, 'Deduction', 'SSS Regular (EE)', 500.00),
(623, 48, 'Deduction', 'SSS Regular (ER)', 1000.00),
(624, 48, 'Deduction', 'SSS WISP (EE)', 375.00),
(625, 48, 'Deduction', 'SSS WISP (ER)', 750.00),
(626, 48, 'Deduction', 'PhilHealth (EE)', 662.50),
(627, 48, 'Deduction', 'PhilHealth (ER)', 662.50),
(628, 48, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(629, 48, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(630, 48, 'Deduction', 'Withholding Tax', 4066.70),
(631, 49, 'Allowance', 'Rice Subsidy', 2500.00),
(632, 49, 'Allowance', 'Meal Allowance', 2500.00),
(633, 49, 'Allowance', 'Laundry Allowance', 400.00),
(634, 49, 'Allowance', 'Travel Allowance', 5000.00),
(635, 49, 'Allowance', 'Communication Allowance', 1500.00),
(636, 49, 'Allowance', 'Overtime Pay', 1875.00),
(637, 49, 'Deduction', 'SSS Regular (EE)', 500.00),
(638, 49, 'Deduction', 'SSS Regular (ER)', 1000.00),
(639, 49, 'Deduction', 'SSS WISP (EE)', 375.00),
(640, 49, 'Deduction', 'SSS WISP (ER)', 750.00),
(641, 49, 'Deduction', 'PhilHealth (EE)', 500.00),
(642, 49, 'Deduction', 'PhilHealth (ER)', 500.00),
(643, 49, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(644, 49, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(645, 49, 'Deduction', 'Late/Undertime', 93.75),
(646, 49, 'Deduction', 'Withholding Tax', 2855.45),
(647, 50, 'Allowance', 'Rice Subsidy', 2500.00),
(648, 50, 'Allowance', 'Meal Allowance', 2000.00),
(649, 50, 'Allowance', 'Laundry Allowance', 400.00),
(650, 50, 'Allowance', 'Travel Allowance', 3500.00),
(651, 50, 'Allowance', 'Communication Allowance', 1200.00),
(652, 50, 'Deduction', 'SSS Regular (EE)', 500.00),
(653, 50, 'Deduction', 'SSS Regular (ER)', 1000.00),
(654, 50, 'Deduction', 'SSS WISP (EE)', 25.00),
(655, 50, 'Deduction', 'SSS WISP (ER)', 50.00),
(656, 50, 'Deduction', 'PhilHealth (EE)', 262.50),
(657, 50, 'Deduction', 'PhilHealth (ER)', 262.50),
(658, 50, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(659, 50, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(660, 50, 'Deduction', 'Withholding Tax', 599.40),
(661, 51, 'Allowance', 'Rice Subsidy', 2500.00),
(662, 51, 'Allowance', 'Meal Allowance', 2500.00),
(663, 51, 'Allowance', 'Laundry Allowance', 400.00),
(664, 51, 'Allowance', 'Travel Allowance', 5000.00),
(665, 51, 'Allowance', 'Communication Allowance', 1500.00),
(666, 51, 'Allowance', 'Overtime Pay', 312.50),
(667, 51, 'Deduction', 'SSS Regular (EE)', 500.00),
(668, 51, 'Deduction', 'SSS Regular (ER)', 1000.00),
(669, 51, 'Deduction', 'SSS WISP (EE)', 375.00),
(670, 51, 'Deduction', 'SSS WISP (ER)', 750.00),
(671, 51, 'Deduction', 'PhilHealth (EE)', 500.00),
(672, 51, 'Deduction', 'PhilHealth (ER)', 500.00),
(673, 51, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(674, 51, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(675, 51, 'Deduction', 'Late/Undertime', 20.84),
(676, 51, 'Deduction', 'Withholding Tax', 2557.53),
(677, 52, 'Allowance', 'Rice Subsidy', 2500.00),
(678, 52, 'Allowance', 'Meal Allowance', 3500.00),
(679, 52, 'Allowance', 'Laundry Allowance', 400.00),
(680, 52, 'Allowance', 'Travel Allowance', 10000.00),
(681, 52, 'Allowance', 'Communication Allowance', 3000.00),
(682, 52, 'Allowance', 'Overtime Pay', 3125.00),
(683, 52, 'Deduction', 'SSS Regular (EE)', 500.00),
(684, 52, 'Deduction', 'SSS Regular (ER)', 1000.00),
(685, 52, 'Deduction', 'SSS WISP (EE)', 375.00),
(686, 52, 'Deduction', 'SSS WISP (ER)', 750.00),
(687, 52, 'Deduction', 'PhilHealth (EE)', 1000.00),
(688, 52, 'Deduction', 'PhilHealth (ER)', 1000.00),
(689, 52, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(690, 52, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(691, 52, 'Deduction', 'Late/Undertime', 125.00),
(692, 52, 'Deduction', 'Withholding Tax', 5331.28),
(693, 53, 'Allowance', 'Rice Subsidy', 2500.00),
(694, 53, 'Allowance', 'Meal Allowance', 3500.00),
(695, 53, 'Allowance', 'Laundry Allowance', 400.00),
(696, 53, 'Allowance', 'Travel Allowance', 10000.00),
(697, 53, 'Allowance', 'Communication Allowance', 3000.00),
(698, 53, 'Allowance', 'Overtime Pay', 1562.50),
(699, 53, 'Deduction', 'SSS Regular (EE)', 500.00),
(700, 53, 'Deduction', 'SSS Regular (ER)', 1000.00),
(701, 53, 'Deduction', 'SSS WISP (EE)', 375.00),
(702, 53, 'Deduction', 'SSS WISP (ER)', 750.00),
(703, 53, 'Deduction', 'PhilHealth (EE)', 1000.00),
(704, 53, 'Deduction', 'PhilHealth (ER)', 1000.00),
(705, 53, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(706, 53, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(707, 53, 'Deduction', 'Withholding Tax', 8259.40),
(708, 54, 'Allowance', 'Rice Subsidy', 2500.00),
(709, 54, 'Allowance', 'Meal Allowance', 1500.00),
(710, 54, 'Allowance', 'Laundry Allowance', 400.00),
(711, 54, 'Allowance', 'Travel Allowance', 2500.00),
(712, 54, 'Allowance', 'Communication Allowance', 800.00),
(713, 54, 'Deduction', 'SSS Regular (EE)', 500.00),
(714, 54, 'Deduction', 'SSS Regular (ER)', 1000.00),
(715, 54, 'Deduction', 'SSS WISP (EE)', 25.00),
(716, 54, 'Deduction', 'SSS WISP (ER)', 50.00),
(717, 54, 'Deduction', 'PhilHealth (EE)', 262.50),
(718, 54, 'Deduction', 'PhilHealth (ER)', 262.50),
(719, 54, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(720, 54, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(721, 54, 'Deduction', 'Late/Undertime', 131.25),
(722, 54, 'Deduction', 'Withholding Tax', 437.22),
(723, 55, 'Allowance', 'Rice Subsidy', 2500.00),
(724, 55, 'Allowance', 'Meal Allowance', 1000.00),
(725, 55, 'Allowance', 'Laundry Allowance', 400.00),
(726, 55, 'Allowance', 'Travel Allowance', 1500.00),
(727, 55, 'Allowance', 'Communication Allowance', 500.00),
(728, 55, 'Allowance', 'Overtime Pay', 468.75),
(729, 55, 'Deduction', 'SSS Regular (EE)', 375.00),
(730, 55, 'Deduction', 'SSS Regular (ER)', 750.00),
(731, 55, 'Deduction', 'PhilHealth (EE)', 187.50),
(732, 55, 'Deduction', 'PhilHealth (ER)', 187.50),
(733, 55, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(734, 55, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(735, 55, 'Deduction', 'Late/Undertime', 11.72),
(736, 56, 'Allowance', 'Rice Subsidy', 2500.00),
(737, 56, 'Allowance', 'Meal Allowance', 3000.00),
(738, 56, 'Allowance', 'Laundry Allowance', 400.00),
(739, 56, 'Allowance', 'Travel Allowance', 7000.00),
(740, 56, 'Allowance', 'Communication Allowance', 2000.00),
(741, 56, 'Deduction', 'SSS Regular (EE)', 500.00),
(742, 56, 'Deduction', 'SSS Regular (ER)', 1000.00),
(743, 56, 'Deduction', 'SSS WISP (EE)', 375.00),
(744, 56, 'Deduction', 'SSS WISP (ER)', 750.00),
(745, 56, 'Deduction', 'PhilHealth (EE)', 662.50),
(746, 56, 'Deduction', 'PhilHealth (ER)', 662.50),
(747, 56, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(748, 56, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(749, 56, 'Deduction', 'Withholding Tax', 4066.70),
(750, 57, 'Allowance', 'Rice Subsidy', 2500.00),
(751, 57, 'Allowance', 'Meal Allowance', 2500.00),
(752, 57, 'Allowance', 'Laundry Allowance', 400.00),
(753, 57, 'Allowance', 'Travel Allowance', 5000.00),
(754, 57, 'Allowance', 'Communication Allowance', 1500.00),
(755, 57, 'Allowance', 'Overtime Pay', 1875.00),
(756, 57, 'Deduction', 'SSS Regular (EE)', 500.00),
(757, 57, 'Deduction', 'SSS Regular (ER)', 1000.00),
(758, 57, 'Deduction', 'SSS WISP (EE)', 375.00),
(759, 57, 'Deduction', 'SSS WISP (ER)', 750.00),
(760, 57, 'Deduction', 'PhilHealth (EE)', 500.00),
(761, 57, 'Deduction', 'PhilHealth (ER)', 500.00),
(762, 57, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(763, 57, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(764, 57, 'Deduction', 'Late/Undertime', 93.75),
(765, 57, 'Deduction', 'Withholding Tax', 2855.45),
(766, 58, 'Allowance', 'Rice Subsidy', 2500.00),
(767, 58, 'Allowance', 'Meal Allowance', 2000.00),
(768, 58, 'Allowance', 'Laundry Allowance', 400.00),
(769, 58, 'Allowance', 'Travel Allowance', 3500.00),
(770, 58, 'Allowance', 'Communication Allowance', 1200.00),
(771, 58, 'Deduction', 'SSS Regular (EE)', 500.00),
(772, 58, 'Deduction', 'SSS Regular (ER)', 1000.00),
(773, 58, 'Deduction', 'SSS WISP (EE)', 25.00),
(774, 58, 'Deduction', 'SSS WISP (ER)', 50.00),
(775, 58, 'Deduction', 'PhilHealth (EE)', 262.50),
(776, 58, 'Deduction', 'PhilHealth (ER)', 262.50),
(777, 58, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(778, 58, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(779, 58, 'Deduction', 'Withholding Tax', 599.40),
(780, 59, 'Allowance', 'Rice Subsidy', 2500.00),
(781, 59, 'Allowance', 'Meal Allowance', 2500.00),
(782, 59, 'Allowance', 'Laundry Allowance', 400.00),
(783, 59, 'Allowance', 'Travel Allowance', 5000.00),
(784, 59, 'Allowance', 'Communication Allowance', 1500.00),
(785, 59, 'Allowance', 'Overtime Pay', 312.50),
(786, 59, 'Deduction', 'SSS Regular (EE)', 500.00),
(787, 59, 'Deduction', 'SSS Regular (ER)', 1000.00),
(788, 59, 'Deduction', 'SSS WISP (EE)', 375.00),
(789, 59, 'Deduction', 'SSS WISP (ER)', 750.00),
(790, 59, 'Deduction', 'PhilHealth (EE)', 500.00),
(791, 59, 'Deduction', 'PhilHealth (ER)', 500.00),
(792, 59, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(793, 59, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(794, 59, 'Deduction', 'Late/Undertime', 20.84),
(795, 59, 'Deduction', 'Withholding Tax', 2557.53),
(796, 60, 'Allowance', 'Rice Subsidy', 2500.00),
(797, 60, 'Allowance', 'Meal Allowance', 3500.00),
(798, 60, 'Allowance', 'Laundry Allowance', 400.00),
(799, 60, 'Allowance', 'Travel Allowance', 10000.00),
(800, 60, 'Allowance', 'Communication Allowance', 3000.00),
(801, 60, 'Allowance', 'Overtime Pay', 3125.00),
(802, 60, 'Deduction', 'SSS Regular (EE)', 500.00),
(803, 60, 'Deduction', 'SSS Regular (ER)', 1000.00),
(804, 60, 'Deduction', 'SSS WISP (EE)', 375.00),
(805, 60, 'Deduction', 'SSS WISP (ER)', 750.00),
(806, 60, 'Deduction', 'PhilHealth (EE)', 1000.00),
(807, 60, 'Deduction', 'PhilHealth (ER)', 1000.00),
(808, 60, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(809, 60, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(810, 60, 'Deduction', 'Late/Undertime', 125.00),
(811, 60, 'Deduction', 'Withholding Tax', 5331.28),
(812, 61, 'Allowance', 'Rice Subsidy', 2500.00),
(813, 61, 'Allowance', 'Meal Allowance', 3500.00),
(814, 61, 'Allowance', 'Laundry Allowance', 400.00),
(815, 61, 'Allowance', 'Travel Allowance', 10000.00),
(816, 61, 'Allowance', 'Communication Allowance', 3000.00),
(817, 61, 'Allowance', 'Overtime Pay', 1562.50),
(818, 61, 'Deduction', 'SSS Regular (EE)', 500.00),
(819, 61, 'Deduction', 'SSS Regular (ER)', 1000.00),
(820, 61, 'Deduction', 'SSS WISP (EE)', 375.00),
(821, 61, 'Deduction', 'SSS WISP (ER)', 750.00),
(822, 61, 'Deduction', 'PhilHealth (EE)', 1000.00),
(823, 61, 'Deduction', 'PhilHealth (ER)', 1000.00),
(824, 61, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(825, 61, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(826, 61, 'Deduction', 'Withholding Tax', 8259.40),
(827, 62, 'Allowance', 'Rice Subsidy', 2500.00),
(828, 62, 'Allowance', 'Meal Allowance', 1500.00),
(829, 62, 'Allowance', 'Laundry Allowance', 400.00),
(830, 62, 'Allowance', 'Travel Allowance', 2500.00),
(831, 62, 'Allowance', 'Communication Allowance', 800.00),
(832, 62, 'Deduction', 'SSS Regular (EE)', 500.00),
(833, 62, 'Deduction', 'SSS Regular (ER)', 1000.00),
(834, 62, 'Deduction', 'SSS WISP (EE)', 25.00),
(835, 62, 'Deduction', 'SSS WISP (ER)', 50.00),
(836, 62, 'Deduction', 'PhilHealth (EE)', 262.50),
(837, 62, 'Deduction', 'PhilHealth (ER)', 262.50),
(838, 62, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(839, 62, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(840, 62, 'Deduction', 'Late/Undertime', 131.25),
(841, 62, 'Deduction', 'Withholding Tax', 437.22),
(842, 63, 'Allowance', 'Rice Subsidy', 2500.00),
(843, 63, 'Allowance', 'Meal Allowance', 1000.00),
(844, 63, 'Allowance', 'Laundry Allowance', 400.00),
(845, 63, 'Allowance', 'Travel Allowance', 1500.00),
(846, 63, 'Allowance', 'Communication Allowance', 500.00),
(847, 63, 'Allowance', 'Overtime Pay', 468.75),
(848, 63, 'Deduction', 'SSS Regular (EE)', 375.00),
(849, 63, 'Deduction', 'SSS Regular (ER)', 750.00),
(850, 63, 'Deduction', 'PhilHealth (EE)', 187.50),
(851, 63, 'Deduction', 'PhilHealth (ER)', 187.50),
(852, 63, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(853, 63, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(854, 63, 'Deduction', 'Late/Undertime', 11.72),
(855, 64, 'Allowance', 'Rice Subsidy', 2500.00),
(856, 64, 'Allowance', 'Meal Allowance', 3000.00),
(857, 64, 'Allowance', 'Laundry Allowance', 400.00),
(858, 64, 'Allowance', 'Travel Allowance', 7000.00),
(859, 64, 'Allowance', 'Communication Allowance', 2000.00),
(860, 64, 'Deduction', 'SSS Regular (EE)', 500.00),
(861, 64, 'Deduction', 'SSS Regular (ER)', 1000.00),
(862, 64, 'Deduction', 'SSS WISP (EE)', 375.00),
(863, 64, 'Deduction', 'SSS WISP (ER)', 750.00),
(864, 64, 'Deduction', 'PhilHealth (EE)', 662.50),
(865, 64, 'Deduction', 'PhilHealth (ER)', 662.50),
(866, 64, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(867, 64, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(868, 64, 'Deduction', 'Withholding Tax', 4066.70),
(869, 65, 'Allowance', 'Rice Subsidy', 2500.00),
(870, 65, 'Allowance', 'Meal Allowance', 2500.00),
(871, 65, 'Allowance', 'Laundry Allowance', 400.00),
(872, 65, 'Allowance', 'Travel Allowance', 5000.00),
(873, 65, 'Allowance', 'Communication Allowance', 1500.00),
(874, 65, 'Allowance', 'Overtime Pay', 1875.00),
(875, 65, 'Deduction', 'SSS Regular (EE)', 500.00),
(876, 65, 'Deduction', 'SSS Regular (ER)', 1000.00),
(877, 65, 'Deduction', 'SSS WISP (EE)', 375.00),
(878, 65, 'Deduction', 'SSS WISP (ER)', 750.00),
(879, 65, 'Deduction', 'PhilHealth (EE)', 500.00),
(880, 65, 'Deduction', 'PhilHealth (ER)', 500.00),
(881, 65, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(882, 65, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(883, 65, 'Deduction', 'Late/Undertime', 93.75),
(884, 65, 'Deduction', 'Withholding Tax', 2855.45),
(885, 66, 'Allowance', 'Rice Subsidy', 2500.00),
(886, 66, 'Allowance', 'Meal Allowance', 2000.00),
(887, 66, 'Allowance', 'Laundry Allowance', 400.00),
(888, 66, 'Allowance', 'Travel Allowance', 3500.00),
(889, 66, 'Allowance', 'Communication Allowance', 1200.00),
(890, 66, 'Deduction', 'SSS Regular (EE)', 500.00),
(891, 66, 'Deduction', 'SSS Regular (ER)', 1000.00),
(892, 66, 'Deduction', 'SSS WISP (EE)', 25.00),
(893, 66, 'Deduction', 'SSS WISP (ER)', 50.00),
(894, 66, 'Deduction', 'PhilHealth (EE)', 262.50),
(895, 66, 'Deduction', 'PhilHealth (ER)', 262.50),
(896, 66, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(897, 66, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(898, 66, 'Deduction', 'Withholding Tax', 599.40),
(899, 67, 'Allowance', 'Rice Subsidy', 2500.00),
(900, 67, 'Allowance', 'Meal Allowance', 2500.00),
(901, 67, 'Allowance', 'Laundry Allowance', 400.00),
(902, 67, 'Allowance', 'Travel Allowance', 5000.00),
(903, 67, 'Allowance', 'Communication Allowance', 1500.00),
(904, 67, 'Allowance', 'Overtime Pay', 312.50),
(905, 67, 'Deduction', 'SSS Regular (EE)', 500.00),
(906, 67, 'Deduction', 'SSS Regular (ER)', 1000.00),
(907, 67, 'Deduction', 'SSS WISP (EE)', 375.00),
(908, 67, 'Deduction', 'SSS WISP (ER)', 750.00),
(909, 67, 'Deduction', 'PhilHealth (EE)', 500.00),
(910, 67, 'Deduction', 'PhilHealth (ER)', 500.00),
(911, 67, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(912, 67, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(913, 67, 'Deduction', 'Late/Undertime', 20.84),
(914, 67, 'Deduction', 'Withholding Tax', 2557.53),
(915, 68, 'Allowance', 'Rice Subsidy', 2500.00),
(916, 68, 'Allowance', 'Meal Allowance', 3500.00),
(917, 68, 'Allowance', 'Laundry Allowance', 400.00),
(918, 68, 'Allowance', 'Travel Allowance', 10000.00),
(919, 68, 'Allowance', 'Communication Allowance', 3000.00),
(920, 68, 'Allowance', 'Overtime Pay', 3125.00),
(921, 68, 'Deduction', 'SSS Regular (EE)', 500.00),
(922, 68, 'Deduction', 'SSS Regular (ER)', 1000.00),
(923, 68, 'Deduction', 'SSS WISP (EE)', 375.00),
(924, 68, 'Deduction', 'SSS WISP (ER)', 750.00),
(925, 68, 'Deduction', 'PhilHealth (EE)', 1000.00),
(926, 68, 'Deduction', 'PhilHealth (ER)', 1000.00),
(927, 68, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(928, 68, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(929, 68, 'Deduction', 'Late/Undertime', 125.00),
(930, 68, 'Deduction', 'Withholding Tax', 5331.28),
(931, 69, 'Allowance', 'Rice Subsidy', 2500.00),
(932, 69, 'Allowance', 'Meal Allowance', 3500.00),
(933, 69, 'Allowance', 'Laundry Allowance', 400.00),
(934, 69, 'Allowance', 'Travel Allowance', 10000.00),
(935, 69, 'Allowance', 'Communication Allowance', 3000.00),
(936, 69, 'Allowance', 'Overtime Pay', 1562.50),
(937, 69, 'Deduction', 'SSS Regular (EE)', 500.00),
(938, 69, 'Deduction', 'SSS Regular (ER)', 1000.00),
(939, 69, 'Deduction', 'SSS WISP (EE)', 375.00),
(940, 69, 'Deduction', 'SSS WISP (ER)', 750.00),
(941, 69, 'Deduction', 'PhilHealth (EE)', 1000.00),
(942, 69, 'Deduction', 'PhilHealth (ER)', 1000.00),
(943, 69, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(944, 69, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(945, 69, 'Deduction', 'Withholding Tax', 8259.40),
(946, 70, 'Allowance', 'Rice Subsidy', 2500.00),
(947, 70, 'Allowance', 'Meal Allowance', 1500.00),
(948, 70, 'Allowance', 'Laundry Allowance', 400.00),
(949, 70, 'Allowance', 'Travel Allowance', 2500.00),
(950, 70, 'Allowance', 'Communication Allowance', 800.00),
(951, 70, 'Deduction', 'SSS Regular (EE)', 500.00),
(952, 70, 'Deduction', 'SSS Regular (ER)', 1000.00),
(953, 70, 'Deduction', 'SSS WISP (EE)', 25.00),
(954, 70, 'Deduction', 'SSS WISP (ER)', 50.00),
(955, 70, 'Deduction', 'PhilHealth (EE)', 262.50),
(956, 70, 'Deduction', 'PhilHealth (ER)', 262.50),
(957, 70, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(958, 70, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(959, 70, 'Deduction', 'Late/Undertime', 131.25),
(960, 70, 'Deduction', 'Withholding Tax', 437.22),
(961, 71, 'Allowance', 'Rice Subsidy', 2500.00),
(962, 71, 'Allowance', 'Meal Allowance', 1000.00),
(963, 71, 'Allowance', 'Laundry Allowance', 400.00),
(964, 71, 'Allowance', 'Travel Allowance', 1500.00),
(965, 71, 'Allowance', 'Communication Allowance', 500.00),
(966, 71, 'Allowance', 'Overtime Pay', 468.75),
(967, 71, 'Deduction', 'SSS Regular (EE)', 375.00),
(968, 71, 'Deduction', 'SSS Regular (ER)', 750.00),
(969, 71, 'Deduction', 'PhilHealth (EE)', 187.50),
(970, 71, 'Deduction', 'PhilHealth (ER)', 187.50),
(971, 71, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(972, 71, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(973, 71, 'Deduction', 'Late/Undertime', 11.72),
(974, 72, 'Allowance', 'Rice Subsidy', 2500.00),
(975, 72, 'Allowance', 'Meal Allowance', 3000.00),
(976, 72, 'Allowance', 'Laundry Allowance', 400.00),
(977, 72, 'Allowance', 'Travel Allowance', 7000.00),
(978, 72, 'Allowance', 'Communication Allowance', 2000.00),
(979, 72, 'Deduction', 'SSS Regular (EE)', 500.00),
(980, 72, 'Deduction', 'SSS Regular (ER)', 1000.00),
(981, 72, 'Deduction', 'SSS WISP (EE)', 375.00),
(982, 72, 'Deduction', 'SSS WISP (ER)', 750.00),
(983, 72, 'Deduction', 'PhilHealth (EE)', 662.50),
(984, 72, 'Deduction', 'PhilHealth (ER)', 662.50),
(985, 72, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(986, 72, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(987, 72, 'Deduction', 'Withholding Tax', 4066.70),
(988, 73, 'Allowance', 'Rice Subsidy', 2500.00),
(989, 73, 'Allowance', 'Meal Allowance', 2500.00),
(990, 73, 'Allowance', 'Laundry Allowance', 400.00),
(991, 73, 'Allowance', 'Travel Allowance', 5000.00),
(992, 73, 'Allowance', 'Communication Allowance', 1500.00),
(993, 73, 'Allowance', 'Overtime Pay', 1875.00),
(994, 73, 'Deduction', 'SSS Regular (EE)', 500.00),
(995, 73, 'Deduction', 'SSS Regular (ER)', 1000.00),
(996, 73, 'Deduction', 'SSS WISP (EE)', 375.00),
(997, 73, 'Deduction', 'SSS WISP (ER)', 750.00),
(998, 73, 'Deduction', 'PhilHealth (EE)', 500.00),
(999, 73, 'Deduction', 'PhilHealth (ER)', 500.00),
(1000, 73, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1001, 73, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1002, 73, 'Deduction', 'Late/Undertime', 93.75),
(1003, 73, 'Deduction', 'Withholding Tax', 2855.45),
(1004, 74, 'Allowance', 'Rice Subsidy', 2500.00),
(1005, 74, 'Allowance', 'Meal Allowance', 2000.00),
(1006, 74, 'Allowance', 'Laundry Allowance', 400.00),
(1007, 74, 'Allowance', 'Travel Allowance', 3500.00),
(1008, 74, 'Allowance', 'Communication Allowance', 1200.00),
(1009, 74, 'Deduction', 'SSS Regular (EE)', 500.00),
(1010, 74, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1011, 74, 'Deduction', 'SSS WISP (EE)', 25.00),
(1012, 74, 'Deduction', 'SSS WISP (ER)', 50.00),
(1013, 74, 'Deduction', 'PhilHealth (EE)', 262.50),
(1014, 74, 'Deduction', 'PhilHealth (ER)', 262.50),
(1015, 74, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1016, 74, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1017, 74, 'Deduction', 'Withholding Tax', 599.40),
(1018, 75, 'Allowance', 'Rice Subsidy', 2500.00),
(1019, 75, 'Allowance', 'Meal Allowance', 2500.00),
(1020, 75, 'Allowance', 'Laundry Allowance', 400.00),
(1021, 75, 'Allowance', 'Travel Allowance', 5000.00),
(1022, 75, 'Allowance', 'Communication Allowance', 1500.00),
(1023, 75, 'Allowance', 'Overtime Pay', 312.50),
(1024, 75, 'Deduction', 'SSS Regular (EE)', 500.00),
(1025, 75, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1026, 75, 'Deduction', 'SSS WISP (EE)', 375.00),
(1027, 75, 'Deduction', 'SSS WISP (ER)', 750.00),
(1028, 75, 'Deduction', 'PhilHealth (EE)', 500.00);
INSERT INTO `payroll_item_components` (`id`, `item_id`, `component_type`, `component_name`, `amount`) VALUES
(1029, 75, 'Deduction', 'PhilHealth (ER)', 500.00),
(1030, 75, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1031, 75, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1032, 75, 'Deduction', 'Late/Undertime', 20.84),
(1033, 75, 'Deduction', 'Withholding Tax', 2557.53),
(1034, 76, 'Allowance', 'Rice Subsidy', 2500.00),
(1035, 76, 'Allowance', 'Meal Allowance', 3500.00),
(1036, 76, 'Allowance', 'Laundry Allowance', 400.00),
(1037, 76, 'Allowance', 'Travel Allowance', 10000.00),
(1038, 76, 'Allowance', 'Communication Allowance', 3000.00),
(1039, 76, 'Allowance', 'Overtime Pay', 3125.00),
(1040, 76, 'Deduction', 'SSS Regular (EE)', 500.00),
(1041, 76, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1042, 76, 'Deduction', 'SSS WISP (EE)', 375.00),
(1043, 76, 'Deduction', 'SSS WISP (ER)', 750.00),
(1044, 76, 'Deduction', 'PhilHealth (EE)', 1000.00),
(1045, 76, 'Deduction', 'PhilHealth (ER)', 1000.00),
(1046, 76, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1047, 76, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1048, 76, 'Deduction', 'Late/Undertime', 125.00),
(1049, 76, 'Deduction', 'Withholding Tax', 5331.28),
(1050, 77, 'Allowance', 'Rice Subsidy', 2500.00),
(1051, 77, 'Allowance', 'Meal Allowance', 3500.00),
(1052, 77, 'Allowance', 'Laundry Allowance', 400.00),
(1053, 77, 'Allowance', 'Travel Allowance', 10000.00),
(1054, 77, 'Allowance', 'Communication Allowance', 3000.00),
(1055, 77, 'Allowance', 'Overtime Pay', 1562.50),
(1056, 77, 'Deduction', 'SSS Regular (EE)', 500.00),
(1057, 77, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1058, 77, 'Deduction', 'SSS WISP (EE)', 375.00),
(1059, 77, 'Deduction', 'SSS WISP (ER)', 750.00),
(1060, 77, 'Deduction', 'PhilHealth (EE)', 1000.00),
(1061, 77, 'Deduction', 'PhilHealth (ER)', 1000.00),
(1062, 77, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1063, 77, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1064, 77, 'Deduction', 'Withholding Tax', 8259.40),
(1065, 78, 'Allowance', 'Rice Subsidy', 2500.00),
(1066, 78, 'Allowance', 'Meal Allowance', 1500.00),
(1067, 78, 'Allowance', 'Laundry Allowance', 400.00),
(1068, 78, 'Allowance', 'Travel Allowance', 2500.00),
(1069, 78, 'Allowance', 'Communication Allowance', 800.00),
(1070, 78, 'Deduction', 'SSS Regular (EE)', 500.00),
(1071, 78, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1072, 78, 'Deduction', 'SSS WISP (EE)', 25.00),
(1073, 78, 'Deduction', 'SSS WISP (ER)', 50.00),
(1074, 78, 'Deduction', 'PhilHealth (EE)', 262.50),
(1075, 78, 'Deduction', 'PhilHealth (ER)', 262.50),
(1076, 78, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1077, 78, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1078, 78, 'Deduction', 'Late/Undertime', 131.25),
(1079, 78, 'Deduction', 'Withholding Tax', 437.22),
(1080, 79, 'Allowance', 'Rice Subsidy', 2500.00),
(1081, 79, 'Allowance', 'Meal Allowance', 1000.00),
(1082, 79, 'Allowance', 'Laundry Allowance', 400.00),
(1083, 79, 'Allowance', 'Travel Allowance', 1500.00),
(1084, 79, 'Allowance', 'Communication Allowance', 500.00),
(1085, 79, 'Allowance', 'Overtime Pay', 468.75),
(1086, 79, 'Deduction', 'SSS Regular (EE)', 375.00),
(1087, 79, 'Deduction', 'SSS Regular (ER)', 750.00),
(1088, 79, 'Deduction', 'PhilHealth (EE)', 187.50),
(1089, 79, 'Deduction', 'PhilHealth (ER)', 187.50),
(1090, 79, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1091, 79, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1092, 79, 'Deduction', 'Late/Undertime', 11.72),
(1093, 80, 'Allowance', 'Rice Subsidy', 2500.00),
(1094, 80, 'Allowance', 'Meal Allowance', 3000.00),
(1095, 80, 'Allowance', 'Laundry Allowance', 400.00),
(1096, 80, 'Allowance', 'Travel Allowance', 7000.00),
(1097, 80, 'Allowance', 'Communication Allowance', 2000.00),
(1098, 80, 'Deduction', 'SSS Regular (EE)', 500.00),
(1099, 80, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1100, 80, 'Deduction', 'SSS WISP (EE)', 375.00),
(1101, 80, 'Deduction', 'SSS WISP (ER)', 750.00),
(1102, 80, 'Deduction', 'PhilHealth (EE)', 662.50),
(1103, 80, 'Deduction', 'PhilHealth (ER)', 662.50),
(1104, 80, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1105, 80, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1106, 80, 'Deduction', 'Withholding Tax', 4066.70),
(1107, 81, 'Allowance', 'Rice Subsidy', 2500.00),
(1108, 81, 'Allowance', 'Meal Allowance', 2500.00),
(1109, 81, 'Allowance', 'Laundry Allowance', 400.00),
(1110, 81, 'Allowance', 'Travel Allowance', 5000.00),
(1111, 81, 'Allowance', 'Communication Allowance', 1500.00),
(1112, 81, 'Allowance', 'Overtime Pay', 1875.00),
(1113, 81, 'Deduction', 'SSS Regular (EE)', 500.00),
(1114, 81, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1115, 81, 'Deduction', 'SSS WISP (EE)', 375.00),
(1116, 81, 'Deduction', 'SSS WISP (ER)', 750.00),
(1117, 81, 'Deduction', 'PhilHealth (EE)', 500.00),
(1118, 81, 'Deduction', 'PhilHealth (ER)', 500.00),
(1119, 81, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1120, 81, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1121, 81, 'Deduction', 'Late/Undertime', 93.75),
(1122, 81, 'Deduction', 'Withholding Tax', 2855.45),
(1123, 82, 'Allowance', 'Rice Subsidy', 2500.00),
(1124, 82, 'Allowance', 'Meal Allowance', 2000.00),
(1125, 82, 'Allowance', 'Laundry Allowance', 400.00),
(1126, 82, 'Allowance', 'Travel Allowance', 3500.00),
(1127, 82, 'Allowance', 'Communication Allowance', 1200.00),
(1128, 82, 'Deduction', 'SSS Regular (EE)', 500.00),
(1129, 82, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1130, 82, 'Deduction', 'SSS WISP (EE)', 25.00),
(1131, 82, 'Deduction', 'SSS WISP (ER)', 50.00),
(1132, 82, 'Deduction', 'PhilHealth (EE)', 262.50),
(1133, 82, 'Deduction', 'PhilHealth (ER)', 262.50),
(1134, 82, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1135, 82, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1136, 82, 'Deduction', 'Withholding Tax', 599.40),
(1137, 83, 'Allowance', 'Rice Subsidy', 2500.00),
(1138, 83, 'Allowance', 'Meal Allowance', 2500.00),
(1139, 83, 'Allowance', 'Laundry Allowance', 400.00),
(1140, 83, 'Allowance', 'Travel Allowance', 5000.00),
(1141, 83, 'Allowance', 'Communication Allowance', 1500.00),
(1142, 83, 'Allowance', 'Overtime Pay', 312.50),
(1143, 83, 'Deduction', 'SSS Regular (EE)', 500.00),
(1144, 83, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1145, 83, 'Deduction', 'SSS WISP (EE)', 375.00),
(1146, 83, 'Deduction', 'SSS WISP (ER)', 750.00),
(1147, 83, 'Deduction', 'PhilHealth (EE)', 500.00),
(1148, 83, 'Deduction', 'PhilHealth (ER)', 500.00),
(1149, 83, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1150, 83, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1151, 83, 'Deduction', 'Late/Undertime', 20.84),
(1152, 83, 'Deduction', 'Withholding Tax', 2557.53),
(1153, 84, 'Allowance', 'Rice Subsidy', 2500.00),
(1154, 84, 'Allowance', 'Meal Allowance', 3500.00),
(1155, 84, 'Allowance', 'Laundry Allowance', 400.00),
(1156, 84, 'Allowance', 'Travel Allowance', 10000.00),
(1157, 84, 'Allowance', 'Communication Allowance', 3000.00),
(1158, 84, 'Allowance', 'Overtime Pay', 3125.00),
(1159, 84, 'Deduction', 'SSS Regular (EE)', 500.00),
(1160, 84, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1161, 84, 'Deduction', 'SSS WISP (EE)', 375.00),
(1162, 84, 'Deduction', 'SSS WISP (ER)', 750.00),
(1163, 84, 'Deduction', 'PhilHealth (EE)', 1000.00),
(1164, 84, 'Deduction', 'PhilHealth (ER)', 1000.00),
(1165, 84, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1166, 84, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1167, 84, 'Deduction', 'Late/Undertime', 125.00),
(1168, 84, 'Deduction', 'Withholding Tax', 8618.78),
(1169, 85, 'Allowance', 'Rice Subsidy', 2500.00),
(1170, 85, 'Allowance', 'Meal Allowance', 3500.00),
(1171, 85, 'Allowance', 'Laundry Allowance', 400.00),
(1172, 85, 'Allowance', 'Travel Allowance', 10000.00),
(1173, 85, 'Allowance', 'Communication Allowance', 3000.00),
(1174, 85, 'Allowance', 'Overtime Pay', 1562.50),
(1175, 85, 'Deduction', 'SSS Regular (EE)', 500.00),
(1176, 85, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1177, 85, 'Deduction', 'SSS WISP (EE)', 375.00),
(1178, 85, 'Deduction', 'SSS WISP (ER)', 750.00),
(1179, 85, 'Deduction', 'PhilHealth (EE)', 1000.00),
(1180, 85, 'Deduction', 'PhilHealth (ER)', 1000.00),
(1181, 85, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1182, 85, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1183, 85, 'Deduction', 'Withholding Tax', 8259.40),
(1184, 86, 'Allowance', 'Rice Subsidy', 2500.00),
(1185, 86, 'Allowance', 'Meal Allowance', 1500.00),
(1186, 86, 'Allowance', 'Laundry Allowance', 400.00),
(1187, 86, 'Allowance', 'Travel Allowance', 2500.00),
(1188, 86, 'Allowance', 'Communication Allowance', 800.00),
(1189, 86, 'Deduction', 'SSS Regular (EE)', 500.00),
(1190, 86, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1191, 86, 'Deduction', 'SSS WISP (EE)', 25.00),
(1192, 86, 'Deduction', 'SSS WISP (ER)', 50.00),
(1193, 86, 'Deduction', 'PhilHealth (EE)', 262.50),
(1194, 86, 'Deduction', 'PhilHealth (ER)', 262.50),
(1195, 86, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1196, 86, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1197, 86, 'Deduction', 'Late/Undertime', 131.25),
(1198, 86, 'Deduction', 'Withholding Tax', 1988.63),
(1199, 87, 'Allowance', 'Rice Subsidy', 2500.00),
(1200, 87, 'Allowance', 'Meal Allowance', 1000.00),
(1201, 87, 'Allowance', 'Laundry Allowance', 400.00),
(1202, 87, 'Allowance', 'Travel Allowance', 1500.00),
(1203, 87, 'Allowance', 'Communication Allowance', 500.00),
(1204, 87, 'Allowance', 'Overtime Pay', 468.75),
(1205, 87, 'Deduction', 'SSS Regular (EE)', 375.00),
(1206, 87, 'Deduction', 'SSS Regular (ER)', 750.00),
(1207, 87, 'Deduction', 'PhilHealth (EE)', 187.50),
(1208, 87, 'Deduction', 'PhilHealth (ER)', 187.50),
(1209, 87, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1210, 87, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1211, 87, 'Deduction', 'Late/Undertime', 11.72),
(1212, 87, 'Deduction', 'Withholding Tax', 894.66),
(1213, 88, 'Allowance', 'Rice Subsidy', 2500.00),
(1214, 88, 'Allowance', 'Meal Allowance', 3000.00),
(1215, 88, 'Allowance', 'Laundry Allowance', 400.00),
(1216, 88, 'Allowance', 'Travel Allowance', 7000.00),
(1217, 88, 'Allowance', 'Communication Allowance', 2000.00),
(1218, 88, 'Deduction', 'SSS Regular (EE)', 500.00),
(1219, 88, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1220, 88, 'Deduction', 'SSS WISP (EE)', 375.00),
(1221, 88, 'Deduction', 'SSS WISP (ER)', 750.00),
(1222, 88, 'Deduction', 'PhilHealth (EE)', 662.50),
(1223, 88, 'Deduction', 'PhilHealth (ER)', 662.50),
(1224, 88, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1225, 88, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1226, 88, 'Deduction', 'Withholding Tax', 4066.70),
(1227, 89, 'Allowance', 'Rice Subsidy', 2500.00),
(1228, 89, 'Allowance', 'Meal Allowance', 2500.00),
(1229, 89, 'Allowance', 'Laundry Allowance', 400.00),
(1230, 89, 'Allowance', 'Travel Allowance', 5000.00),
(1231, 89, 'Allowance', 'Communication Allowance', 1500.00),
(1232, 89, 'Allowance', 'Overtime Pay', 1875.00),
(1233, 89, 'Deduction', 'SSS Regular (EE)', 500.00),
(1234, 89, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1235, 89, 'Deduction', 'SSS WISP (EE)', 375.00),
(1236, 89, 'Deduction', 'SSS WISP (ER)', 750.00),
(1237, 89, 'Deduction', 'PhilHealth (EE)', 500.00),
(1238, 89, 'Deduction', 'PhilHealth (ER)', 500.00),
(1239, 89, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1240, 89, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1241, 89, 'Deduction', 'Late/Undertime', 93.75),
(1242, 89, 'Deduction', 'Withholding Tax', 2855.45),
(1243, 90, 'Allowance', 'Rice Subsidy', 2500.00),
(1244, 90, 'Allowance', 'Meal Allowance', 2000.00),
(1245, 90, 'Allowance', 'Laundry Allowance', 400.00),
(1246, 90, 'Allowance', 'Travel Allowance', 3500.00),
(1247, 90, 'Allowance', 'Communication Allowance', 1200.00),
(1248, 90, 'Deduction', 'SSS Regular (EE)', 500.00),
(1249, 90, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1250, 90, 'Deduction', 'SSS WISP (EE)', 25.00),
(1251, 90, 'Deduction', 'SSS WISP (ER)', 50.00),
(1252, 90, 'Deduction', 'PhilHealth (EE)', 262.50),
(1253, 90, 'Deduction', 'PhilHealth (ER)', 262.50),
(1254, 90, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1255, 90, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1256, 90, 'Deduction', 'Withholding Tax', 2204.88),
(1257, 91, 'Allowance', 'Rice Subsidy', 2500.00),
(1258, 91, 'Allowance', 'Meal Allowance', 2500.00),
(1259, 91, 'Allowance', 'Laundry Allowance', 400.00),
(1260, 91, 'Allowance', 'Travel Allowance', 5000.00),
(1261, 91, 'Allowance', 'Communication Allowance', 1500.00),
(1262, 91, 'Allowance', 'Overtime Pay', 312.50),
(1263, 91, 'Deduction', 'SSS Regular (EE)', 500.00),
(1264, 91, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1265, 91, 'Deduction', 'SSS WISP (EE)', 375.00),
(1266, 91, 'Deduction', 'SSS WISP (ER)', 750.00),
(1267, 91, 'Deduction', 'PhilHealth (EE)', 500.00),
(1268, 91, 'Deduction', 'PhilHealth (ER)', 500.00),
(1269, 91, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1270, 91, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1271, 91, 'Deduction', 'Late/Undertime', 20.84),
(1272, 91, 'Deduction', 'Withholding Tax', 2557.53),
(1273, 92, 'Allowance', 'Rice Subsidy', 2500.00),
(1274, 92, 'Allowance', 'Meal Allowance', 3500.00),
(1275, 92, 'Allowance', 'Laundry Allowance', 400.00),
(1276, 92, 'Allowance', 'Travel Allowance', 10000.00),
(1277, 92, 'Allowance', 'Communication Allowance', 3000.00),
(1278, 92, 'Allowance', 'Overtime Pay', 3125.00),
(1279, 92, 'Deduction', 'SSS Regular (EE)', 500.00),
(1280, 92, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1281, 92, 'Deduction', 'SSS WISP (EE)', 375.00),
(1282, 92, 'Deduction', 'SSS WISP (ER)', 750.00),
(1283, 92, 'Deduction', 'PhilHealth (EE)', 1000.00),
(1284, 92, 'Deduction', 'PhilHealth (ER)', 1000.00),
(1285, 92, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1286, 92, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1287, 92, 'Deduction', 'Late/Undertime', 125.00),
(1288, 92, 'Deduction', 'Withholding Tax', 8618.78),
(1289, 93, 'Allowance', 'Rice Subsidy', 2500.00),
(1290, 93, 'Allowance', 'Meal Allowance', 3500.00),
(1291, 93, 'Allowance', 'Laundry Allowance', 400.00),
(1292, 93, 'Allowance', 'Travel Allowance', 10000.00),
(1293, 93, 'Allowance', 'Communication Allowance', 3000.00),
(1294, 93, 'Allowance', 'Overtime Pay', 1562.50),
(1295, 93, 'Deduction', 'SSS Regular (EE)', 500.00),
(1296, 93, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1297, 93, 'Deduction', 'SSS WISP (EE)', 375.00),
(1298, 93, 'Deduction', 'SSS WISP (ER)', 750.00),
(1299, 93, 'Deduction', 'PhilHealth (EE)', 1000.00),
(1300, 93, 'Deduction', 'PhilHealth (ER)', 1000.00),
(1301, 93, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1302, 93, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1303, 93, 'Deduction', 'Withholding Tax', 8259.40),
(1304, 94, 'Allowance', 'Rice Subsidy', 2500.00),
(1305, 94, 'Allowance', 'Meal Allowance', 1500.00),
(1306, 94, 'Allowance', 'Laundry Allowance', 400.00),
(1307, 94, 'Allowance', 'Travel Allowance', 2500.00),
(1308, 94, 'Allowance', 'Communication Allowance', 800.00),
(1309, 94, 'Deduction', 'SSS Regular (EE)', 500.00),
(1310, 94, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1311, 94, 'Deduction', 'SSS WISP (EE)', 25.00),
(1312, 94, 'Deduction', 'SSS WISP (ER)', 50.00),
(1313, 94, 'Deduction', 'PhilHealth (EE)', 262.50),
(1314, 94, 'Deduction', 'PhilHealth (ER)', 262.50),
(1315, 94, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1316, 94, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1317, 94, 'Deduction', 'Late/Undertime', 131.25),
(1318, 94, 'Deduction', 'Withholding Tax', 1988.63),
(1319, 95, 'Allowance', 'Rice Subsidy', 2500.00),
(1320, 95, 'Allowance', 'Meal Allowance', 1000.00),
(1321, 95, 'Allowance', 'Laundry Allowance', 400.00),
(1322, 95, 'Allowance', 'Travel Allowance', 1500.00),
(1323, 95, 'Allowance', 'Communication Allowance', 500.00),
(1324, 95, 'Allowance', 'Overtime Pay', 468.75),
(1325, 95, 'Deduction', 'SSS Regular (EE)', 375.00),
(1326, 95, 'Deduction', 'SSS Regular (ER)', 750.00),
(1327, 95, 'Deduction', 'PhilHealth (EE)', 187.50),
(1328, 95, 'Deduction', 'PhilHealth (ER)', 187.50),
(1329, 95, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1330, 95, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1331, 95, 'Deduction', 'Late/Undertime', 11.72),
(1332, 95, 'Deduction', 'Withholding Tax', 894.66),
(1333, 96, 'Allowance', 'Rice Subsidy', 2500.00),
(1334, 96, 'Allowance', 'Meal Allowance', 3000.00),
(1335, 96, 'Allowance', 'Laundry Allowance', 400.00),
(1336, 96, 'Allowance', 'Travel Allowance', 7000.00),
(1337, 96, 'Allowance', 'Communication Allowance', 2000.00),
(1338, 96, 'Deduction', 'SSS Regular (EE)', 500.00),
(1339, 96, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1340, 96, 'Deduction', 'SSS WISP (EE)', 375.00),
(1341, 96, 'Deduction', 'SSS WISP (ER)', 750.00),
(1342, 96, 'Deduction', 'PhilHealth (EE)', 662.50),
(1343, 96, 'Deduction', 'PhilHealth (ER)', 662.50),
(1344, 96, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1345, 96, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1346, 96, 'Deduction', 'Withholding Tax', 4066.70),
(1347, 97, 'Allowance', 'Rice Subsidy', 2500.00),
(1348, 97, 'Allowance', 'Meal Allowance', 2500.00),
(1349, 97, 'Allowance', 'Laundry Allowance', 400.00),
(1350, 97, 'Allowance', 'Travel Allowance', 5000.00),
(1351, 97, 'Allowance', 'Communication Allowance', 1500.00),
(1352, 97, 'Allowance', 'Overtime Pay', 1875.00),
(1353, 97, 'Deduction', 'SSS Regular (EE)', 500.00),
(1354, 97, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1355, 97, 'Deduction', 'SSS WISP (EE)', 375.00),
(1356, 97, 'Deduction', 'SSS WISP (ER)', 750.00),
(1357, 97, 'Deduction', 'PhilHealth (EE)', 500.00),
(1358, 97, 'Deduction', 'PhilHealth (ER)', 500.00),
(1359, 97, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1360, 97, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1361, 97, 'Deduction', 'Late/Undertime', 93.75),
(1362, 97, 'Deduction', 'Withholding Tax', 2855.45),
(1363, 98, 'Allowance', 'Rice Subsidy', 2500.00),
(1364, 98, 'Allowance', 'Meal Allowance', 2000.00),
(1365, 98, 'Allowance', 'Laundry Allowance', 400.00),
(1366, 98, 'Allowance', 'Travel Allowance', 3500.00),
(1367, 98, 'Allowance', 'Communication Allowance', 1200.00),
(1368, 98, 'Deduction', 'SSS Regular (EE)', 500.00),
(1369, 98, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1370, 98, 'Deduction', 'SSS WISP (EE)', 25.00),
(1371, 98, 'Deduction', 'SSS WISP (ER)', 50.00),
(1372, 98, 'Deduction', 'PhilHealth (EE)', 262.50),
(1373, 98, 'Deduction', 'PhilHealth (ER)', 262.50),
(1374, 98, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1375, 98, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1376, 98, 'Deduction', 'Withholding Tax', 2204.88),
(1377, 99, 'Allowance', 'Rice Subsidy', 2500.00),
(1378, 99, 'Allowance', 'Meal Allowance', 2500.00),
(1379, 99, 'Allowance', 'Laundry Allowance', 400.00),
(1380, 99, 'Allowance', 'Travel Allowance', 5000.00),
(1381, 99, 'Allowance', 'Communication Allowance', 1500.00),
(1382, 99, 'Allowance', 'Overtime Pay', 312.50),
(1383, 99, 'Deduction', 'SSS Regular (EE)', 500.00),
(1384, 99, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1385, 99, 'Deduction', 'SSS WISP (EE)', 375.00),
(1386, 99, 'Deduction', 'SSS WISP (ER)', 750.00),
(1387, 99, 'Deduction', 'PhilHealth (EE)', 500.00),
(1388, 99, 'Deduction', 'PhilHealth (ER)', 500.00),
(1389, 99, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1390, 99, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1391, 99, 'Deduction', 'Late/Undertime', 20.84),
(1392, 99, 'Deduction', 'Withholding Tax', 2557.53),
(1393, 100, 'Allowance', 'Rice Subsidy', 2500.00),
(1394, 100, 'Allowance', 'Meal Allowance', 2000.00),
(1395, 100, 'Allowance', 'Laundry Allowance', 400.00),
(1396, 100, 'Allowance', 'Travel Allowance', 3500.00),
(1397, 100, 'Allowance', 'Communication Allowance', 1200.00),
(1398, 100, 'Deduction', 'SSS Regular (EE)', 500.00),
(1399, 100, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1400, 100, 'Deduction', 'SSS WISP (EE)', 225.00),
(1401, 100, 'Deduction', 'SSS WISP (ER)', 450.00),
(1402, 100, 'Deduction', 'PhilHealth (EE)', 362.50),
(1403, 100, 'Deduction', 'PhilHealth (ER)', 362.50),
(1404, 100, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1405, 100, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1406, 100, 'Deduction', 'Withholding Tax', 1226.70),
(1407, 101, 'Allowance', 'Rice Subsidy', 2500.00),
(1408, 101, 'Allowance', 'Meal Allowance', 3000.00),
(1409, 101, 'Allowance', 'Laundry Allowance', 400.00),
(1410, 101, 'Allowance', 'Travel Allowance', 7000.00),
(1411, 101, 'Allowance', 'Communication Allowance', 2000.00),
(1412, 101, 'Deduction', 'SSS Regular (EE)', 500.00),
(1413, 101, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1414, 101, 'Deduction', 'SSS WISP (EE)', 375.00),
(1415, 101, 'Deduction', 'SSS WISP (ER)', 750.00),
(1416, 101, 'Deduction', 'PhilHealth (EE)', 675.00),
(1417, 101, 'Deduction', 'PhilHealth (ER)', 675.00),
(1418, 101, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1419, 101, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1420, 101, 'Deduction', 'Withholding Tax', 4164.20),
(1421, 102, 'Allowance', 'Rice Subsidy', 2500.00),
(1422, 102, 'Allowance', 'Meal Allowance', 2000.00),
(1423, 102, 'Allowance', 'Laundry Allowance', 400.00),
(1424, 102, 'Allowance', 'Travel Allowance', 3500.00),
(1425, 102, 'Allowance', 'Communication Allowance', 1200.00),
(1426, 102, 'Deduction', 'SSS Regular (EE)', 500.00),
(1427, 102, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1428, 102, 'Deduction', 'SSS WISP (EE)', 225.00),
(1429, 102, 'Deduction', 'SSS WISP (ER)', 450.00),
(1430, 102, 'Deduction', 'PhilHealth (EE)', 362.50),
(1431, 102, 'Deduction', 'PhilHealth (ER)', 362.50),
(1432, 102, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1433, 102, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1434, 102, 'Deduction', 'Withholding Tax', 1226.70),
(1435, 103, 'Allowance', 'Rice Subsidy', 2500.00),
(1436, 103, 'Allowance', 'Meal Allowance', 3500.00),
(1437, 103, 'Allowance', 'Laundry Allowance', 400.00),
(1438, 103, 'Allowance', 'Travel Allowance', 10000.00),
(1439, 103, 'Allowance', 'Communication Allowance', 3000.00),
(1440, 103, 'Allowance', 'Overtime Pay', 3125.00),
(1441, 103, 'Deduction', 'SSS Regular (EE)', 500.00),
(1442, 103, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1443, 103, 'Deduction', 'SSS WISP (EE)', 375.00),
(1444, 103, 'Deduction', 'SSS WISP (ER)', 750.00),
(1445, 103, 'Deduction', 'PhilHealth (EE)', 1000.00),
(1446, 103, 'Deduction', 'PhilHealth (ER)', 1000.00),
(1447, 103, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1448, 103, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1449, 103, 'Deduction', 'Late/Undertime', 125.00),
(1450, 103, 'Deduction', 'Withholding Tax', 8618.78),
(1451, 104, 'Allowance', 'Rice Subsidy', 2500.00),
(1452, 104, 'Allowance', 'Meal Allowance', 3500.00),
(1453, 104, 'Allowance', 'Laundry Allowance', 400.00),
(1454, 104, 'Allowance', 'Travel Allowance', 10000.00),
(1455, 104, 'Allowance', 'Communication Allowance', 3000.00),
(1456, 104, 'Allowance', 'Overtime Pay', 1562.50),
(1457, 104, 'Deduction', 'SSS Regular (EE)', 500.00),
(1458, 104, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1459, 104, 'Deduction', 'SSS WISP (EE)', 375.00),
(1460, 104, 'Deduction', 'SSS WISP (ER)', 750.00),
(1461, 104, 'Deduction', 'PhilHealth (EE)', 1000.00),
(1462, 104, 'Deduction', 'PhilHealth (ER)', 1000.00),
(1463, 104, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1464, 104, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1465, 104, 'Deduction', 'Withholding Tax', 8259.40),
(1466, 105, 'Allowance', 'Rice Subsidy', 2500.00),
(1467, 105, 'Allowance', 'Meal Allowance', 1500.00),
(1468, 105, 'Allowance', 'Laundry Allowance', 400.00),
(1469, 105, 'Allowance', 'Travel Allowance', 2500.00),
(1470, 105, 'Allowance', 'Communication Allowance', 800.00),
(1471, 105, 'Deduction', 'SSS Regular (EE)', 500.00),
(1472, 105, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1473, 105, 'Deduction', 'SSS WISP (EE)', 25.00),
(1474, 105, 'Deduction', 'SSS WISP (ER)', 50.00),
(1475, 105, 'Deduction', 'PhilHealth (EE)', 262.50),
(1476, 105, 'Deduction', 'PhilHealth (ER)', 262.50),
(1477, 105, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1478, 105, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1479, 105, 'Deduction', 'Late/Undertime', 131.25),
(1480, 105, 'Deduction', 'Withholding Tax', 1988.63),
(1481, 106, 'Allowance', 'Rice Subsidy', 2500.00),
(1482, 106, 'Allowance', 'Meal Allowance', 1000.00),
(1483, 106, 'Allowance', 'Laundry Allowance', 400.00),
(1484, 106, 'Allowance', 'Travel Allowance', 1500.00),
(1485, 106, 'Allowance', 'Communication Allowance', 500.00),
(1486, 106, 'Allowance', 'Overtime Pay', 468.75),
(1487, 106, 'Deduction', 'SSS Regular (EE)', 375.00),
(1488, 106, 'Deduction', 'SSS Regular (ER)', 750.00),
(1489, 106, 'Deduction', 'PhilHealth (EE)', 187.50),
(1490, 106, 'Deduction', 'PhilHealth (ER)', 187.50),
(1491, 106, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1492, 106, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1493, 106, 'Deduction', 'Late/Undertime', 11.72),
(1494, 106, 'Deduction', 'Withholding Tax', 894.66),
(1495, 107, 'Allowance', 'Rice Subsidy', 2500.00),
(1496, 107, 'Allowance', 'Meal Allowance', 3000.00),
(1497, 107, 'Allowance', 'Laundry Allowance', 400.00),
(1498, 107, 'Allowance', 'Travel Allowance', 7000.00),
(1499, 107, 'Allowance', 'Communication Allowance', 2000.00),
(1500, 107, 'Deduction', 'SSS Regular (EE)', 500.00),
(1501, 107, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1502, 107, 'Deduction', 'SSS WISP (EE)', 375.00),
(1503, 107, 'Deduction', 'SSS WISP (ER)', 750.00),
(1504, 107, 'Deduction', 'PhilHealth (EE)', 662.50),
(1505, 107, 'Deduction', 'PhilHealth (ER)', 662.50),
(1506, 107, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1507, 107, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1508, 107, 'Deduction', 'Withholding Tax', 4066.70),
(1509, 108, 'Allowance', 'Rice Subsidy', 2500.00),
(1510, 108, 'Allowance', 'Meal Allowance', 2500.00),
(1511, 108, 'Allowance', 'Laundry Allowance', 400.00),
(1512, 108, 'Allowance', 'Travel Allowance', 5000.00),
(1513, 108, 'Allowance', 'Communication Allowance', 1500.00),
(1514, 108, 'Allowance', 'Overtime Pay', 1875.00),
(1515, 108, 'Deduction', 'SSS Regular (EE)', 500.00),
(1516, 108, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1517, 108, 'Deduction', 'SSS WISP (EE)', 375.00),
(1518, 108, 'Deduction', 'SSS WISP (ER)', 750.00),
(1519, 108, 'Deduction', 'PhilHealth (EE)', 500.00),
(1520, 108, 'Deduction', 'PhilHealth (ER)', 500.00),
(1521, 108, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1522, 108, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1523, 108, 'Deduction', 'Late/Undertime', 93.75),
(1524, 108, 'Deduction', 'Withholding Tax', 2855.45),
(1525, 109, 'Allowance', 'Rice Subsidy', 2500.00),
(1526, 109, 'Allowance', 'Meal Allowance', 2000.00),
(1527, 109, 'Allowance', 'Laundry Allowance', 400.00),
(1528, 109, 'Allowance', 'Travel Allowance', 3500.00),
(1529, 109, 'Allowance', 'Communication Allowance', 1200.00),
(1530, 109, 'Deduction', 'SSS Regular (EE)', 500.00),
(1531, 109, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1532, 109, 'Deduction', 'SSS WISP (EE)', 25.00),
(1533, 109, 'Deduction', 'SSS WISP (ER)', 50.00),
(1534, 109, 'Deduction', 'PhilHealth (EE)', 262.50),
(1535, 109, 'Deduction', 'PhilHealth (ER)', 262.50),
(1536, 109, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1537, 109, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1538, 109, 'Deduction', 'Withholding Tax', 2204.88),
(1539, 110, 'Allowance', 'Rice Subsidy', 2500.00),
(1540, 110, 'Allowance', 'Meal Allowance', 2500.00),
(1541, 110, 'Allowance', 'Laundry Allowance', 400.00),
(1542, 110, 'Allowance', 'Travel Allowance', 5000.00),
(1543, 110, 'Allowance', 'Communication Allowance', 1500.00),
(1544, 110, 'Allowance', 'Overtime Pay', 312.50),
(1545, 110, 'Deduction', 'SSS Regular (EE)', 500.00),
(1546, 110, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1547, 110, 'Deduction', 'SSS WISP (EE)', 375.00),
(1548, 110, 'Deduction', 'SSS WISP (ER)', 750.00),
(1549, 110, 'Deduction', 'PhilHealth (EE)', 500.00),
(1550, 110, 'Deduction', 'PhilHealth (ER)', 500.00),
(1551, 110, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1552, 110, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1553, 110, 'Deduction', 'Late/Undertime', 20.84),
(1554, 110, 'Deduction', 'Withholding Tax', 2557.53),
(1555, 111, 'Allowance', 'Rice Subsidy', 2500.00),
(1556, 111, 'Allowance', 'Meal Allowance', 2000.00),
(1557, 111, 'Allowance', 'Laundry Allowance', 400.00),
(1558, 111, 'Allowance', 'Travel Allowance', 3500.00),
(1559, 111, 'Allowance', 'Communication Allowance', 1200.00),
(1560, 111, 'Deduction', 'SSS Regular (EE)', 500.00),
(1561, 111, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1562, 111, 'Deduction', 'SSS WISP (EE)', 225.00),
(1563, 111, 'Deduction', 'SSS WISP (ER)', 450.00),
(1564, 111, 'Deduction', 'PhilHealth (EE)', 362.50),
(1565, 111, 'Deduction', 'PhilHealth (ER)', 362.50),
(1566, 111, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1567, 111, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1568, 111, 'Deduction', 'Withholding Tax', 1226.70),
(1569, 112, 'Allowance', 'Rice Subsidy', 2500.00),
(1570, 112, 'Allowance', 'Meal Allowance', 3000.00),
(1571, 112, 'Allowance', 'Laundry Allowance', 400.00),
(1572, 112, 'Allowance', 'Travel Allowance', 7000.00),
(1573, 112, 'Allowance', 'Communication Allowance', 2000.00),
(1574, 112, 'Deduction', 'SSS Regular (EE)', 500.00),
(1575, 112, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1576, 112, 'Deduction', 'SSS WISP (EE)', 375.00),
(1577, 112, 'Deduction', 'SSS WISP (ER)', 750.00),
(1578, 112, 'Deduction', 'PhilHealth (EE)', 675.00),
(1579, 112, 'Deduction', 'PhilHealth (ER)', 675.00),
(1580, 112, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1581, 112, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1582, 112, 'Deduction', 'Withholding Tax', 4164.20),
(1583, 113, 'Allowance', 'Rice Subsidy', 2500.00),
(1584, 113, 'Allowance', 'Meal Allowance', 2000.00),
(1585, 113, 'Allowance', 'Laundry Allowance', 400.00),
(1586, 113, 'Allowance', 'Travel Allowance', 3500.00),
(1587, 113, 'Allowance', 'Communication Allowance', 1200.00),
(1588, 113, 'Deduction', 'SSS Regular (EE)', 500.00),
(1589, 113, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1590, 113, 'Deduction', 'SSS WISP (EE)', 225.00),
(1591, 113, 'Deduction', 'SSS WISP (ER)', 450.00),
(1592, 113, 'Deduction', 'PhilHealth (EE)', 362.50),
(1593, 113, 'Deduction', 'PhilHealth (ER)', 362.50),
(1594, 113, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1595, 113, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1596, 113, 'Deduction', 'Withholding Tax', 1226.70),
(1597, 114, 'Allowance', 'Rice Subsidy', 2500.00),
(1598, 114, 'Allowance', 'Meal Allowance', 3500.00),
(1599, 114, 'Allowance', 'Laundry Allowance', 400.00),
(1600, 114, 'Allowance', 'Travel Allowance', 10000.00),
(1601, 114, 'Allowance', 'Communication Allowance', 3000.00),
(1602, 114, 'Allowance', 'Overtime Pay', 3125.00),
(1603, 114, 'Deduction', 'SSS Regular (EE)', 500.00),
(1604, 114, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1605, 114, 'Deduction', 'SSS WISP (EE)', 375.00),
(1606, 114, 'Deduction', 'SSS WISP (ER)', 750.00),
(1607, 114, 'Deduction', 'PhilHealth (EE)', 1000.00),
(1608, 114, 'Deduction', 'PhilHealth (ER)', 1000.00),
(1609, 114, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1610, 114, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1611, 114, 'Deduction', 'Late/Undertime', 125.00),
(1612, 114, 'Deduction', 'Withholding Tax', 8618.78),
(1613, 115, 'Allowance', 'Rice Subsidy', 2500.00),
(1614, 115, 'Allowance', 'Meal Allowance', 3500.00),
(1615, 115, 'Allowance', 'Laundry Allowance', 400.00),
(1616, 115, 'Allowance', 'Travel Allowance', 10000.00),
(1617, 115, 'Allowance', 'Communication Allowance', 3000.00),
(1618, 115, 'Allowance', 'Overtime Pay', 1562.50),
(1619, 115, 'Deduction', 'SSS Regular (EE)', 500.00),
(1620, 115, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1621, 115, 'Deduction', 'SSS WISP (EE)', 375.00),
(1622, 115, 'Deduction', 'SSS WISP (ER)', 750.00),
(1623, 115, 'Deduction', 'PhilHealth (EE)', 1000.00),
(1624, 115, 'Deduction', 'PhilHealth (ER)', 1000.00),
(1625, 115, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1626, 115, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1627, 115, 'Deduction', 'Withholding Tax', 8259.40),
(1628, 116, 'Allowance', 'Rice Subsidy', 2500.00),
(1629, 116, 'Allowance', 'Meal Allowance', 1500.00),
(1630, 116, 'Allowance', 'Laundry Allowance', 400.00),
(1631, 116, 'Allowance', 'Travel Allowance', 2500.00),
(1632, 116, 'Allowance', 'Communication Allowance', 800.00),
(1633, 116, 'Deduction', 'SSS Regular (EE)', 500.00),
(1634, 116, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1635, 116, 'Deduction', 'SSS WISP (EE)', 25.00),
(1636, 116, 'Deduction', 'SSS WISP (ER)', 50.00),
(1637, 116, 'Deduction', 'PhilHealth (EE)', 262.50),
(1638, 116, 'Deduction', 'PhilHealth (ER)', 262.50),
(1639, 116, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1640, 116, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1641, 116, 'Deduction', 'Late/Undertime', 131.25),
(1642, 116, 'Deduction', 'Withholding Tax', 1988.63),
(1643, 117, 'Allowance', 'Rice Subsidy', 2500.00),
(1644, 117, 'Allowance', 'Meal Allowance', 1000.00),
(1645, 117, 'Allowance', 'Laundry Allowance', 400.00),
(1646, 117, 'Allowance', 'Travel Allowance', 1500.00),
(1647, 117, 'Allowance', 'Communication Allowance', 500.00),
(1648, 117, 'Allowance', 'Overtime Pay', 468.75),
(1649, 117, 'Deduction', 'SSS Regular (EE)', 375.00),
(1650, 117, 'Deduction', 'SSS Regular (ER)', 750.00),
(1651, 117, 'Deduction', 'PhilHealth (EE)', 187.50),
(1652, 117, 'Deduction', 'PhilHealth (ER)', 187.50),
(1653, 117, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1654, 117, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1655, 117, 'Deduction', 'Late/Undertime', 11.72),
(1656, 117, 'Deduction', 'Withholding Tax', 894.66),
(1657, 118, 'Allowance', 'Rice Subsidy', 2500.00),
(1658, 118, 'Allowance', 'Meal Allowance', 3000.00),
(1659, 118, 'Allowance', 'Laundry Allowance', 400.00),
(1660, 118, 'Allowance', 'Travel Allowance', 7000.00),
(1661, 118, 'Allowance', 'Communication Allowance', 2000.00),
(1662, 118, 'Deduction', 'SSS Regular (EE)', 500.00),
(1663, 118, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1664, 118, 'Deduction', 'SSS WISP (EE)', 375.00),
(1665, 118, 'Deduction', 'SSS WISP (ER)', 750.00),
(1666, 118, 'Deduction', 'PhilHealth (EE)', 662.50),
(1667, 118, 'Deduction', 'PhilHealth (ER)', 662.50),
(1668, 118, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1669, 118, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1670, 118, 'Deduction', 'Withholding Tax', 4066.70),
(1671, 119, 'Allowance', 'Rice Subsidy', 2500.00),
(1672, 119, 'Allowance', 'Meal Allowance', 2500.00),
(1673, 119, 'Allowance', 'Laundry Allowance', 400.00),
(1674, 119, 'Allowance', 'Travel Allowance', 5000.00),
(1675, 119, 'Allowance', 'Communication Allowance', 1500.00),
(1676, 119, 'Allowance', 'Overtime Pay', 1875.00),
(1677, 119, 'Deduction', 'SSS Regular (EE)', 500.00),
(1678, 119, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1679, 119, 'Deduction', 'SSS WISP (EE)', 375.00),
(1680, 119, 'Deduction', 'SSS WISP (ER)', 750.00),
(1681, 119, 'Deduction', 'PhilHealth (EE)', 500.00),
(1682, 119, 'Deduction', 'PhilHealth (ER)', 500.00),
(1683, 119, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1684, 119, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1685, 119, 'Deduction', 'Late/Undertime', 93.75),
(1686, 119, 'Deduction', 'Withholding Tax', 2855.45),
(1687, 120, 'Allowance', 'Rice Subsidy', 2500.00),
(1688, 120, 'Allowance', 'Meal Allowance', 2000.00),
(1689, 120, 'Allowance', 'Laundry Allowance', 400.00),
(1690, 120, 'Allowance', 'Travel Allowance', 3500.00),
(1691, 120, 'Allowance', 'Communication Allowance', 1200.00),
(1692, 120, 'Deduction', 'SSS Regular (EE)', 500.00),
(1693, 120, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1694, 120, 'Deduction', 'SSS WISP (EE)', 25.00),
(1695, 120, 'Deduction', 'SSS WISP (ER)', 50.00),
(1696, 120, 'Deduction', 'PhilHealth (EE)', 262.50),
(1697, 120, 'Deduction', 'PhilHealth (ER)', 262.50),
(1698, 120, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1699, 120, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1700, 120, 'Deduction', 'Withholding Tax', 2204.88),
(1701, 121, 'Allowance', 'Rice Subsidy', 2500.00),
(1702, 121, 'Allowance', 'Meal Allowance', 2500.00),
(1703, 121, 'Allowance', 'Laundry Allowance', 400.00),
(1704, 121, 'Allowance', 'Travel Allowance', 5000.00),
(1705, 121, 'Allowance', 'Communication Allowance', 1500.00),
(1706, 121, 'Allowance', 'Overtime Pay', 312.50),
(1707, 121, 'Deduction', 'SSS Regular (EE)', 500.00),
(1708, 121, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1709, 121, 'Deduction', 'SSS WISP (EE)', 375.00),
(1710, 121, 'Deduction', 'SSS WISP (ER)', 750.00),
(1711, 121, 'Deduction', 'PhilHealth (EE)', 500.00),
(1712, 121, 'Deduction', 'PhilHealth (ER)', 500.00),
(1713, 121, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1714, 121, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1715, 121, 'Deduction', 'Late/Undertime', 20.84),
(1716, 121, 'Deduction', 'Withholding Tax', 2557.53),
(1717, 122, 'Allowance', 'Rice Subsidy', 2500.00),
(1718, 122, 'Allowance', 'Meal Allowance', 2000.00),
(1719, 122, 'Allowance', 'Laundry Allowance', 400.00),
(1720, 122, 'Allowance', 'Travel Allowance', 3500.00),
(1721, 122, 'Allowance', 'Communication Allowance', 1200.00),
(1722, 122, 'Deduction', 'SSS Regular (EE)', 500.00),
(1723, 122, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1724, 122, 'Deduction', 'SSS WISP (EE)', 225.00),
(1725, 122, 'Deduction', 'SSS WISP (ER)', 450.00),
(1726, 122, 'Deduction', 'PhilHealth (EE)', 362.50),
(1727, 122, 'Deduction', 'PhilHealth (ER)', 362.50),
(1728, 122, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1729, 122, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1730, 122, 'Deduction', 'Withholding Tax', 1226.70),
(1731, 123, 'Allowance', 'Rice Subsidy', 2500.00),
(1732, 123, 'Allowance', 'Meal Allowance', 3000.00),
(1733, 123, 'Allowance', 'Laundry Allowance', 400.00),
(1734, 123, 'Allowance', 'Travel Allowance', 7000.00),
(1735, 123, 'Allowance', 'Communication Allowance', 2000.00),
(1736, 123, 'Deduction', 'SSS Regular (EE)', 500.00),
(1737, 123, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1738, 123, 'Deduction', 'SSS WISP (EE)', 375.00),
(1739, 123, 'Deduction', 'SSS WISP (ER)', 750.00),
(1740, 123, 'Deduction', 'PhilHealth (EE)', 675.00),
(1741, 123, 'Deduction', 'PhilHealth (ER)', 675.00),
(1742, 123, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1743, 123, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1744, 123, 'Deduction', 'Withholding Tax', 4164.20),
(1745, 124, 'Allowance', 'Rice Subsidy', 2500.00),
(1746, 124, 'Allowance', 'Meal Allowance', 2000.00),
(1747, 124, 'Allowance', 'Laundry Allowance', 400.00),
(1748, 124, 'Allowance', 'Travel Allowance', 3500.00),
(1749, 124, 'Allowance', 'Communication Allowance', 1200.00),
(1750, 124, 'Deduction', 'SSS Regular (EE)', 500.00),
(1751, 124, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1752, 124, 'Deduction', 'SSS WISP (EE)', 225.00),
(1753, 124, 'Deduction', 'SSS WISP (ER)', 450.00),
(1754, 124, 'Deduction', 'PhilHealth (EE)', 362.50),
(1755, 124, 'Deduction', 'PhilHealth (ER)', 362.50),
(1756, 124, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1757, 124, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1758, 124, 'Deduction', 'Withholding Tax', 1226.70),
(1759, 125, 'Allowance', 'Rice Subsidy', 2500.00),
(1760, 125, 'Allowance', 'Meal Allowance', 3500.00),
(1761, 125, 'Allowance', 'Laundry Allowance', 400.00),
(1762, 125, 'Allowance', 'Travel Allowance', 10000.00),
(1763, 125, 'Allowance', 'Communication Allowance', 3000.00),
(1764, 125, 'Allowance', 'Overtime Pay', 3125.00),
(1765, 125, 'Deduction', 'SSS Regular (EE)', 500.00),
(1766, 125, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1767, 125, 'Deduction', 'SSS WISP (EE)', 375.00),
(1768, 125, 'Deduction', 'SSS WISP (ER)', 750.00),
(1769, 125, 'Deduction', 'PhilHealth (EE)', 1000.00),
(1770, 125, 'Deduction', 'PhilHealth (ER)', 1000.00),
(1771, 125, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1772, 125, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1773, 125, 'Deduction', 'Late/Undertime', 125.00),
(1774, 125, 'Deduction', 'Withholding Tax', 8618.78),
(1775, 126, 'Allowance', 'Rice Subsidy', 2500.00),
(1776, 126, 'Allowance', 'Meal Allowance', 3500.00),
(1777, 126, 'Allowance', 'Laundry Allowance', 400.00),
(1778, 126, 'Allowance', 'Travel Allowance', 10000.00),
(1779, 126, 'Allowance', 'Communication Allowance', 3000.00),
(1780, 126, 'Allowance', 'Overtime Pay', 1562.50),
(1781, 126, 'Deduction', 'SSS Regular (EE)', 500.00),
(1782, 126, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1783, 126, 'Deduction', 'SSS WISP (EE)', 375.00),
(1784, 126, 'Deduction', 'SSS WISP (ER)', 750.00),
(1785, 126, 'Deduction', 'PhilHealth (EE)', 1000.00),
(1786, 126, 'Deduction', 'PhilHealth (ER)', 1000.00),
(1787, 126, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1788, 126, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1789, 126, 'Deduction', 'Withholding Tax', 8259.40),
(1790, 127, 'Allowance', 'Rice Subsidy', 2500.00),
(1791, 127, 'Allowance', 'Meal Allowance', 1500.00),
(1792, 127, 'Allowance', 'Laundry Allowance', 400.00),
(1793, 127, 'Allowance', 'Travel Allowance', 2500.00),
(1794, 127, 'Allowance', 'Communication Allowance', 800.00),
(1795, 127, 'Deduction', 'SSS Regular (EE)', 500.00),
(1796, 127, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1797, 127, 'Deduction', 'SSS WISP (EE)', 25.00),
(1798, 127, 'Deduction', 'SSS WISP (ER)', 50.00),
(1799, 127, 'Deduction', 'PhilHealth (EE)', 262.50),
(1800, 127, 'Deduction', 'PhilHealth (ER)', 262.50),
(1801, 127, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1802, 127, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1803, 127, 'Deduction', 'Late/Undertime', 131.25),
(1804, 127, 'Deduction', 'Withholding Tax', 1988.63),
(1805, 128, 'Allowance', 'Rice Subsidy', 2500.00),
(1806, 128, 'Allowance', 'Meal Allowance', 1000.00),
(1807, 128, 'Allowance', 'Laundry Allowance', 400.00),
(1808, 128, 'Allowance', 'Travel Allowance', 1500.00),
(1809, 128, 'Allowance', 'Communication Allowance', 500.00),
(1810, 128, 'Allowance', 'Overtime Pay', 468.75),
(1811, 128, 'Deduction', 'SSS Regular (EE)', 375.00),
(1812, 128, 'Deduction', 'SSS Regular (ER)', 750.00),
(1813, 128, 'Deduction', 'PhilHealth (EE)', 187.50),
(1814, 128, 'Deduction', 'PhilHealth (ER)', 187.50),
(1815, 128, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1816, 128, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1817, 128, 'Deduction', 'Late/Undertime', 11.72),
(1818, 128, 'Deduction', 'Withholding Tax', 894.66),
(1819, 129, 'Allowance', 'Rice Subsidy', 2500.00),
(1820, 129, 'Allowance', 'Meal Allowance', 3000.00),
(1821, 129, 'Allowance', 'Laundry Allowance', 400.00),
(1822, 129, 'Allowance', 'Travel Allowance', 7000.00),
(1823, 129, 'Allowance', 'Communication Allowance', 2000.00),
(1824, 129, 'Deduction', 'SSS Regular (EE)', 500.00),
(1825, 129, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1826, 129, 'Deduction', 'SSS WISP (EE)', 375.00),
(1827, 129, 'Deduction', 'SSS WISP (ER)', 750.00),
(1828, 129, 'Deduction', 'PhilHealth (EE)', 662.50),
(1829, 129, 'Deduction', 'PhilHealth (ER)', 662.50),
(1830, 129, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1831, 129, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1832, 129, 'Deduction', 'Withholding Tax', 4066.70),
(1833, 130, 'Allowance', 'Rice Subsidy', 2500.00),
(1834, 130, 'Allowance', 'Meal Allowance', 2500.00),
(1835, 130, 'Allowance', 'Laundry Allowance', 400.00),
(1836, 130, 'Allowance', 'Travel Allowance', 5000.00),
(1837, 130, 'Allowance', 'Communication Allowance', 1500.00),
(1838, 130, 'Allowance', 'Overtime Pay', 1875.00),
(1839, 130, 'Deduction', 'SSS Regular (EE)', 500.00),
(1840, 130, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1841, 130, 'Deduction', 'SSS WISP (EE)', 375.00),
(1842, 130, 'Deduction', 'SSS WISP (ER)', 750.00),
(1843, 130, 'Deduction', 'PhilHealth (EE)', 500.00),
(1844, 130, 'Deduction', 'PhilHealth (ER)', 500.00),
(1845, 130, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1846, 130, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1847, 130, 'Deduction', 'Late/Undertime', 93.75),
(1848, 130, 'Deduction', 'Withholding Tax', 2855.45),
(1849, 131, 'Allowance', 'Rice Subsidy', 2500.00),
(1850, 131, 'Allowance', 'Meal Allowance', 2000.00),
(1851, 131, 'Allowance', 'Laundry Allowance', 400.00),
(1852, 131, 'Allowance', 'Travel Allowance', 3500.00),
(1853, 131, 'Allowance', 'Communication Allowance', 1200.00),
(1854, 131, 'Deduction', 'SSS Regular (EE)', 500.00),
(1855, 131, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1856, 131, 'Deduction', 'SSS WISP (EE)', 25.00),
(1857, 131, 'Deduction', 'SSS WISP (ER)', 50.00),
(1858, 131, 'Deduction', 'PhilHealth (EE)', 262.50),
(1859, 131, 'Deduction', 'PhilHealth (ER)', 262.50),
(1860, 131, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1861, 131, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1862, 131, 'Deduction', 'Withholding Tax', 2204.88),
(1863, 132, 'Allowance', 'Rice Subsidy', 2500.00),
(1864, 132, 'Allowance', 'Meal Allowance', 2500.00),
(1865, 132, 'Allowance', 'Laundry Allowance', 400.00),
(1866, 132, 'Allowance', 'Travel Allowance', 5000.00),
(1867, 132, 'Allowance', 'Communication Allowance', 1500.00),
(1868, 132, 'Allowance', 'Overtime Pay', 312.50),
(1869, 132, 'Deduction', 'SSS Regular (EE)', 500.00),
(1870, 132, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1871, 132, 'Deduction', 'SSS WISP (EE)', 375.00),
(1872, 132, 'Deduction', 'SSS WISP (ER)', 750.00),
(1873, 132, 'Deduction', 'PhilHealth (EE)', 500.00),
(1874, 132, 'Deduction', 'PhilHealth (ER)', 500.00),
(1875, 132, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1876, 132, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1877, 132, 'Deduction', 'Late/Undertime', 20.84),
(1878, 132, 'Deduction', 'Withholding Tax', 2557.53),
(1879, 133, 'Allowance', 'Rice Subsidy', 2500.00),
(1880, 133, 'Allowance', 'Meal Allowance', 2000.00),
(1881, 133, 'Allowance', 'Laundry Allowance', 400.00),
(1882, 133, 'Allowance', 'Travel Allowance', 3500.00),
(1883, 133, 'Allowance', 'Communication Allowance', 1200.00),
(1884, 133, 'Deduction', 'SSS Regular (EE)', 500.00),
(1885, 133, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1886, 133, 'Deduction', 'SSS WISP (EE)', 225.00),
(1887, 133, 'Deduction', 'SSS WISP (ER)', 450.00),
(1888, 133, 'Deduction', 'PhilHealth (EE)', 362.50),
(1889, 133, 'Deduction', 'PhilHealth (ER)', 362.50),
(1890, 133, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1891, 133, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1892, 133, 'Deduction', 'Withholding Tax', 1226.70),
(1893, 134, 'Allowance', 'Rice Subsidy', 2500.00),
(1894, 134, 'Allowance', 'Meal Allowance', 3000.00),
(1895, 134, 'Allowance', 'Laundry Allowance', 400.00),
(1896, 134, 'Allowance', 'Travel Allowance', 7000.00),
(1897, 134, 'Allowance', 'Communication Allowance', 2000.00),
(1898, 134, 'Deduction', 'SSS Regular (EE)', 500.00),
(1899, 134, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1900, 134, 'Deduction', 'SSS WISP (EE)', 375.00),
(1901, 134, 'Deduction', 'SSS WISP (ER)', 750.00),
(1902, 134, 'Deduction', 'PhilHealth (EE)', 675.00),
(1903, 134, 'Deduction', 'PhilHealth (ER)', 675.00),
(1904, 134, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1905, 134, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1906, 134, 'Deduction', 'Withholding Tax', 4164.20),
(1907, 135, 'Allowance', 'Rice Subsidy', 2500.00),
(1908, 135, 'Allowance', 'Meal Allowance', 2000.00),
(1909, 135, 'Allowance', 'Laundry Allowance', 400.00),
(1910, 135, 'Allowance', 'Travel Allowance', 3500.00),
(1911, 135, 'Allowance', 'Communication Allowance', 1200.00),
(1912, 135, 'Deduction', 'SSS Regular (EE)', 500.00),
(1913, 135, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1914, 135, 'Deduction', 'SSS WISP (EE)', 225.00),
(1915, 135, 'Deduction', 'SSS WISP (ER)', 450.00),
(1916, 135, 'Deduction', 'PhilHealth (EE)', 362.50),
(1917, 135, 'Deduction', 'PhilHealth (ER)', 362.50),
(1918, 135, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1919, 135, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1920, 135, 'Deduction', 'Withholding Tax', 1226.70),
(1921, 136, 'Allowance', 'Rice Subsidy', 2500.00),
(1922, 136, 'Allowance', 'Meal Allowance', 3500.00),
(1923, 136, 'Allowance', 'Laundry Allowance', 400.00),
(1924, 136, 'Allowance', 'Travel Allowance', 10000.00),
(1925, 136, 'Allowance', 'Communication Allowance', 3000.00),
(1926, 136, 'Allowance', 'Overtime Pay', 3125.00),
(1927, 136, 'Deduction', 'SSS Regular (EE)', 500.00),
(1928, 136, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1929, 136, 'Deduction', 'SSS WISP (EE)', 375.00),
(1930, 136, 'Deduction', 'SSS WISP (ER)', 750.00),
(1931, 136, 'Deduction', 'PhilHealth (EE)', 1000.00),
(1932, 136, 'Deduction', 'PhilHealth (ER)', 1000.00),
(1933, 136, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1934, 136, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1935, 136, 'Deduction', 'Late/Undertime', 125.00),
(1936, 136, 'Deduction', 'Withholding Tax', 8618.78),
(1937, 137, 'Allowance', 'Rice Subsidy', 2500.00),
(1938, 137, 'Allowance', 'Meal Allowance', 3500.00),
(1939, 137, 'Allowance', 'Laundry Allowance', 400.00),
(1940, 137, 'Allowance', 'Travel Allowance', 10000.00),
(1941, 137, 'Allowance', 'Communication Allowance', 3000.00),
(1942, 137, 'Allowance', 'Overtime Pay', 1562.50),
(1943, 137, 'Deduction', 'SSS Regular (EE)', 500.00),
(1944, 137, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1945, 137, 'Deduction', 'SSS WISP (EE)', 375.00),
(1946, 137, 'Deduction', 'SSS WISP (ER)', 750.00),
(1947, 137, 'Deduction', 'PhilHealth (EE)', 1000.00),
(1948, 137, 'Deduction', 'PhilHealth (ER)', 1000.00),
(1949, 137, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1950, 137, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1951, 137, 'Deduction', 'Withholding Tax', 8259.40),
(1952, 138, 'Allowance', 'Rice Subsidy', 2500.00),
(1953, 138, 'Allowance', 'Meal Allowance', 1500.00),
(1954, 138, 'Allowance', 'Laundry Allowance', 400.00),
(1955, 138, 'Allowance', 'Travel Allowance', 2500.00),
(1956, 138, 'Allowance', 'Communication Allowance', 800.00),
(1957, 138, 'Deduction', 'SSS Regular (EE)', 500.00),
(1958, 138, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1959, 138, 'Deduction', 'SSS WISP (EE)', 25.00),
(1960, 138, 'Deduction', 'SSS WISP (ER)', 50.00),
(1961, 138, 'Deduction', 'PhilHealth (EE)', 262.50),
(1962, 138, 'Deduction', 'PhilHealth (ER)', 262.50),
(1963, 138, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1964, 138, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1965, 138, 'Deduction', 'Late/Undertime', 131.25),
(1966, 138, 'Deduction', 'Withholding Tax', 1988.63),
(1967, 139, 'Allowance', 'Rice Subsidy', 2500.00),
(1968, 139, 'Allowance', 'Meal Allowance', 1000.00),
(1969, 139, 'Allowance', 'Laundry Allowance', 400.00),
(1970, 139, 'Allowance', 'Travel Allowance', 1500.00),
(1971, 139, 'Allowance', 'Communication Allowance', 500.00),
(1972, 139, 'Allowance', 'Overtime Pay', 468.75),
(1973, 139, 'Deduction', 'SSS Regular (EE)', 375.00),
(1974, 139, 'Deduction', 'SSS Regular (ER)', 750.00),
(1975, 139, 'Deduction', 'PhilHealth (EE)', 187.50),
(1976, 139, 'Deduction', 'PhilHealth (ER)', 187.50),
(1977, 139, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1978, 139, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1979, 139, 'Deduction', 'Late/Undertime', 11.72),
(1980, 139, 'Deduction', 'Withholding Tax', 894.66),
(1981, 140, 'Allowance', 'Rice Subsidy', 2500.00),
(1982, 140, 'Allowance', 'Meal Allowance', 3000.00),
(1983, 140, 'Allowance', 'Laundry Allowance', 400.00),
(1984, 140, 'Allowance', 'Travel Allowance', 7000.00),
(1985, 140, 'Allowance', 'Communication Allowance', 2000.00),
(1986, 140, 'Deduction', 'SSS Regular (EE)', 500.00),
(1987, 140, 'Deduction', 'SSS Regular (ER)', 1000.00),
(1988, 140, 'Deduction', 'SSS WISP (EE)', 375.00),
(1989, 140, 'Deduction', 'SSS WISP (ER)', 750.00),
(1990, 140, 'Deduction', 'PhilHealth (EE)', 662.50),
(1991, 140, 'Deduction', 'PhilHealth (ER)', 662.50),
(1992, 140, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(1993, 140, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(1994, 140, 'Deduction', 'Withholding Tax', 4066.70),
(1995, 141, 'Allowance', 'Rice Subsidy', 2500.00),
(1996, 141, 'Allowance', 'Meal Allowance', 2500.00),
(1997, 141, 'Allowance', 'Laundry Allowance', 400.00),
(1998, 141, 'Allowance', 'Travel Allowance', 5000.00),
(1999, 141, 'Allowance', 'Communication Allowance', 1500.00),
(2000, 141, 'Allowance', 'Overtime Pay', 1875.00),
(2001, 141, 'Deduction', 'SSS Regular (EE)', 500.00),
(2002, 141, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2003, 141, 'Deduction', 'SSS WISP (EE)', 375.00),
(2004, 141, 'Deduction', 'SSS WISP (ER)', 750.00),
(2005, 141, 'Deduction', 'PhilHealth (EE)', 500.00),
(2006, 141, 'Deduction', 'PhilHealth (ER)', 500.00);
INSERT INTO `payroll_item_components` (`id`, `item_id`, `component_type`, `component_name`, `amount`) VALUES
(2007, 141, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2008, 141, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2009, 141, 'Deduction', 'Late/Undertime', 93.75),
(2010, 141, 'Deduction', 'Withholding Tax', 2855.45),
(2011, 142, 'Allowance', 'Rice Subsidy', 2500.00),
(2012, 142, 'Allowance', 'Meal Allowance', 2000.00),
(2013, 142, 'Allowance', 'Laundry Allowance', 400.00),
(2014, 142, 'Allowance', 'Travel Allowance', 3500.00),
(2015, 142, 'Allowance', 'Communication Allowance', 1200.00),
(2016, 142, 'Deduction', 'SSS Regular (EE)', 500.00),
(2017, 142, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2018, 142, 'Deduction', 'SSS WISP (EE)', 25.00),
(2019, 142, 'Deduction', 'SSS WISP (ER)', 50.00),
(2020, 142, 'Deduction', 'PhilHealth (EE)', 262.50),
(2021, 142, 'Deduction', 'PhilHealth (ER)', 262.50),
(2022, 142, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2023, 142, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2024, 142, 'Deduction', 'Withholding Tax', 2204.88),
(2025, 143, 'Allowance', 'Rice Subsidy', 2500.00),
(2026, 143, 'Allowance', 'Meal Allowance', 2500.00),
(2027, 143, 'Allowance', 'Laundry Allowance', 400.00),
(2028, 143, 'Allowance', 'Travel Allowance', 5000.00),
(2029, 143, 'Allowance', 'Communication Allowance', 1500.00),
(2030, 143, 'Allowance', 'Overtime Pay', 312.50),
(2031, 143, 'Deduction', 'SSS Regular (EE)', 500.00),
(2032, 143, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2033, 143, 'Deduction', 'SSS WISP (EE)', 375.00),
(2034, 143, 'Deduction', 'SSS WISP (ER)', 750.00),
(2035, 143, 'Deduction', 'PhilHealth (EE)', 500.00),
(2036, 143, 'Deduction', 'PhilHealth (ER)', 500.00),
(2037, 143, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2038, 143, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2039, 143, 'Deduction', 'Late/Undertime', 20.84),
(2040, 143, 'Deduction', 'Withholding Tax', 2557.53),
(2041, 144, 'Allowance', 'Rice Subsidy', 2500.00),
(2042, 144, 'Allowance', 'Meal Allowance', 2000.00),
(2043, 144, 'Allowance', 'Laundry Allowance', 400.00),
(2044, 144, 'Allowance', 'Travel Allowance', 3500.00),
(2045, 144, 'Allowance', 'Communication Allowance', 1200.00),
(2046, 144, 'Deduction', 'SSS Regular (EE)', 500.00),
(2047, 144, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2048, 144, 'Deduction', 'SSS WISP (EE)', 225.00),
(2049, 144, 'Deduction', 'SSS WISP (ER)', 450.00),
(2050, 144, 'Deduction', 'PhilHealth (EE)', 362.50),
(2051, 144, 'Deduction', 'PhilHealth (ER)', 362.50),
(2052, 144, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2053, 144, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2054, 144, 'Deduction', 'Withholding Tax', 1226.70),
(2055, 145, 'Allowance', 'Rice Subsidy', 2500.00),
(2056, 145, 'Allowance', 'Meal Allowance', 3000.00),
(2057, 145, 'Allowance', 'Laundry Allowance', 400.00),
(2058, 145, 'Allowance', 'Travel Allowance', 7000.00),
(2059, 145, 'Allowance', 'Communication Allowance', 2000.00),
(2060, 145, 'Deduction', 'SSS Regular (EE)', 500.00),
(2061, 145, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2062, 145, 'Deduction', 'SSS WISP (EE)', 375.00),
(2063, 145, 'Deduction', 'SSS WISP (ER)', 750.00),
(2064, 145, 'Deduction', 'PhilHealth (EE)', 675.00),
(2065, 145, 'Deduction', 'PhilHealth (ER)', 675.00),
(2066, 145, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2067, 145, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2068, 145, 'Deduction', 'Withholding Tax', 4164.20),
(2069, 146, 'Allowance', 'Rice Subsidy', 2500.00),
(2070, 146, 'Allowance', 'Meal Allowance', 2000.00),
(2071, 146, 'Allowance', 'Laundry Allowance', 400.00),
(2072, 146, 'Allowance', 'Travel Allowance', 3500.00),
(2073, 146, 'Allowance', 'Communication Allowance', 1200.00),
(2074, 146, 'Deduction', 'SSS Regular (EE)', 500.00),
(2075, 146, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2076, 146, 'Deduction', 'SSS WISP (EE)', 225.00),
(2077, 146, 'Deduction', 'SSS WISP (ER)', 450.00),
(2078, 146, 'Deduction', 'PhilHealth (EE)', 362.50),
(2079, 146, 'Deduction', 'PhilHealth (ER)', 362.50),
(2080, 146, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2081, 146, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2082, 146, 'Deduction', 'Withholding Tax', 1226.70),
(2083, 147, 'Allowance', 'Rice Subsidy', 2500.00),
(2084, 147, 'Allowance', 'Meal Allowance', 3500.00),
(2085, 147, 'Allowance', 'Laundry Allowance', 400.00),
(2086, 147, 'Allowance', 'Travel Allowance', 10000.00),
(2087, 147, 'Allowance', 'Communication Allowance', 3000.00),
(2088, 147, 'Allowance', 'Overtime Pay', 3125.00),
(2089, 147, 'Deduction', 'SSS Regular (EE)', 500.00),
(2090, 147, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2091, 147, 'Deduction', 'SSS WISP (EE)', 375.00),
(2092, 147, 'Deduction', 'SSS WISP (ER)', 750.00),
(2093, 147, 'Deduction', 'PhilHealth (EE)', 1000.00),
(2094, 147, 'Deduction', 'PhilHealth (ER)', 1000.00),
(2095, 147, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2096, 147, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2097, 147, 'Deduction', 'Late/Undertime', 125.00),
(2098, 147, 'Deduction', 'Withholding Tax', 8618.78),
(2099, 148, 'Allowance', 'Rice Subsidy', 2500.00),
(2100, 148, 'Allowance', 'Meal Allowance', 3500.00),
(2101, 148, 'Allowance', 'Laundry Allowance', 400.00),
(2102, 148, 'Allowance', 'Travel Allowance', 10000.00),
(2103, 148, 'Allowance', 'Communication Allowance', 3000.00),
(2104, 148, 'Allowance', 'Overtime Pay', 1562.50),
(2105, 148, 'Deduction', 'SSS Regular (EE)', 500.00),
(2106, 148, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2107, 148, 'Deduction', 'SSS WISP (EE)', 375.00),
(2108, 148, 'Deduction', 'SSS WISP (ER)', 750.00),
(2109, 148, 'Deduction', 'PhilHealth (EE)', 1000.00),
(2110, 148, 'Deduction', 'PhilHealth (ER)', 1000.00),
(2111, 148, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2112, 148, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2113, 148, 'Deduction', 'Withholding Tax', 8259.40),
(2114, 149, 'Allowance', 'Rice Subsidy', 2500.00),
(2115, 149, 'Allowance', 'Meal Allowance', 1500.00),
(2116, 149, 'Allowance', 'Laundry Allowance', 400.00),
(2117, 149, 'Allowance', 'Travel Allowance', 2500.00),
(2118, 149, 'Allowance', 'Communication Allowance', 800.00),
(2119, 149, 'Deduction', 'SSS Regular (EE)', 500.00),
(2120, 149, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2121, 149, 'Deduction', 'SSS WISP (EE)', 25.00),
(2122, 149, 'Deduction', 'SSS WISP (ER)', 50.00),
(2123, 149, 'Deduction', 'PhilHealth (EE)', 262.50),
(2124, 149, 'Deduction', 'PhilHealth (ER)', 262.50),
(2125, 149, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2126, 149, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2127, 149, 'Deduction', 'Late/Undertime', 131.25),
(2128, 149, 'Deduction', 'Withholding Tax', 1988.63),
(2129, 150, 'Allowance', 'Rice Subsidy', 2500.00),
(2130, 150, 'Allowance', 'Meal Allowance', 1000.00),
(2131, 150, 'Allowance', 'Laundry Allowance', 400.00),
(2132, 150, 'Allowance', 'Travel Allowance', 1500.00),
(2133, 150, 'Allowance', 'Communication Allowance', 500.00),
(2134, 150, 'Allowance', 'Overtime Pay', 468.75),
(2135, 150, 'Deduction', 'SSS Regular (EE)', 375.00),
(2136, 150, 'Deduction', 'SSS Regular (ER)', 750.00),
(2137, 150, 'Deduction', 'PhilHealth (EE)', 187.50),
(2138, 150, 'Deduction', 'PhilHealth (ER)', 187.50),
(2139, 150, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2140, 150, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2141, 150, 'Deduction', 'Late/Undertime', 11.72),
(2142, 150, 'Deduction', 'Withholding Tax', 894.66),
(2143, 151, 'Allowance', 'Rice Subsidy', 2500.00),
(2144, 151, 'Allowance', 'Meal Allowance', 3000.00),
(2145, 151, 'Allowance', 'Laundry Allowance', 400.00),
(2146, 151, 'Allowance', 'Travel Allowance', 7000.00),
(2147, 151, 'Allowance', 'Communication Allowance', 2000.00),
(2148, 151, 'Deduction', 'SSS Regular (EE)', 500.00),
(2149, 151, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2150, 151, 'Deduction', 'SSS WISP (EE)', 375.00),
(2151, 151, 'Deduction', 'SSS WISP (ER)', 750.00),
(2152, 151, 'Deduction', 'PhilHealth (EE)', 662.50),
(2153, 151, 'Deduction', 'PhilHealth (ER)', 662.50),
(2154, 151, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2155, 151, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2156, 151, 'Deduction', 'Withholding Tax', 4066.70),
(2157, 152, 'Allowance', 'Rice Subsidy', 2500.00),
(2158, 152, 'Allowance', 'Meal Allowance', 2500.00),
(2159, 152, 'Allowance', 'Laundry Allowance', 400.00),
(2160, 152, 'Allowance', 'Travel Allowance', 5000.00),
(2161, 152, 'Allowance', 'Communication Allowance', 1500.00),
(2162, 152, 'Allowance', 'Overtime Pay', 1875.00),
(2163, 152, 'Deduction', 'SSS Regular (EE)', 500.00),
(2164, 152, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2165, 152, 'Deduction', 'SSS WISP (EE)', 375.00),
(2166, 152, 'Deduction', 'SSS WISP (ER)', 750.00),
(2167, 152, 'Deduction', 'PhilHealth (EE)', 500.00),
(2168, 152, 'Deduction', 'PhilHealth (ER)', 500.00),
(2169, 152, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2170, 152, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2171, 152, 'Deduction', 'Late/Undertime', 93.75),
(2172, 152, 'Deduction', 'Withholding Tax', 2855.45),
(2173, 153, 'Allowance', 'Rice Subsidy', 2500.00),
(2174, 153, 'Allowance', 'Meal Allowance', 2000.00),
(2175, 153, 'Allowance', 'Laundry Allowance', 400.00),
(2176, 153, 'Allowance', 'Travel Allowance', 3500.00),
(2177, 153, 'Allowance', 'Communication Allowance', 1200.00),
(2178, 153, 'Deduction', 'SSS Regular (EE)', 500.00),
(2179, 153, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2180, 153, 'Deduction', 'SSS WISP (EE)', 25.00),
(2181, 153, 'Deduction', 'SSS WISP (ER)', 50.00),
(2182, 153, 'Deduction', 'PhilHealth (EE)', 262.50),
(2183, 153, 'Deduction', 'PhilHealth (ER)', 262.50),
(2184, 153, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2185, 153, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2186, 153, 'Deduction', 'Withholding Tax', 2204.88),
(2187, 154, 'Allowance', 'Rice Subsidy', 2500.00),
(2188, 154, 'Allowance', 'Meal Allowance', 2500.00),
(2189, 154, 'Allowance', 'Laundry Allowance', 400.00),
(2190, 154, 'Allowance', 'Travel Allowance', 5000.00),
(2191, 154, 'Allowance', 'Communication Allowance', 1500.00),
(2192, 154, 'Allowance', 'Overtime Pay', 312.50),
(2193, 154, 'Deduction', 'SSS Regular (EE)', 500.00),
(2194, 154, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2195, 154, 'Deduction', 'SSS WISP (EE)', 375.00),
(2196, 154, 'Deduction', 'SSS WISP (ER)', 750.00),
(2197, 154, 'Deduction', 'PhilHealth (EE)', 500.00),
(2198, 154, 'Deduction', 'PhilHealth (ER)', 500.00),
(2199, 154, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2200, 154, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2201, 154, 'Deduction', 'Late/Undertime', 20.84),
(2202, 154, 'Deduction', 'Withholding Tax', 2557.53),
(2203, 155, 'Allowance', 'Rice Subsidy', 2500.00),
(2204, 155, 'Allowance', 'Meal Allowance', 2000.00),
(2205, 155, 'Allowance', 'Laundry Allowance', 400.00),
(2206, 155, 'Allowance', 'Travel Allowance', 3500.00),
(2207, 155, 'Allowance', 'Communication Allowance', 1200.00),
(2208, 155, 'Deduction', 'SSS Regular (EE)', 500.00),
(2209, 155, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2210, 155, 'Deduction', 'SSS WISP (EE)', 225.00),
(2211, 155, 'Deduction', 'SSS WISP (ER)', 450.00),
(2212, 155, 'Deduction', 'PhilHealth (EE)', 362.50),
(2213, 155, 'Deduction', 'PhilHealth (ER)', 362.50),
(2214, 155, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2215, 155, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2216, 155, 'Deduction', 'Withholding Tax', 1226.70),
(2217, 156, 'Allowance', 'Rice Subsidy', 2500.00),
(2218, 156, 'Allowance', 'Meal Allowance', 3000.00),
(2219, 156, 'Allowance', 'Laundry Allowance', 400.00),
(2220, 156, 'Allowance', 'Travel Allowance', 7000.00),
(2221, 156, 'Allowance', 'Communication Allowance', 2000.00),
(2222, 156, 'Deduction', 'SSS Regular (EE)', 500.00),
(2223, 156, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2224, 156, 'Deduction', 'SSS WISP (EE)', 375.00),
(2225, 156, 'Deduction', 'SSS WISP (ER)', 750.00),
(2226, 156, 'Deduction', 'PhilHealth (EE)', 675.00),
(2227, 156, 'Deduction', 'PhilHealth (ER)', 675.00),
(2228, 156, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2229, 156, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2230, 156, 'Deduction', 'Withholding Tax', 4164.20),
(2231, 157, 'Allowance', 'Rice Subsidy', 2500.00),
(2232, 157, 'Allowance', 'Meal Allowance', 2000.00),
(2233, 157, 'Allowance', 'Laundry Allowance', 400.00),
(2234, 157, 'Allowance', 'Travel Allowance', 3500.00),
(2235, 157, 'Allowance', 'Communication Allowance', 1200.00),
(2236, 157, 'Deduction', 'SSS Regular (EE)', 500.00),
(2237, 157, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2238, 157, 'Deduction', 'SSS WISP (EE)', 225.00),
(2239, 157, 'Deduction', 'SSS WISP (ER)', 450.00),
(2240, 157, 'Deduction', 'PhilHealth (EE)', 362.50),
(2241, 157, 'Deduction', 'PhilHealth (ER)', 362.50),
(2242, 157, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2243, 157, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2244, 157, 'Deduction', 'Withholding Tax', 1226.70),
(2245, 158, 'Allowance', 'Rice Subsidy', 2500.00),
(2246, 158, 'Allowance', 'Meal Allowance', 3500.00),
(2247, 158, 'Allowance', 'Laundry Allowance', 400.00),
(2248, 158, 'Allowance', 'Travel Allowance', 10000.00),
(2249, 158, 'Allowance', 'Communication Allowance', 3000.00),
(2250, 158, 'Allowance', 'Overtime Pay', 3125.00),
(2251, 158, 'Deduction', 'SSS Regular (EE)', 500.00),
(2252, 158, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2253, 158, 'Deduction', 'SSS WISP (EE)', 375.00),
(2254, 158, 'Deduction', 'SSS WISP (ER)', 750.00),
(2255, 158, 'Deduction', 'PhilHealth (EE)', 1000.00),
(2256, 158, 'Deduction', 'PhilHealth (ER)', 1000.00),
(2257, 158, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2258, 158, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2259, 158, 'Deduction', 'Late/Undertime', 125.00),
(2260, 158, 'Deduction', 'Withholding Tax', 8618.78),
(2261, 159, 'Allowance', 'Rice Subsidy', 2500.00),
(2262, 159, 'Allowance', 'Meal Allowance', 3500.00),
(2263, 159, 'Allowance', 'Laundry Allowance', 400.00),
(2264, 159, 'Allowance', 'Travel Allowance', 10000.00),
(2265, 159, 'Allowance', 'Communication Allowance', 3000.00),
(2266, 159, 'Allowance', 'Overtime Pay', 1562.50),
(2267, 159, 'Deduction', 'SSS Regular (EE)', 500.00),
(2268, 159, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2269, 159, 'Deduction', 'SSS WISP (EE)', 375.00),
(2270, 159, 'Deduction', 'SSS WISP (ER)', 750.00),
(2271, 159, 'Deduction', 'PhilHealth (EE)', 1000.00),
(2272, 159, 'Deduction', 'PhilHealth (ER)', 1000.00),
(2273, 159, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2274, 159, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2275, 159, 'Deduction', 'Withholding Tax', 8259.40),
(2276, 160, 'Allowance', 'Rice Subsidy', 2500.00),
(2277, 160, 'Allowance', 'Meal Allowance', 1500.00),
(2278, 160, 'Allowance', 'Laundry Allowance', 400.00),
(2279, 160, 'Allowance', 'Travel Allowance', 2500.00),
(2280, 160, 'Allowance', 'Communication Allowance', 800.00),
(2281, 160, 'Deduction', 'SSS Regular (EE)', 500.00),
(2282, 160, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2283, 160, 'Deduction', 'SSS WISP (EE)', 25.00),
(2284, 160, 'Deduction', 'SSS WISP (ER)', 50.00),
(2285, 160, 'Deduction', 'PhilHealth (EE)', 262.50),
(2286, 160, 'Deduction', 'PhilHealth (ER)', 262.50),
(2287, 160, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2288, 160, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2289, 160, 'Deduction', 'Late/Undertime', 131.25),
(2290, 160, 'Deduction', 'Withholding Tax', 1988.63),
(2291, 161, 'Allowance', 'Rice Subsidy', 2500.00),
(2292, 161, 'Allowance', 'Meal Allowance', 1000.00),
(2293, 161, 'Allowance', 'Laundry Allowance', 400.00),
(2294, 161, 'Allowance', 'Travel Allowance', 1500.00),
(2295, 161, 'Allowance', 'Communication Allowance', 500.00),
(2296, 161, 'Allowance', 'Overtime Pay', 468.75),
(2297, 161, 'Deduction', 'SSS Regular (EE)', 375.00),
(2298, 161, 'Deduction', 'SSS Regular (ER)', 750.00),
(2299, 161, 'Deduction', 'PhilHealth (EE)', 187.50),
(2300, 161, 'Deduction', 'PhilHealth (ER)', 187.50),
(2301, 161, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2302, 161, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2303, 161, 'Deduction', 'Late/Undertime', 11.72),
(2304, 161, 'Deduction', 'Withholding Tax', 894.66),
(2305, 162, 'Allowance', 'Rice Subsidy', 2500.00),
(2306, 162, 'Allowance', 'Meal Allowance', 3000.00),
(2307, 162, 'Allowance', 'Laundry Allowance', 400.00),
(2308, 162, 'Allowance', 'Travel Allowance', 7000.00),
(2309, 162, 'Allowance', 'Communication Allowance', 2000.00),
(2310, 162, 'Deduction', 'SSS Regular (EE)', 500.00),
(2311, 162, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2312, 162, 'Deduction', 'SSS WISP (EE)', 375.00),
(2313, 162, 'Deduction', 'SSS WISP (ER)', 750.00),
(2314, 162, 'Deduction', 'PhilHealth (EE)', 662.50),
(2315, 162, 'Deduction', 'PhilHealth (ER)', 662.50),
(2316, 162, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2317, 162, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2318, 162, 'Deduction', 'Withholding Tax', 4066.70),
(2319, 163, 'Allowance', 'Rice Subsidy', 2500.00),
(2320, 163, 'Allowance', 'Meal Allowance', 2500.00),
(2321, 163, 'Allowance', 'Laundry Allowance', 400.00),
(2322, 163, 'Allowance', 'Travel Allowance', 5000.00),
(2323, 163, 'Allowance', 'Communication Allowance', 1500.00),
(2324, 163, 'Allowance', 'Overtime Pay', 1875.00),
(2325, 163, 'Deduction', 'SSS Regular (EE)', 500.00),
(2326, 163, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2327, 163, 'Deduction', 'SSS WISP (EE)', 375.00),
(2328, 163, 'Deduction', 'SSS WISP (ER)', 750.00),
(2329, 163, 'Deduction', 'PhilHealth (EE)', 500.00),
(2330, 163, 'Deduction', 'PhilHealth (ER)', 500.00),
(2331, 163, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2332, 163, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2333, 163, 'Deduction', 'Late/Undertime', 93.75),
(2334, 163, 'Deduction', 'Withholding Tax', 2855.45),
(2335, 164, 'Allowance', 'Rice Subsidy', 2500.00),
(2336, 164, 'Allowance', 'Meal Allowance', 2000.00),
(2337, 164, 'Allowance', 'Laundry Allowance', 400.00),
(2338, 164, 'Allowance', 'Travel Allowance', 3500.00),
(2339, 164, 'Allowance', 'Communication Allowance', 1200.00),
(2340, 164, 'Deduction', 'SSS Regular (EE)', 500.00),
(2341, 164, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2342, 164, 'Deduction', 'SSS WISP (EE)', 25.00),
(2343, 164, 'Deduction', 'SSS WISP (ER)', 50.00),
(2344, 164, 'Deduction', 'PhilHealth (EE)', 262.50),
(2345, 164, 'Deduction', 'PhilHealth (ER)', 262.50),
(2346, 164, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2347, 164, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2348, 164, 'Deduction', 'Withholding Tax', 2204.88),
(2349, 165, 'Allowance', 'Rice Subsidy', 2500.00),
(2350, 165, 'Allowance', 'Meal Allowance', 2500.00),
(2351, 165, 'Allowance', 'Laundry Allowance', 400.00),
(2352, 165, 'Allowance', 'Travel Allowance', 5000.00),
(2353, 165, 'Allowance', 'Communication Allowance', 1500.00),
(2354, 165, 'Allowance', 'Overtime Pay', 312.50),
(2355, 165, 'Deduction', 'SSS Regular (EE)', 500.00),
(2356, 165, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2357, 165, 'Deduction', 'SSS WISP (EE)', 375.00),
(2358, 165, 'Deduction', 'SSS WISP (ER)', 750.00),
(2359, 165, 'Deduction', 'PhilHealth (EE)', 500.00),
(2360, 165, 'Deduction', 'PhilHealth (ER)', 500.00),
(2361, 165, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2362, 165, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2363, 165, 'Deduction', 'Late/Undertime', 20.84),
(2364, 165, 'Deduction', 'Withholding Tax', 2557.53),
(2365, 166, 'Allowance', 'Rice Subsidy', 2500.00),
(2366, 166, 'Allowance', 'Meal Allowance', 2000.00),
(2367, 166, 'Allowance', 'Laundry Allowance', 400.00),
(2368, 166, 'Allowance', 'Travel Allowance', 3500.00),
(2369, 166, 'Allowance', 'Communication Allowance', 1200.00),
(2370, 166, 'Deduction', 'SSS Regular (EE)', 500.00),
(2371, 166, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2372, 166, 'Deduction', 'SSS WISP (EE)', 225.00),
(2373, 166, 'Deduction', 'SSS WISP (ER)', 450.00),
(2374, 166, 'Deduction', 'PhilHealth (EE)', 362.50),
(2375, 166, 'Deduction', 'PhilHealth (ER)', 362.50),
(2376, 166, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2377, 166, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2378, 166, 'Deduction', 'Withholding Tax', 1226.70),
(2379, 167, 'Allowance', 'Rice Subsidy', 2500.00),
(2380, 167, 'Allowance', 'Meal Allowance', 3000.00),
(2381, 167, 'Allowance', 'Laundry Allowance', 400.00),
(2382, 167, 'Allowance', 'Travel Allowance', 7000.00),
(2383, 167, 'Allowance', 'Communication Allowance', 2000.00),
(2384, 167, 'Deduction', 'SSS Regular (EE)', 500.00),
(2385, 167, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2386, 167, 'Deduction', 'SSS WISP (EE)', 375.00),
(2387, 167, 'Deduction', 'SSS WISP (ER)', 750.00),
(2388, 167, 'Deduction', 'PhilHealth (EE)', 675.00),
(2389, 167, 'Deduction', 'PhilHealth (ER)', 675.00),
(2390, 167, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2391, 167, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2392, 167, 'Deduction', 'Withholding Tax', 4164.20),
(2393, 168, 'Allowance', 'Rice Subsidy', 2500.00),
(2394, 168, 'Allowance', 'Meal Allowance', 2000.00),
(2395, 168, 'Allowance', 'Laundry Allowance', 400.00),
(2396, 168, 'Allowance', 'Travel Allowance', 3500.00),
(2397, 168, 'Allowance', 'Communication Allowance', 1200.00),
(2398, 168, 'Deduction', 'SSS Regular (EE)', 500.00),
(2399, 168, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2400, 168, 'Deduction', 'SSS WISP (EE)', 225.00),
(2401, 168, 'Deduction', 'SSS WISP (ER)', 450.00),
(2402, 168, 'Deduction', 'PhilHealth (EE)', 362.50),
(2403, 168, 'Deduction', 'PhilHealth (ER)', 362.50),
(2404, 168, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2405, 168, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2406, 168, 'Deduction', 'Withholding Tax', 1226.70),
(2407, 169, 'Allowance', 'Rice Subsidy', 2500.00),
(2408, 169, 'Allowance', 'Meal Allowance', 3500.00),
(2409, 169, 'Allowance', 'Laundry Allowance', 400.00),
(2410, 169, 'Allowance', 'Travel Allowance', 10000.00),
(2411, 169, 'Allowance', 'Communication Allowance', 3000.00),
(2412, 169, 'Allowance', 'Overtime Pay', 3125.00),
(2413, 169, 'Deduction', 'SSS Regular (EE)', 500.00),
(2414, 169, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2415, 169, 'Deduction', 'SSS WISP (EE)', 375.00),
(2416, 169, 'Deduction', 'SSS WISP (ER)', 750.00),
(2417, 169, 'Deduction', 'PhilHealth (EE)', 1000.00),
(2418, 169, 'Deduction', 'PhilHealth (ER)', 1000.00),
(2419, 169, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2420, 169, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2421, 169, 'Deduction', 'Late/Undertime', 125.00),
(2422, 169, 'Deduction', 'Withholding Tax', 8618.78),
(2423, 170, 'Allowance', 'Rice Subsidy', 2500.00),
(2424, 170, 'Allowance', 'Meal Allowance', 3500.00),
(2425, 170, 'Allowance', 'Laundry Allowance', 400.00),
(2426, 170, 'Allowance', 'Travel Allowance', 10000.00),
(2427, 170, 'Allowance', 'Communication Allowance', 3000.00),
(2428, 170, 'Allowance', 'Overtime Pay', 1562.50),
(2429, 170, 'Deduction', 'SSS Regular (EE)', 500.00),
(2430, 170, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2431, 170, 'Deduction', 'SSS WISP (EE)', 375.00),
(2432, 170, 'Deduction', 'SSS WISP (ER)', 750.00),
(2433, 170, 'Deduction', 'PhilHealth (EE)', 1000.00),
(2434, 170, 'Deduction', 'PhilHealth (ER)', 1000.00),
(2435, 170, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2436, 170, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2437, 170, 'Deduction', 'Withholding Tax', 8259.40),
(2438, 171, 'Allowance', 'Rice Subsidy', 2500.00),
(2439, 171, 'Allowance', 'Meal Allowance', 1500.00),
(2440, 171, 'Allowance', 'Laundry Allowance', 400.00),
(2441, 171, 'Allowance', 'Travel Allowance', 2500.00),
(2442, 171, 'Allowance', 'Communication Allowance', 800.00),
(2443, 171, 'Deduction', 'SSS Regular (EE)', 500.00),
(2444, 171, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2445, 171, 'Deduction', 'SSS WISP (EE)', 25.00),
(2446, 171, 'Deduction', 'SSS WISP (ER)', 50.00),
(2447, 171, 'Deduction', 'PhilHealth (EE)', 262.50),
(2448, 171, 'Deduction', 'PhilHealth (ER)', 262.50),
(2449, 171, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2450, 171, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2451, 171, 'Deduction', 'Late/Undertime', 131.25),
(2452, 171, 'Deduction', 'Withholding Tax', 1988.63),
(2453, 172, 'Allowance', 'Rice Subsidy', 2500.00),
(2454, 172, 'Allowance', 'Meal Allowance', 1000.00),
(2455, 172, 'Allowance', 'Laundry Allowance', 400.00),
(2456, 172, 'Allowance', 'Travel Allowance', 1500.00),
(2457, 172, 'Allowance', 'Communication Allowance', 500.00),
(2458, 172, 'Allowance', 'Overtime Pay', 468.75),
(2459, 172, 'Deduction', 'SSS Regular (EE)', 375.00),
(2460, 172, 'Deduction', 'SSS Regular (ER)', 750.00),
(2461, 172, 'Deduction', 'PhilHealth (EE)', 187.50),
(2462, 172, 'Deduction', 'PhilHealth (ER)', 187.50),
(2463, 172, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2464, 172, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2465, 172, 'Deduction', 'Late/Undertime', 11.72),
(2466, 172, 'Deduction', 'Withholding Tax', 894.66),
(2467, 173, 'Allowance', 'Rice Subsidy', 2500.00),
(2468, 173, 'Allowance', 'Meal Allowance', 3000.00),
(2469, 173, 'Allowance', 'Laundry Allowance', 400.00),
(2470, 173, 'Allowance', 'Travel Allowance', 7000.00),
(2471, 173, 'Allowance', 'Communication Allowance', 2000.00),
(2472, 173, 'Deduction', 'SSS Regular (EE)', 500.00),
(2473, 173, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2474, 173, 'Deduction', 'SSS WISP (EE)', 375.00),
(2475, 173, 'Deduction', 'SSS WISP (ER)', 750.00),
(2476, 173, 'Deduction', 'PhilHealth (EE)', 662.50),
(2477, 173, 'Deduction', 'PhilHealth (ER)', 662.50),
(2478, 173, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2479, 173, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2480, 173, 'Deduction', 'Withholding Tax', 4066.70),
(2481, 174, 'Allowance', 'Rice Subsidy', 2500.00),
(2482, 174, 'Allowance', 'Meal Allowance', 2500.00),
(2483, 174, 'Allowance', 'Laundry Allowance', 400.00),
(2484, 174, 'Allowance', 'Travel Allowance', 5000.00),
(2485, 174, 'Allowance', 'Communication Allowance', 1500.00),
(2486, 174, 'Allowance', 'Overtime Pay', 1875.00),
(2487, 174, 'Deduction', 'SSS Regular (EE)', 500.00),
(2488, 174, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2489, 174, 'Deduction', 'SSS WISP (EE)', 375.00),
(2490, 174, 'Deduction', 'SSS WISP (ER)', 750.00),
(2491, 174, 'Deduction', 'PhilHealth (EE)', 500.00),
(2492, 174, 'Deduction', 'PhilHealth (ER)', 500.00),
(2493, 174, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2494, 174, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2495, 174, 'Deduction', 'Late/Undertime', 93.75),
(2496, 174, 'Deduction', 'Withholding Tax', 2855.45),
(2497, 175, 'Allowance', 'Rice Subsidy', 2500.00),
(2498, 175, 'Allowance', 'Meal Allowance', 2000.00),
(2499, 175, 'Allowance', 'Laundry Allowance', 400.00),
(2500, 175, 'Allowance', 'Travel Allowance', 3500.00),
(2501, 175, 'Allowance', 'Communication Allowance', 1200.00),
(2502, 175, 'Deduction', 'SSS Regular (EE)', 500.00),
(2503, 175, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2504, 175, 'Deduction', 'SSS WISP (EE)', 25.00),
(2505, 175, 'Deduction', 'SSS WISP (ER)', 50.00),
(2506, 175, 'Deduction', 'PhilHealth (EE)', 262.50),
(2507, 175, 'Deduction', 'PhilHealth (ER)', 262.50),
(2508, 175, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2509, 175, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2510, 175, 'Deduction', 'Withholding Tax', 2204.88),
(2511, 176, 'Allowance', 'Rice Subsidy', 2500.00),
(2512, 176, 'Allowance', 'Meal Allowance', 2500.00),
(2513, 176, 'Allowance', 'Laundry Allowance', 400.00),
(2514, 176, 'Allowance', 'Travel Allowance', 5000.00),
(2515, 176, 'Allowance', 'Communication Allowance', 1500.00),
(2516, 176, 'Allowance', 'Overtime Pay', 312.50),
(2517, 176, 'Deduction', 'SSS Regular (EE)', 500.00),
(2518, 176, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2519, 176, 'Deduction', 'SSS WISP (EE)', 375.00),
(2520, 176, 'Deduction', 'SSS WISP (ER)', 750.00),
(2521, 176, 'Deduction', 'PhilHealth (EE)', 500.00),
(2522, 176, 'Deduction', 'PhilHealth (ER)', 500.00),
(2523, 176, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2524, 176, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2525, 176, 'Deduction', 'Late/Undertime', 20.84),
(2526, 176, 'Deduction', 'Withholding Tax', 2557.53),
(2527, 177, 'Allowance', 'Rice Subsidy', 2500.00),
(2528, 177, 'Allowance', 'Meal Allowance', 2000.00),
(2529, 177, 'Allowance', 'Laundry Allowance', 400.00),
(2530, 177, 'Allowance', 'Travel Allowance', 3500.00),
(2531, 177, 'Allowance', 'Communication Allowance', 1200.00),
(2532, 177, 'Deduction', 'SSS Regular (EE)', 500.00),
(2533, 177, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2534, 177, 'Deduction', 'SSS WISP (EE)', 225.00),
(2535, 177, 'Deduction', 'SSS WISP (ER)', 450.00),
(2536, 177, 'Deduction', 'PhilHealth (EE)', 362.50),
(2537, 177, 'Deduction', 'PhilHealth (ER)', 362.50),
(2538, 177, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2539, 177, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2540, 177, 'Deduction', 'Withholding Tax', 1226.70),
(2541, 178, 'Allowance', 'Rice Subsidy', 2500.00),
(2542, 178, 'Allowance', 'Meal Allowance', 3000.00),
(2543, 178, 'Allowance', 'Laundry Allowance', 400.00),
(2544, 178, 'Allowance', 'Travel Allowance', 7000.00),
(2545, 178, 'Allowance', 'Communication Allowance', 2000.00),
(2546, 178, 'Deduction', 'SSS Regular (EE)', 500.00),
(2547, 178, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2548, 178, 'Deduction', 'SSS WISP (EE)', 375.00),
(2549, 178, 'Deduction', 'SSS WISP (ER)', 750.00),
(2550, 178, 'Deduction', 'PhilHealth (EE)', 675.00),
(2551, 178, 'Deduction', 'PhilHealth (ER)', 675.00),
(2552, 178, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2553, 178, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2554, 178, 'Deduction', 'Withholding Tax', 4164.20),
(2555, 179, 'Allowance', 'Rice Subsidy', 2500.00),
(2556, 179, 'Allowance', 'Meal Allowance', 2000.00),
(2557, 179, 'Allowance', 'Laundry Allowance', 400.00),
(2558, 179, 'Allowance', 'Travel Allowance', 3500.00),
(2559, 179, 'Allowance', 'Communication Allowance', 1200.00),
(2560, 179, 'Deduction', 'SSS Regular (EE)', 500.00),
(2561, 179, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2562, 179, 'Deduction', 'SSS WISP (EE)', 225.00),
(2563, 179, 'Deduction', 'SSS WISP (ER)', 450.00),
(2564, 179, 'Deduction', 'PhilHealth (EE)', 362.50),
(2565, 179, 'Deduction', 'PhilHealth (ER)', 362.50),
(2566, 179, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2567, 179, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2568, 179, 'Deduction', 'Withholding Tax', 1226.70),
(2569, 180, 'Allowance', 'Rice Subsidy', 2500.00),
(2570, 180, 'Allowance', 'Meal Allowance', 2000.00),
(2571, 180, 'Allowance', 'Laundry Allowance', 400.00),
(2572, 180, 'Allowance', 'Travel Allowance', 3500.00),
(2573, 180, 'Allowance', 'Communication Allowance', 1200.00),
(2574, 180, 'Deduction', 'SSS Regular (EE)', 500.00),
(2575, 180, 'Deduction', 'SSS Regular (ER)', 1000.00),
(2576, 180, 'Deduction', 'SSS WISP (EE)', 225.00),
(2577, 180, 'Deduction', 'SSS WISP (ER)', 450.00),
(2578, 180, 'Deduction', 'PhilHealth (EE)', 362.50),
(2579, 180, 'Deduction', 'PhilHealth (ER)', 362.50),
(2580, 180, 'Deduction', 'Pag-IBIG (EE)', 100.00),
(2581, 180, 'Deduction', 'Pag-IBIG (ER)', 100.00),
(2582, 180, 'Deduction', 'Withholding Tax', 1226.70);

-- --------------------------------------------------------

--
-- Table structure for table `payroll_tax_simulation`
--

CREATE TABLE `payroll_tax_simulation` (
  `employee_id` int(11) NOT NULL,
  `tax_monthly` decimal(15,2) NOT NULL DEFAULT 0.00,
  `expected_monthly_net` decimal(15,2) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `philhealth_settings`
--

CREATE TABLE `philhealth_settings` (
  `period_id` int(11) NOT NULL,
  `employee_share_pct` decimal(5,2) DEFAULT NULL,
  `employer_share_pct` decimal(5,2) DEFAULT NULL,
  `salary_ceiling` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `philhealth_settings`
--

INSERT INTO `philhealth_settings` (`period_id`, `employee_share_pct`, `employer_share_pct`, `salary_ceiling`) VALUES
(1, 2.50, 2.50, 100000.00);

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `PositionID` int(11) NOT NULL,
  `PositionName` varchar(100) NOT NULL,
  `JobDescription` text DEFAULT NULL,
  `PositionCode` varchar(10) DEFAULT NULL,
  `DepartmentID` int(11) DEFAULT NULL,
  `SalaryGradeID` int(11) DEFAULT NULL,
  `AuthorizedHeadcount` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`PositionID`, `PositionName`, `JobDescription`, `PositionCode`, `DepartmentID`, `SalaryGradeID`, `AuthorizedHeadcount`) VALUES
(1, 'Administrator', 'Responsible for overall administrative operations, coordinating internal processes, and ensuring organizational policies are followed.', 'ADM', 1, 6, 2),
(2, 'HR Data Specialist', 'Manages employee data, HR documentation, and supports HR operations including employee records, reports, and compliance tasks.', 'HRDS', 2, 2, 1),
(3, 'HR Manager', 'Oversees HR department functions including recruitment, employee relations, policy implementation, and performance management.', 'HRM', 2, 5, 1),
(4, 'HR Staff', 'Supports HR operations by assisting in recruitment activities, employee onboarding, documentation, and employee services.', 'HRS', 2, 1, 1),
(5, 'Compensation Analyst', 'Analyzes employee compensation structures, salary adjustments, and benefits programs to ensure fair and competitive compensation practices.', 'CA', 2, 4, 1),
(6, 'Payroll Processor', 'Processes payroll transactions, calculates employee salaries and deductions, and ensures payroll accuracy and compliance with financial policies.', 'PAY', 2, 2, 1),
(7, 'Supervisor', 'Supervises operational staff, monitors workflow efficiency, and ensures departmental objectives and service standards are achieved.', 'SV', 2, 4, 1),
(8, 'Finance Manager', 'Responsible for managing financial reporting, budgeting, and financial analysis to support business decision making.', 'FIN-MGR', 3, 5, 1),
(10, 'Logistics Officer', 'Coordinates logistics activities including inventory management, transportation scheduling, and supply chain operations.', 'LOG-OFF', 4, 3, 2),
(12, 'Loan Service Associates', 'Supports loan processing activities including documentation review, customer service assistance, and loan record management.', 'LSA', 5, 6, 1),
(13, 'Loan Officers', 'Evaluates loan applications, performs credit assessments, and manages borrower relationships to ensure responsible lending.', 'L-OFF', 5, 3, 5),
(15, 'Logistic Manager', 'Manages logistics operations, supervises logistics personnel, and ensures efficient movement of goods and materials.', 'LM', 4, 5, 1),
(16, 'HR Officer', 'Handles HR operations including recruitment coordination, employee relations, and HR policy implementation.', 'HRO', 2, 3, 1),
(26, 'SYSTEM ADMINISTRATOR', '0', 'FACT', 1, 1, 2),
(27, 'example', 'testing', 'example', 1, 6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `position_competencies`
--

CREATE TABLE `position_competencies` (
  `id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  `competency_id` int(11) NOT NULL,
  `level_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `position_competencies`
--

INSERT INTO `position_competencies` (`id`, `position_id`, `competency_id`, `level_id`, `created_at`) VALUES
(1, 3, 1, 3, '2026-03-13 17:02:44'),
(2, 3, 2, 3, '2026-03-13 17:02:44'),
(3, 3, 13, 3, '2026-03-13 17:02:44'),
(4, 3, 14, 2, '2026-03-13 17:02:44'),
(5, 4, 1, 2, '2026-03-13 17:02:44'),
(6, 4, 2, 2, '2026-03-13 17:02:44'),
(7, 4, 15, 2, '2026-03-13 17:02:44'),
(8, 8, 1, 3, '2026-03-13 17:02:44'),
(9, 8, 20, 3, '2026-03-13 17:02:44'),
(10, 8, 6, 2, '2026-03-13 17:02:44'),
(11, 10, 1, 2, '2026-03-13 17:02:44'),
(12, 10, 2, 2, '2026-03-13 17:02:44'),
(13, 10, 6, 2, '2026-03-13 17:02:44'),
(14, 13, 1, 2, '2026-03-13 17:02:44'),
(15, 13, 2, 2, '2026-03-13 17:02:44'),
(16, 13, 6, 3, '2026-03-13 17:02:44'),
(18, 5, 9, 1, '2026-03-13 17:55:42'),
(19, 5, 7, 1, '2026-03-13 17:55:42'),
(20, 5, 10, 1, '2026-03-13 17:55:42'),
(21, 5, 1, 1, '2026-03-13 17:55:42'),
(22, 5, 11, 1, '2026-03-13 17:55:42'),
(23, 5, 8, 1, '2026-03-13 17:55:42'),
(24, 5, 12, 1, '2026-03-13 17:55:42'),
(25, 5, 15, 1, '2026-03-13 17:55:42'),
(26, 5, 19, 1, '2026-03-13 17:55:42'),
(27, 5, 16, 1, '2026-03-13 17:55:42'),
(28, 5, 3, 1, '2026-03-13 17:55:42'),
(29, 5, 14, 1, '2026-03-13 17:55:42'),
(30, 5, 17, 1, '2026-03-13 17:55:42'),
(31, 5, 6, 1, '2026-03-13 17:55:42'),
(32, 5, 4, 1, '2026-03-13 17:55:42'),
(33, 5, 13, 1, '2026-03-13 17:55:42'),
(34, 5, 2, 1, '2026-03-13 17:55:42'),
(35, 5, 5, 1, '2026-03-13 17:55:42'),
(36, 5, 18, 1, '2026-03-13 17:55:42'),
(37, 1, 9, 1, '2026-03-13 18:10:15'),
(38, 1, 42, 1, '2026-03-13 18:10:15'),
(39, 2, 7, 2, '2026-03-14 00:49:44'),
(40, 2, 9, 1, '2026-03-14 00:49:44'),
(41, 2, 10, 3, '2026-03-14 00:49:44'),
(42, 16, 7, 1, '2026-03-14 00:50:42'),
(43, 16, 9, 1, '2026-03-14 00:50:42'),
(44, 16, 10, 1, '2026-03-14 00:50:42'),
(48, 12, 9, 1, '2026-03-14 00:51:24'),
(49, 15, 7, 1, '2026-03-14 00:51:51'),
(50, 15, 9, 1, '2026-03-14 00:51:51'),
(51, 7, 7, 1, '2026-03-14 00:52:11'),
(52, 7, 9, 1, '2026-03-14 00:52:11'),
(53, 6, 7, 2, '2026-03-14 00:52:30'),
(54, 6, 9, 1, '2026-03-14 00:52:30'),
(76, 26, 41, 1, '2026-03-15 06:41:53'),
(77, 26, 42, 1, '2026-03-15 06:41:53'),
(78, 26, 43, 1, '2026-03-15 06:41:53'),
(79, 26, 44, 1, '2026-03-15 06:41:53'),
(80, 26, 45, 1, '2026-03-15 06:41:53'),
(81, 27, 41, 1, '2026-03-15 15:38:50'),
(82, 27, 42, 2, '2026-03-15 15:38:50'),
(83, 27, 43, 1, '2026-03-15 15:38:50'),
(84, 27, 44, 1, '2026-03-15 15:38:50'),
(85, 27, 45, 1, '2026-03-15 15:38:50');

-- --------------------------------------------------------

--
-- Table structure for table `position_requests`
--

CREATE TABLE `position_requests` (
  `RequestID` int(11) NOT NULL,
  `RequestType` enum('Add','Update','Delete') DEFAULT 'Add',
  `TargetPositionID` int(11) DEFAULT NULL,
  `PositionName` varchar(255) NOT NULL,
  `JobDescription` text DEFAULT NULL,
  `PositionCode` varchar(50) DEFAULT NULL,
  `DepartmentID` int(11) NOT NULL,
  `SalaryGradeID` int(11) NOT NULL,
  `AuthorizedHeadcount` int(11) DEFAULT 1,
  `Status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `RequestedBy` varchar(100) DEFAULT NULL,
  `DateRequested` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `position_requests`
--

INSERT INTO `position_requests` (`RequestID`, `RequestType`, `TargetPositionID`, `PositionName`, `JobDescription`, `PositionCode`, `DepartmentID`, `SalaryGradeID`, `AuthorizedHeadcount`, `Status`, `RequestedBy`, `DateRequested`) VALUES
(1, 'Add', NULL, 'Logistic Manager', NULL, 'LM', 4, 5, 1, 'Approved', 'Red Gin Baldon', '2026-03-07 21:26:37'),
(2, 'Delete', 14, 'HR Officer', NULL, 'HRO', 2, 3, 1, 'Approved', 'Red Gin Baldon', '2026-03-08 02:50:34'),
(3, 'Update', 11, 'Inventory Clerk', NULL, 'INV-CLK', 4, 2, 4, 'Rejected', 'Red Gin Baldon', '2026-03-08 02:51:23'),
(4, 'Add', NULL, 'HR Officer', NULL, 'HRO', 2, 3, 1, 'Rejected', 'Red Gin Baldon', '2026-03-09 14:58:36'),
(5, 'Add', NULL, 'HR Officer', NULL, 'HRO', 2, 3, 1, 'Approved', 'Red Gin Baldon', '2026-03-09 14:59:18'),
(6, 'Add', NULL, 'Faculty', NULL, 'F-1234', 2, 4, 1, 'Approved', 'Red Gin Baldon', '2026-03-12 03:03:00'),
(7, 'Delete', 18, 'Senior Developer', NULL, 'SD', 1, 4, 1, 'Approved', 'Red Gin Baldon', '2026-03-12 18:06:56'),
(8, 'Delete', 19, 'SENIOR DEVELOPER', NULL, 'SD', 1, 4, 1, 'Approved', 'Red Gin Baldon', '2026-03-12 18:07:54'),
(9, 'Delete', 18, 'Senior Developer', NULL, 'SD', 1, 4, 1, 'Approved', 'Red Gin Baldon', '2026-03-12 18:07:59'),
(10, 'Delete', 18, 'Senior Developer', NULL, 'SD', 1, 4, 1, 'Approved', 'Red Gin Baldon', '2026-03-12 18:14:06'),
(11, 'Delete', 19, 'SENIOR DEVELOPER', NULL, 'SD', 1, 4, 1, 'Approved', 'Red Gin Baldon', '2026-03-12 18:14:13'),
(12, 'Delete', 17, 'Faculty', NULL, 'F-1234', 2, 4, 1, 'Rejected', 'Red Gin Baldon', '2026-03-12 18:15:56'),
(13, 'Update', 26, 'SYSTEM ADMINISTRATOR', 'Manages an organization\'s IT infrastructure, including servers, networks, and software, ensuring they run securely, efficiently, and with high uptime.', 'FACT', 1, 1, 2, 'Rejected', 'Red Gin Baldon', '2026-03-15 06:59:35');

-- --------------------------------------------------------

--
-- Table structure for table `recruitment_requisitions`
--

CREATE TABLE `recruitment_requisitions` (
  `RequisitionID` int(11) NOT NULL,
  `PositionID` int(11) NOT NULL,
  `RequestedBy` varchar(100) NOT NULL,
  `Status` enum('Pending','Active','Posted','Closed','Cancelled') DEFAULT 'Pending',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recruitment_requisitions`
--

INSERT INTO `recruitment_requisitions` (`RequisitionID`, `PositionID`, `RequestedBy`, `Status`, `CreatedAt`) VALUES
(1, 13, 'Red Gin Baldon', 'Posted', '2026-03-08 04:47:32'),
(2, 15, 'Red Gin Baldon', 'Posted', '2026-03-09 05:49:26'),
(3, 12, 'Red Gin Baldon', 'Cancelled', '2026-03-09 09:29:53'),
(4, 16, 'Red Gin Baldon', 'Posted', '2026-03-09 15:04:57'),
(6, 10, 'Noriel Dimailig', 'Cancelled', '2026-03-10 00:57:06'),
(8, 8, 'Noriel Dimailig', 'Cancelled', '2026-03-10 01:03:33'),
(9, 10, 'Noriel Dimailig', 'Cancelled', '2026-03-10 01:21:05'),
(10, 10, 'Noriel Dimailig', 'Cancelled', '2026-03-10 03:10:49'),
(11, 12, 'Noriel Dimailig', 'Posted', '2026-03-10 03:18:45'),
(12, 10, 'Noriel Dimailig', 'Cancelled', '2026-03-11 11:04:49'),
(13, 10, 'Red Gin Baldon', 'Posted', '2026-03-12 00:30:02'),
(14, 8, 'Red Gin Baldon', 'Posted', '2026-03-12 03:01:59'),
(20, 26, 'Red Gin Baldon', 'Posted', '2026-03-15 06:42:16'),
(21, 27, 'Red Gin Baldon', 'Posted', '2026-03-15 15:39:29');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `RoleID` int(11) NOT NULL,
  `RoleName` varchar(50) NOT NULL,
  `Description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`RoleID`, `RoleName`, `Description`) VALUES
(1, 'Administrator', 'System Administrator with full access'),
(2, 'HR Manager', 'Oversees the implementation, data integrity, and daily operation of Human Resources Information Systems'),
(3, 'HR Data Specialist', 'maintains, cleanses, and analyzes employee information'),
(4, 'HR Staff', 'provide essential operational support by managing the employee lifecycle, including recruiting, onboarding, payroll administration, and record-keeping'),
(5, 'Compensation Analyst', 'Professional who researches, analyzes, and designs employee pay structures (salaries, bonuses, benefits) to ensure internal fairness and external market competitiveness'),
(6, 'Payroll Processor', 'A professional or software system responsible for accurately calculating employee wages, managing tax withholdings, and ensuring on-time pay distribution'),
(7, 'Supervisor', 'A frontline manager responsible for leading a team, overseeing daily operations, and ensuring work aligns with company goals'),
(8, 'Financial Officer', 'Is responsible for managing an organization\'s financial health by overseeing budgets, monitoring daily transactions, preparing financial reports, and ensuring compliance with regulations'),
(9, 'Loan Officers', 'Use a process called underwriting to assess whether applicants qualify for loans'),
(10, 'Loan Service Associates', 'Perform routine administrative, transactional, operational, and customer support tasks'),
(11, 'Inventory Clerk', 'Record inventory for a company so that items are accurately stocked and stored where they belong'),
(12, 'Logistics Officer', 'Plan and coordinate the logistics operations, including procurement, transportation, warehousing, and distribution activities'),
(13, 'Logistic Manager', 'A professional responsible for overseeing the storage, transportation, and distribution of goods within a supply chain'),
(14, 'Accountant job', 'prepare and examine financial records, identify potential areas of opportunity and risk, and provide solutions for businesses and individuals'),
(15, 'Finance Manager', 'Prepare financial statements, business activity reports, and forecasts'),
(16, 'HR Officer', 'Represent a company\'s policies, procedures and goals, and many of their tasks revolve around instilling these values in employees, whilst making sure the policies are also fair to the employees.');

-- --------------------------------------------------------

--
-- Table structure for table `salary_grades`
--

CREATE TABLE `salary_grades` (
  `SalaryGradeID` int(11) NOT NULL,
  `period_id` int(11) DEFAULT NULL,
  `GradeLevel` varchar(10) NOT NULL,
  `GradeName` varchar(100) DEFAULT NULL,
  `MinSalary` decimal(15,2) NOT NULL,
  `MaxSalary` decimal(15,2) NOT NULL,
  `MidSalary` decimal(15,2) GENERATED ALWAYS AS ((`MinSalary` + `MaxSalary`) / 2) STORED,
  `Currency` varchar(10) DEFAULT 'PHP',
  `IsActive` tinyint(1) DEFAULT 1,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `Description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `salary_grades`
--

INSERT INTO `salary_grades` (`SalaryGradeID`, `period_id`, `GradeLevel`, `GradeName`, `MinSalary`, `MaxSalary`, `Currency`, `IsActive`, `CreatedAt`, `UpdatedAt`, `Description`) VALUES
(1, 1, 'SG-1', 'Entry Level', 16000.00, 20000.00, 'PHP', 1, '2026-02-23 08:35:28', '2026-03-04 04:58:20', 'Entry Support (HR Staff, Finance Assistants)'),
(2, 1, 'SG-2', 'Professional I', 25000.00, 30000.00, 'PHP', 1, '2026-02-23 08:35:28', '2026-03-17 02:47:06', 'Professional I (Payroll Processor, HR Data Specialist)'),
(3, 1, 'SG-3', 'Professional II', 29000.00, 42000.00, 'PHP', 1, '2026-02-23 08:35:28', '2026-03-04 04:58:20', 'Professional II (HR Analyst, Finance Officer)'),
(4, 1, 'SG-4', 'Senior Associate\n', 41000.00, 55000.00, 'PHP', 1, '2026-02-23 08:35:28', '2026-03-04 04:58:20', 'Senior Specialist (Compensation Analyst, Senior Finance)'),
(5, 1, 'SG-5', 'Manager', 54000.00, 75000.00, 'PHP', 1, '2026-02-23 08:35:28', '2026-03-04 04:58:20', 'Management (HR Manager, Finance Manager)'),
(6, 1, 'SG-6', 'Executive', 81000.00, 120000.00, 'PHP', 1, '2026-02-23 08:35:28', '2026-03-16 18:39:35', 'Executive (Administrator, Director)');

-- --------------------------------------------------------

--
-- Table structure for table `salary_grade_proposals`
--

CREATE TABLE `salary_grade_proposals` (
  `ProposalID` int(11) NOT NULL,
  `BatchReference` varchar(50) DEFAULT NULL,
  `SalaryGradeID` int(11) NOT NULL,
  `ProposedMinSalary` decimal(15,2) NOT NULL,
  `ProposedMaxSalary` decimal(15,2) NOT NULL,
  `Reason` text NOT NULL,
  `ProposedBy` int(11) DEFAULT NULL,
  `Status` enum('Pending','Endorsed','Manager Approved','Applied','Rejected') DEFAULT 'Pending',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `proof_file_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `salary_grade_proposals`
--

INSERT INTO `salary_grade_proposals` (`ProposalID`, `BatchReference`, `SalaryGradeID`, `ProposedMinSalary`, `ProposedMaxSalary`, `Reason`, `ProposedBy`, `Status`, `CreatedAt`, `UpdatedAt`, `proof_file_url`) VALUES
(1, 'batch_migration_1', 1, 16000.00, 20000.00, 'wala lang', 7, 'Applied', '2026-03-01 17:36:00', '2026-03-02 05:41:21', NULL),
(2, 'batch_migration_1', 2, 21000.00, 30000.00, 'wala lang', 7, 'Applied', '2026-03-01 17:36:00', '2026-03-02 05:41:29', NULL),
(3, 'batch_migration_1', 3, 28000.00, 42000.00, 'wala lang', 7, 'Applied', '2026-03-01 17:36:00', '2026-03-02 05:41:33', NULL),
(4, 'batch_migration_1', 4, 40000.00, 55000.00, 'wala lang', 7, 'Applied', '2026-03-01 17:36:00', '2026-03-02 05:41:39', NULL),
(5, 'batch_migration_1', 5, 53000.00, 75000.00, 'wala lang', 7, 'Applied', '2026-03-01 17:36:00', '2026-03-02 05:41:44', NULL),
(6, 'batch_migration_1', 6, 80000.00, 120000.00, 'wala lang', 7, 'Applied', '2026-03-01 17:36:00', '2026-03-02 05:41:49', NULL),
(7, 'batch_69a5258b092d11.14308175', 1, 15000.00, 20000.00, 'try', 7, 'Rejected', '2026-03-02 05:52:11', '2026-03-02 05:56:19', NULL),
(8, 'batch_69a52733dabe87.45660111', 1, 15000.00, 20000.00, 'test', 7, 'Rejected', '2026-03-02 05:59:15', '2026-03-04 04:43:37', NULL),
(9, 'batch_69a52733dabe87.45660111', 2, 20000.00, 30000.00, 'test', 7, 'Rejected', '2026-03-02 05:59:15', '2026-03-04 04:43:37', NULL),
(10, 'batch_69a5746ded85e7.07419751', 1, 15000.00, 20000.00, 'try', 7, 'Pending', '2026-03-02 11:28:45', '2026-03-02 11:28:45', NULL),
(11, 'batch_69a70b3f29d693.72011293', 1, 15000.00, 20000.00, 'test', 7, 'Pending', '2026-03-03 16:24:31', '2026-03-03 16:24:31', NULL),
(12, 'batch_69a7165063a405.89337660', 1, 16001.00, 20000.00, 'test', 7, 'Pending', '2026-03-03 17:11:44', '2026-03-03 17:11:44', NULL),
(13, 'batch_69a79325dcb7d5.30120775', 1, 14000.00, 20000.00, 'test', 7, 'Applied', '2026-03-04 02:04:21', '2026-03-04 03:21:42', NULL),
(14, 'batch_69a795b3427832.47686676', 1, 14000.00, 20000.00, 'test2', 7, 'Applied', '2026-03-04 02:15:15', '2026-03-04 03:26:01', NULL),
(15, 'batch_69a7a58ea81c90.34034724', 1, 15000.00, 20000.00, 'test', 7, 'Applied', '2026-03-04 03:22:54', '2026-03-04 03:28:02', NULL),
(16, 'batch_69a7a58ea81c90.34034724', 2, 20000.00, 30000.00, 'test', 7, 'Applied', '2026-03-04 03:22:54', '2026-03-04 03:28:02', NULL),
(17, 'batch_69a7b8abcfdda8.01844093', 1, 16000.00, 20000.00, 'TEST', 7, 'Applied', '2026-03-04 04:44:27', '2026-03-04 04:58:20', NULL),
(18, 'batch_69a7b8abcfdda8.01844093', 2, 21000.00, 30000.00, 'TEST', 7, 'Applied', '2026-03-04 04:44:27', '2026-03-04 04:58:20', NULL),
(19, 'batch_69a7b8abcfdda8.01844093', 3, 29000.00, 42000.00, 'TEST', 7, 'Applied', '2026-03-04 04:44:27', '2026-03-04 04:58:20', NULL),
(20, 'batch_69a7b8abcfdda8.01844093', 4, 41000.00, 55000.00, 'TEST', 7, 'Applied', '2026-03-04 04:44:27', '2026-03-04 04:58:20', NULL),
(21, 'batch_69a7b8abcfdda8.01844093', 5, 54000.00, 75000.00, 'TEST', 7, 'Applied', '2026-03-04 04:44:27', '2026-03-04 04:58:20', NULL),
(22, 'batch_69a7b8abcfdda8.01844093', 6, 81000.00, 120000.00, 'TEST', 7, 'Applied', '2026-03-04 04:44:27', '2026-03-04 04:58:20', NULL),
(23, 'batch_69a8fa843ac4b4.03580821', 1, 17000.00, 20000.00, 'test', 7, 'Pending', '2026-03-05 03:37:40', '2026-03-05 03:37:40', NULL),
(24, 'batch_69aed3baca4855.62920620', 1, 15000.00, 20000.00, 'test', 7, 'Pending', '2026-03-09 14:05:46', '2026-03-09 14:05:46', 'uploads/proofs/proof_69aed3bac96dc0.44401257.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `simulation_drafts`
--

CREATE TABLE `simulation_drafts` (
  `DraftID` int(11) NOT NULL,
  `CycleName` varchar(100) NOT NULL,
  `period_id` int(11) NOT NULL,
  `ProposedBy` int(11) DEFAULT NULL,
  `BudgetUsedPct` decimal(5,2) DEFAULT 0.00,
  `TotalBudget` decimal(15,2) DEFAULT 0.00,
  `TotalCost` decimal(15,2) DEFAULT 0.00,
  `DateStarted` datetime DEFAULT current_timestamp(),
  `LastSaved` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `EmployeeData` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`EmployeeData`)),
  `Status` varchar(50) DEFAULT 'Draft',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `SalaryScaleData` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `simulation_drafts`
--

INSERT INTO `simulation_drafts` (`DraftID`, `CycleName`, `period_id`, `ProposedBy`, `BudgetUsedPct`, `TotalBudget`, `TotalCost`, `DateStarted`, `LastSaved`, `EmployeeData`, `Status`, `CreatedAt`, `UpdatedAt`, `SalaryScaleData`) VALUES
(1, 'FY2025 Cycle', 1, NULL, 0.00, 5000000.00, 0.00, '2026-03-04 22:34:08', '2026-03-04 23:37:07', '[{\"EmployeeID\":\"1\",\"PropPct\":3,\"PropAmt\":2400,\"GradeID\":\"6\"},{\"EmployeeID\":\"2\",\"PropPct\":4,\"PropAmt\":3200,\"GradeID\":\"6\"},{\"EmployeeID\":\"7\",\"PropPct\":4,\"PropAmt\":1600,\"GradeID\":\"4\"},{\"EmployeeID\":\"3\",\"PropPct\":2,\"PropAmt\":420,\"GradeID\":\"2\"},{\"EmployeeID\":\"6\",\"PropPct\":4,\"PropAmt\":2120,\"GradeID\":\"5\"},{\"EmployeeID\":\"4\",\"PropPct\":3,\"PropAmt\":450,\"GradeID\":\"1\"},{\"EmployeeID\":\"8\",\"PropPct\":2,\"PropAmt\":420,\"GradeID\":\"3\"},{\"EmployeeID\":\"10\",\"PropPct\":2,\"PropAmt\":800,\"GradeID\":\"4\"}]', 'Draft', '2026-03-04 16:48:52', '2026-03-04 16:48:52', NULL),
(2, 'FY2026', 1, 2, 10.11, 5000000.00, 42140.00, '2026-03-05 01:01:03', '2026-03-17 20:59:06', '[{\"EmployeeID\":\"1\",\"FirstName\":\"Joshua\",\"LastName\":\"Suruiz\",\"EmployeeCode\":\"ADM20261001\",\"OldSalary\":92882,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":92882,\"GradeID\":\"6\",\"CompaRatio\":0},{\"EmployeeID\":\"2\",\"FirstName\":\"Red\",\"LastName\":\"Gin Baldon\",\"EmployeeCode\":\"ADM20261002\",\"OldSalary\":94694,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":94694,\"GradeID\":\"6\",\"CompaRatio\":0},{\"EmployeeID\":\"7\",\"FirstName\":\"Miguel\",\"LastName\":\"Padre\",\"EmployeeCode\":\"CA20261007\",\"OldSalary\":45676,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":45676,\"GradeID\":\"4\",\"CompaRatio\":0},{\"EmployeeID\":\"23\",\"FirstName\":\"denzel\",\"LastName\":\"Ortiz\",\"EmployeeCode\":\"example20260015\",\"OldSalary\":85000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":85000,\"GradeID\":\"6\",\"CompaRatio\":0},{\"EmployeeID\":\"20\",\"FirstName\":\"buya\",\"LastName\":\"buya\",\"EmployeeCode\":\"FACT20260014\",\"OldSalary\":16000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":16000,\"GradeID\":\"1\",\"CompaRatio\":0},{\"EmployeeID\":\"24\",\"FirstName\":\"Miguel\",\"LastName\":\"Padre\",\"EmployeeCode\":\"FACT20260016\",\"OldSalary\":16000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":16000,\"GradeID\":\"1\",\"CompaRatio\":0},{\"EmployeeID\":\"3\",\"FirstName\":\"Noriel\",\"LastName\":\"Dimailig\",\"EmployeeCode\":\"HRDS20261003\",\"OldSalary\":25500,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":25500,\"GradeID\":\"2\",\"CompaRatio\":0},{\"EmployeeID\":\"6\",\"FirstName\":\"Glory\",\"LastName\":\"Job\",\"EmployeeCode\":\"HRM20261006\",\"OldSalary\":60158,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":60158,\"GradeID\":\"5\",\"CompaRatio\":0},{\"EmployeeID\":\"16\",\"FirstName\":\"Johnmar\",\"LastName\":\"Solis\",\"EmployeeCode\":\"HRO20260011\",\"OldSalary\":29000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":29000,\"GradeID\":\"3\",\"CompaRatio\":0},{\"EmployeeID\":\"4\",\"FirstName\":\"Earl\",\"LastName\":\"Caber\",\"EmployeeCode\":\"HRS20261004\",\"OldSalary\":17146,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":17146,\"GradeID\":\"1\",\"CompaRatio\":0},{\"EmployeeID\":\"14\",\"FirstName\":\"Joshua\",\"LastName\":\"Suruiz\",\"EmployeeCode\":\"L-OFF20260009\",\"OldSalary\":29000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":29000,\"GradeID\":\"3\",\"CompaRatio\":0},{\"EmployeeID\":\"15\",\"FirstName\":\"test\",\"LastName\":\"three\",\"EmployeeCode\":\"LM20260010\",\"OldSalary\":54000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":54000,\"GradeID\":\"5\",\"CompaRatio\":0},{\"EmployeeID\":\"18\",\"FirstName\":\"Earl\",\"LastName\":\"Alarcon\",\"EmployeeCode\":\"LOG-OFF20260012\",\"OldSalary\":29000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":29000,\"GradeID\":\"3\",\"CompaRatio\":0},{\"EmployeeID\":\"19\",\"FirstName\":\"EARL\",\"LastName\":\"ALARCON\",\"EmployeeCode\":\"LSA20260013\",\"OldSalary\":85000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":85000,\"GradeID\":\"6\",\"CompaRatio\":0},{\"EmployeeID\":\"8\",\"FirstName\":\"Daniella\",\"LastName\":\"Magtangob\",\"EmployeeCode\":\"PAY20261008\",\"OldSalary\":29000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":29000,\"GradeID\":\"3\",\"CompaRatio\":0},{\"EmployeeID\":\"10\",\"FirstName\":\"Mike\",\"LastName\":\"Dabu\",\"EmployeeCode\":\"SV20261009\",\"OldSalary\":41000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":41000,\"GradeID\":\"4\",\"CompaRatio\":0}]', 'Sent to Finance', '2026-03-04 17:01:03', '2026-03-17 12:59:06', '[{\"SalaryGradeID\":\"1\",\"MinSalary\":16000,\"MaxSalary\":20000},{\"SalaryGradeID\":\"2\",\"MinSalary\":25000,\"MaxSalary\":30000},{\"SalaryGradeID\":\"3\",\"MinSalary\":29000,\"MaxSalary\":42000},{\"SalaryGradeID\":\"4\",\"MinSalary\":41000,\"MaxSalary\":55000},{\"SalaryGradeID\":\"5\",\"MinSalary\":54000,\"MaxSalary\":75000},{\"SalaryGradeID\":\"6\",\"MinSalary\":81000,\"MaxSalary\":120000}]'),
(3, 'Test Cycle 1773159792', 1, NULL, 10.50, 5000000.00, 500000.00, '2026-03-11 00:23:12', '2026-03-11 00:23:12', '[{\"EmployeeID\":1,\"PropPct\":5,\"PropAmt\":1000,\"GradeID\":1}]', 'Draft', '2026-03-10 16:23:12', '2026-03-10 16:23:12', NULL),
(4, 'TESTING', 1, 2, 10.11, 5000000.00, 42140.00, '2026-03-16 19:27:26', '2026-03-17 01:57:26', '[{\"EmployeeID\":\"1\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"2\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"7\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"4\"},{\"EmployeeID\":\"23\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"20\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"1\"},{\"EmployeeID\":\"24\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"1\"},{\"EmployeeID\":\"3\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"2\"},{\"EmployeeID\":\"6\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"5\"},{\"EmployeeID\":\"16\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"4\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"1\"},{\"EmployeeID\":\"14\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"15\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"5\"},{\"EmployeeID\":\"18\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"19\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"8\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"10\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"4\"}]', 'Draft', '2026-03-16 11:27:26', '2026-03-16 17:57:26', '[{\"SalaryGradeID\":\"1\",\"MinSalary\":16000,\"MaxSalary\":20000},{\"SalaryGradeID\":\"2\",\"MinSalary\":21000,\"MaxSalary\":30000},{\"SalaryGradeID\":\"3\",\"MinSalary\":29000,\"MaxSalary\":42000},{\"SalaryGradeID\":\"4\",\"MinSalary\":41000,\"MaxSalary\":55000},{\"SalaryGradeID\":\"5\",\"MinSalary\":54000,\"MaxSalary\":75000},{\"SalaryGradeID\":\"6\",\"MinSalary\":81000,\"MaxSalary\":120000}]'),
(5, 'SANAGUMANA', 1, 2, 10.11, 5000000.00, 42140.00, '2026-03-17 01:45:04', '2026-03-17 01:45:04', '[{\"EmployeeID\":\"1\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"2\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"7\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"4\"},{\"EmployeeID\":\"23\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"20\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"1\"},{\"EmployeeID\":\"24\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"1\"},{\"EmployeeID\":\"3\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"2\"},{\"EmployeeID\":\"6\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"5\"},{\"EmployeeID\":\"16\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"4\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"1\"},{\"EmployeeID\":\"14\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"15\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"5\"},{\"EmployeeID\":\"18\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"19\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"8\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"10\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"4\"}]', 'Draft', '2026-03-16 17:45:04', '2026-03-16 17:45:04', '[{\"SalaryGradeID\":\"1\",\"MinSalary\":16000,\"MaxSalary\":20000},{\"SalaryGradeID\":\"2\",\"MinSalary\":21000,\"MaxSalary\":30000},{\"SalaryGradeID\":\"3\",\"MinSalary\":29000,\"MaxSalary\":42000},{\"SalaryGradeID\":\"4\",\"MinSalary\":41000,\"MaxSalary\":55000},{\"SalaryGradeID\":\"5\",\"MinSalary\":54000,\"MaxSalary\":75000},{\"SalaryGradeID\":\"6\",\"MinSalary\":81000,\"MaxSalary\":120000}]'),
(6, 'TESTINGEEEEEEEEE', 1, 2, 10.11, 5000000.00, 42140.00, '2026-03-17 02:17:34', '2026-03-17 02:18:02', '[{\"EmployeeID\":\"1\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"2\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"7\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"4\"},{\"EmployeeID\":\"23\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"20\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"1\"},{\"EmployeeID\":\"24\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"1\"},{\"EmployeeID\":\"3\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"2\"},{\"EmployeeID\":\"6\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"5\"},{\"EmployeeID\":\"16\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"4\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"1\"},{\"EmployeeID\":\"14\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"15\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"5\"},{\"EmployeeID\":\"18\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"19\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"8\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"10\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"4\"}]', 'Sent to Finance', '2026-03-16 18:17:34', '2026-03-16 18:18:02', '[{\"SalaryGradeID\":\"1\",\"MinSalary\":16000,\"MaxSalary\":20000},{\"SalaryGradeID\":\"2\",\"MinSalary\":21000,\"MaxSalary\":30000},{\"SalaryGradeID\":\"3\",\"MinSalary\":29000,\"MaxSalary\":42000},{\"SalaryGradeID\":\"4\",\"MinSalary\":41000,\"MaxSalary\":55000},{\"SalaryGradeID\":\"5\",\"MinSalary\":54000,\"MaxSalary\":75000},{\"SalaryGradeID\":\"6\",\"MinSalary\":81000,\"MaxSalary\":120000}]'),
(7, 'TESTING2', 1, 2, 10.11, 5000000.00, 42140.00, '2026-03-17 02:31:46', '2026-03-17 02:32:09', '[{\"EmployeeID\":\"1\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"2\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"7\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"4\"},{\"EmployeeID\":\"23\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"20\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"1\"},{\"EmployeeID\":\"24\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"1\"},{\"EmployeeID\":\"3\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"2\"},{\"EmployeeID\":\"6\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"5\"},{\"EmployeeID\":\"16\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"4\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"1\"},{\"EmployeeID\":\"14\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"15\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"5\"},{\"EmployeeID\":\"18\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"19\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"8\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"10\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"4\"}]', 'Sent to Finance', '2026-03-16 18:31:46', '2026-03-16 18:32:09', '[{\"SalaryGradeID\":\"1\",\"MinSalary\":16000,\"MaxSalary\":20000},{\"SalaryGradeID\":\"2\",\"MinSalary\":21000,\"MaxSalary\":30000},{\"SalaryGradeID\":\"3\",\"MinSalary\":29000,\"MaxSalary\":42000},{\"SalaryGradeID\":\"4\",\"MinSalary\":41000,\"MaxSalary\":55000},{\"SalaryGradeID\":\"5\",\"MinSalary\":54000,\"MaxSalary\":75000},{\"SalaryGradeID\":\"6\",\"MinSalary\":81000,\"MaxSalary\":120000}]'),
(8, 'TESTING3', 1, 2, 10.11, 5000000.00, 42140.00, '2026-03-17 02:34:06', '2026-03-17 02:34:27', '[{\"EmployeeID\":\"1\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"2\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"7\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"4\"},{\"EmployeeID\":\"23\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"20\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"1\"},{\"EmployeeID\":\"24\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"1\"},{\"EmployeeID\":\"3\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"2\"},{\"EmployeeID\":\"6\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"5\"},{\"EmployeeID\":\"16\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"4\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"1\"},{\"EmployeeID\":\"14\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"15\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"5\"},{\"EmployeeID\":\"18\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"19\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"8\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"10\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"4\"}]', 'Sent to Finance', '2026-03-16 18:34:06', '2026-03-16 18:34:27', '[{\"SalaryGradeID\":\"1\",\"MinSalary\":16000,\"MaxSalary\":20000},{\"SalaryGradeID\":\"2\",\"MinSalary\":21000,\"MaxSalary\":30000},{\"SalaryGradeID\":\"3\",\"MinSalary\":29000,\"MaxSalary\":42000},{\"SalaryGradeID\":\"4\",\"MinSalary\":41000,\"MaxSalary\":55000},{\"SalaryGradeID\":\"5\",\"MinSalary\":54000,\"MaxSalary\":75000},{\"SalaryGradeID\":\"6\",\"MinSalary\":81000,\"MaxSalary\":120000}]'),
(9, 'TESTING4', 1, 2, 10.11, 5000000.00, 42140.00, '2026-03-17 02:36:35', '2026-03-17 02:38:47', '[{\"EmployeeID\":\"1\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"2\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"7\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"4\"},{\"EmployeeID\":\"23\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"20\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"1\"},{\"EmployeeID\":\"24\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"1\"},{\"EmployeeID\":\"3\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"2\"},{\"EmployeeID\":\"6\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"5\"},{\"EmployeeID\":\"16\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"4\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"1\"},{\"EmployeeID\":\"14\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"15\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"5\"},{\"EmployeeID\":\"18\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"19\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"8\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"10\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"4\"}]', 'Approved', '2026-03-16 18:36:35', '2026-03-16 18:38:47', '[{\"SalaryGradeID\":\"1\",\"MinSalary\":16000,\"MaxSalary\":20000},{\"SalaryGradeID\":\"2\",\"MinSalary\":21000,\"MaxSalary\":30000},{\"SalaryGradeID\":\"3\",\"MinSalary\":29000,\"MaxSalary\":42000},{\"SalaryGradeID\":\"4\",\"MinSalary\":41000,\"MaxSalary\":55000},{\"SalaryGradeID\":\"5\",\"MinSalary\":54000,\"MaxSalary\":75000},{\"SalaryGradeID\":\"6\",\"MinSalary\":81000,\"MaxSalary\":120000}]'),
(10, 'testingggggggggg', 1, 2, 10.11, 5000000.00, 42140.00, '2026-03-17 10:41:45', '2026-03-17 10:42:17', '[{\"EmployeeID\":\"1\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"2\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"7\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"4\"},{\"EmployeeID\":\"23\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"20\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"1\"},{\"EmployeeID\":\"24\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"1\"},{\"EmployeeID\":\"3\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"2\"},{\"EmployeeID\":\"6\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"5\"},{\"EmployeeID\":\"16\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"4\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"1\"},{\"EmployeeID\":\"14\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"15\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"5\"},{\"EmployeeID\":\"18\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"19\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"8\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"10\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"4\"}]', 'Sent to Finance', '2026-03-17 02:41:45', '2026-03-17 02:42:17', '[{\"SalaryGradeID\":\"1\",\"MinSalary\":16000,\"MaxSalary\":20000},{\"SalaryGradeID\":\"2\",\"MinSalary\":21000,\"MaxSalary\":30000},{\"SalaryGradeID\":\"3\",\"MinSalary\":29000,\"MaxSalary\":42000},{\"SalaryGradeID\":\"4\",\"MinSalary\":41000,\"MaxSalary\":55000},{\"SalaryGradeID\":\"5\",\"MinSalary\":54000,\"MaxSalary\":75000},{\"SalaryGradeID\":\"6\",\"MinSalary\":81000,\"MaxSalary\":120000}]'),
(11, 'testingssssssssss sanaaaa', 1, 2, 10.11, 5000000.00, 42140.00, '2026-03-17 10:46:20', '2026-03-17 10:47:06', '[{\"EmployeeID\":\"1\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"2\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"7\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"4\"},{\"EmployeeID\":\"23\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"20\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"1\"},{\"EmployeeID\":\"24\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"1\"},{\"EmployeeID\":\"3\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"2\"},{\"EmployeeID\":\"6\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"5\"},{\"EmployeeID\":\"16\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"4\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"1\"},{\"EmployeeID\":\"14\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"15\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"5\"},{\"EmployeeID\":\"18\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"19\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"6\"},{\"EmployeeID\":\"8\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"3\"},{\"EmployeeID\":\"10\",\"PropPct\":0,\"PropAmt\":0,\"GradeID\":\"4\"}]', 'Approved', '2026-03-17 02:46:20', '2026-03-17 02:47:06', '[{\"SalaryGradeID\":\"1\",\"MinSalary\":16000,\"MaxSalary\":20000},{\"SalaryGradeID\":\"2\",\"MinSalary\":21000,\"MaxSalary\":30000},{\"SalaryGradeID\":\"3\",\"MinSalary\":29000,\"MaxSalary\":42000},{\"SalaryGradeID\":\"4\",\"MinSalary\":41000,\"MaxSalary\":55000},{\"SalaryGradeID\":\"5\",\"MinSalary\":54000,\"MaxSalary\":75000},{\"SalaryGradeID\":\"6\",\"MinSalary\":81000,\"MaxSalary\":120000}]'),
(12, 'testingggg', 1, 2, 10.11, 5000000.00, 42140.00, '2026-03-17 18:07:23', '2026-03-17 18:07:23', '[{\"EmployeeID\":\"1\",\"FirstName\":\"Joshua\",\"LastName\":\"Suruiz\",\"EmployeeCode\":\"ADM20261001\",\"OldSalary\":92882,\"MarketAdjustment\":0,\"MeritPct\":2,\"IncreaseAmount\":0,\"NewSalary\":92882,\"GradeID\":\"6\",\"CompaRatio\":0},{\"EmployeeID\":\"2\",\"FirstName\":\"Red\",\"LastName\":\"Gin Baldon\",\"EmployeeCode\":\"ADM20261002\",\"OldSalary\":94694,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":94694,\"GradeID\":\"6\",\"CompaRatio\":0},{\"EmployeeID\":\"7\",\"FirstName\":\"Miguel\",\"LastName\":\"Padre\",\"EmployeeCode\":\"CA20261007\",\"OldSalary\":45676,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":45676,\"GradeID\":\"4\",\"CompaRatio\":0},{\"EmployeeID\":\"23\",\"FirstName\":\"denzel\",\"LastName\":\"Ortiz\",\"EmployeeCode\":\"example20260015\",\"OldSalary\":85000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":85000,\"GradeID\":\"6\",\"CompaRatio\":0},{\"EmployeeID\":\"20\",\"FirstName\":\"buya\",\"LastName\":\"buya\",\"EmployeeCode\":\"FACT20260014\",\"OldSalary\":16000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":16000,\"GradeID\":\"1\",\"CompaRatio\":0},{\"EmployeeID\":\"24\",\"FirstName\":\"Miguel\",\"LastName\":\"Padre\",\"EmployeeCode\":\"FACT20260016\",\"OldSalary\":16000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":16000,\"GradeID\":\"1\",\"CompaRatio\":0},{\"EmployeeID\":\"3\",\"FirstName\":\"Noriel\",\"LastName\":\"Dimailig\",\"EmployeeCode\":\"HRDS20261003\",\"OldSalary\":25500,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":25500,\"GradeID\":\"2\",\"CompaRatio\":0},{\"EmployeeID\":\"6\",\"FirstName\":\"Glory\",\"LastName\":\"Job\",\"EmployeeCode\":\"HRM20261006\",\"OldSalary\":60158,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":60158,\"GradeID\":\"5\",\"CompaRatio\":0},{\"EmployeeID\":\"16\",\"FirstName\":\"Johnmar\",\"LastName\":\"Solis\",\"EmployeeCode\":\"HRO20260011\",\"OldSalary\":29000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":29000,\"GradeID\":\"3\",\"CompaRatio\":0},{\"EmployeeID\":\"4\",\"FirstName\":\"Earl\",\"LastName\":\"Caber\",\"EmployeeCode\":\"HRS20261004\",\"OldSalary\":17146,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":17146,\"GradeID\":\"1\",\"CompaRatio\":0},{\"EmployeeID\":\"14\",\"FirstName\":\"Joshua\",\"LastName\":\"Suruiz\",\"EmployeeCode\":\"L-OFF20260009\",\"OldSalary\":29000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":29000,\"GradeID\":\"3\",\"CompaRatio\":0},{\"EmployeeID\":\"15\",\"FirstName\":\"test\",\"LastName\":\"three\",\"EmployeeCode\":\"LM20260010\",\"OldSalary\":54000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":54000,\"GradeID\":\"5\",\"CompaRatio\":0},{\"EmployeeID\":\"18\",\"FirstName\":\"Earl\",\"LastName\":\"Alarcon\",\"EmployeeCode\":\"LOG-OFF20260012\",\"OldSalary\":29000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":29000,\"GradeID\":\"3\",\"CompaRatio\":0},{\"EmployeeID\":\"19\",\"FirstName\":\"EARL\",\"LastName\":\"ALARCON\",\"EmployeeCode\":\"LSA20260013\",\"OldSalary\":85000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":85000,\"GradeID\":\"6\",\"CompaRatio\":0},{\"EmployeeID\":\"8\",\"FirstName\":\"Daniella\",\"LastName\":\"Magtangob\",\"EmployeeCode\":\"PAY20261008\",\"OldSalary\":29000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":29000,\"GradeID\":\"3\",\"CompaRatio\":0},{\"EmployeeID\":\"10\",\"FirstName\":\"Mike\",\"LastName\":\"Dabu\",\"EmployeeCode\":\"SV20261009\",\"OldSalary\":41000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":41000,\"GradeID\":\"4\",\"CompaRatio\":0}]', 'Draft', '2026-03-17 10:07:23', '2026-03-17 10:07:23', '[{\"SalaryGradeID\":\"1\",\"MinSalary\":16000,\"MaxSalary\":20000},{\"SalaryGradeID\":\"2\",\"MinSalary\":25000,\"MaxSalary\":30000},{\"SalaryGradeID\":\"3\",\"MinSalary\":29000,\"MaxSalary\":42000},{\"SalaryGradeID\":\"4\",\"MinSalary\":41000,\"MaxSalary\":55000},{\"SalaryGradeID\":\"5\",\"MinSalary\":54000,\"MaxSalary\":75000},{\"SalaryGradeID\":\"6\",\"MinSalary\":81000,\"MaxSalary\":120000}]'),
(13, 'testinggggggggggg', 1, 2, 10.11, 5000000.00, 42140.00, '2026-03-17 18:49:50', '2026-03-17 18:49:57', '[{\"EmployeeID\":\"1\",\"FirstName\":\"Joshua\",\"LastName\":\"Suruiz\",\"EmployeeCode\":\"ADM20261001\",\"OldSalary\":92882,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":92882,\"GradeID\":\"6\",\"CompaRatio\":0},{\"EmployeeID\":\"2\",\"FirstName\":\"Red\",\"LastName\":\"Gin Baldon\",\"EmployeeCode\":\"ADM20261002\",\"OldSalary\":94694,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":94694,\"GradeID\":\"6\",\"CompaRatio\":0},{\"EmployeeID\":\"7\",\"FirstName\":\"Miguel\",\"LastName\":\"Padre\",\"EmployeeCode\":\"CA20261007\",\"OldSalary\":45676,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":45676,\"GradeID\":\"4\",\"CompaRatio\":0},{\"EmployeeID\":\"23\",\"FirstName\":\"denzel\",\"LastName\":\"Ortiz\",\"EmployeeCode\":\"example20260015\",\"OldSalary\":85000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":85000,\"GradeID\":\"6\",\"CompaRatio\":0},{\"EmployeeID\":\"20\",\"FirstName\":\"buya\",\"LastName\":\"buya\",\"EmployeeCode\":\"FACT20260014\",\"OldSalary\":16000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":16000,\"GradeID\":\"1\",\"CompaRatio\":0},{\"EmployeeID\":\"24\",\"FirstName\":\"Miguel\",\"LastName\":\"Padre\",\"EmployeeCode\":\"FACT20260016\",\"OldSalary\":16000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":16000,\"GradeID\":\"1\",\"CompaRatio\":0},{\"EmployeeID\":\"3\",\"FirstName\":\"Noriel\",\"LastName\":\"Dimailig\",\"EmployeeCode\":\"HRDS20261003\",\"OldSalary\":25500,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":25500,\"GradeID\":\"2\",\"CompaRatio\":0},{\"EmployeeID\":\"6\",\"FirstName\":\"Glory\",\"LastName\":\"Job\",\"EmployeeCode\":\"HRM20261006\",\"OldSalary\":60158,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":60158,\"GradeID\":\"5\",\"CompaRatio\":0},{\"EmployeeID\":\"16\",\"FirstName\":\"Johnmar\",\"LastName\":\"Solis\",\"EmployeeCode\":\"HRO20260011\",\"OldSalary\":29000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":29000,\"GradeID\":\"3\",\"CompaRatio\":0},{\"EmployeeID\":\"4\",\"FirstName\":\"Earl\",\"LastName\":\"Caber\",\"EmployeeCode\":\"HRS20261004\",\"OldSalary\":17146,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":17146,\"GradeID\":\"1\",\"CompaRatio\":0},{\"EmployeeID\":\"14\",\"FirstName\":\"Joshua\",\"LastName\":\"Suruiz\",\"EmployeeCode\":\"L-OFF20260009\",\"OldSalary\":29000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":29000,\"GradeID\":\"3\",\"CompaRatio\":0},{\"EmployeeID\":\"15\",\"FirstName\":\"test\",\"LastName\":\"three\",\"EmployeeCode\":\"LM20260010\",\"OldSalary\":54000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":54000,\"GradeID\":\"5\",\"CompaRatio\":0},{\"EmployeeID\":\"18\",\"FirstName\":\"Earl\",\"LastName\":\"Alarcon\",\"EmployeeCode\":\"LOG-OFF20260012\",\"OldSalary\":29000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":29000,\"GradeID\":\"3\",\"CompaRatio\":0},{\"EmployeeID\":\"19\",\"FirstName\":\"EARL\",\"LastName\":\"ALARCON\",\"EmployeeCode\":\"LSA20260013\",\"OldSalary\":85000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":85000,\"GradeID\":\"6\",\"CompaRatio\":0},{\"EmployeeID\":\"8\",\"FirstName\":\"Daniella\",\"LastName\":\"Magtangob\",\"EmployeeCode\":\"PAY20261008\",\"OldSalary\":29000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":29000,\"GradeID\":\"3\",\"CompaRatio\":0},{\"EmployeeID\":\"10\",\"FirstName\":\"Mike\",\"LastName\":\"Dabu\",\"EmployeeCode\":\"SV20261009\",\"OldSalary\":41000,\"MarketAdjustment\":0,\"MeritPct\":0,\"IncreaseAmount\":0,\"NewSalary\":41000,\"GradeID\":\"4\",\"CompaRatio\":0}]', 'Sent to Finance', '2026-03-17 10:49:50', '2026-03-17 10:49:57', '[{\"SalaryGradeID\":\"1\",\"MinSalary\":16000,\"MaxSalary\":20000},{\"SalaryGradeID\":\"2\",\"MinSalary\":25000,\"MaxSalary\":30000},{\"SalaryGradeID\":\"3\",\"MinSalary\":29000,\"MaxSalary\":42000},{\"SalaryGradeID\":\"4\",\"MinSalary\":41000,\"MaxSalary\":55000},{\"SalaryGradeID\":\"5\",\"MinSalary\":54000,\"MaxSalary\":75000},{\"SalaryGradeID\":\"6\",\"MinSalary\":81000,\"MaxSalary\":120000}]');

-- --------------------------------------------------------

--
-- Table structure for table `simulation_proposals`
--

CREATE TABLE `simulation_proposals` (
  `ProposalID` int(11) NOT NULL,
  `CycleName` varchar(255) NOT NULL,
  `SalaryScaleData` longtext DEFAULT NULL,
  `PeriodID` int(11) NOT NULL,
  `DeptCode` varchar(50) DEFAULT NULL,
  `TotalBudget` decimal(15,2) DEFAULT 0.00,
  `TotalImpact` decimal(15,2) DEFAULT 0.00,
  `RemainingBudget` decimal(15,2) DEFAULT 0.00,
  `Status` varchar(50) DEFAULT 'Proposed',
  `FinanceRef` varchar(100) DEFAULT NULL,
  `ProposedBy` int(11) DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `simulation_proposals`
--

INSERT INTO `simulation_proposals` (`ProposalID`, `CycleName`, `SalaryScaleData`, `PeriodID`, `DeptCode`, `TotalBudget`, `TotalImpact`, `RemainingBudget`, `Status`, `FinanceRef`, `ProposedBy`, `CreatedAt`) VALUES
(1, 'FY2026', NULL, 1, 'GLOBAL', 5000000.00, 28260.00, 4660880.00, 'Proposed', NULL, 2, '2026-03-17 00:27:40'),
(2, 'FY2026', NULL, 1, 'GLOBAL', 5000000.00, 29070.00, 4651160.00, 'Proposed', NULL, 2, '2026-03-17 00:36:13'),
(3, 'FY2026', NULL, 1, 'GLOBAL', 5000000.00, 24370.00, 4707560.00, 'Proposed', NULL, 2, '2026-03-17 00:43:21'),
(4, 'FY2026', NULL, 1, 'GLOBAL', 5000000.00, 24370.00, 4707560.00, 'Proposed', NULL, 2, '2026-03-17 00:46:47'),
(5, 'FY2026', NULL, 1, 'GLOBAL', 5000000.00, 24370.00, 4707560.00, 'Proposed', NULL, 2, '2026-03-17 00:49:00'),
(6, 'FY2026', NULL, 1, 'GLOBAL', 5000000.00, 24370.00, 4707560.00, 'Proposed', NULL, 2, '2026-03-17 00:50:29'),
(7, 'FY2026', NULL, 1, 'GLOBAL', 5000000.00, 24370.00, 4707560.00, 'Proposed', NULL, 2, '2026-03-17 00:54:52'),
(8, 'FY2026', NULL, 1, 'GLOBAL', 5000000.00, 24370.00, 4707560.00, 'Proposed', NULL, 2, '2026-03-17 00:57:15'),
(9, 'FY2026', NULL, 1, 'GLOBAL', 5000000.00, 14000.00, 4832000.00, 'Proposed', NULL, 2, '2026-03-17 00:58:56'),
(10, 'FY2026', NULL, 1, 'GLOBAL', 5000000.00, 29070.00, 4651160.00, 'Proposed', NULL, 2, '2026-03-17 01:10:06'),
(11, 'SANAGUMANA', NULL, 1, 'GLOBAL', 5000000.00, 29070.00, 4651160.00, 'Proposed', NULL, 2, '2026-03-17 01:45:35'),
(12, 'FY2026', NULL, 1, 'GLOBAL', 5000000.00, 37289.00, 4552532.00, 'Proposed', NULL, 2, '2026-03-17 01:48:03'),
(13, 'FY2026', '[{\"SalaryGradeID\":\"1\",\"MinSalary\":18000,\"MaxSalary\":20000},{\"SalaryGradeID\":\"2\",\"MinSalary\":21000,\"MaxSalary\":30000},{\"SalaryGradeID\":\"3\",\"MinSalary\":29000,\"MaxSalary\":42000},{\"SalaryGradeID\":\"4\",\"MinSalary\":41000,\"MaxSalary\":55000},{\"SalaryGradeID\":\"5\",\"MinSalary\":54000,\"MaxSalary\":75000},{\"SalaryGradeID\":\"6\",\"MinSalary\":81000,\"MaxSalary\":120000}]', 1, 'GLOBAL', 5000000.00, 30430.00, 4634840.00, 'Proposed', NULL, 2, '2026-03-17 02:08:29'),
(14, 'TESTINGEEEEEEEEE', '[{\"SalaryGradeID\":\"1\",\"MinSalary\":19000,\"MaxSalary\":20000},{\"SalaryGradeID\":\"2\",\"MinSalary\":21000,\"MaxSalary\":30000},{\"SalaryGradeID\":\"3\",\"MinSalary\":29000,\"MaxSalary\":42000},{\"SalaryGradeID\":\"4\",\"MinSalary\":41000,\"MaxSalary\":55000},{\"SalaryGradeID\":\"5\",\"MinSalary\":54000,\"MaxSalary\":75000},{\"SalaryGradeID\":\"6\",\"MinSalary\":81000,\"MaxSalary\":120000}]', 1, 'GLOBAL', 5000000.00, 33460.00, 4598480.00, 'Proposed', NULL, 2, '2026-03-17 02:18:02'),
(15, 'TESTING2', '[{\"SalaryGradeID\":\"1\",\"MinSalary\":16000,\"MaxSalary\":20000},{\"SalaryGradeID\":\"2\",\"MinSalary\":21000,\"MaxSalary\":30000},{\"SalaryGradeID\":\"3\",\"MinSalary\":29000,\"MaxSalary\":42000},{\"SalaryGradeID\":\"4\",\"MinSalary\":41000,\"MaxSalary\":55000},{\"SalaryGradeID\":\"5\",\"MinSalary\":54000,\"MaxSalary\":75000},{\"SalaryGradeID\":\"6\",\"MinSalary\":85000,\"MaxSalary\":120000}]', 1, 'GLOBAL', 5000000.00, 40650.00, 4512200.00, 'Proposed', NULL, 2, '2026-03-17 02:32:09'),
(16, 'TESTING3', '[{\"SalaryGradeID\":\"1\",\"MinSalary\":16000,\"MaxSalary\":20000},{\"SalaryGradeID\":\"2\",\"MinSalary\":21000,\"MaxSalary\":30000},{\"SalaryGradeID\":\"3\",\"MinSalary\":29000,\"MaxSalary\":42000},{\"SalaryGradeID\":\"4\",\"MinSalary\":41000,\"MaxSalary\":55000},{\"SalaryGradeID\":\"5\",\"MinSalary\":54000,\"MaxSalary\":75000},{\"SalaryGradeID\":\"6\",\"MinSalary\":85000,\"MaxSalary\":120000}]', 1, 'GLOBAL', 5000000.00, 40650.00, 4512200.00, 'Proposed', NULL, 2, '2026-03-17 02:34:27'),
(17, 'TESTING4', '[{\"SalaryGradeID\":\"1\",\"MinSalary\":16000,\"MaxSalary\":20000},{\"SalaryGradeID\":\"2\",\"MinSalary\":21000,\"MaxSalary\":30000},{\"SalaryGradeID\":\"3\",\"MinSalary\":29000,\"MaxSalary\":42000},{\"SalaryGradeID\":\"4\",\"MinSalary\":41000,\"MaxSalary\":55000},{\"SalaryGradeID\":\"5\",\"MinSalary\":54000,\"MaxSalary\":75000},{\"SalaryGradeID\":\"6\",\"MinSalary\":85000,\"MaxSalary\":120000}]', 1, 'GLOBAL', 5000000.00, 40650.00, 4512200.00, 'Approved', 'ALLOC-2026-HR17', 2, '2026-03-17 02:36:45'),
(18, 'FY2026', '[{\"SalaryGradeID\":\"1\",\"MinSalary\":16000,\"MaxSalary\":20000},{\"SalaryGradeID\":\"2\",\"MinSalary\":21000,\"MaxSalary\":30000},{\"SalaryGradeID\":\"3\",\"MinSalary\":29000,\"MaxSalary\":42000},{\"SalaryGradeID\":\"4\",\"MinSalary\":41000,\"MaxSalary\":55000},{\"SalaryGradeID\":\"5\",\"MinSalary\":54000,\"MaxSalary\":75000},{\"SalaryGradeID\":\"6\",\"MinSalary\":81000,\"MaxSalary\":120000}]', 1, 'GLOBAL', 5000000.00, 10873.00, 4869524.00, 'Approved', 'ALLOC-2026-HR18', 2, '2026-03-17 02:39:15'),
(19, 'testingggggggggg', '[{\"SalaryGradeID\":\"1\",\"MinSalary\":16000,\"MaxSalary\":20000},{\"SalaryGradeID\":\"2\",\"MinSalary\":21000,\"MaxSalary\":30000},{\"SalaryGradeID\":\"3\",\"MinSalary\":29000,\"MaxSalary\":42000},{\"SalaryGradeID\":\"4\",\"MinSalary\":41000,\"MaxSalary\":55000},{\"SalaryGradeID\":\"5\",\"MinSalary\":54000,\"MaxSalary\":75000},{\"SalaryGradeID\":\"6\",\"MinSalary\":81000,\"MaxSalary\":120000}]', 1, 'GLOBAL', 5000000.00, 16615.00, 4800620.00, 'Proposed', NULL, 2, '2026-03-17 10:42:17'),
(20, 'testingssssssssss sanaaaa', '[{\"SalaryGradeID\":\"1\",\"MinSalary\":16000,\"MaxSalary\":20000},{\"SalaryGradeID\":\"2\",\"MinSalary\":25000,\"MaxSalary\":30000},{\"SalaryGradeID\":\"3\",\"MinSalary\":29000,\"MaxSalary\":42000},{\"SalaryGradeID\":\"4\",\"MinSalary\":41000,\"MaxSalary\":55000},{\"SalaryGradeID\":\"5\",\"MinSalary\":54000,\"MaxSalary\":75000},{\"SalaryGradeID\":\"6\",\"MinSalary\":81000,\"MaxSalary\":120000}]', 1, 'GLOBAL', 5000000.00, 12533.00, 4849604.00, 'Approved', 'ALLOC-2026-HR20', 2, '2026-03-17 10:46:43'),
(21, 'FY2026', '[{\"SalaryGradeID\":\"1\",\"MinSalary\":16000,\"MaxSalary\":20000},{\"SalaryGradeID\":\"2\",\"MinSalary\":25000,\"MaxSalary\":30000},{\"SalaryGradeID\":\"3\",\"MinSalary\":29000,\"MaxSalary\":42000},{\"SalaryGradeID\":\"4\",\"MinSalary\":41000,\"MaxSalary\":55000},{\"SalaryGradeID\":\"5\",\"MinSalary\":54000,\"MaxSalary\":75000},{\"SalaryGradeID\":\"6\",\"MinSalary\":81000,\"MaxSalary\":120000}]', 1, 'GLOBAL', 5000000.00, 10329.00, 4876052.00, 'Proposed', NULL, 2, '2026-03-17 18:11:30'),
(22, 'FY2026', '[{\"SalaryGradeID\":\"1\",\"MinSalary\":16000,\"MaxSalary\":20000},{\"SalaryGradeID\":\"2\",\"MinSalary\":25000,\"MaxSalary\":30000},{\"SalaryGradeID\":\"3\",\"MinSalary\":29000,\"MaxSalary\":42000},{\"SalaryGradeID\":\"4\",\"MinSalary\":41000,\"MaxSalary\":55000},{\"SalaryGradeID\":\"5\",\"MinSalary\":54000,\"MaxSalary\":75000},{\"SalaryGradeID\":\"6\",\"MinSalary\":81000,\"MaxSalary\":120000}]', 1, 'GLOBAL', 5000000.00, 8472.00, 4898336.00, 'Proposed', NULL, 2, '2026-03-17 18:26:17'),
(23, 'FY2026', '[{\"SalaryGradeID\":\"1\",\"MinSalary\":16000,\"MaxSalary\":20000},{\"SalaryGradeID\":\"2\",\"MinSalary\":25000,\"MaxSalary\":30000},{\"SalaryGradeID\":\"3\",\"MinSalary\":29000,\"MaxSalary\":42000},{\"SalaryGradeID\":\"4\",\"MinSalary\":41000,\"MaxSalary\":55000},{\"SalaryGradeID\":\"5\",\"MinSalary\":54000,\"MaxSalary\":75000},{\"SalaryGradeID\":\"6\",\"MinSalary\":81000,\"MaxSalary\":120000}]', 1, 'GLOBAL', 5000000.00, 8472.00, 4898336.00, 'Proposed', NULL, 2, '2026-03-17 18:41:55'),
(24, 'testinggggggggggg', '[{\"SalaryGradeID\":\"1\",\"MinSalary\":16000,\"MaxSalary\":20000},{\"SalaryGradeID\":\"2\",\"MinSalary\":25000,\"MaxSalary\":30000},{\"SalaryGradeID\":\"3\",\"MinSalary\":29000,\"MaxSalary\":42000},{\"SalaryGradeID\":\"4\",\"MinSalary\":41000,\"MaxSalary\":55000},{\"SalaryGradeID\":\"5\",\"MinSalary\":54000,\"MaxSalary\":75000},{\"SalaryGradeID\":\"6\",\"MinSalary\":81000,\"MaxSalary\":120000}]', 1, 'GLOBAL', 5000000.00, 8472.00, 4898336.00, 'Proposed', NULL, 2, '2026-03-17 18:49:57'),
(25, 'FY2026', '[{\"SalaryGradeID\":\"1\",\"MinSalary\":16000,\"MaxSalary\":20000},{\"SalaryGradeID\":\"2\",\"MinSalary\":25000,\"MaxSalary\":30000},{\"SalaryGradeID\":\"3\",\"MinSalary\":29000,\"MaxSalary\":42000},{\"SalaryGradeID\":\"4\",\"MinSalary\":41000,\"MaxSalary\":55000},{\"SalaryGradeID\":\"5\",\"MinSalary\":54000,\"MaxSalary\":75000},{\"SalaryGradeID\":\"6\",\"MinSalary\":81000,\"MaxSalary\":120000}]', 1, 'GLOBAL', 5000000.00, 8472.00, 4898336.00, 'Proposed', NULL, 2, '2026-03-17 18:54:08'),
(26, 'FY2026', '[{\"SalaryGradeID\":\"1\",\"MinSalary\":16000,\"MaxSalary\":20000},{\"SalaryGradeID\":\"2\",\"MinSalary\":25000,\"MaxSalary\":30000},{\"SalaryGradeID\":\"3\",\"MinSalary\":29000,\"MaxSalary\":42000},{\"SalaryGradeID\":\"4\",\"MinSalary\":41000,\"MaxSalary\":55000},{\"SalaryGradeID\":\"5\",\"MinSalary\":54000,\"MaxSalary\":75000},{\"SalaryGradeID\":\"6\",\"MinSalary\":81000,\"MaxSalary\":120000}]', 1, 'GLOBAL', 5000000.00, 8472.00, 4898336.00, 'Proposed', NULL, 2, '2026-03-17 20:42:16'),
(27, 'FY2026', '[{\"SalaryGradeID\":\"1\",\"MinSalary\":16000,\"MaxSalary\":20000},{\"SalaryGradeID\":\"2\",\"MinSalary\":25000,\"MaxSalary\":30000},{\"SalaryGradeID\":\"3\",\"MinSalary\":29000,\"MaxSalary\":42000},{\"SalaryGradeID\":\"4\",\"MinSalary\":41000,\"MaxSalary\":55000},{\"SalaryGradeID\":\"5\",\"MinSalary\":54000,\"MaxSalary\":75000},{\"SalaryGradeID\":\"6\",\"MinSalary\":81000,\"MaxSalary\":120000}]', 1, 'GLOBAL', 5000000.00, 8472.00, 4898336.00, 'Proposed', NULL, 2, '2026-03-17 20:49:20'),
(28, 'FY2026', '[{\"SalaryGradeID\":\"1\",\"MinSalary\":16000,\"MaxSalary\":20000},{\"SalaryGradeID\":\"2\",\"MinSalary\":25000,\"MaxSalary\":30000},{\"SalaryGradeID\":\"3\",\"MinSalary\":29000,\"MaxSalary\":42000},{\"SalaryGradeID\":\"4\",\"MinSalary\":41000,\"MaxSalary\":55000},{\"SalaryGradeID\":\"5\",\"MinSalary\":54000,\"MaxSalary\":75000},{\"SalaryGradeID\":\"6\",\"MinSalary\":81000,\"MaxSalary\":120000}]', 1, 'GLOBAL', 5000000.00, 8472.00, 4898336.00, 'Proposed', NULL, 2, '2026-03-17 20:59:06');

-- --------------------------------------------------------

--
-- Table structure for table `simulation_proposal_items`
--

CREATE TABLE `simulation_proposal_items` (
  `ItemID` int(11) NOT NULL,
  `ProposalID` int(11) NOT NULL,
  `EmployeeID` int(11) NOT NULL,
  `OriginalSalary` decimal(15,2) DEFAULT 0.00,
  `MarketAdjustment` decimal(15,2) DEFAULT 0.00,
  `MeritPct` decimal(5,2) DEFAULT 0.00,
  `MeritAmount` decimal(15,2) DEFAULT 0.00,
  `NewSalary` decimal(15,2) DEFAULT 0.00,
  `NewGradeID` int(11) DEFAULT NULL,
  `CompaRatio` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `simulation_proposal_items`
--

INSERT INTO `simulation_proposal_items` (`ItemID`, `ProposalID`, `EmployeeID`, `OriginalSalary`, `MarketAdjustment`, `MeritPct`, `MeritAmount`, `NewSalary`, `NewGradeID`, `CompaRatio`) VALUES
(1, 1, 1, 80000.00, 1000.00, 3.00, 2430.00, 83430.00, 6, 80.60),
(2, 1, 2, 80000.00, 1000.00, 6.00, 4860.00, 85860.00, 6, 80.60),
(3, 1, 7, 40000.00, 1000.00, 6.00, 2460.00, 43460.00, 4, 85.40),
(4, 1, 23, 81000.00, 0.00, 0.00, 0.00, 81000.00, 6, 80.60),
(5, 1, 20, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(6, 1, 24, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(7, 1, 3, 21000.00, 0.00, 3.00, 630.00, 21630.00, 2, 82.40),
(8, 1, 6, 53000.00, 1000.00, 6.00, 3240.00, 57240.00, 5, 83.70),
(9, 1, 16, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(10, 1, 4, 15000.00, 1000.00, 4.00, 640.00, 16640.00, 1, 88.90),
(11, 1, 14, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(12, 1, 15, 54000.00, 0.00, 0.00, 0.00, 54000.00, 5, 83.70),
(13, 1, 18, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(14, 1, 19, 81000.00, 0.00, 0.00, 0.00, 81000.00, 6, 80.60),
(15, 1, 8, 21000.00, 8000.00, 0.00, 0.00, 29000.00, 3, 81.70),
(16, 1, 10, 40000.00, 1000.00, 0.00, 0.00, 41000.00, 4, 85.40),
(17, 2, 1, 80000.00, 1000.00, 4.00, 3240.00, 84240.00, 6, 80.60),
(18, 2, 2, 80000.00, 1000.00, 6.00, 4860.00, 85860.00, 6, 80.60),
(19, 2, 7, 40000.00, 1000.00, 6.00, 2460.00, 43460.00, 4, 85.40),
(20, 2, 23, 81000.00, 0.00, 0.00, 0.00, 81000.00, 6, 80.60),
(21, 2, 20, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(22, 2, 24, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(23, 2, 3, 21000.00, 0.00, 3.00, 630.00, 21630.00, 2, 82.40),
(24, 2, 6, 53000.00, 1000.00, 6.00, 3240.00, 57240.00, 5, 83.70),
(25, 2, 16, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(26, 2, 4, 15000.00, 1000.00, 4.00, 640.00, 16640.00, 1, 88.90),
(27, 2, 14, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(28, 2, 15, 54000.00, 0.00, 0.00, 0.00, 54000.00, 5, 83.70),
(29, 2, 18, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(30, 2, 19, 81000.00, 0.00, 0.00, 0.00, 81000.00, 6, 80.60),
(31, 2, 8, 21000.00, 8000.00, 0.00, 0.00, 29000.00, 3, 81.70),
(32, 2, 10, 40000.00, 1000.00, 0.00, 0.00, 41000.00, 4, 85.40),
(33, 3, 1, 80000.00, 1000.00, 3.00, 2430.00, 83430.00, 6, 80.60),
(34, 3, 2, 80000.00, 1000.00, 4.00, 3240.00, 84240.00, 6, 80.60),
(35, 3, 7, 40000.00, 1000.00, 4.00, 1640.00, 42640.00, 4, 85.40),
(36, 3, 23, 81000.00, 0.00, 0.00, 0.00, 81000.00, 6, 80.60),
(37, 3, 20, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(38, 3, 24, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(39, 3, 3, 21000.00, 0.00, 2.00, 420.00, 21420.00, 2, 82.40),
(40, 3, 6, 53000.00, 1000.00, 4.00, 2160.00, 56160.00, 5, 83.70),
(41, 3, 16, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(42, 3, 4, 15000.00, 1000.00, 3.00, 480.00, 16480.00, 1, 88.90),
(43, 3, 14, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(44, 3, 15, 54000.00, 0.00, 0.00, 0.00, 54000.00, 5, 83.70),
(45, 3, 18, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(46, 3, 19, 81000.00, 0.00, 0.00, 0.00, 81000.00, 6, 80.60),
(47, 3, 8, 21000.00, 8000.00, 0.00, 0.00, 29000.00, 3, 81.70),
(48, 3, 10, 40000.00, 1000.00, 0.00, 0.00, 41000.00, 4, 85.40),
(49, 4, 1, 80000.00, 1000.00, 3.00, 2430.00, 83430.00, 6, 80.60),
(50, 4, 2, 80000.00, 1000.00, 4.00, 3240.00, 84240.00, 6, 80.60),
(51, 4, 7, 40000.00, 1000.00, 4.00, 1640.00, 42640.00, 4, 85.40),
(52, 4, 23, 81000.00, 0.00, 0.00, 0.00, 81000.00, 6, 80.60),
(53, 4, 20, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(54, 4, 24, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(55, 4, 3, 21000.00, 0.00, 2.00, 420.00, 21420.00, 2, 82.40),
(56, 4, 6, 53000.00, 1000.00, 4.00, 2160.00, 56160.00, 5, 83.70),
(57, 4, 16, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(58, 4, 4, 15000.00, 1000.00, 3.00, 480.00, 16480.00, 1, 88.90),
(59, 4, 14, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(60, 4, 15, 54000.00, 0.00, 0.00, 0.00, 54000.00, 5, 83.70),
(61, 4, 18, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(62, 4, 19, 81000.00, 0.00, 0.00, 0.00, 81000.00, 6, 80.60),
(63, 4, 8, 21000.00, 8000.00, 0.00, 0.00, 29000.00, 3, 81.70),
(64, 4, 10, 40000.00, 1000.00, 0.00, 0.00, 41000.00, 4, 85.40),
(65, 5, 1, 80000.00, 1000.00, 3.00, 2430.00, 83430.00, 6, 80.60),
(66, 5, 2, 80000.00, 1000.00, 4.00, 3240.00, 84240.00, 6, 80.60),
(67, 5, 7, 40000.00, 1000.00, 4.00, 1640.00, 42640.00, 4, 85.40),
(68, 5, 23, 81000.00, 0.00, 0.00, 0.00, 81000.00, 6, 80.60),
(69, 5, 20, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(70, 5, 24, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(71, 5, 3, 21000.00, 0.00, 2.00, 420.00, 21420.00, 2, 82.40),
(72, 5, 6, 53000.00, 1000.00, 4.00, 2160.00, 56160.00, 5, 83.70),
(73, 5, 16, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(74, 5, 4, 15000.00, 1000.00, 3.00, 480.00, 16480.00, 1, 88.90),
(75, 5, 14, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(76, 5, 15, 54000.00, 0.00, 0.00, 0.00, 54000.00, 5, 83.70),
(77, 5, 18, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(78, 5, 19, 81000.00, 0.00, 0.00, 0.00, 81000.00, 6, 80.60),
(79, 5, 8, 21000.00, 8000.00, 0.00, 0.00, 29000.00, 3, 81.70),
(80, 5, 10, 40000.00, 1000.00, 0.00, 0.00, 41000.00, 4, 85.40),
(81, 6, 1, 80000.00, 1000.00, 3.00, 2430.00, 83430.00, 6, 80.60),
(82, 6, 2, 80000.00, 1000.00, 4.00, 3240.00, 84240.00, 6, 80.60),
(83, 6, 7, 40000.00, 1000.00, 4.00, 1640.00, 42640.00, 4, 85.40),
(84, 6, 23, 81000.00, 0.00, 0.00, 0.00, 81000.00, 6, 80.60),
(85, 6, 20, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(86, 6, 24, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(87, 6, 3, 21000.00, 0.00, 2.00, 420.00, 21420.00, 2, 82.40),
(88, 6, 6, 53000.00, 1000.00, 4.00, 2160.00, 56160.00, 5, 83.70),
(89, 6, 16, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(90, 6, 4, 15000.00, 1000.00, 3.00, 480.00, 16480.00, 1, 88.90),
(91, 6, 14, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(92, 6, 15, 54000.00, 0.00, 0.00, 0.00, 54000.00, 5, 83.70),
(93, 6, 18, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(94, 6, 19, 81000.00, 0.00, 0.00, 0.00, 81000.00, 6, 80.60),
(95, 6, 8, 21000.00, 8000.00, 0.00, 0.00, 29000.00, 3, 81.70),
(96, 6, 10, 40000.00, 1000.00, 0.00, 0.00, 41000.00, 4, 85.40),
(97, 7, 1, 80000.00, 1000.00, 3.00, 2430.00, 83430.00, 6, 80.60),
(98, 7, 2, 80000.00, 1000.00, 4.00, 3240.00, 84240.00, 6, 80.60),
(99, 7, 7, 40000.00, 1000.00, 4.00, 1640.00, 42640.00, 4, 85.40),
(100, 7, 23, 81000.00, 0.00, 0.00, 0.00, 81000.00, 6, 80.60),
(101, 7, 20, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(102, 7, 24, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(103, 7, 3, 21000.00, 0.00, 2.00, 420.00, 21420.00, 2, 82.40),
(104, 7, 6, 53000.00, 1000.00, 4.00, 2160.00, 56160.00, 5, 83.70),
(105, 7, 16, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(106, 7, 4, 15000.00, 1000.00, 3.00, 480.00, 16480.00, 1, 88.90),
(107, 7, 14, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(108, 7, 15, 54000.00, 0.00, 0.00, 0.00, 54000.00, 5, 83.70),
(109, 7, 18, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(110, 7, 19, 81000.00, 0.00, 0.00, 0.00, 81000.00, 6, 80.60),
(111, 7, 8, 21000.00, 8000.00, 0.00, 0.00, 29000.00, 3, 81.70),
(112, 7, 10, 40000.00, 1000.00, 0.00, 0.00, 41000.00, 4, 85.40),
(113, 8, 1, 80000.00, 1000.00, 3.00, 2430.00, 83430.00, 6, 80.60),
(114, 8, 2, 80000.00, 1000.00, 4.00, 3240.00, 84240.00, 6, 80.60),
(115, 8, 7, 40000.00, 1000.00, 4.00, 1640.00, 42640.00, 4, 85.40),
(116, 8, 23, 81000.00, 0.00, 0.00, 0.00, 81000.00, 6, 80.60),
(117, 8, 20, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(118, 8, 24, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(119, 8, 3, 21000.00, 0.00, 2.00, 420.00, 21420.00, 2, 82.40),
(120, 8, 6, 53000.00, 1000.00, 4.00, 2160.00, 56160.00, 5, 83.70),
(121, 8, 16, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(122, 8, 4, 15000.00, 1000.00, 3.00, 480.00, 16480.00, 1, 88.90),
(123, 8, 14, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(124, 8, 15, 54000.00, 0.00, 0.00, 0.00, 54000.00, 5, 83.70),
(125, 8, 18, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(126, 8, 19, 81000.00, 0.00, 0.00, 0.00, 81000.00, 6, 80.60),
(127, 8, 8, 21000.00, 8000.00, 0.00, 0.00, 29000.00, 3, 81.70),
(128, 8, 10, 40000.00, 1000.00, 0.00, 0.00, 41000.00, 4, 85.40),
(129, 9, 1, 80000.00, 1000.00, 0.00, 0.00, 81000.00, 6, 80.60),
(130, 9, 2, 80000.00, 1000.00, 0.00, 0.00, 81000.00, 6, 80.60),
(131, 9, 7, 40000.00, 1000.00, 0.00, 0.00, 41000.00, 4, 85.40),
(132, 9, 23, 81000.00, 0.00, 0.00, 0.00, 81000.00, 6, 80.60),
(133, 9, 20, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(134, 9, 24, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(135, 9, 3, 21000.00, 0.00, 0.00, 0.00, 21000.00, 2, 82.40),
(136, 9, 6, 53000.00, 1000.00, 0.00, 0.00, 54000.00, 5, 83.70),
(137, 9, 16, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(138, 9, 4, 15000.00, 1000.00, 0.00, 0.00, 16000.00, 1, 88.90),
(139, 9, 14, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(140, 9, 15, 54000.00, 0.00, 0.00, 0.00, 54000.00, 5, 83.70),
(141, 9, 18, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(142, 9, 19, 81000.00, 0.00, 0.00, 0.00, 81000.00, 6, 80.60),
(143, 9, 8, 21000.00, 8000.00, 0.00, 0.00, 29000.00, 3, 81.70),
(144, 9, 10, 40000.00, 1000.00, 0.00, 0.00, 41000.00, 4, 85.40),
(145, 10, 1, 80000.00, 1000.00, 4.00, 3240.00, 84240.00, 6, 80.60),
(146, 10, 2, 80000.00, 1000.00, 6.00, 4860.00, 85860.00, 6, 80.60),
(147, 10, 7, 40000.00, 1000.00, 6.00, 2460.00, 43460.00, 4, 85.40),
(148, 10, 23, 81000.00, 0.00, 0.00, 0.00, 81000.00, 6, 80.60),
(149, 10, 20, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(150, 10, 24, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(151, 10, 3, 21000.00, 0.00, 3.00, 630.00, 21630.00, 2, 82.40),
(152, 10, 6, 53000.00, 1000.00, 6.00, 3240.00, 57240.00, 5, 83.70),
(153, 10, 16, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(154, 10, 4, 15000.00, 1000.00, 4.00, 640.00, 16640.00, 1, 88.90),
(155, 10, 14, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(156, 10, 15, 54000.00, 0.00, 0.00, 0.00, 54000.00, 5, 83.70),
(157, 10, 18, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(158, 10, 19, 81000.00, 0.00, 0.00, 0.00, 81000.00, 6, 80.60),
(159, 10, 8, 21000.00, 8000.00, 0.00, 0.00, 29000.00, 3, 81.70),
(160, 10, 10, 40000.00, 1000.00, 0.00, 0.00, 41000.00, 4, 85.40),
(161, 11, 1, 80000.00, 1000.00, 4.00, 3240.00, 84240.00, 6, 80.60),
(162, 11, 2, 80000.00, 1000.00, 6.00, 4860.00, 85860.00, 6, 80.60),
(163, 11, 7, 40000.00, 1000.00, 6.00, 2460.00, 43460.00, 4, 85.40),
(164, 11, 23, 81000.00, 0.00, 0.00, 0.00, 81000.00, 6, 80.60),
(165, 11, 20, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(166, 11, 24, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(167, 11, 3, 21000.00, 0.00, 3.00, 630.00, 21630.00, 2, 82.40),
(168, 11, 6, 53000.00, 1000.00, 6.00, 3240.00, 57240.00, 5, 83.70),
(169, 11, 16, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(170, 11, 4, 15000.00, 1000.00, 4.00, 640.00, 16640.00, 1, 88.90),
(171, 11, 14, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(172, 11, 15, 54000.00, 0.00, 0.00, 0.00, 54000.00, 5, 83.70),
(173, 11, 18, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(174, 11, 19, 81000.00, 0.00, 0.00, 0.00, 81000.00, 6, 80.60),
(175, 11, 8, 21000.00, 8000.00, 0.00, 0.00, 29000.00, 3, 81.70),
(176, 11, 10, 40000.00, 1000.00, 0.00, 0.00, 41000.00, 4, 85.40),
(177, 12, 1, 80000.00, 1000.00, 4.00, 3240.00, 84240.00, 6, 80.60),
(178, 12, 2, 80000.00, 1000.00, 5.90, 4779.00, 85779.00, 6, 80.60),
(179, 12, 7, 40000.00, 1000.00, 4.00, 1640.00, 42640.00, 4, 85.40),
(180, 12, 23, 81000.00, 0.00, 0.00, 0.00, 81000.00, 6, 80.60),
(181, 12, 20, 16000.00, 0.00, 0.00, 0.00, 19000.00, 1, 97.40),
(182, 12, 24, 16000.00, 0.00, 0.00, 0.00, 19000.00, 1, 97.40),
(183, 12, 3, 21000.00, 0.00, 3.00, 630.00, 21630.00, 2, 82.40),
(184, 12, 6, 53000.00, 1000.00, 6.00, 3240.00, 57240.00, 5, 83.70),
(185, 12, 16, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(186, 12, 4, 15000.00, 0.00, 4.00, 760.00, 19760.00, 1, 97.40),
(187, 12, 14, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(188, 12, 15, 54000.00, 0.00, 0.00, 0.00, 54000.00, 5, 83.70),
(189, 12, 18, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(190, 12, 19, 81000.00, 0.00, 0.00, 0.00, 81000.00, 6, 80.60),
(191, 12, 8, 21000.00, 8000.00, 0.00, 0.00, 29000.00, 3, 81.70),
(192, 12, 10, 40000.00, 1000.00, 0.00, 0.00, 41000.00, 4, 85.40),
(193, 13, 1, 80000.00, 1000.00, 3.00, 2430.00, 83430.00, 6, 80.60),
(194, 13, 2, 80000.00, 1000.00, 4.00, 3240.00, 84240.00, 6, 80.60),
(195, 13, 7, 40000.00, 1000.00, 4.00, 1640.00, 42640.00, 4, 85.40),
(196, 13, 23, 81000.00, 0.00, 0.00, 0.00, 81000.00, 6, 80.60),
(197, 13, 20, 16000.00, 0.00, 0.00, 0.00, 18000.00, 1, 94.70),
(198, 13, 24, 16000.00, 0.00, 0.00, 0.00, 18000.00, 1, 94.70),
(199, 13, 3, 21000.00, 0.00, 2.00, 420.00, 21420.00, 2, 82.40),
(200, 13, 6, 53000.00, 1000.00, 4.00, 2160.00, 56160.00, 5, 83.70),
(201, 13, 16, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(202, 13, 4, 15000.00, 0.00, 3.00, 540.00, 18540.00, 1, 94.70),
(203, 13, 14, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(204, 13, 15, 54000.00, 0.00, 0.00, 0.00, 54000.00, 5, 83.70),
(205, 13, 18, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(206, 13, 19, 81000.00, 0.00, 0.00, 0.00, 81000.00, 6, 80.60),
(207, 13, 8, 21000.00, 8000.00, 0.00, 0.00, 29000.00, 3, 81.70),
(208, 13, 10, 40000.00, 1000.00, 0.00, 0.00, 41000.00, 4, 85.40),
(209, 14, 1, 80000.00, 1000.00, 3.00, 2430.00, 83430.00, 6, 80.60),
(210, 14, 2, 80000.00, 1000.00, 4.00, 3240.00, 84240.00, 6, 80.60),
(211, 14, 7, 40000.00, 1000.00, 4.00, 1640.00, 42640.00, 4, 85.40),
(212, 14, 23, 81000.00, 0.00, 0.00, 0.00, 81000.00, 6, 80.60),
(213, 14, 20, 16000.00, 0.00, 0.00, 0.00, 19000.00, 1, 97.40),
(214, 14, 24, 16000.00, 0.00, 0.00, 0.00, 19000.00, 1, 97.40),
(215, 14, 3, 21000.00, 0.00, 2.00, 420.00, 21420.00, 2, 82.40),
(216, 14, 6, 53000.00, 1000.00, 4.00, 2160.00, 56160.00, 5, 83.70),
(217, 14, 16, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(218, 14, 4, 15000.00, 0.00, 3.00, 570.00, 19570.00, 1, 97.40),
(219, 14, 14, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(220, 14, 15, 54000.00, 0.00, 0.00, 0.00, 54000.00, 5, 83.70),
(221, 14, 18, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(222, 14, 19, 81000.00, 0.00, 0.00, 0.00, 81000.00, 6, 80.60),
(223, 14, 8, 21000.00, 8000.00, 0.00, 0.00, 29000.00, 3, 81.70),
(224, 14, 10, 40000.00, 1000.00, 0.00, 0.00, 41000.00, 4, 85.40),
(225, 15, 1, 80000.00, 0.00, 3.00, 2550.00, 87550.00, 6, 82.90),
(226, 15, 2, 80000.00, 0.00, 4.00, 3400.00, 88400.00, 6, 82.90),
(227, 15, 7, 40000.00, 1000.00, 4.00, 1640.00, 42640.00, 4, 85.40),
(228, 15, 23, 81000.00, 0.00, 0.00, 0.00, 85000.00, 6, 82.90),
(229, 15, 20, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(230, 15, 24, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(231, 15, 3, 21000.00, 0.00, 2.00, 420.00, 21420.00, 2, 82.40),
(232, 15, 6, 53000.00, 1000.00, 4.00, 2160.00, 56160.00, 5, 83.70),
(233, 15, 16, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(234, 15, 4, 15000.00, 1000.00, 3.00, 480.00, 16480.00, 1, 88.90),
(235, 15, 14, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(236, 15, 15, 54000.00, 0.00, 0.00, 0.00, 54000.00, 5, 83.70),
(237, 15, 18, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(238, 15, 19, 81000.00, 0.00, 0.00, 0.00, 85000.00, 6, 82.90),
(239, 15, 8, 21000.00, 8000.00, 0.00, 0.00, 29000.00, 3, 81.70),
(240, 15, 10, 40000.00, 1000.00, 0.00, 0.00, 41000.00, 4, 85.40),
(241, 16, 1, 80000.00, 0.00, 3.00, 2550.00, 87550.00, 6, 82.90),
(242, 16, 2, 80000.00, 0.00, 4.00, 3400.00, 88400.00, 6, 82.90),
(243, 16, 7, 40000.00, 1000.00, 4.00, 1640.00, 42640.00, 4, 85.40),
(244, 16, 23, 81000.00, 0.00, 0.00, 0.00, 85000.00, 6, 82.90),
(245, 16, 20, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(246, 16, 24, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(247, 16, 3, 21000.00, 0.00, 2.00, 420.00, 21420.00, 2, 82.40),
(248, 16, 6, 53000.00, 1000.00, 4.00, 2160.00, 56160.00, 5, 83.70),
(249, 16, 16, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(250, 16, 4, 15000.00, 1000.00, 3.00, 480.00, 16480.00, 1, 88.90),
(251, 16, 14, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(252, 16, 15, 54000.00, 0.00, 0.00, 0.00, 54000.00, 5, 83.70),
(253, 16, 18, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(254, 16, 19, 81000.00, 0.00, 0.00, 0.00, 85000.00, 6, 82.90),
(255, 16, 8, 21000.00, 8000.00, 0.00, 0.00, 29000.00, 3, 81.70),
(256, 16, 10, 40000.00, 1000.00, 0.00, 0.00, 41000.00, 4, 85.40),
(257, 17, 1, 80000.00, 0.00, 3.00, 2550.00, 87550.00, 6, 82.90),
(258, 17, 2, 80000.00, 0.00, 4.00, 3400.00, 88400.00, 6, 82.90),
(259, 17, 7, 40000.00, 1000.00, 4.00, 1640.00, 42640.00, 4, 85.40),
(260, 17, 23, 81000.00, 0.00, 0.00, 0.00, 85000.00, 6, 82.90),
(261, 17, 20, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(262, 17, 24, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(263, 17, 3, 21000.00, 0.00, 2.00, 420.00, 21420.00, 2, 82.40),
(264, 17, 6, 53000.00, 1000.00, 4.00, 2160.00, 56160.00, 5, 83.70),
(265, 17, 16, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(266, 17, 4, 15000.00, 1000.00, 3.00, 480.00, 16480.00, 1, 88.90),
(267, 17, 14, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(268, 17, 15, 54000.00, 0.00, 0.00, 0.00, 54000.00, 5, 83.70),
(269, 17, 18, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(270, 17, 19, 81000.00, 0.00, 0.00, 0.00, 85000.00, 6, 82.90),
(271, 17, 8, 21000.00, 8000.00, 0.00, 0.00, 29000.00, 3, 81.70),
(272, 17, 10, 40000.00, 1000.00, 0.00, 0.00, 41000.00, 4, 85.40),
(273, 18, 1, 87550.00, 0.00, 3.00, 2627.00, 90177.00, 6, 87.10),
(274, 18, 2, 88400.00, 0.00, 4.00, 3536.00, 91936.00, 6, 88.00),
(275, 18, 7, 42640.00, 0.00, 4.00, 1706.00, 44346.00, 4, 88.80),
(276, 18, 23, 85000.00, 0.00, 0.00, 0.00, 85000.00, 6, 84.60),
(277, 18, 20, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(278, 18, 24, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(279, 18, 3, 21420.00, 0.00, 2.00, 428.00, 21848.00, 2, 84.00),
(280, 18, 6, 56160.00, 0.00, 4.00, 2246.00, 58406.00, 5, 87.10),
(281, 18, 16, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(282, 18, 4, 16480.00, 0.00, 2.00, 330.00, 16810.00, 1, 91.60),
(283, 18, 14, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(284, 18, 15, 54000.00, 0.00, 0.00, 0.00, 54000.00, 5, 83.70),
(285, 18, 18, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(286, 18, 19, 85000.00, 0.00, 0.00, 0.00, 85000.00, 6, 84.60),
(287, 18, 8, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(288, 18, 10, 41000.00, 0.00, 0.00, 0.00, 41000.00, 4, 85.40),
(289, 19, 1, 90177.00, 0.00, 4.00, 3607.00, 93784.00, 6, 89.70),
(290, 19, 2, 91936.00, 0.00, 6.00, 5516.00, 97452.00, 6, 91.50),
(291, 19, 7, 44346.00, 0.00, 6.00, 2661.00, 47007.00, 4, 92.40),
(292, 19, 23, 85000.00, 0.00, 0.00, 0.00, 85000.00, 6, 84.60),
(293, 19, 20, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(294, 19, 24, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(295, 19, 3, 21848.00, 0.00, 3.00, 655.00, 22503.00, 2, 85.70),
(296, 19, 6, 58406.00, 0.00, 6.00, 3504.00, 61910.00, 5, 90.60),
(297, 19, 16, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(298, 19, 4, 16810.00, 0.00, 4.00, 672.00, 17482.00, 1, 93.40),
(299, 19, 14, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(300, 19, 15, 54000.00, 0.00, 0.00, 0.00, 54000.00, 5, 83.70),
(301, 19, 18, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(302, 19, 19, 85000.00, 0.00, 0.00, 0.00, 85000.00, 6, 84.60),
(303, 19, 8, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(304, 19, 10, 41000.00, 0.00, 0.00, 0.00, 41000.00, 4, 85.40),
(305, 20, 1, 90177.00, 0.00, 3.00, 2705.00, 92882.00, 6, 89.70),
(306, 20, 2, 91936.00, 0.00, 3.00, 2758.00, 94694.00, 6, 91.50),
(307, 20, 7, 44346.00, 0.00, 3.00, 1330.00, 45676.00, 4, 92.40),
(308, 20, 23, 85000.00, 0.00, 0.00, 0.00, 85000.00, 6, 84.60),
(309, 20, 20, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(310, 20, 24, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(311, 20, 3, 21848.00, 0.00, 2.00, 500.00, 25500.00, 2, 90.90),
(312, 20, 6, 58406.00, 0.00, 3.00, 1752.00, 60158.00, 5, 90.60),
(313, 20, 16, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(314, 20, 4, 16810.00, 0.00, 2.00, 336.00, 17146.00, 1, 93.40),
(315, 20, 14, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(316, 20, 15, 54000.00, 0.00, 0.00, 0.00, 54000.00, 5, 83.70),
(317, 20, 18, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(318, 20, 19, 85000.00, 0.00, 0.00, 0.00, 85000.00, 6, 84.60),
(319, 20, 8, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(320, 20, 10, 41000.00, 0.00, 0.00, 0.00, 41000.00, 4, 85.40),
(321, 21, 1, 92882.00, 0.00, 4.00, 3715.00, 96597.00, 6, 96.10),
(322, 21, 2, 94694.00, 0.00, 3.00, 2841.00, 97535.00, 6, 97.00),
(323, 21, 7, 45676.00, 0.00, 3.00, 1370.00, 47046.00, 4, 98.00),
(324, 21, 23, 85000.00, 0.00, 0.00, 0.00, 85000.00, 6, 84.60),
(325, 21, 20, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(326, 21, 24, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(327, 21, 3, 25500.00, 0.00, 1.00, 255.00, 25755.00, 2, 93.70),
(328, 21, 6, 60158.00, 0.00, 3.00, 1805.00, 61963.00, 5, 96.10),
(329, 21, 16, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(330, 21, 4, 17146.00, 0.00, 2.00, 343.00, 17489.00, 1, 97.20),
(331, 21, 14, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(332, 21, 15, 54000.00, 0.00, 0.00, 0.00, 54000.00, 5, 83.70),
(333, 21, 18, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(334, 21, 19, 85000.00, 0.00, 0.00, 0.00, 85000.00, 6, 84.60),
(335, 21, 8, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(336, 21, 10, 41000.00, 0.00, 0.00, 0.00, 41000.00, 4, 85.40),
(337, 22, 1, 92882.00, 0.00, 2.00, 1858.00, 94740.00, 6, 94.30),
(338, 22, 2, 94694.00, 0.00, 3.00, 2841.00, 97535.00, 6, 97.00),
(339, 22, 7, 45676.00, 0.00, 3.00, 1370.00, 47046.00, 4, 98.00),
(340, 22, 23, 85000.00, 0.00, 0.00, 0.00, 85000.00, 6, 84.60),
(341, 22, 20, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(342, 22, 24, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(343, 22, 3, 25500.00, 0.00, 1.00, 255.00, 25755.00, 2, 93.70),
(344, 22, 6, 60158.00, 0.00, 3.00, 1805.00, 61963.00, 5, 96.10),
(345, 22, 16, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(346, 22, 4, 17146.00, 0.00, 2.00, 343.00, 17489.00, 1, 97.20),
(347, 22, 14, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(348, 22, 15, 54000.00, 0.00, 0.00, 0.00, 54000.00, 5, 83.70),
(349, 22, 18, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(350, 22, 19, 85000.00, 0.00, 0.00, 0.00, 85000.00, 6, 84.60),
(351, 22, 8, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(352, 22, 10, 41000.00, 0.00, 0.00, 0.00, 41000.00, 4, 85.40),
(353, 23, 1, 92882.00, 0.00, 2.00, 1858.00, 94740.00, 6, 94.30),
(354, 23, 2, 94694.00, 0.00, 3.00, 2841.00, 97535.00, 6, 97.00),
(355, 23, 7, 45676.00, 0.00, 3.00, 1370.00, 47046.00, 4, 98.00),
(356, 23, 23, 85000.00, 0.00, 0.00, 0.00, 85000.00, 6, 84.60),
(357, 23, 20, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(358, 23, 24, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(359, 23, 3, 25500.00, 0.00, 1.00, 255.00, 25755.00, 2, 93.70),
(360, 23, 6, 60158.00, 0.00, 3.00, 1805.00, 61963.00, 5, 96.10),
(361, 23, 16, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(362, 23, 4, 17146.00, 0.00, 2.00, 343.00, 17489.00, 1, 97.20),
(363, 23, 14, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(364, 23, 15, 54000.00, 0.00, 0.00, 0.00, 54000.00, 5, 83.70),
(365, 23, 18, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(366, 23, 19, 85000.00, 0.00, 0.00, 0.00, 85000.00, 6, 84.60),
(367, 23, 8, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(368, 23, 10, 41000.00, 0.00, 0.00, 0.00, 41000.00, 4, 85.40),
(369, 24, 1, 92882.00, 0.00, 2.00, 1858.00, 94740.00, 6, 94.30),
(370, 24, 2, 94694.00, 0.00, 3.00, 2841.00, 97535.00, 6, 97.00),
(371, 24, 7, 45676.00, 0.00, 3.00, 1370.00, 47046.00, 4, 98.00),
(372, 24, 23, 85000.00, 0.00, 0.00, 0.00, 85000.00, 6, 84.60),
(373, 24, 20, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(374, 24, 24, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(375, 24, 3, 25500.00, 0.00, 1.00, 255.00, 25755.00, 2, 93.70),
(376, 24, 6, 60158.00, 0.00, 3.00, 1805.00, 61963.00, 5, 96.10),
(377, 24, 16, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(378, 24, 4, 17146.00, 0.00, 2.00, 343.00, 17489.00, 1, 97.20),
(379, 24, 14, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(380, 24, 15, 54000.00, 0.00, 0.00, 0.00, 54000.00, 5, 83.70),
(381, 24, 18, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(382, 24, 19, 85000.00, 0.00, 0.00, 0.00, 85000.00, 6, 84.60),
(383, 24, 8, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(384, 24, 10, 41000.00, 0.00, 0.00, 0.00, 41000.00, 4, 85.40),
(385, 25, 1, 92882.00, 0.00, 2.00, 1858.00, 94740.00, 6, 94.30),
(386, 25, 2, 94694.00, 0.00, 3.00, 2841.00, 97535.00, 6, 97.00),
(387, 25, 7, 45676.00, 0.00, 3.00, 1370.00, 47046.00, 4, 98.00),
(388, 25, 23, 85000.00, 0.00, 0.00, 0.00, 85000.00, 6, 84.60),
(389, 25, 20, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(390, 25, 24, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(391, 25, 3, 25500.00, 0.00, 1.00, 255.00, 25755.00, 2, 93.70),
(392, 25, 6, 60158.00, 0.00, 3.00, 1805.00, 61963.00, 5, 96.10),
(393, 25, 16, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(394, 25, 4, 17146.00, 0.00, 2.00, 343.00, 17489.00, 1, 97.20),
(395, 25, 14, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(396, 25, 15, 54000.00, 0.00, 0.00, 0.00, 54000.00, 5, 83.70),
(397, 25, 18, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(398, 25, 19, 85000.00, 0.00, 0.00, 0.00, 85000.00, 6, 84.60),
(399, 25, 8, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(400, 25, 10, 41000.00, 0.00, 0.00, 0.00, 41000.00, 4, 85.40),
(401, 26, 1, 92882.00, 0.00, 2.00, 1858.00, 94740.00, 6, 94.30),
(402, 26, 2, 94694.00, 0.00, 3.00, 2841.00, 97535.00, 6, 97.00),
(403, 26, 7, 45676.00, 0.00, 3.00, 1370.00, 47046.00, 4, 98.00),
(404, 26, 23, 85000.00, 0.00, 0.00, 0.00, 85000.00, 6, 84.60),
(405, 26, 20, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(406, 26, 24, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(407, 26, 3, 25500.00, 0.00, 1.00, 255.00, 25755.00, 2, 93.70),
(408, 26, 6, 60158.00, 0.00, 3.00, 1805.00, 61963.00, 5, 96.10),
(409, 26, 16, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(410, 26, 4, 17146.00, 0.00, 2.00, 343.00, 17489.00, 1, 97.20),
(411, 26, 14, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(412, 26, 15, 54000.00, 0.00, 0.00, 0.00, 54000.00, 5, 83.70),
(413, 26, 18, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(414, 26, 19, 85000.00, 0.00, 0.00, 0.00, 85000.00, 6, 84.60),
(415, 26, 8, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(416, 26, 10, 41000.00, 0.00, 0.00, 0.00, 41000.00, 4, 85.40),
(417, 27, 1, 92882.00, 0.00, 2.00, 1858.00, 94740.00, 6, 94.30),
(418, 27, 2, 94694.00, 0.00, 3.00, 2841.00, 97535.00, 6, 97.00),
(419, 27, 7, 45676.00, 0.00, 3.00, 1370.00, 47046.00, 4, 98.00),
(420, 27, 23, 85000.00, 0.00, 0.00, 0.00, 85000.00, 6, 84.60),
(421, 27, 20, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(422, 27, 24, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(423, 27, 3, 25500.00, 0.00, 1.00, 255.00, 25755.00, 2, 93.70),
(424, 27, 6, 60158.00, 0.00, 3.00, 1805.00, 61963.00, 5, 96.10),
(425, 27, 16, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(426, 27, 4, 17146.00, 0.00, 2.00, 343.00, 17489.00, 1, 97.20),
(427, 27, 14, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(428, 27, 15, 54000.00, 0.00, 0.00, 0.00, 54000.00, 5, 83.70),
(429, 27, 18, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(430, 27, 19, 85000.00, 0.00, 0.00, 0.00, 85000.00, 6, 84.60),
(431, 27, 8, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(432, 27, 10, 41000.00, 0.00, 0.00, 0.00, 41000.00, 4, 85.40),
(433, 28, 1, 92882.00, 0.00, 2.00, 1858.00, 94740.00, 6, 94.30),
(434, 28, 2, 94694.00, 0.00, 3.00, 2841.00, 97535.00, 6, 97.00),
(435, 28, 7, 45676.00, 0.00, 3.00, 1370.00, 47046.00, 4, 98.00),
(436, 28, 23, 85000.00, 0.00, 0.00, 0.00, 85000.00, 6, 84.60),
(437, 28, 20, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(438, 28, 24, 16000.00, 0.00, 0.00, 0.00, 16000.00, 1, 88.90),
(439, 28, 3, 25500.00, 0.00, 1.00, 255.00, 25755.00, 2, 93.70),
(440, 28, 6, 60158.00, 0.00, 3.00, 1805.00, 61963.00, 5, 96.10),
(441, 28, 16, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(442, 28, 4, 17146.00, 0.00, 2.00, 343.00, 17489.00, 1, 97.20),
(443, 28, 14, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(444, 28, 15, 54000.00, 0.00, 0.00, 0.00, 54000.00, 5, 83.70),
(445, 28, 18, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(446, 28, 19, 85000.00, 0.00, 0.00, 0.00, 85000.00, 6, 84.60),
(447, 28, 8, 29000.00, 0.00, 0.00, 0.00, 29000.00, 3, 81.70),
(448, 28, 10, 41000.00, 0.00, 0.00, 0.00, 41000.00, 4, 85.40);

-- --------------------------------------------------------

--
-- Table structure for table `sss_settings`
--

CREATE TABLE `sss_settings` (
  `period_id` int(11) NOT NULL,
  `employee_share_pct` decimal(5,2) DEFAULT NULL,
  `employer_share_pct` decimal(5,2) DEFAULT NULL,
  `max_msc_monthly` decimal(15,2) DEFAULT NULL,
  `wisp_threshold` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sss_settings`
--

INSERT INTO `sss_settings` (`period_id`, `employee_share_pct`, `employer_share_pct`, `max_msc_monthly`, `wisp_threshold`) VALUES
(1, 5.00, 10.00, 35000.00, 20000.00);

-- --------------------------------------------------------

--
-- Table structure for table `statutory_proposals`
--

CREATE TABLE `statutory_proposals` (
  `ProposalID` int(11) NOT NULL,
  `BatchReference` varchar(50) NOT NULL,
  `Category` varchar(50) NOT NULL,
  `FieldName` varchar(100) NOT NULL,
  `OldValue` decimal(15,2) DEFAULT NULL,
  `ProposedValue` decimal(15,2) DEFAULT NULL,
  `Reason` text NOT NULL,
  `ProofPath` varchar(255) DEFAULT NULL,
  `Status` enum('Pending','Endorsed','Manager Approved','Applied','Rejected') DEFAULT 'Pending',
  `ProposedBy` int(11) DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `statutory_proposals`
--

INSERT INTO `statutory_proposals` (`ProposalID`, `BatchReference`, `Category`, `FieldName`, `OldValue`, `ProposedValue`, `Reason`, `ProofPath`, `Status`, `ProposedBy`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'stat_69a56e725ea036.53664764', 'SSS', 'max_msc_monthly', 35000.00, 35000.00, 'test', 'uploads/statutory_proofs/stat_69a56e725ea036.53664764.jpg', 'Rejected', 7, '2026-03-02 11:03:14', '2026-03-02 11:22:40'),
(2, 'stat_69a56e725ea036.53664764', 'SSS', 'employee_share_pct', 5.00, 4.50, 'test', 'uploads/statutory_proofs/stat_69a56e725ea036.53664764.jpg', 'Rejected', 7, '2026-03-02 11:03:14', '2026-03-02 11:22:40'),
(3, 'stat_69a56e725ea036.53664764', 'SSS', 'employer_share_pct', 10.00, 10.00, 'test', 'uploads/statutory_proofs/stat_69a56e725ea036.53664764.jpg', 'Rejected', 7, '2026-03-02 11:03:14', '2026-03-02 11:22:40'),
(4, 'stat_69a56e725ea036.53664764', 'SSS', 'wisp_threshold', 20000.00, 20000.00, 'test', 'uploads/statutory_proofs/stat_69a56e725ea036.53664764.jpg', 'Rejected', 7, '2026-03-02 11:03:14', '2026-03-02 11:22:40'),
(5, 'stat_69a56e725ea036.53664764', 'PhilHealth', 'salary_ceiling', 100000.00, 100000.00, 'test', 'uploads/statutory_proofs/stat_69a56e725ea036.53664764.jpg', 'Rejected', 7, '2026-03-02 11:03:14', '2026-03-02 11:22:40'),
(6, 'stat_69a56e725ea036.53664764', 'PhilHealth', 'employee_share_pct', 2.50, 2.50, 'test', 'uploads/statutory_proofs/stat_69a56e725ea036.53664764.jpg', 'Rejected', 7, '2026-03-02 11:03:14', '2026-03-02 11:22:40'),
(7, 'stat_69a56e725ea036.53664764', 'PhilHealth', 'employer_share_pct', 2.50, 2.50, 'test', 'uploads/statutory_proofs/stat_69a56e725ea036.53664764.jpg', 'Rejected', 7, '2026-03-02 11:03:14', '2026-03-02 11:22:40'),
(8, 'stat_69a56e725ea036.53664764', 'Pag-IBIG', 'monthly_cap_ee', 200.00, 200.00, 'test', 'uploads/statutory_proofs/stat_69a56e725ea036.53664764.jpg', 'Rejected', 7, '2026-03-02 11:03:14', '2026-03-02 11:22:40'),
(9, 'stat_69a56e725ea036.53664764', 'Pag-IBIG', 'monthly_cap_er', 200.00, 200.00, 'test', 'uploads/statutory_proofs/stat_69a56e725ea036.53664764.jpg', 'Rejected', 7, '2026-03-02 11:03:14', '2026-03-02 11:22:40'),
(10, 'stat_69a56e725ea036.53664764', 'Pag-IBIG', 'employee_rate_pct', 2.00, 2.00, 'test', 'uploads/statutory_proofs/stat_69a56e725ea036.53664764.jpg', 'Rejected', 7, '2026-03-02 11:03:14', '2026-03-02 11:22:40'),
(11, 'stat_69a56e725ea036.53664764', 'BIR Tax', 'tax_exempt_limit', 250000.00, 250000.00, 'test', 'uploads/statutory_proofs/stat_69a56e725ea036.53664764.jpg', 'Rejected', 7, '2026-03-02 11:03:14', '2026-03-02 11:22:40'),
(12, 'stat_69a56e725ea036.53664764', 'BIR Tax', 'de_minimis_cap', 90000.00, 90000.00, 'test', 'uploads/statutory_proofs/stat_69a56e725ea036.53664764.jpg', 'Rejected', 7, '2026-03-02 11:03:14', '2026-03-02 11:22:40'),
(13, 'stat_69a56e725ea036.53664764', 'BIR Tax', 'thirteenth_month_cap', 90000.00, 90000.00, 'test', 'uploads/statutory_proofs/stat_69a56e725ea036.53664764.jpg', 'Rejected', 7, '2026-03-02 11:03:14', '2026-03-02 11:22:40'),
(14, 'stat_69a57343a9d920.57980512', 'SSS', 'employee_share_pct', 5.00, 4.50, 'test', 'uploads/statutory_proofs/stat_69a57343a9d920.57980512.jpg', 'Applied', 7, '2026-03-02 11:23:47', '2026-03-02 11:41:21'),
(15, 'stat_69a57793056bb0.48619510', 'SSS', 'employee_share_pct', 4.50, 5.00, 'test', 'uploads/statutory_proofs/stat_69a57793056bb0.48619510.jpg', 'Applied', 7, '2026-03-02 11:42:11', '2026-03-02 11:52:39'),
(16, 'stat_69a58bdb1cab06.32267020', 'SSS', 'employee_share_pct', 5.00, 5.50, 'test', 'uploads/statutory_proofs/stat_69a58bdb1cab06.32267020.jpg', 'Rejected', 7, '2026-03-02 13:08:43', '2026-03-04 08:45:58'),
(17, 'stat_69a7d90f80a459.37761454', 'SSS', 'employee_share_pct', 5.00, 4.50, 'test', 'uploads/statutory_proofs/stat_69a7d90f80a459.37761454.jpg', 'Rejected', 7, '2026-03-04 07:02:39', '2026-03-04 08:45:52'),
(18, 'stat_69a7eca6ec7fe5.61235505', 'SSS', 'employee_share_pct', 5.00, 10.00, 'test', 'uploads/statutory_proofs/stat_69a7eca6ec7fe5.61235505.jpg', 'Rejected', 7, '2026-03-04 08:26:14', '2026-03-04 08:45:46'),
(19, 'stat_69a7eca6ec7fe5.61235505', 'SSS', 'employer_share_pct', 10.00, 20.00, 'test', 'uploads/statutory_proofs/stat_69a7eca6ec7fe5.61235505.jpg', 'Rejected', 7, '2026-03-04 08:26:15', '2026-03-04 08:45:46'),
(20, 'stat_69a7eca6ec7fe5.61235505', 'PhilHealth', 'employee_share_pct', 2.50, 5.00, 'test', 'uploads/statutory_proofs/stat_69a7eca6ec7fe5.61235505.jpg', 'Rejected', 7, '2026-03-04 08:26:15', '2026-03-04 08:45:46'),
(21, 'stat_69a7eca6ec7fe5.61235505', 'PhilHealth', 'employer_share_pct', 2.50, 5.00, 'test', 'uploads/statutory_proofs/stat_69a7eca6ec7fe5.61235505.jpg', 'Rejected', 7, '2026-03-04 08:26:15', '2026-03-04 08:45:46');

-- --------------------------------------------------------

--
-- Table structure for table `system_notifications`
--

CREATE TABLE `system_notifications` (
  `id` int(11) NOT NULL,
  `module_target` varchar(50) NOT NULL,
  `role_target` varchar(50) DEFAULT 'all',
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_notifications`
--

INSERT INTO `system_notifications` (`id`, `module_target`, `role_target`, `message`, `is_read`, `created_at`) VALUES
(1, 'compensation_cycle', 'all', 'Salary scale proposal batch batch_69a52733dabe87.45660111 has been endorsed and is awaiting manager approval.', 1, '2026-03-02 07:31:20'),
(2, 'compensation_cycle', 'all', 'New statutory change proposal submitted by Miguel Padre. Batch: stat_69a56e725ea036.53664764', 1, '2026-03-02 11:03:14'),
(3, 'compensation_cycle', 'all', 'New statutory change proposal submitted by Miguel Padre. Batch: stat_69a57343a9d920.57980512', 1, '2026-03-02 11:23:47'),
(4, 'compensation_cycle', 'all', 'Statutory change proposal batch stat_69a57343a9d920.57980512 has been endorsed and is awaiting manager approval.', 1, '2026-03-02 11:39:51'),
(5, 'compensation_cycle', 'all', 'Statutory adjustment batch stat_69a57343a9d920.57980512 has been approved and applied.', 1, '2026-03-02 11:41:21'),
(6, 'compensation_cycle', 'all', 'New statutory change proposal submitted by Miguel Padre. Batch: stat_69a57793056bb0.48619510', 1, '2026-03-02 11:42:11'),
(7, 'compensation_cycle', 'all', 'Statutory change proposal batch stat_69a57793056bb0.48619510 has been endorsed and is awaiting manager approval.', 1, '2026-03-02 11:51:57'),
(8, 'compensation_cycle', 'all', 'Statutory adjustment batch stat_69a57793056bb0.48619510 has been approved and applied.', 1, '2026-03-02 11:52:39'),
(9, 'compensation_cycle', 'supervisor', 'New statutory change proposal submitted by Miguel Padre. Batch: stat_69a58bdb1cab06.32267020', 1, '2026-03-02 13:08:43'),
(10, 'compensation_cycle', 'hr manager', 'New statutory proposal endorsement from supervisor for batch stat_69a58bdb1cab06.32267020.', 1, '2026-03-02 13:21:15'),
(11, 'compensation_cycle', 'supervisor', 'Statutory proposed by you has been endorsed to manager. Batch: stat_69a58bdb1cab06.32267020', 1, '2026-03-02 13:21:15'),
(12, 'compensation_cycle', 'supervisor', 'A new merit matrix adjustment batch (MRT_69A69380F2AA9) has been proposed and requires your endorsement.', 1, '2026-03-03 07:53:37'),
(13, 'compensation_cycle', 'supervisor', 'A new allowance adjustment batch (ALW_69A693C6C5F61) has been proposed and requires your endorsement.', 1, '2026-03-03 07:54:46'),
(14, 'compensation_cycle', 'hr manager', 'New merit matrix proposal endorsement from supervisor for batch MRT_69A69380F2AA9.', 1, '2026-03-03 07:56:59'),
(15, 'compensation_cycle', 'hr manager', 'New allowance proposal endorsement from supervisor for batch ALW_69A693C6C5F61.', 1, '2026-03-03 07:57:07'),
(16, 'compensation_cycle', 'all', 'Your merit matrix proposal batch MRT_69A69380F2AA9 has been APPROVED and applied.', 0, '2026-03-03 10:23:27'),
(17, 'compensation_cycle', 'supervisor', 'New salary scale change proposal submitted by Miguel Padre. Batch: batch_69a70b3f29d693.72011293', 0, '2026-03-03 16:24:31'),
(18, 'compensation_cycle', 'supervisor', 'New salary scale change proposal submitted by Miguel Padre. Batch: batch_69a7165063a405.89337660', 0, '2026-03-03 17:11:44'),
(19, 'compensation_cycle', 'supervisor', 'New salary scale change proposal submitted by Miguel Padre. Batch: batch_69a79325dcb7d5.30120775', 0, '2026-03-04 02:04:21'),
(20, 'compensation_cycle', 'hr manager', 'New salary scale proposal endorsement from supervisor for batch batch_69a79325dcb7d5.30120775.', 0, '2026-03-04 02:08:50'),
(21, 'compensation_cycle', 'supervisor', 'Statutory proposed by you has been endorsed to manager. Batch: batch_69a79325dcb7d5.30120775', 0, '2026-03-04 02:08:50'),
(22, 'compensation_cycle', 'supervisor', 'New salary scale change proposal submitted by Miguel Padre. Batch: batch_69a795b3427832.47686676', 0, '2026-03-04 02:15:15'),
(23, 'compensation_cycle', 'hr manager', 'New salary scale proposal endorsement from supervisor for batch batch_69a795b3427832.47686676.', 0, '2026-03-04 02:15:38'),
(24, 'compensation_cycle', 'supervisor', 'Statutory proposed by you has been endorsed to manager. Batch: batch_69a795b3427832.47686676', 0, '2026-03-04 02:15:38'),
(25, 'finance_approval', 'finance', 'Salary scale proposal batch batch_69a795b3427832.47686676 has been approved by the Manager and forwarded to Finance.', 0, '2026-03-04 02:40:44'),
(26, 'finance_approval', 'finance', 'Salary scale proposal batch batch_69a79325dcb7d5.30120775 has been approved by the Manager and forwarded to Finance.', 0, '2026-03-04 03:17:41'),
(27, 'compensation_cycle', 'hr manager', 'Salary scale proposal batch batch_69a79325dcb7d5.30120775 has been officially applied by Finance.', 0, '2026-03-04 03:21:42'),
(28, 'compensation_cycle', 'supervisor', 'New salary scale change proposal submitted by Miguel Padre. Batch: batch_69a7a58ea81c90.34034724', 0, '2026-03-04 03:22:54'),
(29, 'compensation_cycle', 'hr manager', 'Salary scale proposal batch batch_69a795b3427832.47686676 has been officially applied by Finance.', 0, '2026-03-04 03:26:01'),
(30, 'compensation_cycle', 'hr manager', 'New salary scale proposal endorsement from supervisor for batch batch_69a7a58ea81c90.34034724.', 0, '2026-03-04 03:26:33'),
(31, 'compensation_cycle', 'supervisor', 'Statutory proposed by you has been endorsed to manager. Batch: batch_69a7a58ea81c90.34034724', 0, '2026-03-04 03:26:33'),
(32, 'finance_approval', 'finance', 'Salary scale proposal batch batch_69a7a58ea81c90.34034724 has been approved by the Manager and forwarded to Finance.', 0, '2026-03-04 03:27:47'),
(33, 'compensation_cycle', 'hr manager', 'Salary scale proposal batch batch_69a7a58ea81c90.34034724 has been officially applied by Finance.', 0, '2026-03-04 03:28:02'),
(34, 'finance_approval', 'finance', 'Salary scale proposal batch batch_69a52733dabe87.45660111 has been approved by the Manager and forwarded to Finance.', 0, '2026-03-04 04:14:57'),
(35, 'compensation_cycle', 'hr manager', 'Salary scale proposal batch batch_69a52733dabe87.45660111 has been rejected by the HR Manager.', 0, '2026-03-04 04:43:37'),
(36, 'compensation_cycle', 'supervisor', 'New salary scale change proposal submitted by Miguel Padre. Batch: batch_69a7b8abcfdda8.01844093', 0, '2026-03-04 04:44:27'),
(37, 'compensation_cycle', 'hr manager', 'New salary scale proposal endorsement from supervisor for batch batch_69a7b8abcfdda8.01844093.', 0, '2026-03-04 04:46:06'),
(38, 'compensation_cycle', 'supervisor', 'Statutory proposed by you has been endorsed to manager. Batch: batch_69a7b8abcfdda8.01844093', 0, '2026-03-04 04:46:06'),
(39, 'finance_approval', 'finance', 'Salary scale proposal batch batch_69a7b8abcfdda8.01844093 has been approved by the Manager and forwarded to Finance.', 0, '2026-03-04 04:47:04'),
(40, 'compensation_cycle', 'hr manager', 'Salary scale proposal batch batch_69a7b8abcfdda8.01844093 has been officially applied by Finance.', 0, '2026-03-04 04:58:20'),
(41, 'compensation_cycle', 'supervisor', 'New statutory change proposal submitted by Miguel Padre. Batch: stat_69a7d90f80a459.37761454', 0, '2026-03-04 07:02:39'),
(42, 'compensation_cycle', 'hr manager', 'New statutory proposal endorsement from supervisor for batch stat_69a7d90f80a459.37761454.', 0, '2026-03-04 07:03:23'),
(43, 'compensation_cycle', 'supervisor', 'Statutory proposed by you has been endorsed to manager. Batch: stat_69a7d90f80a459.37761454', 0, '2026-03-04 07:03:23'),
(44, 'finance_approval', 'finance', 'Statutory adjustment batch stat_69a7d90f80a459.37761454 has been approved by the Manager and forwarded to Finance.', 0, '2026-03-04 07:35:49'),
(45, 'finance_approval', 'finance', 'Statutory adjustment batch stat_69a58bdb1cab06.32267020 has been approved by the Manager and forwarded to Finance.', 0, '2026-03-04 07:45:11'),
(46, 'compensation_cycle', 'supervisor', 'New statutory change proposal submitted by Miguel Padre. Batch: stat_69a7eca6ec7fe5.61235505', 0, '2026-03-04 08:26:15'),
(47, 'compensation_cycle', 'hr manager', 'New statutory proposal endorsement from supervisor for batch stat_69a7eca6ec7fe5.61235505.', 0, '2026-03-04 08:28:08'),
(48, 'compensation_cycle', 'supervisor', 'Statutory proposed by you has been endorsed to manager. Batch: stat_69a7eca6ec7fe5.61235505', 0, '2026-03-04 08:28:08'),
(49, 'finance_approval', 'finance', 'Statutory adjustment batch stat_69a7eca6ec7fe5.61235505 has been approved by the Manager and forwarded to Finance.', 0, '2026-03-04 08:29:34'),
(50, 'compensation_cycle', 'hr manager', 'Statutory adjustment batch stat_69a7eca6ec7fe5.61235505 has been rejected by the HR Manager.', 0, '2026-03-04 08:45:46'),
(51, 'compensation_cycle', 'hr manager', 'Statutory adjustment batch stat_69a7d90f80a459.37761454 has been rejected by the HR Manager.', 0, '2026-03-04 08:45:52'),
(52, 'compensation_cycle', 'hr manager', 'Statutory adjustment batch stat_69a58bdb1cab06.32267020 has been rejected by the HR Manager.', 0, '2026-03-04 08:45:58'),
(53, 'compensation_cycle', 'supervisor', 'A new allowance adjustment batch (ALW_69A7FD3F49A3A) has been proposed and requires your endorsement.', 0, '2026-03-04 09:37:03'),
(54, 'compensation_cycle', 'supervisor', 'A new merit matrix adjustment batch (MRT_69A7FD863510F) has been proposed and requires your endorsement.', 0, '2026-03-04 09:38:14'),
(55, 'compensation_cycle', 'hr manager', 'New allowance proposal endorsement from supervisor for batch ALW_69A7FD3F49A3A.', 0, '2026-03-04 09:39:26'),
(56, 'compensation_cycle', 'hr manager', 'New merit matrix proposal endorsement from supervisor for batch MRT_69A7FD863510F.', 0, '2026-03-04 09:39:33'),
(57, 'compensation_cycle', 'all', 'Your merit matrix proposal batch MRT_69A7FD863510F has been APPROVED by the Manager and forwarded to Finance.', 0, '2026-03-04 09:50:16'),
(58, 'compensation_cycle', 'all', 'Your allowance proposal batch ALW_69A7FD3F49A3A has been APPROVED by the Manager and forwarded to Finance.', 0, '2026-03-04 09:53:47'),
(59, 'compensation_review', 'hr manager', 'New Compensation Proposal submitted for cycle: FY2026 by Miguel Padre. Total Impact: ₱40,381.05', 0, '2026-03-04 17:06:04'),
(60, 'compensation_review', 'hr manager', 'New Compensation Proposal submitted for cycle: FY2026 by Miguel Padre. Total Impact: ₱40,381.05', 0, '2026-03-04 17:27:33'),
(61, 'compensation_review', 'hr manager', 'A new compensation simulation has been endorsed by supervisor.', 0, '2026-03-04 17:28:46'),
(62, 'finance_approval', 'finance manager', 'A compensation simulation has been approved by the Manager and awaits final Finance review.', 0, '2026-03-04 17:51:41'),
(63, 'compensation_cycle', 'supervisor', 'New salary scale change proposal submitted by Miguel Padre. Batch: batch_69a8fa843ac4b4.03580821', 0, '2026-03-05 03:37:40'),
(64, 'compensation_cycle', 'supervisor', 'New salary scale change proposal submitted by Miguel Padre. Batch: batch_69aed3baca4855.62920620', 0, '2026-03-09 14:05:46'),
(65, 'compensation_review', 'hr manager', 'New Compensation Proposal submitted for cycle: FY2026 by Miguel Padre. Total Impact: ₱53,676.25', 0, '2026-03-10 08:18:44'),
(66, 'compensation_review', 'hr manager', 'New Compensation Proposal submitted for cycle: FY2026 by Mike Dabu. Total Impact: ₱49,940.15', 0, '2026-03-10 17:06:43'),
(67, 'compensation_verify', 'supervisor', 'New Compensation Proposal submitted for cycle: FY2026 by Mike Dabu. Awaiting your verification.', 0, '2026-03-10 17:29:52'),
(68, 'compensation_review', 'hr manager', 'A compensation simulation has been verified by the Supervisor and awaits your review.', 0, '2026-03-10 17:55:15'),
(69, 'finance_approval', 'finance manager', 'A compensation simulation has been reviewed by the Manager and awaits final Finance approval.', 0, '2026-03-10 18:01:50'),
(70, 'compensation_cycle', 'hr manager', 'Compensation simulation for cycle has been FINALLY APPROVED by Finance.', 0, '2026-03-10 18:02:43');

-- --------------------------------------------------------

--
-- Table structure for table `taxbenefits`
--

CREATE TABLE `taxbenefits` (
  `BenefitID` int(11) NOT NULL,
  `EmployeeID` int(11) DEFAULT NULL,
  `TINNumber` varchar(50) DEFAULT NULL,
  `SSSNumber` varchar(50) DEFAULT NULL,
  `PhilHealthNumber` varchar(50) DEFAULT NULL,
  `PagIBIGNumber` varchar(50) DEFAULT NULL,
  `TaxStatus` varchar(50) DEFAULT NULL,
  `VerificationStatus` varchar(20) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `taxbenefits`
--

INSERT INTO `taxbenefits` (`BenefitID`, `EmployeeID`, `TINNumber`, `SSSNumber`, `PhilHealthNumber`, `PagIBIGNumber`, `TaxStatus`, `VerificationStatus`) VALUES
(1, 1, '123-456-789-000', '34-1234567-8', '12-050123456-7', '1212-3434-5656', 'S', 'Verified'),
(2, 2, '361-436-789-000', '95-1214567-8', '94-050223456-7', '9332-3434-5656', 'S', 'Verified'),
(3, 3, '321-456-789-000', '65-1234567-8', '21-050123456-7', '1312-3434-5656', 'S', 'Verified'),
(4, 4, '3321-654-987-000', '54-3234567-8', '14-03113456-7', '1431-3434-5656', 'S', 'Pending'),
(5, 7, '111-654-987-000', '54-333367-8', '14-04343456-7', '1414-1223-5656', 'S', 'Pending'),
(6, 6, '321-324-987-000', '14-1234567-8', '14-053123456-7', '114-3434-5656', 'S', 'Pending'),
(7, 2, '361-436-789-000', '95-1214567-8', '94-050223456-7', '9332-3434-5656', 'S', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `timesheet_employee_summary`
--

CREATE TABLE `timesheet_employee_summary` (
  `SummaryID` int(11) NOT NULL,
  `PeriodID` int(11) NOT NULL,
  `EmployeeID` int(11) NOT NULL,
  `DepartmentID` int(11) NOT NULL,
  `PositionID` int(11) NOT NULL,
  `IsEligibleForHolidayPay` tinyint(1) NOT NULL DEFAULT 1,
  `RegularHours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `OvertimeHours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `NightDiffHours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `RegHolidayHours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `SpecHolidayHours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `UnworkedHolidayHours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `HolidayOvertimeHours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `LateMinutes` int(11) NOT NULL DEFAULT 0,
  `UndertimeMinutes` int(11) NOT NULL DEFAULT 0,
  `AbsencesHours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `PaidLeaveHours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `UnpaidLeaveHours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `TotalPayableHours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `Notes` varchar(255) DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timesheet_employee_summary`
--

INSERT INTO `timesheet_employee_summary` (`SummaryID`, `PeriodID`, `EmployeeID`, `DepartmentID`, `PositionID`, `IsEligibleForHolidayPay`, `RegularHours`, `OvertimeHours`, `NightDiffHours`, `RegHolidayHours`, `SpecHolidayHours`, `UnworkedHolidayHours`, `HolidayOvertimeHours`, `LateMinutes`, `UndertimeMinutes`, `AbsencesHours`, `PaidLeaveHours`, `UnpaidLeaveHours`, `TotalPayableHours`, `Notes`, `CreatedAt`, `UpdatedAt`) VALUES
(0, 1, 1, 1, 1, 1, 160.00, 10.00, 0.00, 0.00, 0.00, 0.00, 0.00, 30, 0, 0.00, 0.00, 0.00, 170.00, NULL, '2026-03-04 18:13:58', '2026-03-04 18:13:58'),
(0, 1, 2, 1, 1, 1, 160.00, 5.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 0.00, 0.00, 165.00, NULL, '2026-03-04 18:13:58', '2026-03-04 18:13:58'),
(0, 1, 3, 2, 2, 1, 160.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 120, 0, 0.00, 0.00, 0.00, 158.00, NULL, '2026-03-04 18:13:58', '2026-03-04 18:13:58'),
(0, 1, 4, 2, 4, 1, 160.00, 8.00, 0.00, 0.00, 0.00, 0.00, 0.00, 15, 0, 0.00, 0.00, 0.00, 168.00, NULL, '2026-03-04 18:13:58', '2026-03-04 18:13:58'),
(0, 1, 6, 2, 3, 1, 160.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 0.00, 0.00, 160.00, NULL, '2026-03-04 18:13:58', '2026-03-04 18:13:58'),
(0, 1, 7, 2, 5, 1, 160.00, 12.00, 0.00, 0.00, 0.00, 0.00, 0.00, 45, 0, 0.00, 0.00, 0.00, 171.25, NULL, '2026-03-04 18:13:58', '2026-03-04 18:13:58'),
(0, 1, 8, 2, 6, 1, 160.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 0.00, 0.00, 160.00, NULL, '2026-03-04 18:13:58', '2026-03-04 18:13:58'),
(0, 1, 10, 2, 7, 1, 160.00, 2.00, 0.00, 0.00, 0.00, 0.00, 0.00, 10, 0, 0.00, 0.00, 0.00, 161.80, NULL, '2026-03-04 18:13:58', '2026-03-04 18:13:58');

-- --------------------------------------------------------

--
-- Table structure for table `useraccountroles`
--

CREATE TABLE `useraccountroles` (
  `UserRoleID` int(11) NOT NULL,
  `AccountID` int(11) DEFAULT NULL,
  `RoleID` int(11) DEFAULT NULL,
  `AssignedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `useraccountroles`
--

INSERT INTO `useraccountroles` (`UserRoleID`, `AccountID`, `RoleID`, `AssignedAt`) VALUES
(2, 1, 1, '2026-02-08 16:34:53'),
(7, 2, 1, '2026-02-09 01:58:28'),
(8, 3, 3, '2026-02-09 07:19:29'),
(9, 4, 4, '2026-02-20 09:26:26'),
(13, 6, 2, '2026-02-21 14:00:20'),
(14, 7, 5, '2026-02-23 11:16:49'),
(16, 9, 7, '2026-03-01 08:39:36'),
(17, 10, 8, '2026-03-03 16:47:52'),
(18, 11, 6, '2026-03-04 18:18:39'),
(19, 16, 16, '2026-03-09 15:21:43'),
(20, 20, 1, '2026-03-15 15:53:34');

-- --------------------------------------------------------

--
-- Table structure for table `useraccounts`
--

CREATE TABLE `useraccounts` (
  `AccountID` int(11) NOT NULL,
  `EmployeeID` int(11) DEFAULT NULL,
  `Username` varchar(50) NOT NULL,
  `Email` varchar(150) DEFAULT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `OTP_Code` varchar(6) DEFAULT NULL,
  `OTP_Expiry` datetime DEFAULT NULL,
  `IsVerified` tinyint(1) DEFAULT 0,
  `AccountStatus` enum('Active','Inactive','Suspended') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `useraccounts`
--

INSERT INTO `useraccounts` (`AccountID`, `EmployeeID`, `Username`, `Email`, `PasswordHash`, `OTP_Code`, `OTP_Expiry`, `IsVerified`, `AccountStatus`) VALUES
(1, 1, 'Joshua Suruiz', 'suruiz.joshuabcp@gmail.com', '$2y$10$MW7j07pxzC/nS6nNW2gt2efiw8hHy0OifrVMDTgnJ5PJVw/1i4uGa', NULL, NULL, 1, 'Active'),
(2, 2, 'Red Gin Baldon', 'suruizandrie@gmail.com', '$2y$10$Xqmv8TP/YYiax3DseufwDOmKYC4CRdqmf4hd2ASgMcwttHL2HT4.K', NULL, NULL, 1, 'Active'),
(3, 3, 'Noriel Dimailig', 'riverojosh19@gmail.com', '$2y$10$h7FqYl3dpl5lxi9M.1MROe7mKykN0xiBfZ5qtbLrnwczzqMQV.6dK', NULL, NULL, 1, 'Active'),
(4, 4, 'Earl Alarcon', 'earl@gmail.com', '$2y$10$pNvPeIuYaJbrX1p6J.DC1uBfmkl.9LPpmpgEgLtvlH8n7Y.98Evqy', '725872', '2026-03-12 19:29:45', 1, 'Active'),
(6, 6, 'Glory Job', 'glory@gmail.com', '$2y$10$YobyvYhmp2hYgDAfhc0jvOImU.ue3DEh5mL9.KGzMKQiZ08ouN9ma', NULL, NULL, 1, 'Active'),
(7, 7, 'Miguel Padre', 'padre@gmail.com', '$2y$10$q5NZoXCW8I2ODBnbXyfaLek/7l1djFj.Xg7Co1WUTTmF/bTwYs8De', NULL, NULL, 1, 'Active'),
(9, 10, 'Mike Dabu', 'mike@gmail.com', '$2y$10$8ahdIMWbQZsAKJOYB0B67.2NyW4GDGH1HSv3m5XDk9YJhEz7hyUcy', NULL, NULL, 1, 'Active'),
(10, NULL, 'Charles Linao', 'charles@gmail.com', '$2y$10$t0GJKUE37GrdJyEloEg0q.S6YOgUPcbme3DyUlluYYdAig1Z/eg0.', NULL, NULL, 1, 'Active'),
(11, 8, 'Daniella Magtangob', 'daniella@gmail.com', '$2y$10$mGPP976yU7jexq.IBsGEe.D1sMCjK5ncsaCV8lGsNldI0v6GQw1Ki', NULL, NULL, 1, 'Active'),
(14, 14, 'joshua.suruiz', 'suruizjoshuaandrierivero@gmail.com', '$2y$10$sR.DBW1TiSGXFc.VXYQ6muGk9AyKW.96EwX75ZkaPzWygTkzeetjy', NULL, NULL, 0, 'Inactive'),
(15, 15, 'test.three', 'suruizjoshua72@gmail.com', '$2y$10$OTX4Q8EjqoZiV9a9n2F4LO.InCl4FqY2AT20CCp3e3QE7.5G97BjW', NULL, NULL, 1, 'Active'),
(16, 16, 'Jonnar Solis', 'Solis@gmail.com', '$2y$10$pIZubHkzzaT4Ev/nR5FT1elc5240OaxWmIXhj88IWQNqsQUl71/OG', NULL, NULL, 1, 'Active'),
(18, 18, 'earl.alarcon', 'earllaurencealarcon@gmail.com', '$2y$10$MZWsRgUGRlz3beYiuhrx.uuxQh9ltBG4VfChiuGlc3Dmh/YGhlHEa', NULL, NULL, 1, 'Active'),
(20, 20, 'buya', 'buya@gmail.com', '$2y$10$QEP7RjJqCEfSnuDszZZoVOfOcmS7i.o7fDYtthXIruxBhnPvJ0zTm', NULL, NULL, 1, 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts_payable`
--
ALTER TABLE `accounts_payable`
  ADD PRIMARY KEY (`id`),
  ADD KEY `batch_id` (`batch_id`);

--
-- Indexes for table `allowance_proposals`
--
ALTER TABLE `allowance_proposals`
  ADD PRIMARY KEY (`ProposalID`);

--
-- Indexes for table `allowance_types`
--
ALTER TABLE `allowance_types`
  ADD PRIMARY KEY (`AllowanceTypeID`);

--
-- Indexes for table `applicants`
--
ALTER TABLE `applicants`
  ADD PRIMARY KEY (`ApplicantID`),
  ADD KEY `PostID` (`PostID`);

--
-- Indexes for table `bankdetails`
--
ALTER TABLE `bankdetails`
  ADD PRIMARY KEY (`BankDetailID`),
  ADD KEY `EmployeeID` (`EmployeeID`);

--
-- Indexes for table `bank_applications`
--
ALTER TABLE `bank_applications`
  ADD PRIMARY KEY (`AppID`),
  ADD KEY `fk_ba_form` (`FormID`),
  ADD KEY `fk_bankapplications_employee` (`EmployeeID`);

--
-- Indexes for table `bank_forms_master`
--
ALTER TABLE `bank_forms_master`
  ADD PRIMARY KEY (`FormID`);

--
-- Indexes for table `bir_tax_settings`
--
ALTER TABLE `bir_tax_settings`
  ADD PRIMARY KEY (`period_id`);

--
-- Indexes for table `compensation_period`
--
ALTER TABLE `compensation_period`
  ADD PRIMARY KEY (`period_id`);

--
-- Indexes for table `competencies`
--
ALTER TABLE `competencies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `competency_categories`
--
ALTER TABLE `competency_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `competency_levels`
--
ALTER TABLE `competency_levels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `competency_questions`
--
ALTER TABLE `competency_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_competency_questions_competency` (`competency_id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`DepartmentID`);

--
-- Indexes for table `emergency_contacts`
--
ALTER TABLE `emergency_contacts`
  ADD PRIMARY KEY (`ContactID`),
  ADD KEY `EmployeeID` (`EmployeeID`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`EmployeeID`),
  ADD UNIQUE KEY `PersonalEmail` (`PersonalEmail`);

--
-- Indexes for table `employee_competencies`
--
ALTER TABLE `employee_competencies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `competency_id` (`competency_id`),
  ADD KEY `level_id` (`level_id`);

--
-- Indexes for table `employee_leave_balances`
--
ALTER TABLE `employee_leave_balances`
  ADD PRIMARY KEY (`BalanceID`),
  ADD UNIQUE KEY `uq_employee_leave_year` (`EmployeeID`,`LeaveTypeID`,`Year`),
  ADD KEY `idx_leavebalance_employee` (`EmployeeID`),
  ADD KEY `idx_leavebalance_type` (`LeaveTypeID`);

--
-- Indexes for table `employee_update_requests`
--
ALTER TABLE `employee_update_requests`
  ADD PRIMARY KEY (`RequestID`),
  ADD KEY `EmployeeID` (`EmployeeID`);

--
-- Indexes for table `employmentinformation`
--
ALTER TABLE `employmentinformation`
  ADD PRIMARY KEY (`EmploymentID`),
  ADD UNIQUE KEY `WorkEmail` (`WorkEmail`),
  ADD KEY `EmployeeID` (`EmployeeID`),
  ADD KEY `DepartmentID` (`DepartmentID`),
  ADD KEY `fk_employment_position` (`PositionID`),
  ADD KEY `fk_salary_grade` (`SalaryGradeID`);

--
-- Indexes for table `final_performance_rating`
--
ALTER TABLE `final_performance_rating`
  ADD PRIMARY KEY (`EvaluationID`),
  ADD UNIQUE KEY `unique_employee_period` (`EmployeeID`,`period_id`),
  ADD KEY `fk_period` (`period_id`);

--
-- Indexes for table `general_ledger`
--
ALTER TABLE `general_ledger`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grade_allowances`
--
ALTER TABLE `grade_allowances`
  ADD PRIMARY KEY (`GradeAllowanceID`),
  ADD UNIQUE KEY `unique_grade_allowance` (`SalaryGradeID`,`AllowanceTypeID`),
  ADD KEY `fk_grade_allowance_type` (`AllowanceTypeID`);

--
-- Indexes for table `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`HolidayID`),
  ADD UNIQUE KEY `uq_holiday_date` (`HolidayDate`),
  ADD KEY `idx_holidays_type` (`HolidayTypeID`),
  ADD KEY `idx_holidays_active_date` (`IsActive`,`HolidayDate`);

--
-- Indexes for table `holiday_type`
--
ALTER TABLE `holiday_type`
  ADD PRIMARY KEY (`HolidayTypeID`),
  ADD UNIQUE KEY `uq_ht_typecode` (`TypeCode`);

--
-- Indexes for table `interview_evaluations`
--
ALTER TABLE `interview_evaluations`
  ADD PRIMARY KEY (`EvaluationID`);

--
-- Indexes for table `interview_schedules`
--
ALTER TABLE `interview_schedules`
  ADD PRIMARY KEY (`ScheduleID`),
  ADD KEY `ApplicantID` (`ApplicantID`),
  ADD KEY `InterviewerID` (`InterviewerID`);

--
-- Indexes for table `job_postings`
--
ALTER TABLE `job_postings`
  ADD PRIMARY KEY (`PostID`),
  ADD KEY `RequisitionID` (`RequisitionID`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`LeaveRequestID`),
  ADD KEY `fk_lr_leavetype` (`LeaveTypeID`),
  ADD KEY `fk_lr_officer` (`OfficerApprovedBy`),
  ADD KEY `fk_lr_hr` (`HRApprovedBy`),
  ADD KEY `idx_lr_employee_status` (`EmployeeID`,`Status`),
  ADD KEY `idx_lr_dates` (`StartDate`,`EndDate`);

--
-- Indexes for table `leave_types`
--
ALTER TABLE `leave_types`
  ADD PRIMARY KEY (`LeaveTypeID`);

--
-- Indexes for table `master_data_dispatches`
--
ALTER TABLE `master_data_dispatches`
  ADD PRIMARY KEY (`DispatchID`),
  ADD KEY `EmployeeID` (`EmployeeID`),
  ADD KEY `Status` (`Status`);

--
-- Indexes for table `merit_matrix_settings`
--
ALTER TABLE `merit_matrix_settings`
  ADD PRIMARY KEY (`matrix_id`),
  ADD KEY `period_id` (`period_id`);

--
-- Indexes for table `merit_proposals`
--
ALTER TABLE `merit_proposals`
  ADD PRIMARY KEY (`ProposalID`);

--
-- Indexes for table `pagibig_settings`
--
ALTER TABLE `pagibig_settings`
  ADD PRIMARY KEY (`period_id`);

--
-- Indexes for table `payout_history`
--
ALTER TABLE `payout_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payroll_batches`
--
ALTER TABLE `payroll_batches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_payroll_batches_code` (`batch_code`);

--
-- Indexes for table `payroll_batch_items`
--
ALTER TABLE `payroll_batch_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_batch_employee` (`batch_id`,`employee_id`),
  ADD KEY `idx_batch` (`batch_id`),
  ADD KEY `idx_employee` (`employee_id`);

--
-- Indexes for table `payroll_item_components`
--
ALTER TABLE `payroll_item_components`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_item` (`item_id`);

--
-- Indexes for table `payroll_tax_simulation`
--
ALTER TABLE `payroll_tax_simulation`
  ADD PRIMARY KEY (`employee_id`);

--
-- Indexes for table `philhealth_settings`
--
ALTER TABLE `philhealth_settings`
  ADD PRIMARY KEY (`period_id`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`PositionID`),
  ADD KEY `DepartmentID` (`DepartmentID`),
  ADD KEY `fk_position_salary_grade` (`SalaryGradeID`);

--
-- Indexes for table `position_competencies`
--
ALTER TABLE `position_competencies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `position_id` (`position_id`),
  ADD KEY `competency_id` (`competency_id`),
  ADD KEY `level_id` (`level_id`);

--
-- Indexes for table `position_requests`
--
ALTER TABLE `position_requests`
  ADD PRIMARY KEY (`RequestID`),
  ADD KEY `DepartmentID` (`DepartmentID`),
  ADD KEY `SalaryGradeID` (`SalaryGradeID`);

--
-- Indexes for table `recruitment_requisitions`
--
ALTER TABLE `recruitment_requisitions`
  ADD PRIMARY KEY (`RequisitionID`),
  ADD KEY `PositionID` (`PositionID`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`RoleID`);

--
-- Indexes for table `salary_grades`
--
ALTER TABLE `salary_grades`
  ADD PRIMARY KEY (`SalaryGradeID`),
  ADD KEY `fk_salary_period` (`period_id`);

--
-- Indexes for table `salary_grade_proposals`
--
ALTER TABLE `salary_grade_proposals`
  ADD PRIMARY KEY (`ProposalID`),
  ADD KEY `SalaryGradeID` (`SalaryGradeID`);

--
-- Indexes for table `simulation_drafts`
--
ALTER TABLE `simulation_drafts`
  ADD PRIMARY KEY (`DraftID`),
  ADD KEY `period_id` (`period_id`);

--
-- Indexes for table `simulation_proposals`
--
ALTER TABLE `simulation_proposals`
  ADD PRIMARY KEY (`ProposalID`);

--
-- Indexes for table `simulation_proposal_items`
--
ALTER TABLE `simulation_proposal_items`
  ADD PRIMARY KEY (`ItemID`),
  ADD KEY `ProposalID` (`ProposalID`);

--
-- Indexes for table `sss_settings`
--
ALTER TABLE `sss_settings`
  ADD PRIMARY KEY (`period_id`);

--
-- Indexes for table `statutory_proposals`
--
ALTER TABLE `statutory_proposals`
  ADD PRIMARY KEY (`ProposalID`),
  ADD KEY `BatchReference` (`BatchReference`);

--
-- Indexes for table `system_notifications`
--
ALTER TABLE `system_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `taxbenefits`
--
ALTER TABLE `taxbenefits`
  ADD PRIMARY KEY (`BenefitID`),
  ADD KEY `EmployeeID` (`EmployeeID`);

--
-- Indexes for table `useraccountroles`
--
ALTER TABLE `useraccountroles`
  ADD PRIMARY KEY (`UserRoleID`),
  ADD KEY `AccountID` (`AccountID`),
  ADD KEY `RoleID` (`RoleID`);

--
-- Indexes for table `useraccounts`
--
ALTER TABLE `useraccounts`
  ADD PRIMARY KEY (`AccountID`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD UNIQUE KEY `EmployeeID` (`EmployeeID`),
  ADD KEY `idx_email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts_payable`
--
ALTER TABLE `accounts_payable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `allowance_proposals`
--
ALTER TABLE `allowance_proposals`
  MODIFY `ProposalID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `allowance_types`
--
ALTER TABLE `allowance_types`
  MODIFY `AllowanceTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `applicants`
--
ALTER TABLE `applicants`
  MODIFY `ApplicantID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `bankdetails`
--
ALTER TABLE `bankdetails`
  MODIFY `BankDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `bank_applications`
--
ALTER TABLE `bank_applications`
  MODIFY `AppID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bank_forms_master`
--
ALTER TABLE `bank_forms_master`
  MODIFY `FormID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `compensation_period`
--
ALTER TABLE `compensation_period`
  MODIFY `period_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `competencies`
--
ALTER TABLE `competencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `competency_categories`
--
ALTER TABLE `competency_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `competency_levels`
--
ALTER TABLE `competency_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `competency_questions`
--
ALTER TABLE `competency_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `DepartmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `emergency_contacts`
--
ALTER TABLE `emergency_contacts`
  MODIFY `ContactID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `EmployeeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `employee_competencies`
--
ALTER TABLE `employee_competencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `employee_leave_balances`
--
ALTER TABLE `employee_leave_balances`
  MODIFY `BalanceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `employee_update_requests`
--
ALTER TABLE `employee_update_requests`
  MODIFY `RequestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `employmentinformation`
--
ALTER TABLE `employmentinformation`
  MODIFY `EmploymentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `final_performance_rating`
--
ALTER TABLE `final_performance_rating`
  MODIFY `EvaluationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `general_ledger`
--
ALTER TABLE `general_ledger`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `grade_allowances`
--
ALTER TABLE `grade_allowances`
  MODIFY `GradeAllowanceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `holidays`
--
ALTER TABLE `holidays`
  MODIFY `HolidayID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `holiday_type`
--
ALTER TABLE `holiday_type`
  MODIFY `HolidayTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `interview_evaluations`
--
ALTER TABLE `interview_evaluations`
  MODIFY `EvaluationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `interview_schedules`
--
ALTER TABLE `interview_schedules`
  MODIFY `ScheduleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `job_postings`
--
ALTER TABLE `job_postings`
  MODIFY `PostID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `LeaveRequestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `LeaveTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `master_data_dispatches`
--
ALTER TABLE `master_data_dispatches`
  MODIFY `DispatchID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `merit_matrix_settings`
--
ALTER TABLE `merit_matrix_settings`
  MODIFY `matrix_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `merit_proposals`
--
ALTER TABLE `merit_proposals`
  MODIFY `ProposalID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `payout_history`
--
ALTER TABLE `payout_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `payroll_batches`
--
ALTER TABLE `payroll_batches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `payroll_batch_items`
--
ALTER TABLE `payroll_batch_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=181;

--
-- AUTO_INCREMENT for table `payroll_item_components`
--
ALTER TABLE `payroll_item_components`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2583;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `PositionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `position_competencies`
--
ALTER TABLE `position_competencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `position_requests`
--
ALTER TABLE `position_requests`
  MODIFY `RequestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `recruitment_requisitions`
--
ALTER TABLE `recruitment_requisitions`
  MODIFY `RequisitionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `RoleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `salary_grades`
--
ALTER TABLE `salary_grades`
  MODIFY `SalaryGradeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `salary_grade_proposals`
--
ALTER TABLE `salary_grade_proposals`
  MODIFY `ProposalID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `simulation_drafts`
--
ALTER TABLE `simulation_drafts`
  MODIFY `DraftID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `simulation_proposals`
--
ALTER TABLE `simulation_proposals`
  MODIFY `ProposalID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `simulation_proposal_items`
--
ALTER TABLE `simulation_proposal_items`
  MODIFY `ItemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=449;

--
-- AUTO_INCREMENT for table `statutory_proposals`
--
ALTER TABLE `statutory_proposals`
  MODIFY `ProposalID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `system_notifications`
--
ALTER TABLE `system_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `taxbenefits`
--
ALTER TABLE `taxbenefits`
  MODIFY `BenefitID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `useraccountroles`
--
ALTER TABLE `useraccountroles`
  MODIFY `UserRoleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `useraccounts`
--
ALTER TABLE `useraccounts`
  MODIFY `AccountID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applicants`
--
ALTER TABLE `applicants`
  ADD CONSTRAINT `applicants_ibfk_1` FOREIGN KEY (`PostID`) REFERENCES `job_postings` (`PostID`);

--
-- Constraints for table `bankdetails`
--
ALTER TABLE `bankdetails`
  ADD CONSTRAINT `bankdetails_ibfk_1` FOREIGN KEY (`EmployeeID`) REFERENCES `employee` (`EmployeeID`) ON DELETE CASCADE;

--
-- Constraints for table `bank_applications`
--
ALTER TABLE `bank_applications`
  ADD CONSTRAINT `fk_ba_form` FOREIGN KEY (`FormID`) REFERENCES `bank_forms_master` (`FormID`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_bankapplications_employee` FOREIGN KEY (`EmployeeID`) REFERENCES `employee` (`EmployeeID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bir_tax_settings`
--
ALTER TABLE `bir_tax_settings`
  ADD CONSTRAINT `fk_bir_period` FOREIGN KEY (`period_id`) REFERENCES `compensation_period` (`period_id`) ON DELETE CASCADE;

--
-- Constraints for table `competencies`
--
ALTER TABLE `competencies`
  ADD CONSTRAINT `competencies_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `competency_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `competency_questions`
--
ALTER TABLE `competency_questions`
  ADD CONSTRAINT `fk_competency_questions_competency` FOREIGN KEY (`competency_id`) REFERENCES `competencies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `emergency_contacts`
--
ALTER TABLE `emergency_contacts`
  ADD CONSTRAINT `emergency_contacts_ibfk_1` FOREIGN KEY (`EmployeeID`) REFERENCES `employee` (`EmployeeID`) ON DELETE CASCADE;

--
-- Constraints for table `employee_competencies`
--
ALTER TABLE `employee_competencies`
  ADD CONSTRAINT `employee_competencies_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`EmployeeID`),
  ADD CONSTRAINT `employee_competencies_ibfk_2` FOREIGN KEY (`competency_id`) REFERENCES `competencies` (`id`),
  ADD CONSTRAINT `employee_competencies_ibfk_3` FOREIGN KEY (`level_id`) REFERENCES `competency_levels` (`id`);

--
-- Constraints for table `employee_leave_balances`
--
ALTER TABLE `employee_leave_balances`
  ADD CONSTRAINT `fk_lb_employee` FOREIGN KEY (`EmployeeID`) REFERENCES `employee` (`EmployeeID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_lb_leavetype` FOREIGN KEY (`LeaveTypeID`) REFERENCES `leave_types` (`LeaveTypeID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `employee_update_requests`
--
ALTER TABLE `employee_update_requests`
  ADD CONSTRAINT `employee_update_requests_ibfk_1` FOREIGN KEY (`EmployeeID`) REFERENCES `employee` (`EmployeeID`) ON DELETE CASCADE;

--
-- Constraints for table `employmentinformation`
--
ALTER TABLE `employmentinformation`
  ADD CONSTRAINT `employmentinformation_ibfk_1` FOREIGN KEY (`EmployeeID`) REFERENCES `employee` (`EmployeeID`) ON DELETE CASCADE,
  ADD CONSTRAINT `employmentinformation_ibfk_2` FOREIGN KEY (`DepartmentID`) REFERENCES `department` (`DepartmentID`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_employment_position` FOREIGN KEY (`PositionID`) REFERENCES `positions` (`PositionID`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_salary_grade` FOREIGN KEY (`SalaryGradeID`) REFERENCES `salary_grades` (`SalaryGradeID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `final_performance_rating`
--
ALTER TABLE `final_performance_rating`
  ADD CONSTRAINT `fk_employee` FOREIGN KEY (`EmployeeID`) REFERENCES `employee` (`EmployeeID`),
  ADD CONSTRAINT `fk_period` FOREIGN KEY (`period_id`) REFERENCES `compensation_period` (`period_id`);

--
-- Constraints for table `grade_allowances`
--
ALTER TABLE `grade_allowances`
  ADD CONSTRAINT `fk_grade_allowance_salarygrade` FOREIGN KEY (`SalaryGradeID`) REFERENCES `salary_grades` (`SalaryGradeID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_grade_allowance_type` FOREIGN KEY (`AllowanceTypeID`) REFERENCES `allowance_types` (`AllowanceTypeID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `holidays`
--
ALTER TABLE `holidays`
  ADD CONSTRAINT `fk_holidays_type` FOREIGN KEY (`HolidayTypeID`) REFERENCES `holiday_type` (`HolidayTypeID`) ON UPDATE CASCADE;

--
-- Constraints for table `interview_schedules`
--
ALTER TABLE `interview_schedules`
  ADD CONSTRAINT `interview_schedules_ibfk_1` FOREIGN KEY (`ApplicantID`) REFERENCES `applicants` (`ApplicantID`),
  ADD CONSTRAINT `interview_schedules_ibfk_2` FOREIGN KEY (`InterviewerID`) REFERENCES `useraccounts` (`AccountID`);

--
-- Constraints for table `job_postings`
--
ALTER TABLE `job_postings`
  ADD CONSTRAINT `job_postings_ibfk_1` FOREIGN KEY (`RequisitionID`) REFERENCES `recruitment_requisitions` (`RequisitionID`) ON DELETE CASCADE;

--
-- Constraints for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD CONSTRAINT `fk_lr_employee` FOREIGN KEY (`EmployeeID`) REFERENCES `employee` (`EmployeeID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_lr_hr` FOREIGN KEY (`HRApprovedBy`) REFERENCES `useraccounts` (`AccountID`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_lr_leavetype` FOREIGN KEY (`LeaveTypeID`) REFERENCES `leave_types` (`LeaveTypeID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_lr_officer` FOREIGN KEY (`OfficerApprovedBy`) REFERENCES `useraccounts` (`AccountID`) ON DELETE SET NULL;

--
-- Constraints for table `merit_matrix_settings`
--
ALTER TABLE `merit_matrix_settings`
  ADD CONSTRAINT `merit_matrix_settings_ibfk_1` FOREIGN KEY (`period_id`) REFERENCES `compensation_period` (`period_id`);

--
-- Constraints for table `pagibig_settings`
--
ALTER TABLE `pagibig_settings`
  ADD CONSTRAINT `fk_pagibig_period` FOREIGN KEY (`period_id`) REFERENCES `compensation_period` (`period_id`) ON DELETE CASCADE;

--
-- Constraints for table `philhealth_settings`
--
ALTER TABLE `philhealth_settings`
  ADD CONSTRAINT `fk_philhealth_period` FOREIGN KEY (`period_id`) REFERENCES `compensation_period` (`period_id`) ON DELETE CASCADE;

--
-- Constraints for table `positions`
--
ALTER TABLE `positions`
  ADD CONSTRAINT `fk_position_salary_grade` FOREIGN KEY (`SalaryGradeID`) REFERENCES `salary_grades` (`SalaryGradeID`) ON DELETE SET NULL,
  ADD CONSTRAINT `positions_ibfk_1` FOREIGN KEY (`DepartmentID`) REFERENCES `department` (`DepartmentID`);

--
-- Constraints for table `position_competencies`
--
ALTER TABLE `position_competencies`
  ADD CONSTRAINT `position_competencies_ibfk_1` FOREIGN KEY (`position_id`) REFERENCES `positions` (`PositionID`),
  ADD CONSTRAINT `position_competencies_ibfk_2` FOREIGN KEY (`competency_id`) REFERENCES `competencies` (`id`),
  ADD CONSTRAINT `position_competencies_ibfk_3` FOREIGN KEY (`level_id`) REFERENCES `competency_levels` (`id`);

--
-- Constraints for table `position_requests`
--
ALTER TABLE `position_requests`
  ADD CONSTRAINT `position_requests_ibfk_1` FOREIGN KEY (`DepartmentID`) REFERENCES `department` (`DepartmentID`),
  ADD CONSTRAINT `position_requests_ibfk_2` FOREIGN KEY (`SalaryGradeID`) REFERENCES `salary_grades` (`SalaryGradeID`);

--
-- Constraints for table `recruitment_requisitions`
--
ALTER TABLE `recruitment_requisitions`
  ADD CONSTRAINT `recruitment_requisitions_ibfk_1` FOREIGN KEY (`PositionID`) REFERENCES `positions` (`PositionID`) ON DELETE CASCADE;

--
-- Constraints for table `salary_grades`
--
ALTER TABLE `salary_grades`
  ADD CONSTRAINT `fk_salary_period` FOREIGN KEY (`period_id`) REFERENCES `compensation_period` (`period_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `salary_grade_proposals`
--
ALTER TABLE `salary_grade_proposals`
  ADD CONSTRAINT `salary_grade_proposals_ibfk_1` FOREIGN KEY (`SalaryGradeID`) REFERENCES `salary_grades` (`SalaryGradeID`) ON DELETE CASCADE;

--
-- Constraints for table `simulation_drafts`
--
ALTER TABLE `simulation_drafts`
  ADD CONSTRAINT `simulation_drafts_ibfk_1` FOREIGN KEY (`period_id`) REFERENCES `compensation_period` (`period_id`);

--
-- Constraints for table `simulation_proposal_items`
--
ALTER TABLE `simulation_proposal_items`
  ADD CONSTRAINT `fk_proposal_hr` FOREIGN KEY (`ProposalID`) REFERENCES `simulation_proposals` (`ProposalID`) ON DELETE CASCADE;

--
-- Constraints for table `sss_settings`
--
ALTER TABLE `sss_settings`
  ADD CONSTRAINT `fk_sss_period` FOREIGN KEY (`period_id`) REFERENCES `compensation_period` (`period_id`) ON DELETE CASCADE;

--
-- Constraints for table `taxbenefits`
--
ALTER TABLE `taxbenefits`
  ADD CONSTRAINT `taxbenefits_ibfk_1` FOREIGN KEY (`EmployeeID`) REFERENCES `employee` (`EmployeeID`) ON DELETE CASCADE;

--
-- Constraints for table `useraccountroles`
--
ALTER TABLE `useraccountroles`
  ADD CONSTRAINT `useraccountroles_ibfk_1` FOREIGN KEY (`AccountID`) REFERENCES `useraccounts` (`AccountID`) ON DELETE CASCADE,
  ADD CONSTRAINT `useraccountroles_ibfk_2` FOREIGN KEY (`RoleID`) REFERENCES `roles` (`RoleID`) ON DELETE CASCADE;

--
-- Constraints for table `useraccounts`
--
ALTER TABLE `useraccounts`
  ADD CONSTRAINT `useraccounts_ibfk_1` FOREIGN KEY (`EmployeeID`) REFERENCES `employee` (`EmployeeID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
