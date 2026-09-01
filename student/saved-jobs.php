<?php

session_start();

require_once "../config/database.php";

if (
    !isset($_SESSION["user_id"])
    ||
    $_SESSION["role"] !== "student"
) {
    header("Location: ../login.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT jobs.*, users.name AS employer_name
    FROM saved_jobs
    JOIN jobs ON saved_jobs.job_id = jobs.id
    JOIN users ON jobs.employer_id = users.id
    WHERE saved_jobs.student_id = ?
    ORDER BY saved_jobs.saved_at DESC
");

$stmt->execute([$_SESSION["user_id"]]);

$savedJobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Saved Jobs - StudentHire</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<?php $base = "../"; include __DIR__ . "/../includes/navbar.php"; ?>

<div class="container">

    <h1>Saved Jobs</h1>

    <?php if (!$savedJobs): ?>

        <p class="muted">You haven't saved any jobs yet. Browse <a href="../jobs.php">open jobs</a> and tap Save to bookmark one.</p>

    <?php else: ?>

        <div class="job-grid">

            <?php foreach ($savedJobs as $job): ?>

                <div class="job-card">

                    <span class="badge"><?= htmlspecialchars($job["job_type"]) ?></span>

                    <h3><?= htmlspecialchars($job["title"]) ?></h3>

                    <p class="employer-line">
                        <span class="avatar-mini">
                            <?= htmlspecialchars(strtoupper(substr($job["employer_name"], 0, 1))) ?>
                        </span>
                        <?= htmlspecialchars($job["employer_name"]) ?>
                    </p>

                    <p>📍 <?= htmlspecialchars($job["location"]) ?></p>

                    <div class="card-actions">

                        <a class="btn" href="../job-details.php?id=<?= $job["id"] ?>">View Job</a>

                        <form method="POST" action="../save-job.php" class="inline-form">
                            <input type="hidden" name="job_id" value="<?= $job["id"] ?>">
                            <input type="hidden" name="redirect" value="student/saved-jobs.php">
                            <button class="btn btn-outline btn-sm" type="submit">Remove</button>
                        </form>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>

</body>

</html>
