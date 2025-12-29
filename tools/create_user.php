<?php
require_once __DIR__ . '/../config/koneksi.php';

$username = 'user1';
$full_name = 'Test User';
$email = null;           // boleh null
$role = 'user';          // user biasa
$password_plain = 'user123';

$hash = password_hash($password_plain, PASSWORD_BCRYPT);

$stmt = mysqli_prepare($conn, "INSERT INTO users (username, password_hash, full_name, email, role) VALUES (?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "sssss", $username, $hash, $full_name, $email, $role);
$ok = mysqli_stmt_execute($stmt);

if (!$ok) {
  die("Gagal insert: " . mysqli_error($conn));
}

echo "OK dibuat!\n";
echo "username: $username\n";
echo "password: $password_plain\n";
echo "role: $role\n";
