<?php
include '../includes/auth_check.php';
include '../config/db.php';

$user_id = $_SESSION['user_id'];
$error = "";

$election_id = isset($_GET['election_id']) ? (int)$_GET['election_id'] : (int)($_POST['election_id'] ?? 0);
$candidate_id = isset($_GET['candidate_id']) ? (int)$_GET['candidate_id'] : (int)($_POST['candidate_id'] ?? 0);

// Server-side guard — re-check even if the Candidates page check was bypassed
$check = $conn->prepare("SELECT id FROM votes WHERE user_id = ? AND election_id = ?");
$check->bind_param("ii", $user_id, $election_id);
$check->execute();
$check->store_result();
$already_voted = $check->num_rows > 0;
$check->close();

if ($already_voted) {
    header("Location: candidates.php?election_id=" . $election_id);
    exit();
}

// STEP 2: Store Vote — runs when the confirmation form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $conn->prepare("INSERT INTO votes (user_id, election_id, candidate_id) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $user_id, $election_id, $candidate_id);

   
    if ($stmt->execute()) {
    require_once '../includes/send_mail.php';
    sendVoteConfirmationEmail($_SESSION['email'], $_SESSION['full_name']);

    header("Location: vote_success.php?election_id=" . $election_id);
    exit();
}
     else {
        $error = "Something went wrong. You may have already voted.";
    }
    $stmt->close();
}

// STEP 1: Show confirmation screen — runs on the initial link click (GET)
$stmt = $conn->prepare("SELECT id, full_name, symbol FROM candidates WHERE id = ?");
$stmt->bind_param("i", $candidate_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: elections.php");
    exit();
}
$candidate = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Confirm Your Vote - Vote</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/style.css">
<style>
  .confirm-card { max-width: 460px; margin: 20px auto 0; padding: 34px; text-align: center; }
  .confirm-icon {
    width: 56px; height: 56px; border-radius: 50%;
    background: var(--blue-light); color: var(--blue);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 18px; font-size: 26px;
  }
  .confirm-card h2 { font-size: 17px; font-weight: 600; margin-bottom: 6px; }
  .confirm-card p { font-size: 13px; color: var(--text-muted); margin-bottom: 20px; }
  .confirm-candidate {
    background: var(--bg); border-radius: 10px; padding: 16px;
    margin-bottom: 22px; text-align: left;
  }
  .confirm-candidate .name { font-weight: 600; font-size: 14.5px; }
  .confirm-candidate .symbol { color: var(--blue); font-size: 12.5px; font-weight: 500; margin-top: 2px; }
  .confirm-actions { display: flex; gap: 10px; }
  .confirm-actions .btn { flex: 1; }
  .confirm-note { font-size: 11.5px; color: var(--text-faint); margin-top: 16px; }
</style>
</head>
<body data-page="elections" data-user="<?php echo htmlspecialchars($_SESSION['full_name']); ?>">

<div id="layout-root"></div>

<main class="main">
  <div class="main-inner">

    <div class="card confirm-card">
      <div class="confirm-icon"><svg class="icon" viewBox="0 0 24 24" style="width:26px;height:26px;"><rect x="4" y="9" width="16" height="11" rx="1.5"/><path d="M8 9V6a4 4 0 0 1 8 0v3"/><path d="M12 13v3"/></svg></div>
      <h2>Confirm Your Vote</h2>
      <p>Please review your choice before submitting. This cannot be changed afterward.</p>

      <?php if ($error): ?>
        <div class="message error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <div class="confirm-candidate">
        <div class="name"><?php echo htmlspecialchars($candidate['full_name']); ?></div>
        <div class="symbol"><?php echo htmlspecialchars($candidate['symbol']); ?></div>
      </div>

      <form method="POST" action="vote.php">
        <input type="hidden" name="election_id" value="<?php echo (int)$election_id; ?>">
        <input type="hidden" name="candidate_id" value="<?php echo (int)$candidate['id']; ?>">
        <div class="confirm-actions">
          <a href="candidates.php?election_id=<?php echo $election_id; ?>" style="flex:1;"><button type="button" class="btn btn-outline btn-block">Cancel</button></a>
          <button type="submit" class="btn btn-primary">Confirm Vote</button>
        </div>
      </form>

      <div class="confirm-note">Each account can vote only once per election.</div>
    </div>

  </div>
</main>

<script src="../js/layout.js"></script>
</body>
</html>
