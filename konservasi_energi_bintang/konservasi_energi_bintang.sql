-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 25, 2026 at 03:42 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `konservasi_energi_bintang`
--

-- --------------------------------------------------------

--
-- Table structure for table `checklist`
--

CREATE TABLE `checklist` (
  `id_checklist` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu_cek` time NOT NULL,
  `ruangan` varchar(100) NOT NULL,
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `checklist`
--

INSERT INTO `checklist` (`id_checklist`, `id_user`, `tanggal`, `waktu_cek`, `ruangan`, `catatan`) VALUES
(1, 2, '2026-11-25', '15:01:00', 'Lab RPL 2', 'Kelas sudah selesai dan siap pulang'),
(2, 5, '2026-11-25', '15:37:00', 'Lab RPL 2', 'Sudah mati semua'),
(3, 2, '2026-11-25', '15:39:00', 'Lab RPL 2', 'Semuanya aman');

-- --------------------------------------------------------

--
-- Table structure for table `detail_checklist`
--

CREATE TABLE `detail_checklist` (
  `id_detail` int(11) NOT NULL,
  `id_checklist` int(11) NOT NULL,
  `id_perangkat` int(11) NOT NULL,
  `kondisi` enum('Sudah Dimatikan','Masih Menyala') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_checklist`
--

INSERT INTO `detail_checklist` (`id_detail`, `id_checklist`, `id_perangkat`, `kondisi`) VALUES
(1, 1, 2, 'Sudah Dimatikan'),
(2, 1, 1, 'Sudah Dimatikan'),
(3, 1, 3, 'Sudah Dimatikan'),
(4, 1, 5, 'Sudah Dimatikan'),
(5, 1, 4, 'Sudah Dimatikan'),
(6, 2, 2, 'Sudah Dimatikan'),
(7, 2, 3, 'Sudah Dimatikan'),
(8, 2, 1, 'Sudah Dimatikan'),
(9, 2, 5, 'Sudah Dimatikan'),
(10, 2, 4, 'Sudah Dimatikan'),
(11, 3, 2, 'Sudah Dimatikan'),
(12, 3, 3, 'Sudah Dimatikan'),
(13, 3, 1, 'Sudah Dimatikan'),
(14, 3, 5, 'Sudah Dimatikan'),
(15, 3, 4, 'Sudah Dimatikan');

-- --------------------------------------------------------

--
-- Table structure for table `perangkat`
--

CREATE TABLE `perangkat` (
  `id_perangkat` int(11) NOT NULL,
  `nama_perangkat` varchar(100) NOT NULL,
  `lokasi` varchar(100) NOT NULL,
  `daya_watt` int(11) NOT NULL DEFAULT 0,
  `status_aktif` enum('Aktif','Nonaktif') DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `perangkat`
--

INSERT INTO `perangkat` (`id_perangkat`, `nama_perangkat`, `lokasi`, `daya_watt`, `status_aktif`) VALUES
(1, 'Lampu LED Kelas', 'Lab RPL 2', 18, 'Aktif'),
(2, 'AC Split 1 PK', 'Lab RPL 2', 750, 'Aktif'),
(3, 'Komputer PC Unit', 'Lab RPL 2', 250, 'Aktif'),
(4, 'Proyektor LCD', 'Lab RPL 2', 220, 'Aktif'),
(5, 'Layar Monitor Besar', 'Lab RPL 2', 55, 'Aktif');

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `id_siswa` int(11) NOT NULL,
  `nis` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kelas` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`id_siswa`, `nis`, `nama`, `kelas`) VALUES
(1, '1001', 'Budi', 'XI RPL 2'),
(2, '1002', 'Budy', 'XI RPL 2'),
(3, '1003', 'Opi', 'XI RPL 1');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','guru','siswa') NOT NULL DEFAULT 'siswa',
  `kelas` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama_lengkap`, `username`, `password`, `role`, `kelas`) VALUES
(1, 'Admin', 'admin', 'adm12345', 'admin', 'X RPL 2'),
(2, 'Budi', 'budi', '12345', 'siswa', 'X RPL 2'),
(3, 'Budy', 'bidy', '12345', 'siswa', 'XI RPL 2'),
(4, 'Opi', 'opi', '12345', 'siswa', 'XI RPL 1'),
(5, 'Guru', 'Guru DPK', 'guru123', 'guru', 'XI RPL 2');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `checklist`
--
ALTER TABLE `checklist`
  ADD PRIMARY KEY (`id_checklist`);

--
-- Indexes for table `detail_checklist`
--
ALTER TABLE `detail_checklist`
  ADD PRIMARY KEY (`id_detail`);

--
-- Indexes for table `perangkat`
--
ALTER TABLE `perangkat`
  ADD PRIMARY KEY (`id_perangkat`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id_siswa`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `checklist`
--
ALTER TABLE `checklist`
  MODIFY `id_checklist` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `detail_checklist`
--
ALTER TABLE `detail_checklist`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `perangkat`
--
ALTER TABLE `perangkat`
  MODIFY `id_perangkat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id_siswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
