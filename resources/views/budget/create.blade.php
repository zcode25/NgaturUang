@extends('layouts.app')

@section('content')

    <!-- ========== section start ========== -->
    <section class="section">
    <div class="container-fluid">
        <!-- ========== title-wrapper start ========== -->
        <div class="title-wrapper pt-30 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
            <div class="title">
                <h2>Anggaran</h2>
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
                <!-- <h6 class="mb-25">Tambah dompet & rekening</h6> -->
                <form action="{{ route('budget.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="input-style-1">
                            <label for="name">Nama <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" placeholder="Masukan nama" value="{{ old('name') }}" />
                            @error('name')
                                <div class="text-danger text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="input-style-1">
                            <label for="start_date">Tanggal Awal <span class="text-danger">*</span></label>
                            <input type="date" id="start_date" name="start_date" placeholder="Masukan tanggal awal" value="{{ old('start_date') }}" />
                            @error('start_date')
                                <div class="text-danger text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="input-style-1">
                            <label for="end_date">Tanggal Akhir <span class="text-danger">*</span></label>
                            <input type="date" id="end_date" name="end_date" placeholder="Masukan tanggal akhir" value="{{ old('end_date') }}" />
                            @error('end_date')
                                <div class="text-danger text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>


                    <!-- end col -->
                    <div class="col-12">
                        <div class="button-group d-flex justify-content-end flex-wrap">
                            <button class="main-btn primary-btn btn-hover text-center">
                                Tambah
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