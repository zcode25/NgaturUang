<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan E-Statement</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
        }

        h2, h3, h4 {
            margin: 0;
            padding: 0;
        }

        .header-table {
            width: 100%;
            margin-bottom: 5px;
        }

        .header-table td {
            vertical-align: top;
        }

        .title-left h2 {
            color: #673DE5;
            margin-bottom: 3px;
        }

        .title-right {
            text-align: right;
        }

        .divider {
            height: 2px;
            background-color: #673DE5;
            margin: 10px 0;
        }

        .summary-table {
            width: 100%;
            background-color: #f3edff;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .summary-table td {
            padding: 5px;
            border: none;
            text-align: center;
        }

        .transaction-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .transaction-table th {
            border-top: 1px solid #999;
            border-bottom: 1px solid #999;
            padding: 8px;
            background-color: #f3edff;
        }

        .transaction-table thead th {
            border: none; /* hilangkan semua border */
            padding: 8px;
            background-color: #f3edff;
            font-weight: bold;
            text-align: left;
        }

        .transaction-table td {
            border-bottom: 1px solid #ddd;
            padding: 6px 8px;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-success { color: green; }
        .text-danger { color: red; }

        .section {
            page-break-after: always;
        }
    </style>
</head>
<body>

@foreach ($statements as $statement)
<div class="{{ !$loop->last ? 'section' : '' }}">
    <table class="header-table">
        <tr>
            <td class="title-left">
                <h2>NgaturUang</h2>
                <p>by terasweb.id</p>
            </td>
            <td class="title-right">
                <h3>Laporan E-Statement</h3>
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <div style="margin-top: 10px;">
        <h3 class="text-center">{{ $statement['wallet']->name }}</h3>
        <p class="text-center"><strong>Periode:</strong> {{ \Carbon\Carbon::parse($startDate)->translatedFormat('j F Y') }} s.d. {{ \Carbon\Carbon::parse($endDate)->translatedFormat('j F Y') }}</p>
    </div>

    <table class="summary-table">
        <tr>
            <td>Saldo Awal</td>
            <td>Pemasukan</td>
            <td>Pengeluaran</td>
            <td>Saldo Akhir</td>
        </tr>
        <tr>
            <td><strong>{{ number_format($statement['saldo_awal'], 0) }} IDR</strong></td>
            <td class="text-success"><strong>+ {{ number_format($statement['total_income'], 0) }} IDR</strong></td>
            <td><strong>- {{ number_format($statement['total_expense'], 0) }} IDR</strong></td>
            <td><strong>{{ number_format($statement['saldo_akhir'], 0) }} IDR</strong></td>
        </tr>
    </table>

    {{-- <h4 class="text-center" style="margin-top: 20px;">Detail Transaksi</h4> --}}

    <table class="transaction-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Rincian Transaksi</th>
                <th>Kategori</th>
                <th style="text-align: right;">Nominal</th>
                <th style="text-align: right;">Saldo</th>
            </tr>
        </thead>
        <tbody>
            <tr>
              <td colspan="4"><strong><em>Saldo Awal</em></strong></td>
              <td class="text-right"><strong>{{ number_format($statement['saldo_awal'], 0) }} IDR</strong></td>
            </tr>
            @forelse ($statement['transactions'] as $trx)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($trx->date)->translatedFormat('j F Y') }}</td>
                    <td>{{ $trx->description }}</td>
                    <td>{{ $trx->category }}</td>
                    <td class="text-right {{ $trx->type === 'income' ? 'text-success' : '' }}">
                        {{ $trx->formatted_nominal }}
                    </td>
                    <td class="text-right"><strong>{{ $trx->formatted_balance }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada transaksi</td>
                </tr>
            @endforelse
             <tr>
                <td colspan="4"><strong><em>Saldo Akhir</em></strong></td>
                <td class="text-right"><strong>{{ number_format($statement['saldo_akhir'], 0) }} IDR</strong></td>
              </tr>
        </tbody>
    </table>
</div>
@endforeach

</body>
</html>
