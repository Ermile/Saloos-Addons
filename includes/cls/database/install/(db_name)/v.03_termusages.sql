-- phpMyAdmin SQL Dump
-- version 4.4.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 04, 2016 at 10:23 AM
-- Server version: 5.6.25
-- PHP Version: 5.6.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dddddddddddddddddddddddd`
--

-- --------------------------------------------------------

--
-- Table structure for table `termusages`
--

CREATE TABLE IF NOT EXISTS `termusages` (
  `term_id` int(10) unsigned NOT NULL,
  `termusage_id` bigint(20) unsigned NOT NULL,
  `termusage_foreign` enum('posts','products','attachments','files','comments') DEFAULT NULL,
  `termusage_order` smallint(5) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `termusages`
--

--
-- Indexes for dumped tables
--

--
-- Indexes for table `termusages`
--
ALTER TABLE `termusages`
  ADD UNIQUE KEY `term+type+object_unique` (`term_id`,`termusage_id`,`termusage_foreign`) USING BTREE;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `termusages`
--
ALTER TABLE `termusages`
  ADD CONSTRAINT `termusages_terms_id` FOREIGN KEY (`term_id`) REFERENCES `terms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
