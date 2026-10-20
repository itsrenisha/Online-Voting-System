<?php
session_start();
include '../config/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in both fields.";
    } else {
        $stmt = $conn->prepare("SELECT id, full_name, password FROM admins WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();

            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['full_name'];
                $_SESSION['admin_email'] = $email;

                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No admin account found with that email.";
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
<title>Admin Login - Vote</title>
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

    <h2>Admin Login</h2>

    <?php if ($error): ?>
        <div class="message error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="form-group">
            <label>Admin Email</label>
            <input type="email" name="email" placeholder="admin@vote.com" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password"placeholder="*******" required>
        </div>
        <button type="submit" class="btn btn-primary">Login to Admin Panel</button>
    </form>

    <div class="link-text">
        Don't have an admin account? <a href="register.php">Register here</a>
    </div>

    <div class="back-link">
      Not an admin? <a href="../login.php">Go to voter login</a>
    </div>
  </div>
</div>
</body>
</html>
