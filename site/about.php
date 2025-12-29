<?php
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/koneksi.php';

include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/navbar.php';
?>

<div class="max-w-6xl mx-auto px-4 py-6">
  <div class="glass-card rounded-2xl p-6">
    <h1 class="text-2xl font-extrabold text-white/90">Tentang <?= APP_NAME ?></h1>
    <p class="text-muted text-sm mt-2">
      <?= APP_NAME ?> adalah website review produk teknologi: sederhana, cepat, dan fokus pada pengalaman pengguna.
      Di sini kamu bisa melihat review, memberi rating, dan berbagi pendapat tanpa ribet fitur e-commerce yang tidak perlu.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-5">
      <div class="input-glass rounded-2xl p-4">
        <div class="text-white/90 font-extrabold">🎯 Fokus Review</div>
        <div class="text-muted text-sm mt-1">Bukan marketplace. Tidak ada cart, kupon, atau riwayat pembelian.</div>
      </div>
      <div class="input-glass rounded-2xl p-4">
        <div class="text-white/90 font-extrabold">🔒 Aman</div>
        <div class="text-muted text-sm mt-1">Password disimpan dengan hash bcrypt.</div>
      </div>
      <div class="input-glass rounded-2xl p-4">
        <div class="text-white/90 font-extrabold">🧊 Glass UI</div>
        <div class="text-muted text-sm mt-1">Tampilan modern, ringan, dan konsisten.</div>
      </div>
    </div>
  </div>

  <div class="glass-card rounded-2xl p-6 mt-4">
    <h2 class="text-lg font-extrabold text-white/90">Fitur Utama</h2>
    <ul class="text-white/75 text-sm mt-3 space-y-2 list-disc pl-5">
      <li>Daftar & login user</li>
      <li>Browse produk berdasarkan kategori</li>
      <li>Tulis review + rating</li>
      <li>Halaman “Review Saya” untuk mengelola review pribadi</li>
      <li>Admin panel untuk kelola produk & kategori</li>
    </ul>

    <div class="mt-5 flex flex-wrap gap-2">
      <a class="btn-primary rounded-lg px-4 py-2" href="<?= BASE_URL ?>/site/">
        Jelajahi Produk
      </a>
      <a class="input-glass rounded-lg px-4 py-2" href="<?= BASE_URL ?>/site/contact.php">
        Hubungi Kami
      </a>
    </div>
  </div>

  <div class="glass-card rounded-2xl p-6 mt-4">
    <h2 class="text-lg font-extrabold text-white/90">Catatan</h2>
    <p class="text-muted text-sm mt-2">
      <?= APP_NAME ?> dibuat untuk latihan/portofolio dan pembelajaran web (PHP + MySQL) dengan desain modern.
      Kalau kamu punya saran fitur yang tetap relevan untuk review website, langsung kirim lewat halaman Contact.
    </p>
  </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
