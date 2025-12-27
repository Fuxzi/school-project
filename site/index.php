<?php
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/koneksi.php';

include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/navbar.php';

// Ambil produk + kategori + ringkasan rating
$sql = "
  SELECT 
    p.id,
    p.title,
    p.type,
    p.description,
    p.image_path,
    p.created_at,
    c.name AS category_name,
    COUNT(r.id) AS total_reviews,
    COALESCE(AVG(r.rating), 0) AS avg_rating
  FROM products p
  LEFT JOIN categories c ON c.id = p.category_id
  LEFT JOIN reviews r ON r.product_id = p.id
  GROUP BY p.id
  ORDER BY p.created_at DESC
";
$res = mysqli_query($conn, $sql);

if (!$res) {
  die("Query error: " . mysqli_error($conn));
}
?>

<section class="mb-6">
  <h1 class="text-2xl font-bold">Produk Terbaru</h1>
  <p class="text-slate-600">Cari dan lihat review produk.</p>
</section>

<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
  <?php while($row = mysqli_fetch_assoc($res)): ?>
    <?php
      // Kalau image_path kosong / null -> pakai placeholder
      $imgPath = trim((string)($row['image_path'] ?? ''));
      $imgUrl = $imgPath !== ''
        ? BASE_URL . '/' . $imgPath
        : BASE_URL . '/assets/img/placeholder.png';
    ?>
    <a href="<?= BASE_URL ?>/site/detail.php?id=<?= (int)$row['id'] ?>"
       class="bg-white rounded-2xl shadow-sm border overflow-hidden hover:shadow-md transition">

      <div class="aspect-[16/10] bg-slate-100">
                <img
            src="<?= htmlspecialchars($imgUrl) ?>"
            alt="<?= htmlspecialchars($row['title']) ?>"
            class="w-full h-full object-cover"
            loading="lazy"
            referrerpolicy="no-referrer"
            onerror="this.onerror=null; this.src='<?= BASE_URL ?>/assets/img/placeholder.png';"
            />

      </div>

      <div class="p-4">
        <div class="text-xs text-slate-500 mb-1">
          <?= htmlspecialchars($row['category_name'] ?? 'Uncategorized') ?>
          <?= !empty($row['type']) ? " • " . htmlspecialchars($row['type']) : "" ?>
        </div>

        <div class="font-semibold text-lg leading-snug">
          <?= htmlspecialchars($row['title']) ?>
        </div>

        <div class="text-sm text-slate-600 mt-2 line-clamp-2">
          <?= htmlspecialchars($row['description'] ?? '') ?>
        </div>

        <div class="mt-3 text-sm flex items-center gap-2">
          <span>⭐ <?= number_format((float)$row['avg_rating'], 1) ?></span>
          <span class="text-slate-500">(<?= (int)$row['total_reviews'] ?> review)</span>
        </div>
      </div>
    </a>
  <?php endwhile; ?>
</section>

<?php
include __DIR__ . '/partials/footer.php';
