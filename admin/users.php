<?php
require_once __DIR__ . '/../config/koneksi.php';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';

if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
$csrf = $_SESSION['csrf'];

$err = '';
$ok  = '';

if (isset($_GET['ok'])) {
  $map = ['role'=>'Role diubah.', 'deleted'=>'User dihapus.'];
  $ok = $map[$_GET['ok']] ?? '';
}

if (isset($_GET['setrole'])) {
  if (!hash_equals($csrf, (string)($_GET['csrf'] ?? ''))) $err = "CSRF tidak valid.";
  else {
    $id = (int)($_GET['id'] ?? 0);
    $role = (string)($_GET['role'] ?? '');
    if (!in_array($role, ['admin','user'], true)) $err = "Role tidak valid.";
    else {
      // biar admin yang login ga bisa “bunuh diri” jadi user tanpa sadar
      $me = (int)($_SESSION['user']['id'] ?? 0);
      if ($me === $id && $role !== 'admin') $err = "Kamu tidak bisa menurunkan role akun kamu sendiri.";
      else {
        $st = mysqli_prepare($conn, "UPDATE users SET role=? WHERE id=?");
        mysqli_stmt_bind_param($st, "si", $role, $id);
        mysqli_stmt_execute($st);
        mysqli_stmt_close($st);
        header("Location: " . BASE_URL . "/admin/users.php?ok=role");
        exit;
      }
    }
  }
}

if (isset($_GET['delete'])) {
  if (!hash_equals($csrf, (string)($_GET['csrf'] ?? ''))) $err = "CSRF tidak valid.";
  else {
    $id = (int)$_GET['delete'];
    $me = (int)($_SESSION['user']['id'] ?? 0);
    if ($me === $id) $err = "Kamu tidak bisa menghapus akun kamu sendiri.";
    else {
      $st = mysqli_prepare($conn, "DELETE FROM users WHERE id=?");
      mysqli_stmt_bind_param($st, "i", $id);
      mysqli_stmt_execute($st);
      mysqli_stmt_close($st);
      header("Location: " . BASE_URL . "/admin/users.php?ok=deleted");
      exit;
    }
  }
}

$q = trim((string)($_GET['q'] ?? ''));
$sql = "SELECT id, username, full_name, email, role, created_at FROM users";
if ($q !== '') {
  $qq = '%' . mysqli_real_escape_string($conn, $q) . '%';
  $sql .= " WHERE username LIKE '$qq' OR full_name LIKE '$qq' OR email LIKE '$qq' ";
}
$sql .= " ORDER BY created_at DESC";
$res = mysqli_query($conn, $sql);
?>
<main class="glass card">
  <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:12px;">
    <div>
      <h1 class="h1">Users</h1>
      <div class="muted">Kelola user & role.</div>
    </div>

    <form method="GET" style="display:flex;gap:10px;align-items:center;">
      <input name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Cari user..." style="padding:10px 12px;border-radius:12px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);color:rgba(255,255,255,.9)">
      <button class="btn" type="submit">Cari</button>
      <a class="btn" href="<?= BASE_URL ?>/admin/users.php">Reset</a>
    </form>
  </div>

  <?php if ($err): ?>
    <div class="card glass" style="margin-top:12px;border-color:rgba(239,68,68,.35)"><?= htmlspecialchars($err) ?></div>
  <?php endif; ?>
  <?php if ($ok): ?>
    <div class="card glass" style="margin-top:12px;border-color:rgba(34,197,94,.35)"><?= htmlspecialchars($ok) ?></div>
  <?php endif; ?>

  <div class="card glass" style="margin-top:14px;">
    <div style="font-weight:900;margin-bottom:10px;">Daftar Users</div>

    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Username</th>
          <th>Nama</th>
          <th>Email</th>
          <th>Role</th>
          <th>Dibuat</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($res && mysqli_num_rows($res) > 0): ?>
        <?php while($u = mysqli_fetch_assoc($res)): ?>
          <tr>
            <td><?= (int)$u['id'] ?></td>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td><?= htmlspecialchars((string)($u['full_name'] ?? '')) ?></td>
            <td><?= htmlspecialchars((string)($u['email'] ?? '')) ?></td>
            <td><span class="badge"><?= htmlspecialchars($u['role']) ?></span></td>
            <td class="muted"><?= htmlspecialchars((string)$u['created_at']) ?></td>
            <td style="white-space:nowrap;">
              <?php if ($u['role'] === 'admin'): ?>
                <a class="btn" href="<?= BASE_URL ?>/admin/users.php?setrole=1&id=<?= (int)$u['id'] ?>&role=user&csrf=<?= htmlspecialchars($csrf) ?>" onclick="return confirm('Ubah role jadi user?')">Jadi User</a>
              <?php else: ?>
                <a class="btn" href="<?= BASE_URL ?>/admin/users.php?setrole=1&id=<?= (int)$u['id'] ?>&role=admin&csrf=<?= htmlspecialchars($csrf) ?>" onclick="return confirm('Ubah role jadi admin?')">Jadi Admin</a>
              <?php endif; ?>
              <a class="btn" href="<?= BASE_URL ?>/admin/users.php?delete=<?= (int)$u['id'] ?>&csrf=<?= htmlspecialchars($csrf) ?>" onclick="return confirm('Hapus user ini?')">Hapus</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="7" class="muted">Belum ada user.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>

<?php include __DIR__ . '/partials/footer.php'; ?>
