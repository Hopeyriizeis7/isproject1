<?php
require 'config.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $fullname = trim($_POST['fullname'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    // Server-side validation (never rely on the browser alone)
    if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) {
        $errors[] = 'Username must be 3-30 letters, numbers or underscores.';
    }
    if ($fullname === '' || strlen($fullname) > 100) {
        $errors[] = 'Please enter your full name.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if ($phone !== '' && !preg_match('/^\+?[0-9]{9,15}$/', $phone)) {
        $errors[] = 'Phone number must be 9-15 digits.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);   // hashed, never plain text
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO users (username, fullname, email, phone, password_hash)
                 VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([$username, $fullname, $email, $phone, $hash]);
            $success = true;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errors[] = 'That username is already taken.';
            } else {
                $errors[] = 'Could not create the account. Try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign up</title>
  <style>
    body{font-family:Arial,sans-serif;background:#f3f4f6;display:flex;justify-content:center;padding:40px}
    form{background:#fff;padding:24px;border-radius:8px;width:340px;box-shadow:0 2px 8px #0002}
    input{width:100%;padding:8px;margin:6px 0 14px;box-sizing:border-box}
    button{width:100%;padding:10px;background:#2563eb;color:#fff;border:0;border-radius:4px;cursor:pointer}
    .err{color:#b91c1c}.ok{color:#15803d}
  </style>
</head>
<body>
<form method="post" action="signup.php">
  <h2>Create account</h2>
  <?php foreach ($errors as $e): ?><p class="err"><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
  <?php if ($success): ?><p class="ok">Account created. <a href="login.php">Log in</a></p><?php endif; ?>

  <label>Username</label>
  <input name="username" required pattern="[A-Za-z0-9_]{3,30}" title="3-30 letters, numbers or _">
  <label>Full name</label>
  <input name="fullname" required maxlength="100">
  <label>Email</label>
  <input type="email" name="email" required>
  <label>Phone (optional)</label>
  <input type="tel" name="phone" pattern="\+?[0-9]{9,15}">
  <label>Password</label>
  <input type="password" name="password" required minlength="8">
  <button type="submit">Sign up</button>
  <p><a href="login.php">Already registered? Log in</a></p>
</form>
</body>
</html>
