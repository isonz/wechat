/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50524
Source Host           : localhost:3306
Source Database       : wechat

Target Server Type    : MYSQL
Target Server Version : 50524
File Encoding         : 65001

Date: 2015-01-23 17:58:28
*/

SET FOREIGN_KEY_CHECKS=0;

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
  `open_id` varchar(64) NOT NULL,
  `nick_name` varchar(30) DEFAULT NULL,
  `sex` tinyint(3) unsigned DEFAULT NULL,
  `country` varchar(20) DEFAULT NULL,
  `province` varchar(20) DEFAULT NULL,
  `city` varchar(30) DEFAULT NULL,
  `county` varchar(30) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `postcode` varchar(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `open_id` (`open_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of wx_users
-- ----------------------------
INSERT INTO `wx_users` VALUES ('1', '', null, null, '中华人民共和国', null, null, null, null, null);
