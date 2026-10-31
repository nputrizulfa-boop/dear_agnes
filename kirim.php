<?php
include "koneksi.php";
include "batas.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

// Jebakan bot: kolom 'website' tersembunyi
if (!empty($_POST['website'])) {
    header("Location: index.php");
    exit;
}

// Terlalu cepat (< 3 detik sejak form dibuka) = kemungkinan bot
$t = (int)($_POST['t'] ?? 0);
if ($t === 0 || (time() - $t) < 3) {
    halamanBlokir("Terlalu cepat", "Silakan isi form dengan tenang, lalu kirim lagi.");
}

// Batas kirim: maksimal 5 pesan per 10 menit
if (hitungPercobaan($conn, 'kirim', 10) >= 5) {
    halamanBlokir("Terlalu banyak pesan", "Silakan coba lagi beberapa menit lagi.");
}

$nama  = trim($_POST['nama'] ?? '');
$kelas = trim($_POST['kelas'] ?? '');
$isi   = trim($_POST['isi'] ?? '');

if ($isi === '') {
    die("Isi curhat/aduan tidak boleh kosong. <a href='index.php'>Kembali</a>");
}
if (mb_strlen($isi) > 3000 || mb_strlen($nama) > 100 || mb_strlen($kelas) > 50) {
    die("Teks terlalu panjang. <a href='index.php'>Kembali</a>");
}

// Kosong = anonim
$nama  = ($nama === '')  ? null : $nama;
$kelas = ($kelas === '') ? null : $kelas;

// Fungsi membuat kode acak, contoh: NPS-7K2M9QX4
function buatKode() {
    $huruf = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789"; // tanpa O, I, 0, 1 agar tidak membingungkan
    $kode = "";
    for ($i = 0; $i < 8; $i++) {
        $kode .= $huruf[random_int(0, strlen($huruf) - 1)];
    }
    return "NPS-" . $kode;
}

// Pastikan kode belum dipakai
do {
    $kode = buatKode();
    $cek = $conn->prepare("SELECT id FROM pengaduan WHERE kode = ?");
    $cek->bind_param("s", $kode);
    $cek->execute();
    $cek->store_result();
} while ($cek->num_rows > 0);

// Simpan ke database
$stmt = $conn->prepare("INSERT INTO pengaduan (kode, nama, kelas, isi) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $kode, $nama, $kelas, $isi);
$stmt->execute();
catatPercobaan($conn, 'kirim');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Terkirim - Dear Mrs. Agnes</title>
  <?php include "tema.php"; ?>
</head>
<body class="bg-slate-50 min-h-screen flex items-start justify-center p-4">
  <div class="w-full max-w-md bg-white rounded-3xl shadow-lg p-8 text-center mt-8">
    <div class="text-5xl">✅</div>
    <h1 class="text-xl font-bold text-gray-800 mt-3">Terkirim ke Mrs. Agnes!</h1>
    <p class="text-sm text-gray-500 mt-2">
      Simpan kode di bawah ini untuk mengecek balasan nanti.
    </p>

    <div id="kode" class="mt-5 bg-slate-100 rounded-xl py-4 text-2xl font-mono font-bold tracking-widest text-sage-600">
      <?= htmlspecialchars($kode) ?>
    </div>

    <button onclick="salin()" id="btn"
      class="mt-4 w-full bg-sage-600 hover:bg-sage-700 text-white font-semibold py-3 rounded-xl">
      Salin Kode
    </button>
    <a href="index.php" class="block mt-3 text-sm text-gray-500 hover:underline">Kembali ke beranda</a>

    <p class="text-xs text-red-500 mt-5">
      ⚠️ Kode ini tidak bisa dipulihkan. Catat atau screenshot sekarang.
    </p>
  </div>

  <script>
    function salin() {
      var teks = document.getElementById('kode').innerText.trim();
      navigator.clipboard.writeText(teks).then(function () {
        document.getElementById('btn').innerText = 'Tersalin ✓';
      });
    }
  </script>
</body>
</html>