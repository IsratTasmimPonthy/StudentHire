# StudentHire — Setup Guide (বাংলা নির্দেশনা নিচে)

A PHP + MySQL job/internship portal for students, employers, and admins, redesigned with a LinkedIn-style look and profile/save/applicant-review features.

## What was fixed

- `assests/` folder (misspelled) renamed to `assets/` — this was breaking every CSS link.
- `admin/dashboard.php` was accidentally nested inside `assets/admin/` — moved to the correct `admin/` folder so login redirects work.
- `jobs_details.php` renamed to `job-details.php` to match the links used everywhere else.
- Removed leftover test/debug code at the top of `student/dashboard.php` that was blocking the whole page.
- Added `database/schema.sql` — the database tables were never included in the upload, so the site had nothing to connect to.

## What's new (LinkedIn-style features)

- **Professional redesign** — LinkedIn blue theme, sticky white navbar, card-based layout, all built from one shared stylesheet.
- **Shared navbar** (`includes/navbar.php`) — one file controls navigation across every page, so it stays consistent.
- **Profile pages** (`profile.php?id=..`) — every student and employer gets a public profile. Students show headline/bio/skills/resume link; employers show a company page with contact info and their open jobs. Edit your own from `edit-profile.php`.
- **Save/bookmark jobs** — students can save a job from the listing or the details page (`save-job.php`) and review them later at `student/saved-jobs.php`.
- **Application tracking** — `student/applications.php` shows every application with a color-coded status badge (Pending/Reviewed/Accepted/Rejected). The student dashboard now shows stats + recent activity instead of one long table.
- **Employer applicant review** (`employer/applicants.php`) — employers see everyone who applied to a job, read their cover letter, and Accept / Mark Reviewed / Reject with one click. The employer dashboard now shows total jobs and total applicants, with an applicant count on every job card.
- **Interview scheduling** — from the applicants page, an employer can schedule (or reschedule) an interview for any applicant: date/time, Online or In-Person, a meeting link or address, and notes. The student sees it on their applications page and dashboard, with an "Upcoming Interviews" counter.
- **Contact information** — employers can add a phone number and office address to their company profile, shown publicly on their profile page. Students can add a phone number, which only shows on their profile to the student themself or to a logged-in employer (e.g. one reviewing their application) — not to the public.

## Database changes for these features

`database/schema.sql` now includes extra profile columns (`bio`, `resume_link`, `phone` on students; `description`, `website`, `phone`, `address` on companies), a `saved_jobs` table, and an `interviews` table.

- **Fresh install:** just import `database/schema.sql` as usual — it already has everything.
- **Already set up an earlier version?** Import `database/update.sql` once. It's safe to run even if you already ran an older copy of it — every statement only adds what's missing, so nothing gets duplicated and no existing data is touched.

## Requirements

- A local PHP server with MySQL — the easiest way is **XAMPP** (Windows/Mac/Linux) or **Laragon** (Windows).
- PHP 8+ and MySQL/MariaDB (both come bundled with XAMPP).

## Steps to run locally (XAMPP)

1. **Install XAMPP** from https://www.apachefriends.org if you don't have it.
2. **Copy the project folder.** Put the whole `student-job-portal` folder inside XAMPP's `htdocs` directory:
   - Windows: `C:\xampp\htdocs\student-job-portal`
   - Mac: `/Applications/XAMPP/htdocs/student-job-portal`
   - Linux: `/opt/lampp/htdocs/student-job-portal`
3. **Start Apache and MySQL** from the XAMPP Control Panel.
4. **Create the database:**
   - Open http://localhost/phpmyadmin
   - Click **Import** → **Choose File** → select `database/schema.sql` from this project → **Go**
   - This creates the `student_job_portal` database with all tables and a few sample jobs.
   - (Upgrading an existing install instead? Import `database/update.sql` — see "Database changes" above.)
5. **Check the DB credentials** in `config/database.php` — the defaults (`root` user, empty password) match a fresh XAMPP install, so you usually don't need to change anything.
6. **Open the site:** go to http://localhost/student-job-portal/

## Using the site

- **Register** an account as a Student or Employer from the site itself — the sign-up form only offers those two roles.
- **Employer accounts** can post jobs right after registering (Dashboard → Post Job).
- **Student accounts** can browse `jobs.php`, view a job, and apply.
- **Admin dashboard**: there's no admin sign-up form on purpose. To view it:
  1. Register a normal account.
  2. In phpMyAdmin, open the `users` table, edit that row, and change its `role` column from `student`/`employer` to `admin`.
  3. Log out and log back in — you'll land on the admin dashboard.
- The sample jobs from `schema.sql` belong to a placeholder "TechNova Ltd." account with no working password — that's just seed data so the homepage isn't empty. Register your own employer account to post real jobs.

## Common issues

| Problem | Fix |
|---|---|
| "Database connection failed" | Make sure MySQL is running in XAMPP and you imported `database/schema.sql`. |
| Blank/broken styling | Make sure the whole folder (including `assets/`) was copied into `htdocs`, not just the `.php` files. |
| 404 on job details | Confirm the folder is at `htdocs/student-job-portal/` and you're visiting `http://localhost/student-job-portal/`, not a subfolder path. |

---

## বাংলা নির্দেশনা

1. **XAMPP ইনস্টল করুন**: https://www.apachefriends.org থেকে।
2. পুরো `student-job-portal` ফোল্ডারটি XAMPP-এর `htdocs` ফোল্ডারে কপি করুন (`C:\xampp\htdocs\student-job-portal`)।
3. XAMPP Control Panel থেকে **Apache** ও **MySQL** চালু করুন।
4. http://localhost/phpmyadmin খুলে **Import** এ গিয়ে `database/schema.sql` ফাইলটি সিলেক্ট করে **Go** চাপুন — এতে ডাটাবেস ও টেবিল তৈরি হয়ে যাবে, সাথে কিছু নমুনা জব। (আগে থেকে পুরনো ভার্সন সেটআপ করা থাকলে `database/schema.sql` এর বদলে `database/update.sql` import করুন, ডেটা মুছে যাবে না।)
5. ব্রাউজারে যান: http://localhost/student-job-portal/
6. সাইট থেকেই **Register** করে Student বা Employer অ্যাকাউন্ট খুলুন। Admin দেখতে চাইলে phpMyAdmin-এ গিয়ে আপনার ইউজারের `role` কলাম পরিবর্তন করে `admin` করে দিন।

**নতুন ফিচারগুলো:** প্রোফাইল পেজ (নিজের প্রোফাইল এডিট করতে পারবেন), জব সেভ/বুকমার্ক করা, আবেদনের status ট্র্যাক করা (Pending/Accepted/Rejected), employer রা applicant list দেখে Accept/Reject করতে পারবেন, **interview schedule** করা যাবে (date/time, online/in-person, link/address, notes সহ), আর প্রোফাইলে **phone/address contact information** যোগ করা যাবে।


## Advanced Features Added

- **Resume/CV upload:** Students can upload PDF/DOC/DOCX resumes (max 5 MB) from Edit Profile; employers can view uploaded CVs from applicant review.
- **Real-time messaging:** Student/employer conversations use AJAX polling every 2 seconds with persistent MySQL messages.
- **Email notifications:** Application, status-change, interview, and message events create in-app notifications and attempt PHP `mail()` delivery. Configure SMTP/PHPMailer for production email.
- **Advanced employer dashboard:** Applicant pipeline, pending/accepted counts, hire rate, job-level applicant counts and applicant messaging.
- **Mobile application:** The project is now a lightweight installable PWA with a manifest and service worker, so it can be added to a phone home screen.
- **Online interview:** Each scheduled interview receives a secure room token and an online interview room page. A real video provider/WebRTC can be connected for production video.
- **Job recommendation algorithm:** Student skills are matched against job title/category/description and ranked with a simple explainable match score.

### Upgrade an existing database
Import `database/update.sql` once in phpMyAdmin. For a fresh database, import the updated `database/schema.sql`.
