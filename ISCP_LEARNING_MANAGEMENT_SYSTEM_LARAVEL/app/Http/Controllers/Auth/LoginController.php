<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login admin
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses login admin
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();

            $admin = Auth::guard('web')->user();
            $admin->login_at = now();
            $admin->save();

            // Tentukan role
            $roles = [
                1 => 'Pemilik',
                2 => 'Admin-Master',
                3 => 'Admin-Operasional',
                4 => 'Admin-Konten',
                5 => 'Admin-Pelayanan',
            ];
            $roleName = $roles[$admin->role] ?? 'Admin';

            // Buat pesan multi-baris dengan HTML
            $message = "
        <b>Berhasil login!</b><br>
        Selamat datang, <b>{$admin->username}</b><br>
        Selaku <b>{$roleName}</b>.<br>
        Selamat bekerja 
    ";
            return redirect()->intended('admin/dashboard')
                ->with('success_html', $message);
        }

        return back()
            ->with('error', 'Username atau password salah.')
            ->onlyInput('username');
    }

    /**
     * Logout admin
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')
            ->with('success', 'Berhasil logout.');
    }
}
