<?php
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/koneksi.php';

include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/navbar.php';

if (session_status() === PHP_SESSION_NONE) session_start();
$user = $_SESSION['user'] ?? null;

if (!function_exists('resolveImageUrl')) {
  function resolveImageUrl(string $imgPath): string {
    $imgPath = trim($imgPath);
    if ($imgPath === '') return BASE_URL . '/assets/img/placeholder.png';

    // BASE_PATH kadang belum ada, fallback ke parent folder
    $basePath = defined('BASE_PATH') ? BASE_PATH : realpath(__DIR__ . '/..');

    $full = $basePath . '/' . ltrim($imgPath, '/');
    if (file_exists($full)) return BASE_URL . '/' . ltrim($imgPath, '/');

    $info = pathinfo($imgPath);
    $dir  = $info['dirname'] ?? '';
    $name = $info['filename'] ?? '';
    foreach (['jpg','jpeg','png','webp'] as $ext) {
      $try = ($dir !== '.' ? $dir . '/' : '') . $name . '.' . $ext;
      if (file_exists($basePath . '/' . ltrim($try, '/'))) {
        return BASE_URL . '/' . ltrim($try, '/');
      }
    }
    return BASE_URL . '/assets/img/placeholder.png';
  }
}

function stars($rating): string {
  $r = (float)$rating;
  $full = (int)floor($r);
  return str_repeat("★", $full) . str_repeat("☆", 5 - $full);
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) die("Produk tidak ditemukan.");

/* Produk + kategori + ringkasan rating */
$sqlProduct = "
  SELECT
    p.*,
    c.name AS category_name,
    COUNT(r.id) AS total_reviews,
    COALESCE(AVG(r.rating), 0) AS avg_rating
  FROM products p
  LEFT JOIN categories c ON c.id = p.category_id
  LEFT JOIN reviews r ON r.product_id = p.id
  WHERE p.id = $id
  GROUP BY p.id
  LIMIT 1
";
$resP = mysqli_query($conn, $sqlProduct);
if (!$resP) die("Query error: " . mysqli_error($conn));
$product = mysqli_fetch_assoc($resP);
if (!$product) die("Produk tidak ditemukan.");

$imgUrl = resolveImageUrl((string)($product['image_path'] ?? ''));

/* List review + comment + user */
$sqlReviews = "
  SELECT
    r.id AS review_id,
    r.rating,
    r.created_at AS review_created_at,

    c.comment_text,
    c.created_at AS comment_created_at,

    u.username,
    u.full_name
  FROM reviews r
  LEFT JOIN comments c ON c.review_id = r.id
  JOIN users u ON u.id = r.user_id
  WHERE r.product_id = $id
  ORDER BY r.created_at DESC
";
$resR = mysqli_query($conn, $sqlReviews);
if (!$resR) die("Query error: " . mysqli_error($conn));
?>

<!-- Breadcrumb -->
<div class="mb-6">
  <a href="<?= BASE_URL ?>/site/" class="text-white/70 hover:text-white transition text-sm">← Kembali</a>
</div>

<section class="grid grid-cols-1 lg:grid-cols-5 gap-6">

  <!-- LEFT: Image -->
  <div class="lg:col-span-3 glass-card rounded-2xl overflow-hidden">
    <div class="aspect-[16/10] bg-black/20">
      <img
        src="<?= htmlspecialchars($imgUrl) ?>"
        alt="<?= htmlspecialchars((string)$product['title']) ?>"
        class="w-full h-full object-cover"
        loading="lazy"
        onerror="this.onerror=null; this.src='<?= BASE_URL ?>/assets/img/placeholder.png';"
      />
    </div>

    <div class="p-5">
      <div class="meta text-xs mb-2">
        <?= htmlspecialchars((string)($product['category_name'] ?? 'Uncategorized')) ?>
        <?= !empty($product['type']) ? " • " . htmlspecialchars((string)$product['type']) : "" ?>
      </div>

      <h1 class="title text-2xl font-extrabold">
        <?= htmlspecialchars((string)$product['title']) ?>
      </h1>

      <p class="desc mt-3 leading-relaxed">
        <?= nl2br(htmlspecialchars((string)($product['description'] ?? ''))) ?>
      </p>
    </div>
  </div>

  <!-- RIGHT: Summary + Add Review -->
  <div class="lg:col-span-2 space-y-6">

    <!-- Summary -->
    <div class="glass-card rounded-2xl p-5">
      <div class="flex items-center justify-between">
        <div>
          <div class="text-white/80 text-sm">Rating</div>
          <div class="text-3xl font-extrabold text-white/95">
            <?= number_format((float)$product['avg_rating'], 1) ?>
          </div>
        </div>

        <div class="text-right">
          <div class="text-white/80 text-sm">Total Review</div>
          <div class="text-xl font-bold text-white/90">
            <?= (int)$product['total_reviews'] ?>
          </div>
        </div>
      </div>

      <div class="mt-3 text-white/80">
        <span class="text-yellow-300"><?= stars($product['avg_rating']) ?></span>
      </div>

      <div class="mt-4 text-white/60 text-xs break-all">
        Image: <?= htmlspecialchars((string)($product['image_path'] ?? '')) ?>
      </div>
    </div>

    <!-- Add Review -->
    <div class="glass-card rounded-2xl p-5">
      <h2 class="text-lg font-bold text-white/90">Tambah Review</h2>
      <p class="text-white/60 text-sm mt-1">Isi rating dan komentar kamu.</p>

      <?php if (!$user): ?>
        <div class="mt-4 p-3 rounded-xl bg-white/5 ring-1 ring-white/10 text-white/70 text-sm">
          Kamu harus <a class="text-white underline" href="<?= BASE_URL ?>/auth/login.php">login</a> untuk mengirim review.
        </div>
      <?php endif; ?>

      <form action="<?= BASE_URL ?>/site/actions/add_review.php" method="POST" class="mt-4 space-y-3">
        <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">

        <div>
          <label class="block text-sm text-white/80 mb-1">Rating (1-5)</label>
          <select name="rating" class="input-glass rounded-lg px-3 py-2 w-full" required <?= !$user ? 'disabled' : '' ?>>
            <option value="" disabled selected>Pilih rating</option>
            <option value="5">5 - Mantap</option>
            <option value="4">4 - Bagus</option>
            <option value="3">3 - Oke</option>
            <option value="2">2 - Kurang</option>
            <option value="1">1 - Jelek</option>
          </select>
        </div>

        <div>
          <label class="block text-sm text-white/80 mb-1">Komentar</label>
          <textarea name="comment" rows="4" class="input-glass rounded-lg px-3 py-2 w-full"
                    placeholder="Tulis komentar..." required <?= !$user ? 'disabled' : '' ?>></textarea>
        </div>

        <?php if ($user): ?>
          <button class="btn-primary w-full rounded-lg px-4 py-2">Kirim Review</button>
        <?php else: ?>
          <a class="btn-primary block text-center w-full rounded-lg px-4 py-2"
             href="<?= BASE_URL ?>/auth/login.php">Login untuk Review</a>
        <?php endif; ?>
      </form>
    </div>

  </div>
</section>

<!-- Reviews list -->
<section class="mt-8">
  <div class="flex items-end justify-between mb-3">
    <h2 class="text-xl font-bold text-white/90">Review Terbaru</h2>
    <div class="text-white/60 text-sm">
      <?= (int)$product['total_reviews'] ?> review
    </div>
  </div>

  <?php if ($resR && mysqli_num_rows($resR) > 0): ?>
    <?php while ($r = mysqli_fetch_assoc($resR)): ?>
      <?php
        $displayName = trim((string)($r['full_name'] ?? ''));
        if ($displayName === '') $displayName = (string)($r['username'] ?? 'user');

        $rawDate = $r['comment_created_at'] ?? $r['review_created_at'] ?? '';
        $niceDate = $rawDate ? date('d M Y H:i', strtotime($rawDate)) : '';
      ?>
      <div class="glass-card rounded-2xl p-5 mb-4">
        <div class="flex items-center justify-between">
          <div class="text-yellow-300 text-sm">
            <?= stars((int)($r['rating'] ?? 0)) ?>
          </div>
          <div class="text-white/50 text-xs">
            <?= htmlspecialchars($niceDate) ?>
          </div>
        </div>

        <div class="mt-2 text-white/70 text-xs">
          oleh <span class="text-white/85 font-semibold"><?= htmlspecialchars($displayName) ?></span>
        </div>

        <div class="mt-3 text-white/85 leading-relaxed">
          <?php if (!empty($r['comment_text'])): ?>
            <?= nl2br(htmlspecialchars((string)$r['comment_text'])) ?>
          <?php else: ?>
            <span class="text-white/50">Belum ada komentar.</span>
          <?php endif; ?>
        </div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <div class="glass-card rounded-2xl p-5 text-white/70">
      Belum ada review untuk produk ini.
    </div>
  <?php endif; ?>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
