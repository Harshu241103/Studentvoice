<?php
require_once 'config/auth.php';
require_once 'config/db.php';
secure_session_start();
require_login();

$valid_categories = ['Academic','Infrastructure','Library','Transport','Hostel','Other'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $title    = trim($_POST['title']    ?? '');
        $desc     = trim($_POST['desc']     ?? '');
        $category = $_POST['category'] ?? '';
        $enroll   = $_SESSION['user'];

        if ($title === '' || $desc === '') {
            $error = 'Title and description are required.';
        } elseif (strlen($title) > 200) {
            $error = 'Title must be under 200 characters.';
        } elseif (!in_array($category, $valid_categories, true)) {
            $error = 'Please select a valid category.';
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO problems (student_enroll, title, description, category) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$enroll, $title, $desc, $category]);
            header("Location: index.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Post Problem — StudentVoice</title>
<link rel="stylesheet" href="css/style.css">
<script src="js/script.js" defer></script>
</head>
<body>

<header>
    <a class="logo" href="index.php">🎓 Student<span>Voice</span></a>
    <nav>
        <a href="index.php"><span>🏠 </span>Home</a>
        <a href="post_problem.php" class="active btn-post"><span>✏️ </span>Post Problem</a>
        <a href="about.php"><span>ℹ️ </span>About</a>
        <a href="logout.php"><span>🚪 </span>Logout</a>
        <button id="themeToggle" class="theme-toggle" onclick="toggleTheme()">☀️</button>
    </nav>
</header>

<main>
    <div class="page-hero">
        <h2>Post a <span>Problem</span></h2>
        <p>Share an issue you're facing on campus — the community will vote on it.</p>
    </div>
    <div class="post-card">
        <?php if ($error): ?>
        <div class="alert alert-error">⚠️ <?php echo h($error); ?></div>
        <?php endif; ?>
        <form method="post" data-validate>
            <input type="hidden" name="csrf_token" value="<?php echo h(csrf_token()); ?>">
            <div class="form-group">
                <label for="title">Problem Title *</label>
                <input type="text" id="title" name="title"
                       placeholder="e.g. No seating in main library"
                       value="<?php echo h($_POST['title'] ?? ''); ?>"
                       required maxlength="200">
            </div>
            <div class="form-group">
                <label for="desc">Description *</label>
                <textarea id="desc" name="desc"
                          placeholder="Describe the issue in detail..."
                          required><?php echo h($_POST['desc'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="category">Category *</label>
                <select id="category" name="category" required>
                    <?php foreach ($valid_categories as $cat): ?>
                    <option value="<?php echo h($cat); ?>"
                        <?php echo (($_POST['category'] ?? '') === $cat) ? 'selected' : ''; ?>>
                        <?php echo h($cat); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:0.5rem;">
                <button type="submit" class="btn btn-primary" style="flex:1;min-width:140px;">🚀 Submit Problem</button>
                <a href="index.php" class="btn btn-secondary" style="flex:1;min-width:140px;text-align:center;">Cancel</a>
            </div>
        </form>
    </div>
</main>

<footer>
    <p>StudentVoice Platform &nbsp;·&nbsp; Raising voices, driving change</p>
</footer>
</body>
</html>
