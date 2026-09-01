-- Run this ONLY if you already imported the previous version of schema.sql
-- and don't want to lose existing data. It adds the new profile fields and
-- the saved_jobs table needed for the new features.
--
-- If you're setting the project up for the first time, ignore this file —
-- just import schema.sql, which already includes everything below.

USE student_job_portal;

ALTER TABLE students
    ADD COLUMN bio TEXT DEFAULT NULL AFTER headline,
    ADD COLUMN resume_link VARCHAR(255) DEFAULT NULL AFTER skills;

ALTER TABLE companies
    ADD COLUMN description TEXT DEFAULT NULL AFTER company_name,
    ADD COLUMN website VARCHAR(255) DEFAULT NULL AFTER description;

CREATE TABLE IF NOT EXISTS saved_jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    job_id INT NOT NULL,
    saved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_save (student_id, job_id),
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE
) ENGINE=InnoDB;
