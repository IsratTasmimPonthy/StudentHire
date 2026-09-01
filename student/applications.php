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
    SELECT
        applications.*,
        jobs.title,
        users.name AS employer_name
    FROM applications
    JOIN jobs ON applications.job_id = jobs.id
    JOIN users ON jobs.employer_id = users.id
    WHERE applications.student_id = ?
    ORDER BY applications.applied_at DESC
");

$stmt->execute([$_SESSION["user_id"]]);

$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>

<head>

    <title>My Applications - StudentHire</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<?php $base = "../"; include __DIR__ . "/../includes/navbar.php"; ?>

<div class="container">

    <h1>My Applications</h1>

    <?php if (!$applications): ?>

        <p class="muted">You haven't applied to anything yet. <a href="../jobs.php">Find a job</a> to get started.</p>

    <?php else: ?>

        <table>

            <tr>
                <th>Job</th>
                <th>Company</th>
                <th>Status</th>
                <th>Applied</th>
            </tr>

            <?php foreach ($applications as $application): ?>

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

    <?php endif; ?>

</div>

</body>

</html>
