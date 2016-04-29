/*
Navicat MySQL Data Transfer

Source Server         : Local
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : saloos_tools

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2016-04-29 21:51:50
*/


-- ----------------------------
-- Table structure for tgmessages
-- ----------------------------
CREATE TABLE IF NOT EXISTS `tgmessages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tgmessage_text` text CHARACTER SET utf8 NOT NULL,
  `tgmessage_meta` mediumtext CHARACTER SET utf8,
  `tgmessage_status` enum('enable','disable','expire','filter','removed','spam') CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
