-- Create the database
CREATE DATABASE IF NOT EXISTS budget_app;
USE budget_app;

-- Drop tables if they exist
DROP TABLE IF EXISTS demands;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS departments;

-- Create departments table
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- Create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role TINYINT(1) DEFAULT 0,
    department_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
);

-- Create demands table
CREATE TABLE demands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    department_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    demand_date DATE,
    reason TEXT,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE
);

-- Insert departments
INSERT INTO departments (name) VALUES 
('HR'),
('Finance'),
('IT'),
('Operations');

-- Insert sample admin user (password is 'admin123')
INSERT INTO users (name, email, password, role, department_id) VALUES (
    'Admin User',
    'admin@example.com',
    -- Use PHP's password_hash('admin123', PASSWORD_DEFAULT) to get the correct hash
    '$2y$10$eW5klj3AqCBZtT1I52FGGOL8dkRb97NHcbY9QpjD6emrLMG4ErAwW', 
    1,
    1
);
