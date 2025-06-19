-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 19, 2025 at 03:10 PM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 7.3.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sistem_integrasi`
--

-- --------------------------------------------------------

--
-- Table structure for table `data_dosen`
--

CREATE TABLE `data_dosen` (
  `nip` varchar(25) NOT NULL,
  `nama_gelar_depan` varchar(15) DEFAULT '-',
  `nama_dosen` varchar(128) NOT NULL,
  `nama_gelar_belakang` varchar(15) DEFAULT '-'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `data_kelas`
--

CREATE TABLE `data_kelas` (
  `kode_kelas` varchar(6) NOT NULL,
  `nama_kelas` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `data_mahasiswa`
--

CREATE TABLE `data_mahasiswa` (
  `nim` varchar(16) NOT NULL,
  `nama_mahasiswa` varchar(128) NOT NULL,
  `angkatan` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `data_mk`
--

CREATE TABLE `data_mk` (
  `kode_mk` varchar(10) NOT NULL,
  `nama_mk` varchar(128) NOT NULL,
  `sks` int(11) NOT NULL,
  `semester` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `data_semester`
--

CREATE TABLE `data_semester` (
  `tahun_1` year(4) NOT NULL,
  `tahun_2` year(4) NOT NULL,
  `semester` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `data_semester`
--

INSERT INTO `data_semester` (`tahun_1`, `tahun_2`, `semester`) VALUES
(2024, 2025, 2);

-- --------------------------------------------------------

--
-- Table structure for table `isi_absen_dosen`
--

CREATE TABLE `isi_absen_dosen` (
  `nip` varchar(25) NOT NULL,
  `id_jadwal` int(11) NOT NULL,
  `tanggal` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `isi_absen_mhs`
--

CREATE TABLE `isi_absen_mhs` (
  `id_mhs` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_kuliah`
--

CREATE TABLE `jadwal_kuliah` (
  `id` int(11) NOT NULL,
  `kode_mk` varchar(10) NOT NULL,
  `kode_kelas` varchar(6) NOT NULL,
  `nip` varchar(25) NOT NULL,
  `nip2` varchar(25) NOT NULL,
  `nip3` varchar(25) NOT NULL,
  `hari` varchar(6) NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `ruang` varchar(64) DEFAULT '',
  `semester_char` varchar(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `laporan_aktivitas`
--

CREATE TABLE `laporan_aktivitas` (
  `id_log` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL,
  `aktivitas` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mhs_ambil_jadwal`
--

CREATE TABLE `mhs_ambil_jadwal` (
  `id` int(11) NOT NULL,
  `nim` varchar(16) NOT NULL,
  `id_jadwal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `username` varchar(16) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`username`, `password`, `level`) VALUES
('-', '336d5ebc5436534e61d16e63ddfca327', 3),
('admin', '0192023a7bbd73250516f069df18b500', 2),
('D001', 'd5cbf528f740b502b79241ff873ce6c5', 3),
('D002', 'b3460c056d63af44fe66bd92c961a144', 3),
('D004', '22d164d469bfc0bc0f2aefe67dd2806e', 3),
('D005', 'b7ca9b1c84b0669f89357ee7bafb1274', 3),
('D006', '0d86b2d838d51e9ae5d1720054578f16', 3),
('D009', 'f6cce92549c349a9953bd877f4860f32', 3),
('D011', 'd5e4effc166af7f050089416e7e53b57', 3),
('D013', 'e66e21ac2b0dbf5f42a77ae8373e789e', 3),
('D014', '420c0ede70cff1d160c862004a7bfee0', 3),
('D016', '5cedfc034ccdad75d58a0fa1875d396a', 3),
('D017', '447f299d2ed46a403af8ec2b9cfff194', 3),
('D018', '62cc46459284fd62c0ad61eb117f1645', 3),
('D022', '13c802aa19ff6d9acf1bd501d7fc9943', 3),
('D024', 'cb756d3448ad467033846e84017824f3', 3),
('D025', 'c2921c13fa62065c8d28c85aae51497e', 3),
('D029', 'fcc8fa0ea88d370507a094126bd40fae', 3),
('D031', '01f00daeed1418fb5cf6d8988d8a8fab', 3),
('D032', '32d5e36f384bebd5d3753a04a0302eec', 3),
('D035', 'e95451662d98089c12c674cd6c4c95fa', 3),
('D036', '8275737b27435dc0e3824ffa513d38c6', 3),
('D296', '47835e4e0eb8e4cbea1512700b57a7f4', 3),
('D335', 'e39cae53e4689c37b38d1e5d21658699', 3),
('D399', '89b81b9b95c49793d1cdecc5d933bd59', 3),
('D448', '14e07cc035e0ec47f7380ab6a2ea3857', 3),
('D523', '7926d82d3c40ca50160a8cf25c05169b', 3),
('D532', '550fb16d3453cbab234f999a75ab7496', 3),
('D559', '7ac105ce11d559539983950121475263', 3),
('D579', '41eaacf78084c24455bf1a6efe983c4c', 3),
('D722', '4c666b52ddaa0521738e8e63dea5a668', 3),
('D760', 'd617402adfa1b1785122d6bfaa21959e', 3),
('D764', '20e4a3ffe09e5606f325367457be7339', 3),
('D829', '6c9bcdbc44d923dc069fe067a90d3296', 3),
('D878', 'd1cb0b6c52de00ec016b4512b2827cf0', 3),
('D966', '43973f2cd2761555581887c05f18a146', 3),
('D969', '5d15c98d7da839a11ac4c54e072a8872', 3),
('D990', 'd5ce3ab32372e9d24f8fd7aa3df8b556', 3),
('kaprodi', '827ccb0eea8a706c4c34a16891f84e7b', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `data_dosen`
--
ALTER TABLE `data_dosen`
  ADD PRIMARY KEY (`nip`);

--
-- Indexes for table `data_kelas`
--
ALTER TABLE `data_kelas`
  ADD PRIMARY KEY (`kode_kelas`);

--
-- Indexes for table `data_mahasiswa`
--
ALTER TABLE `data_mahasiswa`
  ADD PRIMARY KEY (`nim`);

--
-- Indexes for table `data_mk`
--
ALTER TABLE `data_mk`
  ADD PRIMARY KEY (`kode_mk`),
  ADD KEY `fk_semester` (`semester`);

--
-- Indexes for table `data_semester`
--
ALTER TABLE `data_semester`
  ADD PRIMARY KEY (`semester`,`tahun_1`,`tahun_2`) USING BTREE,
  ADD KEY `tahun_1` (`tahun_1`) USING BTREE,
  ADD KEY `semester` (`semester`) USING BTREE,
  ADD KEY `tahun_2` (`tahun_2`) USING BTREE;

--
-- Indexes for table `isi_absen_dosen`
--
ALTER TABLE `isi_absen_dosen`
  ADD PRIMARY KEY (`id_jadwal`,`tanggal`) USING BTREE,
  ADD KEY `fk_isi_dosen` (`nip`);

--
-- Indexes for table `isi_absen_mhs`
--
ALTER TABLE `isi_absen_mhs`
  ADD PRIMARY KEY (`id_mhs`,`tanggal`) USING BTREE,
  ADD KEY `fk_id_mhs` (`id_mhs`) USING BTREE;

--
-- Indexes for table `jadwal_kuliah`
--
ALTER TABLE `jadwal_kuliah`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_mk` (`kode_mk`),
  ADD KEY `fk_kelas` (`kode_kelas`),
  ADD KEY `fk_nip` (`nip`),
  ADD KEY `fk_nip2` (`nip2`),
  ADD KEY `fk_nip3` (`nip3`);

--
-- Indexes for table `laporan_aktivitas`
--
ALTER TABLE `laporan_aktivitas`
  ADD PRIMARY KEY (`id_log`);

--
-- Indexes for table `mhs_ambil_jadwal`
--
ALTER TABLE `mhs_ambil_jadwal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_nim_mhs` (`nim`),
  ADD KEY `fk_jadwal_mhs` (`id_jadwal`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `jadwal_kuliah`
--
ALTER TABLE `jadwal_kuliah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `laporan_aktivitas`
--
ALTER TABLE `laporan_aktivitas`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mhs_ambil_jadwal`
--
ALTER TABLE `mhs_ambil_jadwal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `data_mk`
--
ALTER TABLE `data_mk`
  ADD CONSTRAINT `fk_semester_mk` FOREIGN KEY (`semester`) REFERENCES `data_semester` (`semester`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `isi_absen_dosen`
--
ALTER TABLE `isi_absen_dosen`
  ADD CONSTRAINT `fk_isi_dosen` FOREIGN KEY (`nip`) REFERENCES `data_dosen` (`nip`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_isi_jadwal` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal_kuliah` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `isi_absen_mhs`
--
ALTER TABLE `isi_absen_mhs`
  ADD CONSTRAINT `fk_id_mhs` FOREIGN KEY (`id_mhs`) REFERENCES `mhs_ambil_jadwal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `jadwal_kuliah`
--
ALTER TABLE `jadwal_kuliah`
  ADD CONSTRAINT `fk_kelas` FOREIGN KEY (`kode_kelas`) REFERENCES `data_kelas` (`kode_kelas`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mk` FOREIGN KEY (`kode_mk`) REFERENCES `data_mk` (`kode_mk`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_nip` FOREIGN KEY (`nip`) REFERENCES `data_dosen` (`nip`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_nip2` FOREIGN KEY (`nip2`) REFERENCES `data_dosen` (`nip`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_nip3` FOREIGN KEY (`nip3`) REFERENCES `data_dosen` (`nip`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `mhs_ambil_jadwal`
--
ALTER TABLE `mhs_ambil_jadwal`
  ADD CONSTRAINT `fk_jadwal_mhs` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal_kuliah` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_nim_mhs` FOREIGN KEY (`nim`) REFERENCES `data_mahasiswa` (`nim`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
