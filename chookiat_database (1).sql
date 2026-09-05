-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
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
-- Database: `chookiat_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `car_code` varchar(30) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `model` varchar(100) NOT NULL,
  `model_year` year(4) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `license_plate` varchar(20) DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('available','reserved','sold','inactive') NOT NULL DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`id`, `car_code`, `brand`, `model`, `model_year`, `color`, `license_plate`, `price`, `status`) VALUES
(1, 'CAR-001', 'Toyota', 'Hilux Revo Smart Cab 2.4 Entry', '2022', 'บรอนซ์เงิน', 'กข-4589 สงขลา', 480000.00, 'available'),
(2, 'CAR-002', 'Isuzu', 'D-Max Hi-Lander 1.9 Ddi', '2021', 'ขาวมุก', 'ขง-7812 สงขลา', 520000.00, 'available'),
(3, 'CAR-003', 'Honda', 'City 1.0 Turbo RS', '2023', 'เทาเมทัลลิก', 'ฆฮ-9901 สงขลา', 560000.00, 'reserved'),
(4, 'CAR-004', 'Mazda', 'Mazda 2 Hatchback 1.3', '2022', 'ดำ', 'กพ-3456 ยะลา', 420000.00, 'sold'),
(5, 'CAR-005', 'Ford', 'Ranger Double Cab 2.0 Turbo', '2020', 'ส้ม', 'บม-6789 ปัตตานี', 590000.00, 'available');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('admin@chookiat.com', '2cdGL8gGgQO3MFPdUygQ48SmX9DKC58UtvhizLiLYefDlY7U4OAIcniBj6P8', '2026-09-04 22:59:51'),
('test@chookiat.com', 'TXYUW8uH33ZFMgjC0iNngx48YQEKTdrbBGlUbwDBWU7103f6wolc7jiLIqgo', '2026-09-04 00:23:29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `username`, `password`, `role`, `is_active`) VALUES
(1, 'Admin Chookiat', 'admin@chookiat.com', 'admin', '$2y$12$Ix7Q6i578tyq29vuD1Pl.eZLZEFuU6plp/MhWwaaeRjS5elrclLeO', 'admin', 1),
(2, 'User', 'user1@chookiat.com', 'user1', '$2y$12$CjkLB663nurccQnsnwMSwednVZHUGPzRjo0NFwe/lh59iGHiP75gO', 'user', 1),
(3, 'Inactive User', 'inactive@chookiat.com', 'inactive_user', '$2y$12$xZv6SJl6ORFUS9khllxjM.D7DhM1KlTYDGaWHT0b6jV5I10SnAKmG', 'user', 0),
(4, 'test123', 'test@chookiat.com', 'test', '$2y$12$I6zSHadQvNN00cRsBH0gCeEbEKVCXcILgDHcvXiHCDvmpKJGrtotS', 'admin', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `car_code` (`car_code`),
  ADD UNIQUE KEY `license_plate` (`license_plate`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
