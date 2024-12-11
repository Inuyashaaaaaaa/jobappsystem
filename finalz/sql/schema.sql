-- Creating the users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,                    -- Unique user ID
    username VARCHAR(255) UNIQUE NOT NULL,                 -- User's username
    password VARCHAR(255) NOT NULL,                        -- Hashed password
    role ENUM('applicant', 'hr', 'admin') NOT NULL,        -- User's role
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP         -- Account creation timestamp
);

-- Creating the job_posts table
CREATE TABLE job_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,                     -- Job post ID
    title VARCHAR(255) NOT NULL,                            -- Job title
    description TEXT NOT NULL,                              -- Job description
    posted_by INT,                                         -- HR user ID (foreign key)
    status ENUM('open', 'closed') DEFAULT 'open',           -- Job status (open or closed)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,        -- Job post creation timestamp
    FOREIGN KEY (posted_by) REFERENCES users(id)           -- Foreign key referencing users(id)
);

-- Creating the messages table
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,                     -- Message ID
    sender_id INT,                                         -- Sender user ID (foreign key)
    receiver_id INT,                                       -- Receiver user ID (foreign key)
    message TEXT NOT NULL,                                  -- Message content
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,        -- Message sent timestamp
    FOREIGN KEY (sender_id) REFERENCES users(id),          -- Foreign key referencing sender's user ID
    FOREIGN KEY (receiver_id) REFERENCES users(id)         -- Foreign key referencing receiver's user ID
);

-- Creating the applications table
CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,                     -- Application ID
    user_id INT,                                           -- Applicant user ID (foreign key)
    job_id INT,                                            -- Job post ID (foreign key)
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending', -- Application status
    hired BOOLEAN DEFAULT FALSE,                            -- Whether the applicant was hired
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,        -- Application submission timestamp
    FOREIGN KEY (user_id) REFERENCES users(id),            -- Foreign key referencing applicant user ID
    FOREIGN KEY (job_id) REFERENCES job_posts(id)          -- Foreign key referencing job post ID
);