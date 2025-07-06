@extends('layouts.app')

@section('content')

    <!-- ========== section start ========== -->
    <section class="section">
    <div class="container-fluid">
        <!-- ========== title-wrapper start ========== -->
        <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
            <div class="title">
                <h2>Profil</h2>
            </div>
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
        </div>
        <!-- ========== title-wrapper end ========== -->
        <div class="row">
        <div class="col-lg-6">
            <div class="card-style mb-30">
                <div class="title d-flex flex-wrap align-items-center justify-content-between mb-3">
                  <div class="left">
                    <h6 class="text-medium mb-30">Ubah Profil</h6>
                  </div>
                </div>
                <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="input-style-1">
                            <label for="email">Nama <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" placeholder="Masukan nama" value="{{ old('name', $user->name) }}" />
                            @error('name')
                                <div class="text-danger text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="input-style-1">
                            <label for="email">Email<span class="text-danger">*</span></label>
                            <input type="text" id="email" name="email" placeholder="Masukan nama" value="{{ old('email', $user->email) }}" />
                            @error('email')
                                <div class="text-danger text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>


                    <!-- end col -->
                    <div class="col-12">
                        <div class="button-group d-flex justify-content-end flex-wrap">
                            <button class="main-btn primary-btn btn-hover text-center">
                                Ubah Profil
                            </button>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
                </form>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card-style mb-30">
                <div class="title d-flex flex-wrap align-items-center justify-content-between mb-3">
                  <div class="left">
                    <h6 class="text-medium mb-30">Ubah Kata Sandi</h6>
                  </div>
                </div>
                <form action="{{ route('profile.changePassword') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="input-style-1">
                            <label for="current_password">Kata Sandi Saat Ini<span class="text-danger">*</span></label>
                            <input type="password" id="current_password" name="current_password" placeholder="Masukan kata sandi saat ini"/>
                            @error('current_password')
                                <div class="text-danger text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="input-style-1">
                            <label for="new_password">Kata Sandi Baru<span class="text-danger">*</span></label>
                            <input type="password" id="new_password" name="new_password" placeholder="Masukan kata sandi baru"/>
                            @error('new_password')
                                <div class="text-danger text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="input-style-1">
                            <label for="new_password_confirmation">Konfirmasi Kata Sandi<span class="text-danger">*</span></label>
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation" placeholder="Konfirmasi kata sandi baru" />
                            @error('new_password_confirmation')
                                <div class="text-danger text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>


                    <!-- end col -->
                    <div class="col-12">
                        <div class="button-group d-flex justify-content-end flex-wrap">
                            <button class="main-btn primary-btn btn-hover text-center">
                                Ubah Kata Sandi
                            </button>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
                </form>
            </div>
        </div>
        <!-- End Col -->
        </div>
        <!-- End Row -->
    </div>
    <!-- end container -->
    </section>
    <!-- ========== section end ========== -->

@endsection