<?php
require_once __DIR__ . '/../../config/env.php';
require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../config/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// wajib login
require_login();

error_reporting(E_ALL);
ini_set('display_errors', 1);

$product_id = (int)($_POST['product_id'] ?? 0);
$rating     = (int)($_POST['rating'] ?? 0);
$comment    = trim($_POST['comment'] ?? '');

// ambil user login dari session
$user_id = (int)($_SESSION['user']['id'] ?? 0);
if ($user_id <= 0) {
  header("Location: " . BASE_URL . "/auth/login.php");
  exit;
}

if ($product_id <= 0) die("Produk tidak valid.");
if ($rating < 1 || $rating > 5) die("Rating harus 1-5.");
if ($comment === '') die("Komentar tidak boleh kosong.");

// amanin comment
$commentEsc = mysqli_real_escape_string($conn, $comment);

// 1) insert review
$sql1 = "INSERT INTO reviews (product_id, user_id, rating, created_at)
         VALUES ($product_id, $user_id, $rating, NOW())";
if (!mysqli_query($conn, $sql1)) {
  die("Gagal insert reviews: " . mysqli_error($conn));
}
$review_id = (int)mysqli_insert_id($conn);

// 2) insert comment
$sql2 = "INSERT INTO comments (review_id, user_id, comment_text, created_at)
         VALUES ($review_id, $user_id, '$commentEsc', NOW())";
if (!mysqli_query($conn, $sql2)) {
  die("Gagal insert comments: " . mysqli_error($conn));
}

// balik ke detail produk
header("Location: " . BASE_URL . "/site/detail.php?id=" . $product_id);
exit;
