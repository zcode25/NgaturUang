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
            <div class="auth-cover-wrapper bg-primary-100">
            <div class="auth-cover">
                <div class="title text-center">
                <h1 class="text-primary mb-10">Selamat Datang di NgaturUang!</h1>
                <p class="text-medium">
                    Kelola keuangan pribadimu dengan mudah dan teratur
                </p>
                </div>
                <div class="cover-image">
                <img src="assets/images/auth/signin-image.svg" alt="" />
                </div>
                <div class="shape-image">
                <img src="assets/images/auth/shape.svg" alt="" />
                </div>
            </div>
            </div>
        </div>
        <!-- end col -->
        <div class="col-lg-6">
            <div class="signin-wrapper">
            <div class="form-wrapper">
                <h6 class="mb-15">Yuk! Kelola Keuangan Kamu</h6>
                <p class="text-sm mb-25">
                Mulai kelola keuangan pribadimu dengan mudah dan teratur.
                </p>
                @if ($errors->has('login_error'))
                <div class="alert-box danger-alert">
                  <div class="alert">
                    <p class="text-medium">
                        {{ $errors->first('login_error') }}
                    </p>
                  </div>
                </div>
                @endif
                <form action="{{ route('signin.authenticate') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-12">
                    <div class="input-style-1">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Masukan email kamu" />
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
                    <div class="button-group d-flex justify-content-center flex-wrap">
                        <button class="main-btn primary-btn btn-hover w-100 text-center">
                        Masuk
                        </button>
                    </div>
                    </div>
                </div>
                <!-- end row -->
                </form>
                <div class="singin-option pt-40">
                <p class="text-sm text-medium text-dark text-center">
                    Belum memiliki akun?
                    <a href="{{ route('signup') }}">Buat akun</a>
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