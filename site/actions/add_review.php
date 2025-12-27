<?php
session_start();
require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../config/env.php';

/**
 * SEMENTARA: user_id dummy biar bisa tes dulu.
 * Nanti kalau login kamu sudah siap, ganti jadi:
 * $user_id = (int)$_SESSION['user_id'];
 */
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 1;

$product_id = (int)($_POST['product_id'] ?? 0);
$rating     = (int)($_POST['rating'] ?? 0);
$text       = trim($_POST['review_text'] ?? '');

if ($product_id <= 0 || $rating < 1 || $rating > 5) {
  die("Data tidak valid.");
}

$stmt = mysqli_prepare($conn, "INSERT INTO reviews (product_id, user_id, rating, review_text) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "iiis", $product_id, $user_id, $rating, $text);
mysqli_stmt_execute($stmt);

header("Location: " . BASE_URL . "/site/detail.php?id=" . $product_id);
exit;
