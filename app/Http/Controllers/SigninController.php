<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class SigninController extends Controller
{
    public function index()
    {
        return view('signin.index');
    } 

    public function authenticate(Request $request): RedirectResponse
    {
        // Validasi input
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Ambil data login saja
        $credentials = $request->only('email', 'password');

        // Ambil status checkbox 'remember me'
        $remember = $request->has('remember');

        // Proses login
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended('home');
        }

        // Gagal login
        return back()->withErrors([
            'login_error' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function signout(Request $request): RedirectResponse
    {
        Auth::logout();
    
        $request->session()->invalidate();
    
        $request->session()->regenerateToken();
    
        return redirect(route('signin'));
    }
}
