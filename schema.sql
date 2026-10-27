-- ========================================================
-- UniversityMeetingDB - Database Schema & Sample Data
-- Department Communication Management System
-- ========================================================

CREATE DATABASE IF NOT EXISTS UniversityMeetingDB;
USE UniversityMeetingDB;

-- Disable foreign key checks for clean resets
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `Participate`;
DROP TABLE IF EXISTS `Confirm`;
DROP TABLE IF EXISTS `Agenda`;
DROP TABLE IF EXISTS `Meeting_Decision`;
DROP TABLE IF EXISTS `Meeting`;
DROP TABLE IF EXISTS `HOD`;
DROP TABLE IF EXISTS `Permanent_lecturer`;
DROP TABLE IF EXISTS `Temporary_lecturer`;
DROP TABLE IF EXISTS `Student_representative`;
DROP TABLE IF EXISTS `User`;

SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------------
-- 1. General Users
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `User` (
    UserId VARCHAR(10) PRIMARY KEY,
    Role VARCHAR(50) NOT NULL,
    Name VARCHAR(100) NOT NULL,
    ContactNumber VARCHAR(15) NOT NULL,
    Email VARCHAR(100) DEFAULT NULL
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- 2. Student Representatives
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS Student_representative (
    UserId VARCHAR(10) PRIMARY KEY,
    AcademicYear INT NOT NULL,
    RegNo VARCHAR(30) NOT NULL UNIQUE,

    CONSTRAINT chk_AcademicYear
        CHECK (AcademicYear > 0),

    CONSTRAINT fk_Student_User
        FOREIGN KEY (UserId)
        REFERENCES `User` (UserId)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- 3. Temporary Lecturers
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS Temporary_lecturer (
    UserId VARCHAR(10) PRIMARY KEY,
    TemporaryEmployeId VARCHAR(20) NOT NULL UNIQUE,
    ContractStartDate DATE NOT NULL,
    ContractEndDate DATE NOT NULL,

    CONSTRAINT chk_Temporary_ContractDates
        CHECK (ContractEndDate >= ContractStartDate),

    CONSTRAINT fk_Temporary_User
        FOREIGN KEY (UserId)
        REFERENCES `User` (UserId)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- 4. Permanent Lecturers
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS Permanent_lecturer (
    UserId VARCHAR(10) PRIMARY KEY,
    PermanentEmployeeId VARCHAR(20) NOT NULL UNIQUE,
    Designation VARCHAR(100) NOT NULL,

    CONSTRAINT fk_Permanent_User
        FOREIGN KEY (UserId)
        REFERENCES `User` (UserId)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- 5. Head of Department (HOD)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS HOD (
    UserId VARCHAR(10) PRIMARY KEY,
    AppoinmentStartDate DATE NOT NULL,
    AppoinmentEndDate DATE NOT NULL,

    CONSTRAINT chk_HOD_AppointmentDates
        CHECK (AppoinmentEndDate >= AppoinmentStartDate),

    CONSTRAINT fk_HOD_PermanentLecturer
        FOREIGN KEY (UserId)
        REFERENCES Permanent_lecturer (UserId)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- 6. Meetings
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS Meeting (
    MeetingId VARCHAR(10) PRIMARY KEY,
    MeetingName VARCHAR(150) NOT NULL,
    MeetingDate DATE NOT NULL,
    MeetingTime TIME NOT NULL,
    MeetingVenue VARCHAR(100) NOT NULL,
    Recording VARCHAR(255),
    HOD_user_id VARCHAR(10) NOT NULL,

    CONSTRAINT fk_Meeting_HOD
        FOREIGN KEY (HOD_user_id)
        REFERENCES HOD (UserId)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- 7. Decisions Made During Meetings
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS Meeting_Decision (
    MeetingId VARCHAR(10) NOT NULL,
    decision VARCHAR(255) NOT NULL,

    PRIMARY KEY (MeetingId, decision),

    CONSTRAINT fk_Decision_Meeting
        FOREIGN KEY (MeetingId)
        REFERENCES Meeting (MeetingId)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- 8. Agendas
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS Agenda (
    AgendaId VARCHAR(10) PRIMARY KEY,
    AgendaTitle VARCHAR(150) NOT NULL,
    Document VARCHAR(255) NOT NULL,
    HOD_user_id VARCHAR(10) NOT NULL,
    MeetingId VARCHAR(10) NOT NULL UNIQUE,

    CONSTRAINT fk_Agenda_HOD
        FOREIGN KEY (HOD_user_id)
        REFERENCES HOD (UserId)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_Agenda_Meeting
        FOREIGN KEY (MeetingId)
        REFERENCES Meeting (MeetingId)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- 9. Attendance Confirmations
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS Confirm (
    UserId VARCHAR(10) NOT NULL,
    MeetingId VARCHAR(10) NOT NULL,
    Status VARCHAR(20) NOT NULL,

    PRIMARY KEY (UserId, MeetingId),

    CONSTRAINT chk_Confirm_Status
        CHECK (Status IN ('Confirmed', 'Pending')),

    CONSTRAINT fk_Confirm_User
        FOREIGN KEY (UserId)
        REFERENCES `User` (UserId)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_Confirm_Meeting
        FOREIGN KEY (MeetingId)
        REFERENCES Meeting (MeetingId)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- 10. Meeting Participation
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS Participate (
    UserId VARCHAR(10) NOT NULL,
    MeetingId VARCHAR(10) NOT NULL,

    PRIMARY KEY (UserId, MeetingId),

    CONSTRAINT fk_Participate_User
        FOREIGN KEY (UserId)
        REFERENCES `User` (UserId)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_Participate_Meeting
        FOREIGN KEY (MeetingId)
        REFERENCES Meeting (MeetingId)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ========================================================
-- Sample Data for Demonstration & Assignment Submission
-- ========================================================

-- 1. Users
INSERT INTO `User` (`UserId`, `Role`, `Name`, `ContactNumber`, `Email`) VALUES
('U001', 'Head of Department', 'Dr. Alan Turing', '0771234567', 'alan.turing@univ.ac.lk'),
('U002', 'Permanent Lecturer', 'Dr. Grace Hopper', '0772345678', 'grace.hopper@univ.ac.lk'),
('U003', 'Permanent Lecturer', 'Dr. Ada Lovelace', '0773456789', 'ada.lovelace@univ.ac.lk'),
('U004', 'Student Representative', 'John Von Neumann', '0774567890', 'john.neumann@univ.ac.lk'),
('U005', 'Temporary Lecturer', 'Claude Shannon', '0775678901', 'claude.shannon@univ.ac.lk');

-- 2. Permanent Lecturers (Superclass: User)
INSERT INTO `Permanent_lecturer` (`UserId`, `PermanentEmployeeId`, `Designation`) VALUES
('U001', 'EMP-P001', 'Professor & Head of Department'),
('U002', 'EMP-P002', 'Senior Lecturer Gr. I'),
('U003', 'EMP-P003', 'Senior Lecturer Gr. II');

-- 3. Temporary Lecturers (Superclass: User)
INSERT INTO `Temporary_lecturer` (`UserId`, `TemporaryEmployeId`, `ContractStartDate`, `ContractEndDate`) VALUES
('U005', 'EMP-T001', '2026-01-01', '2026-12-31');

-- 4. Student Representatives (Superclass: User)
INSERT INTO `Student_representative` (`UserId`, `AcademicYear`, `RegNo`) VALUES
('U004', 3, 'REG/2023/CS/042');

-- 5. Head of Department (HOD) (Superclass: Permanent_lecturer)
INSERT INTO `HOD` (`UserId`, `AppoinmentStartDate`, `AppoinmentEndDate`) VALUES
('U001', '2025-01-01', '2027-12-31');

-- 6. Meetings
INSERT INTO `Meeting` (`MeetingId`, `MeetingName`, `MeetingDate`, `MeetingTime`, `MeetingVenue`, `Recording`, `HOD_user_id`) VALUES
('M001', 'Curriculum Committee Meeting', '2026-11-05', '09:30:00', 'Boardroom A', 'curriculum-review-m001.mp4', 'U001'),
('M002', 'Semester Examination Moderation', '2026-11-12', '14:00:00', 'Meeting Room B', NULL, 'U001'),
('M003', 'Staff-Student Liaison Committee', '2026-11-20', '10:00:00', 'Conference Hall 1', 'liaison-committee-m003.mp4', 'U001');

-- 7. Meeting Decisions (composite PK: MeetingId, decision)
INSERT INTO `Meeting_Decision` (`MeetingId`, `decision`) VALUES
('M001', 'Approved inclusion of Advanced Database Systems in curriculum.'),
('M001', 'Agreed on bi-weekly progress review for third-year database projects.'),
('M002', 'Finalized examination paper rubric and assigned secondary reviewers.');

-- 8. Agendas (Note: MeetingId is UNIQUE)
INSERT INTO `Agenda` (`AgendaId`, `AgendaTitle`, `Document`, `HOD_user_id`, `MeetingId`) VALUES
('A001', 'Review of Database Systems Syllabus', 'database_curriculum_v2.pdf', 'U001', 'M001'),
('A002', 'Final Paper Moderation Guidelines', 'exam_moderation_rules.pdf', 'U001', 'M002'),
('A003', 'Student Feedback & Action Items', 'student_feedback_2026.pdf', 'U001', 'M003');

-- 9. Confirmations (Status: Confirmed or Pending)
INSERT INTO `Confirm` (`UserId`, `MeetingId`, `Status`) VALUES
('U001', 'M001', 'Confirmed'),
('U002', 'M001', 'Confirmed'),
('U003', 'M001', 'Pending'),
('U004', 'M001', 'Confirmed'),
('U002', 'M002', 'Confirmed'),
('U003', 'M002', 'Pending');

-- 10. Participations
INSERT INTO `Participate` (`UserId`, `MeetingId`) VALUES
('U001', 'M001'),
('U002', 'M001'),
('U004', 'M001');
