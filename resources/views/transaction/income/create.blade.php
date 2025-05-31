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
                <h2>Pemasukan</h2>
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
                <form id="confirm-form" action="{{ route('income.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-6">
                        <div class="input-style-1">
                            <label for="date">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" id="date" name="date" placeholder="Masukan tanggal" value="{{ old('date', date('Y-m-d')) }}" />
                            @error('date')
                                <div class="text-danger text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="input-style-1">
                          <label for="name">Nama Pemasukan <span class="text-danger">*</span></label>
                          <input type="text" id="name" name="name" placeholder="Masukan nama pemasukan" value="{{ old('name') }}" />
                          @error('name')
                              <div class="text-danger text-sm mt-2">{{ $message }}</div>
                          @enderror
                        </div>
                        <div class="select-style-1">
                            <label for="category_id">Kategori <span class="text-danger">*</span></label>
                            <div class="select-position">
                                <select name="category_id" id="category_id" required>
                                    <option value="" disabled selected>Pilih kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('type')
                                <div class="text-danger text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="select-style-1">
                            <label for="wallet_id">Dompet & Rekening <span class="text-danger">*</span></label>
                            <div class="select-position">
                                <select name="wallet_id" id="wallet_id" required>
                                    <option value="" disabled selected>Pilih dompet & rekening</option>
                                    @foreach ($wallets as $wallet)
                                        <option value="{{ $wallet->id }}" data-currency="{{ $wallet->currency }}" {{ old('wallet_id') == $wallet->id ? 'selected' : '' }}>{{ $wallet->name }} {{ $wallet->account_number ? '- ' . $wallet->account_number : '' }} {!! $wallet->status == 'active' ? '<span class="text-success">(Aktif)</span>' : '<span class="text-danger">(Nonaktif)</span>' !!}</option> 
                                    @endforeach
                                </select>
                            </div>
                            @error('wallet_id')
                                <div class="text-danger text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        
                    </div>

                    <div class="col-6">
                        <div class="input-style-1">
                          <label for="amount">Jumlah <span class="text-danger">*</span></label>
                          <input type="number" id="amount" name="amount" placeholder="Masukan jumlah" value="{{ old('amount') }}" />
                          @error('amount')
                              <div class="text-danger text-sm mt-2">{{ $message }}</div>
                          @enderror
                        </div>
                        <div class="input-style-1" id="exchangeRateWrapper" style="display: none;">
                            <label for="exchange_rate">Nilai Tukar USD ke IDR <span class="text-danger">*</span></label>
                            <input type="number" id="exchange_rate" name="exchange_rate" placeholder="Masukan jumlah" value="{{ old('exchange_rate') }}" />
                            @error('exchange_rate')
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

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const walletSelect = document.getElementById('wallet_id');
            const exchangeRateWrapper = document.getElementById('exchangeRateWrapper');
            const exchangeRateInput = document.getElementById('exchange_rate');

            function toggleExchangeRateInput() {
                const selectedOption = walletSelect.options[walletSelect.selectedIndex];
                const currency = selectedOption.getAttribute('data-currency');

                if (currency === 'USD') {
                    exchangeRateWrapper.style.display = 'block';
                    // exchangeRateInput.required = true;
                } else {
                    exchangeRateWrapper.style.display = 'none';
                    // exchangeRateInput.required = false;
                    exchangeRateInput.value = '';
                }
            }

            walletSelect.addEventListener('change', toggleExchangeRateInput);
            toggleExchangeRateInput();
        });
    </script>
@endsection
