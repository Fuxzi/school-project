-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 29, 2025 at 07:59 AM
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
-- Database: `icikiwir_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(80) NOT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `created_at`) VALUES
(1, 'Laptop', 'laptop', '2025-12-27 21:03:12'),
(2, 'Headset', 'headset', '2025-12-27 21:03:12'),
(3, 'Keyboard', 'keyboard', '2025-12-27 21:03:12'),
(4, 'Mouse', 'mouse', '2025-12-27 21:28:34'),
(5, 'Monitor', 'monitor', '2025-12-27 21:28:34'),
(6, 'GPU / VGA', 'gpu', '2025-12-27 21:28:34'),
(7, 'Lainnya', 'lainnya', '2025-12-27 21:28:34');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `review_id`, `user_id`, `comment_text`, `created_at`) VALUES
(1, 5, 1, 'test', '2025-12-29 06:40:17'),
(2, 6, 1, 'test', '2025-12-29 06:40:30'),
(3, 7, 1, 'test', '2025-12-29 06:40:35'),
(4, 8, 1, 'test', '2025-12-29 06:40:40'),
(5, 9, 1, 'kureng', '2025-12-29 06:43:21'),
(6, 10, 1, 'bagus', '2025-12-29 06:43:26');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `type` varchar(80) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `title`, `type`, `description`, `image_path`, `created_at`, `updated_at`) VALUES
(1, 1, 'Lenovo ThinkPad X1', 'Laptop', 'Laptop bisnis ringan, keyboard nyaman.', 'assets/img/lenovo_thinkpad_x1.png', '2025-12-27 21:15:31', '2025-12-27 22:09:39'),
(2, 2, 'Sony WH-1000XM5', 'Headset', 'Noise cancelling mantap, nyaman dipakai lama.', 'assets/img/sony_wh-1000xm5.png', '2025-12-27 21:15:31', '2025-12-27 22:09:39'),
(3, 1, 'Laptop Acer', 'Laptop', 'Laptop Acer untuk kerja dan kuliah, performa stabil.', 'assets/img/laptop_acer.png', '2025-12-27 21:29:16', NULL),
(4, 1, 'Laptop MSI', 'Laptop', 'Laptop MSI untuk gaming dan produktivitas, build solid.', 'assets/img/laptop_msi.png', '2025-12-27 21:29:16', NULL),
(5, 1, 'Laptop ROG', 'Laptop', 'ASUS ROG untuk gaming, refresh rate tinggi dan kencang.', 'assets/img/laptop_rog.png', '2025-12-27 21:29:16', NULL),
(6, 2, 'Razer Kraken Headset', 'Headset', 'Headset gaming Razer Kraken, mic jelas dan nyaman.', 'assets/img/headset_kraken.png', '2025-12-27 21:29:16', NULL),
(7, 2, 'Razer Kraken (Variant)', 'Headset', 'Varian lain dari Kraken, fokus bass dan detail.', 'assets/img/headset_kraken2.png', '2025-12-27 21:29:16', NULL),
(8, 2, 'HyperX Cloud (Image hyperx_10)', 'Headset', 'Headset HyperX Cloud, nyaman untuk pemakaian lama.', 'assets/img/hyperx_10.png', '2025-12-27 21:29:16', NULL),
(9, 2, 'HyperX Headset (Variant)', 'Headset', 'Varian HyperX headset, soundstage luas untuk game FPS.', 'assets/img/hyperxheadset.png', '2025-12-27 21:29:16', '2025-12-27 22:14:06'),
(10, 3, 'Keyboard Astroboy', 'Keyboard', 'Keyboard tema Astroboy, layout compact.', 'assets/img/kb_astroboy.png', '2025-12-27 21:29:16', NULL),
(11, 3, 'Keyboard Hutao', 'Keyboard', 'Keyboard tema Hutao, cocok untuk setup aesthetic.', 'assets/img/kb_hutao.png', '2025-12-27 21:29:16', NULL),
(12, 3, 'Keyboard Kazuha', 'Keyboard', 'Keyboard tema Kazuha, keycap unik dan clean.', 'assets/img/kb_kazuha.png', '2025-12-27 21:29:17', NULL),
(13, 3, 'Keyboard KUNAI', 'Keyboard', 'Keyboard KUNAI, nuansa warna pastel dan modern.', 'assets/img/KUNAI.png', '2025-12-27 21:29:17', NULL),
(14, 4, 'Logitech G102', 'Mouse', 'Mouse Logitech G102, ringan dan responsif.', 'assets/img/logitech_g102.png', '2025-12-27 21:29:17', NULL),
(15, 4, 'Logitech G502 X', 'Mouse', 'Mouse Logitech G502X, ergonomis dan banyak tombol.', 'assets/img/logitech_g502x.png', '2025-12-27 21:29:17', NULL),
(16, 4, 'Razer DeathAdder', 'Mouse', 'Mouse Razer DeathAdder, grip nyaman dan sensor akurat.', 'assets/img/mouse_deathadder.png', '2025-12-27 21:29:17', NULL),
(17, 4, 'Mouse Paimon', 'Mouse', 'Mouse tema Paimon, cocok buat setup unik.', 'assets/img/mouse_paimon.png', '2025-12-27 21:29:17', NULL),
(18, 5, 'Monitor Mase', 'Monitor', 'Monitor gaming, warna kontras dan desain modern.', 'assets/img/monitor_mase.png', '2025-12-27 21:29:17', NULL),
(19, 5, 'Monitor Sakura', 'Monitor', 'Monitor tema Sakura, cocok untuk aesthetic setup.', 'assets/img/monitor_sakura.png', '2025-12-27 21:29:17', NULL),
(20, 5, 'Monitor Samsul', 'Monitor', 'Monitor untuk kerja dan entertainment, panel nyaman.', 'assets/img/monitor_samsul.png', '2025-12-27 21:29:18', NULL),
(21, 6, 'NVIDIA GTX 1660', 'GPU', 'VGA GTX 1660, masih kuat untuk 1080p.', 'assets/img/gtx_1660.png', '2025-12-27 21:29:18', NULL),
(22, 6, 'NVIDIA RTX 3080', 'GPU', 'VGA RTX 3080, performa tinggi untuk AAA gaming.', 'assets/img/rtx_3080.png', '2025-12-27 21:29:18', NULL),
(23, 6, 'NVIDIA RTX 3090', 'GPU', 'VGA RTX 3090, VRAM besar untuk kerja berat.', 'assets/img/rtx_3090.png', '2025-12-27 21:29:18', NULL),
(24, 6, 'AMD Radeon RX 6900', 'GPU', 'VGA Radeon RX 6900, kompetitif untuk gaming high-end.', 'assets/img/radeon_rx6900.png', '2025-12-27 21:29:18', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `rating`, `review_text`, `created_at`) VALUES
(1, 20, 1, 5, '', '2025-12-28 01:11:50'),
(2, 20, 1, 3, '', '2025-12-28 01:20:35'),
(5, 20, 1, 5, NULL, '2025-12-29 06:40:17'),
(6, 20, 1, 4, NULL, '2025-12-29 06:40:29'),
(7, 20, 1, 3, NULL, '2025-12-29 06:40:35'),
(8, 20, 1, 3, NULL, '2025-12-29 06:40:39'),
(9, 21, 1, 1, NULL, '2025-12-29 06:43:21'),
(10, 21, 1, 5, NULL, '2025-12-29 06:43:26');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `full_name`, `email`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$QmQ1m3m1e6n0k9Gv2c6eTe8aVv3yQzVfJtqz5f2qgX1ZpVvTnq9uS', 'Administrator', NULL, 'admin', '2025-12-27 21:03:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_comments_user` (`user_id`),
  ADD KEY `idx_comments_review_id` (`review_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_products_category_id` (`category_id`),
  ADD KEY `idx_products_title` (`title`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reviews_product_id` (`product_id`),
  ADD KEY `idx_reviews_user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `fk_comments_review` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
