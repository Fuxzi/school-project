<?php
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/koneksi.php';

if (session_status() === PHP_SESSION_NONE) session_start();

include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/navbar.php';

if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf'];

$err = '';
$ok  = '';

$name  = trim((string)($_POST['name'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$msg   = trim((string)($_POST['message'] ?? ''));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!hash_equals($csrf, (string)($_POST['csrf'] ?? ''))) {
    $err = "CSRF tidak valid.";
  } elseif ($name === '' || $email === '' || $msg === '') {
    $err = "Nama, email, dan pesan wajib diisi.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $err = "Format email tidak valid.";
  } elseif (mb_strlen($msg) < 10) {
    $err = "Pesan minimal 10 karakter.";
  } else {
    // Opsional: simpan ke DB kalau tabel contact_messages ada
    // Kalau tabelnya tidak ada, halaman tetap jalan (cuma tampil sukses).
    $saved = false;

    $check = mysqli_query($conn, "SHOW TABLES LIKE 'contact_messages'");
    if ($check && mysqli_num_rows($check) > 0) {
      $userId = (int)($_SESSION['user']['id'] ?? 0);

      $st = mysqli_prepare($conn, "
        INSERT INTO contact_messages (user_id, name, email, message)
        VALUES (?,?,?,?)
      ");
      mysqli_stmt_bind_param($st, "isss", $userId, $name, $email, $msg);
      $saved = mysqli_stmt_execute($st);
      mysqli_stmt_close($st);
    }

    $ok = "Pesan terkirim. Terima kasih!";
    // reset form
    $name = $email = $msg = '';
  }
}
?>

<div class="max-w-6xl mx-auto px-4 py-6">
  <div class="glass-card rounded-2xl p-6">
    <h1 class="text-2xl font-extrabold text-white/90">Contact</h1>
    <p class="text-muted text-sm mt-2">
      Punya saran fitur, lapor bug, atau mau request produk untuk direview? Kirim pesan di bawah ini.
    </p>

    <?php if ($err): ?>
      <div class="input-glass rounded-lg mt-4 p-3 border border-red-500/30 text-red-100 text-sm">
        <?= htmlspecialchars($err) ?>
      </div>
    <?php endif; ?>

    <?php if ($ok): ?>
      <div class="input-glass rounded-lg mt-4 p-3 border border-emerald-400/30 text-emerald-100 text-sm">
        <?= htmlspecialchars($ok) ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="mt-4 grid gap-3">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">

      <div>
        <label class="text-white/70 text-sm">Nama</label>
        <input class="input-glass rounded-lg px-3 py-2 w-full text-white/90"
               name="name" value="<?= htmlspecialchars($name) ?>" placeholder="Nama kamu" required>
      </div>

      <div>
        <label class="text-white/70 text-sm">Email</label>
        <input class="input-glass rounded-lg px-3 py-2 w-full text-white/90"
               type="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="email@contoh.com" required>
      </div>

      <div>
        <label class="text-white/70 text-sm">Pesan</label>
        <textarea class="input-glass rounded-lg px-3 py-2 w-full text-white/90"
                  name="message" rows="5" placeholder="Tulis pesan..." required><?= htmlspecialchars($msg) ?></textarea>
      </div>

      <button class="btn-primary rounded-lg px-5 py-2 w-max" type="submit">
        Kirim Pesan
      </button>
    </form>
  </div>

  <div class="glass-card rounded-2xl p-6 mt-4">
    <h2 class="text-lg font-extrabold text-white/90">Info</h2>
    <div class="text-white/75 text-sm mt-2 space-y-1">
      <div>📌 Website: <?= APP_NAME ?></div>
      <div>🔐 Untuk urusan akun: gunakan menu Pengaturan untuk ganti password.</div>
      <div>🛠️ Admin: kelola produk & kategori lewat Admin Panel.</div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
