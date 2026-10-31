<?php
include "auth.php";

$id = (int)($_GET['id'] ?? 0);
$pesan = "";
$sukses = false;

// Proses form (balas / ubah status / hapus)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id   = (int)($_POST['id'] ?? 0);
    $aksi = $_POST['aksi'] ?? '';

    if ($aksi === 'hapus') {
        $del = $conn->prepare("DELETE FROM pengaduan WHERE id = ?");
        $del->bind_param("i", $id);
        $del->execute();
        header("Location: dashboard.php");
        exit;
    }

    if ($aksi === 'simpan') {
        $balasan = trim($_POST['balasan'] ?? '');
        $status  = $_POST['status'] ?? 'diproses';

        if (!in_array($status, ['diterima', 'diproses', 'dibalas'], true)) {
            $pesan = "Status tidak valid.";
        } elseif ($status === 'dibalas' && $balasan === '') {
            $pesan = "Tulis balasan dulu sebelum menandai 'Dibalas'.";
        } else {
            $balasanDb = ($balasan === '') ? null : $balasan;
            if ($status === 'dibalas') {
                $up = $conn->prepare("UPDATE pengaduan SET balasan = ?, status = ?, replied_at = NOW() WHERE id = ?");
            } else {
                $up = $conn->prepare("UPDATE pengaduan SET balasan = ?, status = ?, replied_at = NULL WHERE id = ?");
            }
            $up->bind_param("ssi", $balasanDb, $status, $id);
            $up->execute();
            $pesan = "Tersimpan.";
            $sukses = true;
        }
    }
}

// Ambil data pesan
$stmt = $conn->prepare("SELECT * FROM pengaduan WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$p = $stmt->get_result()->fetch_assoc();

if (!$p) {
    die("Pesan tidak ditemukan. <a href='dashboard.php'>Kembali</a>");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Balas Pesan</title>
  <?php include "../tema.php"; ?>
</head>
<body class="bg-slate-50 p-4">
  <div class="max-w-xl mx-auto space-y-4">

    <a href="dashboard.php" class="text-sm text-gray-500 hover:underline">← Kembali ke daftar</a>

    <?php if ($pesan): ?>
      <p class="text-sm rounded-xl p-3 text-center <?= $sukses ? 'text-green-700 bg-green-50' : 'text-red-600 bg-red-50' ?>">
        <?= htmlspecialchars($pesan) ?>
      </p>
    <?php endif; ?>

    <!-- Isi pesan -->
    <div class="bg-white rounded-3xl shadow-lg p-6">
      <div class="flex items-center justify-between">
        <span class="font-mono font-bold text-sage-600"><?= htmlspecialchars($p['kode']) ?></span>
        <span class="text-xs text-gray-400"><?= date('d M Y, H:i', strtotime($p['created_at'])) ?></span>
      </div>
      <p class="text-sm text-gray-500 mt-2">
        <b>Pengirim:</b> <?= $p['nama'] ? htmlspecialchars($p['nama']) : 'Anonim' ?>
        <?= $p['kelas'] ? ' • Kelas ' . htmlspecialchars($p['kelas']) : '' ?>
      </p>
      <div class="mt-4 bg-slate-50 rounded-2xl p-4">
        <p class="text-sm text-gray-800 whitespace-pre-line"><?= htmlspecialchars($p['isi']) ?></p>
      </div>
    </div>

    <!-- Form balasan -->
    <div class="bg-white rounded-3xl shadow-lg p-6">
      <h2 class="text-lg font-bold text-gray-800">Balasan untuk pengirim</h2>
      <form method="post" class="mt-4 space-y-4">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <input type="hidden" name="aksi" value="simpan">
        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">

        <textarea name="balasan" rows="6" placeholder="Tulis balasan dari Mrs. Agnes..."
          class="w-full px-4 py-3 rounded-xl bg-slate-100 text-sm outline-none focus:ring-2 focus:ring-sage-400"><?= htmlspecialchars($p['balasan'] ?? '') ?></textarea>

        <div>
          <label class="text-sm font-semibold text-gray-700">Status</label>
          <select name="status" class="w-full mt-1 px-4 py-3 rounded-xl bg-slate-100 text-sm outline-none">
            <option value="diterima" <?= $p['status'] === 'diterima' ? 'selected' : '' ?>>📩 Diterima (baru)</option>
            <option value="diproses" <?= $p['status'] === 'diproses' ? 'selected' : '' ?>>⏳ Sedang diproses</option>
            <option value="dibalas"  <?= $p['status'] === 'dibalas'  ? 'selected' : '' ?>>✅ Sudah dibalas</option>
          </select>
        </div>

        <button type="submit" class="w-full bg-sage-600 hover:bg-sage-700 text-white font-semibold py-3 rounded-xl">Simpan</button>
      </form>

      <form method="post" onsubmit="return confirm('Hapus pesan ini secara permanen?')" class="mt-3">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <input type="hidden" name="aksi" value="hapus">
        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
        <button class="w-full text-sm text-red-600 bg-red-50 hover:bg-red-100 py-2 rounded-xl">Hapus pesan</button>
      </form>
    </div>
  </div>
</body>
</html>