@extends('layouts.guest')

@section('content')

<section class="signin-section">
    <div class="container-fluid">
        <!-- ========== title-wrapper start ========== -->
        <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title">
                    <h2>NgaturUang</h2><span>by terasweb.id</span>
                </div>
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
        </div>
        <!-- ========== title-wrapper end ========== -->

        <div class="row g-0 auth-row">
            <div class="col-lg-6">
            <div class="auth-cover-wrapper position-relative" style="background-image: url('/assets/images/landing/bg-hero1.jpg'); background-size: cover; background-position: center; min-height: 500px;">

                <!-- Overlay -->
                <div class="position-absolute top-0 start-0 w-100 h-100" style="background-color: rgba(0, 0, 0, 0.7); z-index: 1;"></div>

                <!-- Konten -->
                <div class="auth-cover d-flex flex-column justify-content-center align-items-center text-center text-white px-4 py-5 position-relative" style="z-index: 2; min-height: 500px;">
                <div class="title mb-4">
                    <h1 class="mb-3 text-light fw-bold  ">Kelola Keuanganmu</h1>
                    <p class="text-medium">Mulai kelola keuangan pribadimu dengan mudah dan teratur.</p>
                    
                </div>
                </div>

            </div>
        </div>


        <!-- end col -->
        <div class="col-lg-6">
            <div class="signup-wrapper">
            <div class="form-wrapper">
                <h6 class="mb-15">Yuk! Buat Akun Sakuku</h6>
                <p class="text-sm mb-25">
                Mulai kelola keuangan pribadimu dengan mudah dan teratur.
                <form action="{{ route('signup.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="input-style-1">
                            <label for="name">Nama</label>
                            <input type="text" id="name" name="name" placeholder="Masukan nama kamu" value="{{ old('name') }}" />
                            @error('name')
                                <div class="text-danger text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-12">
                        <div class="input-style-1">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="Masukan email kamu" value="{{ old('email') }}" />
                            @error('email')
                                <div class="text-danger text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-12">
                        <div class="input-style-1">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" placeholder="Masukan password kamu" />
                            @error('password')
                                <div class="text-danger text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-12">
                        <div class="input-style-1">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi password kamu" />
                            @error('password_confirmation')
                                <div class="text-danger text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-12">
                    <div class="button-group d-flex justify-content-center flex-wrap">
                        <button class="main-btn primary-btn btn-hover w-100 text-center">
                        Buat Akun
                        </button>
                    </div>
                    </div>
                </div>
                <!-- end row -->
                </form>
                <div class="singup-option pt-40">
                    <p class="text-sm text-medium text-dark text-center">
                        Sudah memiliki akun? <a href="{{ route('signin') }}">Masuk</a>
                    </p>
                </div>
            </div>
            </div>
        </div>
        <!-- end col -->
        </div>
        <!-- end row -->
    </div>
</section>

@endsection