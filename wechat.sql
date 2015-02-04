/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50524
Source Host           : localhost:3306
Source Database       : wechat

Target Server Type    : MYSQL
Target Server Version : 50524
File Encoding         : 65001

Date: 2015-02-04 18:35:52
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wx_chats
-- ----------------------------
DROP TABLE IF EXISTS `wx_chats`;
CREATE TABLE `wx_chats` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `merch_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `create_at` int(11) unsigned NOT NULL DEFAULT '0',
  `msg_type` varchar(20) DEFAULT NULL,
  `contents` text,
  `tofrom` tinyint(1) DEFAULT '0' COMMENT '0:user 到 merch, 1:merch 到 user',
  PRIMARY KEY (`id`),
  KEY `chats_merch_id` (`merch_id`),
  KEY `chats_user_id` (`user_id`),
  KEY `msg_type` (`msg_type`),
  CONSTRAINT `chats_merch_id` FOREIGN KEY (`merch_id`) REFERENCES `wx_merchants` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `chats_user_id` FOREIGN KEY (`user_id`) REFERENCES `wx_users` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of wx_chats
-- ----------------------------
INSERT INTO `wx_chats` VALUES ('1', '1', '4', '1422956035', 'text', '{\"Content\":\"Hi\",\"MsgId\":\"6111549634175053539\"}', '0');
INSERT INTO `wx_chats` VALUES ('2', '1', '4', '1422956087', 'image', '{\"PicUrl\":\"http:\\/\\/mmbiz.qpic.cn\\/mmbiz\\/iaYqV1M34zjDexFzSe686RY72JCiaOicZJFYSIOQoV9nA0SnqhFgrIWpCzCY66LRvxibI6zl6l6AtApLl2cAzN7EXA\\/0\",\"MediaId\":\"6G1UZfrneUZkcF8umKf6CfeLeprVufn8GMTNkF_aqjRzkrDdw76kzE1BnOP1VBaE\",\"MsgId\":\"6111549857513352952\"}', '0');
INSERT INTO `wx_chats` VALUES ('3', '1', '4', '1422957516', 'text', '{\"Content\":\"Hi\",\"MsgId\":\"6111555995021619521\"}', '0');
INSERT INTO `wx_chats` VALUES ('4', '1', '4', '1422958548', 'text', '{\"Content\":\"Hi\",\"MsgId\":\"6111560427427869376\"}', '0');
INSERT INTO `wx_chats` VALUES ('5', '1', '4', '1422958628', 'text', '{\"Content\":\"H\",\"MsgId\":\"6111560771025253082\"}', '0');
INSERT INTO `wx_chats` VALUES ('6', '1', '4', '1422958668', 'text', '{\"Content\":\"H\",\"MsgId\":\"6111560942823944935\"}', '0');
INSERT INTO `wx_chats` VALUES ('7', '1', '4', '1422958754', 'text', '{\"Content\":\"Hi\",\"MsgId\":\"6111561312191132437\"}', '0');
INSERT INTO `wx_chats` VALUES ('8', '1', '4', '1422958767', 'text', '{\"Content\":\"Of\",\"MsgId\":\"6111561368025707292\"}', '0');
INSERT INTO `wx_chats` VALUES ('9', '1', '4', '1422959650', 'text', '{\"Content\":\"Oj\",\"MsgId\":\"6111565160481830055\"}', '0');
INSERT INTO `wx_chats` VALUES ('10', '1', '4', '1422959498', 'text', '{\"content\":\"\\u6b22\\u8fce\\u5149\\u4e34\\uff01\"}', '1');
INSERT INTO `wx_chats` VALUES ('11', '1', '4', '1423014878', 'text', '{\"Content\":\"Oj\",\"MsgId\":\"6111802362935664581\"}', '0');
INSERT INTO `wx_chats` VALUES ('12', '1', '4', '1423014726', 'text', '{\"content\":\"\\u6b22\\u8fce\\u5149\\u4e34\\uff01\"}', '1');
INSERT INTO `wx_chats` VALUES ('13', '1', '4', '1423019725', 'text', '{\"Content\":\"KO\",\"MsgId\":\"6111823180642150460\"}', '0');
INSERT INTO `wx_chats` VALUES ('14', '1', '4', '1423019572', 'text', '{\"content\":\"\\u6b22\\u8fce\\u5149\\u4e34\\uff01\"}', '1');
INSERT INTO `wx_chats` VALUES ('15', '1', '4', '1423031421', 'text', '{\"Content\":\"OK\",\"MsgId\":\"6111873414579649207\"}', '0');
INSERT INTO `wx_chats` VALUES ('16', '1', '4', '1423031269', 'image', '{\"media_id\":\"6G1UZfrneUZkcF8umKf6CfeLeprVufn8GMTNkF_aqjRzkrDdw76kzE1BnOP1VBaE\"}', '1');
INSERT INTO `wx_chats` VALUES ('17', '1', '4', '1423031449', 'text', '{\"Content\":\"OK\",\"MsgId\":\"6111873534838733504\"}', '0');
INSERT INTO `wx_chats` VALUES ('18', '1', '4', '1423031296', 'image', '{\"media_id\":\"6G1UZfrneUZkcF8umKf6CfeLeprVufn8GMTNkF_aqjRzkrDdw76kzE1BnOP1VBaE\"}', '1');
INSERT INTO `wx_chats` VALUES ('19', '1', '4', '1423031479', 'text', '{\"Content\":\"OK p\",\"MsgId\":\"6111873663687752395\"}', '0');
INSERT INTO `wx_chats` VALUES ('20', '1', '4', '1423031326', 'image', '{\"media_id\":\"6G1UZfrneUZkcF8umKf6CfeLeprVufn8GMTNkF_aqjRzkrDdw76kzE1BnOP1VBaE\"}', '1');
INSERT INTO `wx_chats` VALUES ('21', '1', '4', '1423031543', 'voice', '{\"Format\":\"amr\",\"MediaId\":\"tJjTL415AFRpC8oo5z8pdEJ9cvc4YGwxneJPSWphgASG5yNqNkSCrZIEz5toOvBp\",\"Recognition\":\"\\u4f60\\u597d\\u4f60\\u597d\",\"MsgId\":\"6111873938361417728\"}', '0');
INSERT INTO `wx_chats` VALUES ('22', '1', '4', '1423031390', 'text', '{\"content\":\"\\u6b22\\u8fce\\u5149\\u4e34\\uff01\"}', '1');
INSERT INTO `wx_chats` VALUES ('23', '1', '4', '1423032374', 'text', '{\"Content\":\"OK\",\"MsgId\":\"6111877507683482717\"}', '0');
INSERT INTO `wx_chats` VALUES ('24', '1', '4', '1423032221', 'voice', '{\"media_id\":\"tJjTL415AFRpC8oo5z8pdEJ9cvc4YGwxneJPSWphgASG5yNqNkSCrZIEz5toOvBp\"}', '1');
INSERT INTO `wx_chats` VALUES ('25', '1', '5', '1423032476', 'text', '{\"Content\":\"\\u4f4e\",\"MsgId\":\"6111877945770146952\"}', '0');
INSERT INTO `wx_chats` VALUES ('26', '1', '5', '1423032323', 'voice', '{\"media_id\":\"tJjTL415AFRpC8oo5z8pdEJ9cvc4YGwxneJPSWphgASG5yNqNkSCrZIEz5toOvBp\"}', '1');
INSERT INTO `wx_chats` VALUES ('27', '1', '5', '1423032849', 'location', '{\"Location_X\":\"23.138941\",\"Location_Y\":\"113.317398\",\"Scale\":\"16\",\"Label\":\"\\u5e7f\\u5dde\\u5e02\\u5929\\u6cb3\\u533a\\u5929\\u6cb3\\u76f4\\u8857160\\u53f7\",\"MsgId\":\"6111879547792948494\"}', '0');
INSERT INTO `wx_chats` VALUES ('28', '1', '5', '1423032697', 'voice', '{\"media_id\":\"tJjTL415AFRpC8oo5z8pdEJ9cvc4YGwxneJPSWphgASG5yNqNkSCrZIEz5toOvBp\"}', '1');
INSERT INTO `wx_chats` VALUES ('29', '1', '4', '1423033518', 'link', '{\"Title\":\"\\u4e00\\u8d77\\u6765DIY\\u6709\\u673a\\u9ed1\\u571f\\u7cd9\\u7c73\\u6cb9\\uff01\",\"Description\":\"\\u5e15\\u65af\\u5a77\\u6709\\u673a\\u9ed1\\u571f\\u7cd9\\u7c73\\u5177\\u6709\\u51cf\\u80a5\\u529f\\u80fd\\uff0c\\u6e05\\u80a0\\u80c3\\uff0c\\u5bf9\\u80c3\\u80a0\\u529f\\u80fd\\u969c\\u788d\\u7684\\u60a3\\u8005\\u6709\\u5f88\\u597d\\u7684\\u7597\\u6548\\uff0c\\u53ef\\u6cbb\\u7597\\u4fbf\\u79d8\\uff0c\\u52a0\\u901f\\u6709\\u5bb3\\u7269\\u8d28\\u4ee3\\u8c22\\uff1b\\u80fd\\u6cbb\\u7597\\u8d2b\\u8840\\uff0c\\u80fd\\u4fc3\\u8fdb\\u8840\\u6db2\\u5faa\\u73af\\uff0c\\u6709\\u5f3a\\u5316\\u4f53\\u8d28\\u7684\\u4f5c\\u7528 \\u3002\\u5e76\\u4e14\\u8fd8\\u53ef\\u51cf\\u5c11\\u7cd6\\u5c3f\\u75c5\\u53d1\\u75c5\\u98ce\\u9669\\u548c\\u6709\\u9884\\u9632\\u591a\\u79cd\\u75be\\u75c5\\u7684\\u529f\\u80fd\\u3002\",\"Url\":\"http:\\/\\/mp.weixin.qq.com\\/s?__biz=MzA4MzM3NzYwOA==&mid=202121571&idx=1&sn=f5fd1bc2aec686ebbab09a69ea0717cc#rd\",\"MsgId\":\"6111882421126069705\"}', '0');
INSERT INTO `wx_chats` VALUES ('30', '1', '4', '1423033366', 'voice', '{\"media_id\":\"tJjTL415AFRpC8oo5z8pdEJ9cvc4YGwxneJPSWphgASG5yNqNkSCrZIEz5toOvBp\"}', '1');

-- ----------------------------
-- Table structure for wx_events
-- ----------------------------
DROP TABLE IF EXISTS `wx_events`;
CREATE TABLE `wx_events` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `merch_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `create_at` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(30) NOT NULL,
  `data` text,
  `tofrom` tinyint(1) DEFAULT '0' COMMENT '0:user 到 merch, 1:merch 到 user',
  PRIMARY KEY (`id`),
  KEY `chats_merch_id` (`merch_id`),
  KEY `chats_user_id` (`user_id`),
  CONSTRAINT `wx_events_ibfk_1` FOREIGN KEY (`merch_id`) REFERENCES `wx_merchants` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `wx_events_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `wx_users` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of wx_events
-- ----------------------------
INSERT INTO `wx_events` VALUES ('1', '1', '4', '1422948091', 'subscribe', null, '0');
INSERT INTO `wx_events` VALUES ('2', '1', '4', '1422951295', 'unsubscribe', null, '0');
INSERT INTO `wx_events` VALUES ('3', '1', '5', '1423032438', 'subscribe', null, '0');
INSERT INTO `wx_events` VALUES ('5', '1', '4', '1423046024', 'subscribe', null, '0');

-- ----------------------------
-- Table structure for wx_merchants
-- ----------------------------
DROP TABLE IF EXISTS `wx_merchants`;
CREATE TABLE `wx_merchants` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` char(18) NOT NULL COMMENT 'AppID',
  `token` varchar(32) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `original_id` varchar(30) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL COMMENT '商户名',
  `app_secret` varchar(40) DEFAULT NULL,
  `encodingAesKey` varchar(45) DEFAULT NULL COMMENT 'EncodingAESKey',
  `is_access` char(1) NOT NULL DEFAULT 'N' COMMENT '是否通过接口认证',
  PRIMARY KEY (`id`),
  UNIQUE KEY `app_id` (`app_id`),
  UNIQUE KEY `token` (`token`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of wx_merchants
-- ----------------------------
INSERT INTO `wx_merchants` VALUES ('1', 'wx407042c1130cd24e', 'placentinxft6tnoJk', 'PlacentinSwiss', 'gh_22325e8ce9b8', '广州帕斯婷商贸有限公司订阅号', 'ff10308f4c4b0c0892be8418569d3165', 'xft6tnoJkkA5W2Yum8nx3Hiyf2sBmCGpFSXXifbtFr3', 'Y');

-- ----------------------------
-- Table structure for wx_setting
-- ----------------------------
DROP TABLE IF EXISTS `wx_setting`;
CREATE TABLE `wx_setting` (
  `name` varchar(30) NOT NULL,
  `value` text,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wx_setting
-- ----------------------------
INSERT INTO `wx_setting` VALUES ('SINA_ENCODE', 'GBK');
INSERT INTO `wx_setting` VALUES ('SINA_LIUTONG_HOLDER_PAGES', '');
INSERT INTO `wx_setting` VALUES ('SINA_LIUTONG_HOLDER_URL', 'http://vip.stock.finance.sina.com.cn/corp/go.php/vCI_CirculateStockHolder/stockid/#ticker#/displaytype/30.phtml');
INSERT INTO `wx_setting` VALUES ('SINA_MAIN_HOLDER_PAGES', '');
INSERT INTO `wx_setting` VALUES ('SINA_MAIN_HOLDER_URL', 'http://vip.stock.finance.sina.com.cn/corp/go.php/vCI_StockHolder/stockid/#ticker#/displaytype/30.phtml');
INSERT INTO `wx_setting` VALUES ('SINA_MONEY_FLOWS_DATA', '{\"sh600015\":\"2015-01-05\"}');
INSERT INTO `wx_setting` VALUES ('SINA_MONEY_FLOWS_URL', 'http://vip.stock.finance.sina.com.cn/quotes_service/api/jsonp.php/var%20moneyFlowData=/MoneyFlow.ssi_ssfx_flzjtj?daima=#ticker#&gettime=1');
INSERT INTO `wx_setting` VALUES ('SINA_STOCK_COUNT_URL', 'http://vip.stock.finance.sina.com.cn/quotes_service/api/json_v2.php/Market_Center.getHQNodeStockCount?node=hs_a');
INSERT INTO `wx_setting` VALUES ('SINA_STOCK_PAGE_URL', 'http://vip.stock.finance.sina.com.cn/quotes_service/api/json_v2.php/Market_Center.getHQNodeData?page=1&num=80&sort=symbol&asc=1&node=hs_a&symbol=&_s_r_a=page');
INSERT INTO `wx_setting` VALUES ('SINA_STOCK_RUN', '{\"SINA_STOCK_COUNT\":2567,\"SINA_STOCK_COUNT_DATE\":\"2015-01-05\",\"SINA_STOCK_RUN_PAGE\":33,\"SINA_STOCK_RUN_PAGE_DATE\":\"2015-01-05\"}');
INSERT INTO `wx_setting` VALUES ('SINA_STOCK_SUSP_RUN', '{\"SINA_STOCK_COUNT\":2567,\"SINA_STOCK_COUNT_DATE\":\"2015-01-14\",\"SINA_SUSP_RUN_PAGE\":33,\"SINA_SUSP_RUN_PAGE_DATE\":\"2015-01-14\"}');
INSERT INTO `wx_setting` VALUES ('STOCK_STOP_DAY', '0,6,2015-01-01,2015-01-02');

-- ----------------------------
-- Table structure for wx_users
-- ----------------------------
DROP TABLE IF EXISTS `wx_users`;
CREATE TABLE `wx_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `merch_id` int(11) unsigned NOT NULL,
  `open_id` varchar(64) NOT NULL,
  `nickname` varchar(30) DEFAULT NULL,
  `sex` tinyint(3) unsigned DEFAULT NULL,
  `country` varchar(20) DEFAULT NULL,
  `province` varchar(20) DEFAULT NULL,
  `city` varchar(30) DEFAULT NULL,
  `county` varchar(30) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `postcode` varchar(6) DEFAULT NULL,
  `language` char(5) DEFAULT NULL,
  `headimgurl` varchar(500) DEFAULT NULL,
  `is_attention` tinyint(4) DEFAULT '1' COMMENT '是否关注状态，0：未关注，1：关注',
  `subscribe_time` int(11) unsigned DEFAULT NULL,
  `unionid` varchar(30) DEFAULT NULL COMMENT '只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。',
  `update_at` int(11) unsigned DEFAULT NULL,
  `create_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `open_id` (`open_id`),
  KEY `users_merch_id` (`merch_id`),
  KEY `is_attention` (`is_attention`),
  CONSTRAINT `users_merch_id` FOREIGN KEY (`merch_id`) REFERENCES `wx_merchants` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of wx_users
-- ----------------------------
INSERT INTO `wx_users` VALUES ('4', '1', 'oGKeLjnWHJV5BLGqov66syOM8bx4', null, null, null, null, null, null, null, null, null, null, '0', null, null, '1423045871', '1422948091');
INSERT INTO `wx_users` VALUES ('5', '1', 'oGKeLjuU-Ug4b1F-TT22rzyZmnqI', null, null, null, null, null, null, null, null, null, null, '1', null, null, null, '1423032438');
