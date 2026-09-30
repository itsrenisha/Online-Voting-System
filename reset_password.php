<?php
session_start();
include 'config/db.php';

$error = "";
$success = "";

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

$email = $_SESSION['reset_email'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($password != $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $password, $email);

        if ($stmt->execute()) {
            unset($_SESSION['reset_email']);
            $success = "Password changed successfully! You can now log in.";
        } else {
            $error = "Password reset failed. Please try again.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reset Password - Vote</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="auth-shell">
    <div class="auth-card">

        <div class="auth-logo">
            <div class="name">Vote</div>
        </div>

        <h2>Reset your password</h2>

        <?php if ($error): ?>
            <div class="message error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>

            <div class="message success">
                <?php echo htmlspecialchars($success); ?>
            </div>

            <div class="link-text">
                <a href="login.php">Go to Login</a>
            </div>

        <?php else: ?>

            <form method="POST">

                <div class="form-group">
                    <label>Password</label>
                    <input
                        type="password"
                        name="password"
                        placeholder="Enter new password"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <input
                        type="password"
                        name="confirm_password"
                        placeholder="Confirm new password"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary">
                    Reset Password
                </button>

            </form>

            <div class="link-text">
                Remember your password?
                <a href="login.php">Login here</a>
            </div>

        <?php endif; ?>

    </div>
</div>

</body>
</html>