/*
Navicat MySQL Data Transfer

Source Server         : Local
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : saloos_tools

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2016-04-29 21:50:48
*/


-- ----------------------------
-- Table structure for tgusers
-- ----------------------------
CREATE TABLE IF NOT EXISTS `tgusers` (
  `id` bigint(20) unsigned NOT NULL,
  `tguser_firstname` varchar(255) NOT NULL DEFAULT '',
  `tguser_lastname` varchar(255) DEFAULT NULL,
  `tguser_username` varchar(255) DEFAULT NULL,
  `tguser_number` varchar(20) DEFAULT NULL,
  `tguser_createdate` datetime DEFAULT NULL,
  `tguser_meta` mediumtext,
  `date_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `username` (`tguser_username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
