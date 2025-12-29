<?php
require_once __DIR__ . '/env.php';

if (session_status() === PHP_SESSION_NONE) session_start();

function current_user() {
  return $_SESSION['user'] ?? null;
}

function require_login() {
  if (empty($_SESSION['user'])) {
    header("Location: " . BASE_URL . "/auth/login.php");
    exit;
  }
}

function require_admin() {
  require_login();
  if (($_SESSION['user']['role'] ?? '') !== 'admin') {
    http_response_code(403);
    die("Akses ditolak (admin only).");
  }
}
