<?php
session_start();
include '../config/db.php';

$error = "";
$success = "";

// Only one admin account is allowed. Check how many already exist.
$count_result = $conn->query("SELECT COUNT(*) AS total FROM admins");
$admin_count = $count_result->fetch_assoc()['total'];
$registration_closed = $admin_count > 0;

if ($_SERVER["REQUEST_METHOD"] == "POST" && !$registration_closed) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
    $error = "All fields are required.";
    } elseif (!preg_match("/^[a-zA-Z ]{2,50}$/", $full_name)) {
        $error = "Full name must contain only letters and spaces.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match("/^[^@\s]+@[^@\s]+\.[^@\s]+$/", $email)) {
        $error = "Please enter a valid email address, e.g. admin@gmail.com.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    }   elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $check = $conn->prepare("SELECT id FROM admins WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "This email is already registered as an admin.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO admins (full_name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $full_name, $email, $hashed_password);

            if ($stmt->execute()) {
                $success = "Admin account created! You can now log in.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
            $stmt->close();
        }
        $check->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Register - Vote</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/style.css">
<style>
  .auth-shell { min-height: 100vh; }
  .admin-badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: var(--blue-light);
    color: var(--blue);
    font-size: 11.5px; font-weight: 600;
    padding: 5px 12px;
    border-radius: 30px;
    margin-bottom: 18px;
  }
  .back-link {
    display: block; text-align: center;
    margin-top: 16px; font-size: 12.5px;
    color: var(--text-faint);
  }
  .back-link a { color: var(--text-muted); font-weight: 500; }
  .back-link a:hover { color: var(--blue); }
</style>
</head>
<body>
<div class="auth-shell">
  <div class="auth-card">
    <div class="auth-logo">
      <div class="logo-mark">🛡️</div>
      <div class="name">Admin<span> Panel</span></div>
    </div>

    <div class="admin-badge">Restricted Access</div>

    <h2>Create Admin Account</h2>

    <?php if ($error): ?>
        <div class="message error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="message success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php if ($registration_closed && !$success): ?>
        <div class="message error">
            Admin registration is closed. This system only allows one admin account, and one has already been created.
        </div>
        <a href="login.php"><button class="btn btn-primary btn-block">Go to Admin Login</button></a>
    <?php elseif (!$success): ?>
        <form method="POST" action="register.php">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" required>
            </div>
            <div class="form-group">
                <label>Admin Email</label>
                <input type="email" name="email" placeholder="admin@vote.com" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary">Register as Admin</button>
        </form>
    <?php endif; ?>

    <div class="link-text">
        Already have an admin account? <a href="login.php">Login here</a>
    </div>

    <div class="back-link">
      Not an admin? <a href="../register.php">Go to voter registration</a>
    </div>
  </div>
</div>
</body>
</html>
