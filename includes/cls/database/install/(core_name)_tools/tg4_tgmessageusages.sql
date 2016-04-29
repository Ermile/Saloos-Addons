/*
Navicat MySQL Data Transfer

Source Server         : Local
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : saloos_tools

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2016-04-29 21:52:20
*/


-- ----------------------------
-- Table structure for tgmessageusages
-- ----------------------------
CREATE TABLE IF NOT EXISTS `tgmessageusages` (
  `id` bigint(20) unsigned NOT NULL,
  `tguser_id` bigint(20) unsigned NOT NULL,
  `tgchat_id` bigint(20) DEFAULT NULL,
  `tgmessage_id` int(11) unsigned NOT NULL,
  `tgmessageusage_date` datetime NOT NULL,
  `tgmessageusage_reply` bigint(20) unsigned DEFAULT NULL,
  `tgmessageusage_meta` mediumtext CHARACTER SET utf8,
  `date_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tgmessageusages_users` (`tguser_id`),
  KEY `tgmessageusages_chats` (`tgchat_id`),
  KEY `tgmessageusages_messages` (`tgmessage_id`),
  CONSTRAINT `tgmessageusages_chats` FOREIGN KEY (`tgchat_id`) REFERENCES `tgchats` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `tgmessageusages_messages` FOREIGN KEY (`tgmessage_id`) REFERENCES `tgmessages` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `tgmessageusages_users` FOREIGN KEY (`tguser_id`) REFERENCES `tgusers` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
