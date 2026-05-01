<?php
require_once "../components/auth.php";
require_once "../components/pdo.php";
checkLogin();
checkAdmin();

$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>User Management</title>
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
        <li><a href="admin_user.php" class="active">Users</a></li>
        <li><a href="profile.php">Profile</a></li>
      </nav>
    </aside>

    <main>
      <div class="card">
        <h2 class="h1">All Users</h2>
        
        <div class="controls">
          <input type="text" id="searchBox" placeholder="Search by username or email...">
          <a class="btn" href="admin_user_create.php">+ Add User</a>
        </div>

        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Name</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $u): ?>
              <tr>
                <td><?= htmlspecialchars($u['id']) ?></td>
                <td><?= htmlspecialchars($u['username']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><span class="badge <?= $u['role'] === 'admin' ? 'badge-admin' : 'badge-user' ?>"><?= htmlspecialchars($u['role']) ?></span></td>
                <td><?= htmlspecialchars($u['firstname'] . " " . $u['lastname']) ?></td>
                <td class="actions">
                  <a class="view" href="#">View</a>
                  <a class="edit" href="admin_user_edit.php?id=<?= (int)$u['id'] ?>">Edit</a>
                  <a class="delete" href="admin_user_delete.php?id=<?= (int)$u['id'] ?>">Delete</a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <script>
    document.getElementById('searchBox').addEventListener('keyup', function(e) {
      const query = e.target.value.toLowerCase();
      document.querySelectorAll('tbody tr').forEach(row => {
        const username = row.cells[1].textContent.toLowerCase();
        const email = row.cells[2].textContent.toLowerCase();
        row.style.display = (username.includes(query) || email.includes(query)) ? '' : 'none';
      });
    });
  </script>
</body>
</html>
