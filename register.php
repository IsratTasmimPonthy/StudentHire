<?php

session_start();
require_once "config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $role = $_POST["role"];

    if (!$name || !$email || !$password) {

        $message = "Please fill in all fields.";

    } else {

        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        try {

            $stmt = $pdo->prepare("
                INSERT INTO users
                (name, email, password, role)
                VALUES (?, ?, ?, ?)
            ");

            $stmt->execute([
                $name,
                $email,
                $hashedPassword,
                $role
            ]);

            $userId = $pdo->lastInsertId();

            if ($role === "student") {

                $stmt = $pdo->prepare("
                    INSERT INTO students (user_id)
                    VALUES (?)
                ");

                $stmt->execute([$userId]);

            } elseif ($role === "employer") {

                $stmt = $pdo->prepare("
                    INSERT INTO companies
                    (user_id, company_name)
                    VALUES (?, ?)
                ");

                $stmt->execute([
                    $userId,
                    $name
                ]);
            }

            header("Location: login.php");
            exit;

        } catch (PDOException $e) {

            $message = "Email already exists.";

        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Register - StudentHire</title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

<?php $base = ""; include __DIR__ . "/includes/navbar.php"; ?>

<div class="auth-container">

    <h1>Create Account</h1>

    <?php if ($message): ?>

        <div class="error">
            <?= htmlspecialchars($message) ?>
        </div>

    <?php endif; ?>

    <form method="POST">

        <label>Name</label>

        <input
            type="text"
            name="name"
            required
        >

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

        <label>Account Type</label>

        <select name="role">

            <option value="student">
                Student
            </option>

            <option value="employer">
                Employer
            </option>

        </select>

        <button class="btn">
            Register
        </button>

    </form>

    <p>
        Already have an account?
        <a href="login.php">Login</a>
    </p>

</div>

</body>

</html>