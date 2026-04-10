<?php
require_once 'config/auth.php';
secure_session_start();
$logged_in = is_logged_in();
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About — StudentVoice</title>
<link rel="stylesheet" href="css/style.css">
<script src="js/script.js" defer></script>
</head>
<body>
<header>
    <a class="logo" href="index.php">🎓 Student<span>Voice</span></a>
    <nav>
        <a href="index.php"><span>🏠 </span>Home</a>
        <?php if ($logged_in): ?>
        <a href="post_problem.php" class="btn-post"><span>✏️ </span>Post Problem</a>
        <a href="about.php" class="active"><span>ℹ️ </span>About</a>
        <a href="logout.php"><span>🚪 </span>Logout</a>
        <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
        <?php endif; ?>
        <button id="themeToggle" class="theme-toggle" onclick="toggleTheme()">☀️</button>
    </nav>
</header>
<main>
    <div class="page-hero">
        <h2>About <span>StudentVoice</span></h2>
        <p>A student-driven platform to surface campus issues and drive real change.</p>
    </div>
    <div style="max-width:620px;line-height:1.8;color:var(--text-muted);margin-bottom:2.5rem;">
        <p>StudentVoice gives every enrolled student a secure space to highlight problems affecting campus life. By voting on issues, the community collectively ranks what matters most — giving administrators a clear view of where improvements are needed.</p>
    </div>
    <div class="about-grid">
        <div class="feature-card">
            <div class="feature-icon">🔒</div>
            <h3>Secure & Private</h3>
            <p>Passwords are hashed with bcrypt. No plain-text credentials are ever stored.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">👍</div>
            <h3>Vote-Based Ranking</h3>
            <p>Each student gets one vote per issue. Problems rise based on real community consensus.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📂</div>
            <h3>Organised by Category</h3>
            <p>Issues are tagged — Academic, Infrastructure, Library, Transport, Hostel — easy to filter.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📊</div>
            <h3>Admin Visibility</h3>
            <p>Administrators can monitor top-voted problems and act on the most pressing concerns.</p>
        </div>
    </div>
    <div style="margin-top:2.5rem;">
        <?php if (!$logged_in): ?>
        <a href="register.php" class="btn btn-primary" style="width:auto;display:inline-flex;">Get Started →</a>
        <?php else: ?>
        <a href="index.php" class="btn btn-primary" style="width:auto;display:inline-flex;">← Back to Board</a>
        <?php endif; ?>
    </div>
</main>
<footer>
    <p>StudentVoice Platform &nbsp;·&nbsp; Raising voices, driving change</p>
</footer>
</body>
</html>
