<?php
require_once __DIR__ . '/../../config/env.php';
require_once __DIR__ . '/../../config/auth.php';
require_admin();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin • <?= defined('APP_NAME') ? APP_NAME : 'App' ?></title>

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
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial;
      color:var(--text);
      background:
        radial-gradient(900px 500px at 20% 10%, rgba(59,130,246,.22), transparent 60%),
        radial-gradient(900px 500px at 80% 20%, rgba(16,185,129,.16), transparent 55%),
        linear-gradient(180deg, var(--bg1), var(--bg2));
    }
    a{color:inherit;text-decoration:none}
    .layout{
      max-width:1200px;
      margin:0 auto;
      padding:18px;
      display:grid;
      grid-template-columns: 260px 1fr;
      gap:16px;
    }
    @media (max-width: 920px){
      .layout{grid-template-columns:1fr}
      .sidebar{position:sticky; top:12px}
    }
    .glass{
      background: rgba(255,255,255,.04);
      border:1px solid var(--stroke);
      border-radius:18px;
      box-shadow: 0 25px 80px rgba(0,0,0,.35);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
    }
    .topbar{
      max-width:1200px;
      margin:0 auto;
      padding:14px 18px 0;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
    }
    .brand{
      display:flex; align-items:center; gap:10px;
      font-weight:900;
      letter-spacing:.2px;
    }
    .pill{
      display:inline-flex; align-items:center; gap:8px;
      padding:8px 10px;
      border-radius:999px;
      background: rgba(255,255,255,.06);
      border:1px solid rgba(255,255,255,.12);
      color: rgba(255,255,255,.82);
      font-size:12px;
    }
    .btn{
      display:inline-flex; align-items:center; justify-content:center;
      padding:9px 12px;
      border-radius:12px;
      background: rgba(255,255,255,.06);
      border:1px solid rgba(255,255,255,.12);
      transition:.15s ease;
      font-weight:700;
      font-size:13px;
    }
    .btn:hover{background: rgba(255,255,255,.10)}
    .h1{font-size:20px;font-weight:900;margin:0}
    .muted{color:var(--muted);font-size:13px}
    .card{padding:14px}
    .grid{display:grid; grid-template-columns: repeat(3, 1fr); gap:12px}
    @media (max-width: 920px){ .grid{grid-template-columns:1fr} }
    .stat{
      padding:14px;
      border-radius:16px;
      background: rgba(255,255,255,.04);
      border:1px solid rgba(255,255,255,.10);
    }
    .stat .k{color:var(--muted);font-size:12px}
    .stat .v{font-size:22px;font-weight:900;margin-top:6px}
    table{width:100%; border-collapse:separate; border-spacing:0 10px}
    th{font-size:12px; color:rgba(255,255,255,.65); text-align:left; padding:0 12px}
    td{
      padding:12px;
      background: rgba(255,255,255,.04);
      border-top:1px solid rgba(255,255,255,.10);
      border-bottom:1px solid rgba(255,255,255,.10);
    }
    tr td:first-child{border-left:1px solid rgba(255,255,255,.10); border-radius:14px 0 0 14px}
    tr td:last-child{border-right:1px solid rgba(255,255,255,.10); border-radius:0 14px 14px 0}
    .badge{
      display:inline-flex; align-items:center;
      padding:4px 8px;
      border-radius:999px;
      font-size:12px;
      border:1px solid rgba(255,255,255,.12);
      background: rgba(255,255,255,.06);
    }
  </style>
</head>
<body>

  <!-- topbar -->
  <div class="topbar">
    <div class="brand">
      🛠️ Admin Panel • <?= defined('APP_NAME') ? APP_NAME : 'App' ?>
      <span class="pill">Login sebagai: <?= htmlspecialchars($_SESSION['user']['username'] ?? 'admin') ?></span>
    </div>
    <div style="display:flex; gap:10px; align-items:center;">
      <a class="btn" href="<?= BASE_URL ?>/site/">Ke Site</a>
      <a class="btn" href="<?= BASE_URL ?>/auth/logout.php">Logout</a>
    </div>
  </div>

  <div class="layout">
