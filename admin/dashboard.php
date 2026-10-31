<?php
include "auth.php";

$allowed = ['diterima', 'diproses', 'dibalas'];
$f = $_GET['status'] ?? '';
if (!in_array($f, $allowed, true)) $f = '';

// Hitung jumlah per status
$hitung = ['diterima' => 0, 'diproses' => 0, 'dibalas' => 0];
$r = $conn->query("SELECT status, COUNT(*) AS n FROM pengaduan GROUP BY status");
while ($x = $r->fetch_assoc()) $hitung[$x['status']] = (int)$x['n'];
$total = array_sum($hitung);

// Ambil daftar pesan (dengan atau tanpa filter)
if ($f !== '') {
    $stmt = $conn->prepare("SELECT id, kode, nama, kelas, isi, status, created_at FROM pengaduan WHERE status = ? ORDER BY created_at DESC");
    $stmt->bind_param("s", $f);
    $stmt->execute();
    $daftar = $stmt->get_result();
} else {
    $daftar = $conn->query("SELECT id, kode, nama, kelas, isi, status, created_at FROM pengaduan ORDER BY created_at DESC");
}

function badge($s) {
    if ($s === 'dibalas')  return '<span class="text-xs font-semibold bg-green-100 text-green-700 px-3 py-1 rounded-full">✅ Dibalas</span>';
    if ($s === 'diproses') return '<span class="text-xs font-semibold bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full">⏳ Diproses</span>';
    return '<span class="text-xs font-semibold bg-sage-100 text-sage-700 px-3 py-1 rounded-full">📩 Baru</span>';
}
function tab($label, $val, $f, $n) {
    $aktif = ($f === $val);
    $cls = $aktif ? 'bg-sage-600 text-white' : 'bg-white text-gray-600 hover:bg-slate-100';
    $href = $val === '' ? 'dashboard.php' : 'dashboard.php?status=' . $val;
    return "<a href='$href' class='text-sm font-semibold px-4 py-2 rounded-xl shadow-sm $cls'>$label ($n)</a>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard</title>
  <?php include "../tema.php"; ?>
</head>
<body class="bg-slate-50 p-4">
  <div class="max-w-3xl mx-auto">

    <div class="flex flex-wrap items-center justify-between gap-3">
      <h1 class="text-xl font-bold">Halo, Mrs.<?= htmlspecialchars($_SESSION['admin_user']) ?> 👋</h1>
      <div class="flex flex-wrap gap-2">
        <a href="ubah_password.php" class="bg-sage-600 hover:bg-sage-700 text-white text-sm font-semibold px-4 py-2 rounded-xl">🔑 Ubah Password</a>
        <a href="pengguna.php" class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 text-sm font-semibold px-4 py-2 rounded-xl">👤 Pengguna</a>
        <a href="logout.php" class="bg-red-50 hover:bg-red-100 text-red-600 text-sm font-semibold px-4 py-2 rounded-xl">Keluar</a>
      </div>
    </div>

    <!-- Ringkasan -->
    <div class="grid grid-cols-3 gap-3 mt-5">
      <div class="bg-sage-50 rounded-2xl p-4 text-center">
        <p class="text-2xl font-bold text-sage-700"><?= $hitung['diterima'] ?></p>
        <p class="text-xs text-sage-600">Pesan baru</p>
      </div>
      <div class="bg-yellow-50 rounded-2xl p-4 text-center">
        <p class="text-2xl font-bold text-yellow-700"><?= $hitung['diproses'] ?></p>
        <p class="text-xs text-yellow-600">Diproses</p>
      </div>
      <div class="bg-green-50 rounded-2xl p-4 text-center">
        <p class="text-2xl font-bold text-green-700"><?= $hitung['dibalas'] ?></p>
        <p class="text-xs text-green-600">Dibalas</p>
      </div>
    </div>

    <!-- Filter -->
    <div class="flex flex-wrap gap-2 mt-5">
      <?= tab('Semua', '', $f, $total) ?>
      <?= tab('Baru', 'diterima', $f, $hitung['diterima']) ?>
      <?= tab('Diproses', 'diproses', $f, $hitung['diproses']) ?>
      <?= tab('Dibalas', 'dibalas', $f, $hitung['dibalas']) ?>
    </div>

    <!-- Daftar pesan -->
    <div class="mt-5 space-y-3">
      <?php if ($daftar->num_rows === 0): ?>
        <div class="bg-white rounded-2xl p-8 text-center text-gray-400 text-sm">Belum ada pesan.</div>
      <?php endif; ?>

      <?php while ($p = $daftar->fetch_assoc()): ?>
        <a href="balas.php?id=<?= (int)$p['id'] ?>" class="block bg-white rounded-2xl shadow-sm hover:shadow-md p-4">
          <div class="flex items-center justify-between gap-2">
            <span class="font-mono text-sm font-bold text-sage-600"><?= htmlspecialchars($p['kode']) ?></span>
            <?= badge($p['status']) ?>
          </div>
          <p class="text-xs text-gray-400 mt-1">
            <?= $p['nama'] ? htmlspecialchars($p['nama']) : 'Anonim' ?>
            <?= $p['kelas'] ? ' • ' . htmlspecialchars($p['kelas']) : '' ?>
            • <?= date('d M Y, H:i', strtotime($p['created_at'])) ?>
          </p>
          <p class="text-sm text-gray-700 mt-2"><?= htmlspecialchars(mb_strimwidth($p['isi'], 0, 120, '...')) ?></p>
        </a>
      <?php endwhile; ?>
    </div>
  </div>
</body>
</html>