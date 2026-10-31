<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dear Mrs. Agnes</title>
  <?php include "tema.php"; ?>
</head>
<body class="bg-slate-50 min-h-screen flex items-start justify-center p-4">

  <div class="w-full max-w-md bg-white rounded-3xl shadow-lg overflow-hidden mt-4">

    <!-- Header biru -->
    <div class="bg-sage-600 text-white p-6 text-center">
      <h1 class="text-2xl font-bold">💬 Hallo Mrs. Agnes</h1>
      <p class="font-semibold text-sm mt-1">Ruang Aspirasi &amp; Pengaduan Warga Sekolah</p>
      <p class="text-xs mt-4 text-sage-100 text-justify">
        Platform ini disediakan khusus untuk seluruh warga sekolah
        yang ingin menyampaikan aspirasi, ide, saran, masukan, maupun
        pengaduan kepada Kepala Sekolah.
      </p>
    </div>

    <!-- Tombol tab -->
    <div class="flex border-b text-sm font-semibold">
      <button id="tab-kirim" onclick="bukaTab('kirim')"
        class="flex-1 py-3 text-sage-600 border-b-2 border-sage-600">
        Kirim Aspirasi/ Pengaduan
      </button>
      <button id="tab-cek" onclick="bukaTab('cek')"
        class="flex-1 py-3 text-gray-500">
        Cek Balasan
      </button>
    </div>

    <!-- Isi tab 1: Kirim -->
    <div id="isi-kirim" class="p-6 space-y-4">
      <form method="post" action="kirim.php" class="space-y-4">
         <div style="position:absolute;left:-9999px" aria-hidden="true">
  <input type="text" name="website" tabindex="-1" autocomplete="off">
</div>
<input type="hidden" name="t" value="<?= time() ?>">
        <div>
          <label class="text-sm font-semibold text-gray-700">
            Nama <span class="text-gray-400 font-normal">(Opsional)</span>
          </label>
          <input type="text" name="nama" placeholder="Samarkan atau kosongkan jika ingin anonim"
            class="w-full mt-1 px-4 py-3 rounded-xl bg-slate-100 text-sm outline-none focus:ring-2 focus:ring-sage-400">
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">
            Kelas <span class="text-gray-400 font-normal">(Opsional)</span>
          </label>
          <input type="text" name="kelas" placeholder="Kosongkan jika ingin anonim"
            class="w-full mt-1 px-4 py-3 rounded-xl bg-slate-100 text-sm outline-none focus:ring-2 focus:ring-sage-400">
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">
            Isi Aspirasi/Pengaduan <span class="text-red-500">*</span>
          </label>
          <textarea name="isi" rows="5" required
            placeholder="Silakan awali curhatan/aduan Anda dengan menulis: Dear Mrs. Agnes..."
            class="w-full mt-1 px-4 py-3 rounded-xl bg-slate-100 text-sm outline-none focus:ring-2 focus:ring-sage-400"></textarea>
        </div>
        <button type="submit"
  class="w-full bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold py-3 rounded-xl">
  Kirim Ke Mrs. Agnes ➤
</button>
      </form>
    </div>

    <!-- Isi tab 2: Cek Balasan -->
    <div id="isi-cek" class="p-6 space-y-4 hidden">
      <form method="post" action="cek.php" class="space-y-4">
        <div>
          <label class="text-sm font-semibold text-gray-700">Masukkan Kode Unik</label>
          <input type="text" name="kode" placeholder="Contoh: NPS-12345" required
            class="w-full mt-1 px-4 py-3 rounded-xl bg-slate-100 text-sm text-center font-mono outline-none focus:ring-2 focus:ring-sage-400">
        </div>
        <button type="submit"
          class="w-full bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold py-3 rounded-xl">
          Cari Balasan
        </button>
      </form>
    </div>

    <!-- Footer -->
    <p class="text-center text-xs text-gray-400 pb-5">
  <b>Aman &amp; Rahasia.</b> Presented by Putri Zulfa Nabighah, S.Kom<br>
  © 2026 SMPN 15 Pekalongan
</p>
  </div>

  <script>
    function bukaTab(nama) {
      var kirim = document.getElementById('isi-kirim');
      var cek   = document.getElementById('isi-cek');
      var tk    = document.getElementById('tab-kirim');
      var tc    = document.getElementById('tab-cek');
      var aktif = 'flex-1 py-3 text-sage-600 border-b-2 border-sage-600';
      var mati  = 'flex-1 py-3 text-gray-500';

      if (nama === 'kirim') {
        kirim.classList.remove('hidden'); cek.classList.add('hidden');
        tk.className = aktif; tc.className = mati;
      } else {
        cek.classList.remove('hidden'); kirim.classList.add('hidden');
        tc.className = aktif; tk.className = mati;
      }
    }
  </script>
</body>
</html>