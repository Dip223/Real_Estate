-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Dec 17, 2025 at 08:51 AM
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
-- Database: `real_estate_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `visit_date` date NOT NULL,
  `visit_time` time NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `property_id`, `buyer_id`, `seller_id`, `visit_date`, `visit_time`, `status`, `created_at`) VALUES
(1, 19, 7, 11, '2025-12-20', '14:00:00', 'approved', '2025-12-17 06:06:47'),
(2, 19, 7, 11, '2026-11-18', '13:50:00', 'rejected', '2025-12-17 06:49:43'),
(3, 18, 7, 11, '2026-08-02', '12:50:00', 'approved', '2025-12-17 06:50:16');

-- --------------------------------------------------------

--
-- Table structure for table `property`
--

CREATE TABLE `property` (
  `property_id` int(11) NOT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `property_type` varchar(50) DEFAULT NULL,
  `bedrooms` int(11) DEFAULT NULL,
  `bathrooms` int(11) DEFAULT NULL,
  `floor` int(11) DEFAULT NULL,
  `square_feet` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'available',
  `category` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property`
--

INSERT INTO `property` (`property_id`, `seller_id`, `title`, `description`, `price`, `location`, `property_type`, `bedrooms`, `bathrooms`, `floor`, `square_feet`, `image`, `status`, `category`, `created_at`) VALUES
(1, 1, 'Luxury Apartment in Dhaka', 'Beautiful 3-bedroom apartment in Dhanmondi.', 12000000.00, 'Dhaka', 'Flat', 3, NULL, NULL, 1450, 'flat1.jpg', 'available', 'Residential', '2025-12-05 15:54:24'),
(2, 2, '2 Room Flat in Chittagong', 'Affordable 2-bedroom flat.', 5500000.00, 'Chittagong', 'Flat', 2, NULL, NULL, 900, 'flat2.jpg', 'available', 'Residential', '2025-12-05 15:54:24'),
(3, 3, '5 Katha Land in Sylhet', 'Prime location land in Sylhet city.', 8000000.00, 'Sylhet', 'Land', NULL, NULL, NULL, 0, 'land1.jpg', 'available', 'Land', '2025-12-05 15:54:24'),
(4, 4, 'Family Apartment in Khulna', 'Perfect for a small family.', 4500000.00, 'Khulna', 'Flat', 1, NULL, NULL, 650, 'flat3.jpg', 'available', 'Residential', '2025-12-05 15:54:24'),
(5, 5, 'Luxury 4 Room Apartment', 'Spacious flat with modern facilities.', 15000000.00, 'Dhaka', 'Flat', 4, NULL, NULL, 1800, 'flat4.jpg', 'available', 'Residential', '2025-12-05 15:54:24'),
(6, 6, '10 Katha Land in Rajshahi', 'Good for commercial use.', 9500000.00, 'Rajshahi', 'Land', NULL, NULL, NULL, 0, 'land2.jpg', 'available', 'Land', '2025-12-05 15:54:24'),
(7, 7, '3 Room Flat in Barisal', 'Nice flat near city center.', 3200000.00, 'Barisal', 'Flat', 3, NULL, NULL, 1100, 'flat5.jpg', 'available', 'Residential', '2025-12-05 15:54:24'),
(8, 11, 'Dream villa', 'this is an duplex house. Which is a luxurious apartment.', 3000000.00, 'Chittagong', 'Apartment', 6, NULL, NULL, 3500, '1765863261_6913_front_exterior_7310.jpg', 'available', NULL, '2025-12-16 05:34:21'),
(9, 11, 'Apartment', 'It 4 Unit, 12th floor building, which have 36 flats.', 1000000.00, 'Dhaka,Purbachol', 'Flat', 4, NULL, NULL, 1800, '1765868793_apartment-balcony-buildings-439391.jpg', 'available', NULL, '2025-12-16 07:06:33'),
(10, 11, 'Double Duplex', 'there is four floors with attaching by staircase. And beautiful and attractive.', 2500000.00, 'Bashundhara', 'Apartment', 6, NULL, NULL, 4500, '1765869486_duplex_shutterstock_1188162424-1024x670.jpg', 'available', NULL, '2025-12-16 07:18:06'),
(11, 11, 'Duplex', 'Beautiful, sweet home', 2300000.00, 'Bashundhara', 'Apartment', 8, NULL, NULL, 5000, '1765869684_Duplex_2.webp', 'available', NULL, '2025-12-16 07:21:24'),
(12, 11, 'Under construction ', 'we will build a building with share 30 people. And now people can buy share with us. This is 10 katha land.', 5000000.00, 'Mirpur-10', 'Flat', 3, NULL, NULL, 1800, '1765901644_house-construction-development-22240636.webp', 'available', NULL, '2025-12-16 16:14:04'),
(13, 11, 'under construction', 'building project on 11 katha land.', 3000000.00, 'Rajshahi', 'Land', 0, NULL, NULL, 0, '1765902621_house-building-construction-site-photo.jpg', 'available', NULL, '2025-12-16 16:30:21'),
(14, 11, 'Green Model Town', 'It is an apartment complex where are 24 flats. Each flat will be sell.', 6000000.00, 'Mirpur-10', 'Flat', 5, NULL, NULL, 2500, '1765906141_OIP.webp', 'available', NULL, '2025-12-16 17:29:01'),
(15, 11, 'Bondhon Society', 'It is a beautiful and luxurious building. Which is stand in 5.6 katha. Each flat will sell.', 6500000.00, 'Mirpur-1', 'Flat', 4, NULL, NULL, 1800, '1765906314_a-contemporary-residential-apartment-building-with-a-luxurious-exterior-and-outdoor-space-this-free-photo.jpg', 'available', NULL, '2025-12-16 17:31:54'),
(16, 11, 'Duplex House', 'Dream duplex house. This is well decorated and well design apartment. ', 7000000.00, 'Bashundhara', 'Apartment', 7, NULL, NULL, 5000, '1765906782_building-with-flat-roof-perfectly-landscaped-green-spaces-ai-generated_547674-2953.avif', 'available', NULL, '2025-12-16 17:39:42'),
(17, 11, 'Luxury Duplex', 'Beautiful family comfort duplex complex.', 10000000.00, 'Chittagong', 'Apartment', 8, NULL, NULL, 5000, '1765907125_d.jpg', 'available', NULL, '2025-12-16 17:45:25'),
(18, 11, 'Great Stone', 'Luxury building for family living.', 3000000.00, 'Bashundhara', 'Flat', 5, 3, 8, 1800, '1765908513_School-Building-2-923x1024.jpg', 'available', NULL, '2025-12-16 18:08:33'),
(19, 11, 'Modern House', 'Very modern and luxurious', 40000000.00, 'Nikunja-1, Dhaka', 'Flat', 8, 4, 3, 2500, '1765908626_OIP (2).webp', 'available', NULL, '2025-12-16 18:10:26');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `property_id` int(11) DEFAULT NULL,
  `buyer_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `property_id`, `buyer_id`, `rating`, `review_text`, `created_at`) VALUES
(1, 8, 7, 5, 'It is very nice and beautiful apartment.', '2025-12-16 05:38:53'),
(2, 8, 7, 4, 'nice but expensive', '2025-12-16 05:39:29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('buyer','seller','admin') DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `otp` varchar(10) DEFAULT NULL,
  `otp_expire` datetime DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `phone`, `password`, `role`, `status`, `otp`, `otp_expire`, `is_verified`, `created_at`) VALUES
(1, 'MHD', 'mhd@gmail.com', '12345678', '$2y$10$HqfbIMt4SEYqiJPPT7w6Zewnh69OACmY8HpCyk8rzRWwHGjPyCkcW', 'buyer', 'active', '102950', NULL, 0, '2025-12-04 02:24:36'),
(2, 'Maruf', 'maruf@gmail.com', '012345678', '$2y$10$x3H5bUn7huqtg1KU4uXCGeen95PiCxVs0a650ZSEJTz85ZPvAMlry', 'buyer', 'active', '836230', NULL, 0, '2025-12-04 03:10:27'),
(3, 'Mehedi Hasan Dip', 'mehedi183012.2003@gmail.com', '01718025553', '$2y$10$vBxQzLcnN8bNwTib5a5/9.OEGFq/NIjBqUuTvjSJ7XZgfXrc6/SpW', 'admin', 'active', NULL, NULL, 1, '2025-12-04 03:16:37'),
(4, 'Mahedi Hasan Shabuj', 'hasanshobujmehedi327@gmail.com', '01609295366', '$2y$10$PdgUq1kVCQsxF7zVXj2I2.RizUpMBXAEIHlny4QLUcHUI32mqzqrS', 'buyer', 'active', '659304', NULL, 0, '2025-12-04 17:42:07'),
(5, 'Mahedi Hasan Shabuj', 'hasanshobuzmehedi327@gmail.com', '01609295366', '$2y$10$Jt72c6P586q.uEy00zfRtOim3AXm9TvfuYpCr.PoJs7wZ3AwG0lt6', 'buyer', 'active', NULL, NULL, 1, '2025-12-04 17:44:56'),
(7, 'Antu', 'mehedi.dip@northsouth.edu', '01872405292', '$2y$10$5yO6tV5rQ62xltL21Q3uLuYAlzg3bamLHa7wKc4fjj1XP6Kp4XWZW', 'buyer', 'active', '334819', '2025-12-16 18:57:38', 1, '2025-12-16 04:35:19'),
(11, 'Fahim', 'zihadmuzahid2003@gmail.com', '04567890', '$2y$10$n0p6trO/UyIQMXXqgmDLbeSYDWyu1B3R7kblGCietIG0Ak7KGQEGS', 'seller', 'active', NULL, NULL, 1, '2025-12-16 05:11:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`);

--
-- Indexes for table `property`
--
ALTER TABLE `property`
  ADD PRIMARY KEY (`property_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `property`
--
ALTER TABLE `property`
  MODIFY `property_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
