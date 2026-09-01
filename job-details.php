<?php

session_start();

require_once "config/database.php";

$id = intval($_GET["id"] ?? 0);

$stmt = $pdo->prepare("
    SELECT jobs.*, users.name AS employer_name
    FROM jobs
    JOIN users ON jobs.employer_id = users.id
    WHERE jobs.id = ?
");

$stmt->execute([$id]);

$job = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$job) {
    die("Job not found.");
}

$isSaved = false;

if (isset($_SESSION["user_id"]) && $_SESSION["role"] === "student") {

    $savedStmt = $pdo->prepare("
        SELECT id
        FROM saved_jobs
        WHERE student_id = ? AND job_id = ?
    ");

    $savedStmt->execute([$_SESSION["user_id"], $job["id"]]);

    $isSaved = (bool) $savedStmt->fetch();
}

?>

<!DOCTYPE html>

<html>

<head>

    <title>
        <?= htmlspecialchars($job["title"]) ?>
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body>

<?php $base = ""; include __DIR__ . "/includes/navbar.php"; ?>

<div class="container">

    <div class="details-card">

        <span class="badge">
            <?= htmlspecialchars($job["job_type"]) ?>
        </span>

        <h1>
            <?= htmlspecialchars($job["title"]) ?>
        </h1>

        <h3>
            <span class="avatar-mini">
                <?= htmlspecialchars(strtoupper(substr($job["employer_name"], 0, 1))) ?>
            </span>
            <a href="profile.php?id=<?= (int) $job["employer_id"] ?>">
                <?= htmlspecialchars($job["employer_name"]) ?>
            </a>
        </h3>

        <p>
            📍 <?= htmlspecialchars($job["location"]) ?>
        </p>

        <p>
            💰 <?= htmlspecialchars($job["salary"]) ?>
        </p>

        <p>
            📅 Deadline:
            <?= htmlspecialchars($job["deadline"]) ?>
        </p>

        <hr>

        <h2>Description</h2>

        <p>
            <?= nl2br(
                htmlspecialchars($job["description"])
            ) ?>
        </p>

        <div class="card-actions">

            <?php if (
                isset($_SESSION["role"])
                &&
                $_SESSION["role"] === "student"
            ): ?>

                <a
                    href="apply.php?id=<?= $job["id"] ?>"
                    class="btn"
                >
                    Apply Now
                </a>

                <form method="POST" action="save-job.php" class="inline-form">
                    <input type="hidden" name="job_id" value="<?= $job["id"] ?>">
                    <input type="hidden" name="redirect" value="job-details.php?id=<?= $job["id"] ?>">

                    <?php if ($isSaved): ?>
                        <button class="btn btn-outline" type="submit">★ Saved</button>
                    <?php else: ?>
                        <button class="btn btn-outline" type="submit">☆ Save for later</button>
                    <?php endif; ?>
                </form>

            <?php elseif (!isset($_SESSION["user_id"])): ?>

                <a
                    href="login.php"
                    class="btn"
                >
                    Login to Apply
                </a>

            <?php endif; ?>

        </div>

    </div>

</div>

</body>

</html>