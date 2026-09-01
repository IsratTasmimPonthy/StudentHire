-- StudentHire database schema
-- Import this file once in phpMyAdmin (or via `mysql` CLI) before running the site.

CREATE DATABASE IF NOT EXISTS student_job_portal
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE student_job_portal;

-- Every account: students, employers, and admins
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'employer', 'admin') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Extra profile row for student accounts (LinkedIn-style profile fields)
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    headline VARCHAR(150) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    skills VARCHAR(255) DEFAULT NULL,
    resume_link VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Extra profile row for employer accounts (company page fields)
CREATE TABLE companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    company_name VARCHAR(150) NOT NULL,
    description TEXT DEFAULT NULL,
    website VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Job / internship postings
CREATE TABLE jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employer_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100) DEFAULT NULL,
    location VARCHAR(120) DEFAULT NULL,
    job_type ENUM('Job', 'Internship') NOT NULL DEFAULT 'Job',
    salary VARCHAR(100) DEFAULT NULL,
    deadline DATE DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employer_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Student applications to jobs (one application per student per job)
CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    student_id INT NOT NULL,
    cover_letter TEXT,
    status ENUM('Pending', 'Reviewed', 'Accepted', 'Rejected') NOT NULL DEFAULT 'Pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_application (job_id, student_id),
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Jobs a student has bookmarked to look at later (LinkedIn-style "Save")
CREATE TABLE saved_jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    job_id INT NOT NULL,
    saved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_save (student_id, job_id),
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Sample data so the homepage isn't empty on first run.
-- This seed employer account has no usable password — register your own
-- employer account from the site to post real jobs. See README.md for
-- how to promote an account to admin.
-- ---------------------------------------------------------------------

INSERT INTO users (name, email, password, role) VALUES
('TechNova Ltd.', 'seed-employer@example.com', 'not-a-real-login', 'employer');

SET @employer_id = LAST_INSERT_ID();

INSERT INTO companies (user_id, company_name, description, website) VALUES
(@employer_id, 'TechNova Ltd.', 'TechNova builds software products for the local student job market and hires interns year-round across engineering, design, and support.', 'https://example.com');

INSERT INTO jobs (employer_id, title, description, category, location, job_type, salary, deadline) VALUES
(@employer_id, 'Frontend Developer Intern', 'Work with our product team building React interfaces used by thousands of students every day. Great mentorship and a flexible schedule around classes.', 'Web Development', 'Dhaka, Bangladesh', 'Internship', '15,000 - 20,000 BDT', DATE_ADD(CURDATE(), INTERVAL 30 DAY)),
(@employer_id, 'Junior Data Analyst', 'Help us turn raw data into dashboards and reports. SQL and spreadsheet skills required; Python is a plus.', 'Data', 'Remote', 'Job', '35,000 - 45,000 BDT', DATE_ADD(CURDATE(), INTERVAL 45 DAY)),
(@employer_id, 'Graphic Design Intern', 'Design social media assets, landing pages, and pitch decks alongside our marketing team.', 'Design', 'Chattogram, Bangladesh', 'Internship', '10,000 BDT', DATE_ADD(CURDATE(), INTERVAL 20 DAY)),
(@employer_id, 'Customer Support Associate', 'Be the first point of contact for our users over chat and email. Strong written English required.', 'Customer Service', 'Dhaka, Bangladesh', 'Job', '25,000 - 30,000 BDT', DATE_ADD(CURDATE(), INTERVAL 60 DAY));
