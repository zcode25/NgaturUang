<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $wallets = Wallet::where('user_id', Auth::id())->where('status', 'active')->latest()->get();

        if($request->status == 'inactive') {
            $wallets = Wallet::where('user_id', Auth::id())->where('status', 'inactive')->latest()->get();
        }

        return view('wallet.index', [
            'wallets' => $wallets,
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
            'balance' => 'required|numeric|min:0',
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
            'balance.required' => 'Saldo wajib dipilih.',
            'balance.numeric' => 'Saldo harus berupa angka.',
            'balance.min' => 'Saldo tidak boleh negatif.',
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
        $wallet = Wallet::findOrFail($id);

        return view('wallet.detail', [
            'wallet' => $wallet,
        ]);
    }

    public function update(Request $request, $id)
    {
        // dd($request);


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
