<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NgaturUang - Kelola Keuangan Pribadi Anda</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; }
  </style>
</head>
<body class="bg-white text-gray-800">

  <!-- HERO -->
  <section class="bg-gradient-to-br from-indigo-600 to-purple-600 text-white py-20">
    <div class="max-w-6xl mx-auto px-6 text-center">
      <h1 class="text-4xl md:text-5xl font-bold mb-4">NgaturUang</h1>
      <p class="text-xl mb-6">Aplikasi cerdas untuk kelola pemasukan, pengeluaran, dan masa depan keuanganmu.</p>
      <div class="space-x-4">
        <a href="#pricing" class="bg-white text-indigo-700 px-6 py-3 rounded-full font-semibold hover:bg-gray-200">Coba Sekarang</a>
        <a href="#features" class="text-white underline hover:text-gray-300">Pelajari Lebih Lanjut</a>
      </div>
    </div>
  </section>

  <!-- FITUR UTAMA -->
  <section id="features" class="py-20 bg-gray-50">
    <div class="max-w-6xl mx-auto px-6">
      <h2 class="text-3xl font-semibold text-center mb-12">Fitur Utama</h2>
      <div class="grid md:grid-cols-3 gap-10 text-center">
        <div>
          <div class="text-indigo-600 mb-4">
            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 1.567-3 3.5S10.343 15 12 15s3-1.567 3-3.5S13.657 8 12 8z" /><path d="M12 2C6.48 2 2 6.92 2 12c0 4.656 3.75 9.5 10 9.5S22 16.656 22 12c0-5.08-4.48-10-10-10z" /></svg>
          </div>
          <h3 class="font-semibold text-xl mb-2">Pantau Pengeluaran</h3>
          <p class="text-gray-600">Catat dan analisa pengeluaran harian agar tidak kebablasan.</p>
        </div>
        <div>
          <div class="text-indigo-600 mb-4">
            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
          </div>
          <h3 class="font-semibold text-xl mb-2">Laporan Bulanan</h3>
          <p class="text-gray-600">Grafik dan ringkasan keuangan per bulan dalam satu klik.</p>
        </div>
        <div>
          <div class="text-indigo-600 mb-4">
            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 10h18M3 6h18M9 14h6m-6 4h6" /></svg>
          </div>
          <h3 class="font-semibold text-xl mb-2">Bisa Custom & Developer Friendly</h3>
          <p class="text-gray-600">Beli source code? Bisa! Ingin pakai SaaS? Juga bisa!</p>
        </div>
      </div>
    </div>
  </section>

  <!-- PRICING -->
  <section id="pricing" class="py-20 bg-white">
    <div class="max-w-5xl mx-auto px-6 text-center">
      <h2 class="text-3xl font-semibold mb-8">Pilih Cara Kamu</h2>
      <div class="grid md:grid-cols-2 gap-8">
        <div class="border rounded-lg p-8 hover:shadow-lg">
          <h3 class="text-xl font-semibold mb-4">Berlangganan</h3>
          <p class="text-4xl font-bold text-indigo-600 mb-4">Rp29.000<span class="text-base font-normal">/bulan</span></p>
          <ul class="text-gray-600 mb-6 space-y-2">
            <li>✔ Akses cloud penuh</li>
            <li>✔ Update & dukungan</li>
            <li>✔ Backup otomatis</li>
          </ul>
          <a href="#" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-full hover:bg-indigo-700">Mulai Berlangganan</a>
        </div>
        <div class="border rounded-lg p-8 hover:shadow-lg">
          <h3 class="text-xl font-semibold mb-4">Beli Source Code</h3>
          <p class="text-4xl font-bold text-indigo-600 mb-4">Rp399.000</p>
          <ul class="text-gray-600 mb-6 space-y-2">
            <li>✔ Kode full Laravel + Vue</li>
            <li>✔ Dokumentasi lengkap</li>
            <li>✔ Sekali bayar, milik selamanya</li>
          </ul>
          <a href="#" class="inline-block bg-gray-800 text-white px-6 py-3 rounded-full hover:bg-gray-900">Beli Sekarang</a>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="bg-gray-100 py-10">
    <div class="max-w-6xl mx-auto px-6 text-center text-gray-600">
      <p class="mb-2 font-semibold">NgaturUang © {{ date('Y') }}</p>
      <p class="text-sm">Dibuat dengan ❤️ oleh Developer Indonesia</p>
    </div>
  </footer>

</body>
</html>
