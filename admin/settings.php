<?php
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/auth.php';
require_admin();

if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
$csrf = $_SESSION['csrf'];

$err = '';
$ok  = '';

function post($k, $d=''){ return isset($_POST[$k]) ? trim((string)$_POST[$k]) : $d; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!hash_equals($csrf, (string)($_POST['csrf'] ?? ''))) {
    $err = "CSRF tidak valid.";
  } else {
    $app  = post('app_name', APP_NAME);
    $base = post('base_url', BASE_URL);

    if ($app === '' || $base === '') $err = "APP_NAME dan BASE_URL tidak boleh kosong.";
    else {
      $path = __DIR__ . '/../config/env.php';
      $content = "<?php\n// config/env.php\n\n"
        . "define('APP_NAME', " . var_export($app, true) . ");\n"
        . "define('BASE_URL', " . var_export($base, true) . ");\n\n"
        . "define('BASE_PATH', realpath(__DIR__ . '/..'));\n\n"
        . "// DB\n"
        . "define('DB_HOST', " . var_export(DB_HOST, true) . ");\n"
        . "define('DB_USER', " . var_export(DB_USER, true) . ");\n"
        . "define('DB_PASS', " . var_export(DB_PASS, true) . ");\n"
        . "define('DB_NAME', " . var_export(DB_NAME, true) . ");\n";

      if (@file_put_contents($path, $content) === false) {
        $err = "Gagal menyimpan env.php (cek permission).";
      } else {
        header("Location: " . BASE_URL . "/admin/settings.php?ok=1");
        exit;
      }
    }
  }
}

if (isset($_GET['ok'])) $ok = "Settings disimpan. Reload halaman jika perlu.";
?>
<?php include __DIR__ . '/partials/head.php'; ?>
<?php include __DIR__ . '/partials/sidebar.php'; ?>

<main class="glass card">
  <div>
    <h1 class="h1">Settings</h1>
    <div class="muted">Pengaturan dasar aplikasi.</div>
  </div>

  <?php if ($err): ?>
    <div class="card glass" style="margin-top:12px;border-color:rgba(239,68,68,.35)"><?= htmlspecialchars($err) ?></div>
  <?php endif; ?>
  <?php if ($ok): ?>
    <div class="card glass" style="margin-top:12px;border-color:rgba(34,197,94,.35)"><?= htmlspecialchars($ok) ?></div>
  <?php endif; ?>

  <div class="card glass" style="margin-top:14px;">
    <form method="POST" style="display:grid;gap:10px;max-width:600px;">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">

      <label class="muted">APP_NAME</label>
      <input name="app_name" value="<?= htmlspecialchars(APP_NAME) ?>" style="padding:10px 12px;border-radius:12px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);color:rgba(255,255,255,.9)">

      <label class="muted">BASE_URL</label>
      <input name="base_url" value="<?= htmlspecialchars(BASE_URL) ?>" style="padding:10px 12px;border-radius:12px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);color:rgba(255,255,255,.9)">

      <button class="btn" type="submit" style="width:max-content;">Simpan</button>
    </form>
  </div>
</main>

<?php include __DIR__ . '/partials/footer.php'; ?>
