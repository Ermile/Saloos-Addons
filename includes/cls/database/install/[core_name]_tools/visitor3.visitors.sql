/*
Navicat MySQL Data Transfer

Source Server         : Local
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : saloos_tools

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2016-04-04 12:33:32
*/


-- ----------------------------
-- Table structure for visitors
-- ----------------------------
CREATE TABLE `visitors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `visitor_ip` int(10) unsigned NOT NULL,
  `url_id` int(10) unsigned NOT NULL,
  `agent_id` int(10) unsigned NOT NULL,
  `url_idreferer` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `visitor_createdate` datetime NOT NULL,
  `date_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `visitorip_index` (`visitor_ip`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
