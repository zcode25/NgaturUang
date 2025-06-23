<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
                'formatted_nominal' => ($trx->type === 'income' ? '+Rp ' : '-Rp ') . number_format($trx->nominal, 0),
                'balance' => $runningBalance,
                'formatted_balance' => 'Rp ' . number_format($runningBalance, 0),
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

}
