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

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $category = trim($_POST["category"]);
    $location = trim($_POST["location"]);
    $type = $_POST["job_type"];
    $salary = trim($_POST["salary"]);
    $deadline = $_POST["deadline"];

    $stmt = $pdo->prepare("
        INSERT INTO jobs
        (
            employer_id,
            title,
            description,
            category,
            location,
            job_type,
            salary,
            deadline
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $_SESSION["user_id"],
        $title,
        $description,
        $category,
        $location,
        $type,
        $salary,
        $deadline
    ]);

    $message = "Job posted successfully!";
}

?>

<!DOCTYPE html>

<html>

<head>

    <title>Post Job</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body>

<?php $base = "../"; include __DIR__ . "/../includes/navbar.php"; ?>

<div class="auth-container wide">

    <h1>
        Post Job / Internship
    </h1>

    <?php if ($message): ?>

        <div class="success">
            <?= htmlspecialchars($message) ?>
        </div>

    <?php endif; ?>

    <form method="POST">

        <label>
            Job Title
        </label>

        <input
            type="text"
            name="title"
            required
        >

        <label>
            Description
        </label>

        <textarea
            name="description"
            rows="8"
            required
        ></textarea>

        <label>
            Category
        </label>

        <input
            type="text"
            name="category"
            placeholder="Web Development"
            required
        >

        <label>
            Location
        </label>

        <input
            type="text"
            name="location"
            placeholder="Dhaka"
            required
        >

        <label>
            Type
        </label>

        <select name="job_type">

            <option value="Internship">
                Internship
            </option>

            <option value="Job">
                Job
            </option>

        </select>

        <label>
            Salary
        </label>

        <input
            type="text"
            name="salary"
            placeholder="20,000 - 30,000 BDT"
        >

        <label>
            Application Deadline
        </label>

        <input
            type="date"
            name="deadline"
            required
        >

        <button class="btn">
            Publish Opportunity
        </button>

    </form>

</div>

</body>

</html>