<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/partials/head.php';
require_once __DIR__ . '/partials/navbar.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { die("Produk tidak valid"); }

$q = mysqli_query($conn, "
  SELECT p.*, c.name AS category_name
  FROM products p
  JOIN categories c ON c.id = p.category_id
  WHERE p.id = $id
  LIMIT 1
");
$product = mysqli_fetch_assoc($q);
if (!$product) { die("Produk tidak ditemukan"); }

$img = $product['image_path']
  ? BASE_URL . '/' . $product['image_path']
  : BASE_URL . '/assets/img/placeholder.png';

$ratingQ = mysqli_query($conn, "
  SELECT COUNT(*) AS total_reviews, COALESCE(AVG(rating),0) AS avg_rating
  FROM reviews
  WHERE product_id = $id
");
$rt = mysqli_fetch_assoc($ratingQ);

$reviews = mysqli_query($conn, "
  SELECT r.*, u.username
  FROM reviews r
  JOIN users u ON u.id = r.user_id
  WHERE r.product_id = $id
  ORDER BY r.created_at DESC
");
?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
  <div class="bg-white rounded-2xl border overflow-hidden">
    <img src="<?= htmlspecialchars($img) ?>" class="w-full h-auto" alt="">
  </div>

  <div>
    <div class="text-sm text-slate-500 mb-2">
      <?= htmlspecialchars($product['category_name']) ?>
      <?= $product['type'] ? " • " . htmlspecialchars($product['type']) : "" ?>
    </div>

    <h1 class="text-3xl font-bold"><?= htmlspecialchars($product['title']) ?></h1>
    <div class="mt-2 text-sm">
      ⭐ <?= number_format((float)$rt['avg_rating'], 1) ?>
      <span class="text-slate-500">(<?= (int)$rt['total_reviews'] ?> review)</span>
    </div>

    <p class="mt-4 text-slate-700 leading-relaxed">
      <?= nl2br(htmlspecialchars($product['description'] ?? '')) ?>
    </p>

    <div class="mt-6 bg-white border rounded-2xl p-4">
      <h2 class="font-semibold">Tulis Review</h2>
      <p class="text-sm text-slate-500 mb-3">
        (Nanti kita sambungkan ke login session kamu)
      </p>

      <form action="<?= BASE_URL ?>/site/actions/add_review.php" method="POST" class="grid gap-3">
        <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
        <label class="text-sm">Rating (1-5)</label>
        <input type="number" min="1" max="5" name="rating" class="border rounded-lg px-3 py-2" required>

        <label class="text-sm">Komentar</label>
        <textarea name="review_text" rows="3" class="border rounded-lg px-3 py-2"></textarea>

        <button class="bg-slate-900 text-white rounded-lg px-4 py-2">
          Kirim Review
        </button>
      </form>
    </div>
  </div>
</div>

<section class="mt-10">
  <h2 class="text-xl font-bold mb-4">Review Terbaru</h2>

  <div class="grid gap-4">
    <?php while($r = mysqli_fetch_assoc($reviews)): ?>
      <div class="bg-white border rounded-2xl p-4">
        <div class="flex items-center justify-between">
          <div class="font-semibold"><?= htmlspecialchars($r['username']) ?></div>
          <div class="text-sm">⭐ <?= (int)$r['rating'] ?>/5</div>
        </div>
        <div class="text-sm text-slate-500 mt-1">
          <?= htmlspecialchars($r['created_at']) ?>
        </div>
        <p class="mt-3 text-slate-700">
          <?= nl2br(htmlspecialchars($r['review_text'] ?? '')) ?>
        </p>
      </div>
    <?php endwhile; ?>
  </div>
</section>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
