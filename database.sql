-- Create database
CREATE DATABASE IF NOT EXISTS usermgmt;
USE usermgmt;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255) NOT NULL UNIQUE,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  firstname VARCHAR(255),
  lastname VARCHAR(255),
  gender VARCHAR(50),
  nationality VARCHAR(100),
  contact_number VARCHAR(20),
  must_change_password TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample users (passwords are hashed using PASSWORD_DEFAULT in PHP)
-- Admin user: admin / admin123
INSERT INTO users (username, email, password, role, firstname, lastname) VALUES
('admin', 'admin@example.com', '$2y$10$YIj7P2DPI4rJ1ZV8/ulBsanskjdf9asdfkjNEw32rksdLa9eFVnzG', 'admin', 'Admin', 'User');

-- Regular user: user / user123
INSERT INTO users (username, email, password, role, firstname, lastname) VALUES
('user', 'user@example.com', '$2y$10$QWpr5DJklk9/sdfasdfvY32rk0sdfasdZVnzGeAsFDa9eFVnzG', 'user', 'John', 'Doe');
