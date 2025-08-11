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
                <h2>Laporan E-Statement</h2>
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
              {{-- FORM FILTER --}}
              <form method="GET" action="{{ route('statement') }}">
                  <div class="row mb-3 align-items-end">
                      <div class="col-xl-4 col-lg-6 col-sm-12">
                          <div class="input-style-1">
                              <label for="start_date">Dari Tanggal: <span class="text-danger">*</span></label>
                              <input type="date" id="start_date" name="start_date" value="{{ $startDate }}">
                          </div>
                      </div>

                      <div class="col-xl-4 col-lg-6 col-sm-12">
                          <div class="input-style-1">
                              <label for="end_date">Sampai Tanggal: <span class="text-danger">*</span></label>
                              <input type="date" id="end_date" name="end_date" value="{{ $endDate }}">
                          </div>
                      </div>

                      <div class="col-xl-4 col-lg-6 col-sm-12">
                        <div class="input-style-1">
                            <label>&nbsp;</label>
                            <button type="submit" class="main-btn primary-btn w-100">Filter</button>
                        </div>
                      </div>
                  </div>
              </form>

              <div class="row mb-3">
                  <div class="col-xl-12 d-flex gap-2">
                      <form method="GET" action="{{ route('statement.export.excel') }}">
                          <input type="hidden" name="start_date" value="{{ $startDate }}">
                          <input type="hidden" name="end_date" value="{{ $endDate }}">
                          <button class="main-btn success-btn">Export Excel</button>
                      </form>

                      <form method="GET" action="{{ route('statement.export.pdf') }}" target="_blank">
                          <input type="hidden" name="start_date" value="{{ $startDate }}">
                          <input type="hidden" name="end_date" value="{{ $endDate }}">
                          <button class="main-btn danger-btn">Export PDF</button>
                      </form>
                  </div>
              </div>
          </div>


        </div>
        <!-- End Col -->
        </div>
        <!-- End Row -->
        <div class="row">
          <div class="col-lg-12">
            @foreach ($statements as $statement)
            <div class="card-style mb-30">
              <div class="card-header text-center mt-3 mb-4">
                <h3><strong>{{ $statement['wallet']->name }}</strong></h3>
              </div>
              <div class="card-body">
                  <div class="row">
                    <div class="col-md-3">
                      <div class="card text-center">
                        <div class="card-body">
                          <p class="card-title mb-1">Saldo Awal</p>
                          <h5 class="card-text">{{ number_format($statement['saldo_awal'], 0) }} IDR</h5>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="card text-center">
                        <div class="card-body">
                          <p class="card-title mb-1">Pemasukan</p>
                          <h5 class="card-text text-success">+ {{ number_format($statement['total_income'], 0) }} IDR</h5>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="card text-center">
                        <div class="card-body">
                          <p class="card-title mb-1">Pengeluaran</p>
                          <h5 class="card-text text-dark">- {{ number_format($statement['total_expense'], 0) }} IDR</h5>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="card text-center">
                        <div class="card-body">
                          <p class="card-title mb-1">Saldo Akhir</p>
                          <h5 class="card-text">{{ number_format($statement['saldo_akhir'], 0) }} IDR</h5>
                        </div>
                      </div>
                    </div>
                  </div>
                  <hr>
                  <div class="table-wrapper table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Rincian Transaksi</th>
                                <th>Tipe Transaksi</th>
                                <th class="text-end">Nominal</th>
                                <th class="text-end">Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                              <td colspan="4"><strong><em>Saldo Awal</em></strong></td>
                              <td class="text-end"><strong>{{ number_format($statement['saldo_awal'], 0) }} IDR</strong></td>
                            </tr>
                            @forelse ($statement['transactions'] as $trx)
                                <tr>
                                  <td>{{ \Carbon\Carbon::parse($trx->date)->format('d F Y') }}</td>
                                  <td>{{ $trx->description }}</td>                                  
                                  <td>{{ $trx->category }}</td>
                                  <td class="text-end {{ $trx->type === 'income' ? 'text-success' : 'text-dark' }}">
                                    {{ $trx->formatted_nominal }}
                                  </td>
                                  <td class="text-end"><strong>{{ $trx->formatted_balance }}</strong></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada transaksi</td>
                                </tr>
                            @endforelse
                            <tr>
                              <td colspan="4"><strong><em>Saldo Akhir</em></strong></td>
                              <td class="text-end"><strong>{{ number_format($statement['saldo_akhir'], 0) }} IDR</strong></td>
                            </tr>
                        </tbody>
                    </table>
                  </div>
              </div>
            </div>
            @endforeach
          </div>
        </div>
    </div>
    <!-- end container -->
    </section>
    <!-- ========== section end ========== -->

@endsection