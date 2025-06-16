-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 12, 2025 at 12:24 PM
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
-- Database: `uzoca`
--

-- --------------------------------------------------------

--
-- Table structure for table `agent_activities`
--

CREATE TABLE `agent_activities` (
  `id` int(11) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `activity_type` enum('property_view','booking','commission') NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `agent_commissions`
--

CREATE TABLE `agent_commissions` (
  `id` int(11) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid') DEFAULT 'pending',
  `payment_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `agent_payments`
--

CREATE TABLE `agent_payments` (
  `id` int(11) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reference` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `payment_type` varchar(50) DEFAULT 'subscription',
  `payment_method` varchar(50) DEFAULT NULL,
  `status` enum('pending','completed','failed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `agent_payment_settings`
--

CREATE TABLE `agent_payment_settings` (
  `id` int(11) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `ecocash_number` varchar(20) DEFAULT NULL,
  `ecocash_name` varchar(100) DEFAULT NULL,
  `mukuru_number` varchar(20) DEFAULT NULL,
  `mukuru_name` varchar(100) DEFAULT NULL,
  `innbucks_number` varchar(20) DEFAULT NULL,
  `innbucks_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `agent_properties`
--

CREATE TABLE `agent_properties` (
  `id` int(11) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `commission_rate` decimal(5,2) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `agent_properties`
--
DELIMITER $$
CREATE TRIGGER `after_agent_activity` AFTER INSERT ON `agent_properties` FOR EACH ROW INSERT INTO agent_activities (agent_id, activity_type, description)
VALUES (NEW.agent_id, 'property_view', CONCAT('Property ', NEW.property_id, ' added to agent portfolio'))
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `agent_subscriptions`
--

CREATE TABLE `agent_subscriptions` (
  `id` int(11) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `plan_type` enum('basic','premium','enterprise') NOT NULL,
  `status` enum('active','expired','cancelled') NOT NULL DEFAULT 'active',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `agent_subscriptions`
--

INSERT INTO `agent_subscriptions` (`id`, `agent_id`, `plan_type`, `status`, `start_date`, `end_date`, `created_at`, `updated_at`) VALUES
(1, 6, 'basic', 'active', '2025-06-02', '2025-07-02', '2025-06-01 23:07:55', '2025-06-01 23:07:55'),
(2, 6, 'basic', 'active', '2025-06-02', '2025-07-02', '2025-06-01 23:10:32', '2025-06-01 23:10:32'),
(3, 6, 'basic', 'active', '2025-06-02', '2025-07-02', '2025-06-01 23:11:06', '2025-06-01 23:11:06'),
(4, 6, 'basic', 'active', '2025-06-02', '2025-07-02', '2025-06-01 23:11:13', '2025-06-01 23:11:13'),
(5, 6, 'basic', 'active', '2025-06-02', '2025-07-02', '2025-06-01 23:13:25', '2025-06-01 23:13:25'),
(6, 6, 'basic', 'active', '2025-06-02', '2025-12-02', '2025-06-01 23:14:06', '2025-06-01 23:14:06'),
(7, 6, 'basic', 'active', '2025-06-02', '2025-07-02', '2025-06-01 23:17:21', '2025-06-01 23:17:21'),
(8, 6, 'basic', 'active', '2025-06-02', '2025-07-02', '2025-06-01 23:20:09', '2025-06-01 23:20:09'),
(9, 6, 'basic', 'active', '2025-06-02', '2025-12-02', '2025-06-01 23:20:14', '2025-06-01 23:20:14'),
(10, 6, 'basic', 'active', '2025-06-02', '2025-07-02', '2025-06-01 23:20:18', '2025-06-01 23:20:18'),
(11, 6, 'basic', 'active', '2025-06-02', '2025-07-02', '2025-06-02 04:10:08', '2025-06-02 04:10:08'),
(12, 6, 'basic', 'active', '2025-06-02', '2025-07-02', '2025-06-02 04:10:17', '2025-06-02 04:10:17'),
(13, 6, 'basic', 'active', '2025-06-02', '2025-07-02', '2025-06-02 04:10:28', '2025-06-02 04:10:28'),
(14, 6, 'basic', 'active', '2025-06-02', '2025-07-02', '2025-06-02 04:12:19', '2025-06-02 04:12:19'),
(15, 6, 'basic', 'active', '2025-06-02', '2025-09-02', '2025-06-02 04:13:48', '2025-06-02 04:13:48'),
(16, 6, 'basic', 'active', '2025-06-02', '2025-07-02', '2025-06-02 04:16:01', '2025-06-02 04:16:01');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `id_number` varchar(50) NOT NULL,
  `booking_code` varchar(50) NOT NULL,
  `move_in_date` date NOT NULL,
  `status` enum('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `bookings`
--
DELIMITER $$
CREATE TRIGGER `generate_booking_code` BEFORE INSERT ON `bookings` FOR EACH ROW SET NEW.booking_code = CONCAT('BK', DATE_FORMAT(NOW(), '%y%m'), LPAD(NEW.id, 4, '0'))
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `booking_payments`
--

CREATE TABLE `booking_payments` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `reference_number` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `qr_code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `landlords`
--

CREATE TABLE `landlords` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `phone` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL,
  `profile_pic` varchar(255) NOT NULL DEFAULT 'profile-pic.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `landlords`
--

INSERT INTO `landlords` (`id`, `name`, `phone`, `email`, `password`, `profile_pic`) VALUES
(1, 'Kelvin', '0783677132', 'kelvin@gmail.com', '$2y$10$PULKHik0fnIMIKy.g03a5OOvgOsPv.borZ.5oEwpHsg5KGsxz8KZW', 'profile-pic.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('property','booking','system') NOT NULL DEFAULT 'system',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_type` varchar(50) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `reference_number` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `index_img` varchar(500) NOT NULL,
  `title` varchar(500) NOT NULL,
  `link` varchar(255) NOT NULL,
  `summary` text NOT NULL,
  `img_1` varchar(500) NOT NULL,
  `img_2` varchar(500) NOT NULL,
  `img_3` varchar(500) NOT NULL,
  `img_4` varchar(500) NOT NULL,
  `img_5` varchar(500) NOT NULL,
  `description` text NOT NULL,
  `type` enum('For Rent','For Sale') NOT NULL DEFAULT 'For Rent',
  `location` varchar(255) NOT NULL,
  `status` enum('available','sold','rented') NOT NULL DEFAULT 'available',
  `main_image` varchar(255) DEFAULT NULL,
  `additional_images` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `owner_id`, `agent_id`, `price`, `category`, `index_img`, `title`, `link`, `summary`, `img_1`, `img_2`, `img_3`, `img_4`, `img_5`, `description`, `type`, `location`, `status`, `main_image`, `additional_images`, `created_at`, `updated_at`) VALUES
(2, 13, NULL, 150.00, NULL, 'thornpark-1749424060-1.jpg', 'Thornpark', 'thornpark', '<p>rooms of 2 with ensuite</p><p>rooms of 3 with ensuites</p><p>rooms of 1</p>', 'thornpark-1749424060-2.jpg', 'thornpark-1749424060-3.jpg', 'thornpark-1749424060-4.jpg', 'thornpark-1749424060-5.jpg', 'thornpark-1749424060-6.jpg', '<p>for university students</p>', 'For Rent', 'Malborough', 'available', NULL, NULL, '2025-06-08 23:07:40', '2025-06-11 15:01:14');

-- --------------------------------------------------------

--
-- Table structure for table `property_images`
--

CREATE TABLE `property_images` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `property_landlords`
--

CREATE TABLE `property_landlords` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property_landlords`
--

INSERT INTO `property_landlords` (`id`, `property_id`, `user_id`, `created_at`) VALUES
(1, 2, 13, '2025-06-08 23:07:40');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `property_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `next_billing_date` timestamp NULL DEFAULT NULL,
  `plan_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `tenants`
--

CREATE TABLE `tenants` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `id_number` varchar(50) NOT NULL,
  `booking_code` varchar(50) NOT NULL,
  `move_in_date` date NOT NULL,
  `status` enum('active','inactive','evicted') NOT NULL DEFAULT 'active',
  `location` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `tenants`
--
DELIMITER $$
CREATE TRIGGER `update_property_status` AFTER INSERT ON `tenants` FOR EACH ROW UPDATE properties 
SET status = 'unavailable' 
WHERE id = NEW.property_id 
AND (SELECT COUNT(*) FROM tenants WHERE property_id = NEW.property_id AND status = 'active') >= 
    (SELECT capacity FROM properties WHERE id = NEW.property_id)
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `id` int(11) NOT NULL,
  `buyer_name` varchar(255) NOT NULL,
  `payment_date` varchar(255) NOT NULL,
  `amount` varchar(255) NOT NULL DEFAULT '',
  `property_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `status` enum('pending','completed','failed') NOT NULL DEFAULT 'pending',
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','agent','landlord','tenant') NOT NULL DEFAULT 'tenant',
  `phone` varchar(255) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT 'profile-pic.jpg',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `phone`, `profile_pic`, `created_at`, `updated_at`) VALUES
(1, 'Briel', 'kidmatrixx01@gmail.com.com', '$2y$10$/V16qhLO6IhkFbDVRa0yIOIsSGleobr6zlFS/IpLU/w2ATZ7LvJQa', 'admin', '0783677131', 'profile-pic.jpg', '2025-06-01 12:10:14', '2025-06-07 12:36:10'),
(2, 'Admin User', 'admin@uzoca.co.zw', '$2y$10$QdgfLoHKZwxAR6xWLLuBwugDaOMMYp7ysGAtUcMGuPBZFK0uwvOcK', 'admin', '+263 78 367 7131', 'profile-pic.jpg', '2025-06-01 13:38:46', '2025-06-04 22:22:09'),
(4, 'New Admin', 'newadmin@uzoca.co.zw', '.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '+263 78 367 7132', 'profile-pic.jpg', '2025-06-01 15:15:48', '2025-06-01 15:15:48'),
(5, 'Shammah', 'shammah@gmail.com', '$2y$10$ZPRBKGwIwXu61yr/PahsuuZA2Z4ttZvrmtYbkoMJTjtg/nXjR6KJ2', '', '0778243567', 'profile-pic.jpg', '2025-06-01 18:58:25', '2025-06-01 18:58:25'),
(6, 'bb', 'kidmatrixx@gmail.com', '$2y$10$jE8hCBrr/OEOzeqNmz12UOLh.wQ7BTKDGbK1tL24dGJMFRRBPfZbK', 'agent', '0773452673', 'profile-pic.jpg', '2025-06-01 19:42:01', '2025-06-01 19:42:01'),
(7, 'Test User', 'test@example.com', '$2y$10$baZJzqaVtdzPCS0OLwkMr.KJEzLYtsensA/xVcuk3DI7p0WA6KLce', 'landlord', '1234567890', 'profile-pic.jpg', '2025-06-03 13:32:28', '2025-06-03 13:32:28'),
(8, 'Test User', 'testuser_39ca7664@example.com', '$2y$10$rrU8cWd/0KeU1x0GrtIIyuolxbJ1QA1tNM3uYn.TgCiooN3b1PUcC', 'landlord', '1234567890', 'profile-pic.jpg', '2025-06-03 13:36:41', '2025-06-03 13:36:41'),
(9, 'Test User', 'test_1748972094@example.com', '$2y$10$S40rSymyT3sjM.3g91pqb.elCvXNe2jzX4ag.F1DLid2oI9aL09uS', 'landlord', NULL, 'profile-pic.jpg', '2025-06-03 17:34:54', '2025-06-03 17:34:54'),
(10, 'Test User', 'test_1748972207@example.com', '$2y$10$.ekMprEI2pt3V2QE0N45C.gIzjdO5Mdmvv6o9XUJXWRy2JQST7Dd6', 'landlord', NULL, 'profile-pic.jpg', '2025-06-03 17:36:48', '2025-06-03 17:36:48'),
(11, 'dee', 'kidmatrixx1@gmail.com', '$2y$10$lricNziKbeN4ad07NEVAuO3DUmWMsgJf1e7hOX97HHhVSa21gQQrq', 'landlord', '0773452670', 'profile-pic.jpg', '2025-06-04 16:03:28', '2025-06-04 16:03:28'),
(12, 'Admin User', 'admin@uzoca.com', '$2y$10$Zbp9UUKsPQIlDgL3Jx0oP.xO4jO4Lcjoqhp4H4WP516siviozwnyG', 'admin', '1234567890', 'profile-pic.jpg', '2025-06-07 12:29:58', '2025-06-08 05:18:09'),
(13, 'Admin User', 'kidmatrixx01@gmail.com', '$2y$10$1gnfl5z83rO/wX/QpMkv7uBeagJUNt/zQVEzhO5XMHvUXA1O2zh3i', 'admin', '0783677131', 'profile-pic.jpg', '2025-06-07 12:36:46', '2025-06-07 13:35:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agent_activities`
--
ALTER TABLE `agent_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_agent_activities_agent` (`agent_id`);

--
-- Indexes for table `agent_commissions`
--
ALTER TABLE `agent_commissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_agent_commissions_agent` (`agent_id`),
  ADD KEY `idx_agent_commissions_booking` (`booking_id`);

--
-- Indexes for table `agent_payments`
--
ALTER TABLE `agent_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference` (`reference`),
  ADD KEY `agent_id` (`agent_id`);

--
-- Indexes for table `agent_payment_settings`
--
ALTER TABLE `agent_payment_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `agent_id` (`agent_id`);

--
-- Indexes for table `agent_properties`
--
ALTER TABLE `agent_properties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_agent_properties_agent` (`agent_id`),
  ADD KEY `idx_agent_properties_property` (`property_id`);

--
-- Indexes for table `agent_subscriptions`
--
ALTER TABLE `agent_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `agent_id` (`agent_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_booking_code` (`booking_code`),
  ADD KEY `property_id` (`property_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_bookings_status` (`status`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `booking_payments`
--
ALTER TABLE `booking_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `landlords`
--
ALTER TABLE `landlords`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_reference` (`reference_number`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `idx_payments_status` (`status`);

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_property_link` (`link`),
  ADD KEY `owner_id` (`owner_id`),
  ADD KEY `agent_id` (`agent_id`);

--
-- Indexes for table `property_images`
--
ALTER TABLE `property_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `property_landlords`
--
ALTER TABLE `property_landlords`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_property_landlord` (`property_id`,`user_id`),
  ADD KEY `idx_property_landlords_user` (`user_id`),
  ADD KEY `idx_property_landlords_property` (`property_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `plan_id` (`plan_id`);

--
-- Indexes for table `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_tenant_booking` (`booking_code`),
  ADD KEY `property_id` (`property_id`),
  ADD KEY `idx_tenants_status` (`status`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id_idx` (`property_id`),
  ADD KEY `transaction_tenant_id_idx` (`tenant_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

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
-- AUTO_INCREMENT for table `agent_activities`
--
ALTER TABLE `agent_activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `agent_commissions`
--
ALTER TABLE `agent_commissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `agent_payments`
--
ALTER TABLE `agent_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `agent_payment_settings`
--
ALTER TABLE `agent_payment_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `agent_properties`
--
ALTER TABLE `agent_properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `agent_subscriptions`
--
ALTER TABLE `agent_subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `booking_payments`
--
ALTER TABLE `booking_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `landlords`
--
ALTER TABLE `landlords`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `property_images`
--
ALTER TABLE `property_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `property_landlords`
--
ALTER TABLE `property_landlords`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `agent_activities`
--
ALTER TABLE `agent_activities`
  ADD CONSTRAINT `agent_activities_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `agent_commissions`
--
ALTER TABLE `agent_commissions`
  ADD CONSTRAINT `agent_commissions_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `agent_commissions_ibfk_2` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`);

--
-- Constraints for table `agent_payments`
--
ALTER TABLE `agent_payments`
  ADD CONSTRAINT `agent_payments_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `agent_payment_settings`
--
ALTER TABLE `agent_payment_settings`
  ADD CONSTRAINT `fk_agent_payment_settings_agent_id` FOREIGN KEY (`agent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `agent_properties`
--
ALTER TABLE `agent_properties`
  ADD CONSTRAINT `agent_properties_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `agent_properties_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`);

--
-- Constraints for table `agent_subscriptions`
--
ALTER TABLE `agent_subscriptions`
  ADD CONSTRAINT `agent_subscription_agent_id` FOREIGN KEY (`agent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `booking_payments`
--
ALTER TABLE `booking_payments`
  ADD CONSTRAINT `booking_payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notification_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `properties_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `property_agent_id` FOREIGN KEY (`agent_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `property_images`
--
ALTER TABLE `property_images`
  ADD CONSTRAINT `property_images_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `property_landlords`
--
ALTER TABLE `property_landlords`
  ADD CONSTRAINT `property_landlords_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `property_landlords_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `review_property_id` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `review_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`);

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `subscriptions_ibfk_2` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`);

--
-- Constraints for table `tenants`
--
ALTER TABLE `tenants`
  ADD CONSTRAINT `tenants_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `transaction_property_id` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `transaction_tenant_id` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transaction_booking_id` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
