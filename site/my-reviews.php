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

/* Hapus review */
if (isset($_GET['delete'])) {
  if (!hash_equals($csrf, (string)($_GET['csrf'] ?? ''))) {
    $err = "CSRF tidak valid.";
  } else {
    $rid = (int)$_GET['delete'];

    $st = mysqli_prepare($conn, "DELETE FROM reviews WHERE id=? AND user_id=?");
    mysqli_stmt_bind_param($st, "ii", $rid, $userId);
    mysqli_stmt_execute($st);

    if (mysqli_stmt_affected_rows($st) > 0) $ok = "Review berhasil dihapus.";
    else $err = "Review tidak ditemukan / bukan milik kamu.";

    mysqli_stmt_close($st);
  }
}

/* Ambil daftar review user */
$st = mysqli_prepare($conn, "
  SELECT r.id, r.rating, r.review_text, r.created_at,
         p.id AS product_id, p.title AS product_title
  FROM reviews r
  JOIN products p ON p.id = r.product_id
  WHERE r.user_id=?
  ORDER BY r.created_at DESC
");
mysqli_stmt_bind_param($st, "i", $userId);
mysqli_stmt_execute($st);
$res = mysqli_stmt_get_result($st);

$rows = [];
while ($res && ($row = mysqli_fetch_assoc($res))) $rows[] = $row;

mysqli_stmt_close($st);
?>

<div class="max-w-6xl mx-auto px-4 py-6">
  <div class="glass-card rounded-2xl p-5">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3">
      <div>
        <h1 class="text-2xl font-extrabold text-white/90">Review Saya</h1>
        <p class="text-muted text-sm mt-1">Daftar review yang pernah kamu buat.</p>
      </div>
      <a class="btn-primary rounded-lg px-4 py-2 w-max" href="<?= BASE_URL ?>/site/">
        Cari produk
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

  <div class="mt-4 grid gap-3">
    <?php if (count($rows) === 0): ?>
      <div class="glass-card rounded-2xl p-5">
        <div class="font-bold text-white/80">Belum ada review.</div>
        <div class="text-muted text-sm mt-1">Buka produk lalu tulis review pertamamu.</div>
      </div>
    <?php else: ?>
      <?php foreach ($rows as $r): ?>
        <div class="glass-card rounded-2xl p-5">
          <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
            <div>
              <div class="title font-extrabold text-white/90">
                <?= htmlspecialchars($r['product_title']) ?>
              </div>
              <div class="meta text-sm mt-1">
                Rating: <span class="font-bold text-white/90"><?= (int)$r['rating'] ?>/5</span>
                <span class="text-white/50">•</span>
                <span class="text-white/60"><?= htmlspecialchars((string)$r['created_at']) ?></span>
              </div>
            </div>

            <div class="flex gap-2">
              <a class="btn-primary rounded-lg px-4 py-2"
                 href="<?= BASE_URL ?>/site/edit-review.php?id=<?= (int)$r['id'] ?>">
                Edit
              </a>

              <a class="input-glass rounded-lg px-4 py-2 border border-red-500/30 text-red-100"
                 href="<?= BASE_URL ?>/site/my-reviews.php?delete=<?= (int)$r['id'] ?>&csrf=<?= htmlspecialchars($csrf) ?>"
                 onclick="return confirm('Hapus review ini?')">
                Hapus
              </a>
            </div>
          </div>

          <?php if (!empty($r['review_text'])): ?>
            <div class="desc text-white/80 mt-3 leading-relaxed">
              <?= nl2br(htmlspecialchars($r['review_text'])) ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
