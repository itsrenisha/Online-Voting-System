<?php
include '../includes/auth_check.php';
include '../config/db.php';

$user_id = $_SESSION['user_id'];

$elections = $conn->query("SELECT id, title, description, start_date, end_date,
    CASE
        WHEN CURDATE() < start_date THEN 'upcoming'
        WHEN CURDATE() > end_date THEN 'completed'
        ELSE 'active'
    END AS status
    FROM elections ORDER BY id DESC");

// Find every election this voter has already voted in, so we can
// show "Already Voted" instead of "Vote Now" per election.
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
<title>Elections - Vote</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/style.css">
<style>
  .election-card { padding: 22px; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap; }
  .election-title { font-size: 15.5px; font-weight: 600; margin-bottom: 4px; }
  .election-desc { font-size: 12.5px; color: var(--text-muted); margin-bottom: 8px; }
  .election-dates { font-size: 11.5px; color: var(--text-faint); }
</style>
</head>
<body data-page="elections" data-user="<?php echo htmlspecialchars($_SESSION['full_name']); ?>">

<div id="layout-root"></div>

<main class="main">
  <div class="main-inner">

    <h1 class="page-title">Elections</h1>
    <p class="page-subtitle">Browse ongoing, upcoming, and completed elections</p>

    <?php if ($elections->num_rows === 0): ?>
      <div class="card section-card">
        <p style="color:var(--text-faint); font-size:13px;">No elections available yet.</p>
      </div>
    <?php endif; ?>

    <?php while ($e = $elections->fetch_assoc()):
      $has_voted = in_array($e['id'], $voted_elections);
    ?>
      <div class="card election-card">
        <div>
          <div class="election-title"><?php echo htmlspecialchars($e['title']); ?></div>
          <div class="election-desc"><?php echo htmlspecialchars($e['description']); ?></div>
          <div class="election-dates"><?php echo htmlspecialchars($e['start_date']); ?> to <?php echo htmlspecialchars($e['end_date']); ?></div>
        </div>

        <div style="display:flex; align-items:center; gap:14px;">
          <?php if ($e['status'] == 'active'): ?>
            <span class="badge badge-green"><span class="badge-dot"></span>Active</span>
          <?php elseif ($e['status'] == 'upcoming'): ?>
            <span class="badge badge-amber"><span class="badge-dot"></span>Upcoming</span>
          <?php else: ?>
            <span class="badge badge-blue"><span class="badge-dot"></span>Completed</span>
          <?php endif; ?>

          <?php if ($e['status'] == 'active' && $has_voted): ?>
            <a href="results.php?election_id=<?php echo $e['id']; ?>"><button class="btn btn-outline">View Results</button></a>
          <?php elseif ($e['status'] == 'active'): ?>
            <a href="candidates.php?election_id=<?php echo $e['id']; ?>"><button class="btn btn-primary">Vote Now</button></a>
          <?php elseif ($e['status'] == 'completed'): ?>
            <a href="results.php?election_id=<?php echo $e['id']; ?>"><button class="btn btn-outline">View Results</button></a>
          <?php else: ?>
            <button class="btn btn-outline" disabled>Not Available</button>
          <?php endif; ?>
        </div>
      </div>
    <?php endwhile; ?>

  </div>
</main>

<script src="../js/layout.js"></script>
</body>
</html>
