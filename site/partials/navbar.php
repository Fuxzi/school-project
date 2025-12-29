<?php
require_once __DIR__ . '/../../config/env.php';
$user = $_SESSION['user'] ?? null; // session sudah dimulai dari head/page
?>

<header class="navbar-glass sticky top-0 z-50">
  <div class="max-w-6xl mx-auto px-4 py-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">

    <!-- Brand -->
 <a href="<?= BASE_URL ?>/site/" class="flex items-center gap-3">
  <img
    src="<?= BASE_URL ?>/assets/img/placeholder.png"
    alt="<?= APP_NAME ?>"
    class="h-14 w-69 rounded-2xl ring-1 ring-white/15 shadow-lg"
  >
  <span class="font-extrabold text-xl text-white/90">
    <?= APP_NAME ?>
  </span>
</a>

    <!-- Search -->
    <form action="<?= BASE_URL ?>/site/search.php" method="GET" class="flex gap-2 w-full md:w-auto">
      <input
        name="q"
        placeholder="Cari produk..."
        class="input-glass rounded-lg px-3 py-2 w-full md:w-72"
      >
      <button class="btn-primary rounded-lg px-4 py-2">
        Search
      </button>
    </form>

    <!-- Nav -->
<nav class="flex gap-4 text-sm justify-end md:justify-start items-center">

  <a class="text-white/70 hover:text-white transition"
     style="text-shadow: 0 0 0 rgba(0,0,0,0);"
     href="<?= BASE_URL ?>/site/">
    Home
  </a>

  <a class="text-white/70 hover:text-white transition"
     href="<?= BASE_URL ?>/site/about.php">
    About
  </a>

  <a class="text-white/70 hover:text-white transition"
     href="<?= BASE_URL ?>/site/contact.php">
    Contact
  </a>

  <?php if ($user): ?>

    <!-- dropdown profile -->
    <div class="dropdown">
      <button type="button"
        class="input-glass rounded-lg px-3 py-2 text-white/80 hover:text-white transition flex items-center gap-2">
        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/10 ring-1 ring-white/15">
          👤
        </span>
        <span class="font-semibold">
          <?= htmlspecialchars(($user['name'] ?? '') ?: ($user['username'] ?? 'User')) ?>
        </span>
        <span class="text-white/50">▾</span>
      </button>
      <?php if (($user['role'] ?? '') === 'admin'): ?>
        <span style="
          font-size:11px;
          padding:2px 8px;
          border-radius:999px;
          background:rgba(59,130,246,.18);
          border:1px solid rgba(59,130,246,.35);
          margin-left:6px;
        ">Admin</span>
      <?php endif; ?>

      <div class="dropdown-panel">
        <div class="dropdown-glass">
          <div style="padding:14px; background:rgba(255,255,255,0.04);">
            <div style="font-weight:800; color:#fff; font-size:18px;">
              <?= htmlspecialchars(($user['name'] ?? '') ?: ($user['username'] ?? 'User')) ?>
            </div>
            <div style="color:rgba(255,255,255,0.6); font-size:12px;">
              Role: <?= htmlspecialchars($user['role'] ?? '-') ?>
            </div>
          </div>

          <div style="padding:10px;">
            <a class="dropdown-item" href="<?= BASE_URL ?>/site/profile.php">Profil Saya</a>
            <a class="dropdown-item" href="<?= BASE_URL ?>/site/my-reviews.php">Review Saya</a>
            <a class="dropdown-item" href="<?= BASE_URL ?>/site/settings.php">Pengaturan</a>

            <?php if (($user['role'] ?? '') === 'admin'): ?>
              <a class="dropdown-item" href="<?= BASE_URL ?>/admin/">Admin Panel</a>
            <?php endif; ?>
            
            <div class="dropdown-divider"></div>

            <!-- Logout dibuat tombol biar "keren" -->
            <a class="btn-primary rounded-lg px-4 py-2 block text-center"
               href="<?= BASE_URL ?>/auth/logout.php">
              Logout
            </a>
          </div>
        </div>
      </div>
    </div>

  <?php else: ?>

    <!-- Login jadi tombol sekelas Search -->
    <a class="btn-primary rounded-lg px-4 py-2"
       href="<?= BASE_URL ?>/auth/login.php">
      Login
    </a>

  <?php endif; ?>

</nav>


  </div>
</header>

<main class="max-w-6xl mx-auto px-4 py-8">
