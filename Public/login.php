<?php
require_once "../Components/pdo.php";
session_start();

// If already logged in, redirect by role
if (isset($_SESSION['user_id'])) {
    if (($_SESSION['role'] ?? '') === 'admin') {
        header('Location: admin_user.php'); exit;
    }
    header('Location: profile.php'); exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Safely read inputs
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($login === '' || $password === '') {
        $error = 'Please enter both login and password.';
    } else {
        // Fetch user by username or email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->execute([$login, $login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Adjust this if your DB column is named 'password_hash' instead of 'password'
        $storedHash = $user['password'] ?? $user['password_hash'] ?? null;

        if ($user && $storedHash && password_verify($password, $storedHash)) {
            // Successful login
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['firstname'] = $user['firstname'] ?? '';
            $_SESSION['lastname'] = $user['lastname'] ?? '';

            // Force password change if flagged (optional)
            if (!empty($user['must_change_password'])) {
                header('Location: change_password.php'); exit;
            }

            if ($user['role'] === 'admin') {
                header('Location: admin_user.php'); exit;
            } else {
                header('Location: profile.php'); exit;
            }
        } else {
            $error = 'Invalid login credentials.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="card" style="max-width:420px;margin:40px auto;">
    <h2 class="h1">Login</h2>

    <?php if ($error): ?>
      <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="">
      <label>Username or Email
        <input type="text" name="login" value="<?= htmlspecialchars($login ?? '') ?>" required>
      </label>

      <label>Password
        <input type="password" name="password" required>
      </label>

      <div class="form-actions">
        <button class="btn" type="submit">Login</button>
        <a class="btn ghost" href="index.php">Cancel</a>
      </div>
    </form>
  </div>
</body>
</html>
