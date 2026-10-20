<?php
include '../includes/auth_check.php';
include '../config/db.php';

$user_id = $_SESSION['user_id'];

$elections = $conn->query("SELECT id, title,
    CASE
        WHEN CURDATE() < start_date THEN 'upcoming'
        WHEN CURDATE() > end_date THEN 'completed'
        ELSE 'active'
    END AS status
    FROM elections ORDER BY id DESC");

$voted_elections = [];
$check = $conn->prepare("SELECT election_id FROM votes WHERE user_id = ?");
$check->bind_param("i", $user_id);
$check->execute();
$voted_result = $check->get_result();
while ($row = $voted_result->fetch_assoc()) {
    $voted_elections[] = $row['election_id'];
}
$check->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Votes - Vote</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/style.css">
</head>
<body data-page="my-votes" data-user="<?php echo htmlspecialchars($_SESSION['full_name']); ?>">

<div id="layout-root"></div>

<main class="main">
  <div class="main-inner">

    <h1 class="page-title">My Votes</h1>
    <p class="page-subtitle">Your participation status across all elections</p>

    <div class="card table-card">
      <table class="data-table">
        <thead>
          <tr>
            <th>Election</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($elections->num_rows > 0): ?>
            <?php while ($e = $elections->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($e['title']); ?></td>
                <td>
                  <?php if (in_array($e['id'], $voted_elections)): ?>
                    <span class="badge badge-green"><span class="badge-dot"></span>Voted</span>
                  <?php else: ?>
                    <span class="badge badge-amber"><span class="badge-dot"></span>Not Voted</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr class="empty-row">
              <td colspan="2">No elections yet.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</main>

<script src="../js/layout.js"></script>
</body>
</html>
