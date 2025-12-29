<?php
require_once __DIR__ . '/../config/koneksi.php';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';

if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
$csrf = $_SESSION['csrf'];

$err = '';
$ok  = '';

if (isset($_GET['delete'])) {
  if (!hash_equals($csrf, (string)($_GET['csrf'] ?? ''))) $err = "CSRF tidak valid.";
  else {
    $id = (int)$_GET['delete'];
    $st = mysqli_prepare($conn, "DELETE FROM reviews WHERE id=?");
    mysqli_stmt_bind_param($st, "i", $id);
    mysqli_stmt_execute($st);
    mysqli_stmt_close($st);
    header("Location: " . BASE_URL . "/admin/reviews.php?ok=deleted");
    exit;
  }
}

if (isset($_GET['ok']) && $_GET['ok'] === 'deleted') $ok = "Review dihapus.";

$q = trim((string)($_GET['q'] ?? ''));
$sql = "
  SELECT r.id, r.rating, r.review_text, r.created_at,
         u.username,
         p.title,
         (SELECT COUNT(*) FROM comments c WHERE c.review_id = r.id) AS total_comments
  FROM reviews r
  JOIN users u ON u.id = r.user_id
  JOIN products p ON p.id = r.product_id
";
if ($q !== '') {
  $qq = '%' . mysqli_real_escape_string($conn, $q) . '%';
  $sql .= " WHERE u.username LIKE '$qq' OR p.title LIKE '$qq' ";
}
$sql .= " ORDER BY r.created_at DESC";
$res = mysqli_query($conn, $sql);
?>
<main class="glass card">
  <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:12px;">
    <div>
      <h1 class="h1">Reviews</h1>
      <div class="muted">Moderasi review user.</div>
    </div>

    <form method="GET" style="display:flex;gap:10px;align-items:center;">
      <input name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Cari user/produk..." style="padding:10px 12px;border-radius:12px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);color:rgba(255,255,255,.9)">
      <button class="btn" type="submit">Cari</button>
      <a class="btn" href="<?= BASE_URL ?>/admin/reviews.php">Reset</a>
    </form>
  </div>

  <?php if ($err): ?>
    <div class="card glass" style="margin-top:12px;border-color:rgba(239,68,68,.35)"><?= htmlspecialchars($err) ?></div>
  <?php endif; ?>
  <?php if ($ok): ?>
    <div class="card glass" style="margin-top:12px;border-color:rgba(34,197,94,.35)"><?= htmlspecialchars($ok) ?></div>
  <?php endif; ?>

  <div class="card glass" style="margin-top:14px;">
    <div style="font-weight:900;margin-bottom:10px;">Daftar Review</div>

    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>User</th>
          <th>Produk</th>
          <th>Rating</th>
          <th>Komentar</th>
          <th>Comments</th>
          <th>Tanggal</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($res && mysqli_num_rows($res) > 0): ?>
        <?php while($r = mysqli_fetch_assoc($res)): ?>
          <tr>
            <td><?= (int)$r['id'] ?></td>
            <td><?= htmlspecialchars($r['username']) ?></td>
            <td><?= htmlspecialchars($r['title']) ?></td>
            <td><span class="badge"><?= (int)$r['rating'] ?>/5</span></td>
            <td class="muted" style="max-width:340px;word-break:break-word;"><?= htmlspecialchars((string)($r['review_text'] ?? '')) ?></td>
            <td><span class="badge"><?= (int)$r['total_comments'] ?></span></td>
            <td class="muted"><?= htmlspecialchars((string)$r['created_at']) ?></td>
            <td style="white-space:nowrap;">
              <a class="btn" href="<?= BASE_URL ?>/admin/reviews.php?delete=<?= (int)$r['id'] ?>&csrf=<?= htmlspecialchars($csrf) ?>" onclick="return confirm('Hapus review ini? (comments ikut terhapus karena FK)')">Hapus</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="8" class="muted">Belum ada review.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>

<?php include __DIR__ . '/partials/footer.php'; ?>
