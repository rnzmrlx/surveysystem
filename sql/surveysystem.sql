-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 21, 2026 at 04:21 PM
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
-- Database: `surveysystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `id` int(11) NOT NULL,
  `response_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `answers`
--

INSERT INTO `answers` (`id`, `response_id`, `question_id`, `answer_text`) VALUES
(11, 9, 16, 'z'),
(12, 10, 17, '5'),
(14, 12, 21, 'cscs'),
(15, 13, 22, 'lkwdqwokd'),
(16, 14, 25, 'wrw'),
(17, 15, 26, 'dasdasd'),
(18, 16, 31, 'vfvwfvfvwv');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `type` enum('accessed','answered','auto_closed','user_registered') NOT NULL,
  `message` text NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `survey_title` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `message`, `user_id`, `survey_id`, `survey_title`, `is_read`, `created_at`) VALUES
(1, 'auto_closed', 'Survey \"End of term evaluation\" has expired and was automatically closed.', NULL, 16, 'End of term evaluation', 1, '2026-05-02 23:30:13'),
(2, 'auto_closed', 'Survey \"End of term evaluation\" has expired and was automatically closed.', NULL, 16, 'End of term evaluation', 1, '2026-05-02 23:34:38'),
(3, 'auto_closed', 'Survey \"geeg\" has expired and was automatically closed.', NULL, 20, 'geeg', 1, '2026-05-08 12:49:09'),
(4, 'auto_closed', 'Survey \"geeg\" has expired and was automatically closed.', NULL, 20, 'geeg', 1, '2026-05-08 12:51:36'),
(5, 'answered', 'Someone submitted a response to: \"dhefehfofje\"', NULL, 19, 'dhefehfofje', 1, '2026-05-08 12:57:35'),
(6, 'auto_closed', 'Survey \"geeg\" has expired and was automatically closed.', NULL, 20, 'geeg', 1, '2026-05-08 23:35:05'),
(7, 'auto_closed', 'Survey \"dhefehfofje\" has expired and was automatically closed.', NULL, 19, 'dhefehfofje', 1, '2026-05-08 23:35:17'),
(8, 'answered', 'Someone submitted a response to: \"fkkfoofa\"', NULL, 21, 'fkkfoofa', 1, '2026-05-09 15:31:59'),
(9, 'answered', 'Someone submitted a response to: \"ksjosfpfjf\"', NULL, 22, 'ksjosfpfjf', 1, '2026-05-09 15:53:07'),
(10, 'answered', 'Someone submitted a response to: \"fefnowejvew\"', NULL, 25, 'fefnowejvew', 1, '2026-05-21 21:38:03');

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE `options` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `option_text` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `survey_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `type` enum('text','textarea','radio','checkbox','scale') DEFAULT NULL,
  `question_type` varchar(50) NOT NULL DEFAULT 'text',
  `options` text DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `survey_id`, `question_text`, `type`, `question_type`, `options`, `is_required`) VALUES
(16, 14, 'csc', NULL, 'text', '[]', 1),
(17, 15, 'is jejo pangit', NULL, 'scale', '[]', 1),
(19, 16, 'how is your teacher\'s performance? rate', NULL, 'scale', '[]', 0),
(21, 18, 'xqwdqwd', NULL, 'text', '[]', 1),
(22, 19, 'ghhth', NULL, 'text', '[]', 1),
(24, 20, 'fwgeg', NULL, 'textarea', '[]', 1),
(25, 21, 'afsdfgsdg', NULL, 'text', '[]', 1),
(26, 22, 'jojoj', NULL, 'text', '[]', 1),
(27, 23, 'vddv', NULL, 'text', '[]', 1),
(30, 24, 'dfwefaf', NULL, 'text', '[]', 1),
(31, 25, 'dvvsv', NULL, 'text', '[]', 1),
(32, 26, 'nnetn', NULL, 'text', '[]', 1);

-- --------------------------------------------------------

--
-- Table structure for table `responses`
--

CREATE TABLE `responses` (
  `id` int(11) NOT NULL,
  `survey_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `responses`
--

INSERT INTO `responses` (`id`, `survey_id`, `user_id`, `submitted_at`) VALUES
(9, 14, 14, '2026-04-25 05:59:36'),
(10, 15, 14, '2026-04-30 03:55:17'),
(11, 16, 14, '2026-04-30 12:05:29'),
(12, 18, 14, '2026-05-02 14:52:59'),
(13, 19, 14, '2026-05-08 04:57:35'),
(14, 21, 14, '2026-05-09 07:31:59'),
(15, 22, 14, '2026-05-09 07:53:07'),
(16, 25, 14, '2026-05-21 13:38:03');

-- --------------------------------------------------------

--
-- Table structure for table `surveys`
--

CREATE TABLE `surveys` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('pending','published','closed') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category_id` int(11) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `surveys`
--

INSERT INTO `surveys` (`id`, `user_id`, `title`, `description`, `status`, `created_at`, `category_id`, `start_date`, `end_date`) VALUES
(14, 8, 'hvzhvzvsv', '', 'closed', '2026-04-25 05:59:15', NULL, '2026-04-25 01:00:00', '2026-04-25 14:30:00'),
(15, 8, 'is jejo pangit?', '', 'closed', '2026-04-30 03:43:19', NULL, '2026-04-30 12:00:00', '2026-04-30 12:30:00'),
(16, 8, 'End of term evaluation', 'rate teachers', 'published', '2026-04-30 12:04:51', NULL, '2026-05-30 00:00:00', '2026-05-31 00:00:00'),
(18, 8, 'skspsk', '', 'published', '2026-05-02 14:47:10', NULL, '0000-00-00 00:00:00', '2026-05-26 13:00:00'),
(19, 8, 'dhefehfofje', '', 'closed', '2026-05-02 15:08:13', NULL, '0000-00-00 00:00:00', '2026-05-31 13:00:00'),
(20, 8, 'geeg', '', 'closed', '2026-05-02 15:27:26', NULL, '2026-05-08 14:39:00', '2026-05-13 13:00:00'),
(21, 8, 'fkkfoofa', '', 'published', '2026-05-09 05:26:18', NULL, '0000-00-00 00:00:00', '2026-05-30 13:00:00'),
(22, 8, 'ksjosfpfjf', '', 'published', '2026-05-09 07:52:01', NULL, '0000-00-00 00:00:00', '2026-06-05 13:00:00'),
(23, 8, 'vdv', '', 'closed', '2026-05-20 04:32:11', NULL, '2026-05-20 01:00:00', '2026-05-20 13:00:00'),
(24, 8, 'mkdjfhihf', '', 'closed', '2026-05-21 13:30:11', NULL, '2026-05-21 01:00:00', '2026-05-21 13:00:00'),
(25, 8, 'fefnowejvew', '', 'published', '2026-05-21 13:37:28', NULL, '2026-05-21 01:00:00', '2026-05-21 23:00:00'),
(26, 8, 'fjefiejiv', '', 'published', '2026-05-21 13:50:13', NULL, '2026-05-21 01:00:00', '2026-05-21 23:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `firstName` varchar(100) NOT NULL,
  `middleName` varchar(100) DEFAULT NULL,
  `lastName` varchar(100) NOT NULL,
  `emailAddress` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `street` varchar(255) DEFAULT NULL,
  `barangay` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `phone` varchar(25) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` varchar(30) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'Philippines',
  `avatar` varchar(255) DEFAULT NULL,
  `notif_email` tinyint(1) DEFAULT 1,
  `notif_sms` tinyint(1) DEFAULT 0,
  `notif_system` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `uuid`, `firstName`, `middleName`, `lastName`, `emailAddress`, `username`, `password`, `street`, `barangay`, `city`, `role`, `phone`, `dob`, `gender`, `bio`, `province`, `zip_code`, `country`, `avatar`, `notif_email`, `notif_sms`, `notif_system`, `last_login`) VALUES
(8, '52084f11-ec70-4d8b-9272-034ce9a46a0d', 'Abigail', 'Gesulga', 'Lapinid', 'abigailjim.lapinid@gmail.com', 'abigail', '$2y$10$LOcDDRqWq9q1hF2GHMKs8uxAZr8bzKCienE1yfYDTe4McB6B/0Hi2', 'hayes st.', 'cugman', 'cagayan de oro city', 'admin', NULL, NULL, NULL, NULL, NULL, NULL, 'Philippines', 'app/uploads/avatars/admin/avatar_8_1778051402.png', 1, 0, 1, NULL),
(14, 'f7202801-7c0b-4f16-96f1-fcd9db4cb0cd', 'Aaliyah', '', 'Cabotaje', 'rea@gmail.com', 'aaliyah', '$2y$10$HODfFIgpWBSvf1lK93BUYuh4ArpX9ZOQCN/onf7ZQtJVtvSNptqCG', '', '', '', 'user', '+639360877015', '2026-04-30', '', '', '', '', 'Philippines', 'app/uploads/avatars/user/avatar_14_1778051474.png', 0, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_notifications`
--

CREATE TABLE `user_notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('survey_published','closing_soon','survey_closed','inactivity','response_recorded','pending_survey') NOT NULL,
  `message` text NOT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `survey_title` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_notifications`
--

INSERT INTO `user_notifications` (`id`, `user_id`, `type`, `message`, `survey_id`, `survey_title`, `is_read`, `created_at`) VALUES
(7, 14, 'response_recorded', 'Your response to \"dhefehfofje\" has been successfully recorded. Thank you!', 19, 'dhefehfofje', 1, '2026-05-08 12:57:35'),
(10, 14, 'response_recorded', 'Your response to \"fkkfoofa\" has been successfully recorded. Thank you!', 21, 'fkkfoofa', 1, '2026-05-09 15:31:59'),
(11, 14, 'response_recorded', 'Your response to \"ksjosfpfjf\" has been successfully recorded. Thank you!', 22, 'ksjosfpfjf', 1, '2026-05-09 15:53:07'),
(12, 14, 'response_recorded', 'Your response to \"fefnowejvew\" has been successfully recorded. Thank you!', 25, 'fefnowejvew', 1, '2026-05-21 21:38:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_answers_responses` (`response_id`),
  ADD KEY `fk_answers_questions` (`question_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_options_question` (`question_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_questions_surveys` (`survey_id`);

--
-- Indexes for table `responses`
--
ALTER TABLE `responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_responses_surveys` (`survey_id`),
  ADD KEY `fk_responses_users` (`user_id`);

--
-- Indexes for table `surveys`
--
ALTER TABLE `surveys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_survey` (`user_id`),
  ADD KEY `fk_surveys_categories` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_survey` (`survey_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `responses`
--
ALTER TABLE `responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `surveys`
--
ALTER TABLE `surveys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `user_notifications`
--
ALTER TABLE `user_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `fk_answers_questions` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_answers_responses` FOREIGN KEY (`response_id`) REFERENCES `responses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `options`
--
ALTER TABLE `options`
  ADD CONSTRAINT `fk_options_question` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_options_questions` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `fk_questions_surveys` FOREIGN KEY (`survey_id`) REFERENCES `surveys` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `responses`
--
ALTER TABLE `responses`
  ADD CONSTRAINT `fk_responses_surveys` FOREIGN KEY (`survey_id`) REFERENCES `surveys` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_responses_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `surveys`
--
ALTER TABLE `surveys`
  ADD CONSTRAINT `fk_surveys_categories` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_survey` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
