<?php
session_start();
if (empty($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include __DIR__ . "/../koneksi.php";

function csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}
function csrf_check() {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        die("Permintaan tidak valid.");
    }
}
?>