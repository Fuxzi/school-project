<?php
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/koneksi.php';

include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/navbar.php';

/* ======================
   Input
====================== */
$q = trim((string)($_GET['q'] ?? ''));

/* ======================
   Helper: resolve image url + fallback
====================== */
function resolve_img_url(string $image_path): string {
  $image_path = trim($image_path);
  $image_path = ltrim($image_path, '/');

  // default (kalau file tidak ada / kosong)
  $fallback = BASE_URL . '/assets/img/placeholder.png';

  if ($image_path === '') return $fallback;

  // Jika DB sudah simpan "assets/...."
  if (strpos($image_path, 'assets/') === 0) {
    $fs = rtrim(BASE_PATH, '/\\') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $image_path);
    return is_file($fs) ? (BASE_URL . '/' . $image_path) : $fallback;
  }

  // Kalau DB cuma simpan nama file, anggap ada di assets/img/
  $fs2 = rtrim(BASE_PATH, '/\\') . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $image_path;
  return is_file($fs2) ? (BASE_URL . '/assets/img/' . $image_path) : $fallback;
}

/* ======================
   Search
====================== */
$items = [];
if ($q !== '') {
  $like = '%' . $q . '%';

  $st = mysqli_prepare($conn, "
    SELECT p.id, p.title, p.type, p.description, p.image_path, p.created_at,
           c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    WHERE p.title LIKE ? OR p.type LIKE ? OR p.description LIKE ?
    ORDER BY p.created_at DESC
    LIMIT 50
  ");
  mysqli_stmt_bind_param($st, "sss", $like, $like, $like);
  mysqli_stmt_execute($st);
  $res = mysqli_stmt_get_result($st);

  while ($res && ($row = mysqli_fetch_assoc($res))) $items[] = $row;
  mysqli_stmt_close($st);
}
?>

<div class="max-w-6xl mx-auto px-4 py-6">
  <div class="glass-card rounded-2xl p-5">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3">
      <div>
        <h1 class="text-2xl font-extrabold text-white/90">Pencarian</h1>
        <p class="text-muted text-sm mt-1">
          Kata kunci:
          <span class="text-white/80 font-semibold"><?= htmlspecialchars($q !== '' ? $q : '-') ?></span>
          <?php if ($q !== ''): ?>
            <span class="text-white/40">•</span>
            <span class="text-white/60"><?= count($items) ?> hasil</span>
          <?php endif; ?>
        </p>
      </div>

      <form action="<?= BASE_URL ?>/site/search.php" method="GET" class="flex gap-2 w-full md:w-auto">
        <input
          name="q"
          value="<?= htmlspecialchars($q) ?>"
          placeholder="Cari produk..."
          class="input-glass rounded-lg px-3 py-2 w-full md:w-72 text-white/90"
        >
        <button class="btn-primary rounded-lg px-4 py-2">Search</button>
      </form>
    </div>
  </div>

  <div class="mt-4">
    <?php if ($q === ''): ?>
      <div class="glass-card rounded-2xl p-5">
        <div class="text-white/80 font-bold">Masukkan kata kunci.</div>
        <div class="text-muted text-sm mt-1">Contoh: “mouse”, “keyboard”, “logitech”, “rtx”, dll.</div>
      </div>

    <?php elseif (count($items) === 0): ?>
      <div class="glass-card rounded-2xl p-5">
        <div class="text-white/80 font-bold">Tidak ada hasil.</div>
        <div class="text-muted text-sm mt-1">Coba kata kunci lain atau lebih umum.</div>
      </div>

    <?php else: ?>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <?php foreach ($items as $p): ?>
          <?php
            $imgUrl = resolve_img_url((string)($p['image_path'] ?? ''));
            $title  = (string)($p['title'] ?? '');
            $desc   = (string)($p['description'] ?? '');
            $cat    = (string)($p['category_name'] ?? '');
            $type   = (string)($p['type'] ?? '');
          ?>

          <a href="<?= BASE_URL ?>/site/detail.php?id=<?= (int)$p['id'] ?>"
             class="glass-card rounded-2xl p-4 hover:opacity-[.98] transition">

            <div class="flex gap-3 items-start">
              <div class="shrink-0">
                <img
                  src="<?= htmlspecialchars($imgUrl) ?>"
                  alt=""
                  class="rounded-xl"
                  style="width:72px;height:72px;object-fit:cover;"
                >
              </div>

              <div class="min-w-0">
                <div class="text-white/90 font-extrabold text-lg leading-snug">
                  <?= htmlspecialchars($title) ?>
                </div>

                <div class="text-white/60 text-sm mt-1 flex flex-wrap gap-2 items-center">
                  <?php if ($cat !== ''): ?>
                    <span class="badge"><?= htmlspecialchars($cat) ?></span>
                  <?php endif; ?>
                  <?php if ($type !== ''): ?>
                    <span class="badge"><?= htmlspecialchars($type) ?></span>
                  <?php endif; ?>
                </div>

                <?php if ($desc !== ''): ?>
                  <div class="text-white/70 text-sm mt-2"
                       style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                    <?= htmlspecialchars($desc) ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>

          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
