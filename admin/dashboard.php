<?php

session_start();

require_once "../config/database.php";

if (
    !isset($_SESSION["user_id"])
    ||
    $_SESSION["role"] !== "admin"
) {
    header("Location: ../login.php");
    exit;
}

$totalUsers = $pdo
    ->query("SELECT COUNT(*) FROM users")
    ->fetchColumn();

$totalJobs = $pdo
    ->query("SELECT COUNT(*) FROM jobs")
    ->fetchColumn();

$totalApplications = $pdo
    ->query("SELECT COUNT(*) FROM applications")
    ->fetchColumn();

?>

<!DOCTYPE html>

<html>

<head>

    <title>Admin Dashboard</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body>

<?php $base = "../"; include __DIR__ . "/../includes/navbar.php"; ?>

<div class="container">

    <h1>
        Admin Dashboard
    </h1>

    <div class="stats">

        <div class="stat-card">

            <h2>
                <?= $totalUsers ?>
            </h2>

            <p>
                Users
            </p>

        </div>

        <div class="stat-card">

            <h2>
                <?= $totalJobs ?>
            </h2>

            <p>
                Jobs
            </p>

        </div>

        <div class="stat-card">

            <h2>
                <?= $totalApplications ?>
            </h2>

            <p>
                Applications
            </p>

        </div>

    </div>

</div>

</body>

</html>