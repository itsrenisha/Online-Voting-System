<?php
include '../includes/auth_check.php';
$election_id = isset($_GET['election_id']) ? (int)$_GET['election_id'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Vote Submitted - Vote</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/style.css">
<style>
  .success-card { max-width: 440px; margin: 40px auto 0; padding: 40px 34px; text-align: center; }
  .success-icon {
    width: 64px; height: 64px; border-radius: 50%;
    background: var(--green-light); color: var(--green);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px; font-size: 30px;
  }
  .success-card h2 { font-size: 18px; font-weight: 600; margin-bottom: 8px; }
  .success-card p { font-size: 13px; color: var(--text-muted); margin-bottom: 24px; }
</style>
</head>
<body data-page="candidates" data-user="<?php echo htmlspecialchars($_SESSION['full_name']); ?>">

<div id="layout-root"></div>

<main class="main">
  <div class="main-inner">

    <div class="card success-card">
      <div class="success-icon"><svg class="icon" viewBox="0 0 24 24" style="width:28px;height:28px;"><path d="m4 12.5 5 5 11-11"/></svg></div>
      <h2>Your vote has been recorded</h2>
      <p>Thank you for participating. Results will be available once you'd like to check them.</p>
      <a href="results.php?election_id=<?php echo $election_id; ?>"><button class="btn btn-primary btn-block">View Results</button></a>
    </div>

  </div>
</main>

<script src="../js/layout.js"></script>
</body>
</html>
