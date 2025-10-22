<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Wallet;
use App\Models\Transaction;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $response = Http::get('https://open.er-api.com/v6/latest/USD');

        if (!$response->ok() || !isset($response['rates']['IDR'])) {
            return response()->json(['error' => 'Gagal mengambil kurs USD ke IDR'], 500);
        }

        $usdToIdr = $response['rates']['IDR'];
        $userId = Auth::id();

        $wallets = Wallet::where('user_id', $userId)
        ->latest()
        ->get()
        ->map(function ($wallet) use ($usdToIdr) {
            $income = Transaction::where('wallet_id', $wallet->id)
                ->where('status', 'active')
                ->where('type', 'income')
                ->sum('amount');

            $expense = Transaction::where('wallet_id', $wallet->id)
                ->where('status', 'active')
                ->where('type', 'expense')
                ->sum('amount');

            $wallet->calculated_balance = $wallet->begin_balance + $income - $expense;

            $wallet->kurs = $usdToIdr;

            return $wallet;
        });

        $totalBalance = $wallets->sum(function ($wallet) use ($usdToIdr) {
            return $wallet->currency === 'USD'
                ? $wallet->calculated_balance * $usdToIdr
                : $wallet->calculated_balance;
        });

        $expenseMonths = DB::table('transactions')
            ->selectRaw('YEAR(date) as tahun, MONTH(date) as bulan')
            ->where('user_id', $userId)
            ->where('type', 'expense')
            ->groupBy('tahun', 'bulan')
            ->get();

        $incomeMonths = DB::table('transactions')
            ->selectRaw('YEAR(date) as tahun, MONTH(date) as bulan')
            ->where('user_id', $userId)
            ->where('type', 'income')
            ->groupBy('tahun', 'bulan')
            ->get();


        $availableMonths = $expenseMonths
            ->concat($incomeMonths)
            ->unique(function ($item) {
                return $item->tahun . '-' . str_pad($item->bulan, 2, '0', STR_PAD_LEFT);
            })
            ->sortByDesc(function ($item) {
                return $item->tahun . '-' . str_pad($item->bulan, 2, '0', STR_PAD_LEFT);
            })
            ->values();

        $selectedMonth = $request->input('month') ?? now()->format('Y-m');

        $totalIncome = Transaction::where('user_id', Auth::id())
            ->where('status', 'active')
            ->where('type', 'income')
            ->selectRaw("
                SUM(
                    CASE 
                        WHEN exchange_rate IS NOT NULL AND exchange_rate > 0 
                        THEN amount * exchange_rate 
                        ELSE amount 
                    END
                ) as total
            ")
            ->value('total') ?? 0;

        $totalExpense = Transaction::where('user_id', Auth::id())
            ->where('status', 'active')
            ->where('type', 'expense')
            ->selectRaw("
                SUM(
                    CASE 
                        WHEN exchange_rate IS NOT NULL AND exchange_rate > 0 
                        THEN amount * exchange_rate 
                        ELSE amount 
                    END
                ) as total
            ")
            ->value('total') ?? 0;

        
        $selectedMonth = $request->input('month', now()->format('Y-m'));

        try {
            [$tahun, $bulan] = explode('-', $selectedMonth);
            $startOfMonth = Carbon::createFromDate($tahun, $bulan)->startOfMonth();
            $endOfMonth = Carbon::createFromDate($tahun, $bulan)->endOfMonth();
            $selectedDate = Carbon::createFromDate($tahun, $bulan)->translatedFormat('F Y');
        } catch (\Exception $e) {
            $startOfMonth = now()->startOfMonth();
            $endOfMonth = now()->endOfMonth();
            $selectedDate = now()->translatedFormat('F Y');
        }

        $incomes = Transaction::selectRaw("
            DATE_FORMAT(date, '%d') as tanggal,
            SUM(
                CASE 
                    WHEN exchange_rate IS NOT NULL AND exchange_rate > 0 
                    THEN amount * exchange_rate 
                    ELSE amount 
                END
            ) as budget_income
        ")
        ->where('user_id', $userId)
        ->where('type', 'income')
        ->where('status', 'active')
        ->whereBetween('date', [$startOfMonth, $endOfMonth])
        ->groupBy('date')
        ->get();

        $expenses = Transaction::selectRaw("
            DATE_FORMAT(date, '%d') as tanggal,
            SUM(
                CASE 
                    WHEN exchange_rate IS NOT NULL AND exchange_rate > 0 
                    THEN amount * exchange_rate 
                    ELSE amount 
                END
            ) as budget_expense
        ")
        ->where('user_id', $userId)
        ->where('type', 'expense')
        ->where('status', 'active')
        ->whereBetween('date', [$startOfMonth, $endOfMonth])
        ->groupBy('date')
        ->get();

        $labels_income = $incomes->pluck('tanggal');
        $data_income = $incomes->pluck('budget_income');
        $total_income = $data_income->sum();

        $labels_expense = $expenses->pluck('tanggal');
        $data_expense = $expenses->pluck('budget_expense');
        $total_expense = $data_expense->sum();


        $availableMonths = DB::table('transactions')
            ->select(DB::raw('YEAR(date) as tahun'), DB::raw('MONTH(date) as bulan'))
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->where('type', 'income')
            ->union(
                DB::table('transactions')
                    ->select(DB::raw('YEAR(date) as tahun'), DB::raw('MONTH(date) as bulan'))
                    ->where('user_id', $userId)
                    ->where('status', 'active')
                    ->where('type', 'expense')
            )
            ->distinct()
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->get();

        
        $totalIncomeMonth = Transaction::where('user_id', Auth::id())
            ->where('status', 'active')
            ->where('type', 'income')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->selectRaw("
                SUM(
                    CASE 
                        WHEN exchange_rate IS NOT NULL AND exchange_rate > 0 
                        THEN amount * exchange_rate 
                        ELSE amount 
                    END
                ) as total
            ")
            ->value('total') ?? 0;

        $totalExpenseMonth = Transaction::where('user_id', Auth::id())
            ->where('status', 'active')
            ->where('type', 'expense')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->selectRaw("
                SUM(
                    CASE 
                        WHEN exchange_rate IS NOT NULL AND exchange_rate > 0 
                        THEN amount * exchange_rate 
                        ELSE amount 
                    END
                ) as total
            ")
            ->value('total') ?? 0;

        $selisihMonth = $totalIncomeMonth - $totalExpenseMonth;

        $category_incomes = Transaction::where('user_id', $userId)
            ->where('status', 'active')
            ->where('type', 'income')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->orderBy('total', 'desc')
            ->get();

        $category_expenses = Transaction::where('user_id', $userId)
            ->where('status', 'active')
            ->where('type', 'expense')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->orderBy('total', 'desc')
            ->get();

        return view('home.index', [
            'totalBalance' => $totalBalance,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'totalSelisih' => $totalIncome - $totalExpense,
            'availableMonths' => $availableMonths,
            'labels_income' => $labels_income,
            'labels_expense' => $labels_expense,
            'data_income' => $data_income,
            'data_expense' => $data_expense,
            'selectedDate' => $selectedDate,
            'total_income' => $total_income,
            'total_expense' => $total_expense,
            'category_incomes' => $category_incomes,
            'category_expenses' => $category_expenses,
            'selectedMonth' => $selectedMonth,
            'availableMonths' => $availableMonths,
            'totalIncomeMonth' => $totalIncomeMonth,
            'totalExpenseMonth' => $totalExpenseMonth,
            'selisihMonth' => $selisihMonth,
        ]);
    }
}