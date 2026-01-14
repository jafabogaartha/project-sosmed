-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 13, 2026 at 02:29 AM
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
-- Database: `db_project_sosmed`
--

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `brand_name` varchar(100) NOT NULL,
  `brand_code` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `brand_name`, `brand_code`, `created_at`) VALUES
(1, 'Raja Steak', 'RS', '2026-01-12 10:28:35'),
(2, 'Kopi Ibukota', 'KI', '2026-01-12 10:28:35'),
(3, 'Mybestea', 'MB', '2026-01-12 10:28:35'),
(4, 'Seblak Express', 'SE', '2026-01-12 10:28:35'),
(5, 'Mentoast', 'MT', '2026-01-12 10:28:35'),
(6, 'You need mie', 'YM', '2026-01-12 10:28:35'),
(7, 'Esteh Ibukota', 'EI', '2026-01-12 10:28:35'),
(8, 'Kentang Gantenk', 'KG', '2026-01-12 10:28:35'),
(9, 'BeNice Coffee', 'BC', '2026-01-12 10:28:35'),
(10, 'Chick Ichik', 'CI', '2026-01-12 10:28:35'),
(11, 'Merlumer', 'ML', '2026-01-12 10:28:35'),
(12, 'Chikuruyuk', 'CY', '2026-01-12 10:28:35'),
(13, 'Tahu Nyonyor', 'TN', '2026-01-12 10:28:35'),
(14, 'K-Mie', 'KM', '2026-01-12 10:28:35'),
(15, 'Chocopedia', 'CP', '2026-01-12 10:28:35'),
(16, 'Republik Coklat', 'RC', '2026-01-12 10:28:35'),
(17, 'Matchachi', 'MC', '2026-01-12 10:28:35');

-- --------------------------------------------------------

--
-- Table structure for table `content_tasks`
--

CREATE TABLE `content_tasks` (
  `id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `target_tiktok` tinyint(1) DEFAULT 1,
  `target_instagram` tinyint(1) DEFAULT 1,
  `script_tiktok` text DEFAULT NULL,
  `caption_instagram` text DEFAULT NULL,
  `production_mode` enum('ready_stock','need_take') DEFAULT 'ready_stock',
  `status` enum('waiting_footage','ready_to_edit','ready_to_post','published') DEFAULT 'ready_to_edit',
  `link_raw_source` varchar(500) DEFAULT NULL,
  `file_final_video` varchar(255) DEFAULT NULL,
  `url_published_post` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `team_assignments`
--

CREATE TABLE `team_assignments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `team_assignments`
--

INSERT INTO `team_assignments` (`id`, `user_id`, `brand_id`, `assigned_at`) VALUES
(1, 2, 1, '2026-01-12 10:28:35'),
(2, 2, 2, '2026-01-12 10:28:35'),
(3, 3, 4, '2026-01-12 10:28:35'),
(4, 4, 1, '2026-01-12 10:28:35');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','specialist','editor') NOT NULL,
  `avatar_color` varchar(50) DEFAULT 'bg-indigo-400',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password_hash`, `role`, `avatar_color`, `created_at`) VALUES
(1, 'Super Admin', 'admin@kantor.com', '123', 'admin', 'bg-gray-800', '2026-01-12 10:28:35'),
(2, 'Andi Sosmed', 'andi@kantor.com', '123', 'specialist', 'bg-blue-500', '2026-01-12 10:28:35'),
(3, 'Bunga Sosmed', 'bunga@kantor.com', '123', 'specialist', 'bg-purple-500', '2026-01-12 10:28:35'),
(4, 'Citra Editor', 'citra@kantor.com', '123', 'editor', 'bg-green-500', '2026-01-12 10:28:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `brand_code` (`brand_code`);

--
-- Indexes for table `content_tasks`
--
ALTER TABLE `content_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `brand_id` (`brand_id`),
  ADD KEY `creator_id` (`creator_id`);

--
-- Indexes for table `team_assignments`
--
ALTER TABLE `team_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_assignment` (`user_id`,`brand_id`),
  ADD KEY `brand_id` (`brand_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `content_tasks`
--
ALTER TABLE `content_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `team_assignments`
--
ALTER TABLE `team_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `content_tasks`
--
ALTER TABLE `content_tasks`
  ADD CONSTRAINT `content_tasks_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `content_tasks_ibfk_2` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `team_assignments`
--
ALTER TABLE `team_assignments`
  ADD CONSTRAINT `team_assignments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `team_assignments_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
