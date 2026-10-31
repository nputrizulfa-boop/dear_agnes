<?php
// Ganti dengan teks acak panjang buatanmu sendiri (rahasia, jangan dibagikan)
define('GARAM', 'fadiaf123fadiaf123fadiaf123fadiaf123fadiaf123');

function sidik() {
    return hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . GARAM);
}

function hitungPercobaan($conn, $jenis, $menit) {
    $s = sidik();
    $stmt = $conn->prepare("SELECT COUNT(*) FROM percobaan WHERE sidik = ? AND jenis = ? AND waktu > (NOW() - INTERVAL ? MINUTE)");
    $stmt->bind_param("ssi", $s, $jenis, $menit);
    $stmt->execute();
    $stmt->bind_result($n);
    $stmt->fetch();
    $stmt->close();
    return (int)$n;
}

function catatPercobaan($conn, $jenis) {
    $s = sidik();
    $stmt = $conn->prepare("INSERT INTO percobaan (sidik, jenis) VALUES (?, ?)");
    $stmt->bind_param("ss", $s, $jenis);
    $stmt->execute();
    $stmt->close();
    // Bersihkan data lama
    $conn->query("DELETE FROM percobaan WHERE waktu < (NOW() - INTERVAL 1 DAY)");
}

function halamanBlokir($judul, $isi) {
    http_response_code(429);
    echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php include "tema.php"; ?></head>
    <body class="bg-slate-50 min-h-screen flex items-start justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-lg p-8 text-center mt-8">
    <div class="text-5xl">⏳</div>
    <h1 class="text-xl font-bold text-gray-800 mt-3">' . htmlspecialchars($judul) . '</h1>
    <p class="text-sm text-gray-500 mt-2">' . htmlspecialchars($isi) . '</p>
    <a href="index.php" class="block mt-6 text-sm text-gray-500 hover:underline">Kembali ke beranda</a>
    </div></body></html>';
    exit;
}
?>