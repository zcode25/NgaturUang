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
                <h2>Anggaran</h2>
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
                    <div class="title d-flex flex-wrap align-items-center justify-content-between mb-3">
                      <div class="left">
                        <h6 class="text-medium mb-30">Tabel Anggaran</h6>
                      </div>
                      <div class="right">
                        <a href="{{ route('budget.create') }}" class="main-btn primary-btn btn-sm">Tambah Data <i class="lni lni-plus ms-1"></i></a>
                      </div>
                    </div>
                    @if ($budgets->isEmpty())
                      <div class="alert alert-info text-center" role="alert">
                          Tidak ada data kategori anggaran
                      </div>
                    @else
                      <div class="table-wrapper table-responsive">
                        <table class="table" id="budget-table">
                          <thead>
                            <tr>
                              <th>
                                <h6>No</h6>
                              </th>
                              <th>
                                <h6>Nama Budget</h6>
                              </th>
                              <th>
                                <h6>Tanggal Mulai</h6>
                              </th>
                              <th>
                                <h6>Tanggal Akhir</h6>
                              </th>
                              <th class="text-end">
                                <h6>Aksi</h6>
                              </th>
                            </tr>
                            <!-- end table row-->
                          </thead>
                          <tbody>
                            @forelse ($budgets as $budget)
                              <tr>
                                <td>
                                  <p>{{ $loop->iteration }}</p>
                                </td>
                                <td>
                                  <p>{{ $budget->name }}</p>
                                </td>
                                <td>
                                  <p>{{ \Carbon\Carbon::parse($budget->start_date)->translatedFormat('j F Y') }}</p>
                                </td>
                                <td>
                                  <p>{{ \Carbon\Carbon::parse($budget->end_date)->translatedFormat('j F Y') }}</p>
                                </td>
                                <td class="action justify-content-end">
                                  <a href="{{ route('budget.edit', ['budget' => $budget->id ]) }}" class="text-dark me-2">
                                    <i class="lni lni-pencil"></i>
                                  </a>
                                  <a href="{{ route('budget.detail', ['budget' => $budget->id ]) }}" class="text-dark me-2">
                                    <i class="lni lni-search-alt"></i>
                                  </a>
                                </td>
                              </tr>
                            @empty
                              <tr class="text-center">
                                <td colspan="3">Belum Ada Data Kategori</td>
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