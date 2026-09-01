<?php

session_start();

require_once "config/database.php";

$id = intval($_GET["id"] ?? 0);

$stmt = $pdo->prepare("
    SELECT *
    FROM users
    WHERE id = ?
");

$stmt->execute([$id]);

$profileUser = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profileUser) {
    die("User not found.");
}

$isOwnProfile = isset($_SESSION["user_id"]) && (int) $_SESSION["user_id"] === $id;

$student = null;
$company = null;
$employerJobs = [];

if ($profileUser["role"] === "student") {

    $studentStmt = $pdo->prepare("
        SELECT *
        FROM students
        WHERE user_id = ?
    ");

    $studentStmt->execute([$id]);

    $student = $studentStmt->fetch(PDO::FETCH_ASSOC);

} elseif ($profileUser["role"] === "employer") {

    $companyStmt = $pdo->prepare("
        SELECT *
        FROM companies
        WHERE user_id = ?
    ");

    $companyStmt->execute([$id]);

    $company = $companyStmt->fetch(PDO::FETCH_ASSOC);

    $jobsStmt = $pdo->prepare("
        SELECT *
        FROM jobs
        WHERE employer_id = ?
        ORDER BY created_at DESC
        LIMIT 6
    ");

    $jobsStmt->execute([$id]);

    $employerJobs = $jobsStmt->fetchAll(PDO::FETCH_ASSOC);
}

$initial = strtoupper(substr($profileUser["name"], 0, 1));

?>

<!DOCTYPE html>
<html>

<head>

    <title><?= htmlspecialchars($profileUser["name"]) ?> - StudentHire</title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

<?php $base = ""; include __DIR__ . "/includes/navbar.php"; ?>

<div class="container">

    <div class="details-card profile-card">

        <div class="profile-header">

            <div class="avatar avatar-lg">
                <?= htmlspecialchars($initial) ?>
            </div>

            <div>
                <h1><?= htmlspecialchars($profileUser["name"]) ?></h1>

                <?php if ($profileUser["role"] === "student"): ?>
                    <p class="profile-role">
                        <?= htmlspecialchars($student["headline"] ?? "") !== ""
                            ? htmlspecialchars($student["headline"])
                            : "Student" ?>
                    </p>
                <?php elseif ($profileUser["role"] === "employer"): ?>
                    <p class="profile-role">
                        <?= htmlspecialchars($company["company_name"] ?? $profileUser["name"]) ?>
                        &middot; Employer
                    </p>
                <?php else: ?>
                    <p class="profile-role">Administrator</p>
                <?php endif; ?>
            </div>

            <?php if ($isOwnProfile): ?>
                <a href="edit-profile.php" class="btn btn-outline profile-edit-btn">Edit Profile</a>
            <?php endif; ?>

        </div>

        <hr>

        <?php if ($profileUser["role"] === "student"): ?>

            <h2>About</h2>
            <p>
                <?= $student && $student["bio"]
                    ? nl2br(htmlspecialchars($student["bio"]))
                    : "<span class='muted'>No bio added yet.</span>" ?>
            </p>

            <h2>Skills</h2>
            <p>
                <?php if ($student && $student["skills"]): ?>
                    <?php foreach (array_filter(array_map("trim", explode(",", $student["skills"]))) as $skill): ?>
                        <span class="badge"><?= htmlspecialchars($skill) ?></span>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span class="muted">No skills added yet.</span>
                <?php endif; ?>
            </p>

            <h2>Resume</h2>
            <p>
                <?php if ($student && $student["resume_link"]): ?>
                    <a href="<?= htmlspecialchars($student["resume_link"]) ?>" target="_blank" rel="noopener">
                        View Resume Link
                    </a>
                <?php else: ?>
                    <span class="muted">No resume link added yet.</span>
                <?php endif; ?>
            </p>

        <?php elseif ($profileUser["role"] === "employer"): ?>

            <h2>About the Company</h2>
            <p>
                <?= $company && $company["description"]
                    ? nl2br(htmlspecialchars($company["description"]))
                    : "<span class='muted'>No company description added yet.</span>" ?>
            </p>

            <h2>Website</h2>
            <p>
                <?php if ($company && $company["website"]): ?>
                    <a href="<?= htmlspecialchars($company["website"]) ?>" target="_blank" rel="noopener">
                        <?= htmlspecialchars($company["website"]) ?>
                    </a>
                <?php else: ?>
                    <span class="muted">No website added yet.</span>
                <?php endif; ?>
            </p>

            <h2>Open Positions</h2>

            <?php if ($employerJobs): ?>

                <div class="job-grid">

                    <?php foreach ($employerJobs as $job): ?>

                        <div class="job-card">

                            <span class="badge"><?= htmlspecialchars($job["job_type"]) ?></span>

                            <h3><?= htmlspecialchars($job["title"]) ?></h3>

                            <p>📍 <?= htmlspecialchars($job["location"]) ?></p>

                            <a class="btn" href="job-details.php?id=<?= $job["id"] ?>">View Job</a>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <p class="muted">No open positions right now.</p>

            <?php endif; ?>

        <?php endif; ?>

    </div>

</div>

</body>

</html>
