SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+08:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `final3`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `hired` tinyint(1) DEFAULT 0,
  `resume_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `user_id`, `job_id`, `status`, `applied_at`, `hired`, `resume_path`) VALUES
(16, 8, 7, 1, '2024-12-13 05:34:22', 0, 'uploads/resumes/675bc75e1f69e-Literature-Review-Matrix.docx - Google Docs.pdf'),
(17, 8, 7, 1, '2024-12-13 09:24:08', 0, 'uploads/resumes/675bfd38ac9c0-Literature-Review-Matrix.docx - Google Docs.pdf'),
(18, 9, 7, 2, '2024-12-13 09:26:07', 0, 'uploads/resumes/675bfdafcb383-GUZMAN_FINAL_5_MIDTERM_EXAM_REGISTER_LOGIN_LOGOUT_AND_WHO_DID_IT.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `job_posts`
--

CREATE TABLE `job_posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `posted_by` int(11) DEFAULT NULL,
  `status` enum('open','closed') DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_posts`
--

INSERT INTO `job_posts` (`id`, `title`, `description`, `posted_by`, `status`, `created_at`) VALUES
(7, 'Computer Programmer', 'Graduated at any computer course and knows C++', 6, 'open', '2024-12-13 03:46:40'),
(8, 'Dishwasher', 'Trip ko lang sir', 6, 'open', '2024-12-13 03:51:30'),
(9, 'HR Assistant', 'Proficient in English Language', 6, 'open', '2024-12-13 09:27:39');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `created_at`) VALUES
(18, 8, 6, 'Hello! I am interested in a position at computer programmer.', '2024-12-13 03:47:29'),
(19, 6, 8, 'Hello! I\'m Abigail Santos, head at HR Department. We will reach out to you as soon as possible. Thank you!', '2024-12-13 03:55:27'),
(20, 8, 6, 'Good day maam! Any updates regarding to my application? Thank you!', '2024-12-13 03:56:34');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('hr','applicant') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(6, 'abigail_santos', '$2y$10$rgLL.doWGeFQG/9r/dqMzuu5DbcZf4d6bZtGTz8FarVdtvAVHnSAK', 'hr', '2024-12-13 03:45:24'),
(7, 'marco_cinco', '$2y$10$CDtDuKr9SQMC8hTnvY8eTuv4/FVEjaJ3OcTx.w844uZYhP532TV6i', 'hr', '2024-12-13 03:45:43'),
(8, 'michael_phelps', '$2y$10$RppctBg.6p1.8FHkclhWBuzY8lLKM/ES9gzrF6Czr1ATgpkmz6Fjq', 'applicant', '2024-12-13 03:45:53'),
(9, 'mira_kei', '$2y$10$GEw6W2Khk7v7689cGeprNu.AVPaj.MvwIJfNSmJCAxp/YoP.LQpC2', 'applicant', '2024-12-13 03:46:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `job_id` (`job_id`);

--
-- Indexes for table `job_posts`
--
ALTER TABLE `job_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `posted_by` (`posted_by`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `job_posts`
--
ALTER TABLE `job_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`job_id`) REFERENCES `job_posts` (`id`);

--
-- Constraints for table `job_posts`
--
ALTER TABLE `job_posts`
  ADD CONSTRAINT `job_posts_ibfk_1` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
