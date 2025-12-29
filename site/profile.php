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

/* Ambil data user terbaru dari DB */
$st = mysqli_prepare($conn, "SELECT id, username, full_name, email, role, created_at FROM users WHERE id=? LIMIT 1");
mysqli_stmt_bind_param($st, "i", $userId);
mysqli_stmt_execute($st);
$res = mysqli_stmt_get_result($st);
$dbUser = $res ? mysqli_fetch_assoc($res) : null;
mysqli_stmt_close($st);

if (!$dbUser) {
  echo '<div class="max-w-6xl mx-auto px-4 py-6"><div class="glass-card rounded-2xl p-5 text-white/80">User tidak ditemukan.</div></div>';
  include __DIR__ . '/partials/footer.php';
  exit;
}

/* Handle update profile */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!hash_equals($csrf, (string)($_POST['csrf'] ?? ''))) {
    $err = "CSRF tidak valid.";
  } else {
    $full_name = trim((string)($_POST['full_name'] ?? ''));
    $email     = trim((string)($_POST['email'] ?? ''));

    // email opsional, tapi kalau diisi harus valid
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $err = "Format email tidak valid.";
    } else {
      $full_name_db = ($full_name !== '') ? $full_name : null;
      $email_db     = ($email !== '') ? $email : null;

      $st = mysqli_prepare($conn, "UPDATE users SET full_name=?, email=? WHERE id=?");
      mysqli_stmt_bind_param($st, "ssi", $full_name_db, $email_db, $userId);
      mysqli_stmt_execute($st);
      mysqli_stmt_close($st);

      $ok = "Profil berhasil disimpan.";

      // refresh data
      $dbUser['full_name'] = $full_name_db;
      $dbUser['email'] = $email_db;

      // sync session biar navbar update namanya
      $_SESSION['user']['name'] = $dbUser['full_name'];
    }
  }
}

/* Statistik user */
$reviewCount = 0;
$commentCount = 0;

$st = mysqli_prepare($conn, "SELECT COUNT(*) c FROM reviews WHERE user_id=?");
mysqli_stmt_bind_param($st, "i", $userId);
mysqli_stmt_execute($st);
$r = mysqli_stmt_get_result($st);
if ($r && ($row = mysqli_fetch_assoc($r))) $reviewCount = (int)$row['c'];
mysqli_stmt_close($st);

$st = mysqli_prepare($conn, "SELECT COUNT(*) c FROM comments WHERE user_id=?");
mysqli_stmt_bind_param($st, "i", $userId);
mysqli_stmt_execute($st);
$r = mysqli_stmt_get_result($st);
if ($r && ($row = mysqli_fetch_assoc($r))) $commentCount = (int)$row['c'];
mysqli_stmt_close($st);
?>

<div class="max-w-6xl mx-auto px-4 py-6">
  <div class="glass-card rounded-2xl p-5">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3">
      <div>
        <h1 class="text-2xl font-extrabold text-white/90">Profil Saya</h1>
        <p class="text-muted text-sm mt-1">Kelola info akun kamu.</p>
      </div>
      <a class="btn-primary rounded-lg px-4 py-2 w-max" href="<?= BASE_URL ?>/site/my-reviews.php">
        Lihat Review Saya
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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-4">
      <div class="input-glass rounded-2xl p-4">
        <div class="text-white/60 text-xs">Total Review</div>
        <div class="text-white/90 text-2xl font-extrabold mt-1"><?= $reviewCount ?></div>
      </div>
      <div class="input-glass rounded-2xl p-4">
        <div class="text-white/60 text-xs">Total Komentar</div>
        <div class="text-white/90 text-2xl font-extrabold mt-1"><?= $commentCount ?></div>
      </div>
      <div class="input-glass rounded-2xl p-4">
        <div class="text-white/60 text-xs">Role</div>
        <div class="text-white/90 text-2xl font-extrabold mt-1"><?= htmlspecialchars($dbUser['role']) ?></div>
      </div>
    </div>
  </div>

  <div class="glass-card rounded-2xl p-5 mt-4">
    <h2 class="text-lg font-extrabold text-white/90">Informasi Akun</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
      <div class="input-glass rounded-xl p-3">
        <div class="text-white/60 text-xs">Username</div>
        <div class="text-white/90 font-bold mt-1"><?= htmlspecialchars($dbUser['username']) ?></div>
      </div>

      <div class="input-glass rounded-xl p-3">
        <div class="text-white/60 text-xs">Dibuat</div>
        <div class="text-white/90 font-bold mt-1"><?= htmlspecialchars((string)$dbUser['created_at']) ?></div>
      </div>
    </div>

    <form method="POST" class="mt-4 grid gap-3">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">

      <div>
        <label class="text-white/70 text-sm">Nama Lengkap</label>
        <input class="input-glass rounded-lg px-3 py-2 w-full text-white/90"
               name="full_name"
               value="<?= htmlspecialchars((string)($dbUser['full_name'] ?? '')) ?>"
               placeholder="Nama lengkap (opsional)">
      </div>

      <div>
        <label class="text-white/70 text-sm">Email</label>
        <input class="input-glass rounded-lg px-3 py-2 w-full text-white/90"
               type="email"
               name="email"
               value="<?= htmlspecialchars((string)($dbUser['email'] ?? '')) ?>"
               placeholder="Email (opsional)">
      </div>

      <button class="btn-primary rounded-lg px-5 py-2 w-max" type="submit">
        Simpan Profil
      </button>
    </form>
  </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
