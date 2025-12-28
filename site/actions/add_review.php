<?php
require_once __DIR__ . '/../../config/env.php';
require_once __DIR__ . '/../../config/koneksi.php';
session_start();

$product_id = (int)($_POST['product_id'] ?? 0);
$rating     = (int)($_POST['rating'] ?? 0);
$comment    = trim($_POST['comment'] ?? '');

// ✅ kalau belum login, pakai Guest user id 1
$user_id = (int)($_SESSION['user_id'] ?? 1);

if ($product_id <= 0) {
  die("Produk tidak valid.");
}
if ($rating < 1 || $rating > 5) {
  die("Rating harus 1 sampai 5.");
}
if ($comment === '') {
  die("Komentar tidak boleh kosong.");
}

/* 1) Insert review */
$sql1 = "INSERT INTO reviews (product_id, rating, created_at)
         VALUES ($product_id, $rating, NOW())";
if (!mysqli_query($conn, $sql1)) {
  die("Insert review gagal: " . mysqli_error($conn));
}
$review_id = (int)mysqli_insert_id($conn);

/* 2) Insert comment */
$commentEsc = mysqli_real_escape_string($conn, $comment);
$sql2 = "INSERT INTO comments (review_id, user_id, comment_text, created_at)
         VALUES ($review_id, $user_id, '$commentEsc', NOW())";
if (!mysqli_query($conn, $sql2)) {
  die("Insert comment gagal: " . mysqli_error($conn));
}

/* Redirect balik ke detail */
header("Location: " . BASE_URL . "/site/detail.php?id=" . $product_id);
exit;
