<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NgaturUang - Kelola Keuangan Pribadi</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
            /* padding-top: 70px; */
        }

        .hero {
            background: url('/assets/images/landing/bg-hero.jpg') no-repeat center center;
            background-size: cover;
            color: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
        }

        .hero img {
            max-width: 100%;
            height: auto;
            display: block;
            }

            .carousel-inner {
            width: 100%;
            }

            @media (max-width: 768px) {
            .hero .container {
                padding-left: 15px;
                padding-right: 15px;
            }
        }

        .hero::after {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .section {
            padding: 80px 0;
        }

        .icon-3d {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
        }

        .feature-card {
            transition: transform 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-10px);
        }

        footer {
            background-color: #222;
        }
    </style>
</head>
<body>

    <!-- ✅ NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                NgaturUang<br>
                <span class="fw-light" style="font-size: 13px">by terasweb.id</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center d-flex flex-column flex-lg-row gap-2">
                    <li class="nav-item w-100 text-center">
                        <a class="btn btn-primary w-100 px-4 py-2" href="{{ route('signup') }}">Daftar</a>
                    </li>
                    <li class="nav-item w-100 text-center">
                        <a class="btn btn-outline-light w-100 px-4 py-2" href="{{ route('signin') }}">Masuk</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <!-- ✅ HERO SECTION -->
    <header class="hero text-center text-white overflow-hidden">
        <div class="container hero-content">
            <div class="row align-items-center">
                <!-- Konten Teks -->
                <div class="col-xl-5 mb-4 text-xl-start" data-aos="fade-right">
                    <h1 class="display-6 fw-bold mb-4">Saatnya naik level bareng keuanganmu.</h1>
                    <p class="mb-4">
                        Dengan NgaturUang, kamu bisa pantau semua pemasukan dan pengeluaran, atur anggaran harian sampai bulanan, dan tetap tenang karena semua data keuanganmu tersusun rapi.
                    </p>
                    <a href="{{ route('signup') }}" class="btn btn-primary me-2 px-4 py-2">Buat Akun Gratis!</a>
                    <a href="#tentang" class="btn btn-outline-light px-4 py-2">Lihat Fitur</a>
                </div>

                <!-- Gambar Carousel -->
                <div class="col-xl-7 d-flex justify-content-center" data-aos="fade-left">
                    <div class="w-100 overflow-hidden">
                        <div id="mockupCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="2500">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="/assets/images/landing/mockup1.png" class="img-fluid d-block mx-auto" alt="Mockup 1">
                                </div>
                                <div class="carousel-item">
                                    <img src="/assets/images/landing/mockup2.png" class="img-fluid d-block mx-auto" alt="Mockup 2">
                                </div>
                                <div class="carousel-item">
                                    <img src="/assets/images/landing/mockup3.png" class="img-fluid d-block mx-auto" alt="Mockup 3">
                                </div>
                                <div class="carousel-item">
                                    <img src="/assets/images/landing/mockup4.png" class="img-fluid d-block mx-auto" alt="Mockup 4">
                                </div>
                                <div class="carousel-item">
                                    <img src="/assets/images/landing/mockup5.png" class="img-fluid d-block mx-auto" alt="Mockup 5">
                                </div>
                                <div class="carousel-item">
                                    <img src="/assets/images/landing/mockup6.png" class="img-fluid d-block mx-auto" alt="Mockup 6">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>




    <!-- ✅ SECTION: TENTANG -->
    <section id="tentang" class="section bg-white text-center" style="padding-top: 200px; padding-bottom: 200px;">
        <div class="container">
            <h2 class="mb-5 fw-bold" data-aos="fade-up"><span class="text-success">NgaturUang!</span> biar uang nggak cuma numpang lewat.</h2>
            <div class="row">
                <div class="col-xl-6 mb-4 d-flex justify-content-center align-items-center text-center p-4">
                    <img src="/assets/images/landing/img1.jpg" alt="image" class="img-fluid rounded-4" data-aos="fade-up">
                </div>
                <div class="col-xl-6 d-flex flex-column justify-content-center text-xl-start">
                    <p class="mb-4" data-aos="fade-up" data-aos-delay="100">
                        NgaturUang adalah aplikasi pengelola keuangan pribadi yang bantu kamu catat semua pemasukan dan pengeluaran, atur anggaran, dan lihat grafik kondisi finansialmu.
                        <span class="fst-italic">Nggak perlu spreadsheet ribet, cukup klik dan beres.</span>
                    </p>
                    <p class="mb-4" data-aos="fade-up" data-aos-delay="100">
                        <span class="fst-italic">Mau tahu berapa banyak uang yang kamu habiskan buat ngopi bulan ini?</span> Atau pengin tahu berapa persen dari income kamu yang bisa ditabung? NgaturUang kasih jawabannya—langsung, real-time, dan gampang dimengerti.
                    </p>
                    <div data-aos="fade-up" data-aos-delay="100">
                        <a href="{{ route("signup") }}" class="btn btn-dark me-2 px-4 py-2">Buat Akun Gratis!</a>
                    </div>
                </div>
                
            </div>
            
        </div>
    </section>

    <!-- ✅ SECTION: FITUR -->
    <section id="fitur" class="section bg-light" style="padding-top: 200px; padding-bottom: 200px;">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold" data-aos="fade-up">Fitur Unggulan</h2>
                <p data-aos="fade-up" data-aos-delay="100">Semua yang kamu butuhkan untuk mengatur keuangan, langsung dari satu aplikasi.</p>
            </div>
            <div class="row align-items-center mb-5">
                <div class="col-xl-6 order-1 order-xl-1 mb-5" data-aos="fade-right">
                    <img src="/assets/images/landing/mockup1.png" alt="Dashboard NgaturUang" class="img-fluid">
                </div>
                <div class="col-xl-6 order-2 order-xl-2 text-xl-start text-center" data-aos="fade-left">
                    <h4 class="mb-3">Dashboard Keuangan yang Intuitif</h4>
                    <p class="text-muted">
                        Pantau saldo, pemasukan, pengeluaran, dan selisih keuanganmu dalam satu tampilan yang jelas dan real-time. Dirancang agar kamu bisa mengambil keputusan keuangan dengan cepat dan tepat—tanpa harus buka banyak spreadsheet.
                    </p>
                </div>
            </div>
            <div class="row align-items-center mb-5">
                <div class="col-xl-6 order-2 order-xl-1 text-xl-end text-center" data-aos="fade-right">
                    <h4 class="mb-3">Kelola Semua Dompet & Rekening</h4>
                    <p class="text-muted">
                        Punya lebih dari satu rekening atau dompet digital? Tenang, kamu bisa kelola semuanya di NgaturUang. 
                        Tambahkan rekening bank, e-wallet, bahkan saldo tunai—semua tercatat rapi dan otomatis dihitung dalam total saldo kamu.
                    </p>
                </div>
                <div class="col-xl-6 order-1 order-xl-2 mb-5" data-aos="fade-left">
                    <img src="/assets/images/landing/mockup2.png" alt="Dompet NgaturUang" class="img-fluid">
                </div>
            </div>
            <div class="row align-items-center mb-5">
                <div class="col-xl-6 order-1 order-xl-1 mb-5" data-aos="fade-right">
                    <img src="/assets/images/landing/mockup3.png" alt="Pemasukan NgaturUang" class="img-fluid">
                </div>
                <div class="col-xl-6 order-2 order-xl-2 text-xl-start text-center" data-aos="fade-left">
                    <h4 class="mb-3">Catat Setiap Pemasukan, Sekecil Apa pun</h4>
                    <p class="text-muted">
                        Gaji bulanan, bonus proyek, uang jajan, hingga cashback dari e-wallet—semua bisa kamu catat dengan mudah. 
                        Nggak ada lagi uang "hilang entah ke mana" karena setiap pemasukan tercatat rapi.
                    </p>
                </div>
            </div>
            <div class="row align-items-center mb-5">
                <div class="col-xl-6 order-2 order-xl-1 text-xl-end text-center" data-aos="fade-right">
                    <h4 class="mb-3">Pantau Ke Mana Uangmu Pergi</h4>
                    <p class="text-muted">
                        Mau tahu berapa banyak yang kamu habiskan buat jajan, transport, atau langganan streaming? Semua bisa kamu lacak di fitur Pengeluaran.
                        Tambahkan detail belanja harianmu, pilih kategori, dan lihat grafik pengeluaran dalam hitungan detik. 
                        Biar kamu bisa atur ulang gaya hidup sesuai prioritas.
                    </p>
                </div>
                <div class="col-xl-6 order-1 order-xl-2 mb-5" data-aos="fade-left">
                    <img src="/assets/images/landing/mockup4.png" alt="Pengeluaran NgaturUang" class="img-fluid">
                </div>
            </div>
            <div class="row align-items-center mb-5">
                <div class="col-xl-6 order-1 order-xl-1 mb-5" data-aos="fade-right">
                    <img src="/assets/images/landing/mockup5.png" alt="Anggaran NgaturUang" class="img-fluid">
                </div>
                <div class="col-xl-6 order-2 order-xl-2 text-xl-start text-center" data-aos="fade-left">
                    <h4 class="mb-3">Bikin Anggaran, Biar Nggak Kebobolan</h4>
                    <p class="text-muted">
                        Susun batas pengeluaran per kategori—seperti makanan, transportasi, hiburan, atau belanja—langsung dari dashboard kamu.
                        Dengan fitur Anggaran, kamu bisa tahu kapan harus ngerem belanja dan kapan bisa kasih diri sendiri reward. Semua demi keuangan yang lebih sehat!
                    </p>
                </div>
            </div>
            <div class="row align-items-center mb-5">
                <div class="col-xl-6 order-2 order-xl-1 text-xl-end text-center" data-aos="fade-right">
                    <h4 class="mb-3">Laporan Keuangan Otomatis & Real-Time</h4>
                    <p class="text-muted">
                        Cek rangkuman pemasukan, pengeluaran, dan sisa saldo kamu kapan saja dalam bentuk grafik dan tabel yang mudah dibaca.
                        Fitur E-Statement bantu kamu refleksi finansial bulanan—lihat tren pengeluaran, temukan kebocoran, dan rencanakan bulan depan lebih baik lagi. 
                    </p>
                </div>
                <div class="col-xl-6 order-1 order-xl-2 mb-5" data-aos="fade-left">
                    <img src="/assets/images/landing/mockup6.png" alt="Laporan NgaturUang" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- ✅ SECTION: PENAWARAN -->
    <section class="section bg-white text-center" style="padding-top: 200px; padding-bottom: 200px;">
        <div class="container">
            <h2 class="fw-bold" data-aos="fade-up">Kenapa Harus NgaturUang?</h2>
            <p class="text-center text-muted mb-5" data-aos="fade-up" data-aos-delay="100">
                Aplikasi keuangan yang dirancang untuk bantu kamu mengatur uang tanpa ribet dan tanpa gangguan.
            </p>
            <div class="row align-items-center">
                <div class="col-xl-6 mb-5" data-aos="fade-left">
                    <img src="/assets/images/landing/mockup1.png" class="img-fluid" alt="Mockup NgaturUang">
                </div>
                <div class="col-xl-6" data-aos="fade-right">
                    <ul class="list-unstyled fs-5 text-xl-start text-center">
                        <li class="mb-3">✅ <strong>Gratis</strong> dan mudah digunakan</li>
                        <li class="mb-3">✅ <strong>Tanpa iklan</strong> yang mengganggu</li>
                        <li class="mb-3">✅ <strong>Data aman</strong> dan terenkripsi</li>
                        <li class="mb-3">✅ Laporan <strong>bulanan & tahunan otomatis</strong></li>
                    </ul>
                </div>
                
            </div>
        </div>
    </section>

    <!-- ✅ SECTION: SIGN UP -->
    <section class="section bg-light text-center" style="padding-top: 200px; padding-bottom: 200px;">
        <div class="container">
            <h2 class="fw-bold mb-3" data-aos="fade-up">Siap Memulai?</h2>
            <p class="mb-5" data-aos="fade-up">Daftar sekarang dan kelola keuanganmu dengan lebih baik. Aplikasi ini gratis dan langsung bisa kamu pakai.</p>
            <div class="row justify-content-center mb-5">
                <div class="col-md-8 col-lg-6" data-aos="fade-up">
                    <div class="w-100 overflow-hidden">
                        <div id="mockupCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="2500">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="/assets/images/landing/mockup1.png" class="img-fluid d-block mx-auto" alt="Mockup 1">
                                </div>
                                <div class="carousel-item">
                                    <img src="/assets/images/landing/mockup2.png" class="img-fluid d-block mx-auto" alt="Mockup 2">
                                </div>
                                <div class="carousel-item">
                                    <img src="/assets/images/landing/mockup3.png" class="img-fluid d-block mx-auto" alt="Mockup 3">
                                </div>
                                <div class="carousel-item">
                                    <img src="/assets/images/landing/mockup4.png" class="img-fluid d-block mx-auto" alt="Mockup 4">
                                </div>
                                <div class="carousel-item">
                                    <img src="/assets/images/landing/mockup5.png" class="img-fluid d-block mx-auto" alt="Mockup 5">
                                </div>
                                <div class="carousel-item">
                                    <img src="/assets/images/landing/mockup6.png" class="img-fluid d-block mx-auto" alt="Mockup 6">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-center flex-wrap gap-3" data-aos="fade-up">
                <a href="{{ route('signup') }}" class="btn btn-dark px-4 py-2">Buat Akun Gratis!</a>
                <a href="https://wa.me/6281288228600?text=Halo%2C+saya+tertarik+dengan+source+code+NgaturUang" class="btn btn-outline-dark px-4 py-2" target="_blank">
                    Dapatkan Source Code
                </a>
            </div>
        </div>
    </section>

    <!-- ✅ FOOTER -->
    <footer class="text-center py-4 text-light">
        <p>&copy; {{ date('Y') }} NgaturUang. All rights reserved.</p>
    </footer>

    <!-- JS SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>
</html>
