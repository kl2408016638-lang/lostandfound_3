-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 20, 2026 at 02:59 PM
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
-- Database: `lostandfound`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `role` enum('user','admin') NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `contactnum` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `role`, `name`, `contactnum`, `email`, `password`) VALUES
(44, 'admin', 'halimah ', '', 'halimah@gmail.com', '1234'),
(51, 'user', 'nadrah', '0164210650', '', 'nn123'),
(52, 'user', 'qayyum dinni', '011111111111', '', '1234'),
(53, 'admin', 'aina ', '', 'aina@gmail.com', '1234'),
(55, 'user', 'ismiyati', '0163617942', '', '1234'),
(57, 'admin', 'shafiee', '', 'shafiee@gmail.com', '1234');

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `admin_name` varchar(100) NOT NULL,
  `action` varchar(50) NOT NULL,
  `target_type` varchar(30) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `target_name` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_logs`
--

INSERT INTO `admin_logs` (`id`, `admin_id`, `admin_name`, `action`, `target_type`, `target_id`, `target_name`, `description`, `ip_address`, `created_at`) VALUES
(1, 44, 'halimah ', 'login', NULL, NULL, NULL, 'Admin logged into system', '::1', '2026-01-20 11:01:54'),
(2, 44, 'halimah ', 'login', NULL, NULL, NULL, 'Admin logged into system', '::1', '2026-01-20 11:06:07'),
(3, 44, 'halimah ', 'logout', NULL, NULL, NULL, 'Admin logged out of system', '::1', '2026-01-20 11:06:25'),
(4, 44, 'halimah ', 'login', NULL, NULL, NULL, 'Admin logged into system', '::1', '2026-01-20 11:06:40'),
(5, 44, 'halimah ', 'logout', NULL, NULL, NULL, 'Admin logged out of system', '::1', '2026-01-20 11:06:57'),
(6, 53, 'aina', 'login', NULL, NULL, NULL, 'Admin logged into system', '::1', '2026-01-20 11:11:55'),
(7, 53, 'aina', 'logout', NULL, NULL, NULL, 'Admin logged out of system', '::1', '2026-01-20 11:12:09'),
(8, 53, 'aina', 'login', NULL, NULL, NULL, 'Admin logged into system', '::1', '2026-01-20 11:12:18'),
(9, 53, 'aina', 'edit_user', 'user', 53, '0', 'Edited user account #53 (aina yasmin)', '::1', '2026-01-20 11:13:39'),
(10, 53, 'aina', 'edit_user', 'user', 53, '0', 'Edited user account #53 (aina )', '::1', '2026-01-20 11:13:51'),
(11, 53, 'aina', 'edit_user', 'user', 52, '0', 'Edited user account #52 (qayyum dinni)', '::1', '2026-01-20 11:14:05'),
(12, 53, 'aina', 'logout', NULL, NULL, NULL, 'Admin logged out of system', '::1', '2026-01-20 11:14:59'),
(13, 44, 'halimah ', 'login', NULL, NULL, NULL, 'Admin logged into system', '::1', '2026-01-20 11:15:38'),
(14, 44, 'halimah ', 'delete_user', 'user', 54, NULL, 'Deleted user account #54', '::1', '2026-01-20 11:16:21'),
(15, 44, 'halimah ', 'update_status', 'found_item', 11, '0', 'Updated item #11 status from  to closed', '::1', '2026-01-20 11:17:19'),
(16, 44, 'halimah ', 'update_status', 'found_item', 11, '0', 'Updated item #11 status from  to pending', '::1', '2026-01-20 11:17:58'),
(17, 44, 'halimah ', 'update_status', 'found_item', 11, '0', 'Updated item #11 status from  to pending', '::1', '2026-01-20 11:18:28'),
(18, 44, 'halimah ', 'update_status', 'found_item', 11, '0', 'Updated item #11 status from  to pending', '::1', '2026-01-20 11:18:58'),
(19, 44, 'halimah ', 'logout', NULL, NULL, NULL, 'Admin logged out of system', '::1', '2026-01-20 11:43:57'),
(20, 53, 'aina ', 'login', NULL, NULL, NULL, 'Admin logged into system', '::1', '2026-01-20 11:45:23'),
(21, 53, 'aina ', 'update_status', 'found_item', 12, '0', 'Updated item #12 status from pending to approved', '::1', '2026-01-20 11:45:30'),
(22, 53, 'aina ', 'logout', NULL, NULL, NULL, 'Admin logged out of system', '::1', '2026-01-20 11:47:36'),
(23, 44, 'halimah ', 'login', NULL, NULL, NULL, 'Admin logged into system', '::1', '2026-01-20 11:48:25'),
(24, 44, 'halimah ', 'update_status', 'found_item', 12, '0', 'Updated item #12 status from approved to claimed', '::1', '2026-01-20 12:15:27'),
(25, 44, 'halimah ', 'update_status', 'found_item', 8, '0', 'Updated item #8 status from closed to claimed', '::1', '2026-01-20 12:15:35'),
(26, 44, 'halimah ', 'logout', NULL, NULL, NULL, 'Admin logged out of system', '::1', '2026-01-20 12:31:19'),
(27, 44, 'halimah ', 'login', NULL, NULL, NULL, 'Admin logged into system', '::1', '2026-01-20 12:43:26'),
(28, 44, 'halimah ', 'logout', NULL, NULL, NULL, 'Admin logged out of system', '::1', '2026-01-20 12:47:57'),
(29, 44, 'halimah ', 'login', NULL, NULL, NULL, 'Admin logged into system', '::1', '2026-01-20 12:56:28'),
(30, 44, 'halimah ', 'logout', NULL, NULL, NULL, 'Admin logged out of system', '::1', '2026-01-20 13:15:40'),
(31, 44, 'halimah ', 'login', NULL, NULL, NULL, 'Admin logged into system', '::1', '2026-01-20 13:24:36'),
(32, 44, 'halimah ', 'logout', NULL, NULL, NULL, 'Admin logged out of system', '::1', '2026-01-20 13:39:31'),
(33, 57, 'shafiee', 'login', NULL, NULL, NULL, 'Admin logged into system', '::1', '2026-01-20 13:49:25');

-- --------------------------------------------------------

--
-- Table structure for table `found_items`
--

CREATE TABLE `found_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `type_item` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `location` varchar(100) NOT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `status` enum('pending','approved','matched','claimed','closed') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `found_items`
--

INSERT INTO `found_items` (`id`, `user_id`, `user_name`, `type_item`, `date`, `time`, `location`, `picture`, `description`, `status`, `created_at`, `updated_at`) VALUES
(6, 51, 'nadrah', 'phone', '2026-01-19', '22:18:00', 'entrance', 'item_1768832317_51.png', 'vv', 'matched', '2026-01-19 22:18:37', '2026-01-19 22:56:49'),
(7, 51, 'nadrah', 'phone', '2026-01-19', '22:34:00', 'entrance', 'item_1768833312_51.png', 'tt', 'approved', '2026-01-19 22:35:12', '2026-01-19 23:30:32'),
(8, 51, 'nadrah', 'books', '2026-01-19', '22:57:00', 'ablution_area', 'item_1768834658_51.png', 'qq', 'claimed', '2026-01-19 22:57:38', '2026-01-20 20:15:35'),
(9, 51, 'nadrah', 'other', '2026-01-19', '23:31:00', 'other', 'item_1768836697_51.png', 'others', 'matched', '2026-01-19 23:31:37', '2026-01-19 23:32:22'),
(10, 52, 'qayyum', 'documents', '2026-01-19', '23:35:00', 'cafeteria', 'item_1768836933_52.png', 'doc', 'matched', '2026-01-19 23:35:33', '2026-01-19 23:36:20'),
(11, 52, 'qayyum', 'electronics', '2026-01-19', '00:02:00', 'main_hall', 'item_1768838542_52.png', 'eeeeee', 'pending', '2026-01-20 00:02:22', '2026-01-20 19:18:58'),
(12, 51, 'nadrah', 'books', '2026-01-20', '19:44:00', 'parking', 'item_1768909482_51.png', 'books', 'claimed', '2026-01-20 19:44:42', '2026-01-20 20:15:27'),
(13, 55, 'ismiyati', 'keys', '2026-01-20', '21:46:00', 'main_hall', 'item_1768916797_55.png', 'keys', 'pending', '2026-01-20 21:46:37', '2026-01-20 21:46:37');

-- --------------------------------------------------------

--
-- Table structure for table `lost_items`
--

CREATE TABLE `lost_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `type_item` varchar(50) NOT NULL,
  `date_lost` date NOT NULL,
  `time_lost` time NOT NULL,
  `location_lost` varchar(100) NOT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `status` enum('pending','approved','matched','claimed','closed') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admin_id` (`admin_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_target` (`target_type`,`target_id`);

--
-- Indexes for table `found_items`
--
ALTER TABLE `found_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_date` (`date`);

--
-- Indexes for table `lost_items`
--
ALTER TABLE `lost_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `found_items`
--
ALTER TABLE `found_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `lost_items`
--
ALTER TABLE `lost_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `found_items`
--
ALTER TABLE `found_items`
  ADD CONSTRAINT `found_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lost_items`
--
ALTER TABLE `lost_items`
  ADD CONSTRAINT `lost_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
