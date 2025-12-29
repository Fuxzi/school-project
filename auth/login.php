<?php
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/koneksi.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';

  $usernameEsc = mysqli_real_escape_string($conn, $username);
  $sql = "SELECT id, username, password_hash, full_name, role
          FROM users
          WHERE username = '$usernameEsc'
          LIMIT 1";
  $res = mysqli_query($conn, $sql);
  if (!$res) die("Query error: " . mysqli_error($conn));
  $user = mysqli_fetch_assoc($res);

  if ($user && password_verify($password, $user['password_hash'])) {
    $_SESSION['user'] = [
      'id' => (int)$user['id'],
      'username' => $user['username'],
      'name' => $user['full_name'],
      'role' => $user['role'],
    ];
    header("Location: " . BASE_URL . "/site/");
    exit;
  }

  $error = "Username atau password salah!";
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Login</title></head>
<body>
  <h2>Login</h2>
  <?php if ($error): ?><p style="color:red;"><?= htmlspecialchars($error) ?></p><?php endif; ?>
  <form method="post">
    <input name="username" placeholder="username" required><br><br>
    <input type="password" name="password" placeholder="password" required><br><br>
    <button type="submit">Masuk</button>
  </form>
</body>
</html>
