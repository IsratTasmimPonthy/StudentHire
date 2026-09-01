<?php

session_start();
require_once "config/database.php";

$keyword = $_GET["keyword"] ?? "";
$category = $_GET["category"] ?? "";
$type = $_GET["type"] ?? "";

$sql = "
    SELECT jobs.*, users.name AS employer_name
    FROM jobs
    JOIN users ON jobs.employer_id = users.id
    WHERE 1=1
";

$params = [];

if ($keyword !== "") {

    $sql .= "
        AND (
            jobs.title LIKE ?
            OR jobs.description LIKE ?
            OR users.name LIKE ?
        )
    ";

    $search = "%$keyword%";

    $params[] = $search;
    $params[] = $search;
    $params[] = $search;
}

if ($category !== "") {

    $sql .= " AND jobs.category = ?";

    $params[] = $category;
}

if ($type !== "") {

    $sql .= " AND jobs.job_type = ?";

    $params[] = $type;
}

$sql .= " ORDER BY jobs.created_at DESC";

$stmt = $pdo->prepare($sql);

$stmt->execute($params);

$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$savedJobIds = [];

if (isset($_SESSION["user_id"]) && $_SESSION["role"] === "student") {

    $savedStmt = $pdo->prepare("
        SELECT job_id
        FROM saved_jobs
        WHERE student_id = ?
    ");

    $savedStmt->execute([$_SESSION["user_id"]]);

    $savedJobIds = $savedStmt->fetchAll(PDO::FETCH_COLUMN);
}

?>

<!DOCTYPE html>

<html>

<head>

    <title>Find Jobs - StudentHire</title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

<?php $base = ""; include __DIR__ . "/includes/navbar.php"; ?>

<div class="container">

    <h1>Find Jobs & Internships</h1>

    <form
        method="GET"
        class="search-box"
    >

        <input
            type="text"
            name="keyword"
            placeholder="Search jobs..."
            value="<?= htmlspecialchars($keyword) ?>"
        >

        <select name="type">

            <option value="">
                All Types
            </option>

            <option
                value="Job"
                <?= $type === "Job" ? "selected" : "" ?>
            >
                Job
            </option>

            <option
                value="Internship"
                <?= $type === "Internship" ? "selected" : "" ?>
            >
                Internship
            </option>

        </select>

        <input
            type="text"
            name="category"
            placeholder="Category"
            value="<?= htmlspecialchars($category) ?>"
        >

        <button class="btn">
            Search
        </button>

    </form>

    <div class="job-grid">

        <?php foreach ($jobs as $job): ?>

            <div class="job-card">

                <span class="badge">
                    <?= htmlspecialchars($job["job_type"]) ?>
                </span>

                <h3>
                    <?= htmlspecialchars($job["title"]) ?>
                </h3>

                <p class="employer-line">
                    <span class="avatar-mini">
                        <?= htmlspecialchars(strtoupper(substr($job["employer_name"], 0, 1))) ?>
                    </span>
                    <a href="profile.php?id=<?= (int) $job["employer_id"] ?>">
                        <?= htmlspecialchars($job["employer_name"]) ?>
                    </a>
                </p>

                <p>
                    📍 <?= htmlspecialchars($job["location"]) ?>
                </p>

                <p>
                    💰 <?= htmlspecialchars($job["salary"]) ?>
                </p>

                <p>
                    <?= htmlspecialchars(
                        substr($job["description"], 0, 150)
                    ) ?>
                    ...
                </p>

                <div class="card-actions">

                    <a
                        class="btn"
                        href="job-details.php?id=<?= $job["id"] ?>"
                    >
                        View Job
                    </a>

                    <?php if (isset($_SESSION["user_id"]) && $_SESSION["role"] === "student"): ?>

                        <form method="POST" action="save-job.php" class="inline-form">
                            <input type="hidden" name="job_id" value="<?= $job["id"] ?>">
                            <input type="hidden" name="redirect" value="jobs.php">

                            <?php if (in_array($job["id"], $savedJobIds)): ?>
                                <button class="btn btn-outline btn-sm" type="submit">★ Saved</button>
                            <?php else: ?>
                                <button class="btn btn-outline btn-sm" type="submit">☆ Save</button>
                            <?php endif; ?>
                        </form>

                    <?php endif; ?>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</div>

</body>

</html>