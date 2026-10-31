<?php
include "koneksi.php";
include "batas.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

// Terlalu banyak tebakan salah? Blokir sementara.
if (hitungPercobaan($conn, 'cek_gagal', 15) >= 10) {
    halamanBlokir("Terlalu banyak percobaan", "Silakan coba lagi sekitar 15 menit lagi.");
}

$kode = strtoupper(trim($_POST['kode'] ?? ''));

$stmt = $conn->prepare("SELECT status, balasan, created_at, replied_at FROM pengaduan WHERE kode = ?");
$stmt->bind_param("s", $kode);
$stmt->execute();
$hasil = $stmt->get_result()->fetch_assoc();

// Catat hanya tebakan yang salah
if (!$hasil) {
    catatPercobaan($conn, 'cek_gagal');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cek Balasan - Dear Mrs. Agnes</title>
  <?php include "tema.php"; ?>
</head>
<body class="bg-slate-50 min-h-screen flex items-start justify-center p-4">
  <div class="w-full max-w-md bg-white rounded-3xl shadow-lg p-8 mt-8">

    <?php if (!$hasil): ?>
      <div class="text-center">
        <div class="text-5xl">🔍</div>
        <h1 class="text-xl font-bold text-gray-800 mt-3">Kode tidak ditemukan</h1>
        <p class="text-sm text-gray-500 mt-2">Periksa kembali kode Anda, lalu coba lagi.</p>
      </div>

    <?php else: ?>
      <p class="text-xs text-gray-400 text-center">Kode</p>
      <div class="mt-1 bg-slate-100 rounded-xl py-3 text-center text-xl font-mono font-bold tracking-widest text-sage-600">
        <?= htmlspecialchars($kode) ?>
      </div>

      <div class="mt-5 text-center">
        <?php if ($hasil['status'] === 'dibalas'): ?>
          <span class="inline-block bg-green-100 text-green-700 text-sm font-semibold px-4 py-1 rounded-full">✅ Sudah dibalas</span>
        <?php elseif ($hasil['status'] === 'diproses'): ?>
          <span class="inline-block bg-yellow-100 text-yellow-700 text-sm font-semibold px-4 py-1 rounded-full">⏳ Sedang diproses</span>
        <?php else: ?>
          <span class="inline-block bg-sage-100 text-sage-700 text-sm font-semibold px-4 py-1 rounded-full">📩 Diterima, menunggu balasan</span>
        <?php endif; ?>
      </div>

      <p class="text-xs text-gray-400 text-center mt-3">
        Dikirim: <?= date('d M Y, H:i', strtotime($hasil['created_at'])) ?>
      </p>

      <?php if ($hasil['status'] === 'dibalas' && $hasil['balasan']): ?>
        <div class="mt-5 bg-sage-50 border border-sage-100 rounded-2xl p-4">
          <p class="text-xs font-semibold text-sage-600 mb-2">Balasan dari Mrs. Agnes</p>
          <p class="text-sm text-gray-700 whitespace-pre-line"><?= htmlspecialchars($hasil['balasan']) ?></p>
        </div>
      <?php endif; ?>
    <?php endif; ?>

    <a href="index.php" class="block mt-6 text-center text-sm text-gray-500 hover:underline">Kembali ke beranda</a>
  </div>
</body>
</html> 