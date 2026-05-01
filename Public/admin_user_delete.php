<?php
require_once "../components/auth.php";
require_once "../components/pdo.php";
checkLogin();
checkAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: admin_user.php");
    exit();
}

// prevent deleting self
if ($id === (int)$_SESSION['user_id']) {
    // optional: prevent deleting yourself
    $_SESSION['flash_error'] = "You cannot delete your own account.";
    header("Location: admin_user.php");
    exit();
}

// fetch user to show confirmation
$stmt = $pdo->prepare("SELECT id, username, email, firstname, lastname FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    header("Location: admin_user.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // perform delete
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['flash_success'] = "User deleted.";
    header("Location: admin_user.php");
    exit();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Delete User</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="app">
    <div class="topbar">
      <div class="brand">User Management</div>
      <div class="user">
        Admin: <?= htmlspecialchars($_SESSION['firstname'] ?? 'Admin') ?>
        <a class="button" href="logout.php">Logout</a>
      </div>
    </div>

    <aside class="sidebar">
      <nav class="nav">
        <li><a href="admin_user.php">Users</a></li>
        <li><a href="profile.php">Profile</a></li>
      </nav>
    </aside>

    <main>
      <div class="card" style="max-width:500px;">
        <h2 class="h1">Delete User</h2>
        
        <div class="alert error">
          <strong>⚠ Warning:</strong> Are you sure you want to delete this user? This action cannot be undone.
        </div>

        <div style="background:rgba(239,68,68,0.02);padding:16px;border-radius:8px;border:1px solid rgba(239,68,68,0.12);margin-bottom:16px;">
          <p><strong><?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></strong></p>
          <p class="small">Username: <?= htmlspecialchars($user['username']) ?></p>
          <p class="small">Email: <?= htmlspecialchars($user['email']) ?></p>
          <p class="small">User ID: <?= htmlspecialchars($user['id']) ?></p>
        </div>

        <form method="POST">
          <div class="form-actions">
            <button class="btn danger" type="submit">Delete User</button>
            <a class="btn ghost" href="admin_user.php">Cancel</a>
          </div>
        </form>
      </div>
    </main>
  </div>
</body>
</html>
