/*
 Navicat MySQL Data Transfer

 Source Server         : 本地
 Source Server Type    : MySQL
 Source Server Version : 50719
 Source Host           : localhost
 Source Database       : wk_caigou

 Target Server Type    : MySQL
 Target Server Version : 50719
 File Encoding         : utf-8

 Date: 12/08/2017 17:38:08 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `sc_email_log`
-- ----------------------------
DROP TABLE IF EXISTS `sc_email_log`;
CREATE TABLE `sc_email_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL DEFAULT '' COMMENT '邮箱',
  `type` tinyint(5) NOT NULL DEFAULT '1' COMMENT '邮件发送状态（1:等待发送，2:发送成功，3:发送失败）',
  `state` tinyint(5) NOT NULL DEFAULT '1' COMMENT '状态（1:有效，2:已失效）',
  `send_time` char(20) NOT NULL COMMENT '发送时间',
  `code` tinyint(5) NOT NULL DEFAULT '3' COMMENT '返回结果（1:成功，2:失败）',
  `u_time` char(20) NOT NULL DEFAULT '' COMMENT '验证时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;
