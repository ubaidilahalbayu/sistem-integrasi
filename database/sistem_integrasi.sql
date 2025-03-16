/*
 Navicat Premium Data Transfer

 Source Server         : db
 Source Server Type    : MySQL
 Source Server Version : 80041 (8.0.41-0ubuntu0.22.04.1)
 Source Host           : localhost:3306
 Source Schema         : sistem_integrasi

 Target Server Type    : MySQL
 Target Server Version : 80041 (8.0.41-0ubuntu0.22.04.1)
 File Encoding         : 65001

 Date: 15/03/2025 03:46:02
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for mahasiswa
-- ----------------------------
DROP TABLE IF EXISTS `mahasiswa`;
CREATE TABLE `mahasiswa` (
  `id` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of mahasiswa
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `username` varchar(16) NOT NULL,
  `password` text,
  `level` smallint DEFAULT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of user
-- ----------------------------
BEGIN;
INSERT INTO `user` (`username`, `password`, `level`) VALUES ('Admin', 'admin123', 1);
INSERT INTO `user` (`username`, `password`, `level`) VALUES ('Ubai', 'ubai123', 3);
INSERT INTO `user` (`username`, `password`, `level`) VALUES ('Udin', 'udin123', 2);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
