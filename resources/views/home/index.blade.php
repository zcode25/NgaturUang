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
                <h2>Halaman Utama</h2>
            </div>
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
        </div>
        <!-- ========== title-wrapper end ========== -->
        <div class="row">
        <div class="col-xl-3 col-lg-6 col-sm-6">
            <div class="icon-card mb-30">
            <div class="icon purple">
                <i class="lni lni-dollar"></i>
            </div>
            <div class="content">
                <h6 class="mb-10">Total Saldo</h6>
                <h3 class="text-bold mb-10">{{ number_format($totalBalance, 0) }} IDR</h3>
            </div>
            </div>
            <!-- End Icon Cart -->
        </div>
        <!-- End Col -->
        <div class="col-xl-3 col-lg-6 col-sm-6">
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
        <div class="col-xl-3 col-lg-6 col-sm-6">
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
        <div class="col-xl-3 col-lg-6 col-sm-6">
            <div class="icon-card mb-30">
            <div class="icon primary">
                <i class="lni lni-dollar"></i>
            </div>
            <div class="content">
                <h6 class="mb-10">Total Selisih</h6>
                <h3 class="text-bold mb-10 {{ ($totalIncome - $totalExpense) < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($totalIncome - $totalExpense, 0) }} IDR</h3>
            </div>
            </div>
            <!-- End Icon Cart -->
        </div>
        <!-- End Col -->
        </div>
        <!-- End Row -->
        <div class="row">
        <div class="col-lg-6">
            <div class="card-style mb-30">
            <div class="title d-flex flex-wrap justify-content-between">
                <div class="left">
                <h6 class="text-medium mb-10">Diagram Pemasukan</h6>
                </div>
                <div class="right">
                    
                <form method="GET" action="{{ route('home') }}">
                    <div class="select-style-1">
                    <div class="select-position">
                    <select name="date_income" onchange="this.form.submit()">
                        @foreach ($date_income as $item)
                            <option value="{{ $item->tahun }}-{{ str_pad($item->bulan, 2, '0', STR_PAD_LEFT) }}"
                                {{ $selectedDate_income == $item->tanggal ? 'selected' : '' }}>
                                {{ $item->tanggal }}
                            </option>
                        @endforeach
                    </select>
                    </div>
                    </div>
                </form>
                <!-- end select -->
                </div>
            </div>
            <!-- End Title -->
            <div class="chart">
                <canvas id="Chart1" width="100%" height="400"></canvas>
            </div>
            <!-- End Chart -->
            </div>
        </div>
        <!-- End Col -->
        <div class="col-lg-6">
            <div class="card-style mb-30">
            <div class="title d-flex flex-wrap justify-content-between">
                <div class="left">
                <h6 class="text-medium mb-10">Diagram Pengeluaran</h6>
                </div>
                <div class="right">
                    
                <form method="GET" action="{{ route('home') }}">
                    <div class="select-style-1">
                    <div class="select-position">
                    <select name="date_expense" onchange="this.form.submit()">
                        @foreach ($date_expense as $item)
                            <option value="{{ $item->tahun }}-{{ str_pad($item->bulan, 2, '0', STR_PAD_LEFT) }}"
                                {{ $selectedDate_expense == $item->tanggal ? 'selected' : '' }}>
                                {{ $item->tanggal }}
                            </option>
                        @endforeach
                    </select>
                    </div>
                    </div>
                </form>
                
                <!-- end select -->
                </div>
            </div>
            <!-- End Title -->
            <div class="chart">
                <canvas id="Chart2" width="100%" height="400"></canvas>
            </div>
            <!-- End Chart -->
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

@section('script')
    <script>
    const labels_income = {!! json_encode($labels_income) !!};
    const chartData_income = {
        labels: labels_income,
        datasets: [{
            label: '{{ $selectedDate_income }}',
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
                        labelColor: function(context) {
                            return {
                                backgroundColor: "#ffffff",
                                color: "#171717"
                            };
                        }
                    },
                    intersect: false,
                    backgroundColor: "#f9f9f9",
                    titleColor: "#8F92A1",
                    bodyColor: "#171717",
                    bodyFont: {
                        family: "Plus Jakarta Sans",
                        size: 16,
                        weight: 'bold'
                    },
                    titleFont: {
                        family: "Plus Jakarta Sans",
                        size: 12
                    },
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

    const myChart1 = new Chart(
        document.getElementById("Chart1").getContext("2d"),
        chartConfig_income
    );
</script>

<script>
    const labels_expense = {!! json_encode($labels_expense) !!};
    const chartData_expense = {
        labels: labels_expense,
        datasets: [{
            label: '{{ $selectedDate_expense }}',
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
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        labelColor: function(context) {
                            return {
                                backgroundColor: "#ffffff",
                                color: "#171717"
                            };
                        }
                    },
                    intersect: false,
                    backgroundColor: "#f9f9f9",
                    titleColor: "#8F92A1",
                    bodyColor: "#171717",
                    bodyFont: {
                        family: "Plus Jakarta Sans",
                        size: 16,
                        weight: 'bold'
                    },
                    titleFont: {
                        family: "Plus Jakarta Sans",
                        size: 12
                    },
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

    const myChart2 = new Chart(
        document.getElementById("Chart2").getContext("2d"),
        chartConfig_expense
    );
</script>

@endsection