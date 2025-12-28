<?php require_once __DIR__ . '/../../config/env.php'; ?>

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
    <nav class="flex gap-4 text-sm justify-end md:justify-start">
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

    </nav>

  </div>
</header>

<main class="max-w-6xl mx-auto px-4 py-8">
