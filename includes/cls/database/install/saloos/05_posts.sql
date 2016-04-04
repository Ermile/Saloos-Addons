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
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `id` bigint(20) unsigned NOT NULL,
  `post_language` char(2) DEFAULT NULL,
  `post_title` varchar(100) NOT NULL,
  `post_slug` varchar(100) NOT NULL,
  `post_url` varchar(255) NOT NULL,
  `post_content` mediumtext,
  `post_meta` mediumtext,
  `post_type` varchar(50) NOT NULL DEFAULT 'post',
  `post_comment` enum('open','closed') DEFAULT NULL,
  `post_count` smallint(5) unsigned DEFAULT NULL,
  `post_order` int(10) unsigned DEFAULT NULL,
  `post_status` enum('publish','draft','schedule','deleted','expire') NOT NULL DEFAULT 'draft',
  `post_parent` bigint(20) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `post_publishdate` datetime DEFAULT NULL,
  `date_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `post_language`, `post_title`, `post_slug`, `post_url`, `post_content`, `post_meta`, `post_type`, `post_comment`, `post_count`, `post_order`, `post_status`, `post_parent`, `user_id`, `post_publishdate`, `date_modified`) VALUES
(1, 'fa', 'درباره ما', 'about', 'about', '&amp;lt;p&amp;gt;&amp;lt;span style=&amp;quot;font-size: 1.35rem;&amp;quot;&amp;gt;این صفحه برای معرفی ما طراحی شده است!&amp;lt;/span&amp;gt;&amp;lt;br&amp;gt;&amp;lt;/p&amp;gt;', '{&quot;thumbid&quot;:&quot;&quot;,&quot;slug&quot;:&quot;about&quot;}', 'page', NULL, NULL, NULL, 'publish', NULL, 1, '2015-10-31 18:45:55', NULL),
(2, 'fa', 'سلام:)', 'hi', 'news/hi', '&amp;lt;p&amp;gt;سلام کهکشان!&amp;lt;/p&amp;gt;&amp;lt;p&amp;gt;اگر شما بازدید کننده هستید: این سایت تازه راه&zwnj;اندازی شده و به امید خدا به زودی مطالب جدید در اون منتشر خواهد شد.&amp;lt;/p&amp;gt;&amp;lt;p&amp;gt;اگر مدیر وب&zwnj;سایت هستید: برای انتشار می&zwnj;تونید همین مطلب رو ویرایش کرده و یا یک نوشته جدید منتشر کنید.&amp;lt;/p&amp;gt;', '{&quot;thumbid&quot;:&quot;&quot;,&quot;slug&quot;:&quot;hi&quot;}', 'post', NULL, NULL, NULL, 'publish', NULL, 1, '2015-10-31 20:45:54', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `url_unique` (`post_url`,`post_language`) USING BTREE,
  ADD KEY `posts_users_id` (`user_id`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_users_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
