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
            <div class="col">
                <div class="icon-card mb-30">
                    <div class="col-md-6 d-flex align-items-center gap-3">
                        <div class="icon purple">
                            <i class="lni lni-dollar"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Total Saldo</h6>
                            <h3 class="text-bold mb-0">{{ number_format($totalBalance, 0) }} IDR</h3>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex justify-content-md-end justify-content-start gap-2 mt-3 mt-md-0">
                        <a href="{{ route('wallet.create') }}" class="main-btn primary-btn btn-sm">
                            Tambah Data <i class="lni lni-plus ms-1"></i>
                        </a>
                        @if(request('status') === 'inactive')
                            <a href="{{ route('wallet') }}" class="main-btn success-btn btn-sm">
                                Data Aktif <i class="lni lni-spinner-arrow ms-1"></i>
                            </a>
                        @else
                            <a href="{{ route('wallet', ['status' => 'inactive']) }}" class="main-btn danger-btn btn-sm">
                                Data Nonaktif <i class="lni lni-spinner-arrow ms-1"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- End Row -->
        <div class="row">
            @forelse ($wallets as $wallet)
            <div class="col-xl-4 col-lg-6 col-sm-12">
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
                            <h6 class="mb-0 me-2">{{ $wallet->name }}  </h6>
                            @if($wallet->status == 'active')
                                <div class="status-btn success-btn me-2">
                                    <span class="text-sm">Aktif</span>
                                </div>
                            @else
                            <div class="status-btn danger-btn-light me-2">
                                    <span class="text-sm">Nonaktif</span>
                                </div>
                            @endif
                            
                            
                            <a href="{{ route('wallet.detail', ['id' => $wallet->id]) }}" class="action">
                                <div class="status-btn dark-btn">
                                    <i class="lni lni-search-alt ms-1"></i>
                                </div>
                            </a>
                            
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
            <!-- End Col -->
            @empty
            <div class="col-lg-12">
                <div class="alert alert-info text-center">
                    @if(request('status') == 'inactive')
                        <p class="text-medium">Belum ada data dompet & rekening status nonaktif</p>
                    @else
                        <p class="text-medium">Belum ada data dompet & rekening</p>
                    @endif
                </div>
            </div>
            @endforelse
        </div>
    </div>
    <!-- end container -->
    </section>
    <!-- ========== section end ========== -->

@endsection