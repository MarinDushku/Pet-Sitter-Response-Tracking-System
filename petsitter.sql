-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 15, 2023 at 04:38 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `petsitter`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `Comment_Id` int(11) NOT NULL,
  `Post_Id` int(11) DEFAULT NULL,
  `Username` varchar(255) DEFAULT NULL,
  `Comment` text DEFAULT NULL,
  `Date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`Comment_Id`, `Post_Id`, `Username`, `Comment`, `Date`) VALUES
(1, 1, 'MD', 'Hello', '2023-12-11 01:43:01'),
(2, 1, 'MD', 'Hello', '2023-12-11 01:43:38'),
(3, 2, 'MD', 'wqeqwe', '2023-12-11 01:56:10'),
(4, 1, 'MD', 'wassap', '2023-12-10 20:11:47'),
(5, 1, 'MD', 'u there!', '2023-12-10 20:20:25'),
(6, 1, 'MD', 'mmmmmmm', '2023-12-10 20:21:47'),
(7, 2, 'Arl', 'Hello', '2023-12-11 07:04:26'),
(8, 2, 'Arl', 'Hello', '2023-12-11 07:14:12'),
(9, 1, 'Arl', 'hello', '2023-12-11 07:53:12'),
(10, 1, 'Arl', 'how are u', '2023-12-11 07:53:28'),
(11, 2, 'MD', 'We good', '2023-12-11 12:57:15'),
(12, 1, 'handler', 'admin here', '2023-12-13 11:08:26'),
(13, 1, 'handler', 'admin here', '2023-12-13 11:08:27'),
(14, 1, 'handler', 'admin here', '2023-12-13 11:08:27'),
(15, 1, 'handler', 'admin here', '2023-12-13 11:08:27'),
(16, 3, 'Arl', 'Hi', '2023-12-14 11:24:37');

-- --------------------------------------------------------

--
-- Table structure for table `healthbehavior`
--

CREATE TABLE `healthbehavior` (
  `Post_Id` int(11) DEFAULT NULL,
  `Vaccinated` enum('Yes','No') NOT NULL,
  `HealthIssues` text DEFAULT NULL,
  `Temperament` text NOT NULL,
  `DietType` varchar(255) NOT NULL,
  `FeedingTimes` varchar(255) NOT NULL,
  `ExerciseNeeds` varchar(255) NOT NULL,
  `FavoriteToys` text DEFAULT NULL,
  `SittingDates` varchar(255) NOT NULL,
  `SittingTime` varchar(255) NOT NULL,
  `SpecialInstructions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `healthbehavior`
--

INSERT INTO `healthbehavior` (`Post_Id`, `Vaccinated`, `HealthIssues`, `Temperament`, `DietType`, `FeedingTimes`, `ExerciseNeeds`, `FavoriteToys`, `SittingDates`, `SittingTime`, `SpecialInstructions`) VALUES
(1, 'Yes', '2131', '31231', 'Salmon', 'All Day', 'Job', 'qweqeq', '1 Jan - 2 Jan', 'wqqwqw', 'qweqeq'),
(2, 'Yes', '211', '121', 'qweqe', '1312', '1212', 'weqeq', '12121', 'qwe', 'qweqweq'),
(3, 'Yes', '211', '121', 'qweqe', '1312', '1212', 'weqeq', '12121', 'qwe', 'qweqweq'),
(4, 'No', 'dafa', 'fhdgd', 'Salmon', 'All Day', 'Job', 'none', '1 Jan - 2 Jan', 'Whenever', 'none'),
(5, 'Yes', '', 'Good', 'fish', '3 times a day', 'walk', 'Rope', '1 Jan - 2 Jan', 'Whenever', '');

-- --------------------------------------------------------

--
-- Table structure for table `petinfo`
--

CREATE TABLE `petinfo` (
  `Post_Id` int(11) NOT NULL,
  `PetName` varchar(255) NOT NULL,
  `PetType` varchar(255) NOT NULL,
  `Breed` varchar(255) DEFAULT NULL,
  `Age` int(11) NOT NULL,
  `Gender` enum('male','female') NOT NULL,
  `Photo` varchar(255) DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `Status` varchar(255) DEFAULT 'Pending',
  `Taken_by` varchar(255) DEFAULT NULL,
  `Username` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `petinfo`
--

INSERT INTO `petinfo` (`Post_Id`, `PetName`, `PetType`, `Breed`, `Age`, `Gender`, `Photo`, `CreatedAt`, `Status`, `Taken_by`, `Username`) VALUES
(1, 'Ted', 'Bear', 'Brown', 12, 'male', '6572f3220da3e.jpg', '2023-12-08 10:42:42', 'Completed', 'Arl', 'MD'),
(2, 'Ted', 'Bear', 'Brown', 12, 'female', '65750b2f480d8.jpg', '2023-12-10 00:49:51', 'Completed', 'Arl', 'MD'),
(3, 'Ted', 'Bear', 'Brown', 12, 'female', '65750b6856e17.jpg', '2023-12-10 00:50:48', 'Assigned', 'Arl', 'MD'),
(4, 'asa', 'Dog', 'Brown', 54, 'male', '6577083e01953.png', '2023-12-11 13:01:50', 'Pending', NULL, 'Balja'),
(5, 'Happy', 'Eagle', 'Haski', 51, 'male', '657a4b7a619f2.jpg', '2023-12-14 00:25:30', 'Assigned', 'Sitter', 'MD');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `role` enum('client','sitter','handler') NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role`, `username`, `password`, `name`, `surname`, `phone`, `email`, `created_at`, `ip`) VALUES
(1, 'client', 'MD', '$2y$10$hrRUJ9/deoWcqEHIg/AFn.UI6U6C6WNidE0G3YfXq050wFITO19.6', 'Marin', 'Dushku', '8564440486', 'marindushku@mail.adelphi.edu', '2023-12-08 10:23:09', '::1'),
(3, 'client', 'user', '$2y$10$I1gmDkuW7e4i5Uwn7SsD1uMTcczvK5LEP/WhRY.F8fxwrXjiHukQG', 'u', 'u', '7', 'j@m', '2023-12-08 10:24:26', '::1'),
(4, 'sitter', 'Sitter', '$2y$10$0QxRgj7Hc2trGMtNugq.peKcZ55Ef3T4/99MvFvR5ziDL5235mg5u', 's', 'itter', '11111111', 'sitter@mail', '2023-12-09 22:08:10', '::1'),
(5, 'sitter', 'Arl', '$2y$10$lo5CDDuysAaoe0J4SY6zl.JFT1bXnt1srvV.UbdrnNla.bGNRF41W', 'Arl', 'Arl', '2222222', 'arl@mail', '2023-12-11 09:02:56', '::1'),
(6, 'client', 'Balja', '$2y$10$E71jbj7J/lVJ8VJZbHKqiOf.m6cmA9.9PmqxmCWm.gLSJnUPlgq7m', 'Balja', 'pu', '123123123', 'balja@mail', '2023-12-11 13:00:45', '::1'),
(7, 'handler', 'Teacher', '$2y$10$hBT2Z3sJ2X3GSW.wcKpVQu20GgP5MqBxcUq79b0Nf0LYByNyRt.ke', 'Teacher', 'T', '12312356', 'teacher@mail', '2023-12-13 01:13:44', '::1'),
(8, 'handler', 'handler', '$2y$10$v.QoyCFAPZRV9d9hw2ltceuagaxMGqNz9WIk14Ifem17KltoZQCNC', 'handler', 'boss', '123456712', 'handle@mail', '2023-12-13 01:16:03', '::1'),
(11, 'client', 'Kaitlyn', '$2y$10$extsvDrKCquecLNAPsbuAONgPdifJe6zL8S1AajE7Xx5L8vInqGy.', 'Kaitlyn', 'Torres', '34241241', 'monkey@mail', '2023-12-13 03:40:30', '::1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`Comment_Id`),
  ADD KEY `Post_Id` (`Post_Id`);

--
-- Indexes for table `healthbehavior`
--
ALTER TABLE `healthbehavior`
  ADD KEY `Post_Id` (`Post_Id`);

--
-- Indexes for table `petinfo`
--
ALTER TABLE `petinfo`
  ADD PRIMARY KEY (`Post_Id`),
  ADD KEY `fk_username` (`Username`);

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
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `Comment_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `petinfo`
--
ALTER TABLE `petinfo`
  MODIFY `Post_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`Post_Id`) REFERENCES `petinfo` (`Post_Id`);

--
-- Constraints for table `healthbehavior`
--
ALTER TABLE `healthbehavior`
  ADD CONSTRAINT `healthbehavior_ibfk_1` FOREIGN KEY (`Post_Id`) REFERENCES `petinfo` (`Post_Id`) ON DELETE CASCADE;

--
-- Constraints for table `petinfo`
--
ALTER TABLE `petinfo`
  ADD CONSTRAINT `fk_username` FOREIGN KEY (`Username`) REFERENCES `users` (`username`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
