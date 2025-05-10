-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 10, 2025 at 07:05 PM
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
-- Database: `griham_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `accommodation_amenities`
--

CREATE TABLE `accommodation_amenities` (
  `service_id` int(10) UNSIGNED NOT NULL,
  `amenity_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `accommodation_services`
--

CREATE TABLE `accommodation_services` (
  `service_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `property_name` varchar(255) NOT NULL,
  `property_type` enum('PG','Hostel','Apartment','Room') NOT NULL,
  `address_id` int(10) UNSIGNED NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `main_image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `accommodation_services`
--
DELIMITER $$
CREATE TRIGGER `delete_saved_accommodation` AFTER DELETE ON `accommodation_services` FOR EACH ROW BEGIN
    DELETE FROM saved_services
    WHERE service_id = OLD.service_id AND service_type = 'accommodation';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `address_id` int(10) UNSIGNED NOT NULL,
  `city` varchar(100) NOT NULL,
  `village` varchar(255) DEFAULT NULL,
  `po` varchar(255) DEFAULT NULL,
  `tehsil` varchar(255) DEFAULT NULL,
  `district` varchar(255) DEFAULT NULL,
  `state` varchar(255) NOT NULL,
  `pincode` varchar(10) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(10,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `amenities`
--

CREATE TABLE `amenities` (
  `amenity_id` int(10) UNSIGNED NOT NULL,
  `amenity_name` varchar(100) NOT NULL,
  `icon_class` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `amenities`
--

INSERT INTO `amenities` (`amenity_id`, `amenity_name`, `icon_class`) VALUES
(1, 'WiFi', 'fas fa-wifi'),
(2, 'CCTV', 'fas fa-video'),
(3, 'AC', 'fas fa-snowflake'),
(4, 'Parking', 'fas fa-parking'),
(5, 'Laundry', 'fas fa-tshirt'),
(6, 'RO Water', 'fas fa-tint'),
(7, '24/7 Security', 'fas fa-shield-alt'),
(8, 'Gym', 'fas fa-dumbbell'),
(9, 'Balcony', 'fas fa-archway'),
(10, 'Housekeeping', 'fas fa-broom'),
(11, 'Geyser', 'fas fa-fire'),
(12, 'Power Backup', 'fas fa-battery-full'),
(13, 'Lift', 'fas fa-elevator'),
(14, 'Common Kitchen', 'fas fa-utensils'),
(15, 'Attached Bathroom', 'fas fa-bath'),
(16, 'Bed', 'fas fa-bed'),
(17, 'Sofa', 'fas fa-couch'),
(18, 'Refrigerator', 'fas fa-ice-cream'),
(19, 'Television', 'fas fa-tv'),
(20, 'Microwave', 'fas fa-microchip'),
(21, 'Dining Table', 'fas fa-chair'),
(22, 'Induction Stove', 'fas fa-burn'),
(23, 'Gas Pipeline', 'fas fa-gas-pump'),
(24, 'Study Table & Chair', 'fas fa-laptop'),
(25, 'Cupboard', 'fas fa-warehouse'),
(26, 'Pet Friendly', 'fas fa-paw'),
(27, 'Bike Parking', 'fas fa-motorcycle'),
(28, 'Car Parking', 'fas fa-car'),
(29, 'Smart Lock', 'fas fa-lock'),
(30, 'Fire Safety', 'fas fa-fire-extinguisher'),
(31, 'Swimming Pool', 'fas fa-swimmer'),
(32, 'Library', 'fas fa-book'),
(33, 'Tennis Court', 'fas fa-table-tennis'),
(34, 'Garden', 'fas fa-tree'),
(35, 'Children’s Play Area', 'fas fa-child'),
(36, 'Cafeteria', 'fas fa-coffee'),
(37, 'Terrace Access', 'fas fa-mountain'),
(38, 'Security Guard', 'fas fa-user-shield'),
(39, 'Visitor Parking', 'fas fa-parking-circle'),
(40, 'Borewell Water', 'fas fa-water'),
(41, 'Modular Kitchen', 'fas fa-utensil-spoon'),
(42, 'Smoke Detector', 'fas fa-smoking-ban'),
(43, 'Music System', 'fas fa-music'),
(44, 'Yoga/Meditation Room', 'fas fa-spa'),
(45, 'Clubhouse', 'fas fa-building'),
(46, 'Community Events', 'fas fa-users'),
(47, 'Treadmill', 'fas fa-running'),
(48, 'Weightlifting Equipment', 'fas fa-weight-hanging'),
(49, 'Free Weights', 'fas fa-dumbbell'),
(50, 'Cross Trainer', 'fas fa-person-running'),
(51, 'Yoga Mats', 'fas fa-square'),
(52, 'Personal Training', 'fas fa-user-tie'),
(53, 'Locker Rooms', 'fas fa-lock'),
(54, 'Shower Facilities', 'fas fa-shower'),
(55, 'Juice Bar', 'fas fa-glass-cheers'),
(56, 'Steam Room', 'fas fa-hot-tub-person'),
(57, 'Sauna', 'fas fa-temperature-high'),
(58, 'Group Classes', 'fas fa-users'),
(59, 'Cardio Machines', 'fas fa-heartbeat'),
(60, 'Work Desk', 'fas fa-desktop'),
(61, 'Mini Fridge', 'fas fa-snowflake'),
(62, 'Room Service', 'fas fa-concierge-bell'),
(63, 'Safe Deposit Box', 'fas fa-box-open'),
(64, 'Iron & Ironing Board', 'fas fa-shirt'),
(65, 'Hair Dryer', 'fas fa-wind'),
(66, 'Complimentary Toiletries', 'fas fa-soap'),
(67, 'Wake-up Service', 'fas fa-bell'),
(68, 'Room Heater', 'fas fa-temperature-high'),
(69, 'Buffet', 'fas fa-utensils'),
(70, 'A La Carte', 'fas fa-list-alt'),
(71, 'Takeout', 'fas fa-shopping-bag'),
(72, 'Delivery', 'fas fa-truck'),
(73, 'Catering', 'fas fa-birthday-cake'),
(74, 'Special Diet Options', 'fas fa-leaf'),
(75, 'Bar', 'fas fa-glass-martini-alt'),
(76, 'Dry Cleaning', 'fas fa-cloud-sun'),
(77, 'Ironing Service', 'fas fa-shirt'),
(78, 'Self-Service Laundry', 'fas fa-washing-machine'),
(79, 'Folding Service', 'fas fa-layer-group'),
(80, 'Walk-in Closet', 'fas fa-door-open'),
(81, 'Ensuite Bathroom', 'fas fa-toilet'),
(82, 'Soundproof Rooms', 'fas fa-volume-mute'),
(83, 'Smart Home Features', 'fas fa-lightbulb'),
(84, 'View', 'fas fa-eye'),
(85, 'Accessibility Features', 'fas fa-wheelchair');

-- --------------------------------------------------------

--
-- Table structure for table `emergency_services`
--

CREATE TABLE `emergency_services` (
  `service_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_details` varchar(255) DEFAULT NULL,
  `address_id` int(10) UNSIGNED NOT NULL,
  `category_id` int(11) NOT NULL,
  `subcategory_id` int(11) DEFAULT NULL,
  `opening_time` time DEFAULT NULL,
  `closing_time` time DEFAULT NULL,
  `is_24_7` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `emergency_service_categories`
--

CREATE TABLE `emergency_service_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emergency_service_categories`
--

INSERT INTO `emergency_service_categories` (`category_id`, `category_name`) VALUES
(1, 'Hospital'),
(2, 'Fire Brigade'),
(3, 'Police Station');

-- --------------------------------------------------------

--
-- Table structure for table `emergency_subcategories`
--

CREATE TABLE `emergency_subcategories` (
  `subcategory_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `subcategory_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emergency_subcategories`
--

INSERT INTO `emergency_subcategories` (`subcategory_id`, `category_id`, `subcategory_name`) VALUES
(1, 1, 'Private'),
(2, 1, 'Government'),
(3, 1, 'Clinic');

-- --------------------------------------------------------

--
-- Table structure for table `gym_services`
--

CREATE TABLE `gym_services` (
  `service_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `address_id` int(10) UNSIGNED NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `main_image` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `opening_hours` time NOT NULL,
  `closing_hours` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `gym_services`
--
DELIMITER $$
CREATE TRIGGER `delete_saved_gym` AFTER DELETE ON `gym_services` FOR EACH ROW BEGIN
    DELETE FROM saved_services
    WHERE service_id = OLD.service_id AND service_type = 'gym';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `gym_services_amenities`
--

CREATE TABLE `gym_services_amenities` (
  `gym_service_id` int(10) UNSIGNED NOT NULL,
  `amenity_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `image_id` int(10) UNSIGNED NOT NULL,
  `entity_type` enum('roommate_accommodation','accommodation_service','meal_service','gym_service','laundry_service') DEFAULT NULL,
  `entity_id` int(10) UNSIGNED NOT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `laundry_services`
--

CREATE TABLE `laundry_services` (
  `service_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `address_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `main_image` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `pickup` enum('Yes','No') NOT NULL DEFAULT 'No',
  `delivery` enum('Yes','No') NOT NULL DEFAULT 'No',
  `dry_cleaning` enum('Yes','No') NOT NULL DEFAULT 'No',
  `washing` enum('Yes','No') NOT NULL DEFAULT 'No',
  `ironing` enum('Yes','No') NOT NULL DEFAULT 'No',
  `opening_hours` time NOT NULL,
  `closing_hours` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `laundry_service_amenities`
--

CREATE TABLE `laundry_service_amenities` (
  `laundry_service_id` int(10) UNSIGNED NOT NULL,
  `amenity_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meal_services`
--

CREATE TABLE `meal_services` (
  `service_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` enum('Cafe','Dhaba','Tiffin') NOT NULL,
  `address_id` int(10) UNSIGNED NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `main_image` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `opening_hours` time NOT NULL,
  `closing_hours` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `meal_services`
--
DELIMITER $$
CREATE TRIGGER `delete_saved_meal_services` AFTER DELETE ON `meal_services` FOR EACH ROW BEGIN
    DELETE FROM saved_services
    WHERE service_id = OLD.service_id AND service_type = 'meal';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `meal_services_amenities`
--

CREATE TABLE `meal_services_amenities` (
  `meal_service_id` int(10) UNSIGNED NOT NULL,
  `amenity_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roommate_accommodations`
--

CREATE TABLE `roommate_accommodations` (
  `accommodation_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `apartment_name` varchar(255) NOT NULL,
  `rent_amount` decimal(10,2) NOT NULL,
  `room_type` enum('Single','Shared','Any') NOT NULL,
  `preferred_gender` enum('Male','Female','Other') NOT NULL,
  `lease_duration` enum('Short-term','Long-term','Flexible') NOT NULL,
  `guest_policy` enum('Moderate','Flexible','Strict') NOT NULL,
  `furnishing_status` enum('Unfurnished','Semi-Furnished','Furnished') NOT NULL,
  `parking` enum('No','Yes') NOT NULL,
  `smoking` enum('Yes','No','Flexible') NOT NULL,
  `drinking` enum('Yes','No','Flexible') NOT NULL,
  `pets` enum('Yes','No','Flexible') NOT NULL,
  `dietary_preference` enum('Vegetarian','Vegan','Non-Vegetarian','No Preference') NOT NULL,
  `daily_schedule` enum('Night Owl','Early Riser','Flexible') NOT NULL,
  `description` text DEFAULT NULL,
  `address_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `roommate_accommodations`
--
DELIMITER $$
CREATE TRIGGER `delete_saved_roommate` AFTER DELETE ON `roommate_accommodations` FOR EACH ROW BEGIN
    DELETE FROM saved_services
    WHERE service_id = OLD.accommodation_id AND service_type = 'roommate';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `roommate_accommodation_amenities`
--

CREATE TABLE `roommate_accommodation_amenities` (
  `accommodation_id` int(10) UNSIGNED NOT NULL,
  `amenity_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saved_services`
--

CREATE TABLE `saved_services` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `service_id` int(11) NOT NULL,
  `service_type` enum('accommodation','meal','gym','roommate','laundry') DEFAULT NULL,
  `saved_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_requests`
--

CREATE TABLE `support_requests` (
  `ticket_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `role` enum('tenant','owner','admin') NOT NULL DEFAULT 'tenant',
  `bio` text DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `profile_status` enum('Active','Inactive','Suspended') NOT NULL DEFAULT 'Active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `phone`, `role`, `bio`, `profile_pic`, `profile_status`, `last_login`, `created_at`, `updated_at`) VALUES
(2, 'Vishvesh Shivam', 'owner@gmail.com', 'df4a68e3984e9c1135117ad241904d8a84b805549aa91527a46ac41e3d8855a4', '9090909090', 'owner', 'Nothing, Just Wandering.', NULL, 'Active', '2025-04-10 15:59:26', '2025-03-26 16:46:57', '2025-05-10 17:02:38'),
(3, 'Aayush kumar', 'user@gmail.com', 'df4a68e3984e9c1135117ad241904d8a84b805549aa91527a46ac41e3d8855a4', '8080808080', 'tenant', 'Student of cse', NULL, 'Active', '2025-04-10 17:13:45', '2025-03-27 08:19:04', '2025-05-10 17:02:49'),
(7, 'Griham Admin', 'admin@gmail.com', 'df4a68e3984e9c1135117ad241904d8a84b805549aa91527a46ac41e3d8855a4', '7070707070', 'admin', 'CSE Student', NULL, 'Active', '2025-04-10 17:36:09', '2025-04-04 11:57:44', '2025-05-10 17:03:16');

-- --------------------------------------------------------

--
-- Table structure for table `user_otps`
--

CREATE TABLE `user_otps` (
  `email` varchar(255) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accommodation_amenities`
--
ALTER TABLE `accommodation_amenities`
  ADD PRIMARY KEY (`service_id`,`amenity_id`),
  ADD KEY `amenity_id` (`amenity_id`);

--
-- Indexes for table `accommodation_services`
--
ALTER TABLE `accommodation_services`
  ADD PRIMARY KEY (`service_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `address_id` (`address_id`);

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`address_id`);

--
-- Indexes for table `amenities`
--
ALTER TABLE `amenities`
  ADD PRIMARY KEY (`amenity_id`),
  ADD UNIQUE KEY `amenity_name` (`amenity_name`);

--
-- Indexes for table `emergency_services`
--
ALTER TABLE `emergency_services`
  ADD PRIMARY KEY (`service_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `subcategory_id` (`subcategory_id`),
  ADD KEY `address_id` (`address_id`);

--
-- Indexes for table `emergency_service_categories`
--
ALTER TABLE `emergency_service_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `emergency_subcategories`
--
ALTER TABLE `emergency_subcategories`
  ADD PRIMARY KEY (`subcategory_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `gym_services`
--
ALTER TABLE `gym_services`
  ADD PRIMARY KEY (`service_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `address_id` (`address_id`);

--
-- Indexes for table `gym_services_amenities`
--
ALTER TABLE `gym_services_amenities`
  ADD PRIMARY KEY (`gym_service_id`,`amenity_id`),
  ADD KEY `amenity_id` (`amenity_id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`image_id`);

--
-- Indexes for table `laundry_services`
--
ALTER TABLE `laundry_services`
  ADD PRIMARY KEY (`service_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `address_id` (`address_id`);

--
-- Indexes for table `laundry_service_amenities`
--
ALTER TABLE `laundry_service_amenities`
  ADD PRIMARY KEY (`laundry_service_id`,`amenity_id`),
  ADD KEY `amenity_id` (`amenity_id`);

--
-- Indexes for table `meal_services`
--
ALTER TABLE `meal_services`
  ADD PRIMARY KEY (`service_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `address_id` (`address_id`);

--
-- Indexes for table `meal_services_amenities`
--
ALTER TABLE `meal_services_amenities`
  ADD PRIMARY KEY (`meal_service_id`,`amenity_id`),
  ADD KEY `amenity_id` (`amenity_id`);

--
-- Indexes for table `roommate_accommodations`
--
ALTER TABLE `roommate_accommodations`
  ADD PRIMARY KEY (`accommodation_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `address_id` (`address_id`);

--
-- Indexes for table `roommate_accommodation_amenities`
--
ALTER TABLE `roommate_accommodation_amenities`
  ADD PRIMARY KEY (`accommodation_id`,`amenity_id`),
  ADD KEY `amenity_id` (`amenity_id`);

--
-- Indexes for table `saved_services`
--
ALTER TABLE `saved_services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_save` (`user_id`,`service_id`,`service_type`);

--
-- Indexes for table `support_requests`
--
ALTER TABLE `support_requests`
  ADD PRIMARY KEY (`ticket_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Indexes for table `user_otps`
--
ALTER TABLE `user_otps`
  ADD PRIMARY KEY (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accommodation_services`
--
ALTER TABLE `accommodation_services`
  MODIFY `service_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `address_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `amenities`
--
ALTER TABLE `amenities`
  MODIFY `amenity_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `emergency_services`
--
ALTER TABLE `emergency_services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emergency_service_categories`
--
ALTER TABLE `emergency_service_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `emergency_subcategories`
--
ALTER TABLE `emergency_subcategories`
  MODIFY `subcategory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `gym_services`
--
ALTER TABLE `gym_services`
  MODIFY `service_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `image_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `laundry_services`
--
ALTER TABLE `laundry_services`
  MODIFY `service_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meal_services`
--
ALTER TABLE `meal_services`
  MODIFY `service_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roommate_accommodations`
--
ALTER TABLE `roommate_accommodations`
  MODIFY `accommodation_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `saved_services`
--
ALTER TABLE `saved_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_requests`
--
ALTER TABLE `support_requests`
  MODIFY `ticket_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accommodation_amenities`
--
ALTER TABLE `accommodation_amenities`
  ADD CONSTRAINT `accommodation_amenities_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `accommodation_services` (`service_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `accommodation_amenities_ibfk_2` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`amenity_id`) ON DELETE CASCADE;

--
-- Constraints for table `accommodation_services`
--
ALTER TABLE `accommodation_services`
  ADD CONSTRAINT `accommodation_services_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `accommodation_services_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`address_id`) ON DELETE CASCADE;

--
-- Constraints for table `emergency_services`
--
ALTER TABLE `emergency_services`
  ADD CONSTRAINT `emergency_services_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `emergency_service_categories` (`category_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `emergency_services_ibfk_2` FOREIGN KEY (`subcategory_id`) REFERENCES `emergency_subcategories` (`subcategory_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `emergency_services_ibfk_3` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`address_id`) ON DELETE CASCADE;

--
-- Constraints for table `emergency_subcategories`
--
ALTER TABLE `emergency_subcategories`
  ADD CONSTRAINT `emergency_subcategories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `emergency_service_categories` (`category_id`) ON DELETE CASCADE;

--
-- Constraints for table `gym_services`
--
ALTER TABLE `gym_services`
  ADD CONSTRAINT `gym_services_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gym_services_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`address_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `gym_services_amenities`
--
ALTER TABLE `gym_services_amenities`
  ADD CONSTRAINT `gym_services_amenities_ibfk_1` FOREIGN KEY (`gym_service_id`) REFERENCES `gym_services` (`service_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gym_services_amenities_ibfk_2` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`amenity_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `laundry_services`
--
ALTER TABLE `laundry_services`
  ADD CONSTRAINT `laundry_services_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `laundry_services_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`address_id`);

--
-- Constraints for table `laundry_service_amenities`
--
ALTER TABLE `laundry_service_amenities`
  ADD CONSTRAINT `laundry_service_amenities_ibfk_1` FOREIGN KEY (`laundry_service_id`) REFERENCES `laundry_services` (`service_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `laundry_service_amenities_ibfk_2` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`amenity_id`) ON DELETE CASCADE;

--
-- Constraints for table `meal_services`
--
ALTER TABLE `meal_services`
  ADD CONSTRAINT `meal_services_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `meal_services_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`address_id`) ON DELETE CASCADE;

--
-- Constraints for table `meal_services_amenities`
--
ALTER TABLE `meal_services_amenities`
  ADD CONSTRAINT `meal_services_amenities_ibfk_1` FOREIGN KEY (`meal_service_id`) REFERENCES `meal_services` (`service_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `meal_services_amenities_ibfk_2` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`amenity_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `roommate_accommodations`
--
ALTER TABLE `roommate_accommodations`
  ADD CONSTRAINT `roommate_accommodations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `roommate_accommodations_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`address_id`) ON DELETE CASCADE;

--
-- Constraints for table `roommate_accommodation_amenities`
--
ALTER TABLE `roommate_accommodation_amenities`
  ADD CONSTRAINT `roommate_accommodation_amenities_ibfk_1` FOREIGN KEY (`accommodation_id`) REFERENCES `roommate_accommodations` (`accommodation_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `roommate_accommodation_amenities_ibfk_2` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`amenity_id`) ON DELETE CASCADE;

--
-- Constraints for table `saved_services`
--
ALTER TABLE `saved_services`
  ADD CONSTRAINT `saved_services_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `support_requests`
--
ALTER TABLE `support_requests`
  ADD CONSTRAINT `support_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
