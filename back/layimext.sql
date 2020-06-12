/*
 Navicat Premium Data Transfer

 Source Server         : phpstudy
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : localhost:3306
 Source Schema         : layimext

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 30/04/2020 17:25:09
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ext_archives
-- ----------------------------
DROP TABLE IF EXISTS `ext_archives`;
CREATE TABLE `ext_archives`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `url` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `keywords` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '文章关键词',
  `description` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '文章的描述',
  `typeid` int(5) NOT NULL DEFAULT 1 COMMENT '文章栏目id',
  `sort` int(3) NOT NULL DEFAULT 1 COMMENT '文章权重',
  `cnum` int(10) NOT NULL DEFAULT 2 COMMENT '文章点击次数',
  `writer` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'admin' COMMENT '文章作者',
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ext_articleinfo
-- ----------------------------
DROP TABLE IF EXISTS `ext_articleinfo`;
CREATE TABLE `ext_articleinfo`  (
  `aid` int(11) NOT NULL COMMENT '文章对应的id',
  `body` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `typeid` int(5) NOT NULL DEFAULT 1 COMMENT '文章栏目id'
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ext_category
-- ----------------------------
DROP TABLE IF EXISTS `ext_category`;
CREATE TABLE `ext_category`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '类别名称',
  `type` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '归属分类',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 启用 2 禁用',
  `typeid` int(11) NOT NULL COMMENT '父类id',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `type`(`type`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ext_chatgroup
-- ----------------------------
DROP TABLE IF EXISTS `ext_chatgroup`;
CREATE TABLE `ext_chatgroup`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '群组id',
  `groupname` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '群组名称',
  `avatar` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '/uploads/group.png' COMMENT '群组头像',
  `video` varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `owner_name` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '群主名称',
  `owner_id` int(11) NULL DEFAULT NULL COMMENT '群主id',
  `owner_avatar` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '群主头像',
  `owner_sign` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '群主签名',
  `state` tinyint(1) NULL DEFAULT -1 COMMENT '1启用',
  `group_no` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `public` tinyint(1) NULL DEFAULT 1 COMMENT '1开放 ',
  `verify` tinyint(1) NULL DEFAULT 1 COMMENT '1需要审核',
  `createtime` int(10) NULL DEFAULT NULL,
  `video2` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `qrcode` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 36 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ext_chatgroup
-- ----------------------------
INSERT INTO `ext_chatgroup` VALUES (4, '梦里花落知多少', 'uploads/avatar/100251584520983.jpeg', 'http://HLS.0n5ut.cn/xinkai2/stream.m3u8', NULL, 1, NULL, NULL, 1, '10025', 1, 1, NULL, 'http://HLS.0n5ut.cn/xinkai2/stream.m3u8', '/uploads/qrcode/20191113/b58a0cd2fb0013fa02884798b0a81ebd.png');
INSERT INTO `ext_chatgroup` VALUES (18, '群聊', '/uploads/20190405/1b260c5a4c28087df0cf3d66a80b0e88.jpg', NULL, NULL, 17, NULL, NULL, -1, '100017', 1, 1, NULL, NULL, NULL);
INSERT INTO `ext_chatgroup` VALUES (19, '我的群聊', 'uploads/avatar/1000031559830180.jpeg', NULL, NULL, 3, NULL, NULL, -1, '100003', 1, 1, NULL, NULL, NULL);
INSERT INTO `ext_chatgroup` VALUES (20, '群聊', '/uploads/20190405/1b260c5a4c28087df0cf3d66a80b0e88.jpg', NULL, NULL, 1, NULL, NULL, -1, '200001', 2, 1, NULL, NULL, NULL);
INSERT INTO `ext_chatgroup` VALUES (21, '群聊', '/uploads/20190405/1b260c5a4c28087df0cf3d66a80b0e88.jpg', NULL, NULL, 17, NULL, NULL, -1, '300001', 1, 1, NULL, NULL, NULL);
INSERT INTO `ext_chatgroup` VALUES (22, '群聊', '/uploads/20190405/1b260c5a4c28087df0cf3d66a80b0e88.jpg', NULL, NULL, 1, NULL, NULL, -1, '400001', 1, 1, NULL, NULL, NULL);
INSERT INTO `ext_chatgroup` VALUES (23, '群聊', '/uploads/20190405/1b260c5a4c28087df0cf3d66a80b0e88.jpg', NULL, NULL, 3, NULL, NULL, -1, '200003', 1, 1, NULL, NULL, NULL);
INSERT INTO `ext_chatgroup` VALUES (24, '群聊', '/uploads/20190405/1b260c5a4c28087df0cf3d66a80b0e88.jpg', NULL, NULL, 3, NULL, NULL, -1, '300003', 1, 1, NULL, NULL, NULL);
INSERT INTO `ext_chatgroup` VALUES (25, '群聊', '/uploads/20190405/1b260c5a4c28087df0cf3d66a80b0e88.jpg', NULL, NULL, 3, NULL, NULL, -1, '400003', 1, 1, NULL, NULL, NULL);
INSERT INTO `ext_chatgroup` VALUES (26, '群聊', '/uploads/20190405/1b260c5a4c28087df0cf3d66a80b0e88.jpg', NULL, NULL, 3, NULL, NULL, -1, '500003', 1, 1, NULL, NULL, NULL);
INSERT INTO `ext_chatgroup` VALUES (27, '群聊', '/uploads/20190405/1b260c5a4c28087df0cf3d66a80b0e88.jpg', NULL, NULL, 17, NULL, NULL, -1, '200017', 1, 1, NULL, NULL, NULL);
INSERT INTO `ext_chatgroup` VALUES (28, '群聊', '/uploads/20190405/1b260c5a4c28087df0cf3d66a80b0e88.jpg', NULL, NULL, 17, NULL, NULL, -1, '300017', 1, 1, NULL, NULL, NULL);
INSERT INTO `ext_chatgroup` VALUES (29, '群聊', '/uploads/20190405/1b260c5a4c28087df0cf3d66a80b0e88.jpg', NULL, NULL, 17, NULL, NULL, -1, '400017', 1, 1, NULL, NULL, NULL);
INSERT INTO `ext_chatgroup` VALUES (30, '群聊', '/uploads/20190405/1b260c5a4c28087df0cf3d66a80b0e88.jpg', NULL, NULL, 1, NULL, NULL, -1, '500001', 1, 1, NULL, NULL, NULL);
INSERT INTO `ext_chatgroup` VALUES (31, '群聊', '/uploads/20190405/1b260c5a4c28087df0cf3d66a80b0e88.jpg', NULL, NULL, 17, NULL, NULL, -1, '500017', 1, 1, NULL, NULL, NULL);
INSERT INTO `ext_chatgroup` VALUES (32, '群聊', '/uploads/group.png', NULL, NULL, 32, NULL, NULL, -1, '100032', 1, 1, NULL, NULL, NULL);
INSERT INTO `ext_chatgroup` VALUES (35, '群聊3101707391', '/uploads/group.png', '12', NULL, 1, NULL, NULL, -1, '500001', 1, 1, 1564909659, 'http://HLS.0n5ut.cn/xinkai2/stream.m3u8', '/uploads/qrcode/20191113/7ded27775dddaeb37d5cdc8c07d04915.png');

-- ----------------------------
-- Table structure for ext_chatlog
-- ----------------------------
DROP TABLE IF EXISTS `ext_chatlog`;
CREATE TABLE `ext_chatlog`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fromid` int(11) NOT NULL COMMENT '会话来源id',
  `fromname` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '消息来源用户名',
  `fromavatar` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '来源的用户头像',
  `toid` int(11) NOT NULL COMMENT '会话发送的id',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '发送的内容',
  `timeline` int(10) NOT NULL COMMENT '记录时间',
  `type` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '聊天类型',
  `needsend` tinyint(1) NULL DEFAULT 0 COMMENT '0 不需要推送 1 需要推送',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fromid`(`fromid`) USING BTREE,
  INDEX `toid`(`toid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 544 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ext_chatlog
-- ----------------------------

-- ----------------------------
-- Table structure for ext_chatuser
-- ----------------------------
DROP TABLE IF EXISTS `ext_chatuser`;
CREATE TABLE `ext_chatuser`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `pwd` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '密码',
  `groupid` int(5) NULL DEFAULT 1 COMMENT '所属的分组id',
  `status` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `sign` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `avatar` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `code` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `addtime` int(10) NULL DEFAULT NULL COMMENT '注册时间',
  `no` int(10) NULL DEFAULT NULL,
  `initMsg` tinyint(4) NULL DEFAULT 0 COMMENT '0首次注册推送公开群消息 ',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 39 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ext_chatuser
-- ----------------------------

-- ----------------------------
-- Table structure for ext_config
-- ----------------------------
DROP TABLE IF EXISTS `ext_config`;
CREATE TABLE `ext_config`  (
  `id` int(11) NOT NULL,
  `kdata` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `vdata` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `time` datetime(0) NULL DEFAULT NULL,
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ext_config
-- ----------------------------
INSERT INTO `ext_config` VALUES (1, 'regPermission', '1', '2019-05-05 20:48:52', '是否开放注册');
INSERT INTO `ext_config` VALUES (2, 'addFriend', '-1', '2019-08-04 17:52:01', '公开群是否可添加好友');

-- ----------------------------
-- Table structure for ext_friend
-- ----------------------------
DROP TABLE IF EXISTS `ext_friend`;
CREATE TABLE `ext_friend`  (
  `userid` int(10) NOT NULL,
  `friendid` int(10) NOT NULL,
  INDEX `userid`(`userid`) USING BTREE,
  INDEX `friendid`(`friendid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ext_friend
-- ----------------------------
INSERT INTO `ext_friend` VALUES (33, 3);
INSERT INTO `ext_friend` VALUES (1, 3);

-- ----------------------------
-- Table structure for ext_groupdetail
-- ----------------------------
DROP TABLE IF EXISTS `ext_groupdetail`;
CREATE TABLE `ext_groupdetail`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `username` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `useravatar` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `usersign` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT ' ',
  `groupid` int(11) NOT NULL,
  `state` tinyint(1) NULL DEFAULT 0 COMMENT '0不是群成员，待审核',
  `role` tinyint(1) NULL DEFAULT 3 COMMENT '1群主2管理员3群员',
  `apply_time` int(10) NULL DEFAULT NULL COMMENT '申请时间',
  `add_time` int(10) NULL DEFAULT NULL COMMENT '入群时间',
  `gag` int(10) NOT NULL DEFAULT 0 COMMENT '禁言到某个时刻',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 152 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ext_groupdetail
-- ----------------------------
INSERT INTO `ext_groupdetail` VALUES (1, 1, '纸飞机', 'http://cdn.firstlinkapp.com/upload/2016_6/1465575923433_33812.jpg', '在深邃的编码世界，做一枚轻盈的纸飞机', 1, 1, 3, 1558518791, 1558518791, 0);
INSERT INTO `ext_groupdetail` VALUES (2, 3, '罗玉凤', 'http://tp1.sinaimg.cn/1241679004/180/5743814375/0', '在自己实力不济的时候，不要去相信什么媒体和记者。他们不是善良的人，有时候候他们的采访对当事人而言就是陷阱', 1, 1, 3, 1558518791, 1558518791, 0);
INSERT INTO `ext_groupdetail` VALUES (4, 17, 'wqq', '/uploads/20190505/69b4585b8de4db84bc69d952e05728ae.jpg', '1', 1, 1, 3, 1558518791, 1558518791, 0);
INSERT INTO `ext_groupdetail` VALUES (5, 24, '前端大神', 'http://tp1.sinaimg.cn/1241679004/180/5743814375/0', '前端就是这么牛', 1, 1, 3, 1558518791, 1558518791, 0);
INSERT INTO `ext_groupdetail` VALUES (6, 24, 'w2w2w2', '/uploads/20190506/c7e604037c555ca37be9c2d70cc9f11a.jpg', '', 3, 1, 3, 1558518791, 1558518791, 0);
INSERT INTO `ext_groupdetail` VALUES (21, 18, '2', '/uploads/20190505/b14fa245600cc65020b79531d4268e3a.jpg', '12', 16, 0, 3, 1558518791, 1558518791, 0);
INSERT INTO `ext_groupdetail` VALUES (22, 13, '前端大神', 'http://tp1.sinaimg.cn/1241679004/180/5743814375/0', '前端就是这么牛', 16, 0, 3, 1558518791, 1558518791, 0);
INSERT INTO `ext_groupdetail` VALUES (23, 3, '罗玉凤', 'http://tp1.sinaimg.cn/1241679004/180/5743814375/0', '在自己实力不济的时候，不要去相信什么媒体和记者。他们不是善良的人，有时候候他们的采访对当事人而言就是陷阱', 16, 1, 3, 1558518791, 1558518794, 0);
INSERT INTO `ext_groupdetail` VALUES (24, 1, '纸飞机', 'http://cdn.firstlinkapp.com/upload/2016_6/1465575923433_33812.jpg', '在深邃的编码世界，做一枚轻盈的纸飞机', 16, 0, 3, 1558518791, 1558518791, 0);
INSERT INTO `ext_groupdetail` VALUES (28, 3, '罗玉凤', 'http://tp1.sinaimg.cn/1241679004/180/5743814375/0', '在自己实力不济的时候，不要去相信什么媒体和记者。他们不是善良的人，有时候候他们的采访对当事人而言就是陷阱', 3, 1, 3, 1558518791, 1558518791, 0);
INSERT INTO `ext_groupdetail` VALUES (29, 1, '纸飞机', 'http://cdn.firstlinkapp.com/upload/2016_6/1465575923433_33812.jpg', '在深邃的编码世界，做一枚轻盈的纸飞机', 17, 1, 3, 1558518791, 1558518791, 0);
INSERT INTO `ext_groupdetail` VALUES (30, 3, '罗玉凤', 'http://tp1.sinaimg.cn/1241679004/180/5743814375/0', '在自己实力不济的时候，不要去相信什么媒体和记者。他们不是善良的人，有时候候他们的采访对当事人而言就是陷阱', 17, 1, 3, 1558518791, 1558518791, 0);
INSERT INTO `ext_groupdetail` VALUES (31, 13, '前端大神', 'http://tp1.sinaimg.cn/1241679004/180/5743814375/0', '前端就是这么牛', 17, 1, 3, 1558518791, 1558518791, 0);
INSERT INTO `ext_groupdetail` VALUES (32, 17, 'wqq', '/uploads/20190505/69b4585b8de4db84bc69d952e05728ae.jpg', '1', 17, 1, 3, 1558518791, 1558518791, 0);
INSERT INTO `ext_groupdetail` VALUES (33, 18, '2', '/uploads/20190505/b14fa245600cc65020b79531d4268e3a.jpg', '12', 17, 1, 3, 1558518791, 1558518791, 0);
INSERT INTO `ext_groupdetail` VALUES (34, 19, '1', '/uploads/20190506/c7e604037c555ca37be9c2d70cc9f11a.jpg', '1', 17, 1, 3, 1558518791, 1558518791, 0);
INSERT INTO `ext_groupdetail` VALUES (35, 20, '3', '/uploads/20190506/c7e604037c555ca37be9c2d70cc9f11a.jpg', '', 17, 1, 3, 1558518791, 1558518791, 0);
INSERT INTO `ext_groupdetail` VALUES (36, 21, '4', '/uploads/20190506/c7e604037c555ca37be9c2d70cc9f11a.jpg', '', 17, 1, 3, 1558518791, 1558518791, 0);
INSERT INTO `ext_groupdetail` VALUES (37, 22, '111', '/uploads/20190506/c7e604037c555ca37be9c2d70cc9f11a.jpg', '', 17, 1, 3, 1558518791, 1558518791, 0);
INSERT INTO `ext_groupdetail` VALUES (38, 23, '2222', '/uploads/20190506/c7e604037c555ca37be9c2d70cc9f11a.jpg', '', 17, 1, 3, 1558518791, 1558518791, 0);
INSERT INTO `ext_groupdetail` VALUES (39, 24, 'w2w2w2', '/uploads/20190506/c7e604037c555ca37be9c2d70cc9f11a.jpg', '', 17, 1, 3, 1558518791, 1558518791, 0);
INSERT INTO `ext_groupdetail` VALUES (75, 1, '纸飞机', 'http://cdn.firstlinkapp.com/upload/2016_6/1465575923433_33812.jpg', '在深邃的编码世界，做一枚轻盈的纸飞机', 4, 1, 1, 1558518791, 1558518791, 0);
INSERT INTO `ext_groupdetail` VALUES (76, 25, 'yang2wen111', '/uploads/20190513/04cc47fc2ad74a6771bccbcce79a388f.jpg', '我们老大也牛逼', 4, 1, 2, 1558518791, 1558518791, 0);
INSERT INTO `ext_groupdetail` VALUES (77, 29, 'aiqi', '/uploads/20190506/c7e604037c555ca37be9c2d70cc9f11a.jpg', '', 4, 1, 3, 1558518791, 1558518791, 0);
INSERT INTO `ext_groupdetail` VALUES (78, 28, 'osi', '/uploads/20190506/c7e604037c555ca37be9c2d70cc9f11a.jpg', '', 4, 1, 3, 1558518791, 1558518791, 1559405895);
INSERT INTO `ext_groupdetail` VALUES (79, 17, 'wqq', '/uploads/20190505/69b4585b8de4db84bc69d952e05728ae.jpg', '1', 4, 1, 3, 1558518756, 1558518791, 0);
INSERT INTO `ext_groupdetail` VALUES (80, 3, '罗玉凤', 'http://tp1.sinaimg.cn/1241679004/180/5743814375/0', '在自己实力不济的时候，不要去相信什么媒体和记者。他们不是善良的人，有时候候他们的采访对当事人而言就是陷阱', 4, 1, 3, 1558518847, 1558518854, 0);
INSERT INTO `ext_groupdetail` VALUES (148, 35, 'ceshi5', NULL, ' ', 4, 1, 3, 1564915800, 1564915800, 0);
INSERT INTO `ext_groupdetail` VALUES (149, 36, 'ceshi112', NULL, ' ', 4, 1, 3, 1565010435, 1565010435, 0);
INSERT INTO `ext_groupdetail` VALUES (150, 37, '菊花茶', NULL, ' ', 4, 1, 3, 1585656358, 1585656358, 0);
INSERT INTO `ext_groupdetail` VALUES (151, 38, 'Axin', NULL, ' ', 4, 1, 3, 1585657615, 1585657615, 0);

-- ----------------------------
-- Table structure for ext_img
-- ----------------------------
DROP TABLE IF EXISTS `ext_img`;
CREATE TABLE `ext_img`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `imgurl` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint(1) NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ext_img
-- ----------------------------
INSERT INTO `ext_img` VALUES (2, '/uploads/20190723/ea0328e8bed9ad6ee17896d29978ad83.png', 1);

-- ----------------------------
-- Table structure for ext_msgbox
-- ----------------------------
DROP TABLE IF EXISTS `ext_msgbox`;
CREATE TABLE `ext_msgbox`  (
  `fromid` int(10) NULL DEFAULT NULL,
  `toid` int(10) NULL DEFAULT NULL,
  `type` char(16) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `status` tinyint(1) NULL DEFAULT 0 COMMENT '0已申请，未查看 1已查看，通过 2已查看，未通过',
  `timeline` int(10) NULL DEFAULT NULL,
  `content` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ext_msgbox
-- ----------------------------
INSERT INTO `ext_msgbox` VALUES (33, 3, 'friend', 1, 1561630182, '');
INSERT INTO `ext_msgbox` VALUES (1, 3, 'friend', 1, 1561715656, 'fff');
INSERT INTO `ext_msgbox` VALUES (1, 17, 'friend', 0, 1562658221, '');

-- ----------------------------
-- Table structure for ext_node
-- ----------------------------
DROP TABLE IF EXISTS `ext_node`;
CREATE TABLE `ext_node`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `node_name` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '节点名称',
  `module_name` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '模块名',
  `control_name` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '控制器名',
  `action_name` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '方法名',
  `is_menu` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否是菜单项 1不是 2是',
  `typeid` int(11) NOT NULL COMMENT '父级节点id',
  `style` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '菜单样式',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 42 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ext_node
-- ----------------------------
INSERT INTO `ext_node` VALUES (1, '用户管理', '#', '#', '#', 2, 0, 'fa fa-users');
INSERT INTO `ext_node` VALUES (2, '用户列表', 'admin', 'user', 'index', 2, 1, '');
INSERT INTO `ext_node` VALUES (3, '添加用户', 'admin', 'user', 'useradd', 1, 2, '');
INSERT INTO `ext_node` VALUES (4, '编辑用户', 'admin', 'user', 'useredit', 1, 2, '');
INSERT INTO `ext_node` VALUES (5, '删除用户', 'admin', 'user', 'userdel', 1, 2, '');
INSERT INTO `ext_node` VALUES (6, '角色列表', 'admin', 'role', 'index', 2, 1, '');
INSERT INTO `ext_node` VALUES (7, '添加角色', 'admin', 'role', 'roleadd', 1, 6, '');
INSERT INTO `ext_node` VALUES (8, '编辑角色', 'admin', 'role', 'roleedit', 1, 6, '');
INSERT INTO `ext_node` VALUES (9, '删除角色', 'admin', 'role', 'roledel', 1, 6, '');
INSERT INTO `ext_node` VALUES (10, '分配权限', 'admin', 'role', 'giveaccess', 1, 6, '');
INSERT INTO `ext_node` VALUES (11, '系统管理', '#', '#', '#', 2, 0, 'fa fa-desktop');
INSERT INTO `ext_node` VALUES (12, '数据备份/还原', 'admin', 'data', 'index', 2, 11, '');
INSERT INTO `ext_node` VALUES (13, '备份数据', 'admin', 'data', 'importdata', 1, 12, '');
INSERT INTO `ext_node` VALUES (14, '还原数据', 'admin', 'data', 'backdata', 1, 12, '');
INSERT INTO `ext_node` VALUES (17, '测试列表', 'admin', 'tcollect', 'testlist', 1, 17, '');
INSERT INTO `ext_node` VALUES (18, '测试文章', 'admin', 'tcollect', 'testarc', 1, 17, '');
INSERT INTO `ext_node` VALUES (23, 'LayChat管理', '#', '#', '#', 2, 0, 'fa fa-paw');
INSERT INTO `ext_node` VALUES (24, 'laychat用户管理', 'admin', 'layuser', 'index', 2, 23, '');
INSERT INTO `ext_node` VALUES (26, 'laychat用户添加', 'admin', 'layuser', 'useradd', 1, 24, '');
INSERT INTO `ext_node` VALUES (27, 'laychat用户删除', 'admin', 'layuser', 'userdel', 1, 24, '');
INSERT INTO `ext_node` VALUES (28, 'laychat用户编辑', 'admin', 'layuser', 'useredit', 1, 24, '');
INSERT INTO `ext_node` VALUES (29, 'laychat群管理', 'admin', 'layuser', 'group', 2, 23, '');
INSERT INTO `ext_node` VALUES (30, 'laychat群添加', 'admin', 'layuser', 'groupadd', 1, 29, '');
INSERT INTO `ext_node` VALUES (31, 'laychat群编辑', 'admin', 'layuser', 'groupedit', 1, 29, '');
INSERT INTO `ext_node` VALUES (32, 'laychat用户删除', 'admin', 'layuser', 'groupdel', 1, 29, '');
INSERT INTO `ext_node` VALUES (33, 'laychat群员管理', 'admin', 'layuser', 'groupmanage', 1, 29, '');
INSERT INTO `ext_node` VALUES (34, 'laychat申请管理', 'admin', 'layuser', 'apply', 2, 23, '');
INSERT INTO `ext_node` VALUES (35, 'laychat审核通过', 'admin', 'layuser', 'changestate', 1, 34, '');
INSERT INTO `ext_node` VALUES (36, '系统设置', 'admin', 'data', 'conf', 2, 11, '');
INSERT INTO `ext_node` VALUES (37, 'laychat群直播状态', 'admin', 'layuser', 'switchstate', 1, 29, '');
INSERT INTO `ext_node` VALUES (38, '接口图片管理', 'admin', 'layuser', 'imgset', 2, 23, '');
INSERT INTO `ext_node` VALUES (39, '添加接口图片', 'admin', 'layuser', 'imgadd', 1, 38, '');
INSERT INTO `ext_node` VALUES (40, '编辑接口图片', 'admin', 'layuser', 'imgedit', 1, 38, '');
INSERT INTO `ext_node` VALUES (41, ' 删除接口图片', 'admin', 'layuser', 'imgdel', 1, 38, '');

-- ----------------------------
-- Table structure for ext_role
-- ----------------------------
DROP TABLE IF EXISTS `ext_role`;
CREATE TABLE `ext_role`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `rolename` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '角色名称',
  `rule` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '权限节点数据',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ext_role
-- ----------------------------
INSERT INTO `ext_role` VALUES (1, '超级管理员', '');
INSERT INTO `ext_role` VALUES (2, '系统维护员', '1,2,3,4,5,6,7,8,9,10');
INSERT INTO `ext_role` VALUES (3, '新闻发布员', '1,2,3,4,5');

-- ----------------------------
-- Table structure for ext_rule
-- ----------------------------
DROP TABLE IF EXISTS `ext_rule`;
CREATE TABLE `ext_rule`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rulename` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '规则标题',
  `baseurl` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '采集站点的地址',
  `listurl` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '列表页地址',
  `ismore` tinyint(1) NOT NULL COMMENT '是否批量采集 1 否 2是',
  `start` int(11) NULL DEFAULT 0 COMMENT '列表页开始地址',
  `end` int(11) NULL DEFAULT 0 COMMENT '列表页结束地址',
  `titlediv` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '标题父层地址',
  `title` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '文章标题内容规则',
  `titleurl` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '标题地址规则',
  `body` varchar(155) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '文章内容规则',
  `addtime` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ext_rule
-- ----------------------------
INSERT INTO `ext_rule` VALUES (1, '脚本之家php文章采集', 'http://www.jb51.net', 'http://www.jb51.net/list/list_15_1.htm', 1, 0, 0, '.artlist dl dt a', 'text', 'href', '#content', 1471244221);
INSERT INTO `ext_rule` VALUES (2, 'thinkphp官网文章规则', 'http://www.thinkphp.cn', 'http://www.thinkphp.cn/code/system/p/1.html', 1, 0, 0, '.extend ul li .hd a', 'text', 'href', '.wrapper .detail-bd', 1471244221);
INSERT INTO `ext_rule` VALUES (3, '果壳网科学人采集规则', 'http://www.guokr.com', 'http://www.guokr.com/scientific/', 1, 0, 0, '#waterfall .article h3 a', 'text', 'href', '.document div:eq(0)', 1471247277);

-- ----------------------------
-- Table structure for ext_user
-- ----------------------------
DROP TABLE IF EXISTS `ext_user`;
CREATE TABLE `ext_user`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '' COMMENT '密码',
  `loginnum` int(11) NULL DEFAULT 0 COMMENT '登陆次数',
  `last_login_ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '' COMMENT '最后登录IP',
  `last_login_time` int(11) NULL DEFAULT 0 COMMENT '最后登录时间',
  `real_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '' COMMENT '真实姓名',
  `status` int(1) NULL DEFAULT 0 COMMENT '状态',
  `typeid` int(11) NULL DEFAULT 1 COMMENT '用户角色id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_bin ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ext_user
-- ----------------------------
INSERT INTO `ext_user` VALUES (1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 110, '118.116.91.8', 1585901163, 'admin', 1, 1);
INSERT INTO `ext_user` VALUES (2, 'xiaobai', '4297f44b13955235245b2497399d7a93', 6, '127.0.0.1', 1470368260, '小白', 1, 2);

-- ----------------------------
-- Table structure for ext_video
-- ----------------------------
DROP TABLE IF EXISTS `ext_video`;
CREATE TABLE `ext_video`  (
  `id` int(10) NOT NULL,
  `url` varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `groupid` int(10) NOT NULL,
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `state` tinyint(1) NULL DEFAULT 1 COMMENT '1可用 ',
  `time` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
