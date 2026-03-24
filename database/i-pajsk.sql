-- =====================================================
-- i-PAJSK Database Schema
-- SMK Dato' Haji Talib Karim
-- MySQL Database Structure
-- =====================================================

-- Create Database (Uncomment if needed)
-- CREATE DATABASE IF NOT EXISTS `i_pajsk` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE `i_pajsk`;

-- =====================================================
-- Table: users
-- Stores all user accounts (admin, teacher, student)
-- =====================================================
CREATE TABLE IF NOT EXISTS `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `ic_number` VARCHAR(255) NOT NULL COMMENT 'Format: 123456-78-9012',
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'teacher', 'student') NOT NULL,
    `phone` VARCHAR(255) NULL DEFAULT NULL,
    `gender` ENUM('male', 'female') NULL DEFAULT NULL,
    `remember_token` VARCHAR(100) NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_ic_number_unique` (`ic_number`),
    UNIQUE KEY `users_email_unique` (`email`),
    KEY `users_role_index` (`role`),
    KEY `users_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: teachers
-- Stores teacher-specific information
-- =====================================================
CREATE TABLE IF NOT EXISTS `teachers` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `staff_id` VARCHAR(255) NOT NULL COMMENT 'Format: STF2024001',
    `subject` VARCHAR(255) NOT NULL,
    `assigned_class` ENUM('alpha', 'delta', 'omega') NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `teachers_staff_id_unique` (`staff_id`),
    KEY `teachers_user_id_foreign` (`user_id`),
    KEY `teachers_deleted_at_index` (`deleted_at`),
    CONSTRAINT `teachers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: students
-- Stores student-specific information
-- =====================================================
CREATE TABLE IF NOT EXISTS `students` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `teacher_id` BIGINT UNSIGNED NOT NULL,
    `academic_session` VARCHAR(255) NOT NULL COMMENT 'Format: 2024/2025',
    `semester` ENUM('1', '2', '3') NOT NULL,
    `sports` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Activity participation',
    `club` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Activity participation',
    `uniform` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Activity participation',
    `position` VARCHAR(255) NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `students_user_id_foreign` (`user_id`),
    KEY `students_teacher_id_foreign` (`teacher_id`),
    KEY `students_deleted_at_index` (`deleted_at`),
    CONSTRAINT `students_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `students_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: activities
-- Stores student activity submissions
-- =====================================================
CREATE TABLE IF NOT EXISTS `activities` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `student_id` BIGINT UNSIGNED NOT NULL,
    `type` ENUM('uniform', 'club', 'sport', 'competition', 'extra') NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `level` ENUM('school', 'district', 'state', 'national', 'international') NOT NULL,
    `achievement` ENUM('participation', 'third', 'second', 'first') NOT NULL,
    `activity_date` DATE NOT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    `teacher_comment` TEXT NULL DEFAULT NULL,
    `approved_by` BIGINT UNSIGNED NULL DEFAULT NULL,
    `approved_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `activities_student_id_foreign` (`student_id`),
    KEY `activities_approved_by_foreign` (`approved_by`),
    KEY `activities_status_index` (`status`),
    KEY `activities_type_index` (`type`),
    KEY `activities_activity_date_index` (`activity_date`),
    CONSTRAINT `activities_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    CONSTRAINT `activities_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `teachers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: submissions
-- Stores activity submission records
-- =====================================================
CREATE TABLE IF NOT EXISTS `submissions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `activity_id` BIGINT UNSIGNED NOT NULL,
    `student_id` BIGINT UNSIGNED NOT NULL,
    `submitted_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    `teacher_feedback` TEXT NULL DEFAULT NULL,
    `reviewed_by` BIGINT UNSIGNED NULL DEFAULT NULL,
    `reviewed_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `submissions_activity_id_foreign` (`activity_id`),
    KEY `submissions_student_id_foreign` (`student_id`),
    KEY `submissions_reviewed_by_foreign` (`reviewed_by`),
    KEY `submissions_status_index` (`status`),
    CONSTRAINT `submissions_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
    CONSTRAINT `submissions_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    CONSTRAINT `submissions_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `teachers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: marks
-- Stores student PAJSK marks/score
-- =====================================================
CREATE TABLE IF NOT EXISTS `marks` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `student_id` BIGINT UNSIGNED NOT NULL,
    `uniform` INT NOT NULL DEFAULT 0 COMMENT 'Max: 20',
    `club` INT NOT NULL DEFAULT 0 COMMENT 'Max: 20',
    `sport` INT NOT NULL DEFAULT 0 COMMENT 'Max: 20',
    `competition` INT NOT NULL DEFAULT 0 COMMENT 'Max: 40',
    `extra` INT NOT NULL DEFAULT 0 COMMENT 'Extra curriculum',
    `total` INT NOT NULL DEFAULT 0 COMMENT 'Sum of components (capped at 100)',
    `grade` VARCHAR(255) NOT NULL DEFAULT 'E' COMMENT 'A/B/C/D/E',
    `is_manual_override` TINYINT(1) NOT NULL DEFAULT 0,
    `last_updated_by` BIGINT UNSIGNED NULL DEFAULT NULL,
    `override_reason` TEXT NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `marks_student_id_unique` (`student_id`),
    KEY `marks_student_id_foreign` (`student_id`),
    KEY `marks_last_updated_by_foreign` (`last_updated_by`),
    CONSTRAINT `marks_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    CONSTRAINT `marks_last_updated_by_foreign` FOREIGN KEY (`last_updated_by`) REFERENCES `teachers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: suggestion_rules
-- Stores suggestion/automation rules
-- =====================================================
CREATE TABLE IF NOT EXISTS `suggestion_rules` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `priority` INT NOT NULL DEFAULT 1,
    `active` TINYINT(1) NOT NULL DEFAULT 1,
    `conditions` JSON NOT NULL COMMENT 'Conditions stored as JSON',
    `actions` JSON NOT NULL COMMENT 'Actions stored as JSON',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `suggestion_rules_active_index` (`active`),
    KEY `suggestion_rules_priority_index` (`priority`),
    KEY `suggestion_rules_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: documents
-- Stores activity proof documents/files
-- =====================================================
CREATE TABLE IF NOT EXISTS `documents` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `activity_id` BIGINT UNSIGNED NOT NULL,
    `filename` VARCHAR(255) NOT NULL,
    `original_name` VARCHAR(255) NOT NULL,
    `path` VARCHAR(255) NOT NULL,
    `mime_type` VARCHAR(255) NOT NULL,
    `size` INT NOT NULL COMMENT 'Size in bytes',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `documents_activity_id_foreign` (`activity_id`),
    CONSTRAINT `documents_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: cache
-- Laravel cache table
-- =====================================================
CREATE TABLE IF NOT EXISTS `cache` (
    `key` VARCHAR(255) NOT NULL,
    `value` MEDIUMTEXT NOT NULL,
    `expiration` INT NOT NULL,
    PRIMARY KEY (`key`),
    KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: cache_locks
-- Laravel cache locks table
-- =====================================================
CREATE TABLE IF NOT EXISTS `cache_locks` (
    `key` VARCHAR(255) NOT NULL,
    `owner` VARCHAR(255) NOT NULL,
    `expiration` INT NOT NULL,
    PRIMARY KEY (`key`),
    KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Additional Indexes for Performance
-- =====================================================

-- Index for searching students by academic session
CREATE INDEX `idx_students_academic_session` ON `students` (`academic_session`);

-- Index for searching activities by date range
CREATE INDEX `idx_activities_date_range` ON `activities` (`activity_date`, `status`);

-- Index for teacher class assignments
CREATE INDEX `idx_teachers_assigned_class` ON `teachers` (`assigned_class`);

-- =====================================================
-- End of Schema
-- =====================================================

