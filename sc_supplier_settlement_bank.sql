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

 Date: 12/08/2017 17:37:53 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `sc_supplier_settlement_bank`
-- ----------------------------
DROP TABLE IF EXISTS `sc_supplier_settlement_bank`;
CREATE TABLE `sc_supplier_settlement_bank` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL COMMENT '用户ID',
  `supplier_id` int(11) NOT NULL COMMENT '供应商ID',
  `settlement_name` varchar(50) NOT NULL COMMENT '结算银行开户名',
  `settlement_number` varchar(50) NOT NULL COMMENT '结算银行账号',
  `bank_name` varchar(50) NOT NULL COMMENT '结算银行名称',
  `bank_branch_name` varchar(50) NOT NULL COMMENT '结算银行支行名称',
  `bank_address` varchar(50) NOT NULL COMMENT '结算银行所在地',
  `bank_branch_code` varchar(50) NOT NULL COMMENT '支行联行号',
  `city_code` tinyint(2) NOT NULL DEFAULT '0' COMMENT '绑定城市公司（0:未绑定）',
  `is_push` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否推送主数据（1:未推送，2:成功，3:失败）',
  `is_state` tinyint(2) NOT NULL DEFAULT '1' COMMENT '审核状态（1:未审核，2:等待审核，3:审核通过，4:审核失败）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;
