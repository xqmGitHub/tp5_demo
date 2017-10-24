/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50547
Source Host           : localhost:3306
Source Database       : tp5.cn

Target Server Type    : MYSQL
Target Server Version : 50547
File Encoding         : 65001

Date: 2017-09-16 18:19:38
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `tp_auth_group`
-- ----------------------------
DROP TABLE IF EXISTS `tp_auth_group`;
CREATE TABLE `tp_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户组id,自增主键',
  `module` varchar(20) NOT NULL COMMENT '用户组所属模块',
  `type` tinyint(4) NOT NULL COMMENT '组类型',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '用户组中文名称',
  `description` varchar(80) NOT NULL DEFAULT '' COMMENT '描述信息',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户组状态：为1正常，为0禁用,-1为删除',
  `rules` text COMMENT '用户组拥有的规则id，多个规则 , 隔开',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of tp_auth_group
-- ----------------------------
INSERT INTO `tp_auth_group` VALUES ('1', 'admin', '1', '公共权限', '公共权限，没有权限限制。', '1', '5,6');
INSERT INTO `tp_auth_group` VALUES ('2', 'admin', '1', '测试', '编辑', '1', '1,4,13,14,15,16,17,18,19,20,11,12');
INSERT INTO `tp_auth_group` VALUES ('3', 'admin', '1', '分组', '分组', '1', '4');

-- ----------------------------
-- Table structure for `tp_auth_group_access`
-- ----------------------------
DROP TABLE IF EXISTS `tp_auth_group_access`;
CREATE TABLE `tp_auth_group_access` (
  `uid` mediumint(8) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_auth_group_access
-- ----------------------------
INSERT INTO `tp_auth_group_access` VALUES ('2', '1');
INSERT INTO `tp_auth_group_access` VALUES ('2', '2');
INSERT INTO `tp_auth_group_access` VALUES ('3', '1');
INSERT INTO `tp_auth_group_access` VALUES ('3', '3');
INSERT INTO `tp_auth_group_access` VALUES ('4', '1');
INSERT INTO `tp_auth_group_access` VALUES ('4', '2');
INSERT INTO `tp_auth_group_access` VALUES ('4', '3');
INSERT INTO `tp_auth_group_access` VALUES ('6', '1');

-- ----------------------------
-- Table structure for `tp_auth_rule`
-- ----------------------------
DROP TABLE IF EXISTS `tp_auth_rule`;
CREATE TABLE `tp_auth_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '规则id,自增主键',
  `pid` int(11) NOT NULL COMMENT '父级ID',
  `module` varchar(20) NOT NULL COMMENT '规则所属module',
  `type` tinyint(2) NOT NULL DEFAULT '1',
  `name` char(80) NOT NULL COMMENT '规则唯一英文标识',
  `title` char(20) NOT NULL COMMENT '规则中文描述',
  `status` tinyint(1) NOT NULL COMMENT '是否有效(0:无效,1:有效)',
  `condition` varchar(300) NOT NULL COMMENT '规则附加条件',
  `is_nav` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0：URL，1：菜单',
  `icon` char(32) DEFAULT NULL COMMENT '图标',
  `hide` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  `is_dev` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否仅开发者模式可见',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='权限规则';

-- ----------------------------
-- Records of tp_auth_rule
-- ----------------------------
INSERT INTO `tp_auth_rule` VALUES ('1', '0', 'admin', '1', 'admin/authManager', '权限管理', '1', '', '1', '&#xe62b;', '1', '0');
INSERT INTO `tp_auth_rule` VALUES ('2', '1', 'admin', '1', 'admin/authManager/groupList', '分组管理', '1', '', '1', null, '1', '0');
INSERT INTO `tp_auth_rule` VALUES ('3', '1', 'admin', '1', 'admin/authManager/nodelist', '节点管理', '1', '', '1', null, '1', '0');
INSERT INTO `tp_auth_rule` VALUES ('4', '0', 'admin', '1', 'admin/systemSettings', '系统管理', '1', '', '1', '&#xe61d;', '1', '0');
INSERT INTO `tp_auth_rule` VALUES ('5', '0', 'admin', '1', 'admin/index/index', '首页', '1', '', '0', null, '0', '0');
INSERT INTO `tp_auth_rule` VALUES ('6', '0', 'admin', '1', 'admin/index/welcome', '欢迎', '1', '', '0', null, '0', '0');
INSERT INTO `tp_auth_rule` VALUES ('7', '4', 'admin', '1', 'admin/systemSettings/configlist', '配置管理', '1', '', '1', '', '1', '0');
INSERT INTO `tp_auth_rule` VALUES ('8', '3', 'admin', '1', 'admin/member/nodeadd', '添加节点', '1', '', '0', null, '0', '0');
INSERT INTO `tp_auth_rule` VALUES ('9', '1', 'admin', '1', 'admin/member/datalist', '用户管理', '1', '', '1', '', '1', '0');
INSERT INTO `tp_auth_rule` VALUES ('10', '4', 'admin', '1', 'admin/systemSettings/websitebase', '网站设置', '1', '', '1', '', '1', '0');
INSERT INTO `tp_auth_rule` VALUES ('11', '0', 'admin', '1', 'admin/cases', '作品管理', '1', '', '1', '', '1', '0');
INSERT INTO `tp_auth_rule` VALUES ('12', '11', 'admin', '1', 'admin/cases/caseslist', '作品', '1', '', '1', '', '1', '0');
INSERT INTO `tp_auth_rule` VALUES ('13', '4', 'admin', '1', 'admin/systemSettings/slidegrouplist', '幻灯片', '1', '', '1', '', '1', '0');
INSERT INTO `tp_auth_rule` VALUES ('14', '13', 'admin', '1', 'admin/systemSettings/slideGroupEdit', '重命名分组', '1', '', '0', '', '1', '0');
INSERT INTO `tp_auth_rule` VALUES ('15', '13', 'admin', '1', 'admin/systemSettings/slide', '浏览幻灯片', '1', '', '0', '', '1', '0');
INSERT INTO `tp_auth_rule` VALUES ('16', '13', 'admin', '1', 'admin/systemSettings/slideEdit', '修改幻灯片', '1', 'admin/systemSettings/slidefilesave', '0', '', '1', '0');
INSERT INTO `tp_auth_rule` VALUES ('17', '13', 'admin', '1', 'admin/systemSettings/slideDel', '删除幻灯片', '1', '', '0', '', '1', '0');
INSERT INTO `tp_auth_rule` VALUES ('18', '13', 'admin', '1', 'admin/systemSettings/slideAdd', '添加幻灯片', '1', '', '0', '', '1', '0');
INSERT INTO `tp_auth_rule` VALUES ('19', '13', 'admin', '1', 'admin/systemSettings/slideGroupDel', '删除分组', '1', '', '0', '', '1', '0');
INSERT INTO `tp_auth_rule` VALUES ('20', '13', 'admin', '1', 'admin/systemSettings/slidefilesave', '上传幻灯片', '1', '', '0', '', '1', '0');

-- ----------------------------
-- Table structure for `tp_cases`
-- ----------------------------
DROP TABLE IF EXISTS `tp_cases`;
CREATE TABLE `tp_cases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `name` varchar(64) NOT NULL COMMENT '作品名称',
  `money` int(11) NOT NULL COMMENT '预算',
  `style` int(2) NOT NULL COMMENT '风格',
  `huose_type` int(2) NOT NULL COMMENT '户型',
  `space` int(2) NOT NULL COMMENT '空间',
  `color` int(2) NOT NULL COMMENT '色系',
  `area` int(5) NOT NULL COMMENT '面积',
  `design_method` varchar(255) DEFAULT NULL COMMENT '设计理念',
  `show_index` smallint(5) NOT NULL DEFAULT '999' COMMENT '排序，0为不显示',
  `pics` varchar(255) DEFAULT NULL COMMENT '效果图',
  `cover` int(11) NOT NULL COMMENT '封面',
  `pid` int(11) DEFAULT NULL COMMENT '区/县',
  `view` int(5) DEFAULT '0' COMMENT '查看次数',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `add_time` int(11) NOT NULL COMMENT '添加时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='作品表';

-- ----------------------------
-- Records of tp_cases
-- ----------------------------
INSERT INTO `tp_cases` VALUES ('1', '1', '123', '213', '2', '3', '1', '2', '500', 'sda', '999', '1', '1', '1', '0', '1', '0', null);

-- ----------------------------
-- Table structure for `tp_collect`
-- ----------------------------
DROP TABLE IF EXISTS `tp_collect`;
CREATE TABLE `tp_collect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cases_id` int(11) NOT NULL COMMENT '收藏作品id',
  `uid` int(11) NOT NULL COMMENT '收藏人',
  `time` int(11) NOT NULL COMMENT '收藏时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='收藏表';

-- ----------------------------
-- Records of tp_collect
-- ----------------------------

-- ----------------------------
-- Table structure for `tp_config`
-- ----------------------------
DROP TABLE IF EXISTS `tp_config`;
CREATE TABLE `tp_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(30) NOT NULL COMMENT '配置标识',
  `type` tinyint(3) NOT NULL COMMENT '配置类型',
  `title` varchar(50) NOT NULL COMMENT '配置标题',
  `remark` varchar(100) NOT NULL COMMENT '详细说明',
  `group` tinyint(3) NOT NULL COMMENT '配置分组',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态',
  `value` text NOT NULL COMMENT '配置值',
  `sort` smallint(3) NOT NULL COMMENT '排序',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='配置表';

-- ----------------------------
-- Records of tp_config
-- ----------------------------
INSERT INTO `tp_config` VALUES ('1', 'CASES_SPACE', '0', '作品空间', '', '2', '1', '1:客厅\r\n2:餐厅\r\n3:卧室\r\n4:书房\r\n5:厨房\r\n6:卫生间', '0', '1504834022', '0');
INSERT INTO `tp_config` VALUES ('2', 'CASES_STYLE', '0', '作品风格', '', '2', '1', '1:欧式\r\n2:中式\r\n3:简约\r\n4:现代\r\n5:地中海', '0', '1504834118', '0');
INSERT INTO `tp_config` VALUES ('3', 'CASES_COLOR', '0', '作品色系', '', '2', '1', '1:浅色\r\n2:深色\r\n3:暖色\r\n4:冷色', '0', '1504834195', '0');
INSERT INTO `tp_config` VALUES ('4', 'CASES_HOUSE_TYPE', '0', '作品户型', '', '2', '1', '1:平层\r\n2:复式\r\n3:别墅', '0', '1504834263', '0');
INSERT INTO `tp_config` VALUES ('5', 'WEBSITE_BASECON', '0', '网站基本配置', '', '5', '1', '{\"WEBSITE_TITLE\":\"\\u6df1\\u5733\\u5e02\\u5c45\\u4f17\\u88c5\\u9970\\u8bbe\\u8ba1\\u5de5\\u7a0b\\u6709\\u9650\\u516c\\u53f8\",\"WEBSITE_KEYWORDS\":\"\\u6df1\\u5733\\u5e02\\u5c45\\u4f17\\u88c5\\u9970\\u8bbe\\u8ba1\\u5de5\\u7a0b\\u6709\\u9650\\u516c\\u53f8,\\u6df1\\u5733\\u5e02\\u5c45\\u4f17\\u88c5\\u9970\\u8bbe\\u8ba1\\u5de5\\u7a0b\\u6709\\u9650\\u516c\\u53f8\",\"WEBSITE_DESCRIPTION\":\"\\u88c5\\u4fee\\u5c31\\u627e\\u6df1\\u5733\\u5e02\\u5c45\\u4f17\\u88c5\\u9970\\u8bbe\\u8ba1\\u5de5\\u7a0b\\u6709\\u9650\\u516c\\u53f8\",\"WEBSITE_ICP\":\"\\u7ca4ICP\\u590705037286\\u53f7\",\"WEBSITE_COPYRIGHT\":\"Copyright@ 1996-2017\\u6df1\\u5733\\u5e02\\u5c45\\u4f17\\u88c5\\u9970\\u8bbe\\u8ba1\\u5de5\\u7a0b\\u6709\\u9650\\u516c\\u53f8\",\"WEBSITE_CLOSE\":\"1\",\"WEBSITE_CLOSE_TIP\":\"\\u7ad9\\u70b9\\u7ef4\\u62a4\\u4e2d\\u2026\\u2026\"}', '0', '1505551428', '1505551428');

-- ----------------------------
-- Table structure for `tp_file`
-- ----------------------------
DROP TABLE IF EXISTS `tp_file`;
CREATE TABLE `tp_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_id` int(11) DEFAULT NULL COMMENT '来源ID',
  `path` char(200) NOT NULL COMMENT '文件路径',
  `title` char(90) DEFAULT NULL COMMENT '标题',
  `extension` char(25) DEFAULT NULL COMMENT '扩展名',
  `size` int(10) DEFAULT NULL COMMENT '大小',
  `md5_hash` varchar(255) DEFAULT NULL COMMENT 'md5哈希值',
  `add_by` char(30) DEFAULT NULL COMMENT '添加者',
  `sort` smallint(5) DEFAULT '0' COMMENT '排序',
  `object_type` tinyint(2) DEFAULT NULL COMMENT '类型',
  `add_time` int(11) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='文件表';

-- ----------------------------
-- Records of tp_file
-- ----------------------------
INSERT INTO `tp_file` VALUES ('1', null, '.\\uploads\\File\\20170909\\92128b1ca58cb56ad13369bdab909edd.gif', '4.gif', 'image/gif', '29889', '57d13c7e0ac338ddd01e92d50f079912', '1', '0', null, '1504936584');
INSERT INTO `tp_file` VALUES ('2', null, '.\\uploads\\File\\20170909\\03c7a8609fb21451262399274fa79d52.gif', 'loading1.gif', 'image/gif', '4912', '8d4de8b51b9aa3ceb99712a2290cdf92', '1', '0', null, '1504936715');
INSERT INTO `tp_file` VALUES ('3', null, '.\\uploads\\File\\20170909\\8cdba85af8f9deeb4acf1b8487f25a14.gif', 'loading2.gif', 'image/gif', '6191', '72b9e4f3cc10fdf835d7ad3d9958f8c8', '1', '0', null, '1504936716');
INSERT INTO `tp_file` VALUES ('4', null, '.\\uploads\\File\\20170909\\6039702e39c6836bf45fb885c8457ecb.gif', 'loading3.gif', 'image/gif', '42347', 'b3abe1326a306159ea5f44f494448668', '1', '0', null, '1504936716');
INSERT INTO `tp_file` VALUES ('5', null, '.\\uploads\\File\\20170909\\17c26f4345fdbf874c2f0f3ecbf262a9.png', '8.png', 'image/png', '21111', 'e98fb94ea303934614952b34503f371b', '1', '0', null, '1504939804');
INSERT INTO `tp_file` VALUES ('6', null, '.\\uploads\\File\\20170909\\b83203ca60bd37c3fa1d06aeb6aad940.png', '3.png', 'image/png', '6640', '311ebc706574a6a25269506299067fb4', '1', '0', null, '1504940140');
INSERT INTO `tp_file` VALUES ('7', null, '.\\uploads\\File\\20170909\\7e6f9d5e2a654c54399c51225a54a807.jpg', '32f5.jpg', 'image/jpeg', '129604', 'b802c3eb407c0187f7b8f1e152209fb3', '1', '0', null, '1504940148');
INSERT INTO `tp_file` VALUES ('8', null, '.\\uploads\\File\\20170909\\aa53e2999c19224281bc4f842072a219.jpg', '1234.jpg', 'image/jpeg', '28232', 'd82dd4414bfbd7c53d152e112cbf78ba', '1', '0', null, '1504940154');
INSERT INTO `tp_file` VALUES ('9', null, '.\\uploads\\File\\20170909\\84661bec2c451af4a17078468c78da90.gif', '5352.gif', 'image/gif', '89085', '535536609a190bbf2b2aa131e0155034', '1', '0', null, '1504940158');
INSERT INTO `tp_file` VALUES ('10', null, '.\\uploads\\File\\20170909\\a0b5aeb8efefd3cc26d8eedeeb3d9a2d.gif', '1-1512091GP9.gif', 'image/gif', '1212465', '5d3bb4b801b3fda9e4ddf388d104470a', '1', '0', null, '1504940784');
INSERT INTO `tp_file` VALUES ('11', null, '.\\uploads\\File\\20170909\\16ae3de74f6d45677f3ebb8b82d2ff7c.jpg', '1a.jpg', 'image/jpeg', '60067', 'b3a876c329e43a4af2e626389bc4a033', '1', '0', '0', '1504942874');
INSERT INTO `tp_file` VALUES ('12', null, '.\\uploads\\File\\20170911\\9fbaae13312e0807ce01ac917eae8109.png', '1388.png', 'image/png', '696139', '1a05164c9c0439cbdc484fa02e823bc2', '1', '0', '0', '1505112820');
INSERT INTO `tp_file` VALUES ('13', null, '.\\uploads\\File\\20170911\\6a6d07e755af14241e96d044af9e2ffd.jpg', 'about03.jpg', 'image/jpeg', '24955', 'd382ea41bdc893d0dbd6010cca04f873', '1', '0', '0', '1505117732');
INSERT INTO `tp_file` VALUES ('14', null, '.\\uploads\\File\\20170912\\a788d83f702e7293056934dd919ccdfc.jpg', '1452500783519_519.jpg', 'image/jpeg', '11621', '250d3b171025d0ede3cf0f3c3eb660f3', '1', '0', '0', '1505199785');

-- ----------------------------
-- Table structure for `tp_follow`
-- ----------------------------
DROP TABLE IF EXISTS `tp_follow`;
CREATE TABLE `tp_follow` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '关注用户id',
  `to_uid` int(11) NOT NULL COMMENT '被关注的用户id',
  `follow_state` tinyint(2) NOT NULL DEFAULT '1' COMMENT '关注状态 1为单方关注 2为双方关注',
  `add_time` int(11) NOT NULL COMMENT '关注时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='关注表';

-- ----------------------------
-- Records of tp_follow
-- ----------------------------
INSERT INTO `tp_follow` VALUES ('1', '5', '55', '1', '1505286716');
INSERT INTO `tp_follow` VALUES ('2', '4', '44', '1', '1505286759');
INSERT INTO `tp_follow` VALUES ('3', '3', '33', '1', '1505286762');
INSERT INTO `tp_follow` VALUES ('4', '2', '22', '1', '1505286764');
INSERT INTO `tp_follow` VALUES ('5', '1', '11', '1', '1505286767');
INSERT INTO `tp_follow` VALUES ('6', '1', '111', '1', '1505286799');
INSERT INTO `tp_follow` VALUES ('7', '11', '1', '1', '1505445139');
INSERT INTO `tp_follow` VALUES ('8', '22', '2', '1', '1505445163');
INSERT INTO `tp_follow` VALUES ('9', '33', '3', '1', '1505445165');
INSERT INTO `tp_follow` VALUES ('10', '44', '4', '1', '1505445167');
INSERT INTO `tp_follow` VALUES ('11', '55', '5', '1', '1505445169');

-- ----------------------------
-- Table structure for `tp_member`
-- ----------------------------
DROP TABLE IF EXISTS `tp_member`;
CREATE TABLE `tp_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL COMMENT '开发者账号',
  `password` varchar(64) NOT NULL,
  `nick_name` varchar(32) DEFAULT NULL COMMENT '昵称',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `login_num` int(11) DEFAULT '0' COMMENT '登录次数',
  `last_login_time` int(11) DEFAULT NULL COMMENT '最后登录时间',
  `last_login_ip` bigint(20) DEFAULT NULL COMMENT '最后登录ip',
  `reg_ip` bigint(20) DEFAULT NULL COMMENT '注册IP',
  `reg_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `status` tinyint(2) DEFAULT '1' COMMENT '状态，1启用，0禁用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='会员表';

-- ----------------------------
-- Records of tp_member
-- ----------------------------
INSERT INTO `tp_member` VALUES ('1', 'admin', 'e298f837054efa675397cc51f6bd0965', 'admin', 'admin@123.com', '46', '1505549302', '2130706433', '2130706433', '1466391021', '1504763457', '1');
INSERT INTO `tp_member` VALUES ('2', 'test', 'e298f837054efa675397cc51f6bd0965', 'test', 'test@qq.com', '11', '1505547649', '2130706433', '2130706433', '1466127548', '1504763471', '1');
INSERT INTO `tp_member` VALUES ('3', 'test1', 'e298f837054efa675397cc51f6bd0965', '非常腻害', '123@123.com', '0', null, null, '2130706433', '1504665877', '1504677081', '1');
INSERT INTO `tp_member` VALUES ('4', 'test2', 'e298f837054efa675397cc51f6bd0965', 'test2', '123@132.com', '0', null, null, '2130706433', '1504666197', null, '1');
INSERT INTO `tp_member` VALUES ('6', 'edit', 'e298f837054efa675397cc51f6bd0965', 'edit', 'edit@123.com', '4', '1504835723', '2130706433', '2130706433', '1504776606', null, '1');

-- ----------------------------
-- Table structure for `tp_slide`
-- ----------------------------
DROP TABLE IF EXISTS `tp_slide`;
CREATE TABLE `tp_slide` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `group` smallint(5) unsigned NOT NULL,
  `title` varchar(60) NOT NULL,
  `main_link` varchar(255) NOT NULL,
  `target` tinyint(2) NOT NULL DEFAULT '0',
  `image` varchar(100) NOT NULL,
  `size` int(10) DEFAULT NULL,
  `extension` char(30) DEFAULT NULL,
  `md5_hash` varchar(255) DEFAULT NULL,
  `summary` text NOT NULL,
  `add_time` int(11) NOT NULL,
  `update_time` int(11) DEFAULT NULL,
  `sort` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='幻灯片';

-- ----------------------------
-- Records of tp_slide
-- ----------------------------
INSERT INTO `tp_slide` VALUES ('1', '1', '1', '1', '0', '.\\uploads\\File\\20170909\\16ae3de74f6d45677f3ebb8b82d2ff7c.jpg', null, null, null, '', '0', null, '1');
INSERT INTO `tp_slide` VALUES ('2', '1', '第二', '2', '0', '.\\uploads\\File\\20170911\\9fbaae13312e0807ce01ac917eae8109.png', null, null, null, '2', '0', null, '15');
INSERT INTO `tp_slide` VALUES ('3', '5', '修改了', '#', '1', '.\\uploads\\File\\20170909\\16ae3de74f6d45677f3ebb8b82d2ff7c.jpg', '60067', 'image/jpeg', 'b3a876c329e43a4af2e626389bc4a033', '修改了呀', '1505203318', '1505549011', '5');
INSERT INTO `tp_slide` VALUES ('4', '3', '', '4', '0', '.\\uploads\\File\\20170909\\16ae3de74f6d45677f3ebb8b82d2ff7c.jpg', null, null, null, '4', '0', null, '3');
INSERT INTO `tp_slide` VALUES ('5', '1', '1', '1', '0', '.\\uploads\\File\\20170911\\6a6d07e755af14241e96d044af9e2ffd.jpg', null, null, null, '', '0', null, '16');
INSERT INTO `tp_slide` VALUES ('7', '0', '', '', '0', '.\\uploads\\File\\20170912\\f0b4a2d413d82e3a0e5fab66f4679337.png', '733262', 'image/png', '8defad1afdce5103d29698dfe6717c9f', '', '1505195132', null, '0');
INSERT INTO `tp_slide` VALUES ('17', '0', '', '', '0', '.\\uploads\\File\\20170912\\288120ed6ba8a3c9cab5a3ff23e08e9b.jpg', '61966', 'image/jpeg', null, '', '1505200349', null, '0');
INSERT INTO `tp_slide` VALUES ('19', '7', '', '', '1', '.\\uploads\\File\\20170912\\82e90fff053f214a61e7a88a3b9effa3.png', '6640', 'image/png', '311ebc706574a6a25269506299067fb4', '', '1505206821', null, '0');

-- ----------------------------
-- Table structure for `tp_slide_group`
-- ----------------------------
DROP TABLE IF EXISTS `tp_slide_group`;
CREATE TABLE `tp_slide_group` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='幻灯片分组';

-- ----------------------------
-- Records of tp_slide_group
-- ----------------------------
INSERT INTO `tp_slide_group` VALUES ('1', '第一组');
INSERT INTO `tp_slide_group` VALUES ('2', '第二组');
INSERT INTO `tp_slide_group` VALUES ('3', '3');
INSERT INTO `tp_slide_group` VALUES ('7', '分组123');
INSERT INTO `tp_slide_group` VALUES ('5', '第五组');

-- ----------------------------
-- Table structure for `tp_user`
-- ----------------------------
DROP TABLE IF EXISTS `tp_user`;
CREATE TABLE `tp_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account` varchar(32) NOT NULL COMMENT '账号',
  `password` varchar(32) NOT NULL COMMENT '密码',
  `nick_name` varchar(64) DEFAULT NULL COMMENT '昵称',
  `login_num` int(11) NOT NULL DEFAULT '0' COMMENT '登陆次数',
  `reg_time` int(11) NOT NULL COMMENT '注册时间',
  `reg_ip` bigint(20) NOT NULL COMMENT '注册IP',
  `last_time` int(11) NOT NULL COMMENT '最后登陆时间',
  `last_ip` bigint(20) NOT NULL COMMENT '最后登陆IP',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=56 DEFAULT CHARSET=utf8 COMMENT='前台用户';

-- ----------------------------
-- Records of tp_user
-- ----------------------------
INSERT INTO `tp_user` VALUES ('1', '1', '1', '1', '0', '0', '0', '0', '0', '1');
INSERT INTO `tp_user` VALUES ('2', '2', '2', '2', '0', '0', '0', '0', '0', '1');
INSERT INTO `tp_user` VALUES ('3', '3', '3', '3', '0', '0', '0', '0', '0', '1');
INSERT INTO `tp_user` VALUES ('4', '4', '4', '4', '0', '0', '0', '0', '0', '1');
INSERT INTO `tp_user` VALUES ('5', '5', '5', '5', '0', '0', '0', '0', '0', '1');
INSERT INTO `tp_user` VALUES ('11', '11', '11', '11', '0', '0', '0', '0', '0', '1');
INSERT INTO `tp_user` VALUES ('22', '22', '22', '22', '0', '0', '0', '0', '0', '1');
INSERT INTO `tp_user` VALUES ('33', '33', '33', '33', '0', '0', '0', '0', '0', '1');
INSERT INTO `tp_user` VALUES ('44', '44', '44', '44', '0', '0', '0', '0', '0', '1');
INSERT INTO `tp_user` VALUES ('55', '55', '55', '55', '0', '0', '0', '0', '0', '1');

-- ----------------------------
-- Table structure for `tp_user_info`
-- ----------------------------
DROP TABLE IF EXISTS `tp_user_info`;
CREATE TABLE `tp_user_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户id',
  `name` varchar(30) CHARACTER SET ucs2 DEFAULT NULL COMMENT '真实姓名',
  `province` int(11) DEFAULT NULL COMMENT '省',
  `city` int(11) DEFAULT NULL COMMENT '市',
  `pid` int(11) DEFAULT NULL COMMENT '区/县',
  `address` varchar(255) CHARACTER SET ucs2 DEFAULT NULL COMMENT '详细地址',
  `styles` char(100) CHARACTER SET ucs2 DEFAULT NULL COMMENT '擅长风格',
  `experience` int(11) DEFAULT NULL COMMENT '工作年限',
  `company` varchar(50) CHARACTER SET ucs2 DEFAULT NULL COMMENT '公司',
  `weixin` varchar(64) CHARACTER SET ucs2 DEFAULT NULL COMMENT '微信',
  `qq` int(11) DEFAULT NULL COMMENT 'QQ',
  `phone` char(20) CHARACTER SET ucs2 DEFAULT NULL COMMENT '电话号码',
  `email` char(90) CHARACTER SET ucs2 DEFAULT NULL COMMENT '邮箱',
  `img` int(11) DEFAULT NULL COMMENT '个人头像',
  `web_img` int(11) DEFAULT NULL COMMENT '网站头像',
  `self_describe` varchar(255) CHARACTER SET ucs2 DEFAULT NULL COMMENT '个人简介',
  `design_method` varchar(255) CHARACTER SET ucs2 DEFAULT NULL COMMENT '设计理念',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='前台用户详情';

-- ----------------------------
-- Records of tp_user_info
-- ----------------------------
