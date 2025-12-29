<?php
require_once __DIR__ . '/../../config/env.php';
if (session_status() === PHP_SESSION_NONE) session_start();
?>

<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= APP_NAME ?></title>

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Theme (cosmic) -->
  <style>
    :root{
      /* Palette ngikut background cosmic kamu */
      --bg-overlay: rgba(11,16,32,.78);
      --card-bg: rgba(15,23,42,.72);
      --border: rgba(148,163,184,.18);

      --text-main: #e5e7eb;
      --text-muted: #94a3b8;

      --primary: #4cc9f0;   /* cyan glow */
      --secondary: #3a0ca3; /* deep purple */
      --accent: #f72585;    /* neon pink */
    }

    html, body { height: 100%; }

    body{
      background-image:
        linear-gradient(var(--bg-overlay), var(--bg-overlay)),
        url("<?= BASE_URL ?>/assets/img/background.jpg");
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      color: var(--text-main);
    }

    /* Helpers */
    .text-muted{ color: var(--text-muted); }

    .navbar-glass{
      background: rgba(11,16,32,.65);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border-bottom: 1px solid var(--border);
    }

    .glass-card{
      background: var(--card-bg);
      border: 1px solid var(--border);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      transition: all .25s ease;
    }
    .glass-card:hover{
      transform: translateY(-4px);
      box-shadow:
        0 0 0 1px rgba(76,201,240,.25),
        0 20px 40px rgba(0,0,0,.45);
    }

    .input-glass{
      background: rgba(15,23,42,.65);
      border: 1px solid var(--border);
      color: var(--text-main);
      outline: none;
    }
    .input-glass::placeholder{ color: var(--text-muted); }
    .input-glass:focus{
      border-color: rgba(76,201,240,.55);
      box-shadow: 0 0 0 3px rgba(76,201,240,.18);
    }

    .btn-primary{
      background: linear-gradient(135deg, var(--primary), var(--accent));
      color: #020617;
      font-weight: 700;
    }
    .btn-primary:hover{ filter: brightness(1.08); }

    a{ color: inherit; }
  </style>
  
  <style>
  /* Card content readability */
  .glass-card .meta { color: rgba(226,232,240,.78) !important; }      /* kategori/type */
  .glass-card .title { color: rgba(255,255,255,.92) !important; }     /* judul produk */
  .glass-card .desc { color: rgba(226,232,240,.70) !important; }      /* deskripsi */
  .glass-card .rating { color: rgba(255,255,255,.88) !important; }    /* rating */
  .glass-card .count { color: rgba(226,232,240,.65) !important; }     /* (x review) */

  /* Optional: card sedikit lebih terang biar teks kebaca */
  :root{ --card-bg: rgba(15,23,42,.78); }
</style>
<style>
  .btn-primary{
    transition: transform .12s ease, filter .12s ease, box-shadow .12s ease;
    box-shadow: 0 12px 35px rgba(236,72,153,.18);
  }
  .btn-primary:hover{
    transform: translateY(-1px);
    filter: brightness(1.03);
    box-shadow: 0 16px 45px rgba(236,72,153,.24);
  }
</style>

<style>
  /* helper dropdown hover */
  .dropdown { position: relative; }
  .dropdown-panel{
    position:absolute;
    right:0;
    top:calc(100% + 10px);
    min-width:280px;
    opacity:0;
    visibility:hidden;
    transform:translateY(-8px);
    transition:160ms ease;
    z-index:60;
  }
  .dropdown:hover .dropdown-panel,
  .dropdown:focus-within .dropdown-panel{
    opacity:1;
    visibility:visible;
    transform:translateY(0);
  }

  /* biar mirip tema glass kamu */
  .dropdown-glass{
    background: rgba(10, 18, 35, 0.92);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.12);
    box-shadow: 0 20px 50px rgba(0,0,0,0.35);
    border-radius: 18px;
    overflow:hidden;
  }
  .dropdown-item{
    display:block;
    padding:10px 12px;
    border-radius:12px;
    color: rgba(255,255,255,0.82);
    text-decoration:none;
    transition: .15s ease;
  }
  .dropdown-item:hover{
    background: rgba(255,255,255,0.10);
    color:#fff;
  }
  .dropdown-divider{
    height:1px;
    background: rgba(255,255,255,0.10);
    margin:8px 0;
  }
</style>

</head>

<body class="min-h-screen">
