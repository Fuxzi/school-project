<?php
require_once __DIR__ . '/../config/koneksi.php';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';

// contoh angka (nanti kamu ganti query beneran)
$products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM products"))['c'] ?? 0;
$users    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM users"))['c'] ?? 0;
$reviews  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM reviews"))['c'] ?? 0;

// contoh list review terbaru (kalau tabel ada)
$latest = mysqli_query($conn, "
  SELECT r.id, r.rating, r.created_at, u.username, p.title
  FROM reviews r
  JOIN users u ON u.id = r.user_id
  JOIN products p ON p.id = r.product_id
  ORDER BY r.created_at DESC
  LIMIT 5
");
?>
<main class="glass card">
  <div style="display:flex; align-items:flex-end; justify-content:space-between; gap:12px;">
    <div>
      <h1 class="h1">Dashboard</h1>
      <div class="muted">Ringkasan data & aktivitas terbaru.</div>
    </div>
    <span class="badge">Role: <?= htmlspecialchars($_SESSION['user']['role'] ?? 'admin') ?></span>
  </div>

  <div class="grid" style="margin-top:14px;">
    <div class="stat">
      <div class="k">Total Produk</div>
      <div class="v"><?= (int)$products ?></div>
    </div>
    <div class="stat">
      <div class="k">Total Users</div>
      <div class="v"><?= (int)$users ?></div>
    </div>
    <div class="stat">
      <div class="k">Total Reviews</div>
      <div class="v"><?= (int)$reviews ?></div>
    </div>
  </div>

  <div class="card glass" style="margin-top:14px;">
    <div style="display:flex; align-items:center; justify-content:space-between;">
      <div style="font-weight:900;">Review Terbaru</div>
      <a class="btn" href="<?= BASE_URL ?>/admin/reviews.php">Lihat Semua</a>
    </div>

    <div style="margin-top:10px;">
      <table>
        <thead>
          <tr>
            <th>User</th>
            <th>Produk</th>
            <th>Rating</th>
            <th>Tanggal</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($latest && mysqli_num_rows($latest) > 0): ?>
          <?php while($row = mysqli_fetch_assoc($latest)): ?>
            <tr>
              <td><?= htmlspecialchars($row['username']) ?></td>
              <td><?= htmlspecialchars($row['title']) ?></td>
              <td><span class="badge"><?= (int)$row['rating'] ?>/5</span></td>
              <td class="muted"><?= htmlspecialchars($row['created_at']) ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="4" class="muted">Belum ada review.</td>
          </tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<?php include __DIR__ . '/partials/footer.php'; ?>
