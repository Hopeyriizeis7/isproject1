<?php
session_start();
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, username, password_hash FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Log in</title>
  <style>
    body{font-family:Arial,sans-serif;background:#f3f4f6;display:flex;justify-content:center;padding:40px}
    form{background:#fff;padding:24px;border-radius:8px;width:340px;box-shadow:0 2px 8px #0002}
    input{width:100%;padding:8px;margin:6px 0 14px;box-sizing:border-box}
    button{width:100%;padding:10px;background:#2563eb;color:#fff;border:0;border-radius:4px;cursor:pointer}
    .err{color:#b91c1c}
  </style>
</head>
<body>
<form method="post" action="login.php">
  <h2>IS Project Login</h2>
  <?php if ($error): ?><p class="err"><?= htmlspecialchars($error) ?></p><?php endif; ?>
  <label>Username</label>
  <input name="username" required>
  <label>Password</label>
  <input type="password" name="password" required>
  <button type="submit">Log in</button>
  <p><a href="signup.php">Need an account? Sign up</a></p>
</form>
</body>
</html>
