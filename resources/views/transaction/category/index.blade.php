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
                        <h2>Kategori Transaksi</h2>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-md-end mb-2">
                        <a href="{{ route('category.create') }}" class="main-btn primary-btn btn-sm">Tambah Data <i class="lni lni-plus ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <!-- ========== title-wrapper end ========== -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card-style mb-30">
                    <div class="title d-flex flex-wrap align-items-center justify-content-between mb-3">
                      <div class="left">
                        <h6 class="text-medium mb-30">Tabel Kategori Transaksi</h6>
                      </div>
                    </div>
                    @if ($categories->isEmpty())
                      <div class="alert alert-info text-center" role="alert">
                          Tidak ada data kategori transaksi
                      </div>
                    @else
                      <div class="table-wrapper table-responsive">
                        <table class="table" id="category-table">
                          <thead>
                            <tr>
                              <th>
                                <h6>No</h6>
                              </th>
                              <th>
                                <h6>Nama Kategori</h6>
                              </th>
                              <th>
                                <h6>Deskripsi</h6>
                              </th>
                              <th class="text-end">
                                <h6>Aksi</h6>
                              </th>
                            </tr>
                            <!-- end table row-->
                          </thead>
                          <tbody>
                            @forelse ($categories as $category)
                            <tr>
                              <td>
                                <p>{{ $loop->iteration }}</p>
                              </td>
                              <td>
                                <p>{{ $category->name }}</p>
                              </td>
                              <td>
                                <p>{{ $category->description }}</p>
                              </td>
                              <td>
                                <div class="action justify-content-end">
                                  <a href="{{ route('category.edit', ['category' => $category->id ]) }}" class="text-dark me-2">
                                    <i class="lni lni-pencil"></i>
                                  </a>
                                  <form id="delete-form-{{ $category->id }}" action="{{ route('category.destroy', $category->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn-delete" data-id="{{ $category->id }}">
                                        <i class="lni lni-trash-can text-danger"></i>
                                    </button>
                                  </form>
                                  
                                </div>
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