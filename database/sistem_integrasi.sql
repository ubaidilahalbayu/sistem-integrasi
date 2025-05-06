/*
 Navicat Premium Data Transfer

 Source Server         : mysql
 Source Server Type    : MySQL
 Source Server Version : 100138 (10.1.38-MariaDB)
 Source Host           : localhost:3306
 Source Schema         : sistem_integrasi

 Target Server Type    : MySQL
 Target Server Version : 100138 (10.1.38-MariaDB)
 File Encoding         : 65001

 Date: 06/05/2025 18:18:44
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for data_dosen
-- ----------------------------
DROP TABLE IF EXISTS `data_dosen`;
CREATE TABLE `data_dosen`  (
  `nip` varchar(16) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `nama_gelar_depan` varchar(15) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '-',
  `nama_dosen` varchar(128) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `nama_gelar_belakang` varchar(15) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '-',
  PRIMARY KEY (`nip`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of data_dosen
-- ----------------------------

-- ----------------------------
-- Table structure for data_kelas
-- ----------------------------
DROP TABLE IF EXISTS `data_kelas`;
CREATE TABLE `data_kelas`  (
  `kode_kelas` varchar(6) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `nama_kelas` varchar(128) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  PRIMARY KEY (`kode_kelas`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of data_kelas
-- ----------------------------

-- ----------------------------
-- Table structure for data_mahasiswa
-- ----------------------------
DROP TABLE IF EXISTS `data_mahasiswa`;
CREATE TABLE `data_mahasiswa`  (
  `nim` varchar(16) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `nama_mahasiswa` varchar(128) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `angkatan` year NOT NULL,
  PRIMARY KEY (`nim`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of data_mahasiswa
-- ----------------------------

-- ----------------------------
-- Table structure for data_mk
-- ----------------------------
DROP TABLE IF EXISTS `data_mk`;
CREATE TABLE `data_mk`  (
  `kode_mk` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `nama_mk` varchar(128) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `sks` int NOT NULL,
  `semester` tinyint(1) NOT NULL,
  PRIMARY KEY (`kode_mk`) USING BTREE,
  INDEX `fk_semester`(`semester` ASC) USING BTREE,
  CONSTRAINT `fk_semester_mk` FOREIGN KEY (`semester`) REFERENCES `data_semester` (`semester`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of data_mk
-- ----------------------------

-- ----------------------------
-- Table structure for data_semester
-- ----------------------------
DROP TABLE IF EXISTS `data_semester`;
CREATE TABLE `data_semester`  (
  `tahun_1` year NOT NULL,
  `tahun_2` year NOT NULL,
  `semester` tinyint(1) NOT NULL,
  PRIMARY KEY (`semester`, `tahun_1`, `tahun_2`) USING BTREE,
  INDEX `tahun_1`(`tahun_1` ASC) USING BTREE,
  INDEX `semester`(`semester` ASC) USING BTREE,
  INDEX `tahun_2`(`tahun_2` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of data_semester
-- ----------------------------
INSERT INTO `data_semester` VALUES (2024, 2025, 1);
INSERT INTO `data_semester` VALUES (2024, 2025, 2);

-- ----------------------------
-- Table structure for isi_absen_dosen
-- ----------------------------
DROP TABLE IF EXISTS `isi_absen_dosen`;
CREATE TABLE `isi_absen_dosen`  (
  `nip` varchar(16) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `id_jadwal` int NOT NULL,
  `tanggal` date NOT NULL,
  PRIMARY KEY (`id_jadwal`, `tanggal`) USING BTREE,
  INDEX `fk_isi_dosen`(`nip` ASC) USING BTREE,
  CONSTRAINT `fk_isi_dosen` FOREIGN KEY (`nip`) REFERENCES `data_dosen` (`nip`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_isi_jadwal` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal_kuliah` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of isi_absen_dosen
-- ----------------------------

-- ----------------------------
-- Table structure for isi_absen_mhs
-- ----------------------------
DROP TABLE IF EXISTS `isi_absen_mhs`;
CREATE TABLE `isi_absen_mhs`  (
  `id_mhs` int NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  PRIMARY KEY (`id_mhs`, `tanggal`) USING BTREE,
  INDEX `fk_id_mhs`(`id_mhs` ASC) USING BTREE,
  CONSTRAINT `fk_id_mhs` FOREIGN KEY (`id_mhs`) REFERENCES `mhs_ambil_jadwal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of isi_absen_mhs
-- ----------------------------

-- ----------------------------
-- Table structure for jadwal_kuliah
-- ----------------------------
DROP TABLE IF EXISTS `jadwal_kuliah`;
CREATE TABLE `jadwal_kuliah`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_mk` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `kode_kelas` varchar(6) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `nip` varchar(16) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `nip2` varchar(16) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `nip3` varchar(16) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `hari` varchar(6) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `ruang` varchar(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '',
  `semester_char` varchar(9) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_mk`(`kode_mk` ASC) USING BTREE,
  INDEX `fk_kelas`(`kode_kelas` ASC) USING BTREE,
  INDEX `fk_nip`(`nip` ASC) USING BTREE,
  INDEX `fk_nip2`(`nip2` ASC) USING BTREE,
  INDEX `fk_nip3`(`nip3` ASC) USING BTREE,
  CONSTRAINT `fk_kelas` FOREIGN KEY (`kode_kelas`) REFERENCES `data_kelas` (`kode_kelas`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mk` FOREIGN KEY (`kode_mk`) REFERENCES `data_mk` (`kode_mk`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_nip` FOREIGN KEY (`nip`) REFERENCES `data_dosen` (`nip`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_nip2` FOREIGN KEY (`nip2`) REFERENCES `data_dosen` (`nip`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_nip3` FOREIGN KEY (`nip3`) REFERENCES `data_dosen` (`nip`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of jadwal_kuliah
-- ----------------------------

-- ----------------------------
-- Table structure for mhs_ambil_jadwal
-- ----------------------------
DROP TABLE IF EXISTS `mhs_ambil_jadwal`;
CREATE TABLE `mhs_ambil_jadwal`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nim` varchar(16) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `id_jadwal` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_nim_mhs`(`nim` ASC) USING BTREE,
  INDEX `fk_jadwal_mhs`(`id_jadwal` ASC) USING BTREE,
  CONSTRAINT `fk_nim_mhs` FOREIGN KEY (`nim`) REFERENCES `data_mahasiswa` (`nim`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_jadwal_mhs` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal_kuliah` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of mhs_ambil_jadwal
-- ----------------------------

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`  (
  `username` varchar(16) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `password` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `level` tinyint(1) NOT NULL,
  PRIMARY KEY (`username`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('admin', '0192023a7bbd73250516f069df18b500', 1);

SET FOREIGN_KEY_CHECKS = 1;
