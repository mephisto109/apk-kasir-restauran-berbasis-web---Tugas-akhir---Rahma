-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 03, 2026 at 06:54 PM
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
('DOD045', 'OD031', 'MN001', 1, '', 'tersedia', 50000),
('DOD046', 'OD032', 'MN002', 1, '', 'tersedia', 30000),
('DOD047', 'OD032', 'MN004', 1, '', 'tersedia', 5000),
('DOD048', 'OD033', 'MN002', 1, '', 'tersedia', 30000),
('DOD049', 'OD033', 'MN004', 1, '', 'tersedia', 5000),
('DOD050', 'OD034', 'MN006', 1, '', 'tersedia', 15000),
('DOD051', 'OD035', 'MN001', 1, '', 'tersedia', 50000),
('DOD052', 'OD036', 'MN001', 1, '', 'tersedia', 50000),
('DOD053', 'OD036', 'MN004', 1, '', 'tersedia', 5000),
('DOD054', 'OD037', 'MN002', 1, '', 'tersedia', 30000),
('DOD055', 'OD038', 'MN002', 2, '', 'tersedia', 60000),
('DOD056', 'OD038', 'MN004', 2, '', 'tersedia', 10000),
('DOD057', 'OD039', 'MN005', 1, '', 'tersedia', 30000),
('DOD058', 'OD040', 'MN005', 1, '', 'tersedia', 30000),
('DOD059', 'OD040', 'MN006', 1, '', 'tersedia', 15000),
('DOD060', 'OD041', 'MN002', 1, '', 'tersedia', 30000),
('DOD061', 'OD041', 'MN004', 1, '', 'tersedia', 5000),
('DOD062', 'OD042', 'MN005', 1, '', 'tersedia', 30000),
('DOD063', 'OD042', 'MN006', 1, '', 'tersedia', 15000),
('DOD064', 'OD043', 'MN005', 1, '', 'tersedia', 30000),
('DOD065', 'OD044', 'MN005', 1, '', 'tersedia', 30000),
('DOD066', 'OD045', 'MN001', 1, '', 'tersedia', 50000),
('DOD067', 'OD045', 'MN004', 1, '', 'tersedia', 5000),
('DOD068', 'OD046', 'MN005', 1, '', 'tersedia', 30000),
('DOD069', 'OD047', 'MN001', 1, '', 'tersedia', 50000),
('DOD070', 'OD048', 'MN001', 1, '', 'tersedia', 50000),
('DOD071', 'OD049', 'MN002', 1, '', 'tersedia', 30000),
('DOD072', 'OD050', 'MN002', 1, '', 'tersedia', 30000),
('DOD073', 'OD050', 'MN006', 2, '', 'tersedia', 30000),
('DOD074', 'OD051', 'MN004', 1, '', 'tersedia', 5000),
('DOD075', 'OD051', 'MN001', 1, '', 'tersedia', 50000),
('DOD076', 'OD052', 'MN001', 1, '', 'tersedia', 50000),
('DOD077', 'OD053', 'MN001', 1, '', 'tersedia', 50000),
('DOD078', 'OD054', 'MN002', 1, '', 'tersedia', 30000),
('DOD079', 'OD054', 'MN004', 1, '', 'tersedia', 5000),
('DOD080', 'OD055', 'MN004', 1, '', 'tersedia', 5000),
('DOD081', 'OD055', 'MN005', 1, '', 'tersedia', 30000),
('DOD082', 'OD056', 'MN004', 1, '', 'tersedia', 5000),
('DOD083', 'OD057', 'MN005', 1, '', 'tersedia', 30000),
('DOD084', 'OD058', 'MN004', 2, '', 'tersedia', 10000),
('DOD085', 'OD059', 'MN001', 1, '', 'tersedia', 50000),
('DOD086', 'OD059', 'MN004', 1, '', 'tersedia', 5000),
('DOD087', 'OD060', 'MN001', 1, '', 'tersedia', 50000),
('DOD088', 'OD061', 'MN005', 1, '', 'tersedia', 30000),
('DOD089', 'OD062', 'MN001', 1, '', 'tersedia', 50000),
('DOD090', 'OD063', 'MN005', 1, '', 'tersedia', 30000),
('DOD091', 'OD063', 'MN003', 1, '', 'tersedia', 10000),
('DOD092', 'OD063', 'MN006', 1, '', 'tersedia', 15000),
('DOD093', 'OD064', 'MN001', 1, '', 'tersedia', 50000),
('DOD094', 'OD064', 'MN004', 1, '', 'tersedia', 5000),
('DOD095', 'OD065', 'MN004', 1, '', 'tersedia', 5000),
('DOD096', 'OD065', 'MN006', 1, '', 'tersedia', 15000),
('DOD097', 'OD066', 'MN001', 10, '', 'tersedia', 500000),
('DOD098', 'OD067', 'MN007', 1, '', 'tersedia', 48000),
('DOD099', 'OD067', 'MN004', 1, '', 'tersedia', 5000),
('DOD100', 'OD068', 'MN001', 1, '', 'tersedia', 50000),
('DOD101', 'OD069', 'MN007', 1, '', 'tersedia', 48000),
('DOD102', 'OD070', 'MN004', 2, '', 'tersedia', 10000),
('DOD103', 'OD071', 'MN008', 1, '', 'tersedia', 52000),
('DOD104', 'OD072', 'MN002', 1, '', 'tersedia', 30000),
('DOD105', 'OD072', 'MN019', 1, '', 'tersedia', 24000),
('DOD106', 'OD073', 'MN007', 1, '', 'tersedia', 48000),
('DOD107', 'OD074', 'MN007', 1, '', 'tersedia', 48000),
('DOD108', 'OD075', 'MN017', 1, '', 'tersedia', 38000),
('DOD109', 'OD076', 'MN017', 1, '', 'tersedia', 38000),
('DOD110', 'OD077', 'MN002', 1, '', 'tersedia', 30000);

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
('MN003', 'minuman', 'teh hijau panas (tawar)', 'Teh hijau Jepang autentik yang diseduh panas dengan aroma menenangkan dan rasa tawar yang bersih di lidah', 'hot ocha.jpg', 10000, 'tersedia', 'aktif'),
('MN004', 'minuman', 'air putih ', 'Air mineral kemasan berkualitas dalam suhu ruang atau dingin yang segar dan murni', 'air putih.jpg', 5000, 'tersedia', 'aktif'),
('MN005', 'makanan', 'Omurice fluffy', 'Selimut telur dadar yang lembut dan creamy membungkus nasi goreng saus tomat yang gurih. Biasanya disajikan dengan siraman saus tomat atau beef stew yang mewah.', 'omurice.jpg', 30000, 'tersedia', 'aktif'),
('MN006', 'minuman', 'Melon Soda Float', 'Soda melon hijau yang ceria dengan topping satu scoop es krim vanila lembut. Kombinasi rasa manis-segar yang membawa Anda bernostalgia ke masa kecil.', '1772348802_melon soda float.jpg', 15000, 'tersedia', 'aktif'),
('MN007', 'makanan', 'Chicken Katsu Curry', 'Nasi hangat dengan chicken katsu crispy disiram curry Jepang yang gurih dan sedikit manisss', 'Chicken Katsu Curry.jpg', 48000, 'tersedia', 'aktif'),
('MN008', 'makanan', 'Ebi Furai Rice', 'Udang goreng tepung renyah dengan saus tartar creamy dan nasi hangat', 'ebi furai rice.jpg', 52000, 'tersedia', 'aktif'),
('MN009', 'makanan', 'Yakiniku Rice Bowl', 'Irisan daging sapi tumis saus yakiniku di atas nasi Jepang pulen', 'yakiniku rice bowl.jpg', 50000, 'tersedia', 'aktif'),
('MN010', 'makanan', 'Salmon Mentai Rice', 'Salmon panggang dengan saus mentai creamy sedikit smoky', 'Salmon Mentai Rice.jpg', 63000, 'tersedia', 'aktif'),
('MN011', 'makanan', 'Tonkotsu Ramen', 'Ramen kuah creamy kaldu tulang dengan chashu dan telur ajitama', 'Tonkotsu Ramen.jpg', 58000, 'tersedia', 'aktif'),
('MN012', 'makanan', 'Spicy Miso Ramen', 'Kuah miso pedas gurih dengan topping ayam dan jagung manis', 'Spicy Miso Ramen.jpg', 55000, 'tersedia', 'aktif'),
('MN013', 'makanan', 'Udon Kake', 'Udon lembut dalam kuah dashi ringan yang comforting ', 'Udon Kake.jpg', 42000, 'tersedia', 'aktif'),
('MN014', 'makanan', 'Chicken Curry Udon ', 'Perpaduan kuah curry Jepang dan udon kenyal hangat', 'Chicken Curry Udon.jpg', 49000, 'tersedia', 'aktif'),
('MN015', 'makanan', 'Gyoza Dumpling ', 'Dumpling ayam panggang dengan kulit crispy bagian bawahnyaa', 'Gyoza.jpg', 25000, 'tersedia', 'aktif'),
('MN016', 'makanan', 'Matcha Parfait', 'Es krim matcha, cornflakes, dan red bean dalam satu gelas manis', 'Matcha Parfait.jpg', 35000, 'tersedia', 'nonaktif'),
('MN017', 'makanan', 'Japanese Pancake', 'Pancake fluffy lembut dengan butter dan maple syrup', 'Japanese Pancake.jpg', 38000, 'tersedia', 'nonaktif'),
('MN018', 'minuman', 'Matcha Latte', 'Minuman matcha creamy dengan aroma teh hijau yang calming\r\n*tambahkan catatan bila ingin dingin atau panas*', 'Matcha Latte.jpg', 26000, 'tersedia', 'aktif'),
('MN019', 'minuman', 'Hojicha Milk Tea', 'Teh hojicha roasted dengan susu lembut dan aroma smoky', 'Hojicha Milk Tea.jpg', 24000, 'tersedia', 'aktif'),
('MN020', 'minuman', 'Lychee Yakult', 'Perpaduan leci dan yakult yang menyegarkan', 'Lychee Yakult.jpg', 22000, 'tersedia', 'aktif'),
('MN021', 'minuman', 'Sakura Soda', 'Soda manis floral warna pink cantik ala musim semi Jepang', 'Sakura Soda.jpg', 25000, 'tersedia', 'aktif');

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
('OD018', 'M004', NULL, 'Maii', '', '2026-04-22', 'selesai', 'dine in'),
('OD019', NULL, 'USN008', 'nakula nalendra', '', '2026-04-23', 'selesai', 'take away'),
('OD020', NULL, 'USN004', 'bonnivier', '', '2026-04-23', 'selesai', 'take away'),
('OD021', NULL, 'USN004', 'bonnivier', '', '2026-04-23', 'selesai', 'take away'),
('OD022', 'M001', 'USN007', 'rion kenzo chana', '', '2026-04-23', 'selesai', 'dine in'),
('OD023', 'M001', 'USN007', 'rion kenzo chana', '', '2026-04-23', 'selesai', 'dine in'),
('OD024', 'M002', 'USN008', 'nakula nalendra', '', '2026-04-23', 'selesai', 'dine in'),
('OD025', 'M002', 'USN008', 'nakula nalendra', '', '2026-04-23', 'selesai', 'dine in'),
('OD026', 'M002', NULL, 'arjuna', '', '2026-04-23', 'selesai', 'dine in'),
('OD027', NULL, NULL, 'arutala', '', '2026-04-23', 'selesai', 'take away'),
('OD028', 'M006', 'USN004', 'bonnivier', 'kalau bisa dipercepat', '2026-04-27', 'selesai', 'dine in'),
('OD029', 'M006', 'USN004', 'bonnivier', '', '2026-04-27', 'selesai', 'dine in'),
('OD030', 'M005', 'USN004', 'bonnivier', '', '2026-04-27', 'selesai', 'dine in'),
('OD031', 'M003', NULL, 'key', 'yang banyak', '2026-04-27', 'selesai', 'dine in'),
('OD032', NULL, NULL, 'krow', 'yang banyak porsinya', '2026-04-27', 'selesai', 'take away'),
('OD033', NULL, NULL, 'rean', '', '2026-04-27', 'selesai', 'take away'),
('OD034', 'M004', NULL, 'ramona', '', '2026-04-27', 'selesai', 'dine in'),
('OD035', 'M006', NULL, 'nakula', '', '2026-04-27', 'selesai', 'dine in'),
('OD036', 'M005', NULL, 'arutala', 'dipercepat masaknya', '2026-04-28', 'selesai', 'dine in'),
('OD037', 'M005', NULL, 'garin', 'ayamnya dipotong dua', '2026-04-28', 'selesai', 'dine in'),
('OD038', 'M005', NULL, 'myciz', '', '2026-04-28', 'selesai', 'dine in'),
('OD039', NULL, NULL, 'jojo', '', '2026-04-28', 'selesai', 'take away'),
('OD040', 'M006', NULL, 'niskala naia', 'dibanyakin porsinya', '2026-04-28', 'selesai', 'dine in'),
('OD041', 'M004', NULL, 'aya aulia', 'air putih nya banyakin\r\n', '2026-04-28', 'selesai', 'dine in'),
('OD042', 'M007', NULL, 'juned', '', '2026-04-29', 'selesai', 'dine in'),
('OD043', NULL, NULL, 'nalendra', '', '2026-04-29', 'selesai', 'take away'),
('OD044', NULL, NULL, 'salma', '', '2026-04-29', 'selesai', 'take away'),
('OD045', NULL, 'USN011', 'Harris Caine', '', '2026-04-29', 'selesai', 'dine in'),
('OD046', 'M006', NULL, 'yuyo', 'saus nya yang banyak', '2026-04-29', 'selesai', 'dine in'),
('OD047', 'M006', NULL, 'reizo', 'jangan pedas', '2026-04-29', 'selesai', 'dine in'),
('OD048', NULL, NULL, 'ansel yoruga', 'sausnya dipisah', '2026-04-29', 'selesai', 'take away'),
('OD049', 'M006', NULL, 'cervy arugea', '', '2026-04-29', 'selesai', 'dine in'),
('OD050', NULL, NULL, 'yuze sou', 'dipercepat', '2026-04-29', 'selesai', 'take away'),
('OD051', NULL, NULL, 'asmilia', '', '2026-04-29', 'selesai', 'take away'),
('OD052', NULL, NULL, 'amameng', '', '2026-04-29', 'selesai', 'take away'),
('OD053', NULL, NULL, 'ramona', '', '2026-04-29', 'selesai', 'take away'),
('OD054', 'M001', NULL, 'Rahma', '', '2026-04-30', 'selesai', 'dine in'),
('OD055', 'M004', NULL, 'joni', 'es nya sedikit', '2026-05-01', 'selesai', 'dine in'),
('OD056', 'M004', NULL, 'junedino', 'airnya yang banyak', '2026-05-02', 'selesai', 'dine in'),
('OD057', NULL, NULL, 'rose', '', '2026-05-02', 'selesai', 'take away'),
('OD058', NULL, NULL, 'iwan', '', '2026-05-02', 'selesai', 'take away'),
('OD059', NULL, NULL, 'oriesa', '', '2026-05-02', 'selesai', 'dine in'),
('OD060', NULL, NULL, 'krow', 'yang banyak', '2026-05-02', 'selesai', 'dine in'),
('OD061', NULL, NULL, 'selia', '', '2026-05-02', 'selesai', 'dine in'),
('OD062', NULL, NULL, 'yuyo', '', '2026-05-02', 'selesai', 'dine in'),
('OD063', NULL, NULL, 'luna', '', '2026-05-02', 'selesai', 'dine in'),
('OD064', 'M007', NULL, 'jennie blackpink', 'pake sayur', '2026-05-02', 'selesai', 'dine in'),
('OD065', NULL, NULL, 'tsuda kenjiro', 'pake es banyak', '2026-05-02', 'selesai', 'take away'),
('OD066', NULL, NULL, 'jianjia', 'semangat yang masaknya', '2026-05-03', 'selesai', 'take away'),
('OD067', NULL, NULL, 'iwak', '', '2026-05-03', 'diproses', 'take away'),
('OD068', NULL, NULL, 'bambang', '', '2026-05-03', 'selesai', 'take away'),
('OD069', NULL, NULL, 'kucing liar', '', '2026-05-03', 'diproses', 'take away'),
('OD070', NULL, NULL, 'yuyo', '', '2026-05-03', 'selesai', 'take away'),
('OD071', NULL, NULL, 'ain', '', '2026-05-03', 'selesai', 'take away'),
('OD072', 'M007', NULL, 'rahma', 'ayam gorengnya tidak pedas', '2026-05-03', 'selesai', 'dine in'),
('OD073', NULL, NULL, 'mafuyu', '', '2026-05-03', 'diproses', 'take away'),
('OD074', 'M004', NULL, 'laufey', '', '2026-05-03', 'diproses', 'dine in'),
('OD075', 'M005', NULL, 'chappel roan', '', '2026-05-03', 'diproses', 'dine in'),
('OD076', 'M004', NULL, 'ila', '', '2026-05-03', 'diproses', 'dine in'),
('OD077', 'M003', NULL, 'ular', '', '2026-05-03', 'diproses', 'dine in');

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
  `id_kasir_rahma` varchar(10) NOT NULL,
  `diskon_rahma` int(11) NOT NULL,
  `pajak_rahma` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_rahma` int(11) NOT NULL,
  `bayar_rahma` int(11) NOT NULL,
  `kembalian_rahma` int(11) NOT NULL,
  `waktu_transaksi_rahma` datetime NOT NULL,
  `metode_bayar_rahma` varchar(50) DEFAULT NULL COMMENT 'cash, qris, debit, gopay, bca_va, dll',
  `midtrans_order_id_rahma` varchar(100) DEFAULT NULL COMMENT 'Order ID yang dikirim ke Midtrans',
  `midtrans_transaction_id_rahma` varchar(100) DEFAULT NULL COMMENT 'Transaction ID dari Midtrans',
  `status_midtrans_rahma` varchar(20) DEFAULT NULL COMMENT 'success, pending, failure'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_transaksi_rahma`
--

INSERT INTO `tbl_transaksi_rahma` (`id_transaksi_rahma`, `id_order_rahma`, `id_kasir_rahma`, `diskon_rahma`, `pajak_rahma`, `total_rahma`, `bayar_rahma`, `kembalian_rahma`, `waktu_transaksi_rahma`, `metode_bayar_rahma`, `midtrans_order_id_rahma`, `midtrans_transaction_id_rahma`, `status_midtrans_rahma`) VALUES
('T001', 'OD001', '', 0, 0.00, 100000, 100000, 0, '2026-02-12 00:00:00', 'cash', NULL, NULL, NULL),
('T002', 'OD002', '', 0, 0.00, 30000, 50000, 20000, '2026-02-12 00:00:00', 'cash', NULL, NULL, NULL),
('T003', 'OD003', '', 0, 0.00, 15000, 20000, 5000, '2026-04-09 00:00:00', 'cash', NULL, NULL, NULL),
('T004', 'OD004', '', 0, 0.00, 165000, 170000, 5000, '2026-04-11 00:00:00', 'cash', NULL, NULL, NULL),
('T005', 'OD005', '', 0, 0.00, 35000, 35000, 0, '2026-04-11 00:00:00', 'cash', NULL, NULL, NULL),
('T006', 'OD006', '', 0, 0.00, 50000, 50000, 0, '2026-04-11 00:00:00', 'cash', NULL, NULL, NULL),
('T007', 'OD007', '', 0, 0.00, 45000, 45000, 0, '2026-04-11 00:00:00', 'cash', NULL, NULL, NULL),
('T008', 'OD010', '', 0, 0.00, 5000, 10000, 5000, '2026-04-21 00:00:00', 'cash', NULL, NULL, NULL),
('T009', 'OD008', '', 0, 0.00, 50000, 50000, 0, '2026-04-21 00:00:00', 'cash', NULL, NULL, NULL),
('T010', 'OD009', '', 0, 0.00, 30000, 30000, 0, '2026-04-21 00:00:00', 'cash', NULL, NULL, NULL),
('T011', 'OD011', '', 0, 0.00, 30000, 30000, 0, '2026-04-21 00:00:00', 'cash', NULL, NULL, NULL),
('T012', 'OD012', '', 0, 0.00, 50000, 50000, 0, '2026-04-21 00:00:00', 'cash', NULL, NULL, NULL),
('T013', 'OD014', '', 0, 0.00, 50000, 60000, 10000, '2026-04-22 00:00:00', 'cash', NULL, NULL, NULL),
('T014', 'OD013', '', 0, 0.00, 5000, 5000, 0, '2026-04-22 00:00:00', 'cash', NULL, NULL, NULL),
('T015', 'OD016', '', 0, 0.00, 90000, 90000, 0, '2026-04-22 00:00:00', 'cash', NULL, NULL, NULL),
('T016', 'OD017', '', 0, 0.00, 540000, 540000, 0, '2026-04-22 00:00:00', 'cash', NULL, NULL, NULL),
('T017', 'OD015', '', 0, 0.00, 50000, 100000, 50000, '2026-04-22 00:00:00', 'cash', NULL, NULL, NULL),
('T018', 'OD018', '', 0, 0.00, 380000, 500000, 120000, '2026-04-22 00:00:00', 'cash', NULL, NULL, NULL),
('T019', 'OD019', '', 10, 0.00, 72000, 80000, 8000, '2026-04-23 00:00:00', 'cash', NULL, NULL, NULL),
('T020', 'OD020', '', 10, 0.00, 63000, 63000, 0, '2026-04-23 00:00:00', 'cash', NULL, NULL, NULL),
('T021', 'OD021', '', 10, 0.00, 9000, 10000, 1000, '2026-04-23 00:00:00', 'cash', NULL, NULL, NULL),
('T022', 'OD022', '', 10, 0.00, 45000, 50000, 5000, '2026-04-23 00:00:00', 'cash', NULL, NULL, NULL),
('T023', 'OD023', '', 10, 0.00, 9000, 9000, 0, '2026-04-23 00:00:00', 'cash', NULL, NULL, NULL),
('T024', 'OD024', '', 10, 0.00, 31500, 32000, 500, '2026-04-23 00:00:00', 'cash', NULL, NULL, NULL),
('T025', 'OD025', '', 10, 0.00, 4500, 5000, 500, '2026-04-23 00:00:00', 'cash', NULL, NULL, NULL),
('T026', 'OD026', '', 0, 0.00, 5000, 5000, 0, '2026-04-23 00:00:00', 'cash', NULL, NULL, NULL),
('T027', 'OD031', '', 0, 0.00, 50000, 50000, 0, '2026-04-27 00:00:00', 'cash', NULL, NULL, NULL),
('T028', 'OD027', '', 0, 0.00, 50000, 50000, 0, '2026-04-27 00:00:00', 'cash', NULL, NULL, NULL),
('T029', 'OD030', '', 10, 0.00, 4500, 4500, 0, '2026-04-27 00:00:00', 'cash', NULL, NULL, NULL),
('T030', 'OD028', '', 10, 0.00, 49500, 49500, 0, '2026-04-27 00:00:00', 'cash', NULL, NULL, NULL),
('T031', 'OD029', '', 10, 0.00, 4500, 4500, 0, '2026-04-27 00:00:00', 'cash', NULL, NULL, NULL),
('T032', 'OD036', '', 0, 0.00, 55000, 55000, 0, '2026-04-28 00:00:00', 'cash', NULL, NULL, NULL),
('T033', 'OD032', '', 0, 0.00, 35000, 40000, 5000, '2026-04-28 00:00:00', 'cash', NULL, NULL, NULL),
('T034', 'OD034', '', 0, 0.00, 15000, 15000, 0, '2026-04-28 00:00:00', 'cash', NULL, NULL, NULL),
('T035', 'OD035', '', 0, 0.00, 50000, 50000, 0, '2026-04-28 00:00:00', 'cash', NULL, NULL, NULL),
('T036', 'OD037', '', 0, 0.00, 30000, 30000, 0, '2026-04-28 00:00:00', 'cash', NULL, NULL, NULL),
('T037', 'OD033', '', 0, 0.00, 35000, 35000, 0, '2026-04-28 00:00:00', 'cash', NULL, NULL, NULL),
('T038', 'OD038', '', 0, 0.00, 70000, 100000, 30000, '2026-04-28 00:00:00', 'cash', NULL, NULL, NULL),
('T039', 'OD040', '', 0, 0.00, 45000, 45000, 0, '2026-04-29 00:00:00', 'cash', NULL, NULL, NULL),
('T040', 'OD041', '', 0, 0.00, 35000, 35000, 0, '2026-04-29 00:00:00', 'cash', NULL, NULL, NULL),
('T041', 'OD042', '', 0, 0.00, 45000, 45000, 0, '2026-04-29 06:09:19', 'cash', NULL, NULL, NULL),
('T042', 'OD043', '', 0, 0.00, 30000, 30000, 0, '2026-04-29 09:04:36', 'cash', NULL, NULL, NULL),
('T043', 'OD039', '', 0, 0.00, 30000, 30000, 0, '2026-04-29 09:49:55', 'cash', NULL, NULL, NULL),
('T044', 'OD045', '', 10, 6050.00, 55550, 60000, 4450, '2026-04-29 21:06:11', 'cash', NULL, NULL, NULL),
('T045', 'OD052', 'USN002', 0, 5500.00, 55500, 60000, 4500, '2026-04-29 22:45:56', 'cash', NULL, NULL, NULL),
('T046', 'OD044', 'USN002', 0, 3300.00, 33300, 33500, 200, '2026-04-29 22:57:50', 'cash', NULL, NULL, NULL),
('T047', 'OD054', 'USN002', 0, 3850.00, 38850, 40000, 1150, '2026-04-30 04:23:46', 'cash', NULL, NULL, NULL),
('T048', 'OD053', 'USN002', 0, 5500.00, 55500, 55500, 0, '2026-04-30 04:29:29', 'cash', NULL, NULL, NULL),
('T049', 'OD051', 'USN002', 0, 6050.00, 61050, 65000, 3950, '2026-04-30 08:08:57', 'cash', NULL, NULL, NULL),
('T050', 'OD046', 'USN002', 0, 3300.00, 33300, 35000, 1700, '2026-04-30 08:10:31', 'cash', NULL, NULL, NULL),
('T051', 'OD048', 'USN002', 0, 5500.00, 55500, 55500, 0, '2026-04-30 08:21:11', 'cash', NULL, NULL, NULL),
('T052', 'OD049', 'USN002', 0, 3300.00, 33300, 33500, 200, '2026-04-30 09:05:54', 'cash', NULL, NULL, NULL),
('T053', 'OD047', 'USN002', 0, 5500.00, 55500, 55500, 0, '2026-04-30 09:08:20', 'cash', NULL, NULL, NULL),
('T054', 'OD055', 'USN002', 0, 3850.00, 38850, 40000, 1150, '2026-05-01 06:44:10', 'cash', NULL, NULL, NULL),
('T055', 'OD058', 'SYSTEM', 0, 1100.00, 11100, 11100, 0, '2026-05-02 14:49:41', 'qris', 'OD058-1777726152', '98da772b-192f-430b-bdaa-91871787cfff', 'lunas'),
('T056', 'OD050', 'USN012', 0, 6600.00, 66600, 66600, 0, '2026-05-02 17:58:19', 'qris', NULL, NULL, NULL),
('T057', 'OD057', 'USN012', 0, 3300.00, 33300, 33300, 0, '2026-05-02 17:58:43', 'debit', NULL, NULL, NULL),
('T058', 'OD056', 'USN012', 0, 550.00, 5550, 5550, 0, '2026-05-02 18:32:37', 'debit', NULL, NULL, NULL),
('T059', 'OD059', 'SYSTEM', 0, 6050.00, 61050, 61050, 0, '2026-05-02 18:35:44', 'qris', 'OD059-1777739730', '93877636-d9d7-4ce8-acee-3d863672b9ad', 'lunas'),
('T060', 'OD060', 'SYSTEM', 0, 5500.00, 55500, 55500, 0, '2026-05-02 18:45:00', 'qris', 'OD060-1777740287', 'da0d3d78-0fcd-426b-80b1-87319888ce23', 'lunas'),
('T061', 'OD061', 'SYSTEM', 0, 3300.00, 33300, 33300, 0, '2026-05-02 19:10:49', 'bank_transfer', 'OD061-1777741836', '09d0188c-5a65-47a1-9093-966b36afeeab', 'lunas'),
('T062', 'OD062', 'SYSTEM', 0, 5500.00, 55500, 55500, 0, '2026-05-02 19:12:35', 'credit_card', 'OD062-1777741874', '168ad417-14d7-4581-94c3-6dedef3e5ad3', 'lunas'),
('T063', 'OD063', 'SYSTEM', 0, 6050.00, 61050, 61050, 0, '2026-05-02 19:21:54', 'qris', 'OD063-1777742461', '9feecd62-c7bd-482c-8499-1fd2ed48a8a8', 'lunas'),
('T064', 'OD064', 'SYSTEM', 0, 6050.00, 61050, 61050, 0, '2026-05-02 20:18:26', 'bank_transfer', 'OD064-1777745879', 'acd3119f-fc1a-4308-a49c-82d137755596', 'lunas'),
('T065', 'OD065', 'USN012', 0, 2200.00, 22200, 22200, 0, '2026-05-02 20:20:28', 'debit', NULL, NULL, NULL),
('T066', 'OD066', 'SYSTEM', 0, 55000.00, 555000, 555000, 0, '2026-05-03 08:10:37', 'qris', 'OD066-1777788610', 'be3ca5dc-acb9-4e32-ab4b-43e8d557a5f9', 'lunas'),
('T067', 'OD067', 'SYSTEM', 0, 5830.00, 58830, 58830, 0, '2026-05-03 10:57:02', 'qris', 'OD067-1777798576', '23801f2c-0ae6-4c71-accd-bb6235a9abcb', 'lunas'),
('T068', 'OD068', 'SYSTEM', 0, 5500.00, 55500, 55500, 0, '2026-05-03 10:59:12', 'qris', 'OD068-1777798650', '2452f6d3-15eb-4bd6-b6dd-6abc6142e0c9', 'lunas'),
('T069', 'OD070', 'USN002', 0, 1100.00, 11100, 11100, 0, '2026-05-03 12:28:52', 'qris', NULL, NULL, NULL),
('T070', 'OD071', 'SYSTEM', 0, 5720.00, 57720, 57720, 0, '2026-05-03 12:29:50', 'qris', 'OD071-1777804153', '2428e5c9-7899-4a5b-bb3a-41fe3e610ca6', 'lunas'),
('T071', 'OD072', 'SYSTEM', 0, 5940.00, 59940, 59940, 0, '2026-05-03 16:33:38', 'qris', 'OD072-1777818775', '0dff4dfc-f0c8-4026-b1cd-6c3eb450828b', 'lunas'),
('T072', 'OD073', 'USN012', 0, 5280.00, 53280, 53280, 0, '2026-05-03 16:42:56', 'cash', NULL, NULL, NULL),
('T073', 'OD069', 'USN002', 0, 5280.00, 53280, 53280, 0, '2026-05-03 17:12:07', 'cash', NULL, NULL, NULL),
('T074', 'OD075', 'USN012', 0, 4180.00, 42180, 42180, 0, '2026-05-03 17:25:43', 'cash', NULL, NULL, NULL),
('T075', 'OD074', 'USN012', 0, 5280.00, 53280, 53280, 0, '2026-05-03 17:34:40', 'cash', NULL, NULL, NULL),
('T076', 'OD076', 'USN002', 0, 4180.00, 42180, 42180, 0, '2026-05-03 17:57:29', '', NULL, NULL, NULL),
('T077', 'OD077', 'USN002', 0, 3300.00, 33300, 40000, 6700, '2026-05-03 18:19:33', 'cash', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_rahma`
--

CREATE TABLE `tbl_user_rahma` (
  `id_user_rahma` varchar(10) NOT NULL,
  `username_rahma` varchar(50) NOT NULL,
  `password_rahma` varchar(255) NOT NULL,
  `nama_rahma` varchar(50) NOT NULL,
  `no_telp_rahma` varchar(15) NOT NULL,
  `id_role_rahma` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user_rahma`
--

INSERT INTO `tbl_user_rahma` (`id_user_rahma`, `username_rahma`, `password_rahma`, `nama_rahma`, `no_telp_rahma`, `id_role_rahma`) VALUES
('USN001', 'taka', '$2y$10$QWltnlKwN0LKtAZKHRJSCeTZjESGp4mPex5Gng4VGJKz9aHBgrtOa', 'Taka Radjiman', '089502250099', 'R001'),
('USN002', 'zea', '$2y$10$aGJ5R/RgNyY1MlEwiJ72q.lucxQJfDIXUH20SGPhFkAubb8h/H04S', 'Zea Cornelia', '089605537645', 'R002'),
('USN004', 'bon', '$2y$10$ZqoanGwr56MorPwKassJFeS8VY.jHAiyZiUVL7Vctkn2R6gPq1hbq', 'bonnivier', '089534569879', 'R003'),
('USN006', 'sadewa ', '$2y$10$ktrjABUJjlyFfha/cAJOEulLEWnqvwkSsPDt66u0hkbpUOaRTgMRG', 'sadewa sagara', '081234567877', 'R004'),
('USN007', 'rion', '$2y$10$m9fYTe5ePAdpFCRk6nwmKuz01xHg2co1Pe4/iu71fBI7L.bIpWRaC', 'rion kenzo chana', '086450096644', 'R003'),
('USN008', 'nakul', '$2y$10$7r4J0/uB8Juc0.gIjM6Ese45RNjTSi3WNeiIIk/wn9i9FDGFJm6dG', 'nakula nalendra', '086589760034', 'R003'),
('USN011', 'ayis', '$2y$10$Dgfe1MuTgmi.TiaVrjqFMuSBqgOs1Nv7lho7YDu.cvy3HkxVKjD46', 'Harris Caine', '097865543286', 'R003'),
('USN012', 'reza', '$2y$10$qrzA9h06ZBQDSx3Rc9SRFOC3qEtT.0EBoYxi8a8Dv1opkZctHSxsK', 'reza avanluna', '086435768976', 'R002');

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
  ADD CONSTRAINT `tbl_order_rahma_ibfk_1` FOREIGN KEY (`id_user_rahma`) REFERENCES `tbl_user_rahma` (`id_user_rahma`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_order_rahma_ibfk_2` FOREIGN KEY (`id_meja_rahma`) REFERENCES `tbl_meja_rahma` (`id_meja_rahma`) ON DELETE SET NULL;

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
