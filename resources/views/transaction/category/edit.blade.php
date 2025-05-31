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
                <h2>Kategori Transaksi</h2>
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
                <form action="{{ route('category.update', ['category' => $category->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-12">
                        <div class="input-style-1">
                            <label for="name">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" placeholder="Masukan nama kategori" value="{{ old('name', $category->name) }}" />
                            @error('name')
                                <div class="text-danger text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="input-style-1">
                          <label for="description">Deskripsi <span class="text-gray text-sm">(Optional)</span></label>
                          <textarea placeholder="Masukan deskripsi" id="description" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
                          @error('description')
                              <div class="text-danger text-sm mt-2">{{ $message }}</div>
                          @enderror
                        </div>
                        
                    </div>


                    <!-- end col -->
                    <div class="col-12">
                        <div class="button-group d-flex justify-content-end flex-wrap">
                            <button class="main-btn primary-btn btn-hover text-center">
                                Perbarui
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