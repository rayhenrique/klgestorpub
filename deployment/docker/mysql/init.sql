-- MySQL initialization script for KL Gestor Pub
-- This script runs when the MySQL container starts for the first time

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `klgestorpub` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user and grant privileges
CREATE USER IF NOT EXISTS 'klgestorpub'@'%' IDENTIFIED BY 'klgestorpub_password';
GRANT ALL PRIVILEGES ON `klgestorpub`.* TO 'klgestorpub'@'%';

-- Grant additional privileges for Laravel operations
GRANT CREATE, ALTER, DROP, INDEX, REFERENCES ON `klgestorpub`.* TO 'klgestorpub'@'%';

-- Create test database for testing
CREATE DATABASE IF NOT EXISTS `klgestorpub_test` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON `klgestorpub_test`.* TO 'klgestorpub'@'%';

-- Flush privileges
FLUSH PRIVILEGES;

-- Use the main database
USE `klgestorpub`;

-- Set timezone
SET time_zone = '-03:00';

-- Enable event scheduler (if needed for Laravel scheduling)
SET GLOBAL event_scheduler = ON;

-- Optimize MySQL settings for Laravel
SET GLOBAL sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';

-- Create a health check table
CREATE TABLE IF NOT EXISTS `health_check` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `status` varchar(10) NOT NULL DEFAULT 'ok',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert initial health check record
INSERT INTO `health_check` (`status`) VALUES ('ok');

-- Show databases and users (for debugging)
SELECT 'Databases created:' as info;
SHOW DATABASES;

SELECT 'Users and privileges:' as info;
SELECT User, Host FROM mysql.user WHERE User = 'klgestorpub';

SELECT 'Setup completed successfully!' as status;