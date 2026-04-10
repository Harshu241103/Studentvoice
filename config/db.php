<?php
// config/db.php — Place this file inside the config/ folder
define('DB_HOST', 'localhost');
define('DB_USER', 'root');   // XAMPP default
define('DB_PASS', '');       // XAMPP default — no password
define('DB_NAME', 'studentvoice');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die("
    <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:sans-serif;background:#0b0f1a;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:20px}
    .box{background:#1a0a0a;border:1px solid #6b1a1a;border-radius:14px;padding:32px;max-width:640px;width:100%}
    h2{color:#ff6b6b;margin-bottom:12px}
    code{background:#2a0000;padding:2px 7px;border-radius:5px;color:#ff9999;font-size:.85rem}
    ol{color:#ffcccc;font-size:.9rem;line-height:2.4;padding-left:20px}
    .err{background:#2a0000;border-radius:8px;padding:12px;color:#ff8888;font-size:.82rem;word-break:break-all;margin-bottom:14px}
    hr{border:none;border-top:1px solid #ff000022;margin:16px 0}
    a{color:#f0a500}
    </style>
    <div class='box'>
      <h2>&#10060; Database Connection Failed</h2>
      <div class='err'>" . htmlspecialchars($e->getMessage()) . "</div>
      <hr>
      <ol>
        <li>Open <b>XAMPP Control Panel</b> — make sure <b>Apache</b> and <b>MySQL</b> are both <b>green (running)</b></li>
        <li>Open <a href='http://localhost/phpmyadmin'>http://localhost/phpmyadmin</a></li>
        <li>In the left sidebar, look for a database named <code>studentvoice</code></li>
        <li>If it does <b>not exist</b>: click <b>New</b> → type <code>studentvoice</code> → click <b>Create</b></li>
        <li>Click on <code>studentvoice</code> → go to <b>Import</b> tab → choose file <code>database/studentvoice.sql</code> → click <b>Go</b></li>
        <li>Refresh this page — it should work now ✅</li>
      </ol>
      <hr>
      <p style='color:#aa8888;font-size:.8rem'>File: <code>config/db.php</code> &nbsp;|&nbsp; DB Name expected: <code>studentvoice</code></p>
    </div>");
}
