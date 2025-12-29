<?php
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/auth.php';

require_login();

include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/navbar.php';

$user = $_SESSION['user'] ?? null;
$userId = (int)($user['id'] ?? 0);

if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf'];

$err = '';
$ok  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!hash_equals($csrf, (string)($_POST['csrf'] ?? ''))) {
    $err = "CSRF tidak valid.";
  } else {
    $current = (string)($_POST['current_password'] ?? '');
    $newpass = (string)($_POST['new_password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');

    if ($current === '' || $newpass === '' || $confirm === '') {
      $err = "Semua field wajib diisi.";
    } elseif (strlen($newpass) < 6) {
      $err = "Password baru minimal 6 karakter.";
    } elseif ($newpass !== $confirm) {
      $err = "Konfirmasi password tidak sama.";
    } else {
      // ambil hash lama
      $st = mysqli_prepare($conn, "SELECT password_hash FROM users WHERE id=? LIMIT 1");
      mysqli_stmt_bind_param($st, "i", $userId);
      mysqli_stmt_execute($st);
      $res = mysqli_stmt_get_result($st);
      $row = $res ? mysqli_fetch_assoc($res) : null;
      mysqli_stmt_close($st);

      if (!$row) {
        $err = "User tidak ditemukan.";
      } elseif (!password_verify($current, (string)$row['password_hash'])) {
        $err = "Password lama salah.";
      } else {
        $hash = password_hash($newpass, PASSWORD_BCRYPT);

        $st = mysqli_prepare($conn, "UPDATE users SET password_hash=? WHERE id=?");
        mysqli_stmt_bind_param($st, "si", $hash, $userId);
        mysqli_stmt_execute($st);
        mysqli_stmt_close($st);

        $ok = "Password berhasil diganti.";
      }
    }
  }
}
?>

<div class="max-w-6xl mx-auto px-4 py-6">
  <div class="glass-card rounded-2xl p-5">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3">
      <div>
        <h1 class="text-2xl font-extrabold text-white/90">Pengaturan</h1>
        <p class="text-muted text-sm mt-1">Kelola keamanan akun kamu.</p>
      </div>
      <a class="input-glass rounded-lg px-4 py-2 w-max" href="<?= BASE_URL ?>/site/profile.php">
        ← Kembali ke Profil
      </a>
    </div>

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
  </div>

  <div class="glass-card rounded-2xl p-5 mt-4">
    <h2 class="text-lg font-extrabold text-white/90">Ganti Password</h2>
    <p class="text-muted text-sm mt-1">Gunakan password yang kuat dan jangan dibagikan.</p>

    <form method="POST" class="mt-4 grid gap-3">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">

      <div>
        <label class="text-white/70 text-sm">Password Lama</label>
        <input class="input-glass rounded-lg px-3 py-2 w-full text-white/90"
               type="password" name="current_password" required
               placeholder="••••••••">
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
          <label class="text-white/70 text-sm">Password Baru</label>
          <input class="input-glass rounded-lg px-3 py-2 w-full text-white/90"
                 type="password" name="new_password" required
                 placeholder="Minimal 6 karakter">
        </div>

        <div>
          <label class="text-white/70 text-sm">Konfirmasi Password Baru</label>
          <input class="input-glass rounded-lg px-3 py-2 w-full text-white/90"
                 type="password" name="confirm_password" required
                 placeholder="Ulangi password baru">
        </div>
      </div>

      <button class="btn-primary rounded-lg px-5 py-2 w-max" type="submit">
        Simpan Password
      </button>
    </form>
  </div>

  <div class="glass-card rounded-2xl p-5 mt-4">
    <h2 class="text-lg font-extrabold text-white/90">Keluar</h2>
    <p class="text-muted text-sm mt-1">Kalau kamu login di perangkat umum, sebaiknya logout.</p>
    <a class="btn-primary rounded-lg px-5 py-2 inline-block mt-3"
       href="<?= BASE_URL ?>/auth/logout.php">
      Logout
    </a>
  </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
