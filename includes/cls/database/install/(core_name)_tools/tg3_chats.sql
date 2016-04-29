/*
Navicat MySQL Data Transfer

Source Server         : Local
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : saloos_tools

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2016-04-29 21:52:02
*/


-- ----------------------------
-- Table structure for tgchats
-- ----------------------------
CREATE TABLE IF NOT EXISTS `tgchats` (
  `id` bigint(20) NOT NULL,
  `type` enum('private','group','supergroup','channel') NOT NULL,
  `tgchats_title` varchar(255) DEFAULT '',
  `tgchats_oldid` bigint(20) DEFAULT NULL COMMENT 'Unique chat identifieri this is filled when a chat is converted to a superchat',
  `tgchats_createdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tgchat_meta` mediumtext,
  `date_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `old_id` (`tgchats_oldid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
