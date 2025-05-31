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
                <h2>Detail Anggaran</h2>
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
                <div class="row">
                  <div class="col-lg-3">
                    <p class="mb-3">Categori</p>
                    <h6 class="text-medium mb-30">{{ $budgetDetail->category->name }}</h6>
                  </div>
                  <div class="col-lg-3">
                    <p class="mb-3">Jumlah</p>
                    <h6 class="text-medium mb-30">Rp {{ number_format($budgetDetail->amount, 0) }}</h6>
                  </div>
                  <div class="col-lg-3">
                    <p class="mb-3">Jumlah Pengeluaran</p>
                    <h6 class="text-medium mb-30">Rp {{ number_format($budgetDetail->category->expenses->sum('amount'), 0) }}</h6>
                  </div>
                  @php
                      $totalExpense = $budgetDetail->category->expenses->sum('amount');
                      $remaining = $budgetDetail->amount - $totalExpense;
                  @endphp
                  <div class="col-lg-3">
                    <p class="mb-3">Tanggal Akhir</p>
                    <h6 class="text-medium mb-30 {{ $remaining < 0 ? 'text-danger' : 'text-success' }}">Rp {{ number_format($remaining, 0) }}</h6>
                  </div>
                </div>
              </div>
          </div>
        <!-- End Col -->
        </div>
        <!-- End Row -->
        <div class="row">
          <div class="col-lg-12">
                <div class="card-style mb-30">
                    <div class="title d-flex flex-wrap align-items-center justify-content-between mb-3">
                      <div class="left">
                        <h6 class="text-medium mb-30">Tabel Detail Anggaran</h6>
                      </div>
                    </div>
                    <div class="table-wrapper table-responsive">
                      <table class="table">
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
                          </tr>
                          <!-- end table row-->
                        </thead>
                        <tbody>
                          @forelse ($expenses as $expense)
                          <tr>
                            <td>
                              <p>{{ $expense->date }}</p>
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
                              <p class="text-danger">+ {{ number_format($expense->amount, 0) }} {{ $expense->wallet->currency }}</p>
                            </td>
                            <td>
                              <p class="text-danger"> {{ number_format($expense->exchange_rate, 0) }} IDR</p>
                            </td>
                            @if($expense->exchange_rate != null)
                            <td>
                              <p class="text-danger">+ {{ number_format($expense->amount * $expense->exchange_rate, 0) }} IDR</p>
                            </td>
                            @else
                            <td>
                              <p class="text-danger">+ {{ number_format($expense->amount, 0) }} IDR</p>
                            </td>
                            @endif
                          </tr>
                          @empty
                          <tr>
                              <td colspan="8" class="text-medium text-center">Belum Ada Data Pemasukan</td>
                          </tr>
                          @endforelse
                          <!-- end table row -->
                        </tbody>
                      </table>
                      <!-- end table -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end container -->
    </section>
    <!-- ========== section end ========== -->

@endsection