/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : task

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2019-06-29 09:27:59
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tk_role
-- ----------------------------
DROP TABLE IF EXISTS `tk_role`;
CREATE TABLE `tk_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '角色表',
  `role_name` varchar(255) NOT NULL,
  `auth_id_list` varchar(255) NOT NULL COMMENT '权限集',
  `type` tinyint(1) DEFAULT '1' COMMENT '类型（1表示总后台，2表示分后台）',
  `remake` varchar(150) DEFAULT '' COMMENT '备注',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='角色表';

-- ----------------------------
-- Records of tk_role
-- ----------------------------
INSERT INTO `tk_role` VALUES ('1', '超级管理员', '*', '1', '', '1', '0');
INSERT INTO `tk_role` VALUES ('11', 'wwww', 'admin/admin', '1', '                                                                                                                                  asdasd              ', '1', '0');
INSERT INTO `tk_role` VALUES ('12', 'www', 'admin/admin', '1', '奥术大师', '1', '1');
