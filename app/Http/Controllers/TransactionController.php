<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;


class TransactionController extends Controller
{
    
    // Category Controller

    public function category()
    {
        
        $categories = Category::where('user_id', Auth::id())
            ->orderBy('name', 'asc')
            ->get();

        return view('transaction.category.index', [
            'categories' => $categories,
        ]);
    }

    public function categoryCreate() 
    {
        return view('transaction.category.create');
    }

    public function categoryStore(Request $request) 
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string|max:255',
        ], [
            'name.required'         => 'Nama kategori wajib diisi.',
            'name.max'              => 'Nama kategori maksimal 255 karakter',
            'description.required'  => 'Deskripsi kategori wajib diisi.',
            'description.max'       => 'Deskripsi kategori maksimal 255 karakter',
        ]);

        $validated['user_id'] = Auth::id();

        Category::create($validated);
        
        return redirect()->route('category')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function categoryEdit(Category $category) 
    {
        return view('transaction.category.edit', [
            'category' => $category,
        ]);
    }

    public function categoryUpdate(Request $request, Category $category) 
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string|max:255',
        ], [
            'name.required'         => 'Nama kategori wajib diisi.',
            'name.max'              => 'Nama kategori maksimal 255 karakter',
            'description.required'  => 'Deskripsi kategori wajib diisi.',
            'description.max'       => 'Deskripsi kategori maksimal 255 karakter',
        ]);

        $category->update($validated);
        
        return redirect()->route('category')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function categoryDestroy(Category $category) 
    {
        try {
            $category->delete();
            return redirect()->route('category')->with('success', 'Kategori berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('category')->with('error', 'Kategori tidak dapat dihapus karena masih digunakan.');
        }
        
    }


    // Income Controller

    public function income(Request $request) 
    {

        $status = $request->status === 'inactive' ? 'inactive' : 'active';

        $incomes = Income::where('user_id', Auth::id())
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();

        $dailyIncome = Income::where('incomes.user_id', Auth::id())
            ->whereDate('incomes.date', now())
            ->where('incomes.status', 'active')
            ->selectRaw("SUM(CASE 
                WHEN wallets.currency = 'USD' THEN incomes.amount * incomes.exchange_rate 
                ELSE incomes.amount 
                END) as total")
            ->join('wallets', 'incomes.wallet_id', '=', 'wallets.id')
            ->value('total') ?? 0;

        $monthlyIncome = Income::where('incomes.user_id', Auth::id())
            ->whereYear('incomes.date', now()->year)
            ->whereMonth('incomes.date', now()->month)
            ->where('incomes.status', 'active')
            ->selectRaw("SUM(CASE 
                WHEN wallets.currency = 'USD' THEN incomes.amount * incomes.exchange_rate 
                ELSE incomes.amount 
                END) as total")
            ->join('wallets', 'incomes.wallet_id', '=', 'wallets.id')
            ->value('total') ?? 0;
        
        $yearlyIncome = Income::where('incomes.user_id', Auth::id())
            ->whereYear('incomes.date', now()->year)
            ->where('incomes.status', 'active')
            ->selectRaw("SUM(CASE 
                WHEN wallets.currency = 'USD' THEN incomes.amount * incomes.exchange_rate 
                ELSE incomes.amount 
                END) as total")
            ->join('wallets', 'incomes.wallet_id', '=', 'wallets.id')
            ->value('total') ?? 0;
        
        $totalIncome = Income::where('incomes.user_id', Auth::id())
            ->where('incomes.status', 'active')
            ->selectRaw("SUM(CASE 
                WHEN wallets.currency = 'USD' THEN incomes.amount * incomes.exchange_rate 
                ELSE incomes.amount 
                END) as total")
            ->join('wallets', 'incomes.wallet_id', '=', 'wallets.id')
            ->value('total') ?? 0;

        return view('transaction.income.index', [
            'incomes' => $incomes,
            'dailyIncome' => $dailyIncome,
            'monthlyIncome' => $monthlyIncome,
            'yearlyIncome' => $yearlyIncome,
            'totalIncome' => $totalIncome,
        ]);
    }

    public function incomeCreate() 
    {
        return view('transaction.income.create', [
            'categories' => Category::where('user_id', Auth::id())->orderBy('name', 'ASC')->get(),
            'wallets' => Wallet::where('user_id', Auth::id())->orderBy('name', 'ASC')->get(),
        ]);
    }

    public function incomeStore(Request $request) 
    {
        
        $wallet = Wallet::findOrFail($request->wallet_id);

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'amount'        => 'required|numeric|min:0',
            'description'   => 'nullable|string|max:255',
            'date'          => 'required|date',
            'category_id'   => 'required|exists:categories,id',
            'wallet_id'     => 'required|exists:wallets,id',
            'exchange_rate' => [
                    Rule::requiredIf($wallet && $wallet->currency === 'USD'),
                    'nullable',
                    'numeric',
                ],
        ], [
            'name.required'         => 'Nama transaksi wajib diisi.',
            'name.max'              => 'Nama transaksi maksimal 255 karakter',
            'amount.required'       => 'Jumlah transaksi wajib diisi.',
            'amount.numeric'        => 'Jumlah transaksi harus berupa angka.',
            'amount.min'            => 'Jumlah transaksi tidak boleh negatif.',
            'description.max'       => 'Deskripsi transaksi maksimal 255 karakter',
            'date.required'         => 'Tanggal transaksi wajib diisi.',
            'date.date'             => 'Tanggal transaksi tidak valid.',
            'category_id.required'  => 'Kategori wajib dipilih.',
            'category_id.exists'    => 'Kategori tidak valid.',
            'wallet_id.required'    => 'Dompet wajib dipilih.',
            'wallet_id.exists'      => 'Dompet tidak valid.',
            'exchange_rate.required' => 'Nilai tukar wajib diisi untuk dompet USD.',
            'exchange_rate.numeric'  => 'Nilai tukar harus berupa angka.',
        ]);

        DB::transaction(function() use ($validated, $wallet) {
            $validated['user_id'] = Auth::id();
            $validated['status'] = "active";

            $amount = $validated['amount'];
            $wallet->balance += $amount;
            $wallet->save();

            Income::create($validated);

         });

        return redirect()->route('income')->with('success', 'Transaksi berhasil ditambahkan.'); 
    }

    public function incomeEdit(Income $income) 
    {
        return view('transaction.income.edit', [
            'income' => $income,
            'categories' => Category::where('user_id', Auth::id())->orderBy('name', 'ASC')->get(),
            'wallets' => Wallet::where('user_id', Auth::id())->orderBy('name', 'ASC')->get(),
        ]);
    }

    public function incomeUpdate(Request $request, Income $income) 
    {
        $newWallet = Wallet::findOrFail($request->wallet_id);
        $oldWallet = $income->wallet;

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'amount'        => 'required|numeric|min:0',
            'description'   => 'nullable|string|max:255',
            'date'          => 'required|date',
            'category_id'   => 'required|exists:categories,id',
            'wallet_id'     => 'required|exists:wallets,id',
            'exchange_rate' => [
                    Rule::requiredIf($newWallet->currency === 'USD'),
                    'nullable',
                    'numeric',
                ],
        ], [
            'name.required'         => 'Nama transaksi wajib diisi.',
            'name.max'              => 'Nama transaksi maksimal 255 karakter',
            'amount.required'       => 'Jumlah transaksi wajib diisi.',
            'amount.numeric'        => 'Jumlah transaksi harus berupa angka.',
            'amount.min'            => 'Jumlah transaksi tidak boleh negatif.',
            'description.max'       => 'Deskripsi transaksi maksimal 255 karakter',
            'date.required'         => 'Tanggal transaksi wajib diisi.',
            'date.date'             => 'Tanggal transaksi tidak valid.',
            'category_id.required'  => 'Kategori wajib dipilih.',
            'category_id.exists'    => 'Kategori tidak valid.',
            'wallet_id.required'    => 'Dompet wajib dipilih.',
            'wallet_id.exists'      => 'Dompet tidak valid.',
            'exchange_rate.required' => 'Nilai tukar wajib diisi untuk dompet USD.',
            'exchange_rate.numeric'  => 'Nilai tukar harus berupa angka.',
        ]);

        DB::transaction(function() use ($income, $validated, $oldWallet, $newWallet){
            $oldAmount = $income->amount;
            $newAmount = $validated['amount'];

            if ($oldWallet->id === $newWallet->id) {
                $difference = $newAmount - $oldAmount;
                $oldWallet->balance += $difference;
                $oldWallet->save();
            } else {
                $oldWallet->balance -= $oldAmount;
                $oldWallet->save();
        
                $newWallet->balance += $newAmount;
                $newWallet->save();
            }

            $validated['user_id'] = Auth::id();
            $income->update($validated);
        });

        return redirect()->route('income')->with('success', 'Transaksi berhasil diperbarui.'); 
    }

    public function incomeDestroy(Income $income) 
    {
        DB::transaction(function () use ($income) {
            $wallet = $income->wallet;
    
            if ($income->status == 'inactive') {
                $income->delete();
            } else {
                $wallet->balance -= $income->amount;
                $wallet->save();
    
                $income->update(['status' => 'inactive']);
            }
        });

        return redirect()->route('income')->with('success', 'Transaksi berhasil dihapus.');

    }

    public function incomeToggle(Income $income) 
    {
        
        DB::transaction(function () use ($income) {
            $wallet = $income->wallet;
    
            if ($income->status == 'active') {
                $wallet->balance -= $income->amount;
                $wallet->save();
    
                $income->update(['status' => 'inactive']);
            } else {
                $wallet->balance += $income->amount;
                $wallet->save();
    
                $income->update(['status' => 'active']);
            }

        });
        
        return redirect()->route('income')->with('success', 'Transaksi berhasil diperbarui.');
        
    }


    // Expense Controller
    public function expense(Request $request) 
    {

        $status = $request->status === 'inactive' ? 'inactive' : 'active';

        $expenses = Expense::where('user_id', Auth::id())
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();

        $dailyExpense = Expense::where('expenses.user_id', Auth::id())
            ->whereDate('expenses.date', now())
            ->where('expenses.status', 'active')
            ->selectRaw("SUM(CASE 
                WHEN wallets.currency = 'USD' THEN expenses.amount * expenses.exchange_rate 
                ELSE expenses.amount 
                END) as total")
            ->join('wallets', 'expenses.wallet_id', '=', 'wallets.id')
            ->value('total') ?? 0;

        

        $monthlyExpense = Expense::where('expenses.user_id', Auth::id())
            ->whereYear('expenses.date', now()->year)
            ->whereMonth('expenses.date', now()->month)
            ->where('expenses.status', 'active')
            ->selectRaw("SUM(CASE 
                WHEN wallets.currency = 'USD' THEN expenses.amount * expenses.exchange_rate 
                ELSE expenses.amount 
                END) as total")
            ->join('wallets', 'expenses.wallet_id', '=', 'wallets.id')
            ->value('total') ?? 0;
        
        $yearlyExpense = Expense::where('expenses.user_id', Auth::id())
            ->whereYear('expenses.date', now()->year)
            ->where('expenses.status', 'active')
            ->selectRaw("SUM(CASE 
                WHEN wallets.currency = 'USD' THEN expenses.amount * expenses.exchange_rate 
                ELSE expenses.amount 
                END) as total")
            ->join('wallets', 'expenses.wallet_id', '=', 'wallets.id')
            ->value('total') ?? 0;
        
        $totalExpense = Expense::where('expenses.user_id', Auth::id())
            ->where('expenses.status', 'active')
            ->selectRaw("SUM(CASE 
                WHEN wallets.currency = 'USD' THEN expenses.amount * expenses.exchange_rate 
                ELSE expenses.amount 
                END) as total")
            ->join('wallets', 'expenses.wallet_id', '=', 'wallets.id')
            ->value('total') ?? 0;

        // dd($monthlyExpense);

        return view('transaction.expense.index', [
            'expenses' => $expenses,
            'dailyExpense' => $dailyExpense,
            'monthlyExpense' => $monthlyExpense,
            'yearlyExpense' => $yearlyExpense,
            'totalExpense' => $totalExpense,
        ]);
    }

    public function expenseCreate() 
    {
        return view('transaction.expense.create', [
            'categories' => Category::where('user_id', Auth::id())->orderBy('name', 'ASC')->get(),
            'wallets' => Wallet::where('user_id', Auth::id())->orderBy('name', 'ASC')->get(),
        ]);
    }

    public function expenseStore(Request $request) 
    {
        
        $wallet = Wallet::findOrFail($request->wallet_id);

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'amount'        => 'required|numeric|min:0',
            'description'   => 'nullable|string|max:255',
            'date'          => 'required|date',
            'category_id'   => 'required|exists:categories,id',
            'wallet_id'     => 'required|exists:wallets,id',
            'exchange_rate' => [
                    Rule::requiredIf($wallet && $wallet->currency === 'USD'),
                    'nullable',
                    'numeric',
                ],
        ], [
            'name.required'         => 'Nama transaksi wajib diisi.',
            'name.max'              => 'Nama transaksi maksimal 255 karakter',
            'amount.required'       => 'Jumlah transaksi wajib diisi.',
            'amount.numeric'        => 'Jumlah transaksi harus berupa angka.',
            'amount.min'            => 'Jumlah transaksi tidak boleh negatif.',
            'description.max'       => 'Deskripsi transaksi maksimal 255 karakter',
            'date.required'         => 'Tanggal transaksi wajib diisi.',
            'date.date'             => 'Tanggal transaksi tidak valid.',
            'category_id.required'  => 'Kategori wajib dipilih.',
            'category_id.exists'    => 'Kategori tidak valid.',
            'wallet_id.required'    => 'Dompet wajib dipilih.',
            'wallet_id.exists'      => 'Dompet tidak valid.',
            'exchange_rate.required' => 'Nilai tukar wajib diisi untuk dompet USD.',
            'exchange_rate.numeric'  => 'Nilai tukar harus berupa angka.',
        ]);

        DB::transaction(function() use ($validated, $wallet) {
            $validated['user_id'] = Auth::id();
            $validated['status'] = "active";

            $amount = $validated['amount'];
            $wallet->balance -= $amount;
            $wallet->save();

            Expense::create($validated);

         });

        return redirect()->route('expense')->with('success', 'Transaksi berhasil ditambahkan.'); 
    }

    public function expenseEdit(Expense $expense) 
    {
        return view('transaction.expense.edit', [
            'expense' => $expense,
            'categories' => Category::where('user_id', Auth::id())->orderBy('name', 'ASC')->get(),
            'wallets' => Wallet::where('user_id', Auth::id())->orderBy('name', 'ASC')->get(),
        ]);
    }

    public function expenseUpdate(Request $request, Expense $expense) 
    {
        $newWallet = Wallet::findOrFail($request->wallet_id);
        $oldWallet = $expense->wallet;

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'amount'        => 'required|numeric|min:0',
            'description'   => 'nullable|string|max:255',
            'date'          => 'required|date',
            'category_id'   => 'required|exists:categories,id',
            'wallet_id'     => 'required|exists:wallets,id',
            'exchange_rate' => [
                    Rule::requiredIf($newWallet->currency === 'USD'),
                    'nullable',
                    'numeric',
                ],
        ], [
            'name.required'         => 'Nama transaksi wajib diisi.',
            'name.max'              => 'Nama transaksi maksimal 255 karakter',
            'amount.required'       => 'Jumlah transaksi wajib diisi.',
            'amount.numeric'        => 'Jumlah transaksi harus berupa angka.',
            'amount.min'            => 'Jumlah transaksi tidak boleh negatif.',
            'description.max'       => 'Deskripsi transaksi maksimal 255 karakter',
            'date.required'         => 'Tanggal transaksi wajib diisi.',
            'date.date'             => 'Tanggal transaksi tidak valid.',
            'category_id.required'  => 'Kategori wajib dipilih.',
            'category_id.exists'    => 'Kategori tidak valid.',
            'wallet_id.required'    => 'Dompet wajib dipilih.',
            'wallet_id.exists'      => 'Dompet tidak valid.',
            'exchange_rate.required' => 'Nilai tukar wajib diisi untuk dompet USD.',
            'exchange_rate.numeric'  => 'Nilai tukar harus berupa angka.',
        ]);

        DB::transaction(function() use ($expense, $validated, $oldWallet, $newWallet){
            $oldAmount = $expense->amount;
            $newAmount = $validated['amount'];

            if ($oldWallet->id === $newWallet->id) {
                $difference = $newAmount - $oldAmount;
                $oldWallet->balance -= $difference;
                $oldWallet->save();
            } else {
                $oldWallet->balance += $oldAmount;
                $oldWallet->save();
        
                $newWallet->balance -= $newAmount;
                $newWallet->save();
            }

            $validated['user_id'] = Auth::id();
            $expense->update($validated);
        });

        return redirect()->route('expense')->with('success', 'Transaksi berhasil diperbarui.'); 
    }

    public function expenseDestroy(Expense $expense) 
    {
        DB::transaction(function () use ($expense) {
            $wallet = $expense->wallet;
    
            if ($expense->status == 'inactive') {
                $expense->delete();
            } else {
                $wallet->balance += $expense->amount;
                $wallet->save();
    
                $expense->update(['status' => 'inactive']);
            }
        });

        return redirect()->route('expense')->with('success', 'Transaksi berhasil dihapus.');

    }

    public function expenseToggle(Expense $expense) 
    {
        
        DB::transaction(function () use ($expense) {
            $wallet = $expense->wallet;
    
            if ($expense->status == 'active') {
                $wallet->balance += $expense->amount;
                $wallet->save();
    
                $expense->update(['status' => 'inactive']);
            } else {
                $wallet->balance -= $expense->amount;
                $wallet->save();
    
                $expense->update(['status' => 'active']);
            }

        });
        
        return redirect()->route('expense')->with('success', 'Transaksi berhasil diperbarui.');
        
    }
}
