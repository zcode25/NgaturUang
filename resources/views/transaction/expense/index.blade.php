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
                <h2>Pengeluaran</h2>
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
                <h6 class="mb-10">Pengeluaran Hari Ini</h6>
                <h3 class="text-bold mb-10">{{ number_format($dailyExpense, 0) }} IDR</h3>
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
                <h6 class="mb-10">Pengeluaran Bulan Ini</h6>
                <h3 class="text-bold mb-10">{{ number_format($monthlyExpense, 0) }} IDR</h3>
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
                <h6 class="mb-10">Pengeluaran Tahun Ini</h6>
                <h3 class="text-bold mb-10">{{ number_format($yearlyExpense, 0) }} IDR</h3>
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
                <h6 class="mb-10">Total Pengeluaran</h6>
                <h3 class="text-bold mb-10">{{ number_format($totalExpense, 0) }} IDR</h3>
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
                  <h6 class="text-medium mb-30">Tabel Pengeluaran</h6>
                </div>
                <div class="right">
                  <a href="{{ route('expense.create') }}" class="main-btn primary-btn btn-sm">Tambah Data <i class="lni lni-plus ms-1"></i></a>
                  @if(request('status') == 'inactive')
                    <a href="{{ route('expense') }}" class="main-btn dark-btn btn-sm">Kembali</a>
                  @else
                    <a href="{{ route('expense', ['status' => 'inactive']) }}" class="main-btn danger-btn btn-sm">Sampah <i class="lni lni-trash-can ms-1"></i></a>
                  @endif
                  
                </div>
              </div>
              @if ($expenses->isEmpty())
                <div class="alert alert-info text-center" role="alert">
                    Tidak ada data kategori pengeluaran
                </div>
              @else
                <div class="table-wrapper table-responsive">
                  <table class="table" id="expense-table">
                    <thead>
                      <tr>
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
                        <th>
                          <h6>Jumlah</h6>
                        </th>
                        <th>
                          <h6>Exchange Rate</h6>
                        </th>
                        <th>
                          <h6>Total</h6>
                        </th>
                        <th class="text-end">
                          <h6>Action</h6>
                        </th>
                      </tr>
                      <!-- end table row-->
                    </thead>
                    <tbody>
                      @forelse ($expenses as $expense)
                      <tr>
                        <td>
                          <p>{{ \Carbon\Carbon::parse($expense->date)->translatedFormat('j F Y') }}</p>
                        </td>
                        <td>
                          <p>{{ $expense->name }}</p>
                        </td>
                        <td>
                          <p>{{ $expense->category->name }}</p>
                        </td>
                        <td>
                          <p>{{ $expense->wallet->name }}  {{ $expense->wallet->account_number ? ' - ' . $expense->wallet->account_number : ''  }} {!! $expense->wallet->status == 'active' ? '<span class="text-success">(Aktif)</span>' : '<span class="text-danger">(Nonaktif)</span>' !!} </p>
                        </td>
                        <td>
                          <p class="text-danger">- {{ number_format($expense->amount, 0) }} {{ $expense->wallet->currency }}</p>
                        </td>
                        <td>
                          <p class="text-danger"> {{ number_format($expense->exchange_rate, 0) }} IDR</p>
                        </td>
                        @if($expense->exchange_rate != null)
                        <td>
                          <p class="text-danger">- {{ number_format($expense->amount * $expense->exchange_rate, 0) }} IDR</p>
                        </td>
                        @else
                        <td>
                          <p class="text-danger">- {{ number_format($expense->amount, 0) }} IDR</p>
                        </td>
                        @endif
                        <td>
                          <div class="action justify-content-end">
                            @if(request('status') == 'inactive')
                            <form action="{{ route('expense.toggle', ['expense' => $expense->id]) }}" method="POST" style="display: inline;">
                              @csrf
                              @method('PUT')
                              <button type="submit" class="btn-success">
                                  <i class="lni lni-spinner-arrow text-dark"></i>
                              </button>
                            </form>
                            @else
                            <a href="{{ route('expense.edit', ['expense' => $expense->id]) }}" class="text-dark me-2">
                              <i class="lni lni-pencil"></i>
                            </a>
                            @endif
                            @if(request('status') == 'inactive')
                            <form id="delete-form-{{ $expense->id }}" action="{{ route('expense.destroy', ['expense' => $expense->id]) }}" method="POST" style="display: inline;">
                              @csrf
                              @method('DELETE')
                              <button type="button" class="btn-delete" data-id="{{ $expense->id }}">
                                <i class="lni lni-trash-can text-danger"></i>
                              </button>
                            </form>
                            @else
                            <form id="delete-form-{{ $expense->id }}" action="{{ route('expense.destroy', ['expense' => $expense->id]) }}" method="POST" style="display: inline;">
                              @csrf
                              @method('DELETE')
                              <button type="button" class="btn-delete" data-id="{{ $expense->id }}">
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