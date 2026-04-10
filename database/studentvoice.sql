

CREATE DATABASE IF NOT EXISTS studentvoice CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE studentvoice;

-- Students table (your original structure, kept intact)
CREATE TABLE IF NOT EXISTS students (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    enrollment VARCHAR(20)  UNIQUE,
    college    VARCHAR(100),
    password   VARCHAR(255)   -- bcrypt hashed (never plain text)
);

-- Problems table (your original structure, kept intact)
CREATE TABLE IF NOT EXISTS problems (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    student_enroll VARCHAR(20),
    title          VARCHAR(200),
    description    TEXT,
    category       VARCHAR(50),
    votes          INT       DEFAULT 0,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Vote log table (NEW — prevents duplicate votes per IP)
-- One row = one vote from one IP on one problem
CREATE TABLE IF NOT EXISTS vote_log (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    problem_id INT         NOT NULL,
    voter_ip   VARCHAR(45) NOT NULL,
    voted_at   TIMESTAMP   DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_vote (problem_id, voter_ip)
);
