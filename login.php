<?php
require_once 'config/auth.php';
require_once 'config/db.php';
secure_session_start();

if (is_logged_in()) { header("Location: index.php"); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $enroll   = trim($_POST['enroll'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($enroll === '' || $password === '') {
            $error = 'Please enter your enrollment number and password.';
        } else {
            $stmt = $pdo->prepare("SELECT id, enrollment, password FROM students WHERE enrollment = ? LIMIT 1");
            $stmt->execute([$enroll]);
            $student = $stmt->fetch();

            if ($student && password_verify($password, $student['password'])) {
                session_regenerate_id(true);
                $_SESSION['user']    = $student['enrollment'];
                $_SESSION['user_id'] = $student['id'];
                header("Location: index.php");
                exit;
            } else {
                $error = 'Invalid enrollment number or password.';
                sleep(1);
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
<title>Login — StudentVoice</title>
<link rel="stylesheet" href="css/style.css">
<script src="js/script.js" defer></script>
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-logo">
            <div class="logo-mark">🎓</div>
            <h1>StudentVoice</h1>
            <p>Sign in to your account</p>
        </div>
        <?php if ($error): ?>
        <div class="alert alert-error">⚠️ <?php echo h($error); ?></div>
        <?php endif; ?>
        <form method="post" data-validate>
            <input type="hidden" name="csrf_token" value="<?php echo h(csrf_token()); ?>">
            <div class="form-group">
                <label for="enroll">Enrollment Number</label>
                <input type="text" id="enroll" name="enroll"
                       placeholder="e.g. 2023CS001"
                       value="<?php echo h($_POST['enroll'] ?? ''); ?>"
                       required autocomplete="username">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       placeholder="Your password"
                       required autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-primary">Sign In</button>
        </form>
        <div class="auth-footer">
            Don't have an account? <a href="register.php">Create one</a>
        </div>
    </div>
</div>
</body>
</html>
