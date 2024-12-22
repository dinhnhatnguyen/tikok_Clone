-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: db:3306
-- Generation Time: Dec 22, 2024 at 03:37 AM
-- Server version: 9.1.0
-- PHP Version: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `php_docker`
--
CREATE DATABASE IF NOT EXISTS `php_docker` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `php_docker`;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int NOT NULL,
  `video_id` int NOT NULL,
  `user_id` int NOT NULL,
  `parent_id` int DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `follows`
--

CREATE TABLE `follows` (
  `follower_id` int NOT NULL,
  `followed_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `follows`
--

INSERT INTO `follows` (`follower_id`, `followed_id`, `created_at`) VALUES
(6, 5, '2024-12-22 03:12:47'),
(7, 5, '2024-12-22 03:14:22'),
(7, 6, '2024-12-22 03:14:19'),
(8, 7, '2024-12-22 03:18:17'),
(9, 5, '2024-12-22 03:21:19'),
(9, 6, '2024-12-22 03:21:14'),
(9, 7, '2024-12-22 03:21:10'),
(9, 8, '2024-12-22 03:21:05');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `video_id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `avatar_url`, `is_active`, `created_at`, `updated_at`) VALUES
(5, 'abc', 'test@test.j', '$2y$12$cbFiOJlI.CCzjab2PSeT6ugX9piohjKSGcysMGwDQEfHZyrYzMFpe', 'tên test nha', 'https://titokclone.s3.ap-southeast-1.amazonaws.com/avatars/IMG_0640.jpeg', 1, '2024-12-21 08:41:06', '2024-12-21 08:41:06'),
(6, 'test', 'tes@h.h', '$2y$12$Z6KqD/BWbsElc1lzra2Gh.bOmRjAqMrTabk9kPLFwVwzN0.iEhiZC', 'test lần nữa', 'https://titokclonephp.s3.ap-southeast-2.amazonaws.com/avatars/IMG_0207%203.JPG', 1, '2024-12-22 03:01:01', '2024-12-22 03:01:01'),
(7, 'nhatnguyen', 'fas@fasf.asfsdfs', '$2y$12$.n8rcBdZlTT6mFo8P8pheOuRmI3qmjbZSamvpgPGyz34/N/D4Ngzy', 'Nguyen dinh nhat', 'https://titokclonephp.s3.ap-southeast-2.amazonaws.com/avatars/IMG_4979.jpeg', 1, '2024-12-22 03:14:04', '2024-12-22 03:14:04'),
(8, 'Nhat', 'nhatnguyendinh18823@gmail.com', '$2y$12$jRgh8FTS4n8g1gU1LT8eXeW2Fp8HTI1WqxSZQqQXDLI/C7zTNLUUW', 'tên nhật nha', 'https://titokclonephp.s3.ap-southeast-2.amazonaws.com/avatars/IMG_5472.jpeg', 1, '2024-12-22 03:16:30', '2024-12-22 03:16:30'),
(9, 'hihi', 'hihi@hihi.hihi', '$2y$12$.tKVgIQ.MqzpB8ZGvBz2Zu0DvufBWwls3h17lGwvq4V4q/RP4OGKW', 'Tên hihi nha', 'https://titokclonephp.s3.ap-southeast-2.amazonaws.com/avatars/IMG_4671%202.JPG', 1, '2024-12-22 03:20:37', '2024-12-22 03:20:37'),
(12, 'nguyennhat', 'a@a.a', '$2y$12$APewpD/IwsxqhoNGGjnGi.ry8QFtm2oh0OE4vPLM1bYxLQk9OnAre', 'Nguyễn Nhật', 'https://titokclonephp.s3.ap-southeast-2.amazonaws.com/avatars/conChim.jpg', 1, '2024-12-22 03:37:21', '2024-12-22 03:37:21');

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `user_id` int NOT NULL,
  `filename` varchar(255) NOT NULL,
  `s3_url` varchar(255) NOT NULL,
  `thumbnail_url` varchar(255) DEFAULT NULL,
  `view_count` int DEFAULT '0',
  `is_featured` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`id`, `title`, `description`, `user_id`, `filename`, `s3_url`, `thumbnail_url`, `view_count`, `is_featured`, `created_at`, `updated_at`) VALUES
(9, 'Cảnh giác với Click Jacking', 'Cảnh giác với Click Jacking ở các trang web lạ ', 5, 'Download (1).mp4', 'https://titokclonephp.s3.ap-southeast-2.amazonaws.com/videos/Download%20%281%29.mp4', 'https://titokclonephp.s3.ap-southeast-2.amazonaws.com/thumbnails/676783257fd65.jpg', 0, 0, '2024-12-22 03:10:29', '2024-12-22 03:10:29'),
(10, 'Web học data', 'Giới thiệu một số web học data hay', 5, 'Download (2).mp4', 'https://titokclonephp.s3.ap-southeast-2.amazonaws.com/videos/Download%20%282%29.mp4', 'https://titokclonephp.s3.ap-southeast-2.amazonaws.com/thumbnails/6767834b23eec.jpg', 0, 0, '2024-12-22 03:11:07', '2024-12-22 03:11:07'),
(11, 'Cách đưa web lên internetnet', 'Hướng dẫn đưa một dự án web ở local lên internet', 6, 'Download (3).mp4', 'https://titokclonephp.s3.ap-southeast-2.amazonaws.com/videos/Download%20%283%29.mp4', 'https://titokclonephp.s3.ap-southeast-2.amazonaws.com/thumbnails/676783a7df6cc.jpg', 0, 0, '2024-12-22 03:12:40', '2024-12-22 03:13:06'),
(12, 'Ảnh giữ chỗ ', 'Giới thiệu cách đặt ảnh giữ chỗ làm ảnh tạm khi dự án lập trình', 7, 'Download (4).mp4', 'https://titokclonephp.s3.ap-southeast-2.amazonaws.com/videos/Download%20%284%29.mp4', 'https://titokclonephp.s3.ap-southeast-2.amazonaws.com/thumbnails/6767844d36766.jpg', 0, 0, '2024-12-22 03:15:25', '2024-12-22 03:15:25'),
(13, 'Github', 'Giới thiệu 15 lệnh git cơ bản', 8, 'Download (5).mp4', 'https://titokclonephp.s3.ap-southeast-2.amazonaws.com/videos/Download%20%285%29.mp4', 'https://titokclonephp.s3.ap-southeast-2.amazonaws.com/thumbnails/676784e394949.jpg', 0, 0, '2024-12-22 03:17:55', '2024-12-22 03:17:55'),
(14, 'Tự động gợi ý địa chỉ', 'Hướng dẫn cài đặt gợi ý địa chỉ sử dụng goong map API', 8, 'Download.mp4', 'https://titokclonephp.s3.ap-southeast-2.amazonaws.com/videos/Download.mp4', 'https://titokclonephp.s3.ap-southeast-2.amazonaws.com/thumbnails/67678538ac354.jpg', 0, 0, '2024-12-22 03:19:21', '2024-12-22 03:19:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `video_id` (`video_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `follows`
--
ALTER TABLE `follows`
  ADD PRIMARY KEY (`follower_id`,`followed_id`),
  ADD KEY `followed_id` (`followed_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`video_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `follows`
--
ALTER TABLE `follows`
  ADD CONSTRAINT `follows_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `follows_ibfk_2` FOREIGN KEY (`followed_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `videos`
--
ALTER TABLE `videos`
  ADD CONSTRAINT `videos_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
