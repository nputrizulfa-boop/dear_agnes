<?php
include "auth.php";

$pesan = "";
$sukses = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $aksi = $_POST['aksi'] ?? '';

    if ($aksi === 'tambah') {
        $username = trim($_POST['username'] ?? '');
        $nama     = trim($_POST['nama'] ?? '');
        $pass     = $_POST['password'] ?? '';

        if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
            $pesan = "Username 3-30 karakter, hanya huruf, angka, dan garis bawah.";
        } elseif ($nama === '') {
            $pesan = "Nama tidak boleh kosong.";
        } elseif (strlen($pass) < 8) {
            $pesan = "Password minimal 8 karakter.";
        } else {
            $cek = $conn->prepare("SELECT id FROM admin WHERE username = ?");
            $cek->bind_param("s", $username);
            $cek->execute();
            $cek->store_result();
            if ($cek->num_rows > 0) {
                $pesan = "Username sudah dipakai.";
            } else {
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                $ins = $conn->prepare("INSERT INTO admin (username, nama, password_hash) VALUES (?, ?, ?)");
                $ins->bind_param("sss", $username, $nama, $hash);
                $ins->execute();
                $pesan = "Pengguna berhasil ditambahkan.";
                $sukses = true;
            }
        }
    }

    if ($aksi === 'hapus') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id === (int)$_SESSION['admin_id']) {
            $pesan = "Kamu tidak bisa menghapus akunmu sendiri.";
        } else {
            $del = $conn->prepare("DELETE FROM admin WHERE id = ?");
            $del->bind_param("i", $id);
            $del->execute();
            $pesan = "Pengguna dihapus.";
            $sukses = true;
        }
    }
}

$daftar = $conn->query("SELECT id, username, nama, created_at FROM admin ORDER BY id");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kelola Pengguna</title>
  <?php include "../tema.php"; ?>
</head>
<body class="bg-slate-50 min-h-screen flex items-start justify-center p-4">
  <div class="w-full max-w-md space-y-5 mt-6">

    <?php if ($pesan): ?>
      <p class="text-sm rounded-xl p-3 text-center <?= $sukses ? 'text-green-700 bg-green-50' : 'text-red-600 bg-red-50' ?>">
        <?= htmlspecialchars($pesan) ?>
      </p>
    <?php endif; ?>

    <div class="bg-white rounded-3xl shadow-lg p-6">
      <h1 class="text-lg font-bold text-gray-800">Tambah Pengguna</h1>
      <form method="post" class="mt-4 space-y-3">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <input type="hidden" name="aksi" value="tambah">
        <input type="text" name="nama" placeholder="Nama lengkap" required
          class="w-full px-4 py-3 rounded-xl bg-slate-100 text-sm outline-none focus:ring-2 focus:ring-sage-400">
        <input type="text" name="username" placeholder="Username" required
          class="w-full px-4 py-3 rounded-xl bg-slate-100 text-sm outline-none focus:ring-2 focus:ring-sage-400">
        <input type="password" name="password" placeholder="Password (min. 8 karakter)" required
          class="w-full px-4 py-3 rounded-xl bg-slate-100 text-sm outline-none focus:ring-2 focus:ring-sage-400">
        <button type="submit" class="w-full bg-sage-600 hover:bg-sage-700 text-white font-semibold py-3 rounded-xl">Tambah</button>
      </form>
    </div>

    <div class="bg-white rounded-3xl shadow-lg p-6">
      <h2 class="text-lg font-bold text-gray-800">Daftar Pengguna</h2>
      <div class="mt-3 divide-y">
        <?php while ($r = $daftar->fetch_assoc()): ?>
          <div class="flex items-center justify-between py-3">
            <div>
              <p class="text-sm font-semibold text-gray-800"><?= htmlspecialchars($r['nama']) ?></p>
              <p class="text-xs text-gray-400">@<?= htmlspecialchars($r['username']) ?></p>
            </div>
            <?php if ((int)$r['id'] !== (int)$_SESSION['admin_id']): ?>
              <form method="post" onsubmit="return confirm('Hapus pengguna ini?')">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                <input type="hidden" name="aksi" value="hapus">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button class="text-xs text-red-600 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-lg">Hapus</button>
              </form>
            <?php else: ?>
              <span class="text-xs text-gray-400">(kamu)</span>
            <?php endif; ?>
          </div>
        <?php endwhile; ?>
      </div>
    </div>

    <a href="dashboard.php" class="block text-center text-sm text-gray-500 hover:underline">Kembali ke dashboard</a>
  </div>
</body>
</html>