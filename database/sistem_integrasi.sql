-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 16, 2025 at 04:10 PM
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
CREATE DATABASE IF NOT EXISTS `sistem_integrasi` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `sistem_integrasi`;

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `id_jadwal` int(11) NOT NULL,
  `tanggal` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `data_dosen`
--

CREATE TABLE `data_dosen` (
  `nip` varchar(16) NOT NULL,
  `nama_dosen` varchar(128) NOT NULL,
  `no_hp` varchar(15) NOT NULL DEFAULT '+62',
  `email` varchar(128) NOT NULL DEFAULT '@mail'
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
  `no_hp` varchar(15) NOT NULL DEFAULT '+62',
  `email` varchar(128) NOT NULL DEFAULT '@mail'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `data_mk`
--

CREATE TABLE `data_mk` (
  `kode_mk` varchar(6) NOT NULL,
  `nama_mk` varchar(128) NOT NULL,
  `sks` smallint(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `isi_absen_dosen`
--

CREATE TABLE `isi_absen_dosen` (
  `id` int(11) NOT NULL,
  `nip` varchar(16) NOT NULL,
  `id_absen` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `isi_absen_mhs`
--

CREATE TABLE `isi_absen_mhs` (
  `id` int(11) NOT NULL,
  `nim` varchar(16) NOT NULL,
  `id_absen` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_kuliah`
--

CREATE TABLE `jadwal_kuliah` (
  `id` int(11) NOT NULL,
  `kode_mk` varchar(6) NOT NULL,
  `kode_kelas` varchar(6) NOT NULL,
  `nip` varchar(16) NOT NULL,
  `hari` varchar(6) NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL
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
('admin', '0192023a7bbd73250516f069df18b500', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_jadwal` (`id_jadwal`);

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
  ADD PRIMARY KEY (`kode_mk`);

--
-- Indexes for table `isi_absen_dosen`
--
ALTER TABLE `isi_absen_dosen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_absen_dosen` (`id_absen`),
  ADD KEY `fk_isi_dosen` (`nip`);

--
-- Indexes for table `isi_absen_mhs`
--
ALTER TABLE `isi_absen_mhs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_absen_mhs` (`id_absen`),
  ADD KEY `fk_isi_mhs` (`nim`);

--
-- Indexes for table `jadwal_kuliah`
--
ALTER TABLE `jadwal_kuliah`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_mk` (`kode_mk`),
  ADD KEY `fk_kelas` (`kode_kelas`),
  ADD KEY `fk_nip` (`nip`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `isi_absen_dosen`
--
ALTER TABLE `isi_absen_dosen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `isi_absen_mhs`
--
ALTER TABLE `isi_absen_mhs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jadwal_kuliah`
--
ALTER TABLE `jadwal_kuliah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `fk_jadwal` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal_kuliah` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `isi_absen_dosen`
--
ALTER TABLE `isi_absen_dosen`
  ADD CONSTRAINT `fk_absen_dosen` FOREIGN KEY (`id_absen`) REFERENCES `absensi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_isi_dosen` FOREIGN KEY (`nip`) REFERENCES `data_dosen` (`nip`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `isi_absen_mhs`
--
ALTER TABLE `isi_absen_mhs`
  ADD CONSTRAINT `fk_absen_mhs` FOREIGN KEY (`id_absen`) REFERENCES `absensi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_isi_mhs` FOREIGN KEY (`nim`) REFERENCES `data_mahasiswa` (`nim`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `jadwal_kuliah`
--
ALTER TABLE `jadwal_kuliah`
  ADD CONSTRAINT `fk_kelas` FOREIGN KEY (`kode_kelas`) REFERENCES `data_kelas` (`kode_kelas`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mk` FOREIGN KEY (`kode_mk`) REFERENCES `data_mk` (`kode_mk`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_nip` FOREIGN KEY (`nip`) REFERENCES `data_dosen` (`nip`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
