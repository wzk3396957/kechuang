/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : task

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2019-06-29 09:27:43
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tk_admin
-- ----------------------------
DROP TABLE IF EXISTS `tk_admin`;
CREATE TABLE `tk_admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员表',
  `username` varchar(255) NOT NULL COMMENT '账号',
  `password` char(32) NOT NULL COMMENT '密码',
  `role_id` int(11) NOT NULL COMMENT '角色id',
  `create_at` int(10) NOT NULL DEFAULT '0' COMMENT '时间',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态（0冻结1启用）',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tk_admin
-- ----------------------------
INSERT INTO `tk_admin` VALUES ('1', 'admin', '0ebaa80e24fb73edc51af9ca013e516f', '1', '1530845318', '1', '0');
INSERT INTO `tk_admin` VALUES ('10', 'wei', '72a0d68fd82688692bfecea349883627', '11', '1561712949', '1', '0');
INSERT INTO `tk_admin` VALUES ('11', 'wei2', '72a0d68fd82688692bfecea349883627', '1', '1561713253', '0', '1');
