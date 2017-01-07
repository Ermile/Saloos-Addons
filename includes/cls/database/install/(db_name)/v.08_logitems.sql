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
-- Table structure for table `logitems`
--

CREATE TABLE IF NOT EXISTS `logitems` (
  `id` smallint(5) unsigned NOT NULL,
  `logitem_title` varchar(100) NOT NULL,
  `logitem_desc` varchar(999) DEFAULT NULL,
  `logitem_meta` mediumtext,
  `logitem_priority` enum('critical','high','medium','low') NOT NULL DEFAULT 'medium',
  `date_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `logitems`
--

INSERT INTO `logitems` (`id`, `logitem_title`, `logitem_desc`, `logitem_meta`, `logitem_priority`, `date_modified`) VALUES
(1, 'low priority', NULL, NULL, 'low', NULL),
(2, 'mediym priority', NULL, NULL, 'medium', NULL),
(3, 'high priority', NULL, NULL, 'high', NULL),
(4, 'critical priority', NULL, NULL, 'critical', NULL),
(5, 'php/error', NULL, NULL, 'critical', NULL),
(6, 'db/error', NULL, NULL, 'high', NULL),
(7, 'account/login', NULL, NULL, 'low', NULL),
(8, 'account/signup', NULL, NULL, 'medium', NULL),
(9, 'account/recovery', NULL, NULL, 'medium', NULL),
(10, 'account/change password', NULL, NULL, 'low', NULL),
(11, 'account/verification sms', NULL, NULL, 'low', NULL),
(12, 'account/verification email', NULL, NULL, 'medium', NULL),
(13, 'Page 400', NULL, NULL, 'low', NULL),
(14, 'Page 401 ', NULL, NULL, 'medium', NULL),
(15, 'Page 403', NULL, NULL, 'low', NULL),
(16, 'Page 404', NULL, NULL, 'low', NULL),
(17, 'Page 500', NULL, NULL, 'low', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `logitems`
--
ALTER TABLE `logitems`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `logitems`
--
ALTER TABLE `logitems`
  MODIFY `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=18;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
