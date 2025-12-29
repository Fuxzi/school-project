<?php
require_once __DIR__ . '/../config/koneksi.php';

$username = 'admin';
$newPassword = 'admin123';

$hash = password_hash($newPassword, PASSWORD_BCRYPT);

$stmt = mysqli_prepare($conn, "UPDATE users SET password_hash=? WHERE username=? LIMIT 1");
mysqli_stmt_bind_param($stmt, "ss", $hash, $username);
mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) <= 0) {
  die("Gagal update / username tidak ketemu.");
}

echo "OK. Password user '$username' sekarang: $newPassword";
