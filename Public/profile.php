<?php
require_once "../Components/auth.php";
require_once "../Components/pdo.php";
checkLogin();

$id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE users SET firstname=?, lastname=?, gender=?, nationality=?, contact_number=? WHERE id=?");
    $stmt->execute([
        $_POST['firstname'], $_POST['lastname'], $_POST['gender'],
        $_POST['nationality'], $_POST['contact_number'], $id
    ]);
    $success = "Profile updated!";
    // Refresh user data
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>My Profile</title>
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
        <li><a href="profile.php" class="active">Profile</a></li>
        <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
          <li><a href="admin_user.php">Users</a></li>
        <?php endif; ?>
      </nav>
    </aside>

    <main>
      <?php if ($success): ?>
        <div class="alert success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>

      <!-- Profile Card -->
      <div class="card">
        <h2 class="h1">Profile</h2>
        <div style="line-height:1.8;">
          <p><strong><?= htmlspecialchars($user['firstname'] . " " . $user['lastname']) ?></strong></p>
          <p class="small">Username: <?= htmlspecialchars($user['username']) ?></p>
          <p class="small">Email: <?= htmlspecialchars($user['email']) ?></p>
          <p class="small">Member since: <?= date('M d, Y', strtotime($user['created_at'])) ?></p>
        </div>
      </div>

      <!-- Edit Details Form -->
      <div class="card">
        <h3 class="h1">Edit My Details</h3>
        <form method="POST">
          <div class="form-grid">
            <label>First Name
              <input type="text" name="firstname" value="<?= htmlspecialchars($user['firstname'] ?? '') ?>">
            </label>
            
            <label>Last Name
              <input type="text" name="lastname" value="<?= htmlspecialchars($user['lastname'] ?? '') ?>">
            </label>
          </div>

          <div class="form-grid">
            <label>Gender
              <input type="text" name="gender" value="<?= htmlspecialchars($user['gender'] ?? '') ?>">
            </label>
            
            <label>Nationality
              <input type="text" name="nationality" value="<?= htmlspecialchars($user['nationality'] ?? '') ?>">
            </label>
          </div>

          <label>Contact Number
            <input type="text" name="contact_number" value="<?= htmlspecialchars($user['contact_number'] ?? '') ?>">
          </label>

          <div class="form-actions">
            <button class="btn" type="submit">Save Changes</button>
          </div>
        </form>
      </div>

      <!-- Change Password -->
      <div class="card">
        <h3 class="h1">Change Password</h3>
        <p class="small">Update your password to keep your account secure.</p>
        <div class="form-actions">
          <a class="btn ghost" href="change_password.php">Change Password</a>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
