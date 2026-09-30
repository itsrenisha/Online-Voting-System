<?php
session_start();
include 'config/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in both fields.";
    } else {
        $stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $email;

                header("Location: voter/dashboard.php");
                exit();
            } else {
                $error = "Incorrect password.";
            }
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

<title>Login - Vote</title>

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

        <h2>Login to your account</h2>

        <?php if ($error): ?>
            <div class="message error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">

            <div class="form-group">
                <label>Email</label>
                <input
                    type="email"
                    name="email"
                    placeholder="voter@gmail.com"
                    required
                >
            </div>

            <div class="form-group">
                <label>Password</label>
                <input
                    type="password"
                    name="password"
                    placeholder="******"
                    required
                >
            </div>

            <div style="text-align: right; margin-bottom: 15px;">
                <a href="forgot_password.php" style="color: #2563eb; font-size: 13px;">
                    Forgot Password?
                </a>
            </div>

            <button type="submit" class="btn btn-primary">
                Login
            </button>

        </form>

        <div class="link-text">
            Don't have an account?
            <a href="register.php">Register here</a>
        </div>

    </div>

</div>

</body>
</html>