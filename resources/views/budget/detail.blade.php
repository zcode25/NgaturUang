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
                  <div class="col-lg-4">
                    <p class="mb-3">Nama</p>
                    <h6 class="text-medium mb-30">{{ $budget->name }}</h6>
                  </div>
                  <div class="col-lg-4">
                    <p class="mb-3">Tanggal Mulai</p>
                    <h6 class="text-medium mb-30">{{ \Carbon\Carbon::parse($budget->start_date)->translatedFormat('j F Y') }}</h6>
                  </div>
                  <div class="col-lg-4">
                    <p class="mb-3">Tanggal Akhir</p>
                    <h6 class="text-medium mb-30">{{ \Carbon\Carbon::parse($budget->end_date)->translatedFormat('j F Y') }}</h6>
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
                  <!-- <h6 class="mb-25">Tambah dompet & rekening</h6> -->
                  <form action="{{ isset($editDetail) ? route('budget.updateDetail', ['budget' => $budget->id, 'budgetDetail' => $editDetail->id]) : route('budget.storeDetail', $budget->id) }}" method="POST">
                    <div class="row">
                    @csrf
                    @if(isset($editDetail))
                        @method('PUT')
                    @endif

                    <!-- Category dropdown only shown on add -->
                    @if(!isset($editDetail))
                    <div class="col-lg-4">
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
                        </div>
                    </div>
                    @else
                    <div class="col-lg-4">
                        <div class="input-style-1">
                          <label for="category">Kategori <span class="text-danger">*</span></label>
                          <input type="text" id="category" name="category" placeholder="Masukan kategori" value="{{ $editDetail->category->name }}" disabled />
                        </div>
                          {{-- <p>{{ $editDetail->category->name }}</p> --}}
                    </div>
                    @endif

                    <!-- Amount input -->
                    <div class="col-lg-4">
                        <div class="input-style-1">
                            <label for="amount">Jumlah <span class="text-danger">*</span></label>
                            <input type="number" id="amount" name="amount" placeholder="Masukan jumlah" value="{{ old('amount', isset($editDetail) ? $editDetail->amount : '') }}" />
                        </div>
                    </div>

                    <!-- Submit button -->
                    <div class="col-4 d-flex flex-column justify-content-center">
                        <div class="button-group d-flex justify-content-start">
                            <button class="main-btn primary-btn btn-hover text-center w-100">
                                {{ isset($editDetail) ? 'Ubah' : 'Tambah' }}
                            </button>
                        </div>
                    </div>
                    </div>
                </form>

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
                    @if ($budgetDetails->isEmpty())
                      <div class="alert alert-info text-center" role="alert">
                          Tidak ada data detail anggaran
                      </div>
                    @else
                      <div class="table-wrapper table-responsive">
                        @php
                          $totalAmount = 0;
                          $totalRemaining = 0;
                          $totalExpenseAll = 0;
                        @endphp
                        <table class="table" id="detail-budget-table">
                          <thead>
                            <tr>
                              <th><h6>Nama Kategori</h6></th>
                              <th><h6>Jumlah</h6></th>
                              <th><h6>Jumlah Pengeluaran</h6></th>
                              <th><h6>Sisa Anggaran</h6></th>
                              <th class="text-end"><h6>Aksi</h6></th>
                            </tr>
                          </thead>
                          <tbody>
                            @forelse ($budgetDetails as $budgetDetail)
                              @php
                                  $totalExpense = $budgetDetail->category->expenses->sum('amount');
                                  $remaining = $budgetDetail->amount - $totalExpense;
                                  $totalAmount += $budgetDetail->amount;
                                  $totalExpenseAll += $totalExpense;
                                  $totalRemaining += $remaining;
                              @endphp
                              <tr>
                                <td><p>{{ $budgetDetail->category->name }}</p></td>
                                <td><p>{{ number_format($budgetDetail->amount, 0) }} IDR</p></td>
                                <td><p>{{ number_format($totalExpense, 0) }} IDR</p></td>
                                <td>
                                  <p class="{{ $remaining < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($remaining, 0) }} IDR
                                  </p>
                                </td>
                                <td class="action justify-content-end">
                                  <a href="{{ route('budget.detail', ['budget' => $budget->id, 'edit' => $budgetDetail->id]) }}" class="text-dark me-2">
                                      <i class="lni lni-pencil"></i>
                                  </a>
                                  <a href="{{ route('budget.budgetDetail', ['budgetDetail' => $budgetDetail->id ]) }}" class="text-dark me-2">
                                    <i class="lni lni-search-alt"></i>
                                  </a>
                                  <form id="delete-form-{{ $budgetDetail->id }}" action="{{ route('budget.budgetDestroy', $budgetDetail->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn-delete" data-id="{{ $budgetDetail->id }}">
                                        <i class="lni lni-trash-can text-danger"></i>
                                    </button>
                                  </form>
                                </td>
                              </tr>
                            @empty
                              <tr class="text-center">
                                <td colspan="5"><p>Belum Ada Data Kategori</p></td>
                              </tr>
                            @endforelse
                          </tbody>

                          @if(count($budgetDetails) > 0)
                          <tr><td colspan="5" style="height: 20px;"></td></tr>
                          <tfoot>
                            <tr class="mt-5">
                              <th><strong>Total</strong></th>
                              <th><strong>{{ number_format($totalAmount, 0) }} IDR</strong></th>
                              <th><strong>{{ number_format($totalExpenseAll, 0) }} IDR</strong></th>
                              <th>
                                <strong class="{{ $totalRemaining < 0 ? 'text-danger' : 'text-success' }}">
                                  {{ number_format($totalRemaining, 0) }} IDR
                                </strong>
                              </th>
                              <th></th>
                            </tr>
                          </tfoot>
                          @endif
                        </table>

                        <!-- end table -->
                      </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- end container -->
    </section>
    <!-- ========== section end ========== -->

@endsection