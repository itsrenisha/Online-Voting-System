<?php
include '../includes/admin_auth_check.php';
include '../config/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    if (empty($title) || empty($start_date) || empty($end_date)) {
        $error = "Title, start date, and end date are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO elections (title, description, start_date, end_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $description, $start_date, $end_date);

        if ($stmt->execute()) {
            header("Location: elections.php");
            exit();
        } else {
            $error = "Something went wrong. Please try again.";
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
<title>Create Election - Vote</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/style.css">
</head>
<body data-page="elections" data-user="<?php echo htmlspecialchars($_SESSION['admin_name']); ?>">

<div id="layout-root"></div>

<main class="main">
  <div class="main-inner">

    <h1 class="page-title">Create Election</h1>
    <p class="page-subtitle">Enter the election details below</p>

    <div class="card form-page-card">
      <?php if ($error): ?>
        <div class="message error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <form method="POST" action="add_election.php">
        <div class="form-group">
          <label>Election Title</label>
          <input type="text" name="title" placeholder="e.g. Student President Election" required>
        </div>
        <div class="form-group">
          <label>Description</label>
          <input type="text" name="description" placeholder="Short description">
        </div>
        <div class="form-group">
          <label>Start Date</label>
          <input type="date" name="start_date" required>
        </div>
        <div class="form-group">
          <label>End Date</label>
          <input type="date" name="end_date" required>
        </div>
        <p style="font-size:12px; color:var(--text-faint); margin-bottom:16px;">
          Status (Upcoming / Active / Completed) is calculated automatically from these dates — no need to set it manually.
        </p>
        <div class="form-actions">
          <a href="elections.php" style="flex:1;"><button type="button" class="btn btn-outline btn-block">Cancel</button></a>
          <button type="submit" class="btn btn-primary">Save Election</button>
        </div>
      </form>
    </div>

  </div>
</main>

<script src="../js/admin-layout.js"></script>
</body>
</html>
