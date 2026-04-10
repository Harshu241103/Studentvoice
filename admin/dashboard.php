<?php
require_once '../config/auth.php';
require_once '../config/db.php';
secure_session_start();
require_login();   // must be logged in to view admin

$stmt = $pdo->query("SELECT * FROM problems ORDER BY votes DESC");
$problems = $stmt->fetchAll();

$total_problems = count($problems);
$total_votes    = array_sum(array_column($problems, 'votes'));

$students_stmt = $pdo->query("SELECT COUNT(*) FROM students");
$total_students = (int)$students_stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard — StudentVoice</title>
<link rel="stylesheet" href="../css/style.css">
<script src="../js/script.js" defer></script>
<style>
.admin-table-wrap { overflow-x: auto; margin-top: 1.5rem; }
table {
    width: 100%; border-collapse: collapse;
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    font-size: 0.875rem;
}
th {
    background: var(--bg3);
    color: var(--text-muted);
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 12px 16px;
    text-align: left;
    border-bottom: 1px solid var(--border);
}
td {
    padding: 12px 16px;
    border-bottom: 1px solid var(--border);
    color: var(--text);
    vertical-align: top;
}
tr:last-child td { border-bottom: none; }
tr:hover td { background: var(--bg3); }
.votes-pill {
    background: rgba(240,165,0,0.15);
    color: var(--accent);
    border: 1px solid rgba(240,165,0,0.3);
    padding: 2px 10px;
    border-radius: 999px;
    font-weight: 700;
    font-size: 0.8rem;
    display: inline-block;
}
.admin-stats { display:flex; gap:1.5rem; flex-wrap:wrap; margin-bottom:2rem; }
</style>
</head>
<body>

<header>
    <a class="logo" href="../index.php">🎓 Student<span>Voice</span></a>
    <nav>
        <a href="../index.php"><span>🏠 </span>Home</a>
        <a href="dashboard.php" class="active"><span>📊 </span>Admin</a>
        <a href="../logout.php"><span>🚪 </span>Logout</a>
        <button id="themeToggle" class="theme-toggle" onclick="toggleTheme()">☀️</button>
    </nav>
</header>

<main>
    <div class="page-hero">
        <h2>Admin <span>Dashboard</span></h2>
        <p>Overview of all reported campus problems, sorted by votes.</p>
    </div>

    <div class="admin-stats">
        <div class="stat">
            <span class="stat-num"><?php echo $total_problems; ?></span>
            <span class="stat-label">Total Problems</span>
        </div>
        <div class="stat">
            <span class="stat-num"><?php echo $total_votes; ?></span>
            <span class="stat-label">Total Votes</span>
        </div>
        <div class="stat">
            <span class="stat-num"><?php echo $total_students; ?></span>
            <span class="stat-label">Registered Students</span>
        </div>
    </div>

    <?php if (empty($problems)): ?>
    <div class="empty-state">
        <div class="empty-icon">📭</div>
        <h3>No problems reported yet</h3>
        <p>Problems posted by students will appear here.</p>
    </div>
    <?php else: ?>
    <div class="admin-table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Votes</th>
                    <th>Posted</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($problems as $i => $row): ?>
                <tr>
                    <td style="color:var(--text-muted)"><?php echo $i + 1; ?></td>
                    <td><?php echo h($row['student_enroll']); ?></td>
                    <td style="max-width:280px">
                        <strong><?php echo h($row['title']); ?></strong>
                        <div style="color:var(--text-muted);font-size:0.8rem;margin-top:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:260px">
                            <?php echo h($row['description']); ?>
                        </div>
                    </td>
                    <td><span class="badge badge-<?php echo strtolower(str_replace(' ','',$row['category'])); ?>"><?php echo h($row['category']); ?></span></td>
                    <td><span class="votes-pill"><?php echo (int)$row['votes']; ?></span></td>
                    <td style="color:var(--text-muted);white-space:nowrap"><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</main>

<footer>
    <p>StudentVoice Admin Panel &nbsp;·&nbsp; Logged in as <?php echo h($_SESSION['user']); ?></p>
</footer>
</body>
</html>
