<?php
require_once 'config/auth.php';
require_once 'config/db.php';
secure_session_start();
require_login();

$voter_ip = get_voter_ip();

$stmt = $pdo->prepare("
    SELECT p.*,
           (SELECT COUNT(*) FROM vote_log WHERE problem_id = p.id AND voter_ip = ?) AS already_voted
    FROM problems p
    ORDER BY p.votes DESC
");
$stmt->execute([$voter_ip]);
$problems = $stmt->fetchAll();

$total_problems = count($problems);
$total_votes    = array_sum(array_column($problems, 'votes'));
$categories     = ['Academic','Infrastructure','Library','Transport','Hostel','Other'];
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>StudentVoice — Campus Problem Board</title>
<link rel="stylesheet" href="css/style.css">
<script src="js/script.js" defer></script>
<script>window._csrf = "<?php echo h(csrf_token()); ?>";</script>
</head>
<body>

<header>
    <a class="logo" href="index.php">🎓 Student<span>Voice</span></a>
    <nav>
        <a href="index.php" class="active"><span>🏠 </span>Home</a>
        <a href="post_problem.php" class="btn-post"><span>✏️ </span>Post Problem</a>
        <a href="about.php"><span>ℹ️ </span>About</a>
        <a href="logout.php"><span>🚪 </span>Logout</a>
        <button id="themeToggle" class="theme-toggle" onclick="toggleTheme()" title="Toggle theme">☀️</button>
    </nav>
</header>

<main>
    <div class="page-hero">
        <h2>Campus <span>Problem</span> Board</h2>
        <p>Logged in as <strong><?php echo h($_SESSION['user']); ?></strong> &nbsp;·&nbsp; Vote on issues that matter to you</p>
    </div>

    <div class="stats-bar">
        <div class="stat">
            <span class="stat-num"><?php echo $total_problems; ?></span>
            <span class="stat-label">Problems Reported</span>
        </div>
        <div class="stat">
            <span class="stat-num"><?php echo $total_votes; ?></span>
            <span class="stat-label">Total Votes Cast</span>
        </div>
        <div class="stat">
            <span class="stat-num"><?php echo count($categories); ?></span>
            <span class="stat-label">Categories</span>
        </div>
    </div>

    <div class="filter-bar">
        <button class="filter-btn active" data-filter="all">All</button>
        <?php foreach ($categories as $cat): ?>
        <button class="filter-btn" data-filter="<?php echo h($cat); ?>"><?php echo h($cat); ?></button>
        <?php endforeach; ?>
    </div>

    <div class="cards-grid">
        <?php if (empty($problems)): ?>
        <div class="empty-state">
            <div class="empty-icon">📭</div>
            <h3>No problems posted yet</h3>
            <p>Be the first to raise a campus issue.</p>
            <a href="post_problem.php" class="btn btn-primary" style="width:auto;margin-top:1rem;">Post a Problem</a>
        </div>
        <?php else: ?>
        <?php foreach ($problems as $i => $row):
            $catKey   = strtolower(str_replace(' ', '', $row['category']));
            $catClass = 'badge-' . $catKey;
            $voted    = (bool)$row['already_voted'];
        ?>
        <div class="card" data-category="<?php echo h($row['category']); ?>"
             style="animation-delay:<?php echo $i * 0.05; ?>s">
            <div class="card-body">
                <div class="card-title"><?php echo h($row['title']); ?></div>
                <div class="card-desc"><?php echo h($row['description']); ?></div>
                <div class="card-meta">
                    <span class="badge <?php echo h($catClass); ?>"><?php echo h($row['category']); ?></span>
                    <span class="card-time">🕐 <?php echo date('d M Y', strtotime($row['created_at'])); ?></span>
                </div>
            </div>
            <div class="vote-section">
                <button class="vote-btn <?php echo $voted ? 'voted' : ''; ?>"
                        onclick="castVote(<?php echo (int)$row['id']; ?>, this)"
                        title="<?php echo $voted ? 'Already voted' : 'Vote for this issue'; ?>">
                    <span class="vote-icon"><?php echo $voted ? '✅' : '👍'; ?></span>
                    <span class="vote-count"><?php echo (int)$row['votes']; ?></span>
                    <span style="font-size:0.7rem;color:var(--text-muted)"><?php echo $voted ? 'Voted' : 'Vote'; ?></span>
                </button>
            </div>
        </div>
        <?php endforeach; ?>
        <div class="empty-state" id="emptyState" style="display:none;">
            <div class="empty-icon">🔍</div>
            <h3>No problems in this category</h3>
            <p>Try a different filter or post one.</p>
        </div>
        <?php endif; ?>
    </div>
</main>

<footer>
    <p>StudentVoice Platform &nbsp;·&nbsp; Raising voices, driving change</p>
</footer>
</body>
</html>
