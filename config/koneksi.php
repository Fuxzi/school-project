<?php
require_once __DIR__ . '/env.php';

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$conn) {
  die("Koneksi DB gagal: " . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8mb4');
