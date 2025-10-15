-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 08, 2025 at 02:06 PM
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
-- Database: `online_voting_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `fullname`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'Asogwa Victor Nnamdi', 'ASOVIC', 'asovic2016@gmail.com', '$2y$10$VJYXiDqOmVdS1BDRdI5d3ehFcC4ktET00z5JWRrdiavaPZh78ZPpS', '2025-10-07 02:08:01'),
(2, 'Emmanuel Chinecherem', 'RichieTech', 'chincherem2018@gmail.com', '$2y$10$drCTVYQccBGdmYQ2B.5st.7oPTXbOYXzQ4AM4dadmB8lgLedIsV0q', '2025-10-08 11:56:28');

-- --------------------------------------------------------

--
-- Table structure for table `contestants`
--

CREATE TABLE `contestants` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `dob` date NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(20) NOT NULL,
  `state` varchar(50) DEFAULT NULL,
  `password` varchar(20) NOT NULL,
  `rpassword` varchar(20) NOT NULL,
  `party` varchar(100) NOT NULL,
  `passport` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contestants`
--

INSERT INTO `contestants` (`id`, `name`, `username`, `dob`, `phone`, `email`, `state`, `password`, `rpassword`, `party`, `passport`) VALUES
(4, 'ASOGWA VICTOR', 'Faith', '2025-10-02', '07038835237', 'admin4@mobilestore.c', NULL, '$2y$10$PdVhoZ0gazx2z', '', 'LP', 'uploads/passport.jpg'),
(5, 'victor nwachukwu', 'nwachukwu448', '2025-10-04', '07067770818', 'nwachukwu448@gmail.c', NULL, '$2y$10$ScJy33Dfg3SgP', '', 'PDP', 'uploads/hero4.jpg'),
(6, 'Emmanuel Chinecherem Chigbo', 'RichieTech', '1999-07-07', '09075112246', 'chincherem2018@gmail', NULL, '$2y$10$hL7BqaLgi7fcK', '', 'YPP', 'uploads/coat of arm.png'),
(7, 'Asogwa Frank', 'Francis', '2025-10-03', '07038835237', 'admin10@mobilestore.', NULL, '$2y$10$0aC9aMastvTm8', '', 'NNPP', 'uploads/'),
(9, 'Power House', 'power', '2025-10-10', '07038835237', 'admin3@mobilestore.c', NULL, '$2y$10$gCWIrkFURR9p6', '', 'PRP', 'uploads/image3.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `voters`
--

CREATE TABLE `voters` (
  `id` int(11) NOT NULL,
  `voter_id` varchar(50) DEFAULT NULL,
  `fullname` varchar(150) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `nin` varchar(50) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `lga` varchar(100) DEFAULT NULL,
  `dob` date NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(20) NOT NULL,
  `rpassword` varchar(20) NOT NULL,
  `passport` varchar(255) NOT NULL,
  `unique_id` varchar(10) NOT NULL,
  `has_voted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `voters`
--

INSERT INTO `voters` (`id`, `voter_id`, `fullname`, `name`, `username`, `phone`, `nin`, `state`, `lga`, `dob`, `email`, `password`, `rpassword`, `passport`, `unique_id`, `has_voted`) VALUES
(1, NULL, NULL, 'Asogwa Victor Nnamdi', 'ASOVIC', '07038835237', NULL, NULL, NULL, '1990-05-02', 'asovic2016@gmail.com', '$2y$10$6lDJJ2KJuhwmZ', 'Nnamdi25', 'uploads/passport.jpg', '53667', 0),
(4, NULL, NULL, 'Asogwa Victor Nnamdi', 'frank', '07038835237', NULL, NULL, NULL, '2025-10-02', 'admin2@mobilestore.com', '$2y$10$/Un6ujGjj68VL', '', 'uploads/1759905183_passport.jpg', 'VOT06CA4F', 0),
(6, NULL, NULL, 'victor nwachukwu', 'nwachukwu448', '07067770818', NULL, NULL, NULL, '2025-10-27', 'nwachukwu448@gmail.com', '$2y$10$Iy7GqgWJlEgwm', '', 'uploads/1759912665_hero4.jpg', 'VOT7B366E', 0),
(7, NULL, NULL, 'Emmanuel Chinecherem Chigbo', 'RichieTech', '09075112246', NULL, NULL, NULL, '2025-10-03', 'chincherem2018@gmail.com', '$2y$10$262UFK97LkRmz', '', 'uploads/1759919198_hero4.jpg', 'VOTA7E3BF', 0),
(8, NULL, NULL, 'Power House', 'power', '07038835237', NULL, NULL, NULL, '2025-10-11', 'admin3@mobilestore.com', '$2y$10$eLabdgjc.w2Ri', '', 'uploads/1759924174_image3.jpeg', 'VOT65488C', 0);

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL,
  `voter_id` varchar(10) NOT NULL,
  `contestant_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`id`, `voter_id`, `contestant_id`) VALUES
(4, '53667', 4),
(5, 'VOT06CA4F', 4),
(6, 'VOT7B366E', 5),
(7, 'VOTA7E3BF', 6),
(8, 'VOT65488C', 9);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `contestants`
--
ALTER TABLE `contestants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `voters`
--
ALTER TABLE `voters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `voter_id` (`voter_id`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `votes_ibfk_1` (`contestant_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `contestants`
--
ALTER TABLE `contestants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `voters`
--
ALTER TABLE `voters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`contestant_id`) REFERENCES `contestants` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
