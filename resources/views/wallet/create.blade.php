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
                <h2>Dompet & Rekening</h2>
            </div>
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
        </div>
        <!-- ========== title-wrapper end ========== -->
        <div class="row">
        <div class="col-lg-12">
            <div class="card-style mb-30">
                <!-- <h6 class="mb-25">Tambah dompet & rekening</h6> -->
                <form id="confirm-form" action="{{ route('wallet.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="input-style-1">
                            <label for="name">Nama Dompet / Rekening <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" placeholder="Masukan nama dompet / rekening" value="{{ old('name') }}" />
                            @error('name')
                                <div class="text-danger text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="select-style-1">
                            <label for="type">Tipe Dompet / Rekening <span class="text-danger">*</span></label>
                            <div class="select-position">
                                <select name="type" id="type" required>
                                    <option value="" disabled>Pilih tipe dompet / rekening</option>
                                    <option value="cash" {{ old('type') == 'cash' ? 'selected' : '' }}>Cash (Tunai)</option>
                                    <option value="bank" {{ old('type') == 'bank' ? 'selected' : '' }}>Bank</option>
                                    <option value="ewallet" {{ old('type') == 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                                    <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>
                            @error('type')
                                <div class="text-danger text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="select-style-1">
                            <label for="currency">Mata Uang <span class="text-danger">*</span></label>
                            <div class="select-position">
                                <select name="currency" id="currency" required>
                                    <option value="" disabled>Pilih mata uang</option>
                                    <option value="IDR" {{ old('currency') == 'IDR' ? 'selected' : '' }}>Rupiah (IDR)</option>
                                    <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>Dolar Amerika (USD)</option>
                                </select>
                            </div>
                            @error('currency')
                                <div class="text-danger text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="input-style-1">
                            <label for="begin_balance">Saldo <span class="text-danger">*</span></label>
                            <input type="number" id="begin_balance" name="begin_balance" placeholder="Masukan saldo" value="{{ old('begin_balance') }}" />
                            @error('begin_balance')
                                <div class="text-danger text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="input-style-1">
                            <label for="account_number">Nomor Dompet / Rekening <span class="text-gray text-sm">(Optional jika cash)</span></label>
                            <input type="text" id="account_number" name="account_number" placeholder="Masukan dompet / rekening" value="{{ old('account_number') }}" />
                            @error('account_number')
                                <div class="text-danger text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="input-style-1">
                            <label for="bank_name">Nama Bank <span class="text-gray text-sm">(Optional jika cash)</span></label>
                            <input type="text" id="bank_name" name="bank_name" placeholder="Masukan nama bank" value="{{ old('bank_name') }}" />
                            @error('bank_name')
                                <div class="text-danger text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="input-style-1">
                            <label for="description">Deskripsi <span class="text-gray text-sm">(Optional)</span></label>
                            <textarea placeholder="Masukan deskripsi" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="text-danger text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- end col -->
                    <div class="col-12">
                        <div class="button-group d-flex justify-content-end flex-wrap">
                            <button class="main-btn btn-confirm primary-btn btn-hover text-center">
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