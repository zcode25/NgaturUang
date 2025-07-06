<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index', [
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        $user->save();

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui.');
    }

    public function changePassword(Request $request)
    {
        // dd($request);
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'new_password.required' => 'Kata sandi baru wajib diisi.',
            'new_password.min' => 'Kata sandi baru minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        if (!password_verify($validated['current_password'], $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Kata sandi saat ini salah.']);
        }

        $user->password = bcrypt($validated['new_password']);
        $user->save();

        return redirect()->route('profile')->with('success', 'Kata sandi berhasil diubah.');
    }
}
