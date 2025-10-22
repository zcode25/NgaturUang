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
                        <h2>Transaksi</h2>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-md-end mb-2">
                        <a href="{{ route('trans.create') }}" class="main-btn primary-btn btn-sm">Tambah Data<i class="lni lni-plus ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <!-- ========== title-wrapper end ========== -->
        <div class="row">
          <div class="col-xl-4 col-lg-4 col-sm-12">
              <div class="icon-card mb-30">
              <div class="icon success">
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
          <div class="col-xl-4 col-lg-4 col-sm-12">
              <div class="icon-card mb-30">
              <div class="icon orange">
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
          <div class="col-xl-4 col-lg-4 col-sm-12">
              <div class="icon-card mb-30">
              <div class="icon primary">
                  <i class="lni lni-dollar"></i>
              </div>
              <div class="content">
                  <h6 class="mb-10">Total Selisih</h6>
                  <h3 class="text-bold mb-10 {{ $selisih < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($selisih, 0) }} IDR</h3>
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
                  <h6 class="text-medium mb-30">Tabel Transaksi</h6>
                </div>
              </div>
              @if($transactions->isEmpty())
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
                        <th>
                          <h6>Tipe</h6>
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
                      @forelse ($transactions as $transaction)
                      <tr>
                        <td>
                          <p>{{ $loop->iteration }}</p>
                        </td>
                        <td>
                          <p>{{ \Carbon\Carbon::parse($transaction->date)->translatedFormat('j F Y') }}</p>
                        </td>
                        <td>
                          <p>{{ $transaction->name }}</p>
                        </td>
                        <td>
                          <p>{{ $transaction->category->name }}</p>
                        </td>
                        <td>
                          <p>{{ $transaction->wallet->name }}  {{ $transaction->wallet->account_number ? ' - ' . $transaction->wallet->account_number : ''  }} {!! $transaction->wallet->status == 'active' ? '<span class="text-success">(Aktif)</span>' : '<span class="text-danger">(Nonaktif)</span>' !!} </p>
                        </td>
                        <td>
                          @if ($transaction->type == "income")
                            <span class="badge bg-success">Pemasukan</span>
                          @elseif ($transaction->type == "expense")
                            <span class="badge bg-danger">Pengeluaran</span>
                          @endif
                        </td>
                        {{-- <td>
                          <p class="text-success">+ {{ number_format($transaction->amount, 0) }} {{ $transaction->wallet->currency }}</p>
                        </td>
                        <td>
                          <p class="text-success"> {{ number_format($transaction->exchange_rate, 0) }} IDR</p>
                        </td> --}}
                        @if($transaction->exchange_rate != null)
                        <td>
                          @if ($transaction->type == "income")
                          <p class="text-success">+ {{ number_format($transaction->amount, 0) }} USD (≈ {{ number_format($transaction->amount * $transaction->exchange_rate, 0) }} IDR)</p>
                          <small class="text-muted">Kurs {{ number_format($transaction->exchange_rate, 0) }} IDR</small>
                          @elseif ($transaction->type == "expense")
                          <p class="text-danger">- {{ number_format($transaction->amount, 0) }} USD (≈ {{ number_format($transaction->amount * $transaction->exchange_rate, 0) }} IDR)</p>
                          <small class="text-muted">Kurs {{ number_format($transaction->exchange_rate, 0) }} IDR</small>
                          @endif
                        </td>
                        @else
                        <td>
                          @if ($transaction->type == "income")
                          <p class="text-success">+ {{ number_format($transaction->amount, 0) }} IDR</p>
                          @elseif ($transaction->type == "expense")
                          <p class="text-danger">- {{ number_format($transaction->amount, 0) }} IDR</p>
                          @endif
                        </td>
                        @endif
                        <td>
                          <div class="action justify-content-end">
                            
                            <a href="{{ route('trans.edit', ['transaction' => $transaction->id]) }}" class="text-dark me-2">
                              <i class="lni lni-pencil"></i>
                            </a>
                            
                            <form id="delete-form-{{ $transaction->id }}" action="{{ route('trans.destroy', ['transaction' => $transaction->id]) }}" method="POST" style="display: inline;">
                              @csrf
                              @method('DELETE')
                              <button type="button" class="btn-delete" data-id="{{ $transaction->id }}">
                                  <i class="lni lni-trash-can text-danger"></i>
                              </button>
                            </form>
                            
                          </div>
                        </td>
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