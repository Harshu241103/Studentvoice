<?php
require_once 'config/auth.php';
require_once 'config/db.php';
secure_session_start();

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Please log in to vote.']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}
if (!verify_csrf($_POST['csrf'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Security check failed. Refresh and try again.']);
    exit;
}

$id       = (int)($_POST['id'] ?? 0);
$voter_ip = get_voter_ip();

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid problem ID.']);
    exit;
}

$check = $pdo->prepare("SELECT id FROM problems WHERE id = ? LIMIT 1");
$check->execute([$id]);
if (!$check->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Problem not found.']);
    exit;
}

try {
    $log = $pdo->prepare("INSERT INTO vote_log (problem_id, voter_ip) VALUES (?, ?)");
    $log->execute([$id, $voter_ip]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'You have already voted on this issue.']);
    exit;
}

$pdo->prepare("UPDATE problems SET votes = votes + 1 WHERE id = ?")->execute([$id]);

$fresh = $pdo->prepare("SELECT votes FROM problems WHERE id = ?");
$fresh->execute([$id]);
echo json_encode(['success' => true, 'votes' => (int)$fresh->fetchColumn()]);
