<?php
session_start();
require_once "config/database.php";

$stmt = $pdo->query("
    SELECT jobs.*, users.name AS employer_name
    FROM jobs
    JOIN users ON jobs.employer_id = users.id
    ORDER BY jobs.created_at DESC
    LIMIT 6
");

$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>StudentHire - Jobs & Internships</title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<?php $base = ""; include __DIR__ . "/includes/navbar.php"; ?>

<section class="hero">

    <div>
        <h1>Find Your Dream Job or Internship</h1>

        <p>
            Connect with companies, discover opportunities,
            and start building your career.
        </p>

        <a href="jobs.php" class="hero-btn">
            Explore Opportunities
        </a>
    </div>

</section>

<section class="container">

    <h2>Latest Opportunities</h2>

    <div class="job-grid">

        <?php foreach ($jobs as $job): ?>

            <div class="job-card">

                <span class="badge">
                    <?= htmlspecialchars($job['job_type']) ?>
                </span>

                <h3>
                    <?= htmlspecialchars($job['title']) ?>
                </h3>

                <p class="employer-line">
                    <span class="avatar-mini">
                        <?= htmlspecialchars(strtoupper(substr($job['employer_name'], 0, 1))) ?>
                    </span>
                    <a href="profile.php?id=<?= (int) $job['employer_id'] ?>">
                        <?= htmlspecialchars($job['employer_name']) ?>
                    </a>
                </p>

                <p>
                    📍 <?= htmlspecialchars($job['location']) ?>
                </p>

                <p>
                    <?= htmlspecialchars(substr($job['description'], 0, 120)) ?>...
                </p>

                <a
                    href="job-details.php?id=<?= $job['id'] ?>"
                    class="btn"
                >
                    View Details
                </a>

            </div>

        <?php endforeach; ?>

    </div>

</section>

</body>
</html>































































