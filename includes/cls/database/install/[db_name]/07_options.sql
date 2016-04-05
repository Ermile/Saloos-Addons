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
-- Table structure for table `options`
--

CREATE TABLE IF NOT EXISTS `options` (
  `id` bigint(20) unsigned NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `post_id` bigint(20) unsigned DEFAULT NULL,
  `option_cat` varchar(50) NOT NULL,
  `option_key` varchar(50) NOT NULL,
  `option_value` varchar(255) DEFAULT NULL,
  `option_meta` mediumtext,
  `option_status` enum('enable','disable','expire') NOT NULL DEFAULT 'enable',
  `date_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`id`, `user_id`, `post_id`, `option_cat`, `option_key`, `option_value`, `option_meta`, `option_status`, `date_modified`) VALUES
(1, NULL, NULL, 'global', 'email', 'info@saloos.ir', NULL, '', NULL),
(2, NULL, NULL, 'global', 'auto_mail', 'no-reply@saloos.ir', NULL, '', NULL),
(19, NULL, NULL, 'permission_id', '1', 'admin', NULL, '', NULL),
(20, NULL, NULL, 'permission_id', '2', 'editor', NULL, '', NULL),
(21, NULL, NULL, 'permission_id', '3', 'viewer', NULL, '', NULL),
(30, NULL, NULL, 'twitter', 'status', 'enable', NULL, 'disable', NULL),
(31, NULL, NULL, 'twitter', 'ConsumerKey', NULL, NULL, 'enable', NULL),
(32, NULL, NULL, 'twitter', 'ConsumerSecret', NULL, NULL, 'enable', NULL),
(33, NULL, NULL, 'twitter', 'AccessToken', NULL, NULL, 'enable', NULL),
(34, NULL, NULL, 'twitter', 'AccessTokenSecret', NULL, NULL, 'enable', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cat+key+value` (`option_cat`,`option_key`,`option_value`) USING BTREE,
  ADD KEY `options_users_id` (`user_id`),
  ADD KEY `options_posts_id` (`post_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=39;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `options`
--
ALTER TABLE `options`
  ADD CONSTRAINT `options_posts_id` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `options_users_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
