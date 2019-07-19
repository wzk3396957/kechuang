/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : task

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2019-06-29 09:27:50
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tk_auth
-- ----------------------------
DROP TABLE IF EXISTS `tk_auth`;
CREATE TABLE `tk_auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '权限表',
  `auth_name` varchar(255) DEFAULT NULL,
  `rules` varchar(255) DEFAULT NULL,
  `params` varchar(255) DEFAULT '[]' COMMENT '参数',
  `pid` int(11) DEFAULT NULL COMMENT '父ID',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '是否删除',
  `type` tinyint(1) DEFAULT '1' COMMENT '类型（1表示总后台2分后台）',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tk_auth
-- ----------------------------
INSERT INTO `tk_auth` VALUES ('1', '权限管理', null, '[]', '0', '0', '1');
INSERT INTO `tk_auth` VALUES ('2', '管理员列表', 'admin/admin', '[]', '1', '0', '1');
INSERT INTO `tk_auth` VALUES ('3', '角色管理', 'role/role', '[]', '1', '0', '1');
INSERT INTO `tk_auth` VALUES ('31', 'xxx', 'xxx', '[]', '0', '0', '1');
INSERT INTO `tk_auth` VALUES ('32', 'xxx', 'xxx', '[]', '31', '0', '1');
