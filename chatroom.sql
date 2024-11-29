-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2024 at 11:03 PM
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
-- Database: `chatroom`
--

-- --------------------------------------------------------

--
-- Table structure for table `chatrooms`
--

CREATE TABLE `chatrooms` (
  `chatroomId` int(10) NOT NULL,
  `chatroomCode` varchar(10) NOT NULL,
  `chatroomName` varchar(255) NOT NULL,
  `logo` varchar(255) NOT NULL DEFAULT 'chatroomLogos/aclc-blue.png',
  `dateCreated` datetime NOT NULL DEFAULT current_timestamp(),
  `lastActive` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chatrooms`
--

INSERT INTO `chatrooms` (`chatroomId`, `chatroomCode`, `chatroomName`, `logo`, `dateCreated`, `lastActive`) VALUES
(19, 'HBe42F9yLv', 'rain', 'chatroomLogos/1718418016_det.png', '2024-06-15 10:20:16', '2024-06-15 10:21:12'),
(20, '7UY9UyXkaq', 'eauigfosdaifhhslkd', 'chatroomLogos/1718421343_moriope.png', '2024-06-15 11:15:43', '2024-06-15 12:48:56'),
(21, '4xNcCY6aUN', 'asd', 'chatroomLogos/aclc-blue.png', '2024-06-15 13:23:52', '2024-06-15 13:23:52'),
(22, 'KZqOglSsYZ', 'aclc', 'chatroomLogos/aclc-blue.png', '2024-06-15 14:35:47', '2024-06-15 14:37:04'),
(23, 'AOOJfm5at6', 'aclc', 'chatroomLogos/aclc-blue.png', '2024-06-15 14:44:03', '2024-06-15 14:44:17');

-- --------------------------------------------------------

--
-- Table structure for table `invites`
--

CREATE TABLE `invites` (
  `inviteCode` varchar(10) NOT NULL,
  `chatroomId` int(10) NOT NULL,
  `dateCreated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `memberId` int(10) NOT NULL,
  `isAdmin` int(1) NOT NULL DEFAULT 0,
  `userId` int(10) NOT NULL,
  `chatroomId` int(10) NOT NULL,
  `dateJoined` datetime NOT NULL DEFAULT current_timestamp(),
  `status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`memberId`, `isAdmin`, `userId`, `chatroomId`, `dateJoined`, `status`) VALUES
(32, 1, 13, 19, '2024-06-15 10:20:16', 0),
(33, 0, 14, 19, '2024-06-15 10:20:49', 1),
(34, 0, 15, 19, '2024-06-15 10:21:12', 0),
(35, 1, 13, 20, '2024-06-15 11:15:43', 0),
(36, 0, 14, 20, '2024-06-15 11:16:15', 0),
(37, 0, 14, 20, '2024-06-15 12:19:37', 0),
(38, 0, 14, 20, '2024-06-15 12:48:56', 0),
(39, 1, 13, 21, '2024-06-15 13:23:52', 1),
(40, 1, 16, 22, '2024-06-15 14:35:47', 1),
(41, 0, 13, 22, '2024-06-15 14:36:26', 0),
(42, 1, 13, 23, '2024-06-15 14:44:03', 1);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `messageId` int(255) NOT NULL,
  `userId` int(10) NOT NULL,
  `chatroomId` int(10) NOT NULL,
  `message` text DEFAULT NULL,
  `uploadFilePath` varchar(255) DEFAULT NULL,
  `dateSent` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`messageId`, `userId`, `chatroomId`, `message`, `uploadFilePath`, `dateSent`) VALUES
(98, 0, 19, 'leaf has created the chatroom.', NULL, '2024-06-15 10:20:16'),
(99, 0, 19, 'leafa has entered the chat.', NULL, '2024-06-15 10:20:49'),
(100, 0, 19, 'hiiiii has entered the chat.', NULL, '2024-06-15 10:21:12'),
(101, 0, 19, 'hiiiii has been kicked from the chat.', NULL, '2024-06-15 10:21:26'),
(102, 0, 19, 'leaf has become the admin.', NULL, '2024-06-15 10:22:28'),
(103, 0, 19, 'leaf has left the chat.', NULL, '2024-06-15 10:22:28'),
(104, 0, 20, 'leaf has created the chatroom.', NULL, '2024-06-15 11:15:43'),
(105, 0, 20, 'leafa has entered the chat.', NULL, '2024-06-15 11:16:15'),
(106, 14, 20, 'asdasda', NULL, '2024-06-15 11:16:27'),
(107, 14, 20, 'ewrwer', NULL, '2024-06-15 11:16:30'),
(108, 0, 20, 'leafa has left the chat.', NULL, '2024-06-15 11:16:54'),
(109, 13, 20, 'asdasd', NULL, '2024-06-15 11:17:14'),
(110, 13, 20, NULL, 'messageUploads/1718421443_FBK.jpg', '2024-06-15 11:17:23'),
(111, 13, 20, 'adassd', 'messageUploads/1718421453_kiwawa.png', '2024-06-15 11:17:33'),
(112, 0, 20, 'leafa has entered the chat.', NULL, '2024-06-15 12:19:37'),
(113, 0, 20, 'leafa has left the chat.', NULL, '2024-06-15 12:19:42'),
(114, 13, 20, 'adfjaf', NULL, '2024-06-15 12:21:13'),
(115, 0, 20, 'leafa has entered the chat.', NULL, '2024-06-15 12:48:56'),
(116, 0, 20, 'leafa has been kicked from the chat.', NULL, '2024-06-15 12:49:06'),
(117, 0, 21, 'leaf has created the chatroom.', NULL, '2024-06-15 13:23:52'),
(118, 0, 20, 'leaf has left the chat.', NULL, '2024-06-15 13:25:48'),
(119, 0, 22, 'ailyn has created the chatroom.', NULL, '2024-06-15 14:35:47'),
(120, 0, 22, 'leaf has entered the chat.', NULL, '2024-06-15 14:36:26'),
(121, 13, 22, NULL, 'messageUploads/1718433424_HANI BLANCO - Template lymphatic-immune worksheet.pdf', '2024-06-15 14:37:04'),
(122, 0, 22, 'leaf has been kicked from the chat.', NULL, '2024-06-15 14:37:59'),
(123, 0, 23, 'leaf has created the chatroom.', NULL, '2024-06-15 14:44:03'),
(124, 13, 23, 'Hii', NULL, '2024-06-15 14:44:07'),
(125, 13, 23, NULL, 'messageUploads/1718433857_me.jpg', '2024-06-15 14:44:17');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userId` int(10) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `imagePath` varchar(255) NOT NULL DEFAULT 'userImages/default.jpg',
  `status` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userId`, `username`, `password`, `email`, `imagePath`, `status`) VALUES
(13, 'leaf', '$2y$10$TDSEuV4h1qW7ivMbn03O/.5OLEKIXVWUmQG2/4tIEIN2jUysV4Vli', 'wildriftnadis@gmail.com', 'userImages/1718429227_me.jpg', 1),
(14, 'leafa', '$2y$10$8vzhjFLBJE5oyJD7uSwhwOmV6dFIF9lE0sMvtOaEc/b2r5HE2hai.', 'mandbvcf@gmail.com', 'userImages/default.jpg', 1),
(15, 'hiiiii', '$2y$10$7ANFx8QaqkzMziKId7ZloeFhEANoSkDNbHBSQhmAHy0dIdu71kF3e', 'khalifa@aclcbukidnon.com', 'userImages/default.jpg', 1),
(16, 'ailyn', '$2y$10$lrpQz/zYfKPN81I2YYeToeWrJdqvAft/JjOqvFXh4UrKdRMwONjwK', 'ibbay.ailyn@gmail.com', 'userImages/1718433534_pumpking.png', 1),
(17, 'NotKhalifa', '$2y$10$mDluyhw2w9.ZJapkazrSW.DFyZr.m0kJ34xoYnb4kNjVXd/G8OOzS', 'manbvcf@gmail.com', 'userImages/default.jpg', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chatrooms`
--
ALTER TABLE `chatrooms`
  ADD PRIMARY KEY (`chatroomId`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`memberId`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`messageId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chatrooms`
--
ALTER TABLE `chatrooms`
  MODIFY `chatroomId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `memberId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `messageId` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userId` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
