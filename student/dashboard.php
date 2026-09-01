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

$userId = $_SESSION["user_id"];

$applicationsCount = $pdo->prepare("
    SELECT COUNT(*) FROM applications WHERE student_id = ?
");
$applicationsCount->execute([$userId]);
$applicationsCount = $applicationsCount->fetchColumn();

$savedCount = $pdo->prepare("
    SELECT COUNT(*) FROM saved_jobs WHERE student_id = ?
");
$savedCount->execute([$userId]);
$savedCount = $savedCount->fetchColumn();

$recentStmt = $pdo->prepare("
    SELECT
        applications.*,
        jobs.title,
        users.name AS employer_name
    FROM applications
    JOIN jobs ON applications.job_id = jobs.id
    JOIN users ON jobs.employer_id = users.id
    WHERE applications.student_id = ?
    ORDER BY applications.applied_at DESC
    LIMIT 5
");
$recentStmt->execute([$userId]);
$recentApplications = $recentStmt->fetchAll(PDO::FETCH_ASSOC);

$profileStmt = $pdo->prepare("SELECT * FROM students WHERE user_id = ?");
$profileStmt->execute([$userId]);
$profile = $profileStmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Student Dashboard</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<?php $base = "../"; include __DIR__ . "/../includes/navbar.php"; ?>

<div class="container">

    <h1>Welcome, <?= htmlspecialchars($_SESSION["name"]) ?></h1>

    <?php if (empty($profile["headline"])): ?>
        <div class="success">
            Add a headline and skills to your profile so employers can find you better.
            <a href="../edit-profile.php">Edit Profile &rarr;</a>
        </div>
    <?php endif; ?>

    <div class="stats">

        <div class="stat-card">
            <h2><?= $applicationsCount ?></h2>
            <p>Applications</p>
        </div>

        <div class="stat-card">
            <h2><?= $savedCount ?></h2>
            <p>Saved Jobs</p>
        </div>

    </div>

    <div class="quick-links">
        <a href="../jobs.php" class="btn">Find Jobs</a>
        <a href="applications.php" class="btn btn-outline">My Applications</a>
        <a href="saved-jobs.php" class="btn btn-outline">Saved Jobs</a>
        <a href="../edit-profile.php" class="btn btn-outline">Edit Profile</a>
    </div>

    <h2>Recent Applications</h2>

    <?php if (!$recentApplications): ?>

        <p class="muted">No applications yet.</p>

    <?php else: ?>

        <table>

            <tr>
                <th>Job</th>
                <th>Company</th>
                <th>Status</th>
                <th>Applied</th>
            </tr>

            <?php foreach ($recentApplications as $application): ?>

                <tr>
                    <td>
                        <a href="../job-details.php?id=<?= $application["job_id"] ?>">
                            <?= htmlspecialchars($application["title"]) ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($application["employer_name"]) ?></td>
                    <td>
                        <span class="badge badge-<?= strtolower($application["status"]) ?>">
                            <?= htmlspecialchars($application["status"]) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($application["applied_at"]) ?></td>
                </tr>

            <?php endforeach; ?>

        </table>

        <p><a href="applications.php">View all applications &rarr;</a></p>

    <?php endif; ?>

</div>

</body>

</html>
