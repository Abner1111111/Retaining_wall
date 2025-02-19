-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 08, 2025 at 12:29 PM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `retaining_wall_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `analysis_methods`
--

DROP TABLE IF EXISTS `analysis_methods`;
CREATE TABLE IF NOT EXISTS `analysis_methods` (
  `id` int NOT NULL AUTO_INCREMENT,
  `assessment_id` int DEFAULT NULL,
  `method_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assessment_id` (`assessment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assessments`
--

DROP TABLE IF EXISTS `assessments`;
CREATE TABLE IF NOT EXISTS `assessments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `age` int DEFAULT NULL,
  `external_factor` varchar(255) DEFAULT NULL,
  `issues_observed` varchar(255) DEFAULT NULL,
  `type_of_design` varchar(255) DEFAULT NULL,
  `type_of_material` varchar(255) DEFAULT NULL,
  `structural_analysis` varchar(255) DEFAULT NULL,
  `in_situ_conditions` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assessment_results`
--

DROP TABLE IF EXISTS `assessment_results`;
CREATE TABLE IF NOT EXISTS `assessment_results` (
  `id` int NOT NULL AUTO_INCREMENT,
  `assessment_id` int DEFAULT NULL,
  `issues_summary` text,
  `condition_diagnosis` text,
  `failure_types` text,
  `recommended_tests` text,
  `recommendations` text,
  `severity_level` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `causes_of_failure` text,
  `functionality` text,
  PRIMARY KEY (`id`),
  KEY `assessment_id` (`assessment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visual_indicators`
--

DROP TABLE IF EXISTS `visual_indicators`;
CREATE TABLE IF NOT EXISTS `visual_indicators` (
  `id` int NOT NULL AUTO_INCREMENT,
  `assessment_id` int DEFAULT NULL,
  `indicator_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assessment_id` (`assessment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
