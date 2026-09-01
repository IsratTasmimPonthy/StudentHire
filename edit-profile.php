<?php

session_start();

require_once "config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION["user_id"];
$role = $_SESSION["role"];

if ($role !== "student" && $role !== "employer") {
    header("Location: profile.php?id=" . $userId);
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if ($role === "student") {

        $headline = trim($_POST["headline"] ?? "");
        $bio = trim($_POST["bio"] ?? "");
        $skills = trim($_POST["skills"] ?? "");
        $resumeLink = trim($_POST["resume_link"] ?? "");

        $stmt = $pdo->prepare("
            UPDATE students
            SET headline = ?, bio = ?, skills = ?, resume_link = ?
            WHERE user_id = ?
        ");

        $stmt->execute([$headline, $bio, $skills, $resumeLink, $userId]);

    } else {

        $companyName = trim($_POST["company_name"] ?? "");
        $description = trim($_POST["description"] ?? "");
        $website = trim($_POST["website"] ?? "");

        $stmt = $pdo->prepare("
            UPDATE companies
            SET company_name = ?, description = ?, website = ?
            WHERE user_id = ?
        ");

        $stmt->execute([$companyName, $description, $website, $userId]);
    }

    $message = "Profile updated successfully!";
}

if ($role === "student") {

    $stmt = $pdo->prepare("SELECT * FROM students WHERE user_id = ?");
    $stmt->execute([$userId]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

} else {

    $stmt = $pdo->prepare("SELECT * FROM companies WHERE user_id = ?");
    $stmt->execute([$userId]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Edit Profile - StudentHire</title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

<?php $base = ""; include __DIR__ . "/includes/navbar.php"; ?>

<div class="auth-container wide">

    <h1>Edit Profile</h1>

    <?php if ($message): ?>
        <div class="success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">

        <?php if ($role === "student"): ?>

            <label>Headline</label>
            <input
                type="text"
                name="headline"
                placeholder="e.g. Computer Science student at DU"
                value="<?= htmlspecialchars($profile["headline"] ?? "") ?>"
            >

            <label>About / Bio</label>
            <textarea
                name="bio"
                rows="5"
                placeholder="Tell employers a bit about yourself..."
            ><?= htmlspecialchars($profile["bio"] ?? "") ?></textarea>

            <label>Skills (comma-separated)</label>
            <input
                type="text"
                name="skills"
                placeholder="HTML, CSS, PHP, Communication"
                value="<?= htmlspecialchars($profile["skills"] ?? "") ?>"
            >

            <label>Resume Link</label>
            <input
                type="url"
                name="resume_link"
                placeholder="https://drive.google.com/..."
                value="<?= htmlspecialchars($profile["resume_link"] ?? "") ?>"
            >

        <?php else: ?>

            <label>Company Name</label>
            <input
                type="text"
                name="company_name"
                required
                value="<?= htmlspecialchars($profile["company_name"] ?? "") ?>"
            >

            <label>Company Description</label>
            <textarea
                name="description"
                rows="5"
                placeholder="What does your company do?"
            ><?= htmlspecialchars($profile["description"] ?? "") ?></textarea>

            <label>Website</label>
            <input
                type="url"
                name="website"
                placeholder="https://yourcompany.com"
                value="<?= htmlspecialchars($profile["website"] ?? "") ?>"
            >

        <?php endif; ?>

        <button class="btn">Save Changes</button>

    </form>

    <p>
        <a href="profile.php?id=<?= $userId ?>">&larr; Back to profile</a>
    </p>

</div>

</body>

</html>
