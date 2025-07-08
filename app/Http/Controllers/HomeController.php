<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Wallet;
use App\Models\Income;
use App\Models\Expense;
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

       // Ambil bulan-tahun dari `expenses`
        $expenseMonths = DB::table('expenses')
            ->selectRaw('YEAR(date) as tahun, MONTH(date) as bulan')
            ->where('user_id', $userId)
            ->groupBy('tahun', 'bulan')
            ->get();

        // Ambil bulan-tahun dari `incomes`
        $incomeMonths = DB::table('incomes')
            ->selectRaw('YEAR(date) as tahun, MONTH(date) as bulan')
            ->where('user_id', $userId)
            ->groupBy('tahun', 'bulan')
            ->get();

        // Gabungkan dan hilangkan duplikat
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

        $totalIncome = Income::join('wallets', 'incomes.wallet_id', '=', 'wallets.id')
                        ->where('incomes.user_id', $userId)      // disambiguasi
                        ->where('incomes.status', 'active')      // disambiguasi
                        ->selectRaw("
                            SUM(
                                CASE
                                    WHEN wallets.currency = 'USD'
                                        THEN incomes.amount * incomes.exchange_rate
                                    ELSE incomes.amount
                                END
                            ) as total
                        ")
                        ->value('total') ?? 0;

        $totalExpense = Expense::join('wallets', 'expenses.wallet_id', '=', 'wallets.id')
                        ->where('expenses.user_id', $userId)        // disambiguasi
                        ->where('expenses.status', 'active')        // disambiguasi
                        ->selectRaw("
                            SUM(
                                CASE
                                    WHEN wallets.currency = 'USD'
                                        THEN expenses.amount * expenses.exchange_rate
                                    ELSE expenses.amount
                                END
                            ) as total
                        ")
                        ->value('total') ?? 0;

        // Ambil bulan dan tahun dari request (format YYYY-MM)
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

        // Ambil income & expense dalam bulan itu
        $incomes = Income::select(DB::raw("DATE_FORMAT(date, '%d') as tanggal"), DB::raw("SUM(amount) as budget_income"))
            ->where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->groupBy('date')
            ->get();

        $expenses = Expense::select(DB::raw("DATE_FORMAT(date, '%d') as tanggal"), DB::raw("SUM(amount) as budget_expense"))
            ->where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->groupBy('date')
            ->get();

        $labels_income = $incomes->pluck('tanggal');
        $data_income = $incomes->pluck('budget_income');
        $total_income = $data_income->sum();

        $labels_expense = $expenses->pluck('tanggal');
        $data_expense = $expenses->pluck('budget_expense');
        $total_expense = $data_expense->sum();

        // Gabungkan income & expense bulan untuk dropdown
        $availableMonths = DB::table('incomes')
            ->select(DB::raw('YEAR(date) as tahun'), DB::raw('MONTH(date) as bulan'))
            ->where('user_id', $userId)
            ->union(
                DB::table('expenses')
                    ->select(DB::raw('YEAR(date) as tahun'), DB::raw('MONTH(date) as bulan'))
                    ->where('user_id', $userId)
            )
            ->distinct()
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->get();

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
        ]);
    }
}