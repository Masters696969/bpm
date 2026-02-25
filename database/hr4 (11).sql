-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 25, 2026 at 07:08 PM
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
(2, 2, 'BDO', '230-31005-2026', 'Payroll'),
(3, 3, 'BDO', '222-444-332-222', 'Payroll'),
(4, 4, 'BDO', '323235566', 'Payroll'),
(5, 7, 'BDO', '321-313-321', 'Payroll'),
(6, 6, 'BDO', '230-31125-2026', 'Payroll');

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
(1, 3, 1, 'uploads/bank_submissions/emp3_1771691387.pdf', 'Confirmed', NULL, '2026-02-21 16:29:47', '2026-02-21 16:31:02'),
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
(2, 'HR Department');

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
(3, 'HRDS20261003', 'Noriel', 'G', 'Dimailig', '2004-05-06', 'Male', 'riverojosh19@gmail.com', '09555223344', 'Quezon City', NULL),
(4, 'HRS20261004', 'Earl', 'J.', 'Caber', '2004-04-02', 'Male', 'earl@gmail.com', '09321223344', 'Quezon City', NULL),
(6, 'HRM20261006', 'Glory', 'J', 'Job', '2001-04-04', 'Male', 'glory@gmail.comm', '09531223344', 'Quezon City', NULL),
(7, 'CA20261007', 'Miguel', 'M', 'Padre', '2005-05-03', 'Male', 'padre@gmail.com', '09535223344', 'Quezon City', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employee_update_requests`
--

CREATE TABLE `employee_update_requests` (
  `RequestID` int(11) NOT NULL,
  `EmployeeID` int(11) NOT NULL,
  `RequestType` varchar(100) NOT NULL DEFAULT 'Update Information',
  `RequestData` text NOT NULL,
  `Status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `RequestDate` datetime NOT NULL DEFAULT current_timestamp(),
  `ReviewedBy` int(11) DEFAULT NULL,
  `ReviewDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_update_requests`
--

INSERT INTO `employee_update_requests` (`RequestID`, `EmployeeID`, `RequestType`, `RequestData`, `Status`, `RequestDate`, `ReviewedBy`, `ReviewDate`) VALUES
(1, 3, 'Update Information', '{\"BankName\":\"BDO\",\"BankAccountNumber\":\"222-444-332-222\"}', 'Approved', '2026-02-20 23:03:38', 3, '2026-02-20 23:13:16'),
(2, 3, 'Update Information', '{\"BankName\":\"BDO\",\"BankAccountNumber\":\"222-444-332-222\"}', 'Approved', '2026-02-21 01:08:30', 3, '2026-02-21 01:09:23'),
(3, 3, 'Update Information', '{\"BankName\":\"BDO\",\"BankAccountNumber\":\"222-444-332-222\",\"AccountType\":\"Payroll\"}', 'Approved', '2026-02-21 01:19:33', 3, '2026-02-21 01:22:16');

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
  `HiringDate` date NOT NULL,
  `WorkEmail` varchar(150) DEFAULT NULL,
  `EmploymentStatus` varchar(50) DEFAULT NULL,
  `DigitalResume` varchar(255) DEFAULT NULL,
  `IDPicture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employmentinformation`
--

INSERT INTO `employmentinformation` (`EmploymentID`, `EmployeeID`, `DepartmentID`, `PositionID`, `SalaryGradeID`, `BaseSalary`, `HiringDate`, `WorkEmail`, `EmploymentStatus`, `DigitalResume`, `IDPicture`) VALUES
(1, 1, 1, 1, 6, 80000.00, '2026-02-08', 'suruiz.joshuabcp@gmail.com', 'Regular', NULL, NULL),
(2, 2, 1, 1, 6, 80000.00, '2026-02-09', 'suruizandrie@gmail.com', 'Regular', NULL, NULL),
(3, 3, 2, 2, 2, 21000.00, '2026-02-09', 'riverojosh19@gmail.com', 'Regular', NULL, NULL),
(4, 4, 2, 4, 1, 15000.00, '2026-02-08', 'earl@gmail.com', 'Regular', NULL, NULL),
(5, 6, 2, 3, 5, 53000.00, '2026-02-09', 'glory@gmail.com', 'Regular', NULL, NULL),
(6, 7, 2, 5, 4, 40000.00, '2026-02-09', 'padre@gmail.com', 'Regular', NULL, NULL);

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
  `SalaryGradeID` int(11) NOT NULL,
  `AllowanceTypeID` int(11) NOT NULL,
  `Amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grade_allowances`
--

INSERT INTO `grade_allowances` (`GradeAllowanceID`, `SalaryGradeID`, `AllowanceTypeID`, `Amount`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 1, 1, 2500.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06'),
(2, 1, 2, 1000.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06'),
(3, 1, 3, 400.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06'),
(4, 1, 4, 1500.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06'),
(5, 1, 6, 500.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06'),
(6, 2, 1, 2500.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06'),
(7, 2, 2, 1500.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06'),
(8, 2, 3, 400.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06'),
(9, 2, 4, 2500.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06'),
(10, 2, 6, 800.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06'),
(11, 3, 1, 2500.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06'),
(12, 3, 2, 2000.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06'),
(13, 3, 3, 400.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06'),
(14, 3, 4, 3500.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06'),
(15, 3, 6, 1200.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06'),
(16, 4, 1, 2500.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06'),
(17, 4, 2, 2500.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06'),
(18, 4, 3, 400.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06'),
(19, 4, 4, 5000.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06'),
(20, 4, 6, 1500.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06'),
(21, 5, 1, 2500.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06'),
(22, 5, 2, 3000.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06'),
(23, 5, 3, 400.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06'),
(24, 5, 4, 7000.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06'),
(25, 5, 6, 2000.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06'),
(26, 6, 1, 2500.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06'),
(27, 6, 2, 3500.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06'),
(28, 6, 3, 400.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06'),
(29, 6, 4, 10000.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06'),
(30, 6, 6, 3000.00, '2026-02-25 17:30:06', '2026-02-25 17:30:06');

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
(5, 'Compensation Analyst', 'CA', 2, 4, 1);

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
(5, 'Compensation Analyst', 'Professional who researches, analyzes, and designs employee pay structures (salaries, bonuses, benefits) to ensure internal fairness and external market competitiveness');

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
(1, 1, 'SG-1', 'Entry Level', 15000.00, 19000.00, 'PHP', 1, '2026-02-23 08:35:28', '2026-02-25 14:38:48', 'Entry Support (HR Staff, Finance Assistants)'),
(2, 1, 'SG-2', 'Professional I', 21000.00, 30000.00, 'PHP', 1, '2026-02-23 08:35:28', '2026-02-23 17:22:15', 'Professional I (Payroll Processor, HR Data Specialist)'),
(3, 1, 'SG-3', 'Professional II', 28000.00, 42000.00, 'PHP', 1, '2026-02-23 08:35:28', '2026-02-23 17:22:19', 'Professional II (HR Analyst, Finance Officer)'),
(4, 1, 'SG-4', 'Senior Associate\n', 40000.00, 55000.00, 'PHP', 1, '2026-02-23 08:35:28', '2026-02-23 17:22:22', 'Senior Specialist (Compensation Analyst, Senior Finance)'),
(5, 1, 'SG-5', 'Manager', 53000.00, 75000.00, 'PHP', 1, '2026-02-23 08:35:28', '2026-02-23 17:22:29', 'Management (HR Manager, Finance Manager)'),
(6, 1, 'SG-6', 'Executive', 80000.00, 120000.00, 'PHP', 1, '2026-02-23 08:35:28', '2026-02-23 17:22:33', 'Executive (Administrator, Director)');

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
(2, 2, '321-654-987-000', '54-1234567-8', '14-050123456-7', '1414-3434-5656', 'S', 'Verified'),
(3, 3, '321-456-789-000', '65-1234567-8', '21-050123456-7', '1312-3434-5656', 'S', 'Verified'),
(4, 4, '3321-654-987-000', '54-3234567-8', '14-03113456-7', '1431-3434-5656', 'S', 'Pending'),
(5, 7, '111-654-987-000', '54-333367-8', '14-04343456-7', '1414-1223-5656', 'S', 'Pending'),
(6, 6, '321-324-987-000', '14-1234567-8', '14-053123456-7', '114-3434-5656', 'S', 'Pending');

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
(14, 7, 5, '2026-02-23 11:16:49');

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
(7, 7, 'Miguel Padre', 'padre@gmail.com', '$2y$10$q5NZoXCW8I2ODBnbXyfaLek/7l1djFj.Xg7Co1WUTTmF/bTwYs8De', NULL, NULL, 1, 'Active');

--
-- Indexes for dumped tables
--

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
-- Indexes for table `pagibig_settings`
--
ALTER TABLE `pagibig_settings`
  ADD PRIMARY KEY (`period_id`);

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
-- Indexes for table `sss_settings`
--
ALTER TABLE `sss_settings`
  ADD PRIMARY KEY (`period_id`);

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
-- AUTO_INCREMENT for table `allowance_types`
--
ALTER TABLE `allowance_types`
  MODIFY `AllowanceTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `bankdetails`
--
ALTER TABLE `bankdetails`
  MODIFY `BankDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `DepartmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `emergency_contacts`
--
ALTER TABLE `emergency_contacts`
  MODIFY `ContactID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `EmployeeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `employee_update_requests`
--
ALTER TABLE `employee_update_requests`
  MODIFY `RequestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `employmentinformation`
--
ALTER TABLE `employmentinformation`
  MODIFY `EmploymentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `PositionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `RoleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `salary_grades`
--
ALTER TABLE `salary_grades`
  MODIFY `SalaryGradeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `taxbenefits`
--
ALTER TABLE `taxbenefits`
  MODIFY `BenefitID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `useraccountroles`
--
ALTER TABLE `useraccountroles`
  MODIFY `UserRoleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `useraccounts`
--
ALTER TABLE `useraccounts`
  MODIFY `AccountID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
