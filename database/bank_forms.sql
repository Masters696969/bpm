-- ============================================================
--  Bank Form Management Schema
--  Run in phpMyAdmin or any MySQL client against `microfinance`
-- ============================================================

-- 1. Master blank forms (HR Data Specialist uploads here)
CREATE TABLE IF NOT EXISTS `bank_forms_master` (
  `FormID`     INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `FormName`   VARCHAR(255) NOT NULL,
  `FilePath`   VARCHAR(500) NOT NULL,
  `IsActive`   TINYINT(1)   NOT NULL DEFAULT 1,
  `UploadedBy` VARCHAR(100),
  `CreatedAt`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Employee submissions (filled PDFs)
CREATE TABLE IF NOT EXISTS `bank_applications` (
  `AppID`       INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `EmployeeID`  INT          NOT NULL,
  `FormID`      INT,
  `UploadedPDF` VARCHAR(500) NOT NULL,
  `Status`      ENUM('Pending','Sent to Bank','Confirmed') NOT NULL DEFAULT 'Pending',
  `Notes`       TEXT,
  `CreatedAt`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt`   TIMESTAMP    ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_ba_form FOREIGN KEY (`FormID`) REFERENCES `bank_forms_master`(`FormID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Optional: link bankdetails to application
-- ALTER TABLE `bankdetails` ADD COLUMN IF NOT EXISTS `AppID` INT NULL;
