<?php
include '../includes/admin_auth_check.php';
include '../config/db.php';

$error = "";
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = (int)$_POST['id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    if (empty($title) || empty($start_date) || empty($end_date)) {
        $error = "Title, start date, and end date are required.";
    } else {
        $stmt = $conn->prepare("UPDATE elections SET title=?, description=?, start_date=?, end_date=? WHERE id=?");
        $stmt->bind_param("ssssi", $title, $description, $start_date, $end_date, $id);

        if ($stmt->execute()) {
            header("Location: elections.php");
            exit();
        } else {
            $error = "Something went wrong. Please try again.";
        }
        $stmt->close();
    }
}

$stmt = $conn->prepare("SELECT title, description, start_date, end_date FROM elections WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: elections.php");
    exit();
}
$election = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Election - Vote</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/style.css">
</head>
<body data-page="elections" data-user="<?php echo htmlspecialchars($_SESSION['admin_name']); ?>">

<div id="layout-root"></div>

<main class="main">
  <div class="main-inner">

    <h1 class="page-title">Edit Election</h1>
    <p class="page-subtitle">Update the election details below</p>

    <div class="card form-page-card">
      <?php if ($error): ?>
        <div class="message error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <form method="POST" action="edit_election.php">
        <input type="hidden" name="id" value="<?php echo (int)$id; ?>">
        <div class="form-group">
          <label>Election Title</label>
          <input type="text" name="title" value="<?php echo htmlspecialchars($election['title']); ?>" required>
        </div>
        <div class="form-group">
          <label>Description</label>
          <input type="text" name="description" value="<?php echo htmlspecialchars($election['description']); ?>">
        </div>
        <div class="form-group">
          <label>Start Date</label>
          <input type="date" name="start_date" value="<?php echo htmlspecialchars($election['start_date']); ?>" required>
        </div>
        <div class="form-group">
          <label>End Date</label>
          <input type="date" name="end_date" value="<?php echo htmlspecialchars($election['end_date']); ?>" required>
        </div>
        <p style="font-size:12px; color:var(--text-faint); margin-bottom:16px;">
          Status (Upcoming / Active / Completed) is calculated automatically from these dates.
        </p>
        <div class="form-actions">
          <a href="elections.php" style="flex:1;"><button type="button" class="btn btn-outline btn-block">Cancel</button></a>
          <button type="submit" class="btn btn-primary">Update Election</button>
        </div>
      </form>
    </div>

  </div>
</main>

<script src="../js/admin-layout.js"></script>
</body>
</html>
