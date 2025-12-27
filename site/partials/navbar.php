<?php require_once __DIR__ . '/../../config/env.php'; ?>
<header class="bg-white border-b">
  <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
    <a href="<?= BASE_URL ?>/site/" class="font-bold text-xl">
      <?= APP_NAME ?>
    </a>

    <form action="<?= BASE_URL ?>/site/search.php" method="GET" class="flex gap-2">
      <input name="q" placeholder="Cari produk..." class="border rounded-lg px-3 py-2 w-56">
      <button class="bg-slate-900 text-white rounded-lg px-4 py-2">Search</button>
    </form>

    <nav class="flex gap-4 text-sm">
      <a class="hover:underline" href="<?= BASE_URL ?>/site/">Home</a>
      <a class="hover:underline" href="<?= BASE_URL ?>/site/about.php">About</a>
      <a class="hover:underline" href="<?= BASE_URL ?>/site/contact.php">Contact</a>
      <a class="hover:underline" href="<?= BASE_URL ?>/admin/">Admin</a>
    </nav>
  </div>
</header>

<main class="max-w-6xl mx-auto px-4 py-8">
