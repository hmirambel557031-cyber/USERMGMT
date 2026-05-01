<?php
require_once "../Components/auth.php";
require_once "../Components/pdo.php";
checkLogin();
checkAdmin();

$errors = [];
$success = "";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: admin_user.php");
    exit();
}

// fetch user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    header("Location: admin_user.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = ($_POST['role'] === 'admin') ? 'admin' : 'user';
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $nationality = trim($_POST['nationality'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $new_password = $_POST['new_password'] ?? '';

    if ($username === '') $errors[] = "Username is required.";
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";

    // check uniqueness excluding current user
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE (username = ? OR email = ?) AND id != ?");
        $stmt->execute([$username, $email, $id]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = "Username or email already in use by another account.";
        }
    }

    if (empty($errors)) {
        // update fields
        $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, role=?, firstname=?, lastname=?, gender=?, nationality=?, contact_number=? WHERE id=?");
        $stmt->execute([$username, $email, $role, $firstname, $lastname, $gender, $nationality, $contact_number, $id]);

        // update password if provided
        if ($new_password !== '') {
            $hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password=? WHERE id=?");
            $stmt->execute([$hash, $id]);
        }

        $success = "User updated successfully.";
        // refresh user data
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Edit User</title>
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
      <div class="card" style="max-width:600px;">
        <h2 class="h1">Edit User #<?= htmlspecialchars($user['id']) ?></h2>

        <?php foreach ($errors as $e): ?>
          <div class="alert error"><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>

        <?php if ($success): ?>
          <div class="alert success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST">
          <div class="form-grid">
            <label>Username
              <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
            </label>
            <label>Email
              <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </label>
          </div>

          <label>Role
            <select name="role">
              <option value="user" <?= ($user['role'] === 'user') ? 'selected' : '' ?>>User</option>
              <option value="admin" <?= ($user['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
            </select>
          </label>

          <div class="form-grid">
            <label>First name
              <input type="text" name="firstname" value="<?= htmlspecialchars($user['firstname']) ?>">
            </label>
            <label>Last name
              <input type="text" name="lastname" value="<?= htmlspecialchars($user['lastname']) ?>">
            </label>
          </div>

          <div class="form-grid">
            <label>Gender
              <input type="text" name="gender" value="<?= htmlspecialchars($user['gender']) ?>">
            </label>
            <label>Nationality
              <input type="text" name="nationality" value="<?= htmlspecialchars($user['nationality']) ?>">
            </label>
          </div>

          <label>Contact number
            <input type="text" name="contact_number" value="<?= htmlspecialchars($user['contact_number']) ?>">
          </label>

          <hr style="margin:20px 0;border:none;border-top:1px solid var(--border);">
          <p class="small">Leave password blank to keep current password.</p>
          <label>New Password
            <input type="password" name="new_password">
          </label>

          <div class="form-actions">
            <button class="btn" type="submit">Save Changes</button>
            <a class="btn ghost" href="admin_user.php">Back to Users</a>
          </div>
        </form>
      </div>
    </main>
  </div>
</body>
</html>
