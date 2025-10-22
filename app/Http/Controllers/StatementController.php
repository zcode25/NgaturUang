<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exports\StatementExport;
use App\Models\Transaction;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Wallet;
use Carbon\Carbon;

class StatementController extends Controller
{

    public function index(Request $request)
    {
        $data = $this->getStatementData($request);
        return view('statement.index', [
            'statements' => $data['statements'],
            'startDate' => $data['startDate'],
            'endDate' => $data['endDate'],
        ]);
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
        $endDate   = $request->input('end_date', now()->endOfMonth()->toDateString());

        $start = Carbon::parse($startDate)->toDateString();
        $end   = Carbon::parse($endDate)->toDateString();

        $wallets = Wallet::where('user_id', $userId)->where('status', 'active')->get();
        $statements = [];

        foreach ($wallets as $wallet) {
            $incomeBefore = Transaction::where('wallet_id', $wallet->id)
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->where('type', 'income')
                ->whereDate('date', '<', $start)
                ->sum('amount');

            $expenseBefore = Transaction::where('wallet_id', $wallet->id)
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->where('type', 'expense')
                ->whereDate('date', '<', $start)
                ->sum('amount');

            $saldoAwal = $wallet->begin_balance + $incomeBefore - $expenseBefore;


            $detailTransaction = Transaction::where('wallet_id', $wallet->id)
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->whereDate('date', '>=', $start)
                ->whereDate('date', '<=', $end)
                ->orderBy('date', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();

            $running = (float) $saldoAwal;
            $transactionsWithBalance = $detailTransaction->map(function ($trx) use (&$running) {
                $amount = (float) $trx->amount;

                if ($trx->type === 'income') {
                    $running += $amount;
                } else { // expense
                    $running -= $amount;
                }

                $trx->running_balance = $running;

                return $trx;
            });

            
            $totalIncome = $transactionsWithBalance->where('type', 'income')->sum('amount');
            $totalExpense = $transactionsWithBalance->where('type', 'expense')->sum('amount');

            $saldoAkhir = $saldoAwal + $totalIncome - $totalExpense;

            $statements[] = [
                'wallet' => $wallet,
                'saldo_awal' => $saldoAwal,
                'transactions' => $transactionsWithBalance,
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'saldo_akhir' => $saldoAkhir,
            ];
        
        }

        return view('statement.index', compact('statements', 'startDate', 'endDate'));
    }

}
