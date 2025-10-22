<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class WalletController extends Controller
{
    public function index()
    {
        $response = Http::get('https://open.er-api.com/v6/latest/USD');

        if (!$response->ok() || !isset($response['rates']['IDR'])) {
            return response()->json(['error' => 'Gagal mengambil kurs USD ke IDR'], 500);
        }

        $usdToIdr = $response['rates']['IDR'];

        $wallets = Wallet::where('user_id', Auth::id())
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

        return view('wallet.index', [
            'wallets' => $wallets,
            'totalBalance' => $totalBalance,
        ]);
    }

    public function create()
    {
        return view('wallet.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:cash,bank,ewallet,other',
            'currency' => 'required|in:IDR,USD',
            'begin_balance' => 'required|numeric|min:0',
            'account_number' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama dompet wajib diisi.',
            'name.max' => 'Nama dompet maksimal 255 karakter.',
            'type.required' => 'Tipe dompet wajib dipilih.',
            'type.in' => 'Tipe dompet tidak valid.',
            'currency.required' => 'Mata uang wajib dipilih.',
            'currency.in' => 'Mata uang hanya boleh IDR atau USD.',
            'begin_balance.required' => 'Saldo wajib dipilih.',
            'begin_balance.numeric' => 'Saldo harus berupa angka.',
            'begin_balance.min' => 'Saldo tidak boleh negatif.',
            'account_number.max' => 'Nomor rekening maksimal 255 karakter.',
            'bank_name.max' => 'Nama bank maksimal 255 karakter.',
            'description.string' => 'Deskripsi harus berupa teks.',
        ]);

        $validated['user_id'] = Auth::id(); 
        $validated['status'] = 'active';

        Wallet::create($validated);

        return redirect()->route('wallet')->with('success', 'Dompet berhasil ditambahkan.');
        
    }

    public function detail($id)
    {
        $wallet = Wallet::where('id', $id)->first();

        if ($wallet) {
            $income = Transaction::where('wallet_id', $wallet->id)
                ->where('status', 'active')
                ->where('type', 'income')
                ->sum('amount');

            $expense = Transaction::where('wallet_id', $wallet->id)
                ->where('status', 'active')
                ->where('type', 'expense')
                ->sum('amount');

            $wallet->calculated_balance = $wallet->begin_balance + $income - $expense;
        }

        return view('wallet.detail', [
            'wallet' => $wallet,
        ]);
    }

    public function update(Request $request, $id)
    {
        $wallet = Wallet::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:cash,bank,ewallet,other',
            'currency' => 'required|in:IDR,USD',
            'account_number' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama dompet wajib diisi.',
            'name.max' => 'Nama dompet maksimal 255 karakter.',
            'type.required' => 'Tipe dompet wajib dipilih.',
            'type.in' => 'Tipe dompet tidak valid.',
            'currency.required' => 'Mata uang wajib dipilih.',
            'currency.in' => 'Mata uang hanya boleh IDR atau USD.',
            'account_number.max' => 'Nomor rekening maksimal 255 karakter.',
            'bank_name.max' => 'Nama bank maksimal 255 karakter.',
            'description.string' => 'Deskripsi harus berupa teks.',
        ]);

        $wallet->update($validated);

        return back()->with('success', 'Dompet berhasil diperbarui.');
    }

    public function toggleStatus($id)
    {
        $wallet = Wallet::findOrFail($id);
        $wallet->status = $wallet->status === 'active' ? 'inactive' : 'active';
        $wallet->save();

        return redirect()->back()->with('success', 'Status dompet berhasil diperbarui.');
    }
}
