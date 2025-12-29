<?php
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/koneksi.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// kalau sudah login
if (!empty($_SESSION['user'])) {
  $to = (($_SESSION['user']['role'] ?? '') === 'admin') ? "/admin/" : "/site/";
  header("Location: " . BASE_URL . $to);
  exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = trim($_POST["username"] ?? "");
  $password = $_POST["password"] ?? "";

  if ($username === "" || $password === "") {
    $error = "Username dan password wajib diisi.";
  } else {
    $u = mysqli_real_escape_string($conn, $username);
    $sql = "SELECT id, username, password_hash, full_name, role
            FROM users
            WHERE username = '$u'
            LIMIT 1";
    $res = mysqli_query($conn, $sql);
    if (!$res) die("Query error: " . mysqli_error($conn));
    $user = mysqli_fetch_assoc($res);

    if ($user && password_verify($password, $user["password_hash"])) {
      $_SESSION["user"] = [
        "id" => (int)$user["id"],
        "username" => $user["username"],
        "name" => $user["full_name"],
        "role" => $user["role"],
      ];

      $to = ($user["role"] === "admin") ? "/admin/" : "/site/";
      header("Location: " . BASE_URL . $to);
      exit;
    }

    $error = "Username atau password salah!";
  }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login • <?= defined('APP_NAME') ? APP_NAME : 'App' ?></title>

  <!-- kalau kamu punya CSS global dari site, link-in di sini juga -->
  <!-- <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css"> -->

  <style>
    :root{
      --bg1:#050b18;
      --bg2:#091a33;
      --glass: rgba(255,255,255,.06);
      --stroke: rgba(255,255,255,.14);
      --text: rgba(255,255,255,.9);
      --muted: rgba(255,255,255,.65);
    }
    *{box-sizing:border-box}
    body{
      margin:0;
      min-height:100vh;
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, "Noto Sans", "Liberation Sans";
      color:var(--text);
      background:
        radial-gradient(900px 500px at 20% 10%, rgba(59,130,246,.25), transparent 60%),
        radial-gradient(900px 500px at 80% 20%, rgba(16,185,129,.18), transparent 55%),
        linear-gradient(180deg, var(--bg1), var(--bg2));
      display:flex;
      align-items:center;
      justify-content:center;
      padding:24px;
    }
    .wrap{
      width:min(980px, 100%);
      display:grid;
      grid-template-columns: 1.2fr .8fr;
      gap:18px;
      align-items:stretch;
    }
    @media (max-width: 860px){
      .wrap{grid-template-columns:1fr}
    }
    .card{
      background: rgba(255,255,255,.04);
      border:1px solid var(--stroke);
      border-radius:22px;
      box-shadow: 0 25px 80px rgba(0,0,0,.35);
      overflow:hidden;
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
    }
    .hero{
      padding:26px;
      display:flex;
      flex-direction:column;
      justify-content:space-between;
      position:relative;
    }
    .brand{
      display:flex;
      align-items:center;
      gap:12px;
    }
    .logo{
      width:56px;height:56px;border-radius:18px;
      background: rgba(255,255,255,.08);
      border:1px solid rgba(255,255,255,.12);
      display:flex;align-items:center;justify-content:center;
      overflow:hidden;
    }
    .logo img{width:100%;height:100%;object-fit:cover}
    .title{
      font-weight:900;
      font-size:24px;
      letter-spacing:.2px;
      margin:0;
    }
    .subtitle{
      margin:6px 0 0;
      color:var(--muted);
      font-size:13px;
      line-height:1.5;
      max-width:46ch;
    }
    .chips{display:flex;gap:10px;flex-wrap:wrap;margin-top:18px}
    .chip{
      font-size:12px;
      color:rgba(255,255,255,.85);
      padding:7px 10px;
      border-radius:999px;
      background: rgba(255,255,255,.08);
      border:1px solid rgba(255,255,255,.12);
    }

    .form{
      padding:26px;
    }
    .form h2{
      margin:0 0 6px;
      font-size:20px;
      font-weight:900;
    }
    .form p{
      margin:0 0 16px;
      color:var(--muted);
      font-size:13px;
    }
    .alert{
      margin: 0 0 14px;
      padding: 10px 12px;
      border-radius: 14px;
      background: rgba(239,68,68,.12);
      border: 1px solid rgba(239,68,68,.25);
      color: rgba(254,226,226,.95);
      font-size: 13px;
    }
    label{
      display:block;
      font-size:12px;
      color:rgba(255,255,255,.75);
      margin: 12px 0 8px;
    }
    .input{
      width:100%;
      padding: 12px 12px;
      border-radius: 14px;
      outline:none;
      border:1px solid rgba(255,255,255,.14);
      background: rgba(255,255,255,.06);
      color: var(--text);
    }
    .input::placeholder{color:rgba(255,255,255,.45)}
    .input:focus{
      border-color: rgba(59,130,246,.55);
      box-shadow: 0 0 0 4px rgba(59,130,246,.18);
    }
    .btn{
      margin-top: 16px;
      width:100%;
      padding: 12px 14px;
      border-radius: 14px;
      border:1px solid rgba(255,255,255,.14);
      background: linear-gradient(180deg, rgba(59,130,246,.9), rgba(37,99,235,.85));
      color:#fff;
      font-weight:800;
      cursor:pointer;
      transition: transform .12s ease, filter .12s ease;
    }
    .btn:hover{filter:brightness(1.02)}
    .btn:active{transform: translateY(1px)}
    .links{
      display:flex;
      justify-content:space-between;
      gap:10px;
      margin-top:12px;
      font-size:12px;
    }
    .links a{
      color: rgba(255,255,255,.72);
      text-decoration:none;
    }
    .links a:hover{color:#fff}
    .small{
      margin-top:18px;
      color:rgba(255,255,255,.55);
      font-size:12px;
      text-align:center;
    }
  </style>
</head>

<body>
  <div class="wrap">

    <!-- kiri: hero / branding -->
    <section class="card hero">
      <div>
        <div class="brand">
          <div class="logo">
            <img src="<?= BASE_URL ?>/assets/img/placeholder.png" alt="logo">
          </div>
          <div>
            <p class="title"><?= defined('APP_NAME') ? APP_NAME : 'App' ?></p>
            <p class="subtitle">Masuk untuk mengelola akun, memberi review, dan akses fitur yang tersedia di website.</p>
          </div>
        </div>

        <div class="chips">
          <span class="chip">⚡ Cepat</span>
          <span class="chip">🔒 Aman (bcrypt)</span>
          <span class="chip">🧊 Glass UI</span>
        </div>
      </div>

      <div class="small">
        <a href="<?= BASE_URL ?>/site/" style="color:rgba(255,255,255,.75);text-decoration:none;">
          ← Kembali ke Home
        </a>
      </div>
    </section>

    <!-- kanan: form -->
    <section class="card form">
      <h2>Login</h2>
      <p>Gunakan username dan password yang terdaftar.</p>

      <?php if ($error): ?>
        <div class="alert"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post" autocomplete="off">
        <label>Username</label>
        <input class="input" name="username" placeholder="admin" required>

        <label>Password</label>
        <input class="input" type="password" name="password" placeholder="••••••••" required>

        <button class="btn" type="submit">Masuk</button>

        <div class="links">
          <div class="links">
            <a href="<?= BASE_URL ?>/site/">Home</a>
            <a href="<?= BASE_URL ?>/auth/register.php">Daftar</a>
          </div>
        </div>
      </form>
    </section>

  </div>
</body>
</html>
