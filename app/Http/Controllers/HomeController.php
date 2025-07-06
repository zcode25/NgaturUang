<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Wallet;
use App\Models\Income;
use App\Models\Expense;
use Illuminate\Support\Facades\Redis;
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

        $wallets = Wallet::where('user_id', $userId)->get();
        $totalBalance = $wallets->sum(function ($wallet) use ($usdToIdr) {
            return $wallet->currency === 'USD'
                ? $wallet->balance * $usdToIdr
                : $wallet->balance;
        });

        $totalIncome = Income::where('incomes.user_id', $userId)
            ->where('incomes.status', 'active')
            ->selectRaw("SUM(CASE 
                WHEN wallets.currency = 'USD' THEN incomes.amount * incomes.exchange_rate 
                ELSE incomes.amount 
                END) as total")
            ->join('wallets', 'incomes.wallet_id', '=', 'wallets.id')
            ->value('total') ?? 0;

        $totalExpense = Expense::where('expenses.user_id', $userId)
            ->where('expenses.status', 'active')
            ->selectRaw("SUM(CASE 
                WHEN wallets.currency = 'USD' THEN expenses.amount * expenses.exchange_rate 
                ELSE expenses.amount 
                END) as total")
            ->join('wallets', 'expenses.wallet_id', '=', 'wallets.id')
            ->value('total') ?? 0;

        // Defaults
        $bulan = null;
        $tahun = null;
        $selectedDate_income = null;
        $selectedDate_expense = null;

        // INCOME DATES
        $date_income = DB::table('incomes')
            ->select(
                DB::raw("MIN(date) as tanggal_asli"),
                DB::raw("DATE_FORMAT(MIN(date), '%M %Y') as tanggal"),
                DB::raw("MONTH(date) as bulan"),
                DB::raw("YEAR(date) as tahun")
            )
            ->where('user_id', $userId)
            ->groupBy(DB::raw('YEAR(date)'), DB::raw('MONTH(date)'))
            ->orderBy(DB::raw('YEAR(date)'), 'desc')
            ->orderBy(DB::raw('MONTH(date)'), 'desc')
            ->get();

        if ($date_income->count() > 0) {
            $bulan = $date_income[0]->bulan;
            $tahun = $date_income[0]->tahun;
            $selectedDate_income = $date_income[0]->tanggal;
        }

        if ($request->has('date_income')) {
            $date = $request->input('date_income');
            [$tahun, $bulan] = explode('-', $date);
            $selectedDate_income = Carbon::createFromDate($tahun, $bulan)->translatedFormat('F Y');
        }

        $incomes = collect();
        $labels_income = collect();
        $data_income = collect();
        $total_income = 0;

        if ($bulan && $tahun) {
            $incomes = Income::select(
                DB::raw("DATE_FORMAT(date, '%d') as tanggal"),
                DB::raw("SUM(amount) as budget_income")
            )
                ->where('user_id', $userId)
                ->whereMonth('date', $bulan)
                ->whereYear('date', $tahun)
                ->groupBy('date')
                ->get();

            $labels_income = $incomes->pluck('tanggal');
            $data_income = $incomes->pluck('budget_income');
            $total_income = $data_income->sum();
        }

        // EXPENSE DATES
        $date_expense = DB::table('expenses')
            ->select(
                DB::raw("MIN(date) as tanggal_asli"),
                DB::raw("DATE_FORMAT(MIN(date), '%M %Y') as tanggal"),
                DB::raw("MONTH(date) as bulan"),
                DB::raw("YEAR(date) as tahun")
            )
            ->where('user_id', $userId)
            ->groupBy(DB::raw('YEAR(date)'), DB::raw('MONTH(date)'))
            ->orderBy(DB::raw('YEAR(date)'), 'desc')
            ->orderBy(DB::raw('MONTH(date)'), 'desc')
            ->get();

        if ($date_expense->count() > 0) {
            $bulan = $date_expense[0]->bulan;
            $tahun = $date_expense[0]->tahun;
            $selectedDate_expense = $date_expense[0]->tanggal;
        }

        if ($request->has('date_expense')) {
            $date = $request->input('date_expense');
            [$tahun, $bulan] = explode('-', $date);
            $selectedDate_expense = Carbon::createFromDate($tahun, $bulan)->translatedFormat('F Y');
        }

        $expenses = collect();
        $labels_expense = collect();
        $data_expense = collect();
        $total_expense = 0;

        if ($bulan && $tahun) {
            $expenses = Expense::select(
                DB::raw("DATE_FORMAT(date, '%d') as tanggal"),
                DB::raw("SUM(amount) as budget_expense")
            )
                ->where('user_id', $userId)
                ->whereMonth('date', $bulan)
                ->whereYear('date', $tahun)
                ->groupBy('date')
                ->get();

            $labels_expense = $expenses->pluck('tanggal');
            $data_expense = $expenses->pluck('budget_expense');
            $total_expense = $data_expense->sum();
        }

        $category_incomes = Income::where('user_id', $userId)
            ->where('status', 'active')
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->orderBy('total', 'desc')
            ->get();

        $category_expenses = Expense::where('user_id', $userId)
            ->where('status', 'active')
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->orderBy('total', 'desc')
            ->get();

        return view('home.index', [
            'totalBalance' => $totalBalance,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'date_income' => $date_income,
            'date_expense' => $date_expense,
            'labels_income' => $labels_income,
            'labels_expense' => $labels_expense,
            'data_income' => $data_income,
            'data_expense' => $data_expense,
            'selectedDate_income' => $selectedDate_income,
            'selectedDate_expense' => $selectedDate_expense,
            'total_income' => $total_income,
            'total_expense' => $total_expense,
            'category_incomes' => $category_incomes,
            'category_expenses' => $category_expenses,
        ]);
    }
    
}
