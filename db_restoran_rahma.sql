-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 07, 2026 at 06:07 PM
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
-- Database: `db_restoran_rahma`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_detail_order_rahma`
--

CREATE TABLE `tbl_detail_order_rahma` (
  `id_dorder_rahma` int(11) NOT NULL,
  `id_order_rahma` int(11) NOT NULL,
  `id_menu_rahma` int(11) NOT NULL,
  `qty_rahma` int(11) NOT NULL,
  `catatan_rahma` varchar(255) NOT NULL,
  `status_item_rahma` enum('tersedia','kosong') NOT NULL,
  `subtotal_rahma` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_meja_rahma`
--

CREATE TABLE `tbl_meja_rahma` (
  `id_meja_rahma` int(11) NOT NULL,
  `status_rahma` enum('terpakai','kosong') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_meja_rahma`
--

INSERT INTO `tbl_meja_rahma` (`id_meja_rahma`, `status_rahma`) VALUES
(1, 'terpakai'),
(2, 'kosong'),
(3, 'kosong'),
(4, 'terpakai'),
(5, 'terpakai');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_menu_rahma`
--

CREATE TABLE `tbl_menu_rahma` (
  `id_menu_rahma` int(11) NOT NULL,
  `kategori_rahma` varchar(20) NOT NULL,
  `nama_menu_rahma` varchar(100) NOT NULL,
  `foto_rahma` varchar(255) NOT NULL,
  `harga_rahma` int(11) NOT NULL,
  `status_menu_rahma` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_menu_rahma`
--

INSERT INTO `tbl_menu_rahma` (`id_menu_rahma`, `kategori_rahma`, `nama_menu_rahma`, `foto_rahma`, `harga_rahma`, `status_menu_rahma`) VALUES
(1, 'makanan', 'Hamburg Steak (hambagu)', '-', 50000, 'tersedia'),
(2, 'makanan', 'Ayam Goreng (Karaage)', '-', 30000, 'tersedia'),
(3, 'minuman', 'teh hijau panas (tawar)', '-', 10000, 'tidak tersedia'),
(4, 'minuman', 'air putih ', '-', 5000, 'tersedia');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_order_rahma`
--

CREATE TABLE `tbl_order_rahma` (
  `id_order_rahma` int(11) NOT NULL,
  `id_meja_rahma` int(11) NOT NULL,
  `id_user_rahma` int(11) NOT NULL,
  `waktu_order_rahma` date NOT NULL,
  `status_order_rahma` enum('dibuat','selesai') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_role_rahma`
--

CREATE TABLE `tbl_role_rahma` (
  `id_role_rahma` int(11) NOT NULL,
  `role_rahma` enum('owner','kasir','member') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_role_rahma`
--

INSERT INTO `tbl_role_rahma` (`id_role_rahma`, `role_rahma`) VALUES
(1, 'owner'),
(2, 'kasir'),
(4, 'member');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_transaksi_rahma`
--

CREATE TABLE `tbl_transaksi_rahma` (
  `id_transaksi_rahma` int(11) NOT NULL,
  `id_order_rahma` int(11) NOT NULL,
  `diskon_rahma` int(11) NOT NULL,
  `total_rahma` int(11) NOT NULL,
  `bayar_rahma` int(11) NOT NULL,
  `kembalian_rahma` int(11) NOT NULL,
  `waktu_transaksi_rahma` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_rahma`
--

CREATE TABLE `tbl_user_rahma` (
  `id_user_rahma` int(11) NOT NULL,
  `username_rahma` varchar(50) NOT NULL,
  `password_rahma` varchar(255) NOT NULL,
  `nama_rahma` varchar(50) NOT NULL,
  `id_role_rahma` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user_rahma`
--

INSERT INTO `tbl_user_rahma` (`id_user_rahma`, `username_rahma`, `password_rahma`, `nama_rahma`, `id_role_rahma`) VALUES
(1, 'taka', '123', 'Taka Radjiman', 1),
(2, 'zea', '123', 'Zea Cornelia', 2),
(4, 'bon', '123', 'bonnivier', 4),
(5, 'jurard', '123', 'jurard ', 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_detail_order_rahma`
--
ALTER TABLE `tbl_detail_order_rahma`
  ADD PRIMARY KEY (`id_dorder_rahma`),
  ADD KEY `id_order_rahma` (`id_order_rahma`),
  ADD KEY `id_menu_rahma` (`id_menu_rahma`);

--
-- Indexes for table `tbl_meja_rahma`
--
ALTER TABLE `tbl_meja_rahma`
  ADD PRIMARY KEY (`id_meja_rahma`);

--
-- Indexes for table `tbl_menu_rahma`
--
ALTER TABLE `tbl_menu_rahma`
  ADD PRIMARY KEY (`id_menu_rahma`);

--
-- Indexes for table `tbl_order_rahma`
--
ALTER TABLE `tbl_order_rahma`
  ADD PRIMARY KEY (`id_order_rahma`),
  ADD KEY `id_meja_rahma` (`id_meja_rahma`),
  ADD KEY `id_user_rahma` (`id_user_rahma`);

--
-- Indexes for table `tbl_role_rahma`
--
ALTER TABLE `tbl_role_rahma`
  ADD PRIMARY KEY (`id_role_rahma`);

--
-- Indexes for table `tbl_transaksi_rahma`
--
ALTER TABLE `tbl_transaksi_rahma`
  ADD PRIMARY KEY (`id_transaksi_rahma`),
  ADD KEY `id_order_rahma` (`id_order_rahma`);

--
-- Indexes for table `tbl_user_rahma`
--
ALTER TABLE `tbl_user_rahma`
  ADD PRIMARY KEY (`id_user_rahma`),
  ADD KEY `id_role_rahma` (`id_role_rahma`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_detail_order_rahma`
--
ALTER TABLE `tbl_detail_order_rahma`
  MODIFY `id_dorder_rahma` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_meja_rahma`
--
ALTER TABLE `tbl_meja_rahma`
  MODIFY `id_meja_rahma` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_menu_rahma`
--
ALTER TABLE `tbl_menu_rahma`
  MODIFY `id_menu_rahma` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_order_rahma`
--
ALTER TABLE `tbl_order_rahma`
  MODIFY `id_order_rahma` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_role_rahma`
--
ALTER TABLE `tbl_role_rahma`
  MODIFY `id_role_rahma` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_transaksi_rahma`
--
ALTER TABLE `tbl_transaksi_rahma`
  MODIFY `id_transaksi_rahma` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_user_rahma`
--
ALTER TABLE `tbl_user_rahma`
  MODIFY `id_user_rahma` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_detail_order_rahma`
--
ALTER TABLE `tbl_detail_order_rahma`
  ADD CONSTRAINT `tbl_detail_order_rahma_ibfk_1` FOREIGN KEY (`id_menu_rahma`) REFERENCES `tbl_menu_rahma` (`id_menu_rahma`),
  ADD CONSTRAINT `tbl_detail_order_rahma_ibfk_2` FOREIGN KEY (`id_order_rahma`) REFERENCES `tbl_order_rahma` (`id_order_rahma`);

--
-- Constraints for table `tbl_order_rahma`
--
ALTER TABLE `tbl_order_rahma`
  ADD CONSTRAINT `tbl_order_rahma_ibfk_1` FOREIGN KEY (`id_meja_rahma`) REFERENCES `tbl_meja_rahma` (`id_meja_rahma`),
  ADD CONSTRAINT `tbl_order_rahma_ibfk_2` FOREIGN KEY (`id_user_rahma`) REFERENCES `tbl_user_rahma` (`id_user_rahma`);

--
-- Constraints for table `tbl_transaksi_rahma`
--
ALTER TABLE `tbl_transaksi_rahma`
  ADD CONSTRAINT `FK id_order` FOREIGN KEY (`id_order_rahma`) REFERENCES `tbl_order_rahma` (`id_order_rahma`);

--
-- Constraints for table `tbl_user_rahma`
--
ALTER TABLE `tbl_user_rahma`
  ADD CONSTRAINT `tbl_user_rahma_ibfk_1` FOREIGN KEY (`id_role_rahma`) REFERENCES `tbl_role_rahma` (`id_role_rahma`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
