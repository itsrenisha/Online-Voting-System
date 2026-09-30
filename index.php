<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: voter/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Vote - Secure Online Voting</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">
<style>
  .lp-nav {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 48px;
    background: var(--card);
    border-bottom: 1px solid var(--border);
  }
  .lp-logo { display: flex; align-items: center; gap: 10px; }
  .lp-logo .logo-mark {
    width: 34px; height: 34px; background: var(--blue); border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 16px;
  }
  .lp-logo-text { line-height: 1.2; }
  .lp-logo-text .name { font-weight: 600; font-size: 15px; }
  .lp-logo-text .name span { color: var(--blue); }
  .lp-logo-text .tag { font-size: 10.5px; color: var(--text-muted); }
  .lp-links { display: flex; align-items: center; gap: 32px; }
  .lp-links a { font-size: 13.5px; font-weight: 500; color: var(--text-muted); transition: color 0.15s; }
  .lp-links a:hover { color: var(--blue); }
  .lp-nav-actions { display: flex; align-items: center; gap: 10px; }

  .lp-hero {
    text-align: center;
    max-width: 720px;
    margin: 0 auto;
    padding: 80px 48px;
  }
  .lp-eyebrow { color: var(--blue); font-size: 12.5px; font-weight: 600; letter-spacing: 0.4px; margin-bottom: 14px; }
  .lp-hero h1 { font-size: 44px; font-weight: 700; line-height: 1.15; letter-spacing: -1px; margin-bottom: 18px; }
  .lp-hero h1 .accent { color: var(--blue); }
  .lp-hero p { color: var(--text-muted); font-size: 15px; line-height: 1.6; max-width: 480px; margin: 0 auto 28px; }
  .lp-hero-actions { display: flex; gap: 12px; flex-wrap: wrap; justify-content: center; }
  .btn-lg { padding: 14px 28px; font-size: 15px; }

  .lp-features {
    max-width: 1200px; margin: 0 auto; padding: 0 48px 70px;
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px;
  }
  .feature-box { padding: 26px 22px; text-align: left; }
  .feature-box .stat-icon {
    width: 42px; height: 42px; border-radius: 11px;
    background: var(--blue-light); color: var(--blue);
    display: flex; align-items: center; justify-content: center; margin-bottom: 16px;
  }
  .feature-box h3 { font-size: 14.5px; font-weight: 600; margin-bottom: 6px; }
  .feature-box p { font-size: 12.5px; color: var(--text-muted); line-height: 1.55; }

  .lp-footer { text-align: center; padding: 24px; color: var(--text-faint); font-size: 12px; }

  .admin-link {
    font-size: 12.5px;
    font-weight: 500;
    color: var(--text-faint);
    padding: 0 4px;
  }
  .admin-link:hover { color: var(--text-muted); }

  @media (max-width: 950px) {
    .lp-hero { padding: 50px 28px; }
    .lp-hero h1 { font-size: 32px; }
    .lp-links { display: none; }
    .lp-features { grid-template-columns: repeat(2, 1fr); padding: 0 28px 50px; }
    .lp-nav { padding: 16px 20px; }
  }
  @media (max-width: 480px) { .lp-features { grid-template-columns: 1fr; } }
</style>
</head>
<body>

<nav class="lp-nav">
  <div class="lp-logo">
    <div class="lp-logo-text">
      <div class="name">Vote</div>
      <div class="tag">Online Voting System</div>
    </div>
  </div>
  <div class="lp-links">
    <a href="index.php">Home</a>
    <a href="#features">Features</a>
    <a href="#">Contact</a>
  </div>
  <div class="lp-nav-actions">
    <a href="admin/login.php" class="admin-link">Admin Login</a>
    <a href="login.php"><button class="btn btn-outline">Login</button></a>
    <a href="register.php"><button class="btn btn-primary">Register</button></a>
  </div>
</nav>

<section class="lp-hero">
  <div class="lp-eyebrow">SECURE · TRANSPARENT · RELIABLE</div>
  <h1>Every Vote, <span class="accent">Verified & Counted.</span></h1>
  <p>A simple digital platform to register, log in, and take part in elections — built with secure authentication so every vote is tied to a verified account.</p>
  <div class="lp-hero-actions">
    <a href="register.php"><button class="btn btn-primary btn-lg">Get Started</button></a>
    <a href="#features"><button class="btn btn-outline btn-lg">Learn More</button></a>
  </div>
</section>

<section class="lp-features" id="features">
  <div class="card feature-box">
    <div class="stat-icon"><svg class="icon" viewBox="0 0 24 24"><path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6l7-3Z"/><path d="m9 12 2 2 4-4"/></svg></div>
    <h3>Secure Login</h3>
    <p>Passwords are hashed and every session is protected before it reaches your dashboard.</p>
  </div>
  <div class="card feature-box">
    <div class="stat-icon"><svg class="icon" viewBox="0 0 24 24"><path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 13 9 5 9-5"/></svg></div>
    <h3>Simple to Use</h3>
    <p>A clean, guided flow from registration to casting your vote — no confusion.</p>
  </div>
  <div class="card feature-box">
    <div class="stat-icon"><svg class="icon" viewBox="0 0 24 24"><path d="M2 12s3.5-6.5 10-6.5S22 12 22 12s-3.5 6.5-10 6.5S2 12 2 12Z"/><circle cx="12" cy="12" r="2.8"/></svg></div>
    <h3>Transparent</h3>
    <p>Clear status updates at every step, from login to your vote being recorded.</p>
  </div>
  <div class="card feature-box">
    <div class="stat-icon"><svg class="icon" viewBox="0 0 24 24"><rect x="3" y="4" width="13" height="9" rx="1.2"/><rect x="17" y="8" width="4" height="12" rx="1"/><path d="M3 17h9"/></svg></div>
    <h3>Accessible Anywhere</h3>
    <p>Works on desktop and mobile, so you can take part from any device.</p>
  </div>
</section>


<footer class="lp-footer">
  © 2026 Vote — A college project.
</footer>

</body>
</html>
