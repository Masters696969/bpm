-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 07, 2026 at 07:19 PM
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
(7, 2, 'BDO', '229-411-332-222', 'Payroll');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `compensation_period`
--

INSERT INTO `compensation_period` (`period_id`, `period_name`, `start_date`, `end_date`, `effective_date`, `status`, `created_at`) VALUES
(1, 'FY2026', '2026-01-01', '2026-02-15', '2026-03-01', 'Active', '2026-02-23 17:21:48');

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
(6, 6, 'Jean', 'Mother', '09204132131', 1);

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
(10, 'SV20261009', 'Mike', NULL, 'Dabu', '0000-00-00', NULL, NULL, NULL, NULL, NULL);

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
  `DigitalResume` varchar(255) DEFAULT NULL,
  `IDPicture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employmentinformation`
--

INSERT INTO `employmentinformation` (`EmploymentID`, `EmployeeID`, `DepartmentID`, `PositionID`, `SalaryGradeID`, `BaseSalary`, `SalaryType`, `HiringDate`, `WorkEmail`, `EmploymentStatus`, `DigitalResume`, `IDPicture`) VALUES
(1, 1, 1, 1, 6, 80000.00, 'Monthly', '2026-02-08', 'suruiz.joshuabcp@gmail.com', 'Regular', NULL, NULL),
(2, 2, 1, 1, 6, 80000.00, 'Monthly', '2026-02-09', 'suruizandrie@gmail.com', 'Regular', NULL, NULL),
(3, 3, 2, 2, 2, 21000.00, 'Hourly', '2026-02-09', 'riverojosh19@gmail.com', 'Regular', NULL, NULL),
(4, 4, 2, 4, 1, 15000.00, 'Hourly', '2026-02-08', 'earl@gmail.com', 'Regular', NULL, NULL),
(5, 6, 2, 3, 5, 53000.00, 'Monthly', '2026-02-09', 'glory@gmail.com', 'Regular', NULL, NULL),
(6, 7, 2, 5, 4, 40000.00, 'Monthly', '2026-02-09', 'padre@gmail.com', 'Regular', NULL, NULL),
(7, 8, 2, 6, 3, 21000.00, 'Hourly', '2026-02-09', 'Daniella@gmail.com', 'Regular', NULL, NULL),
(8, 10, 2, 7, 4, 40000.00, 'Monthly', '2026-02-09', 'mike@gmail.com', 'Regular', NULL, NULL);

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
-- Table structure for table `payroll_batches`
--

CREATE TABLE `payroll_batches` (
  `id` int(11) NOT NULL,
  `batch_code` varchar(50) NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `pay_type` enum('Semi-Monthly','Monthly') NOT NULL DEFAULT 'Semi-Monthly',
  `status` enum('Processing','Pending Approval','Approved','Rejected','Finalized','Disbursed') NOT NULL DEFAULT 'Processing',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll_batches`
--

INSERT INTO `payroll_batches` (`id`, `batch_code`, `period_start`, `period_end`, `pay_type`, `status`, `created_by`, `created_at`) VALUES
(4, 'PR-2026-072', '2026-03-01', '2026-03-15', 'Semi-Monthly', 'Processing', 0, '2026-03-04 18:32:41'),
(5, 'PR-2026-366', '2026-03-01', '2026-03-15', 'Semi-Monthly', 'Processing', 0, '2026-03-04 18:56:08'),
(6, 'PR-2026-588', '2026-03-01', '2026-03-15', 'Semi-Monthly', 'Processing', 0, '2026-03-04 18:59:56'),
(7, 'PR-2026-498', '2026-03-01', '2026-03-15', 'Semi-Monthly', 'Processing', 0, '2026-03-04 19:02:58'),
(8, 'PR-2026-746', '2026-03-01', '2026-03-15', 'Semi-Monthly', '', 0, '2026-03-04 19:09:35'),
(9, 'PR-2026-053', '2026-03-01', '2026-03-15', 'Semi-Monthly', '', 0, '2026-03-04 19:32:41'),
(10, 'PR-2026-354', '2026-03-01', '2026-03-15', 'Semi-Monthly', '', 0, '2026-03-04 19:57:16'),
(11, 'PR-2026-744', '2026-03-01', '2026-03-15', 'Semi-Monthly', 'Approved', 0, '2026-03-04 19:59:36'),
(12, 'PR-2026-844', '2026-03-01', '2026-03-15', 'Semi-Monthly', 'Processing', 0, '2026-03-05 02:24:16'),
(13, 'PR-2026-324', '2026-03-01', '2026-03-15', 'Semi-Monthly', 'Pending Approval', 0, '2026-03-05 03:23:12'),
(14, 'PR-2026-058', '2026-03-16', '2026-03-31', 'Semi-Monthly', 'Processing', 0, '2026-03-06 17:38:19');

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
(4, 4, 1, 40000.00, 19400.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1975.00, 0.00, 57425.00, 'Computed', '2026-03-04 18:32:41'),
(5, 4, 2, 40000.00, 19400.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1975.00, 0.00, 57425.00, 'Computed', '2026-03-04 18:32:41'),
(6, 4, 3, 10500.00, 7700.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 887.50, 0.00, 17312.50, 'Computed', '2026-03-04 18:32:41'),
(7, 4, 4, 7500.00, 5900.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 662.50, 0.00, 12737.50, 'Computed', '2026-03-04 18:32:41'),
(8, 4, 6, 26500.00, 14900.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1637.50, 0.00, 39762.50, 'Computed', '2026-03-04 18:32:41'),
(9, 4, 7, 20000.00, 11900.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1475.00, 0.00, 30425.00, 'Computed', '2026-03-04 18:32:41'),
(10, 4, 8, 10500.00, 9600.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 887.50, 0.00, 19212.50, 'Computed', '2026-03-04 18:32:42'),
(11, 4, 10, 20000.00, 11900.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1475.00, 0.00, 30425.00, 'Computed', '2026-03-04 18:32:42'),
(12, 5, 1, 40000.00, 19400.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 5058.33, 2958.33, 57466.67, 'Computed', '2026-03-04 18:56:08'),
(13, 5, 2, 40000.00, 19400.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 4933.33, 2958.33, 56029.17, 'Computed', '2026-03-04 18:56:08'),
(14, 5, 3, 10500.00, 7700.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 1027.08, 8.33, 17172.92, 'Computed', '2026-03-04 18:56:08'),
(15, 5, 4, 7500.00, 5900.00, 375.00, 750.00, 0.00, 0.00, 187.50, 187.50, 100.00, 100.00, 674.22, 0.00, 13194.53, 'Computed', '2026-03-04 18:56:08'),
(16, 5, 6, 26500.00, 14900.00, 500.00, 1000.00, 375.00, 750.00, 662.50, 662.50, 100.00, 100.00, 3245.83, 1608.33, 38154.17, 'Computed', '2026-03-04 18:56:08'),
(17, 5, 7, 20000.00, 11900.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 2527.08, 958.33, 31247.92, 'Computed', '2026-03-04 18:56:08'),
(18, 5, 8, 10500.00, 9600.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 895.83, 8.33, 19204.17, 'Computed', '2026-03-04 18:56:08'),
(19, 5, 10, 20000.00, 11900.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 2454.17, 958.33, 29758.33, 'Computed', '2026-03-04 18:56:08'),
(20, 6, 1, 40000.00, 19400.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 7431.28, 5331.28, 55093.72, 'Computed', '2026-03-04 18:59:57'),
(21, 6, 2, 40000.00, 19400.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 12659.40, 10684.40, 48303.10, 'Computed', '2026-03-04 18:59:57'),
(22, 6, 3, 10500.00, 7700.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 2059.20, 1040.45, 16140.80, 'Computed', '2026-03-04 18:59:57'),
(23, 6, 4, 7500.00, 5900.00, 375.00, 750.00, 0.00, 0.00, 187.50, 187.50, 100.00, 100.00, 1090.93, 416.71, 12777.82, 'Computed', '2026-03-04 18:59:57'),
(24, 6, 6, 26500.00, 14900.00, 500.00, 1000.00, 375.00, 750.00, 662.50, 662.50, 100.00, 100.00, 7515.65, 5878.15, 33884.35, 'Computed', '2026-03-04 18:59:57'),
(25, 6, 7, 20000.00, 11900.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 5614.20, 4045.45, 28160.80, 'Computed', '2026-03-04 18:59:57'),
(26, 6, 8, 10500.00, 9600.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 2334.20, 1446.70, 17765.80, 'Computed', '2026-03-04 18:59:57'),
(27, 6, 10, 20000.00, 11900.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 5243.37, 3747.53, 26969.13, 'Computed', '2026-03-04 18:59:57'),
(28, 7, 1, 40000.00, 19400.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 7431.28, 5331.28, 55093.72, 'Computed', '2026-03-04 19:02:58'),
(29, 7, 2, 40000.00, 19400.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 12659.40, 10684.40, 48303.10, 'Computed', '2026-03-04 19:02:58'),
(30, 7, 3, 10500.00, 7700.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 2059.20, 1040.45, 16140.80, 'Computed', '2026-03-04 19:02:58'),
(31, 7, 4, 7500.00, 5900.00, 375.00, 750.00, 0.00, 0.00, 187.50, 187.50, 100.00, 100.00, 1090.93, 416.71, 12777.82, 'Computed', '2026-03-04 19:02:58'),
(32, 7, 6, 26500.00, 14900.00, 500.00, 1000.00, 375.00, 750.00, 662.50, 662.50, 100.00, 100.00, 7515.65, 5878.15, 33884.35, 'Computed', '2026-03-04 19:02:58'),
(33, 7, 7, 20000.00, 11900.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 5614.20, 4045.45, 28160.80, 'Computed', '2026-03-04 19:02:58'),
(34, 7, 8, 10500.00, 9600.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 2334.20, 1446.70, 17765.80, 'Computed', '2026-03-04 19:02:58'),
(35, 7, 10, 20000.00, 11900.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 5243.37, 3747.53, 26969.13, 'Computed', '2026-03-04 19:02:58'),
(36, 8, 1, 40000.00, 9700.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 7431.28, 5331.28, 45393.72, 'Computed', '2026-03-04 19:09:35'),
(37, 8, 2, 40000.00, 9700.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 10234.40, 8259.40, 41028.10, 'Computed', '2026-03-04 19:09:35'),
(38, 8, 3, 10500.00, 3850.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 1455.97, 437.22, 12894.03, 'Computed', '2026-03-04 19:09:35'),
(39, 8, 4, 7500.00, 2950.00, 375.00, 750.00, 0.00, 0.00, 187.50, 187.50, 100.00, 100.00, 674.22, 0.00, 10244.53, 'Computed', '2026-03-04 19:09:35'),
(40, 8, 6, 26500.00, 7450.00, 500.00, 1000.00, 375.00, 750.00, 662.50, 662.50, 100.00, 100.00, 5704.20, 4066.70, 28245.80, 'Computed', '2026-03-04 19:09:35'),
(41, 8, 7, 20000.00, 5950.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 4424.20, 2855.45, 23400.80, 'Computed', '2026-03-04 19:09:35'),
(42, 8, 8, 10500.00, 4800.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 1486.90, 599.40, 13813.10, 'Computed', '2026-03-04 19:09:35'),
(43, 8, 10, 20000.00, 5950.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 4053.37, 2557.53, 22209.13, 'Computed', '2026-03-04 19:09:35'),
(44, 9, 1, 40000.00, 9700.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 7431.28, 5331.28, 45393.72, 'Computed', '2026-03-04 19:32:41'),
(45, 9, 2, 40000.00, 9700.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 10234.40, 8259.40, 41028.10, 'Computed', '2026-03-04 19:32:41'),
(46, 9, 3, 10500.00, 3850.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 1455.97, 437.22, 12894.03, 'Computed', '2026-03-04 19:32:41'),
(47, 9, 4, 7500.00, 2950.00, 375.00, 750.00, 0.00, 0.00, 187.50, 187.50, 100.00, 100.00, 674.22, 0.00, 10244.53, 'Computed', '2026-03-04 19:32:41'),
(48, 9, 6, 26500.00, 7450.00, 500.00, 1000.00, 375.00, 750.00, 662.50, 662.50, 100.00, 100.00, 5704.20, 4066.70, 28245.80, 'Computed', '2026-03-04 19:32:41'),
(49, 9, 7, 20000.00, 5950.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 4424.20, 2855.45, 23400.80, 'Computed', '2026-03-04 19:32:41'),
(50, 9, 8, 10500.00, 4800.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 1486.90, 599.40, 13813.10, 'Computed', '2026-03-04 19:32:41'),
(51, 9, 10, 20000.00, 5950.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 4053.37, 2557.53, 22209.13, 'Computed', '2026-03-04 19:32:41'),
(52, 10, 1, 40000.00, 9700.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 7431.28, 5331.28, 45393.72, 'Computed', '2026-03-04 19:57:16'),
(53, 10, 2, 40000.00, 9700.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 10234.40, 8259.40, 41028.10, 'Computed', '2026-03-04 19:57:16'),
(54, 10, 3, 10500.00, 3850.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 1455.97, 437.22, 12894.03, 'Computed', '2026-03-04 19:57:16'),
(55, 10, 4, 7500.00, 2950.00, 375.00, 750.00, 0.00, 0.00, 187.50, 187.50, 100.00, 100.00, 674.22, 0.00, 10244.53, 'Computed', '2026-03-04 19:57:16'),
(56, 10, 6, 26500.00, 7450.00, 500.00, 1000.00, 375.00, 750.00, 662.50, 662.50, 100.00, 100.00, 5704.20, 4066.70, 28245.80, 'Computed', '2026-03-04 19:57:16'),
(57, 10, 7, 20000.00, 5950.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 4424.20, 2855.45, 23400.80, 'Computed', '2026-03-04 19:57:16'),
(58, 10, 8, 10500.00, 4800.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 1486.90, 599.40, 13813.10, 'Computed', '2026-03-04 19:57:16'),
(59, 10, 10, 20000.00, 5950.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 4053.37, 2557.53, 22209.13, 'Computed', '2026-03-04 19:57:16'),
(60, 11, 1, 40000.00, 9700.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 7431.28, 5331.28, 45393.72, 'Computed', '2026-03-04 19:59:37'),
(61, 11, 2, 40000.00, 9700.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 10234.40, 8259.40, 41028.10, 'Computed', '2026-03-04 19:59:37'),
(62, 11, 3, 10500.00, 3850.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 1455.97, 437.22, 12894.03, 'Computed', '2026-03-04 19:59:37'),
(63, 11, 4, 7500.00, 2950.00, 375.00, 750.00, 0.00, 0.00, 187.50, 187.50, 100.00, 100.00, 674.22, 0.00, 10244.53, 'Computed', '2026-03-04 19:59:37'),
(64, 11, 6, 26500.00, 7450.00, 500.00, 1000.00, 375.00, 750.00, 662.50, 662.50, 100.00, 100.00, 5704.20, 4066.70, 28245.80, 'Computed', '2026-03-04 19:59:37'),
(65, 11, 7, 20000.00, 5950.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 4424.20, 2855.45, 23400.80, 'Computed', '2026-03-04 19:59:37'),
(66, 11, 8, 10500.00, 4800.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 1486.90, 599.40, 13813.10, 'Computed', '2026-03-04 19:59:37'),
(67, 11, 10, 20000.00, 5950.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 4053.37, 2557.53, 22209.13, 'Computed', '2026-03-04 19:59:37'),
(68, 12, 1, 40000.00, 9700.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 7431.28, 5331.28, 45393.72, 'Computed', '2026-03-05 02:24:16'),
(69, 12, 2, 40000.00, 9700.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 10234.40, 8259.40, 41028.10, 'Computed', '2026-03-05 02:24:16'),
(70, 12, 3, 10500.00, 3850.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 1455.97, 437.22, 12894.03, 'Computed', '2026-03-05 02:24:16'),
(71, 12, 4, 7500.00, 2950.00, 375.00, 750.00, 0.00, 0.00, 187.50, 187.50, 100.00, 100.00, 674.22, 0.00, 10244.53, 'Computed', '2026-03-05 02:24:16'),
(72, 12, 6, 26500.00, 7450.00, 500.00, 1000.00, 375.00, 750.00, 662.50, 662.50, 100.00, 100.00, 5704.20, 4066.70, 28245.80, 'Computed', '2026-03-05 02:24:16'),
(73, 12, 7, 20000.00, 5950.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 4424.20, 2855.45, 23400.80, 'Computed', '2026-03-05 02:24:17'),
(74, 12, 8, 10500.00, 4800.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 1486.90, 599.40, 13813.10, 'Computed', '2026-03-05 02:24:17'),
(75, 12, 10, 20000.00, 5950.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 4053.37, 2557.53, 22209.13, 'Computed', '2026-03-05 02:24:17'),
(76, 13, 1, 40000.00, 9700.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 7431.28, 5331.28, 45393.72, 'Computed', '2026-03-05 03:23:12'),
(77, 13, 2, 40000.00, 9700.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 10234.40, 8259.40, 41028.10, 'Computed', '2026-03-05 03:23:12'),
(78, 13, 3, 10500.00, 3850.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 1455.97, 437.22, 12894.03, 'Computed', '2026-03-05 03:23:12'),
(79, 13, 4, 7500.00, 2950.00, 375.00, 750.00, 0.00, 0.00, 187.50, 187.50, 100.00, 100.00, 674.22, 0.00, 10244.53, 'Computed', '2026-03-05 03:23:12'),
(80, 13, 6, 26500.00, 7450.00, 500.00, 1000.00, 375.00, 750.00, 662.50, 662.50, 100.00, 100.00, 5704.20, 4066.70, 28245.80, 'Computed', '2026-03-05 03:23:13'),
(81, 13, 7, 20000.00, 5950.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 4424.20, 2855.45, 23400.80, 'Computed', '2026-03-05 03:23:13'),
(82, 13, 8, 10500.00, 4800.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 1486.90, 599.40, 13813.10, 'Computed', '2026-03-05 03:23:13'),
(83, 13, 10, 20000.00, 5950.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 4053.37, 2557.53, 22209.13, 'Computed', '2026-03-05 03:23:13'),
(84, 14, 1, 40000.00, 9700.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 10718.78, 8618.78, 42106.22, 'Computed', '2026-03-06 17:38:19'),
(85, 14, 2, 40000.00, 9700.00, 500.00, 1000.00, 375.00, 750.00, 1000.00, 1000.00, 100.00, 100.00, 10234.40, 8259.40, 41028.10, 'Computed', '2026-03-06 17:38:19'),
(86, 14, 3, 19090.91, 3850.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 3007.38, 1988.63, 19933.53, 'Computed', '2026-03-06 17:38:19'),
(87, 14, 4, 13636.36, 2950.00, 375.00, 750.00, 0.00, 0.00, 187.50, 187.50, 100.00, 100.00, 1568.88, 894.66, 15486.23, 'Computed', '2026-03-06 17:38:19'),
(88, 14, 6, 26500.00, 7450.00, 500.00, 1000.00, 375.00, 750.00, 662.50, 662.50, 100.00, 100.00, 5704.20, 4066.70, 28245.80, 'Computed', '2026-03-06 17:38:19'),
(89, 14, 7, 20000.00, 5950.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 4424.20, 2855.45, 23400.80, 'Computed', '2026-03-06 17:38:19'),
(90, 14, 8, 19090.91, 4800.00, 500.00, 1000.00, 25.00, 50.00, 262.50, 262.50, 100.00, 100.00, 3092.38, 2204.88, 20798.53, 'Computed', '2026-03-06 17:38:19'),
(91, 14, 10, 20000.00, 5950.00, 500.00, 1000.00, 375.00, 750.00, 500.00, 500.00, 100.00, 100.00, 4053.37, 2557.53, 22209.13, 'Computed', '2026-03-06 17:38:19');

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
(1272, 91, 'Deduction', 'Withholding Tax', 2557.53);

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
  `PositionCode` varchar(10) DEFAULT NULL,
  `DepartmentID` int(11) DEFAULT NULL,
  `SalaryGradeID` int(11) DEFAULT NULL,
  `AuthorizedHeadcount` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`PositionID`, `PositionName`, `PositionCode`, `DepartmentID`, `SalaryGradeID`, `AuthorizedHeadcount`) VALUES
(1, 'Administrator', 'ADM', 1, 6, 1),
(2, 'HR Data Specialist', 'HRDS', 2, 2, 1),
(3, 'HR Manager', 'HRM', 2, 5, 1),
(4, 'HR Staff', 'HRS', 2, 1, 1),
(5, 'Compensation Analyst', 'CA', 2, 4, 1),
(6, 'Payroll Processor', 'PAY', 2, 2, 1),
(7, 'Supervisor', 'SV', 2, 4, 1),
(8, 'Finance Manager', 'FIN-MGR', 3, 5, 1),
(9, 'Accountant', 'ACC', 3, 4, 2),
(10, 'Logistics Officer', 'LOG-OFF', 4, 3, 2),
(11, 'Inventory Clerk', 'INV-CLK', 4, 2, 3),
(12, 'Loan Service Associates', 'LSA', 5, 6, 1),
(13, 'Loan Officer', 'L-OFF', 5, 3, 5);

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
(8, 'Financial Officer', 'Is responsible for managing an organization\'s financial health by overseeing budgets, monitoring daily transactions, preparing financial reports, and ensuring compliance with regulations');

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
(2, 1, 'SG-2', 'Professional I', 21000.00, 30000.00, 'PHP', 1, '2026-02-23 08:35:28', '2026-03-04 04:58:20', 'Professional I (Payroll Processor, HR Data Specialist)'),
(3, 1, 'SG-3', 'Professional II', 29000.00, 42000.00, 'PHP', 1, '2026-02-23 08:35:28', '2026-03-04 04:58:20', 'Professional II (HR Analyst, Finance Officer)'),
(4, 1, 'SG-4', 'Senior Associate\n', 41000.00, 55000.00, 'PHP', 1, '2026-02-23 08:35:28', '2026-03-04 04:58:20', 'Senior Specialist (Compensation Analyst, Senior Finance)'),
(5, 1, 'SG-5', 'Manager', 54000.00, 75000.00, 'PHP', 1, '2026-02-23 08:35:28', '2026-03-04 04:58:20', 'Management (HR Manager, Finance Manager)'),
(6, 1, 'SG-6', 'Executive', 81000.00, 120000.00, 'PHP', 1, '2026-02-23 08:35:28', '2026-03-04 04:58:20', 'Executive (Administrator, Director)');

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
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `salary_grade_proposals`
--

INSERT INTO `salary_grade_proposals` (`ProposalID`, `BatchReference`, `SalaryGradeID`, `ProposedMinSalary`, `ProposedMaxSalary`, `Reason`, `ProposedBy`, `Status`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'batch_migration_1', 1, 16000.00, 20000.00, 'wala lang', 7, 'Applied', '2026-03-01 17:36:00', '2026-03-02 05:41:21'),
(2, 'batch_migration_1', 2, 21000.00, 30000.00, 'wala lang', 7, 'Applied', '2026-03-01 17:36:00', '2026-03-02 05:41:29'),
(3, 'batch_migration_1', 3, 28000.00, 42000.00, 'wala lang', 7, 'Applied', '2026-03-01 17:36:00', '2026-03-02 05:41:33'),
(4, 'batch_migration_1', 4, 40000.00, 55000.00, 'wala lang', 7, 'Applied', '2026-03-01 17:36:00', '2026-03-02 05:41:39'),
(5, 'batch_migration_1', 5, 53000.00, 75000.00, 'wala lang', 7, 'Applied', '2026-03-01 17:36:00', '2026-03-02 05:41:44'),
(6, 'batch_migration_1', 6, 80000.00, 120000.00, 'wala lang', 7, 'Applied', '2026-03-01 17:36:00', '2026-03-02 05:41:49'),
(7, 'batch_69a5258b092d11.14308175', 1, 15000.00, 20000.00, 'try', 7, 'Rejected', '2026-03-02 05:52:11', '2026-03-02 05:56:19'),
(8, 'batch_69a52733dabe87.45660111', 1, 15000.00, 20000.00, 'test', 7, 'Rejected', '2026-03-02 05:59:15', '2026-03-04 04:43:37'),
(9, 'batch_69a52733dabe87.45660111', 2, 20000.00, 30000.00, 'test', 7, 'Rejected', '2026-03-02 05:59:15', '2026-03-04 04:43:37'),
(10, 'batch_69a5746ded85e7.07419751', 1, 15000.00, 20000.00, 'try', 7, 'Pending', '2026-03-02 11:28:45', '2026-03-02 11:28:45'),
(11, 'batch_69a70b3f29d693.72011293', 1, 15000.00, 20000.00, 'test', 7, 'Pending', '2026-03-03 16:24:31', '2026-03-03 16:24:31'),
(12, 'batch_69a7165063a405.89337660', 1, 16001.00, 20000.00, 'test', 7, 'Pending', '2026-03-03 17:11:44', '2026-03-03 17:11:44'),
(13, 'batch_69a79325dcb7d5.30120775', 1, 14000.00, 20000.00, 'test', 7, 'Applied', '2026-03-04 02:04:21', '2026-03-04 03:21:42'),
(14, 'batch_69a795b3427832.47686676', 1, 14000.00, 20000.00, 'test2', 7, 'Applied', '2026-03-04 02:15:15', '2026-03-04 03:26:01'),
(15, 'batch_69a7a58ea81c90.34034724', 1, 15000.00, 20000.00, 'test', 7, 'Applied', '2026-03-04 03:22:54', '2026-03-04 03:28:02'),
(16, 'batch_69a7a58ea81c90.34034724', 2, 20000.00, 30000.00, 'test', 7, 'Applied', '2026-03-04 03:22:54', '2026-03-04 03:28:02'),
(17, 'batch_69a7b8abcfdda8.01844093', 1, 16000.00, 20000.00, 'TEST', 7, 'Applied', '2026-03-04 04:44:27', '2026-03-04 04:58:20'),
(18, 'batch_69a7b8abcfdda8.01844093', 2, 21000.00, 30000.00, 'TEST', 7, 'Applied', '2026-03-04 04:44:27', '2026-03-04 04:58:20'),
(19, 'batch_69a7b8abcfdda8.01844093', 3, 29000.00, 42000.00, 'TEST', 7, 'Applied', '2026-03-04 04:44:27', '2026-03-04 04:58:20'),
(20, 'batch_69a7b8abcfdda8.01844093', 4, 41000.00, 55000.00, 'TEST', 7, 'Applied', '2026-03-04 04:44:27', '2026-03-04 04:58:20'),
(21, 'batch_69a7b8abcfdda8.01844093', 5, 54000.00, 75000.00, 'TEST', 7, 'Applied', '2026-03-04 04:44:27', '2026-03-04 04:58:20'),
(22, 'batch_69a7b8abcfdda8.01844093', 6, 81000.00, 120000.00, 'TEST', 7, 'Applied', '2026-03-04 04:44:27', '2026-03-04 04:58:20'),
(23, 'batch_69a8fa843ac4b4.03580821', 1, 17000.00, 20000.00, 'test', 7, 'Pending', '2026-03-05 03:37:40', '2026-03-05 03:37:40');

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
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `simulation_drafts`
--

INSERT INTO `simulation_drafts` (`DraftID`, `CycleName`, `period_id`, `ProposedBy`, `BudgetUsedPct`, `TotalBudget`, `TotalCost`, `DateStarted`, `LastSaved`, `EmployeeData`, `Status`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'FY2025 Cycle', 1, NULL, 0.00, 5000000.00, 0.00, '2026-03-04 22:34:08', '2026-03-04 23:37:07', '[{\"EmployeeID\":\"1\",\"PropPct\":3,\"PropAmt\":2400,\"GradeID\":\"6\"},{\"EmployeeID\":\"2\",\"PropPct\":4,\"PropAmt\":3200,\"GradeID\":\"6\"},{\"EmployeeID\":\"7\",\"PropPct\":4,\"PropAmt\":1600,\"GradeID\":\"4\"},{\"EmployeeID\":\"3\",\"PropPct\":2,\"PropAmt\":420,\"GradeID\":\"2\"},{\"EmployeeID\":\"6\",\"PropPct\":4,\"PropAmt\":2120,\"GradeID\":\"5\"},{\"EmployeeID\":\"4\",\"PropPct\":3,\"PropAmt\":450,\"GradeID\":\"1\"},{\"EmployeeID\":\"8\",\"PropPct\":2,\"PropAmt\":420,\"GradeID\":\"3\"},{\"EmployeeID\":\"10\",\"PropPct\":2,\"PropAmt\":800,\"GradeID\":\"4\"}]', 'Draft', '2026-03-04 16:48:52', '2026-03-04 16:48:52'),
(2, 'FY2026', 1, 7, 0.00, 5000000.00, 0.00, '2026-03-05 01:01:03', '2026-03-05 10:51:12', '[{\"EmployeeID\":\"1\",\"PropPct\":3,\"PropAmt\":2400,\"GradeID\":\"6\"},{\"EmployeeID\":\"2\",\"PropPct\":4,\"PropAmt\":3200,\"GradeID\":\"6\"},{\"EmployeeID\":\"7\",\"PropPct\":4,\"PropAmt\":1600,\"GradeID\":\"4\"},{\"EmployeeID\":\"3\",\"PropPct\":2,\"PropAmt\":420,\"GradeID\":\"2\"},{\"EmployeeID\":\"6\",\"PropPct\":4,\"PropAmt\":2120,\"GradeID\":\"5\"},{\"EmployeeID\":\"4\",\"PropPct\":3,\"PropAmt\":450,\"GradeID\":\"1\"},{\"EmployeeID\":\"8\",\"PropPct\":2,\"PropAmt\":420,\"GradeID\":\"3\"},{\"EmployeeID\":\"10\",\"PropPct\":2,\"PropAmt\":800,\"GradeID\":\"4\"}]', 'Approved', '2026-03-04 17:01:03', '2026-03-05 02:51:12');

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
(63, 'compensation_cycle', 'supervisor', 'New salary scale change proposal submitted by Miguel Padre. Batch: batch_69a8fa843ac4b4.03580821', 0, '2026-03-05 03:37:40');

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
(18, 11, 6, '2026-03-04 18:18:39');

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
(4, 4, 'Earl Caber', 'earl@gmail.com', '$2y$10$pNvPeIuYaJbrX1p6J.DC1uBfmkl.9LPpmpgEgLtvlH8n7Y.98Evqy', NULL, NULL, 1, 'Active'),
(6, 6, 'Glory Job', 'glory@gmail.com', '$2y$10$YobyvYhmp2hYgDAfhc0jvOImU.ue3DEh5mL9.KGzMKQiZ08ouN9ma', NULL, NULL, 1, 'Active'),
(7, 7, 'Miguel Padre', 'padre@gmail.com', '$2y$10$q5NZoXCW8I2ODBnbXyfaLek/7l1djFj.Xg7Co1WUTTmF/bTwYs8De', NULL, NULL, 1, 'Active'),
(9, 10, 'Mike Dabu', 'mike@gmail.com', '$2y$10$8ahdIMWbQZsAKJOYB0B67.2NyW4GDGH1HSv3m5XDk9YJhEz7hyUcy', NULL, NULL, 1, 'Active'),
(10, NULL, 'Charles Linao', 'charles@gmail.com', '$2y$10$t0GJKUE37GrdJyEloEg0q.S6YOgUPcbme3DyUlluYYdAig1Z/eg0.', NULL, NULL, 1, 'Active'),
(11, 8, 'Daniella Magtangob', 'daniella@gmail.com', '$2y$10$mGPP976yU7jexq.IBsGEe.D1sMCjK5ncsaCV8lGsNldI0v6GQw1Ki', NULL, NULL, 1, 'Active');

--
-- Indexes for dumped tables
--

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
-- Indexes for table `grade_allowances`
--
ALTER TABLE `grade_allowances`
  ADD PRIMARY KEY (`GradeAllowanceID`),
  ADD UNIQUE KEY `unique_grade_allowance` (`SalaryGradeID`,`AllowanceTypeID`),
  ADD KEY `fk_grade_allowance_type` (`AllowanceTypeID`);

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
-- AUTO_INCREMENT for table `bankdetails`
--
ALTER TABLE `bankdetails`
  MODIFY `BankDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `DepartmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `emergency_contacts`
--
ALTER TABLE `emergency_contacts`
  MODIFY `ContactID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `EmployeeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `employee_update_requests`
--
ALTER TABLE `employee_update_requests`
  MODIFY `RequestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `employmentinformation`
--
ALTER TABLE `employmentinformation`
  MODIFY `EmploymentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `final_performance_rating`
--
ALTER TABLE `final_performance_rating`
  MODIFY `EvaluationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `grade_allowances`
--
ALTER TABLE `grade_allowances`
  MODIFY `GradeAllowanceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

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
-- AUTO_INCREMENT for table `payroll_batches`
--
ALTER TABLE `payroll_batches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `payroll_batch_items`
--
ALTER TABLE `payroll_batch_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `payroll_item_components`
--
ALTER TABLE `payroll_item_components`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1273;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `PositionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `RoleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `salary_grades`
--
ALTER TABLE `salary_grades`
  MODIFY `SalaryGradeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `salary_grade_proposals`
--
ALTER TABLE `salary_grade_proposals`
  MODIFY `ProposalID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `simulation_drafts`
--
ALTER TABLE `simulation_drafts`
  MODIFY `DraftID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `statutory_proposals`
--
ALTER TABLE `statutory_proposals`
  MODIFY `ProposalID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `system_notifications`
--
ALTER TABLE `system_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `taxbenefits`
--
ALTER TABLE `taxbenefits`
  MODIFY `BenefitID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `useraccountroles`
--
ALTER TABLE `useraccountroles`
  MODIFY `UserRoleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `useraccounts`
--
ALTER TABLE `useraccounts`
  MODIFY `AccountID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

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
-- Constraints for table `emergency_contacts`
--
ALTER TABLE `emergency_contacts`
  ADD CONSTRAINT `emergency_contacts_ibfk_1` FOREIGN KEY (`EmployeeID`) REFERENCES `employee` (`EmployeeID`) ON DELETE CASCADE;

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
