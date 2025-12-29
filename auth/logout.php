<?php
require_once __DIR__ . '/../config/env.php';
session_start();
session_destroy();
header("Location: " . BASE_URL . "/site/");
exit;
