<?php
include '../includes/admin_auth_check.php';
include '../config/db.php';

// Get all elections for the dropdown
$elections = $conn->query("SELECT id, title FROM elections ORDER BY id DESC");

// Which election is currently selected? Default to the first one.
$election_id = isset($_GET['election_id']) ? (int)$_GET['election_id'] : 0;
if ($election_id === 0 && $elections->num_rows > 0) {
    $first = $elections->fetch_assoc();
    $election_id = $first['id'];
    $elections->data_seek(0); // rewind so the dropdown loop below sees all rows again
}

$results = [];
$total_votes = 0;

if ($election_id > 0) {
    $stmt = $conn->prepare(
        "SELECT c.full_name, c.symbol, COUNT(v.id) AS vote_count
         FROM candidates c
         LEFT JOIN votes v ON v.candidate_id = c.id AND v.election_id = c.election_id
         WHERE c.election_id = ?
         GROUP BY c.id
         ORDER BY vote_count DESC"
    );
    $stmt->bind_param("i", $election_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
    $stmt->close();
    $total_votes = array_sum(array_column($results, 'vote_count'));
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Results - Vote Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/style.css">
<style>
  .result-row { display: flex; align-items: center; gap: 14px; padding: 14px 0; border-bottom: 1px solid var(--border); }
  .result-row:first-child { padding-top: 0; }
  .result-row:last-child { border-bottom: none; padding-bottom: 0; }
  .result-name { font-weight: 600; font-size: 13.5px; }
  .result-symbol { font-size: 12px; color: var(--text-muted); }
  .result-bar-track { background: var(--bg); border-radius: 30px; height: 8px; margin-top: 6px; overflow: hidden; }
  .result-bar-fill { background: var(--blue); height: 100%; border-radius: 30px; }
  .result-count { font-weight: 600; font-size: 14px; min-width: 70px; text-align: right; }
  .election-select {
    margin-bottom: 20px; padding: 11px 13px; border: 1px solid var(--border);
    border-radius: var(--radius-sm); font-family: inherit; font-size: 13.5px; max-width: 320px;
  }
</style>
</head>
<body data-page="results" data-user="<?php echo htmlspecialchars($_SESSION['admin_name']); ?>">

<div id="layout-root"></div>

<main class="main">
  <div class="main-inner">

    <h1 class="page-title">Results</h1>
    <p class="page-subtitle">Select an election to see its results</p>

    <form method="GET" action="results.php">
      <select name="election_id" class="election-select" onchange="this.form.submit()">
        <?php $elections->data_seek(0); while ($e = $elections->fetch_assoc()): ?>
          <option value="<?php echo $e['id']; ?>" <?php echo $e['id'] == $election_id ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($e['title']); ?>
          </option>
        <?php endwhile; ?>
      </select>
    </form>

    <div class="card section-card">
      <?php if (empty($results)): ?>
        <p style="color:var(--text-faint); font-size:13px;">No candidates in this election yet.</p>
      <?php else: ?>
        <p class="page-subtitle" style="margin-bottom:16px;">Total votes cast: <?php echo $total_votes; ?></p>
        <?php foreach ($results as $r):
          $pct = $total_votes > 0 ? round(($r['vote_count'] / $total_votes) * 100) : 0;
        ?>
          <div class="result-row">
            <div style="flex:1;">
              <div class="result-name"><?php echo htmlspecialchars($r['full_name']); ?></div>
              <div class="result-symbol"><?php echo htmlspecialchars($r['symbol']); ?></div>
              <div class="result-bar-track">
                <div class="result-bar-fill" style="width:<?php echo $pct; ?>%;"></div>
              </div>
            </div>
            <div class="result-count"><?php echo (int)$r['vote_count']; ?> votes</div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </div>
</main>

<script src="../js/admin-layout.js"></script>
</body>
</html>
