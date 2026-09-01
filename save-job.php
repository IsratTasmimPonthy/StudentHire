<?php

session_start();

require_once "config/database.php";

if (
    !isset($_SESSION["user_id"])
    ||
    $_SESSION["role"] !== "student"
) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: jobs.php");
    exit;
}

$jobId = intval($_POST["job_id"] ?? 0);
$redirect = $_POST["redirect"] ?? "jobs.php";

// Only allow redirecting back within this app, never to an external URL
if (preg_match('/^[a-zA-Z0-9_\-\.\/?=&]*$/', $redirect) !== 1) {
    $redirect = "jobs.php";
}

$checkStmt = $pdo->prepare("
    SELECT id
    FROM saved_jobs
    WHERE student_id = ? AND job_id = ?
");

$checkStmt->execute([$_SESSION["user_id"], $jobId]);

$existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

if ($existing) {

    $deleteStmt = $pdo->prepare("
        DELETE FROM saved_jobs
        WHERE id = ?
    ");

    $deleteStmt->execute([$existing["id"]]);

} else {

    $insertStmt = $pdo->prepare("
        INSERT INTO saved_jobs (student_id, job_id)
        VALUES (?, ?)
    ");

    $insertStmt->execute([$_SESSION["user_id"], $jobId]);
}

header("Location: " . $redirect);
exit;

?>
