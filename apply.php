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

$jobId = intval($_GET["id"] ?? 0);

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $coverLetter = trim($_POST["cover_letter"]);

    try {

        $stmt = $pdo->prepare("
            INSERT INTO applications
            (job_id, student_id, cover_letter)
            VALUES (?, ?, ?)
        ");

        $stmt->execute([
            $jobId,
            $_SESSION["user_id"],
            $coverLetter
        ]);

        $message = "Application submitted successfully!";

    } catch (PDOException $e) {

        $message = "You have already applied for this job.";

    }
}

?>

<!DOCTYPE html>

<html>

<head>

    <title>Apply - StudentHire</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body>

<?php $base = ""; include __DIR__ . "/includes/navbar.php"; ?>

<div class="auth-container">

    <h1>Apply for Opportunity</h1>

    <?php if ($message): ?>

        <div class="success">
            <?= htmlspecialchars($message) ?>
        </div>

    <?php endif; ?>

    <form method="POST">

        <label>
            Cover Letter
        </label>

        <textarea
            name="cover_letter"
            rows="10"
            required
            placeholder="Write your cover letter..."
        ></textarea>

        <button class="btn">
            Submit Application
        </button>

    </form>

</div>

</body>

</html>