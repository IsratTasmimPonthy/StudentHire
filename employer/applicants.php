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

$jobId = intval($_GET["job_id"] ?? 0);

// Make sure this job actually belongs to the logged-in employer
$jobStmt = $pdo->prepare("
    SELECT *
    FROM jobs
    WHERE id = ? AND employer_id = ?
");

$jobStmt->execute([$jobId, $_SESSION["user_id"]]);

$job = $jobStmt->fetch(PDO::FETCH_ASSOC);

if (!$job) {
    die("Job not found.");
}

$statusMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $applicationId = intval($_POST["application_id"] ?? 0);
    $newStatus = $_POST["status"] ?? "";

    $allowedStatuses = ["Pending", "Reviewed", "Accepted", "Rejected"];

    if (in_array($newStatus, $allowedStatuses, true)) {

        // The job_id check here keeps an employer from updating an
        // application that isn't tied to one of their own jobs
        $updateStmt = $pdo->prepare("
            UPDATE applications
            SET status = ?
            WHERE id = ? AND job_id = ?
        ");

        $updateStmt->execute([$newStatus, $applicationId, $jobId]);

        $statusMessage = "Application updated.";
    }
}

$applicantsStmt = $pdo->prepare("
    SELECT
        applications.*,
        users.name AS applicant_name,
        users.id AS applicant_id,
        students.headline AS applicant_headline
    FROM applications
    JOIN users ON applications.student_id = users.id
    LEFT JOIN students ON students.user_id = users.id
    WHERE applications.job_id = ?
    ORDER BY applications.applied_at DESC
");

$applicantsStmt->execute([$jobId]);

$applicants = $applicantsStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Applicants - <?= htmlspecialchars($job["title"]) ?></title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<?php $base = "../"; include __DIR__ . "/../includes/navbar.php"; ?>

<div class="container">

    <p><a href="dashboard.php">&larr; Back to Dashboard</a></p>

    <h1>Applicants for <?= htmlspecialchars($job["title"]) ?></h1>

    <?php if ($statusMessage): ?>
        <div class="success"><?= htmlspecialchars($statusMessage) ?></div>
    <?php endif; ?>

    <?php if (!$applicants): ?>

        <p class="muted">No applications yet for this posting.</p>

    <?php else: ?>

        <div class="applicant-list">

            <?php foreach ($applicants as $applicant): ?>

                <div class="applicant-card">

                    <div class="applicant-info">

                        <span class="avatar-mini">
                            <?= htmlspecialchars(strtoupper(substr($applicant["applicant_name"], 0, 1))) ?>
                        </span>

                        <div>
                            <a href="../profile.php?id=<?= $applicant["applicant_id"] ?>">
                                <strong><?= htmlspecialchars($applicant["applicant_name"]) ?></strong>
                            </a>
                            <p class="muted">
                                <?= htmlspecialchars($applicant["applicant_headline"] ?? "") !== ""
                                    ? htmlspecialchars($applicant["applicant_headline"])
                                    : "No headline set" ?>
                            </p>
                        </div>

                        <span class="badge badge-<?= strtolower($applicant["status"]) ?> applicant-status">
                            <?= htmlspecialchars($applicant["status"]) ?>
                        </span>

                    </div>

                    <details>
                        <summary>Cover letter</summary>
                        <p><?= nl2br(htmlspecialchars($applicant["cover_letter"])) ?></p>
                    </details>

                    <form method="POST" class="status-form">

                        <input type="hidden" name="application_id" value="<?= $applicant["id"] ?>">

                        <button class="btn btn-sm" type="submit" name="status" value="Accepted">Accept</button>
                        <button class="btn btn-outline btn-sm" type="submit" name="status" value="Reviewed">Mark Reviewed</button>
                        <button class="btn btn-danger btn-sm" type="submit" name="status" value="Rejected">Reject</button>

                    </form>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>

</body>

</html>
