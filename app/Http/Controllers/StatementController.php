<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exports\StatementExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Wallet;
use Carbon\Carbon;

class StatementController extends Controller
{


    public function index(Request $request)
    {
        $userId = Auth::id();

        // parse tanggal (format yyyy-mm-dd). gunakan startOfDay / endOfDay jika perlu waktu.
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', now()->endOfMonth()->toDateString());

        // normalize sebagai date string (safe)
        $start = Carbon::parse($startDate)->toDateString();
        $end   = Carbon::parse($endDate)->toDateString();

        $wallets = Wallet::where('user_id', $userId)->get();
        $statements = [];

        foreach ($wallets as $wallet) {
            // ---------------------------
            // 1) Hitung net change sejak startDate (semua transaksi >= startDate sampai sekarang)
            // ---------------------------
            $incomeFromStart = DB::table('incomes')
                ->where('wallet_id', $wallet->id)
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->whereDate('date', '>=', $start)
                ->sum('amount');

            $expenseFromStart = DB::table('expenses')
                ->where('wallet_id', $wallet->id)
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->whereDate('date', '>=', $start)
                ->sum('amount');

            $netFromStart = $incomeFromStart - $expenseFromStart;

            // ---------------------------
            // 2) Hitung saldo awal (posisi sebelum transaksi pada startDate)
            //    saldo_awal = current_balance - netChange(sejak startDate)
            // ---------------------------
            // fallback: jika wallet->balance tidak tersedia, pakai opening_balance jika ada
            $currentBalance = $wallet->balance ?? ($wallet->opening_balance ?? 0);

            $saldoAwal = $currentBalance - $netFromStart;

            // ---------------------------
            // 3) Hitung total income & expense pada periode (start..end)
            // ---------------------------
            $totalIncome = DB::table('incomes')
                ->where('wallet_id', $wallet->id)
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->whereDate('date', '>=', $start)
                ->whereDate('date', '<=', $end)
                ->sum('amount');

            $totalExpense = DB::table('expenses')
                ->where('wallet_id', $wallet->id)
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->whereDate('date', '>=', $start)
                ->whereDate('date', '<=', $end)
                ->sum('amount');

            // ---------------------------
            // 4) Ambil transaksi periode (gabung incomes + expenses) lalu urutkan
            // ---------------------------
            $incomeQuery = DB::table('incomes')
                ->join('categories', 'incomes.category_id', '=', 'categories.id')
                ->select(
                    DB::raw("DATE(incomes.date) as date"),
                    'incomes.created_at',
                    'incomes.name',
                    DB::raw('incomes.amount as nominal'),
                    DB::raw('categories.name as category'),
                    DB::raw("'income' as type")
                )
                ->where('incomes.wallet_id', $wallet->id)
                ->where('incomes.user_id', $userId)
                ->where('incomes.status', 'active')
                ->whereDate('incomes.date', '>=', $start)
                ->whereDate('incomes.date', '<=', $end);

            $expenseQuery = DB::table('expenses')
                ->join('categories', 'expenses.category_id', '=', 'categories.id')
                ->select(
                    DB::raw("DATE(expenses.date) as date"),
                    'expenses.created_at',
                    'expenses.name',
                    DB::raw('expenses.amount as nominal'),
                    DB::raw('categories.name as category'),
                    DB::raw("'expense' as type")
                )
                ->where('expenses.wallet_id', $wallet->id)
                ->where('expenses.user_id', $userId)
                ->where('expenses.status', 'active')
                ->whereDate('expenses.date', '>=', $start)
                ->whereDate('expenses.date', '<=', $end);

            // unionAll lalu ambil hasil; kemudian urutkan di collection untuk kepastian order
            $rawTransactions = $incomeQuery->unionAll($expenseQuery)->get();

            $transactions = collect($rawTransactions)
                ->sortBy([
                    ['date', 'asc'],
                    ['created_at', 'asc']
                ])
                ->values();

            // ---------------------------
            // 5) Hitung saldo berjalan (running balance)
            // ---------------------------
            $runningBalance = (float) $saldoAwal;
            $detailedTransactions = $transactions->map(function ($trx) use (&$runningBalance) {
                $nominal = (float) $trx->nominal;
                $amount = $trx->type === 'income' ? $nominal : -$nominal;
                $runningBalance += $amount;

                return (object)[
                    'date' => $trx->date,
                    'created_at' => $trx->created_at,
                    'description' => $trx->name,
                    'type' => $trx->type,
                    'category' => $trx->category,
                    'nominal' => $nominal,
                    'formatted_nominal' => ($trx->type === 'income' ? '+ ' : '- ') . number_format($nominal, 0) . ' IDR',
                    'balance' => $runningBalance,
                    'formatted_balance' => number_format($runningBalance, 0) . ' IDR',
                ];
            });

            // ---------------------------
            // 6) Hitung saldo akhir berdasarkan saldoAwal + net periode
            // ---------------------------
            $saldoAkhir = $saldoAwal + ($totalIncome - $totalExpense);

            $statements[] = [
                'wallet' => $wallet,
                'saldo_awal' => $saldoAwal,
                'transactions' => $detailedTransactions,
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'saldo_akhir' => $saldoAkhir,
            ];
        }

        return view('statement.index', compact('statements', 'startDate', 'endDate'));
    }



    public function exportExcel(Request $request)
    {
        $data = $this->getStatementData($request);
        return Excel::download(new StatementExport($data['statements'], $data['startDate'], $data['endDate']), 'e-statement.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getStatementData($request);
        $pdf = Pdf::loadView('statement.export', [
            'statements' => $data['statements'],
            'startDate' => $data['startDate'],
            'endDate' => $data['endDate'],
        ]);
        return $pdf->stream('e-statement.pdf');
    }


    private function getStatementData(Request $request)
    {
        $userId = Auth::id();

        // parse tanggal (format yyyy-mm-dd). gunakan startOfDay / endOfDay jika perlu waktu.
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', now()->endOfMonth()->toDateString());

        // normalize sebagai date string (safe)
        $start = Carbon::parse($startDate)->toDateString();
        $end   = Carbon::parse($endDate)->toDateString();

        $wallets = Wallet::where('user_id', $userId)->get();
        $statements = [];

        foreach ($wallets as $wallet) {
            // ---------------------------
            // 1) Hitung net change sejak startDate (semua transaksi >= startDate sampai sekarang)
            // ---------------------------
            $incomeFromStart = DB::table('incomes')
                ->where('wallet_id', $wallet->id)
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->whereDate('date', '>=', $start)
                ->sum('amount');

            $expenseFromStart = DB::table('expenses')
                ->where('wallet_id', $wallet->id)
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->whereDate('date', '>=', $start)
                ->sum('amount');

            $netFromStart = $incomeFromStart - $expenseFromStart;

            // ---------------------------
            // 2) Hitung saldo awal (posisi sebelum transaksi pada startDate)
            //    saldo_awal = current_balance - netChange(sejak startDate)
            // ---------------------------
            // fallback: jika wallet->balance tidak tersedia, pakai opening_balance jika ada
            $currentBalance = $wallet->balance ?? ($wallet->opening_balance ?? 0);

            $saldoAwal = $currentBalance - $netFromStart;

            // ---------------------------
            // 3) Hitung total income & expense pada periode (start..end)
            // ---------------------------
            $totalIncome = DB::table('incomes')
                ->where('wallet_id', $wallet->id)
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->whereDate('date', '>=', $start)
                ->whereDate('date', '<=', $end)
                ->sum('amount');

            $totalExpense = DB::table('expenses')
                ->where('wallet_id', $wallet->id)
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->whereDate('date', '>=', $start)
                ->whereDate('date', '<=', $end)
                ->sum('amount');

            // ---------------------------
            // 4) Ambil transaksi periode (gabung incomes + expenses) lalu urutkan
            // ---------------------------
            $incomeQuery = DB::table('incomes')
                ->join('categories', 'incomes.category_id', '=', 'categories.id')
                ->select(
                    DB::raw("DATE(incomes.date) as date"),
                    'incomes.created_at',
                    'incomes.name',
                    DB::raw('incomes.amount as nominal'),
                    DB::raw('categories.name as category'),
                    DB::raw("'income' as type")
                )
                ->where('incomes.wallet_id', $wallet->id)
                ->where('incomes.user_id', $userId)
                ->where('incomes.status', 'active')
                ->whereDate('incomes.date', '>=', $start)
                ->whereDate('incomes.date', '<=', $end);

            $expenseQuery = DB::table('expenses')
                ->join('categories', 'expenses.category_id', '=', 'categories.id')
                ->select(
                    DB::raw("DATE(expenses.date) as date"),
                    'expenses.created_at',
                    'expenses.name',
                    DB::raw('expenses.amount as nominal'),
                    DB::raw('categories.name as category'),
                    DB::raw("'expense' as type")
                )
                ->where('expenses.wallet_id', $wallet->id)
                ->where('expenses.user_id', $userId)
                ->where('expenses.status', 'active')
                ->whereDate('expenses.date', '>=', $start)
                ->whereDate('expenses.date', '<=', $end);

            // unionAll lalu ambil hasil; kemudian urutkan di collection untuk kepastian order
            $rawTransactions = $incomeQuery->unionAll($expenseQuery)->get();

            $transactions = collect($rawTransactions)
                ->sortBy([
                    ['date', 'asc'],
                    ['created_at', 'asc']
                ])
                ->values();

            // ---------------------------
            // 5) Hitung saldo berjalan (running balance)
            // ---------------------------
            $runningBalance = (float) $saldoAwal;
            $detailedTransactions = $transactions->map(function ($trx) use (&$runningBalance) {
                $nominal = (float) $trx->nominal;
                $amount = $trx->type === 'income' ? $nominal : -$nominal;
                $runningBalance += $amount;

                return (object)[
                    'date' => $trx->date,
                    'created_at' => $trx->created_at,
                    'description' => $trx->name,
                    'type' => $trx->type,
                    'category' => $trx->category,
                    'nominal' => $nominal,
                    'formatted_nominal' => ($trx->type === 'income' ? '+ ' : '- ') . number_format($nominal, 0) . ' IDR',
                    'balance' => $runningBalance,
                    'formatted_balance' => number_format($runningBalance, 0) . ' IDR',
                ];
            });

            // ---------------------------
            // 6) Hitung saldo akhir berdasarkan saldoAwal + net periode
            // ---------------------------
            $saldoAkhir = $saldoAwal + ($totalIncome - $totalExpense);

            $statements[] = [
                'wallet' => $wallet,
                'saldo_awal' => $saldoAwal,
                'transactions' => $detailedTransactions,
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'saldo_akhir' => $saldoAkhir,
            ];
        }

        return view('statement.index', compact('statements', 'startDate', 'endDate'));
    }

}
