-- Grant all privileges on the database to root user
GRANT ALL PRIVILEGES ON topic_talk.* TO 'root'@'localhost';
FLUSH PRIVILEGES;

-- Select the database to use
USE topic_talk;

-- Create the users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    bio TEXT,
    avatar VARCHAR(255),
    role ENUM('Regular User', 'Moderator', 'Admin') DEFAULT 'Regular User'
);