<?php
require_once "../components/auth.php";
require_once "../components/pdo.php";
checkLogin();

$id = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id=?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (password_verify($_POST['current_password'], $user['password'])) {
        if ($_POST['new_password'] === $_POST['confirm_password']) {
            $newHash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password=? WHERE id=?");
            $stmt->execute([$newHash, $id]);
            $success = "Password updated successfully!";
        } else {
            $error = "Passwords do not match.";
        }
    } else {
        $error = "Current password is incorrect.";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Change Password</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="app">
    <div class="topbar">
      <div class="brand">User Account</div>
      <div class="user">
        <?= htmlspecialchars($_SESSION['firstname'] ?? '') ?>
        <a class="button" href="logout.php">Logout</a>
      </div>
    </div>

    <aside class="sidebar">
      <nav class="nav">
        <li><a href="profile.php">Profile</a></li>
        <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
          <li><a href="admin_user.php">Users</a></li>
        <?php endif; ?>
      </nav>
    </aside>

    <main>
      <div class="card" style="max-width:500px;">
        <h2 class="h1">Change Password</h2>

        <?php if ($error): ?>
          <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
          <div class="alert success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST">
          <label>Current Password
            <input type="password" name="current_password" required>
          </label>

          <label>New Password
            <input type="password" name="new_password" required>
          </label>

          <label>Confirm Password
            <input type="password" name="confirm_password" required>
          </label>

          <div class="form-actions">
            <button class="btn" type="submit">Update Password</button>
            <a class="btn ghost" href="profile.php">Back to Profile</a>
          </div>
        </form>
      </div>
    </main>
  </div>
</body>
</html>
