<?php include '../includes/admin_auth_check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - Vote</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/style.css">
<style>
  .page-head { margin-bottom: 22px; }

  .section-card { padding: 22px; margin-bottom: 20px; }
  .section-title { font-size: 15px; font-weight: 600; margin-bottom: 16px; }

  .activity-item { display: flex; align-items: center; gap: 12px; padding: 11px 0; border-bottom: 1px solid var(--border); }
  .activity-item:first-child { padding-top: 0; }
  .activity-item:last-child { border-bottom: none; padding-bottom: 0; }
  .activity-icon {
    width: 34px; height: 34px; border-radius: 9px;
    background: var(--bg); color: var(--text-muted);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .activity-text { font-size: 12.5px; color: var(--text); }
  .activity-time { font-size: 11px; color: var(--text-faint); }
</style>
</head>
<body data-page="dashboard" data-user="<?php echo htmlspecialchars($_SESSION['admin_name']); ?>">

<div id="layout-root"></div>

<main class="main">
  <div class="main-inner">

    <div class="page-head">
      <h1 class="page-title">Admin Dashboard</h1>
      <p class="page-subtitle" style="margin-bottom:0;">Overview of the voting system</p>
    </div>

    <div class="card section-card">
      <div class="section-title">Recent Activity</div>
      <div class="activity-item">
        <div class="activity-icon" id="ic-act1"></div>
        <div>
          <div class="activity-text">No activity</div>
        </div>
      </div>
    </div>
  </div>
</main>

<script src="../js/admin-layout.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("ic-act1").innerHTML = ADMIN_ICONS.bell;
  });
</script>
</body>
</html>
