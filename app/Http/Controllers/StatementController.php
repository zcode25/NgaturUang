<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exports\StatementExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Wallet;

class StatementController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        $wallets = Wallet::where('user_id', $userId)->get();
        $statements = [];

        foreach ($wallets as $wallet) {
            // Ambil total transaksi selama periode
            $totalIncome = DB::table('incomes')
                ->where('wallet_id', $wallet->id)
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->whereBetween('date', [$startDate, $endDate])
                ->sum('amount');

            $totalExpense = DB::table('expenses')
                ->where('wallet_id', $wallet->id)
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->whereBetween('date', [$startDate, $endDate])
                ->sum('amount');

            // Hitung saldo awal dari balance akhir
            $saldoAwal = $wallet->balance - $totalIncome + $totalExpense;

            // Gabungkan transaksi selama periode
            $transactions = DB::table('incomes')
                ->join('categories', 'incomes.category_id', '=', 'categories.id')
                ->select(
                    'incomes.date',
                    'incomes.created_at',
                    'incomes.name',
                    'incomes.amount as nominal',
                    'categories.name as category',
                    DB::raw("'income' as type")
                )
                ->where('incomes.wallet_id', $wallet->id)
                ->where('incomes.user_id', $userId)
                ->where('incomes.status', 'active')
                ->whereBetween('incomes.date', [$startDate, $endDate])
                ->unionAll(
                    DB::table('expenses')
                        ->join('categories', 'expenses.category_id', '=', 'categories.id')
                        ->select(
                            'expenses.date',
                            'expenses.created_at',
                            'expenses.name',
                            'expenses.amount as nominal',
                            'categories.name as category',
                            DB::raw("'expense' as type")
                        )
                        ->where('expenses.wallet_id', $wallet->id)
                        ->where('expenses.user_id', $userId)
                        ->where('expenses.status', 'active')
                        ->whereBetween('expenses.date', [$startDate, $endDate])
                )
                ->orderBy('date')
                ->orderBy('created_at')
                ->get();

            // Hitung saldo berjalan
            $runningBalance = $saldoAwal;
            $detailedTransactions = $transactions->map(function ($trx) use (&$runningBalance) {
                $amount = $trx->type === 'income' ? $trx->nominal : -$trx->nominal;
                $runningBalance += $amount;

                return (object)[
                    'datetime' => $trx->date,
                    'description' => $trx->name,
                    'type' => $trx->type,
                    'category' => $trx->category,
                    'nominal' => $trx->nominal,
                    'formatted_nominal' => ($trx->type === 'income' ? '+ ' : '- ') . number_format($trx->nominal, 0) . ' IDR',
                    'balance' => $runningBalance,
                    'formatted_balance' => number_format($runningBalance, 0) . ' IDR',
                ];
            });

            $statements[] = [
                'wallet' => $wallet,
                'saldo_awal' => $saldoAwal,
                'transactions' => $detailedTransactions,
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'saldo_akhir' => $wallet->balance, // langsung ambil dari kolom balance
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
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        $wallets = Wallet::where('user_id', $userId)->get();
        $statements = [];

        foreach ($wallets as $wallet) {
            $totalIncome = DB::table('incomes')
                ->where('wallet_id', $wallet->id)
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->whereBetween('date', [$startDate, $endDate])
                ->sum('amount');

            $totalExpense = DB::table('expenses')
                ->where('wallet_id', $wallet->id)
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->whereBetween('date', [$startDate, $endDate])
                ->sum('amount');

            $saldoAwal = $wallet->balance - $totalIncome + $totalExpense;

            $transactions = DB::table('incomes')
                ->join('categories', 'incomes.category_id', '=', 'categories.id')
                ->select(
                    'incomes.date',
                    'incomes.created_at',
                    'incomes.name',
                    'incomes.amount as nominal',
                    'categories.name as category',
                    DB::raw("'income' as type")
                )
                ->where('incomes.wallet_id', $wallet->id)
                ->where('incomes.user_id', $userId)
                ->where('incomes.status', 'active')
                ->whereBetween('incomes.date', [$startDate, $endDate])
                ->unionAll(
                    DB::table('expenses')
                        ->join('categories', 'expenses.category_id', '=', 'categories.id')
                        ->select(
                            'expenses.date',
                            'expenses.created_at',
                            'expenses.name',
                            'expenses.amount as nominal',
                            'categories.name as category',
                            DB::raw("'expense' as type")
                        )
                        ->where('expenses.wallet_id', $wallet->id)
                        ->where('expenses.user_id', $userId)
                        ->where('expenses.status', 'active')
                        ->whereBetween('expenses.date', [$startDate, $endDate])
                )
                ->orderBy('date')
                ->orderBy('created_at')
                ->get();

            $runningBalance = $saldoAwal;
            $detailedTransactions = $transactions->map(function ($trx) use (&$runningBalance) {
                $amount = $trx->type === 'income' ? $trx->nominal : -$trx->nominal;
                $runningBalance += $amount;

                return (object)[
                    'datetime' => $trx->date,
                    'description' => $trx->name,
                    'type' => $trx->type,
                    'category' => $trx->category,
                    'nominal' => $trx->nominal,
                    'formatted_nominal' => ($trx->type === 'income' ? '+ ' : '- ') . number_format($trx->nominal, 0) . ' IDR',
                    'balance' => $runningBalance,
                    'formatted_balance' => number_format($runningBalance, 0) . ' IDR',
                ];
            });

            $statements[] = [
                'wallet' => $wallet,
                'saldo_awal' => $saldoAwal,
                'transactions' => $detailedTransactions,
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'saldo_akhir' => $wallet->balance,
            ];
        }

        return compact('statements', 'startDate', 'endDate');
    }

}
