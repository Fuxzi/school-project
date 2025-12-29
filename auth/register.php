<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/env.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username  = trim($_POST['username'] ?? '');
  $full_name = trim($_POST['full_name'] ?? '');
  $email     = trim($_POST['email'] ?? '');
  $password  = $_POST['password'] ?? '';
  $confirm   = $_POST['confirm'] ?? '';

  if ($username === '' || $password === '' || $confirm === '') {
    $error = "Username dan password wajib diisi.";
  } elseif ($password !== $confirm) {
    $error = "Password tidak sama.";
  } elseif (strlen($password) < 6) {
    $error = "Password minimal 6 karakter.";
  } else {
    $cek = mysqli_prepare($conn, "SELECT id FROM users WHERE username=?");
    mysqli_stmt_bind_param($cek, "s", $username);
    mysqli_stmt_execute($cek);
    mysqli_stmt_store_result($cek);

    if (mysqli_stmt_num_rows($cek) > 0) {
      $error = "Username sudah digunakan.";
    } else {
      $hash = password_hash($password, PASSWORD_BCRYPT);
$role = 'user';

$full_name_db = ($full_name !== '') ? $full_name : null;
$email_db     = ($email !== '') ? $email : null;

$st = mysqli_prepare(
  $conn,
  "INSERT INTO users (username, password_hash, full_name, email, role)
   VALUES (?,?,?,?,?)"
);

mysqli_stmt_bind_param(
  $st,
  "sssss",
  $username,
  $hash,
  $full_name_db,
  $email_db,
  $role
);

mysqli_stmt_execute($st);
$

      $success = "Registrasi berhasil. Silakan login.";
    }
    mysqli_stmt_close($cek);
  }
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Register • <?= APP_NAME ?></title>

<style>
:root{
  --bg1:#050b18;
  --bg2:#091a33;
  --glass:rgba(255,255,255,.06);
  --stroke:rgba(255,255,255,.14);
  --text:rgba(255,255,255,.9);
  --muted:rgba(255,255,255,.65);
}
*{box-sizing:border-box}
body{
  margin:0;
  min-height:100vh;
  font-family: ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Arial;
  color:var(--text);
  background:
    radial-gradient(900px 500px at 20% 10%, rgba(59,130,246,.22), transparent 60%),
    radial-gradient(900px 500px at 80% 20%, rgba(16,185,129,.16), transparent 55%),
    linear-gradient(180deg,var(--bg1),var(--bg2));
  display:flex;
  align-items:center;
  justify-content:center;
}
a{color:inherit;text-decoration:none}
.glass{
  background:var(--glass);
  border:1px solid var(--stroke);
  border-radius:22px;
  backdrop-filter:blur(14px);
  box-shadow:0 30px 90px rgba(0,0,0,.4);
}
.auth-wrap{
  width:100%;
  max-width:920px;
  padding:20px;
  display:grid;
  grid-template-columns:1.1fr .9fr;
  gap:20px;
}
@media(max-width:900px){
  .auth-wrap{grid-template-columns:1fr}
}
.auth-left{
  padding:26px;
}
.auth-right{
  padding:26px;
}
.h1{margin:0;font-size:22px;font-weight:900}
.muted{font-size:13px;color:var(--muted)}
.form{
  display:grid;
  gap:12px;
  margin-top:14px;
}
label{
  font-size:12px;
  color:var(--muted);
}
input{
  width:100%;
  padding:11px 14px;
  border-radius:14px;
  background:rgba(255,255,255,.06);
  border:1px solid rgba(255,255,255,.14);
  color:var(--text);
  outline:none;
}
input::placeholder{color:rgba(255,255,255,.45)}
input:focus{
  border-color:#3b82f6;
  box-shadow:0 0 0 2px rgba(59,130,246,.25);
}
.btn{
  margin-top:6px;
  padding:12px;
  border-radius:16px;
  border:1px solid rgba(255,255,255,.16);
  background:linear-gradient(135deg,#2563eb,#1d4ed8);
  color:#fff;
  font-weight:800;
  cursor:pointer;
}
.badge{
  display:inline-block;
  padding:6px 10px;
  border-radius:999px;
  font-size:12px;
  border:1px solid rgba(255,255,255,.16);
  background:rgba(255,255,255,.06);
}
</style>
</head>

<body>

<div class="auth-wrap">

  <div class="glass auth-left">
    <h2 class="h1"><?= APP_NAME ?> Reviews</h2>
    <p class="muted">Daftar untuk memberi review dan mengelola akun.</p>
    <div style="margin-top:12px">
      <span class="badge">🔒 Aman (bcrypt)</span>
      <span class="badge">⚡ Cepat</span>
      <span class="badge">🎨 Glass UI</span>
    </div>
  </div>

  <div class="glass auth-right">
    <h2 class="h1">Register</h2>
    <p class="muted">Buat akun baru</p>

    <?php if ($error): ?>
      <div class="badge" style="border-color:#ef4444;color:#fecaca"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="badge" style="border-color:#22c55e;color:#bbf7d0"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" class="form">
      <div>
        <label>Username *</label>
        <input name="username" required>
      </div>

      <div>
        <label>Nama Lengkap (opsional)</label>
        <input name="full_name">
      </div>

      <div>
        <label>Email (opsional)</label>
        <input type="email" name="email">
      </div>

      <div>
        <label>Password *</label>
        <input type="password" name="password" required>
      </div>

      <div>
        <label>Konfirmasi Password *</label>
        <input type="password" name="confirm" required>
      </div>

      <button class="btn">Daftar</button>
    </form>

    <p class="muted" style="margin-top:12px">
      Sudah punya akun?
      <a href="<?= BASE_URL ?>/auth/login.php">Login</a>
    </p>
  </div>

</div>

</body>
</html>
