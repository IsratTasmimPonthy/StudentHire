<?php

session_start();

require_once "../config/database.php";

if (
    !isset($_SESSION["user_id"])
    ||
    $_SESSION["role"] !== "employer"
) {
    header("Location: ../login.php");
    exit;
}

$employerId = $_SESSION["user_id"];

$stmt = $pdo->prepare("
    SELECT
        jobs.*,
        (
            SELECT COUNT(*)
            FROM applications
            WHERE applications.job_id = jobs.id
        ) AS applicant_count
    FROM jobs
    WHERE employer_id = ?
    ORDER BY created_at DESC
");

$stmt->execute([$employerId]);

$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalJobs = count($jobs);

$totalApplicants = 0;
foreach ($jobs as $job) {
    $totalApplicants += (int) $job["applicant_count"];
}

?>

<!DOCTYPE html>

<html>

<head>

    <title>Employer Dashboard</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body>

<?php $base = "../"; include __DIR__ . "/../includes/navbar.php"; ?>

<div class="container">

    <h1>
        Employer Dashboard
    </h1>

    <div class="stats">

        <div class="stat-card">
            <h2><?= $totalJobs ?></h2>
            <p>Jobs Posted</p>
        </div>

        <div class="stat-card">
            <h2><?= $totalApplicants ?></h2>
            <p>Total Applicants</p>
        </div>

    </div>

    <div class="quick-links">
        <a href="post-job.php" class="btn">+ Post New Job</a>
        <a href="../edit-profile.php" class="btn btn-outline">Edit Company Profile</a>
    </div>

    <h2>
        My Jobs
    </h2>

    <?php if (!$jobs): ?>

        <p class="muted">You haven't posted any jobs yet.</p>

    <?php else: ?>

        <div class="job-grid">

            <?php foreach ($jobs as $job): ?>

                <div class="job-card">

                    <span class="badge">
                        <?= htmlspecialchars(
                            $job["job_type"]
                        ) ?>
                    </span>

                    <h3>
                        <?= htmlspecialchars(
                            $job["title"]
                        ) ?>
                    </h3>

                    <p>
                        <?= htmlspecialchars(
                            $job["location"]
                        ) ?>
                    </p>

                    <p>
                        Deadline:
                        <?= htmlspecialchars(
                            $job["deadline"]
                        ) ?>
                    </p>

                    <p>
                        <strong><?= (int) $job["applicant_count"] ?></strong> applicant(s)
                    </p>

                    <a
                        href="applicants.php?job_id=<?= $job["id"] ?>"
                        class="btn"
                    >
                        View Applicants
                    </a>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>

</body>

</html>
