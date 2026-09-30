<?php
session_start();
include 'config/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = strtolower(trim($_POST['email']));

    if (empty($email)) {
        $error = "Email is required.";
    } elseif (!preg_match("/^[a-zA-Z0-9._%+-]+@(gmail\.com|[a-zA-Z0-9.-]+\.edu\.np)$/", $email)) {
        $error = "Please use a valid Gmail or .edu.np email address.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $_SESSION['reset_email'] = $email;

            header("Location: reset_password.php");
            exit();
        } else {
            $error = "No account found with that email.";
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

    <title>Forgot Password - Vote</title>

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

        <h2>Forgot your password?</h2>

        <?php if ($error): ?>
            <div class="message error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label>Email</label>

                <input
                    type="email"
                    name="email"
                    placeholder="example@gmail.com"
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary">
                Continue
            </button>

        </form>

        <div class="link-text">
            Remember your password?
            <a href="login.php">Login here</a>
        </div>

    </div>
</div>

</body>
</html>