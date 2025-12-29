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

$reviewId = (int)($_GET['id'] ?? 0);
if ($reviewId <= 0) {
  echo '<div class="max-w-6xl mx-auto px-4 py-6"><div class="glass-card rounded-2xl p-5 text-white/80">ID review tidak valid.</div></div>';
  include __DIR__ . '/partials/footer.php';
  exit;
}

/* Ambil review (pastikan milik user) */
$st = mysqli_prepare($conn, "
  SELECT r.id, r.rating, r.review_text, r.created_at,
         p.id AS product_id, p.title AS product_title
  FROM reviews r
  JOIN products p ON p.id = r.product_id
  WHERE r.id=? AND r.user_id=?
  LIMIT 1
");
mysqli_stmt_bind_param($st, "ii", $reviewId, $userId);
mysqli_stmt_execute($st);
$res = mysqli_stmt_get_result($st);
$data = $res ? mysqli_fetch_assoc($res) : null;
mysqli_stmt_close($st);

if (!$data) {
  echo '<div class="max-w-6xl mx-auto px-4 py-6"><div class="glass-card rounded-2xl p-5 text-white/80">Review tidak ditemukan / bukan milik kamu.</div></div>';
  include __DIR__ . '/partials/footer.php';
  exit;
}

/* Handle update */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!hash_equals($csrf, (string)($_POST['csrf'] ?? ''))) {
    $err = "CSRF tidak valid.";
  } else {
    $rating = (int)($_POST['rating'] ?? 0);
    $text   = trim((string)($_POST['review_text'] ?? ''));

    if ($rating < 1 || $rating > 5) {
      $err = "Rating harus 1 sampai 5.";
    } elseif ($text === '') {
      $err = "Review tidak boleh kosong.";
    } else {
      $st = mysqli_prepare($conn, "UPDATE reviews SET rating=?, review_text=? WHERE id=? AND user_id=?");
      mysqli_stmt_bind_param($st, "isii", $rating, $text, $reviewId, $userId);
      mysqli_stmt_execute($st);
      mysqli_stmt_close($st);

      $ok = "Review berhasil disimpan.";
      // refresh data
      $data['rating'] = $rating;
      $data['review_text'] = $text;
    }
  }
}
?>

<div class="max-w-6xl mx-auto px-4 py-6">
  <div class="glass-card rounded-2xl p-5">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3">
      <div>
        <h1 class="text-2xl font-extrabold text-white/90">Edit Review</h1>
        <p class="text-muted text-sm mt-1">
          Produk: <span class="text-white/80 font-semibold"><?= htmlspecialchars($data['product_title']) ?></span>
        </p>
      </div>

      <a class="input-glass rounded-lg px-4 py-2 w-max"
         href="<?= BASE_URL ?>/site/my-reviews.php">
        ← Kembali
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

    <form method="POST" class="mt-4 grid gap-3">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">

      <div>
        <label class="text-white/70 text-sm">Rating</label>
        <select name="rating" class="input-glass rounded-lg px-3 py-2 w-full text-white/90">
          <?php for ($i=1; $i<=5; $i++): ?>
            <option value="<?= $i ?>" <?= ((int)$data['rating'] === $i) ? 'selected' : '' ?>>
              <?= $i ?> / 5
            </option>
          <?php endfor; ?>
        </select>
      </div>

      <div>
        <label class="text-white/70 text-sm">Review</label>
        <textarea name="review_text" rows="5"
          class="input-glass rounded-lg px-3 py-2 w-full text-white/90"
          placeholder="Tulis review kamu..."><?= htmlspecialchars((string)$data['review_text']) ?></textarea>
      </div>

      <div class="flex gap-2">
        <button class="btn-primary rounded-lg px-5 py-2" type="submit">Simpan</button>
        <a class="input-glass rounded-lg px-5 py-2"
           href="<?= BASE_URL ?>/site/my-reviews.php">Batal</a>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
