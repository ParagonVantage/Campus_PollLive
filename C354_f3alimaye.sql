-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 07, 2025 at 04:12 AM
-- Server version: 8.0.44-0ubuntu0.24.04.1
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `C354_f3alimaye`
--

-- --------------------------------------------------------

--
-- Table structure for table `Persons`
--

CREATE TABLE `Persons` (
  `SSN` int NOT NULL,
  `FirstName` varchar(30) NOT NULL,
  `LastName` varchar(30) NOT NULL,
  `Age` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Persons`
--

INSERT INTO `Persons` (`SSN`, `FirstName`, `LastName`, `Age`) VALUES
(88888, 'Dave', 'Smith', 23),
(999888777, 'Tom', 'Davis', 18);

-- --------------------------------------------------------

--
-- Table structure for table `POLLS`
--

CREATE TABLE `POLLS` (
  `PollID` int NOT NULL,
  `CreatorUsername` varchar(50) NOT NULL,
  `Question` varchar(255) NOT NULL,
  `Description` text,
  `Category` varchar(50) DEFAULT NULL,
  `CreatedAt` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `POLLS`
--

INSERT INTO `POLLS` (`PollID`, `CreatorUsername`, `Question`, `Description`, `Category`, `CreatedAt`) VALUES
(1, 't00732070', 'What is your program', '', 'Campus', '2025-11-17 10:30:01'),
(2, 'john', 'What is your favorite course?', '', 'Courses', '2025-11-25 18:27:21'),
(3, 'Jake', 'What is your favorite TRUSU Club', '', 'Campus', '2025-11-25 18:44:42'),
(4, 'Max', 'What is your year of study?', '', 'Campus', '2025-11-25 18:48:28'),
(5, 'Bob', 'How many friends have you made?', '', 'Campus', '2025-11-25 19:03:03'),
(6, 't00732070', 'What is your favorite TRUSU event', '', 'Campus', '2025-11-26 18:11:28'),
(7, 't00732070', 'What do you like the most?', 'Wolfpack teams', 'Sports', '2025-11-26 18:35:17'),
(8, 'htkuzipa', 'What is your favourite anime', 'Be real', 'Campus', '2025-11-28 09:39:35'),
(9, 'schloingus', 'how do you feel about the recent construction on campus', '', 'Campus', '2025-11-28 12:14:45'),
(10, 'abc', 'How big is the universe?', '', 'Science', '2025-12-04 11:11:07');

-- --------------------------------------------------------

--
-- Table structure for table `POLL_OPTIONS`
--

CREATE TABLE `POLL_OPTIONS` (
  `OptionID` int NOT NULL,
  `PollID` int NOT NULL,
  `OptionText` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `POLL_OPTIONS`
--

INSERT INTO `POLL_OPTIONS` (`OptionID`, `PollID`, `OptionText`) VALUES
(1, 1, 'Science'),
(2, 1, 'Computer Science'),
(3, 1, 'Arts & Education'),
(4, 1, 'Business'),
(5, 2, 'MATH 3120'),
(6, 2, 'COMP 3610'),
(7, 2, 'MATH 4430'),
(8, 2, 'COMP 3540'),
(9, 3, 'TRU CS CLUB'),
(10, 3, 'TRU Running Club'),
(11, 3, 'TRU ANIME CLUB'),
(12, 3, 'TRU Hiking Club'),
(13, 4, '1'),
(14, 4, '2'),
(15, 4, '3'),
(16, 4, '4'),
(17, 5, '1-5'),
(18, 5, '6-10'),
(19, 5, '11-25'),
(20, 5, '26-100'),
(21, 6, 'Back to School BBQ'),
(22, 6, 'MOVIE NIGHT'),
(23, 6, 'WINTER FEST'),
(24, 6, 'OPEN HOUSE'),
(25, 7, 'Volleyball'),
(26, 7, 'Football'),
(27, 7, 'Basketball'),
(28, 8, 'Bleach'),
(29, 8, 'One Piece'),
(30, 8, 'Naruto'),
(31, 8, 'Fairy Tale'),
(32, 9, 'hate it'),
(33, 9, 'love it'),
(34, 9, 'don\'t care'),
(35, 10, 'Big'),
(36, 10, 'Very big'),
(37, 10, 'Huge');

-- --------------------------------------------------------

--
-- Table structure for table `POLL_VOTES`
--

CREATE TABLE `POLL_VOTES` (
  `VoteID` int NOT NULL,
  `PollID` int NOT NULL,
  `OptionID` int NOT NULL,
  `Username` varchar(50) NOT NULL,
  `VotedAt` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `POLL_VOTES`
--

INSERT INTO `POLL_VOTES` (`VoteID`, `PollID`, `OptionID`, `Username`, `VotedAt`) VALUES
(1, 1, 2, 't00732070', '2025-11-17 10:30:22'),
(2, 2, 7, 'john', '2025-11-25 18:27:32'),
(3, 1, 2, 'john', '2025-11-25 18:27:38'),
(4, 5, 18, 'Bob', '2025-11-25 19:03:30'),
(5, 4, 15, 'Bob', '2025-11-25 19:03:37'),
(6, 3, 11, 'Bob', '2025-11-25 19:03:43'),
(7, 2, 8, 'Bob', '2025-11-25 19:03:50'),
(8, 1, 1, 'Bob', '2025-11-25 19:03:57'),
(9, 5, 19, 't00732070', '2025-11-26 09:23:14'),
(10, 4, 14, 't00732070', '2025-11-26 09:23:20'),
(11, 3, 12, 't00732070', '2025-11-26 09:23:24'),
(12, 2, 7, 't00732070', '2025-11-26 09:23:32'),
(13, 5, 19, 'Max', '2025-11-26 11:57:18'),
(14, 6, 24, 't00732070', '2025-11-26 18:34:08'),
(15, 8, 29, 'htkuzipa', '2025-11-28 09:39:48'),
(16, 8, 29, 't00732070', '2025-11-28 10:05:05'),
(17, 7, 25, 't00732070', '2025-12-02 10:53:35'),
(18, 10, 37, 'abcd', '2025-12-04 11:13:18'),
(19, 8, 31, 'abcd', '2025-12-04 11:13:50'),
(20, 7, 27, 'abc', '2025-12-04 11:15:29'),
(21, 10, 37, 'abc', '2025-12-04 11:16:22');

-- --------------------------------------------------------

--
-- Table structure for table `USERS`
--

CREATE TABLE `USERS` (
  `ID` int NOT NULL,
  `USERNAME` varchar(20) NOT NULL,
  `DisplayName` varchar(100) DEFAULT NULL,
  `PASSWORD` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `EMAIL` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Bio` text,
  `DATE` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `USERS`
--

INSERT INTO `USERS` (`ID`, `USERNAME`, `DisplayName`, `PASSWORD`, `EMAIL`, `Bio`, `DATE`) VALUES
(1, 'John', NULL, 'thejohn', 'John@tru.ca', NULL, 20080922),
(2, 'Bob', NULL, 'secretbob', 'Bob@tru.ca', NULL, 20100921),
(3, 'Max', NULL, 'secretmax', 'Max@tru.ca', NULL, 20180928),
(4, 'Jake', NULL, 'secretjk', 'Jake@tru.ca', NULL, 20080718),
(5, 'Matt', NULL, 'secretmt', 'Matt@tru.ca', NULL, 20050822),
(6, 'jdoe', NULL, '', 'jdoe@tru.ca', NULL, 2025),
(7, 'sam', NULL, 'wonderfulworld', 'sam@tru.ca', NULL, 2025),
(8, 'frank', NULL, 'wonderfulworld', 'frank@tru.ca', NULL, 2025),
(9, 't00732070', 'Agneya', 'Rycbar999*', 't00732070@mytru.ca', '', 20251022),
(10, 't00732071', NULL, 'Rycbar999', 'vantageparagon@gmail.com', NULL, 20251022),
(13, 'abc12', 'abc12', 'edbd1887e772e13c251f688a5f10c1ffbb67960d', 'abc12@tru.ca', 'abc12', 20251126),
(23, 'tim', 'hortons', 'timhortons@gmail.com', '', '', 20251126),
(24, 'tru', 'trusu', 'trusu@mytru.ca', '', '', 20251126),
(26, 'htkuzipa', '', 'qwerty', 'hkuzipa@gmail.com', '', 20251128),
(27, 'schloingus', '', 'boingus', 't00728171@mytru.ca', '', 20251128),
(28, 'abc', '', 'abc', 'abc@tru.ca', '', 20251204),
(29, 'abcd', '', 'abcd', 'abcd@tru.ca', '', 20251204);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Persons`
--
ALTER TABLE `Persons`
  ADD PRIMARY KEY (`SSN`);

--
-- Indexes for table `POLLS`
--
ALTER TABLE `POLLS`
  ADD PRIMARY KEY (`PollID`);

--
-- Indexes for table `POLL_OPTIONS`
--
ALTER TABLE `POLL_OPTIONS`
  ADD PRIMARY KEY (`OptionID`),
  ADD KEY `PollID` (`PollID`);

--
-- Indexes for table `POLL_VOTES`
--
ALTER TABLE `POLL_VOTES`
  ADD PRIMARY KEY (`VoteID`),
  ADD UNIQUE KEY `uq_one_vote_per_user` (`PollID`,`Username`),
  ADD KEY `fk_votes_option` (`OptionID`);

--
-- Indexes for table `USERS`
--
ALTER TABLE `USERS`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `POLLS`
--
ALTER TABLE `POLLS`
  MODIFY `PollID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `POLL_OPTIONS`
--
ALTER TABLE `POLL_OPTIONS`
  MODIFY `OptionID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `POLL_VOTES`
--
ALTER TABLE `POLL_VOTES`
  MODIFY `VoteID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `USERS`
--
ALTER TABLE `USERS`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `POLL_OPTIONS`
--
ALTER TABLE `POLL_OPTIONS`
  ADD CONSTRAINT `POLL_OPTIONS_ibfk_1` FOREIGN KEY (`PollID`) REFERENCES `POLLS` (`PollID`) ON DELETE CASCADE;

--
-- Constraints for table `POLL_VOTES`
--
ALTER TABLE `POLL_VOTES`
  ADD CONSTRAINT `fk_votes_option` FOREIGN KEY (`OptionID`) REFERENCES `POLL_OPTIONS` (`OptionID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_votes_poll` FOREIGN KEY (`PollID`) REFERENCES `POLLS` (`PollID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
