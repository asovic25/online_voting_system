-- ============================================================
-- ONLINE VOTING SYSTEM
-- Database: online_voting_system
-- Developer: Asogwa Victor Nnamdi
-- Technology: PHP + MySQL
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- ------------------------------------------------------------
-- Create database
-- ------------------------------------------------------------

CREATE DATABASE IF NOT EXISTS `online_voting_system`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE `online_voting_system`;

-- ------------------------------------------------------------
-- Remove existing tables
-- ------------------------------------------------------------

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `votes`;
DROP TABLE IF EXISTS `contestants`;
DROP TABLE IF EXISTS `voters`;
DROP TABLE IF EXISTS `admins`;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- TABLE: admins
-- ============================================================

CREATE TABLE `admins` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `fullname` VARCHAR(100) NOT NULL,
    `username` VARCHAR(50) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_admin_username` (`username`),
    UNIQUE KEY `unique_admin_email` (`email`)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;


-- ============================================================
-- TABLE: contestants
-- ============================================================

CREATE TABLE `contestants` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `username` VARCHAR(100) NOT NULL,
    `dob` DATE NOT NULL,
    `phone` VARCHAR(20) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `state` VARCHAR(50) DEFAULT NULL,
    `password` VARCHAR(255) NOT NULL,
    `rpassword` VARCHAR(255) DEFAULT NULL,
    `party` VARCHAR(100) NOT NULL,
    `passport` VARCHAR(255) NOT NULL,

    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_contestant_username` (`username`)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;


-- ============================================================
-- TABLE: voters
-- ============================================================

CREATE TABLE `voters` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `voter_id` VARCHAR(50) DEFAULT NULL,
    `fullname` VARCHAR(150) DEFAULT NULL,
    `name` VARCHAR(100) NOT NULL,
    `username` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(20) NOT NULL,
    `nin` VARCHAR(50) DEFAULT NULL,
    `state` VARCHAR(100) DEFAULT NULL,
    `lga` VARCHAR(100) DEFAULT NULL,
    `dob` DATE NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `rpassword` VARCHAR(255) DEFAULT NULL,
    `passport` VARCHAR(255) NOT NULL,
    `unique_id` VARCHAR(10) NOT NULL,
    `has_voted` TINYINT(1) DEFAULT 0,

    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_voter_id` (`voter_id`),
    UNIQUE KEY `unique_voter_username` (`username`),
    UNIQUE KEY `unique_voter_email` (`email`),
    UNIQUE KEY `unique_voter_code` (`unique_id`)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;


-- ============================================================
-- TABLE: votes
-- ============================================================

CREATE TABLE `votes` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `voter_id` VARCHAR(10) NOT NULL,
    `contestant_id` INT(11) NOT NULL,

    PRIMARY KEY (`id`),

    KEY `idx_votes_voter_id` (`voter_id`),
    KEY `idx_votes_contestant_id` (`contestant_id`),

    CONSTRAINT `fk_votes_voter`
        FOREIGN KEY (`voter_id`)
        REFERENCES `voters` (`unique_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT `fk_votes_contestant`
        FOREIGN KEY (`contestant_id`)
        REFERENCES `contestants` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;


-- ============================================================
-- IMPORTANT:
-- NO REAL USERS, PASSWORDS, EMAILS, PHONE NUMBERS,
-- PHOTOGRAPHS OR VOTING RECORDS ARE INCLUDED.
-- ============================================================

COMMIT;

SET FOREIGN_KEY_CHECKS = 1;