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
        <div class="col-xl-3 col-lg-4 col-sm-6">
            <div class="icon-card mb-30">
            <div class="icon success">
                <i class="lni lni-dollar"></i>
            </div>
            <div class="content">
                <h6 class="mb-10">Pemasukan Hari Ini</h6>
                <h3 class="text-bold mb-10">{{ number_format($dailyIncome, 0) }} IDR</h3>
            </div>
            </div>
            <!-- End Icon Cart -->
        </div>
        <!-- End Col -->
        <div class="col-xl-3 col-lg-4 col-sm-6">
            <div class="icon-card mb-30">
            <div class="icon primary">
                <i class="lni lni-dollar"></i>
            </div>
            <div class="content">
                <h6 class="mb-10">Pemasukan Bulan Ini</h6>
                <h3 class="text-bold mb-10">{{ number_format($monthlyIncome, 0) }} IDR</h3>
            </div>
            </div>
            <!-- End Icon Cart -->
        </div>
        <!-- End Col -->
        <div class="col-xl-3 col-lg-4 col-sm-6">
            <div class="icon-card mb-30">
            <div class="icon orange">
                <i class="lni lni-dollar"></i>
            </div>
            <div class="content">
                <h6 class="mb-10">Pemasukan Tahun Ini</h6>
                <h3 class="text-bold mb-10">{{ number_format($yearlyIncome, 0) }} IDR</h3>
            </div>
            </div>
            <!-- End Icon Cart -->
        </div>
        <!-- End Col -->
        <div class="col-xl-3 col-lg-4 col-sm-6">
            <div class="icon-card mb-30">
            <div class="icon purple">
                <i class="lni lni-dollar"></i>
            </div>
            <div class="content">
                <h6 class="mb-10">Total Pemasukan</h6>
                <h3 class="text-bold mb-10">{{ number_format($totalIncome, 0) }} IDR</h3>
            </div>
            </div>
            <!-- End Icon Cart -->
        </div>
        <!-- End Col -->
        </div>
        <!-- End Row -->
        <div class="row">
          <div class="col-lg-12">
            <div class="card-style mb-30">
              <div class="title d-flex flex-wrap align-items-center justify-content-between mb-3">
                <div class="left">
                  <h6 class="text-medium mb-30">Tabel Pemasukan</h6>
                </div>
                <div class="right">
                  <a href="{{ route('income.create') }}" class="main-btn primary-btn btn-sm">Tambah Data <i class="lni lni-plus ms-1"></i></a>
                  @if(request('status') == 'inactive')
                    <a href="{{ route('income') }}" class="main-btn dark-btn btn-sm">Kembali</a>
                  @else
                    <a href="{{ route('income', ['status' => 'inactive']) }}" class="main-btn danger-btn btn-sm">Sampah <i class="lni lni-trash-can ms-1"></i></a>
                  @endif
                  
                </div>
              </div>
              @if(request('status') == 'inactive' && $incomes->isEmpty())
                <div class="alert alert-info text-center" role="alert">
                  Belum ada data pemasukan di sampah
                </div>
              @elseif($incomes->isEmpty())
                <div class="alert alert-info text-center" role="alert">
                  Belum ada data pemasukan
                </div>
              @else
                <div class="table-wrapper table-responsive">
                  <table class="table" id="income-table">
                    <thead>
                      <tr>
                        <th>
                          <h6>No</h6>
                        </th>
                        <th>
                          <h6>Tanggal</h6>
                        </th>
                        <th>
                          <h6>Nama Pemasukan</h6>
                        </th>
                        <th>
                          <h6>Kategori</h6>
                        </th>
                        <th>
                          <h6>Dompet & Rekening</h6>
                        </th>
                        {{-- <th>
                          <h6>Jumlah</h6>
                        </th>
                        <th>
                          <h6>Exchange Rate</h6>
                        </th> --}}
                        <th>
                          <h6>Total</h6>
                        </th>
                        <th class="text-end">
                          <h6>Aksi</h6>
                        </th>
                      </tr>
                      <!-- end table row-->
                    </thead>
                    <tbody>
                      @forelse ($incomes as $income)
                      <tr>
                        <td>
                          <p>{{ $loop->iteration }}</p>
                        </td>
                        <td>
                          <p>{{ \Carbon\Carbon::parse($income->date)->translatedFormat('j F Y') }}</p>
                        </td>
                        <td>
                          <p>{{ $income->name }}</p>
                        </td>
                        <td>
                          <p>{{ $income->category->name }}</p>
                        </td>
                        <td>
                          <p>{{ $income->wallet->name }}  {{ $income->wallet->account_number ? ' - ' . $income->wallet->account_number : ''  }} {!! $income->wallet->status == 'active' ? '<span class="text-success">(Aktif)</span>' : '<span class="text-danger">(Nonaktif)</span>' !!} </p>
                        </td>
                        {{-- <td>
                          <p class="text-success">+ {{ number_format($income->amount, 0) }} {{ $income->wallet->currency }}</p>
                        </td>
                        <td>
                          <p class="text-success"> {{ number_format($income->exchange_rate, 0) }} IDR</p>
                        </td> --}}
                        @if($income->exchange_rate != null)
                        <td>
                          <p class="text-success">+ {{ number_format($income->amount, 0) }} USD (≈ {{ number_format($income->amount * $income->exchange_rate, 0) }} IDR)</p>
                          <small class="text-muted">Kurs {{ number_format($income->exchange_rate, 0) }} IDR</small>
                        </td>
                        @else
                        <td>
                          <p class="text-success">+ {{ number_format($income->amount, 0) }} IDR</p>
                        </td>
                        @endif
                        <td>
                          <div class="action justify-content-end">
                            @if(request('status') == 'inactive')
                            <form action="{{ route('income.toggle', ['income' => $income->id]) }}" method="POST" style="display: inline;">
                              @csrf
                              @method('PUT')
                              <button type="submit" class="btn-success">
                                  <i class="lni lni-spinner-arrow text-dark"></i>
                              </button>
                            </form>
                            @else
                            <a href="{{ route('income.edit', ['income' => $income->id]) }}" class="text-dark me-2">
                              <i class="lni lni-pencil"></i>
                            </a>
                            @endif
                            @if(request('status') == 'inactive')
                            <form id="delete-form-{{ $income->id }}" action="{{ route('income.destroy', ['income' => $income->id]) }}" method="POST" style="display: inline;">
                              @csrf
                              @method('DELETE')
                              <button type="button" class="btn-delete" data-id="{{ $income->id }}">
                                <i class="lni lni-trash-can text-danger"></i>
                              </button>
                            </form>
                            @else
                            <form id="delete-form-{{ $income->id }}" action="{{ route('income.destroy', ['income' => $income->id]) }}" method="POST" style="display: inline;">
                              @csrf
                              @method('DELETE')
                              <button type="button" class="btn-delete" data-id="{{ $income->id }}">
                                  <i class="lni lni-trash-can text-danger"></i>
                              </button>
                            </form>
                            @endif
                            
                          </div>
                        </td>
                      </tr>
                      @empty
                      <tr>
                        @if(request('status') == 'inactive')
                          <td colspan="8" class="text-medium text-center">Belum Ada Data Pemasukan di Sampah</td>
                        @else
                          <td colspan="8" class="text-medium text-center">Belum Ada Data Pemasukan</td>
                        @endif
                      </tr>
                      @endforelse
                      <!-- end table row -->
                    </tbody>
                  </table>
                  <!-- end table -->
                </div>
              @endif
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