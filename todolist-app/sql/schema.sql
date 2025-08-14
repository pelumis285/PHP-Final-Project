-- schema.sql
-- Run this SQL code to create the necessary tables for your project.
-- This fulfills the SQL requirements from the PDF.

-- Create a table for users
CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE, -- Emails must be unique (Requirement 4c)
    password VARCHAR(255) NOT NULL,    -- Passwords will be hashed (Requirement 4a)
    image_path VARCHAR(255) DEFAULT NULL, -- Path for uploaded image (Requirement 4d)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create a table for the website's content (the to-do list items)
CREATE TABLE IF NOT EXISTS tasks (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    task_name VARCHAR(255) NOT NULL,
    is_completed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);