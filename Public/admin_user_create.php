<?php
require_once "../components/auth.php";
require_once "../components/pdo.php";
checkLogin();
checkAdmin();

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // collect and trim
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = ($_POST['role'] === 'admin') ? 'admin' : 'user';
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $nationality = trim($_POST['nationality'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $password = $_POST['password'] ?? '';

    // basic validation
    if ($username === '') $errors[] = "Username is required.";
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if ($password === '') $errors[] = "Password is required.";

    // check uniqueness
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = "Username or email already exists.";
        }
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, firstname, lastname, gender, nationality, contact_number, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$username, $email, $hash, $role, $firstname, $lastname, $gender, $nationality, $contact_number]);
        $success = "User created successfully.";
        // optionally redirect back to admin list
        header("Location: admin_user.php");
        exit();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Create User</title>
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
        <h2 class="h1">Create User</h2>

        <?php foreach ($errors as $e): ?>
          <div class="alert error"><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>

        <form method="POST">
          <div class="form-grid">
            <label>Username
              <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
            </label>
            <label>Email
              <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </label>
          </div>

          <label>Password
            <input type="password" name="password" required>
          </label>

          <label>Role
            <select name="role">
              <option value="user" <?= (($_POST['role'] ?? '') === 'user') ? 'selected' : '' ?>>User</option>
              <option value="admin" <?= (($_POST['role'] ?? '') === 'admin') ? 'selected' : '' ?>>Admin</option>
            </select>
          </label>

          <div class="form-grid">
            <label>First name
              <input type="text" name="firstname" value="<?= htmlspecialchars($_POST['firstname'] ?? '') ?>">
            </label>
            <label>Last name
              <input type="text" name="lastname" value="<?= htmlspecialchars($_POST['lastname'] ?? '') ?>">
            </label>
          </div>

          <div class="form-grid">
            <label>Gender
              <input type="text" name="gender" value="<?= htmlspecialchars($_POST['gender'] ?? '') ?>">
            </label>
            <label>Nationality
              <input type="text" name="nationality" value="<?= htmlspecialchars($_POST['nationality'] ?? '') ?>">
            </label>
          </div>

          <label>Contact number
            <input type="text" name="contact_number" value="<?= htmlspecialchars($_POST['contact_number'] ?? '') ?>">
          </label>

          <div class="form-actions">
            <button class="btn" type="submit">Create</button>
            <a class="btn ghost" href="admin_user.php">Cancel</a>
          </div>
        </form>
      </div>
    </main>
  </div>
</body>
</html>
