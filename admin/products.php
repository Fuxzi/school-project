<?php
require_once __DIR__ . '/../config/koneksi.php';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';

/* ======================
   CSRF
====================== */
if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf'];

function post($k, $d = '') {
  return isset($_POST[$k]) ? trim((string)$_POST[$k]) : $d;
}

/* ======================
   Scan gambar assets/img
====================== */
$imgDir = BASE_PATH . '/assets/img';
$images = [];

if (is_dir($imgDir)) {
  foreach (scandir($imgDir) as $f) {
    if (preg_match('/\.(jpg|jpeg|png|webp)$/i', $f)) {
      $images[] = $f;
    }
  }
}

/* ======================
   Ambil kategori
====================== */
$categories = [];
$q = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name ASC");
while ($q && ($r = mysqli_fetch_assoc($q))) {
  $categories[] = $r;
}

/* ======================
   Mode edit
====================== */
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$edit = null;

if ($editId > 0) {
  $st = mysqli_prepare($conn, "SELECT * FROM products WHERE id=?");
  mysqli_stmt_bind_param($st, "i", $editId);
  mysqli_stmt_execute($st);
  $res = mysqli_stmt_get_result($st);
  $edit = $res ? mysqli_fetch_assoc($res) : null;
  mysqli_stmt_close($st);
}

/* ======================
   Hapus produk
====================== */
if (isset($_GET['delete'])) {
  if (!hash_equals($csrf, (string)($_GET['csrf'] ?? ''))) {
    die("CSRF tidak valid");
  }

  $id = (int)$_GET['delete'];
  $st = mysqli_prepare($conn, "DELETE FROM products WHERE id=?");
  mysqli_stmt_bind_param($st, "i", $id);
  mysqli_stmt_execute($st);
  mysqli_stmt_close($st);

  header("Location: products.php?ok=deleted");
  exit;
}

/* ======================
   Simpan produk
====================== */
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!hash_equals($csrf, (string)($_POST['csrf'] ?? ''))) {
    $error = "CSRF tidak valid";
  } else {
    $id          = (int)post('id');
    $title       = post('title');
    $type        = post('type');
    $category_id = (int)post('category_id');
    $desc        = post('description');
    $image       = post('image_path');

    if ($title === '' || $category_id === 0) {
      $error = "Judul & kategori wajib diisi";
    } elseif ($image && !in_array($image, $images, true)) {
      $error = "File gambar tidak valid";
    } else {
      if ($id > 0) {
        $st = mysqli_prepare($conn,
          "UPDATE products 
           SET category_id=?, title=?, type=?, description=?, image_path=? 
           WHERE id=?"
        );
        mysqli_stmt_bind_param($st, "issssi",
          $category_id, $title, $type, $desc, $image, $id
        );
      } else {
        $st = mysqli_prepare($conn,
          "INSERT INTO products (category_id, title, type, description, image_path)
           VALUES (?,?,?,?,?)"
        );
        mysqli_stmt_bind_param($st, "issss",
          $category_id, $title, $type, $desc, $image
        );
      }

      mysqli_stmt_execute($st);
      mysqli_stmt_close($st);

      header("Location: products.php?ok=saved");
      exit;
    }
  }
}

/* ======================
   List produk
====================== */
$list = mysqli_query($conn,
  "SELECT p.*, c.name AS category_name
   FROM products p
   LEFT JOIN categories c ON c.id = p.category_id
   ORDER BY p.created_at DESC"
);
?>

<main class="glass card">
  <h1 class="h1">Produk</h1>

  <?php if ($error): ?>
    <div class="card glass" style="border-color:#ef4444"><?= $error ?></div>
  <?php endif; ?>

  <?php if (isset($_GET['ok'])): ?>
    <div class="card glass" style="border-color:#22c55e">Data berhasil disimpan</div>
  <?php endif; ?>

  <!-- FORM -->
  <div class="card glass">
    <h3><?= $edit ? 'Edit Produk' : 'Tambah Produk' ?></h3>

    <form method="POST" class="form-admin">
  <input type="hidden" name="csrf" value="<?= $csrf ?>">
  <input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">

  <div>
    <label>Judul Produk</label>
    <input name="title" placeholder="Judul produk"
      value="<?= htmlspecialchars($edit['title'] ?? '') ?>" required>
  </div>

  <div class="form-row-2">
    <div>
      <label>Tipe</label>
      <input name="type" placeholder="Tipe"
        value="<?= htmlspecialchars($edit['type'] ?? '') ?>">
    </div>

    <div>
      <label>Kategori</label>
      <select name="category_id" required>
        <option value="">-- Pilih Kategori --</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= $c['id'] ?>"
            <?= (($edit['category_id'] ?? 0) == $c['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div>
    <label>Deskripsi</label>
    <textarea name="description" rows="3"
      placeholder="Deskripsi"><?= htmlspecialchars($edit['description'] ?? '') ?></textarea>
  </div>

  <div>
    <label>Gambar</label>
    <select name="image_path">
      <option value="">-- Pilih Gambar --</option>
      <?php foreach ($images as $img): ?>
        <option value="<?= $img ?>"
          <?= (($edit['image_path'] ?? '') === $img) ? 'selected' : '' ?>>
          <?= $img ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <button class="btn btn-primary">Simpan</button>
</form>

  </div>

  <!-- TABLE -->
  <div class="card glass">
    <h3>Daftar Produk</h3>

    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Judul</th>
          <th>Kategori</th>
          <th>Gambar</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($p = mysqli_fetch_assoc($list)): ?>
          <tr>
            <td><?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['title']) ?></td>
            <td><span class="badge"><?= htmlspecialchars($p['category_name']) ?></span></td>
            <td>
              <?php if ($p['image_path']): ?>
                <img class="img-thumb"
                src="<?= BASE_URL ?>/assets/img/<?= $p['image_path'] ?>">
              <?php endif; ?>
            </td>
            <td>
              <a class="btn" href="?edit=<?= $p['id'] ?>">Edit</a>
              <a class="btn"
                 href="?delete=<?= $p['id'] ?>&csrf=<?= $csrf ?>"
                 onclick="return confirm('Hapus produk?')">Hapus</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</main>

<?php include __DIR__ . '/partials/footer.php'; ?>
