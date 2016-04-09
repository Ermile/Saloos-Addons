/*
Navicat MySQL Data Transfer

Source Server         : Local
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : saloos_tools

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2016-04-09 20:09:44
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for services
-- ----------------------------
DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `service_name` varchar(50) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
