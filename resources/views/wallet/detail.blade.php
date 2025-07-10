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
                <h2>Dompet & Rekening</h2>
            </div>
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
        </div>
        <!-- ========== title-wrapper end ========== -->
        <div class="row">
            <div class="col-xl-12">
                <div class="icon-card mb-30">
                    @php
                        $iconClass = match($wallet->type) {
                            'cash' => 'lni lni-dollar',
                            'bank' => 'lni lni-credit-cards',
                            'ewallet' => 'lni lni-wallet',
                            'other' => 'lni lni-coin',
                        };

                        $colorClass = match($wallet->type) {
                            'cash' => 'success',
                            'bank' => 'primary',
                            'ewallet' => 'orange',
                            'other' => 'purple',
                        };
                    @endphp

                    <div class="icon {{ $colorClass }}">
                        <i class="{{ $iconClass }}"></i>
                    </div>


                    <div class="content">
                        <div class="d-flex justify-content-start align-items-center mb-10">
                            <h6 class="mb-0 me-2">{{ $wallet->name }}</h6>
                        </div>

                        <h3 class="text-bold mb-10">
                            {{ number_format($wallet->balance, 0) }} {{ $wallet->currency }}
                        </h3>

                        @php
                            $accountLabel = match($wallet->type) {
                                'bank' => 'No. Rekening',
                                'ewallet' => 'No. E-Wallet',
                                'cash' => 'Tunai',
                                default => 'Info Akun',
                            };
                        @endphp

                        @if($wallet->type === 'cash')
                            <p class="text-sm text-gray mb-0">{{ $accountLabel }}</p>
                        @elseif($wallet->account_number)
                            <p class="text-sm text-gray mb-0">{{ $accountLabel }}: {{ $wallet->account_number }}</p>
                        @else
                            <p class="text-sm text-gray mb-0">-</p>
                        @endif

                    </div>
                </div>
            </div>
        </div>
        <!-- End Col -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card-style mb-30">
                    <!-- <h6 class="mb-25">Tambah dompet & rekening</h6> -->
                    <form action="{{ route('wallet.update', ['id' => $wallet->id]) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-6">
                            <div class="input-style-1">
                                <label for="name">Nama Dompet / Rekening <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" placeholder="Masukan nama dompet / rekening" value="{{ old('name', $wallet->name) }}" />
                                @error('name')
                                    <div class="text-danger text-sm mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="select-style-1">
                                <label for="type">Tipe Dompet / Rekening <span class="text-danger">*</span></label>
                                <div class="select-position">
                                    <select name="type" id="type" required>
                                        <option value="" disabled>Pilih tipe dompet / rekening</option>
                                        <option value="cash" {{ old('type', $wallet->type) == 'cash' ? 'selected' : '' }}>Cash (Tunai)</option>
                                        <option value="bank" {{ old('type', $wallet->type) == 'bank' ? 'selected' : '' }}>Bank</option>
                                        <option value="ewallet" {{ old('type', $wallet->type) == 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                                        <option value="other" {{ old('type', $wallet->type) == 'other' ? 'selected' : '' }}>Lainnya</option>
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
                                        <option value="IDR" {{ old('currency', $wallet->currency) == 'IDR' ? 'selected' : '' }}>Rupiah (IDR)</option>
                                        <option value="USD" {{ old('currency', $wallet->currency) == 'USD' ? 'selected' : '' }}>Dolar Amerika (USD)</option>
                                    </select>
                                </div>
                                @error('currency')
                                    <div class="text-danger text-sm mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- <div class="input-style-1">
                                <label for="balance">Saldo <span class="text-danger">*</span></label>
                                <input type="number" id="balance" name="balance" placeholder="Masukan saldo" value="{{ old('balance') }}" />
                                @error('balance')
                                    <div class="text-danger text-sm mt-2">{{ $message }}</div>
                                @enderror
                            </div> -->
                            
                        </div>

                        <div class="col-6">
                            <div class="input-style-1">
                                <label for="account_number">Nomor Dompet / Rekening <span class="text-gray text-sm">(Optional jika cash)</span></label>
                                <input type="text" id="account_number" name="account_number" placeholder="Masukan dompet / rekening" value="{{ old('account_number', $wallet->account_number) }}" />
                                @error('account_number')
                                    <div class="text-danger text-sm mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="input-style-1">
                                <label for="bank_name">Nama Bank <span class="text-gray text-sm">(Optional jika cash)</span></label>
                                <input type="text" id="bank_name" name="bank_name" placeholder="Masukan nama bank" value="{{ old('bank_name', $wallet->bank_name) }}" />
                                @error('bank_name')
                                    <div class="text-danger text-sm mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="input-style-1">
                                <label for="description">Deskripsi <span class="text-gray text-sm">(Optional)</span></label>
                                <textarea placeholder="Masukan deskripsi" id="description" name="description" rows="3">{{ old('description', $wallet->description) }}</textarea>
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
         <div class="row">
            <div class="col-lg-12">
                <div class="card-style mb-30">
                <div class="title d-flex flex-wrap align-items-center justify-content-between">
                    <div class="left">
                        <h6 class="text-medium">Perbarui Status {{ $wallet->name }}</h6>
                    </div>
                    <div class="right">
                        <form id="activation-form" action="{{ route('wallet.toggle', ['id' => $wallet->id]) }}" method="POST">
                            @csrf
                            @if($wallet->status === 'active')
                                <button type="submit" class="main-btn danger-btn btn-hover btn-activation">
                                    Nonaktifkan
                                </button>
                            @else
                                <button type="submit" class="main-btn success-btn btn-hover">
                                    Aktifkan
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
                <!-- End Title -->
                </div>
            </div>
        </div>
    </div>
    <!-- end container -->
    </section>
    <!-- ========== section end ========== -->

@endsection