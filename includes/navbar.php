<?php
// Expects $base to be set by the including page: "" for root-level pages,
// "../" for pages one folder deep (student/, employer/, admin/).
$base = $base ?? "";
?>
<nav class="navbar">

    <a href="<?= $base ?>index.php" class="logo">StudentHire</a>

    <div>

        <a href="<?= $base ?>index.php">Home</a>
        <a href="<?= $base ?>jobs.php">Jobs</a>

        <?php if (isset($_SESSION["user_id"])): ?>

            <?php if ($_SESSION["role"] === "student"): ?>
                <a href="<?= $base ?>student/dashboard.php">Dashboard</a>
                <a href="<?= $base ?>student/saved-jobs.php">Saved</a>
            <?php elseif ($_SESSION["role"] === "employer"): ?>
                <a href="<?= $base ?>employer/dashboard.php">Dashboard</a>
            <?php elseif ($_SESSION["role"] === "admin"): ?>
                <a href="<?= $base ?>admin/dashboard.php">Admin</a>
            <?php endif; ?>

            <a href="<?= $base ?>profile.php?id=<?= (int) $_SESSION["user_id"] ?>">Profile</a>
            <a href="<?= $base ?>logout.php">Logout</a>

        <?php else: ?>

            <a href="<?= $base ?>login.php">Login</a>
            <a href="<?= $base ?>register.php" class="btn">Register</a>

        <?php endif; ?>

    </div>

</nav>
