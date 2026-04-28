-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2026 at 07:21 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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
  `id_dorder_rahma` varchar(10) NOT NULL,
  `id_order_rahma` varchar(10) NOT NULL,
  `id_menu_rahma` varchar(10) NOT NULL,
  `qty_rahma` int(11) NOT NULL,
  `catatan_rahma` varchar(255) NOT NULL,
  `status_item_rahma` enum('tersedia','kosong') NOT NULL,
  `subtotal_rahma` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_detail_order_rahma`
--

INSERT INTO `tbl_detail_order_rahma` (`id_dorder_rahma`, `id_order_rahma`, `id_menu_rahma`, `qty_rahma`, `catatan_rahma`, `status_item_rahma`, `subtotal_rahma`) VALUES
('DOD001', 'OD001', 'MN001', 2, 'jangan terlalu pedas', 'tersedia', 100000),
('DOD002', 'OD002', 'MN002', 1, 'porsinya banyakin', 'tersedia', 30000),
('DOD003', 'OD003', 'MN004', 3, '-', 'tersedia', 15000),
('DOD004', 'OD004', 'MN002', 1, '-', 'tersedia', 30000),
('DOD005', 'OD004', 'MN001', 2, '-', 'tersedia', 100000),
('DOD006', 'OD004', 'MN004', 1, '-', 'tersedia', 5000),
('DOD007', 'OD004', 'MN006', 2, '-', 'tersedia', 30000),
('DOD008', 'OD005', 'MN002', 1, '', 'tersedia', 30000),
('DOD009', 'OD005', 'MN004', 1, '', 'tersedia', 5000),
('DOD010', 'OD006', 'MN001', 1, '', 'tersedia', 50000),
('DOD011', 'OD007', 'MN002', 1, '', 'tersedia', 30000),
('DOD012', 'OD007', 'MN006', 1, '', 'tersedia', 15000),
('DOD013', 'OD008', 'MN001', 1, '', 'tersedia', 50000),
('DOD014', 'OD009', 'MN002', 1, '', 'tersedia', 30000),
('DOD015', 'OD010', 'MN004', 1, '', 'tersedia', 5000),
('DOD016', 'OD011', 'MN002', 1, '', 'tersedia', 30000),
('DOD017', 'OD012', 'MN001', 1, '', 'tersedia', 50000),
('DOD018', 'OD013', 'MN004', 1, '', 'tersedia', 5000),
('DOD019', 'OD014', 'MN001', 1, '', 'tersedia', 50000),
('DOD020', 'OD015', 'MN001', 1, '', 'tersedia', 50000),
('DOD021', 'OD016', 'MN002', 1, '', 'tersedia', 30000),
('DOD022', 'OD016', 'MN001', 1, '', 'tersedia', 50000),
('DOD023', 'OD016', 'MN004', 2, '', 'tersedia', 10000),
('DOD024', 'OD017', 'MN006', 36, '', 'tersedia', 540000),
('DOD025', 'OD018', 'MN001', 7, '', 'tersedia', 350000),
('DOD026', 'OD018', 'MN006', 2, '', 'tersedia', 30000),
('DOD027', 'OD019', 'MN002', 1, '', 'tersedia', 30000),
('DOD028', 'OD019', 'MN001', 1, '', 'tersedia', 50000),
('DOD029', 'OD020', 'MN002', 2, '', 'tersedia', 60000),
('DOD030', 'OD020', 'MN004', 2, '', 'tersedia', 10000),
('DOD031', 'OD021', 'MN004', 2, '', 'tersedia', 10000),
('DOD032', 'OD022', 'MN002', 1, '', 'tersedia', 30000),
('DOD033', 'OD022', 'MN006', 1, '', 'tersedia', 15000),
('DOD034', 'OD022', 'MN004', 1, '', 'tersedia', 5000),
('DOD035', 'OD023', 'MN004', 2, '', 'tersedia', 10000),
('DOD036', 'OD024', 'MN002', 1, '', 'tersedia', 30000),
('DOD037', 'OD024', 'MN004', 1, '', 'tersedia', 5000),
('DOD038', 'OD025', 'MN004', 1, '', 'tersedia', 5000),
('DOD039', 'OD026', 'MN004', 1, '', 'tersedia', 5000),
('DOD040', 'OD027', 'MN001', 1, '', 'tersedia', 50000),
('DOD041', 'OD028', 'MN001', 1, '', 'tersedia', 50000),
('DOD042', 'OD028', 'MN004', 1, '', 'tersedia', 5000),
('DOD043', 'OD029', 'MN004', 1, '', 'tersedia', 5000),
('DOD044', 'OD030', 'MN004', 1, '', 'tersedia', 5000),
('DOD045', 'OD031', 'MN001', 1, '', 'tersedia', 50000);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_meja_rahma`
--

CREATE TABLE `tbl_meja_rahma` (
  `id_meja_rahma` varchar(10) NOT NULL,
  `status_rahma` enum('terpakai','kosong') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_meja_rahma`
--

INSERT INTO `tbl_meja_rahma` (`id_meja_rahma`, `status_rahma`) VALUES
('M001', 'kosong'),
('M002', 'kosong'),
('M003', 'kosong'),
('M004', 'kosong'),
('M005', 'kosong'),
('M006', 'kosong'),
('M007', 'kosong');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_menu_rahma`
--

CREATE TABLE `tbl_menu_rahma` (
  `id_menu_rahma` varchar(10) NOT NULL,
  `kategori_rahma` varchar(20) NOT NULL,
  `nama_menu_rahma` varchar(100) NOT NULL,
  `deskripsi_rahma` text NOT NULL,
  `foto_rahma` varchar(255) NOT NULL,
  `harga_rahma` int(11) NOT NULL,
  `status_menu_rahma` varchar(20) NOT NULL,
  `status_rahma` enum('aktif','nonaktif') DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_menu_rahma`
--

INSERT INTO `tbl_menu_rahma` (`id_menu_rahma`, `kategori_rahma`, `nama_menu_rahma`, `deskripsi_rahma`, `foto_rahma`, `harga_rahma`, `status_menu_rahma`, `status_rahma`) VALUES
('MN001', 'makanan', 'Hamburg Steak (hambagu)', 'Bistik daging cincang khas Jepang yang juicy dan empuk, disiram saus demi-glace gurih. Disajikan dengan sayuran dan nasi hangat', 'hambagu.jpg', 50000, 'tersedia', 'aktif'),
('MN002', 'makanan', 'Ayam Goreng (Karaage)', 'Potongan paha ayam fillet tanpa tulang yang digoreng garing keemasan. Gurih, aromatik, dan disajikan dengan perasan lemon segar', 'karaage.jpg', 30000, 'tersedia', 'aktif'),
('MN003', 'minuman', 'teh hijau panas (tawar)', 'Teh hijau Jepang autentik yang diseduh panas dengan aroma menenangkan dan rasa tawar yang bersih di lidah', 'hot ocha.jpg', 10000, 'habis', 'aktif'),
('MN004', 'minuman', 'air putih ', 'Air mineral kemasan berkualitas dalam suhu ruang atau dingin yang segar dan murni', 'air putih.jpg', 5000, 'tersedia', 'aktif'),
('MN005', 'makanan', 'Omurice fluffy', 'Selimut telur dadar yang lembut dan creamy membungkus nasi goreng saus tomat yang gurih. Biasanya disajikan dengan siraman saus tomat atau beef stew yang mewah.', 'omurice.jpg', 30000, 'habis', 'aktif'),
('MN006', 'minuman', 'Melon Soda Float', 'Soda melon hijau yang ceria dengan topping satu scoop es krim vanila lembut. Kombinasi rasa manis-segar yang membawa Anda bernostalgia ke masa kecil.', '1772348802_melon soda float.jpg', 15000, 'tersedia', 'aktif');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_order_rahma`
--

CREATE TABLE `tbl_order_rahma` (
  `id_order_rahma` varchar(10) NOT NULL,
  `id_meja_rahma` varchar(10) DEFAULT NULL,
  `id_user_rahma` varchar(10) DEFAULT NULL,
  `nama_pelanggan_rahma` varchar(20) NOT NULL,
  `keterangan_rahma` varchar(255) NOT NULL,
  `waktu_order_rahma` date NOT NULL,
  `status_order_rahma` enum('menunggu_pembayaran','diproses','selesai','disajikan') NOT NULL,
  `jenis_pesanan_rahma` enum('dine in','take away') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_order_rahma`
--

INSERT INTO `tbl_order_rahma` (`id_order_rahma`, `id_meja_rahma`, `id_user_rahma`, `nama_pelanggan_rahma`, `keterangan_rahma`, `waktu_order_rahma`, `status_order_rahma`, `jenis_pesanan_rahma`) VALUES
('OD001', 'M001', 'USN004', 'bonnivier', 'cepat masaknya', '2026-02-12', 'selesai', 'dine in'),
('OD002', 'M002', NULL, 'arion', 'di antar ke meja nanti magrib', '2026-02-12', 'selesai', 'dine in'),
('OD003', 'M003', NULL, 'Nakula ', '-', '2026-04-09', 'selesai', 'dine in'),
('OD004', 'M001', NULL, 'yuyo', '', '2026-04-09', 'selesai', 'dine in'),
('OD005', 'M002', 'USN004', 'bonnivier', '', '2026-04-11', 'selesai', 'dine in'),
('OD006', 'M001', NULL, 'bimbim', 'cepat bikinnya', '2026-04-11', 'selesai', 'dine in'),
('OD007', 'M007', NULL, 'nakula', '', '2026-04-11', 'selesai', 'dine in'),
('OD008', NULL, NULL, 'sagara', '', '2026-04-21', 'selesai', 'dine in'),
('OD009', NULL, NULL, 'sagara', 'semangat kerjanya', '2026-04-21', 'selesai', 'dine in'),
('OD010', NULL, NULL, 'harris', '', '2026-04-21', 'selesai', 'dine in'),
('OD011', NULL, NULL, 'harris', 'zipzipzip', '2026-04-21', 'selesai', 'take away'),
('OD012', 'M003', NULL, 'ukiki', 'dagingnya yang banyak', '2026-04-21', 'selesai', 'dine in'),
('OD013', 'M003', NULL, 'nakula', '', '2026-04-22', 'selesai', 'dine in'),
('OD014', NULL, NULL, 'yuyo', '', '2026-04-22', 'selesai', 'take away'),
('OD015', NULL, NULL, 'iwak', '', '2026-04-22', 'selesai', 'take away'),
('OD016', NULL, NULL, 'bimbim', '', '2026-04-22', 'selesai', 'take away'),
('OD017', 'M007', NULL, 'sagara', 'Harus hijau', '2026-04-22', 'selesai', 'dine in'),
('OD018', 'M004', 'USN010', 'Maii', '', '2026-04-22', 'selesai', 'dine in'),
('OD019', NULL, 'USN008', 'nakula nalendra', '', '2026-04-23', 'selesai', 'take away'),
('OD020', NULL, 'USN004', 'bonnivier', '', '2026-04-23', 'selesai', 'take away'),
('OD021', NULL, 'USN004', 'bonnivier', '', '2026-04-23', 'selesai', 'take away'),
('OD022', 'M001', 'USN007', 'rion kenzo chana', '', '2026-04-23', 'selesai', 'dine in'),
('OD023', 'M001', 'USN007', 'rion kenzo chana', '', '2026-04-23', 'selesai', 'dine in'),
('OD024', 'M002', 'USN008', 'nakula nalendra', '', '2026-04-23', 'selesai', 'dine in'),
('OD025', 'M002', 'USN008', 'nakula nalendra', '', '2026-04-23', 'selesai', 'dine in'),
('OD026', 'M002', NULL, 'arjuna', '', '2026-04-23', 'selesai', 'dine in'),
('OD027', NULL, NULL, 'arutala', '', '2026-04-23', '', 'take away'),
('OD028', 'M001', 'USN004', 'bonnivier', 'kalau bisa dipercepat', '2026-04-27', '', 'dine in'),
('OD029', 'M001', 'USN004', 'bonnivier', '', '2026-04-27', '', 'dine in'),
('OD030', 'M001', 'USN004', 'bonnivier', '', '2026-04-27', '', 'dine in'),
('OD031', 'M003', NULL, 'key', 'yang banyak', '2026-04-27', 'selesai', 'dine in');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_role_rahma`
--

CREATE TABLE `tbl_role_rahma` (
  `id_role_rahma` varchar(10) NOT NULL,
  `role_rahma` enum('owner','kasir','member','chef') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_role_rahma`
--

INSERT INTO `tbl_role_rahma` (`id_role_rahma`, `role_rahma`) VALUES
('R001', 'owner'),
('R002', 'kasir'),
('R003', 'member'),
('R004', 'chef');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_transaksi_rahma`
--

CREATE TABLE `tbl_transaksi_rahma` (
  `id_transaksi_rahma` varchar(10) NOT NULL,
  `id_order_rahma` varchar(10) NOT NULL,
  `diskon_rahma` int(11) NOT NULL,
  `total_rahma` int(11) NOT NULL,
  `bayar_rahma` int(11) NOT NULL,
  `kembalian_rahma` int(11) NOT NULL,
  `waktu_transaksi_rahma` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_transaksi_rahma`
--

INSERT INTO `tbl_transaksi_rahma` (`id_transaksi_rahma`, `id_order_rahma`, `diskon_rahma`, `total_rahma`, `bayar_rahma`, `kembalian_rahma`, `waktu_transaksi_rahma`) VALUES
('T001', 'OD001', 0, 100000, 100000, 0, '2026-02-12'),
('T002', 'OD002', 0, 30000, 50000, 20000, '2026-02-12'),
('T003', 'OD003', 0, 15000, 20000, 5000, '2026-04-09'),
('T004', 'OD004', 0, 165000, 170000, 5000, '2026-04-11'),
('T005', 'OD005', 0, 35000, 35000, 0, '2026-04-11'),
('T006', 'OD006', 0, 50000, 50000, 0, '2026-04-11'),
('T007', 'OD007', 0, 45000, 45000, 0, '2026-04-11'),
('T008', 'OD010', 0, 5000, 10000, 5000, '2026-04-21'),
('T009', 'OD008', 0, 50000, 50000, 0, '2026-04-21'),
('T010', 'OD009', 0, 30000, 30000, 0, '2026-04-21'),
('T011', 'OD011', 0, 30000, 30000, 0, '2026-04-21'),
('T012', 'OD012', 0, 50000, 50000, 0, '2026-04-21'),
('T013', 'OD014', 0, 50000, 60000, 10000, '2026-04-22'),
('T014', 'OD013', 0, 5000, 5000, 0, '2026-04-22'),
('T015', 'OD016', 0, 90000, 90000, 0, '2026-04-22'),
('T016', 'OD017', 0, 540000, 540000, 0, '2026-04-22'),
('T017', 'OD015', 0, 50000, 100000, 50000, '2026-04-22'),
('T018', 'OD018', 0, 380000, 500000, 120000, '2026-04-22'),
('T019', 'OD019', 10, 72000, 80000, 8000, '2026-04-23'),
('T020', 'OD020', 10, 63000, 63000, 0, '2026-04-23'),
('T021', 'OD021', 10, 9000, 10000, 1000, '2026-04-23'),
('T022', 'OD022', 10, 45000, 50000, 5000, '2026-04-23'),
('T023', 'OD023', 10, 9000, 9000, 0, '2026-04-23'),
('T024', 'OD024', 10, 31500, 32000, 500, '2026-04-23'),
('T025', 'OD025', 10, 4500, 5000, 500, '2026-04-23'),
('T026', 'OD026', 0, 5000, 5000, 0, '2026-04-23'),
('T027', 'OD031', 0, 50000, 50000, 0, '2026-04-27');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_rahma`
--

CREATE TABLE `tbl_user_rahma` (
  `id_user_rahma` varchar(10) NOT NULL,
  `username_rahma` varchar(50) NOT NULL,
  `password_rahma` varchar(255) NOT NULL,
  `nama_rahma` varchar(50) NOT NULL,
  `id_role_rahma` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user_rahma`
--

INSERT INTO `tbl_user_rahma` (`id_user_rahma`, `username_rahma`, `password_rahma`, `nama_rahma`, `id_role_rahma`) VALUES
('USN001', 'taka', '$2y$10$QWltnlKwN0LKtAZKHRJSCeTZjESGp4mPex5Gng4VGJKz9aHBgrtOa', 'Taka Radjiman', 'R001'),
('USN002', 'zea', '$2y$10$aGJ5R/RgNyY1MlEwiJ72q.lucxQJfDIXUH20SGPhFkAubb8h/H04S', 'Zea Cornelia', 'R002'),
('USN004', 'bon', '$2y$10$ZqoanGwr56MorPwKassJFeS8VY.jHAiyZiUVL7Vctkn2R6gPq1hbq', 'bonnivier', 'R003'),
('USN006', 'sadewa ', '$2y$10$ktrjABUJjlyFfha/cAJOEulLEWnqvwkSsPDt66u0hkbpUOaRTgMRG', 'sadewa sagara', 'R004'),
('USN007', 'rion', '$2y$10$m9fYTe5ePAdpFCRk6nwmKuz01xHg2co1Pe4/iu71fBI7L.bIpWRaC', 'rion kenzo chana', 'R003'),
('USN008', 'nakul', '$2y$10$7r4J0/uB8Juc0.gIjM6Ese45RNjTSi3WNeiIIk/wn9i9FDGFJm6dG', 'nakula nalendra', 'R003'),
('USN009', 'putra', '$2y$10$GOPCHtjGemeC6KJNFWqTIulp8L41kfJWIJ2iUtjTHqkIPleNhj7Ve', 'rizqy ramadhan indrawan putra', 'R003'),
('USN010', 'Teg', '$2y$10$XlNuO5mPHb/mT4UUOymlFuAoyMJSEPJBrnwsjDger8U30v9/nEO2u', 'Maii', 'R003');

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
  ADD CONSTRAINT `tbl_transaksi_rahma_ibfk_1` FOREIGN KEY (`id_order_rahma`) REFERENCES `tbl_order_rahma` (`id_order_rahma`);

--
-- Constraints for table `tbl_user_rahma`
--
ALTER TABLE `tbl_user_rahma`
  ADD CONSTRAINT `id_roleFK` FOREIGN KEY (`id_role_rahma`) REFERENCES `tbl_role_rahma` (`id_role_rahma`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
