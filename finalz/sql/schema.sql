-- Users Table
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('hr', 'applicant') NOT NULL
);

-- Job Posts Table
CREATE TABLE `job_posts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `posted_by` INT NOT NULL,
    `status` ENUM('open', 'closed') DEFAULT 'open',
    FOREIGN KEY (`posted_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- Messages Table
CREATE TABLE `messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sender_id` INT NOT NULL,
    `receiver_id` INT NOT NULL,
    `message` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`sender_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`receiver_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- Applications Table
CREATE TABLE `applications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `job_id` INT NOT NULL,
    `status` ENUM('applied', 'accepted', 'rejected') DEFAULT 'applied',
    `hired` BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`job_id`) REFERENCES `job_posts`(`id`) ON DELETE CASCADE
);
