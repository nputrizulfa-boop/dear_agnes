<?php
include "auth.php";

$pesan = "";
$sukses = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $lama   = $_POST['lama'] ?? '';
    $baru   = $_POST['baru'] ?? '';
    $ulang  = $_POST['ulang'] ?? '';

    $stmt = $conn->prepare("SELECT password_hash FROM admin WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['admin_id']);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if (!$row || !password_verify($lama, $row['password_hash'])) {
        $pesan = "Password lama salah.";
    } elseif (strlen($baru) < 8) {
        $pesan = "Password baru minimal 8 karakter.";
    } elseif ($baru !== $ulang) {
        $pesan = "Konfirmasi password tidak sama.";
    } else {
        $hash = password_hash($baru, PASSWORD_DEFAULT);
        $up = $conn->prepare("UPDATE admin SET password_hash = ? WHERE id = ?");
        $up->bind_param("si", $hash, $_SESSION['admin_id']);
        $up->execute();
        $pesan = "Password berhasil diubah.";
        $sukses = true;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ubah Password</title>
  <?php include "../tema.php"; ?>
</head>
<body class="bg-slate-50 min-h-screen flex items-start justify-center p-4">
  <div class="w-full max-w-sm bg-white rounded-3xl shadow-lg p-8 mt-10">
    <h1 class="text-xl font-bold text-center text-gray-800">Ubah Password</h1>

    <?php if ($pesan): ?>
      <p class="mt-4 text-sm rounded-xl p-3 text-center <?= $sukses ? 'text-green-700 bg-green-50' : 'text-red-600 bg-red-50' ?>">
        <?= htmlspecialchars($pesan) ?>
      </p>
    <?php endif; ?>

    <form method="post" class="mt-5 space-y-4">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <input type="password" name="lama" placeholder="Password lama" required
        class="w-full px-4 py-3 rounded-xl bg-slate-100 text-sm outline-none focus:ring-2 focus:ring-sage-400">
      <input type="password" name="baru" placeholder="Password baru (min. 8 karakter)" required
        class="w-full px-4 py-3 rounded-xl bg-slate-100 text-sm outline-none focus:ring-2 focus:ring-sage-400">
      <input type="password" name="ulang" placeholder="Ulangi password baru" required
        class="w-full px-4 py-3 rounded-xl bg-slate-100 text-sm outline-none focus:ring-2 focus:ring-sage-400">
      <button type="submit" class="w-full bg-sage-600 hover:bg-sage-700 text-white font-semibold py-3 rounded-xl">Simpan Password</button>
    </form>
    <a href="dashboard.php" class="block mt-5 text-center text-sm text-gray-500 hover:underline">Kembali ke dashboard</a>
  </div>
</body>
</html>