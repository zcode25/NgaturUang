<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Wallet;
use App\Models\Transaction;
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

    // Transaction

    public function trans() 
    {
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

        $selisih = $totalIncome - $totalExpense;

        $transactions = Transaction::where('status', 'active')->orderBy('date', 'DESC')->latest()->get();

        return view('transaction.trans.index', [
            'transactions' => $transactions,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'selisih' => $selisih,
        ]);
    }

    public function transCreate() 
    {
        $types = [
            'income' => 'Pemasukan',
            'expense' => 'Pengeluaran',
        ];
        return view('transaction.trans.create', [
            'categories' => Category::where('user_id', Auth::id())->orderBy('name', 'ASC')->get(),
            'wallets' => Wallet::where('user_id', Auth::id())->orderBy('name', 'ASC')->get(),
            'types' => $types,
        ]);
    }

    public function transStore(Request $request) 
    {
        $wallet = Wallet::findOrFail($request->wallet_id);

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'amount'        => 'required|numeric|min:0',
            'description'   => 'nullable|string|max:255',
            'date'          => 'required|date',
            'type'          => 'required',
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
            'type.required'         => 'Tipe transaksi wajib dipilih.',
            'category_id.required'  => 'Kategori wajib dipilih.',
            'category_id.exists'    => 'Kategori tidak valid.',
            'wallet_id.required'    => 'Dompet wajib dipilih.',
            'wallet_id.exists'      => 'Dompet tidak valid.',
            'exchange_rate.required' => 'Nilai tukar wajib diisi untuk dompet USD.',
            'exchange_rate.numeric'  => 'Nilai tukar harus berupa angka.',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = "active";

        Transaction::create($validated);

        return redirect()->route('trans')->with('success', 'Transaksi berhasil ditambahkan.'); 

    }

    public function transEdit(Transaction $transaction) 
    {
        $types = [
            'income' => 'Pemasukan',
            'expense' => 'Pengeluaran',
        ];
        return view('transaction.trans.edit', [
            'types' => $types,
            'transaction' => $transaction,
            'categories' => Category::where('user_id', Auth::id())->orderBy('name', 'ASC')->get(),
            'wallets' => Wallet::where('user_id', Auth::id())->orderBy('name', 'ASC')->get(),
        ]);
    }

    public function transUpdate(Request $request, Transaction $transaction) 
    {
        $wallet = Wallet::findOrFail($request->wallet_id);

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'amount'        => 'required|numeric|min:0',
            'description'   => 'nullable|string|max:255',
            'date'          => 'required|date',
            'type'          => 'required',
            'category_id'   => 'required|exists:categories,id',
            'wallet_id'     => 'required|exists:wallets,id',
            'exchange_rate' => [
                    Rule::requiredIf($wallet->currency === 'USD'),
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
            'type.required'         => 'Tipe transaksi wajib dipilih.',
            'category_id.required'  => 'Kategori wajib dipilih.',
            'category_id.exists'    => 'Kategori tidak valid.',
            'wallet_id.required'    => 'Dompet wajib dipilih.',
            'wallet_id.exists'      => 'Dompet tidak valid.',
            'exchange_rate.required' => 'Nilai tukar wajib diisi untuk dompet USD.',
            'exchange_rate.numeric'  => 'Nilai tukar harus berupa angka.',
        ]);

        $validated['user_id'] = Auth::id();
        $transaction->update($validated);

        return redirect()->route('trans')->with('success', 'Transaksi berhasil diperbarui.'); 
    }

    public function transDestroy(Transaction $transaction) {
        $transaction->update(['status' => 'inactive']);

        return redirect()->route('trans')->with('success', 'Transaksi berhasil dihapus.'); 
    }


}