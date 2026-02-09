-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 09, 2026 at 08:22 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

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
(1, 1, 'BDO', '001234567890', 'Savings'),
(2, 2, 'BDO', '112233445566', 'Savings');

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
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `EmployeeID` int(11) NOT NULL,
  `FirstName` varchar(100) NOT NULL,
  `MiddleName` varchar(100) DEFAULT NULL,
  `LastName` varchar(100) NOT NULL,
  `DateOfBirth` date NOT NULL,
  `Gender` varchar(20) DEFAULT NULL,
  `PersonalEmail` varchar(150) DEFAULT NULL,
  `PhoneNumber` varchar(20) DEFAULT NULL,
  `PermanentAddress` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`EmployeeID`, `FirstName`, `MiddleName`, `LastName`, `DateOfBirth`, `Gender`, `PersonalEmail`, `PhoneNumber`, `PermanentAddress`) VALUES
(1, 'Joshua', 'Rivero', 'Suruiz', '2004-04-06', 'Male', 'suruizandrie@gmail.com', '09111223344', 'Quezon City'),
(2, 'Red Gin', 'G', 'Baldon', '2005-04-06', 'Male', 'red@gmail.comm', '09111223344', 'Quezon City');

-- --------------------------------------------------------

--
-- Table structure for table `employmentinformation`
--

CREATE TABLE `employmentinformation` (
  `EmploymentID` int(11) NOT NULL,
  `EmployeeID` int(11) DEFAULT NULL,
  `DepartmentID` int(11) DEFAULT NULL,
  `PositionID` int(11) DEFAULT NULL,
  `HiringDate` date NOT NULL,
  `WorkEmail` varchar(150) DEFAULT NULL,
  `EmploymentStatus` varchar(50) DEFAULT NULL,
  `DigitalResume` varchar(255) DEFAULT NULL,
  `IDPicture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employmentinformation`
--

INSERT INTO `employmentinformation` (`EmploymentID`, `EmployeeID`, `DepartmentID`, `PositionID`, `HiringDate`, `WorkEmail`, `EmploymentStatus`, `DigitalResume`, `IDPicture`) VALUES
(1, 1, 1, 1, '2026-02-08', 'suruiz.joshuabcp@gmail.com', 'Regular', NULL, NULL),
(2, 2, 2, 1, '2026-02-09', 'suruizandrie@gmail.com', 'Regular', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `PositionID` int(11) NOT NULL,
  `PositionName` varchar(100) NOT NULL,
  `DepartmentID` int(11) DEFAULT NULL,
  `SalaryGradeID` int(11) DEFAULT NULL,
  `AuthorizedHeadcount` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`PositionID`, `PositionName`, `DepartmentID`, `SalaryGradeID`, `AuthorizedHeadcount`) VALUES
(1, 'Administrator', 1, 6, 1);

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
(3, 'HR Data Specialist', 'maintains, cleanses, and analyzes employee information');

-- --------------------------------------------------------

--
-- Table structure for table `salary_grades`
--

CREATE TABLE `salary_grades` (
  `SalaryGradeID` int(11) NOT NULL,
  `GradeLevel` varchar(10) NOT NULL,
  `MinSalary` decimal(15,2) NOT NULL,
  `MaxSalary` decimal(15,2) NOT NULL,
  `Description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `salary_grades`
--

INSERT INTO `salary_grades` (`SalaryGradeID`, `GradeLevel`, `MinSalary`, `MaxSalary`, `Description`) VALUES
(1, 'SG-1', 15000.00, 19000.00, 'Entry Support (HR Staff, Finance Assistants)'),
(2, 'SG-2', 21000.00, 30000.00, 'Professional I (Payroll Processor, HR Data Specialist)'),
(3, 'SG-3', 28000.00, 42000.00, 'Professional II (HR Analyst, Finance Officer)'),
(4, 'SG-4', 40000.00, 55000.00, 'Senior Specialist (Compensation Analyst, Senior Finance)'),
(5, 'SG-5', 53000.00, 75000.00, 'Management (HR Manager, Finance Manager)'),
(6, 'SG-6', 80000.00, 120000.00, 'Executive (Administrator, Director)');

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
(2, 2, '321-654-987-000', '54-1234567-8', '14-050123456-7', '1414-3434-5656', 'S', 'Verified');

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
(8, 3, 3, '2026-02-09 07:19:29');

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
(3, NULL, 'Noriel Dimailig', 'riverojosh19@gmail.com', '$2y$10$h7FqYl3dpl5lxi9M.1MROe7mKykN0xiBfZ5qtbLrnwczzqMQV.6dK', NULL, NULL, 1, 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bankdetails`
--
ALTER TABLE `bankdetails`
  ADD PRIMARY KEY (`BankDetailID`),
  ADD KEY `EmployeeID` (`EmployeeID`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`DepartmentID`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`EmployeeID`),
  ADD UNIQUE KEY `PersonalEmail` (`PersonalEmail`);

--
-- Indexes for table `employmentinformation`
--
ALTER TABLE `employmentinformation`
  ADD PRIMARY KEY (`EmploymentID`),
  ADD UNIQUE KEY `WorkEmail` (`WorkEmail`),
  ADD KEY `EmployeeID` (`EmployeeID`),
  ADD KEY `DepartmentID` (`DepartmentID`),
  ADD KEY `fk_employment_position` (`PositionID`);

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
  ADD PRIMARY KEY (`SalaryGradeID`);

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
-- AUTO_INCREMENT for table `bankdetails`
--
ALTER TABLE `bankdetails`
  MODIFY `BankDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `DepartmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `EmployeeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `employmentinformation`
--
ALTER TABLE `employmentinformation`
  MODIFY `EmploymentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `PositionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `RoleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `salary_grades`
--
ALTER TABLE `salary_grades`
  MODIFY `SalaryGradeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `taxbenefits`
--
ALTER TABLE `taxbenefits`
  MODIFY `BenefitID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `useraccountroles`
--
ALTER TABLE `useraccountroles`
  MODIFY `UserRoleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `useraccounts`
--
ALTER TABLE `useraccounts`
  MODIFY `AccountID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bankdetails`
--
ALTER TABLE `bankdetails`
  ADD CONSTRAINT `bankdetails_ibfk_1` FOREIGN KEY (`EmployeeID`) REFERENCES `employee` (`EmployeeID`) ON DELETE CASCADE;

--
-- Constraints for table `employmentinformation`
--
ALTER TABLE `employmentinformation`
  ADD CONSTRAINT `employmentinformation_ibfk_1` FOREIGN KEY (`EmployeeID`) REFERENCES `employee` (`EmployeeID`) ON DELETE CASCADE,
  ADD CONSTRAINT `employmentinformation_ibfk_2` FOREIGN KEY (`DepartmentID`) REFERENCES `department` (`DepartmentID`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_employment_position` FOREIGN KEY (`PositionID`) REFERENCES `positions` (`PositionID`) ON DELETE SET NULL;

--
-- Constraints for table `positions`
--
ALTER TABLE `positions`
  ADD CONSTRAINT `fk_position_salary_grade` FOREIGN KEY (`SalaryGradeID`) REFERENCES `salary_grades` (`SalaryGradeID`) ON DELETE SET NULL,
  ADD CONSTRAINT `positions_ibfk_1` FOREIGN KEY (`DepartmentID`) REFERENCES `department` (`DepartmentID`);

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
