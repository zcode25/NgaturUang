@extends('layouts.app')

@section('content')
<section class="section">
<div class="container-fluid">

    <!-- Judul -->
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title">
                    <h2>Halaman Utama</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end mb-2">
                    <button class="main-btn light-btn btn-sm" id="toggle-visibility">
                        Tampilkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Dropdown Bulan -->

    
    

    <!-- Kartu Ringkasan -->
   <div class="row">
        <div class="col-xl-3 col-lg-6 col-sm-6">
            <div class="icon-card mb-30">
                <div class="icon purple">
                    <i class="lni lni-dollar"></i>
                </div>
                <div class="content">
                    <h6 class="mb-10">Total Saldo</h6>
                    <h3 class="text-bold mb-10"><span class="secure" data-value="{{ number_format($totalBalance, 0) }}">******</span> IDR</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-sm-6">
            <div class="icon-card mb-30">
                <div class="icon success">
                    <i class="lni lni-dollar"></i>
                </div>
                <div class="content">
                    <h6 class="mb-10">Total Pemasukan</h6>
                    <h3 class="text-bold mb-10"><span class="secure" data-value="{{ number_format($totalIncome, 0) }}">******</span> IDR</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-sm-6">
            <div class="icon-card mb-30">
                <div class="icon orange">
                    <i class="lni lni-dollar"></i>
                </div>
                <div class="content">
                    <h6 class="mb-10">Total Pengeluaran</h6>
                    <h3 class="text-bold mb-10"><span class="secure" data-value="{{ number_format($totalExpense, 0) }}">******</span> IDR</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-sm-6">
            <div class="icon-card mb-30">
                <div class="icon primary">
                    <i class="lni lni-dollar"></i>
                </div>
                <div class="content">
                    <h6 class="mb-10">Total Selisih</h6>
                    <h3 class="text-bold mb-10 {{ $totalSelisih < 0 ? 'text-danger' : 'text-success' }}">
                        <span class="secure" data-value="{{ number_format($totalSelisih, 0) }}">******</span> IDR
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card-style">
                <div class="title d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <h6 class="text-medium m-0">Filter Bulan & Tahun</h6>
                    <form method="GET" action="{{ route('home') }}" class="d-flex align-items-center">
                        <div class="select-style-1 mb-0">
                            <div class="select-position">
                                <select name="month" id="month" onchange="this.form.submit()" class="form-select">
                                    @if ($availableMonths->count() > 0)
                                        @foreach ($availableMonths as $item)
                                            @php
                                                $formattedValue = $item->tahun . '-' . str_pad($item->bulan, 2, '0', STR_PAD_LEFT);
                                                $tanggal = $formattedValue . '-01';
                                            @endphp
                                            <option value="{{ $formattedValue }}"
                                                {{ $selectedMonth == $formattedValue ? 'selected' : '' }}>
                                                {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('F Y') }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="">Belum ada data</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card-style mb-30">
                <div class="title">
                    <h6 class="text-medium mb-10">Diagram Pemasukan</h6>
                </div>
                <div class="chart">
                    <canvas id="Chart1" width="100%" height="400"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card-style mb-30">
                <div class="title">
                    <h6 class="text-medium mb-10">Diagram Pengeluaran</h6>
                </div>
                <div class="chart">
                    <canvas id="Chart2" width="100%" height="400"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Kategori -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card-style mb-30">
                <div class="title mb-3">
                    <h6 class="text-medium mb-30">Tabel Kategori Pemasukan</h6>
                </div>
                @if ($category_incomes->isEmpty())
                    <div class="alert alert-info text-center" role="alert">
                        Tidak ada data kategori pemasukan
                    </div>
                @else
                    <div class="table-wrapper table-responsive">
                        <table class="table" id="category-income-table">
                            <thead>
                                <tr>
                                    <th><h6>No</h6></th>
                                    <th><h6>Nama Kategori</h6></th>
                                    <th><h6>Total</h6></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($category_incomes as $category_income)
                                    <tr>
                                        <td><p>{{ $loop->iteration }}</p></td>
                                        <td><p>{{ $category_income->category->name ?? '-' }}</p></td>
                                        <td><p>{{ number_format($category_income->total, 0) }} IDR</p></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
                
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card-style mb-30">
                <div class="title mb-3">
                    <h6 class="text-medium mb-30">Tabel Kategori Pengeluaran</h6>
                </div>
                @if ($category_expenses->isEmpty())
                    <div class="alert alert-info text-center" role="alert">
                        Tidak ada data kategori pengeluaran
                    </div>
                @else
                    <div class="table-wrapper table-responsive">
                        <table class="table" id="category-expense-table">
                            <thead>
                                <tr>
                                    <th><h6>No</h6></th>
                                    <th><h6>Nama Kategori</h6></th>
                                    <th><h6>Total</h6></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($category_expenses as $category_expense)
                                    <tr>
                                        <td><p>{{ $loop->iteration ?? '-' }}</p></td>
                                        <td><p>{{ $category_expense->category->name ?? '-'}}</p></td>
                                        <td><p>{{ number_format($category_expense->total, 0) ?? '-' }} IDR</p></td>
                                    </tr>
                                @empty
                                    <tr>
                                    <td colspan="3" class="text-center">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
</section>
@endsection

@section('script')
<script>
    const labels_income = {!! json_encode($labels_income) !!};
    const chartData_income = {
        labels: labels_income,
        datasets: [{
            label: '{{ $selectedMonth }}',
            backgroundColor: 'transparent',
            borderColor: '#365CF5',
            data: {!! json_encode($data_income) !!},
            pointBackgroundColor: 'transparent',
            pointHoverBackgroundColor: '#365CF5',
            pointBorderColor: 'transparent',
            pointHoverBorderColor: '#fff',
            pointHoverBorderWidth: 5,
            borderWidth: 5,
            pointRadius: 8,
            pointHoverRadius: 8,
            cubicInterpolationMode: 'monotone',
            fill: false
        }]
    };

    const chartConfig_income = {
        type: 'line',
        data: chartData_income,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        labelColor: () => ({ backgroundColor: "#ffffff", color: "#171717" })
                    },
                    intersect: false,
                    backgroundColor: "#f9f9f9",
                    titleColor: "#8F92A1",
                    bodyColor: "#171717",
                    bodyFont: { family: "Plus Jakarta Sans", size: 16, weight: 'bold' },
                    titleFont: { family: "Plus Jakarta Sans", size: 12 },
                    displayColors: false,
                    padding: { x: 30, y: 10 },
                    bodyAlign: "center",
                    titleAlign: "center"
                },
                legend: { display: false }
            },
            scales: {
                y: {
                    grid: { display: false, drawTicks: false, drawBorder: false },
                    ticks: { padding: 35 }
                },
                x: {
                    grid: {
                        drawBorder: false,
                        color: "rgba(143, 146, 161, .1)",
                        zeroLineColor: "rgba(143, 146, 161, .1)"
                    },
                    ticks: { padding: 20 }
                }
            }
        }
    };

    const myChart1 = new Chart(document.getElementById("Chart1").getContext("2d"), chartConfig_income);

    const labels_expense = {!! json_encode($labels_expense) !!};
    const chartData_expense = {
        labels: labels_expense,
        datasets: [{
            label: '{{ $selectedMonth }}',
            backgroundColor: 'transparent',
            borderColor: '#dc3545',
            data: {!! json_encode($data_expense) !!},
            pointBackgroundColor: 'transparent',
            pointHoverBackgroundColor: '#dc3545',
            pointBorderColor: 'transparent',
            pointHoverBorderColor: '#fff',
            pointHoverBorderWidth: 5,
            borderWidth: 5,
            pointRadius: 8,
            pointHoverRadius: 8,
            cubicInterpolationMode: 'monotone',
            fill: false
        }]
    };

    const chartConfig_expense = {
        type: 'line',
        data: chartData_expense,
        options: chartConfig_income.options
    };

    const myChart2 = new Chart(document.getElementById("Chart2").getContext("2d"), chartConfig_expense);

    let isVisible = false;
    document.getElementById('toggle-visibility').addEventListener('click', function () {
        isVisible = !isVisible;
        document.querySelectorAll('.secure').forEach(el => {
            el.textContent = isVisible ? el.dataset.value : '******';
        });

        // const icon = document.getElementById('visibility-icon');
        // icon.classList.toggle('lni-eye');
        // icon.classList.toggle('lni-eye-crossed');
        this.innerHTML = `${isVisible ? 'Sembunyikan' : 'Tampilkan'}`;
    });

</script>
@endsection
