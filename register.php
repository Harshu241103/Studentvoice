<?php
require_once 'config/auth.php';
require_once 'config/db.php';
secure_session_start();

if (is_logged_in()) { header("Location: index.php"); exit; }

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $enroll   = trim($_POST['enroll']  ?? '');
        $college  = trim($_POST['college'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm']  ?? '';

        if ($enroll === '' || $college === '' || $password === '') {
            $error = 'All fields are required.';
        } elseif (strlen($enroll) > 20) {
            $error = 'Enrollment number too long (max 20 characters).';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters.';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match.';
        } else {
            $check = $pdo->prepare("SELECT id FROM students WHERE enrollment = ? LIMIT 1");
            $check->execute([$enroll]);
            if ($check->fetch()) {
                $error = 'This enrollment number is already registered.';
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                $stmt = $pdo->prepare("INSERT INTO students (enrollment, college, password) VALUES (?, ?, ?)");
                $stmt->execute([$enroll, $college, $hash]);
                $success = 'Account created! You can now log in.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register — StudentVoice</title>
<link rel="stylesheet" href="css/style.css">
<script src="js/script.js" defer></script>
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-logo">
            <div class="logo-mark">🎓</div>
            <h1>StudentVoice</h1>
            <p>Create your account</p>
        </div>
        <?php if ($error): ?>
        <div class="alert alert-error">⚠️ <?php echo h($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
        <div class="alert alert-success">✅ <?php echo h($success); ?>
            <a href="login.php" style="margin-left:8px;color:inherit;font-weight:700;">Login →</a>
        </div>
        <?php endif; ?>
        <?php if (!$success): ?>
        <form method="post" data-validate>
            <input type="hidden" name="csrf_token" value="<?php echo h(csrf_token()); ?>">
            <div class="form-group">
                <label for="enroll">Enrollment Number *</label>
                <input type="text" id="enroll" name="enroll"
                       placeholder="e.g. 2023CS001"
                       value="<?php echo h($_POST['enroll'] ?? ''); ?>"
                       required maxlength="20" autocomplete="username">
            </div>
            <div class="form-group">
                <label for="college">College / Institution *</label>
                <input type="text" id="college" name="college"
                       placeholder="e.g. GTU Engineering College"
                       value="<?php echo h($_POST['college'] ?? ''); ?>"
                       required maxlength="100">
            </div>
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password"
                       placeholder="At least 6 characters"
                       required minlength="6" autocomplete="new-password">
            </div>
            <div class="form-group">
                <label for="confirm">Confirm Password *</label>
                <input type="password" id="confirm" name="confirm"
                       placeholder="Repeat password"
                       required autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-primary">Create Account</button>
        </form>
        <?php endif; ?>
        <div class="auth-footer">
            Already have an account? <a href="login.php">Sign in</a>
        </div>
    </div>
</div>
</body>
</html>
