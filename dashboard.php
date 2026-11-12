<?php
session_start();
require 'config.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$errors = [];
$saved = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || strlen($name) > 100) { $errors[] = 'Enter your name.'; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Enter a valid email.'; }
    if ($message === '' || strlen($message) > 1000) { $errors[] = 'Message is required (max 1000 characters).'; }

    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO feedback (name, email, message) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, $message]);
        $saved = true;
    }
}

$rows = $pdo->query('SELECT id, name, email, message, submitted_at FROM feedback ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard</title>
  <style>
    body{font-family:Arial,sans-serif;background:#f3f4f6;padding:30px}
    .card{background:#fff;padding:20px;border-radius:8px;max-width:640px;margin:0 auto 20px;box-shadow:0 2px 8px #0002}
    input,textarea{width:100%;padding:8px;margin:6px 0 14px;box-sizing:border-box}
    button{padding:10px 18px;background:#2563eb;color:#fff;border:0;border-radius:4px;cursor:pointer}
    table{width:100%;border-collapse:collapse}th,td{border:1px solid #ddd;padding:6px;text-align:left}
    .err{color:#b91c1c}.ok{color:#15803d}
  </style>
</head>
<body>
<div class="card">
  <h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></h2>
  <a href="logout.php">Log out</a>
</div>

<div class="card">
  <h3>Course feedback form</h3>
  <?php foreach ($errors as $e): ?><p class="err"><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
  <?php if ($saved): ?><p class="ok">Thank you, your feedback was saved.</p><?php endif; ?>
  <form method="post" action="dashboard.php">
    <label>Name</label><input name="name" required maxlength="100">
    <label>Email</label><input type="email" name="email" required>
    <label>Message</label><textarea name="message" rows="4" required maxlength="1000"></textarea>
    <button type="submit">Submit</button>
  </form>
</div>

<div class="card">
  <h3>Saved feedback</h3>
  <table>
    <tr><th>ID</th><th>Name</th><th>Email</th><th>Message</th><th>When</th></tr>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= (int)$r['id'] ?></td>
        <td><?= htmlspecialchars($r['name']) ?></td>
        <td><?= htmlspecialchars($r['email']) ?></td>
        <td><?= htmlspecialchars($r['message']) ?></td>
        <td><?= htmlspecialchars($r['submitted_at']) ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>
</body>
</html>
