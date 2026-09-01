<?php

session_start();
require_once "config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $pdo->prepare("
        SELECT *
        FROM users
        WHERE email = ?
    ");

    $stmt->execute([$email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user["password"])) {

        $_SESSION["user_id"] = $user["id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["role"] = $user["role"];

        if ($user["role"] === "student") {
            header("Location: student/dashboard.php");

        } elseif ($user["role"] === "employer") {
            header("Location: employer/dashboard.php");

        } else {
            header("Location: admin/dashboard.php");
        }

        exit;

    } else {

        $message = "Invalid email or password.";

    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Login - StudentHire</title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

<?php $base = ""; include __DIR__ . "/includes/navbar.php"; ?>

<div class="auth-container">

    <h1>Login</h1>

    <?php if ($message): ?>

        <div class="error">
            <?= htmlspecialchars($message) ?>
        </div>

    <?php endif; ?>

    <form method="POST">

        <label>Email</label>

        <input
            type="email"
            name="email"
            required
        >

        <label>Password</label>

        <input
            type="password"
            name="password"
            required
        >

        <button class="btn">
            Login
        </button>

    </form>

    <p>
        Don't have an account?
        <a href="register.php">Register</a>
    </p>

</div>

</body>

</html>